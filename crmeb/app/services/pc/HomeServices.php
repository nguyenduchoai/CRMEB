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

namespace app\services\pc;

use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\BaseServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductServices;
use app\services\user\UserServices;

class HomeServices extends BaseServices
{
    /**
     * Sản phẩm theo danh mục ở trang chủ
     * @param int $uid
     * @return array
     */
    public function getCategoryProduct(int $uid = 0)
    {
        /** @var StoreCategoryServices $category */
        $category = app()->make(StoreCategoryServices::class);
        /** @var StoreProductServices $product */
        $product = app()->make(StoreProductServices::class);
        $vip_user = $uid ? app()->make(UserServices::class)->value(['uid' => $uid], 'is_money_level') : 0;
        [$page, $limit] = $this->getPageValue();
        $list = $category->getCid($page, $limit);
        foreach ($list as $key => &$info) {
            $productList = $product->getSearchList(['cid' => $info['id'], 'star' => 1, 'is_show' => 1, 'is_del' => 0, 'vip_user' => $vip_user], 1, 8, ['id,store_name,image,IFNULL(sales, 0) + IFNULL(ficti, 0) as sales,price,ot_price,presale']);
            if (!count($productList)) unset($list[$key]);
            foreach ($productList as &$item) {
                if (count($item['star'])) {
                    $item['star'] = bcdiv((string)array_sum(array_column($item['star'], 'product_score')), (string)count($item['star']), 1);
                } else {
                    $item['star'] = '5.0';
                }
                $item['checkCoupon'] = app()->make(StoreCouponIssueServices::class)->checkProductCoupon($item['id']);
            }
            $info['productList'] = get_thumb_water($productList, 'big');
        }
        $data['list'] = array_merge($list);
        $data['count'] = $category->getCidCount();
        return $data;
    }
}
