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

use app\jobs\notice\EnterpriseWechatJob;
use app\services\message\NoticeService;
use crmeb\services\HttpService;
use think\facade\Log;

/**
 * Gửi tin nhắn WeCom
 * Created by PhpStorm.
 * User: xurongyao <763569752@qq.com>
 * Date: 2021/9/22 1:23 PM
 */
class EnterpriseWechatService extends NoticeService
{
    /**
     * Kiểm tra có mở quyền không
     * @var bool
     */
    private $isOpen = true;

    /**
     * Có mở quyền không
     * @param string $mark
     * @return $this
     */
    public function isOpen(string $mark)
    {
        $this->isOpen = $this->noticeInfo['is_ent_wechat'] == 1 && $this->noticeInfo['url'] !== '';
        return $this;

    }

    /**
     * Gửi tin nhắn CSKH qua WeCom
     * @param $data
     */
    public function weComSend($data)
    {
        if ($this->noticeInfo['is_ent_wechat'] == 1 && $this->noticeInfo['url'] !== '') {
            $url = $this->noticeInfo['url'];
            $ent_wechat_text = $this->noticeInfo['ent_wechat_text'];
            try {
                $str = $ent_wechat_text;
                foreach ($data as $key => $item) {
                    $str = str_replace('{' . $key . '}', $item, $str);
                }
                $s = explode('\n', $str);
                $d = '';
                foreach ($s as $item) {
                    $d .= $item . "\n>";
                }
                $d = substr($d, 0, strlen($d) - 2);
                HttpService::postRequest($url, json_encode([
                    'msgtype' => 'markdown',
                    'markdown' => ['content' => $d]
                ]));
            } catch (\Throwable $e) {
                Log::error('Gửi tin nhắn nhóm WeCom thất bại, nguyên nhân:' . $e->getMessage());

            }
        }
    }
}
