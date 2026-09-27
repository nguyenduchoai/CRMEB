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


use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\HttpService;

/**
 * Giấy phép thương mại
 * Class SystemAuthServices
 * @package app\services\system
 */
class SystemAuthServices extends BaseServices
{

    /**
     * Yêu cầu cấp phép
     * @param array $data
     * @return bool
     */
    public function authApply(array $data)
    {
        $res = HttpService::postRequest('http://authorize.crmeb.net/api/auth_apply', $data);
        if ($res === false) {
            throw new AdminException(100028);
        }
        $res = json_decode($res, true);
        if (isset($res['status'])) {
            if ($res['status'] == 400) {
                throw new AdminException(100028);
            } else {
                return true;
            }
        }
    }
}
