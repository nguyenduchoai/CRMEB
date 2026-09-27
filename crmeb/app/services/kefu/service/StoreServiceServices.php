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
namespace app\services\kefu\service;


use app\dao\service\StoreServiceDao;
use app\services\BaseServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\FormBuilder;

/**
 * CSKH
 * Class StoreServiceServices
 * @package app\services\kefu\service
 * @method getStoreServiceOrderNotice() Lấy nhân viên CSKH nhận thông báo
 */
class StoreServiceServices extends BaseServices
{

    /**
     * Tạo form
     * @var Form
     */
    protected $builder;

    /**
     * Phương thức khởi tạo
     * StoreServiceServices constructor.
     * @param StoreServiceDao $dao
     */
    public function __construct(StoreServiceDao $dao, FormBuilder $builder)
    {
        $this->dao = $dao;
        $this->builder = $builder;
    }

    /**
     * Lấy danh sách nhân viên CSKH
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
        foreach ($list as &$item) {
            if (strpos($item['avatar'], '/statics/system_images/') !== false) {
                $item['avatar'] = set_file_url($item['avatar']);
            }
        }
        $this->updateNonExistentService(array_column($list, 'uid'));
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * @param array $uids
     * @return bool
     */
    public function updateNonExistentService(array $uids = [])
    {
        if (!$uids) {
            return true;
        }
        /** @var UserServices $services */
        $services = app()->make(UserServices::class);
        $userUids = $services->getColumn([['uid', 'in', $uids]], 'uid');
        $unUids = array_diff($uids, $userUids);
        return $this->dao->deleteNonExistentService($unUids);
    }

    /**
     * Tạo form nhân viên CSKH
     * @param array $formData
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createServiceForm(array $formData = [])
    {
        if ($formData) {
            $field[] = $this->builder->frameImage('avatar', 'Ảnh đại diện CSKH', $this->url(config('app.admin_prefix', 'admin') . '/widget.images/index', ['fodder' => 'avatar'], true), $formData['avatar'] ?? '')->icon('el-icon-user')->width('950px')->height('560px')->props(['footer' => false]);
        } else {
            $field[] = $this->builder->frameImage('image', 'Chọn người dùng', $this->url(config('app.admin_prefix', 'admin') . '/system.user/list', ['fodder' => 'image'], true))->icon('el-icon-user')->width('950px')->height('560px')->Props(['srcKey' => 'image', 'footer' => false]);
            $field[] = $this->builder->hidden('uid', 0);
            $field[] = $this->builder->hidden('avatar', '');
        }
        $field[] = $this->builder->input('nickname', 'Tên nhân viên CSKH', $formData['nickname'] ?? '')->col(24)->required();
        $field[] = $this->builder->input('phone', 'Số điện thoại', $formData['phone'] ?? '')->col(24)->required();
        if ($formData) {
            $field[] = $this->builder->input('account', 'Tài khoản đăng nhập', $formData['account'] ?? '')->col(24)->required();
            $field[] = $this->builder->input('password', 'Mật khẩu đăng nhập')->type('password')->col(24)->placeholder('Để trống nếu không đổi mật khẩu');
            $field[] = $this->builder->input('true_password', 'Xác nhận mật khẩu')->type('password')->col(24)->placeholder('Để trống nếu không đổi mật khẩu');
        } else {
            $field[] = $this->builder->input('account', 'Tài khoản đăng nhập')->col(24)->required();
            $field[] = $this->builder->input('password', 'Mật khẩu đăng nhập')->type('password')->col(24)->required();
            $field[] = $this->builder->input('true_password', 'Xác nhận mật khẩu')->type('password')->col(24)->required();
        }
        $field[] = $this->builder->switches('status', 'Trạng thái CSKH', (string)($formData['status'] ?? 1))->appendControl('1', [
            $this->builder->switches('customer', 'Quản lý đơn hàng trên điện thoại:', (string)($formData['customer'] ?? 0)),
            $this->builder->switches('notify', 'Thông báo đơn hàng:', (string)($formData['notify'] ?? 0)),
        ])->activeValue('1')->inactiveValue('0');
        return $field;
    }

    /**
     * Lấy form tạo nhân viên CSKH
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return create_form('Thêm nhân viên CSKH', $this->createServiceForm(), $this->url('/app/wechat/kefu'), 'POST');
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
        return create_form('Sửa nhân viên CSKH', $this->createServiceForm($serviceInfo->toArray()), $this->url('/app/wechat/kefu/' . $id), 'PUT');
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
     * Kiểm tra người dùng có phải là nhân viên CSKH không
     * @param array $where
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkoutIsService(array $where)
    {
        return (bool)$this->dao->count($where);
    }

    /**
     * Truy vấn bản ghi chat và lấy uid nhân viên CSKH
     * @param int $uid uid người dùng hiện tại
     * @param int $uidTo ID phân trang về trước
     * @param int $limit Số bản ghi hiển thị
     * @param int $toUid uid CSKH
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRecord(int $uid, int $uidTo, int $limit = 10, int $toUid = 0)
    {
        if (!$toUid) {
            $serviceInfoList = $this->getServiceList(['status' => 1, 'online' => 1]);
            if (!count($serviceInfoList)) {
                throw new ApiException('Hiện không có nhân viên CSKH trực tuyến, vui lòng liên hệ lại sau');
            }
            $uids = array_column($serviceInfoList['list'], 'uid');
            if (!$uids) {
                throw new ApiException('Hiện không có nhân viên CSKH trực tuyến, vui lòng liên hệ lại sau');
            }
            /** @var StoreServiceRecordServices $recordServices */
            $recordServices = app()->make(StoreServiceRecordServices::class);
            //Ưu tiên trò chuyện với nhân viên CSKH đã chat lần trước
            $toUid = $recordServices->getLatelyMsgUid(['to_uid' => $uid], 'user_id');
            //Nếu khách chat lần trước không còn trong danh sách nhân viên CSKH hiện tại thì làm lại
            if (!in_array($toUid, $uids)) {
                $toUid = 0;
            }
            if (!$toUid) {
                $toUid = $uids[array_rand($uids)] ?? 0;
            }
            if (!$toUid) {
                throw new ApiException('Hiện không có nhân viên CSKH trực tuyến, vui lòng liên hệ lại sau');
            }
        }
        $userInfo = $this->dao->get(['uid' => $toUid], ['nickname', 'avatar']);
        if (!$userInfo) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userInfo = $userServices->get(['uid' => $toUid], ['nickname', 'avatar']);
            if (!$userInfo) {
                $userInfo['nickname'] = '';
                $userInfo['avatar'] = '';
            }
        }
        if ($userInfo['avatar']) $userInfo['avatar'] = set_file_url($userInfo['avatar']);
        /** @var StoreServiceLogServices $logServices */
        $logServices = app()->make(StoreServiceLogServices::class);
        $result = ['serviceList' => [], 'uid' => $toUid, 'nickname' => $userInfo['nickname'], 'avatar' => $userInfo['avatar']];
        $serviceLogList = $logServices->getServiceChatList(['chat' => [$uid, $toUid], 'is_tourist' => 0], $limit, $uidTo);
        $result['serviceList'] = array_reverse($logServices->tidyChat($serviceLogList));
        return $result;
    }
}
