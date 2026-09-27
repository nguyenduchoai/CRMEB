<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace app\jobs;


use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;

/**
 * Kiểm tra hàng đợi tin nhắn có được thực thi không
 * Class CheckQueueJob
 * @package app\jobs
 */
class CheckQueueJob extends BaseJobs
{
    use QueueTrait;

    public function doJob($key)
    {
        $path = root_path('runtime') . '.queue';
        file_put_contents($path, $key);
        return true;
    }
}
