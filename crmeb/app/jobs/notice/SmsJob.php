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

namespace app\jobs\notice;

use app\services\message\notice\SmsService;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;


class SmsJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Gửi SMS
     * @param $switch
     * @param $adminList
     * @param $order
     * @return bool
     */
    public function doJob($phone, array $data, string $template)
    {

        try{
            /** @var SmsService $smsServices */
            $smsServices = app()->make(SmsService::class);
            $smsServices->send(true, $phone, $data, $template);
            return true;
        }catch (\Throwable $e) {
            Log::error('Gửi SMS thất bại, nguyên nhân:' . $e->getMessage());
        }

    }

}
