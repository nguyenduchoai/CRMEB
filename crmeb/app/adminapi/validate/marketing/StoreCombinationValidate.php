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

class StoreCombinationValidate extends Validate
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
        'temp_id' => 'require',
        'description' => 'require',
        'attrs' => 'require',
        'items' => 'require',
        'people' => 'require|gt:1',
        'effective_time' => 'require|gt:0',
        'virtual' => 'require|gt:0|elt:100',
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
        'virtual.require' => 'Vui lòng điền tỷ lệ mua chung ảo',
        'virtual.gt' => 'Số người tham gia ảo không được lớn hơn số người cần để thành nhóm',
        'virtual.elt' => 'Số người tham gia ảo không được lớn hơn số người cần để thành nhóm',
        'once_num.require' => 'Vui lòng điền số lượng mua mỗi lần',
        'once_num.gt' => 'Số lượng mua mỗi lần phải lớn hơn 0',
        'temp_id.require' => 'Vui lòng chọn mẫu phí vận chuyển',
        'description.require' => 'Vui lòng điền chi tiết sản phẩm',
        'attrs.require' => 'Vui lòng chọn quy cách',
        'people.require' => 'Vui lòng điền số người cần để thành nhóm',
        'people.gt' => 'Số người mua chung không được ít hơn 2 người',
        'effective_time.require' => 'Vui lòng điền thời hạn thành nhóm',
        'effective_time.gt' => 'Thời hạn thành nhóm phải lớn hơn 0',
    ];

    protected $scene = [
        'save' => ['product_id', 'title', 'info', 'unit_name', 'image', 'images', 'section_time', 'is_host', 'is_show', 'num', 'people', 'once_num', 'virtual', 'temp_id', 'sort', 'description', 'attrs', 'items', 'people', 'effective_time'],
    ];
}
