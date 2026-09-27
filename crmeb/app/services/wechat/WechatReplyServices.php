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

namespace app\services\wechat;

use app\services\BaseServices;
use app\dao\wechat\WechatReplyDao;
use app\services\kefu\KefuServices;
use crmeb\exceptions\AdminException;
use crmeb\services\app\WechatService;
use crmeb\services\FormBuilder;

/**
 * Class UserWechatuserServices
 * @package app\services\user
 * @method delete($id, ?string $key = null)  Xóa
 * @method update($id, array $data, ?string $key = null) Cập nhật dữ liệu
 */
class WechatReplyServices extends BaseServices
{

    /**
     * UserWechatuserServices constructor.
     * @param WechatReplyDao $dao
     */
    public function __construct(WechatReplyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Loại thông báo
     * @return string[]
     */
    public function replyType()
    {
        return ['text', 'image', 'news', 'voice'];
    }

    /**
     * Truy vấn tổng số tùy chỉnh đơn giản
     * @param array $where
     * @return int
     */
    public function getCount(array $where): int
    {
        return $this->dao->getCount($where);
    }

    /**
     * Danh sách tìm kiếm theo điều kiện phức tạp
     * @param array $where
     * @param string $field
     * @return array
     */
    public function getWhereUserList(array $where, string $field): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getListByModel($where, $field, $page, $limit);
        $count = $this->dao->getCountByWhere($where);
        return [$list, $count];
    }

