<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\services\user;

use app\dao\user\UserExtractDao;
use app\services\BaseServices;
use app\services\order\StoreOrderCreateServices;
use app\services\statistic\CapitalFlowServices;
use app\services\system\admin\SystemAdminServices;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\AliPayService;
use crmeb\services\FormBuilder as Form;
use crmeb\services\app\WechatService;
use crmeb\services\pay\Pay;
use crmeb\services\wechat\Payment;
use crmeb\services\workerman\ChannelService;
use EasyWeChat\Payment\Order;
use think\exception\ValidateException;
use think\facade\Route as Url;

/**
 *
 * Class UserExtractServices
 * @package app\services\user
 */
class UserExtractServices extends BaseServices
{

    /**
     * UserExtractServices constructor.
     * @param UserExtractDao $dao
     */
    public function __construct(UserExtractDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy một bản ghi rút tiền
     * @param int $id
     * @param array $field
     * @return array|\think\Model|null
     */
    public function getExtract(int $id, array $field = [])
    {
        return $this->dao->get($id, $field);
    }

    /**
     * Lấy tổng số lần rút tiền của một người dùng
     * @param int $uid
     * @return float
     */
    public function getUserExtract(int $uid)
    {
        return $this->dao->getWhereSum(['uid' => $uid, 'status' => 1]);
    }

    /**
     * Lấy danh sách tổng số lần rút tiền của một số người dùng
     * @param array $uids
     */
    public function getUsersSumList(array $uids)
    {
        return $this->dao->getWhereSumList(['uid' => $uids, 'status' => 1]);
    }

    public function getCount(array $where = [])
    {
        return $this->dao->getCount($where);
    }

    /**
     * Lấy danh sách rút tiền
     * @param array $where
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserExtractList(array $where, string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getExtractList($where, $field, $page, $limit);
        foreach ($list as &$item) {
            $item['nickname'] = $item['user']['nickname'] ?? '';
            $item['receive_price'] = bcsub((string)$item['extract_price'], (string)$item['extract_fee'], 2);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy tổng số lần rút tiền
     * @param array $where
     */
    public function getExtractSum(array $where)
    {
        return $this->dao->getExtractMoneyByWhere($where, 'extract_price');
    }

    /**
     * Từ chối yêu cầu rút tiền
     * @param $id
     * @param $fail_msg
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeFail(int $id, $userExtract, $message)
    {
        $fail_time = time();
        $extract_number = $userExtract['extract_price'];
        $mark = 'Rút tiền thất bại, hoàn lại hoa hồng' . $extract_number . 'đ';
        $uid = $userExtract['uid'];
        $status = -1;
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        $this->transaction(function () use ($user, $uid, $id, $extract_number, $message, $userServices, $status, $fail_time) {
            //Thêm bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $now_brokerage = bcadd((string)$user['brokerage_price'], (string)$extract_number, 2);
            $userBrokerageServices->income('extract_fail', $uid, $extract_number, $now_brokerage, $id);
            if (!$userServices->update($uid, ['brokerage_price' => bcadd((string)$user['brokerage_price'], (string)$extract_number, 2)], 'uid'))
                throw new AdminException(400657);
            if (!$this->dao->update($id, ['fail_time' => $fail_time, 'fail_msg' => $message, 'status' => $status])) {
                throw new AdminException(100007);
            }
        });

        event('NoticeListener', [['uid' => $uid, 'userType' => strtolower($user['user_type']), 'extract_number' => $extract_number, 'nickname' => $user['nickname'], 'message' => $message], 'user_balance_change']);

        //Thông báo tùy chỉnh - Người dùng rút tiền thất bại
        $userExtract['nickname'] = $user['nickname'];
        $userExtract['message'] = $message;
        $userExtract['time'] = date('Y-m-d H:i:s');
        $userExtract['price'] = $extract_number;
        $userExtract['phone'] = app()->make(UserServices::class)->value($userExtract['uid'], 'phone');
        event('CustomNoticeListener', [$userExtract['uid'], $userExtract, 'extract_fail']);

        //Sự kiện tùy chỉnh - Người dùng rút tiền thất bại
        event('CustomEventListener', ['admin_extract_fail', [
            'uid' => $userExtract['uid'],
            'price' => $userExtract['price'],
            'pay_type' => $userExtract['extract_type'],
            'nickname' => $userExtract['price'],
            'phone' => $userExtract['phone'],
            'fail_time' => date('Y-m-d H:i:s')
        ]]);

        return true;
    }

    /**
     * Duyệt yêu cầu rút tiền
     * @param int $id
     * @param $userExtract
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeSuccess(int $id, $userExtract)
    {
        $extractNumber = bcsub($userExtract['extract_price'], $userExtract['extract_fee'], 2);
        /** @var WechatUserServices $wechatServices */
        $wechatServices = app()->make(WechatUserServices::class);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userType = $userServices->value(['uid' => $userExtract['uid']], 'user_type');
        $nickname = $userServices->value(['uid' => $userExtract['uid']], 'nickname');
        $phone = $userServices->value(['uid' => $userExtract['uid']], 'phone');
        $order_id = $userExtract['wechat_order_id'] != '' ? $userExtract['wechat_order_id'] : app()->make(StoreOrderCreateServices::class)->getNewOrderId('tx');
        $insertData = ['wechat_order_id' => $order_id, 'nickname' => $nickname, 'phone' => $phone];

        //Tự động rút tiền về ví WeChat
        if (sys_config('weixin_extract_type', 0) && $userExtract['extract_type'] == 'weixin') {
            $type = '';
            $openid = $wechatServices->uidToOpenid($userExtract['uid'], $userExtract['channel_type']);
            if ($userExtract['channel_type'] == 'wechat') {
                $type = Order::JSAPI;
            } elseif ($userExtract['channel_type'] == 'routine') {
                $type = 'mini';
            } elseif ($userExtract['channel_type'] == 'app') {
                $type = Order::APP;
            }
            if (!$openid) {
                $openid = $wechatServices->uidToOpenid($userExtract['uid'], 'wechat');
                $type = Order::JSAPI;
            }
            if (!$openid) {
                $openid = $wechatServices->uidToOpenid($userExtract['uid'], 'routine');
                $type = 'mini';
            }
            if (!$openid) {
                $openid = $wechatServices->uidToOpenid((int)$userExtract['uid'], 'app');
                $type = Order::APP;
            }
            if (!$openid) {
                throw new ValidateException('Người dùng này hiện chưa hỗ trợ tự động chuyển khoản vào ví WeChat, vui lòng chuyển khoản thủ công');
            }
            //Chuyển tiền cho merchant v3
            if (sys_config('pay_wechat_type')) {
                $pay = new Pay('v3_wechat_pay');
                if (sys_config('v3_pay_public_key') != '') {
                    $res = $pay->merchantPayNew(
                        $type,
                        $order_id,
                        sys_config('v3_transfer_scene_id', '1000'),
                        $openid,
                        $userExtract['real_name'],
                        bcmul($extractNumber, '100', 0),
                        'Rút hoa hồng về ví WeChat',
                        sys_config('site_url') . '/api/transfer/notify/' . $type,
                        'Thù lao lao động',
                        [
                            [
                                'info_type' => 'Loại vị trí công việc',
                                'info_content' => 'Thưởng cộng tác viên'
                            ],
                            [
                                'info_type' => 'Mô tả thù lao',
                                'info_content' => 'Rút tiền thưởng đơn hàng giới thiệu'
                            ],
                        ]
                    );
                    $this->dao->update($id, [
                        'out_bill_no' => $res['out_bill_no'] ?? '',
                        'package_info' => $res['package_info'] ?? '',
                        'state' => $res['state'] ?? '',
                        'transfer_bill_no' => $res['transfer_bill_no'] ?? '',
                        'fail_reason' => $res['fail_reason'] ?? '',
                        'status' => 1
                    ]);
                    event('NoticeListener', [['uid' => $userExtract['uid'], 'order_id' => $order_id, 'extractNumber' => $extractNumber, 'type' => 1], 'revenue_received']);
                    return 'v3_extract';
                } else {
                    $res = $pay->merchantPay($openid, $order_id, $extractNumber, [
                        'type' => $type,
                        'batch_name' => 'Rút hoa hồng về ví WeChat',
                        'batch_remark' => 'Vào lúc' . date('Y-m-d H:i:s') . 'bạn đã rút tiền.' . $extractNumber . 'đ'
                    ]);
                    $this->dao->update($id, ['wechat_order_id' => $order_id]);
                }

            } else {
                // Rút tiền qua WeChat
                $res = WechatService::merchantPay($openid, $order_id, (string)$extractNumber, 'Rút hoa hồng về ví WeChat');
            }

            if (!$res) {
                throw new ApiException(400658);
            }
        }
        if (sys_config('alipay_extract_type', 0) && $userExtract['extract_type'] == 'alipay') {
            // Xây dựng tham số rút tiền Alipay
            $alipaySignType = sys_config('alipay_sign_type');
            if ($alipaySignType == 0) {
                $bizParams = [
                    'payee_type' => 'ALIPAY_LOGONID', // Loại tài khoản người nhận, ALIPAY_LOGONID - tài khoản đăng nhập Alipay
                    'payee_account' => $userExtract['real_name'], // Tài khoản người nhận, tài khoản Alipay đã xác thực danh tính thực
                    'amount' => $extractNumber, // Số tiền rút
                    'payer_show_name' => sys_config('site_name'), // Họ tên người thanh toán/tên cá nhân
                    'payee_real_name' => $userExtract['user_name'], // Họ tên thật của người nhận/tên cá nhân
                    'remark' => 'Rút tiền ₫' . $extractNumber . ' về Alipay', // Ghi chú nghiệp vụ
                ];
            } else {
                $bizParams = [
                    'out_biz_no' => $order_id, // Mã đơn hàng của merchant
                    'trans_amount' => $extractNumber,
                    'biz_scene' => 'DIRECT_TRANSFER',
                    'product_code' => 'TRANS_ACCOUNT_NO_PWD',
                    'order_title' => sys_config('site_name') . 'Rút tiền',
                    'payee_info' => [
                        'identity' => $userExtract['alipay_code'],
                        'identity_type' => 'ALIPAY_LOGON_ID',
                        'name' => $userExtract['user_name'],
                    ],
                    'remark' => 'Rút tiền ₫' . $extractNumber . ' về Alipay', // Ghi chú nghiệp vụ
                ];
            }
            // Gọi service Alipay để gửi yêu cầu rút tiền Alipay
            $res = AliPayService::instance()->merchantPay($bizParams, $alipaySignType);
            // Nếu yêu cầu rút tiền Alipay thất bại thì ném ngoại lệ (exception)
            if (!$res) {
                throw new ApiException('Rút tiền thất bại, vui lòng kiểm tra nhật ký sau!');
            }
        }

        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $user = $userService->getUserInfo($userExtract['uid']);
        $insertData['nickname'] = $user['nickname'];
        $insertData['phone'] = $user['phone'];

        /** @var CapitalFlowServices $capitalFlowServices */
        $capitalFlowServices = app()->make(CapitalFlowServices::class);
        $capitalFlowServices->setFlow([
            'order_id' => $order_id,
            'uid' => $userExtract['uid'],
            'price' => bcmul('-1', $extractNumber, 2),
            'pay_type' => $userExtract['extract_type'],
            'nickname' => $insertData['nickname'],
            'phone' => $insertData['phone']
        ], 'extract');

        if (!$this->dao->update($id, ['status' => 1])) {
            throw new AdminException(100007);
        }
        event('NoticeListener', [['uid' => $userExtract['uid'], 'userType' => strtolower($userType), 'extractNumber' => $extractNumber, 'nickname' => $nickname], 'user_extract']);

        //Thông báo tùy chỉnh - rút tiền người dùng thành công
        $userExtract['nickname'] = $nickname;
        $userExtract['phone'] = $phone;
        $userExtract['time'] = date('Y-m-d H:i:s');
        $userExtract['price'] = $extractNumber;
        event('CustomNoticeListener', [$userExtract['uid'], $userExtract, 'extract_success']);

        //Sự kiện tùy chỉnh - rút tiền người dùng thành công
        event('CustomEventListener', ['admin_extract_success', [
            'uid' => $userExtract['uid'],
            'price' => $extractNumber,
            'pay_type' => $userExtract['extract_type'],
            'nickname' => $insertData['nickname'],
            'phone' => $phone,
            'success_time' => date('Y-m-d H:i:s')
        ]]);

        return true;
    }

