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

use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use think\facade\Log;

/**
 * Lớp service quản lý môi trường Node.js
 *
 * Tổng quan chức năng:
 * Lớp service này phụ trách kiểm tra và quản lý môi trường chạy cần thiết cho việc tải lên Mini Program qua CI,
 * bao gồm kiểm tra và hướng dẫn cài đặt Node.js, npm và công cụ miniprogram-ci.
 *
 * Chức năng chính:
 * 1. Kiểm tra môi trường - Kiểm tra trạng thái cài đặt và phiên bản của Node.js, npm, miniprogram-ci
 * 2. Nhận diện hệ thống - Nhận diện loại hệ điều hành (CentOS/Ubuntu/macOS/Windows)
 * 3. Hướng dẫn cài đặt - Cung cấp lệnh cài đặt tương ứng theo hệ điều hành
 * 4. Kiểm tra hàm exec - Kiểm tra hàm exec() của PHP có khả dụng không
 *
 * Yêu cầu môi trường:
 * - Node.js >= 14.0.0 (khuyến nghị 18.x LTS)
 * - npm (được cài đặt cùng Node.js)
 * - miniprogram-ci (cài đặt toàn cục bằng npm install -g miniprogram-ci)
 * - Hàm PHP exec() không bị vô hiệu hóa
 *
 * @package app\services\system
 */
class NodeEnvironmentServices extends BaseServices
{
    /**
     * Yêu cầu phiên bản Node.js tối thiểu
     *
     * Công cụ miniprogram-ci cần Node.js 14.0.0 hoặc phiên bản cao hơn.
     */
    const MIN_NODE_VERSION = '14.0.0';

    /**
     * Phiên bản Node.js khuyến nghị
     *
     * Khuyến nghị dùng phiên bản Node.js 18 LTS, mang lại hiệu năng và bảo mật tốt hơn.
     */
    const RECOMMENDED_NODE_VERSION = '18';

    /**
     * Lấy thông tin trạng thái môi trường đầy đủ
     *
     * Kiểm tra và trả về toàn bộ thông tin môi trường cần thiết cho việc tải lên Mini Program qua CI,
     * frontend dựa vào các thông tin này để hiển thị trạng thái sẵn sàng của môi trường hoặc hướng dẫn người dùng hoàn tất cấu hình môi trường.
     *
     * @return array Thông tin trạng thái môi trường đầy đủ, gồm:
     *               - os: Thông tin hệ điều hành {family, type, version}
     *               - node: Trạng thái Node.js {installed, version, path, meets_requirement}
     *               - npm: Trạng thái npm {installed, version}
     *               - miniprogram_ci: Trạng thái công cụ CI {installed, version}
     *               - ready: Môi trường đã hoàn toàn sẵn sàng chưa
     *               - can_install: có hỗ trợ cài đặt tự động không
     *               - exec_enabled: Hàm exec có khả dụng không
     *               - message: thông báo
     */
    public function getEnvironmentStatus(): array
    {
        // Kiểm tra từng hạng mục môi trường
        $nodeInfo = $this->checkNodeInstalled();       // Trạng thái Node.js
        $npmInfo = $this->checkNpmInstalled();         // Trạng thái npm
        $ciInfo = $this->checkMiniprogramCIInstalled(); // Trạng thái miniprogram-ci
        $osInfo = $this->getOsInfo();                  // Thông tin hệ điều hành
        $execEnabled = $this->isExecEnabled();         // Tính khả dụng của hàm exec

        return [
            'os' => $osInfo,                           // Thông tin hệ điều hành
            'node' => $nodeInfo,                       // Trạng thái Node.js
            'npm' => $npmInfo,                         // Trạng thái npm
            'miniprogram_ci' => $ciInfo,               // Trạng thái miniprogram-ci
            // Điều kiện môi trường sẵn sàng: Node.js + npm + miniprogram-ci đều đã được cài đặt và exec khả dụng
            'ready' => $nodeInfo['installed'] && $npmInfo['installed'] && $ciInfo['installed'] && $execEnabled,
            'can_install' => $this->canAutoInstall(),  // Có hỗ trợ cài đặt tự động không
            'exec_enabled' => $execEnabled,            // Hàm exec có khả dụng không
            'message' => $execEnabled ? '' : 'Máy chủ đã vô hiệu hóa hàm exec, không thể sử dụng tính năng tải lên Mini Program. Vui lòng liên hệ quản trị viên máy chủ để bật hàm exec.',
        ];
    }

