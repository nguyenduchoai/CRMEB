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

namespace app\services\system\store;


use app\dao\system\store\SystemStoreStaffDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder;

/**
 * Nhân viên cửa hàng
 * Class SystemStoreStaffServices
 * @package app\services\system\store
 * @mixin SystemStoreStaffDao
 */
class SystemStoreStaffServices extends BaseServices
{
    /**
     * @var FormBuilder
     */
    protected $builder;

    /**
     * Phương thức khởi tạo
     * SystemStoreStaffServices constructor.
     * @param SystemStoreStaffDao $dao
     * @param FormBuilder $builder
     */
    public function __construct(SystemStoreStaffDao $dao, FormBuilder $builder)
    {
        $this->dao = $dao;
        $this->builder = $builder;
    }

    /**
     * Kiểm tra có phải nhân viên cửa hàng có quyền xác nhận sử dụng không
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function verifyStatus($uid)
    {
        return (bool)$this->dao->getOne(['uid' => $uid, 'status' => 1, 'verify_status' => 1]);
    }

    /**
     * Lấy danh sách nhân viên cửa hàng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreStaffList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getStoreStaffList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy danh sách cửa hàng trong select box
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreSelectFormData()
    {
        /** @var SystemStoreServices $service */
        $service = app()->make(SystemStoreServices::class);
        $menus = [];
        foreach ($service->getStore() as $menu) {
            $menus[] = ['value' => $menu['id'], 'label' => $menu['name']];
        }
        return $menus;
    }

    /**
     * Lấy biểu mẫu nhân viên xác nhận sử dụng
     * @param array $formData
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createStoreStaffForm(array $formData = [])
    {
        if ($formData) {
            $field[] = $this->builder->frameImage('image', 'Đổi ảnh đại diện', $this->url(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'image'),true), $formData['avatar'] ?? '')->icon('el-icon-user')->width('950px')->height('560px')->props(['footer' => false]);
        } else {
            $field[] = $this->builder->frameImage('image', 'Người dùng cửa hàng', $this->url(config('app.admin_prefix', 'admin') . '/system.User/list', ['fodder' => 'image'], true))->icon('el-icon-user')->width('950px')->height('560px')->Props(['srcKey' => 'image', 'footer' => false]);
        }
        $field[] = $this->builder->hidden('uid', $formData['uid'] ?? 0);
        $field[] = $this->builder->hidden('avatar', $formData['avatar'] ?? '');
        $field[] = $this->builder->select('store_id', 'Điểm nhận hàng trực thuộc', ($formData['store_id'] ?? ''))->setOptions($this->getStoreSelectFormData())->filterable(true);
        $field[] = $this->builder->input('staff_name', 'Tên nhân viên xác nhận', $formData['staff_name'] ?? '')->col(24)->required();
        $field[] = $this->builder->input('phone', 'Số điện thoại', $formData['phone'] ?? '')->col(24)->required();
        $field[] = $this->builder->radio('verify_status', 'Cho phép xác nhận sử dụng', $formData['verify_status'] ?? 1)->options([['value' => 1, 'label' => 'Bật'], ['value' => 0, 'label' => 'Tắt']]);
        $field[] = $this->builder->radio('status', 'Trạng thái', $formData['status'] ?? 1)->options([['value' => 1, 'label' => 'Bật'], ['value' => 0, 'label' => 'Tắt']]);
        return $field;
    }

    /**
     * Biểu mẫu thêm nhân viên xác nhận sử dụng
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createForm()
    {
        return create_form('Thêm nhân viên xác nhận', $this->createStoreStaffForm(), $this->url('/merchant/store_staff/save/0'));
    }

    /**
     * Biểu mẫu (form) sửa nhân viên xác nhận sử dụng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateForm(int $id)
    {
        $storeStaff = $this->dao->get($id);
        if (!$storeStaff) {
            throw new AdminException(100026);
        }
        return create_form('Sửa nhân viên xác nhận', $this->createStoreStaffForm($storeStaff->toArray()), $this->url('/merchant/store_staff/save/' . $id));
    }

}
