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

namespace app\adminapi\controller\v1\user;

use app\adminapi\controller\AuthController;
use app\services\user\UserLabelCateServices;
use app\adminapi\validate\user\UserLabeCateValidata;
use app\services\user\UserLabelServices;
use think\facade\App;
use app\Request;

/**
 * Class UserLabelCate
 * @package app\adminapi\controller\v1\user
 */
class UserLabelCate extends AuthController
{
    /**
     * UserLabelCate constructor.
     * @param App $app
     * @param UserLabelCateServices $services
     */
    public function __construct(App $app, UserLabelCateServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $where = $request->postMore([
            ['name', '']
        ]);
        $where['type'] = 0;
        return app('json')->success($this->services->getLabelList($where));
    }

    /**
     * Hiển thị trang form tạo resource.
     *
     * @return \think\Response
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu resource mới tạo
     *
     * @param Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = $request->postMore([
            ['name', ''],
            ['sort', 0]
        ]);

        $this->validate($data, UserLabeCateValidata::class);

        if ($this->services->count(['name' => $data['name']])) {
            return app('json')->fail('Danh mục này đã tồn tại');
        }
        $data['type'] = 0;
        if ($this->services->save($data)) {
            $this->services->deleteCateCache();
            return app('json')->success('Lưu thành công');
        } else {
            return app('json')->fail('Lưu thất bại');
        }
    }

    /**
     * Hiển thị resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $info = $this->services->get($id);
        if (!$info) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }
        return app('json')->success($info->toArray());
    }

    /**
     * Hiển thị trang form sửa resource.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        return app('json')->success($this->services->updateForm((int)$id));
    }

    /**
     * Lưu resource đã cập nhật
     *
     * @param Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->postMore([
            ['name', ''],
            ['sort', 0],
        ]);

        $this->validate($data, UserLabeCateValidata::class);

        if ($this->services->update($id, $data)) {
            $this->services->deleteCateCache();
            return app('json')->success('Sửa thành công');
        } else {
            return app('json')->fail('Sửa thất bại');
        }
    }

    /**
     * Xóa resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id || !($info = $this->services->get($id))) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }
        /** @var $labelService $labelservice */
        $labelService = app()->make(UserLabelServices::class);
        $count = $labelService->getCount(['label_cate' => $id]);
        if($count) return app('json')->fail('Danh mục này có nhãn, vui lòng xóa nhãn trước');
        if ($info->delete()) {
            $this->services->deleteCateCache();
            return app('json')->success('Xóa thành công');
        } else {
            return app('json')->fail('Xóa thất bại');
        }
    }

    /**
     * Lấy tất cả danh mục nhãn người dùng
     * @return mixed
     */
    public function getAll()
    {
        return app('json')->success($this->services->getLabelCateAll());
    }
}
