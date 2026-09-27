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

namespace app\adminapi\controller\v1\user\member;

use app\adminapi\controller\AuthController;
use app\services\user\member\MemberCardServices;
use app\services\user\member\MemberRightServices;
use app\services\user\member\MemberShipServices;
use think\facade\App;

/**
 * Class MemberCard
 * @package app\adminapi\controller\v1\user\member
 */
class MemberCard extends AuthController
{
    /**
     * @var MemberCardServices
     */
    protected $services;

    /**
     * Khởi tạo handle tầng service
     * MemberCard constructor.
     * @param App $app
     * @param MemberCardServices $memberCardServices
     */
    public function __construct(App $app, MemberCardServices $memberCardServices)
    {
        parent::__construct($app);
        $this->services = $memberCardServices;
    }

    /**
     * Danh sách thẻ thành viên
     * @param $card_batch_id
     * @return mixed
     */
    public function index($card_batch_id)
    {
        $where = $this->request->getMore([
            ['card_number', ""],
            ['phone', ""],
            ['card_batch_id', $card_batch_id],
            ['is_use', ""],
            ['is_status', ""],
            ['page', 1],
            ['limit', 20],
        ]);
        $data = $this->services->getSearchList($where);
        return app('json')->success($data);

    }

    /**
     * Danh mục thành viên
     * @return mixed
     */
    public function member_ship()
    {
        /** @var MemberShipServices $memberShipService */
        $memberShipService = app()->make(MemberShipServices::class);
        $data = $memberShipService->getSearchList();
        return app('json')->success($data);
    }

    /**
     * Lưu danh mục
     * @param $id
     * @param MemberShipServices $memberShipServices
     * @return mixed
     */
    public function ship_save($id, MemberShipServices $memberShipServices)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['price', ''],
            ['pre_price', ''],
            ['vip_day', ''],
            ['type', ''],
            ['sort', ''],
        ]);
        $memberShipServices->save((int)$id, $data);
        return app('json')->success($id ? 'Sửa thành công' : 'Thêm thành công');
    }

    /**
     * Xóa
     * @param $id
     * @param MemberShipServices $memberShipServices
     * @return mixed
     */
    public function delete($id,MemberShipServices $memberShipServices)
    {
        if (!$id) return app('json')->fail('Dữ liệu không tồn tại');
        $res = $memberShipServices->delete((int)$id);
        return app('json')->success($res ? 'Xóa thành công' : 'Xóa thất bại');
    }

    /**
     * Lấy lịch sử thành viên
     * @return mixed
     */
    public function member_record()
    {
        $where = $this->request->getMore([
            ['name', ""],
            ['add_time', ""],
            ['member_type', ""],
            ['pay_type', ""],
            ['page', 1],
            ['limit', 20],
        ]);
        $data = $this->services->getSearchRecordList($where);
        return app('json')->success($data);
    }

    /**
     * Quyền lợi thành viên
     * @return mixed
     */
    public function member_right()
    {
        /** @var MemberRightServices $memberRightService */
        $memberRightService = app()->make(MemberRightServices::class);
        $data = $memberRightService->getSearchList();
        return app('json')->success($data);
    }

    /**
     * Lưu quyền lợi thành viên
     * @param $id
     * @param MemberRightServices $memberRightServices
     * @return mixed
     */
    public function right_save($id, MemberRightServices $memberRightServices)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['show_title', ''],
            ['image', ''],
            ['right_type', ''],
            ['explain', ''],
            ['number', ''],
            ['sort', ''],
            ['status', ''],
        ]);
        $memberRightServices->save((int)$id, $data);
        return app('json')->success('Sửa quyền lợi thành công');
    }

    /**
     * Sửa trạng thái kích hoạt/khóa thẻ thành viên
     * @return mixed
     */
    public function set_status()
    {
        [$card_id, $status] = $this->request->getMore([
            ['card_id', 0],
            ['status', 0],
        ], true);
        $res = $this->services->setStatus($card_id, $status);
        if ($res) return app('json')->success('Thao tác thành công');
        return app('json')->fail('Thao tác thất bại');
    }

    /**
     * Bật/tắt loại thành viên trả phí
     * @return mixed
     */
    public function set_ship_status()
    {
        [$id, $is_del] = $this->request->getMore([
            ['id', 0],
            ['is_del', 0],
        ], true);
        /** @var MemberShipServices $memberShipService */
        $memberShipService = app()->make(MemberShipServices::class);
        $res = $memberShipService->setStatus($id, $is_del);
        if ($res) return app('json')->success('Thao tác thành công');
        return app('json')->success('Thao tác thất bại');
    }
}
