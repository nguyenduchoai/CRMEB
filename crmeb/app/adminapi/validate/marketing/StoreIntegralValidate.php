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
        'product_id.require' => 'Vui lòng chọn sản phẩm',
        'title.require' => 'Vui lòng nhập tên sản phẩm',
        'info.require' => 'Vui lòng điền giới thiệu chương trình',
        'unit_name.require' => 'Vui lòng điền đơn vị tính',
        'image.require' => 'Vui lòng chọn ảnh trình chiếu sản phẩm',
        'images.require' => 'Vui lòng chọn ảnh trình chiếu sản phẩm',
        'description.require' => 'Vui lòng điền chi tiết sản phẩm',
        'attrs.require' => 'Vui lòng chọn quy cách',
    ];

    protected $scene = [
        'save' => ['product_id', 'title', 'unit_name', 'image', 'images',  'num', 'once_num', 'sort', 'description', 'attrs', 'items'],
    ];
}
