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
declare (strict_types=1);

namespace app\services\shipping;

use app\services\BaseServices;
use app\dao\shipping\ShippingTemplatesDao;
use crmeb\exceptions\AdminException;

/**
 * Mẫu phí vận chuyển
 * Class ShippingTemplatesServices
 * @package app\services\shipping
 * @method getSelectList() Lấy danh sách dropdown
 * @method get($id) Lấy một dòng dữ liệu
 * @method getShippingColumn(array $where, string $field, string $key) Lấy dữ liệu mẫu phí vận chuyển theo điều kiện chỉ định
 */
class ShippingTemplatesServices extends BaseServices
{

    /**
     * Phương thức khởi tạo
     * ShippingTemplatesServices constructor.
     * @param ShippingTemplatesDao $dao
     */
    public function __construct(ShippingTemplatesDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách mẫu phí vận chuyển
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getShippingList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $data = $this->dao->getShippingList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('data', 'count');
    }

    /**
     * Lấy mẫu phí vận chuyển cần sửa
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getShipping(int $id)
    {
        $templates = $this->dao->get($id);
        if (!$templates) {
            throw new AdminException('Mẫu cần sửa không tồn tại');
        }
        /** @var ShippingTemplatesFreeServices $freeServices */
        $freeServices = app()->make(ShippingTemplatesFreeServices::class);
        /** @var ShippingTemplatesRegionServices $regionServices */
        $regionServices = app()->make(ShippingTemplatesRegionServices::class);
        /** @var ShippingTemplatesNoDeliveryServices $noDeliveryServices */
        $noDeliveryServices = app()->make(ShippingTemplatesNoDeliveryServices::class);
        $data['appointList'] = $freeServices->getFreeList($id);
        $data['templateList'] = $regionServices->getRegionList($id);
        $data['noDeliveryList'] = $noDeliveryServices->getNoDeliveryList($id);
        if (!isset($data['templateList'][0]['region'])) {
            $data['templateList'][0]['region'] = ['city_id' => 0, 'name' => 'Mặc định toàn quốc'];
        }
        $data['formData'] = [
            'name' => $templates->name,
            'type' => $templates->getData('type'),
            'appoint_check' => intval($templates->getData('appoint')),
            'no_delivery_check' => intval($templates->getData('no_delivery')),
            'sort' => intval($templates->getData('sort')),
        ];
        return $data;
    }

    /**
     * Lưu hoặc sửa mẫu phí vận chuyển
     * @param int $id
     * @param array $temp
     * @param array $data
     * @return mixed
     */
    public function save(int $id, array $temp, array $data)
    {
        if ($id) {
            $res = $this->dao->update($id, $temp);
        } else {
            $id = $this->dao->insertGetId($temp);
            $res = true;
        }

        /** @var ShippingTemplatesRegionServices $regionServices */
        $regionServices = app()->make(ShippingTemplatesRegionServices::class);


        return $this->transaction(function () use ($regionServices, $data, $id, $res) {
            //Đặt khu vực giao hàng
            $res = $res && $regionServices->saveRegion($data['region_info'], (int)$data['type'], (int)$id);
            if (!$res) {
                throw new AdminException('Thêm phí vận chuyển cho khu vực chỉ định thất bại');
            }
            //Đặt miễn phí vận chuyển chỉ định
            if ($data['appoint']) {
                /** @var ShippingTemplatesFreeServices $freeServices */
                $freeServices = app()->make(ShippingTemplatesFreeServices::class);
                $res = $res && $freeServices->saveFree($data['appoint_info'], (int)$data['type'], (int)$id);
            }

            //Đặt không giao tới
            if ($data['no_delivery']) {
                /** @var ShippingTemplatesNoDeliveryServices $noDeliveryServices */
                $noDeliveryServices = app()->make(ShippingTemplatesNoDeliveryServices::class);
                $res = $res && $noDeliveryServices->saveNoDelivery($data['no_delivery_info'], (int)$id);
            }

            if ($res) {
                return true;
            } else {
                throw new AdminException('Lưu thất bại');
            }
        });
    }

    /**
     * Xóa mẫu phí vận chuyển
     * @param int $id
     */
    public function detete(int $id)
    {
        $this->dao->delete($id);
        /** @var ShippingTemplatesFreeServices $freeServices */
        $freeServices = app()->make(ShippingTemplatesFreeServices::class);
        /** @var ShippingTemplatesRegionServices $regionServices */
        $regionServices = app()->make(ShippingTemplatesRegionServices::class);
        /** @var ShippingTemplatesNoDeliveryServices $noDeliveryServices */
        $noDeliveryServices = app()->make(ShippingTemplatesNoDeliveryServices::class);
        $freeServices->delete($id, 'temp_id');
        $regionServices->delete($id, 'temp_id');
        $noDeliveryServices->delete($id, 'temp_id');
    }
}
