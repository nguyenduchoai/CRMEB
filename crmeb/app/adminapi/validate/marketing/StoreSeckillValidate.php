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

class StoreSeckillValidate extends Validate
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
        'images' => 'require',
        'section_time' => 'require',
        'num' => 'require|gt:0',
        'once_num' => 'require|gt:0',
        'time_id' => 'require',
        'temp_id' => 'require',
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
        'images.require' => '400349',
        'section_time.require' => '400353',
        'num.require' => '400354',
        'num.gt' => '400355',
        'once_num.require' => '400366',
        'once_num.gt' => '400367',
        'time_id.require' => '400377',
        'temp_id.require' => '400360',
        'description.require' => '400361',
        'attrs.require' => '400362',
    ];

    protected $scene = [
        'save' => ['product_id', 'title', 'info', 'unit_name', 'image', 'images', 'give_integral', 'section_time', 'is_hot', 'status', 'num', 'once_num', 'time_id', 'temp_id', 'sort', 'description', 'attrs', 'items'],
    ];
}
