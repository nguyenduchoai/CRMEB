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
declare (strict_types=1);

namespace app\services\pc;


use app\services\BaseServices;
use app\services\shipping\SystemCityServices;

class PublicServices extends BaseServices
{
    /**
     * Lấy dữ liệu thành phố
     * @param int $pid
     * @return mixed
     */
    public function getCity(int $pid)
    {
        /** @var SystemCityServices $city */
        $city = app()->make(SystemCityServices::class);
        $list = $city->getColumn(['parent_id' => $pid, 'is_show' => 1], 'city_id,name');
        return $list;
    }
}
