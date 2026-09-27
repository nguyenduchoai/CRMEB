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

namespace app\services\shipping;


use app\dao\shipping\ShippingTemplatesNoDeliveryDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Không giao đến
 * Class ShippingTemplatesNoDeliveryServices
 * @package app\services\shipping
 * @method isNoDelivery($tempId, $cityid) Có thuộc khu vực không giao đến hay không
 */
class ShippingTemplatesNoDeliveryServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * ShippingTemplatesNoDeliveryServices constructor.
     * @param ShippingTemplatesNoDeliveryDao $dao
     */
    public function __construct(ShippingTemplatesNoDeliveryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Thêm thông tin không giao tới
     * @param array $noDeliveryInfo
     * @param int $tempId
     * @return bool|mixed
     */
    public function saveNoDelivery(array $noDeliveryInfo, int $tempId = 0)
    {
        $res = true;
        if ($tempId) {
            if ($this->dao->count(['temp_id' => $tempId])) {
                $res = $this->dao->delete($tempId, 'temp_id');
            }
        }
        $placeList = [];
        mt_srand();
        foreach ($noDeliveryInfo as $item) {
            if (isset($item['place']) && is_array($item['place'])) {
                $uniqid = uniqid('adminapi') . rand(1000, 9999);
                foreach ($item['place'] as $value) {
                    if (isset($value['children']) && is_array($value['children'])) {
                        foreach ($value['children'] as $vv) {
                            if (!isset($vv['city_id'])) {
                                throw new AdminException(400591);
                            }
                            $placeList [] = [
                                'temp_id' => $tempId,
                                'province_id' => $value['city_id'] ?? 0,
                                'city_id' => $vv['city_id'] ?? 0,
                                'uniqid' => $uniqid,
                            ];
                        }
                    }
                }
            }
        }
        if (count($placeList)) {
            return $res && $this->dao->saveAll($placeList);
        } else {
            return $res;
        }
    }

    /**
     * Lấy địa chỉ thành phố miễn phí vận chuyển chỉ định
     * @param int $tempId
     * @return array
     */
    public function getNoDeliveryList(int $tempId)
    {
        $freeIdList = $this->dao->getShippingGroupArray(['temp_id' => $tempId], 'uniqid', 'uniqid', '');
        $freeData = [];
        foreach ($freeIdList as $uniqid) {
            $freeData[] = [
                'place' => $this->getNoDeliveryTemp($uniqid),
            ];
        }
        foreach ($freeData as &$item) {
            $item['placeName'] = implode(';', array_column($item['place'], 'name'));
        }
        return $freeData;
    }

    /**
     * Lấy tỉnh không giao tới
     * @param string $uniqid
     * @return array
     */
    public function getNoDeliveryTemp(string $uniqid)
    {
        /** @var ShippingTemplatesNoDeliveryCityServices $service */
        $service = app()->make(ShippingTemplatesNoDeliveryCityServices::class);
        $infoList = $service->getUniqidList(['uniqid' => $uniqid]);
        $childrenData = [];
        foreach ($infoList as $item) {
            $childrenData[] = [
                'city_id' => $item['province_id'],
                'name' => $item['name'] ?? 'Toàn quốc',
                'children' => $this->getCityTemp($uniqid, $item['province_id'])
            ];
        }
        return $childrenData;
    }

    /**
     * Lấy dữ liệu quận/thành phố
     * @param string $uniqid
     * @param int $provinceId
     * @return array
     */
    public function getCityTemp(string $uniqid, int $provinceId)
    {
        /** @var ShippingTemplatesNoDeliveryCityServices $service */
        $service = app()->make(ShippingTemplatesNoDeliveryCityServices::class);
        $infoList = $service->getUniqidList(['uniqid' => $uniqid, 'province_id' => $provinceId], false);
        $childrenData = [];
        foreach ($infoList as $item) {
            $childrenData[] = [
                'city_id' => $item['city_id'],
                'name' => $item['name'] ?? 'Toàn quốc',
            ];
        }
        return $childrenData;
    }

}
