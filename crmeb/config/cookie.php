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
// | Cài đặt Cookie
// +----------------------------------------------------------------------
return [
    // Thời gian lưu cookie
    'expire'    => 0,
    // Đường dẫn lưu cookie
    'path'      => '/',
    // Domain hợp lệ của cookie
    'domain'    => '',
    // Cookie bật truyền tải an toàn
    'secure'    => false,
    // Cài đặt httponly
    'httponly'  => false,
    // Có dùng setcookie không
    'setcookie' => true,
    // Header cross-domain (CORS)
    'header'    => [
        'Access-Control-Allow-Origin'       => '*',
        'Access-Control-Allow-Headers'      => 'Authori-zation,Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, Form-type, Cb-lang, Invalid-zation',
        'Access-Control-Allow-Methods'      => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        'Access-Control-Max-Age'            =>  '1728000',
        'Access-Control-Allow-Credentials'  => 'true'
    ],
    // Tên token
    'token_name' => 'Authori-zation',
];
