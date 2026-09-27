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

use app\services\message\notice\EnterpriseWechatService;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class EnterpriseWechatJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Gửi tin nhắn đến nhóm WeCom
     * @param $data
     * @return bool
     */
    public function doJob($data): bool
    {
        try {
            /** @var EnterpriseWechatService $enterpriseWechatService */
            $enterpriseWechatService = app()->make(EnterpriseWechatService::class);
            $enterpriseWechatService->weComSend($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Gửi tin nhắn nhóm WeCom thất bại, nguyên nhân:' . $e->getMessage());
        }
    }
}
