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
namespace crmeb\utils;

/**
 * Class hỗ trợ xử lý mảng
 * Class Arr
 * @package crmeb\utils
 */
class Arr
{
    /**
     * Thêm giá trị mặc định cho mảng
     * @param array $keys
     * @param array $configList
     * @return array
     */
    public static function getDefaultValue(array $keys, array $configList = [])
    {
        $value = [];
        foreach ($keys as $val) {
            if (is_array($val)) {
                $k = $val[0] ?? '';
                $v = $val[1] ?? '';
            } else {
                $k = $val;
                $v = '';
            }
            $value[$k] = $configList[$k] ?? $v;
        }
        return $value;
    }

    /**
     * Lấy danh sách menu ivew
     * @param array $data
     * @return array
     */
    public static function getMenuIviewList(array $data)
    {
        return Arr::toIviewUi(Arr::getTree($data));
    }

    /**
     * Chuyển đổi giá trị key mà iviewUi cần
     * @param $data
     * @return array
     */
    public static function toIviewUi($data)
    {
        $newData = [];
        foreach ($data as $k => $v) {
            $temp = [];
            $temp['id'] = $v['id'];
            $temp['pid'] = $v['pid'];
            $temp['path'] = $v['menu_path'];
            $temp['title'] = $v['menu_name'];
            $temp['icon'] = $v['icon'];
            $temp['header'] = $v['header'];
            $temp['is_header'] = $v['is_header'];
            $temp['is_show'] = $v['is_show_path'];
            if ($v['is_show_path']) {
                $temp['auth'] = ['hidden'];
            }
            if (!empty($v['children'])) {
                $temp['children'] = self::toIviewUi($v['children']);
            }
            $newData[] = $temp;
        }
        return $newData;
    }

    /**
     * Lấy menu dạng cây (tree)
     * @param $data
     * @param int $pid
     * @param int $level
     * @return array
     */
    public static function getTree($data, $pid = 0, $level = 1)
    {
        $childs = self::getChild($data, $pid, $level);
        $dataSort = array_column($childs, 'sort');
        array_multisort($dataSort, SORT_DESC, $childs);
        foreach ($childs as $key => $navItem) {
            $resChild = self::getTree($data, $navItem['id']);
            if (null != $resChild) {
                $childs[$key]['children'] = $resChild;
            }
        }
        return $childs;
    }

    /**
     * Lấy submenu
     * @param $arr
     * @param $id
     * @param $lev
     * @return array
     */
    private static function getChild(&$arr, $id, $lev)
    {
        $child = [];
        foreach ($arr as  $value) {
            if ($value['pid'] == $id) {
                $value['level'] = $lev;
                $child[] = $value;
            }
        }
        return $child;
    }

    /**
     * Định dạng dữ liệu
     * @param array $array
     * @param $value
     * @param int $default
     * @return mixed
     */
    public static function setValeTime(array $array, $value, $default = 0)
    {
        foreach ($array as $item) {
            if (!isset($value[$item]))
                $value[$item] = $default;
            else if (is_string($value[$item]))
                $value[$item] = (float)$value[$item];
        }
        return $value;
    }

    /**
     * Lấy tập hợp một giá trị nào đó trong mảng hai chiều để tạo lại thành mảng, và kiểm tra mỗi phần tử trong mảng có đúng (true) không
     * @param array $data
     * @param string $filed
     * @return array
     */
    public static function getArrayFilterValeu(array $data, string $filed)
    {
        return array_filter(array_unique(array_column($data, $filed)), function ($item) {
            if ($item) {
                return $item;
            }
        });
    }

    /**
     * Chuyển mảng thành chuỗi và loại bỏ trùng lặp
     * @param array $data
     * @return false|string[]
     */
    public static function unique(array $data)
    {
        return array_unique(explode(',', implode(',', $data)));
    }

    /**
     * Lấy giá trị key chỉ định trong mảng sau khi loại bỏ trùng lặp
     * @param array $list
     * @param string $key
     * @return array
     */
    public static function getUniqueKey(array $list, string $key)
    {
        return array_unique(array_column($list, $key));
    }

    /**
     * Lấy giá trị ngẫu nhiên trong mảng
     * @param array $data
     * @return bool|mixed
     */
    public static function getArrayRandKey(array $data)
    {
        if (!$data) {
            return false;
        }
        $mun = rand(0, count($data));
        if (!isset($data[$mun])) {
            return self::getArrayRandKey($data);
        }
        return $data[$mun];
    }
}
