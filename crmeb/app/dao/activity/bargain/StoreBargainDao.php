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

namespace app\dao\activity\bargain;

use app\dao\BaseDao;
use app\model\activity\bargain\StoreBargain;

/**
 *
 * Class StoreBargainDao
 * @package app\dao\activity
 */
class StoreBargainDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreBargain::class;
    }

    /**
     * Lấy danh sách săn giảm giá
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
        return $this->search($where, false)->where('is_del', 0)
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

    /**
     * Lấy id các hoạt động săn giảm giá đang mở, trả về dạng mảng
     * @param array $ids Rỗng thì trả về tất cả
     * @param array $field
     * @return array
     */
    public function getBargainIdsArray(array $ids = [], array $field = [])
    {
        return $this->search(['is_del' => 0, 'status' => 1])->where('start_time', '<=', time())
            ->where('stop_time', '>=', time())
            ->when($ids, function ($query) use ($ids) {
                $query->whereIn('product_id', $ids);
            })->column(implode(',', $field), 'product_id');
    }

    /**
     * Lấy dữ liệu săn giảm giá theo id
     * @param array $ids
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function idByBargainList(array $ids, string $field)
    {
        return $this->getModel()->whereIn('id', $ids)->field($field)->select()->toArray();
    }

    /**
     * Hoạt động săn giảm giá đang diễn ra
     * @param int $status
     * @return StoreBargain
     */
    public function validWhere(int $status = 1)
    {
        return $this->getModel()->where('is_del', 0)->where('status', $status)->where('start_time', '<', time())->where('stop_time', '>', time());

    }

    /**
     * Danh sách săn giảm giá
     * @param int $page
     * @param int $limit
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bargainList(int $page, int $limit, string $field = '*')
    {
        return $this->search(['is_del' => 0, 'status' => 1])
            ->where('start_time', '<=', time())
            ->where('stop_time', '>=', time())
            ->where('product_id', 'IN', function ($query) {
                $query->name('store_product')->where('is_del', 0)->field('id');
            })->with('product')->field($field)->page($page, $limit)->order('sort DESC,id DESC')->select()->toArray();
    }

    /**
     * Trang thiết kế giao diện ở trang quản trị lấy danh sách săn giảm giá
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function DiyBargainList(array $where, int $page, int $limit)
    {
        return $this->search($where, false)
            ->when(isset($where['sid']) && $where['sid'], function ($query) use ($where) {
                $query->whereIn('id', function ($query) use ($where) {
                    $query->name('store_product_cate')->where('cate_id', $where['sid'])->field('product_id')->select();
                });
            })->when(isset($where['cid']) && $where['cid'], function ($query) use ($where) {
                $query->whereIn('id', function ($query) use ($where) {
                    $query->name('store_product_cate')->whereIn('cate_id', function ($query) use ($where) {
                        $query->name('store_category')->where('pid', $where['cid'])->field('id')->select();
                    })->field('product_id')->select();
                });
            })->where('start_time', '<=', time())
            ->where('stop_time', '>=', time())
            ->where('product_id', 'IN', function ($query) {
                $query->name('store_product')->where('is_show', 1)->where('is_del', 0)->field('id');
            })->with('product')->page($page, $limit)->order('sort DESC,id DESC')->select()->toArray();
    }

    /**
     * Săn giảm giá trang chủ
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHomeList(array $where, int $page, int $limit)
    {
        return $this->search($where, false)
            ->when(isset($where['sid']) && $where['sid'], function ($query) use ($where) {
                $query->whereIn('id', function ($query) use ($where) {
                    $query->name('store_product_cate')->where('cate_id', $where['sid'])->field('product_id')->select();
                });
            })->when(isset($where['cid']) && $where['cid'], function ($query) use ($where) {
                $query->whereIn('id', function ($query) use ($where) {
                    $query->name('store_product_cate')->whereIn('cate_id', function ($query) use ($where) {
                        $query->name('store_category')->where('pid', $where['cid'])->field('id')->select();
                    })->field('product_id')->select();
                });
            })->when($page != 0 && $limit != 0, function ($query) use ($page, $limit) {
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
            })
            ->where('start_time', '<=', time())
            ->where('stop_time', '>=', time())
            ->where('product_id', 'IN', function ($query) {
                $query->name('store_product')->where('is_show', 1)->where('is_del', 0)->field('id');
            })->with('product')->select()->toArray();
    }

    /**
     * Lấy số lượng theo điều kiện
     * @param array $where
     * @return int
     */
    public function getCount(array $where)
    {
        return $this->search($where, false)
            ->when(isset($where['sid']) && $where['sid'], function ($query) use ($where) {
                $query->whereIn('product_id', function ($query) use ($where) {
                    $query->name('store_product_cate')->where('cate_id', $where['sid'])->field('product_id')->select();
                });
            })->when(isset($where['cid']) && $where['cid'], function ($query) use ($where) {
                $query->whereIn('product_id', function ($query) use ($where) {
                    $query->name('store_product_cate')->whereIn('cate_id', function ($query) use ($where) {
                        $query->name('store_category')->where('pid', $where['cid'])->field('id')->select();
                    })->field('product_id')->select();
                });
            })->where('start_time', '<=', time())
            ->where('stop_time', '>=', time())
            ->where('product_id', 'IN', function ($query) {
                $query->name('store_product')->where('is_show', 1)->where('is_del', 0)->field('id');
            })->count();
    }

    /**
     * Sửa trạng thái săn giảm giá
     * @param int $id
     * @param string $field
     * @return mixed
     */
    public function addBargain(int $id, string $field)
    {
        return $this->getModel()->where('id', $id)->inc($field, 1)->update();
    }

    public function getProductExist($productIds)
    {
        return $this->getModel()->where('product_id', 'in', $productIds)->where('is_del', 0)
            ->group('product_id')->column('COUNT(*) as count', 'product_id');
    }
}
