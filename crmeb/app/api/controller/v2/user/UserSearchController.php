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

namespace app\api\controller\v2\user;


use app\services\user\UserSearchServices;
use think\Request;

/**
 * Class UserInvoiceController
 * @package app\api\controller\v2\user
 */
class UserSearchController
{
    /**
     * @var UserSearchServices
     */
    protected $services;

    /**
     * UserSearchController constructor.
     * @param UserSearchServices $services
     */
    public function __construct(UserSearchServices $services)
    {
        $this->services = $services;
    }

    public function getUserSeachList(Request $request)
    {
        return app('json')->success($this->services->getUserList((int)$request->uid()));
    }

    public function cleanUserSearch(Request $request)
    {
        $uid = (int)$request->uid();
        $this->services->delete(['uid' => $uid]);
        return app('json')->success(100002);
    }
}
