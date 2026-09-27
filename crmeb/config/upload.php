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

return [
    //Chế độ upload mặc định, cấu hình admin ưu tiên hơn, khi thêm loại thì chỉ số phải khớp với tên driver, dùng chữ thường
    'default' => 'local',
    //Dung lượng tệp tải lên 50M
    'filesize' => 52428800,
    //Loại hậu tố file upload
    'fileExt' => ['jpg', 'jpeg', 'png', 'gif', 'pem', 'mp3', 'wma', 'wav', 'amr', 'mp4', 'key', 'xlsx', 'xls', 'txt', 'ico', 'crt', 'webp', 'zip'],
    //Loại file upload
    'fileMime' => [
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/png',
        'text/plain',
        'audio/mpeg',
        'video/mp4',
        'application/octet-stream',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-works',
        'application/vnd.ms-excel',
        'application/zip',
        'text/xml',
        'image/x-icon',
        'image/vnd.microsoft.icon',
        'application/x-x509-ca-cert',
        'image/webp',
        'application/x-zip-compressed',
        // Bổ sung phần còn thiếu
        'audio/x-ms-wma',              // wma
        'audio/wav',                   // wav
        'audio/amr',                   // amr
        'application/x-pem-file',      // pem
        // Tương thích Windows
        'audio/mp3',                   // mp3 Windows
        'audio/wave',                  // wav Windows Chrome
        'audio/x-wav',                 // wav Windows IE/Edge
        'application/msexcel',         // xls Windows
    ],
    //Chế độ driver, cấu hình này ưu tiên hơn cấu hình admin, khi thêm cấu hình ở admin hãy thêm tiền tố, ví dụ thêm cấu hình Qiniu Cloud: accessKey thì ở admin thêm tên biến qiniu_accessKey
    'stores' => [
        //Cấu hình upload cục bộ
        'local' => [],
        //Cấu hình upload Qiniu Cloud
        'qiniu' => [
            'AccessKeyId' => '', // sys_config('qiniu_accessKey')
            'AccessKeySecret' => '', // sys_config('qiniu_secretKey')
        ],
        //oss Cấu hình upload Alibaba Cloud
        'oss' => [
            'AccessKeyId' => '', // sys_config('accessKey')
            'AccessKeySecret' => '', // sys_config('secretKey')
        ],
        //cos Cấu hình upload Tencent Cloud
        'cos' => [
            'AccessKeyId' => '', //sys_config('tengxun_accessKey')
            'AccessKeySecret' => '', //sys_config('tengxun_secretKey')
            'APPID' => '', //sys_config('tengxun_appid')
        ],
        //oss JD Cloud
        'jdoss' => [
            'AccessKeyId' => '', // sys_config('accessKey')
            'AccessKeySecret' => '', // sys_config('secretKey')
        ],
        //oss Huawei Cloud
        'obs' => [
            'AccessKeyId' => '', // sys_config('accessKey')
            'AccessKeySecret' => '', // sys_config('secretKey')
        ],
        //oss Tianyi Cloud
        'tyoss' => [
            'AccessKeyId' => '', // sys_config('accessKey')
            'AccessKeySecret' => '', // sys_config('secretKey')
        ],
    ]
];
