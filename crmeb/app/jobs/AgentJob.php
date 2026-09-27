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

namespace app\jobs;

use app\services\agent\AgentLevelServices;
use app\services\user\UserServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * Kiểm tra nâng hạng cộng tác viên
 * Class OrderJob
 * @package crmeb\jobs
 */
class AgentJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Thực hiện kiểm tra nâng hạng
     * @param $order
     * @return bool
     */
    public function doJob(int $uid)
    {
        //Kiểm tra nâng hạng cộng tác viên
        try {
            //Cửa hàng có mở tiếp thị liên kết hay không
            if (!sys_config('brokerage_func_status')) {
                return true;
            }
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userInfo = $userServices->getUserInfo($uid);
            if (!$userInfo) {
                return true;
            }
            //Lấy uid cấp trên || nếu mở tự mua thì trả về uid của chính mình
            $spread_uid = $userServices->getSpreadUid($uid, $userInfo);
            $two_spread_uid = 0;
            if ($spread_uid > 0 && $one_user_info = $userServices->getUserInfo($spread_uid)) {
                $two_spread_uid = $userServices->getSpreadUid($spread_uid, $one_user_info, false);
            }
            $uids = array_unique([$uid, $spread_uid, $two_spread_uid]);

            /** @var AgentLevelServices $agentLevelServices */
            $agentLevelServices = app()->make(AgentLevelServices::class);
            //Kiểm tra nâng hạng
            $agentLevelServices->checkUserLevelFinish($uid, $uids);

            return true;
        } catch (\Throwable $e) {
            Log::error('Kiểm tra nâng cấp cấp độ CTV thất bại, nguyên nhân:' . $e->getMessage());
        }
    }
}
