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
declare(strict_types=1);

return [
    'font_file' => '', //Đường dẫn gói font tùy chỉnh, để trống thì dùng giá trị mặc định
    //Mã xác thực dạng chữ
    'click_world' => [
        'backgrounds' => []
    ],
    //Mã xác thực dạng trượt
    'block_puzzle' => [
        /*Đường dẫn ảnh nền, để trống thì dùng giá trị mặc định, hỗ trợ 2 kiểu dữ liệu string và array. string là thư mục ảnh mặc định, array là mảng chỉ số chứa địa chỉ ảnh cụ thể*/
        'backgrounds' => [
            public_path().'statics/images/check1.jpg',
            public_path().'statics/images/check2.jpg',
            public_path().'statics/images/check3.jpg',
            public_path().'statics/images/check4.jpg',
        ],

        /*Ảnh mẫu, định dạng như trên, hỗ trợ string và array*/
        'templates' => [],

        'offset' => 10, //Độ lệch cho phép sai số

        'is_cache_pixel' => true, //Có mở cache giá trị pixel ảnh không, mở lên sẽ tăng hiệu năng phản hồi phía server (nhưng khi đổi ảnh cần xóa cache)

        'is_interfere' => true, //Mở ảnh gây nhiễu
    ],
    //Watermark
    'watermark' => [
        'fontsize' => 12,
        'color' => '#FFFFFF',
        'text' => 'CRMEB'
    ],
    'cache' => [
        //Nếu bạn dùng framework và muốn dùng driver cache kiểu như redis, thì nên đổi sang driver cache của framework
        'constructor' => app()->make(\think\Cache::class),
        'method' => [
            //Tuân theo chuẩn PSR-16 thì không cần đặt mục này (tp6, laravel, hyperf). Còn tp5 thì không hỗ trợ (phương thức cache của tp5 là rm, nên phải cấu hình là "delete" => "rm")
            /**
             * 'get' => 'get', //Lấy
             * 'set' => 'set', //Đặt
             * 'delete' => 'delete',//Xóa
             * 'has' => 'has' //Kiểm tra key có tồn tại không
             */
        ],
        'options' => [
            //Nếu bạn vẫn dùng \Fastknife\Utils\CacheUtils làm driver cache của mình, thì có thể tự tùy chỉnh cấu hình cache.
            'expire' => 300,//Thời hạn cache (mặc định là 0, nghĩa là cache vĩnh viễn)
            'prefix' => '', //Tiền tố cache
            'path' => '', //Thư mục bộ nhớ đệm (cache)
            'serialize' => [], //Phương thức serialize và deserialize cache
        ]
    ]
];
