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

namespace app\services\system;

use app\services\system\log\SystemFileMd5Services;
use SplFileInfo;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use app\jobs\UpgradeJob;
use app\services\BaseServices;
use crmeb\services\FileService;
use crmeb\services\HttpService;
use crmeb\services\CacheService;
use crmeb\utils\fileVerification;
use crmeb\exceptions\AdminException;
use app\dao\system\upgrade\UpgradeLogDao;

/**
 * Nâng cấp trực tuyến
 * Class UpgradeServices
 * @package app\services\system
 */
class UpgradeServices extends BaseServices
{
    const LOGIN_URL = 'http://upgrade.crmeb.net/api/login';
    const UPGRADE_URL = 'http://upgrade.crmeb.net/api/upgrade/list';
    const UPGRADE_CURRENT_URL = 'http://upgrade.crmeb.net/api/upgrade/current_list';
    const AGREEMENT_URL = 'http://upgrade.crmeb.net/api/upgrade/agreement';
    const PACKAGE_DOWNLOAD_URL = 'http://upgrade.crmeb.net/api/upgrade/download';
    const UPGRADE_STATUS_URL = 'http://upgrade.crmeb.net/api/upgrade/status';
    const UPGRADE_LOG_URL = 'http://upgrade.crmeb.net/api/upgrade/log';

    /**
     * @var array $requestData
     */
    private $requestData = [];

    /**
     * @var int $timeStamp
     */
    private $timeStamp = 0;

    /**
     * UpgradeServices constructor.
     * @param UpgradeLogDao $dao
     */
    public function __construct(UpgradeLogDao $dao)
    {
        $this->dao = $dao;
        $versionData = $this->getVersion();
        //        if ($versionData['version_code'] < 450) return true;
        if (empty($versionData)) {
            throw new AdminException('Mất thông tin cấp phép');
        }

        $this->timeStamp = time();
        $recVersion = $this->recombinationVersion($versionData['version'] ?? '');

        $this->requestData = [
            'nonce' => mt_rand(111, 999),
            'host' => app()->request->host(),
            'timestamp' => $this->timeStamp,
            'app_id' => trim($versionData['app_id'] ?? ''),
            'app_key' => trim($versionData['app_key'] ?? ''),
            'version' => implode('.', $recVersion)
        ];

        if (!CacheService::get('upgrade_auth_token')) {
            $this->getAuth();
        }
    }

    /**
     * Lấy thông tin phiên bản
     * @return void
     */
    /**
     * Lấy thông tin cấu hình file
     * @param string $name
     * @param string $path
     * @return array|string
     */
    public function getVersion(string $name = '', string $path = '')
    {
        $file = '.version';
        $arr = [];
        $list = @file($path ?: app()->getRootPath() . $file);

        foreach ($list as $val) {
            list($k, $v) = explode('=', str_replace(PHP_EOL, '', $val));
            $arr[$k] = $v;
        }
        return !empty($name) ? $arr[$name] ?? '' : $arr;
    }

    /**
     * Lấy số phiên bản
     * @param $input
     * @return array
     */
    public function recombinationVersion($input): array
    {
        $version = substr($input, strpos($input, ' v') + 1);
        return array_map(function ($item) {
            if (preg_match('/\d+/', $item, $arr)) {
                $item = $arr[0];
            }
            return (int)$item;
        }, explode('.', $version));
    }

    /**
     * Lấy Token
     * @return void
     */
    public function getAuth()
    {
        $this->getSign($this->timeStamp);
        $result = HttpService::postRequest(self::LOGIN_URL, $this->requestData);
        if (!$result) {
            throw new AdminException('Ủy quyền thất bại');
        }

        $authData = json_decode($result, true);
        if (!isset($authData['status']) || $authData['status'] != 200) {
            Log::error(['msg' => $authData['msg'] ?? '', 'error' => $authData['data'] ?? []]);
            throw new AdminException($authData['msg'] ?? 'Ủy quyền thất bại');
        }
        CacheService::set('upgrade_auth_token', $authData['data']['access_token'], 7200);
    }

    /**
     * Lấy chữ ký (signature)
     * @param int $timeStamp
     * @return void
     */
    public function getSign(int $timeStamp)
    {
        $data = $this->requestData;
        if ((!isset($data['host']) || !$data['host']) ||
            (!isset($data['nonce']) || !$data['nonce']) ||
            (!isset($data['app_id']) || !$data['app_id']) ||
            (!isset($data['version']) || !$data['version']) ||
            (!isset($data['app_key']) || !$data['app_key'])
        ) {
            throw new AdminException('Xác thực đã hết hiệu lực, vui lòng gửi lại yêu cầu');
        }

        $host = $data['host'];
        $nonce = $data['nonce'];
        $appId = $data['app_id'];
        $appKey = $data['app_key'];
        $version = $data['version'];
        unset($data['sign'], $data['nonce'], $data['host'], $data['version'], $data['app_id'], $data['app_key']);

        $params = json_encode($data);
        $shaiAtt = [
            'host' => $host,
            'nonce' => $nonce,
            'app_id' => $appId,
            'params' => $params,
            'app_key' => $appKey,
            'version' => $version,
            'time_stamp' => $timeStamp
        ];

        sort($shaiAtt, SORT_STRING);
        $shaiStr = implode(',', $shaiAtt);
        $this->requestData['sign'] = hash("SHA256", $shaiStr);
    }

    /**
     * Danh sách nâng cấp
     * @return mixed
     */
    public function getUpgradeList()
    {
        [$page, $limit] = $this->getPageValue();
        $this->requestData['page'] = (string)($page ?: 1);
        $this->requestData['limit'] = (string)($limit ?: 10);
        $this->getSign($this->timeStamp);
        $result = HttpService::getRequest(self::UPGRADE_URL, $this->requestData);
        if (!$result) {
            throw new AdminException('Lấy danh sách nâng cấp thất bại');
        }

        $data = json_decode($result, true);
        if (!$this->checkAuth($data)) {
            throw new AdminException($data['msg'] ?? 'Lấy danh sách nâng cấp thất bại');
        }
        return $data['data'] ?? [];
    }

    /**
     * Danh sách có thể nâng cấp
     * @return mixed
     */
    public function getUpgradeableList()
    {
        $this->getSign($this->timeStamp);
        $result = HttpService::getRequest(self::UPGRADE_CURRENT_URL, $this->requestData, ['Access-Token: Bearer ' . CacheService::get('upgrade_auth_token')]);
        if (!$result) {
            throw new AdminException('Lấy danh sách có thể nâng cấp thất bại');
        }

        $data = json_decode($result, true);
        if (!$this->checkAuth($data)) {
            throw new AdminException($data['msg'] ?? 'Lấy danh sách nâng cấp thất bại');
        }

        if ($data['data']) {
            $routineData = [
                'version' => $data['data'][0]['first_version'] . '.' . $data['data'][0]['second_version'] . '.' . $data['data'][0]['third_version'],
                'desc' => $data['data'][0]['content'],
                'is_live' => 0
            ];
            CacheService::set('routine_upload_data', $routineData, 86400);
        }
        return $data['data'] ?? [];
    }

    /**
     * Thỏa thuận nâng cấp
     * @return mixed
     */
    public function getAgreement()
    {
        $this->getSign($this->timeStamp);
        $result = HttpService::getRequest(self::AGREEMENT_URL, $this->requestData, ['Access-Token: Bearer ' . CacheService::get('upgrade_auth_token')]);
        if (!$result) {
            throw new AdminException('Lấy thỏa thuận nâng cấp thất bại');
        }

        $data = json_decode($result, true);
        if (!$this->checkAuth($data)) {
            throw new AdminException($data['msg'] ?? 'Lấy thỏa thuận nâng cấp thất bại');
        }
        return $data['data'] ?? [];
    }

    /**
     * Tải xuống
     * @param string $packageKey
     * @return bool
     */
    public function packageDownload(string $packageKey): bool
    {
        $token = md5(time());

        //Kiểm tra dung lượng cơ sở dữ liệu
        $this->checkDatabaseSize();

        $this->requestData['package_key'] = $packageKey;
        $this->getSign($this->timeStamp);
        $result = HttpService::getRequest(self::PACKAGE_DOWNLOAD_URL, $this->requestData, ['Access-Token: Bearer ' . CacheService::get('upgrade_auth_token')]);
        if (!$result) {
            throw new AdminException('Lấy gói nâng cấp thất bại');
        }
        $data = json_decode($result, true);

        if (!$this->checkAuth($data)) {
            throw new AdminException($data['msg'] ?? 'Ủy quyền thất bại');
        }

        if (empty($data['data']['server_package_link']) && empty($data['data']['client_package_link']) && empty($data['data']['pc_package_link'])) {
            CacheService::set($token . 'upgrade_status', 2, 86400);
            return true;
        }

        if (!empty($data['data']['server_package_link'])) {
            $this->downloadFile($data['data']['server_package_link'], $token . '_server_package');
        } else {
            CacheService::set($token . '_server_package', 2, 86400);
        }

        if (!empty($data['data']['client_package_link'])) {
            $this->downloadFile($data['data']['client_package_link'], $token . '_client_package');
        } else {
            CacheService::set($token . '_client_package', 2, 86400);
        }

        if (!empty($data['data']['pc_package_link'])) {
            $this->downloadFile($data['data']['pc_package_link'], $token . '_pc_package');
        } else {
            CacheService::set($token . '_pc_package', 2, 86400);
        }

        CacheService::set('upgrade_token', $token, 86400);
        CacheService::set($token . '_upgrade_data', $data, 86400);
        return true;
    }

