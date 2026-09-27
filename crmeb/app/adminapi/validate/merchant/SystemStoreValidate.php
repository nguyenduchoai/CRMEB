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
namespace app\adminapi\validate\merchant;

use think\Validate;

class SystemStoreValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'introduction' => 'require',
        'phone' => 'require',
        'address' => 'require',
        'image' => 'require',
        'oblong_image' => 'require',
        'detailed_address' => 'require',
        'latlng' => 'require',
        'day_time' => 'require',
    ];
    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'name.require' => 'Vui lòng điền tên cửa hàng',
        'introduction.require' => 'Vui lòng điền giới thiệu cửa hàng',
        'phone.require' => 'Vui lòng điền số điện thoại cửa hàng',
        'image.require' => 'Vui lòng chọn logo điểm nhận hàng',
        'oblong_image.require' => 'Vui lòng chọn ảnh lớn của điểm nhận hàng',
        'address.require' => 'Vui lòng chọn địa chỉ',
        'detailed_address.require' => 'Vui lòng điền địa chỉ chi tiết',
        'latlng.require' => 'Vui lòng chọn kinh độ và vĩ độ',
        'day_time.require' => 'Vui lòng chọn giờ hoạt động',
    ];

    protected $scene = [
        'save' => ['name', 'phone', 'address', 'detailed_address', 'latlng', 'day_time', 'image', 'oblong_image'],
    ];
}
