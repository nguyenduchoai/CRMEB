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

use app\adminapi\controller\AuthController;
use app\services\shipping\SystemCityServices;
use think\facade\App;
use crmeb\services\{CacheService};


/**
 * Dữ liệu thành phố
 * Class SystemCity
 * @package app\adminapi\controller\v1\setting
 */
class SystemCity extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemCity constructor.
     * @param App $app
     * @param SystemCityServices $services
     */
    public function __construct(App $app, SystemCityServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách thành phố
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['parent_id', 'd'], 0]
        ]);
        return app('json')->success($this->services->getCityList($where));
    }

    /**
     * Thêm thành phố
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        [$parentId] = $this->request->getMore([
            [['parent_id', 'd'], 0]
        ], true);
        return app('json')->success($this->services->createCityForm($parentId));
    }

    /**
     * Lưu
     */
    public function save()
    {
        $data = $this->request->postMore([
            [['id', 'd'], 0],
            [['name', 's'], ''],
            [['merger_name', 's'], ''],
            [['area_code', 's'], ''],
            [['lng', 's'], ''],
            [['lat', 's'], ''],
            [['level', 'd'], 0],
            [['parent_id', 'd'], 0],
        ]);
        $this->validate($data, \app\adminapi\validate\setting\SystemCityValidate::class, 'save');
        if ($data['parent_id'] == 0) {
            $data['merger_name'] = $data['name'];
        } else {
            $data['merger_name'] = $this->services->value(['id' => $data['parent_id']], 'name') . ',' . $data['name'];
        }
        if ($data['id'] == 0) {
            unset($data['id']);
            $data['level'] = $data['level'] + 1;
            $data['city_id'] = intval($this->services->getCityIdMax() + 1);
            $this->services->save($data);
            return app('json')->success(100000);
        } else {
            unset($data['level']);
            unset($data['parent_id']);
            $this->services->update($data['id'], $data);
            return app('json')->success(100001);
        }
    }

    /**
     * Sửa thành phố
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        [$id] = $this->request->getMore([
            [['id', 'd'], 0]
        ], true);
        return app('json')->success($this->services->updateCityForm($id));
    }

    /**
     * Xóa thành phố
     * @throws \Exception
     */
    public function delete()
    {
        [$id] = $this->request->getMore([
            [['city_id', 'd'], 0]
        ], true);
        $this->services->deleteCity($id);
        return app('json')->success(100002);
    }

    /**
     * Xóa cache thành phố
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function clean_cache()
    {
        CacheService::delete('CITY_LIST');
        CacheService::delete('CITY_FULL_LIST');
        return app('json')->success(400185);
    }

    /**
     * Lấy danh sách đầy đủ dữ liệu thành phố
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/10
     */
    public function fullList()
    {
        return app('json')->success($this->services->fullList('parent_id,name as label,city_id as value'));
    }
}
