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
 * Enum phương thức truy cập
 * Class ActionEnum
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/8/12
 * @package crmeb\services\crud\enum
 */
class ActionEnum
{
    //Danh sách
    const INDEX = 'index';
    //Lấy dữ liệu tạo
    const CREATE = 'create';
    //Lưu
    const SAVE = 'save';
    //Lấy dữ liệu sửa
    const EDIT = 'edit';
    //Sửa
    const UPDATE = 'update';
    //Trạng thái
    const STATUS = 'status';
    //Xóa
    const DELETE = 'delete';
    //Xem
    const READ = 'read';
    //Tên tất cả phương thức
    const ACTION_ALL = [
        self::INDEX,
        self::CREATE,
        self::SAVE,
        self::EDIT,
        self::UPDATE,
        self::STATUS,
        self::DELETE,
        self::READ
    ];
}
