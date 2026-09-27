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
namespace crmeb\utils;

use Exception;

/**
 * Tính toán chữ ký
 * Class fileVerification
 * @package crmeb\utils
 */
class fileVerification
{
    public $path = "";
    public $fileValue = "";

    /**
     * Đường dẫn project
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function getSignature(string $path): string
    {
        if (!is_dir($path) && !is_file($path)) {
            throw new Exception($path . " không phải là tệp hoặc thư mục hợp lệ!");
        }

        $appPath = $path . DS . 'app';
        if (!is_dir($appPath)) {
            throw new Exception($appPath . " không phải là thư mục hợp lệ!");
        }

        $crmebPath = $path . DS . 'crmeb';
        if (!is_dir($crmebPath)) {
            throw new Exception($crmebPath . " không phải là thư mục hợp lệ!");
        }

        $this->path = $appPath;
        $this->getFileSignature($appPath);
        $this->path = $crmebPath;
        $this->getFileSignature($crmebPath);
        return md5($this->fileValue);
    }

    /**
     * Tính chữ ký
     * @param string $path
     * @return void
     * @throws Exception
     */
    public function getFileSignature(string $path)
    {
        if (!is_dir($path)) {
            $this->fileValue .= @md5_file($path);
        } else {
            if (!$dh = opendir($path)) throw new Exception($path . " File open failed!");
            while (($file = readdir($dh)) != false) {
                if ($file == "." || $file == "..") {
                    continue;
                } else {
                    $this->getFileSignature($path . DS . $file);
                }
            }
            closedir($dh);
        }
    }
}