    /**
     * Kiểm tra hàm PHP exec() có khả dụng không
     *
     * Chức năng tải lên Mini Program qua CI phụ thuộc vào hàm exec() của PHP để chạy các công cụ dòng lệnh.
     * Nhiều máy chủ vô hiệu hóa hàm này vì lý do bảo mật, nên cần kiểm tra tính khả dụng của nó.
     *
     * Các bước kiểm tra:
     * 1. Kiểm tra hàm exec có tồn tại không
     * 2. Kiểm tra cấu hình disable_functions có chứa exec không
     * 3. Thử chạy lệnh đơn giản để xác minh tính khả dụng thực tế
     *
     * @return bool exec khả dụng thì trả về true, ngược lại trả về false
     */
    public function isExecEnabled(): bool
    {
        // Kiểm tra hàm exec có tồn tại không
        if (!function_exists('exec')) {
            return false;
        }

        // Kiểm tra cấu hình disable_functions có vô hiệu hóa exec không
        $disabled = explode(',', ini_get('disable_functions'));
        $disabled = array_map('trim', $disabled);
        if (in_array('exec', $disabled)) {
            return false;
        }

        // Thử chạy một lệnh đơn giản để xác minh tính khả dụng thực tế
        $output = [];
        $code = 0;
        @exec('echo test 2>&1', $output, $code);

        return $code === 0 && !empty($output);
    }

    /**
     * Kiểm tra Node.js đã được cài đặt chưa
     *
     * Lấy trạng thái cài đặt và thông tin phiên bản của Node.js bằng cách chạy lệnh node -v.
     *
     * @return array Node.js thông tin trạng thái, gồm:
     *               - installed: Đã cài đặt chưa
     *               - version: Số phiên bản (ví dụ 18.17.0)
     *               - path: Đường dẫn file thực thi
     *               - meets_requirement: Có đáp ứng yêu cầu phiên bản tối thiểu không
     */
    public function checkNodeInstalled(): array
    {
        $result = [
            'installed' => false,
            'version' => '',
            'path' => '',
            'meets_requirement' => false,
        ];

        // Chạy node -v để lấy thông tin phiên bản
        $output = $this->execCommand('node -v 2>&1');
        if ($output && preg_match('/v?(\d+\.\d+\.\d+)/', $output, $matches)) {
            $result['installed'] = true;
            $result['version'] = $matches[1];
            // Kiểm tra phiên bản có đáp ứng yêu cầu tối thiểu không (>= 14.0.0)
            $result['meets_requirement'] = version_compare($matches[1], self::MIN_NODE_VERSION, '>=');

            // Lấy đường dẫn đầy đủ của file thực thi node
            $path = $this->execCommand('which node 2>&1');
            $result['path'] = trim($path);
        }

        return $result;
    }

    /**
     * Kiểm tra npm đã được cài đặt chưa
     *
     * npm là trình quản lý gói của Node.js, thường được cài đặt cùng Node.js.
     * Dùng để cài đặt các gói npm như miniprogram-ci.
     *
     * @return array npm thông tin trạng thái, gồm:
     *               - installed: Đã cài đặt chưa
     *               - version: số phiên bản
     */
    public function checkNpmInstalled(): array
    {
        $result = [
            'installed' => false,
            'version' => '',
        ];

        // Chạy npm -v để lấy thông tin phiên bản
        $output = $this->execCommand('npm -v 2>&1');
        if ($output && preg_match('/(\d+\.\d+\.\d+)/', $output, $matches)) {
            $result['installed'] = true;
            $result['version'] = $matches[1];
        }

        return $result;
    }

    /**
     * Kiểm tra miniprogram-ci đã được cài đặt toàn cục chưa
     *
     * miniprogram-ci là công cụ tải lên code Mini Program do WeChat chính thức cung cấp.
     * Cần cài đặt toàn cục bằng npm install -g miniprogram-ci.
     *
     * @return array miniprogram-ci thông tin trạng thái, gồm:
     *               - installed: Đã cài đặt chưa
     *               - version: số phiên bản
     */
    public function checkMiniprogramCIInstalled(): array
    {
        $result = [
            'installed' => false,
            'version' => '',
        ];

        // Kiểm tra các gói được cài đặt toàn cục bằng npm list -g
        $output = $this->execCommand('npm list -g miniprogram-ci --depth=0 2>&1');
        if ($output && preg_match('/miniprogram-ci@(\d+\.\d+\.\d+)/', $output, $matches)) {
            $result['installed'] = true;
            $result['version'] = $matches[1];
        }

        return $result;
    }

