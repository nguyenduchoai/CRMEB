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

namespace app\kefuapi\controller;

use app\Request;
use think\facade\App;
use app\services\kefu\KefuServices;
use app\services\other\CategoryServices;
use app\kefuapi\validate\SpeechcraftValidate;
use app\services\kefu\service\StoreServiceSpeechcraftServices;

/**
 * Class Service
 * @package app\kefuapi\controller
 */
class Service extends AuthController
{
    /**
     * Service constructor.
     * @param App $app
     * @param KefuServices $services
     */
    public function __construct(App $app, KefuServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách chuyển tiếp CSKH
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceList(Request $request, $uid = 0)
    {
        $where = $request->getMore([
            ['nickname', ''],
        ]);
        return app('json')->success($this->services->getServiceList($where, [$this->kefuInfo['uid'], $uid]));
    }

    /**
     * Danh sách mẫu câu
     * @param Request $request
     * @param StoreServiceSpeechcraftServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSpeechcraftList(Request $request, StoreServiceSpeechcraftServices $services)
    {
        $where = $request->getMore([
            ['title', ''],
            ['cate_id', ''],
            ['type', 0]
        ]);
        if ($where['type']) {
            $where['kefu_id'] = $this->kefuId;
        } else {
            $where['kefu_id'] = 0;
        }
        $data = $services->getSpeechcraftList($where);
        return app('json')->success($data['list']);
    }

    /**
     * Thêm danh mục
     * @param Request $request
     * @param CategoryServices $services
     * @return mixed
     */
    public function saveCate(Request $request, CategoryServices $services)
    {
        $data = $request->postMore([
            ['name', ''],
            [['sort', 'd'], 0],
        ]);

        if (!$data['name']) {
            return app('json')->fail(410095);
        }
        $data['add_time'] = time();
        $data['owner_id'] = $this->kefuId;
        $data['type'] = 1;

        $services->save($data);
        return app('json')->success(100021);
    }

    /**
     * Sửa danh mục
     * @param Request $request
     * @param CategoryServices $services
     * @param $id
     * @return mixed
     */
    public function editCate(Request $request, CategoryServices $services, $id)
    {
        $data = $request->postMore([
            ['name', ''],
            [['sort', 'd'], 0],
        ]);

        if (!$data['name']) {
            return app('json')->fail(410095);
        }

        $cateInfo = $services->get($id);
        if (!$cateInfo) {
            return app('json')->fail(100026);
        }
        $cateInfo->name = $data['name'];
        $cateInfo->sort = $data['sort'];

        if ($cateInfo->save()) {
            return app('json')->success(100001);
        } else {
            return app('json')->fail(100007);
        }
    }

    /**
     * Xóa danh mục
     * @param CategoryServices $services
     * @param $id
     * @return mixed
     */
    public function deleteCate(CategoryServices $services, $id)
    {
        $cateInfo = $services->get($id);
        if (!$cateInfo) {
            return app('json')->fail(410096);
        }

        if ($cateInfo->delete()) {
            return app('json')->success(100002);
        } else {
            return app('json')->fail(100008);
        }
    }

    /**
     * Lấy danh mục CSKH hiện tại
     * @param CategoryServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCateList(CategoryServices $services, $type)
    {
        return app('json')->success($services->getCateList(['owner_id' => $type ? $this->kefuId : 0, 'type' => 1], ['id', 'name', 'sort']));
    }

    /**
     * Thêm câu trả lời mẫu
     * @param Request $request
     * @param StoreServiceSpeechcraftServices $services
     * @return mixed
     */
    public function saveSpeechcraft(Request $request, StoreServiceSpeechcraftServices $services, CategoryServices $categoryServices)
    {
        $data = $request->postMore([
            ['title', ''],
            ['cate_id', 0],
            ['message', ''],
            ['sort', 0]
        ]);

        validate(SpeechcraftValidate::class)->check($data);

        if (!$categoryServices->count(['owner_id' => $this->kefuId, 'type' => 1, 'id' => $data['cate_id']])) {
            return app('json')->fail(410096);
        }
        if ($services->count(['message' => $data['message']])) {
            return app('json')->fail(410097);
        }
        $data['add_time'] = time();
        $data['kefu_id'] = $this->kefuId;

        $res = $services->save($data);
        if ($res) {
            return app('json')->success(100021, null, $res->toArray());
        } else {
            return app('json')->fail(100022);
        }
    }

    /**
     * Sửa câu trả lời mẫu
     * @param Request $request
     * @param StoreServiceSpeechcraftServices $services
     * @param $id
     * @return mixed
     */
    public function editSpeechcraft(Request $request, StoreServiceSpeechcraftServices $services, CategoryServices $categoryServices, $id)
    {
        $data = $request->postMore([
            ['title', ''],
            ['cate_id', 0],
            ['message', ''],
        ]);

        if (!$data['message']) {
            return app('json')->fail(410102);
        }
        if (!$categoryServices->count(['owner_id' => $this->kefuId, 'type' => 1, 'id' => $data['cate_id']])) {
            return app('json')->fail(100026);
        }
        $speechcraft = $services->get($id);
        if (!$speechcraft) {
            return app('json')->fail(100026);
        }
        if (!$speechcraft->kefu_id) {
            return app('json')->fail(410101);
        }
        $speechcraft->title = $data['title'];
        if ($data['cate_id']) {
            $speechcraft->cate_id = $data['cate_id'];
        }
        $speechcraft->message = $data['message'];

        if ($speechcraft->save()) {
            return app('json')->success(100001);
        } else {
            return app('json')->fail(100007);
        }
    }

    /**
     * Xóa câu trả lời mẫu
     * @param StoreServiceSpeechcraftServices $services
     * @param $id
     * @return mixed
     */
    public function deleteSpeechcraft(StoreServiceSpeechcraftServices $services, $id)
    {
        $speechcraft = $services->get($id);
        if (!$speechcraft) {
            return app('json')->fail(410100);
        }
        if ($speechcraft->delete()) {
            return app('json')->success(100002);
        } else {
            return app('json')->fail(100008);
        }
    }

    /**
     * Lịch sử trò chuyện
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatList(Request $request)
    {
        [$uid, $upperId, $is_tourist] = $request->postMore([
            ['uid', 0],
            ['upperId', 0],
            ['is_tourist', 0],
        ], true);
        if (!$uid) {
            return app('json')->fail(100100);
        }
        return app('json')->success($this->services->getChatList($this->kefuInfo['uid'], $uid, (int)$upperId, $is_tourist));
    }

    /**
     * Thông tin chi tiết CSKH hiện tại
     * @return mixed
     */
    public function getServiceInfo()
    {
        $this->kefuInfo['site_name'] = sys_config('site_name');
        return app('json')->success($this->kefuInfo->toArray());
    }

    /**
     * Chuyển tiếp CSKH
     * @return mixed
     */
    public function transfer()
    {
        [$kefuToUid, $uid] = $this->request->postMore([
            ['kefuToUid', 0],
            ['uid', 0]
        ], true);
        if (!$kefuToUid || !$uid) {
            return app('json')->fail(410098);
        }
        $this->services->setTransfer($this->kefuInfo['uid'], (int)$uid, (int)$kefuToUid);
        return app('json')->success(410099);
    }
}
