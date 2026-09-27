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
namespace app\adminapi\controller\v1\marketing\live;

use app\adminapi\controller\AuthController;
use app\services\activity\live\LiveAnchorServices;
use think\facade\App;

/**
 * Streamer của phòng livestream
 * Class LiveAnchor
 * @package app\controller\admin\store
 */
class LiveAnchor extends AuthController
{
    /**
     * LiveAnchor constructor.
     * @param App $app
     * @param LiveAnchorServices $services
     */
    public function __construct(App $app, LiveAnchorServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách
     * @return mixed
     */
    public function list()
    {
        $where = $this->request->postMore([
            ['kerword', ''],
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * Form thêm/sửa
     * @return mixed
     */
    public function add()
    {
        list($id) = $this->request->getMore([
            ['id', 0],
        ], true);
        return app('json')->success($this->services->add((int)$id));
    }

    /**
     * Lưu dữ liệu form nhãn
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['name', ''],
            ['wechat', ''],
            ['phone', ''],
            ['cover_img', '']
        ]);
        $this->validate($data, \app\adminapi\validate\marketing\LiveAnchorValidate::class, 'save');
        $res = $this->services->save((int)$data['id'], $data);
        if ($res === true) {
            return app('json')->success(100000, ['auth' => false]);
        }else{
            return app('json')->fail(100006);
        }
    }

    /**
     * Xóa
     * @return mixed
     * @throws \Exception
     */
    public function delete()
    {
        list($id) = $this->request->getMore([
            ['id', 0],
        ], true);
        if (!$id) return app('json')->fail(100100);
        $this->services->delAnchor((int)$id);
        return app('json')->success(100002);
    }

    /**
     * Thiết lập hiện|ẩn hạng thành viên
     * @param string $id
     * @param string $is_show
     * @return mixed
     */
    public function setShow($id = '', $is_show = '')
    {
        if ($is_show == '' || $id == '') return app('json')->fail(100100);
        $this->services->setShow((int)$id, (int)$is_show);
        return app('json')->success(100014);
    }

    /**
     * Đồng bộ streamer
     * @return mixed
     */
    public function syncAnchor()
    {
        $this->services->syncAnchor();
        return app('json')->success(100038);
    }
}
