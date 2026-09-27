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

namespace app\listener\admin;

/**
 * Class AdminLogin
 * @package app\listener\admin
 */
class AdminLoginListener
{

    public function handle($event)
    {
        $res = false;
        $res1 = false;
        try {
            [$key] = $event;
            //Kiểm tra hàng đợi tin nhắn có được thực thi không
            $path = root_path('runtime') . '.queue';
            $content = file_get_contents($path);
            $res = $key === $content;
            if (sys_config('queue_open', 0) == 0) $res = true;
        } catch (\Throwable $e) {
        }

        try {
            $timerPath = root_path('runtime') . '.timer';
            $timer = file_get_contents($timerPath);
            if ($timer && $timer <= time() && $timer > (time() - 70)) {
                $res1 = true;
            }
        } catch (\Throwable $e) {
        }

        return [$res, $res1];
    }
}
