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

namespace app\services\article;

use app\dao\article\ArticleContentDao;
use app\services\BaseServices;

/**
 * Class ArticleContentServices
 * @package app\services\article
 * @method save(array $data) lưu
 * @method update($id, array $data, ?string $key = null)
 */
class ArticleContentServices extends BaseServices
{
    /**
     * ArticleContentServices constructor.
     * @param ArticleContentDao $dao
     */
    public function __construct(ArticleContentDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Xóa
     * @param int $id
     * @return bool
     */
    public function del(int $id)
    {
        return $this->dao->del($id);
    }
}
