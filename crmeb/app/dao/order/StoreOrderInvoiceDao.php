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

namespace app\dao\order;


use app\dao\BaseDao;
use app\model\order\StoreOrderInvoice;

/**
 * Class StoreOrderInvoiceDao
 * @package app\dao\order
 */
class StoreOrderInvoiceDao extends BaseDao
{
    /**
     * Giới hạn trường tìm kiếm chính xác
     * @var string[]
     */
    protected $withField = ['uid', 'order_id', 'real_name', 'user_phone'];

    protected function setModel(): string
    {
        return StoreOrderInvoice::class;
    }

    /**
     * Tìm kiếm hóa đơn
     * @param array $where
     * @param bool $search
     * @return \crmeb\basic\BaseModel
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/20
     */
    public function search(array $where = [], bool $search = false)
    {
        $realName = $where['real_name'] ?? '';
        $fieldKey = $where['field_key'] ?? '';
        $fieldKey = $fieldKey == 'all' ? '' : $fieldKey;
        $type = $where['type'] ?? '';
        unset($where['type']);
        return parent::search($where, $search)->when($type, function ($query) use ($type) {
            switch ($type) {
                case 1://Chờ xuất
                    $query->where('is_invoice', 0)->where('is_refund', 0);
                    break;
                case 2://Đã xuất
                    $query->where('is_invoice', 1);
                    break;
                case 3://Hoàn tiền
                    $query->where('is_refund', 1);
                    break;
                case 4://Chưa xuất
                    $query->where('is_invoice', 0)->where('invoice_time', 0)->where('is_refund', 0);
                    break;
            }
        })->when($realName && $fieldKey && in_array($fieldKey, $this->withField), function ($query) use ($realName, $fieldKey) {
            $query->where('order_id', 'IN', function ($que) use ($fieldKey, $realName) {
                $que->name('store_order')->where(trim($fieldKey), trim($realName))->field(['id'])->select();
            });
        })->when($realName && !$fieldKey, function ($query) use ($where) {
            $query->where(function ($que) use ($where) {
                $que->whereLike('order_id|invoice_id|name|drawer_phone|email|tell|address|bank', "%{$where['real_name']}%")
                    ->whereOr('order_id', 'IN', function ($order) use ($where) {
                        $order->name('store_order')->where('uid|real_name|order_id', 'LIKE', "%{$where['real_name']}%")->field('id')->select();
                    })
                    ->whereOr('uid', 'in', function ($q) use ($where) {
                        $q->name('user')->whereLike('nickname|uid|phone', '%' . $where['real_name'] . '%')->field(['uid'])->select();
                    });
            });
        });
    }

    /**
     * @param array $where
     * @param string $field
     * @param array|string[] $with
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', array $with = ['order'], string $order = '', int $page = 0, int $limit = 0)
    {
        return $this->search($where)->field($field)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->when($order != '', function ($query) use ($order) {
            $query->order($order);
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }
}
