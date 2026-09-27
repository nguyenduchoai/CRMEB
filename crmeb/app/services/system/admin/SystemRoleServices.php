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

namespace app\services\system\admin;

use app\dao\system\admin\SystemRoleDao;

use app\Request;
use app\services\BaseServices;
use app\services\system\SystemMenusServices;
use crmeb\exceptions\AuthException;
use crmeb\services\CacheService;

/**
 * Class SystemRoleServices
 * @package app\services\system\admin
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method save(array $data) Lưu dữ liệu
 * @method get(int $id, ?array $field = []) Lấy dữ liệu
 * @method delete(int $id, ?string $key = null) Xóa dữ liệu
 */
class SystemRoleServices extends BaseServices
{

    /**
     * Tiền tố cache quyền của quản trị viên hiện tại
     */
    const ADMIN_RULES_LEVEL = 'Admin_rules_level_';

    /**
     * SystemRoleServices constructor.
     * @param SystemRoleDao $dao
     */
    public function __construct(SystemRoleDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy quyền
     * @return mixed
     */
    public function getRoleArray(array $where = [], string $field = '', string $key = '')
    {
        return $this->dao->getRoule($where, $field, $key);
    }

    /**
     * Lấy danh sách tên quyền cần cho biểu mẫu
     * @param int $level
     * @return array
     */
    public function getRoleFormSelect(int $level)
    {
        $list = $this->getRoleArray(['level' => $level, 'status' => 1]);
        $options = [];
        foreach ($list as $id => $roleName) {
            $options[] = ['label' => $roleName, 'value' => $id];
        }
        return $options;
    }

    /**
     * Danh sách quản lý vai trò
     * @param array $where
     * @return array
     */
    public function getRoleList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getRouleList($where, $page, $limit);
        $count = $this->dao->count($where);
        /** @var SystemMenusServices $service */
        $service = app()->make(SystemMenusServices::class);
        foreach ($list as &$item) {
            $item['rules'] = implode(',', array_merge($service->column(['id' => $item['rules']], 'menu_name', 'id')));
        }
        return compact('count', 'list');
    }

    /**
     * Xác thực quyền ở admin
     * @param Request $request
     * @return bool|void
     * @throws \throwable
     */
    public function verifyAuth(Request $request)
    {
        // Lấy interface hiện tại và loại interface
        $rule = trim(strtolower($request->rule()->getRule()));
        $method = trim(strtolower($request->method()));

        // Cho qua khi interface thuộc một trong hai loại sau
        if (in_array($rule, ['setting/admin/logout', 'menuslist'])) {
            return true;
        }

        // Lấy tất cả loại interface và interface tương ứng
        $allAuth = CacheService::remember('all_auth', function () {
            /** @var SystemMenusServices $menusService */
            $menusService = app()->make(SystemMenusServices::class);
            $allList = $menusService->getColumn([['api_url', '<>', ''], ['auth_type', '=', 2]], 'api_url,methods');
            $allAuth = [];
            foreach ($allList as $item) {
                $allAuth[trim(strtolower($item['methods']))][] = trim(strtolower(str_replace(' ', '', $item['api_url'])));
            }
            return $allAuth;
        });

        // Cho qua khi chưa thêm menu quyền
        if (!in_array($rule, $allAuth[$method])) return true;

        // Nếu là interface crud thì cho qua
        if (strpos($rule, 'crud/') === 0) return true;

        // Lấy danh sách quyền interface của quản trị viên, cho qua nếu tồn tại
        $auth = $this->getRolesByAuth($request->adminInfo()['roles'], 2);
        if (isset($auth[$method]) && in_array($rule, $auth[$method])) {
            return true;
        } else {
            return true;
        }
    }

    /**
     * Lấy quyền chỉ định
     * @param array $rules
     * @param int $type
     * @param string $cachePrefix
     * @return array|mixed
     * @throws \throwable
     */
    public function getRolesByAuth(array $rules, int $type = 1, string $cachePrefix = self::ADMIN_RULES_LEVEL)
    {
        if (empty($rules)) return [];
        $cacheName = md5($cachePrefix . '_' . $type . '_' . implode('_', $rules));
        return CacheService::remember($cacheName, function () use ($rules, $type) {
            /** @var SystemMenusServices $menusService */
            $menusService = app()->make(SystemMenusServices::class);
            $authList = $menusService->getColumn([['id', 'IN', $this->getRoleIds($rules)], ['auth_type', '=', $type]], 'api_url,methods');
            $rolesAuth = [];
            foreach ($authList as $item) {
                $rolesAuth[trim(strtolower($item['methods']))][] = trim(strtolower(str_replace(' ', '', $item['api_url'])));
            }
            return $rolesAuth;
        });
    }

    /**
     * Lấy ID quyền
     * @param array $rules
     * @return array
     */
    public function getRoleIds(array $rules)
    {
        $rules = $this->dao->getColumn([['id', 'IN', $rules], ['status', '=', '1']], 'rules', 'id');
        return array_unique(explode(',', implode(',', $rules)));
    }
}
