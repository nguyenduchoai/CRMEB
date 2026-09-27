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

namespace upgrade;

use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use crmeb\exceptions\AdminException;

/**
 * Trình quản lý phiên bản
 * Dùng để quản lý nâng cấp vượt phiên bản
 * Class VersionManager
 * @package upgrade
 */
class VersionManager
{
    /**
     * Cấu hình
     * @var array
     */
    protected $config = [];

    /**
     * Tiền tố bảng cơ sở dữ liệu
     * @var string
     */
    protected $prefix = '';

    /**
     * Thông tin phiên bản hiện tại
     * @var array
     */
    protected $currentVersion = [];

    /**
     * Giải thích các loại SQL
     */
    const SQL_TYPE_CREATE_TABLE = 1;    // Tạo bảng
    const SQL_TYPE_DROP_TABLE = 2;       // Xóa bảng
    const SQL_TYPE_ADD_COLUMN = 3;       // Thêm trường
    const SQL_TYPE_MODIFY_COLUMN = 4;    // Trường được sửa
    const SQL_TYPE_DROP_COLUMN = 5;      // Xóa trường
    const SQL_TYPE_INSERT_DATA = 6;      // Thêm dữ liệu
    const SQL_TYPE_UPDATE_DATA = 7;      // Dữ liệu cần chỉnh sửa
    const SQL_TYPE_DELETE_DATA = 8;      // Xóa dữ liệu
    const SQL_TYPE_RAW = -1;             // Thực thi SQL trực tiếp

    /**
     * VersionManager constructor.
     */
    public function __construct()
    {
        $this->config = Config::get('upgrade', []);
        $this->prefix = config('database.connections.' . config('database.default'))['prefix'];
        $this->currentVersion = $this->getCurrentVersion();
    }

    /**
     * Lấy thông tin phiên bản hệ thống hiện tại
     * @return array
     */
    public function getCurrentVersion(): array
    {
        $file = app()->getRootPath() . '.version';
        $arr = [];

        if (!file_exists($file)) {
            return $arr;
        }

        $list = @file($file);
        if (!$list) {
            return $arr;
        }

        foreach ($list as $val) {
            $val = str_replace(["\r", "\n", "\t"], '', $val);
            if (strpos($val, '=') !== false) {
                list($k, $v) = explode('=', $val, 2);
                $arr[trim($k)] = trim($v);
            }
        }

        return $arr;
    }

    /**
     * Lấy mã phiên bản hiện tại
     * @return int
     */
    public function getCurrentVersionCode(): int
    {
        return (int)($this->currentVersion['version_code'] ?? 0);
    }

    /**
     * Lấy tên phiên bản hiện tại
     * @return string
     */
    public function getCurrentVersionName(): string
    {
        return $this->currentVersion['version'] ?? '';
    }

    /**
     * Lấy danh sách tất cả phiên bản
     * @return array
     */
    public function getAllVersions(): array
    {
        return $this->config['versions'] ?? [];
    }

    /**
     * Lấy thông tin phiên bản mới nhất
     * @return array
     */
    public function getLatestVersion(): array
    {
        $versions = $this->getAllVersions();
        return end($versions) ?: [];
    }

    /**
     * Lấy danh sách phiên bản cần nâng cấp
     * Tất cả phiên bản từ phiên bản hiện tại đến phiên bản mới nhất
     * @return array
     */
    public function getPendingVersions(): array
    {
        $currentCode = $this->getCurrentVersionCode();
        $versions = $this->getAllVersions();
        $pending = [];
        foreach ($versions as $version) {
            if ($version['code'] > $currentCode) {
                $pending[] = $version;
            }
        }

        // Sắp xếp theo mã phiên bản từ nhỏ đến lớn
        usort($pending, function ($a, $b) {
            return $a['code'] - $b['code'];
        });

        return $pending;
    }

    /**
     * Lấy khoảng cách phiên bản cần nâng cấp
     * @return int
     */
    public function getVersionGap(): int
    {
        return count($this->getPendingVersions());
    }

