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
/**
 * Cấu hình tạo module
 * php think build model_name
 */
return [
    // File cần tự động tạo
    '__file__'   => ['.htaccess','ExecptionHandle.php'],
    // Thư mục cần tự động tạo
    '__dir__'    => ['controller/v1','config','lang','validates/login/','route'],
    // Lớp controller cần tự động tạo
    'controller' => ['Index'],
    // Xác thực form cần tự động tạo
    'validates' => ['Index'],
    // Route cần tự động tạo
    'route' => ['route'],
    // File cấu hình cần tự động tạo
    'config'      => ['route'],
    // File cấu hình đa ngôn ngữ cần tự động tạo
    'lang'      => ['zh-CN','en-US'],
    // Mẫu cần tự động tạo
//    'view'       => ['index/index'],
];
