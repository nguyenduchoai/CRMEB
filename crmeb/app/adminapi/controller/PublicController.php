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

namespace app\adminapi\controller;


use app\Request;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\system\SystemRouteServices;
use crmeb\services\CacheService;
use think\Response;
use think\facade\Db;

class PublicController
{

    /**
     * Tải xuống tệp
     * @param string $key
     * @return Response|\think\response\File
     */
    public function download(Request $request, string $key = '')
    {
        if ($key == '') {
            $key = $request->getMore([
                ['key', ''],
            ], true);
        }
        if (!$key) {
            return Response::create()->code(500);
        }
        $fileName = CacheService::get($key);
        if (is_array($fileName) && isset($fileName['path']) && isset($fileName['fileName']) && $fileName['path'] && $fileName['fileName'] && file_exists($fileName['path'])) {
            CacheService::delete($key);
            return download($fileName['path'], $fileName['fileName']);
        }
        return Response::create()->code(500);
    }

    /**
     * Lấy tên miền request của workerman
     * @return mixed
     */
    public function getWorkerManUrl()
    {
        return app('json')->success(getWorkerManUrl());
    }

    /**
     * Quét mã để tải lên
     * @param Request $request
     * @param int $upload_type
     * @param int $type
     * @return Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/06/13
     */
    public function scanUpload(Request $request, $upload_type = 0, $type = 0)
    {
        [$file, $uploadToken, $pid] = $request->postMore([
            ['file', 'file'],
            ['uploadToken', ''],
            ['pid', 0]
        ], true);
        $service = app()->make(SystemAttachmentServices::class);
        if (CacheService::get('scan_upload') != $uploadToken) {
            return app('json')->fail('Cấu hình đã thay đổi hoặc token đã hết hạn');
        }
        $service->upload((int)$pid, $file, $upload_type, $type, '', $uploadToken);
        return app('json')->success('Tải lên thành công');
    }

    public function import(Request $request)
    {
        $filePath = $request->param('file_path', '');
        if (empty($filePath)) {
            return app('json')->fail('Tệp không tồn tại');
        }
        app()->make(SystemRouteServices::class)->import($filePath);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Thông tin máy chủ
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/9/24
     */
    public function getSystemInfo()
    {
        $info['server'] = [
            ['name' => 'Hệ điều hành máy chủ', 'require' => 'Tương tự UNIX', 'value' => PHP_OS],
            ['name' => 'Môi trường WEB', 'require' => 'Apache/Nginx/IIS', 'value' => $_SERVER['SERVER_SOFTWARE']],
        ];
        $gd_info = function_exists('gd_info') ? gd_info() : array();
        $info['environment'] = [
            ['name' => 'Phiên bản PHP', 'require' => '7.1-7.4', 'value' => phpversion()],
            ['name' => 'Phiên bản MySql', 'require' => '5.6-8.0', 'value' => Db::query("SELECT VERSION()")[0]['VERSION()']],
            ['name' => 'MySqli', 'require' => 'Bật', 'value' => function_exists('mysqli_connect')],
            ['name' => 'Openssl', 'require' => 'Bật', 'value' => function_exists('openssl_encrypt')],
            ['name' => 'Session', 'require' => 'Bật', 'value' => function_exists('session_start')],
            ['name' => 'Safe_Mode', 'require' => 'Bật', 'value' => !ini_get('safe_mode')],
            ['name' => 'GD', 'require' => 'Bật', 'value' => !empty($gd_info['GD Version'])],
            ['name' => 'Curl', 'require' => 'Bật', 'value' => function_exists('curl_init')],
            ['name' => 'Bcmath', 'require' => 'Bật', 'value' => function_exists('bcadd')],
            ['name' => 'Upload', 'require' => 'Bật', 'value' => (bool)ini_get('file_uploads')],
        ];

        $info['permissions'] = [
            ['name' => 'backup', 'require' => 'Đọc/ghi', 'value' => is_readable(root_path('backup')) && is_writable(root_path('backup'))],
            ['name' => 'public', 'require' => 'Đọc/ghi', 'value' => is_readable(root_path('public')) && is_writable(root_path('public'))],
            ['name' => 'runtime', 'require' => 'Đọc/ghi', 'value' => is_readable(root_path('runtime')) && is_writable(root_path('runtime'))],
            ['name' => '.env', 'require' => 'Đọc/ghi', 'value' => is_readable(root_path() . '.env') && is_writable(root_path() . '.env')],
            ['name' => '.version', 'require' => 'Đọc/ghi', 'value' => is_readable(root_path() . '.version') && is_writable(root_path() . '.version')],
            ['name' => '.constant', 'require' => 'Đọc/ghi', 'value' => is_readable(root_path() . '.constant') && is_writable(root_path() . '.constant')],
        ];

        if (function_exists('exec')) {
            $workermanOutput = $timerOutput = $queueOutput = [];
            // exec("ps aux | grep 'php think workerman' | grep -v grep", $workermanOutput);
            $targetPort = config('workerman.chat.port');
            $thinkPath = root_path(); // Đường dẫn tuyệt đối của file think
            $checkService = function($service) {
                if($service === 'queue'){
                    $command = 'queue:listen'; 
                    // Chạy lệnh ps để tìm tiến trình hàng đợi
                    exec("ps aux | grep '{$command}' | grep -v grep", $output);
                    
                    // Nếu output không rỗng, nghĩa là tiến trình đang tồn tại
                    return !empty($output);
                } else {
                    $pidFile = root_path('runtime') . $service . '.pid';
                    // Ưu tiên kiểm tra file PID
                    if (!file_exists($pidFile)) {
                        return false;
                    }
                    
                    // Nếu file PID tồn tại, thử lấy trạng thái tiến trình
                    $pid = trim(file_get_contents($pidFile));
                    if ($pid && is_numeric($pid)) {
                        if (function_exists('posix_kill') && posix_kill($pid, 0)) {
                            return true;
                        }
                        // Cách kiểm tra dự phòng
                        if (function_exists('exec')) {
                            $output = [];
                            exec("ps -ef | grep " . escapeshellarg($pid) . " | grep -v grep", $output);
                            // Kiểm tra có output của tiến trình không phải grep hay không
                            return !empty($output);
                        }
                    }
                }
            };
        
            $info['process'] = [
                ['name' => 'Kết nối liên tục', 'require' => 'Bật', 'value' => $checkService('workerman')],
                ['name' => 'Tác vụ định kỳ', 'require' => 'Bật', 'value' => $checkService('timer')],
                ['name' => 'Hàng đợi tin nhắn', 'require' => 'Bật', 'value' => $checkService('queue')],
            ];
            
        } else {
            $info['process'] = [
                ['name' => 'Kết nối liên tục', 'require' => 'Bật', 'value' => file_exists(root_path('runtime') . 'workerman.pid')],
                ['name' => 'Tác vụ định kỳ', 'require' => 'Bật', 'value' => file_exists(root_path('runtime') . 'timer.pid')],
                ['name' => 'Hàng đợi tin nhắn', 'require' => 'Bật', 'value' => file_exists(root_path('runtime') . '.queue')],
            ];
        }

        return app('json')->success($info);
    }

    public function customAdminJs()
    {
        return sys_config('custom_admin_js', '');
    }
}
