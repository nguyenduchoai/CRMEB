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

use app\services\BaseServices;
use app\dao\user\UserBillDao;
use app\services\order\StoreOrderServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use think\Exception;
use crmeb\services\CacheService;
use think\facade\Log;

/**
 *
 * Class UserBillServices
 * @package app\services\user
 * @method takeUpdate(int $uid, int $id) Cập nhật trạng thái nhận hàng
 * @method sum(array $where, string $field) Tính tổng
 * @method count(array $where) Tính số dòng (số bản ghi)
 * @method getTotalSum(array $where) Tính tổng số sản phẩm trong đơn hàng theo một điều kiện
 * @method getBillSum(array $where) Lấy tổng số theo một điều kiện
 * @method getList(array $where, string $field, int $page, int $limit, $typeWhere = [], $order = 'id desc') Lấy tổng số theo một điều kiện
 * @method getUserRefundPriceList(array $time, string $timeType, string $str, string $field = 'add_time', array $with = []) Lấy số tiền hoàn trả, nhóm theo thời gian
 */
class UserBillServices extends BaseServices
{

    /**
     * Mẫu bản ghi người dùng
     * @var array[]
     */
    protected $incomeData = [
        'pay_give_integral' => [
            'title' => 'Tặng điểm thưởng khi mua sản phẩm',
            'category' => 'integral',
            'type' => 'product_gain',
            'mark' => 'Mua sản phẩm được tặng {%num%} điểm thưởng',
            'status' => 1,
            'pm' => 1
        ],
        'order_give_integral' => [
            'title' => 'Tặng điểm thưởng khi đặt hàng',
            'category' => 'integral',
            'type' => 'gain',
            'mark' => 'Đặt hàng được tặng {%num%} điểm thưởng',
            'status' => 1,
            'pm' => 1
        ],
        'order_give_exp' => [
            'title' => 'Đặt hàng được tặng điểm kinh nghiệm',
            'category' => 'exp',
            'type' => 'gain',
            'mark' => 'Đặt hàng được tặng {%num%} điểm kinh nghiệm',
            'status' => 1,
            'pm' => 1
        ],
        'get_brokerage' => [
            'title' => 'Nhận hoa hồng giới thiệu',
            'category' => 'now_money',
            'type' => 'brokerage',
            'mark' => '{%nickname%} đã chi tiêu thành công {%pay_price%}đ, thưởng hoa hồng giới thiệu {%number%}',
            'status' => 1,
            'pm' => 1
        ],
        'get_two_brokerage' => [
            'title' => 'Nhận hoa hồng giới thiệu',
            'category' => 'now_money',
            'type' => 'brokerage',
            'mark' => 'Người được giới thiệu cấp 2 {%nickname%} đã chi tiêu thành công {%pay_price%}đ, thưởng hoa hồng giới thiệu {%number%}',
            'status' => 1,
            'pm' => 1
        ],
        'get_user_brokerage' => [
            'title' => 'Nhận hoa hồng giới thiệu người dùng',
            'category' => 'now_money',
            'type' => 'brokerage_user',
            'mark' => 'Giới thiệu thành công người dùng: {%nickname%}, thưởng hoa hồng giới thiệu {%number%}',
            'status' => 1,
            'pm' => 1
        ],
        'pay_product_refund' => [
            'title' => 'Hoàn tiền sản phẩm',
            'category' => 'now_money',
            'type' => 'pay_product_refund',
            'mark' => 'Hoàn tiền đơn hàng {%payType%} {%number%}đ',
            'status' => 1,
            'pm' => 1
        ],
        'integral_refund' => [
            'title' => 'Trừ điểm thưởng đã tặng khi đặt hàng',
            'category' => 'integral',
            'type' => 'order_deduction',
            'mark' => 'Mua sản phẩm thất bại, thu hồi {%num%} điểm thưởng đã tặng',
            'status' => 1,
            'pm' => 0
        ],
        'order_integral_refund' => [
            'title' => 'Hoàn lại điểm thưởng đã dùng khi đặt hàng',
            'category' => 'integral',
            'type' => 'integral_refund',
            'mark' => 'Mua sản phẩm thất bại, hoàn lại {%num%} điểm thưởng',
            'status' => 1,
            'pm' => 1
        ],
        'pay_product_integral_back' => [
            'title' => 'Hoàn điểm thưởng sản phẩm',
            'category' => 'integral',
            'type' => 'pay_product_integral_back',
            'mark' => 'Đơn hàng hoàn {%num%} điểm thưởng vào tài khoản điểm thưởng của người dùng',
            'status' => 1,
            'pm' => 1
        ],
        'deduction' => [
            'title' => 'Khấu trừ bằng điểm thưởng',
            'category' => 'integral',
            'type' => 'deduction',
            'mark' => 'Mua sản phẩm dùng {%number%} điểm thưởng để khấu trừ {%deductionPrice%}đ',
            'status' => 1,
            'pm' => 0
        ],
        'pay_product' => [
            'title' => 'Mua sản phẩm bằng số dư',
            'category' => 'now_money',
            'type' => 'pay_product',
            'mark' => 'Thanh toán {%num%}đ bằng số dư để mua sản phẩm',
            'status' => 1,
            'pm' => 0
        ],
        'pay_money' => [
            'title' => 'Mua sản phẩm',
            'category' => 'now_money',
            'type' => 'pay_money',
            'mark' => 'Thanh toán {%num%}đ để mua sản phẩm',
            'status' => 1,
            'pm' => 0
        ],
        'system_add' => [
            'title' => 'Hệ thống cộng số dư',
            'category' => 'now_money',
            'type' => 'system_add',
            'mark' => 'Hệ thống cộng {%num%}đ',
            'status' => 1,
            'pm' => 1
        ],
        'brokerage_to_nowMoney' => [
            'title' => 'Rút hoa hồng về số dư',
            'category' => 'now_money',
            'type' => 'extract',
            'mark' => 'Rút hoa hồng về số dư {%num%}đ',
            'status' => 1,
            'pm' => 0
        ],
        'pay_member' => [
            'title' => 'Mua gói thành viên',
            'category' => 'now_money',
            'type' => 'pay_member',
            'mark' => 'Thanh toán {%num%}đ để mua gói thành viên',
            'status' => 1,
            'pm' => 0
        ],
        'offline_scan' => [
            'title' => 'Thu ngân tại quầy',
            'category' => 'now_money',
            'type' => 'offline_scan',
            'mark' => 'Thanh toán tại quầy {%num%}đ',
            'status' => 1,
            'pm' => 0
        ],
        'lottery_use_integral' => [
            'title' => 'Dùng điểm thưởng tham gia quay thưởng',
            'category' => 'integral',
            'type' => 'lottery_use',
            'mark' => 'Tham gia quay thưởng dùng {%num%} điểm thưởng',
            'status' => 1,
            'pm' => 0
        ],
        'lottery_give_integral' => [
            'title' => 'Tặng điểm thưởng khi trúng quay thưởng',
            'category' => 'integral',
            'type' => 'lottery_add',
            'mark' => 'Quay trúng thưởng, được tặng {%num%} điểm thưởng',
            'status' => 1,
            'pm' => 1
        ],
        'lottery_use_money' => [
            'title' => 'Tham gia quay thưởng dùng số dư',
            'category' => 'now_money',
            'type' => 'lottery_use',
            'mark' => 'Tham gia quay thưởng dùng số dư {%num%}',
            'status' => 1,
            'pm' => 0
        ],
        'lottery_give_money' => [
            'title' => 'Quay trúng thưởng, được tặng số dư',
            'category' => 'now_money',
            'type' => 'lottery_add',
            'mark' => 'Quay trúng thưởng, được tặng số dư {%num%}',
            'status' => 1,
            'pm' => 1
        ],
        'storeIntegral_use_integral' => [
            'title' => 'Đổi điểm lấy sản phẩm',
            'category' => 'integral',
            'type' => 'storeIntegral_use',
            'mark' => 'Đổi sản phẩm tại cửa hàng đổi điểm, dùng {%num%} điểm thưởng',
            'status' => 1,
            'pm' => 0
        ],
    ];

