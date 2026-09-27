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

namespace app\services\system\config;

use app\dao\system\config\SystemGroupDao;
use app\services\BaseServices;

/**
 * Dữ liệu tổ hợp
 * Class SystemGroupServices
 * @package app\services\system\config
 * @method getConfigNameId(string $configName) Lấy ID cấu hình
 * @method save(array $data) Thêm dữ liệu mới
 * @method get(int $id, ?array $field = []) Lấy một dòng dữ liệu
 * @method count(array $where = []): int Lấy số lượng theo điều kiện
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method delete($id, ?string $key = null) Xóa dữ liệu
 * @method value(array $where, ?string $field = '') Lấy một giá trị
 */
class SystemGroupServices extends BaseServices
{

    /**
     * SystemGroupServices constructor.
     * @param SystemGroupDao $dao
     */
    public function __construct(SystemGroupDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách dữ liệu tổ hợp
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupList(array $where, array $field = ['*'])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getGroupList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        foreach ($list as $key => $value) {
            if (isset($value['fields'])) {
                $list[$key]['typelist'] = $value['fields'];
                unset($list[$key]['fields']);
            }
        }
        return compact('list', 'count');
    }

    /**
     * Lấy phần header dưới tab dữ liệu tổ hợp
     * @param int $id
     * @return array
     */
    public function getGroupDataTabHeader(int $id)
    {
        $data = $this->getValueFields($id);
        $header = [];
        foreach ($data as $key => $item) {
            if ($item['type'] == 'upload' || $item['type'] == 'uploads') {
                $header[$key]['key'] = $item['title'];
                $header[$key]['minWidth'] = 60;
                $header[$key]['type'] = 'img';
            } elseif ($item['title'] == 'url' || $item['title'] == 'wap_url' || $item['title'] == 'link' || $item['title'] == 'wap_link') {
                $header[$key]['key'] = $item['title'];
                $header[$key]['minWidth'] = 200;
            } else {
                $header[$key]['key'] = $item['title'];
                $header[$key]['minWidth'] = 100;
            }
            $header[$key]['title'] = $item['name'];
        }
        array_unshift($header, ['key' => 'id', 'title' => 'Mã số', 'minWidth' => 60]);
        array_push($header, ['slot' => 'status', 'title' => 'Khả dụng', 'minWidth' => 80], ['key' => 'sort', 'title' => 'Thứ tự sắp xếp', 'minWidth' => 80], ['slot' => 'action', 'fixed' => 'right', 'title' => 'Thao tác', 'minWidth' => 120]);
        return compact('header');
    }

    /**
     * Lấy các trường fields của dữ liệu tổ hợp
     * @param int $id
     * @return array|mixed
     */
    public function getValueFields(int $id)
    {
        return json_decode($this->dao->value(['id' => $id], 'fields'), true) ?: [];
    }

}
