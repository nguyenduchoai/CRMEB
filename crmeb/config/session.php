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
// | Cài đặt session
// +----------------------------------------------------------------------

return [
    // session name
    'name'           => '',
    // Biến gửi SESSION_ID, giải quyết upload flash cross-domain
    'var_session_id' => '',
    // Kiểu driver, hỗ trợ file redis memcache memcached
    'type'           => 'file',
    // Thời gian hết hạn
    'expire'         => 10800,
    // Tiền tố
    'prefix'         => '',
];
