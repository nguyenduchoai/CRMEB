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
namespace app\adminapi\controller\v1\application\routine;

use app\adminapi\controller\AuthController;
use app\services\wechat\RoutineSchemeServices;
use think\facade\App;

class RoutineScheme extends AuthController
{
    public function __construct(App $app, RoutineSchemeServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    public function schemeList()
    {
        $where = $this->request->postMore([
            ['title', ''],
        ]);
        return app('json')->success($this->services->schemeList($where));
    }

    public function schemeForm($id)
    {
        return app('json')->success($this->services->schemeForm($id));
    }

    public function schemeSave($id)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['path', ''],
            ['expire_type', -1],
            ['expire_num', 0],
        ]);
        $this->services->schemeSave($id, $data);
        return app('json')->success('Lưu thành công');
    }

    public function schemeDel($id)
    {
        $res = $this->services->delete($id);
        if (!$res) return app('json')->fail('Xóa thất bại');
        return app('json')->success('Xóa thành công');
    }
}