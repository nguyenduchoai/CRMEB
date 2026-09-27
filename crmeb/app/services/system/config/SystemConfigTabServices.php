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

namespace app\services\system\config;


use app\dao\system\config\SystemConfigTabDao;
use app\services\BaseServices;
use app\services\system\SystemMenusServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;

/**
 * Danh mục cấu hình hệ thống
 * Class SystemConfigTabServices
 * @package app\services\system\config
 * @method save(array $data) Ghi dữ liệu
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method delete($id, ?string $key = null) Xóa dữ liệu
 */
class SystemConfigTabServices extends BaseServices
{
    /**
     * SystemConfigTabServices constructor.
     * @param SystemConfigTabDao $dao
     */
    public function __construct(SystemConfigTabDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Đọc danh mục phần đầu của cài đặt hệ thống
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfigTab(int $pid)
    {
        $list = $this->dao->getConfigTabAll(['status' => 1, 'pid' => $pid], ['id', 'id as value', 'title as label', 'pid', 'icon', 'type'], $pid ? [] : [['type', '=', '0']]);
        return get_tree_children($list);
    }

    /**
     * Lấy danh sách danh mục cấu hình
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfgTabList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getConfgTabList($where, $page, $limit);
        $count = $this->dao->count($where);
        $menusValue = [];
        foreach ($list as $item) {
            $menusValue[] = $item->getData();
        }
        $list = get_tree_children($menusValue);
        $count = 0;
        return compact('list', 'count');
    }

    /**
     * Lấy cây dropdown chọn danh mục cấu hình
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSelectForm()
    {
        $menuList = $this->dao->getConfigTabAll([], ['id', 'pid', 'title']);
        $list = sort_list_tier($menuList, 0, 'pid', 'id');
        $menus = [['value' => 0, 'label' => 'Cấp cao nhất']];
        foreach ($list as $menu) {
            $menus[] = ['value' => $menu['id'], 'label' => $menu['html'] . $menu['title']];
        }
        return $menus;
    }

    /**
     * Dữ liệu dạng cây danh mục cấu hình
     * @param $value
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/9/12
     */
    public function getConfigTabListForm($value)
    {
        $configTabList = $this->dao->getConfigTabAll([], ['id as value', 'pid', 'title as label']);
        if ($value) {
            $data = get_tree_value($configTabList, $value);
        } else {
            $data = [0];
        }
        $configTabList = get_tree_children($configTabList, 'children', 'value');
        array_unshift($configTabList, ['value' => 0, 'pid' => 0, 'label' => 'Danh mục cấp cao nhất']);
        return [$configTabList, array_reverse($data)];
    }

    /**
     * Tạo form
     * @param array $formData
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createConfigTabForm(array $formData = [])
    {
        [$configTabList, $data1] = $this->getConfigTabListForm((int)($formData['pid'] ?? 0), 3);
        $form[] = Form::cascader('pid', 'Danh mục cha', $data1)->options($configTabList)->filterable(true)->props(['props' => ['multiple' => false, 'checkStrictly' => true, 'emitPath' => false]])->style(['width'=>'100%']);
        $form[] = Form::input('title', 'Tên danh mục', $formData['title'] ?? '');
        $form[] = Form::input('eng_title', 'Tên trường tiếng Anh của danh mục', $formData['eng_title'] ?? '');
        $form[] = Form::frameInput('icon', 'Biểu tượng', $this->url(config('app.admin_prefix', 'admin') . '/widget.widgets/icon', ['fodder' => 'icon'], true), $formData['icon'] ?? '')->icon('el-icon-picture-outline')->height('560px')->props(['footer' => false]);
        $form[] = Form::radio('type', 'Loại', $formData['type'] ?? 0)->options([
            ['value' => 0, 'label' => 'Hệ thống'],
            ['value' => 3, 'label' => 'Khác']
        ]);
        [$menusList, $data2] = app()->make(SystemMenusServices::class)->getFormCascaderMenus((int)($formData['menus_id'] ?? 0));
        $form[] = Form::cascader('menus_id', 'Menu liên kết', $data2)->options($menusList)->filterable(true)->props(['props' => ['multiple' => false, 'checkStrictly' => true, 'emitPath' => false]])->style(['width'=>'100%']);
        $form[] = Form::radio('status', 'Trạng thái', $formData['status'] ?? 1)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 2, 'label' => 'Ẩn']]);
        $form[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($formData['sort'] ?? 0))->precision(0)->controls(false);
        return $form;
    }

    /**
     * Biểu mẫu thêm danh mục cấu hình
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm()
    {
        return create_form('Thêm danh mục cấu hình', $this->createConfigTabForm(), $this->url('/setting/config_class'));
    }

    /**
     * Biểu mẫu sửa danh mục cấu hình
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function updateForm(int $id)
    {
        $configTabInfo = $this->dao->get($id);
        if (!$configTabInfo) {
            throw new AdminException(100026);
        }
        return create_form('Sửa danh mục cấu hình', $this->createConfigTabForm($configTabInfo->toArray()), $this->url('/setting/config_class/' . $id), 'PUT');
    }
}
