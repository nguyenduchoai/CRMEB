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
namespace app\services\wechat;


use app\dao\wechat\WechatQrcodeDao;
use app\services\BaseServices;
use app\services\other\QrcodeServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\user\UserLabelRelationServices;
use app\services\user\UserLabelServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use app\services\other\UploadService;
use crmeb\services\app\WechatService;
use think\facade\Config;

/**
 * Class WechatQrcodeServices
 * @package app\services\wechat
 */
class WechatQrcodeServices extends BaseServices
{
    /**
     * WechatQrcodeServices constructor.
     * @param WechatQrcodeDao $dao
     */
    public function __construct(WechatQrcodeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách mã kênh
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function qrcodeList($where)
    {
        /** @var UserLabelServices $userLabel */
        $userLabel = app()->make(UserLabelServices::class);
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['y_follow'] = $item['y_follow'] ?? 0;
            $item['stop'] = $item['end_time'] ? ($item['end_time'] > time() ? 1 : -1) : 0;
            $item['label_name'] = $userLabel->getColumn([['id', 'in', $item['label_id']]], 'label_name');
            $item['end_time'] = date('Y-m-d H:i:s', $item['end_time']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy chi tiết
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function qrcodeInfo($id)
    {
        $info = $this->dao->get($id);
        if ($info) {
            $info = $info->toArray();
        } else {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $info['label_id'] = explode(',', $info['label_id']);
        foreach ($info['label_id'] as &$item) {
            $item = (int)$item;
        }
        /** @var UserLabelServices $userLabelServices */
        $userLabelServices = app()->make(UserLabelServices::class);
        $info['label_id'] = $userLabelServices->getLabelList(['ids' => $info['label_id']], ['id', 'label_name']);
        $info['time'] = $info['continue_time'];
        $info['content'] = json_decode($info['content'], true);
        $info['data'] = json_decode($info['data'], true);
        $info['avatar'] = $userService->value(['uid' => $info['uid']], 'avatar');
        return $info;
    }

    /**
     * Lưu dữ liệu mã kênh
     * @param $id
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveQrcode($id, $data)
    {
        $data['label_id'] = implode(',', $data['label_id']);
        $data['add_time'] = time();
        $data['continue_time'] = $data['time'];
        $data['end_time'] = $data['time'] ? $data['add_time'] + ($data['time'] * 86400) : 0;
        /** @var WechatReplyServices $replyServices */
        $replyServices = app()->make(WechatReplyServices::class);
        $type = $data['type'];
        if ($data['type'] == 'url') $type = 'text';
        $content = $data['content'];
        if ($data['type'] == 'news') $content = $data['content']['list'] ?? [];
        $method = 'tidy' . ucfirst($type);
        $data['data'] = $replyServices->{$method}($content, 0);
        $data['content'] = json_encode($data['content']);
        $data['data'] = json_encode($data['data']);
        if ($id) {
            $info = $this->dao->get($id);
            if (!$info) throw new AdminException('Dữ liệu không tồn tại');
            if ($info['image'] == '') $data['image'] = $this->getChannelCode($id);
            $info = $this->dao->update($id, $data);
            if (!$info) throw new AdminException('Lưu thất bại');
        } else {
            $info = $this->dao->save($data);
            $image = $this->getChannelCode($info['id']);
            $info = $this->dao->update($info['id'], ['image' => $image]);
            if (!$info) throw new AdminException('Lưu thất bại');
        }
        return true;
    }

    /**
     * Tạo mã kênh
     * @param int $id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChannelCode($id = 0)
    {
        /** @var SystemAttachmentServices $systemAttachment */
        $systemAttachment = app()->make(SystemAttachmentServices::class);
        $name = 'wechatqrcode_' . $id . '.jpg';
        $siteUrl = sys_config('site_url', '');
        $imageInfo = $systemAttachment->getInfo(['name' => $name]);
        if (!$imageInfo) {
            /** @var QrcodeServices $qrCode */
            $qrCode = app()->make(QrcodeServices::class);
            //OA WeChat
            $resCode = $qrCode->getForeverQrcode('wechatqrcode', $id);
            if ($resCode) {
                $res = ['res' => $resCode, 'id' => $resCode['id']];
            } else {
                $res = false;
            }
            if (!$res) throw new AdminException('Tạo mã QR thất bại');
            $imageInfo = $this->downloadImage($resCode['url'], $name);
            $systemAttachment->attachmentAdd($name, $imageInfo['size'], $imageInfo['type'], $imageInfo['att_dir'], $imageInfo['att_dir'], 1, $imageInfo['image_type'], time(), 1);
        }
        return strpos($imageInfo['att_dir'], 'http') === false ? $siteUrl . $imageInfo['att_dir'] : $imageInfo['att_dir'];
    }

    /**
     * Tải hình ảnh
     * @param string $url
     * @param string $name
     * @param int $type
     * @param int $timeout
     * @param int $w
     * @param int $h
     * @return string
     */
    public function downloadImage($url = '', $name = '', $type = 0, $timeout = 30, $w = 0, $h = 0)
    {
        if (!strlen(trim($url))) return '';
        if (!strlen(trim($name))) {
            //TODO lấy tên file cần tải xuống
            $downloadImageInfo = $this->getImageExtname($url);
            $ext = $downloadImageInfo['ext_name'];
            $name = $downloadImageInfo['file_name'];
            if (!strlen(trim($name))) return '';
        } else {
            $ext = $this->getImageExtname($name)['ext_name'];
        }
        if (!in_array($ext, Config::get('upload.fileExt'))) {
            throw new AdminException('Sai định dạng');
        }
        //TODO lấy phương thức dùng để tải file từ xa
        if ($type) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //TODO bỏ qua kiểm tra chứng chỉ
            if (stripos($url, "https://") !== FALSE) curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  //TODO kiểm tra trong chứng chỉ xem có thuật toán mã hóa SSL không
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('user-agent:' . $_SERVER['HTTP_USER_AGENT']));
            if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//TODO có thu thập trang sau khi chuyển hướng 301, 302 không
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            try {
                ob_start();
                readfile($url);
                $content = ob_get_contents();
                ob_end_clean();
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        $size = strlen(trim($content));
        if (!$content || $size <= 2) return 'Lấy luồng dữ liệu hình ảnh thất bại';
        $upload_type = sys_config('upload_type', 1);
        $upload = UploadService::init();
        if ($upload->to('attach/spread/agent')->setAuthThumb(false)->stream($content, $name) === false) {
            return $upload->getError();
        }
        $imageInfo = $upload->getUploadInfo();
        $data['att_dir'] = $imageInfo['dir'];
        $data['name'] = $imageInfo['name'];
        $data['size'] = $imageInfo['size'];
        $data['type'] = $imageInfo['type'];
        $data['image_type'] = $upload_type;
        $data['is_exists'] = false;
        return $data;
    }

    /**
     * Lấy phần mở rộng của ảnh sẽ tải xuống
     * @param string $url
     * @param string $ex
     * @return array|string[]
     */
    public function getImageExtname($url = '', $ex = 'jpg')
    {
        $_empty = ['file_name' => '', 'ext_name' => $ex];
        if (!$url) return $_empty;
        if (strpos($url, '?')) {
            $_tarr = explode('?', $url);
            $url = trim($_tarr[0]);
        }
        $arr = explode('.', $url);
        if (!is_array($arr) || count($arr) <= 1) return $_empty;
        $ext_name = trim($arr[count($arr) - 1]);
        $ext_name = !$ext_name ? $ex : $ext_name;
        return ['file_name' => md5($url) . '.' . $ext_name, 'ext_name' => $ext_name];
    }

    /**
     * Phương thức sau khi quét mã xong
     * @param $qrcodeInfo
     * @param $userInfo
     * @param $spreadInfo
     * @param int $isFollow
     * @return mixed
     */
    public function wechatQrcodeRecord($qrcodeInfo, $userInfo, $spreadInfo, $isFollow = 1)
    {
        $response = $this->transaction(function () use ($qrcodeInfo, $userInfo, $spreadInfo, $isFollow) {

            //Gắn nhãn người dùng
            /** @var UserLabelRelationServices $labelServices */
            $labelServices = app()->make(UserLabelRelationServices::class);
            foreach ($qrcodeInfo['label_id'] as $item) {
                $labelArr[] = [
                    'uid' => $userInfo['uid'],
                    'label_id' => $item['id'] ?? $item
                ];
            }
            $labelServices->saveAll($labelArr);

            //Tăng số lượt quét mã QR
            $this->dao->upFollowAndScan($qrcodeInfo['id'], $isFollow);

            //Ghi bản ghi quét mã
            /** @var WechatQrcodeRecordServices $recordServices */
            $recordServices = app()->make(WechatQrcodeRecordServices::class);
            $data['qid'] = $qrcodeInfo['id'];
            $data['uid'] = $userInfo['uid'];
            $data['is_follow'] = $isFollow;
            $data['add_time'] = time();
            $recordServices->save($data);

            //Nội dung trả lời tin nhắn
            return $this->replyDataByMessage($qrcodeInfo['type'], $qrcodeInfo['data']);
        });
        return $response;
    }

    /**
     * Gửi tin nhắn sau khi quét mã
     * @param $type
     * @param $data
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Voice
     */
    public function replyDataByMessage($type, $data)
    {
        if ($type == 'text') {
            return WechatService::textMessage($data['content']);
        } else if ($type == 'image') {
            return WechatService::imageMessage($data['media_id']);
        } else if ($type == 'news') {
            $title = $data['title'] ?? '';
            $image = $data['image'] ?? '';
            $description = $data['synopsis'] ?? '';
            $url = $data['url'] ?? '';
            return WechatService::newsMessage($title, $description, $url, $image);
        } else if ($type == 'url') {
            $title = $data['content'];
            $image = sys_config('h5_avatar');
            $description = $data['content'];
            $url = $data['content'];
            return WechatService::newsMessage($title, $description, $url, $image);
        } else if ($type == 'voice') {
            return WechatService::voiceMessage($data['media_id']);
        }
    }
}
