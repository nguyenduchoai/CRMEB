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

namespace app\dao\other;


use app\dao\BaseDao;
use app\model\other\TemplateMessage;

/**
 * Tin nhắn mẫu
 * Class TemplateMessageDao
 * @package app\dao\other
 */
class TemplateMessageDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return TemplateMessage::class;
    }

    /**
     * Lấy danh sách tin nhắn mẫu
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTemplateList(array $where, int $page, int $limit)
    {

        return $this->getModel()->when(isset($where['name']) && $where['name']!='', function ($query) use ($where) {
            $query->where('name','LIKE', "%$where[name]%");
        })->when(isset($where['status']) && $where['status']!='', function ($query) use ($where) {
            $query->where('status',$where['status']);
        })->where('type',$where['type'])->page($page, $limit)->select()->toArray();
    }

}
