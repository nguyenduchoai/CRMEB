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
namespace app\adminapi\controller\v1\product;

use app\adminapi\controller\AuthController;
use app\services\product\product\StoreProductProtectionServices;
use think\facade\App;

class StoreProductProtection extends AuthController
{
    public function __construct(App $app, StoreProductProtectionServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    public function protectionList()
    {
        $where = $this->request->getMore([
            ['title', ''],
            ['status', '']
        ]);
        $where['is_del'] = 0;
        return app('json')->success($this->services->protectionList($where));
    }

    public function protectionInfo($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $info = $this->services->protectionInfo($id);
        return app('json')->success($info);
    }

    public function protectionForm($id)
    {
        return app('json')->success($this->services->protectionForm($id));
    }

    public function protectionSave($id)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['content', ''],
            ['image', ''],
            ['sort', 0],
            ['status', 0]
        ]);
        $this->services->protectionSave($id, $data);
        return app('json')->success('Lưu thành công');
    }

    public function protectionStatus($id, $status)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->protectionStatus($id, $status);
        return app('json')->success('Sửa thành công');
    }

    public function protectionDel($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->protectionDel($id);
        return app('json')->success('Xóa thành công');
    }
}
