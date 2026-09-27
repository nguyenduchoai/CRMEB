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
use app\model\service\StoreServiceRecord;

/**
 * Class StoreServiceRecordDao
 * @package app\dao\service
 */
class StoreServiceRecordDao extends BaseDao
{

    /**
     * StoreServiceRecordDao constructor.
     */
    public function __construct()
    {
        $this->deleteWeekRecord();
    }

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreServiceRecord::class;
    }

    /**
     * Xóa bản ghi khách vãng lai tuần trước
     */
    protected function deleteWeekRecord()
    {
        $this->search(['time' => 'last week', 'timeKey' => 'update_time', 'is_tourist' => 1])->delete();
    }

    /**
     *
     * @param array $where
     * @param array $data
     * @return \crmeb\basic\BaseModel
     */
    public function updateOnline(array $where, array $data)
    {
        return $this->getModel()->whereNotIn('to_uid', $where['notUid'])->update($data);
    }

    /**
     * Lấy danh sách người dùng chat với CSKH
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param array $with
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(array $where, int $page, int $limit, array $with = [])
    {
        return $this->search($where)->page($page, $limit)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->order('update_time desc')->select()->toArray();
    }

    /**
     * Tra cứu uid người dùng vừa chat gần nhất
     * @param array $where
     * @param string $key
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLatelyMsgUid(array $where, string $key)
    {
        return $this->search($where)->order('update_time DESC')->value($key);
    }
}
