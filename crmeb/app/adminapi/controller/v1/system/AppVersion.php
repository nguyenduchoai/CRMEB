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

namespace app\adminapi\controller\v1\system;


use app\adminapi\controller\AuthController;
use app\services\system\AppVersionServices;
use think\facade\App;

/**
 *
 * Class AppVersion
 * @package app\adminapi\controller\v1\system
 */
class AppVersion extends AuthController
{
    /**
     * user constructor.
     * @param App $app
     * @param AppVersionServices $services
     */
    public function __construct(App $app, AppVersionServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách phiên bản
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/4/2
     */
    public function list()
    {
        [$platform] = $this->request->getMore([
            ['platform', '']
        ], true);
        return app('json')->success($this->services->versionList($platform));
    }

    /**
     * Form thêm phiên bản
     * @param $id
     * @return \think\Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/4/2
     */
    public function crate($id)
    {
        return app('json')->success($this->services->createForm($id));
    }

    /**
     * Lưu dữ liệu
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/4/2
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['version', ''],
            ['platform', 1],
            ['info', ''],
            ['is_force', 1],
            ['url', ''],
            ['is_new', 1],
        ]);
        $id = $data['id'];
        unset($data['id']);
        $this->services->versionSave($id, $data);
        return app('json')->success('Thêm thành công');
    }

    /**
     * Xóa phiên bản App
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/4/2
     */
    public function del($id)
    {
        $this->services->delete($id);
        return app('json')->success('Xóa thành công');
    }
}
