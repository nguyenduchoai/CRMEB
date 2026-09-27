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
namespace app\adminapi\controller\v1\marketing\integral;

use app\adminapi\controller\AuthController;
use app\services\activity\integral\StoreIntegralServices;
use think\facade\App;

/**
 * Quản lý cửa hàng đổi điểm
 * Class StoreCombination
 * @package app\admin\controller\store
 */
class StoreIntegral extends AuthController
{
    /**
     * StoreIntegral constructor.
     * @param App $app
     * @param StoreIntegralServices $services
     */
    public function __construct(App $app, StoreIntegralServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách sản phẩm đổi điểm
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['integral_time', ''],
            ['is_show', ''],
            ['store_name', '']
        ]);
        $where['is_del'] = 0;
        $list = $this->services->systemPage($where);
        return app('json')->success($list);
    }

    /**
     * Lưu sản phẩm
     * @param int $id
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            [['product_id', 'd'], 0],
            [['title', 's'], ''],
            [['unit_name', 's'], ''],
            ['image', ''],
            ['images', []],
            [['num', 'd'], 0],
            [['is_host', 'd'], 0],
            [['is_show', 'd'], 0],
            [['once_num', 'd'], 0],
            [['sort', 'd'], 0],
            [['description', 's'], ''],
            ['attrs', []],
            ['items', []],
            ['copy', 0]
        ]);
        $this->validate($data, \app\adminapi\validate\marketing\StoreIntegralValidate::class, 'save');
        if ($id) {
            $integral = $this->services->get((int)$id);
            if (!$integral) {
                return app('json')->fail(100026);
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
     * Thêm sản phẩm theo lô
     * @return mixed
     */
    public function batch_add()
    {
        $data = $this->request->postMore([
            ['attrs', []],
            [['is_show', 'd'], 0]
        ]);
        $this->services->saveBatchData($data);
        return app('json')->success(100000);
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
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_show($id, $is_show)
    {
        $this->services->update($id, ['is_show' => $is_show]);
        return app('json')->success(100014);
    }

    /**
     * Xóa resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        $this->services->update($id, ['is_del' => 1]);
        return app('json')->success(100002);
    }

}
