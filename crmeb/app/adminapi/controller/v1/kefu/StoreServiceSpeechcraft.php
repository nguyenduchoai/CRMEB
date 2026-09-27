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

namespace app\adminapi\controller\v1\kefu;

use app\Request;
use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\kefu\service\StoreServiceSpeechcraftServices;
use app\adminapi\validate\service\StoreServiceSpeechcraftValidata;

/**
 * Controller rỗng của mẫu trả lời nhanh
 * Class StoreServiceSpeechcraft
 * @package app\adminapi\controller\v1\application\wechat
 */
class StoreServiceSpeechcraft extends AuthController
{
    /**
     * StoreServiceSpeechcraft constructor.
     * @param App $app
     * @param StoreServiceSpeechcraftServices $services
     */
    public function __construct(App $app, StoreServiceSpeechcraftServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(Request $request)
    {
        $where = $request->getMore([
            ['title', ''],
            ['message', ''],
            [['cate_id', 'd'], ''],
        ]);
        $where['kefu_id'] = 0;
        return app('json')->success($this->services->getSpeechcraftList($where));
    }

    /**
     * Hiển thị trang form tạo resource
     * @return mixed
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu resource mới tạo
     * @param Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = $request->postMore([
            ['title', ''],
            ['message', ''],
            [['cate_id', 'd'], 0],
            ['sort', 0],
        ]);

        $this->validate($data, StoreServiceSpeechcraftValidata::class);
        $data['add_time'] = time();
        $data['kefu_id'] = 0;
        if ($this->services->count(['message' => $data['message']])) {
            return app('json')->fail('Không thể thêm trùng câu trả lời mẫu');
        }
        if ($this->services->save($data)) {
            return app('json')->success('Tạo câu trả lời mẫu thành công');
        } else {
            return app('json')->fail('Tạo câu trả lời mẫu thất bại');
        }
    }

    /**
     * Hiển thị resource được chỉ định
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $info = $this->services->get($id);
        if (!$info) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }
        return app('json')->success($info);
    }

    /**
     * Hiển thị trang form sửa resource
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->updateForm((int)$id));
    }

    /**
     * Lưu resource đã cập nhật
     * @param Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->postMore([
            ['title', ''],
            ['message', ''],
            ['sort', 0],
            [['cate_id', 'd'], 0],
        ]);

        $this->validate($data, StoreServiceSpeechcraftValidata::class);
        $message = $this->services->get(['message' => $data['message']]);
        if ($message && $message['id'] != $id) {
            return app('json')->fail('Không thể thêm trùng câu trả lời mẫu');
        }
        if ($this->services->update($id, $data)) {
            return app('json')->success('Sửa thành công');
        } else {
            return app('json')->fail('Sửa thất bại');
        }

    }

    /**
     * Xóa resource được chỉ định
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id || !($info = $this->services->get($id))) {
            return app('json')->fail('Câu trả lời mẫu cần xóa không tồn tại');
        }
        if ($info->delete()) {
            return app('json')->success('Xóa thành công');
        } else {
            return app('json')->fail('Xóa thất bại');
        }
    }
}
