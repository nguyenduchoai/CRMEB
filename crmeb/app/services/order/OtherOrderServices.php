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

namespace app\services\order;


use app\dao\order\OtherOrderDao;
use app\services\BaseServices;
use app\services\pay\PayServices;
use app\services\statistic\CapitalFlowServices;
use app\services\user\member\MemberShipServices;
use app\services\user\UserBillServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserServices;
use app\services\user\member\MemberCardServices;
use crmeb\exceptions\ApiException;
use think\App;
use app\jobs\OtherOrderJob;

/**
 * Class OtherOrderServices
 * @package app\services\order
 * @method getDistinctCount(array $where, $field, ?bool $search = true)
 * @method getPayUserCount(int $time, string $channel_type)
 * @method getTrendData($time, $type, $timeType)
 */
class OtherOrderServices extends BaseServices
{
    /**
     * @var OtherOrderDao
     */
    protected $dao;

    /**
     * Khởi tạo, lấy handle tầng dao
     * OtherOrderServices constructor.
     * @param OtherOrderDao $dao
     */
    public function __construct(OtherOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Tạo dữ liệu đơn hàng mua thành viên
     * @param array $data
     * @return mixed
     */
    public function addOtherOrderData(array $data)
    {
        if (!$data) throw new ApiException('Dữ liệu không tồn tại');
        $add = [
            'uid' => $data['uid'],
            'type' => $data['type'] ?? 1,
            'order_id' => $data['order_id'],
            'channel_type' => $data['channel_type'],
            'pay_type' => $data['pay_type'] ?? 0,
            'member_type' => $data['member_type'] ?? 0,
            'member_price' => $data['member_price'] ?? 0.00,
            'pay_price' => $data['pay_price'] ?? 0.00,
            'code' => $data['member_code'] ?? "",
            'vip_day' => $data['vip_day'] ?? 0,
            'is_permanent' => $data['is_permanent'] ?? 0,
            'is_free' => $data['is_free'] ?? 0,
            'overdue_time' => $data['overdue_time'] ?? 0,
            'status' => 0,
            'paid' => $data['paid'] ?? 0,
            'pay_time' => $data['pay_time'] ?? 0,
            'money' => $data['money'] ?? 0,
            'add_time' => time(),
        ];
        return $this->dao->save($add);
    }

    /**
     * Có thể nhận miễn phí không
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isCanGetFree(int $uid)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        /** @var MemberShipServices $memberShipService */
        $memberShipService = app()->make(MemberShipServices::class);
        /** @var StoreOrderEconomizeServices $economizeService */
        $economizeService = app()->make(StoreOrderEconomizeServices::class);
        $freeDay = $memberShipService->getVipDay(['type' => "free"]);
        $freeConfig = array();
        $freeConfig['price'] = 0;
        $freeConfig['pre_price'] = 0;
        $freeConfig['title'] = "Thành viên miễn phí";
        $freeConfig['type'] = "free";
        $freeConfig['vip_day'] = $freeDay ?: 0;
        $userInfo = $userService->get($uid);
        if ($freeConfig) {
            $freeConfig['is_record'] = 0;
            $record = $this->dao->getOneByWhere(['uid' => $uid, 'is_free' => 1]);
            if ($record) {
                $freeConfig['is_record'] = 1;
            }
        }
        $registerTime = $this->TimeConvert(['start_time' => date('Y-m-d H:i:s', $userInfo['add_time']), 'end_time' => date('Y-m-d H:i:s', time())]);
        $userInfo['register_days'] = $registerTime['days'];
        $userInfo['economize_money'] = $economizeService->sumEconomizeMoney($uid);
        $userInfo['shop_name'] = sys_config('site_name');
        $freeConfig['user_info'] = $userInfo;
        return $freeConfig;
    }

