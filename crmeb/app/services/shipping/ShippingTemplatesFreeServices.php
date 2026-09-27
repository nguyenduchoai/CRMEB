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

namespace app\services\shipping;


use app\dao\shipping\ShippingTemplatesFreeDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Miễn phí vận chuyển
 * Class ShippingTemplatesFreeServices
 * @package app\services\shipping
 * @method  delete($id, ?string $key = null) Xóa dữ liệu
 * @method isFree($tempId, $cityid, $number, $price) Có đủ điều kiện miễn phí vận chuyển hay không
 */
class ShippingTemplatesFreeServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * ShippingTemplatesFreeServices constructor.
     * @param ShippingTemplatesDao $dao
     */
    public function __construct(ShippingTemplatesFreeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Thêm thông tin miễn phí vận chuyển
     * @param array $appointInfo
     * @param int $type
     * @param int $tempId
     * @return bool
     * @throws \Exception
     */
    public function saveFree(array $appointInfo, int $type = 0, int $tempId = 0)
    {
        $res = true;
        if ($tempId) {
            if ($this->dao->count(['temp_id' => $tempId])) {
                $res = $this->dao->delete($tempId, 'temp_id');
            }
        }
        $placeList = [];
        foreach ($appointInfo as $item) {
            if (isset($item['place']) && is_array($item['place'])) {
                $uniqid = uniqid('adminapi') . rand(1000, 9999);
                foreach ($item['place'] as $value) {
                    if (isset($value['children']) && is_array($value['children'])) {
                        foreach ($value['children'] as $vv) {
                            if (!isset($vv['city_id'])) {
                                throw new AdminException('Thiếu ID thành phố, không thể lưu');
                            }
                            $placeList [] = [
                                'temp_id' => $tempId,
                                'province_id' => $value['city_id'] ?? 0,
                                'city_id' => $vv['city_id'] ?? 0,
                                'number' => $item['a_num'] ?? 0,
                                'price' => $item['a_price'] ?? 0,
                                'type' => $type,
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
    public function getFreeList(int $tempId)
    {
        $freeIdList = $this->dao->getShippingGroupArray(['temp_id' => $tempId], 'uniqid', 'uniqid', '');
        $freeData = [];
        $infos = $this->dao->getShippingArray(['uniqid' => $freeIdList, 'temp_id' => $tempId], '*', 'uniqid');
        foreach ($freeIdList as $uniqid) {
            $info = $infos[$uniqid];
            $freeData[] = [
                'place' => $this->getFreeTemp($uniqid, $info['province_id']),
                'a_num' => $info['number'] ? floatval($info['number']) : 0,
                'a_price' => $info['price'] ? floatval($info['price']) : 0,
            ];
        }
        foreach ($freeData as &$item) {
            $item['placeName'] = implode(';', array_column($item['place'], 'name'));
        }
        return $freeData;
    }

    /**
     * Lấy tỉnh được miễn phí vận chuyển
     * @param string $uniqid
     * @param int $provinceId
     * @return array
     */
    public function getFreeTemp(string $uniqid, int $provinceId)
    {
        /** @var ShippingTemplatesFreeCityServices $service */
        $service = app()->make(ShippingTemplatesFreeCityServices::class);
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
        /** @var ShippingTemplatesFreeCityServices $service */
        $service = app()->make(ShippingTemplatesFreeCityServices::class);
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
