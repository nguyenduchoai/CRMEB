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
        'product_id.require' => 'Vui lòng chọn sản phẩm',
        'title.require' => 'Vui lòng nhập tên sản phẩm',
        'info.require' => 'Vui lòng điền giới thiệu chương trình',
        'unit_name.require' => 'Vui lòng điền đơn vị tính',
        'images.require' => 'Vui lòng chọn ảnh trình chiếu sản phẩm',
        'section_time.require' => 'Vui lòng chọn khung giờ chương trình',
        'num.require' => 'Vui lòng điền giới hạn số lượng mua',
        'num.gt' => 'Giới hạn số lượng mua phải lớn hơn 0',
        'once_num.require' => 'Vui lòng điền số lượng mua mỗi lần',
        'once_num.gt' => 'Số lượng mua mỗi lần phải lớn hơn 0',
        'time_id.require' => 'Vui lòng chọn khung giờ flash sale',
        'temp_id.require' => 'Vui lòng chọn mẫu phí vận chuyển',
        'description.require' => 'Vui lòng điền chi tiết sản phẩm',
        'attrs.require' => 'Vui lòng chọn quy cách',
    ];

    protected $scene = [
        'save' => ['product_id', 'title', 'info', 'unit_name', 'image', 'images', 'give_integral', 'section_time', 'is_hot', 'status', 'num', 'once_num', 'time_id', 'temp_id', 'sort', 'description', 'attrs', 'items'],
    ];
}
