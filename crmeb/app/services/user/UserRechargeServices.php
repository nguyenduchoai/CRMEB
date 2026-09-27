<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\services\user;

use app\dao\user\UserRechargeDao;
use app\services\BaseServices;
use app\services\order\StoreOrderCreateServices;
use app\services\pay\PayServices;
use app\services\pay\RechargeServices;
use app\services\statistic\CapitalFlowServices;
use app\services\system\config\SystemGroupDataServices;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\FormBuilder as Form;
use crmeb\services\pay\Pay;
use think\facade\Route as Url;

/**
 *
 * Class UserRechargeServices
 * @package app\services\user
 * @method be($map, string $field = '') Truy vấn xem một dữ liệu có tồn tại không
 * @method getDistinctCount(array $where, $field, ?bool $search = true)
 * @method getTrendData($time, $type, $timeType)
 */
class UserRechargeServices extends BaseServices
{

    /**
     * UserRechargeServices constructor.
     * @param UserRechargeDao $dao
     */
    public function __construct(UserRechargeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy một dòng dữ liệu
     * @param int $id
     * @param array $field
     */
    public function getRecharge(int $id, array $field = [])
    {
        return $this->dao->get($id, $field);
    }

    /**
     * Lấy dữ liệu thống kê
     * @param array $where
     * @param string $field
     * @return float
     */
    public function getRechargeSum(array $where, string $field = '')
    {
        $whereData = [];
        if (isset($where['data'])) {
            $whereData['time'] = $where['data'];
        }
        if (isset($where['paid']) && $where['paid'] != '') {
            $whereData['paid'] = $where['paid'];
        }
        if (isset($where['nickname']) && $where['nickname']) {
            $whereData['like'] = $where['nickname'];
        }
        if (isset($where['recharge_type']) && $where['recharge_type']) {
            $whereData['recharge_type'] = $where['recharge_type'];
        }
        return $this->dao->getWhereSumField($whereData, $field);
    }

    /**
     * Lấy danh sách nạp tiền
     * @param array $where
     * @param string $field
     * @return array
     */
    public function getRechargeList(array $where, string $field = '*', $is_page = true)
    {
        $whereData = [];
        if (isset($where['data'])) {
            $whereData['time'] = $where['data'];
        }
        if (isset($where['paid']) && $where['paid'] != '') {
            $whereData['paid'] = $where['paid'];
        }
        if (isset($where['nickname']) && $where['nickname']) {
            $whereData['like'] = $where['nickname'];
        }
        [$page, $limit] = $this->getPageValue($is_page);
        $list = $this->dao->getList($whereData, $field, $page, $limit);
        $count = $this->dao->count($whereData);

        foreach ($list as &$item) {
            switch ($item['recharge_type']) {
                case PayServices::WEIXIN_PAY:
                    $item['_recharge_type'] = 'Nạp tiền qua WeChat';
                    break;
                case 'system':
                    $item['_recharge_type'] = 'Nạp tiền từ hệ thống';
                    break;
                case PayServices::ALIAPY_PAY:
                    $item['_recharge_type'] = 'Nạp tiền qua Alipay';
                    break;
                default:
                    $item['_recharge_type'] = 'Nạp tiền khác';
                    break;
            }
            $item['_pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : 'Chưa có';
            $item['_add_time'] = $item['add_time'] ? date('Y-m-d H:i:s', $item['add_time']) : 'Chưa có';
            $item['paid_type'] = $item['paid'] ? 'Đã thanh toán' : 'Chưa thanh toán';
            $item['avatar'] = strpos($item['avatar'] ?? '', 'http') === false ? (sys_config('site_url') . $item['avatar']) : $item['avatar'];
            unset($item['user']);
        }
        return compact('list', 'count');
    }

    /**
     * Lấy dữ liệu nạp tiền của người dùng
     * @return array
     */
    public function user_recharge(array $where)
    {
        $data = [];
        $data['sumPrice'] = $this->getRechargeSum($where, 'price');
        $data['sumRefundPrice'] = $this->getRechargeSum($where, 'refund_price');
        $where['recharge_type'] = 'alipay';
        $data['sumAlipayPrice'] = $this->getRechargeSum($where, 'price');
        $where['recharge_type'] = 'weixin';
        $data['sumWeixinPrice'] = $this->getRechargeSum($where, 'price');
        return [
            [
                'name' => 'Tổng số tiền nạp',
                'field' => 'đ',
                'count' => $data['sumPrice'],
                'className' => 'iconjiaoyijine',
                'col' => 6,
            ],
            [
                'name' => 'Số tiền nạp đã hoàn',
                'field' => 'đ',
                'count' => $data['sumRefundPrice'],
                'className' => 'iconshangpintuikuanjine',
                'col' => 6,
            ],
            [
                'name' => 'Số tiền nạp qua Alipay',
                'field' => 'đ',
                'count' => $data['sumAlipayPrice'],
                'className' => 'iconzhifubao',
                'col' => 6,
            ],
            [
                'name' => 'Số tiền nạp qua WeChat',
                'field' => 'đ',
                'count' => $data['sumWeixinPrice'],
                'className' => 'iconweixinzhifu',
                'col' => 6,
            ],
        ];
    }

    /**
     * Form hoàn tiền
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/24
     */
    public function refund_edit(int $id)
    {
        $UserRecharge = $this->getRecharge($id);
        if (!$UserRecharge) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        if ($UserRecharge['paid'] != 1) {
            throw new AdminException('Đơn hàng chưa thanh toán');
        }
        if ($UserRecharge['price'] == $UserRecharge['refund_price']) {
            throw new AdminException('Đã hoàn hết số tiền thanh toán, không thể hoàn tiền thêm');
        }
        if ($UserRecharge['recharge_type'] == 'balance') {
            throw new AdminException('Hoa hồng đã chuyển vào số dư, không thể hoàn tiền');
        }
        $f = array();
        $f[] = Form::input('order_id', 'Mã đơn hoàn tiền', $UserRecharge->getData('order_id'))->disabled(true);
        $f[] = Form::radio('refund_price', 'Trạng thái', 1)->options([['label' => 'Tiền gốc (trừ số dư được tặng)', 'value' => 1], ['label' => 'Chỉ tiền gốc', 'value' => 0]]);
        return create_form('Sửa', $f, Url::buildUrl('/finance/recharge/' . $id), 'PUT');
    }

    /**
     * Thao tác hoàn tiền
     * @param int $id
     * @param string $refund_price
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refund_update(int $id, string $refund_price)
    {
        $UserRecharge = $this->getRecharge($id);
        if (!$UserRecharge) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        if ($UserRecharge['price'] == $UserRecharge['refund_price']) {
            throw new AdminException('Đã hoàn hết số tiền thanh toán, không thể hoàn tiền thêm');
        }
        if ($UserRecharge['recharge_type'] == 'balance') {
            throw new AdminException('Hoa hồng đã chuyển vào số dư, không thể hoàn tiền');
        }
        $data['refund_price'] = $UserRecharge['price'];
        $refund_data['pay_price'] = $UserRecharge['price'];
        $refund_data['refund_price'] = $UserRecharge['price'];
        if ($refund_price == 1) {
            $number = bcadd($UserRecharge['price'], $UserRecharge['give_price'], 2);
        } else {
            $number = $UserRecharge['price'];
        }

        try {
            $recharge_type = $UserRecharge['recharge_type'];
            if ($recharge_type == 'weixin') {
                $refund_data['wechat'] = true;
            } else {
                $refund_data['trade_no'] = $UserRecharge['trade_no'];
                $refund_data['order_id'] = $UserRecharge['order_id'];
                /** @var WechatUserServices $wechatUserServices */
                $wechatUserServices = app()->make(WechatUserServices::class);
                $refund_data['open_id'] = $wechatUserServices->uidToOpenid((int)$UserRecharge['uid'], 'routine') ?? '';
                $refund_data['pay_new_weixin_open'] = sys_config('pay_new_weixin_open');
                /** @var StoreOrderCreateServices $storeOrderCreateServices */
                $storeOrderCreateServices = app()->make(StoreOrderCreateServices::class);
                $refund_data['refund_no'] = $storeOrderCreateServices->getNewOrderId('tk');
            }
            if ($recharge_type == 'allinpay') {
                $drivers = 'allin_pay';
                $trade_no = $UserRecharge['trade_no'];
            } elseif (sys_config('pay_wechat_type')) {
                $drivers = 'v3_wechat_pay';
                $trade_no = $UserRecharge['trade_no'];
            } else {
                $drivers = 'wechat_pay';
                $trade_no = $UserRecharge['order_id'];
            }
            /** @var Pay $pay */
            $pay = app()->make(Pay::class, [$drivers]);
            $pay->refund($trade_no, $refund_data);
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
        if (!$this->dao->update($id, $data)) {
            throw new AdminException('Sửa thất bại');
        }

        //Sửa số dư người dùng
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($UserRecharge['uid']);
        if ($userInfo['now_money'] > $number) {
            $now_money = bcsub((string)$userInfo['now_money'], $number, 2);
        } else {
            $number = $userInfo['now_money'];
            $now_money = 0;
        }
        $userServices->update((int)$UserRecharge['uid'], ['now_money' => $now_money], 'uid');

        //Ghi vào dòng tiền
        /** @var CapitalFlowServices $capitalFlowServices */
        $capitalFlowServices = app()->make(CapitalFlowServices::class);
        $UserRecharge['nickname'] = $userInfo['nickname'];
        $UserRecharge['phone'] = $userInfo['phone'];
        $capitalFlowServices->setFlow($UserRecharge, 'refund_recharge');

        //Lưu bản ghi số dư
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $userMoneyServices->income('user_recharge_refund', $UserRecharge['uid'], $number, $now_money, $id);

        //Đẩy thông báo nhắc nhở
        event('NoticeListener', [['user_type' => strtolower($userInfo['user_type']), 'data' => $data, 'UserRecharge' => $UserRecharge, 'now_money' => $refund_price], 'recharge_order_refund_status']);

        //Thông báo tùy chỉnh - Hoàn tiền nạp
        $UserRecharge['now_money'] = $now_money;
        $UserRecharge['time'] = date('Y-m-d H:i:s');
        event('NoticeListener', [$UserRecharge['uid'], $UserRecharge, 'recharge_refund']);

        //Sự kiện tùy chỉnh - Admin hoàn tiền nạp
        event('CustomEventListener', ['admin_recharge_refund', [
            'uid' => $UserRecharge['uid'],
            'refund_price' => $UserRecharge['price'],
            'now_money' => $now_money,
            'nickname' => $UserRecharge['price'],
            'phone' => $UserRecharge['phone'],
            'refund_time' => date('Y-m-d H:i:s')
        ]]);

        return true;
    }

