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

namespace app\services\yihaotong;


use app\dao\sms\SmsRecordDao;
use app\services\BaseServices;
/**
 * Lịch sử gửi SMS
 * Class SmsRecordServices
 * @package app\services\message\sms
 * @method save(array $data) Lưu dữ liệu
 * @method getColumn(array $where, ?string $field, ?string $key = '')
 * @method update(int $id, array $data, ?string $field = '')
 * @method getCodeNull
 */
class SmsRecordServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * SmsRecordServices constructor.
     * @param SmsRecordDao $dao
     */
    public function __construct(SmsRecordDao $dao)
    {
        $this->dao = $dao;
    }
}
