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
namespace app\adminapi\controller\v1\setting;

use app\adminapi\controller\AuthController;
use app\outapi\validate\StoreOutAccountValidate;
use app\services\out\OutAccountServices;
use app\services\out\OutInterfaceServices;
use think\facade\App;

/**
 * Tài khoản API bên ngoài
 * Class SystemOutAccount
 * @package app\adminapi\controller\v1\setting
 */
class SystemOutAccount extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemOut constructor.
     * @param App $app
     * @param OutAccountServices $services
     */
    public function __construct(App $app, OutAccountServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Thông tin tài khoản
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', '', ''],
            ['status', ''],
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * Sửa trạng thái
     * @param string $status
     * @param string $id
     * @return mixed
     */
    public function set_status($id = '', $status = '')
    {
        if ($status == '' || $id == '') return app('json')->fail(100100);
        $this->services->update($id, ['status' => $status]);
        return app('json')->success($status == 1 ? 100012 : 100013);
    }

    /**
     * Xóa
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if ($id == '') return app('json')->fail(100100);
        $this->services->update($id, ['is_del' => 1]);
        return app('json')->success(100002);
    }

    /**
     * Lưu
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save()
    {
        $data = $this->request->postMore([
            [['appid', 's'], ''],
            [['appsecret', 's'], ''],
            [['title', 's'], ''],
            ['rules', []],
        ]);
        $this->validate($data, StoreOutAccountValidate::class, 'save');
        if ($this->services->getOne(['appid' => $data['appid']])) return app('json')->fail('Tài khoản bị trùng');
        $data['apppwd'] = $data['appsecret'];
        $data['appsecret'] = password_hash($data['appsecret'], PASSWORD_DEFAULT);
        $data['add_time'] = time();
        $data['rules'] = implode(',', $data['rules']);
        if (!$this->services->save($data)) {
            return app('json')->fail(100006);
        } else {
            return app('json')->success(100000);
        }
    }

    /**
     * Sửa
     * @param string $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id = '')
    {
        $data = $this->request->postMore([
            [['appsecret', 's'], ''],
            [['title', 's'], ''],
            ['rules', []],
        ]);

        $this->validate($data, StoreOutAccountValidate::class, 'update');
        if (!$this->services->getOne(['id' => $id])) return app('json')->fail('Không tồn tại tài khoản này');
        $data['apppwd'] = $data['appsecret'];
        $data['appsecret'] = password_hash($data['appsecret'], PASSWORD_DEFAULT);
        $data['rules'] = implode(',', $data['rules']);
        $res = $this->services->update($id, $data);
        if (!$res) {
            return app('json')->fail(100006);
        } else {
            return app('json')->success(100000);
        }
    }

    /**
     * Cài đặt API đẩy dữ liệu cho tài khoản
     * @param $id
     * @return mixed
     */
    public function outSetUpSave($id)
    {
        $data = $this->request->postMore([
            ['push_open', 0],
            ['push_account', ''],
            ['push_password', ''],
            ['push_token_url', ''],
            ['user_update_push', ''],
            ['order_create_push', ''],
            ['order_pay_push', ''],
            ['refund_create_push', ''],
            ['refund_cancel_push', ''],
        ]);
        $this->services->outSetUpSave($id, $data);
        return app('json')->success(100000);
    }

    /**
     * Danh sách API bên ngoài
     * @param OutInterfaceServices $service
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function outInterfaceList(OutInterfaceServices $service)
    {
        return app('json')->success($service->outInterfaceList());
    }

    /**
     * Lưu tài liệu API
     * @param $id
     * @param OutInterfaceServices $service
     * @return mixed
     */
    public function saveInterface($id, OutInterfaceServices $service)
    {
        $data = $this->request->postMore([
            ['pid', 0], //ID cha
            ['type', 0], //Loại: 0 là menu, 1 là API
            ['name', ''], //Tên
            ['describe', ''], //Mô tả
            ['method', ''], //Phương thức
            ['url', ''], //Địa chỉ liên kết
            ['request_params', []], //Tham số request
            ['return_params', []], //Tham số trả về
            ['request_example', ''], //Ví dụ request
            ['return_example', ''], //Ví dụ dữ liệu trả về
            ['error_code', []] //Mã lỗi
        ]);
        $service->saveInterface((int)$id, $data);
        return app('json')->success(100000);
    }

    /**
     * Tài liệu API công khai
     * @param $id
     * @param OutInterfaceServices $service
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function interfaceInfo($id, OutInterfaceServices $service)
    {
        return app('json')->success($service->interfaceInfo($id));
    }

    /**
     * Sửa tên API
     * @param OutInterfaceServices $service
     * @return mixed
     */
    public function editInterfaceName(OutInterfaceServices $service)
    {
        $data = $this->request->postMore([
            ['id', 0], //ID cha
            ['name', ''], //Tên
        ]);
        if (!$data['id'] || !$data['name']) {
            return app('json')->success(100100);
        }
        $service->editInterfaceName($data);
        return app('json')->success(100001);
    }

    /**
     * Xóa API
     * @param $id
     * @param OutInterfaceServices $service
     * @return mixed
     */
    public function delInterface($id, OutInterfaceServices $service)
    {
        if (!$id) return app('json')->success(100100);
        $service->delInterface($id);
        return app('json')->success(100002);
    }

    /**
     * Kiểm thử API lấy token
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function textOutUrl()
    {
        $data = $this->request->postMore([
            ['push_account', 0],
            ['push_password', 0],
            ['push_token_url', '']
        ]);
        return app('json')->success('100014', $this->services->textOutUrl($data));
    }
}
