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
use think\facade\Env;

// +----------------------------------------------------------------------
// | Cài đặt cache
// +----------------------------------------------------------------------

return [
    // Driver cache mặc định
    'default' => Env::get('cache.driver', 'file'),

    // Cấu hình cách kết nối cache
    'stores'  => [
        'file' => [
            // Kiểu driver
            'type'       => 'File',
            // Thư mục lưu cache
            'path'       => app()->getRuntimePath() . 'cache' . DIRECTORY_SEPARATOR,
            // Tiền tố cache
            'prefix'     => '',
            // Thời hạn cache, 0 nghĩa là cache vĩnh viễn
            'expire'     => 0,
            // Tiền tố nhãn cache
            'tag_prefix' => 'tag:',
            // Cơ chế serialize, ví dụ ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // Các kết nối cache khác
        // Bộ nhớ đệm redis
        'redis'   =>  [
            // Kiểu driver
            'type'          => 'redis',
            // Địa chỉ server
            'host'          => Env::get('redis.redis_hostname', '127.0.0.1'),
            // Cổng (port)
            'port'          => Env::get('redis.port', '6379'),
            // Mật khẩu
            'password'      => Env::get('redis.redis_password', ''),
            // Thời hạn cache, 0 nghĩa là cache vĩnh viễn
            'expire'        => 0 ,
            // Tiền tố cache
            'prefix'     => Env::get('cache.cache_prefix', 'c:'),
            // Tiền tố nhãn cache
            'tag_prefix'    => Env::get('cache.cache_tag_prefix', 'CRMEB:'),
            // Cơ sở dữ liệu, cơ sở dữ liệu số 0
            'select'        => intval(Env::get('redis.select', 0)),
            // Cơ chế serialize, ví dụ ['serialize', 'unserialize']
            'serialize'     => [],
            // Server chủ động đóng
            'timeout'       => 0
        ],
    ],
];
