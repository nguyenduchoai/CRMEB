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

namespace app\services\message\notice;

use app\services\message\NoticeService;
use app\services\kefu\service\StoreServiceServices;
use app\services\message\MessageSystemServices;
use think\facade\Log;

/**
 * Danh sách tin nhắn SMS đã gửi
 * Created by PhpStorm.
 * User: xurongyao <763569752@qq.com>
 * Date: 2021/9/22 1:23 PM
 */
class SystemMsgService extends NoticeService
{
    /**
     * Gửi tin nhắn
     * @param int $uid
     * @param $data
     * @return bool|void
     */
    public function sendMsg(int $uid, $data)
    {
        try {
            if ($this->noticeInfo['is_system'] == 1) {
                $title = $this->noticeInfo['system_title'];
                $str = $this->noticeInfo['system_text'];
                foreach ($data as $key => $item) {
                    $str = str_replace('{' . $key . '}', $item, $str);
                    $title = str_replace('{' . $key . '}', $item, $title);
                }
                $sdata = [];
                $sdata['mark'] = $this->noticeInfo['mark'];
                $sdata['uid'] = $uid;
                $sdata['content'] = $str;
                $sdata['title'] = $title;
                $sdata['type'] = 1;
                $sdata['add_time'] = time();
                $sdata['data'] = json_encode($data);
                /** @var MessageSystemServices $MessageSystemServices */
                $MessageSystemServices = app()->make(MessageSystemServices::class);
                $MessageSystemServices->save($sdata);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return true;
        }
    }

    /**
     * Gửi thông báo nội bộ cho nhân viên CSKH
     * @param $data
     * @return bool|void
     */
    public function kefuSystemSend($data)
    {
        /** @var MessageSystemServices $MessageSystemServices */
        $MessageSystemServices = app()->make(MessageSystemServices::class);
        /** @var StoreServiceServices $StoreServiceServices */
        $StoreServiceServices = app()->make(StoreServiceServices::class);
        $adminList = $StoreServiceServices->getStoreServiceOrderNotice();
        try {
            if ($this->noticeInfo['is_system'] == 1) {
                $save = [];
                $title = $this->noticeInfo['system_title'];
                $str = $this->noticeInfo['system_text'];
                foreach ($data as $k => $val) {
                    $str = str_replace('{' . $k . '}', $val, $str);
                    $title = str_replace('{' . $k . '}', $val, $title);
                }
                foreach ($adminList as $key => $item) {
                    $save[$key]['mark'] = $this->noticeInfo['mark'];
                    $save[$key]['uid'] = $item['uid'];
                    $save[$key]['content'] = $str;
                    $save[$key]['title'] = $title;
                    $save[$key]['type'] = 2;
                    $save[$key]['add_time'] = time();
                    $save[$key]['data'] = json_encode($data);
                }
                $MessageSystemServices->saveAll($save);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return true;
        }
    }
}
