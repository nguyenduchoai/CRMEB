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
namespace app\adminapi\controller\v1\diy;

use app\adminapi\controller\AuthController;
use app\services\diy\DiyProServices;
use app\services\product\product\StoreProductServices;
use think\facade\App;

class DiyPro extends AuthController
{
    public function __construct(App $app, DiyProServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    public function getList()
    {
        return app('json')->success($this->services->getList());
    }

    public function getInfo($id = 0)
    {
        if ($id == 0) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->getInfo($id));
    }

    public function saveInfo($id = 0)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['title', ''],
            ['value', ''],
            ['type', 1],
            ['cover_image', ''],
            ['is_show', 0],
            ['is_bg_color', 0],
            ['is_bg_pic', 0],
            ['bg_tab_val', 0],
            ['color_picker', ''],
            ['bg_pic', ''],
            ['is_diy', 1],
            ['is_pro', 1],
            ['type', 2],
        ]);
        $value = is_string($data['value']) ? json_decode($data['value'], true) : $data['value'];
        foreach ($value as &$item) {
            if ($item['name'] === 'goodList') {
                if (isset($item['selectConfig']['list'])) {
                    unset($item['selectConfig']['list']);
                }
                if (isset($item['goodsList']['list']) && is_array($item['goodsList']['list'])) {
                    $limitMax = config('database.page.limitMax', 50);
                    if (isset($item['numConfig']['val']) && isset($item['tabConfig']['tabVal']) && $item['tabConfig']['tabVal'] == 0 && $item['numConfig']['val'] > $limitMax) {
                        return app('json')->fail('Số lượng sản phẩm bạn thiết lập vượt quá giới hạn của hệ thống, giới hạn tối đa' . $limitMax . 'sản phẩm');
                    }
                    $item['goodsList']['ids'] = array_column($item['goodsList']['list'], 'id');
                    unset($item['goodsList']['list'], $item['productList']['list']);
                }
            } elseif ($item['name'] === 'articleList') {
                if (isset($item['selectList']['list']) && is_array($item['selectList']['list'])) {
                    unset($item['selectList']['list']);
                }
            } elseif ($item['name'] === 'promotionList') {
                if (isset($item['tabConfig']['list']) && $item['tabConfig']['list']) {
                    $list = $item['tabConfig']['list'];
                    foreach ($list as &$tabValue) {
                        if (isset($tabValue['goodsList']['list']) && is_array($tabValue['goodsList']['list'])) {
                            $limitMax = config('database.page.limitMax', 50);
                            if (isset($tabValue['numConfig']['val']) && isset($tabValue['tabConfig']['tabVal']) && $tabValue['tabConfig']['tabVal'] == 0 && $tabValue['numConfig']['val'] > $limitMax) {
                                return app('json')->fail('Số lượng sản phẩm bạn thiết lập vượt quá giới hạn của hệ thống, giới hạn tối đa' . $limitMax . 'sản phẩm');
                            }
                            $tabValue['goodsList']['ids'] = array_column($tabValue['goodsList']['list'], 'id');
                        }
                        unset($tabValue['goodsList']['list'], $tabValue['productList']['list']);
                    }
                    $item['tabConfig']['list'] = $list;
                }
            } elseif ($item['name'] === 'newVip') {
                unset($item['newVipList']['list']);
            } elseif ($item['name'] === 'shortVideo') {
                unset($item['videoList']);
            }
        }
        $data['value'] = json_encode($value);
        $data['version'] = uniqid();
        return app('json')->success($id ? 'Sửa thành công' : 'Lưu thành công', ['id' => $this->services->saveInfo($id, $data)]);
    }

    public function delInfo($id)
    {
        $this->services->delInfo($id);
        return app('json')->success('Xóa thành công');
    }

    public function setInfoStatus($id)
    {
        return app('json')->success($this->services->setInfoStatus($id));
    }

    public function getProduct()
    {
        $where = $this->request->getMore([
            ['cate_id', []], //Tìm kiếm danh mục
            ['salesOrder', ''], //Sắp xếp theo lượt bán
            ['priceOrder', ''], //Sắp xếp theo giá
            ['store_label_id', []], //ID nhãn
            ['ids', ''], //ID sản phẩm
        ]);
        $where['is_show'] = 1;
        $where['is_del'] = 0;
        if (is_string($where['ids']) && $where['ids'] != '') $where['ids'] = explode(',', $where['ids']);
        [$page, $limit] = $this->services->getPageValue();
        $list = app()->make(StoreProductServices::class)->getSearchList($where, $page, $limit, ['id,store_name,cate_id,image,IFNULL(sales, 0) + IFNULL(ficti, 0) as sales,price,stock,activity,ot_price,spec_type,recommend_image,unit_name,is_vip,vip_price']);
        return app('json')->success($list);
    }

    public function updateName($id = 0)
    {
        [$name] = $this->request->postMore([
            ['name', '']
        ], true);
        if (!$name) return app('json')->fail('Vui lòng nhập tên');
        $this->services->updateName($id, $name);
        return app('json')->success('Sửa thành công');
    }

    public function exportDIYData($id)
    {
        $value = $this->services->exportDIYData($id);
        $filename = 'Dữ liệu DIY_' . date('YmdHis', time()) . '.txt';
        return app('json')->success('Xuất thành công', ['value' => $value, 'filename' => $filename]);
    }

    public function importDIYData()
    {
        // Lấy file
        $file = $this->request->file('file');
        if (!$file) return app('json')->fail('Vui lòng tải lên tệp');

        // Lấy đường dẫn tạm của file
        $tempPath = $file->getRealPath();

        // Dùng file stream để đọc nội dung
        $content = file_get_contents($tempPath);

        // Lưu nội dung
        $this->services->importDIYData($content);
        return app('json')->success('Nhập thành công');
    }

    public function textField()
    {
        $user = [
            ['label' => 'Tên người dùng', 'value' => 'nickname'],
            ['label' => 'id người dùng', 'value' => 'uid'],
            ['label' => 'Ảnh đại diện người dùng', 'value' => 'image'],
            ['label' => 'Sản phẩm yêu thích', 'value' => 'collection_num'],
            ['label' => 'Thêm vào giỏ hàng', 'value' => 'cart_num'],
            ['label' => 'Tổng số đơn hàng', 'value' => 'order_num'],
            ['label' => 'Điểm thưởng của tôi', 'value' => 'integral'],
            ['label' => 'Số dư của tôi', 'value' => 'now_money'],
            ['label' => 'Hoa hồng của tôi', 'value' => 'brokerage_price'],
            ['label' => 'Tin nhắn chưa đọc', 'value' => 'unread_msg_num'],
        ];

        $article = [
            ['label' => 'Tiêu đề bài viết', 'value' => 'title'],
            ['label' => 'ID bài viết', 'value' => 'id'],
            ['label' => 'Ảnh bìa bài viết', 'value' => 'image'],
            ['label' => 'Danh mục bài viết', 'value' => 'cid_name'],
            ['label' => 'Mô tả ngắn bài viết', 'value' => 'synopsis'],
            ['label' => 'Lượt xem bài viết', 'value' => 'visit'],
            ['label' => 'Thời gian thêm', 'value' => 'add_time'],
        ];

        $coupon = [
            ['label' => 'Tên phiếu giảm giá', 'value' => 'coupon_title'],
            ['label' => 'ID phiếu giảm giá', 'value' => 'id'],
            ['label' => 'Loại phiếu giảm giá', 'value' => 'type'],
            ['label' => 'Mệnh giá phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Trạng thái phiếu giảm giá', 'value' => 'status'],
            ['label' => 'Thời gian nhận', 'value' => 'receive_time'],
            ['label' => 'Thời gian sử dụng', 'value' => 'use_time'],
            ['label' => 'Điều kiện sử dụng', 'value' => 'use_min_price'],
            ['label' => 'Số lượng phát hành', 'value' => 'receive_count'],
            ['label' => 'Thời gian thêm', 'value' => 'add_time'],
        ];

        $product = [
            ['label' => 'Tên sản phẩm', 'value' => 'store_name'],
            ['label' => 'id sản phẩm', 'value' => 'id'],
            ['label' => 'Hình ảnh sản phẩm', 'value' => 'image'],
            ['label' => 'Mô tả ngắn sản phẩm', 'value' => 'store_info'],
            ['label' => 'Đơn vị tính', 'value' => 'unit_name'],
            ['label' => 'Danh mục sản phẩm', 'value' => 'cate_name'],
            ['label' => 'Tồn kho sản phẩm', 'value' => 'stock'],
            ['label' => 'Giá bán sản phẩm', 'value' => 'price'],
            ['label' => 'Giá bán cao nhất của sản phẩm', 'value' => 'max_price'],
            ['label' => 'Giá bán thấp nhất của sản phẩm', 'value' => 'min_price'],
            ['label' => 'Giá gốc sản phẩm', 'value' => 'ot_price'],
            ['label' => 'Giá gốc cao nhất của sản phẩm', 'value' => 'max_ot_price'],
            ['label' => 'Giá gốc thấp nhất của sản phẩm', 'value' => 'min_ot_price'],
            ['label' => 'Số lượng mua tối thiểu của sản phẩm', 'value' => 'min_qty'],
            ['label' => 'Lượt bán sản phẩm', 'value' => 'sales'],
            ['label' => 'Lượt truy cập sản phẩm', 'value' => 'browse'],
            ['label' => 'Thời gian thêm sản phẩm', 'value' => 'add_time'],
        ];

        return app('json')->success(compact('user', 'article', 'coupon', 'product'));
    }
}
