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
namespace app\adminapi\controller\v1\marketing;

use app\adminapi\controller\AuthController;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\activity\StoreActivityServices;
use app\services\product\sku\StoreProductAttrValueServices;
use crmeb\services\CacheService;
use think\facade\App;

/**
 * Controller flash sale giới hạn thời gian
 * Class StoreSeckill
 * @package app\admin\controller\store
 */
class StoreSeckill extends AuthController
{
    public function __construct(App $app, StoreSeckillServices $services)
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
        $where = $this->request->getMore([
            ['start_status', ''],
            [['status', 's'], ''],
            [['store_name', 's'], ''],
            [['product_id', 'd'], 0],
            ['activity_name', ''],
            ['time', ''],
            ['time_ids', []],
        ]);
        return app('json')->success($this->services->systemPage($where));
    }

    /**
     * Chi tiết
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        $info = $this->services->getInfo($id);
        return app('json')->success(compact('info'));
    }

    /**
     * Lưu sản phẩm flash sale
     * @param int $id
     */
    public function save($id)
    {
        $data = $this->request->postMore([
            [['product_id', 'd'], 0],
            [['title', 's'], ''],
            [['info', 's'], ''],
            [['unit_name', 's'], ''],
            ['images', []],
            [['give_integral', 'd'], 0],
            ['section_time', []],
            [['is_hot', 'd'], 0],
            [['status', 'd'], 0],
            [['num', 'd'], 0],
            [['once_num', 'd'], 0],
            ['time_id', []],
            [['temp_id', 'd'], 0],
            [['sort', 'd'], 0],
            [['description', 's'], ''],
            ['attrs', []],
            ['items', []],
            ['copy', 0],
            ['logistics', []],//Hình thức vận chuyển
            ['freight', 1],//Cài đặt phí vận chuyển
            ['postage', 0],//Phí vận chuyển
            ['custom_form', ''],
            ['virtual_type', 0],
            ['is_commission', 0],
        ]);
        $this->validate($data, \app\adminapi\validate\marketing\StoreSeckillValidate::class, 'save');
        $this->services->saveData($id, $data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Xóa flash sale
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->update($id, ['is_del' => 1]);
        /** @var StoreProductAttrValueServices $storeProductAttrValueServices */
        $storeProductAttrValueServices = app()->make(StoreProductAttrValueServices::class);
        $unique = $storeProductAttrValueServices->value(['product_id' => $id, 'type' => 1], 'unique');
        if ($unique) {
            CacheService::delete('seckill_' . $unique . '_1');
        }
        return app('json')->success('Xóa thành công');
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == 1) {
            $info = $this->services->get($id);
            if ($info['stop_time'] < time()) {
                return app('json')->fail('Hoạt động đã kết thúc, không thể tiếp tục đăng bán');
            }
        }
        $this->services->update($id, ['status' => $status]);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Danh sách khung giờ flash sale
     * @return mixed
     */
    public function time_list()
    {
        $list['data'] = sys_data('routine_seckill_time');
        foreach ($list['data'] as &$item) {
            $startTime = sprintf("%02d:00", $item['time']);
            $endTime = sprintf("%02d:00", $item['time'] + $item['continued']);
            $item['time_name'] = $startTime . '-' . $endTime;
        }
        return app('json')->success(compact('list'));
    }

    /**
     * Thống kê flash sale
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function seckillStatistics($id)
    {
        $data = $this->services->seckillStatistics($id);
        return app('json')->success($data);
    }

    /**
     * Thống kê người tham gia flash sale
     * @param $id
     * @return mixed
     */
    public function seckillPeople($id)
    {
        [$keyword] = $this->request->getMore([
            ['real_name', '', '', 'keyword']
        ], true);
        return app('json')->success($this->services->seckillPeople($id, $keyword));
    }

    /**
     * Thống kê đơn hàng flash sale
     * @param $id
     * @return mixed
     */
    public function seckillOrder($id)
    {
        $where = $this->request->getMore([
            ['real_name', ''],
            ['status', '']
        ]);
        return app('json')->success($this->services->seckillOrder($id, $where));
    }

    public function seckillActivityList()
    {
        $where = $this->request->getMore([
            ['time', ''],
            ['status', ''],
            ['title', ''],
            ['time_ids', []]
        ]);
        $where['is_del'] = 0;
        $where['type'] = 1;
        return app('json')->success(app()->make(StoreActivityServices::class)->activityList($where));
    }

    public function seckillActivityInfo($id)
    {
        return app('json')->success(app()->make(StoreActivityServices::class)->activityInfo($id));
    }

    public function seckillActivitySave($id)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['section_time', []],
            ['time_ids', []],
            ['num', 0],
            ['once_num', 0],
            ['status', 1],
            ['is_commission', 0],
            ['product_infos', []]
        ]);
        $this->services->seckillActivitySave($id, $data);
        return app('json')->success('Lưu thành công');
    }

    public function seckillActivityDel($id)
    {
        app()->make(StoreActivityServices::class)->activityDel($id, 1);
        return app('json')->success('Xóa thành công');
    }

    public function seckillActivityStatus($id, $status)
    {
        app()->make(StoreActivityServices::class)->activityStatus($id, $status, 1);
        return app('json')->success('Sửa thành công');
    }
}