    /**
     * Xóa
     * @param int $id
     * @return bool
     */
    public function delRecharge(int $id)
    {
        $rechargInfo = $this->getRecharge($id);
        if (!$rechargInfo) throw new AdminException('Dữ liệu không tồn tại');
        if ($rechargInfo->paid) {
            throw new AdminException('Không thể xóa bản ghi đơn hàng đã thanh toán');
        }
        if ($this->dao->delete($id))
            return true;
        else
            throw new AdminException('Xóa thất bại');
    }

    /**
     * Tạo mã đơn nạp tiền
     * @return bool|string
     */
    public function getOrderId()
    {
        return 'wx' . date('YmdHis', time()) . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * Chuyển hoa hồng vào số dư
     * @param int $uid
     * @param $price
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function importNowMoney(int $uid, $price)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException('Tham số không hợp lệ');
        }
        /** @var UserBrokerageServices $frozenPrices */
        $frozenPrices = app()->make(UserBrokerageServices::class);
        $broken_commission = $frozenPrices->getUserFrozenPrice($uid);
        $commissionCount = bcsub((string)$user['brokerage_price'], (string)$broken_commission, 2);
        if ($price > $commissionCount) {
            throw new ApiException('Số tiền chuyển không được lớn hơn hoa hồng có thể rút');
        }
        $edit_data = [];
        $edit_data['now_money'] = bcadd((string)$user['now_money'], (string)$price, 2);
        $edit_data['brokerage_price'] = $user['brokerage_price'] > $price ? bcsub((string)$user['brokerage_price'], (string)$price, 2) : 0;
        if (!$userServices->update($uid, $edit_data, 'uid')) {
            throw new ApiException('Sửa thất bại');
        }

