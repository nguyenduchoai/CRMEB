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


use app\dao\shipping\SystemCityDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder as Form;

/**
 * Dữ liệu thành phố
 * Class SystemCityServices
 * @package app\services\shipping
 * @method deleteCity(int $cityId) Xóa dữ liệu theo cityId
 * @method getCityIdMax() Lấy cityId lớn nhất
 * @method save(array $data) Lưu dữ liệu
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method value(array $where, ?string $field = '') Lấy một dòng dữ liệu
 * @method getShippingCity() Lấy dữ liệu thành phố của mẫu phí vận chuyển
 */
class SystemCityServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * SystemCityServices constructor.
     * @param SystemCityDao $dao
     */
    public function __construct(SystemCityDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy dữ liệu thành phố
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCityList(array $where)
    {
//        $list = $this->dao->getCityList($where);
//        $cityIds = array_column($list, 'parent_id');
//        $cityNames = $this->dao->getCityArray(['city_id' => $cityIds], 'name', 'city_id');
//        foreach ($list as &$item) {
//            $item['parent_id'] = $cityNames[$item['parent_id']] ?? 'Trung Quốc';
//        }
//        return $list;
//        return CacheService::get('tree_city_list', function () {
        return $this->getSonCityList($where['parent_id']);
//        }, 86400);
    }

    /**
     * Danh sách thành phố dạng cây (tree)
     * @param int $pid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSonCityList($pid = 0)
    {
        $list = $this->dao->getCityList(['parent_id' => $pid], 'id,city_id,level,name');
        $parent_name = $pid ? $this->dao->value(['city_id' => $pid], 'name') : 'Trung Quốc';
        $is_add = $pid == 0 || $this->dao->value(['city_id' => $pid], 'parent_id') == 0 ? 1 : 0;
        $arr = [];
        if ($list) {
            foreach ($list as $item) {
                $data = [];
                $data['id'] = $item['id'];
                $data['city_id'] = $item['city_id'];
                $data['label'] = $item['name'];
                $data['parent_name'] = $parent_name;
                if ($is_add) {
                    $data['children'] = [];
                    $data['hasChildren'] = [];
                    $data['_loading'] = false;
                }
                $arr [] = $data;
            }
        }
        return $arr;
    }

    /**
     * Biểu mẫu thêm dữ liệu thành phố
     * @param int $parentId
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createCityForm(int $parentId)
    {
        if ($parentId) {
            $info = $this->dao->getOne(['city_id' => $parentId], 'level,city_id,name');
        } else {
            $info = ['level' => 0, 'city_id' => 0, 'name' => 'Trung Quốc'];
        }
        $field[] = Form::hidden('level', $info['level']);
        $field[] = Form::hidden('parent_id', $info['city_id']);
        $field[] = Form::input('parent_name', 'Tên cấp trên', $info['name'])->disabled(true)->readonly(true);
        $field[] = Form::input('name', 'Tên')->required('Vui lòng điền tên thành phố');
        return create_form('Thêm thành phố', $field, $this->url('/setting/city/save'));
    }

    /**
     * Tạo mới dữ liệu thành phố
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function updateCityForm(int $id)
    {
        $info = $this->dao->get($id);
        if (!$info) {
            throw new AdminException(100026);
        }
        $info = $info->toArray();
        $info['parent_name'] = $this->dao->value(['city_id' => $info['parent_id']], 'name') ?: 'Trung Quốc';
        $field[] = Form::hidden('id', $info['id']);
        $field[] = Form::hidden('level', $info['level']);
        $field[] = Form::hidden('parent_id', $info['parent_id']);
        $field[] = Form::input('parent_name', 'Tên cấp trên', $info['parent_name'])->readonly(true);
        $field[] = Form::input('name', 'Tên', $info['name'])->required('Vui lòng điền tên thành phố');
        $field[] = Form::input('merger_name', 'Tên gộp', $info['merger_name'])->placeholder('Định dạng: Shaanxi,Xi’an,Yanta')->required('Vui lòng nhập tên gộp');
        return create_form('Sửa thành phố', $field, $this->url('/setting/city/save'));
    }

    /**
     * Lấy dữ liệu thành phố
     * @return mixed
     */
    public function cityList()
    {
        return CacheService::remember('CITY_LIST', function () {
            $allCity = $this->dao->getCityList([], 'city_id as v,name as n,parent_id');
            return sort_city_tier($allCity, 0);
        }, 0);
    }

    /**
     * Lấy danh sách đầy đủ dữ liệu thành phố
     * @return mixed
     */
    public function fullList($field = '*')
    {
        return CacheService::remember('CITY_FULL_LIST', function () use ($field) {
            return $this->fullListTree($this->dao->fullList($field));
        }, 0);
    }

    /**
     * Định dạng lấy danh sách đầy đủ dữ liệu thành phố
     * @param $data
     * @param int $pid
     * @param array $navList
     * @return array|mixed
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/10
     */
    function fullListTree($data, $pid = 0, $navList = [])
    {
        foreach ($data as $k => $menu) {
            if ($menu['parent_id'] == $pid) {
                unset($menu['parent_id']);
                unset($data[$k]);
                $menu['children'] = $this->fullListTree($data, $menu['value']);
                if(!count($menu['children'])) unset($menu['children']);
                $navList[] = $menu;
            }
        }
        return $navList;
    }
}
