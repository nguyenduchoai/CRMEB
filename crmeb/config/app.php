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
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Cài đặt ứng dụng
// +----------------------------------------------------------------------

use think\facade\Env;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

return [
    // Địa chỉ ứng dụng
    'app_host'         => Env::get('app.host', ''),
    // Namespace của ứng dụng
    'app_namespace'    => '',
    // Có bật route không
    'with_route'       => true,
    // Có bật event không
    'with_event'       => true,
    // Chế độ đa ứng dụng tự động
    'auto_multi_app'   => true,
    // Mapping ứng dụng (chỉ có hiệu lực ở chế độ đa ứng dụng tự động)
    'app_map'          => [],
    // Liên kết domain (chỉ có hiệu lực ở chế độ đa ứng dụng tự động)
    'domain_bind'      => [],
    // Danh sách ứng dụng cấm truy cập qua URL (chỉ có hiệu lực ở chế độ đa ứng dụng tự động)
    'deny_app_list'    => [],
    // Ứng dụng mặc định
    'default_app'      => '',

    'app_express'      => true,
    // Múi giờ mặc định
    'default_timezone' => 'Asia/Ho_Chi_Minh',
    // File template trang lỗi (exception)
    'exception_tmpl'   => app()->getRootPath() . 'public/statics/exception.tpl',
    // Thông tin hiển thị lỗi, chỉ có hiệu lực ở chế độ không debug
    'error_message'    => 'Lỗi trang! Vui lòng thử lại sau~',
    // Hiển thị thông tin lỗi
    'show_error_msg'   => false,
    // Công tắc nhắc nhở khi chưa mở lệnh hàng đợi tin nhắn hoặc lệnh tác vụ định kỳ
    'console_remind'   => true,
    // Tiền tố route admin
    'admin_prefix'     => 'admin',
    //Đường dẫn file frontend do chức năng sinh code tạo ra
    'admin_template_path' => dirname(root_path()) . DS . 'template' . DS . 'admin' . DS . 'src' . DS,
    //Khi lưu crud có tạo file trực tiếp luôn không
    'crud_make'        => true
];
