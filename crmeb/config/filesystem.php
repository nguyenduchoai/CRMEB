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
use think\facade\Env;

// Env::get() tự đổi true/false/on/off thành kiểu bool, các giá trị đó không được coi là mật khẩu
$password = Env::get('filesystem.password', '');

return [
    'default' => Env::get('filesystem.driver', 'public'),
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            'type'       => 'local',
            'root'       => app()->getRootPath() . 'public/uploads',
            'url'        => '/uploads',
            'visibility' => 'public',
        ],
        'pem' => [
            'type'       => 'local',
            'root'       => app()->getRootPath() . 'runtime/pem',
            'url'        => '',
        ],
        // Các thông tin cấu hình ổ đĩa khác
    ],
    //Mật khẩu phát triển hệ thống (trình sửa file online). Để trống = tắt; chỉ đặt qua [FILESYSTEM] PASSWORD trong .env, không ghi vào đây
    'password' => is_string($password) ? $password : ''
];
