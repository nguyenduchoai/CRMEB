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

namespace app\dao\product\product;

use app\dao\BaseDao;
use app\model\product\product\StoreCategory;

/**
 * Class StoreCategoryDao
 * @package app\dao\product\product
 */
class StoreCategoryDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreCategory::class;
    }

    /**
     * Lấy danh sách danh mục
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where)
    {
        return $this->search($where)->with('children')->order('sort desc,id desc')->select()->toArray();
    }

    /**
     *
     * @param array $where
     * @param array $field
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTierList(array $where = [], array $field = ['*'])
    {
        return $this->search($where)->field($field)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * Danh sách chọn danh mục cấp trên khi thêm/sửa
     * @param array $where
     * @return array
     * @throws \ReflectionException
     */
    public function getMenus(array $where)
    {
        return $this->search($where)->order('sort desc,id desc')->column('cate_name,id');
    }

    /**
     * Lấy danh mục theo id
     * @param string $cateIds
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCateArray(string $cateIds)
    {
        return $this->search(['id' => $cateIds])->field('cate_name,id')->select()->toArray();
    }

    /**
     * Danh sách danh mục dùng riêng cho trang frontend
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCategory()
    {
        return $this->getModel()->with('children')->where('is_show', 1)->where('pid', 0)->order('sort desc,id desc')->hidden(['add_time', 'is_show', 'sort', 'children.sort', 'children.add_time', 'children.pid', 'children.is_show'])->select()->toArray();
    }

    /**
     * Lấy id cấp trên theo id danh mục
     * @param array $cateId
     * @return array
     */
    public function cateIdByPid(array $cateId)
    {
        return $this->getModel()->whereIn('id', $cateId)->column('pid');
    }

    /**
     * Lấy danh mục cấp 2 hiển thị ở trang chủ  sắp xếp giảm dần theo mặc định
     * @param int $limit
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function byIndexList($limit = 4, $field = 'id,cate_name,pid,pic')
    {
        return $this->getModel()->where('pid', '>', 0)->where('is_show', 1)->field($field)->order('sort DESC')->limit($limit)->select()->toArray();
    }

    /**
     * Lấy tập hợp gồm danh mục cấp 1 và cấp 2
     * @param $cateId
     * @return mixed
     */
    public function getCateParentAndChildName(string $cateId)
    {
        return $this->getModel()->alias('c')->join('StoreCategory b', 'b.id = c.pid')
            ->where('c.id', 'IN', $cateId)->field('c.cate_name as two,b.cate_name as one,c.id')
            ->select()->toArray();
    }

    /**
     * Lấy id danh mục cấp 1 có sản phẩm theo số lượng
     * @param $page
     * @param $limit
     * @return array
     */
    public function getCid($page, $limit)
    {
        return $this->getModel()
            ->where('is_show', 1)
            ->where('pid', 0)
            ->where('id', 'in', function ($query) {
                $query->name('store_product_cate')->where('status', 1)->group('cate_pid')->field('cate_pid')->select()->toArray();
            })
            ->page($page, $limit)
            ->order('sort DESC,id DESC')
            ->select()->toArray();
    }

    /**
     * Lấy số lượng danh mục cấp 1 có sản phẩm theo số lượng
     * @param $page
     * @param $limit
     * @return int
     */
    public function getCidCount()
    {
        return $this->getModel()
            ->where('is_show', 1)
            ->where('pid', 0)
            ->where('id', 'in', function ($query) {
                $query->name('store_product_cate')->where('status', 1)->group('cate_pid')->field('cate_pid')->select()->toArray();
            })
            ->count();
    }

    /**
     * Lấy tất cả danh mục (bản thân và cấp dưới) theo id danh mục
     * @param $id
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAllById($id, string $field = 'id')
    {
        if (is_array($id)) {
            return $this->getModel()->where(function ($query) use ($id) {
                $query->whereIn('id', $id)->whereOr('pid', 'in', $id);
            })->where('is_show', 1)->field($field)->select()->toArray();
        } else {
            return $this->getModel()->where(function ($query) use ($id) {
                $query->where('id', $id)->whereOr('pid', $id);
            })->where('is_show', 1)->field($field)->select()->toArray();
        }
    }

    /**
     * Lấy tất cả danh mục cấp 2 có thể tìm kiếm
     * @param array $where
     * @param string $field
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getALlByIndex(array $where, string $field = 'id,cate_name,pid,pic', $limit = 0)
    {
        $pid = $where['pid'] ?? -1;
        return $this->getModel()->where('is_show', 1)->field($field)
            ->when(in_array($pid, [0, -1]), function ($query) use ($pid) {
                switch ($pid) {
                    case -1://Tất cả cấp 1
                        $query->where('pid', 0);
                        break;
                    case 0://Tất cả cấp 2
                        $query->where('pid', '>', 0);
                }
            })->when((int)$pid > 0, function ($query) use ($pid) {
                $query->where('pid', $pid);
            })->when(isset($where['name']) && $where['name'], function ($query) use ($where) {
                $query->whereLike('id|cate_name', '%' . $where['name'] . '%');
            })->when($limit > 0, function ($query) use ($limit) {
                $query->limit($limit);
            })->order('sort DESC')->select()->toArray();
    }
}
