<?php
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
// | Cấu hình console
// +----------------------------------------------------------------------
return [
    // Người dùng thực thi (không có hiệu lực trên Windows)
    'user' => null,
    // Định nghĩa lệnh (command)
    'commands' => [
        'workerman' => \crmeb\command\Workerman::class,
        'timer' => \crmeb\command\Timer::class,
        'util' => \crmeb\command\Util::class,
        'npm' => \crmeb\command\Npm::class
    ],
];
