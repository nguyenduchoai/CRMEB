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
namespace app\adminapi\validate\marketing;

use think\Validate;

class StoreIntegralValidate extends Validate
{

    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'product_id' => 'require',
        'title' => 'require',
        'info' => 'require',
        'unit_name' => 'require',
        'image' => 'require',
        'images' => 'require',
        'description' => 'require',
        'attrs' => 'require',
        'items' => 'require',
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'product_id.require' => '400337',
        'title.require' => '400338',
        'info.require' => '400347',
        'unit_name.require' => '400348',
        'image.require' => '400349',
        'images.require' => '400349',
        'description.require' => '400361',
        'attrs.require' => '400362',
    ];

    protected $scene = [
        'save' => ['product_id', 'title', 'unit_name', 'image', 'images',  'num', 'once_num', 'sort', 'description', 'attrs', 'items'],
    ];
}
