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
// | Cài đặt ứng dụng
// +----------------------------------------------------------------------

return [
    // Có bắt buộc dùng route không
    'url_route_must'        => true,
    // Gộp quy tắc route
    'route_rule_merge'      => true,
    // Route có khớp hoàn toàn không
    'route_complete_match'  => true,
    // Có tự động chuyển đổi tên controller và action trong URL không
    'url_convert'           => false,
];
