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

namespace app\services\activity\lottery;

use app\services\BaseServices;
use app\dao\activity\lottery\LuckLotteryDao;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\product\product\StoreProductServices;
use app\services\user\UserBillServices;
use app\services\user\UserLabelRelationServices;
use app\services\user\UserLabelServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;

/**
 *
 * Class LuckLotteryServices
 * @package app\services\activity\lottery
 * @method getFactorLottery(int $factor = 1, string $field = '*', array $with = ['prize'], bool $is_doing = true)
 */
class LuckLotteryServices extends BaseServices
{
    /**
     * Hình thức quay thưởng, số lượng giải thưởng
     * @var int[]
     */
    protected $lottery_type = [
        '1' => 8 //Lưới 9 ô
    ];
    /**
     * Loại hình quay thưởng
     * @var string[]
     */
    protected $lottery_factor = [
        '1' => 'Quay bằng điểm thưởng',
//        '2' => 'Quay thưởng bằng số dư',
        '3' => 'Thanh toán đơn hàng',
        '4' => 'Đánh giá đơn hàng',
//        '5' => 'Quay thưởng khi theo dõi OA WeChat'
    ];

    /**
     * LuckLotteryServices constructor.
     * @param LuckLotteryDao $dao
     */
    public function __construct(LuckLotteryDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        if ($where['time'] == '') {
            $where['time'] = [];
        } else {
            $time = explode('-', $where['time']);
            $where['time'] = [strtotime($time[0]), strtotime($time[1]) + 86399];
        }
        $data = $this->dao->getList($where, '*', 'id desc', $page, $limit);
        foreach ($data['list'] as &$item) {
            $item['lottery_type'] = $this->lottery_factor[$item['factor']] ?? 'Không xác định';
            if ($item['start_time'] > time()) {
                $item['status_name'] = 'Chưa bắt đầu';
                $item['lottery_status'] = 0;
            } else if (bcadd((string)$item['end_time'], '86400') < time()) {
                $item['status_name'] = 'Đã kết thúc';
                $item['lottery_status'] = 2;
            } else {
                $item['status_name'] = 'Đang diễn ra';
                $item['lottery_status'] = 1;
            }
            $item['start_time'] = $item['start_time'] ? date('Y-m-d H:i:s', $item['start_time']) : '';
            $item['end_time'] = $item['end_time'] ? date('Y-m-d 23:59:59', $item['end_time']) : '';
            if (!count($item['records'])) {
                $item['records_total_user'] = 0;
                $item['records_wins_user'] = 0;
                $item['records_total_num'] = 0;
                $item['records_wins_num'] = 0;
            } else {
                $item['records_total_user'] = $item['records'][0]['total_user'];
                $item['records_wins_user'] = $item['records'][0]['wins_user'];
                $item['records_total_num'] = $item['records'][0]['total_num'];
                $item['records_wins_num'] = $item['records'][0]['wins_num'];
            }
        }
        return $data;
    }

