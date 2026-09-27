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
namespace app\adminapi\controller\v1\marketing\live;

use app\adminapi\controller\AuthController;
use app\services\activity\live\LiveRoomServices;
use think\facade\App;

/**
 * Phòng livestream
 * Class LiveRoom
 * @package app\adminapi\controller\v1\marketing\live
 */
class LiveRoom extends AuthController
{
    /**
     * LiveRoom constructor.
     * @param App $app
     * @param LiveRoomServices $services
     */
    public function __construct(App $app, LiveRoomServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách phòng livestream
     * @return mixed
     */
    public function list()
    {
        $where = $this->request->postMore([
            ['kerword', ''],
            ['status', '']
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * Chi tiết phòng livestream
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->get((int)$id)->toArray());
    }

    /**
     * Thêm phòng livestream
     * @return mixed
     */
    public function add()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['cover_img', ''],
            ['share_img', ''],
            ['anchor_name', ''],
            ['anchor_wechat', ''],
            ['phone', ''],
            ['start_time', ['', '']],
            ['type', 1],
            ['screen_type', 1],
            ['close_like', 0],
            ['close_goods', 0],
            ['close_comment', 0],
            ['replay_status', 1],
            ['sort', 0]
        ]);
        if (mb_strlen($data['name']) < 6 || mb_strlen($data['name']) > 17) {
            return app('json')->fail('Độ dài tên phải từ 6-17 ký tự');
        }
        $this->validate($data, \app\adminapi\validate\marketing\LiveRoomValidate::class, 'save');
        $this->services->add($data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Thêm sản phẩm vào phòng livestream
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function addGoods()
    {
        [$room_id, $goods_ids] = $this->request->postMore([
            ['room_id', 0],
            ['goods_ids', []]
        ], true);
        $this->services->exportGoods((int)$room_id, $goods_ids);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Gửi duyệt
     * @param $id
     * @return mixed
     */
    public function apply($id)
    {
        [$status, $msg] = $this->request->postMore([
            ['status', ''],
            ['msg', '']
        ], true);
        $this->services->apply((int)$id, $status, $msg);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Thiết lập trạng thái
     * @param $id
     * @param $is_show
     * @return mixed
     */
    public function setShow($id, $is_show)
    {
        $this->services->isShow((int)$id, $is_show);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Xóa phòng livestream
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->services->delete($id);
        return app('json')->success('Xóa thành công');
    }

    /**
     * Đồng bộ phòng livestream
     * @return mixed
     */
    public function syncRoom()
    {
        $this->services->syncRoomStatus();
        return app('json')->success('Đồng bộ thành công');
    }

}
