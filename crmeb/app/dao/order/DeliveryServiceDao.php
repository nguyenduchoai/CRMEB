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

namespace app\dao\order;

use app\dao\BaseDao;
use app\model\order\DeliveryService;

/**DAO vận chuyển
 * Class DeliveryServiceDao
 * @package app\dao\service
 */
class DeliveryServiceDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return DeliveryService::class;
    }

    /**
     * Lấy danh sách nhân viên giao hàng
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(array $where, int $page, int $limit)
    {
        return $this->search($where, false)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->when(isset($where['noId']), function ($query) use ($where) {
            $query->where('id', '<>', $where['noId']);
        })->order('id DESC')->select()->toArray();
    }

    /**Lấy danh sách tất cả người giao hàng
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(int $page, int $limit)
    {
        $list = $this->getModel()->where(['status' => 1])->order('id DESC')->limit($page, $limit)->select()->toArray();
        foreach ($list as &$item) {
            $item['wx_name'] = $item['nickname'];
        }
        $count = $this->getModel()->where(['status' => 1])->count();
        return [$list, $count];
    }


}
