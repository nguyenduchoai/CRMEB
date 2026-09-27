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
namespace app\outapi\controller;


use crmeb\basic\BaseController;
use think\facade\Validate;

/**
 * Lớp cơ sở, lớp mà tất cả controller kế thừa
 * Class AuthController
 * @package app\controller\out
 * @method success($msg = 'ok', array $data = [])
 * @method fail($msg = 'error', array $data = [])
 */
class AuthController extends BaseController
{

    /**
     * ID API bên ngoài hiện tại
     * @var
     */
    protected $outId;

    /**
     * Thông tin API bên ngoài hiện tại
     * @var
     */
    protected $outInfo;

    /**
     * Quyền API bên ngoài hiện tại
     * @var array
     */
    protected $auth = [];


    /**
     * Khởi tạo
     */
    protected function initialize()
    {
        $this->outId = $this->request->outId();
        $this->outInfo = $this->request->outInfo();
        $this->auth = $this->outInfo['rule'] ?? [];
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
