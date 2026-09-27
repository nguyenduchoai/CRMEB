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
namespace app\services\activity;

use app\dao\activity\StoreActivityDao;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\BaseServices;
use app\services\product\product\StoreProductServices;
use app\services\product\sku\StoreProductAttrValueServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;

class StoreActivityServices extends BaseServices
{
    public function __construct(StoreActivityDao $dao)
    {
        $this->dao = $dao;
    }

    public function activityList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->activityList($where, '*', $page, $limit, ['seckill' => function ($query) {
            $query->field('id,activity_id');
        }]);
        $seckillTimesData = sys_data('routine_seckill_time');
        $resultArray = [];
        foreach ($seckillTimesData as $timesData) {
            $id = $timesData['id'];
            $time = intval($timesData['time']);
            $continued = intval($timesData['continued']);
            // Định dạng thời gian, thêm ":00"
            $startTime = sprintf("%02d:00", $time);
            $endTime = sprintf("%02d:00", $time + $continued);
            $resultArray[$id] = $startTime . '-' . $endTime;
        }
        $endIds = [];
        foreach ($list as &$item) {
            $item['start_day'] = date('Y-m-d H:i:s', $item['start_day']);
            if (bcadd($item['end_day'], '86400') < time()) {
                $endIds[] = $item['id'];
                $item['status'] = 0;
            }
            $item['end_day'] = date('Y-m-d 23:59:59', $item['end_day']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['product_count'] = count($item['seckill']);
            foreach (explode(',', $item['time_ids']) as $timeId) {
                $item['times_list'][] = $resultArray[$timeId] ?? [];
            }
        }
        if (count($endIds)) {
            foreach ($endIds as $endId) {
                $this->activityStatus($endId, 0, 1);
            }
        }
        $count = $this->dao->activityCount($where);
        return compact('list', 'count');
    }

    public function activityInfo($id)
    {
        $info = $this->dao->get(['id' => $id]);
        if (!$info) throw new AdminException('Dữ liệu không tồn tại');
        $info = $info->toArray();
        /** @var StoreSeckillServices $seckillServices */
        $seckillServices = app()->make(StoreSeckillServices::class);
        $seckill = $seckillServices->getList(['activity_id' => $id, 'is_del' => 0], 0, 0);
        $info['section_time'] = [date('Y-m-d', $info['start_day']), date('Y-m-d', $info['end_day'])];
        $info['time_ids'] = array_map('intval', explode(',', $info['time_ids']));
        $productList = [];
        if ($seckill) {
            /** @var StoreProductServices $productServices */
            $productServices = app()->make(StoreProductServices::class);
            $productList = $productServices->searchList(['id' => array_column($seckill, 'product_id'), 'is_del' => 0]);
            $productList = $productList['list'] ?? [];
            $seckill = array_combine(array_column($seckill, 'product_id'), $seckill);
            //Đưa vào giá sản phẩm flash sale
            foreach ($productList as &$product) {
                $product['product_price'] = $product['price'];
                $seckillInfo = $seckill[$product['id']] ?? [];
                $attrValue = $product['attrs'] ?? [];
                if ($seckillInfo && $attrValue) {
                    $product['status'] = $seckillInfo['status'];
                    $seckillAttrValue = $seckillInfo['attrs'] ?? [];
                    if ($seckillAttrValue) {
                        $seckillAttrValue = array_combine(array_column($seckillAttrValue, 'suk'), $seckillAttrValue);
                        foreach ($attrValue as &$value) {
                            $value['status'] = 0;
                            if (isset($seckillAttrValue[$value['suk']])) {
                                $value['status'] = 1;
                            }
                            $value['product_price'] = $value['price'];
                            if ($value['status']) {
                                $value['quota'] = $seckillAttrValue[$value['suk']]['quota'] ?? 0;
                                $value['quota_show'] = $seckillAttrValue[$value['suk']]['quota_show'] ?? 0;
                                $value['price'] = $seckillAttrValue[$value['suk']]['price'] ?? 0;
                                $value['cost'] = $seckillAttrValue[$value['suk']]['cost'] ?? 0;
                                $value['ot_price'] = $seckillAttrValue[$value['suk']]['ot_price'] ?? 0;
                            }
                        }
                        $product['attrs'] = $attrValue;
                    }
                }
            }
        }
        $info['product_infos'] = $productList;
        return $info;
    }

    public function activityDel($id, $type)
    {
        if (!$id) throw new AdminException('Thiếu tham số');
        $this->dao->update($id, ['is_del' => 1]);

        if ($type == 1) {
            /** @var StoreSeckillServices $storeSeckillServices */
            $storeSeckillServices = app()->make(StoreSeckillServices::class);
            $ids = $storeSeckillServices->getColumn(['activity_id' => $id], 'id');
            /** @var StoreProductAttrValueServices $storeProductAttrValueServices */
            $storeProductAttrValueServices = app()->make(StoreProductAttrValueServices::class);
            foreach ($ids as $sid) {
                $storeSeckillServices->update($sid, ['is_del' => 1]);
                $unique = $storeProductAttrValueServices->value(['product_id' => $id, 'type' => 1], 'unique');
                if ($unique) {
                    CacheService::delete('seckill_' . $unique . '_1');
                }
            }
        }

        return true;
    }

    public function activityStatus($id, $status, $type)
    {
        if (!$id) throw new AdminException('Thiếu tham số');
        $info = $this->dao->get(['id' => $id]);
        if (!$info) throw new AdminException('Dữ liệu không tồn tại');
        if (bcadd($info['end_day'], '86400') < time() && $status == 1) {
            throw new AdminException('Chương trình đã kết thúc, không thể thao tác');
        }
        $this->dao->update($id, ['status' => $status]);
        if ($type == 1) {
            /** @var StoreSeckillServices $storeSeckillServices */
            $storeSeckillServices = app()->make(StoreSeckillServices::class);
            $ids = $storeSeckillServices->getColumn(['activity_id' => $id], 'id');
            foreach ($ids as $sid) {
                $storeSeckillServices->update($sid, ['status' => $status]);
            }
        }
        return true;
    }
}
