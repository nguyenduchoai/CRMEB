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
namespace app\adminapi\controller\v1\setting;

use app\services\system\config\SystemGroupDataServices;
use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\system\config\SystemGroupServices;

/**
 * Dữ liệu tổ hợp
 * Class SystemGroup
 * @package app\adminapi\controller\v1\setting
 */
class SystemGroup extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemGroup constructor.
     * @param App $app
     * @param SystemGroupServices $services
     */
    public function __construct(App $app, SystemGroupServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', '']
        ]);
        return app('json')->success($this->services->getGroupList($where));
    }

    /**
     * Hiển thị trang form tạo resource.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * Lưu resource mới tạo
     *
     * @return \think\Response
     */
    public function save()
    {
        $params = $this->request->postMore([
            ['name', ''],
            ['config_name', ''],
            [['cate_id', 'd'], 0],
            ['info', ''],
            ['typelist', []],
        ]);

        //Kiểm tra tên nhóm dữ liệu
        if (!$params['name']) {
            return app('json')->fail('Vui lòng nhập tên');
        }
        if (!$params['config_name']) {
            return app('json')->fail('Vui lòng nhập tên cấu hình');
        }
        $data["name"] = $params['name'];
        $data["config_name"] = $params['config_name'];
        $data["info"] = $params['info'];
        $data["cate_id"] = $params['cate_id'];
        //Kiểm tra thông tin trường
        if (!count($params['typelist']))
            return app('json')->fail('Phải có ít nhất một trường');
        else {
            $validate = ["name", "type", "title", "description"];
            foreach ($params["typelist"] as $key => $value) {
                foreach ($value as $name => $field) {
                    if (empty($field["value"]) && in_array($name, $validate))
                        return app('json')->fail("Trường" . ($key + 1) . "：" . $field["placeholder"] . "không được để trống!");
                    else
                        $data["fields"][$key][$name] = $field["value"];
                }
            }
        }
        $data["fields"] = json_encode($data["fields"]);
        $this->services->save($data);
        \crmeb\services\CacheService::clear();
        return app('json')->success('Thêm nhóm dữ liệu thành công');
    }

    /**
     * Hiển thị resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $info = $this->services->get($id);
        $fields = json_decode($info['fields'], true);
        $type_list = [];
        foreach ($fields as $key => $v) {
            $type_list[$key]['name']['value'] = $v['name'];
            $type_list[$key]['title']['value'] = $v['title'];
            $type_list[$key]['type']['value'] = $v['type'];
            $type_list[$key]['param']['value'] = $v['param'];
        }
        $info['typelist'] = $type_list;
        unset($info['fields']);
        return app('json')->success(compact('info'));
    }

    /**
     * Hiển thị trang form sửa resource.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Lưu resource đã cập nhật
     *
     * @param int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $params = $this->request->postMore([
            ['name', ''],
            ['config_name', ''],
            [['cate_id', 'd'], 0],
            ['info', ''],
            ['typelist', []],
        ]);

        //Kiểm tra tên nhóm dữ liệu
        if (!$params['name']) return app('json')->fail('Vui lòng nhập tên');
        if (!$params['config_name']) return app('json')->fail('Vui lòng nhập tên cấu hình');
        //Kiểm tra ID có tồn tại không, tồn tại thì là sửa, không tồn tại thì là thêm
        if (!$id) {
            if ($this->services->count(['config_name' => $params['config_name']])) {
                return app('json')->fail('Từ khóa dữ liệu đã tồn tại');
            }
        }
        $data["name"] = $params['name'];
        $data["config_name"] = $params['config_name'];
        $data["info"] = $params['info'];
        $data["cate_id"] = $params['cate_id'];
        //Kiểm tra thông tin trường
        if (!count($params['typelist']))
            return app('json')->fail('Phải có ít nhất một trường');
        else {
            $validate = ["name", "type", "title", "description"];
            foreach ($params["typelist"] as $key => $value) {
                foreach ($value as $name => $field) {
                    if (empty($field["value"]) && in_array($name, $validate))
                        return app('json')->fail('Trường không được để trống');
                    else
                        $data["fields"][$key][$name] = $field["value"];
                }
            }
        }
        $data["fields"] = json_encode($data["fields"]);
        $this->services->update($id, $data);
        \crmeb\services\CacheService::clear();
        return app('json')->success('Thêm nhóm dữ liệu thành công');
    }

    /**
     * Xóa resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id, SystemGroupDataServices $services)
    {
        if (!$this->services->delete($id))
            return app('json')->fail('Xóa thất bại');
        else {
            $services->delete($id, 'gid');
            return app('json')->success('Xóa thành công');
        }
    }

    /**
     * Lấy dữ liệu tổ hợp
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroup()
    {
        return app('json')->success($this->services->getGroupList(['cate_id' => 1], ['id', 'name', 'config_name'])['list']);
    }
}
