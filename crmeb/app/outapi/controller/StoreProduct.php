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
namespace app\outapi\controller;

use app\services\order\StoreCartServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\OutStoreProductServices;
use think\facade\App;

/**
 * Class StoreProduct
 * @package aapp\outapi\controller
 */
class StoreProduct extends AuthController
{
    protected $services;

    public function __construct(App $app, OutStoreProductServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['cate_id', ''],
            ['store_name', ''],
            ['type', 1],
            ['is_live', 0],
            ['is_new', ''],
            ['is_virtual', -1],
            ['is_presale', -1]
        ]);
        $where['is_del'] = 0;
        /** @var StoreCategoryServices $storeCategoryServices */
        $storeCategoryServices = app()->make(StoreCategoryServices::class);
        if ($where['cate_id'] !== '') {
            if ($storeCategoryServices->value(['id' => $where['cate_id']], 'pid')) {
                $where['sid'] = $where['cate_id'];
            } else {
                $where['cid'] = $where['cate_id'];
            }
        }

        unset($where['cate_id']);
        $list = $this->services->searchList($where);
        return app('json')->success($list);
    }

    /**
     * Sửa trạng thái
     * @param string $id
     * @param string $is_show
     */
    public function set_show($id = '', $is_show = '')
    {
        if ($id == '' || $is_show == '') return app('json')->fail('Tham số không hợp lệ');
        $this->services->setShow((int)$id, (int)$is_show);
        return app('json')->success($is_show == 1 ? 'Hiển thị thành công' : 'Ẩn thành công');
    }

    /**
     * Lấy thông tin sản phẩm
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id = 0)
    {
        return app('json')->success($this->services->getInfo((int)$id));
    }

    /**
     * Lưu
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['cate_id', []],//ID danh mục
            ['store_name', ''],//Tên sản phẩm
            ['keyword', ''],//Từ khóa
            ['unit_name', 'cái'],//Đơn vị
            ['store_info', ''],//Mô tả ngắn sản phẩm
            ['slider_image', []],//Ảnh trình chiếu
            ['video_open', 0],//Có mở video không
            ['video_link', ''],//Liên kết video
            ['spec_type', 0],//Đơn/đa quy cách
            ['items', []],//Quy cách
            ['attrs', []],//Quy cách
            ['description', ''],//Chi tiết sản phẩm
            ['description_images', []],//Chi tiết sản phẩm
            ['logistics', []],//Hình thức vận chuyển
            ['freight', 1],//Cài đặt phí vận chuyển
            ['postage', 0],//Phí vận chuyển
            ['is_sub', 0],//Hoa hồng là riêng hay theo mặc định
            ['is_vip', 0],//Giá thành viên trả phí
            ['recommend', []],//Đề xuất sản phẩm
            ['temp_id', 0],//ID mẫu phí vận chuyển
            ['give_integral', 0],//Tặng điểm thưởng
            ['presale', 0],//Bật/tắt sản phẩm đặt trước
            ['presale_time', 0],//Thời gian đặt trước
            ['presale_day', 0],//Ngày giao hàng đặt trước
            ['vip_product', 0],//Có phải sản phẩm dành cho thành viên trả phí không
            ['activity', []],//Thứ tự ưu tiên hoạt động
            ['command_word', ''],//Mã chia sẻ sản phẩm
            ['is_show', 0],//Đăng bán
            ['ficti', 0],//Lượt bán ảo
            ['sort', 0],//Thứ tự sắp xếp
            ['recommend_image', ''],//Ảnh đề xuất sản phẩm
            ['custom_form', []],//Form tùy chỉnh
            ['is_limit', 0],//Có giới hạn mua không
            ['limit_type', 0],//Loại giới hạn mua
            ['limit_num', 0]//Số lượng giới hạn mua
        ]);
        $id = $this->services->save(0, $data);
        return app('json')->success('Lưu thành công', ['id' => $id]);
    }

    /**
     * Cập nhật
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['cate_id', []],//ID danh mục
            ['store_name', ''],//Tên sản phẩm
            ['keyword', ''],//Từ khóa
            ['unit_name', 'cái'],//Đơn vị
            ['store_info', ''],//Mô tả ngắn sản phẩm
            ['slider_image', []],//Ảnh trình chiếu
            ['video_open', 0],//Có mở video không
            ['video_link', ''],//Liên kết video
            ['spec_type', 0],//Đơn/đa quy cách
            ['items', []],//Quy cách
            ['attrs', []],//Quy cách
            ['description', ''],//Chi tiết sản phẩm
            ['description_images', []],//Chi tiết sản phẩm
            ['logistics', []],//Hình thức vận chuyển
            ['freight', 1],//Cài đặt phí vận chuyển
            ['postage', 0],//Phí vận chuyển
            ['is_sub', 0],//Hoa hồng là riêng hay theo mặc định
            ['is_vip', 0],//Giá thành viên trả phí
            ['recommend', []],//Đề xuất sản phẩm
            ['temp_id', 0],//ID mẫu phí vận chuyển
            ['give_integral', 0],//Tặng điểm thưởng
            ['presale', 0],//Bật/tắt sản phẩm đặt trước
            ['presale_time', 0],//Thời gian đặt trước
            ['presale_day', 0],//Ngày giao hàng đặt trước
            ['vip_product', 0],//Có phải sản phẩm dành cho thành viên trả phí không
            ['activity', []],//Thứ tự ưu tiên hoạt động
            ['command_word', ''],//Mã chia sẻ sản phẩm
            ['is_show', 0],//Đăng bán
            ['ficti', 0],//Lượt bán ảo
            ['sort', 0],//Thứ tự sắp xếp
            ['recommend_image', ''],//Ảnh đề xuất sản phẩm
            ['custom_form', []],//Form tùy chỉnh
            ['is_limit', 0],//Có giới hạn mua không
            ['limit_type', 0],//Loại giới hạn mua
            ['limit_num', 0]//Số lượng giới hạn mua
        ]);
        $this->services->save((int)$id, $data);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //Khi xóa sản phẩm, kiểm tra có đang tham gia hoạt động không
        $this->services->checkActivity($id);
        $res = $this->services->del($id);
        /** @var StoreCartServices $cartService */
        $cartService = app()->make(StoreCartServices::class);
        $cartService->changeStatus($id, 0);
        return app('json')->success($res);
    }

    /**
     * Đồng bộ tồn kho
     * @return void
     */
    public function uploadStock()
    {
        [$items] = $this->request->postMore([['items', []]], true);

        foreach ($items as $item) {
            if (!isset($item['bar_code']) || !isset($item['bar_code_number']) || !isset($item['qty'])) {
                return app('json')->fail('Vui lòng kiểm tra mã thuộc tính hoặc số lượng tồn kho');
            }
        }

        if (count($items) > 100) {
            return app('json')->fail('Số bản ghi đồng bộ không được vượt quá 100');
        }

        $this->services->syncStock($items);
        return app('json')->success('Thao tác thành công');
    }
}