        //Ghi bản ghi nạp tiền
        $rechargeInfo = [
            'uid' => $uid,
            'order_id' => app()->make(StoreOrderCreateServices::class)->getNewOrderId('cz'),
            'recharge_type' => 'balance',
            'price' => $price,
            'give_price' => 0,
            'paid' => 1,
            'pay_time' => time(),
            'add_time' => time()
        ];
        if (!$re = $this->dao->save($rechargeInfo)) {
            throw new ApiException('Ghi nhận nạp tiền vào số dư thất bại');
        }

        //Lịch sử số dư
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $userMoneyServices->income('brokerage_to_nowMoney', $uid, $price, $edit_data['now_money'], $re['id']);

        //Ghi bản ghi rút tiền
        $extractInfo = [
            'uid' => $uid,
            'real_name' => $user['nickname'],
            'extract_type' => 'balance',
            'extract_price' => $price,
            'balance' => $user['brokerage_price'],
            'add_time' => time(),
            'status' => 1
        ];
        /** @var UserExtractServices $userExtract */
        $userExtract = app()->make(UserExtractServices::class);
        $userExtract->save($extractInfo);

        //Bản ghi rút hoa hồng
        /** @var UserBrokerageServices $userBrokerageServices */
        $userBrokerageServices = app()->make(UserBrokerageServices::class);
        $userBrokerageServices->income('brokerage_to_nowMoney', $uid, $price, $edit_data['brokerage_price'], $re['id']);
        return true;
    }

    /**
     * Yêu cầu nạp tiền
     * @param int $uid
     * @param $price
     * @param $recharId
     * @param $type
     * @param $from
     * @param bool $renten
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function recharge(int $uid, $price, $recharId, $type, $from, bool $renten = false)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException('Người dùng không tồn tại');
        }
        switch ((int)$type) {
            case 0: //Thanh toán nạp số dư
                $paid_price = 0;
                if ($recharId) {
                    /** @var SystemGroupDataServices $systemGroupData */
                    $systemGroupData = app()->make(SystemGroupDataServices::class);
                    $data = $systemGroupData->getDateValue($recharId);
                    if ($data === false) {
                        throw new ApiException('Gói nạp tiền bạn chọn đã ngừng áp dụng');
                    } else {
                        $paid_price = $data['give_money'] ?? 0;
                        $price = $data['price'] ?? 0;
                    }
                }
                $recharge_data = [];
                $recharge_data['order_id'] = app()->make(StoreOrderCreateServices::class)->getNewOrderId('cz');
                $recharge_data['uid'] = $uid;
                $recharge_data['price'] = $price;
                $recharge_data['recharge_type'] = $from;
                $recharge_data['paid'] = 0;
                $recharge_data['add_time'] = time();
                $recharge_data['give_price'] = $paid_price;
                $recharge_data['channel_type'] = $user['user_type'];
                if (!$rechargeOrder = $this->dao->save($recharge_data)) {
                    throw new ApiException('Tạo đơn nạp tiền thất bại');
                }
                try {
                    /** @var RechargeServices $recharge */
                    $recharge = app()->make(RechargeServices::class);
                    $order_info = $recharge->recharge($rechargeOrder);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage());
                }
                if ($renten) {
                    return $order_info;
                }
                return ['msg' => '', 'type' => $from, 'data' => $order_info];
            case 1: //Chuyển hoa hồng vào số dư
                $this->importNowMoney($uid, $price);
                return ['msg' => 'Chuyển vào số dư thành công', 'type' => $from, 'data' => []];
            default:
                throw new ApiException('Tham số không hợp lệ');
        }
    }

    /**
     * Sau khi người dùng nạp tiền thành công
     * @param $orderId
     * @param array $other
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function rechargeSuccess($orderId, array $other = [])
    {
        $order = $this->dao->getOne(['order_id' => $orderId, 'paid' => 0]);
        if (!$order) {
            throw new ApiException('Đơn hàng không tồn tại');
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo((int)$order['uid']);
        if (!$user) {
            throw new ApiException('Người dùng không tồn tại');
        }
        $price = bcadd((string)$order['price'], (string)$order['give_price'], 2);
        if (!$this->dao->update($order['id'], ['paid' => 1, 'recharge_type' => $other['pay_type'], 'pay_time' => time(), 'trade_no' => $other['trade_no'] ?? ''], 'id')) {
            throw new ApiException('Chỉnh sửa đơn hàng thất bại');
        }
        $now_money = bcadd((string)$user['now_money'], (string)$price, 2);
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $userMoneyServices->income('user_recharge', $user['uid'], ['number' => $price, 'price' => $order['price'], 'give_price' => $order['give_price']], $now_money, $order['id']);
        if (!$userServices->update((int)$order['uid'], ['now_money' => $now_money], 'uid')) {
            throw new ApiException('Chỉnh sửa thông tin người dùng thất bại');
        }

        /** @var CapitalFlowServices $capitalFlowServices */
        $capitalFlowServices = app()->make(CapitalFlowServices::class);
        $order['nickname'] = $user['nickname'];
        $order['phone'] = $user['phone'];
        $capitalFlowServices->setFlow($order, 'recharge');

        //Đẩy thông báo nhắc nhở
        event('NoticeListener', [['order' => $order, 'now_money' => $now_money], 'recharge_success']);

        //Thông báo tùy chỉnh - Đơn hàng từ chối hoàn tiền
        $order['now_money'] = $now_money;
        $order['time'] = date('Y-m-d H:i:s');
        event('CustomNoticeListener', [$order['uid'], $order, 'recharge_success']);

        $order['pay_type'] = $other['pay_type'];
        // Service đơn hàng Mini Program
        event('OrderShippingListener', ['recharge', $order, 3, '', '']);

        //Sự kiện tùy chỉnh - Người dùng nạp tiền
        event('CustomEventListener', ['user_recharge', [
            'uid' => $order['uid'],
            'id' => (int)$order['id'],
            'order_id' => $orderId,
            'nickname' => $order['nickname'],
            'phone' => $order['phone'],
            'price' => $order['price'],
            'give_price' => $order['give_price'],
            'now_money' => $order['now_money'],
            'recharge_time' => date('Y-m-d H:i:s'),
        ]]);

        return true;
    }

    /**
     * Truy vấn số tiền nạp của người dùng
     * @param array $where
     * @param string $rechargeSumField
     * @param string $selectType
     * @param string $group
     * @return float|int
     */
    public function getRechargeMoneyByWhere(array $where, string $rechargeSumField, string $selectType, string $group = "")
    {
        switch ($selectType) {
            case "sum" :
                return $this->dao->getWhereSumField($where, $rechargeSumField);
            case "group" :
                return $this->dao->getGroupField($where, $rechargeSumField, $group);
        }
    }
}
