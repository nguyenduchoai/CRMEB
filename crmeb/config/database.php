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

return [
    // Cấu hình kết nối cơ sở dữ liệu dùng theo mặc định
    'default'         => Env::get('database.driver', 'mysql'),

    // Thông tin cấu hình kết nối cơ sở dữ liệu
    'connections'     => [
        'mysql' => [
            // Loại cơ sở dữ liệu
            'type'            => Env::get('database.type', 'mysql'),
            // Địa chỉ server
            'hostname'        => Env::get('database.hostname', '127.0.0.1'),
            // Tên cơ sở dữ liệu
            'database'        => Env::get('database.database', 'crmeb31'),
            // Tên người dùng
            'username'        => Env::get('database.username', 'root'),
            // Mật khẩu
            'password'        => Env::get('database.password', 'root'),
            // Cổng (port)
            'hostport'        => Env::get('database.hostport', '3306'),
            // DSN kết nối
            'dsn'             => '',
            // Tham số kết nối cơ sở dữ liệu
            'params'          => [],
            // Bảng mã cơ sở dữ liệu mặc định dùng utf8
            'charset'         => Env::get('database.charset', 'utf8'),
            // Tiền tố bảng cơ sở dữ liệu
            'prefix'          => Env::get('database.prefix', 'eb_'),
            // Chế độ debug cơ sở dữ liệu
            'debug'           => Env::get('database.debug', true),
            // Cách triển khai cơ sở dữ liệu: 0 tập trung (một server), 1 phân tán (server chính-phụ)
            'deploy'          => 0,
            // Cơ sở dữ liệu có tách đọc/ghi không, chỉ có hiệu lực ở chế độ chính-phụ
            'rw_separate'     => false,
            // Số server chính sau khi tách đọc/ghi
            'master_num'      => 1,
            // Chỉ định số thứ tự server phụ
            'slave_no'        => '',
            // Có kiểm tra nghiêm ngặt trường có tồn tại không
            'fields_strict'   => true,
            // Có cần phân tích hiệu năng SQL không
            'sql_explain'     => false,
            // Class Builder
            'builder'         => '',
            // Class Query
            'query'           => '',
            // Có cần tự kết nối lại khi mất kết nối không
            'break_reconnect' => true,
        ],

        // Các thông tin cấu hình cơ sở dữ liệu khác
    ],

    // Quy tắc truy vấn thời gian tùy chỉnh
    'time_query_rule' => [],
    // Tự động ghi trường timestamp
    'auto_timestamp'  => 'timestamp',
    // Định dạng thời gian mặc định sau khi lấy trường thời gian ra
    'datetime_format' => 'Y-m-d H:i:s',
    //Cấu hình phân trang dữ liệu
    'page' => [
        //Key số trang
        'pageKey' => 'page',
        //Key số lượng lấy mỗi trang
        'limitKey' => 'limit',
        //Giá trị tối đa lấy mỗi trang
        'limitMax' => 100,
        //Số dòng mặc định
        'defaultLimit' => 10,
    ]
];
