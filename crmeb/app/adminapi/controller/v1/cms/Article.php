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
use app\services\article\ArticleServices;
use think\facade\App;

/**
 * Quản lý bài viết
 * Class Article
 * @package app\adminapi\controller\v1\cms
 */
class Article extends AuthController
{
    /**
     * @var ArticleServices
     */
    protected $service;

    /**
     * Article constructor.
     * @param App $app
     * @param ArticleServices $service
     */
    public function __construct(App $app, ArticleServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * Lấy danh sách
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', ''],
            ['pid', 0, '', 'cid'],
        ]);
        $data = $this->service->getList($where);
        return app('json')->success($data);
    }

    /**
     * Lưu dữ liệu bài viết
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['cid', ''],
            ['title', ''],
            ['author', ''],
            ['image_input', ''],
            ['content', ''],
            ['synopsis', 0],
            ['share_title', ''],
            ['share_synopsis', ''],
            ['sort', 0],
            ['url', ''],
            ['is_banner', 0],
            ['is_hot', 0],
            ['status', 1]
        ]);
        $this->service->save($data);
        return app('json')->success(100021);
    }

    /**
     * Lấy dữ liệu một bài viết
     * @param int $id
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id = 0)
    {
        if (!$id) return app('json')->fail(100100);
        $info = $this->service->read($id);
        return app('json')->success($info);
    }

    /**
     * Xóa bài viết
     * @param int $id
     * @return mixed
     */
    public function delete($id = 0)
    {
        if (!$id) return app('json')->fail(100100);
        $this->service->del($id);
        return app('json')->success(100002);
    }

    /**
     * Liên kết sản phẩm với bài viết
     * @param int $id
     * @return mixed
     */
    public function relation($id)
    {
        if (!$id) return app('json')->fail(100100);
        list($product_id) = $this->request->postMore([
            ['product_id', 0]
        ], true);
        $res = $this->service->bindProduct($id, $product_id);
        if ($res) {
            return app('json')->success(400300);
        } else {
            return app('json')->fail(400301);
        }
    }

    /**
     * Hủy liên kết sản phẩm
     * @param int $id
     * @return mixed
     */
    public function unrelation($id)
    {
        if (!$id) return app('json')->fail(100100);
        $res = $this->service->bindProduct($id);
        if ($res) {
            return app('json')->success(100019);
        } else {
            return app('json')->fail(100020);
        }
    }
}