    /**Chuyển đổi thời gian tìm kiếm
     * @param $timeKey
     * @param bool $isNum
     * @throws \Exception
     */
    public function TimeConvert($timeKey, $isNum = false)
    {
        switch ($timeKey) {
            case "today":
                $data['start_time'] = date('Y-m-d 00:00:00', time());
                $data['end_time'] = date('Y-m-d 23:59:59', time());
                $data['days'] = 1;
                break;
            case "yestoday":
                $data['start_time'] = date('Y-m-d 00:00:00', strtotime('-1 day'));
                $data['end_time'] = date('Y-m-d 23:59:59', strtotime('-1 day'));
                $data['days'] = 1;
                break;
            case "last_month":
                $data['start_time'] = date('Y-m-01 00:00:00', strtotime('-1 month'));
                $data['end_time'] = date('Y-m-t 23:59:59', strtotime('-1 month'));
                $data['days'] = 30;
                break;
            case "month":
                $data['start_time'] = $month_start_time = date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));
                $data['end_time'] = date('Y-m-d 23:59:59', strtotime("$month_start_time +1 month -1 day"));
                $data['days'] = 30;
                break;
            case "year":
                $data['start_time'] = date('Y-01-01 00:00:00', time());
                $data['end_time'] = date('Y-12-t 23:59:59', time());
                $data['days'] = 365;
                break;
            case "last_year":
                $data['start_time'] = date('Y-01-01 00:00:00', strtotime('-1 year'));
                $data['end_time'] = date('Y-12-t 23:59:59', strtotime('-1 year'));
                $data['days'] = 365;
                break;
            case 30:
            case 15:
            case 7:
                if (!$isNum) {
                    $data['start_time'] = date("Y-m-d 00:00:00", strtotime("-$timeKey day"));
                    $data['end_time'] = date('Y-m-d 23:59:59', time());
                    $data['days'] = $timeKey;
                } else {
                    $day = $timeKey * 2;
                    $data['start_time'] = date("Y-m-d 00:00:00", strtotime("-$day  day"));
                    $data['end_time'] = date("Y-m-d 23:59:59", strtotime("-$timeKey day"));
                    $data['days'] = $timeKey;
                }
                break;
            default:
                $datetime_start = new \DateTime($timeKey['start_time']);
                $datetime_end = new \DateTime($timeKey['end_time']);
                $days = $datetime_start->diff($datetime_end)->days;
                $days = $days > 0 ? $days : 1;
                if (!$isNum) {
                    $data['start_time'] = $timeKey['start_time'];
                    $data['end_time'] = $timeKey['end_time'];
                    $data['days'] = $days;
                } else {
                    $data['start_time'] = date("Y-m-d 00:00:00", strtotime("-$days day"));
                    $data['end_time'] = $timeKey['start_time'];
                    $data['days'] = $days;
                }
        }
        return $data;
    }


    /**
     * Truy vấn dữ liệu đơn hàng thẻ thành viên
     * @param array $where
     * @param string $field
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOne(array $where, string $field = '*')
    {
        return $this->dao->getOne($where, $field);
    }

    /**
     * @param int $uid
     * @param string $channelType Kênh thanh toán
     * @param bool $memberType Loại thẻ thành viên
     * @param string $payPrice Số tiền thanh toán
     * @param string $payType Phương thức thanh toán
     * @param $type Loại đơn hàng
     * @return mixed
     * @throws \Exception
     */
    public function createOrder(int $uid, string $channelType, $memberType = false, string $payPrice, string $payType, $type, $money, $mcId)
    {
        /** @var StoreOrderCreateServices $storeOrderCreateService */
        $storeOrderCreateService = app()->make(StoreOrderCreateServices::class);
        $orderInfo = [
            'uid' => $uid,
            'order_id' => $storeOrderCreateService->getNewOrderId('hy'),
            'pay_price' => $payPrice,
            'pay_type' => $payType,
            'channel_type' => $channelType,
            'member_code' => "",
        ];
        if ($type != 3) { //Phân biệt 0: nhận thành viên miễn phí 1: mua thành viên 2: dùng mã thẻ để nhận thành viên 3: thanh toán ngoại tuyến
            if (!$memberType) throw new ApiException('Loại thành viên không tồn tại');
            list($memberPrice, $isFree, $isPermanent, $overdueTime, $type, $newMemberRight) = $this->checkPayMemberType($memberType, $payPrice, $type, $uid, $mcId);
            $orderInfo['member_price'] = $memberPrice;
            $orderInfo['money'] = $memberPrice;
            $orderInfo['vip_day'] = $newMemberRight[$mcId]['vip_day'];
            $orderInfo['member_type'] = $memberType;
            $orderInfo['overdue_time'] = $overdueTime;
            $orderInfo['is_permanent'] = $isPermanent;
            $orderInfo['is_free'] = $isFree;
            $orderInfo['type'] = $type;
            $changeType = "create_member_order";
        } else {
            $orderInfo['type'] = $type;
            $orderInfo['member_code'] = "";
            $changeType = "create_offline_scan_order";
            $orderInfo['money'] = $money ?: $payPrice;
        }
        $memberOrder = $this->addOtherOrderData($orderInfo);
        if (!$memberOrder) {
            throw new ApiException('Tạo đơn hàng thất bại');
        }
        /** @var OtherOrderStatusServices $statusService */
        $statusService = app()->make(OtherOrderStatusServices::class);
        $statusService->save([
            'oid' => $memberOrder['id'],
            'change_type' => $changeType,
            'change_message' => 'Tạo đơn hàng',
            'change_time' => time(),
            'shop_type' => $type,
        ]);
        return $memberOrder;
    }

    /**
     * Thanh toán khi nhận thẻ miễn phí
     * @param $orderInfo
     * @return bool
     */
    public function zeroYuanPayment($orderInfo)
    {
        if ($orderInfo['paid']) {
            throw new ApiException('Đơn hàng đã được thanh toán');
        }
        if ($orderInfo['member_type'] != 'free') {
            throw new ApiException('Thanh toán thất bại');
        }
        $res = $this->paySuccess($orderInfo, 'yue'); //Thanh toán bằng số dư thành công
        return $res;
    }

    /**
     * Thanh toán thẻ thành viên thành công
     * @param array $orderInfo
     * @param string $paytype
     * @return bool
     */
    public function paySuccess(array $orderInfo, string $paytype = PayServices::WEIXIN_PAY, array $other = [])
    {
        /** @var OtherOrderStatusServices $statusService */
        $statusService = app()->make(OtherOrderStatusServices::class);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $type = 'pay_member';
        $res1 = true;
        switch ($orderInfo['type']) {
            case 0:
            case 1:
            case 2:
                $type = "pay_member";
                $res1 = $userServices->setMemberOverdueTime($orderInfo['vip_day'], $orderInfo['uid'], 1, $orderInfo['member_type']);
                break;
            case 3:
                $type = "offline_scan";
                $res1 = true;
                break;
        }
        if ($paytype == PayServices::ALIAPY_PAY && isset($other['trade_no'])) {
            $updata['trade_no'] = $other['trade_no'];
        }
        $updata['paid'] = 1;
        $updata['pay_type'] = $paytype;
        $updata['pay_time'] = time();
        $orderInfo['pay_time'] = $updata['pay_time'];
        $res2 = $this->dao->update($orderInfo['id'], $updata);
        $res3 = $statusService->save([
            'oid' => $orderInfo['id'],
            'change_type' => 'pay_success',
            'change_message' => 'Người dùng thanh toán thành công',
            'shop_type' => $orderInfo['type'],
            'change_time' => time()
        ]);

        $now_money = $userServices->value(['uid' => $orderInfo['uid']], 'now_money');
        $res4 = $userBillServices->income($type, $orderInfo['uid'], $orderInfo['pay_price'], $now_money, $orderInfo['id']);
        //Gửi tin nhắn sau khi thanh toán thành công
        OtherOrderJob::dispatch([$orderInfo]);
        $orderInfo['is_channel'] = 2;
        $orderInfo['total_num'] = 1;

        if ($orderInfo['pay_type'] != 'yue') {
            /** @var CapitalFlowServices $capitalFlowServices */
            $capitalFlowServices = app()->make(CapitalFlowServices::class);
            $userInfo = $userServices->get($orderInfo['uid']);
            $orderInfo['nickname'] = $userInfo['nickname'];
            $orderInfo['phone'] = $userInfo['phone'];
            $capitalFlowServices->setFlow($orderInfo, $type);
        }
        $res = $res1 && $res2 && $res3 && $res4;
        //Cấu hình trả hoa hồng khi mua thành viên trả phí
        if (sys_config('member_brokerage', 0) == 1 && sys_config('brokerage_func_status', 0) == 1) {
            $spread_one = sys_config('is_self_brokerage') ? $orderInfo['uid'] : $userServices->getSpreadUid($orderInfo['uid']);
            $spread_two = sys_config('brokerage_level', 2) == 2 ? $userServices->getSpreadUid($spread_one, [], false) : 0;
            $spread_one_price = bcmul((string)$orderInfo['pay_price'], (string)bcdiv((string)sys_config('store_brokerage_ratio', 0), '100', 4), 2);
            $spread_two_price = bcmul((string)$orderInfo['pay_price'], (string)bcdiv((string)sys_config('store_brokerage_two', 0), '100', 4), 2);
            if ($spread_one && $spread_one_price > 0 && $userServices->checkUserPromoter($spread_one)) $this->memberBrokerage($spread_one, $spread_one_price, sys_config('is_self_brokerage') ? 'get_self_member_brokerage' : 'get_member_brokerage', $orderInfo);
            if ($spread_two && $spread_two_price > 0 && $userServices->checkUserPromoter($spread_two)) $this->memberBrokerage($spread_two, $spread_two_price, 'get_two_member_brokerage', $orderInfo);
        }

        $orderInfo['pay_type'] = $paytype;
        // Service đơn hàng Mini Program
        event('OrderShippingListener', [$type == 'pay_member' ? 'member' : 'offline_scan', $orderInfo, 3, '', '']);
        return false !== $res;
    }

    /**
     * Trả hoa hồng khi mua thành viên trả phí
     * @param $uid
     * @param $price
     * @param $type
     * @param $orderInfo
     */
    public function memberBrokerage($uid, $price, $type, $orderInfo)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($uid);
        // Số tiền sau khi trả hoa hồng cho người giới thiệu cấp trên
        $balance = bcadd($userInfo['brokerage_price'], $price, 2);
        // Thêm hoa hồng người dùng
        $res1 = $userServices->bcInc($uid, 'brokerage_price', $price, 'uid');
        if ($res1) {
            //Thời gian đóng băng
            $broken_time = intval(sys_config('extract_time'));
            $frozen_time = time() + $broken_time * 86400;
            // Thêm bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $userBrokerageServices->income($type, $uid, [
                'nickname' => $userInfo['nickname'],
                'pay_price' => floatval($orderInfo['pay_price']),
                'number' => floatval($price),
                'frozen_time' => $frozen_time
            ], $balance, $orderInfo['id']);
        }
    }

    /**
     * Sửa
     * @param $where
     * @param array $data
     * @return \crmeb\basic\BaseModel
     */
    public function update($where, array $data)
    {
        return $this->dao->update($where, $data);
    }

    /**
     * Kiểm tra dữ liệu mua thẻ thành viên
     * @param string $memberType
     * @param string $payPrice
     * @param string $type
     * @param $uid
     * @param $mcId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkPayMemberType(string $memberType, string $payPrice, string $type, $uid, $mcId)
    {
        /** @var MemberCardServices $memberCardService */
        $memberCardService = app()->make(MemberCardServices::class);
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $userInfo = $userService->get($uid);
        if ($userInfo['is_money_level'] > 0 && $userInfo['is_ever_level'] > 0) throw new ApiException('Bạn đã là thành viên vĩnh viễn, không cần mua thêm');
        $newMemberRight = $memberCardService->getMemberTypeValue();
        if (!array_key_exists($mcId, $newMemberRight)) throw new ApiException('Thẻ thành viên này tạm thời không thể mua');
        $price = $newMemberRight[$mcId]['pre_price'];
        if ($payPrice != $price || ($memberType != 'free' && $payPrice <= 0)) throw new ApiException('Tham số không hợp lệ');
        if ($memberType == 'free' && $newMemberRight[$mcId]['vip_day'] <= 0) throw new ApiException('Tham số không hợp lệ');
        switch ($memberType) {
            case "free": //Thành viên miễn phí
                $isCanGetFree = $this->isCanGetFree($uid);
                if ($isCanGetFree['is_record'] == 1) throw new ApiException('Bạn đã nhận thành viên miễn phí rồi');
                $memberPrice = 0.00; //Giá thẻ thành viên
                $isFree = 1; //Đại diện cho miễn phí
                $isPermanent = 0; //Đại diện cho không vĩnh viễn
                $overdueTime = bcadd(bcmul(abs($newMemberRight[$mcId]['vip_day']), "86400", 0), time(), 0);
                break;
            case "month":
            case "year":
            case "quarter":
            case "owner":
                $memberPrice = $price;
                $isFree = 0;
                $isPermanent = 0;
                $overdueTime = bcadd(bcmul(abs($newMemberRight[$mcId]['vip_day']), '86400', 0), time(), 0);
                break;
            case "ever":
                $memberPrice = $price;
                $isFree = 0;
                $isPermanent = 1;
                $overdueTime = 0;
                break;
            default:
                throw new ApiException('Loại thẻ thành viên này chưa mở bán');
        }
        //return compact('member_price', 'is_free', 'is_permanent', 'overdue_time', 'type');
        return [$memberPrice, $isFree, $isPermanent, $overdueTime, $type, $newMemberRight];
    }

    /**
     * Truy vấn số tiền mua thành viên theo người dùng
     * @param array $where
     * @return mixed
     */
    public function getMemberMoneyByWhere(array $where, string $sumField, string $selectType, string $group = "")
    {
        switch ($selectType) {
            case "sum":
                return $this->dao->getWhereSumField($where, $sumField);
            case "group":
                return $this->dao->getGroupField($where, $sumField, $group);
        }
    }

    /**
     * Danh sách thu ngân tại quầy
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getScanOrderList(array $where)
    {
        $where['type'] = 3;
        $where['paid'] = 1;
        [$page, $limit] = $this->getPageValue();
        if ($where['add_time']) {
            [$startTime, $endTime] = explode('-', $where['add_time']);
            if ($startTime || $endTime) {
                $startTime = strtotime($startTime);
                $endTime = strtotime($endTime . ' 23:59:59');
                $where['add_time'] = [$startTime, $endTime];
            }
        }
        if ($where['name']) {
            /** @var UserServices $userService */
            $userService = app()->make(UserServices::class);
            $userInfo = $userService->getUserInfoList(['nickname' => $where['name']], "uid");
            if ($userInfo) $where['uid'] = array_column($userInfo, 'uid');
        }
        $list = $this->dao->getScanOrderList($where, $page, $limit);
        if ($list) {
            /** @var UserServices $userService */
            $userService = app()->make(UserServices::class);
            $userInfo = $userService->getColumn([['uid', 'in', array_unique(array_column($list, 'uid'))]], 'uid,phone,nickname', 'uid');
            foreach ($list as &$v) {
                $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $v['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
                $v['phone'] = $userInfo[$v['uid']]['phone'] ?? '';
                $v['nickname'] = $userInfo[$v['uid']]['nickname'] ?? '';
                switch ($v['pay_type']) {
                    case "yue":
                        $v['pay_type'] = "Số dư";
                        break;
                    case "weixin":
                        $v['pay_type'] = "WeChat";
                        break;
                    case "alipay":
                        $v['pay_type'] = "Alipay";
                        break;
                }
                $v['true_price'] = bcsub($v['money'], $v['pay_price'], 2);
            }
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy lịch sử thành viên
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMemberRecord(array $where)
    {
        $where['type'] = [0, 1, 2];
        $where['paid'] = 1;
        if ($where['add_time']) {
            $searchTime = explode('-', $where['add_time']);
            $startTime = strtotime($searchTime[0]);
            $endTime = strtotime($searchTime[1]);
            if ($startTime == $endTime) {
                $endTime += 86400;
            }
            $where['add_time'] = [$startTime, $endTime];
        }
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getMemberRecord($where, $page, $limit);
        if ($list) {
            /** @var MemberShipServices $memberShipService */
            $memberShipService = app()->make(MemberShipServices::class);
            $shipInfo = $memberShipService->getApiList([]);
            $shipInfo = array_column($shipInfo, 'title', 'type');
            $shipInfo['owner'] = 'Tùy chỉnh';
            foreach ($list as &$v) {
                $v['member_type'] = $v['member_type'] ? $shipInfo[$v['member_type']] ?? 'Khác' : 'Khác';
                $v['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
                $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $v['overdue_time'] = date('Y-m-d H:i:s', $v['overdue_time']);
                switch ($v['pay_type']) {
                    case "yue":
                        $v['pay_type'] = "Số dư";
                        break;
                    case "weixin":
                        $v['pay_type'] = "WeChat";
                        break;
                    case "alipay":
                        $v['pay_type'] = "Alipay";
                        break;
                    case 'allinpay':
                        $v['pay_type'] = "Allinpay";
                        break;
                    case "admin":
                        $v['pay_type'] = "Tặng từ trang quản trị";
                        break;
                }
                if ($v['type'] == 0) $v['pay_type'] = "Nhận miễn phí";
                if ($v['type'] == 2) {
                    $v['pay_type'] = "Nhận bằng mã thẻ";
                    $v['member_type'] = "Kích hoạt bằng mã thẻ";
                }
                if ($v['type'] == 1 && $v['is_free'] == 1) $v['pay_type'] = "Nhận miễn phí";
                $v['user']['overdue_time'] = date('Y-m-d', $v['user']['overdue_time']) == "1970-01-01" ? "" : date('Y-m-d H:i:s', $v['user']['overdue_time']);
            }
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
