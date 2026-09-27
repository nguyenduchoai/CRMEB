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
namespace app\api\controller\v1\publics;

use app\services\article\ArticleCategoryServices;
use crmeb\services\CacheService;

/**
 * Lớp danh mục bài viết
 * Class ArticleCategoryController
 * @package app\api\controller\publics
 */
class ArticleCategoryController
{
    protected $services;

    public function __construct(ArticleCategoryServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách danh mục bài viết
     * @return mixed
     */
    public function lst()
    {
        $cateInfo = CacheService::remember('ARTICLE_CATEGORY', function () {
            $cateInfo = $this->services->getArticleCategory();
            array_unshift($cateInfo, ['id' => 0, 'title' => 'Nổi bật']);
            return $cateInfo;
        });
        return app('json')->success($cateInfo);
    }
}
