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

use app\services\product\product\OutStoreProductServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class ProductStockJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Tính toán tách đơn
     * @param array $data
     * @return bool
     */
    public function distribute(array $data): bool
    {
        try {
            foreach ($data as $key => $item) {
                ProductStockJob::dispatch('calcValueStock', [$key]);
            }
        } catch (\Exception $e) {
            Log::error(['msg' => 'Tính toán tách thất bại, nguyên nhân lỗi:' . $e->getMessage(), 'data' => $data]);
        }
        return true;
    }

    /**
     * Tính tồn kho
     * @param int $id
     * @return bool
     */
    public function calcValueStock(int $id): bool
    {
        try {
            /** @var OutStoreProductServices $services */
            $services = app()->make(OutStoreProductServices::class);
            $services->calcStockByAttrValue($id);
        } catch (\Exception $e) {
            Log::error(['msg' => 'Tính tồn kho sản phẩm thất bại, nguyên nhân lỗi:' . $e->getMessage(), 'data' => $id]);
        }
        return true;
    }
}