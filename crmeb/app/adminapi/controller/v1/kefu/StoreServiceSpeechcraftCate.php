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

namespace app\adminapi\controller\v1\kefu;


use app\Request;
use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\kefu\service\StoreServiceSpeechcraftCateServices;

/**
 * Class StoreServiceSpeechcraftCate
 * @package app\adminapi\controller\v1\application\wechat
 */
class StoreServiceSpeechcraftCate extends AuthController
{

    /**
     * StoreServiceSpeechcraftCate constructor.
     * @param App $app
     * @param StoreServiceSpeechcraftCateServices $services
     */
    public function __construct(App $app, StoreServiceSpeechcraftCateServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', '']
        ]);
        $where['owner_id'] = 0;
        $where['type'] = 1;
        return app('json')->success($this->services->getCateList($where));
    }

    /**
     * Lấy form tạo
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu dữ liệu
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['sort', 0],
        ]);

        if (!$data['name']) {
            return app('json')->fail('Vui lòng điền tên danh mục');
        }

        if ($this->services->count(['name' => $data['name'], 'type' => 1, 'owner_id' => 0])) {
            return app('json')->fail('Danh mục này đã tồn tại');
        }

        $data['add_time'] = time();
        $data['type'] = 1;

        $this->services->save($data);
        return app('json')->success('Thêm thành công');
    }

    /**
     * Lấy form sửa
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->editForm((int)$id));
    }

    /**
     * Sửa và lưu
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $data = $request->postMore([
            ['name', ''],
            [['sort', 'd'], 0],
        ]);
        if (!$data['name']) {
            return app('json')->fail('Vui lòng điền tên danh mục');
        }

        $cateInfo = $this->services->get($id);
        if (!$cateInfo) {
            return app('json')->fail('Danh mục không tồn tại');
        }
        $cateInfo->name = $data['name'];
        $cateInfo->sort = $data['sort'];
        $cateInfo->save();
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if ($id == 0) return app('json')->fail('Không thể xóa danh mục hệ thống');
        $cateInfo = $this->services->get($id);
        if (!$cateInfo) {
            return app('json')->fail('Danh mục không tồn tại');
        }
        $cateInfo->delete();
        return app('json')->success('Xóa thành công');
    }
}
