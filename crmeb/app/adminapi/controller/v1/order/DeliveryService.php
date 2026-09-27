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
namespace app\adminapi\controller\v1\order;

use app\adminapi\controller\AuthController;
use app\services\order\DeliveryServiceServices;
use app\services\user\UserWechatuserServices;
use think\facade\App;

/**
 * Quản lý nhân viên giao hàng
 * Class StoreService
 * @package app\admin\controller\store
 */
class DeliveryService extends AuthController
{
    /**
     * DeliveryService constructor.
     * @param App $app
     * @param DeliveryServiceServices $services
     */
    public function __construct(App $app, DeliveryServiceServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách nhân viên giao hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        return app('json')->success($this->services->getServiceList([]));
    }

    /**
     * Biểu mẫu thêm nhân viên CSKH
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function add()
    {
        return app('json')->success($this->services->create());
    }

    /**
     * Lưu người giao hàng
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['image', ''],
            ['uid', 0],
            ['avatar', ''],
            ['phone', ''],
            ['nickname', ''],
            ['status', 1],
        ]);

        $this->services->saveDeliveryService($data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Form sửa
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->edit((int)$id));
    }

    /**
     * Sửa nhân viên giao hàng
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['avatar', ''],
            ['nickname', ''],
            ['phone', ''],
            ['status', 1],
        ]);

        $this->services->updateDeliveryService((int)$id, $data);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa nhân viên giao hàng
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
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
        if ($status == '' || $id == 0) return app('json')->fail('Tham số không hợp lệ');
        $this->services->update($id, ['status' => $status]);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Lấy danh sách tất cả người giao hàng
     * @return mixed
     */
    public function get_delivery_list()
    {
        $data = $this->services->getDeliveryList();
        return app('json')->success($data);
    }

}
