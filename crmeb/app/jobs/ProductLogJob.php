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


use app\services\product\product\StoreProductLogServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class ProductLogJob extends BaseJobs
{
    use QueueTrait;
    /**
     * @param $type  'visit','cart','order','pay','collect','refund'
     * @param $data
     * @return bool
     */
    public function doJob($type,$data)
    {
        try {
            /** @var StoreProductLogServices $productLogServices */
            $productLogServices = app()->make(StoreProductLogServices::class);
            $productLogServices->createLog($type, $data);
        }catch (\Throwable $e){
            Log::error('Ghi nhật ký sản phẩm xảy ra lỗi, nguyên nhân lỗi:' . $e->getMessage());
        }
        return true;
    }
}
