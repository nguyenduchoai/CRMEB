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
namespace app\adminapi\controller\v1\product;

use app\adminapi\controller\AuthController;
use app\services\product\product\StoreProductReplyServices;
use think\facade\App;

/**
 * Controller quản lý đánh giá
 * Class StoreProductReply
 * @package app\admin\controller\store
 */
class StoreProductReply extends AuthController
{
    public function __construct(App $app, StoreProductReplyServices $service)
    {
        parent::__construct($app);
        $this->services = $service;
    }

    /**
     * Hiển thị danh sách resource
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['is_reply', ''],
            ['store_name', ''],
            ['account', ''],
            ['data', ''],
            ['product_id', 0],
            ['key', ''],
            ['order', ''],
            ['status', ''],
        ]);
        $list = $this->services->sysPage($where);
        return app('json')->success($list);
    }

    /**
     * Xóa đánh giá
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->services->del($id);
        return app('json')->success(100002);
    }

    /**
     * Trả lời đánh giá
     * @param $id
     * @return mixed
     */
    public function set_reply($id)
    {
        [$content] = $this->request->postMore([
            ['content', '']
        ], true);
        $this->services->setReply($id, $content);
        return app('json')->success(400169);
    }

    /**
     * Tạo form đánh giá ảo
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function fictitious_reply()
    {
        list($product_id) = $this->request->postMore([
            ['product_id', 0],
        ], true);
        return app('json')->success($this->services->createForm($product_id));
    }

    /**
     * Lưu đánh giá ảo
     * @return mixed
     */
    public function save_fictitious_reply()
    {
        $data = $this->request->postMore([
            ['image', ''],
            ['nickname', ''],
            ['avatar', ''],
            ['comment', ''],
            ['pics', []],
            ['product_score', 0],
            ['service_score', 0],
            ['product_id', 0],
            ['add_time', 0],
            ['suk', ''],
        ]);
        if (!$data['product_id']) {
            $data['product_id'] = $data['image']['product_id'] ?? '';
        }
        $this->validate(['product_id' => $data['product_id'], 'nickname' => $data['nickname'], 'avatar' => $data['avatar'], 'comment' => $data['comment'], 'product_score' => $data['product_score'], 'service_score' => $data['service_score']], \app\adminapi\validate\product\StoreProductReplyValidate::class, 'save');
        $this->services->saveReply($data);
        return app('json')->success(100000);
    }

    /**
     * Duyệt đánh giá sản phẩm
     * @param $id
     * @param $status
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/4/22
     */
    public function set_status($id, $status)
    {
        $this->services->update($id, ['status' => $status]);
        return app('json')->success($status == 1 ? 'Đã duyệt' : 'Từ chối thành công');
    }

    /**
     * Duyệt đánh giá sản phẩm theo lô
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/6/18
     */
    public function batch_set_status()
    {
        list($ids, $status) = $this->request->postMore([
            ['ids', []],
            ['status', 0]
        ], true);
        $this->services->batchUpdate($ids, ['status' => $status]);
        return app('json')->success($status == 1 ? 'Đã duyệt' : 'Từ chối thành công');
    }
}
