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

return [
    // Ký tự phân tách pathinfo
    'pathinfo_depr'         => '/',
    // Hậu tố URL giả tĩnh (pseudo-static)
    'url_html_suffix'       => 'html',
    // Tham số URL kiểu thông thường, dùng để tự động tạo
    'url_common_param'      => true,
    // Có mở phân giải route trễ (lazy) không
    'url_lazy_route'        => false,
    // Có bắt buộc dùng route không
    'url_route_must'        => true,
    // Gộp quy tắc route
    'route_rule_merge'      => false,
    // Route có khớp hoàn toàn không
    'route_complete_match'  => true,
    // Dùng route bằng annotation
    'route_annotation'      => false,
    // Có mở cache route không
    'route_check_cache'     => false,
    // Tham số kết nối cache route
    'route_cache_option'    => [],
    // Key cache route
    'route_check_cache_key' => '',
    // Tên tầng truy cập controller
    'controller_layer'      => 'controller',
    // Tên controller trống
    'empty_controller'      => 'Error',
    // Có dùng hậu tố controller không
    'controller_suffix'     => false,
    // Quy tắc biến route mặc định
    'default_route_pattern' => '[\w\.]+',
    // Có tự động chuyển đổi tên controller và action trong URL không
    'url_convert'           => true,
    // Có mở cache request không, true là tự động cache, hỗ trợ đặt quy tắc cache request
    'request_cache'         => false,
    // Thời hạn cache request
    'request_cache_expire'  => null,
    // Quy tắc loại trừ cache request toàn cục
    'request_cache_except'  => [],
    // Tên controller mặc định
    'default_controller'    => 'Index',
    // Tên action mặc định
    'default_action'        => 'index',
    // Hậu tố phương thức action
    'action_suffix'         => '',
    // Phương thức xử lý trả về định dạng JSONP mặc định
    'default_jsonp_handler' => 'jsonpReturn',
    // Phương thức xử lý JSONP mặc định
    'var_jsonp_handler'     => 'callback',
];
