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
namespace app\jobs;

use app\services\order\OutStoreOrderRefundServices;
use app\services\order\OutStoreOrderServices;
use app\services\user\UserServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class OutPushJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Đẩy thông báo đơn hàng
     * @param int $oid
     * @param string $pushUrl
     * @param int $step
     * @return bool
     */
    public function orderCreate(int $oid, string $pushUrl, int $step = 0): bool
    {
        if ($step > 2) {
            Log::error('Đơn hàng' . $oid . 'đẩy dữ liệu thất bại');
            return true;
        }

        try {
            /** @var OutStoreOrderServices $services */
            $services = app()->make(OutStoreOrderServices::class);
            if (!$services->orderCreatePush($oid, $pushUrl)) {
                OutPushJob::dispatchSecs(($step + 1) * 5, 'orderCreate', [$oid, $pushUrl, $step + 1]);
            }
        } catch (\Exception $e) {
            Log::error('Đơn hàng' . $oid . 'đẩy dữ liệu thất bại, nguyên nhân:' . $e->getMessage());
            OutPushJob::dispatchSecs(($step + 1) * 5, 'orderCreate', [$oid, $pushUrl, $step + 1]);
        }

        return true;
    }

    /**
     * Đẩy dữ liệu thanh toán đơn hàng
     * @param int $oid
     * @param string $pushUrl
     * @param int $step
     * @return bool
     */
    public function paySuccess(int $oid, string $pushUrl, int $step = 0): bool
    {
        if ($step > 2) {
            Log::error('Thanh toán đơn hàng' . $oid . 'đẩy dữ liệu thất bại');
            return true;
        }

        try {
            /** @var OutStoreOrderServices $services */
            $services = app()->make(OutStoreOrderServices::class);
            if (!$services->paySuccessPush($oid, $pushUrl)) {
                OutPushJob::dispatchSecs(($step + 1) * 5, 'paySuccess', [$oid, $pushUrl, $step + 1]);
            }
        } catch (\Exception $e) {
            Log::error('Thanh toán đơn hàng' . $oid . 'đẩy dữ liệu thất bại, nguyên nhân:' . $e->getMessage());
            OutPushJob::dispatchSecs(($step + 1) * 5, 'paySuccess', [$oid, $pushUrl, $step + 1]);
        }

        return true;
    }

    /**
     * Tạo đơn hậu mãi
     * @param int $oid
     * @param string $pushUrl
     * @param int $step
     * @return bool
     */
    public function refundCreate(int $oid, string $pushUrl, int $step = 0): bool
    {
        if ($step > 2) {
            Log::error('Đơn đổi trả' . $oid . 'đẩy dữ liệu thất bại');
            return true;
        }

        try {
            /** @var OutStoreOrderRefundServices $services */
            $services = app()->make(OutStoreOrderRefundServices::class);
            if (!$services->refundCreatePush($oid, $pushUrl)) {
                OutPushJob::dispatchSecs(($step + 1) * 5, 'refundCreate', [$oid, $pushUrl, $step + 1]);
            }
        } catch (\Exception $e) {
            Log::error('Đơn đổi trả' . $oid . 'đẩy dữ liệu thất bại, nguyên nhân:' . $e->getMessage());
            OutPushJob::dispatchSecs(($step + 1) * 5, 'refundCreate', [$oid, $pushUrl, $step + 1]);
        }
        return true;
    }

    /**
     * Hủy yêu cầu
     * @param int $oid
     * @param string $pushUrl
     * @param int $step
     * @return bool
     */
    public function refundCancel(int $oid, string $pushUrl, int $step = 0): bool
    {
        if ($step > 2) {
            Log::error('Hủy đơn đổi trả' . $oid . 'đẩy dữ liệu thất bại');
            return true;
        }

        try {
            /** @var OutStoreOrderRefundServices $services */
            $services = app()->make(OutStoreOrderRefundServices::class);
            if (!$services->cancelApplyPush($oid, $pushUrl)) {
                OutPushJob::dispatchSecs(($step + 1) * 5, 'refundCancel', [$oid, $pushUrl, $step + 1]);
            }
        } catch (\Exception $e) {
            Log::error('Hủy đơn đổi trả' . $oid . 'đẩy dữ liệu thất bại, nguyên nhân:' . $e->getMessage());
            OutPushJob::dispatchSecs(($step + 1) * 5, 'refundCancel', [$oid, $pushUrl, $step + 1]);
        }
        return true;
    }

    /**
     * Đẩy thông báo thay đổi số dư, điểm thưởng, hoa hồng, điểm kinh nghiệm
     * @param array $data
     * @param string $pushUrl
     * @param int $step
     * @return bool
     */
    public function userUpdate(array $data, string $pushUrl, int $step = 0): bool
    {
        if ($step > 2) {
            Log::error('Đẩy dữ liệu thay đổi người dùng thất bại');
            return true;
        }

        try {
            /** @var UserServices $services */
            $services = app()->make(UserServices::class);
            if (!$services->userUpdate($data, $pushUrl)) {
                OutPushJob::dispatchSecs(($step + 1) * 5, 'userUpdate', [$data, $pushUrl, $step + 1]);
            }
        } catch (\Exception $e) {
            OutPushJob::dispatchSecs(($step + 1) * 5, 'userUpdate', [$data, $pushUrl, $step + 1]);
        }
        return true;
    }
}