    /**
     * Hiển thị danh sách resource
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(array $where)
    {
        $list = $this->getUserExtractList($where);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        //Số tiền chờ rút
        $where['status'] = 0;
        $extract_statistics['price'] = $this->getExtractSum($where);
        //Số tiền đã rút
        $where['status'] = 1;
        $extract_statistics['priced'] = $this->getExtractSum($where);
        /** @var UserBrokerageServices $userBrokerageServices */
        $userBrokerageServices = app()->make(UserBrokerageServices::class);
        $where['pm'] = 1;
        $brokerage_count = $userBrokerageServices->getUsersBokerageSum($where);
        $refund_brokerage = $userBrokerageServices->sum(['type' => 'refund'], 'number');
        $extract_statistics['brokerage_count'] = bcsub((string)$brokerage_count, (string)$refund_brokerage, 2);
        //Số tiền chưa rút
        $extract_statistics['brokerage_not'] = $extract_statistics['brokerage_count'] > $extract_statistics['priced'] ? bcsub((string)$extract_statistics['brokerage_count'], (string)$extract_statistics['priced'], 2) : 0.00;
        return compact('extract_statistics', 'list');
    }

    /**
     * Hiển thị trang form sửa resource.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit(int $id)
    {
        $UserExtract = $this->getExtract($id);
        if (!$UserExtract) {
            throw new AdminException(100026);
        }
        $f = array();
        $f[] = Form::input('real_name', 'Họ tên', $UserExtract['real_name']);
        $f[] = Form::number('extract_price', 'Số tiền rút', (float)$UserExtract['extract_price'])->precision(2)->disabled(true);
        if ($UserExtract['extract_type'] == 'alipay') {
            $f[] = Form::input('alipay_code', 'Tài khoản Alipay', $UserExtract['alipay_code']);
        } else if ($UserExtract['extract_type'] == 'weixin') {
            $f[] = Form::input('wechat', 'ID WeChat', $UserExtract['wechat']);
        } else if ($UserExtract['extract_type'] == 'balance') {
        } else {
            $f[] = Form::input('bank_code', 'Số thẻ ngân hàng', $UserExtract['bank_code']);
            $f[] = Form::input('bank_address', 'Ngân hàng mở tài khoản', $UserExtract['bank_address']);
        }
        $f[] = Form::input('mark', 'Ghi chú', $UserExtract['mark'])->type('textarea');
        return create_form('Sửa', $f, Url::buildUrl('/finance/extract/' . $id), 'PUT');
    }

    public function update(int $id, array $data)
    {
        if (!$this->dao->update($id, $data))
            throw new AdminException(100007);
        else
            return true;
    }

    /**
     * Từ chối
     * @param $id
     * @return mixed
     */
    public function refuse(int $id, string $message)
    {
        $extract = $this->getExtract($id);
        if (!$extract) {
            throw new AdminException(100026);
        }
        if ($extract->status == 1) {
            throw new AdminException(400659);
        }
        if ($extract->status == -1) {
            throw new AdminException(400660);
        }
        $res = $this->changeFail($id, $extract, $message);
        if ($res) {
            return true;
        } else {
            throw new AdminException(100005);
        }
    }

    /**
     * Duyệt
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function adopt(int $id)
    {
        $extract = $this->getExtract($id);
        if (!$extract) {
            throw new AdminException(100026);
        }
        if ($extract->status == 1) {
            throw new AdminException(400659);
        }
        if ($extract->status == -1) {
            throw new AdminException(400660);
        }
        $res = $this->changeSuccess($id, $extract);
        if ($res) {
            return $res;
        } else {
            throw new AdminException(100005);
        }
    }

    /**Số lượng chờ rút tiền
     * @return int
     */
    public function userExtractCount()
    {
        return $this->dao->count(['status' => 0]);
    }

    /**
     * Rút tiền về thẻ ngân hàng
     * @param int $uid
     * @return mixed
     */
    public function bank(int $uid)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $user = $userService->getUserInfo($uid, 'brokerage_price,uid');
        if (!$user) {
            throw new ApiException(100026);
        }
        /** @var UserBrokerageServices $services */
        $services = app()->make(UserBrokerageServices::class);
        $data['broken_commission'] = $services->getUserFrozenPrice($uid);
        if ($data['broken_commission'] < 0)
            $data['broken_commission'] = '0';
        $data['brokerage_price'] = $user['brokerage_price'];
        //Hoa hồng có thể rút
        $data['commissionCount'] = bcsub((string)$data['brokerage_price'], (string)$data['broken_commission'], 2);
        $extractBank = sys_config('user_extract_bank') ?? []; //Ngân hàng rút tiền
        $extractBank = str_replace("\r\n", "\n", $extractBank);//Ngăn không tương thích
        $data['extractBank'] = explode("\n", is_array($extractBank) ? ($extractBank[0] ?? $extractBank) : $extractBank);
        $data['minPrice'] = sys_config('user_extract_min_price');//Số tiền rút tối thiểu
        $data['weixinExtractType'] = (int)sys_config('weixin_extract_type', 0);//Cách nhận tiền qua WeChat
        $data['alipayExtractType'] = (int)sys_config('alipay_extract_type', 0);//Cách nhận tiền qua Alipay
        $data['withdrawal_fee'] = sys_config('withdrawal_fee', 0);//Phí rút tiền
        return $data;
    }

    /**
     * Yêu cầu rút tiền
     * @param int $uid
     * @param array $data
     */
    public function cash(int $uid, array $data)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $user = $userService->getUserInfo($uid);
        if (!$user) {
            throw new ApiException(100026);
        }

        if ($data['extract_type'] == 'weixin' && !sys_config('weixin_extract_type', 0) && !$data['weixin']) {
            throw new ApiException(400110);
        }

        if ($data['extract_type'] == 'weixin' && bccomp($data['money'], '0.1', 2) < 0) {
            throw new ApiException('Số tiền rút qua WeChat không được nhỏ hơn 0.1đ');
        }

        /** @var WechatUserServices $wechatServices */
        $wechatServices = app()->make(WechatUserServices::class);
        $openid = $wechatServices->uidToOpenid($uid, 'wechat');
        if (!$openid) $openid = $wechatServices->uidToOpenid($uid, 'routine');

        if ($data['extract_type'] == 'weixin' && sys_config('weixin_extract_type', 0) && !$openid) {
            throw new ApiException(410024);
        }

        /** @var UserBrokerageServices $services */
        $services = app()->make(UserBrokerageServices::class);
        $data['broken_commission'] = $services->getUserFrozenPrice($uid);
        if ($data['broken_commission'] < 0)
            $data['broken_commission'] = 0;
        $data['brokerage_price'] = $user['brokerage_price'];
        //Hoa hồng có thể rút
        $commissionCount = bcsub((string)$data['brokerage_price'], (string)$data['broken_commission'], 2);
        if ($data['money'] > $commissionCount) {
            throw new ApiException(400661);
        }

        $extractPrice = $user['brokerage_price'];
        $userExtractMinPrice = sys_config('user_extract_min_price');
        if ($data['money'] < $userExtractMinPrice) {
            throw new ApiException(400662, ['money' => $userExtractMinPrice]);
        }
        if ($extractPrice < 0) {
            throw new ApiException(400663, ['money' => $data['money']]);
        }
        if ($data['money'] > $extractPrice) {
            throw new ApiException(400663, ['money' => $data['money']]);
        }
        if ($data['money'] <= 0) {
            throw new ApiException(400664);
        }
        $data['extract_price'] = bcmul($data['money'], '1', 2);
        $insertData = [
            'wechat_order_id' => app()->make(StoreOrderCreateServices::class)->getNewOrderId('tx'),
            'uid' => $user['uid'],
            'extract_type' => $data['extract_type'],
            'extract_price' => $data['extract_price'],
            'extract_fee' => bcmul((string)$data['extract_price'], bcdiv((string)sys_config('withdrawal_fee', '0'), '100', 4), 2),
            'add_time' => time(),
            'balance' => $user['brokerage_price'],
            'status' => 0,
            'channel_type' => $data['channel_type']
        ];
        if (isset($data['name']) && strlen(trim($data['name']))) $insertData['real_name'] = $data['name'];
        else $insertData['real_name'] = $user['nickname'];
        if (isset($data['cardnum'])) $insertData['bank_code'] = $data['cardnum'];
        else $insertData['bank_code'] = '';
        if (isset($data['bankname'])) $insertData['bank_address'] = $data['bankname'];
        else $insertData['bank_address'] = '';
        if (isset($data['weixin'])) $insertData['wechat'] = $data['weixin'];
        else $insertData['wechat'] = $user['nickname'];
        $mark = '';
        $feeMark = sys_config('withdrawal_fee', 0) == 0 ? '' : ', phí thủ tục' . $insertData['extract_fee'] . 'đ';
        if ($data['extract_type'] == 'alipay') {
            $insertData['alipay_code'] = $data['alipay_code'];
            $insertData['qrcode_url'] = $data['qrcode_url'];
            $insertData['user_name'] = $data['user_name'];
            $insertData['real_name'] = $data['user_name'];
            $mark = 'Rút tiền qua Alipay' . $insertData['extract_price'] . 'đ' . $feeMark;
        } else if ($data['extract_type'] == 'bank') {
            $mark = 'Dùng thẻ UnionPay' . $insertData['bank_code'] . 'Rút tiền' . $insertData['extract_price'] . 'đ' . $feeMark;
        } else if ($data['extract_type'] == 'weixin') {
            $insertData['user_name'] = $data['user_name'];
            $insertData['real_name'] = $data['user_name'];
            $insertData['qrcode_url'] = $data['qrcode_url'];
            $mark = 'Rút tiền qua WeChat' . $insertData['extract_price'] . 'đ' . $feeMark;
            if (sys_config('weixin_extract_type', 0) && $openid) {
                if ($data['extract_price'] < 0.1) {
                    throw new ApiException(400665);
                }
            }
        }
        $res1 = $this->transaction(function () use ($insertData, $data, $uid, $userService, $user, $mark) {
            if (!$res1 = $this->dao->save($insertData)) {
                throw new ApiException(410121);
            }
            $balance = bcsub((string)$user['brokerage_price'], $data['extract_price'], 2) ?? 0;
            if (!$userService->update($uid, ['brokerage_price' => $balance], 'uid')) {
                throw new ApiException(410121);
            }

            //Lưu bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $userBrokerageServices->income('extract', $uid, ['mark' => $mark, 'number' => $data['extract_price']], $balance, $res1['id']);
            return $res1;
        });

        try {
            ChannelService::instance()->send('WITHDRAW', ['id' => $res1->id]);
        } catch (\Exception $e) {
        }
        /** @var SystemAdminServices $systemAdmin */
        $systemAdmin = app()->make(SystemAdminServices::class);
        $systemAdmin->adminNewPush();
        //Tin nhắn
        event('NoticeListener', [['nickname' => $user['nickname'], 'money' => $data['extract_price']], 'kefu_send_extract_application']);

        //Sự kiện tùy chỉnh - Người dùng rút tiền
        event('CustomEventListener', ['user_extract', [
            'uid' => $insertData['uid'],
            'phone' => $user['phone'],
            'extract_type' => $insertData['extract_type'],
            'extract_price' => $insertData['extract_price'],
            'extract_fee' => $insertData['extract_fee'],
            'extract_time' => date('Y-m-d H:i:s'),
        ]]);

        return true;
    }

    /**
     * @param array $where
     * @param string $SumField
     * @param string $selectType
     * @param string $group
     * @return float|mixed
     */
    public function getOutMoneyByWhere(array $where, string $SumField, string $selectType, string $group = "")
    {
        switch ($selectType) {
            case "sum" :
                return $this->dao->getWhereSumField($where, $SumField);
            case "group" :
                return $this->dao->getGroupField($where, $SumField, $group);
        }
    }
}
