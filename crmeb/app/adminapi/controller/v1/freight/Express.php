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
namespace app\adminapi\controller\v1\freight;

use app\adminapi\controller\AuthController;
use app\services\shipping\ExpressServices;
use think\facade\App;

/**
 * Vận chuyển
 * Class Express
 * @package app\adminapi\controller\v1\freight
 */
class Express extends AuthController
{
    /**
     * Phương thức khởi tạo
     * Express constructor.
     * @param App $app
     * @param ExpressServices $services
     */
    public function __construct(App $app, ExpressServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách vận chuyển
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['keyword', ''],
            ['is_show', '']
        ]);
        return app('json')->success($this->services->getExpressList($where));
    }

    /**
     * Hiển thị trang form tạo resource
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu resource mới tạo
     * @return \think\Response
     */
    public function save()
    {
        $data = $this->request->postMore([
            'name',
            'code',
            ['sort', 0],
            ['is_show', 0]]);
        if (!$data['name']) return app('json')->fail('Vui lòng nhập tên công ty');
        $this->services->save($data);
        return app('json')->success('Thêm công ty thành công');
    }

    /**
     * Hiển thị trang form sửa resource
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->updateForm((int)$id));
    }

    /**
     * Lưu resource đã cập nhật
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['account', ''],
            ['key', ''],
            ['net_name', ''],
            ['courier_name', ''],
            ['customer_name', ''],
            ['code_name', ''],
            ['sort', 0],
            ['is_show', 0]]);
        if (!$expressInfo = $this->services->get($id)) return app('json')->fail('Dữ liệu không tồn tại');
        if ($expressInfo['net'] == 1 && !$data['net_name']) {
            return app('json')->fail('Vui lòng nhập bưu cục lấy hàng');
        }
        if ($expressInfo['check_man'] == 1 && !$data['courier_name']) {
            return app('json')->fail('Vui lòng nhập tên nhân viên chuyển phát');
        }
        if ($expressInfo['partner_name'] == 1 && !$data['customer_name']) {
            return app('json')->fail('Vui lòng nhập tên tài khoản khách hàng');
        }
        if ($expressInfo['is_code'] == 1 && !$data['code_name']) {
            return app('json')->fail('Vui lòng nhập mã số vận đơn điện tử');
        }
        $expressInfo->account = $data['account'];
        $expressInfo->key = $data['key'];
        $expressInfo->net_name = $data['net_name'];
        $expressInfo->courier_name = $data['courier_name'];
        $expressInfo->customer_name = $data['customer_name'];
        $expressInfo->code_name = $data['code_name'];
        $expressInfo->sort = $data['sort'];
        $expressInfo->is_show = $data['is_show'];
        $expressInfo->status = 1;
        $expressInfo->save();
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa resource được chỉ định
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $res = $this->services->delete($id);
        if (!$res)
            return app('json')->fail('Xóa thất bại');
        else
            return app('json')->success('Xóa thành công');
    }

    /**
     * Sửa trạng thái
     * @param int $id
     * @param string $status
     * @return mixed
     */
    public function set_status($id = 0, $status = '')
    {
        if ($status == '' || $id == 0) return app('json')->fail('Tham số không hợp lệ');
        $this->services->update($id, ['is_show' => $status]);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Đồng bộ đơn vị vận chuyển của platform
     * @return mixed
     */
    public function syncExpress()
    {
        $this->services->syncExpress();
        return app('json')->success('Đồng bộ thành công');
    }
}
