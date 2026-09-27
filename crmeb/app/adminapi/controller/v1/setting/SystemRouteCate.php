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


use app\adminapi\controller\AuthController;
use app\services\system\SystemRouteCateServices;
use app\services\system\SystemRouteServices;
use think\facade\App;
use think\Request;

/**
 * Class SystemRouteCate
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/6
 * @package app\adminapi\controller\v1\setting
 */
class SystemRouteCate extends AuthController
{

    /**
     * SystemRouteCate constructor.
     * @param App $app
     * @param SystemRouteCateServices $services
     */
    public function __construct(App $app, SystemRouteCateServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function index()
    {
        return app('json')->success($this->services->getAllList());
    }

    /**
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function create()
    {
        return app('json')->success($this->services->getFrom(0, $this->request->get('app_name', 'adminapi')));
    }

    /**
     * @param Request $request
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function save(Request $request)
    {
        $data = $request->postMore([
            ['path', []],
            ['name', ''],
            ['sort', 0],
            ['app_name', ''],
        ]);

        if (!$data['name']) {
            return app('json')->fail('Tên danh mục API không được để trống');
        }

        $data['add_time'] = time();
        $data['pid'] = $data['path'][count($data['path']) - 1] ?? 0;
        $this->services->save($data);


        return app('json')->success('Lưu thành công');

    }

    /**
     * @param $id
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function edit($id)
    {
        return app('json')->success($this->services->getFrom($id, $this->request->get('app_name', 'adminapi')));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function update(Request $request, $id)
    {
        $data = $request->postMore([
            ['path', []],
            ['name', ''],
            ['sort', 0],
            ['app_name', ''],
        ]);

        if (!$data['name']) {
            return app('json')->fail('Tên danh mục API không được để trống');
        }

        $data['pid'] = $data['path'][count($data['path']) - 1] ?? 0;
        $this->services->update($id, $data);

        return app('json')->success('Sửa thành công');
    }

    /**
     * @param SystemRouteServices $service
     * @param $id
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function delete(SystemRouteServices $service, $id)
    {
        if (!$id) {
            return app('json')->fail('API không tồn tại');
        }

        if ($service->count(['cate_id' => $id])) {
            return app('json')->fail('Danh mục này có API, không thể xóa');
        }

        $this->services->delete($id);

        return app('json')->success('Xóa thành công');
    }
}
