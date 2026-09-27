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

namespace app\services\order;


use app\dao\order\DeliveryServiceDao;
use app\services\BaseServices;
use app\services\kefu\service\StoreServiceLogServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder;


/**
 * Giao hàng
 * Class DeliveryServiceServices
 * @package app\services\order
 * @method getStoreServiceOrderNotice() Lấy nhân viên CSKH nhận thông báo
 */
class DeliveryServiceServices extends BaseServices
{
    /**
     * Tạo form
     * @var Form
     */
    protected $builder;

    /**Phương thức khởi tạo
     * DeliveryServiceServices constructor.
     * @param DeliveryServiceDao $dao
     * @param FormBuilder $builder
     */
    public function __construct(DeliveryServiceDao $dao, FormBuilder $builder)
    {
        $this->dao = $dao;
        $this->builder = $builder;
    }

    /**
     * Lấy danh sách nhân viên giao hàng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getServiceList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     *Lấy danh sách nhân viên giao hàng
     */
    public function getDeliveryList()
    {
        [$page, $limit] = $this->getPageValue();
        [$list, $count] = $this->dao->getList($page, $limit);
        return compact('list', 'count');
    }

    /**
     * Tạo form người giao hàng
     * @param array $formData
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createServiceForm(array $formData = [])
    {
        if ($formData) {
            $field[] = $this->builder->frameImage('avatar', 'Ảnh đại diện nhân viên giao hàng', $this->url(config('app.admin_prefix', 'admin') . '/widget.images/index', ['fodder' => 'avatar'], true), $formData['avatar'] ?? '')->icon('el-icon-user')->width('950px')->height('560px')->props(['footer' => false]);
        } else {
            $field[] = $this->builder->frameImage('image', 'Người dùng cửa hàng', $this->url(config('app.admin_prefix', 'admin') . '/system.user/list', ['fodder' => 'image'], true))->icon('el-icon-user')->width('950px')->height('560px')->Props(['srcKey' => 'image', 'footer' => false]);
            $field[] = $this->builder->hidden('uid', 0);
            $field[] = $this->builder->hidden('avatar', '');
        }
        $field[] = $this->builder->input('nickname', 'Tên nhân viên giao hàng', $formData['nickname'] ?? '')->required('Vui lòng nhập tên')->col(24);
        $field[] = $this->builder->input('phone', 'Số điện thoại', $formData['phone'] ?? '')->required('Vui lòng nhập số điện thoại')->col(24)->maxlength(11);
        $field[] = $this->builder->radio('status', 'Trạng thái nhân viên giao hàng', $formData['status'] ?? 1)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return $field;
    }

    /**
     * Lấy form tạo người giao hàng
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return create_form('Thêm nhân viên giao hàng', $this->createServiceForm(), $this->url('/order/delivery/save'), 'POST');
    }

    /**
     * Lấy form sửa
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit(int $id)
    {
        $serviceInfo = $this->dao->get($id);
        if (!$serviceInfo) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        return create_form('Sửa nhân viên giao hàng', $this->createServiceForm($serviceInfo->toArray()), $this->url('/order/delivery/update/' . $id), 'PUT');
    }

    /**
     * Lấy danh sách người dùng đã chat với một người
     * @param int $uid
     * @return array|array[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatUser(int $uid)
    {
        /** @var StoreServiceLogServices $serviceLog */
        $serviceLog = app()->make(StoreServiceLogServices::class);
        /** @var UserServices $serviceUser */
        $serviceUser = app()->make(UserServices::class);
        $uids = $serviceLog->getChatUserIds($uid);
        if (!$uids) {
            return [];
        }
        return $serviceUser->getUserList(['uid' => $uids], 'nickname,uid,avatar as headimgurl');
    }

    /**
     * Kiểm tra người dùng có phải người giao hàng không
     * @param int $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkoutIsService(int $uid)
    {
        return (bool)$this->dao->count(['uid' => $uid, 'status' => 1]);
    }

    /**
     * Lưu resource mới tạo
     * @param array $data
     * @return void
     */
    public function saveDeliveryService(array $data)
    {
        if ($data['image'] == '') throw new AdminException('Vui lòng chọn người dùng');
        $data['uid'] = $data['image']['uid'];
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $userInfo = $userService->get($data['uid']);
        if ($data['phone'] == '') {
            if (!$userInfo['phone']) {
                throw new AdminException('Vui lòng điền số điện thoại');
            } else {
                $data['phone'] = $userInfo['phone'];
            }
        } else {
            if (!check_phone($data['phone'])) {
                throw new AdminException('Số điện thoại sai định dạng');
            }
        }
        if ($data['nickname'] == '') $data['nickname'] = $userInfo['nickname'];
        $data['avatar'] = $data['image']['image'];
        if ($this->dao->count(['uid' => $data['uid']])) {
            throw new AdminException('Nhân viên giao hàng đã tồn tại');
        }
        if ($this->dao->count(['phone' => $data['phone']])) {
            throw new AdminException('Mỗi số điện thoại chỉ được thêm một nhân viên giao hàng');
        }
        unset($data['image']);
        $data['add_time'] = time();
        $res = $this->dao->save($data);
        if (!$res) throw new AdminException('Lưu thất bại');
        return true;
    }

    /**
     * Cập nhật tài nguyên
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateDeliveryService(int $id, array $data)
    {
        $delivery = $this->dao->get($id);
        if (!$delivery) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        if ($data["nickname"] == '') {
            throw new AdminException('Tên nhân viên giao hàng không được để trống');
        }
        if (!$data['phone']) {
            throw new AdminException('Vui lòng điền số điện thoại');
        }
        if (!check_phone($data['phone'])) {
            throw new AdminException('Số điện thoại sai định dạng');
        }
        if ($delivery['phone'] != $data['phone'] && $this->dao->count(['phone' => $data['phone']])) {
            throw new AdminException('Mỗi số điện thoại chỉ được thêm một nhân viên giao hàng');
        }
        $res = $this->dao->update($id, $data);
        if (!$res) throw new AdminException('Sửa thất bại');
        return true;
    }
}