    /**
     * Lấy thông tin hệ điều hành
     *
     * Nhận diện loại hệ điều hành của máy chủ, dùng để cung cấp hướng dẫn cài đặt tương ứng.
     * Kiểm tra bằng dòng lệnh để tránh bị ảnh hưởng bởi giới hạn open_basedir.
     *
     * Các hệ thống hỗ trợ nhận diện:
     * - Linux: CentOS/RHEL/Rocky/Ubuntu/Debian/Alpine, v.v.
     * - macOS: Lấy phiên bản qua sw_vers
     * - Windows: Nhận diện qua PHP_OS_FAMILY
     *
     * @return array Thông tin hệ điều hành, gồm:
     *               - family: Họ hệ điều hành (Linux/Darwin/Windows)
     *               - type: Loại cụ thể (centos/ubuntu/debian/macos/windows)
     *               - version: Số phiên bản hệ thống
     */
    public function getOsInfo(): array
    {
        $os = PHP_OS_FAMILY;  // Lấy họ hệ điều hành mà PHP nhận diện
        $type = 'unknown';
        $version = '';

        if ($os === 'Linux') {
            // Hệ thống Linux: Đọc /etc/os-release bằng dòng lệnh để lấy thông tin bản phân phối
            $osRelease = $this->execCommand('cat /etc/os-release 2>/dev/null');

            if ($osRelease) {
                // Phân tích nội dung file os-release
                $lines = explode("\n", $osRelease);
                $osInfo = [];
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line, 2);
                        $osInfo[trim($key)] = trim($value, '"\' ');
                    }
                }

                $id = strtolower($osInfo['ID'] ?? '');
                $version = $osInfo['VERSION_ID'] ?? '';

