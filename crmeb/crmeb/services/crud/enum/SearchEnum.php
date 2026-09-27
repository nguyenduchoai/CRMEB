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

namespace crmeb\services\crud\enum;

/**
 * Enum cách tìm kiếm
 * Class SearchEnum
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/8/14
 * @package crmeb\services\crud\enum
 */
class SearchEnum
{
    //Bằng
    const SEARCH_TYPE_EQ = '=';
    //Nhỏ hơn hoặc bằng
    const SEARCH_TYPE_LTEQ = '<=';
    //Lớn hơn hoặc bằng
    const SEARCH_TYPE_GTEQ = '>=';
    //Không bằng
    const SEARCH_TYPE_NEQ = '<>';
    //Tìm kiếm gần đúng
    const SEARCH_TYPE_LIKE = 'LIKE';
    //Khoảng - dùng để tìm kiếm khoảng thời gian
    const SEARCH_TYPE_BETWEEN = 'BETWEEN';

    //Loại tìm kiếm
    const SEARCH_TYPE = [
        self::SEARCH_TYPE_EQ,
        self::SEARCH_TYPE_LTEQ,
        self::SEARCH_TYPE_GTEQ,
        self::SEARCH_TYPE_NEQ,
        self::SEARCH_TYPE_LIKE,
        self::SEARCH_TYPE_BETWEEN,
    ];
}
