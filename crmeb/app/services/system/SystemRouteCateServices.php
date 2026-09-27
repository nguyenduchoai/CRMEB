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

namespace app\services\system;


use app\dao\system\SystemRouteCateDao;
use app\services\BaseServices;
use crmeb\services\FormBuilder;

/**
 * Class SystemRouteCateServices
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/6
 * @package app\services\system
 */
class SystemRouteCateServices extends BaseServices
{

    /**
     * SystemRouteCateServices constructor.
     * @param SystemRouteCateDao $dao
     */
    public function __construct(SystemRouteCateDao $dao)
    {
        $this->dao = $dao;
    }

    public function getPathValue(array $path)
    {
        $pathAttr = explode('/', $path);
        $pathData = [];
        foreach ($pathAttr as $item) {
            if (!$item) {
                $pathData[] = $item;
            }
        }
        return $pathAttr;
    }

    /**
     * @param array $path
     * @param int $id
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function setPathValue(array $path, int $id)
    {
        return ($path ? '/' . implode('/', $path) : '') . '/' . $id . '/';
    }

    /**
     * @param string $appName
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function getAllList(string $appName = 'outapi', string $field = '*', string $order = '')
    {
        $list = $this->dao->selectList(['app_name' => $appName], $field, 0, 0, $order)->toArray();
        return get_tree_children($list);
    }

    /**
     * @param int $id
     * @param string $appName
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    public function getFrom(int $id = 0, string $appName = 'outapi')
    {
        $url = '/system/route_cate';
        $cateInfo = [];
        $path = [];
        if ($id) {
            $cateInfo = $this->dao->get($id);
            $cateInfo = $cateInfo ? $cateInfo->toArray() : [];
            $url .= '/' . $id;
            $path = explode('/', $cateInfo['path']);
            $newPath = [];
            foreach ($path as $item) {
                if ($item) {
                    $newPath[] = $item;
                }
            }
            $path = $newPath;
        }
        $options = $this->dao->selectList(['app_name' => $appName], 'name as label,id as value,id,pid')->toArray();
        $rule = [
//            FormBuilder::cascader('path', 'danh mục cấp trên', $path)->data(get_tree_children($options)),
            FormBuilder::input('name', 'Tên danh mục', $cateInfo['name'] ?? '')->required(),
            FormBuilder::number('sort', 'Thứ tự sắp xếp', (int)($cateInfo['sort'] ?? 0)),
            FormBuilder::hidden('app_name', $appName)
        ];

        return create_form($id ? 'Sửa danh mục' : 'Thêm danh mục', $rule, $url, $id ? 'PUT' : 'POST');
    }
}
