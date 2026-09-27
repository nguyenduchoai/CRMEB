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

use app\dao\system\SystemMenusDao;
use app\services\BaseServices;
use app\services\system\admin\SystemRoleServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use crmeb\utils\Arr;

/**
 * Menu quyền
 * Class SystemMenusServices
 * @package app\services\system
 * @method save(array $data) Lưu dữ liệu
 * @method get(int $id, ?array $field = []) Lấy dữ liệu
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method getSearchList() Tìm kiếm trang chủ
 * @method getColumn(array $where, string $field, ?string $key = '') Tìm kiếm trang chủ
 * @method getVisitName(string $rule) Lấy tên menu theo địa chỉ truy cập
 */
class SystemMenusServices extends BaseServices
{

    /**
     * Khởi tạo
     * SystemMenusServices constructor.
     * @param SystemMenusDao $dao
     */
    public function __construct(SystemMenusDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy dữ liệu menu chưa bị bộ chỉnh sửa (modifier) thay đổi
     * @param $menusList
     * @return array
     */
    public function getMenusData($menusList)
    {
        $data = [];
        foreach ($menusList as $item) {
            $item = $item->getData();
            if (isset($item['menu_path'])) {
                $item['menu_path'] = '/' . config('app.admin_prefix', 'admin') . $item['menu_path'];
            }
            $data[] = $item;
        }

        return $data;
    }

    /**
     * Lấy menu quyền và quyền của admin
     * @param $rouleId
     * @param int $level
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenusList($rouleId, int $level)
    {
        /** @var SystemRoleServices $systemRoleServices */
        $systemRoleServices = app()->make(SystemRoleServices::class);
        $rules = $systemRoleServices->getRoleArray(['status' => 1, 'id' => $rouleId], 'rules');
        $rulesStr = Arr::unique($rules);
        $menusList = $this->dao->getMenusRoule(['route' => $level ? $rulesStr : '', 'is_show_path' => 1]);
        $unique = $this->dao->getMenusUnique(['unique' => $level ? $rulesStr : '']);
        return [Arr::getMenuIviewList($this->getMenusData($menusList)), $unique];
    }

