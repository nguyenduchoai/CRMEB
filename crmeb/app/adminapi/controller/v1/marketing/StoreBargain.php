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
namespace app\adminapi\controller\v1\marketing;

use app\adminapi\controller\AuthController;
use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\bargain\StoreBargainUserHelpServices;
use app\services\activity\bargain\StoreBargainUserServices;
use think\facade\App;

/**
 * Quản lý săn giảm giá
 * Class StoreBargain
 * @package app\adminapi\controller\v1\marketing
 */
class StoreBargain extends AuthController
{
    /**
     * StoreBargain constructor.
     * @param App $app
     * @param StoreBargainServices $services
     */
    public function __construct(App $app, StoreBargainServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách săn giảm giá
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['start_status', ''],
            ['status', ''],
            ['store_name', ''],
            ['product_id', 0],
        ]);
        $where['is_del'] = 0;
        $list = $this->services->getStoreBargainList($where);
        return app('json')->success($list);
    }

    /**
     * Lưu sản phẩm săn giảm giá
     * @param $id
     * @return mixed
     */
    public function save($id)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['info', ''],
            ['unit_name', ''],
            ['section_time', []],
            ['image', ''],
            ['images', []],
            ['bargain_max_price', 0],
            ['bargain_min_price', 0],
            ['sort', 0],
            ['give_integral', 0],
            ['is_hot', 0],
            ['status', 0],
            ['product_id', 0],
            ['description', ''],
            ['attrs', []],
            ['items', []],
            ['temp_id', 0],
            ['rule', ''],
            ['num', 1],
            ['copy', 0],
            ['bargain_num', 1],
            ['people_num', 1],
            ['logistics', []],//Hình thức vận chuyển
            ['freight', 1],//Cài đặt phí vận chuyển
            ['postage', 0],//Phí vận chuyển
            ['custom_form', ''],
            ['virtual_type', 0],
            ['is_commission', 0],
        ]);
        $this->validate($data, \app\adminapi\validate\marketing\StoreBargainValidate::class, 'save');
        if ($data['section_time']) {
            [$start_time, $end_time] = $data['section_time'];
            if (strtotime($end_time) < time()) {
                return app('json')->fail(400507);
            }
        }
        $bragain = [];
        if ($id) {
            $bragain = $this->services->get((int)$id);
            if (!$bragain) {
                return app('json')->fail(100026);
            }
        }
        //Hạn chế sửa
        if ($data['copy'] == 0 && $bragain) {
            if ($bragain['stop_time'] < time()) {
                return app('json')->fail(400508);
            }
        }
        if ($data['copy'] == 1) {
            $id = 0;
            unset($data['copy']);
        }
        $this->services->saveData($id, $data);
        return app('json')->success(100000);
    }

    /**
     * Lấy chi tiết
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        $info = $this->services->getInfo($id);
        return app('json')->success(compact('info'));
    }

    /**
     * Xóa săn giảm giá
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->services->update($id, ['is_del' => 1]);
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        $bargainUserService->userBargainStatusFail($id, true);
        return app('json')->success(100002);
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        if ($status == 0) {
            $bargainUserService->userBargainStatusFail($id, false);
        } else {
            $info = $this->services->get($id);
            if ($info['stop_time'] < time()) {
                return app('json')->fail('Hoạt động đã kết thúc, không thể tiếp tục đăng bán');
            }
        }
        $this->services->update($id, ['status' => $status]);
        return app('json')->success(100001);
    }

    /**
     * Danh sách săn giảm giá
     * @return mixed
     */
    public function bargainList()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['data', '', '', 'time'],
        ]);
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        $list = $bargainUserService->bargainUserList($where);
        return app('json')->success($list);
    }

    /**
     * Thông tin săn giảm giá
     * @param $id
     * @return mixed
     */
    public function bargainListInfo($id)
    {
        /** @var StoreBargainUserHelpServices $bargainUserHelpService */
        $bargainUserHelpService = app()->make(StoreBargainUserHelpServices::class);
        $list = $bargainUserHelpService->getHelpList((int)$id);
        return app('json')->success(compact('list'));
    }

    /**
     * Thống kê săn giảm giá
     * @param $id
     * @return mixed
     */
    public function bargainStatistics($id)
    {
        $data = $this->services->bargainStatistics($id);
        return app('json')->success($data);
    }

    /**
     * Danh sách săn giảm giá
     * @param $id
     * @return mixed
     */
    public function bargainStatisticsList($id)
    {
        $where = $this->request->getMore([
            ['real_name', ''],
        ]);
        $data = $this->services->bargainStatisticsList($id, $where);
        return app('json')->success($data);
    }

    /**
     * Đơn săn giảm giá
     * @param $id
     * @return mixed
     */
    public function bargainStatisticsOrder($id)
    {
        $where = $this->request->getMore([
            ['real_name', ''],
            ['status', '']
        ]);
        return app('json')->success($this->services->bargainStatisticsOrder($id, $where));
    }
}
