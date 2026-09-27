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
namespace app\adminapi\controller\v1\application\wechat;

use app\adminapi\controller\AuthController;
use crmeb\services\app\WechatService;
use think\facade\App;
use app\services\wechat\WechatNewsCategoryServices;
use app\services\article\ArticleServices;
use think\facade\Log;

/**
 * Thông tin tin bài
 * Class WechatNewsCategory
 * @package app\admin\controller\wechat
 *
 */
class WechatNewsCategory extends AuthController
{
    /**
     * Phương thức khởi tạo
     * Menus constructor.
     * @param App $app
     * @param WechatNewsCategoryServices $services
     */
    public function __construct(App $app, WechatNewsCategoryServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách tin nhắn hình ảnh - văn bản
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['page', 1],
            ['limit', 20],
            ['cate_name', '']
        ]);
        $list = $this->services->getAll($where);
        return app('json')->success($list);
    }

    /**
     * Chi tiết tin bài
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        $info = $this->services->get($id);
        /** @var ArticleServices $services */
        $services = app()->make(ArticleServices::class);
        $new = $services->articlesList($info['new_id']);
        if ($new) $new = $new->toArray();
        $info['new'] = $new;
        return app('json')->success(compact('info'));
    }

    /**
     * Xóa tin bài
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return app('json')->fail(100008);
        else
            return app('json')->success(100002);
    }

    /**
     * Lưu khi thêm mới hoặc sửa
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['list', []],
            ['id', 0]
        ]);
        try {
            $id = [];
            $countList = count($data['list']);
            if (!$countList) return app('json')->fail(400243);
            /** @var ArticleServices $services */
            $services = app()->make(ArticleServices::class);
            foreach ($data['list'] as $k => $v) {
                if ($v['title'] == '') return app('json')->fail(400244);
                if ($v['author'] == '') return app('json')->fail(400245);
                if ($v['content'] == '') return app('json')->fail(400246);
                if ($v['synopsis'] == '') return app('json')->fail(400247);
                $v['status'] = 1;
                $v['add_time'] = time();
                if ($v['id']) {
                    $idC = $v['id'];
                    $services->save($v);
                    unset($v['id']);
                    $data['list'][$k]['id'] = $idC;
                    $id[] = $idC;
                } else {
                    $res = $services->save($v);
                    unset($v['id']);
                    $id[] = $res['id'];
                    $data['list'][$k]['id'] = $res['id'];
                }
            }
            $countId = count($id);
            if ($countId != $countList) {
                if ($data['id']) return app('json')->fail(100007);
                else return app('json')->fail(100022);
            } else {
                $newsCategory['cate_name'] = $data['list'][0]['title'];
                $newsCategory['new_id'] = implode(',', $id);
                $newsCategory['sort'] = 0;
                $newsCategory['add_time'] = time();
                $newsCategory['status'] = 1;
                if ($data['id']) {
                    $this->services->update($data['id'], $newsCategory, 'id');
                    return app('json')->success(100001);
                } else {
                    $this->services->save($newsCategory);
                    return app('json')->success(100021);
                }
            }
        } catch (\Exception $e) {
            return app('json')->fail(100101);
        }
    }

    /**
     * Gửi tin nhắn
     */
    public function push()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['user_ids', '']
        ]);
        if (!$data['id']) return app('json')->fail(100100);
        $list = $this->services->getWechatNewsItem($data['id']);
        $wechatNews = [];
        if ($list) {
            if (is_array($list['new']) && count($list['new'])) {
                $wechatNews['title'] = $list['new'][0]['title'];
                $wechatNews['image_input'] = $list['new'][0]['image_input'];
                $wechatNews['date'] = date('d/m', time());
                $wechatNews['description'] = $list['new'][0]['synopsis'];
                $wechatNews['id'] = $list['new'][0]['id'];
            }
        }
        if ($data['user_ids'] != '') {//Tin nhắn CSKH
            $wechatNews = $this->services->wechatPush($wechatNews);
            $message = WechatService::newsMessage($wechatNews);
            $errorLog = [];//Người dùng gửi thất bại
            $user = $this->services->getWechatUser($data['user_ids'], 'nickname,subscribe,openid', 'uid');
            if ($user) {
                foreach ($user as $v) {
                    if ($v['subscribe'] && $v['openid']) {
                        try {
                            WechatService::staffService()->message($message)->to($v['openid'])->send();
                        } catch (\Exception $e) {
                            Log::error($v['nickname'] . 'gửi thất bại, nguyên nhân' . $e->getMessage());
                            $errorLog[] = $v['nickname'] . 'Gửi thất bại';
                        }
                    } else {
                        $errorLog[] = $v['nickname'] . 'gửi thất bại do chưa theo dõi (không phải người dùng OA WeChat)';
                    }
                }
                if (!count($errorLog)) {
                    return app('json')->success(100030);
                } else {
                    return app('json')->fail(100031);
                }
            } else {
                return app('json')->fail(100031);
            }

        }

    }

    /**
     * Danh sách hình ảnh - văn bản khi gửi thông báo
     * @return mixed
     */
    public function send_news()
    {
        $where = $this->request->getMore([
            ['cate_name', ''],
            ['page', 1],
            ['limit', 10]
        ]);
        return app('json')->success($this->services->list($where));
    }

}
