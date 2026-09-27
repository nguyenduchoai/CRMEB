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

namespace app\services\product\product;


use app\dao\product\product\StoreVisitDao;
use app\services\BaseServices;

/**
 * Class StoreVisitService
 * @package app\services\product\product
 * @method getSum($where,$field)
 * @method getDistinctCount(array $where, $field, $search = true)
 * @method getProductTrend($time, $timeType, $str) Xu hướng sản phẩm
 */
class StoreVisitServices extends BaseServices
{
    public function __construct(StoreVisitDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     *  Đặt thông tin đã xem
     * @param $uid
     * @param int $product_id
     * @param int $cate
     * @param string $type
     * @param string $content
     * @param int $min
     */
    public function setView($uid, $product_id = 0, $product_type = 'product', $cate = 0, $type = '', $content = '', $min = 20)
    {

        $view = $this->dao->getOne(['uid' => $uid, 'product_id' => $product_id, 'product_type' => $product_type], 'count,add_time,id');
        if ($view && $type != 'search') {
            $time = time();
            if (($view['add_time'] + $min) < $time) {
                $this->dao->update($view['id'], ['count' => $view['count'] + 1, 'add_time' => time()]);
            }
        } else {
            $cate = explode(',', $cate)[0];
            $this->dao->save([
                'add_time' => time(),
                'count' => 1,
                'product_id' => $product_id,
                'product_type' => $product_type,
                'cate_id' => $cate,
                'type' => $type,
                'uid' => $uid,
                'content' => $content
            ]);
        }
    }
}
