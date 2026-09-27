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

/** File định nghĩa event
 * Ví dụ gọi event:
 * @param mixed $event Tên event (hoặc tên class)
 * @param mixed $args  Tham số
 * event($event,$args);
 * event('OrderCreateAfterListener',$order);
*/ 

return [
    'bind' => [],

    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [\app\listener\http\HttpEndListener::class], //Event callback khi kết thúc request HTTP
        'LogLevel' => [],
        'LogWrite' => [],
        'QueueStartListener' => [\app\listener\queue\QueueStartListener::class],
        'UserLoginListener' => [\app\listener\user\LoginListener::class],
        'AdminLoginListener' => [\app\listener\admin\AdminLoginListener::class],//Đăng nhập quản trị viên
        'UserRegisterListener' => [\app\listener\user\RegisterListener::class], //Event sau khi người dùng đăng ký
        'WechatAuthListener' => [\app\listener\wechat\AuthListener::class], //Sự kiện sau khi người dùng ủy quyền
        'OrderCreateAfterListener' => [\app\listener\order\OrderCreateAfterListener::class], //Event sau khi tạo đơn hàng
        'OrderPaySuccessListener' => [\app\listener\order\OrderPaySuccessListener::class], //Event sau khi đơn hàng thanh toán thành công
        'OrderDeliveryListener' => [\app\listener\order\OrderDeliveryListener::class], //Event sau khi đơn hàng giao hàng
        'OrderTakeListener' => [\app\listener\order\OrderTakeListener::class], //Event sau khi đơn hàng được nhận hàng
        'OrderRefundCreateAfterListener' => [\app\listener\order\OrderRefundCreateAfterListener::class], //Event sau khi tạo đơn hậu mãi
        'OrderRefundCancelAfterListener' => [\app\listener\order\OrderRefundCancelAfterListener::class], //Event sau khi hủy đơn hậu mãi
        'OutPushListener' => [\app\listener\out\OutPushListener::class], //Event đẩy dữ liệu ra bên ngoài
        'UserLevelListener' => [\app\listener\user\UserLevelListener::class], //Event nâng cấp người dùng
        'UserVisitListener' => [\app\listener\user\UserVisitListener::class], //Sự kiện người dùng truy cập
        'NoticeListener' => [\app\listener\notice\NoticeListener::class], //Event thông báo -> tin nhắn
        'CustomNoticeListener' => [\app\listener\notice\CustomNoticeListener::class], //Event thông báo -> gửi tin nhắn tùy chỉnh
        'NotifyListener' => [\app\listener\pay\NotifyListener::class],//Callback thanh toán bất đồng bộ
        'OrderShippingListener' => [\app\listener\order\OrderShippingListener::class],//Quản lý giao hàng Mini Program
        'CustomEventListener' => [\app\listener\CustomEventListener::class],//Sự kiện tùy chỉnh
    ],
];


