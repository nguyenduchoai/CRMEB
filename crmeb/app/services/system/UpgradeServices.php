<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\services\system;

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
//        $versionData = $this->getVersion();
//        if ($versionData['version_code'] < 450) return true;
//        if (empty($versionData)) {
//            throw new AdminException('Mất thông tin ủy quyền');
//        }
//
//        $this->timeStamp = time();
//        $recVersion = $this->recombinationVersion($versionData['version'] ?? '');
//        $this->dao = $dao;
//
//        $this->requestData = [
//            'nonce' => mt_rand(111, 999),
//            'host' => app()->request->host(),
//            'timestamp' => $this->timeStamp,
//            'app_id' => trim($versionData['app_id'] ?? ''),
//            'app_key' => trim($versionData['app_key'] ?? ''),
//            'version' => implode('.', $recVersion)
//        ];
//
//        if (!CacheService::get('upgrade_auth_token')) {
//            $this->getAuth();
//        }
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
            (!isset($data['app_key']) || !$data['app_key'])) {
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


        //Kiểm tra chữ ký dự án
        $this->checkSignature();

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

        CacheService::set($token . '_database_backup', 1, 86400);
        UpgradeJob::dispatch('databaseBackup', [$token]);

        CacheService::set($token . '_project_backup', 1, 86400);
        UpgradeJob::dispatch('projectBackup', [$token]);

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
        $fp_output = fopen($downloadPath . DS . $fileName, 'w');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FILE, $fp_output);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (stripos($url, "https://") !== FALSE) curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_exec($ch);
        curl_close($ch);

        if (strpos($fileName, 'zip') === false) {
            throw new AdminException('Định dạng gói cài đặt không hợp lệ');
        }

        /** @var FileService $fileService */
        $fileService = app()->make(FileService::class);
        $downloadFilePath = $downloadPath . DS . substr($fileName, 0, strpos($fileName, 'zip') - 1);
        if (!$fileService->extractFile($downloadPath . DS . $fileName, $downloadFilePath)) {
            throw new AdminException('Giải nén gói nâng cấp thất bại');
        }

        CacheService::set($seq . '_path', $downloadFilePath, 86400);
        CacheService::set($seq . '_name', $downloadPath . DS . $fileName, 86400);
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
        $filePath = app()->getRootPath() . 'upgrade' . DS . date('Y-m-d');;
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

            $version = str_replace('.', '', $this->requestData['version']);
            $backServices->getDbBackup()->setFile(['name' => date("YmdHis") . '_' . $version, 'part' => 1]);
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

            $projectPath = $this->getProjectDir($appPath);
            if (empty($projectPath)) {
                throw new AdminException('Lỗi khi lấy thư mục dự án');
            }

            foreach ($projectPath as $key => $path) {
                foreach ($path as $item) {
                    if ($key == 'file') {
                        $fileService->handleFile($appPath . $item, $backupDir . DS . $item, 'copy', false, ['zip']);
                    } else {
                        $fileService->handleDir($appPath . $item, $backupDir . DS . $item, 'copy', false, ['uploads']);
                    }
                }
            }

            $version = str_replace('.', '', $this->requestData['version']);
            $fileName = date("YmdHis") . '_' . $version . '_project' . '.zip';
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

            return true;
        } catch (\Exception $e) {
            Log::error('Nâng cấp thất bại, lý do:' . $e->getMessage());
            CacheService::set($token . 'upgrade_status', -1, 86400);
            CacheService::set($token . 'upgrade_status_tip', 'Nâng cấp thất bại, lý do:' . $e->getMessage(), 86400);
        }
        return false;
    }

    /**
     * Lấy thư mục dự án
     * @param $path
     * @return array
     */
    public function getProjectDir($path): array
    {
        /** @var FileService $fileService */
        $fileService = app()->make(FileService::class);
        $list = $fileService->getDirs($path);
        $ignore = ['.', '..', '.git', '.idea', 'runtime', 'backup', 'upgrade'];
        foreach ($list as $key => $path) {
            if (empty($key)) {
                unset($list[$key]);
                continue;
            }
            if (is_array($path)) {
                foreach ($path as $key2 => $item) {
                    if (in_array($item, $ignore) && $item) {
                        unset($list[$key][$key2]);
                    }
                }
            }
        }
        return $list;
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
     * Kiểm tra chữ ký
     * @return void
     * @throws \Exception
     */
    public function checkSignature()
    {
        $projectSignature = rtrim($this->getVersion('project_signature'));
        if (!$projectSignature) {
            throw new AdminException('Lỗi khi lấy chữ ký dự án');
        }

        /** @var fileVerification $verification */
        $verification = app()->make(fileVerification::class);
        $newSignature = $verification->getSignature(app()->getRootPath());
        if ($projectSignature != $newSignature) {
            throw new AdminException('Lỗi khi đối chiếu chữ ký dự án');
        }
    }

    /**
     * Tạo chữ ký
     * @return void
     * @throws \Exception
     */
    public function generateSignature()
    {
        $file = app()->getRootPath() . '.version';
        if (!$data = @file($file)) {
            throw new AdminException('Đọc tệp .version thất bại');
        }
        $list = [];
        if (!empty($data)) {
            foreach ($data as $datum) {
                list($name, $value) = explode('=', $datum);
                $list[$name] = rtrim($value);
            }
        }

        if (!isset($list['project_signature'])) {
            $list['project_signature'] = '';
        }

        /** @var fileVerification $verification */
        $verification = app()->make(fileVerification::class);
        $list['project_signature'] = $verification->getSignature(app()->getRootPath());

        $str = "";
        foreach ($list as $key => $item) {
            $str .= "{$key}={$item}\n";
        }

        file_put_contents($file, $str);
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
                        if (in_array($item['type'], [1, 3, 6])) {
                            throw new AdminException($table . $tip[$item['type']] ?? 'Lỗi không xác định');
                        }
                    } else {
                        if (in_array($item['type'], [4, 5, 7])) {
                            throw new AdminException($table . $tip[$item['type']] ?? 'Lỗi không xác định');
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
                            throw new AdminException($table . $tip[$item['type']] ?? 'Lỗi không xác định');
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

        //Tạo chữ ký dự án
        $this->generateSignature();

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
     * Kiểm tra quyền truy cập
     * @param array $data
     * @return bool
     */
    public function checkAuth(array $data): bool
    {
        if (!isset($data['status']) || $data['status'] != 200) {
            if ($data['status'] == 410000) {
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

        $upgradeData['status'] = $data['data']['status'] ?? 0;
        $upgradeData['force_reminder'] = $data['data']['force_reminder'] ?? 0;
        $upgradeData['title'] = $upgradeData['status'] < 1 ? "Bạn đã nâng cấp lên phiên bản mới nhất, không cần cập nhật" : "Hệ thống có phiên bản mới để cập nhật";
        return $upgradeData;
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
            $item['file_status'] = 1;
            $item['data_status'] = 1;
            if (!$item['package_link'] || !is_file($rootPath . 'backup' . DS . $item['package_link'])) {
                $item['file_status'] = 0;
            }

            if (!$item['data_link'] || !is_file($rootPath . 'backup' . DS . $item['data_link'])) {
                $item['data_status'] = 0;
            }
            unset($item['package_link'], $item['data_link']);
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
}
