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
namespace app\listener;

use app\services\system\SystemEventServices;
use think\facade\Log;

class CustomEventListener
{
    public function handle($event)
    {
        [$mark, $data] = $event;
        try {
            $list = app()->make(SystemEventServices::class)->selectList(['mark' => $mark, 'is_del' => 0, 'is_open' => 1])->toArray();
            foreach ($list as $item) {
                eval(json_decode($item['customCode']));
            }
        } catch (\Throwable $e) {
            $listener_log_open = config("log.listener_log", false);
            if ($listener_log_open) {
                $date = date('Y-m-d H:i:s', time());
                Log::write($date . 'Lỗi sự kiện tùy chỉnh:' . $e->getMessage(), 'listener');
            }
        }
    }
}