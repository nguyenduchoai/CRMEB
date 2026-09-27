<?php

use think\facade\Env;

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
    //Mật khẩu phát triển hệ thống
    'password' => ''
];
