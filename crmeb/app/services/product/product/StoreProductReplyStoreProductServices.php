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

namespace app\services\product\product;

use app\services\BaseServices;
use app\dao\product\product\StoreProductReplyStoreProductDao;

/**
 *
 * Class StoreProductReplyStoreProductServices
 * @package app\services\product\product
 */
class StoreProductReplyStoreProductServices extends BaseServices
{

    /**
     * StoreProductReplyStoreProductServices constructor.
     * @param StoreProductReplyStoreProductDao $dao
     */
    public function __construct(StoreProductReplyStoreProductDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách đánh giá
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductReplyList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getProductReplyList($where, $page, $limit);
        $count = $this->dao->replyCount($where + ['is_del' => 0]);
        return compact('list', 'count');
    }
}
