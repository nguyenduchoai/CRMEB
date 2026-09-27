<?php
// +----------------------------------------------------------------------
// | Cấu hình SMS
// +----------------------------------------------------------------------

return [
    //Tài khoản nền tảng
    'account' => '',
    //Khóa bí mật nền tảng
    'secret' => '',
    //Chế độ driver
    'stores' => [
        'sms' => [
            //Hạn mức gửi mỗi ngày cho một số điện thoại
            'maxPhoneCount' => 10,
            //Hạn mức gửi mã xác thực mỗi phút
            'maxMinuteCount' => 20,
            //Hạn mức gửi mỗi ngày cho một IP
            'maxIpCount' => 50,
            //ID mẫu SMS
            'template_id' => [
                //Thời hạn tùy chỉnh của mã xác thực
                'VERIFICATION_CODE_TIME' => 538393,
                //Mã xác thực
                'VERIFICATION_CODE' => 518076,
                //Thanh toán thành công
                'PAY_SUCCESS_CODE' => 520268,
                //Nhắc nhở giao hàng
                'DELIVER_GOODS_CODE' => 520269,
                //Nhắc nhở xác nhận đã nhận hàng
                'TAKE_DELIVERY_CODE' => 520271,
                //Nhắc nhở quản trị viên khi có đơn hàng mới
                'ADMIN_PLACE_ORDER_CODE' => 520272,
                //Nhắc nhở quản trị viên khi trả hàng
                'ADMIN_RETURN_GOODS_CODE' => 520274,
                //Nhắc nhở quản trị viên khi thanh toán thành công
                'ADMIN_PAY_SUCCESS_CODE' => 520273,
                //Quản trị viên xác nhận đã nhận hàng
                'ADMIN_TAKE_DELIVERY_CODE' => 520422,
                //Nhắc nhở đổi giá
                'PRICE_REVISION_CODE' => 528288,
                //Đơn hàng chưa thanh toán
                'ORDER_PAY_FALSE' => 528116,
            ]
        ]
    ]
];