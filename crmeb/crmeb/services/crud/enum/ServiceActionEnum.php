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
 * Enum phương thức tầng logic
 * Class ServiceActionEnum
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/8/14
 * @package crmeb\services\crud\enum
 */
class ServiceActionEnum
{
    //Tìm kiếm
    const INDEX = 'index';
    //Lấy form
    const FORM = 'form';
    //Lưu
    const SAVE = 'save';
    //Cập nhật
    const UPDATE = 'update';

    const SERVICE_ACTION_ALL = [
        self::INDEX,
        self::FORM,
        self::SAVE,
        self::UPDATE
    ];
}
