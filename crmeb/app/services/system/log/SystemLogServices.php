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

namespace app\services\system\log;


use app\dao\system\log\SystemLogDao;
use app\services\BaseServices;
use app\services\system\admin\SystemAdminServices;
use app\services\system\SystemMenusServices;
use app\services\system\SystemRouteServices;

/**
 * Nhật ký hệ thống
 * Class SystemLogServices
 * @package app\services\system\log
 * @method deleteLog() Xóa log định kỳ
 */
class SystemLogServices extends BaseServices
{

    /**
     * Phương thức khởi tạo
     * SystemLogServices constructor.
     * @param SystemLogDao $dao
     */
    public function __construct(SystemLogDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Ghi log truy cập
     * @param int $adminId
     * @param string $adminName
     * @param string $type
     * @return bool
     */
    public function recordAdminLog(int $adminId, string $adminName, string $type)
    {
        $request = app()->request;
        $module = app('http')->getName();
        $rule = trim(strtolower($request->rule()->getRule()));

        /** @var SystemMenusServices $service */
        $service = app()->make(SystemMenusServices::class);
        $data = [
            'method' => $module,
            'admin_id' => $adminId,
            'add_time' => time(),
            'admin_name' => $adminName,
            'path' => $rule,
            'page' => $service->getVisitName($rule) ?: 'Không xác định',
            'ip' => $request->ip(),
            'type' => $type
        ];
        if ($this->dao->save($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lấy danh sách log hệ thống
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLogList(array $where, int $level)
    {
        [$page, $limit] = $this->getPageValue();
        if (!$where['admin_id']) {
            /** @var SystemAdminServices $service */
            $service = app()->make(SystemAdminServices::class);
            $where['admin_id'] = $service->getAdminIds($level);
        }
        $routeArr = app()->make(SystemRouteServices::class)->getNameList('adminapi');
        $list = $this->dao->getLogList($where, $page, $limit);
        $count = $this->dao->count($where);
        foreach($list as &$item){
            $item['path_name'] = $routeArr[$item['path']] ?? 'Không xác định';
        }
        return compact('list', 'count');
    }
}
