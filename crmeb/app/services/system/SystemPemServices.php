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

use app\dao\system\SystemPemDao;
use app\services\BaseServices;

class SystemPemServices extends BaseServices
{
    public function __construct(SystemPemDao $dao)
    {
        $this->dao = $dao;
    }

    public function savePem($data)
    {
        $this->dao->savePem($data);
        return true;
    }

    public function getPemPath($name)
    {
        $path = '';
        $info = $this->dao->get(['name' => $name]);
        if ($info) {
            $path = root_path('runtime/pem') . $info['path'] . '.pem';
            if (!file_exists($path)) {
                // Nếu thư mục runtime/pem không tồn tại thì tạo thư mục
                if (!file_exists(root_path('runtime/pem'))) {
                    mkdir(root_path('runtime/pem'), 0777, true);
                }
                file_put_contents($path, $info['content']);
            }
        }
        return $path;
    }
}
