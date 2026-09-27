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

/**
 * Ví dụ định nghĩa cấu trúc thư mục ứng dụng do php think build tự động tạo
 */
return [
    // File cần tự động tạo
    '__file__'   => [],
    // Thư mục cần tự động tạo
    '__dir__'    => ['controller', 'model', 'view'],
    // Controller cần tự động tạo
    'controller' => ['Index'],
    // Model cần tự động tạo
    'model'      => ['User'],
    // Mẫu cần tự động tạo
    'view'       => ['index/index'],
];
