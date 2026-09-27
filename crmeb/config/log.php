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
use think\facade\Env;

// +----------------------------------------------------------------------
// | Cài đặt log
// +----------------------------------------------------------------------
return [
    // Kênh ghi log mặc định
    'default'      => Env::get('log.channel', 'file'),
    // Cấp độ ghi log
    'level'        => ['error', 'warning', 'fail', 'success', 'info', 'notice', 'crontab', 'crmeb', 'listener'],
    // Kênh ghi theo loại log ['error'=>'email',...]
    'type_channel' => [],
    //Có mở log nghiệp vụ thành công không
    'success_log'  => false,
    //Có mở log nghiệp vụ thất bại không
    'fail_log'     => false,
    //Có mở log tác vụ định kỳ không
    'timer_log'    => false,
    //Có mở log sự kiện tùy chỉnh không
    'listener_log'    => false,
    // Danh sách kênh log
    'channels'     => [
        'file' => [
            // Cách ghi log
            'type'        => 'File',
            // Thư mục lưu log
            'path'        => app()->getRuntimePath() . 'log' . DIRECTORY_SEPARATOR,
            // Ghi log vào một file
            'single'      => false,
            // Cấp độ log riêng
            'apart_level' => ['error', 'fail', 'success', 'crontab', 'crmeb', 'listener'],
            // Số lượng file log tối đa
            'max_files'   => 60,
            'time_format' => 'Y-m-d H:i:s',
            'format'      => '%s|%s|%s'
        ],
        // Cấu hình kênh log khác
    ],
];