    /**
     * Có cần nâng cấp không
     * @return bool
     */
    public function needUpgrade(): bool
    {
        return $this->getVersionGap() > 0;
    }

    /**
     * Lấy cấu hình yêu cầu phiên bản tối thiểu
     * @return array
     */
    public function getMinVersionConfig(): array
    {
        return $this->config['min_version'] ?? [];
    }

    /**
     * Kiểm tra phiên bản hiện tại có đáp ứng yêu cầu phiên bản tối thiểu không
     * @return bool
     */
    public function meetsMinVersionRequirement(): bool
    {
        $minVersion = $this->getMinVersionConfig();
        if (empty($minVersion)) {
            return true; // Chưa cấu hình phiên bản tối thiểu, mặc định cho phép
        }

        $minCode = $minVersion['code'] ?? 0;
        $currentCode = $this->getCurrentVersionCode();

        return $currentCode >= $minCode;
    }

    /**
     * Lấy thông báo lỗi về phiên bản tối thiểu
     * @return string
     */
    public function getMinVersionMessage(): string
    {
        $minVersion = $this->getMinVersionConfig();
        return $minVersion['message'] ?? 'Phiên bản hiện tại không hỗ trợ tính năng nâng cấp trực tuyến vượt phiên bản';
    }

    /**
     * Kiểm tra tính khả dụng của nâng cấp vượt phiên bản
     * @return array ['available' => bool, 'message' => string, 'current_version' => string, 'min_version' => string]
     */
    public function checkUpgradeAvailability(): array
    {
        $currentCode = $this->getCurrentVersionCode();
        $currentVersion = $this->getCurrentVersionName();
        $minVersion = $this->getMinVersionConfig();

        if (empty($minVersion)) {
            return [
                'available' => true,
                'message' => 'Có thể sử dụng nâng cấp vượt phiên bản',
                'current_version' => $currentVersion,
                'current_code' => $currentCode,
                'min_version' => '',
                'min_code' => 0
            ];
        }

        $minCode = $minVersion['code'] ?? 0;
        $available = $currentCode >= $minCode;

        return [
            'available' => $available,
            'message' => $available ? 'Có thể sử dụng nâng cấp vượt phiên bản' : $this->getMinVersionMessage(),
            'current_version' => $currentVersion,
            'current_code' => $currentCode,
            'min_version' => $minVersion['version'] ?? '',
            'min_code' => $minCode
        ];
    }

    /**
     * Lấy script nâng cấp của phiên bản
     * @param array $version
     * @return array
     */
    public function getVersionUpgradeData(array $version): array
    {
        $filePath = ($this->config['upgrade_path'] ?? '') . ($version['file'] ?? '');

        if (!file_exists($filePath)) {
            return [];
        }

        $data = include $filePath;
        return is_array($data) ? $data : [];
    }

    /**
     * Lấy tất cả SQL nâng cấp chờ thực thi
     * @return array
     */
    public function getAllPendingUpgradeSql(): array
    {
        $pendingVersions = $this->getPendingVersions();
        $allSql = [];

        foreach ($pendingVersions as $version) {
            $upgradeData = $this->getVersionUpgradeData($version);
            if (!empty($upgradeData['update_sql'])) {
                foreach ($upgradeData['update_sql'] as $sql) {
                    $sql['version'] = $version['version'];
                    $sql['version_code'] = $version['code'];
                    $allSql[] = $sql;
                }
            }
        }

        return $allSql;
    }

