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

use app\services\order\StoreOrderInvoiceServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;

class OrderInvoiceJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Hàng đợi tự động xuất hóa đơn
     * @param $id
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function autoInvoice($id)
    {
        try {
            if (sys_config('elec_invoice', 1) != 1) {
                return true;
            }
            /** @var StoreOrderInvoiceServices $services */
            $services = app()->make(StoreOrderInvoiceServices::class);
            $services->invoiceIssuance($id);
        } catch (\Exception $e) {
        }
        return true;
    }

    /**
     * Hàng đợi tự động hủy hóa đơn
     * @param $id
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function autoInvoiceRed($id)
    {
        try {
            if (sys_config('elec_invoice', 1) != 1) {
                return true;
            }
            /** @var StoreOrderInvoiceServices $services */
            $services = app()->make(StoreOrderInvoiceServices::class);
            $services->redInvoiceIssuance($id);
        } catch (\Exception $e) {
        }
        return true;
    }
}