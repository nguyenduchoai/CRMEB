<?php

namespace crmeb\utils;

use crmeb\exceptions\AdminException;

/**
 * Công tắc các tính năng có thể ghi file, sửa CSDL hoặc tải mã từ bên ngoài về server.
 * Mặc định đều tắt, chỉ bật khi cấu hình trong tệp .env.
 * Class FeatureSwitch
 * @package crmeb\utils
 */
class FeatureSwitch
{
    /**
     * Trình sửa file online: bật khi mục PASSWORD trong phần [FILESYSTEM] khác rỗng
     */
    const FILE_EDITOR = 'file_editor';

    /**
     * Bộ sinh mã CRUD (ghi file, tạo/sửa/xóa bảng): bật khi [APP] CRUD_MAKE = true
     */
    const CRUD_MAKE = 'crud_make';

    /**
     * Nâng cấp trực tuyến và nâng cấp xuyên phiên bản: bật khi [UPGRADE] ONLINE_ENABLE = true
     */
    const ONLINE_UPGRADE = 'online_upgrade';

    /**
     * Thông báo khi tính năng đang tắt (chuỗi gốc để getLang() tra mã trong eb_lang_code)
     */
    const MESSAGES = [
        self::FILE_EDITOR => 'Trình sửa file online đang tắt, nếu cần dùng, vui lòng đặt mục PASSWORD trong phần [FILESYSTEM] của tệp .env',
        self::CRUD_MAKE => 'Bộ sinh mã CRUD đang tắt, nếu cần dùng, vui lòng đặt mục CRUD_MAKE trong phần [APP] của tệp .env thành true',
        self::ONLINE_UPGRADE => 'Nâng cấp trực tuyến đang tắt, nếu cần dùng, vui lòng đặt mục ONLINE_ENABLE trong phần [UPGRADE] của tệp .env thành true',
    ];

    /**
     * Tính năng có đang bật không; tên không xác định luôn coi là tắt
     * @param string $feature
     * @return bool
     */
    public static function enabled(string $feature): bool
    {
        switch ($feature) {
            case self::FILE_EDITOR:
                $password = config('filesystem.password');
                return is_string($password) && $password !== '';
            case self::CRUD_MAKE:
                return config('app.crud_make') === true;
            case self::ONLINE_UPGRADE:
                return config('upgrade.online_enable') === true;
            default:
                return false;
        }
    }

    /**
     * Ném AdminException nếu tính năng đang tắt
     * @param string $feature
     * @return void
     */
    public static function check(string $feature)
    {
        if (!self::enabled($feature)) {
            throw new AdminException(self::MESSAGES[$feature] ?? 'Thao tác không hợp lệ');
        }
    }
}
