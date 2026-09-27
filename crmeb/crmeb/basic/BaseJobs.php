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

namespace crmeb\basic;


use crmeb\interfaces\JobInterface;
use think\facade\Log;
use think\queue\Job;

/**
 * Class cơ sở hàng đợi tin nhắn
 * Class BaseJobs
 * @package crmeb\basic
 */
abstract class BaseJobs implements JobInterface
{

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $this->fire(...$arguments);
    }

    /**
     * Chạy hàng đợi tin nhắn
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job, $data): void
    {
        try {
            $action = $data['do'] ?? 'doJob';//Tên tác vụ
            $infoData = $data['data'] ?? [];//Dữ liệu thực thi
            $errorCount = $data['errorCount'] ?? 0;//Số lần lỗi tối đa
            $this->runJob($action, $job, $infoData, $errorCount);
        } catch (\Throwable $e) {
            Log::error('Lỗi hàng đợi:' . $e->getMessage());
            $job->delete();
        }
    }

    /**
     * Thực thi hàng đợi
     * @param string $action
     * @param Job $job
     * @param array $infoData
     * @param int $errorCount
     */
    protected function runJob(string $action, Job $job, array $infoData, int $errorCount = 3)
    {

        $action = method_exists($this, $action) ? $action : 'handle';
        if (!method_exists($this, $action)) {
            $job->delete();
        }

        if ($this->{$action}(...$infoData)) {
            //Xóa nhiệm vụ
            $job->delete();
        } else {
            if ($job->attempts() >= $errorCount && $errorCount) {
                //Xóa nhiệm vụ
                $job->delete();
            } else {
                //Đưa lại vào hàng đợi
                $job->release();
            }
        }

    }
}
