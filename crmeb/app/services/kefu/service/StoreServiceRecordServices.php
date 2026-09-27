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

namespace app\services\kefu\service;


use app\dao\service\StoreServiceRecordDao;
use app\services\BaseServices;
use crmeb\utils\Str;
use think\Model;

/**
 * Class StoreServiceRecordServices
 * @package app\services\kefu\service
 * @method array|Model|null getLatelyMsgUid(array $where, string $key) Tra cứu uid người dùng vừa chat gần nhất
 */
class StoreServiceRecordServices extends BaseServices
{

    /**
     * StoreServiceRecordServices constructor.
     * @param StoreServiceRecordDao $dao
     */
    public function __construct(StoreServiceRecordDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách chat của người dùng với CSKH
     * @param int $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(int $userId, string $nickname, int $isTourist = 0)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getServiceList(['user_id' => $userId, 'title' => $nickname, 'is_tourist' => $isTourist], $page, $limit, ['user', 'service']);
        foreach ($list as &$item) {
            if ($item['message_type'] == 1) {
                $item['message'] = Str::substrUTf8($item['message'], '10', 'UTF-8', '');
            }
            if (isset($item['kefu_nickname']) && $item['kefu_nickname']) {
                $item['nickname'] = $item['kefu_nickname'];
            }
            if (isset($item['wx_nickname']) && $item['wx_nickname'] && !$item['nickname']) {
                $item['nickname'] = $item['wx_nickname'];
            }
            if (isset($item['kefu_avatar']) && $item['kefu_avatar']) {
                $item['avatar'] = $item['kefu_avatar'];
            }
            if (isset($item['wx_avatar']) && $item['wx_avatar'] && !$item['avatar']) {
                $item['avatar'] = $item['wx_avatar'];
            }
            $item['_update_time'] = date('Y-m-d H:i', $item['update_time']);
        }
        return $list;
    }

    /**
     * Cập nhật thông tin người dùng CSKH
     * @param int $uid
     * @param array $data
     * @return mixed
     */
    public function updateRecord(array $where, array $data)
    {
        return $this->dao->update($where, $data);
    }

    /**
     * Ghi dữ liệu người liên quan đến chat
     * @param int $uid
     * @param int $toUid
     * @param string $message
     * @param int $type
     * @param int $messageType
     * @param int $num
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveRecord(int $uid, int $toUid, string $message, int $type, int $messageType, int $num, int $isTourist = 0, string $nickname = '', string $avatar = '')
    {
        $info = $this->dao->get(['user_id' => $toUid, 'to_uid' => $uid]);
        if ($info) {
            $info->type = $type;
            $info->message = $message;
            $info->message_type = $messageType;
            $info->update_time = time();
            $info->mssage_num = $num;
            if ($avatar) $info->avatar = $avatar;
            if ($nickname) $info->nickname = $nickname;
            $info->save();
            $this->dao->update(['user_id' => $uid, 'to_uid' => $toUid], ['message' => $message, 'message_type' => $messageType]);
            return $info->toArray();
        } else {
            return $this->dao->save([
                'user_id' => $toUid,
                'to_uid' => $uid,
                'type' => $type,
                'message' => $message,
                'avatar' => $avatar,
                'nickname' => $nickname,
                'message_type' => $messageType,
                'mssage_num' => $num,
                'add_time' => time(),
                'update_time' => time(),
                'is_tourist' => $isTourist
            ])->toArray();
        }
    }
}
