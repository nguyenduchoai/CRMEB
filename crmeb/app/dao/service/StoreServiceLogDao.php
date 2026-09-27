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

namespace app\dao\service;

use app\dao\BaseDao;
use app\model\service\StoreServiceLog;

/**
 * DAO lịch sử chat CSKH
 * Class StoreServiceLogDao
 * @package app\dao\service
 */
class StoreServiceLogDao extends BaseDao
{

    /**
     * StoreServiceLogDao constructor.
     */
    public function __construct()
    {
        //Xóa lịch sử chat năm ngoái
//        $this->removeChat();
//        $this->removeYesterDayChat();
    }

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreServiceLog::class;
    }

    /**
     * Lấy uid và to_uid trong lịch sử chat
     * @param int $uid
     * @return mixed
     */
    public function getServiceUserUids(int $uid)
    {
        return $this->search(['uid' => $uid])->group('uid,to_uid')->field(['uid', 'to_uid'])->select()->toArray();
    }

    /**
     * Lấy lịch sử chat có phân trang
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(array $where, int $page, int $limit, array $field = ['*'])
    {
        return $this->search($where)->with('user')->field($field)->order('add_time DESC')->page($page, $limit)->select()->toArray();
    }

    /**
     * Lấy lịch sử chat khi lật trang trước
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param bool $isUp
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatList(array $where, int $limit = 20, int $upperId = 0)
    {
        return $this->search($where)->when($upperId, function ($query) use ($upperId, $limit) {
            $query->where('id', '<', $upperId)->limit($limit)->order('id DESC');
        })->when(!$upperId, function ($query) use ($limit) {
            $query->limit($limit)->order('id DESC');
        })->with(['user'])->select()->toArray();
    }

    /**
     * Xóa lịch sử chat năm ngoái
     * @return bool
     */
    public function removeChat()
    {
        return $this->search(['time' => 'last year'])->delete();
    }

    /**
     * Xóa lịch sử chat của khách vãng lai tuần trước
     * @return bool
     */
    public function removeYesterDayChat()
    {
        return $this->search(['time' => 'last week', 'is_tourist' => 1])->delete();
    }


    /**
     * Lấy số lượng theo điều kiện
     * @param array $where
     * @return int
     */
    public function whereByCount(array $where)
    {
        return $this->search(['uid' => $where['uid']])->order('id DESC')->where('add_time', '<', time() - 300)->count();
    }

    /**
     * Lấy số lượng tin nhắn chưa đọc
     * @param array $where
     * @return int
     */
    public function getMessageNum(array $where)
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * Tìm kiếm lịch sử chat
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMessageList(array $where)
    {
        return $this->search(['chat' => $where['chat']])->when(isset($where['add_time']) && $where['add_time'], function ($query) use ($where) {
            $query->where('add_time', '>', $where['add_time']);
        })->select()->toArray();
    }
}
