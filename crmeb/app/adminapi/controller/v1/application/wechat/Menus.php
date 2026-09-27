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
namespace app\adminapi\controller\v1\application\wechat;

use app\adminapi\controller\AuthController;
use app\services\wechat\WechatMenuServices;
use think\facade\App;

/**
 * Controller menu WeChat
 * Class Menus
 * @package app\admin\controller\wechat
 */
class Menus extends AuthController
{
    /**
     * Phương thức khởi tạo
     * Menus constructor.
     * @param App $app
     * @param WechatMenuServices $services
     */
    public function __construct(App $app, WechatMenuServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy menu
     * @return mixed
     */
    public function index()
    {
        $menus = $this->services->getWechatMenu();
        return app('json')->success(compact('menus'));
    }

    /**
     * Lưu menu
     * @return mixed
     */
    public function save()
    {
        $buttons = request()->post('button/a', []);
        if(strlen($buttons[0]['name']) > 15) return app('json')->fail('Tên menu không được vượt quá 5 ký tự');
        if (!count($buttons)) return app('json')->fail(400238);
        $this->services->saveMenu($buttons);
        return app('json')->success(100001);
    }
}