    /**
     * Trả lời khi theo dõi
     * @param string $key
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDataByKey(string $key)
    {
        /** @var WechatKeyServices $services */
        $services = app()->make(WechatKeyServices::class);
        $data = $services->getOne(['keys' => $key]);
        $resdata = $this->dao->getOne(['id' => $data['reply_id'] ?? 0]);
        $resdata['data'] = isset($resdata['data']) ? json_decode($resdata['data'], true) : [];
        $resdata['key'] = $key;
        return $resdata;
    }

    /**
     * Lưu từ khóa
     * @param $data
     * @param $id
     * @param $key
     * @param $type
     * @param int $status
     * @return bool
     */
    public function redact($data, $id, $key, $type, $status = 1)
    {
        $method = 'tidy' . ucfirst($type);
        if ($id == 'undefined') {
            $id = 0;
        }
        if (isset($data['content']) && $data['content'] == '' && isset($data['src']) && $data['src'] == '') $data = $data['list'][0] ?? [];
        try {
            $res = $this->{$method}($data, $id);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
        if (!$res) return false;
        $arr = [];
        /** @var WechatKeyServices $keyServices */
        $keyServices = app()->make(WechatKeyServices::class);
        $count = $this->dao->getCount(['id' => $id]);
        if ($count) {
            $keyServices->delete($id, 'reply_id');
            $insertData = explode(',', $key);
            foreach ($insertData as $k => $v) {
                $arr[$k]['keys'] = $v;
                $arr[$k]['reply_id'] = $id;
            }
            $res = $this->dao->update($id, ['type' => $type, 'data' => json_encode($res), 'status' => $status], 'id');
            $res1 = $keyServices->saveAll($arr);
            if (!$res || !$res1) {
                throw new AdminException('Lưu thất bại');
            }
        } else {
            $reply = $this->dao->save([
                'type' => $type,
                'data' => json_encode($res),
                'status' => $status,
            ]);
            $insertData = explode(',', $key);
            foreach ($insertData as $k => $v) {
                $arr[$k]['keys'] = $v;
                $arr[$k]['reply_id'] = $reply->id;
            }
            $res = $keyServices->saveAll($arr);
            if (!$res) throw new AdminException('Lưu thất bại');
        }
        return true;
    }

    /**
     * Lấy tất cả từ khóa
     * @param array $where
     * @return array
     */
    public function getKeyAll($where = array())
    {
        /** @var WechatReplyKeyServices $replyKeyServices */
        $replyKeyServices = app()->make(WechatReplyKeyServices::class);
        $data = $replyKeyServices->getReplyKeyAll($where);
        /** @var WechatKeyServices $keyServices */
        $keyServices = app()->make(WechatKeyServices::class);
        foreach ($data['list'] as &$item) {
            if ($item['data']) $item['data'] = json_decode($item['data'], true);
            switch ($item['type']) {
                case 'text':
                    $item['typeName'] = 'Tin nhắn văn bản';
                    break;
                case  'image':
                    $item['typeName'] = 'Tin nhắn hình ảnh';
                    break;
                case 'news':
                    $item['typeName'] = 'Tin nhắn hình ảnh và văn bản';
                    break;
                case 'voice':
                    $item['typeName'] = 'Tin nhắn thoại';
                    break;
            }
            $keys = $keyServices->getColumn(['reply_id' => $item['id']], 'keys');
            $item['key'] = implode(',', $keys);
        }
        return $data;
    }

    /**
     * Truy vấn một bản ghi
     * @param $key
     * @return array|null|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getKeyInfo(int $id)
    {
        $resdata = $this->dao->getOne(['id' => $id]);
        /** @var WechatKeyServices $keyServices */
        $keyServices = app()->make(WechatKeyServices::class);
        $keys = $keyServices->getColumn(['reply_id' => $resdata['id']], 'keys');
        $resdata['data'] = $resdata['data'] ? json_decode($resdata['data'], true) : [];
        $resdata['key'] = implode(',', $keys);
        return $resdata;
    }

    /**
     * Xử lý tin nhắn dạng văn bản nhập vào
     * @param $data
     * @param $key
     * @return array|bool
     */
    public function tidyText($data, $id)
    {
        $res = [];
        if (!isset($data['content']) || $data['content'] == '') {
            throw new AdminException('Vui lòng nhập nội dung tin nhắn trả lời');
        }
        $res['content'] = $data['content'];
        return $res;
    }

    /**
     * Xử lý tài nguyên hình ảnh
     * @param $data
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function tidyImage($data, $id)
    {
        if (!isset($data['src']) || $data['src'] == '') {
            throw new AdminException('Vui lòng tải lên hình ảnh trả lời');
        }
        $reply = $this->dao->get((int)$id);
        if ($reply) $reply['data'] = json_decode($reply['data'], true);
        if ($reply && isset($reply['data']['src']) && $reply['data']['src'] == $data['src']) {
            $res = $reply['data'];
        } else {
            $res = [];
            //TODO Chuyển ảnh sang media
            $res['src'] = $data['src'];
            try {
                $material = WechatService::materialService()->uploadImage(url_to_path($data['src']));
            } catch (\Throwable $e) {
                throw new AdminException(WechatService::getMessage($e->getMessage()));
            }
            $res['media_id'] = $material->media_id;
            $dataEvent = ['type' => 'image', 'media_id' => $material->media_id, 'path' => $res['src'], 'url' => $material->url];
            /** @var WechatMediaServices $mateServices */
            $mateServices = app()->make(WechatMediaServices::class);
            $mateServices->save($dataEvent);
        }
        return $res;
    }

    /**
     * Xử lý tài nguyên âm thanh
     * @param $data
     * @param $id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function tidyVoice($data, $id)
    {
        if (!isset($data['src']) || $data['src'] == '') {
            throw new AdminException('Vui lòng tải lên âm thanh trả lời');
        }
        $reply = $this->dao->get((int)$id);
        if ($reply) $reply['data'] = json_decode($reply['data'], true);
        if ($reply && isset($reply['data']['src']) && $reply['data']['src'] == $data['src']) {
            $res = $reply['data'];
        } else {
            $res = [];
            //TODO Chuyển âm thanh sang media
            $res['src'] = $data['src'];
            try {
                $material = WechatService::materialService()->uploadVoice(url_to_path($data['src']));
            } catch (\Throwable $e) {
                throw new AdminException(WechatService::getMessage($e->getMessage()));
            }
            $res['media_id'] = $material->media_id;
            $dataEvent = ['media_id' => $material->media_id, 'path' => $res['src'], 'type' => 'voice'];
            /** @var WechatMediaServices $mateServices */
            $mateServices = app()->make(WechatMediaServices::class);
            $mateServices->save($dataEvent);
        }
        return $res;
    }

    /**
     * Xử lý tài nguyên bài viết ảnh-văn
     * @param $data
     * @param $id
     * @return bool
     */
    public function tidyNews($data, $id = 0)
    {
//        if ($id != 0) {
//            $data = $data['list'][0];
//        }
        if (!count($data)) {
            throw new AdminException('Vui lòng chọn tin bài');
        }
        $siteUrl = sys_config('site_url');
        if (empty($data['url'])) $data['url'] = $siteUrl . '/pages/extension/news_details/index?id=' . $data['id'];
        if (count($data['image_input'])) $data['image'] = $data['image_input'][0];
        return $data;
    }

    /**
     * Lấy từ khóa
     * @param $key
     * @param string $openId
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Transfer|\EasyWeChat\Message\Voice
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reply($key, string $openId = '')
    {
        $res = $this->dao->getKey($key);
        if (empty($res)) {
            /** @var KefuServices $services */
            $services = app()->make(KefuServices::class);
            $services->replyTransferService($key, $openId);
            return WechatService::transfer();
        }
        return $this->replyDataByMessage($res->toArray());
    }

    /**
     * Theo nội dung từ khóa, trả về nội dung tương ứng
     * @param array $res
     * @return array|\EasyWeChat\Message\Image|\EasyWeChat\Message\News|\EasyWeChat\Message\Text|\EasyWeChat\Message\Voice
     */
    public function replyDataByMessage(array $res)
    {
        $res['data'] = json_decode($res['data'], true);
        if ($res['type'] == 'text') {
            return WechatService::textMessage($res['data']['content']);
        } else if ($res['type'] == 'image') {
            return WechatService::imageMessage($res['data']['media_id']);
        } else if ($res['type'] == 'news') {
            $title = $res['data']['title'] ?? '';
            $image = $res['data']['image'] ?? '';
            $description = $res['data']['synopsis'] ?? '';
            $url = $res['data']['url'] ?? '';
            return WechatService::newsMessage($title, $description, $url, $image);
        } else if ($res['type'] == 'voice') {
            return WechatService::voiceMessage($res['data']['media_id']);
        }
    }

    /**
     * Biểu mẫu thêm/sửa tự động trả lời CSKH
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplyForm($id = 0)
    {
        $replyInfo = [];
        if ($id) {
            $replyInfo = $this->dao->get($id, ['*'], ['kefuKey']);
            $replyInfo['data'] = json_decode($replyInfo['data'], true);
        }
        $field[] = FormBuilder::input('keys', 'Từ khóa', $replyInfo['keys'] ?? '')->col(24)->required();
        $field[] = FormBuilder::radio('type', 'Loại trả lời', (string)($replyInfo['type'] ?? 'text'))->appendControl('text', [
            FormBuilder::input('data', 'Nội dung trả lời', (string)($replyInfo['data']['content'] ?? ''))->required(),
        ])->appendControl('image', [
            FormBuilder::frameImage('data', 'Hình ảnh trả lời', $this->url(config('app.admin_prefix', 'admin') . '/widget.images/index', ['fodder' => 'data'], true), (string)($replyInfo['data']['src'] ?? ''))->icon('el-icon-picture-outline')->width('950px')->height('560px')->Props(['footer' => false]),
        ])->options([['label' => 'Tin nhắn văn bản', 'value' => 'text'], ['label' => 'Tin nhắn hình ảnh', 'value' => 'image']]);
        $field[] = FormBuilder::radio('status', 'Trạng thái', $replyInfo['status'] ?? 1)->options([['label' => 'Bật', 'value' => 1], ['label' => 'Tắt', 'value' => 0]]);
        return create_form('Tự động trả lời CSKH', $field, $this->url('/app/kefu/auto_reply/save/' . $id), 'POST');
    }

    /**
     * Lưu trả lời tự động
     * @param $id
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplySave($id, $data)
    {
        if ($id) {
            $this->dao->update($id, [
                'type' => $data['type'],
                'data' => json_encode($data['type'] == 'text' ? ['content' => $data['data']] : ['src' => $data['data']]),
                'status' => $data['status'],
            ]);
            $res = app()->make(WechatKeyServices::class)->update(['reply_id' => $id], ['keys' => $data['keys']]);
        } else {
            $reply = $this->dao->save([
                'type' => $data['type'],
                'data' => json_encode($data['type'] == 'text' ? ['content' => $data['data']] : ['src' => $data['data']]),
                'status' => $data['status'],
            ]);
            $res = app()->make(WechatKeyServices::class)->save([
                'reply_id' => $reply->id,
                'keys' => $data['keys'],
                'key_type' => 1,
            ]);
        }
        if (!$res) throw new AdminException('Lưu thất bại');
        return true;
    }

    /**
     * Xóa trả lời tự động
     * @param $id
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplyDel($id)
    {
        $this->dao->delete($id);
        app()->make(WechatKeyServices::class)->delete(['reply_id' => $id]);
        return true;
    }
}
