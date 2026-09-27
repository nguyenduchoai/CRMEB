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


use app\dao\shipping\ShippingTemplatesRegionDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Phí vận chuyển chỉ định
 * Class ShippingTemplatesRegionServices
 * @package app\services\shipping
 * @method  delete($id, ?string $key = null) Xóa dữ liệu
 * @method getTempRegionList(array $tempIds, array $cityId) Lấy danh sách dữ liệu miễn phí vận chuyển theo id mẫu phí vận chuyển và id thành phố
 */
class ShippingTemplatesRegionServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * ShippingTemplatesRegionServices constructor.
     * @param ShippingTemplatesRegionDao $dao
     */
    public function __construct(ShippingTemplatesRegionDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Thêm thông tin phí vận chuyển
     * @param array $regionInfo
     * @param int $type
     * @param int $tempId
     * @return bool
     * @throws \Exception
     */
    public function saveRegion(array $regionInfo, int $type = 0, $tempId = 0)
    {
        $res = true;
        if ($tempId) {
            if ($this->dao->count(['temp_id' => $tempId])) {
                $res = $this->dao->delete($tempId, 'temp_id');
            }
        }
        $regionList = [];
        foreach ($regionInfo as $item) {
            if (isset($item['region']) && is_array($item['region'])) {
                $uniqid = uniqid('adminapi') . rand(1000, 9999);
                foreach ($item['region'] as $value) {
                    if (isset($value['children']) && is_array($value['children'])) {
                        foreach ($value['children'] as $vv) {
                            if (!isset($vv['city_id'])) {
                                throw new AdminException(400591);
                            }
                            $regionList[] = [
                                'temp_id' => $tempId,
                                'province_id' => $value['city_id'] ?? 0,
                                'city_id' => $vv['city_id'] ?? 0,
                                'first' => $item['first'] ?? 0,
                                'first_price' => $item['price'] ?? 0,
                                'continue' => $item['continue'] ?? 0,
                                'continue_price' => $item['continue_price'] ?? 0,
                                'type' => $type,
                                'uniqid' => $uniqid,
                            ];
                        }
                    } else {
                        $regionList[0] = [
                            'temp_id' => $tempId,
                            'province_id' => 0,
                            'city_id' => 0,
                            'first' => $item['first'] ?? 0,
                            'first_price' => $item['price'] ?? 0,
                            'continue' => $item['continue'] ?? 0,
                            'continue_price' => $item['continue_price'] ?? 0,
                            'type' => $type,
                            'uniqid' => $uniqid,
                        ];
                    }
                }
            }
        }
        return $res && $this->dao->saveAll($regionList);
    }

    /**
     * Lấy dữ liệu thành phố trong một mẫu phí vận chuyển
     * @param int $tempId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRegionList(int $tempId)
    {
        $regionList = $this->dao->getShippingGroupArray(['temp_id' => $tempId], 'uniqid', 'uniqid', '');
        $regionData = [];
        $infos = $this->dao->getShippingArray(['uniqid' => $regionList, 'temp_id' => $tempId], '*', 'uniqid');
        foreach ($regionList as $uniqid) {
            $info = $infos[$uniqid];
            if ($info['province_id'] == 0) {
                $regionData[] = [
                    'region' => [
                        'city_id' => 0,
                        'name' => 'Mặc định toàn quốc',
                    ],
                    'regionName' => 'Mặc định toàn quốc',
                    'first' => $info['first'] ? floatval($info['first']) : 0,
                    'price' => $info['first_price'] ? floatval($info['first_price']) : 0,
                    'continue' => $info['continue'] ? floatval($info['continue']) : 0,
                    'continue_price' => $info['continue_price'] ? floatval($info['continue_price']) : 0,
                    'uniqid' => $info['uniqid'],
                ];
            } else {
                $regionData[] = [
                    'region' => $this->getRegionTemp($uniqid, $info['province_id']),
                    'regionName' => '',
                    'first' => $info['first'] ? floatval($info['first']) : 0,
                    'price' => $info['first_price'] ? floatval($info['first_price']) : 0,
                    'continue' => $info['continue'] ? floatval($info['continue']) : 0,
                    'continue_price' => $info['continue_price'] ? floatval($info['continue_price']) : 0,
                    'uniqid' => $info['uniqid'],
                ];
            }
        }

        foreach ($regionData as &$item) {
            if (!$item['regionName']) {
                $item['regionName'] = implode(';', array_map(function ($val) {
                    return $val['name'];
                }, $item['region']));
            }
        }

        return $regionData;
    }

    /**
     * Lấy mẫu phí vận chuyển theo tỉnh
     * @param string $uniqid
     * @param int $provinceId
     * @return array
     */
    public function getRegionTemp(string $uniqid, int $provinceId)
    {
        /** @var ShippingTemplatesRegionCityServices $services */
        $services = app()->make(ShippingTemplatesRegionCityServices::class);
        $infoList = $services->getUniqidList(['uniqid' => $uniqid]);
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
     * Lấy dữ liệu theo quận/thành phố
     * @param string $uniqid
     * @param int $provinceId
     * @return array
     */
    public function getCityTemp(string $uniqid, int $provinceId)
    {
        /** @var ShippingTemplatesRegionCityServices $services */
        $services = app()->make(ShippingTemplatesRegionCityServices::class);
        $infoList = $services->getUniqidList(['uniqid' => $uniqid, 'province_id' => $provinceId], false);
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
