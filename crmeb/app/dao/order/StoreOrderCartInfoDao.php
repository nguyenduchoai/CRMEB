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

namespace app\dao\order;


use app\dao\BaseDao;
use app\model\order\StoreOrderCartInfo;

/**
 * Chi tiết đơn hàng
 * Class StoreOrderCartInfoDao
 * @package app\dao\order
 */
class StoreOrderCartInfoDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreOrderCartInfo::class;
    }

    /**
     * Lấy danh sách chi tiết giỏ hàng
     * @param array $where
     * @param array $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCartInfoList(array $where, array $field)
    {
        return $this->search($where)->field($field)->select()->toArray();
    }

    /**
     * Lấy thông tin giỏ hàng, trả về dạng mảng
     * @param array $where
     * @param string $field
     * @param string $key
     */
    public function getCartColunm(array $where, string $field, string $key = '')
    {
        return $this->search($where)->column($field, $key);
    }

    public function getSplitCartNum($cart_ids)
    {
        $res = $this->getModel()->whereIn('old_cart_id', $cart_ids)->field('sum(cart_num) as num,old_cart_id')->group('old_cart_id')->select()->toArray();
        $data = [];
        foreach ($res as $value) {
            $data[$value['old_cart_id']] = $value['num'];
        }
        return $data;
    }
}
