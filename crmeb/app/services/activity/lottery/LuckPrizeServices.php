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

namespace app\services\activity\lottery;

use app\services\BaseServices;
use app\dao\activity\lottery\LuckPrizeDao;
use app\services\activity\coupon\StoreCouponIssueServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;

/**
 *
 * Class LuckPrizeServices
 * @package app\services\activity\lottery
 */
class LuckPrizeServices extends BaseServices
{
    /**
     * @var array 1: không trúng thưởng 2: điểm thưởng 3: số dư 4: lì xì 5: phiếu giảm giá 6: sản phẩm nội bộ 7: điểm kinh nghiệm hạng 8: hạng người dùng 9: số ngày SVIP
     */
    public $prize_type = [
        '1' => 'Không trúng thưởng',
        '2' => 'Điểm thưởng',
        '3' => 'Số dư',
        '4' => 'Lì xì',
        '5' => 'Phiếu giảm giá',
        '6' => 'Sản phẩm trong cửa hàng',
        '7' => 'Điểm kinh nghiệm',
        '8' => 'Hạng người dùng',
        '9' => 'Số ngày svip'
    ];

    /**
     * Trường dữ liệu giải thưởng
     * @var array
     */
    public $prize = [
        'id' => 0,
        'type' => 1,
        'lottery_id' => 0,
        'name' => '',
        'prompt' => '',
        'image' => '',
        'chance' => 0,
        'total' => 0,
        'coupon_id' => 0,
        'product_id' => 0,
        'unique' => '',
        'num' => 1,
        'sort' => 0,
        'status' => 1,
        'is_del' => 0,
        'add_time' => 0,
        'percent' => 0,
    ];

    /**
     * LuckPrizeServices constructor.
     * @param LuckPrizeDao $dao
     */
    public function __construct(LuckPrizeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Kiểm tra dữ liệu giải thưởng
     * @param array $data
     * @return array
     */
    public function checkPrizeData(array $data)
    {
        $data = array_merge($this->prize, array_intersect_key($data, $this->prize));
        if (!isset($data['name']) || !$data['name']) {
            throw new AdminException(400538);
        }
        if (!isset($data['image']) || !$data['image']) {
            throw new AdminException(400539);
        }
        if (!isset($data['percent']) || !$data['percent']) {
            throw new AdminException('Vui lòng nhập xác suất trúng thưởng của phần thưởng');
        }
        if (!isset($data['type']) || !isset($this->prize_type[$data['type']])) {
            throw new AdminException(400541);
        }
        if (in_array($data['type'], [2, 3, 4]) && (!isset($data['num']) || !$data['num'])) {
            $msg = '';
            switch ($data['type']) {
                case 2:
                    $msg = 'Điểm thưởng';
                    break;
                case 3:
                    $msg = 'Số dư';
                    break;
                case 4:
                    $msg = 'Lì xì';
                    break;
            }
            throw new AdminException(400542, ['type' => $msg]);
        }
        if ($data['type'] == 5 && (!isset($data['coupon_id']) || !$data['coupon_id'])) {
            throw new AdminException(400543);
        }
        if ($data['type'] == 6 && (!isset($data['product_id']) || !$data['product_id'])) {
            throw new AdminException(400337);
        }
        return $data;
    }

    /**
     * Lấy tất cả giải thưởng của một chương trình quay thưởng
     * @param int $lottery_id
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLotteryPrizeList(int $lottery_id, string $field = '*')
    {
        return $this->dao->getPrizeList($lottery_id, $field);
    }


    /**
     * Giải thưởng ngẫu nhiên
     * @param array $data
     * @return array|mixed
     */
    function getLuckPrize(array $data)
    {
        $totalPercent = array_sum(array_column($data, 'percent')) * 100;
        $prize = [];
        if (!$data) return $prize;
        mt_srand();
        $random = mt_rand(1, (int)$totalPercent);
        $range = 0;
        $newPrize = array_combine(array_column($data, 'type'), $data);
        foreach ($data as $item) {
            // Chuyển tỷ lệ phần trăm sang khoảng phần nghìn
            $range += $item['percent'] * 100; // Ví dụ 12.34% -> 1234
            if ($random <= $range) {
                if (($item['type'] != 1 && $item['total'] != -1 && $item['total'] <= 0)) {
                    $prize = $newPrize[1] ?? [];
                } else {
                    $prize = $item;
                }
                break;
            }
        }
        return $prize;
    }

    /**
     * Sau khi trúng thưởng, giảm số lượng giải thưởng
     * @param int $id
     * @param array $prize
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function decPrizeNum(int $id, array $prize = [])
    {
        if (!$id) return false;
        if (!$prize) {
            $prize = $this->dao->get($id);
        }
        if (!$prize) {
            throw new ApiException(410048);
        }
        //Không phải giải không trúng thưởng thì giảm số lượng giải thưởng
        if ($prize['type'] != 1 && $prize['total'] >= 1) {
            $total = $prize['total'] - 1;
            if (!$this->dao->update($id, ['total' => $total], 'id')) {
                throw new ApiException(410070);
            }
        }
        return true;
    }
}
