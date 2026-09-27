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
    //Mở rộng mặc định
    'default' => 'yihaotong',
    //Hạn mức gửi mỗi ngày cho một số điện thoại
    'maxPhoneCount' => 20,
    //Hạn mức gửi mã xác thực mỗi phút
    'maxMinuteCount' => 5,
    //Hạn mức gửi mỗi ngày cho một IP
    'maxIpCount' => 50,
    //Chế độ driver
    'stores' => [
        //Yihaotong
        'yihaotong' => [
            'sms_account' => '',
            'sms_token' => ''
        ],
        //Alibaba Cloud
        'aliyun' => [
            'aliyun_SignName' => '',
            'aliyun_AccessKeyId' => '',
            'aliyun_AccessKeySecret' => '',
            'aliyun_RegionId' => '',
        ],
        //Tencent Cloud
        'tencent' => [
            'tencent_sms_app_id' => '',
            'tencent_sms_secret_id' => '',
            'tencent_sms_secret_key' => '',
            'tencent_sms_sign_name' => '',
            'tencent_sms_region' => '',
        ]
    ]
];
