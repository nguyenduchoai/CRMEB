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

namespace app\services\system\store;


use app\dao\system\store\SystemStoreDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Cửa hàng
 * Class SystemStoreServices
 * @package app\services\system\store
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method get(int $id, ?array $field = []) Lấy dữ liệu
 */
class SystemStoreServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * SystemStoreServices constructor.
     * @param SystemStoreDao $dao
     */
    public function __construct(SystemStoreDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách điểm nhận hàng
     * @param array $where
     * @param string $latitude
     * @param string $longitude
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreList(array $where, array $field = ['*'], string $latitude = '', string $longitude = '')
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getStoreList($where, $field, $page, $limit, $latitude, $longitude);
        foreach ($list as &$item) {
            if (isset($item['distance'])) {
                $item['range'] = bcdiv($item['distance'], '1000', 1);
            }
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy thông tin thống kê phần đầu của điểm nhận hàng
     * @return mixed
     */
    public function getStoreData()
    {
        $data['show'] = [
            'name' => 'Điểm nhận hàng đang hiển thị',
            'num' => $this->dao->count(['type' => 0]),
        ];
        $data['hide'] = [
            'name' => 'Điểm nhận hàng đang ẩn',
            'num' => $this->dao->count(['type' => 1]),
        ];
        $data['recycle'] = [
            'name' => 'Điểm nhận hàng trong thùng rác',
            'num' => $this->dao->count(['type' => 2])
        ];
        return $data;
    }

    /**
     * Lưu hoặc sửa cửa hàng
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function saveStore(int $id, array $data)
    {
        return $this->transaction(function () use ($id, $data) {
            if ($id) {
                if ($this->dao->update($id, $data)) {
                    return true;
                } else {
                    throw new AdminException(100007);
                }
            } else {
                $data['add_time'] = time();
                $data['is_show'] = 1;
                if ($this->dao->save($data)) {
                    return true;
                } else {
                    throw new AdminException(100006);
                }
            }
        });
    }

    /**
     * Admin lấy chi tiết điểm nhận hàng
     * @param int $id
     * @param string $felid
     * @return array|false|mixed|string|string[]|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreDispose(int $id, string $felid = '')
    {
        if ($felid) {
            return $this->dao->value(['id' => $id], $felid);
        } else {
            $storeInfo = $this->dao->get($id);
            if ($storeInfo) {
                $storeInfo['latlng'] = $storeInfo['latitude'] . ',' . $storeInfo['longitude'];
                $storeInfo['dataVal'] = $storeInfo['valid_time'] ? explode(' - ', $storeInfo['valid_time']) : [];
                $storeInfo['timeVal'] = $storeInfo['day_time'] ? explode(' - ', $storeInfo['day_time']) : [];
                $storeInfo['address2'] = $storeInfo['address'] ? explode(',', $storeInfo['address']) : [];
                return $storeInfo;
            }
            return false;
        }
    }

    /**
     * Lấy cửa hàng không phân trang
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStore()
    {
        return $this->dao->getStore(['type' => 0]);
    }

    /**
     * Lấy danh sách nhân viên cửa hàng để xuất
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getExportData(array $where)
    {
        return $this->dao->getStoreList($where, ['*']);
    }

}
