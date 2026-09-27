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
namespace app\adminapi\controller\v1\setting;

use app\adminapi\controller\AuthController;
use app\services\system\config\SystemConfigServices;
use app\services\system\config\SystemConfigTabServices;
use think\facade\App;


/**
 * Danh mục cấu hình
 * Class SystemConfigTab
 * @package app\adminapi\controller\v1\setting
 */
class SystemConfigTab extends AuthController
{
    /**
     * Phương thức khởi tạo g
     * SystemConfigTab constructor.
     * @param App $app
     * @param SystemConfigTabServices $services
     */
    public function __construct(App $app, SystemConfigTabServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['title', '']
        ]);
        return app('json')->success($this->services->getConfgTabList($where));
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
     * @return \think\Response
     */
    public function save()
    {
        $data = $this->request->postMore([
            'eng_title',
            'status',
            'title',
            'icon',
            ['type', 0],
            ['sort', 0],
            ['pid', 0],
            ['menus_id', 0],
        ]);
        if (is_array($data['pid'])) $data['pid'] = end($data['pid']);
        if (!$data['title']) return app('json')->fail('Vui lòng nhập tiêu đề');
        $this->services->save($data);
        return app('json')->success('Thêm danh mục cấu hình thành công');
    }

    /**
     * Hiển thị resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
     * @param int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            'title',
            'status',
            'eng_title',
            'icon',
            ['type', 0],
            ['sort', 0],
            ['pid', 0],
            ['menus_id', 0],
        ]);
        if (is_array($data['pid'])) $data['pid'] = end($data['pid']);
        if (!$data['title']) return app('json')->fail('Vui lòng nhập tiêu đề');
        if (!$data['eng_title']) return app('json')->fail('Vui lòng nhập tên trường');
        $this->services->update($id, $data);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete(SystemConfigServices $services, $id)
    {
        if ($services->count(['tab_id' => $id])) {
            return app('json')->fail('Có cấu hình cấp dưới, không thể xóa');
        }
        if (!$this->services->delete($id))
            return app('json')->fail('Xóa thất bại');
        else
            return app('json')->success('Xóa thành công');
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $this->services->update($id, ['status' => $status]);
        return app('json')->success('Cài đặt thành công');
    }
}