    /**
     * Lấy chi tiết quay thưởng
     * @param int $id
     * @return array|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLotteryInfo(int $id)
    {
        $lottery = $this->dao->getLottery($id, '*', ['prize']);
        if (!$lottery) {
            throw new ApiException('Chương trình không tồn tại hoặc đã bị xóa');
        }
        $lottery = $lottery->toArray();
        if (isset($lottery['prize']) && $lottery['prize']) {
            $product_ids = array_unique(array_column($lottery['prize'], 'product_id'));
            $coupon_ids = array_unique(array_column($lottery['prize'], 'coupon_id'));
            /** @var StoreProductServices $productServices */
            $productServices = app()->make(StoreProductServices::class);
            $products = $productServices->getColumn([['id', 'in', $product_ids]], 'id,store_name,image', 'id');
            /** @var StoreCouponIssueServices $couponServices */
            $couponServices = app()->make(StoreCouponIssueServices::class);
            $coupons = $couponServices->getColumn([['id', 'in', $coupon_ids]], 'id,coupon_title', 'id');
            foreach ($lottery['prize'] as &$prize) {
                $prize['coupon_title'] = $prize['goods_image'] = '';
                if ($prize['type'] == 6) {
                    $prize['goods_image'] = $products[$prize['product_id']]['image'] ?? '';
                }
                if ($prize['type'] == 5) {
                    $prize['coupon_title'] = $coupons[$prize['coupon_id']]['coupon_title'] ?? '';
                }
            }
        }
        return $lottery;
    }

    /**
     * Lấy dữ liệu theo loại
     * @param int $factor
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getlotteryFactorInfo(int $factor)
    {
        $lottery = $this->dao->getFactorLottery($factor, '*', ['prize']);
        if (!$lottery) {
            return [];
        }
        $lottery = $lottery->toArray();
        if (isset($lottery['prize']) && $lottery['prize']) {
            $product_ids = array_unique(array_column($lottery['prize'], 'product_id'));
            $coupon_ids = array_unique(array_column($lottery['prize'], 'coupon_id'));
            /** @var StoreProductServices $productServices */
            $productServices = app()->make(StoreProductServices::class);
            $products = $productServices->getColumn([['id', 'in', $product_ids]], 'id,store_name,image', 'id');
            /** @var StoreCouponIssueServices $couponServices */
            $couponServices = app()->make(StoreCouponIssueServices::class);
            $coupons = $couponServices->getColumn([['id', 'in', $coupon_ids]], 'id,coupon_title', 'id');
            foreach ($lottery['prize'] as &$prize) {
                $prize['coupon_title'] = $prize['goods_image'] = '';
                if ($prize['type'] == 6) {
                    $prize['goods_image'] = $products[$prize['product_id']]['image'] ?? '';
                }
                if ($prize['type'] == 5) {
                    $prize['coupon_title'] = $coupons[$prize['coupon_id']]['coupon_title'] ?? '';
                }
            }
        }
        foreach ($lottery['user_level'] as &$item) {
            $item = (int)$item;
        }
        /** @var UserLabelServices $userLabelServices */
        $userLabelServices = app()->make(UserLabelServices::class);
        $lottery['user_label'] = !empty($lottery['user_label']) ? $userLabelServices->getLabelList(['ids' => $lottery['user_label']], ['id', 'label_name']) : [];
        return $lottery;
    }

    /**
     * Thêm chương trình quay thưởng và giải thưởng
     * @param array $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(array $data)
    {
        $prizes = $data['prize'];
        $total = array_sum(array_column($prizes, 'percent'));
        if ($total != 100) {
            throw new AdminException('Tổng xác suất trúng của các phần thưởng không bằng 100%, vui lòng kiểm tra!');
        }
        $prize_num = $this->lottery_type[1];
        if (count($prizes) != $prize_num) {
            throw new AdminException('Vui lòng thêm sản phẩm');
        }
        unset($data['prize']);
        return $this->transaction(function () use ($data, $prizes) {
            $time = time();
            $data['add_time'] = $time;
            if (!$lottery = $this->dao->save($data)) {
                throw new AdminException('Thêm chương trình quay thưởng thất bại');
            }
            if ($data['status']) {
                $this->setStatus((int)$lottery->id, $data['status']);
            }
            /** @var LuckPrizeServices $luckPrizeServices */
            $luckPrizeServices = app()->make(LuckPrizeServices::class);
            $data = [];
            $sort = 1;
            $prizeStatus = false;
            foreach ($prizes as $prize) {
                if (isset($prize['type']) && $prize['type'] == 1) $prizeStatus = true;
                $prize = $luckPrizeServices->checkPrizeData($prize);
                $prize['lottery_id'] = $lottery->id;
                unset($prize['id']);
                $prize['add_time'] = $time;
                $prize['sort'] = $sort;
                $data[] = $prize;
                $sort++;
            }
            if (!$prizeStatus) {
                throw new AdminException('Phải thiết lập ít nhất một ô không trúng thưởng');
            }
            if (!$luckPrizeServices->saveAll($data)) {
                throw new AdminException('Thêm chương trình quay thưởng thất bại');
            }
            return true;
        });
    }

    /**
     * Sửa chương trình quay thưởng và giải thưởng
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit(int $id, array $data)
    {
        $lottery = $this->dao->getLottery($id);
        if (!$lottery) {
            throw new AdminException('Chương trình quay thưởng không tồn tại');
        }
        $newPrizes = $data['prize'];
        $percentArr = array_column($newPrizes, 'percent');
        $allPercent = 0;
        foreach ($percentArr as $k => $v) {
            $allPercent = bcadd((string)$allPercent, (string)$v, 2);
        }
        if ($allPercent != 100) {
            throw new AdminException('Tổng xác suất trúng của các phần thưởng không bằng 100%, vui lòng kiểm tra!');
        }
        unset($data['prize'], $data['id']);
        $prize_num = $this->lottery_type[1];
        if (count($newPrizes) != $prize_num) {
            throw new AdminException('Vui lòng thêm sản phẩm');
        }
        if ($data['attends_user'] == 1) {
            $data['user_label'] = $data['user_level'] = [];
            $data['is_svip'] = -1;
        }
        /** @var LuckPrizeServices $luckPrizeServices */
        $luckPrizeServices = app()->make(LuckPrizeServices::class);
        $prizes = $luckPrizeServices->getLotteryPrizeList($id);
        return $this->transaction(function () use ($id, $lottery, $data, $newPrizes, $prizes, $luckPrizeServices) {
            $updateIds = array_column($newPrizes, 'id');
            $oldIds = array_column($prizes, 'id');
            $delIds = array_merge(array_diff($oldIds, $updateIds));
            $insert = [];
            $time = time();
            $sort = 1;
            $prizeStatus = false;
            foreach ($newPrizes as $prize) {
                if (isset($prize['type']) && $prize['type'] == 1) $prizeStatus = true;
                $prize = $luckPrizeServices->checkPrizeData($prize);
                $prize['sort'] = $sort;
                if (isset($prize['id']) && $prize['id']) {
                    if (!$prize['lottery_id']) {
                        throw new AdminException('Tham số không hợp lệ');
                    }
                    if (!$luckPrizeServices->update($prize['id'], $prize, 'id')) {
                        throw new AdminException('Sửa thất bại');
                    }
                } else {
                    unset($prize['id']);
                    $prize['lottery_id'] = $id;
                    $prize['add_time'] = $time;
                    $prize['sort'] = $sort;
                    $insert[] = $prize;
                }
                $sort++;
            }
            if (!$prizeStatus) {
                throw new AdminException('Phải thiết lập ít nhất một ô không trúng thưởng');
            }
            if ($insert) {
                if (!$luckPrizeServices->saveAll($insert)) {
                    throw new AdminException('Thêm thất bại');
                }
            }
            if ($delIds) {
                if (!$luckPrizeServices->update([['id', 'in', $delIds]], ['is_del' => 1])) {
                    throw new AdminException('Xóa thất bại');
                }
            }
            if (!$this->dao->update($id, $data)) {
                throw new AdminException('Sửa thất bại');
            }
            //Đang bán
            if (!$lottery['status'] && $data['status']) {
                $this->setStatus($id, $data['status']);
            }
            return true;
        });
    }

    /**
     * Lấy số lượt quay thưởng còn lại của người dùng trong một chương trình
     * @param int $uid
     * @param int $lottery_id
     * @param array $userInfo
     * @param array $lottery
     * @return false|float|int|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLotteryNum(int $uid, int $lottery_id, array $userInfo = [], array $lottery = [])
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if (!$userInfo) {
            $userInfo = $userServices->getUserInfo($uid);
        }
        if (!$userInfo) {
            throw new ApiException('Người dùng không tồn tại');
        }
        if (!$lottery) {
            $lottery = $this->dao->getLottery($lottery_id, '*', [], true);
        }
        if (!$lottery) {
            throw new ApiException('Chương trình không tồn tại hoặc đã bị xóa');
        }
        //Loại hình quay thưởng: 1: điểm thưởng 2: số dư 3: đặt hàng thanh toán thành công 4: đánh giá đơn hàng 5: giới thiệu người mới
        switch ($lottery['factor']) {
            case 1:
                /** @var UserBillServices $userBillServices */
                $userBillServices = app()->make(UserBillServices::class);
                $usable_integral = bcsub((string)$userInfo['integral'], (string)$userBillServices->getBillSum(['uid' => $userInfo['uid'], 'is_frozen' => 1]), 0);
                return $usable_integral > 0 && $lottery['factor_num'] > 0 ? floor($usable_integral / $lottery['factor_num']) : 0;
            case 2:
                return $userInfo['now_money'] > 0 && $lottery['factor_num'] > 0 ? floor($userInfo['now_money'] / $lottery['factor_num']) : 0;
            case 3:
                return $this->getCacheLotteryNum($uid, 'order');
            case 4:
                return $this->getCacheLotteryNum($uid, 'comment');
            case 5:
                return $userInfo['spread_lottery'] ?? 0;
            default:
                throw new ApiException('Hiện chưa có chương trình thuộc loại này');
        }
    }

    /**
     * Kiểm tra điều kiện quay thưởng của người dùng (hạng người dùng, thành viên trả phí, nhãn người dùng)
     * @param int $uid
     * @param int $lottery_id
     * @param array $userInfo
     * @param array $lottery
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkoutUserAuth(int $uid, int $lottery_id, array $userInfo = [], array $lottery = [])
    {
        if (!$userInfo) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userInfo = $userServices->getUserInfo($uid);
        }
        if (!$userInfo) {
            throw new ApiException('Người dùng không tồn tại');
        }
        if (!$lottery) {
            $lottery = $this->dao->getLottery($lottery_id, '*', [], true);
        }
        if (!$lottery) {
            throw new ApiException('Chương trình không tồn tại hoặc đã bị xóa');
        }
        //Một phần người dùng tham gia
        if ($lottery['attends_user'] == 2) {
            //Hạng người dùng
            if ($lottery['user_level'] && !in_array($userInfo['level'], $lottery['user_level'])) {
                throw new ApiException('Bạn tạm thời không thể tham gia chương trình này');
            }
            //Nhãn người dùng
            if ($lottery['user_label']) {
                /** @var UserLabelRelationServices $userlableRelation */
                $userlableRelation = app()->make(UserLabelRelationServices::class);
                $user_labels = $userlableRelation->getUserLabels($uid);
                if (!array_intersect($lottery['user_label'], $user_labels)) {
                    throw new ApiException('Bạn tạm thời không thể tham gia chương trình này');
                }
            }
            //Có phải thành viên trả phí không
            if ($lottery['is_svip'] != -1) {
                if (($lottery['is_svip'] == 1 && $userInfo['is_money_level'] <= 0) || ($lottery['is_svip'] == 0 && $userInfo['is_money_level'] > 0)) {
                    throw new ApiException('Bạn tạm thời không thể tham gia chương trình này');
                }
            }
        }
        return true;
    }

    /**
     * Quay thưởng
     * @param int $uid
     * @param int $lottery_id
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function luckLottery(int $uid, int $lottery_id, $channel_type)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        if (!$userInfo) {
            throw new ApiException('Người dùng không tồn tại');
        }
        $lottery = $this->dao->getLottery($lottery_id, '*', [], true);
        if (!$lottery) {
            throw new ApiException('Chương trình không tồn tại hoặc đã bị xóa');
        }
        $userInfo = $userInfo->toArray();
        $lottery = $lottery->toArray();
        //Xác thực danh tính người dùng
        $this->checkoutUserAuth($uid, $lottery_id, $userInfo, $lottery);

        /** @var LuckPrizeServices $lotteryPrizeServices */
        $lotteryPrizeServices = app()->make(LuckPrizeServices::class);
        $lotteryPrize = $lotteryPrizeServices->getPrizeList($lottery_id);
        if (!$lotteryPrize) {
            throw new ApiException('Trạng thái chương trình không hợp lệ, vui lòng liên hệ quản trị viên');
        }
        if ($this->getLotteryNum($uid, $lottery_id, $userInfo, $lottery) < 1) {
            //Loại hình quay thưởng: 1: điểm thưởng 2: số dư 3: đặt hàng thanh toán thành công 4: đánh giá đơn hàng 5: giới thiệu người mới
            switch ($lottery['factor']) {
                case 1:
                    throw new ApiException('Không đủ điểm thưởng khả dụng, không còn lượt quay thưởng');
                case 2:
                    throw new ApiException('Số dư không đủ, không còn lượt quay thưởng');
                case 3:
                    throw new ApiException('Mua sản phẩm để nhận thêm lượt quay thưởng');
                case 4:
                    throw new ApiException('Đánh giá đơn hàng đã hoàn thành để nhận thêm lượt quay thưởng');
                case 5:
                    throw new ApiException('Mời thêm bạn bè để nhận lượt quay thưởng');
                default:
                    throw new ApiException('Hiện chưa có chương trình thuộc loại này');
            }
        }
        return $this->transaction(function () use ($uid, $lotteryPrize, $userInfo, $lottery, $channel_type) {
            /** @var LuckPrizeServices $luckPrizeServices */
            $luckPrizeServices = app()->make(LuckPrizeServices::class);
            //Quay thưởng ngẫu nhiên
            $prize = $luckPrizeServices->getLuckPrize($lotteryPrize);
            if (!$prize) {
                throw new ApiException('Trạng thái chương trình không hợp lệ, vui lòng liên hệ quản trị viên');
            }
            //Trúng thưởng thì trừ điểm thưởng, số dư
            $this->lotteryFactor($uid, $userInfo, $lottery);
            //Trúng thưởng thì giảm số lượng giải thưởng
            $luckPrizeServices->decPrizeNum($prize['id'], $prize);
            /** @var LuckLotteryRecordServices $lotteryRecordServices */
            $lotteryRecordServices = app()->make(LuckLotteryRecordServices::class);
            //Trúng thưởng thì ghi lại bản ghi
            $record = $lotteryRecordServices->insertPrizeRecord($uid, $prize, $userInfo, $channel_type);
            //Không phải sản phẩm nội bộ thì nhận thưởng trực tiếp
            if ($prize['type'] != 6) {
                $lotteryRecordServices->receivePrize($uid, (int)$record->id);
            }
            $prize['lottery_record_id'] = $record->id;

            //Sự kiện tùy chỉnh - người dùng quay thưởng
            event('CustomEventListener', ['user_lottery', [
                'uid' => $uid,
                'lottery_id' => $prize['lottery_id'],
                'prize_id' => $prize['id'],
                'record_id' => $record['id'],
                'lottery_time' => date('Y-m-d H:i:s'),
            ]]);

            return $prize;
        });
    }

    /**
     * Quay thưởng tiêu tốn thì trừ điểm thưởng, số dư... của người dùng
     * @param int $uid
     * @param array $userInfo
     * @param array $lottery
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function lotteryFactor(int $uid, array $userInfo, array $lottery)
    {
        if (!$userInfo || !$lottery) {
            return true;
        }
        //Loại hình quay thưởng: 1: điểm thưởng 2: số dư 3: đặt hàng thanh toán thành công 4: đánh giá đơn hàng 5: giới thiệu người mới
        switch ($lottery['factor']) {
            case 1:
                if ($userInfo['integral'] > $lottery['factor_num']) {
                    $integral = bcsub((string)$userInfo['integral'], (string)$lottery['factor_num'], 0);
                } else {
                    $integral = 0;
                }
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                /** @var UserBillServices $userBillServices */
                $userBillServices = app()->make(UserBillServices::class);
                $userBillServices->income('lottery_use_integral', $uid, $lottery['factor_num'], $integral, $lottery['id']);
                if (!$userServices->update($uid, ['integral' => $integral], 'uid')) {
                    throw new ApiException('Trừ điểm thưởng của người dùng khi quay thưởng thất bại');
                }
                break;
            case 2:
                if ($userInfo['now_money'] >= $lottery['factor_num']) {
                    $now_money = bcsub((string)$userInfo['now_money'], (string)$lottery['factor_num'], 2);
                } else {
                    throw new ApiException('Quay thưởng thất bại, số dư không đủ');
                }
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                /** @var UserMoneyServices $userMoneyServices */
                $userMoneyServices = app()->make(UserMoneyServices::class);
                $userMoneyServices->income('lottery_use_money', $uid, $lottery['factor_num'], $now_money, $lottery['id']);
                if (!$userServices->update($uid, ['now_money' => $now_money], 'uid')) {
                    throw new ApiException('Trừ số dư của người dùng khi quay thưởng thất bại');
                }
                break;
            case 3:
            case 4:
                //Xóa bộ nhớ đệm số lượt quay thưởng
                $this->delCacheLotteryNum($uid, $lottery['factor'] == 3 ? 'order' : 'comment');
                break;
            case 5:
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $spread_lottery = 0;
                if ($userInfo['spread_lottery'] > 1) {
                    $spread_lottery = $userInfo['spread_lottery'] - 1;
                }
                if (!$userServices->update($uid, ['spread_lottery' => $spread_lottery], 'uid')) {
                    throw new ApiException('Trừ lượt quay thưởng nhận được từ giới thiệu của người dùng thất bại');
                }
                break;
            default:
                throw new ApiException('Hiện chưa có chương trình thuộc loại này');
        }
        return true;
    }

    /**
     * Xóa
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delLottery(int $id)
    {
        $lottery = $this->dao->getLottery($id);
        if ($lottery) {
            $res = $this->dao->update(['id' => $id], ['is_del' => 1]);
            if (!$res) {
                throw new AdminException('Xóa thất bại');
            }
        }
        return true;
    }

    /**
     * Đặt trạng thái chương trình quay thưởng
     * @param int $id
     * @param $status
     * @return false|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setStatus(int $id, $status)
    {
        if (!$id) return false;
        $lottery = $this->dao->getLottery($id, 'id,factor');
        if (!$lottery) return false;
        return $this->dao->update($id, ['status' => $status], 'id');
    }

    /**
     *  Lưu tạm số lượt quay thưởng khi đặt hàng thanh toán, đánh giá
     * @param int $uid
     * @param string $type
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setCacheLotteryNum(int $uid, string $type = 'order')
    {
        $factor = $type == 'order' ? 3 : 4;
        $lottery = $this->dao->getFactorLottery($factor, 'id,factor_num', ['prize'], true);
        if (!$lottery || !$lottery['factor_num']) {
            return true;
        }
        $key = 'user_' . $type . '_luck_lottery_' . $uid;
        return CacheService::set($key, $lottery['factor_num'], 120);
    }

    /**
     * Lấy ra số lượt quay thưởng có được từ đặt hàng thanh toán, đánh giá
     * @param int $uid
     * @param string $type
     * @return int|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getCacheLotteryNum(int $uid, string $type = 'order')
    {
        $key = 'user_' . $type . '_luck_lottery_' . $uid;
        $num = CacheService::get($key);
        return empty($num) ? 0 : $num;
    }

    /**
     * Xóa bộ nhớ đệm sau khi quay thưởng
     * @param int $uid
     * @param string $type
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delCacheLotteryNum(int $uid, string $type = 'order')
    {
        $key = 'user_' . $type . '_luck_lottery_' . $uid;
        $num = $this->getCacheLotteryNum($uid, $type);
        if ($num > 1) {
            CacheService::set($key, $num - 1, 120);
        } else {
            CacheService::delete($key);
        }
        return true;
    }

    public function factorList()
    {
        $list = $this->dao->selectList(['status' => 1, 'is_del' => 0], 'id,name,factor,is_use')->toArray();
        $data = [
            'info' => [
                'point' => '',
                'pay' => '',
                'evaluate' => ''
            ],
            'point' => [],
            'pay' => [],
            'evaluate' => []
        ];
        foreach ($list as $item) {
            if ($item['factor'] == 1) {
                $data['point'][] = $item;
                if ($data['info']['point'] == '') {
                    $data['info']['point'] = $item['is_use'] ? $item['id'] : '';
                }
            } elseif ($item['factor'] == 3) {
                $data['pay'][] = $item;
                if ($data['info']['pay'] == '') {
                    $data['info']['pay'] = $item['is_use'] ? $item['id'] : '';
                }
            } else {
                $data['evaluate'][] = $item;
                if ($data['info']['evaluate'] == '') {
                    $data['info']['evaluate'] = $item['is_use'] ? $item['id'] : '';
                }
            }
        }
        return $data;
    }

    public function factorUse($data)
    {
        $this->dao->update(['is_del' => 0], ['is_use' => 0]);
        if ($data['point']) {
            $this->dao->update(['factor' => 1, 'id' => $data['point']], ['is_use' => 1]);
        }
        if ($data['pay']) {
            $this->dao->update(['factor' => 3, 'id' => $data['pay']], ['is_use' => 1]);
        }
        if ($data['evaluate']) {
            $this->dao->update(['factor' => 4, 'id' => $data['evaluate']], ['is_use' => 1]);
        }
        return true;
    }
}
