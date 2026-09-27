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

namespace app\dao\sms;

use app\dao\BaseDao;
use app\model\sms\SmsRecord;

/**
 * Lịch sử gửi SMS
 * Class SmsRecordDao
 * @package app\dao\sms
 */
class SmsRecordDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    public function setModel(): string
    {
        return SmsRecord::class;
    }

    /**
     * Lịch sử gửi SMS
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRecordList(array $where, int $page, int $limit)
    {
        return $this->search($where)->page($page, $limit)->order('add_time DESC')->select()->toArray();
    }

    /**
     * Lấy 20 bản ghi SMS không có trạng thái từ 10 phút trước
     * @return array
     */
    public function getCodeNull()
    {
        return $this->getModel()->where([
            ['resultcode', '=', null],
            ['add_time', '<=', time() - 600],
            ['record_id', '>', 0]
        ])->limit(20)->column('record_id');
    }

}
