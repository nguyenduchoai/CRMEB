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
// | Cài đặt template
// +----------------------------------------------------------------------

return [
    // Loại template engine dùng Think
    'type'          => 'Think',
    // Quy tắc render template mặc định: 1 chuyển thành chữ thường + gạch dưới, 2 chuyển toàn bộ thành chữ thường, 3 giữ nguyên tên action
    'auto_rule'     => 1,
    // Tên thư mục template
    'view_dir_name' => 'view',
    // Hậu tố template
    'view_suffix'   => 'html',
    // Ký tự phân tách tên file template
    'view_depr'     => DIRECTORY_SEPARATOR,
    // Thẻ bắt đầu tag thông thường của template engine
    'tpl_begin'     => '{',
    // Thẻ kết thúc tag thông thường của template engine
    'tpl_end'       => '}',
    // Thẻ bắt đầu tag của thư viện tag (taglib)
    'taglib_begin'  => '{',
    // Thẻ kết thúc tag của thư viện tag (taglib)
    'taglib_end'    => '}',
    //Đường dẫn file template
    'view_path'     => public_path(),
];
