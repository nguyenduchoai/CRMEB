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
    //Chế độ thanh toán mặc định
    'default' => 'wechat_pay',
    //Phương thức thanh toán
    'payType' => ['weixin' => 'WeChat Pay', 'yue' => 'Thanh toán bằng số dư', 'offline' => 'Thanh toán ngoại tuyến'],
    //Phương thức rút tiền
    'extractType' => ['alipay', 'bank', 'weixin'],
    //Phương thức giao hàng
    'deliveryType' => ['send' => 'Cửa hàng tự giao', 'express' => 'Giao qua đơn vị vận chuyển'],
    //Chế độ driver
    'stores' => [
        //WeChat Pay
        'wechat_pay' => [],
        //Thanh toán Alipay
        'ali_pay' => [],
        //Thanh toán bằng số dư
        'yue' => [],
    ]
];