                // Ánh xạ loại hệ điều hành
                if (in_array($id, ['centos', 'rhel', 'rocky', 'almalinux', 'fedora'])) {
                    $type = 'centos';  // Dòng Red Hat
                } elseif ($id === 'ubuntu') {
                    $type = 'ubuntu';
                } elseif (in_array($id, ['debian', 'raspbian'])) {
                    $type = 'debian';
                } elseif ($id === 'alpine') {
                    $type = 'alpine';
                } else {
                    $type = $id ?: 'linux';
                }
            } else {
                // Phương án dự phòng: Dùng lệnh uname
                $uname = $this->execCommand('uname -a 2>/dev/null');
                $type = 'linux';
                $version = $uname ?: '';
            }
        } elseif ($os === 'Darwin') {
            // Hệ thống macOS
            $type = 'macos';
            $version = $this->execCommand('sw_vers -productVersion 2>&1') ?: '';
        } elseif ($os === 'Windows') {
            // Hệ thống Windows
            $type = 'windows';
        }

        return [
            'family' => $os,            // Họ hệ điều hành
            'type' => $type,            // Loại cụ thể
            'version' => trim($version), // Số phiên bản
        ];
    }

    /**
     * Kiểm tra có hỗ trợ cài đặt tự động không
     *
     * Kiểm tra môi trường máy chủ có hỗ trợ tự động cài đặt Node.js và các công cụ liên quan không.
     * Chức năng cài đặt tự động phụ thuộc vào loại hệ điều hành và tính khả dụng của các hàm PHP.
     *
     * Hệ điều hành được hỗ trợ: CentOS/RHEL, Ubuntu, Debian, Alpine, macOS
     *
     * @return bool Hỗ trợ cài đặt tự động thì trả về true
     */
    public function canAutoInstall(): bool
    {
        $os = $this->getOsInfo();

        // Danh sách hệ điều hành hỗ trợ cài đặt tự động
        $supportedOs = ['centos', 'rhel', 'ubuntu', 'debian', 'alpine', 'macos'];

        if (!in_array($os['type'], $supportedOs)) {
            return false;
        }

        // Kiểm tra có quyền thực thi lệnh không (exec hoặc shell_exec)
        if (!function_exists('exec') && !function_exists('shell_exec')) {
            return false;
        }

        return true;
    }

    /**
     * Thực thi lệnh và trả về output
     *
     * Phương thức thực thi lệnh được đóng gói, ưu tiên dùng exec, nếu không khả dụng thì thử shell_exec.
     *
     * @param string $command Lệnh cần thực thi
     * @return string|null Output của lệnh, thực thi thất bại thì trả về null
     */
    protected function execCommand(string $command): ?string
    {
        // Ưu tiên dùng hàm exec
        if (function_exists('exec')) {
            $output = [];
            exec($command, $output);
            return implode("\n", $output);
        }
        // Dự phòng: Dùng hàm shell_exec
        elseif (function_exists('shell_exec')) {
            return shell_exec($command);
        }

        return null;
    }

    /**
     * Lấy URL script cài đặt một cú nhấp
     *
     * Trả về địa chỉ script Shell dùng để tự động cài đặt môi trường Node.js.
     *
     * @return string Địa chỉ URL của script cài đặt
     */
    public function getInstallScriptUrl(): string
    {
//        return sys_config('site_url', '') . '/statics/scripts/install_node_env.sh';
        return 'https://www.crmeb.com/static/upgrade/install_node_env.sh';
    }

    /**
     * Lấy hướng dẫn cài đặt (cách cài đặt thủ công)
     *
     * Trả về các bước cài đặt Node.js và miniprogram-ci tương ứng theo loại hệ điều hành của máy chủ.
     * Mỗi hệ điều hành đều có lệnh cài đặt được tối ưu riêng.
     *
     * Hệ điều hành được hỗ trợ:
     * - CentOS/RHEL: Dùng kho rpm của NodeSource
     * - Ubuntu/Debian: Dùng kho deb của NodeSource
     * - macOS: Dùng trình quản lý gói Homebrew
     * - Windows: Tải gói cài đặt từ trang web chính thức của Node.js
     *
     * @return array Thông tin hướng dẫn cài đặt, gồm:
     *               - title: tiêu đề hướng dẫn (ví dụ “Hướng dẫn cài đặt CentOS/RHEL”)
     *               - steps: Mảng các bước cài đặt, chứa các câu lệnh dòng lệnh cụ thể
     *               - script_url: địa chỉ URL của script cài đặt một chạm
     */
    public function getInstallGuide(): array
    {
        // Lấy thông tin hệ điều hành hiện tại
        $os = $this->getOsInfo();

        // Hướng dẫn cài đặt cho từng hệ điều hành
        $guides = [
            // Dòng CentOS/RHEL - Dùng trình quản lý gói yum
            'centos' => [
                'title' => 'Hướng dẫn cài đặt CentOS/RHEL',
                'steps' => [
                    '1. Thêm kho NodeSource:',
                    '   curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -',
                    '2. Cài đặt Node.js:',
                    '   sudo yum install -y nodejs',
                    '3. Kiểm tra cài đặt:',
                    '   node -v && npm -v',
                    '4. Cài đặt miniprogram-ci:',
                    '   sudo npm install miniprogram-ci -g',
                ],
            ],
            // Ubuntu - Dùng trình quản lý gói apt
            'ubuntu' => [
                'title' => 'Hướng dẫn cài đặt Ubuntu/Debian',
                'steps' => [
                    '1. Thêm kho NodeSource:',
                    '   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -',
                    '2. Cài đặt Node.js:',
                    '   sudo apt-get install -y nodejs',
                    '3. Kiểm tra cài đặt:',
                    '   node -v && npm -v',
                    '4. Cài đặt miniprogram-ci:',
                    '   sudo npm install miniprogram-ci -g',
                ],
            ],
            // Debian - Giống Ubuntu
            'debian' => [
                'title' => 'Hướng dẫn cài đặt Ubuntu/Debian',
                'steps' => [
                    '1. Thêm kho NodeSource:',
                    '   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -',
                    '2. Cài đặt Node.js:',
                    '   sudo apt-get install -y nodejs',
                    '3. Kiểm tra cài đặt:',
                    '   node -v && npm -v',
                    '4. Cài đặt miniprogram-ci:',
                    '   sudo npm install miniprogram-ci -g',
                ],
            ],
            // macOS - Dùng Homebrew
            'macos' => [
                'title' => 'Hướng dẫn cài đặt macOS',
                'steps' => [
                    '1. Cài đặt Homebrew (nếu chưa cài):',
                    '   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"',
                    '2. Cài đặt Node.js:',
                    '   brew install node@18',
                    '3. Kiểm tra cài đặt:',
                    '   node -v && npm -v',
                    '4. Cài đặt miniprogram-ci:',
                    '   npm install miniprogram-ci -g',
                ],
            ],
            // Windows - Tải về và cài đặt từ trang web chính thức
            'windows' => [
                'title' => 'Hướng dẫn cài đặt Windows',
                'steps' => [
                    '1. Tải xuống gói cài đặt Node.js:',
                    '   Truy cập https://nodejs.org/zh-cn/download/',
                    '2. Chạy trình cài đặt, làm theo hướng dẫn để hoàn tất cài đặt',
                    '3. Mở Command Prompt, kiểm tra cài đặt:',
                    '   node -v && npm -v',
                    '4. Cài đặt miniprogram-ci:',
                    '   npm install miniprogram-ci -g',
                ],
            ],
        ];

        // Lấy hướng dẫn tương ứng theo loại hệ điều hành, hệ thống không xác định thì dùng hướng dẫn chung
        $type = $os['type'] ?: 'unknown';
        $guide = isset($guides[$type]) ? $guides[$type] : [
            'title' => 'Hướng dẫn cài đặt chung',
            'steps' => [
                '1. Truy cập trang chủ Node.js để tải gói cài đặt:',
                '   https://nodejs.org/zh-cn/download/',
                '2. Làm theo tài liệu chính thức để hoàn tất cài đặt',
                '3. Kiểm tra cài đặt:',
                '   node -v && npm -v',
                '4. Cài đặt miniprogram-ci:',
                '   npm install miniprogram-ci -g',
            ],
        ];

        // Thêm URL script cài đặt một cú nhấp
        $guide['script_url'] = $this->getInstallScriptUrl();

        return $guide;
    }
}
