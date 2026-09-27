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
use app\model\activity\combination\StoreCombination;

/**
 *
 * Class StoreCombinationDao
 * @package app\dao\activity
 */
class StoreCombinationDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreCombination::class;
    }

    /**
     * Tìm kiếm
     * @param array $where
     * @param bool $search
     * @return \crmeb\basic\BaseModel|mixed|\think\Model
     * @throws \ReflectionException
     */
    public function search(array $where = [], bool $search = false)
    {
        return parent::search($where, $search)->when(isset($where['pinkIngTime']), function ($query) use ($where) {
            $time = time();
            [$startTime, $stopTime] = is_array($where['pinkIngTime']) ? $where['pinkIngTime'] : [$time, $time];
            $query->where('start_time', '<=', $startTime)->where('stop_time', '>=', $stopTime);
        })->when(isset($where['storeProductId']), function ($query) {
            $query->where('product_id', 'IN', function ($query) {
                $query->name('store_product')->where('is_del', 0)->field('id');
            });
        })->when(isset($where['sid']) && $where['sid'], function ($query) use ($where) {
            $query->whereIn('product_id', function ($query) use ($where) {
                $query->name('store_product_cate')->where('cate_id', $where['sid'])->field('product_id')->select();
            });
        })->when(isset($where['cid']) && $where['cid'], function ($query) use ($where) {
            $query->whereIn('product_id', function ($query) use ($where) {
                $query->name('store_product_cate')->whereIn('cate_id', function ($query) use ($where) {
                    $query->name('store_category')->where('pid', $where['cid'])->field('id')->select();
                })->field('product_id')->select();
            });
        })->when(isset($where['id']) && $where['id'], function ($query) use ($where) {
            $query->where('id', $where['id']);
        });
    }

    /**
     * Lấy số lượng bản ghi theo điều kiện chỉ định
     * @param array $where
     * @param bool $search
     * @return int
     * @throws \ReflectionException
     */
    public function count(array $where = [], bool $search = true)
    {
        return $this->search($where, $search)->count();
    }

    /**
     * Danh sách sản phẩm mua chung
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
        return $this->search($where)->where('is_del', 0)->with('getPrice')
            ->when(isset($where['start_status']) && $where['start_status'] !== '', function ($query) use ($where) {
                $time = time();
                switch ($where['start_status']) {
                    case -1:
                        $query->where('stop_time', '<', $time);
                        break;
                    case 0:
                        $query->where('start_time', '>', $time);
                        break;
                    case 1:
                        $query->where('start_time', '<=', $time)->where('stop_time', '>=', $time);
                        break;
                }
            })->when($page != 0 && $limit != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->when(isset($where['product_id']) && $where['product_id'] != 0, function ($query) use ($where) {
                $query->where('product_id', $where['product_id']);
            })->order('sort desc,id desc')->select()->toArray();
    }

    /**Lấy danh sách
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHomeList(array $where, int $page = 0, int $limit = 0)
    {
        return $this->search($where)
            ->when($page != 0 && $limit != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->when(isset($where['priceOrder']) && $where['priceOrder'] != '', function ($query) use ($where) {
                if ($where['priceOrder'] === 'desc') {
                    $query->order("price desc");
                } else {
                    $query->order("price asc");
                }
            })->when(isset($where['newsOrder']) && $where['newsOrder'] != '', function ($query) use ($where) {
                if ($where['newsOrder'] === 'news') {
                    $query->order("id desc");
                }
            })->when(isset($where['salesOrder']) && $where['salesOrder'] != '', function ($query) use ($where) {
                if ($where['salesOrder'] === 'desc') {
                    $query->order("sales desc");
                } else {
                    $query->order("sales asc");
                }
            })->when(isset($where['ids']) && $where['ids'], function ($query) use ($where) {
                if ((isset($where['priceOrder']) && $where['priceOrder'] != '') || (isset($where['salesOrder']) && $where['salesOrder'] != '')) {
                    $query->whereIn('id', $where['ids']);
                } else {
                    $query->whereIn('id', $where['ids'])->orderField('id', $where['ids'], 'asc');
                }
            })->with(['getPrice'])->select()->toArray();
    }

    /**
     * Lấy các sản phẩm đang mua chung, trả về dạng mảng
     * @param array $ids ids Rỗng thì trả về tất cả
     * @param array $field
     * @return array
     * @throws \ReflectionException
     */
    public function getPinkIdsArray(array $ids = [], array $field = [])
    {
        return $this->search(['is_del' => 0, 'is_show' => 1, 'pinkIngTime' => 1])
            ->when($ids, function ($query) use ($ids) {
                $query->whereIn('product_id', $ids);
            })->column(implode(',', $field), 'product_id');
    }

    /**
     * Lấy danh sách mua chung
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function combinationList(array $where, int $page, int $limit)
    {
        return $this->search($where)->with('getPrice')->page($page, $limit)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * Lấy số lượng theo điều kiện
     * @param array $where
     * @return int
     * @throws \ReflectionException
     */
    public function getCount(array $where)
    {
        return $this->search($where)->count();
    }

    /**
     * Trang thiết kế giao diện lấy danh sách mua chung sản phẩm
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function diyCombinationList(array $where, int $page, int $limit)
    {
        return $this->search($where)->with('getCategory')->page($page, $limit)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * Lấy dữ liệu mua chung theo id
     * @param array $ids
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function idCombinationList(array $ids, string $field)
    {
        return $this->getModel()->whereIn('id', $ids)->field($field)->select()->toArray();
    }

    /**
     * Lấy một bản ghi dữ liệu mua chung
     * @param int $id
     * @param string $field
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function validProduct(int $id, string $field)
    {
        $where = ['is_show' => 1, 'is_del' => 0, 'pinkIngTime' => true];
        return $this->search($where)->where('id', $id)->with(['total'])->field($field)->order('add_time desc')->find();
    }

    /**
     * Lấy mua chung được đề xuất
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCombinationHost()
    {
        $where = ['is_del' => 0, 'is_host' => 1, 'is_show' => 1, 'pinkIngTime' => true];
        return $this->search($where)->order('id desc')->select()->toArray();
    }

    public function getProductExist($productIds)
    {
        return $this->getModel()->where('product_id', 'in', $productIds)->where('is_del', 0)
            ->group('product_id')->column('COUNT(*) as count', 'product_id');
    }
}
