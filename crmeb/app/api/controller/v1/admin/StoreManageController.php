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
namespace app\api\controller\v1\admin;

use app\Request;
use app\services\admin\StoreManageServices;
use app\services\shipping\ShippingTemplatesServices;

class StoreManageController
{
    protected StoreManageServices $services;

    public function __construct(StoreManageServices $services)
    {
        $this->services = $services;
    }

    /**
     * Thống kê cửa hàng
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function statistics()
    {
        return app('json')->success($this->services->statistics());
    }

    /**
     * Sản phẩm của cửa hàng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function product(Request $request)
    {
        $where = $request->getMore([
            [['page', 'd'], 1],
            [['limit', 'd'], 10],
            ['type', ''],
            ['store_name', ''],
        ]);
        return app('json')->success($this->services->product($where));
    }

    /**
     * Đăng bán/ngừng bán sản phẩm của cửa hàng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productShow(Request $request)
    {
        [$id, $isShow] = $request->postMore([
            [['id', 'd'], 0],
            [['is_show', 'd'], 0],
        ], true);
        $this->services->productShow($id, $isShow);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Nhãn sản phẩm của cửa hàng
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productLabel()
    {
        return app('json')->success($this->services->productLabel());
    }

    /**
     * Lưu nhãn sản phẩm của cửa hàng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function saveProductLabel(Request $request)
    {
        [$ids, $label_list] = $request->postMore([
            ['ids', []],
            ['label_list', []],
        ], true);
        if (is_int($ids)) $ids = [$ids];
        $this->services->saveProductLabel($ids, $label_list);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Danh mục sản phẩm của cửa hàng
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productCate()
    {
        return app('json')->success($this->services->productCate());
    }

    /**
     * Lưu danh mục sản phẩm của cửa hàng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function saveProductCate(Request $request)
    {
        [$ids, $cate_id] = $request->postMore([
            ['ids', []],
            ['cate_id', []],
        ], true);
        if (is_int($ids)) $ids = [$ids];
        $this->services->saveProductCate($ids, $cate_id);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Thuộc tính sản phẩm của cửa hàng
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productAttr($id)
    {
        return app('json')->success($this->services->productAttr($id));
    }

    /**
     * Lưu thuộc tính sản phẩm của cửa hàng
     * @param Request $request
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function saveProductAttr(Request $request, $id)
    {
        [$attr_value] = $request->postMore([
            ['attr_value', []],
        ], true);
        $this->services->saveProductAttr($id, $attr_value);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Mẫu phí vận chuyển sản phẩm của cửa hàng
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/8
     */
    public function shippingTemp()
    {
        /** @var ShippingTemplatesServices $shippingTemplatesServices */
        $shippingTemplatesServices = app()->make(ShippingTemplatesServices::class);
        $data = $shippingTemplatesServices->getSelectList();
        $data = array_merge([['id' => 0, 'name' => 'Chưa chọn']], $data);
        return app('json')->success($data);
    }

    /**
     * Tạo sản phẩm
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/9
     */
    public function createProduct(Request $request)
    {
        $data = $request->postMore([
            ['store_name', ''],
            ['slider_image', []],
            ['cate_id', []],
            ['unit_name', ''],
            ['attr', []],
            ['content', ''],
            ['logistics', []],
            ['freight', 2],
            ['postage', 0],
            ['temp_id', 0],
            ['spec_type', 0],
        ]);
        $this->services->createProduct($data);
        return app('json')->success('Tạo thành công');
    }

    /**
     * Danh sách người dùng của cửa hàng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function user(Request $request)
    {
        $where = $request->getMore([
            ['page', 1],
            ['limit', 10],
            ['nickname', ''],
            ['group_id', 0],
            ['level', 0],
            ['label_id', ''],
        ]);
        return app('json')->success($this->services->user($where));
    }

    /**
     * Thông tin người dùng
     * @param $uid
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userInfo($uid)
    {
        return app('json')->success($this->services->userInfo($uid));
    }

    /**
     * Nhóm người dùng
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userGroup()
    {
        return app('json')->success($this->services->userGroup());
    }

    /**
     * Hạng người dùng
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userLevel()
    {
        return app('json')->success($this->services->userLevel());
    }

    /**
     * Nhãn người dùng
     * @param int $uid
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userLabel($uid = 0)
    {
        return app('json')->success($this->services->userLabel($uid));
    }

    /**
     * Danh sách phiếu giảm giá của người dùng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userCoupon(Request $request)
    {
        $where = $request->getMore([
            ['page', 1],
            ['limit', 10],
            ['coupon_title', ''],
            ['uid', 0],
        ]);
        return app('json')->success($this->services->userCoupon($where));
    }

    /**
     * Chỉnh sửa thông tin người dùng
     * @param Request $request
     * @param $uid
     * @return \think\Response
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userUpdate(Request $request, $uid)
    {
        $data = $request->postMore([
            ['type', 0],
            ['number', 0],
            ['status', 0],
            ['level', 0],
            ['group_id', 0],
            ['days', 0],
            ['coupon_id', 0],
            ['label_id', []],
        ]);
        $this->services->userUpdate($uid, $data);
        return app('json')->success('Sửa thành công');
    }

}
