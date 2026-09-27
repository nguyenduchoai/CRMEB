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

namespace app\dao\activity\combination;

use app\dao\BaseDao;
use app\model\activity\combination\StorePink;

/**
 *
 * Class StorePinkDao
 * @package app\dao\activity
 */
class StorePinkDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StorePink::class;
    }

    /**
     * Lấy tập hợp số lượng mua chung
     * @param array $where
     * @return array
     */
    public function getPinkCount(array $where = [])
    {
        return $this->getModel()->where($where)->group('cid')->column('count(*)', 'cid');
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, int $page = 0, int $limit = 0)
    {
        return $this->search($where, false)->when($where['k_id'] != 0, function ($query) use ($where) {
            $query->whereOr('id', $where['k_id']);
        })->when(isset($where['keyword']) && $where['keyword'] != '', function ($query) use ($where) {
            $query->where('uid|nickname', 'like', '%' . $where['keyword'] . '%');
        })->with('getProduct')->when($page != 0, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('add_time desc')->select()->toArray();
    }

    /**
     * Lấy người đang trong nhóm mua chung, lấy bản ghi được tạo sớm nhất
     * @param array $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinking(array $where)
    {
        return $this->search($where)->order('add_time asc')->find();
    }

    /**
     * Lấy danh sách mua chung
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pinkList(array $where)
    {
        return $this->search($where)
            ->where('stop_time', '>', time())
            ->order('add_time desc')
            ->select()->toArray();
    }

    /**
     * Lấy số người đang mua chung
     * @param int $kid
     * @return int
     */
    public function getPinkPeople(int $kid)
    {
        return $this->count(['k_id' => $kid, 'is_refund' => 0]) + 1;
    }

    /**
     * Lấy số người đang mua chung
     * @param array $kids
     * @return int
     */
    public function getPinkPeopleCount(array $kids)
    {
        $count = $this->getModel()->whereIn('k_id', $kids)->where('is_refund', 0)->group('k_id')->column('COUNT(id) as count', 'k_id');
        $counts = [];
        foreach ($kids as &$item) {
            if (isset($count[$item])) {
                $counts[$item] = $count[$item] + 1;
            } else {
                $counts[$item] = 1;
            }
        }
        return $counts;
    }

    /**
     * Lấy danh sách mua chung thành công
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function successList(int $uid)
    {
        return $this->search(['status' => 2, 'is_refund' => 0])
//            ->where('uid', '<>', $uid)
                ->limit(10)
            ->select()->toArray();
    }


    /**
     * Lấy số lượng mua chung đã hoàn thành
     * @return float
     * @throws \ReflectionException
     */
    public function getPinkOkSumTotalNum()
    {
        return $this->sum(['status' => 2, 'is_refund' => 0], 'total_num');
    }

    /**
     * Có thể tiếp tục mua chung hay không
     * @param int $id
     * @param int $uid
     * @return int
     */
    public function isPink(int $id, int $uid)
    {
        return $this->getModel()->where('k_id|id', $id)->where('uid', $uid)->where('is_refund', 0)->count();
    }

    /**
     * Lấy một thông tin mua chung
     * @param int $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkUserOne(int $id)
    {
        return $this->search()->with('getProduct')->find($id);
    }

    /**
     * Lấy thông tin mua chung
     * @param array $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkUserList(array $where)
    {
        return $this->getModel()->where($where)->with('getProduct')->select()->toArray();
    }

    /**
     * Lấy danh sách mua chung đã kết thúc
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pinkListEnd()
    {
        return $this->getModel()->where('stop_time', '<=', time())
            ->where('status', 1)
            ->where('k_id', 0)
            ->where('is_refund', 0)
            ->field('id,people,k_id,uid,stop_time,order_id_key')->select()->toArray();
    }
}
