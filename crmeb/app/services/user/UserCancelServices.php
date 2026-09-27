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
namespace app\services\user;

use app\dao\user\UserCancelDao;
use app\services\BaseServices;
use app\services\kefu\service\StoreServiceServices;
use app\services\wechat\WechatUserServices;
use crmeb\services\CacheService;

class UserCancelServices extends BaseServices
{
    protected $status = ['Chờ duyệt', 'Đã duyệt', 'Đã từ chối'];

    /**
     * UserExtractServices constructor.
     * @param UserCancelDao $dao
     */
    public function __construct(UserCancelDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Gửi yêu cầu hủy tài khoản người dùng
     * @param $userInfo
     * @return mixed
     */
    public function SetUserCancel($uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        /** @var WechatUserServices $wechatUserServices */
        $wechatUserServices = app()->make(WechatUserServices::class);
        /** @var StoreServiceServices $ServiceServices */
        $ServiceServices = app()->make(StoreServiceServices::class);
        $userServices->update($uid, ['is_del' => 1]);
        $userServices->update(['spread_uid' => $uid], ['spread_uid' => 0, 'spread_time' => 0]);
        $wechatUserServices->update(['uid' => $uid], ['is_del' => 1]);
        $ServiceServices->delete(['uid' => $uid]);

        $user = $userServices->getUserInfo($uid);

        //Sự kiện tùy chỉnh - Người dùng hủy tài khoản
        event('CustomEventListener', ['user_cancel', [
            'uid' => $uid,
            'nickname' => $user['nickname'],
            'phone' => $user['phone'],
            'add_time' => date('Y-m-d H:i:s', $user['add_time']),
            'cancel_time' => date('Y-m-d H:i:s'),
            'user_type' => $user['user_type'],
        ]]);

        return true;
    }

    /**
     * Lấy danh sách hủy tài khoản
     * @param $where
     * @return array
     */
    public function getCancelList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['up_time'] = $item['up_time'] != 0 ? date('Y-m-d H:i:s', $item['add_time']) : '';
            $item['status'] = $this->status[$item['status']];
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Ghi chú
     * @param $id
     * @param $mark
     * @return mixed
     */
    public function serMark($id, $mark)
    {
        return $this->dao->update($id, ['remark' => $mark]);
    }
}