    /**
     * Thực hiện tải xuống
     * @param string $seq
     * @param string $url
     * @param string $downloadPath
     * @param string $fileName
     * @param int $timeout
     * @return void
     */
    public function download(string $seq, string $url, string $downloadPath, string $fileName, int $timeout = 300)
    {
        ini_set('memory_limit', '-1');

        $filePath = $downloadPath . DS . $fileName;
        $fp_output = fopen($filePath, 'w');
        if (!$fp_output) {
            throw new AdminException('Không thể tạo tệp tải xuống');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);        // Timeout kết nối
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);         // Tổng thời gian timeout
        curl_setopt($ch, CURLOPT_FILE, $fp_output);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);      // Tự động theo chuyển hướng (redirect)
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.crmeb.com');
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp_output);  // Đóng handle file

        // Kiểm tra kết quả tải xuống
        if ($result === false || !empty($error)) {
            @unlink($filePath);
            throw new AdminException('Tải xuống thất bại: ' . $error);
        }

        if ($httpCode !== 200) {
            @unlink($filePath);
            throw new AdminException('Tải xuống thất bại, mã trạng thái HTTP: ' . $httpCode);
        }

        // Kiểm tra file có tồn tại và có nội dung không
        if (!is_file($filePath) || filesize($filePath) < 100) {
            @unlink($filePath);
            throw new AdminException('Tệp đã tải xuống không hợp lệ');
        }

        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'zip') {
            throw new AdminException('Định dạng gói cài đặt không hợp lệ');
        }

        /** @var FileService $fileService */
        $fileService = app()->make(FileService::class);
        $downloadFilePath = $downloadPath . DS . pathinfo($fileName, PATHINFO_FILENAME);
        if (!$fileService->extractFile($filePath, $downloadFilePath)) {
            throw new AdminException('Giải nén gói nâng cấp thất bại');
        }

        CacheService::set($seq . '_path', $downloadFilePath, 86400);
        CacheService::set($seq . '_name', $filePath, 86400);
        CacheService::set($seq, 2, 86400);
    }

    /**
     * Bắt đầu tải xuống
     * @param string $packageLink
     * @param string $seq
     * @return void
     */
    private function downloadFile(string $packageLink, string $seq)
    {
        $fileName = substr($packageLink, strrpos($packageLink, '/') + 1);
        $filePath = app()->getRootPath() . 'upgrade' . DS . date('Y-m-d');
        if (!is_dir($filePath)) mkdir($filePath, 0755, true);
        UpgradeJob::dispatch('download', [$seq, $packageLink, $filePath, $fileName, 300]);
        CacheService::set($seq, 1, 86400);
    }

    /**
     * Tiến trình nâng cấp
     * @return array
     */
    public function getProgress(): array
    {
        $token = CacheService::get('upgrade_token');
        if (empty($token)) {
            throw new AdminException('Vui lòng nâng cấp lại');
        }

        $serverProgress = CacheService::get($token . '_server_package'); // Tiến độ tải gói phía server
        $clientProgress = CacheService::get($token . '_client_package'); // Tiến độ tải gói phía client
        $pcProgress = CacheService::get($token . '_pc_package'); // Tiến độ tải gói phía PC
        $databaseBackupProgress = CacheService::get($token . '_database_backup'); // Tiến độ backup cơ sở dữ liệu
        $projectBackupProgress = CacheService::get($token . '_project_backup'); // Tiến độ backup dự án

        $databaseUpgradeProgress = CacheService::get($token . '_database_upgrade'); // Tiến độ nâng cấp cơ sở dữ liệu
        $coverageProjectProgress = CacheService::get($token . '_coverage_project'); // Tiến độ ghi đè dự án

        $stepNum = 1;
        $tip = 'Bắt đầu nâng cấp';
        if ($serverProgress == $clientProgress && $clientProgress == $pcProgress) {
            $tip = $serverProgress == 1 ? 'Bắt đầu tải xuống gói cài đặt' : 'Tải xuống gói cài đặt hoàn tất';
            if ($serverProgress == 2) {
                $stepNum += 1;
            }
        } else {
            $tip = 'Đang tải xuống gói cài đặt';
        }

        if ($databaseBackupProgress == 2) {
            $tip = 'Sao lưu cơ sở dữ liệu hoàn tất';
            $stepNum += 1;
        }

        if ($projectBackupProgress == 2) {
            $tip = 'Sao lưu dự án hoàn tất';
            $stepNum += 1;
        }

        if ((int)$databaseUpgradeProgress == 2) {
            $tip = 'Nâng cấp cơ sở dữ liệu hoàn tất';
            $stepNum += 1;
        }

        if ((int)$coverageProjectProgress == 2) {
            $tip = 'Nâng cấp dự án hoàn tất';
            $stepNum += 1;
        }

        $upgradeStatus = (int)CacheService::get($token . 'upgrade_status');
        if ($upgradeStatus == 2) {
            $stepNum = 6;
            $tip = 'Nâng cấp hoàn tất';
        } elseif ($upgradeStatus < 0) {
            $this->saveLog($token);
            throw new AdminException(CacheService::get($token . 'upgrade_status_tip', 'Nâng cấp thất bại'));
        } elseif ($serverProgress == 2 && $clientProgress == 2 && $pcProgress == 2 && $databaseBackupProgress == 2 && $projectBackupProgress == 2) {
            try {
                $this->overwriteProject();
            } catch (\Exception $e) {
                $this->sendUpgradeLog($token);
            }
        }

        $speed = sprintf("%.1f", $stepNum / 6 * 100);
        return compact('speed', 'tip');
    }

    /**
     * Sao lưu cơ sở dữ liệu
     * @param $token
     * @return bool
     * @throws \think\db\exception\BindParamException
     */
    public function databaseBackup($token): bool
    {
        try {
            //Backup dữ liệu bảng
            /** @var SystemDatabackupServices $backServices */
            $backServices = app()->make(SystemDatabackupServices::class);
            $tables = $backServices->getDataList();
            if (count($tables['list']) < 1) {
                throw new AdminException('Lấy bảng dữ liệu thất bại');
            }

            // Lấy số phiên bản từ file .version
            $versionData = $this->getVersion();
            $backServices->getDbBackup()->setFile(['name' => $versionData['version_code'], 'part' => 1]);
            $tables = implode(',', array_column($tables['list'], 'name'));
            $result = $backServices->backup($tables);
            if (!empty($result)) {
                throw new AdminException('Sao lưu cơ sở dữ liệu thất bại ' . $result);
            }

            $fileData = $backServices->getDbBackup()->getFile();
            $fileName = $fileData['filename'] . '.gz';
            if (!is_file($fileData['filepath'] . $fileName)) {
                throw new AdminException('Sao lưu cơ sở dữ liệu thất bại');
            }
            CacheService::set($token . '_database_backup', 2, 86400);
            CacheService::set($token . '_database_backup_name', $fileName, 86400);
            return true;
        } catch (\Exception $e) {
            Log::error('Nâng cấp thất bại, lý do:' . $e->getMessage());
            CacheService::set($token . 'upgrade_status', -1, 86400);
            CacheService::set($token . 'upgrade_status_tip', 'Nâng cấp thất bại, lý do:' . $e->getMessage(), 86400);
        }
        return false;
    }

    /**
     * Sao lưu dự án
     * @param string $token
     * @return bool
     */
    public function projectBackup(string $token): bool
    {
        try {
            ini_set('memory_limit', '-1');
            $appPath = app()->getRootPath();
            /** @var FileService $fileService */
            $fileService = app()->make(FileService::class);

            $dir = 'backup' . DS . date('Ymd') . DS . $token;
            $backupDir = $appPath . $dir;
            $fileService->handleDir($appPath . 'app', $backupDir . DS . 'app');
            $fileService->handleDir($appPath . 'config', $backupDir . DS . 'config');
            $fileService->handleDir($appPath . 'crmeb', $backupDir . DS . 'crmeb');

            // Lấy số phiên bản từ file .version
            $versionData = $this->getVersion();
            $fileName = $versionData['version_code'] . '-1.project.zip';
            $filePath = $appPath . 'backup' . DS . $fileName;

            /** @var FileService $fileService */
            $fileService = app()->make(FileService::class);
            $result = $fileService->addZip($backupDir, $filePath, $backupDir);
            if (!$result) {
                throw new AdminException('Sao lưu dự án thất bại');
            }

            CacheService::set($token . '_project_backup', 2, 86400);
            CacheService::set($token . '_project_backup_name', $fileName, 86400);

            //Kiểm tra backup dự án
            if (!is_file($filePath)) {
                throw new AdminException('Kiểm tra bản sao lưu dự án thất bại');
            }

            // Nén xong, xóa các file đã di chuyển
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($appPath . 'backup' . DS . date('Ymd'), \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            /** @var SplFileInfo $fileInfo */
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isDir()) {
                    @rmdir($fileInfo->getRealPath());
                } else {
                    @unlink($fileInfo->getRealPath());
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Nâng cấp thất bại, lý do:' . $e->getMessage());
            CacheService::set($token . 'upgrade_status', -1, 86400);
            CacheService::set($token . 'upgrade_status_tip', 'Nâng cấp thất bại, lý do:' . $e->getMessage(), 86400);
        }
        return false;
    }

    /**
     * Nâng cấp
     * @return bool
     * @throws \Exception
     */
    public function overwriteProject(): bool
    {
        try {
            if (!$token = CacheService::get('upgrade_token')) {
                throw new AdminException('Vui lòng tải lại gói nâng cấp');
            }

            if (CacheService::get($token . 'is_execute') == 2) {
                return true;
            }
            CacheService::set($token . 'is_execute', 2, 86400);

            $dataBackupName = CacheService::get($token . '_database_backup_name');
            if (!$dataBackupName || !is_file(app()->getRootPath() . 'backup' . DS . $dataBackupName)) {
                throw new AdminException('Sao lưu cơ sở dữ liệu thất bại');
            }

            $serverPackageFilePath = CacheService::get($token . '_server_package_path');
            if (!is_dir($serverPackageFilePath)) {
                throw new AdminException('Lỗi khi lấy tệp dự án');
            }

            // Thực thi file sql
            if (!$this->databaseUpgrade($token, $serverPackageFilePath)) {
                throw new AdminException('Nâng cấp cơ sở dữ liệu thất bại');
            }

            // Thay thế thư mục file
            $this->coverageProject($token);

            // Gửi log nâng cấp
            $this->sendUpgradeLog($token);
            $this->saveLog($token);
            CacheService::set($token . 'upgrade_status', 2, 86400);
            return true;
        } catch (\Exception $e) {
            Log::error('Nâng cấp thất bại, lý do:' . $e->getMessage());
            CacheService::set($token . 'upgrade_status', -1, 86400);
            CacheService::set($token . 'upgrade_status_tip', 'Nâng cấp thất bại, lý do:' . $e->getMessage(), 86400);
        }
        return false;
    }

    /**
     * Ghi log
     * @param $token
     * @return void
     */
    public function saveLog($token)
    {
        if (CacheService::get($token . 'is_save') == 2) {
            return true;
        }
        CacheService::set($token . 'is_save', 2, 86400);

        $upgradeData = CacheService::get($token . '_upgrade_data');

        $this->dao->save([
            'title' => $upgradeData['data']['title'] ?? '',
            'content' => $upgradeData['data']['content'] ?? '',
            'first_version' => $upgradeData['data']['first_version'] ?? '',
            'second_version' => $upgradeData['data']['second_version'] ?? '',
            'third_version' => $upgradeData['data']['third_version'] ?? '',
            'fourth_version' => $upgradeData['data']['fourth_version'] ?? '',
            'upgrade_time' => time(),
            'error_data' => CacheService::get($token . 'upgrade_status_tip', ''),
            'package_link' => CacheService::get($token . '_project_backup_name', ''),
            'data_link' => CacheService::get($token . '_database_backup_name', '')
        ]);
    }

    /**
     * Gửi log
     * @param string $token
     * @return bool
     */
    public function sendUpgradeLog(string $token): bool
    {
        try {
            $versionBefore = CacheService::get('version_before', '');
            $versionData = $this->getVersion();
            if (empty($versionData)) {
                throw new AdminException('Mất thông tin cấp phép');
            }
            $versionAfter = $this->recombinationVersion($versionData['version'] ?? '');

            $this->requestData['version_before'] = implode('.', $versionBefore);
            $this->requestData['version_after'] = implode('.', $versionAfter);
            $this->requestData['error_data'] = CacheService::get($token . 'upgrade_status_tip', '');

            $this->getSign($this->timeStamp);
            $result = HttpService::postRequest(self::UPGRADE_LOG_URL, $this->requestData, ['Access-Token: Bearer ' . CacheService::get('upgrade_auth_token')]);
            if (!$result) {
                throw new AdminException('Đẩy nhật ký nâng cấp thất bại');
            }

            $data = json_decode($result, true);
            $this->checkAuth($data);
        } catch (\Exception $e) {
            Log::error(['msg' => 'Gửi nhật ký nâng cấp thất bại, lý do' . ($data['msg'] ?? '') . $e->getMessage(), 'data' => $data]);
        }
        return true;
    }

    /**
     * Nâng cấp cơ sở dữ liệu
     * @param string $token
     * @param string $serverPackageFilePath
     * @return bool
     */
    public function databaseUpgrade(string $token, string $serverPackageFilePath): bool
    {
        $databaseFilePath = $serverPackageFilePath . DS . "upgrade" . DS . "database.php";
        if (!is_file($databaseFilePath)) {
            CacheService::set($token . '_database_upgrade', 2, 86400);
            return true;
        }
        CacheService::set($token . '_database_upgrade', 1, 86400);

        $sqlData = include $databaseFilePath;
        $nowCode = $this->getVersion('version_code');
        if ($sqlData['new_code'] <= $nowCode) {
            CacheService::set($token . '_database_upgrade', 2, 86400);
            return true;
        }

        $updateSql = $upgradeSql = [];
        foreach ($sqlData['update_sql'] as $items) {
            if ($items['code'] > $nowCode) {
                $upgradeSql[] = $items;
            }
        }

        if (empty($upgradeSql)) {
            CacheService::set($token . '_database_upgrade', 2, 86400);
            return true;
        }

        $prefix = config('database.connections.' . config('database.default'))['prefix'];
        Db::startTrans();
        try {
            foreach ($upgradeSql as $item) {
                $tip = [
                    '1' => 'bảng đã tồn tại',
                    '2' => 'bảng không tồn tại',
                    '3' => 'bảng, trường' . ($item['field'] ?? '') . 'đã tồn tại',
                    '4' => 'bảng, trường' . ($item['field'] ?? '') . 'không tồn tại',
                    '5' => 'bảng, trường cần xóa' . ($item['field'] ?? '') . 'không tồn tại',
                    '6' => 'bảng, dữ liệu đã tồn tại',
                    '6_2' => 'bảng, ID cha cần tra cứu không tồn tại',
                    '7' => 'bảng, dữ liệu đã tồn tại',
                    '8' => 'bảng, dữ liệu không tồn tại',
                ];
                if (!isset($item['table']) || !$item['table']) {
                    throw new AdminException('Vui lòng kiểm tra cấu trúc dữ liệu nâng cấp:table');
                }

                if (!isset($item['sql']) || !$item['sql']) {
                    throw new AdminException('Vui lòng kiểm tra cấu trúc dữ liệu nâng cấp:sql');
                }

                $whereTable = '';
                $table = $prefix . $item['table'];
                if (isset($item['whereTable']) && $item['whereTable']) {
                    $whereTable = $prefix . $item['whereTable'];
                }

                if (isset($item['findSql']) && $item['findSql']) {
                    $findSql = str_replace('@table', $table, $item['findSql']);
                    if (!empty(Db::query($findSql))) {
                        // 1 tạo bảng 2 xóa bảng 3 thêm cột 4 sửa cột 5 xóa cột 6 thêm dữ liệu 7 sửa dữ liệu 8 xóa dữ liệu -1 thực thi trực tiếp
                        // Bảng/trường/dữ liệu đã tồn tại thì bỏ qua, không làm gián đoạn nâng cấp
                        if (in_array($item['type'], [1, 3, 6])) {
                            Log::notice(['type' => 'database_upgrade_skip', 'reason' => $table . ($tip[$item['type']] ?? ''), 'item' => json_encode($item)]);
                            continue;
                        }
                    } else {
                        // Bảng/trường/dữ liệu không tồn tại thì bỏ qua thao tác sửa và xóa
                        if (in_array($item['type'], [4, 5, 7])) {
                            Log::notice(['type' => 'database_upgrade_skip', 'reason' => $table . ($tip[$item['type']] ?? ''), 'item' => json_encode($item)]);
                            continue;
                        }

                        if ($item['type'] == 8) {
                            continue;
                        }
                    }
                }

                if ($item['type'] == 4) {
                    if (!isset($item['rollback_sql']) || !$item['rollback_sql']) {
                        throw new AdminException('Vui lòng kiểm tra cấu trúc dữ liệu nâng cấp:rollback_sql');
                    }
                    $updateSql[] = $item;
                }

                $upSql = str_replace('@table', $table, $item['sql']);
                if ($item['type'] == 6 || $item['type'] == 7) {
                    if (isset($item['whereSql']) && $item['whereSql']) {
                        $whereSql = str_replace('@whereTable', $whereTable, $item['whereSql']);
                        $tabId = Db::query($whereSql)[0]['tabId'] ?? 0;
                        if (!$tabId) {
                            // Dữ liệu liên kết không tồn tại thì bỏ qua, không làm gián đoạn nâng cấp
                            Log::notice(['type' => 'database_upgrade_skip', 'reason' => $table . ' dữ liệu liên kết không tồn tại', 'item' => json_encode($item)]);
                            continue;
                        }
                        $upSql = str_replace('@tabId', $tabId, $upSql);
                    }
                } elseif ($item['type'] == 8) {
                    $upSql = str_replace(['@table', '@field', '@value'], [$table, $item['field'], $item['value']], $item['sql']);
                } elseif ($item['type'] == -1) {
                    if (isset($item['new_table']) && $item['new_table']) {
                        $new_table = $prefix . $item['new_table'];
                        $upSql = str_replace('@new_table', $new_table, $upSql);
                    }
                }

                if ($upSql) {
                    Db::execute($upSql);
                }
                Log::write(['type' => 'database_upgrade', '`item' => json_encode($item), 'upSql' => $upSql], 'notice');
            }

            Db::commit();
            CacheService::set($token . '_database_upgrade', 2, 86400);
        } catch (\Throwable $e) {
            Db::rollback();
            Log::error(['msg' => 'Nâng cấp cơ sở dữ liệu thất bại, lý do:' . $e->getMessage(), 'data' => json_encode($upgradeSql)]);
            CacheService::set($token . 'upgrade_status', -1, 86400);
            CacheService::set($token . 'upgrade_status_tip', 'Nâng cấp cơ sở dữ liệu thất bại, lý do:' . $e->getMessage(), 86400);
            if (!empty($updateSql)) {
                $this->rollbackStructure($prefix, $updateSql);
            }
            return false;
        }
        return true;
    }

    /**
     * Ghi đè dự án
     * @param string $token
     * @return bool
     */
    public function coverageProject(string $token): bool
    {
        $versionData = $this->getVersion();
        if (empty($versionData)) {
            throw new AdminException('Thông tin cấp phép bất thường');
        }
        CacheService::set('version_before', $this->recombinationVersion($versionData['version'] ?? ''), 86400);

        /** @var FileService $fileService */
        $fileService = app()->make(FileService::class);

        // Dự án phía server
        $serverPackageName = CacheService::get($token . '_server_package_name');

        // Dự án phía client
        $clientPackageName = CacheService::get($token . '_client_package_name');

        // Dự án phía PC
        $pcPackageName = CacheService::get($token . '_pc_package_name');

        if (!is_file($serverPackageName) && !is_file($clientPackageName) && !is_file($pcPackageName)) {
            throw new AdminException('Tệp nâng cấp bị lỗi, vui lòng tải lại');
        }

        if (is_file($serverPackageName) && !$fileService->extractFile($serverPackageName, app()->getRootPath())) {
            throw new AdminException('Giải nén phía máy chủ thất bại');
        }

        if (is_file($clientPackageName) && !$fileService->extractFile($clientPackageName, app()->getRootPath())) {
            throw new AdminException('Giải nén phía máy khách thất bại');
        }

        if (is_file($pcPackageName) && !$fileService->extractFile($pcPackageName, app()->getRootPath())) {
            throw new AdminException('Giải nén phía PC thất bại');
        }

        CacheService::set($token . '_coverage_project', 2, 86400);
        return true;
    }

    /**
     * Rollback cấu trúc bảng
     * @param string $prefix
     * @param array $updateSql
     * @return void
     */
    public function rollbackStructure(string $prefix, array $updateSql): void
    {
        try {
            foreach ($updateSql as $item) {
                Db::execute(str_replace('@table', $prefix . $item['table'], $item['rollback_sql']));
            }
        } catch (\Exception $e) {
            Log::error(['msg' => 'Hoàn tác cấu trúc cơ sở dữ liệu thất bại', 'error' => $e->getFile() . '__' . $e->getLine() . '__' . $e->getMessage(), 'data' => $updateSql]);
        }
    }

    /**
     * Khôi phục bản sao lưu cơ sở dữ liệu
     * @param string $backupFileName Tên file sao lưu
     * @return bool
     */
    public function restoreDatabase(string $backupFileName): bool
    {
        try {
            $backupPath = app()->getRootPath() . 'backup' . DS . $backupFileName;
            if (!is_file($backupPath)) {
                throw new AdminException('Tệp sao lưu cơ sở dữ liệu không tồn tại');
            }

            // Kiểm tra có phải file nén gz không
            $isGz = (pathinfo($backupFileName, PATHINFO_EXTENSION) === 'gz');

            // Đọc trực tiếp và thực thi file SQL
            $db = \think\facade\Db::connect();

            if ($isGz) {
                $gz = gzopen($backupPath, 'r');
                if (!$gz) {
                    throw new AdminException('Không thể mở tệp sao lưu');
                }

                $sql = '';
                while (!gzeof($gz)) {
                    $line = gzgets($gz);
                    // Bỏ qua chú thích và dòng trống
                    if (empty(trim($line)) || strpos(trim($line), '--') === 0) {
                        continue;
                    }
                    $sql .= $line;
                    // Kiểm tra có phải câu lệnh SQL hoàn chỉnh không
                    if (preg_match('/;\s*$/', trim($sql))) {
                        try {
                            $db->execute($sql);
                        } catch (\Exception $e) {
                            // Bỏ qua lỗi của SET và một số câu lệnh đặc biệt
                            if (strpos($sql, 'SET FOREIGN_KEY_CHECKS') === false) {
                                Log::warning('Thực thi SQL thất bại: ' . $e->getMessage());
                            }
                        }
                        $sql = '';
                    }
                }
                gzclose($gz);
            } else {
                $handle = fopen($backupPath, 'r');
                if (!$handle) {
                    throw new AdminException('Không thể mở tệp sao lưu');
                }

                $sql = '';
                while (!feof($handle)) {
                    $line = fgets($handle);
                    // Bỏ qua chú thích và dòng trống
                    if (empty(trim($line)) || strpos(trim($line), '--') === 0) {
                        continue;
                    }
                    $sql .= $line;
                    // Kiểm tra có phải câu lệnh SQL hoàn chỉnh không
                    if (preg_match('/;\s*$/', trim($sql))) {
                        try {
                            $db->execute($sql);
                        } catch (\Exception $e) {
                            if (strpos($sql, 'SET FOREIGN_KEY_CHECKS') === false) {
                                Log::warning('Thực thi SQL thất bại: ' . $e->getMessage());
                            }
                        }
                        $sql = '';
                    }
                }
                fclose($handle);
            }

            Log::notice(['type' => 'database_restore', 'file' => $backupFileName]);
            return true;
        } catch (\Exception $e) {
            Log::error('Khôi phục cơ sở dữ liệu thất bại: ' . $e->getMessage());
            throw new AdminException('Khôi phục cơ sở dữ liệu thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Khôi phục bản sao lưu file dự án
     * @param string $backupFileName Tên file sao lưu
     * @return bool
     */
    public function restoreProject(string $backupFileName): bool
    {
        try {
            $backupPath = app()->getRootPath() . 'backup' . DS . $backupFileName;
            if (!is_file($backupPath)) {
                throw new AdminException('Tệp sao lưu dự án không tồn tại');
            }

            /** @var FileService $fileService */
            $fileService = app()->make(FileService::class);

            // Giải nén file zip vào thư mục gốc dự án
            $result = $fileService->extractFile($backupPath, app()->getRootPath());
            if (!$result) {
                throw new AdminException('Khôi phục tệp dự án thất bại');
            }

            Log::notice(['type' => 'project_restore', 'file' => $backupFileName]);
            return true;
        } catch (\Exception $e) {
            Log::error('Khôi phục tệp dự án thất bại: ' . $e->getMessage());
            throw new AdminException('Khôi phục tệp dự án thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Rollback toàn bộ về phiên bản chỉ định
     * @param int $logId ID nhật ký nâng cấp
     * @return array
     */
    public function rollbackToVersion(int $logId): array
    {
        try {
            // Lấy bản ghi nâng cấp
            $logData = $this->dao->getOne(['id' => $logId]);
            if (!$logData) {
                throw new AdminException('Bản ghi nâng cấp không tồn tại');
            }

            $result = [
                'database_restored' => false,
                'project_restored' => false,
                'version_restored' => false,
                'message' => ''
            ];

            // 1. Khôi phục cơ sở dữ liệu
            if (!empty($logData['data_link'])) {
                $this->restoreDatabase($logData['data_link']);
                $result['database_restored'] = true;
            }

            // 2. Khôi phục file dự án
            if (!empty($logData['package_link'])) {
                $this->restoreProject($logData['package_link']);
                $result['project_restored'] = true;
            }

            // 3. Khôi phục số phiên bản
            $versionStr = sprintf(
                '%s.%s.%s.%s',
                $logData['first_version'] ?? '5',
                $logData['second_version'] ?? '5',
                $logData['third_version'] ?? '0',
                $logData['fourth_version'] ?? '0'
            );
            $versionCode = (int)($logData['first_version'] . $logData['second_version'] . $logData['third_version']);

            $versionManager = $this->getVersionManager();
            $versionManager->updateVersionFile('CRMEB-BZ v' . $versionStr, $versionCode);
            $result['version_restored'] = true;

            $result['message'] = 'Quay lại phiên bản thành công';
            Log::notice(['type' => 'version_rollback', 'log_id' => $logId, 'version' => $versionStr]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Quay lại phiên bản thất bại: ' . $e->getMessage());
            throw new AdminException('Quay lại phiên bản thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách phiên bản có thể rollback
     * @return array
     */
    public function getRollbackVersions(): array
    {
        $list = $this->dao->getList(['id', 'title', 'first_version', 'second_version', 'third_version', 'fourth_version', 'upgrade_time', 'package_link', 'data_link'], 1, 20);

        $rootPath = app()->getRootPath();
        $rollbackList = [];

        foreach ($list as $item) {
            $canRollback = true;
            $reason = [];

            // Kiểm tra file sao lưu có tồn tại không
            if (empty($item['package_link']) || !is_file($rootPath . 'backup' . DS . $item['package_link'])) {
                $canRollback = false;
                $reason[] = 'Tệp sao lưu dự án không tồn tại';
            }

            if (empty($item['data_link']) || !is_file($rootPath . 'backup' . DS . $item['data_link'])) {
                $canRollback = false;
                $reason[] = 'Tệp sao lưu cơ sở dữ liệu không tồn tại';
            }

            $rollbackList[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'version' => sprintf('v%s.%s.%s', $item['first_version'], $item['second_version'], $item['third_version']),
                'upgrade_time' => date('Y-m-d H:i:s', $item['upgrade_time']),
                'can_rollback' => $canRollback,
                'reason' => implode(', ', $reason)
            ];
        }

        return $rollbackList;
    }

    /**
     * Kiểm tra quyền truy cập
     * @param array $data
     * @return bool
     */
    public function checkAuth(array $data): bool
    {
        if (!isset($data['status']) || $data['status'] != 200) {
            if ($data['status'] == 'Vui lòng nhập tài khoản và mật khẩu') {
                $this->getAuth();
            }
            Log::error(['msg' => $data['msg'] ?? '', 'error' => $data]);
            return false;
        }
        return true;
    }

    /**
     * Trạng thái nâng cấp
     * @return array
     */
    public function getUpgradeStatus(): array
    {
        $this->getSign($this->timeStamp);
        $result = HttpService::getRequest(self::UPGRADE_STATUS_URL, $this->requestData, ['Access-Token: Bearer ' . CacheService::get('upgrade_auth_token')]);
        if (!$result) {
            throw new AdminException('Lấy trạng thái nâng cấp thất bại');
        }

        $data = json_decode($result, true);
        $this->checkAuth($data);

        if (!isset($data['data']['auth']) || !$data['data']['auth']) {
            throw new AdminException('Tên miền của bạn chưa được cấp phép, vui lòng đăng ký cấp phép trước');
        }

        $upgradeData['status'] = $data['data']['status'] ?? 0;
        $upgradeData['force_reminder'] = $data['data']['force_reminder'] ?? 0;
        $upgradeData['title'] = $upgradeData['status'] < 1 ? "Bạn đã nâng cấp lên phiên bản mới nhất, không cần cập nhật" : "Hệ thống có phiên bản mới để cập nhật";
        return $upgradeData;
    }

    /**
     * Thực hiện lại nâng cấp
     * @param $type
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/2/27
     */
    public function reExecute($type)
    {
        $token = CacheService::get('upgrade_token');
        switch ($type) {
            case 0:
                CacheService::set($token . '_check_md5_status', 0, 86400);
                break;
            case 1:
                CacheService::set($token . '_database_backup', 0, 86400);
                CacheService::set($token . '_project_backup', 0, 86400);
                break;
            case 2:
                CacheService::set($token . '_server_package', 0, 86400);
                CacheService::set($token . '_client_package', 0, 86400);
                CacheService::set($token . '_pc_package', 0, 86400);
                break;
            case 3:
                CacheService::set($token . '_coverage_project', 0, 86400);
                break;
            default:
                throw new AdminException('Tham số không hợp lệ');
        }
        return true;
    }

    /**
     * Lấy tiến độ tải xuống
     * Trả về trạng thái tiến độ tải xuống gói, sao lưu, giải nén
     * @param $type
     */
    public function getDownloadProgress($type)
    {
        // Nếu type không thuộc 0,1,2,3,4 thì trả về lỗi
        if (!in_array($type, [0, 1, 2, 3, 4])) {
            throw new AdminException('Tham số không hợp lệ');
        }

        // Lấy token nâng cấp
        $token = CacheService::get('upgrade_token');
        if (empty($token)) {
            return [
                'stage' => 'idle',
                'progress' => 0,
                'message' => 'Chưa bắt đầu tải xuống',
                'completed' => false,
                'can_upgrade' => false
            ];
        }

        // Giai đoạn 1: Kiểm tra file có thay đổi không
        if ($type == 0) {
            // Kiểm tra trạng thái MD5, 0 chưa kiểm tra, 1 đang kiểm tra, 2 kiểm tra thành công, 3 bỏ qua
            $checkMd5Status = (int)CacheService::get($token . '_check_md5_status', 0);
            // Kiểm tra dữ liệu khác biệt file MD5, rỗng là không có khác biệt, ngược lại là các file khác biệt
            $checkMd5File = (array)CacheService::get($token . '_check_md5_file', []);
            // Kiểm tra file có thay đổi không
            if ($checkMd5Status < 2) {
                // Khi trạng thái kiểm tra file là 0, bắt đầu kiểm tra
                if ($checkMd5Status == 0) {
                    // Đặt trạng thái kiểm tra là 1, bắt đầu kiểm tra
                    CacheService::set($token . '_check_md5_status', 1, 86400);
                    // Kích hoạt tác vụ kiểm tra
                    UpgradeJob::dispatch('checkFileMd5', [$token]);
                }
                // Khi trạng thái kiểm tra file là 1, chờ kiểm tra hoàn tất
                if (empty($checkMd5File)) {
                    return [
                        'stage' => 'loading',
                        'type' => 0,
                        'progress' => 0,
                        'message' => 'Đang kiểm tra tệp có thay đổi hay không...',
                        'completed' => false,
                        'can_upgrade' => false
                    ];
                } else {
                    return [
                        'stage' => 'error',
                        'type' => 0,
                        'progress' => 0,
                        'message' => 'Tệp đã bị thay đổi, vui lòng xác nhận có tiếp tục nâng cấp hay không...',
                        'completed' => false,
                        'can_upgrade' => false,
                        'data' => $checkMd5File
                    ];
                }
            } else {
                return [
                    'stage' => 'success',
                    'type' => 0,
                    'progress' => 0,
                    'message' => 'Kiểm tra tệp hoàn tất',
                    'completed' => false,
                    'can_upgrade' => false
                ];
            }
        }

        // Giai đoạn 2: Sao lưu
        if ($type == 1) {
            // Kiểm tra trạng thái sao lưu cơ sở dữ liệu, 0 chưa sao lưu, 1 đang sao lưu, 2 sao lưu thành công, 3 bỏ qua
            $databaseBackup = (int)CacheService::get($token . '_database_backup', 0);
            // Kiểm tra trạng thái sao lưu dự án, 0 chưa sao lưu, 1 đang sao lưu, 2 sao lưu thành công, 3 bỏ qua
            $projectBackup = (int)CacheService::get($token . '_project_backup', 0);
            // Kiểm tra sao lưu
            if ($databaseBackup < 2 || $projectBackup < 2) {
                // Khi trạng thái sao lưu cơ sở dữ liệu là 0, bắt đầu sao lưu
                if ($databaseBackup == 0) {
                    // Đặt trạng thái sao lưu cơ sở dữ liệu là 1, bắt đầu sao lưu
                    CacheService::set($token . '_database_backup', 1, 86400);
                    // Kích hoạt tác vụ sao lưu
                    UpgradeJob::dispatch('databaseBackup', [$token]);
                }
                // Khi trạng thái sao lưu dự án là 0, bắt đầu sao lưu
                if ($projectBackup == 0) {
                    // Đặt trạng thái sao lưu dự án là 1, bắt đầu sao lưu
                    CacheService::set($token . '_project_backup', 1, 86400);
                    // Kích hoạt tác vụ sao lưu
                    UpgradeJob::dispatch('projectBackup', [$token]);
                }
                return [
                    'stage' => 'loading',
                    'type' => 1,
                    'progress' => 33,
                    'message' => 'Đang sao lưu tệp và cơ sở dữ liệu...',
                    'completed' => false,
                    'can_upgrade' => false
                ];
            } else {
                // Khi trạng thái kiểm tra file là 2, kiểm tra file đã sao lưu thành công chưa, đường dẫn sao lưu là backup/600-1.project.zip và backup/600-1.sql.gz
                $backupPath = app()->getRootPath() . 'backup' . DS;
                $versionData = $this->getVersion();
                $projectBackupFile = $backupPath . $versionData['version_code'] . '-1.project.zip';
                $databaseBackupFile = $backupPath . $versionData['version_code'] . '-1.sql.gz';
                if (!is_file($projectBackupFile) || !is_file($databaseBackupFile)) {
                    return [
                        'stage' => 'error',
                        'type' => 1,
                        'progress' => 33,
                        'message' => 'Sao lưu hệ thống thất bại!',
                        'completed' => false,
                        'can_upgrade' => false
                    ];
                } else {
                    return [
                        'stage' => 'success',
                        'type' => 1,
                        'progress' => 33,
                        'message' => 'Sao lưu hệ thống thành công!',
                        'completed' => false,
                        'can_upgrade' => false
                    ];
                }
            }
        }

        // Giai đoạn 3: Tải gói cập nhật
        if ($type == 2) {
            // Kiểm tra trạng thái gói nâng cấp phía server, 0 chưa tải, 1 đang tải, 2 tải thành công, 3 bỏ qua
            $serverPackage = (int)CacheService::get($token . '_server_package', 0);
            // Kiểm tra trạng thái gói nâng cấp phía client, 0 chưa tải, 1 đang tải, 2 tải thành công, 3 bỏ qua
            $clientPackage = (int)CacheService::get($token . '_client_package', 0);
            // Kiểm tra trạng thái gói nâng cấp PC, 0 chưa tải, 1 đang tải, 2 tải thành công, 3 bỏ qua
            $pcPackage = (int)CacheService::get($token . '_pc_package', 0);
            // Kiểm tra việc tải gói nâng cấp
            if ($serverPackage < 2 || $clientPackage < 2 || $pcPackage < 2) {
                return [
                    'stage' => 'loading',
                    'type' => 2,
                    'progress' => 66,
                    'message' => 'Đang tải xuống gói nâng cấp...',
                    'completed' => false,
                    'can_upgrade' => false,
                    'detail' => [
                        'server' => $serverPackage,
                        'client' => $clientPackage,
                        'pc' => $pcPackage
                    ]
                ];
            } else {
                return [
                    'stage' => 'success',
                    'type' => 2,
                    'progress' => 66,
                    'message' => 'Tải xuống gói nâng cấp hoàn tất!',
                    'completed' => false,
                    'can_upgrade' => false
                ];
            }
        }

        // Giai đoạn 4: Ghi đè file nâng cấp cơ sở dữ liệu, tiến hành nâng cấp
        if ($type == 3) {
            $coverageProject = (int)CacheService::get($token . '_coverage_project', 0);
            if ($coverageProject < 2) {
                // Giải nén và ghi đè file cập nhật cơ sở dữ liệu
                if ($coverageProject == 0) {
                    // Đặt trạng thái giải nén ghi đè dự án là 1, bắt đầu giải nén ghi đè
                    CacheService::set($token . '_coverage_project', 1, 86400);
                    // Lấy thư mục chứa file đã tải về và giải nén
                    $serverPackagePath = CacheService::get($token . '_server_package_path');
                    // Kiểm tra trong thư mục $downloadFilePath có tồn tại thư mục config/ và upgrade/versions/ không, nếu có thì ghi đè hai thư mục này vào thư mục gốc dự án
                    $configPath = $serverPackagePath . DS . 'config';
                    $versionsPath = $serverPackagePath . DS . 'upgrade' . DS . 'versions';
                    if (is_dir($configPath) && is_dir($versionsPath)) {
                        /** @var FileService $fileService */
                        $fileService = app()->make(FileService::class);
                        // Sao chép thư mục config
                        $res = $fileService->copyDir($configPath, app()->getRootPath() . 'config');
                        // Sao chép thư mục upgrade/versions
                        $res = $res && $fileService->copyDir($versionsPath, app()->getRootPath() . 'upgrade' . DS . 'versions');
                        // Ghi đè thành công
                        if ($res) {
                            // Đặt trạng thái giải nén ghi đè dự án là 2, ghi đè thành công
                            CacheService::set($token . '_coverage_project', 2, 86400);
                            return [
                                'stage' => 'loading',
                                'type' => 3,
                                'progress' => 100,
                                'message' => 'Đã ghi đè xong tệp nâng cấp cơ sở dữ liệu, bắt đầu thực hiện nâng cấp...',
                                'completed' => true,
                                'can_upgrade' => true,
                            ];
                        }
                    }
                }
            } else {
                // Thực thi tất cả các bản nâng cấp vượt phiên bản
                $data = $this->executeAllCrossVersionUpgrade();
                // Thực thi thành công
                return [
                    'stage' => 'success',
                    'type' => 3,
                    'progress' => 100,
                    'message' => 'Cập nhật cơ sở dữ liệu hoàn tất',
                    'completed' => true,
                    'can_upgrade' => true,
                    'data' => $data
                ];
            }
        }

        // Giai đoạn 5: Ghi đè file dự án
        if ($type == 4) {
            // Ghi đè file dự án
            $this->coverageProject($token);
            return [
                'stage' => 'complete',
                'type' => 4,
                'progress' => 100,
                'message' => 'Cập nhật toàn bộ hoàn tất',
                'completed' => true,
                'can_upgrade' => true,
                'routine_upload_data' => CacheService::get('routine_upload_data', [])
            ];
        }

    }

    /**
     * Log nâng cấp
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUpgradeLogList(): array
    {
        [$page, $limit] = $this->getPageValue();
        $count = $this->dao->count();
        $list = $this->dao->getList(['id', 'title', 'content', 'first_version', 'second_version', 'third_version', 'fourth_version', 'upgrade_time', 'package_link', 'data_link'], $page, $limit);
        $rootPath = app()->getRootPath();
        foreach ($list as &$item) {
            $item['file_status'] = 0;
            $item['data_status'] = 0;
            if ($item['package_link'] && is_file($rootPath . 'backup' . DS . $item['package_link'])) {
                $item['package_link'] = 'backup/' . $item['package_link'];
                $item['file_status'] = 1;
            }
            if ($item['data_link'] && is_file($rootPath . 'backup' . DS . $item['data_link'])) {
                $item['data_link'] = 'backup/' . $item['data_link'];
                $item['data_status'] = 1;
            }
            $item['upgrade_time'] = date('Y-m-d H:i:s', $item['upgrade_time']);
        }
        return compact('list', 'count');
    }

    /**
     * Xuất
     * @param int $id
     * @param string $type
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function export(int $id, string $type)
    {
        $data = $this->dao->getOne(['id' => $id], 'package_link, data_link');
        if (!$data || !$data['package_link']) {
            throw new AdminException('Tệp sao lưu không tồn tại');
        }

        $fileName = $type == 'file' ? $data['package_link'] : $data['data_link'];
        $filePath = app()->getRootPath() . 'backup' . DS . $fileName;
        if (!is_file($filePath)) {
            throw new AdminException('Tệp sao lưu không tồn tại');
        }

        //Tải xuống tệp
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();
        readfile($filePath); //Xuất file
    }

    /**
     * Kiểm tra dung lượng cơ sở dữ liệu
     * @return bool
     */
    public function checkDatabaseSize(): bool
    {
        if (!$database = Config::get('database.connections.' . Config::get('database.default') . '.database')) {
            throw new AdminException('Lấy thông tin cơ sở dữ liệu thất bại');
        }

        $result = Db::query("select concat(round(sum(data_length/1024/1024))) as size from information_schema.tables where table_schema='{$database}';");
        if ((int)($result[0]['size'] ?? '') > 500) {
            throw new AdminException('Tệp cơ sở dữ liệu quá lớn, không thể nâng cấp');
        }
        return true;
    }

    // ==================== Các phương thức liên quan đến nâng cấp vượt phiên bản ====================

    /**
     * Lấy instance của trình quản lý phiên bản
     * @return \upgrade\VersionManager
     */
    protected function getVersionManager()
    {
        // Nạp thủ công class VersionManager (tránh sửa composer.json)
        $file = app()->getRootPath() . 'upgrade' . DIRECTORY_SEPARATOR . 'VersionManager.php';
        if (!class_exists('\\upgrade\\VersionManager') && file_exists($file)) {
            require_once $file;
        }
        return new \upgrade\VersionManager();
    }

    /**
     * Lấy tổng quan nâng cấp vượt phiên bản
     * @return array
     */
    public function getCrossVersionUpgradeOverview(): array
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->getUpgradeOverview();
    }

    /**
     * Lấy danh sách phiên bản chờ nâng cấp
     * @return array
     */
    public function getPendingVersions(): array
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->getPendingVersions();
    }

    /**
     * Lấy tất cả SQL nâng cấp chờ thực thi
     * @return array
     */
    public function getAllPendingUpgradeSql(): array
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->getAllPendingUpgradeSql();
    }

    /**
     * Thực hiện nâng cấp vượt phiên bản
     * @param int $step Bước đang thực thi hiện tại
     * @return array ['success' => bool, 'step' => int, 'total' => int, 'message' => string, 'completed' => bool]
     */
    public function executeCrossVersionUpgrade(int $step = 0): array
    {
        $versionManager = $this->getVersionManager();
        $allSql = $versionManager->getAllPendingUpgradeSql();
        $total = count($allSql);

        // Kiểm tra đã hoàn tất toàn bộ nâng cấp chưa
        if ($step >= $total) {
            // Lấy thông tin phiên bản trước khi nâng cấp
            $beforeVersion = CacheService::get('cross_version_before_version', []);
            $pendingVersions = $versionManager->getPendingVersions();

            // Cập nhật file phiên bản thành phiên bản mới nhất
            $latestVersion = $versionManager->getLatestVersion();
            if (!empty($latestVersion)) {
                $versionManager->updateVersionFile($latestVersion['version'], $latestVersion['code']);
            }

            // Lưu nhật ký nâng cấp
            $token = CacheService::get('cross_version_upgrade_token', '');
            if ($token) {
                $this->saveCrossVersionUpgradeLog($token, $beforeVersion, $latestVersion, $pendingVersions);
            }

            return [
                'success' => true,
                'step' => $step,
                'total' => $total,
                'message' => 'Nâng cấp hoàn tất',
                'completed' => true,
                'current_version' => $latestVersion['version'] ?? ''
            ];
        }

        // Với bước đầu tiên, thực hiện sao lưu
        if ($step == 0) {
            // Ghi lại thông tin phiên bản trước khi nâng cấp
            $beforeVersion = $versionManager->getCurrentVersion();
            CacheService::set('cross_version_before_version', $beforeVersion, 86400);

            $token = md5(time() . uniqid());
            CacheService::set('cross_version_upgrade_token', $token, 86400);

            $backupResult = $this->performBackup($token);
            if (!$backupResult) {
                return [
                    'success' => false,
                    'step' => $step,
                    'total' => $total,
                    'message' => 'Sao lưu thất bại, không thể tiếp tục nâng cấp',
                    'completed' => false
                ];
            }
        }

        // Thực thi SQL của bước hiện tại
        $sqlItem = $allSql[$step];
        $result = $versionManager->executeSqlItem($sqlItem);

        $message = $result['message'] ?? '';
        if (isset($sqlItem['version'])) {
            $message = "[{$sqlItem['version']}] " . $message;
        }

        return [
            'success' => $result['success'],
            'step' => $step + 1,
            'total' => $total,
            'message' => $message,
            'completed' => false,
            'skipped' => $result['skipped'] ?? false,
            'sql_info' => [
                'table' => $sqlItem['table'] ?? '',
                'field' => $sqlItem['field'] ?? '',
                'type' => $sqlItem['type'] ?? 0,
                'version' => $sqlItem['version'] ?? ''
            ]
        ];
    }

    /**
     * Thực thi toàn bộ nâng cấp vượt phiên bản bằng một cú nhấp
     * @return array
     */
    public function executeAllCrossVersionUpgrade(): array
    {
        $versionManager = $this->getVersionManager();
        $pendingVersions = $versionManager->getPendingVersions();

        if (empty($pendingVersions)) {
            return [
                'success' => true,
                'message' => 'Đang là phiên bản mới nhất, không cần nâng cấp',
                'executed' => 0,
                'skipped' => 0,
                'failed' => 0,
                'sql_logs' => []
            ];
        }

        // Ghi lại thông tin phiên bản trước khi nâng cấp
        $beforeVersion = $versionManager->getCurrentVersion();

        // Sao lưu trước khi thực hiện nâng cấp vượt phiên bản
        $token = CacheService::get('upgrade_token');
        CacheService::set('cross_version_upgrade_token', $token, 86400);

        // Khởi tạo trạng thái tiến độ
        CacheService::set($token . '_sql_progress', ['current' => 0, 'total' => 0], 86400);
        CacheService::set($token . '_sql_logs', [], 86400);
        CacheService::set($token . '_upgrade_complete', 0, 86400);

        $executed = 0;
        $skipped = 0;
        $failed = 0;
        $failedMessages = [];
        $migrationResults = [];
        $sqlLogs = [];

        // Đếm tổng số câu SQL
        $totalSql = 0;
        foreach ($pendingVersions as $version) {
            $upgradeData = $versionManager->getVersionUpgradeData($version);
            if (!empty($upgradeData['update_sql'])) {
                $totalSql += count($upgradeData['update_sql']);
            }
        }
        $this->updateSqlProgress($token, 0, $totalSql);

        $currentSql = 0;

        // Duyệt qua từng phiên bản
        foreach ($pendingVersions as $version) {
            $upgradeData = $versionManager->getVersionUpgradeData($version);

            // Thực thi nâng cấp SQL
            if (!empty($upgradeData['update_sql'])) {
                foreach ($upgradeData['update_sql'] as $sqlItem) {
                    $currentSql++;
                    $result = $versionManager->executeSqlItem($sqlItem);

                    // Ghi log thực thi SQL
                    $logEntry = [
                        'version' => $version['version'],
                        'table' => $sqlItem['table'] ?? '-',
                        'field' => $sqlItem['field'] ?? '',
                        'type' => $sqlItem['type'] ?? 0,
                        'status' => $result['success'] ? (isset($result['skipped']) && $result['skipped'] ? 'skipped' : 'success') : 'failed',
                        'message' => $result['message'] ?? ''
                    ];
                    $sqlLogs[] = $logEntry;
                    $this->addSqlLog($token, $logEntry);

                    if ($result['success']) {
                        if (isset($result['skipped']) && $result['skipped']) {
                            $skipped++;
                        } else {
                            $executed++;
                        }
                    } else {
                        $failed++;
                        $failedMessages[] = "[{$version['version']}] " . $result['message'];
                    }

                    // Cập nhật tiến độ
                    $this->updateSqlProgress($token, $currentSql, $totalSql);
                }
            }

            // Thực thi handler migration dữ liệu
            if (!empty($upgradeData['data_handlers']) && $failed == 0) {
                /** @var DataMigrationServices $migrationServices */
                $migrationServices = app()->make(DataMigrationServices::class);
                $migrationResult = $migrationServices->executeAllHandlers($upgradeData['data_handlers']);
                $migrationResults[$version['version']] = $migrationResult;

                if (!$migrationResult['success']) {
                    $failed++;
                    $failedMessages[] = "[{$version['version']}] Di chuyển dữ liệu thất bại";
                }
            }

            // Cập nhật file phiên bản sau khi nâng cấp xong mỗi phiên bản
            $versionManager->updateVersionFile($version['version'], $version['code']);
            Log::notice(['type' => 'cross_version_upgrade', 'version' => $version['version'], 'code' => $version['code']]);
        }

        // Đánh dấu nâng cấp hoàn tất
        $this->markUpgradeComplete($token);

        // Cuối cùng lấy thông tin phiên bản mới nhất
        $latestVersion = $versionManager->getLatestVersion();

        // Lưu nhật ký nâng cấp sau khi nâng cấp thành công
        if ($failed == 0) {
            $this->saveCrossVersionUpgradeLog($token, $beforeVersion, $latestVersion, $pendingVersions);
        }

        return [
            'success' => $failed == 0,
            'message' => $failed == 0 ? 'Nâng cấp thành công' : 'Một số câu lệnh SQL thực thi thất bại',
            'executed' => $executed,
            'skipped' => $skipped,
            'failed' => $failed,
            'total' => $totalSql,
            'failed_messages' => $failedMessages,
            'migration_results' => $migrationResults,
            'current_version' => $latestVersion['version'] ?? '',
            'sql_logs' => $sqlLogs
        ];
    }

    /**
     * Thực hiện sao lưu
     * @param string $token
     * @return bool
     */
    protected function performBackup(string $token): bool
    {
        try {
            // Thực hiện sao lưu cơ sở dữ liệu
            CacheService::set($token . '_database_backup', 1, 86400);
            $this->databaseBackup($token);

            // Chờ sao lưu cơ sở dữ liệu hoàn tất
            $maxWait = 30; // Chờ tối đa 30 giây
            $waited = 0;
            while ($waited < $maxWait) {
                if (CacheService::get($token . '_database_backup') == 2) {
                    break;
                }
                sleep(1);
                $waited++;
            }

            if (CacheService::get($token . '_database_backup') != 2) {
                throw new AdminException('Sao lưu cơ sở dữ liệu quá thời gian chờ');
            }

            // Thực hiện sao lưu dự án
            CacheService::set($token . '_project_backup', 1, 86400);
            $this->projectBackup($token);

            // Chờ sao lưu dự án hoàn tất
            $waited = 0;
            while ($waited < $maxWait) {
                if (CacheService::get($token . '_project_backup') == 2) {
                    break;
                }
                sleep(1);
                $waited++;
            }

            if (CacheService::get($token . '_project_backup') != 2) {
                throw new AdminException('Sao lưu dự án quá thời gian chờ');
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Thực hiện sao lưu thất bại: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy trạng thái sao lưu
     * @return array
     */
    public function getBackupStatus(): array
    {
        $token = CacheService::get('upgrade_token');
        if (empty($token)) {
            return [
                'database_backup' => 0,
                'project_backup' => 0,
                'message' => 'Chưa bắt đầu sao lưu'
            ];
        }

        $databaseBackup = CacheService::get($token . '_database_backup');
        $projectBackup = CacheService::get($token . '_project_backup');

        return [
            'database_backup' => $databaseBackup ?? 0,
            'project_backup' => $projectBackup ?? 0,
            'message' => $this->getBackupMessage($databaseBackup, $projectBackup)
        ];
    }

    /**
     * Lấy thông tin trạng thái sao lưu
     * @param int $databaseBackup
     * @param int $projectBackup
     * @return string
     */
    private function getBackupMessage(int $databaseBackup, int $projectBackup): string
    {
        if ($databaseBackup == 2 && $projectBackup == 2) {
            return 'Sao lưu hoàn tất';
        } elseif ($databaseBackup == 1 || $projectBackup == 1) {
            return 'Đang sao lưu';
        } elseif ($databaseBackup == 0 && $projectBackup == 0) {
            return 'Chưa bắt đầu sao lưu';
        } else {
            return 'Sao lưu gặp lỗi';
        }
    }

    /**
     * Lấy chi tiết tiến độ nâng cấp
     * @return array
     */
    public function getUpgradeProgressDetail(): array
    {
        $token = CacheService::get('cross_version_upgrade_token') ?: CacheService::get('upgrade_token');

        if (empty($token)) {
            return [
                'step' => 0,
                'progress' => 0,
                'step_details' => [
                    'database' => 'Chưa bắt đầu',
                    'project' => 'Chưa bắt đầu',
                    'sql' => 'Chưa bắt đầu',
                    'complete' => 'Chưa bắt đầu',
                ],
                'sql_logs' => [],
            ];
        }

        // Lấy trạng thái của từng bước
        $databaseBackup = CacheService::get($token . '_database_backup', 0);
        $projectBackup = CacheService::get($token . '_project_backup', 0);
        $sqlProgress = CacheService::get($token . '_sql_progress', ['current' => 0, 'total' => 0]);
        $sqlLogs = CacheService::get($token . '_sql_logs', []);
        $upgradeComplete = CacheService::get($token . '_upgrade_complete', 0);

        // Tính bước hiện tại và tiến độ
        $step = 0;
        $progress = 0;
        $stepDetails = [
            'database' => 'Đang chờ...',
            'project' => 'Đang chờ...',
            'sql' => 'Đang chờ...',
            'complete' => 'Đang chờ...',
        ];

        // Bước 1: Sao lưu cơ sở dữ liệu
        if ($databaseBackup == 1) {
            $step = 0;
            $progress = 10;
            $stepDetails['database'] = 'Đang sao lưu cơ sở dữ liệu...';
        } elseif ($databaseBackup == 2) {
            $step = 1;
            $progress = 25;
            $stepDetails['database'] = 'Sao lưu cơ sở dữ liệu thành công ✓';
        }

        // Bước 2: Sao lưu file dự án
        if ($databaseBackup == 2) {
            if ($projectBackup == 1) {
                $step = 1;
                $progress = 35;
                $stepDetails['project'] = 'Đang sao lưu tệp dự án...';
            } elseif ($projectBackup == 2) {
                $step = 2;
                $progress = 50;
                $stepDetails['project'] = 'Sao lưu tệp dự án thành công ✓';
            }
        }

        // Bước 3: Thực thi SQL
        if ($databaseBackup == 2 && $projectBackup == 2) {
            $current = $sqlProgress['current'] ?? 0;
            $total = $sqlProgress['total'] ?? 0;

            if ($total > 0) {
                $sqlPercent = ($current / $total) * 100;
                $progress = 50 + ($sqlPercent * 0.4); // 50-90
                $stepDetails['sql'] = "Đang thực thi: {$current}/{$total}";

                if ($current >= $total) {
                    $step = 3;
                    $progress = 90;
                    $failedCount = count(array_filter($sqlLogs, fn($log) => $log['status'] === 'failed'));
                    $stepDetails['sql'] = $failedCount > 0
                        ? "Thực thi SQL hoàn tất ({$failedCount} mục thất bại)"
                        : 'Thực thi SQL hoàn tất ✓';
                }
            } else {
                $stepDetails['sql'] = 'Chờ thực thi...';
            }
        }

        // Bước 4: Hoàn tất
        if ($upgradeComplete == 2) {
            $step = 4;
            $progress = 100;
            $stepDetails['complete'] = 'Nâng cấp hoàn tất ✓';
        }

        return [
            'step' => $step,
            'progress' => (int)$progress,
            'step_details' => $stepDetails,
            'sql_logs' => array_slice($sqlLogs, -50), // Trả về tối đa 50 bản ghi
        ];
    }

    /**
     * Cập nhật tiến độ thực thi SQL
     * @param string $token
     * @param int $current
     * @param int $total
     * @return void
     */
    protected function updateSqlProgress(string $token, int $current, int $total): void
    {
        CacheService::set($token . '_sql_progress', ['current' => $current, 'total' => $total], 86400);
    }

    /**
     * Thêm log thực thi SQL
     * @param string $token
     * @param array $log
     * @return void
     */
    protected function addSqlLog(string $token, array $log): void
    {
        $logs = CacheService::get($token . '_sql_logs', []);
        $logs[] = $log;
        CacheService::set($token . '_sql_logs', $logs, 86400);
    }

    /**
     * Đánh dấu nâng cấp hoàn tất
     * @param string $token
     * @return void
     */
    protected function markUpgradeComplete(string $token): void
    {
        CacheService::set($token . '_upgrade_complete', 2, 86400);
    }

    /**
     * Kiểm tra có cần nâng cấp vượt phiên bản không
     * @return bool
     */
    public function needCrossVersionUpgrade(): bool
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->needUpgrade();
    }

    /**
     * Lấy khoảng cách phiên bản
     * @return int
     */
    public function getVersionGap(): int
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->getVersionGap();
    }

    /**
     * Kiểm tra tính khả dụng của nâng cấp vượt phiên bản
     * Kiểm tra phiên bản hiện tại có đáp ứng yêu cầu phiên bản tối thiểu không
     * @return array
     */
    public function checkCrossVersionUpgradeAvailability(): array
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->checkUpgradeAvailability();
    }

    /**
     * Phiên bản hiện tại có đáp ứng yêu cầu phiên bản tối thiểu không
     * @return bool
     */
    public function meetsMinVersionRequirement(): bool
    {
        $versionManager = $this->getVersionManager();
        return $versionManager->meetsMinVersionRequirement();
    }

    /**
     * Lưu nhật ký nâng cấp vượt phiên bản
     * @param string $token Token nâng cấp
     * @param array $beforeVersion Thông tin phiên bản trước khi nâng cấp
     * @param array $latestVersion Thông tin phiên bản sau khi nâng cấp
     * @param array $pendingVersions Danh sách các phiên bản được nâng cấp
     * @return bool
     */
    protected function saveCrossVersionUpgradeLog(string $token, array $beforeVersion, array $latestVersion, array $pendingVersions): bool
    {
        try {
            // Phân tích số phiên bản trước khi nâng cấp
            $beforeVersionStr = $beforeVersion['version'] ?? '';
            // Phân tích số phiên bản sau khi nâng cấp
            $afterVersionStr = $latestVersion['version'] ?? '';
            $afterVersionParts = $this->parseVersionString($afterVersionStr);

            // Lấy tên file sao lưu
            $packageLink = CacheService::get($token . '_project_backup_name', '');
            $dataLink = CacheService::get($token . '_database_backup_name', '');
            $updateContent = CacheService::get('routine_upload_data', [])['desc'] ?? 'Chưa có';
            // Lưu vào cơ sở dữ liệu
            $this->dao->save([
                'title' => 'Nâng cấp ' . $afterVersionStr . ' hoàn tất',
                'content' => 'Nâng cấp phiên bản: ' . $beforeVersionStr . ' -> ' . $afterVersionStr . '; nội dung cập nhật:' . $updateContent . '；',
                'first_version' => $afterVersionParts['first'] ?? '6',
                'second_version' => $afterVersionParts['second'] ?? '0',
                'third_version' => $afterVersionParts['third'] ?? '0',
                'fourth_version' => $afterVersionParts['fourth'] ?? '0',
                'upgrade_time' => time(),
                'error_data' => '',
                'package_link' => $packageLink,
                'data_link' => $dataLink
            ]);

            Log::notice(['type' => 'cross_version_upgrade_log', 'before' => $beforeVersionStr, 'after' => $afterVersionStr, 'token' => $token]);
            return true;
        } catch (\Exception $e) {
            Log::error('Lưu log nâng cấp vượt phiên bản thất bại: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Phân tích chuỗi phiên bản
     * @param string $versionStr Ví dụ "CRMEB-BZ v5.6.4"
     * @return array
     */
    protected function parseVersionString(string $versionStr): array
    {
        $result = ['first' => '5', 'second' => '5', 'third' => '0', 'fourth' => '0'];

        // Khớp số phiên bản như v5.6.4 hoặc 5.6.4
        if (preg_match('/v?(\d+)\.(\d+)\.(\d+)(?:\.(\d+))?/i', $versionStr, $matches)) {
            $result['first'] = $matches[1] ?? '5';
            $result['second'] = $matches[2] ?? '5';
            $result['third'] = $matches[3] ?? '0';
            $result['fourth'] = $matches[4] ?? '0';
        }

        return $result;
    }
}
