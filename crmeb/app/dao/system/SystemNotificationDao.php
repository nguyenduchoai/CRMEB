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
declare (strict_types=1);

namespace app\dao\system;

use app\dao\BaseDao;
use app\model\system\SystemNotification;

/**
 *
 * Class SystemUserLevelDao
 * @package app\dao\system
 */
class SystemNotificationDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemNotification::class;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/16
     */
    public function getList(array $where, string $field = '*', int $page = 0, $limit = 0)
    {
        return $this->getModel()->where($where)->field($field)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('id asc')->select()->toArray();
    }

    /**
     * Lấy tempid
     * @param $type
     * @return array
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/16
     */
    public function getTempId($type)
    {
        $whereField = 'is_' . $type;
        $field = $type . '_tempid';
        return array_unique($this->getModel()->where($whereField, 1)->where($field, '<>', '')->column($field));
    }

    /**
     * Lấy tempkey
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/16
     */
    public function getTempKey($type)
    {
        $whereField = 'is_' . $type;
        $field = $type . '_tempkey';
        $content = $type . '_content,name';
        return $this->getModel()->where($whereField, 1)->column($content, $field);
    }

}
