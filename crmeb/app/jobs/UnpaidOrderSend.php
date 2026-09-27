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


use app\services\order\StoreOrderServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;

/**
 * Gửi SMS sau 10 phút chưa thanh toán
 * Class UnpaidOrderSend
 * @package crmeb\jobs
 */
class UnpaidOrderSend extends BaseJobs
{
    use QueueTrait;

    public function doJob($id)
    {
        /** @var StoreOrderServices $services */
        $services = app()->make(StoreOrderServices::class);
        $orderInfo = $services->get($id);
        if (!$orderInfo) {
            return true;
        }
        if ($orderInfo->paid) {
            return true;
        }
        if ($orderInfo->is_del) {
            return true;
        }
        //Gửi tin nhắn cho người dùng khi nhận hàng
        event('NoticeListener', [['order' => $orderInfo], 'order_pay_false']);
        return true;
    }

}
