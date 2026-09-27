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
namespace app\outapi\validate;

use think\Validate;

class StoreCategoryValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'pid' => 'number|egt:0',
        'cate_name' => 'require|max:25',
        'pic' => 'max:128',
        'big_pic' => 'max:200',
        'sort' => 'number|egt:0',
        'is_show' => 'in:0,1'
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'pid.number' => '400745',
        'pid.egt' => '400745',
        'cate_name.require' => '410095',
        'cate_name.max' => '400746',
        'pic.max' => '400747',
        'big_pic.max' => '400748',
        'sort.number' => '400749',
        'sort.egt' => '400750',
        'is_show.in' => '400751',
    ];

    protected $scene = [
        'save' => ['pid', 'cate_name', 'pic', 'big_pic', 'sort', 'is_show'],
    ];
}