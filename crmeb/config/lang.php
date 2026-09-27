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
// | Cài đặt đa ngôn ngữ
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    // Ngôn ngữ mặc định
    'default_lang' => Env::get('lang.default_lang', 'vi-vn'),
    // Danh sách ngôn ngữ được phép
    'allow_lang_list' => ['vi-vn', 'en-us', 'zh-cn'],
    // Tên biến tự động phát hiện đa ngôn ngữ
    'detect_var' => 'lang',
    // Có dùng Cookie để ghi lại không
    'use_cookie' => true,
    // Biến cookie đa ngôn ngữ
    'cookie_var' => 'cb_lang',
    // Gói ngôn ngữ mở rộng
    'extend_list' => [
        'vi-vn' => [app()->getBasePath() . 'lang/vi_vn.php', app()->getBasePath() . 'lang/vi_vn_think.php'],
        'vi_vn' => [app()->getBasePath() . 'lang/vi_vn.php', app()->getBasePath() . 'lang/vi_vn_think.php'],
        'zh_cn' => app()->getBasePath() . 'lang/zh_cn.php',
        'en_us' => app()->getBasePath() . 'lang/en_us.php',
    ],
    // Chuyển đổi Accept-Language sang tên gói ngôn ngữ tương ứng
    'accept_language' => [
        'vi' => 'vi-vn',
        'vi-vn' => 'vi-vn',
        'zh-hans-cn' => 'zh_cn',
        'en-hans-us' => 'en_us',
    ],
    // Có hỗ trợ nhóm ngôn ngữ không
    'allow_group' => true,
];
