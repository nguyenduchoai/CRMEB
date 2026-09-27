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

namespace app\dao\activity\lottery;

use app\dao\BaseDao;
use app\model\activity\lottery\LuckLottery;

/**
 *
 * Class LuckLotteryDao
 * @package app\dao\activity\lottery
 */
class LuckLotteryDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return LuckLottery::class;
    }

    /**
     * Tìm kiếm vòng quay may mắn
     * @param array $data
     * @param bool $search
     * @return \crmeb\basic\BaseModel
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/20
     */
    public function search(array $where = [], bool $search = false)
    {
        return parent::search($where, $search)->when(isset($where['id']) && $where['id'], function ($query) use ($where) {
            $query->where('id', $where['id']);
        })->when(isset($where['start']) && $where['start'] !== '', function ($query) use ($where) {
            $time = time();
            switch ($where['start']) {
                case 0:
                    $query->where('start_time', '>', $time)->where('status', 1);
                    break;
                case -1:
                    $query->where(function ($query1) use ($time) {
                        $query1->where('end_time', '<', $time)->whereOr('status', 0);
                    });
                    break;
                case 1:
                    $query->where('status', 1)->where(function ($query1) use ($time) {
                        $query1->where(function ($query2) use ($time) {
                            $query2->where('start_time', '<=', $time)->where('end_time', '>=', $time);
                        })->whereOr(function ($query3) {
                            $query3->where('start_time', 0)->where('end_time', 0);
                        });
                    });
                    break;
            }
        });
    }

    /**
     * Danh sách hoạt động quay thưởng
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', string $order = 'id desc', int $page = 0, int $limit = 0)
    {
        $model = $this->getModel()->when($where['is_del'] !== '', function ($query) use ($where) {
            $query->where('is_del', $where['is_del']);
        })->when($where['factor'] !== '', function ($query) use ($where) {
            $query->where('factor', $where['factor']);
        })->when($where['start'] !== '', function ($query) use ($where) {
            if ($where['start'] == 0) {
                $query->where('start_time', '>', time());
            } elseif ($where['start'] == 1) {
                $query->where('start_time', '<', time())->where('end_time', '>', time());
            } else {
                $query->where('end_time', '<', time());
            }
        })->when($where['status'] !== '', function ($query) use ($where) {
            $query->where('status', $where['status']);
        })->when(count($where['time']) == 2, function ($query) use ($where) {
            $query->where('start_time', '<=', $where['time'][0])->where('end_time', '>=', $where['time'][1]);
        })->when($where['keyword'] !== '', function ($query) use ($where) {
            $query->where('name|content', 'like', '%' . $where['keyword'] . '%');
        });
        $count = $model->count();
        $list = $model->with(['records' => function ($query) {
            $query->field([
                'lottery_id',
                'COUNT(DISTINCT uid) AS total_user',      // Tổng số người tham gia
                'COUNT(DISTINCT CASE WHEN type != 1 THEN uid END) AS wins_user', // Số người trúng thưởng
                'COUNT(*) AS total_num',                    // Tổng số lần tham gia
                'SUM(type != 1) AS wins_num',                  // Số lần trúng thưởng
            ])->group('lottery_id');
        }])->field($field)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order($order)->select()->toArray();
        return compact('count', 'list');
    }

    /**
     * Lấy một hoạt động
     * @param int $id
     * @param string $field
     * @param array|string[] $with
     * @param bool $is_doing
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLottery(int $id, string $field = '*', array $with = ['prize'], bool $is_doing = false)
    {
        $where = ['id' => $id];
        $where['is_del'] = 0;
        if ($is_doing) $where['start'] = 1;
        return $this->search($where)->field($field)->when($with, function ($query) use ($with) {
            $query->with($with);
        })->find();
    }

    /**
     * Lấy một bản ghi dữ liệu quay thưởng theo loại quay thưởng
     * @param int $factor
     * @param string $field
     * @param array|string[] $with
     * @param bool $is_doing
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFactorLottery(int $factor = 1, string $field = '*', array $with = ['prize'], bool $is_doing = false)
    {
        $where = ['factor' => $factor, 'is_del' => 0, 'is_use' => 1];
        if ($is_doing) $where['start'] = 1;
        return $this->search($where)->field($field)->when($with, function ($query) use ($with) {
            $query->with($with);
        })->order('id desc')->find();
    }
}
