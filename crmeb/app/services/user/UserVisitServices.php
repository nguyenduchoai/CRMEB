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

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserVisitDao;
use think\facade\Log;

/**
 *
 * Class UserVisitServices
 * @package app\services\user
 * @method count(array $where)
 * @method getDistinctCount(array $where, $field, ?bool $search = true)
 * @method sum(array $where, string $field)
 * @method getTrendData($time, $type, $timeType, $str)
 * @method getRegion($time, $channelType)
 * @method int groupCount(array $where, string $group = 'uid') Lấy số lượng bản ghi theo nhóm
 */
class UserVisitServices extends BaseServices
{

    /**
     * UserVisitServices constructor.
     * @param UserVisitDao $dao
     */
    public function __construct(UserVisitDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Sau khi đăng nhập, ghi bản ghi truy cập
     * @param array|object $user
     * @return mixed
     */
    public function loginSaveVisit($user)
    {
        try {
            $data = [
                'url' => '/pages/index/index',
                'uid' => $user['uid'] ?? 0,
                'ip' => request()->ip(),
                'add_time' => time(),
                'province' => $user['province'] ?? '',
                'channel_type' => $user['user_type'] ?? 'h5'
            ];
            if (!$data['uid']) {
                return false;
            }
            return $this->dao->save($data);
        } catch (\Throwable $e) {
            Log::error('Lỗi ghi nhật ký truy cập khi đăng nhập, lý do lỗi:' . $e->getMessage());
        }
    }

}
