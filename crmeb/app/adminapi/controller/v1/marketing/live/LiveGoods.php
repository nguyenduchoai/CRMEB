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
namespace app\adminapi\controller\v1\marketing\live;

use app\adminapi\controller\AuthController;
use app\services\activity\live\LiveGoodsServices;
use think\facade\App;

/**
 * Sản phẩm trong phòng livestream
 * Class LiveGoods
 * @package app\controller\admin\store
 */
class LiveGoods extends AuthController
{
    /**
     * LiveGoods constructor.
     * @param App $app
     * @param LiveGoodsServices $services
     */
    public function __construct(App $app, LiveGoodsServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách sản phẩm trong phòng livestream
     * @return mixed
     */
    public function list()
    {
        $where = $this->request->postMore([
            ['kerword', ''],
            ['status', ''],
            ['is_show', ''],
            ['live_id', 0]
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * Tạo sản phẩm livestream
     * @return mixed
     */
    public function create()
    {
        [$product_ids] = $this->request->postMore([
            ['product_id', []]
        ], true);
        return app('json')->success($this->services->create($product_ids));
    }

    /**
     * Tải lên sản phẩm livestream
     * @return mixed
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        [$goods_info] = $this->request->postMore([
            ['goods_info', []]
        ], true);
        $error = false;
        foreach ($goods_info as $goods) {
            $this->validate($goods, \app\adminapi\validate\marketing\LiveGoodsValidate::class, 'save');
            if (!preg_match('/.*(\.png|\.jpg|\.jpeg|\.gif)$/', $goods['image']) && strpos(strtolower($goods['image']), "phar://") !== false) {
                $error = true;
            }
        }
        if ($error) return app('json')->fail(40137);
        $this->services->add($goods_info);
        return app('json')->success(100000);
    }

    /**
     * Chi tiết sản phẩm
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        if (!$id) return app('json')->fail(100100);
        $goods = $this->services->get($id, ['*'], ['product']);
        return app('json')->success($goods ? $goods->toArray() : []);
    }

    /**
     * Đồng bộ sản phẩm livestream
     * @return mixed
     */
    public function syncGoods()
    {
        $this->services->syncGoodStatus();
        return app('json')->success(100038);
    }

    /**
     * Gửi lại để duyệt
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function audit($id)
    {
        if (!$id) return app('json')->fail(100100);
        $this->services->audit((int)$id);
        return app('json')->success(100014);
    }

    /**
     * Rút lại yêu cầu duyệt
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function resetAudit($id)
    {
        if (!$id) return app('json')->fail(100100);
        $this->services->resetAudit((int)$id);
        return app('json')->success(100014);
    }

    /**
     * Thiết lập trạng thái
     * @param int $id
     * @param $is_show
     * @return mixed
     */
    public function setShow(int $id, $is_show)
    {
        if (!$id) return app('json')->fail(100100);
        return app('json')->success($this->services->isShow($id, $is_show));
    }

    /**
     * Xóa sản phẩm
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        $this->services->delete($id);
        return app('json')->success(100002);
    }

}
