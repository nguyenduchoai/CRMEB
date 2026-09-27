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
namespace app\adminapi\controller\v1\cms;

use app\adminapi\controller\AuthController;
use app\services\article\ArticleCategoryServices;
use crmeb\services\CacheService;
use think\facade\App;

/**
 * Quản lý danh mục bài viết
 * Class ArticleCategory
 * @package app\adminapi\controller\v1\cms
 */
class ArticleCategory extends AuthController
{
    /**
     * @var ArticleCategoryServices
     */
    protected $service;

    /**
     * ArticleCategory constructor.
     * @param App $app
     * @param ArticleCategoryServices $service
     */
    public function __construct(App $app, ArticleCategoryServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * Lấy danh sách danh mục
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['title', ''],
            ['type', 0]
        ]);
        $type = $where['type'];
        unset($where['type']);
        $data = $this->service->getList($where);
        if ($type == 1) $data = $data['list'];
        return app('json')->success($data);
    }

    /**
     * Tạo form thêm mới
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->service->createForm(0));
    }

    /**
     * Lưu danh mục mới tạo
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['pid', 0],
            ['intr', ''],
            ['image', ''],
            ['sort', 0],
            ['status', 0]
        ]);
        if (!$data['title']) {
            return app('json')->fail(400100);
        }
        $data['add_time'] = time();
        $this->service->save($data);
        CacheService::delete('ARTICLE_CATEGORY');
        CacheService::delete('ARTICLE_CATEGORY_PC');
        return app('json')->success(100021);
    }

    /**
     * Tạo form sửa
     * @param int $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id = 0)
    {
        if (!$id) return app('json')->fail(100100);
        return app('json')->success($this->service->createForm($id));
    }

    /**
     * Lưu danh mục đã sửa
     * @param $id
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['title', ''],
            ['pid', 0],
            ['intr', ''],
            ['image', ''],
            ['sort', 0],
            ['status', 0]
        ]);
        $this->service->update($data);
        CacheService::delete('ARTICLE_CATEGORY');
        CacheService::delete('ARTICLE_CATEGORY_PC');
        return app('json')->success(100001);
    }

    /**
     * Xóa danh mục bài viết
     * @param $id
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        $this->service->del($id);
        CacheService::delete('ARTICLE_CATEGORY');
        CacheService::delete('ARTICLE_CATEGORY_PC');
        return app('json')->success(100002);
    }

    /**
     * Sửa trạng thái danh mục bài viết
     * @param int $id
     * @param int $status
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) return app('json')->fail(100100);
        $this->service->setStatus($id, $status);
        CacheService::delete('ARTICLE_CATEGORY');
        CacheService::delete('ARTICLE_CATEGORY_PC');
        return app('json')->success(100014);
    }

    /**
     * Lấy danh mục bài viết
     * @return mixed
     */
    public function categoryList()
    {
        return app('json')->success($this->service->getArticleTwoCategory());
    }

    /**
     * Danh sách dạng cây
     * @return mixed
     * @throws \ReflectionException
     */
    public function getTreeList()
    {
        $list = $this->service->getTreeList();
        return app('json')->success($list);
    }
}
