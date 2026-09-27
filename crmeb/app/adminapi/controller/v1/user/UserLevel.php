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
namespace app\adminapi\controller\v1\user;

use app\adminapi\controller\AuthController;
use app\services\user\UserLevelServices;
use think\facade\App;

/**
 * Cài đặt thành viên
 * Class UserLevel
 * @package app\adminapi\controller\v1\user
 */
class UserLevel extends AuthController
{

    /**
     * user constructor.
     * @param App $app
     * @param UserLevelServices $services
     */
    public function __construct(App $app, UserLevelServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /*
     * Lấy form thêm resource
     * */
    public function create()
    {
        $where = $this->request->getMore(
            ['id', 0]
        );
        return app('json')->success($this->services->edit((int)$where['id']));
    }

    /*
     * Thêm hoặc sửa hạng thành viên
     * @param $id ID hạng cần sửa
     * @return json
     * */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['name', ''],
            ['is_forever', 0],
            ['money', 0],
            ['is_pay', 0],
            ['valid_date', 0],
            ['grade', 0],
            ['discount', 0],
            ['icon', ''],
            ['image', ''],
            ['is_show', ''],
            ['exp_num', 0]
        ]);
        if ($data['valid_date'] == 0) $data['is_forever'] = 1;//Thời gian hiệu lực bằng 0 nghĩa là vĩnh viễn
        if (!$data['name']) return app('json')->fail(400324);
        if (!$data['grade']) return app('json')->fail(400325);
        if (!$data['icon']) return app('json')->fail(400327);
        if (!$data['image']) return app('json')->fail(400328);
        if (!$data['exp_num']) return app('json')->fail(400329);
        $this->services->save((int)$data['id'], $data);
        return app('json')->success(100000);
    }

    /*
     * Lấy danh sách vip đã cài đặt trong hệ thống
     * @param int page
     * @param int limit
     * */
    public function get_system_vip_list()
    {
        $where = $this->request->getMore([
            ['page', 0],
            ['limit', 10],
            ['title', ''],
            ['is_show', ''],
        ]);
        return app('json')->success($this->services->getSytemList($where));
    }

    /*
     * Xóa hạng thành viên
     * @param int $id
     * */
    public function delete($id)
    {
        return app('json')->success($this->services->delLevel((int)$id));
    }

    /**
     * Thiết lập hiện|ẩn hạng thành viên
     *
     * @return json
     */
    public function set_show($is_show = '', $id = '')
    {
        if ($is_show == '' || $id == '') return app('json')->fail(100100);
        return app('json')->success($this->services->setShow((int)$id, (int)$is_show));
    }

    /**
     * Sửa nhanh danh sách hạng
     * field:value name: Thành viên Kim cương/grade:8/discount:92.00
     * @return json
     */
    public function set_value($id)
    {
        $data = $this->request->postMore([
            ['field', ''],
            ['value', '']
        ]);
        if ($data['field'] == '' || $data['value'] == '') return app('json')->fail(100100);
        $this->services->setValue((int)$id, $data);
        return app('json')->success(100000);
    }


}
