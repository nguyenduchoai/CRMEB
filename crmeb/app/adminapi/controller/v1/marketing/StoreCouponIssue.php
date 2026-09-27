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
namespace app\adminapi\controller\v1\marketing;

use app\adminapi\controller\AuthController;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\product\product\StoreProductCouponServices;
use app\services\product\product\StoreProductServices;
use think\facade\App;

/**
 * Quản lý phiếu giảm giá đã phát hành
 * Class StoreCouponIssue
 * @package app\adminapi\controller\v1\marketing
 */
class StoreCouponIssue extends AuthController
{
    public function __construct(App $app, StoreCouponIssueServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách phiếu giảm giá
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', 1],
            ['coupon_title', ''],
            ['receive_type', '', '', 'receive_types'],
            ['type', ''],
            ['coupon_type', ''],
        ]);
        $list = $this->services->getCouponIssueList($where);
        return app('json')->success($list);
    }

    /**
     * Thêm phiếu giảm giá
     * @return mixed
     */
    public function saveCoupon()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['coupon_title', ''],
            ['coupon_price', 0.00],
            ['use_min_price', 0.00],
            ['coupon_time', 0],
            ['start_use_time', 0],
            ['end_use_time', 0],
            ['start_time', 0],
            ['end_time', 0],
            ['receive_type', 0],
            ['is_permanent', 0],
            ['total_count', 0],
            ['product_id', ''],
            ['category_id', []],
            ['type', 0],
            ['sort', 0],
            ['status', 0],
            ['receive_limit', 1],
            ['user_type', 1],
        ]);
        $res = $this->services->saveCoupon($data);
        if ($res) return app('json')->success(100000);
    }

    /**
     * Sửa trạng thái phiếu giảm giá
     * @param $id
     * @param $status
     * @return mixed
     */
    public function status($id, $status)
    {
        $this->services->update($id, ['status' => $status]);
        return app('json')->success(100001);
    }

    /**
     * Sao chép phiếu giảm giá để lấy chi tiết phiếu giảm giá
     * @param int $id
     * @return mixed
     */
    public function copy($id = 0)
    {
        if (!$id) return app('json')->fail(100100);
        $info = $this->services->get($id);
        if ($info) $info = $info->toArray();
        if ($info['receive_type'] == 1 || $info['receive_type'] == 3) {
            $info['user_type'] = 1;
        }
        if ($info['receive_type'] == 4) {
            $info['user_type'] = 2;
            $info['receive_type'] = 1;
        }
        if ($info['product_id'] != '') {
            $productIds = explode(',', $info['product_id']);
            /** @var StoreProductServices $product */
            $product = app()->make(StoreProductServices::class);
            $productImages = $product->getColumn([['id', 'in', $productIds]], 'image', 'id');
            foreach ($productIds as $item) {
                $info['productInfo'][] = [
                    'product_id' => $item,
                    'image' => $productImages[$item]
                ];
            }
        }
        if ($info['category_id'] != '') {
            $info['category_id'] = explode(',', $info['category_id']);
            foreach ($info['category_id'] as &$category_id) {
                $category_id = (int)$category_id;
            }
        }
        return app('json')->success($info);
    }

    /**
     * Xóa
     * @param string $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->services->update($id, ['is_del' => 1]);
        /** @var StoreProductCouponServices $storeProductService */
        $storeProductService = app()->make(StoreProductCouponServices::class);
        //Xóa liên kết sản phẩm với phiếu giảm giá này
        $storeProductService->delete(['issue_coupon_id' => $id]);
        return app('json')->success(100002);
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->createForm($id));
    }

    /**
     * Lịch sử nhận
     * @param string $id
     * @return mixed|string
     */
    public function issue_log($id)
    {
        $list = $this->services->issueLog($id);
        return app('json')->success($list);
    }
}
