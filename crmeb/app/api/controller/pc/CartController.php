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

namespace app\api\controller\pc;


use app\Request;
use app\services\pc\CartServices;

class CartController
{
    protected $services;

    public function __construct(CartServices $services)
    {
        $this->services = $services;
    }

    /**
     * Lấy danh sách giỏ hàng của người dùng
     * @param Request $request
     * @return mixed
     */
    public function getCartList(Request $request)
    {
        $uid = $request->uid();
        $data = $this->services->getCartList((int)$uid);
        return app('json')->success($data);
    }
}
