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

namespace app\services\message;

use app\services\BaseServices;
use crmeb\services\CacheService;

/**
 * Class services thông báo nội bộ
 * Class MessageSystemServices
 */
class NoticeService extends BaseServices
{
    protected $noticeInfo;
    protected $event;

    /**
     * Cài đặt
     * @param string $event
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setEvent(string $event)
    {
        if ($this->event != $event) {
            /** @var SystemNotificationServices $services */
            $services = app()->make(SystemNotificationServices::class);
            $noticeInfo = $services->getOneNotce(['mark' => $event]);
            $this->noticeInfo = $noticeInfo ? $noticeInfo->toArray() : [];
            $this->event = $event;
        }
        return $this;
    }
}
