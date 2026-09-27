<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
return [

    // Chương trình hệ thống nhận tin nhắn được gửi tới, thông qua cổng này để đẩy tới khách hàng hoặc CSKH tương ứng, cùng với thông báo popup đơn hàng mới ở admin
    'channel' => [
        //Cổng lắng nghe giao tiếp nội bộ
        'port' => 40003,
        //Địa chỉ giao tiếp nội bộ
        'ip' => '127.0.0.1',
    ],

    // notice gửi tin nhắn cho chương trình khi có đơn hàng mới và đơn hoàn tiền mới, thông báo tin nhắn ở admin
    'admin' => [
        //Thỏa thuận
        'protocol' => 'websocket',
        //Địa chỉ lắng nghe
        'ip' => '0.0.0.0',
        //Cổng lắng nghe
        'port' => 40001,
        //Đặt số lượng process khởi động cho instance Worker hiện tại
        'serverCount' => 1,
    ],

    // msg khách hàng hoặc CSKH gửi tin nhắn cho chương trình, thông báo tin nhắn CSKH
    'chat' => [
        //Thỏa thuận
        'protocol' => 'websocket',
        //Địa chỉ lắng nghe
        'ip' => '0.0.0.0',
        //Cổng lắng nghe
        'port' => 40002,
        //Đặt số lượng process khởi động cho instance Worker hiện tại
        'serverCount' => 1,
    ],
];
