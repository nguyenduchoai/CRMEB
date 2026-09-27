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
 * Cấu hình nâng cấp
 * Danh sách phiên bản được sắp xếp từ nhỏ đến lớn, khi nâng cấp sẽ thực thi theo thứ tự
 */
return [
    // Yêu cầu phiên bản tối thiểu (chỉ khi đạt phiên bản này mới dùng được tính năng nâng cấp trực tuyến vượt phiên bản)
    // Vì tính năng nâng cấp trực tuyến vượt phiên bản được phát triển từ phiên bản v6.0.0, người dùng ở phiên bản thấp hơn không thể sử dụng
    'min_version' => [
        'version' => 'CRMEB-BZ v6.0.0',
        'code' => 600,
        'message' => 'Tính năng nâng cấp trực tuyến vượt phiên bản chỉ dùng được từ phiên bản v6.0.0 trở lên, vui lòng nâng cấp thủ công lên phiên bản v6.0.0 trước'
    ],

    // Danh sách phiên bản (sắp xếp theo phiên bản từ nhỏ đến lớn)
    // version: tên phiên bản
    // code: mã phiên bản (số, dùng để so sánh)
    // file: tên tệp script nâng cấp (tương đối so với thư mục upgrade/versions/)
    // description: mô tả phiên bản
    'versions' => [
        [
            'version' => 'CRMEB-BZ v6.0.0',
            'code' => 600,
            'file' => 'v6.0.0.php',
            'description' => 'Phiên bản tối ưu hiệu năng'
        ],
    ],

    // Thư mục script nâng cấp
    'upgrade_path' => app()->getRootPath() . 'upgrade' . DIRECTORY_SEPARATOR . 'versions' . DIRECTORY_SEPARATOR,

    // Thông tin nền tảng
    'platform' => 'CRMEB',

    // Thông tin xác thực APP (có thể đọc từ tệp .version để ghi đè)
    'app_id' => 'ze7x9rxsv09l6pvsyo',
    'app_key' => 'fuF7U9zaybLa5gageVQzxtxQMFnvU2OI',

    // Cấu hình máy chủ nâng cấp từ xa
    'remote' => [
        'login_url' => 'https://upgrade.crmeb.net/api/login',
        'upgrade_url' => 'https://upgrade.crmeb.net/api/upgrade/list',
        'upgrade_current_url' => 'https://upgrade.crmeb.net/api/upgrade/current_list',
        'agreement_url' => 'https://upgrade.crmeb.net/api/upgrade/agreement',
        'package_download_url' => 'https://upgrade.crmeb.net/api/upgrade/download',
        'upgrade_status_url' => 'https://upgrade.crmeb.net/api/upgrade/status',
        'upgrade_log_url' => 'https://upgrade.crmeb.net/api/upgrade/log',
    ],

    // Cấu hình sao lưu
    'backup' => [
        'database' => true,  // Có sao lưu cơ sở dữ liệu không
        'project' => true,   // Có sao lưu tệp dự án không
        'path' => app()->getRootPath() . 'backup' . DIRECTORY_SEPARATOR,
    ],

    // Thư mục bỏ qua (khi sao lưu)
    'ignore_dirs' => ['.', '..', '.git', '.idea', 'runtime', 'backup', 'upgrade'],

    // Phần mở rộng tệp bỏ qua
    'ignore_extensions' => ['zip', 'gz', 'log'],
];
