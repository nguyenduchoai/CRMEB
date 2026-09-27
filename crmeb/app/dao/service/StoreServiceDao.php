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

namespace app\dao\service;

use app\dao\BaseDao;
use app\model\service\StoreService;

/**
 * DAO chăm sóc khách hàng
 * Class StoreServiceDao
 * @package app\dao\service
 */
class StoreServiceDao extends BaseDao
{

    /**
     * Người dùng không tồn tại thì cấm luôn
     * @param array $uids
     * @return bool|\crmeb\basic\BaseModel
     */
    public function deleteNonExistentService(array $uids = [])
    {
        if ($uids) {
            return $this->getModel()->whereIn('uid', $uids)->update(['status' => 0]);
        } else {
            return true;
        }
    }

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreService::class;
    }

    /**
     * Lấy danh sách nhân viên CSKH
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
        return $this->search($where, false)->with('user')->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->when(isset($where['noId']), function ($query) use ($where) {
            $query->whereNotIn('uid', $where['noId']);
        })->order('id DESC')->field('id,uid,avatar,nickname as wx_name,status,add_time,phone')->select()->toArray();
    }

    /**
     * Lấy nhân viên CSKH nhận thông báo
     * @return array
     */
    public function getStoreServiceOrderNotice(int $customer = 0)
    {
        return $this->getModel()->where(['status' => 1, 'notify' => 1])->when($customer, function ($query) use ($customer) {
            $query->where('customer', $customer);
        })->field(['nickname', 'phone', 'uid', 'customer'])->select()->toArray();
    }

    /**
     *
     * @param array $where
     * @param array $data
     * @return \crmeb\basic\BaseModel
     */
    public function updateOnline(array $where, array $data)
    {
        return $this->getModel()->whereNotIn('uid', $where['notUid'])->update($data);
    }

    /**
     * Thống kê số lượng
     * @param array $where
     * @param bool $search
     * @return int
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/05/10
     */
    public function count(array $where = [], bool $search = true)
    {
        return $this->search($where, false)->when(isset($where['noId']), function ($query) use ($where) {
            $query->whereNotIn('uid', $where['noId']);
        })->count();
    }
}
