<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\adminapi\validate\marketing;

use think\Validate;

class LiveGoodsValidate extends Validate
{

    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => 'require',
        'store_name' => 'require',
        'image' => 'require',
        'price' => 'require|gt:0',
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'id.require' => 'Vui lòng chọn sản phẩm',
        'store_name.require' => 'Vui lòng nhập tên sản phẩm',
        'image.require' => 'Vui lòng chọn ảnh nền',
        'price.require' => 'Vui lòng nhập giá livestream',
        'price.gt' => 'Giá livestream phải lớn hơn 0',
    ];

    protected $scene = [
        'save' => ['id', 'store_name', 'image', 'price'],
    ];
}
