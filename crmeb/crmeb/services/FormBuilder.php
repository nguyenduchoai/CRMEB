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

namespace crmeb\services;

//use FormBuilder\Factory\Iview as Form;
use FormBuilder\Factory\Elm as Form;

/**
 * Form Builder
 * Class FormBuilder
 * @package crmeb\services
 */
class FormBuilder extends Form
{

    public static function setOptions($call){
        if (is_array($call)) {
            return $call;
        }else{
            return  $call();
        }

    }


}
