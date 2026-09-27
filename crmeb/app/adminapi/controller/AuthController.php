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
namespace app\adminapi\controller;


use crmeb\basic\BaseController;
use think\facade\Validate;

/**
 * Lớp cơ sở, lớp mà tất cả controller kế thừa
 * Class AuthController
 * @package app\adminapi\controller
 */
class AuthController extends BaseController
{
    /**
     * Thông tin quản trị viên đang đăng nhập
     * @var
     */
    protected $adminInfo;

    /**
     * ID quản trị viên đang đăng nhập
     * @var
     */
    protected $adminId;

    /**
     * Quyền của quản trị viên hiện tại
     * @var array
     */
    protected $auth = [];


    /**
     * Khởi tạo
     */
    protected function initialize()
    {
        $this->adminId = $this->request->adminId();
        $this->adminInfo = $this->request->adminInfo();
        $this->auth = $this->request->adminInfo['rule'] ?? [];
    }

    /**
     * Xác thực dữ liệu
     * @param array $data
     * @param $validate
     * @param null $message
     * @param bool $batch
     * @return bool
     */
    final protected function validate(array $data, $validate, $message = null, bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // Scene được hỗ trợ
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }

            if (is_string($message) && empty($scene)) {
                $v->scene($message);
            }
        }

        if (is_array($message))
            $v->message($message);


        // Có xác thực theo lô không
        if ($batch) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}