    /**
     * Lấy danh sách cấu trúc cây menu admin
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, array $field = ['*'])
    {
        $menusList = $this->dao->getMenusList($where, $field);
        $menusList = $this->getMenusData($menusList);
        return get_tree_children($menusList);
    }

    /**
     * Lấy danh sách menu cần cho biểu mẫu (form)
     * @return array[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getFormSelectMenus()
    {
        $menuList = $this->dao->getMenusRoule(['is_del' => 0], ['id', 'pid', 'menu_name']);
        $list = sort_list_tier($this->getMenusData($menuList), '0', 'pid', 'id');
        $menus = [['value' => 0, 'label' => 'Cấp cao nhất']];
        foreach ($list as $menu) {
            $menus[] = ['value' => $menu['id'], 'label' => $menu['html'] . $menu['menu_name']];
        }
        return $menus;
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFormCascaderMenus(int $value = 0, $auth_type = 0)
    {
        $where = ['is_del' => 0];
        $menuList = $this->dao->getMenusRoule($where, ['id as value', 'pid', 'menu_name as label']);
        $menuList = $this->getMenusData($menuList);
        if ($value) {
            $data = get_tree_value($menuList, $value);
        } else {
            $data = [];
        }
        return [get_tree_children($menuList, 'children', 'value'), array_reverse($data)];
    }

    /**
     * Tạo biểu mẫu sinh quy cách quyền
     * @param array $formData
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createMenusForm(array $formData = [])
    {
        $field[] = Form::input('menu_name', 'Tên nút', $formData['menu_name'] ?? '')->required('Tên nút là bắt buộc');
        $field[] = Form::input('menu_path', 'Tên route', $formData['menu_path'] ?? '')->placeholder('Vui lòng nhập địa chỉ route chuyển hướng ở frontend')->required('Vui lòng nhập địa chỉ route frontend');
        $field[] = Form::input('unique_auth', 'Mã định danh quyền', $formData['unique_auth'] ?? '')->placeholder('Để trống sẽ được hệ thống tự động tạo');
        $field[] = Form::frameInput('icon', 'Biểu tượng', $this->url(config('app.admin_prefix', 'admin') . '/widget.widgets/icon', ['fodder' => 'icon']), $formData['icon'] ?? '')->icon('md-add')->height('560px')->props(['footer' => false]);
        $field[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($formData['sort'] ?? 0))->precision(0);
        $field[] = Form::radio('auth_type', 'Loại', $formData['auth_type'] ?? 1)->options([['value' => 1, 'label' => 'Menu'], ['value' => 3, 'label' => 'Nút'], ['value' => 2, 'label' => 'API']]);
        $field[] = Form::radio('is_show', 'Trạng thái quyền', $formData['is_show'] ?? 1)->options([['value' => 1, 'label' => 'Bật'], ['value' => 0, 'label' => 'Tắt']]);
        $field[] = Form::radio('is_show_path', 'Hiển thị', $formData['is_show_path'] ?? 0)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        [$menuList, $data] = $this->getFormCascaderMenus((int)($formData['pid'] ?? 0), 3);
        $field[] = Form::cascader('menu_list', 'ID cấp cha', $data)->options($menuList)->filterable(true);
        return $field;
    }

    /**
     * Thêm biểu mẫu quyền
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createMenus()
    {
        return create_form('Thêm quyền', $this->createMenusForm(), $this->url('/setting/save'));
    }

    /**
     * Sửa menu quyền
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateMenus(int $id)
    {
        $menusInfo = $this->dao->get($id);
        if (!$menusInfo) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        return create_form('Sửa quyền', $this->createMenusForm($menusInfo->getData()), $this->url('/setting/update/' . $id), 'PUT');
    }

    /**
     * Lấy một dòng dữ liệu
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        $menusInfo = $this->dao->get($id);
        if (!$menusInfo) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        $menu = $menusInfo->getData();
        $menu['pid'] = (int)$menu['pid'];
        $menu['auth_type'] = (int)$menu['auth_type'];
        $menu['is_header'] = (int)$menu['is_header'];
        $menu['is_show'] = (int)$menu['is_show'];
        $menu['is_show_path'] = (int)$menu['is_show_path'];
        if (!$menu['path']) {
            [$menuList, $data] = $this->getFormCascaderMenus($menu['pid']);
            $menu['path'] = $data;
        } else {
            $menu['path'] = explode('/', $menu['path']);
            if (is_array($menu['path'])) {
                $menu['path'] = array_map(function ($item) {
                    return (int)$item;
                }, $menu['path']);
            }
        }
        return $menu;
    }

    /**
     * Xóa menu
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $ids = $this->dao->column(['pid' => $id], 'id');
        if (count($ids)) {
            foreach ($ids as $value) {
                $this->delete($value);
            }
        }
        return $this->dao->delete($id);
    }

    /**
     * Lấy quy cách vai trò cần thêm
     * @param $roles
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenus($roles, $check = []): array
    {
        $field = ['menu_name', 'pid', 'id'];
        $where = ['is_del' => 0, 'is_show_path' => 1];
        if (!$roles) {
            $menus = $this->dao->getMenusRoule($where, $field);
        } else {
            /** @var SystemRoleServices $service */
            $service = app()->make(SystemRoleServices::class);
            $roles = is_string($roles) ? explode(',', $roles) : $roles;
            $ids = $service->getRoleIds($roles);
            $menus = $this->dao->getMenusRoule(['rule' => $ids] + $where, $field);
        }
        foreach ($menus as &$item) {
            $item['checked'] = in_array($item['id'], $check);
        }
        return $this->tidyMenuTier(false, $menus);
    }

    /**
     * Kết hợp dữ liệu menu
     * @param bool $adminFilter
     * @param $menusList
     * @param int $pid
     * @param array $navList
     * @return array
     */
    public function tidyMenuTier(bool $adminFilter = false, $menusList, int $pid = 0, array $navList = []): array
    {
        foreach ($menusList as $k => $menu) {
            $menu = $menu->getData();
            $menu['title'] = $menu['menu_name'];
            unset($menu['menu_name']);
            if ($menu['pid'] == $pid) {
                unset($menusList[$k]);
                $menu['children'] = $this->tidyMenuTier($adminFilter, $menusList, $menu['id']);
//                if ($pid == 0 && !count($menu['children'])) continue;
                if ($menu['children']) $menu['expand'] = true;
                $navList[] = $menu;
            }
        }
        return $navList;
    }
}
