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

namespace app\dao\article;


use app\dao\BaseDao;
use app\model\article\ArticleContent;

/**
 * Chi tiết bài viết
 * Class ArticleContentDao
 * @package app\dao\article
 */
class ArticleContentDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ArticleContent::class;
    }

    /**
     * Xóa dữ liệu theo id
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function del(int $id)
    {
        return $this->getModel()->where('nid',$id)->delete();
    }
}
