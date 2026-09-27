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
namespace app\outapi\controller;

use think\facade\App;
use app\outapi\validate\StoreCategoryValidate;
use app\services\product\product\StoreCategoryServices;

/**
 * Controller danh mục sản phẩm
 * Class StoreCategory
 * @package app\outapi\controller
 */
class StoreCategory extends AuthController
{
    /**
     * @var StoreCategoryServices
     */
    protected $services;

    /**
     * StoreCategory constructor.
     * @param App $app
     * @param StoreCategoryServices $services
     */
    public function __construct(App $app, StoreCategoryServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách danh mục
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['is_show', ''],
            ['pid', ''],
            ['cate_name', ''],
        ]);
        $where['pid'] = -2;
        $data = $this->services->getCategoryList($where);
        return app('json')->success($data);
    }

    /**
     * Thêm danh mục
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['cate_name', ''],
            ['pic', ''],
            ['big_pic', ''],
            ['sort', 0],
            ['is_show', 0]
        ]);
        $this->validate($data, StoreCategoryValidate::class, 'save');
        $cateId = $this->services->createData($data);
        return app('json')->success(100000, ['id' => $cateId]);
    }

    /**
     * Cập nhật danh mục
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['cate_name', ''],
            ['pic', ''],
            ['big_pic', ''],
            ['sort', 0],
            ['is_show', 0]
        ]);
        $this->validate($data, StoreCategoryValidate::class, 'save');
        $this->services->editData($id, $data);
        return app('json')->success(100001);
    }

    /**
     * Xóa danh mục
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->services->del((int)$id);
        return app('json')->success(100002);
    }

    /**
     * Chi tiết
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        $info = $this->services->getInfo((int)$id);
        return app('json')->success($info);
    }

    /**
     * Sửa trạng thái
     * @param string $id
     * @param string $is_show
     */
    public function set_show($id = '', $is_show = '')
    {
        if ( $id == '' || $is_show == '') return app('json')->fail(100100);
        $this->services->setShow((int)$id, (int)$is_show);
        return app('json')->success($is_show == 1 ? 100003 : 100004);
    }
}
