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

namespace app\dao\system\config;

use app\dao\BaseDao;
use app\model\system\config\SystemConfig;

/**
 * Cấu hình hệ thống
 * Class SystemConfigDao
 * @package app\dao\system\config
 */
class SystemConfigDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemConfig::class;
    }

    /**
     * Lấy một cấu hình hệ thống
     * @param string $configNmae
     * @return mixed
     * @throws \ReflectionException
     */
    public function getConfigValue(string $configNmae)
    {
        return $this->search(['menu_name' => $configNmae])->value('value');
    }

    /**
     * Lấy tất cả cấu hình
     * @param array $configName
     * @return array
     * @throws \ReflectionException
     */
    public function getConfigAll(array $configName = [])
    {
        if ($configName) {
            return $this->search(['menu_name' => $configName])->column('value', 'menu_name');
        } else {
            return $this->getModel()->column('value', 'menu_name');
        }
    }

    /**
     * Lấy danh sách cấu hình có phân trang
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfigList(array $where, int $page, int $limit)
    {
        return $this->search($where)->page($page, $limit)->order('sort desc,id asc')->select()->toArray();
    }

    /**
     * Lấy danh sách cấu hình thuộc một số danh mục cấu hình
     * @param int $tabId
     * @param int $status
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfigTabAllList(int $tabId, int $status = 1)
    {
        $where['tab_id'] = $tabId;
        if ($status == 1) $where['status'] = $status;
        return $this->search($where)->order('sort desc,id ASC')->select()->toArray();
    }

    /**
     * Lấy loại tải lên trong cấu hình tải lên
     * @param string $configName
     * @return array
     * @throws \ReflectionException
     */
    public function getUploadTypeList(string $configName)
    {
        return $this->search(['menu_name' => $configName])->column('upload_type', 'type');
    }
}