    /**
     * Thực thi một câu SQL nâng cấp
     * @param array $sqlItem
     * @return array ['success' => bool, 'message' => string]
     */
    public function executeSqlItem(array $sqlItem): array
    {
        $type = $sqlItem['type'] ?? 0;
        $table = $this->prefix . ($sqlItem['table'] ?? '');
        $field = $sqlItem['field'] ?? '';
        $findSql = $sqlItem['findSql'] ?? '';
        $sql = $sqlItem['sql'] ?? '';
        $whereSql = $sqlItem['whereSql'] ?? '';
        $whereTable = isset($sqlItem['whereTable']) ? $this->prefix . $sqlItem['whereTable'] : '';
        $newTable = isset($sqlItem['new_table']) ? $this->prefix . $sqlItem['new_table'] : '';

        try {
            // Thay thế tên bảng
            if ($findSql) {
                $findSql = str_replace('@table', $table, $findSql);
            }

            // Kiểm tra trước
            if ($findSql) {
                $exists = !empty(Db::query($findSql));

                switch ($type) {
                    case self::SQL_TYPE_CREATE_TABLE:
                    case self::SQL_TYPE_ADD_COLUMN:
                    case self::SQL_TYPE_INSERT_DATA:
                        if ($exists) {
                            return ['success' => true, 'message' => $this->getSkipMessage($type, $table, $field), 'skipped' => true];
                        }
                        break;
                    case self::SQL_TYPE_MODIFY_COLUMN:
                    case self::SQL_TYPE_DROP_COLUMN:
                    case self::SQL_TYPE_UPDATE_DATA:
                        if (!$exists) {
                            return ['success' => true, 'message' => $this->getSkipMessage($type, $table, $field), 'skipped' => true];
                        }
                        break;
                    case self::SQL_TYPE_DELETE_DATA:
                        if (!$exists) {
                            return ['success' => true, 'message' => 'Dữ liệu không tồn tại, bỏ qua thao tác xóa', 'skipped' => true];
                        }
                        break;
                }
            }

            // Thay thế placeholder trong SQL
            $execSql = str_replace('@table', $table, $sql);

            // Xử lý truy vấn bảng liên kết
            if (in_array($type, [self::SQL_TYPE_INSERT_DATA, self::SQL_TYPE_UPDATE_DATA]) && $whereSql && $whereTable) {
                $whereSql = str_replace('@whereTable', $whereTable, $whereSql);
                $result = Db::query($whereSql);
                $tabId = $result[0]['tabId'] ?? 0;
                if (!$tabId) {
                    return ['success' => true, 'message' => 'Dữ liệu liên kết không tồn tại, bỏ qua', 'skipped' => true];
                }
                $execSql = str_replace('@tabId', $tabId, $execSql);
            }

            // Xử lý tên bảng mới
            if ($type == self::SQL_TYPE_RAW && $newTable) {
                $execSql = str_replace('@new_table', $newTable, $execSql);
            }

            // Thực thi SQL
            if ($execSql) {
                Db::execute($execSql);
                Log::write(['type' => 'upgrade_sql', 'sql' => $execSql, 'item' => json_encode($sqlItem)], 'notice');
            }

            return ['success' => true, 'message' => $this->getSuccessMessage($type, $table, $field)];

        } catch (\Throwable $e) {
            Log::error(['type' => 'upgrade_sql_error', 'error' => $e->getMessage(), 'item' => json_encode($sqlItem)]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Lấy thông báo bỏ qua
     */
    protected function getSkipMessage(int $type, string $table, string $field): string
    {
        $messages = [
            self::SQL_TYPE_CREATE_TABLE => "Bảng {$table} đã tồn tại",
            self::SQL_TYPE_DROP_TABLE => "Bảng {$table} không tồn tại",
            self::SQL_TYPE_ADD_COLUMN => "Trường {$field} trong bảng {$table} đã tồn tại",
            self::SQL_TYPE_MODIFY_COLUMN => "Trường {$field} trong bảng {$table} không tồn tại",
            self::SQL_TYPE_DROP_COLUMN => "Trường {$field} trong bảng {$table} không tồn tại",
            self::SQL_TYPE_INSERT_DATA => "Dữ liệu trong bảng {$table} đã tồn tại",
            self::SQL_TYPE_UPDATE_DATA => "Dữ liệu trong bảng {$table} không tồn tại",
        ];
        return $messages[$type] ?? 'Bỏ qua';
    }

    /**
     * Lấy thông báo thành công
     */
    protected function getSuccessMessage(int $type, string $table, string $field): string
    {
        $messages = [
            self::SQL_TYPE_CREATE_TABLE => "Tạo bảng {$table} thành công",
            self::SQL_TYPE_DROP_TABLE => "Xóa bảng {$table} thành công",
            self::SQL_TYPE_ADD_COLUMN => "Thêm trường {$field} vào bảng {$table} thành công",
            self::SQL_TYPE_MODIFY_COLUMN => "Sửa trường {$field} trong bảng {$table} thành công",
            self::SQL_TYPE_DROP_COLUMN => "Xóa trường {$field} khỏi bảng {$table} thành công",
            self::SQL_TYPE_INSERT_DATA => "Thêm dữ liệu vào bảng {$table} thành công",
            self::SQL_TYPE_UPDATE_DATA => "Sửa dữ liệu trong bảng {$table} thành công",
            self::SQL_TYPE_DELETE_DATA => "Xóa dữ liệu khỏi bảng {$table} thành công",
            self::SQL_TYPE_RAW => "Thực thi SQL trên bảng {$table} thành công",
        ];
        return $messages[$type] ?? 'Thực thi thành công';
    }

    /**
     * Cập nhật tệp phiên bản
     * @param string $version
     * @param int $code
     * @return bool
     */
    public function updateVersionFile(string $version, int $code): bool
    {
        $file = app()->getRootPath() . '.version';
        $data = $this->getCurrentVersion();

        $data['version'] = $version;
        $data['version_code'] = $code;
        $data['platform'] = $this->config['platform'] ?? 'CRMEB';
        $data['app_id'] = $data['app_id'] ?? ($this->config['app_id'] ?? '');
        $data['app_key'] = $data['app_key'] ?? ($this->config['app_key'] ?? '');

        $content = '';
        foreach ($data as $key => $value) {
            $content .= "{$key}={$value}\n";
        }

        return file_put_contents($file, $content) !== false;
    }

    /**
     * Lấy thông tin tổng quan nâng cấp
     * @return array
     */
    public function getUpgradeOverview(): array
    {
        $currentVersion = $this->getCurrentVersionName();
        $currentCode = $this->getCurrentVersionCode();
        $latestVersion = $this->getLatestVersion();
        $pendingVersions = $this->getPendingVersions();

        return [
            'current_version' => $currentVersion,
            'current_code' => $currentCode,
            'latest_version' => $latestVersion['version'] ?? '',
            'latest_code' => $latestVersion['code'] ?? 0,
            'need_upgrade' => $this->needUpgrade(),
            'version_gap' => $this->getVersionGap(),
            'pending_versions' => array_map(function ($v) {
                return [
                    'version' => $v['version'],
                    'code' => $v['code'],
                    'description' => $v['description'] ?? ''
                ];
            }, $pendingVersions)
        ];
    }

    /**
     * Lấy tất cả bộ xử lý di chuyển dữ liệu đang chờ thực thi
     * @return array
     */
    public function getAllPendingDataHandlers(): array
    {
        $pendingVersions = $this->getPendingVersions();
        $allHandlers = [];

        foreach ($pendingVersions as $version) {
            $upgradeData = $this->getVersionUpgradeData($version);
            if (!empty($upgradeData['data_handlers'])) {
                foreach ($upgradeData['data_handlers'] as $handler) {
                    $handler['version'] = $version['version'];
                    $handler['version_code'] = $version['code'];
                    $allHandlers[] = $handler;
                }
            }
        }

        return $allHandlers;
    }

    /**
     * Lấy bộ xử lý di chuyển dữ liệu của phiên bản chỉ định
     * @param array $version
     * @return array
     */
    public function getVersionDataHandlers(array $version): array
    {
        $upgradeData = $this->getVersionUpgradeData($version);
        return $upgradeData['data_handlers'] ?? [];
    }
}