    /**
     * UserBillServices constructor.
     * @param UserBillDao $dao
     */
    public function __construct(UserBillDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * TODO Lấy tổng số bản ghi người dùng
     * @param $uid
     * @param string $category
     * @param array $type
     * @return mixed
     */
    public function getRecordCount(int $uid, $category = 'now_money', $type = [], $time = '', $pm = false)
    {

        $where = [];
        $where['uid'] = $uid;
        $where['category'] = $category;
        $where['status'] = 1;

        if (is_string($type) && strlen(trim($type))) {
            $where['type'] = explode(',', $type);
        }
        if ($time) {
            $where['time'] = $time;
        }
        if ($pm) {
            $where['pm'] = 0;
        }
        return $this->dao->getBillSumColumn($where);
    }

    public function getUsersBokerageSum(array $where, $time = 0)
    {
        $where_data = [
            'type' => ['brokerage', 'brokerage_user'],
            'category' => 'now_money',
            'status' => 1,
            'pm' => $where['pm'] ?? '',
            'uid' => $where['uid'] ?? '',
            'time' => $where['time'] ?? 0
        ];
        if ($time) $where_data['time'] = $time;
        return $this->dao->getBillSumColumn($where_data);
    }

    /**
     * Tổng hoa hồng của một người dùng
     * @param int $uid
     * @return float
     */
    public function getUserBillBrokerageSum(int $uid, array $type = ['brokerage', 'brokerage_user'], $time = '')
    {
        $where = ['uid' => $uid, 'category' => 'now_money'];
        if ($type) $where['type'] = $type;
        if ($time) $where['time'] = $time;
        return $this->dao->getBillSum($where);
    }

    /**
     * Lấy tổng số hoa hồng của người dùng|tất cả
     * @param int $uid
     * @param array $where_time
     * @return float
     */
    public function getBrokerageSum(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'now_money', 'type' => ['system_add', 'pay_product', 'extract', 'pay_product_refund', 'system_sub'], 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        return $this->dao->getBillSum($where);
    }

    public function getBrokerageNumSum($link_ids = [])
    {
        $where = ['category' => 'now_money', 'type' => ['brokerage', 'brokerage_user']];
        if ($link_ids) $where['link_id'] = $link_ids;
        return $this->dao->getBillSum($where);
    }

    /**
     * Lấy tổng số hoa hồng của người dùng|tất cả
     * @param int $uid
     * @param array $where_time
     * @return float
     */
    public function getBrokerageCount(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'now_money', 'type' => ['system_add', 'pay_product', 'extract', 'pay_product_refund', 'system_sub'], 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        return $this->dao->getBillCount($where);
    }

    /**
     * Danh sách biến động số dư của người dùng|tất cả
     * @param int $uid
     * @param string $field
     * @return array
     */
    public function getBrokerageList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => 'now_money', 'type' => ['pay_money', 'system_add', 'pay_product_refund', 'pay_member', 'offline_scan', 'lottery_add', 'system_sub']];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        foreach ($list as &$item) {
            $value = array_filter($this->incomeData, function ($value) use ($item) {
                if ($item['type'] == $value['type']) {
                    return $item['title'];
                }
            });
            $item['type_title'] = $value[$item['type']]['title'] ?? 'Loại không xác định';
        }
        return compact('list', 'count');
    }

    /**
     * Lấy tổng số lần nạp tiền của người dùng
     * @param int $uid
     * @return float
     */
    public function getRechargeSum(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'now_money', 'type' => 'recharge', 'pm' => 1, 'status' => 1];//Người dùng nạp tiền vào số dư
        $where_system = ['category' => 'now_money', 'type' => 'system_add', 'pm' => 1, 'status' => 1];//Hệ thống tặng số dư
        if ($uid) $where['uid'] = $where_system['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_system['add_time'] = $where_time;
        $sum1 = $this->dao->getBillSum($where);
        $sum2 = $this->dao->getBillSum($where_system);
        return bcadd((string)$sum1, (string)$sum2, 2);
    }

    /**
     * Danh sách nạp tiền của người dùng|tất cả
     * @param int $uid
     * @param string $field
     * @return array
     */
    public function getRechargeList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => 'now_money', 'type' => 'recharge'];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy tổng điểm thưởng của người dùng
     * @param int $uid
     * @return float
     */
    public function getIntegralSum(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'integral', 'type' => ['sign', 'system_add'], 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        return $this->dao->getBillSum($where);
    }

    /**
     * Lấy tổng số lần nhận điểm thưởng của người dùng
     * @param int $uid
     * @return float
     */
    public function getIntegralCount(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'integral', 'type' => ['sign', 'system_add'], 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        return $this->dao->getBillCount($where);
    }

    /**
     * Lấy danh sách điểm thưởng
     * @param int $uid
     * @param array $where_time
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getIntegralList(int $uid = 0, array $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => 'integral'];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        foreach ($list as &$item) {
            $item['number'] = intval($item['number']);
            $item['is_frozen'] = $item['frozen_time'] > time() ? 1 : 0;
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy tổng số lần điểm danh của người dùng
     * @param int $uid
     * @return float
     */
    public function getSignlSum(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'integral', 'type' => 'sign', 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        return $this->dao->getBillSum($where);
    }

    /**
     * Lấy tổng số lần điểm danh của người dùng
     * @param int $uid
     * @return float
     */
    public function getSignCount(int $uid = 0, $where_time = [])
    {
        $where = ['category' => 'integral', 'type' => 'sign', 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        return $this->dao->getBillCount($where);
    }

    /**
     * Lấy danh sách điểm danh
     * @param int $uid
     * @param array $where_time
     * @param string $field
     * @return array
     */
    public function getSignList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => 'integral', 'type' => 'sign'];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['add_time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Tổng điểm kinh nghiệm
     * @param int $uid
     * @param array $where_time
     * @return float
     */
    public function getExpSum(int $uid = 0, $where_time = [])
    {
        $where = ['category' => ['exp'], 'pm' => 1, 'status' => 1];
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['time'] = $where_time;
        return $this->dao->getBillSum($where);
    }

    /**
     * Lấy danh sách tất cả điểm kinh nghiệm
     * @param int $uid
     * @param array $where_time
     * @param string $field
     * @return array
     */
    public function getExpList(int $uid = 0, $where_time = [], string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $where = ['category' => ['exp']];
        $where['status'] = 1;
        if ($uid) $where['uid'] = $uid;
        if ($where_time) $where['time'] = $where_time;
        $list = $this->dao->getList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }


    /**
     * Tăng hoa hồng
     * @param int $uid
     * @param string $type
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function incomeNowMoney(int $uid, string $type, array $data)
    {
        $data['uid'] = $uid;
        $data['category'] = 'now_money';
        $data['type'] = $type;
        $data['pm'] = 1;
        $data['status'] = 1;
        $data['add_time'] = time();
        if (!$this->dao->save($data))
            throw new AdminException(400655);
        return true;
    }

    /**
     * Trừ hoa hồng
     * @param int $uid
     * @param string $type
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function expendNowMoney(int $uid, string $type, array $data)
    {
        $data['uid'] = $uid;
        $data['category'] = 'now_money';
        $data['type'] = $type;
        $data['pm'] = 0;
        $data['status'] = 1;
        $data['add_time'] = time();
        if (!$this->dao->save($data))
            throw new AdminException(400655);
        return true;
    }

    /**
     * Tăng điểm thưởng
     * @param int $uid
     * @param string $type
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function incomeIntegral(int $uid, string $type, array $data)
    {
        $data['uid'] = $uid;
        $data['category'] = 'integral';
        $data['type'] = $type;
        $data['pm'] = 1;
        $data['status'] = 1;
        $data['add_time'] = time();
        if (!$this->dao->save($data))
            throw new AdminException(400655);
        return true;
    }

    /**
     * Trừ điểm thưởng
     * @param int $uid
     * @param string $type
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function expendIntegral(int $uid, string $type, array $data)
    {
        $data['uid'] = $uid;
        $data['category'] = 'integral';
        $data['type'] = $type;
        $data['pm'] = 0;
        $data['status'] = 1;
        $data['add_time'] = time();
        if (!$this->dao->save($data))
            throw new AdminException(400655);
        return true;
    }


    /**
     * Ghi bản ghi người dùng
     * @param string $type Loại ghi
     * @param int $uid
     * @param int|string|array $number
     * @param int|string $balance
     * @param int $link_id
     * @return bool|mixed
     */
    public function income(string $type, int $uid, $number, $balance, $link_id)
    {
        $data = $this->incomeData[$type] ?? null;
        if (!$data) {
            return true;
        }
        $data['uid'] = $uid;
        $data['balance'] = $balance ?? 0;
        $data['link_id'] = $link_id;
        if (is_array($number)) {
            $key = array_keys($number);
            $key = array_map(function ($item) {
                return '{%' . $item . '%}';
            }, $key);
            $value = array_values($number);
            $data['number'] = $number['number'] ?? 0;
            $data['mark'] = str_replace($key, $value, $data['mark']);
        } else {
            $data['number'] = $number;
            $data['mark'] = str_replace(['{%num%}'], $number, $data['mark']);
        }
        $data['add_time'] = time();
        if ($type == 'pay_give_integral' || $type == 'order_give_integral') {
            $integral_frozen = sys_config('integral_frozen');
            if ($integral_frozen) {
                $data['frozen_time'] = $data['add_time'] + ($integral_frozen * 86400);
            }
        }

        return $this->dao->save($data);
    }

    /**
     * Tăng điểm kinh nghiệm khi mời người dùng mới
     * @param int $spreadUid
     */
    public function inviteUserIncExp(int $spreadUid)
    {
        if (!$spreadUid) {
            return false;
        }
        //Hạng người dùng có mở không
        if (!sys_config('member_func_status', 1)) {
            return false;
        }
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $spread_user = $userService->getUserInfo($spreadUid);
        if (!$spread_user) {
            return false;
        }
        $exp_num = sys_config('invite_user_exp', 0);
        if ($exp_num) {
            $userService->incField($spreadUid, 'exp', (int)$exp_num);
            $data = [];
            $data['uid'] = $spreadUid;
            $data['number'] = $exp_num;
            $data['category'] = 'exp';
            $data['type'] = 'invite_user';
            $data['title'] = $data['mark'] = 'Thưởng mời người mới';
            $data['balance'] = (int)$spread_user['exp'] + (int)$exp_num;
            $data['pm'] = 1;
            $data['status'] = 1;
            $this->dao->save($data);
        }
        //Kiểm tra hạng thành viên
        try {
            //Event nâng cấp người dùng
            event('UserLevelListener', [$spreadUid]);
        } catch (\Throwable $e) {
            Log::error('Nâng hạng thành viên thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Lấy type
     * @param array $where
     * @param string $filed
     */
    public function getBillType(array $where)
    {
        return $this->dao->getType($where);
    }

    /**
     * Loại giao dịch (tiền)
     */
    public function bill_type()
    {
        $where = [];
        $where['not_type'] = ['gain', 'system_sub', 'deduction', 'sign'];
        $where['not_category'] = ['exp', 'integral'];
        return CacheService::remember('user_type_list', function () use ($where) {
            return ['list' => $this->getBillType($where)];
        }, 600);
    }

    /**
     * Lấy danh sách giao dịch (dòng tiền)
     * @param array $where
     * @param string $field
     * @return array
     */
    public function getBillList(array $where, string $field = '*', $is_page = true)
    {
        $where_data = [];
        if (isset($where['uid']) && $where['uid'] != '') {
            $where_data['uid'] = $where['uid'];
        }
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $where_data['time'] = str_replace('-', '/', $where['start_time']) . ' - ' . str_replace('-', '/', $where['end_time']);
        }
        if (isset($where['category']) && $where['category'] != '') {
            $where_data['category'] = $where['category'];
        }
        if (isset($where['type']) && $where['type'] != '') {
            $where_data['type'] = $where['type'];
            if ($where['type'] == 'brokerage') $where_data['pm'] = 1;
        }
        $where_data['not_category'] = ['integral', 'exp', 'share'];
        $where_data['not_type'] = $where['type'] == 'pay_product' ? ['gain', 'system_sub', 'deduction', 'sign'] : ['gain', 'system_sub', 'deduction', 'sign', 'pay_product'];
        if (isset($where['nickname']) && $where['nickname'] != '') {
            $where_data['like'] = $where['nickname'];
        }
        if (isset($where['excel']) && $where['excel'] != '') {
            $where_data['excel'] = $where['excel'];
        } else {
            $where_data['excel'] = 0;
        }
        [$page, $limit] = $this->getPageValue($is_page);
        $data = $this->dao->getBillList($where_data, $field, $page, $limit);
        foreach ($data as &$item) {
            $item['nickname'] = $item['user']['nickname'] ?? '';
            unset($item['user']);
        }
        $count = $this->dao->count($where_data);
        return compact('data', 'count');
    }

    /**
     * Lấy danh sách hoa hồng
     * @param array $where
     * @param int $limit
     * @return array
     */
    public function getCommissionList(array $where, int $limit = 0)
    {
        $where_data = [];
        $where_data['time'] = $where['time'];
        if (isset($where['nickname']) && $where['nickname']) {
            $where_data[] = ['u.nickname|u.uid', 'LIKE', "%$where[nickname]%"];
        }
        if (isset($where['price_max']) && isset($where['price_min'])) {
            if ($where['price_max'] != '' && $where['price_min'] != '') {
                $where_data[] = ['u.brokerage_price', 'between', [$where['price_min'], $where['price_max']]];
            } elseif ($where['price_min'] != '' && $where['price_max'] == '') {
                $where_data[] = ['u.brokerage_price', '>=', $where['price_min']];
            } elseif ($where['price_min'] == '' && $where['price_max'] != '') {
                $where_data[] = ['u.brokerage_price', '<=', $where['price_max']];
            }
        }
        $order_string = '';
        $order_arr = ['asc', 'desc'];
        if (isset($where['sum_number']) && in_array($where['sum_number'], $order_arr)) {
            $order_string .= ',income ' . $where['sum_number'];
        }
        if (isset($where['brokerage_price']) && in_array($where['brokerage_price'], $order_arr)) {
            $order_string .= ',u.brokerage_price ' . $where['brokerage_price'];
        }
        if ($order_string) {
            $order_string = trim($order_string, ',');
        }
        /** @var UserUserBrokerageServices $userUserBrokerage */
        $userUserBrokerage = app()->make(UserUserBrokerageServices::class);
        [$count, $list] = $userUserBrokerage->getBrokerageList($where_data, 'b.type,b.pm,sum(IF(b.pm = 1 AND b.type <> \'extract_fail\', b.number, 0)) as income,sum(IF(b.pm = 0, b.number, 0)) as pay,u.nickname,u.phone,u.uid,u.now_money,u.brokerage_price,b.add_time as time', $order_string, $limit);
        $uids = array_unique(array_column($list, 'uid'));
        /** @var UserExtractServices $userExtract */
        $userExtract = app()->make(UserExtractServices::class);
        $extractSumList = $userExtract->getUsersSumList($uids);
        foreach ($list as &$item) {
            $item['sum_number'] = $item['income'];
            $item['nickname'] = $item['nickname'] . " | " . ($item['phone'] ? $item['phone'] . " | " : '') . $item['uid'];
            $item['extract_price'] = $extractSumList[$item['uid']] ?? 0;
            $item['time'] = $item['time'] ? date('Y-m-d H:i:s', $item['time']) : '';
        }
        return compact('count', 'list');
    }

    public function user_info(int $uid)
    {
        /** @var UserServices $user */
        $user = app()->make(UserServices::class);
        $user_info = $user->getUserInfo($uid, 'nickname,spread_uid,now_money,add_time,brokerage_price');
        if (!$user_info) {
            throw new AdminException(400119);
        }
        $user_info = $user_info->toArray();
        $user_info['number'] = $user_info['brokerage_price'];
        $user_info['add_time'] = date('Y-m-d H:i:s', $user_info['add_time']);
        $user_info['spread_name'] = $user_info['spread_uid'] ? $user->getUserInfo((int)$user_info['spread_uid'], 'nickname', true)['nickname'] ?? '' : '';
        return compact('user_info');
    }

    /**
     * Ghi số lần chia sẻ
     * @param int $uid uid người dùng
     * @param int $cd Thời gian hồi (cooldown)
     * @return Boolean
     * */
    public function setUserShare(int $uid, $cd = 300)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new AdminException(400119);
        }
        $cachename = 'Share_' . $uid;
        if (CacheService::get($cachename)) {
            return false;
        }
        $data = ['title' => 'Lịch sử chia sẻ của người dùng', 'uid' => $uid, 'category' => 'share', 'type' => 'share', 'number' => 0, 'link_id' => 0, 'balance' => 0, 'mark' => date('Y-m-d H:i:s', time()) . ': người dùng chia sẻ'];
        if (!$this->dao->save($data)) {
            throw new AdminException(400656);
        }
        CacheService::set($cachename, 1, $cd);
        return true;
    }

    /**
     * Lấy danh sách rút hoa hồng
     * @param int $uid
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBillOneList(int $uid, array $where)
    {
        $where['uid'] = $uid;
        $data = $this->getBillList($where);
        foreach ($data['data'] as &$item) {
            $item['_add_time'] = $item['add_time'] ?? '';
        }
        return $data;
    }

    /**
     * Lấy danh sách điểm thưởng
     * @param array $where
     * @param string $field
     * @return array
     */
    public function getPointList(array $where, string $field = '*', $is_page = true)
    {
        $where_data = [];
        $where_data['category'] = 'integral';
        if (isset($where['uid']) && $where['uid'] != '') {
            $where_data['uid'] = $where['uid'];
        }
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $where_data['time'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        if (isset($where['type']) && $where['type'] != '') {
            $where_data['type'] = $where['type'];
        }
        if (isset($where['nickname']) && $where['nickname'] != '') {
            $where_data['like'] = $where['nickname'];
        }
        if (isset($where['excel']) && $where['excel'] != '') {
            $where_data['excel'] = $where['excel'];
        } else {
            $where_data['excel'] = 0;
        }
        [$page, $limit] = $this->getPageValue($is_page);
        $list = $this->dao->getBillList($where_data, $field, $page, $limit);
        foreach ($list as &$item) {
            $item['nickname'] = $item['user']['nickname'] ?? '';
            $item['number'] = intval($item['number']);
            $item['balance'] = intval($item['balance']);
            unset($item['user']);
        }
        $count = $this->dao->count($where_data);
        return compact('list', 'count');
    }

    /**
     * Thông tin phần đầu điểm thưởng
     * @param array $where
     * @return array[]
     */
    public function getUserPointBadgelist(array $where)
    {
        $data = [];
        $where_data = [];
        $where_data['category'] = 'integral';
        if ($where['start_time'] != '' && $where['end_time'] != '') {
            $where_data['time'] = $where['start_time'] . ' - ' . $where['end_time'];
        }
        if (isset($where['nickname']) && $where['nickname'] != '') {
            $where_data['like'] = $where['nickname'];
        }
        $data['SumIntegral'] = intval($this->dao->getBillSumColumn($where_data + ['pm' => 1, 'integral_type' => 'get']));
        $where_data['type'] = 'sign';
        $data['CountSign'] = $this->dao->getUserSignPoint($where_data);
        $data['SumSign'] = intval($this->dao->getBillSumColumn($where_data));
        $where_data['type'] = ['deduction', 'system_sub'];
        $data['SumDeductionIntegral'] = intval($this->dao->getBillSumColumn($where_data));
        return [
            [
                'col' => 6,
                'count' => $data['SumIntegral'],
                'name' => 'Tổng điểm thưởng (điểm)',
            ],
            [
                'col' => 6,
                'count' => $data['CountSign'],
                'name' => 'Số lượt điểm danh của khách hàng (lượt)',
            ],
            [
                'col' => 6,
                'count' => $data['SumSign'],
                'name' => 'Điểm thưởng tặng khi điểm danh (điểm)',
            ],
            [
                'col' => 6,
                'count' => $data['SumDeductionIntegral'],
                'name' => 'Điểm thưởng đã dùng (điểm)',
            ],
        ];
    }

    /**
     * Hoàn hoa hồng
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderRefundBrokerageBack(int $id, string $orderId)
    {
        $brokerageList = $this->dao->getUserBillList([
            'category' => 'now_money',
            'type' => 'brokerage',
            'link_id' => $id,
            'pm' => 1
        ]);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $brokerages = $userServices->getColumn([['uid', 'in', array_column($brokerageList, 'uid')]], 'brokerage_price', 'uid');
        $userBillData = [];
        $res = true;
        foreach ($brokerageList as $item) {
            $usermoney = $brokerages[$item['uid']] ?? 0;
            if ($item['number'] > $usermoney) {
                $item['number'] = $usermoney;
            }
            $res = $res && $userServices->bcDec($item['uid'], 'brokerage_price', (string)$item['number'], 'uid');
            $userBillData[] = [
                'title' => 'Thu hồi hoa hồng do hoàn tiền',
                'uid' => $item['uid'],
                'pm' => 0,
                'add_time' => time(),
                'category' => 'now_money',
                'type' => 'brokerage',
                'number' => $item['number'],
                'link_id' => $id,
                'balance' => bcsub((string)$usermoney, (string)$item['number'], 2),
                'mark' => 'Hoàn tiền đơn hàng, trừ hoa hồng' . floatval($item['number']) . 'đ'
            ];
        }
        if ($userBillData) {
            $res = $res && $this->dao->saveAll($userBillData);
        }
        /** @var UserBrokerageFrozenServices $services */
        $services = app()->make(UserBrokerageFrozenServices::class);
        $services->updateFrozen($orderId);
        return $res;
    }

    /**
     * Xếp hạng hoa hồng
     * @param string $time
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function brokerageRankList(string $time = 'week')
    {
        $where = [];
        $where['category'] = 'now_money';
        $where['type'] = ['brokerage', 'brokerage_user'];
        if ($time) {
            $where['time'] = $time;
        }
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->brokerageRankList($where, $page, $limit);
        foreach ($list as $key => &$item) {
            if (!isset($item['user']) || !$item['user']) {
                unset($list['$key']);
                continue;
            }
            $item['nickname'] = $item['user']['nickname'] ?? '';
            $item['avatar'] = $item['user']['avatar'] ?? '';
            if ($item['brokerage_price'] == '0.00' || $item['brokerage_price'] == 0 || !$item['brokerage_price']) {
                unset($list[$key]);
            }
            unset($item['user']);
        }
        return $list;
    }

    /**
     * Lấy xếp hạng người dùng
     * @param int $uid
     * @param string $time
     */
    public function getUserBrokerageRank(int $uid, string $time = 'week')
    {
        $where = [];
        $where['category'] = 'now_money';
        $where['type'] = ['brokerage', 'brokerage_user'];
        if ($time) {
            $where['time'] = $time;
        }
        $list = $this->dao->brokerageRankList($where, 0, 0);
        $position_tmp_one = array_column($list, 'uid');
        $position_tmp_two = array_column($list, 'brokerage_price', 'uid');
        if (!in_array($uid, $position_tmp_one)) {
            $position = 0;
        } else {
            if ($position_tmp_two[$uid] == 0.00) {
                $position = 0;
            } else {
                $position = array_search($uid, $position_tmp_one) + 1;
            }
        }
        return $position;
    }


    /**
     * Dữ liệu giới thiệu    Hoa hồng hôm qua   Số tiền đã rút lũy kế  Hoa hồng hiện tại
     * @param int $uid
     * @return mixed
     */
    public function commission(int $uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if (!$userServices->getUserInfo($uid)) {
            throw new ApiException(100026);
        }
        /** @var UserExtractServices $userExtract */
        $userExtract = app()->make(UserExtractServices::class);
        $data = [];
        $data['uid'] = $uid;
        $data['pm'] = 1;
        $data['commissionSum'] = $this->getUsersBokerageSum($data);
        $data['pm'] = 0;
        $data['commissionRefund'] = $this->getUsersBokerageSum($data);
        $data['commissionCount'] = $data['commissionSum'] > $data['commissionRefund'] ? bcsub((string)$data['commissionSum'], (string)$data['commissionRefund'], 2) : 0.00;
        $data['lastDayCount'] = $this->getUsersBokerageSum($data, 'yesterday');//Hoa hồng ngày hôm qua
        $data['extractCount'] = $userExtract->getUserExtract($uid);//Tổng số tiền đã rút

        return $data;
    }

    /**
     * Dữ liệu trang xếp hạng hoa hồng ở frontend
     * @param int $uid
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function brokerage_rank(int $uid, $type)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        if (!$userService->getUserInfo($uid)) {
            throw new ApiException(100026);
        }
        return [
            'rank' => $this->brokerageRankList($type),
            'position' => $this->getUserBrokerageRank($uid, $type)
        ];
    }

    /**
     * @param $uid
     * @param $type
     * @return array
     */
    public function getUserBillList(int $uid, int $type)
    {
        $where = [];
        $where['uid'] = $uid;
        $where['category'] = 'now_money';
        switch ((int)$type) {
            case 0:
                $where['type'] = ['recharge', 'pay_money', 'system_add', 'pay_product_refund', 'system_sub', 'pay_member', 'offline_scan', 'lottery_add'];
                break;
            case 1:
                $where['type'] = ['pay_money', 'pay_member', 'offline_scan', 'user_recharge_refund'];
                break;
            case 2:
                $where['type'] = ['recharge', 'system_add', 'lottery_add'];
                break;
            case 3:
                $where['type'] = ['brokerage', 'brokerage_user'];
                break;
            case 4:
                $where['type'] = ['extract'];
                /** @var UserExtractServices $userExtractService */
                $userExtractService = app()->make(UserExtractServices::class);
                $userExtract = $userExtractService->getColumn(['uid' => $uid], 'fail_msg', 'id');
                break;
        }
        $field = 'FROM_UNIXTIME(add_time,"%Y-%m") as time,group_concat(id SEPARATOR ",") ids';
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getUserBillListByGroup($where, $field, 'time', $page, $limit);
        $data = [];
        if ($list) {
            $listIds = array_column($list, 'ids');
            $ids = [];
            foreach ($listIds as $id) {
                $ids = array_merge($ids, explode(',', $id));
            }
            $info = $this->dao->getColumn([['id', 'in', $ids]], 'FROM_UNIXTIME(add_time,"%Y-%m-%d %H:%i") as add_time,title,number,pm,link_id', 'id');
            foreach ($list as $item) {
                $value['time'] = $item['time'];
                $id = explode(',', $item['ids']);
                array_multisort($id, SORT_DESC);
                $value['list'] = [];
                foreach ($id as $v) {
                    if (isset($info[$v])) {
                        if ($info[$v]['pm'] == 1 && $type == 4) $info[$v]['fail_msg'] = $userExtract[$info[$v]['link_id']];
                        $value['list'][] = $info[$v];
                    }
                }
                array_push($data, $value);
            }
        }
        return $data;
    }

    /**
     * Giới thiệu - Tổng hoa hồng/rút tiền
     * @param int $uid
     * @param $type 3 Hoa hồng  4 rút tiền
     * @return mixed
     */
    public function spread_count(int $uid, $type)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        if (!$userService->getUserInfo($uid)) {
            throw new ApiException(100026);
        }
        $count = 0;
        if ($type == 3) {
            $count1 = $this->getRecordCount($uid, 'now_money', ['brokerage', 'brokerage_user']);
            $count2 = $this->getRecordCount($uid, 'now_money', ['brokerage', 'brokerage_user'], '', true);
            $count = $count1 - $count2;
        } else if ($type == 4) {
            /** @var UserExtractServices $userExtract */
            $userExtract = app()->make(UserExtractServices::class);
            $count = $userExtract->getUserExtract($uid);//Tổng số tiền đã rút (lũy kế)
        }
        return $count ?: 0;
    }

    /**
     * Đơn hàng giới thiệu
     * @param Request $request
     * @return mixed
     */
    public function spread_order(int $uid, array $data)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        if (!$userService->getUserInfo($uid, 'uid')) {
            throw new ApiException(100026);
        }
        $result = ['list' => [], 'time' => [], 'count' => 0];
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        [$page, $limit] = $this->getPageValue();
        $time = [];
        $where = ['paid' => 1, 'type' => 6, 'spread_or_uid' => $uid, 'pid' => 0, 'refund_status' => 0];
        $list = $storeOrderServices->getlist($where, ['id,order_id,uid,add_time,spread_uid,status,spread_two_uid,one_brokerage,two_brokerage,pay_price,pid'], $page, $limit, ['split']);
        $result['count'] = $storeOrderServices->count($where);
        $time_data = [];
        if ($list) {
            $uids = array_unique(array_column($list, 'uid'));
            $userInfos = $userService->getColumn([['uid', 'in', $uids]], 'uid,avatar,nickname', 'uid');
            foreach ($list as &$item) {
                $item['avatar'] = $userInfos[$item['uid']]['avatar'] ?? '';
                $item['nickname'] = $userInfos[$item['uid']]['nickname'] ?? '';
                $item['number'] = $item['spread_uid'] == $uid ? $item['one_brokerage'] : $item['two_brokerage'];
                $item['time'] = $item['add_time'] ? date('Y-m-d H:i', $item['add_time']) : '';
                $item['time_key'] = $item['add_time'] ? date('Y-m', $item['add_time']) : '';
                $item['type'] = in_array($item['status'], [2, 3]) ? 'brokerage' : 'number';
                foreach ($item['split'] as $key => $items) {
                    $item['children'][] = [
                        'order_id' => $items['order_id'],
                        'number' => $item['spread_uid'] == $uid ? $items['one_brokerage'] : $items['two_brokerage'],
                        'type' => in_array($item['status'], [2, 3]) ? 'brokerage' : 'number',
                    ];
                    unset($item['split'][$key]);
                }
            }
            $times = array_unique(array_column($list, 'time_key'));
            $time_data = [];
            $i = 0;
            foreach ($times as $time) {
                $time_data[$i]['time'] = $time;
                $time_data[$i]['count'] = $storeOrderServices->getMonthCount($where + ['pid' => 0], $time);
                $i++;
            }
        }
        $result['list'] = $list;
        $result['time'] = $time_data;
        return $result;
    }


    /**Truy vấn số tiền nạp của người dùng
     * @param array $where
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

    /**
     * Đơn hàng của đại lý khu vực/đại lý
     * @param $uid
     * @return array
     */
    public function divisionOrder($uid)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $userInfo = $userService->getUserInfo($uid);
        if (!$userInfo) {
            throw new ApiException(100026);
        }
        $division_type = $userInfo['division_type'];
        [$page, $limit] = $this->getPageValue();
        $where = ['paid' => 1, 'type' => 1, 'pid' => 0];
        if ($division_type == 1) {
            $where = $where + ['division_id' => $uid, 'division_brokerage_greater' => 0];
        } elseif ($division_type == 2) {
            $where = $where + ['agent_id' => $uid, 'agent_brokerage_greater' => 0];
        }

        $list = $storeOrderServices->getlist($where, ['id,order_id,uid,add_time,spread_uid,division_id,agent_id,status,spread_two_uid,one_brokerage,two_brokerage,agent_brokerage,division_brokerage,pay_price,pid'], $page, $limit, ['split']);
        $result['count'] = $storeOrderServices->count($where);
        $time_data = [];
        if ($list) {
            $uids = array_unique(array_column($list, 'uid'));
            $userInfos = $userService->getColumn([['uid', 'in', $uids]], 'uid,avatar,nickname', 'uid');
            foreach ($list as &$item) {
                $item['avatar'] = $userInfos[$item['uid']]['avatar'] ?? '';
                $item['nickname'] = $userInfos[$item['uid']]['nickname'] ?? '';
                $item['time'] = $item['add_time'] ? date('Y-m-d H:i', $item['add_time']) : '';
                $item['time_key'] = $item['add_time'] ? date('Y-m', $item['add_time']) : '';
                $item['type'] = in_array($item['status'], [2, 3]) ? 'brokerage' : 'number';
                if ($division_type == 1) {
                    $item['number'] = $item['division_brokerage'];
                } elseif ($division_type == 2) {
                    $item['number'] = $item['agent_brokerage'];
                }
            }
            $times = array_unique(array_column($list, 'time_key'));
            $time_data = [];
            $i = 0;
            foreach ($times as $time) {
                $time_data[$i]['time'] = $time;
                $time_data[$i]['count'] = $storeOrderServices->getMonthCount($where + ['pid' => 0], $time);
                $i++;
            }
        }
        $result['list'] = $list;
        $result['time'] = $time_data;
        return $result;
    }
}
