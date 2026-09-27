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
namespace app\adminapi\controller\v1\kefu;

use app\adminapi\controller\AuthController;
use app\services\kefu\LoginServices;
use app\services\kefu\service\StoreServiceLogServices;
use app\services\kefu\service\StoreServiceServices;
use app\services\user\UserServices;
use app\services\user\UserWechatuserServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use think\facade\App;

/**
 * Quản lý CSKH
 * Class StoreService
 * @package app\admin\controller\store
 */
class StoreService extends AuthController
{
    /**
     * StoreService constructor.
     * @param App $app
     * @param StoreServiceServices $services
     */
    public function __construct(App $app, StoreServiceServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
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
     * Hiển thị trang form tạo resource
     * @param UserWechatuserServices $services
     * @return mixed
     */
    public function create(UserWechatuserServices $services)
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['data', '', '', 'time'],
            ['type', '', '', 'user_type'],
        ]);
        $where['is_del'] = 0;
        [$list, $count] = $services->getWhereUserList($where, 'u.nickname,u.uid,u.avatar as headimgurl,w.subscribe,w.province,w.country,w.city,w.sex,u.user_type,u.is_del,u.phone,u.add_time');
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d', $item['add_time']);
        }
        return app('json')->success(compact('list', 'count'));
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
     * Lưu resource mới tạo
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['image', ''],
            ['uid', 0],
            ['avatar', ''],
            ['customer', ''],
            ['notify', ''],
            ['phone', ''],
            ['account', ''],
            ['password', ''],
            ['true_password', ''],
            ['phone', ''],
            ['nickname', ''],
            ['status', 1],
        ]);
        if ($data['image'] == '') return app('json')->fail(400250);
        $data['uid'] = $data['image']['uid'];
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $userInfo = $userService->get($data['uid']);
        if ($data['phone'] == '') {
            if (!$userInfo['phone']) {
                throw new AdminException(400251);
            } else {
                $data['phone'] = $userInfo['phone'];
            }
        } else {
            if (!check_phone($data['phone'])) {
                throw new AdminException(400252);
            }
        }
        if ($data['nickname'] == '') $data['nickname'] = $userInfo['nickname'];
        $data['avatar'] = $data['image']['image'];
        if ($this->services->count(['uid' => $data['uid']])) {
            return app('json')->fail(400253);
        }
        unset($data['image']);
        $data['add_time'] = time();
        if (!$data['account']) {
            return app('json')->fail(400254);
        }
        if (!preg_match('/^[a-zA-Z0-9]{4,30}$/', $data['account'])) {
            return app('json')->fail(400255);
        }
        if (!$data['password']) {
            return app('json')->fail(400256);
        }
        if (!preg_match('/^[0-9a-z_$]{6,20}$/i', $data['password'])) {
            return app('json')->fail(400257);
        }
        if ($this->services->count(['phone' => $data['phone']])) {
            return app('json')->fail(400258);
        }
        if ($this->services->count(['account' => $data['account']])) {
            return app('json')->fail(400259);
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $res = $this->services->save($data);
        if ($res) {
            return app('json')->success(400260);
        } else {
            return app('json')->fail(400261);
        }
    }

    /**
     * Hiển thị trang form sửa resource
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->edit((int)$id));
    }

    /**
     * Lưu resource mới tạo
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['avatar', ''],
            ['nickname', ''],
            ['account', ''],
            ['phone', ''],
            ['status', 1],
            ['notify', 1],
            ['customer', 1],
            ['password', ''],
            ['true_password', ''],
        ]);
        $customer = $this->services->get((int)$id);
        if (!$customer) {
            return app('json')->fail(100026);
        }
        if ($data["nickname"] == '') {
            return app('json')->fail(400262);
        }
        if (!check_phone($data['phone'])) {
            return app('json')->fail(400252);
        }
        if ($customer['phone'] != $data['phone'] && $this->services->count(['phone' => $data['phone']])) {
            return app('json')->fail(400258);
        }
        if ($data['password']) {
            if (!preg_match('/^[0-9a-z_$]{6,16}$/i', $data['password'])) {
                return app('json')->fail(400257);
            }
            if (!$data['true_password']) {
                return app('json')->fail(400263);
            }
            if ($data['password'] != $data['true_password']) {
                return app('json')->fail(400264);
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $this->services->update($id, $data);
        return app('json')->success(100001);
    }

    /**
     * Xóa resource được chỉ định
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return app('json')->fail(100008);
        else
            return app('json')->success(100002);
    }

    /**
     * Sửa trạng thái
     * @param UserServices $services
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status(UserServices $services, $id, $status)
    {
        if ($status == '' || $id == 0) return app('json')->fail(100100);
        $info = $this->services->get($id, ['status', 'uid']);
        if (!$services->count(['uid' => $info['uid']])) {
            $info->status = 1;
            $info->save();
            return app('json')->fail(400265);
        }
        $info->status = $status;
        $info->save();
        return app('json')->success(100014);
    }

    /**
     * Lịch sử trò chuyện
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chat_user($id)
    {
        $uid = $this->services->value(['id' => $id], 'uid');
        if (!$uid) {
            return app('json')->fail(100026);
        }
        return app('json')->success($this->services->getChatUser((int)$uid));
    }


    /**
     * Lịch sử trò chuyện
     * @param StoreServiceLogServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chat_list(StoreServiceLogServices $services)
    {
        $data = $this->request->getMore([
            ['uid', 0],
            ['to_uid', 0],
            ['id', 0]
        ]);
        if ($data['uid']) {
            CacheService::set('admin_chat_list' . $this->adminId, $data);
        }
        $data = CacheService::get('admin_chat_list' . $this->adminId);
        if ($data['uid']) {
            $where = [
                'chat' => [$data['uid'], $data['to_uid']],
            ];
        } else {
            $where = [];
        }
        $list = $services->getChatLogList($where);
        return app('json')->success($list);
    }

    /**
     * Đăng nhập CSKH
     * @param LoginServices $services
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function keufLogin(LoginServices $services, $id)
    {
        $serviceInfo = $services->get($id);
        if (!$serviceInfo) {
            return app('json')->fail(400266);
        }
        if (!$serviceInfo->account || !$serviceInfo->password) {
            return app('json')->fail(400267);
        }
        return app('json')->success($services->authLogin($serviceInfo->account));
    }

}
