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
namespace app\adminapi\controller\v1\setting;

use crmeb\exceptions\AdminException;
use app\services\other\CacheServices;
use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\system\config\SystemGroupDataServices;
use app\services\system\config\SystemGroupServices;

/**
 * Quản lý dữ liệu
 * Class SystemGroupData
 * @package app\adminapi\controller\v1\setting
 */
class SystemGroupData extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemGroupData constructor.
     * @param App $app
     * @param SystemGroupDataServices $services
     */
    public function __construct(App $app, SystemGroupDataServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy phần đầu danh sách dữ liệu
     * @return mixed
     */
    public function header(SystemGroupServices $services)
    {
        [$gid, $config_name] = $this->request->getMore([
            ['gid', 0],
            ['config_name', '']
        ], true);
        if (!$gid && !$config_name) return app('json')->fail(100100);
        if (!$gid) {
            $gid = $services->value(['config_name' => $config_name], 'id');
        }
        return app('json')->success($services->getGroupDataTabHeader($gid));
    }

    /**
     * Hiển thị danh sách resource
     *
     * @return \think\Response
     */
    public function index(SystemGroupServices $group)
    {
        $where = $this->request->getMore([
            ['gid', 0],
            ['status', ''],
            ['config_name', '']
        ]);
        if (!$where['gid'] && !$where['config_name']) return app('json')->fail(100100);
        if (!$where['gid']) {
            $where['gid'] = $group->value(['config_name' => $where['config_name']], 'id');
        }
        unset($where['config_name']);
        return app('json')->success($this->services->getGroupDataList($where));
    }

    /**
     * Hiển thị trang form tạo resource.
     *
     * @return \think\Response
     */
    public function create()
    {
        $gid = $this->request->param('gid/d');
        if ($this->services->isGroupGidSave($gid, 4, 'index_categy_images')) {
            return app('json')->fail(400298);
        }
        if ($this->services->isGroupGidSave($gid, 7, 'sign_day_num')) {
            return app('json')->fail(400299);
        }
        return app('json')->success($this->services->createForm($gid));
    }

    /**
     * Lưu resource mới tạo
     *
     * @return \think\Response
     */
    public function save(SystemGroupServices $services)
    {
        $params = request()->post();
        $gid = (int)$params['gid'];
        $group = $services->getOne(['id' => $gid], 'id,config_name,fields');
        if ($group && $group['config_name'] == 'order_details_images') {
            $groupDatas = $this->services->getColumn(['gid' => $gid], 'value', 'id');
            foreach ($groupDatas as $groupData) {
                $groupData = json_decode($groupData, true);
                if (isset($groupData['order_status']['value']) && $groupData['order_status']['value'] == $params['order_status']) {
                    return app('json')->fail(400188);
                }
            }
        }
        if ($group && $group['config_name'] == 'user_recharge_quota') {
            if ($params['price'] <= 0) return app('json')->fail('Giá bán phải lớn hơn 0');
            if ($params['give_money'] < 0) return app('json')->fail('Số tiền tặng không được nhỏ hơn 0');
        }
        $this->services->checkSeckillTime($services, $gid, $params);
        $this->checkSign($services, $gid, $params);
        $fields = json_decode($group['fields'], true) ?? [];
        $value = [];
        foreach ($params as $key => $param) {
            foreach ($fields as $index => $field) {
                if ($key == $field["title"]) {
                    if ($param == "")
                        return app('json')->fail(400297);
                    else {
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }
        $data = [
            "gid" => $params['gid'],
            "add_time" => time(),
            "value" => json_encode($value),
            "sort" => $params["sort"] ?: 0,
            "status" => $params["status"]
        ];
        $this->services->save($data);
        \crmeb\services\CacheService::clear();
        return app('json')->success(400189);
    }

    /**
     * Hiển thị resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * Hiển thị trang form sửa resource.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $gid = $this->request->param('gid/d');
        if (!$gid) {
            return app('json')->fail(100100);
        }
        return app('json')->success($this->services->updateForm((int)$gid, (int)$id));
    }

    /**
     * Lưu resource đã cập nhật
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(SystemGroupServices $services, $id)
    {
        $groupData = $this->services->get($id);
        $fields = $services->getValueFields((int)$groupData["gid"]);
        $params = request()->post();
        $group = $services->getOne(['id' => $params['gid']], 'id,config_name,fields');
        if ($group && $group['config_name'] == 'user_recharge_quota') {
            if ($params['price'] <= 0) return app('json')->fail('Giá bán phải lớn hơn 0');
            if ($params['give_money'] < 0) return app('json')->fail('Số tiền tặng không được nhỏ hơn 0');
        }
        $this->services->checkSeckillTime($services, $groupData["gid"], $params, $id);
        $this->checkSign($services, $groupData["gid"], $params);
        $value = [];
        foreach ($params as $key => $param) {
            foreach ($fields as $index => $field) {
                if ($key == $field["title"]) {
                    if ($param == '')
                        return app('json')->fail(400297);
                    else {
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }
        $data = [
            "value" => json_encode($value),
            "sort" => $params["sort"],
            "status" => $params["status"]
        ];
        $this->services->update($id, $data);
        \crmeb\services\CacheService::clear();
        return app('json')->success(100001);
    }

    /**
     * Xóa resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return app('json')->fail(100008);
        else {
            \crmeb\services\CacheService::clear();
            return app('json')->success(100002);
        }
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) return app('json')->fail(100100);
        $this->services->update($id, ['status' => $status]);
        \crmeb\services\CacheService::clear();
        return app('json')->success(100014);
    }


    /**
     * Kiểm tra cấu hình điểm danh
     * @param SystemGroupServices $services
     * @param $gid
     * @param $params
     * @param int $id
     * @return mixed
     */
    public function checkSign(SystemGroupServices $services, $gid, $params, $id = 0)
    {
        $name = $services->value(['id' => $gid], 'config_name');
        if ($name == 'sign_day_num') {
            if (!$params['sign_num']) {
                throw new AdminException(400196);
            }
            if (!preg_match('/^\+?[1-9]\d*$/', $params['sign_num'])) {
                throw new AdminException(400197);
            }
        }
    }

    /**
     * Lấy nội dung quảng cáo trang chăm sóc khách hàng
     * @return mixed
     */
    public function getKfAdv()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('kf_adv', '');
        return app('json')->success(compact('content'));
    }

    /**
     * Thiết lập nội dung quảng cáo trang chăm sóc khách hàng
     * @return mixed
     */
    public function setKfAdv()
    {
        $content = $this->request->post('content');
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $cache->setDbCache('kf_adv', $content);
        return app('json')->success(100014);
    }

    public function saveAll()
    {
        $params = request()->post();
        if (!isset($params['config_name']) || !isset($params['data'])) {
            return app('json')->fail(100100);
        }
        $this->services->saveAllData($params['data'], $params['config_name']);
        return app('json')->success(400295);
    }


    /**
     * Lấy nội dung thỏa thuận người dùng
     * @return mixed
     */
    public function getUserAgreement()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('user_agreement', '');
        return app('json')->success(compact('content'));
    }

    /**
     * Thiết lập nội dung thỏa thuận người dùng
     * @return mixed
     */
    public function setUserAgreement()
    {
        $content = $this->request->post('content');
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $cache->setDbCache('user_agreement', $content);
        return app('json')->success(100014);
    }
}
