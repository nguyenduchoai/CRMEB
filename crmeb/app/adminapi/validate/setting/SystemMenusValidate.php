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
namespace app\adminapi\validate\setting;

use think\Validate;

class SystemMenusValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'	=>	['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
	protected $rule = [

    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'	=>	'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [];
}
