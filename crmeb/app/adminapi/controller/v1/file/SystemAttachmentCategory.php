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
namespace app\adminapi\controller\v1\file;

use app\adminapi\controller\AuthController;
use app\services\system\attachment\SystemAttachmentCategoryServices;
use think\facade\App;

/**
 * Lớp quản lý danh mục ảnh
 * Class SystemAttachmentCategory
 * @package app\adminapi\controller\v1\file
 */
class SystemAttachmentCategory extends AuthController
{
    /**
     * @var SystemAttachmentCategoryServices
     */
    protected $service;

    /**
     * @param App $app
     * @param SystemAttachmentCategoryServices $service
     */
    public function __construct(App $app, SystemAttachmentCategoryServices $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * Hiển thị danh sách resource
     * @return \think\Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', ''],
            ['pid', 0],
            ['all', 0],
            ['type', 0],
        ]);
        if ($where['name'] != '' || $where['all'] == 1) $where['pid'] = '';
        return app('json')->success($this->service->getAll($where));
    }

    /**
     * Form thêm mới
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        [$id, $type] = $this->request->getMore([
            ['id', 0],
            ['type', 0],
        ], true);
        return app('json')->success($this->service->createForm($id, $type));
    }

    /**
     * Lưu mục thêm mới
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['name', ''],
            ['type', 0],
        ]);
        if (is_array($data['pid'])) $data['pid'] = end($data['pid']);
        if (!$data['name']) {
            return app('json')->fail(400100);
        }
        $this->service->save($data);
        return app('json')->success(100021);
    }

    /**
     * Form sửa
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->service->editForm($id));
    }

    /**
     * Lưu resource đã cập nhật
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['name', '']
        ]);
        if (is_array($data['pid'])) $data['pid'] = end($data['pid']);
        if (!$data['name']) {
            return app('json')->fail(400100);
        }
        if ($data['pid'] == $id) {
            return app('json')->fail('Danh mục cha không thể là chính nó');
        }
        $info = $this->service->get($id);
        $count = $this->service->count(['pid' => $id]);
        if ($count && $info['pid'] != $data['pid']) return app('json')->fail(400105);
        $this->service->update($id, $data);
        return app('json')->success(100001);
    }

    /**
     * Xóa resource được chỉ định
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $this->service->del($id);
        return app('json')->success(100002);
    }
}
