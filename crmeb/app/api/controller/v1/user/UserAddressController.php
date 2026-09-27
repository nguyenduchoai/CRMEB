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
namespace app\api\controller\v1\user;

use app\Request;
use app\services\user\UserAddressServices;
use app\services\wechat\WechatUserServices;

/**
 * Lớp địa chỉ người dùng
 * Class UserController
 * @package app\api\controller\store
 */
class UserAddressController
{
    protected $services = NUll;

    /**
     * UserController constructor.
     * @param UserAddressServices $services
     */
    public function __construct(UserAddressServices $services)
    {
        $this->services = $services;
    }

    /**
     * Địa chỉ - Lấy một địa chỉ
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function address(Request $request, $id)
    {
        $uid = (int)$request->uid();
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $info = $this->services->address((int)$id);
        if ($info['uid'] != $uid) return app('json')->fail('Dữ liệu không tồn tại');
        return app('json')->success($info);
    }

    /**
     * Danh sách địa chỉ
     * @param Request $request
     * @return mixed
     */
    public function address_list(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getUserAddressList($uid, 'id,real_name,phone,province,city,district,detail,is_default,city_id'));
    }

    /**
     * Đặt địa chỉ mặc định
     * @param Request $request
     * @return mixed
     */
    public function address_default_set(Request $request)
    {
        list($id) = $request->getMore([['id', 0]], true);
        if (!$id || !is_numeric($id)) return app('json')->fail('Tham số không hợp lệ');
        $uid = (int)$request->uid();
        $res = $this->services->setDefault($uid, (int)$id);
        $province = $this->services->value(['id' => $id], 'province');
        app()->make(WechatUserServices::class)->update(['uid' => $uid], ['province' => $province]);
        if (!$res)
            return app('json')->fail('Địa chỉ không tồn tại');
        else
            return app('json')->success('Cài đặt thành công');
    }

    /**
     * Lấy địa chỉ mặc định
     * @param Request $request
     * @return mixed
     */
    public function address_default(Request $request)
    {
        $uid = (int)$request->uid();
        $defaultAddress = $this->services->getUserDefaultAddress($uid, 'id,real_name,phone,province,city,district,detail,is_default');
        if ($defaultAddress) {
            $defaultAddress = $defaultAddress->toArray();
            return app('json')->success($defaultAddress);
        }
        return app('json')->success('empty', []);
    }

    /**
     * Sửa - Thêm địa chỉ
     * @param Request $request
     * @return mixed
     */
    public function address_edit(Request $request)
    {
        $addressInfo = $request->postMore([
            ['address', []],
            ['is_default', false],
            ['real_name', ''],
            ['post_code', ''],
            ['phone', ''],
            ['detail', ''],
            [['id', 'd'], 0],
            [['type', 'd'], 0]
        ]);
        if (!isset($addressInfo['address']['province']) || !$addressInfo['address']['province'] || $addressInfo['address']['province'] == 'Tỉnh') return app('json')->fail('Địa chỉ nhận hàng sai định dạng');
        if (!isset($addressInfo['address']['city']) || !$addressInfo['address']['city'] || $addressInfo['address']['city'] == 'Thành phố') return app('json')->fail('Địa chỉ nhận hàng sai định dạng hoặc hệ thống chưa cập nhật đầy đủ địa chỉ này');
        if (!isset($addressInfo['address']['district']) || !$addressInfo['address']['district'] || $addressInfo['address']['district'] == 'Quận/Huyện') return app('json')->fail('Địa chỉ nhận hàng sai định dạng hoặc hệ thống chưa cập nhật đầy đủ địa chỉ này');
        if (!isset($addressInfo['address']['city_id']) && $addressInfo['type'] == 0) return app('json')->fail('Địa chỉ nhận hàng sai định dạng, vui lòng chọn lại');
        if (!$addressInfo['detail']) return app('json')->fail('Vui lòng điền địa chỉ chi tiết');
        $uid = (int)$request->uid();
        $res = $this->services->editAddress($uid, $addressInfo);
        if ($res) {
            app()->make(WechatUserServices::class)->update(['uid' => $uid], ['province' => $addressInfo['address']['province']]);
            return app('json')->success($res['type'] == 'edit' ? 'Sửa thành công' : $res['data']);
        } else {
            return app('json')->fail('Sửa thất bại');
        }

    }

    /**
     * Xóa địa chỉ
     * @param Request $request
     * @return mixed
     */
    public function address_del(Request $request)
    {
        list($id) = $request->postMore([['id', 0]], true);
        if (!$id || !is_numeric($id)) return app('json')->fail('Tham số không hợp lệ');
        $uid = (int)$request->uid();
        $re = $this->services->delAddress($uid, (int)$id);
        if ($re)
            return app('json')->success('Xóa thành công');
        else
            return app('json')->fail('Xóa thất bại');
    }
}
