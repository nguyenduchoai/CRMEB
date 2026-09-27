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

namespace app\services\other\export;

use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreDescriptionServices;
use app\services\product\product\StoreProductServices;
use app\services\product\sku\StoreProductAttrResultServices;
use app\services\user\member\MemberCardServices;
use app\services\user\UserServices;
use crmeb\services\SpreadsheetExcelService;

class ExportServices extends BaseServices
{
    /**
     * Xuất người dùng
     * @param $where
     * @return array
     */
    public function exportUserList($where)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data = $userServices->index($where)['list'];
        $header = ['ID người dùng', 'Biệt danh', 'Họ tên', 'Giới tính', 'Điện thoại', 'Hạng người dùng', 'Nhóm người dùng', 'Nhãn người dùng', 'Loại người dùng', 'Số dư người dùng', 'Thời gian đăng nhập cuối', 'Thời gian đăng ký', 'Đã hủy tài khoản'];
        $filename = 'Danh sách người dùng_' . date('YmdHis', time());
        $export = $fileKey = [];
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $item) {
                $one_data = [
                    'uid' => $item['uid'],
                    'nickname' => $item['nickname'],
                    'real_name' => $item['real_name'],
                    'sex' => $item['sex'],
                    'phone' => $item['phone'],
                    'level' => $item['level'],
                    'group_id' => $item['group_id'],
                    'labels' => $item['labels'],
                    'user_type' => $item['user_type'],
                    'now_money' => $item['now_money'],
                    'last_time' => date('Y-m-d H:i:s', $item['last_time']),
                    'add_time' => date('Y-m-d H:i:s', $item['add_time']),
                    'is_del' => $item['is_del'] ? 'Đã hủy' : 'Bình thường'
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất đơn hàng
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function exportOrderList($where)
    {
        $header = ['Mã đơn hàng', 'Họ tên người nhận', 'Số điện thoại người nhận', 'Địa chỉ nhận hàng', 'Tên sản phẩm', 'Quy cách', 'Số lượng', 'Giá', 'Tổng giá', 'Thanh toán thực tế', 'Trạng thái thanh toán', 'Thời gian thanh toán', 'Trạng thái đơn hàng', 'Thời gian đặt hàng', 'Ghi chú của người dùng', 'Ghi chú của người bán', 'Thông tin biểu mẫu'];
        $filename = 'Danh sách đơn hàng_' . date('YmdHis', time());
        $export = $fileKey = [];
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $data = $orderServices->getOrderList($where)['data'];
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $item) {
                if ($item['paid'] == 1) {
                    switch ($item['pay_type']) {
                        case 'weixin':
                            $item['pay_type_name'] = 'WeChat Pay';
                            break;
                        case 'yue':
                            $item['pay_type_name'] = 'Thanh toán bằng số dư';
                            break;
                        case 'offline':
                            $item['pay_type_name'] = 'Thanh toán ngoại tuyến';
                            break;
                        default:
                            $item['pay_type_name'] = 'Thanh toán khác';
                            break;
                    }
                } else {
                    switch ($item['pay_type']) {
                        default:
                            $item['pay_type_name'] = 'Chưa thanh toán';
                            break;
                        case 'offline':
                            $item['pay_type_name'] = 'Thanh toán ngoại tuyến';
                            break;
                    }
                }
                if ($item['paid'] == 0 && $item['status'] == 0) {
                    $item['status_name'] = 'Chưa thanh toán';
                } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['shipping_type'] == 1 && $item['refund_status'] == 0) {
                    $item['status_name'] = 'Chưa giao hàng';
                } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['shipping_type'] == 2 && $item['refund_status'] == 0) {
                    $item['status_name'] = 'Chưa xác nhận sử dụng';
                } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['shipping_type'] == 1 && $item['refund_status'] == 0) {
                    $item['status_name'] = 'Chờ nhận hàng';
                } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['shipping_type'] == 2 && $item['refund_status'] == 0) {
                    $item['status_name'] = 'Chưa xác nhận sử dụng';
                } else if ($item['paid'] == 1 && $item['status'] == 2 && $item['refund_status'] == 0) {
                    $item['status_name'] = 'Chờ đánh giá';
                } else if ($item['paid'] == 1 && $item['status'] == 3 && $item['refund_status'] == 0) {
                    $item['status_name'] = 'Đã hoàn thành';
                } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                    $item['status_name'] = 'Đang hoàn tiền';
                } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                    $item['status_name'] = 'Đã hoàn tiền';
                }
                $custom_form = '';
                foreach ($item['custom_form'] as $custom_form_value) {
                    if (is_string($custom_form_value['value'])) {
                        $custom_form .= $custom_form_value['title'] . '：' . $custom_form_value['value'] . '；';
                    } elseif (is_array($custom_form_value['value'])) {
                        $custom_form .= $custom_form_value['title'] . '：' . implode(',', $custom_form_value['value']) . '；';
                    }
                }

//                $goodsName = [];
//                foreach ($item['_info'] as $value) {
//                    $_info = $value['cart_info'];
//                    $sku = '';
//                    if (isset($_info['productInfo']['attrInfo'])) {
//                        if (isset($_info['productInfo']['attrInfo']['suk'])) {
//                            $sku = '(' . $_info['productInfo']['attrInfo']['suk'] . ')';
//                        }
//                    }
//                    if (isset($_info['productInfo']['store_name'])) {
//                        $goodsName[] = implode(' ',
//                            [$_info['productInfo']['store_name'],
//                                $sku,
//                                "[{$_info['cart_num']} * {$_info['truePrice']}]"
//                            ]);
//                    }
//                }
//                $one_data = [
//                    'order_id' => $item['order_id'],
//                    'real_name' => $item['real_name'],
//                    'user_phone' => $item['user_phone'],
//                    'user_address' => $item['user_address'],
//                    'goods_name' => $goodsName ? implode("\n", $goodsName) : '',
//                    'total_price' => $item['total_price'],
//                    'pay_price' => $item['pay_price'],
//                    'pay_type_name' => $item['pay_type_name'],
//                    'pay_time' => $item['pay_time'] > 0 ? date('Y-m-d H:i', (int)$item['pay_time']) : 'Chưa có',
//                    'status_name' => $item['status_name'] ?? 'Trạng thái không xác định',
//                    'add_time' => $item['add_time'],
//                    'mark' => $item['mark'],
//                    'remark' => $item['remark'],
//                    'custom_form' => $custom_form,
//                ];
                $goodsInfo = [];
                foreach ($item['_info'] as $value) {
                    $goodsInfo[] = [
                        $value['cart_info']['productInfo']['store_name'],
                        $value['cart_info']['productInfo']['attrInfo']['suk'],
                        $value['cart_info']['cart_num'],
                        $value['cart_info']['truePrice'],
                    ];
                }
                $one_data = [
                    $item['order_id'],
                    $item['real_name'],
                    $item['user_phone'],
                    $item['user_address'],
                    $goodsInfo,
                    $item['total_price'],
                    $item['pay_price'],
                    $item['pay_type_name'],
                    $item['pay_time'] > 0 ? date('Y-m-d H:i', (int)$item['pay_time']) : 'Chưa có',
                    $item['status_name'] ?? 'Trạng thái không xác định',
                    $item['add_time'],
                    $item['mark'],
                    $item['remark'],
                    $custom_form,
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất đơn hàng
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function exportOrderDeliveryList()
    {
        $header = ['ID đơn hàng', 'Mã đơn hàng', 'Tên đơn vị vận chuyển', 'Mã đơn vị vận chuyển', 'Mã vận đơn', 'Họ tên người nhận', 'Số điện thoại người nhận', 'Địa chỉ nhận hàng', 'Thông tin sản phẩm', 'Thanh toán thực tế', 'Ghi chú của người dùng'];
        $filename = 'Phiếu giao hàng_' . date('YmdHis', time());
        $export = $fileKey = [];
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $data = $orderServices->getOrderList(['status' => 1, 'shipping_type' => 1, 'virtual_type' => 0, 'pid' => 0])['data'];
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $item) {
                $goodsName = [];
                foreach ($item['_info'] as $value) {
                    $_info = $value['cart_info'];
                    $sku = '';
                    if (isset($_info['productInfo']['attrInfo'])) {
                        if (isset($_info['productInfo']['attrInfo']['suk'])) {
                            $sku = '(' . $_info['productInfo']['attrInfo']['suk'] . ')';
                        }
                    }
                    if (isset($_info['productInfo']['store_name'])) {
                        $goodsName[] = implode(' ',
                            [$_info['productInfo']['store_name'],
                                $sku,
                                "[{$_info['cart_num']} * {$_info['truePrice']}]"
                            ]);
                    }
                }
                $one_data = [
                    'id' => $item['id'],
                    'order_id' => $item['order_id'],
                    'delivery_name' => '',
                    'delivery_code' => '',
                    'delivery_id' => '',
                    'real_name' => $item['real_name'],
                    'user_phone' => $item['user_phone'],
                    'user_address' => $item['user_address'],
                    'goods_name' => $goodsName ? implode("\n", $goodsName) : '',
                    'pay_price' => $item['pay_price'],
                    'mark' => $item['mark'],
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất sản phẩm
     * @param $where
     * @return array
     */
    public function exportProductList($where)
    {
        /** @var StoreProductServices $productServices */
        $productServices = app()->make(StoreProductServices::class);
        [$page, $limit] = $this->getPageValue();
        $cateIds = [];
        if (isset($where['cate_id']) && $where['cate_id']) {
            /** @var StoreCategoryServices $storeCategory */
            $storeCategory = app()->make(StoreCategoryServices::class);
            $cateIds = $storeCategory->getColumn(['pid' => (int)$where['cate_id']], 'id');
        }
        if ($cateIds) {
            $cateIds[] = $where['cate_id'];
            $where['cate_id'] = $cateIds;
        }
        $productList = $productServices->dao->getList($where, $page, $limit);
        $header = [
            'ID sản phẩm',
            'Tên sản phẩm', 'Loại sản phẩm', 'Danh mục sản phẩm (cấp 1)', 'Danh mục sản phẩm (cấp 2)', 'Đơn vị tính',
            'Số lượng đã bán', 'Số lượng mua tối thiểu',
            'Loại quy cách', 'Tên quy cách', 'Giá bán', 'Giá gốc', 'Giá vốn', 'Tồn kho', 'Trọng lượng', 'Thể tích', 'Mã sản phẩm', 'Mã vạch',
            'Mô tả ngắn sản phẩm', 'Từ khóa sản phẩm', 'Mã chia sẻ sản phẩm',
            'Mua hàng tặng điểm thưởng'
        ];
        $filename = 'Xuất sản phẩm_' . date('YmdHis', time());
        $virtualType = ['Sản phẩm thường', 'Mã thẻ/Lưu trữ đám mây', 'Phiếu giảm giá', 'Sản phẩm ảo'];
        $export = $fileKey = [];
        if (!empty($productList)) {
            $productList = array_column($productList, null, 'id');
            $productIds = array_column($productList, 'id');
            $descriptionArr = app()->make(StoreDescriptionServices::class)->getColumn([['product_id', 'in', $productIds], ['type', '=', 0]], 'description', 'product_id');
            $cateIds = implode(',', array_column($productList, 'cate_id'));
            /** @var StoreCategoryServices $categoryService */
            $categoryService = app()->make(StoreCategoryServices::class);
            $cateList = $categoryService->getCateParentAndChildName($cateIds);
            $attrResultArr = app()->make(StoreProductAttrResultServices::class)->getColumn([['product_id', 'in', $productIds], ['type', '=', 0]], 'result', 'product_id');
            $i = 0;
            foreach ($attrResultArr as $product_id => &$attrResult) {
                $attrResult = json_decode($attrResult, true);
                foreach ($attrResult['value'] as &$value) {
                    $productInfo = $productList[$product_id];
                    $cateName = array_filter($cateList, function ($val) use ($productInfo) {
                        if (in_array($val['id'], explode(',', $productInfo['cate_id']))) {
                            return $val;
                        }
                    });
                    $skuArr = array_combine(array_column($attrResult['attr'], 'value'), $value['detail']);
                    $attrArr = [];
                    foreach ($attrResult['attr'] as $attrArray) {
                        // Ghép 'value' và 'detail' của mỗi mảng con thành chuỗi
                        if (isset($attrArray['detail'][0]['value'])) {
                            $attrArray['detail'] = array_column($attrArray['detail'], 'value');
                        }
                        $detailString = implode(',', $attrArray['detail']); // Chuyển mảng detail thành chuỗi phân tách bằng dấu phẩy
                        $attrArr[] = $attrArray['value'] . '=' . $detailString;
                    }
                    $attrString = implode(';', $attrArr);
                    if (reset($cateName)['one'] == null) {
                        $cate_name_one = reset($cateName)['two'] ?? '';
                        $cate_name_two = '';
                    } else {
                        $cate_name_one = reset($cateName)['one'] ?? '';
                        $cate_name_two = reset($cateName)['two'] ?? '';
                    }
                    $one_data = [
                        'id' => intval($product_id),
                        'store_name' => $productInfo['store_name'],
                        'virtual_type' => $virtualType[$productInfo['virtual_type']],
                        'cate_name_one' => $cate_name_one,
                        'cate_name_two' => $cate_name_two,
                        'unit_name' => $productInfo['unit_name'],
                        'ficti' => intval($productInfo['ficti']),
                        'min_qty' => intval($productInfo['min_qty']),
                        'spec_type' => intval($productInfo['spec_type']) == 1 ? 'Nhiều quy cách' : 'Một quy cách',
                        'sku_name' => implode(',', $value['detail']),
                        'price' => floatval($value['price']),
                        'ot_price' => floatval($value['ot_price']),
                        'cost' => floatval($value['cost']),
                        'stock' => intval($value['stock']),
                        'weight' => intval($value['weight'] ?? 0),
                        'volume' => intval($value['volume'] ?? 0),
                        'bar_code' => $value['bar_code'] ?? '',
                        'bar_code_number' => $value['bar_code_number'] ?? '',
                        'store_info' => $productInfo['store_info'],
                        'keyword' => $productInfo['keyword'],
                        'command_word' => $productInfo['command_word'],
                        'give_integral' => $productInfo['give_integral'],
                    ];
                    $export[] = $one_data;
                    if ($i == 0) {
                        $fileKey = array_keys($one_data);
                    }
                    $i++;
                }
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất sản phẩm săn giảm giá
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function exportBargainList($where)
    {
        $header = ['Tên săn giảm giá', 'Giá khởi điểm', 'Giá thấp nhất', 'Số người tham gia', 'Số người thành công', 'Tồn kho còn lại', 'Trạng thái chương trình', 'Thời gian chương trình', 'Thời gian thêm'];
        $filename = 'Danh sách săn giảm giá_' . date('YmdHis', time());
        $export = $fileKey = [];
        /** @var StoreBargainServices $bargainServices */
        $bargainServices = app()->make(StoreBargainServices::class);
        $data = $bargainServices->getStoreBargainList($where)['list'];
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $item) {
                $one_data = [
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'min_price' => $item['min_price'],
                    'count_people_all' => $item['count_people_all'],
                    'count_people_success' => $item['count_people_success'],
                    'quota' => $item['quota'],
                    'start_name' => $item['start_name'],
                    'activity_time' => $item['start_time'] . '~' . $item['stop_time'],
                    'add_time' => $item['add_time']
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất sản phẩm mua chung
     * @param $where
     * @return array
     */
    public function exportCombinationList($where)
    {
        $header = ['Tên mua chung', 'Giá mua chung', 'Giá gốc', 'Số người mua chung', 'Số người tham gia', 'Số nhóm thành công', 'Tồn kho còn lại', 'Trạng thái chương trình', 'Thời gian chương trình', 'Thời gian thêm'];
        $filename = 'Danh sách mua chung_' . date('YmdHis', time());
        $export = $fileKey = [];
        /** @var StoreCombinationServices $combinationServices */
        $combinationServices = app()->make(StoreCombinationServices::class);
        $data = $combinationServices->systemPage($where)['list'];
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $item) {
                $one_data = [
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'ot_price' => $item['ot_price'],
                    'count_people' => $item['count_people'],
                    'count_people_all' => $item['count_people_all'],
                    'count_people_pink' => $item['count_people_pink'],
                    'quota' => $item['quota'],
                    'start_name' => $item['start_name'],
                    'activity_time' => $item['start_time'] . '~' . $item['stop_time'],
                    'add_time' => $item['add_time']
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất flash sale
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function exportSeckillList($where)
    {
        $header = ['Tên flash sale', 'Giá flash sale', 'Giá gốc', 'Tồn kho còn lại', 'Trạng thái chương trình', 'Thời gian chương trình', 'Thời gian thêm'];
        $filename = 'Danh sách flash sale_' . date('YmdHis', time());
        $export = $fileKey = [];
        /** @var StoreSeckillServices $seckillServices */
        $seckillServices = app()->make(StoreSeckillServices::class);
        $data = $seckillServices->systemPage($where)['list'];
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $item) {
                $one_data = [
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'ot_price' => $item['ot_price'],
                    'quota' => $item['quota'],
                    'start_name' => $item['start_name'],
                    'activity_time' => $item['start_time'] . '~' . $item['stop_time'],
                    'add_time' => $item['add_time']
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Xuất thẻ thành viên
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function exportMemberCard($id)
    {
        /** @var MemberCardServices $memberCardServices */
        $memberCardServices = app()->make(MemberCardServices::class);
        $data = $memberCardServices->getExportData(['batch_card_id' => $id]);
        $header = ['Số thẻ thành viên', 'Mật khẩu', 'Người nhận', 'Số điện thoại người nhận', 'Thời gian nhận', 'Đã sử dụng'];
        $filename = $data['title'] . 'danh sách lô_' . date('YmdHis', time());
        $export = $fileKey = [];
        if (!empty($data['data'])) {
            $userIds = array_column($data['data']->toArray(), 'use_uid');
            /** @var  UserServices $userService */
            $userService = app()->make(UserServices::class);
            $userList = $userService->getColumn([['uid', 'in', $userIds]], 'nickname,phone,real_name', 'uid');


            $i = 0;
            foreach ($data['data'] as $item) {
                $one_data = [
                    'card_number' => $item['card_number'],
                    'card_password' => $item['card_password'],
                    'user_name' => $userList[$item['use_uid']]['real_name'] ?? $userList[$item['use_uid']]['nickname'] ?? '',
                    'user_phone' => $userList[$item['use_uid']]['phone'] ?? "",
                    'use_time' => $item['use_time'],
                    'use_uid' => $item['use_uid'] ? 'Đã nhận' : 'Chưa nhận'
                ];
                $export[] = $one_data;
                if ($i == 0) {
                    $fileKey = array_keys($one_data);
                }
                $i++;
            }
        }
        return compact('header', 'fileKey', 'export', 'filename');
    }

    /**
     * Yêu cầu xuất dữ liệu thực tế
     * @param $header Tiêu đề bảng Excel
     * @param $title Tiêu đề
     * @param array $export Điền dữ liệu
     * @param string $filename Lưu tên file
     * @param string $suffix Lưu phần mở rộng file
     * @param bool $is_save true|false Có lưu vào cục bộ không
     * @return mixed
     */
    public function export($header, $title_arr, $export = [], $filename = '', $suffix = 'xlsx', $is_save = false)
    {
        $title = isset($title_arr[0]) && !empty($title_arr[0]) ? $title_arr[0] : 'Dữ liệu xuất';
        $name = isset($title_arr[1]) && !empty($title_arr[1]) ? $title_arr[1] : 'Dữ liệu xuất';
        $info = isset($title_arr[2]) && !empty($title_arr[2]) ? $title_arr[2] : date('Y-m-d H:i:s', time());

        $path = SpreadsheetExcelService::instance()->setExcelHeader($header)
            ->setExcelTile($title, $name, $info)
            ->setExcelContent($export)
            ->excelSave($filename, $suffix, $is_save);
        $path = $this->siteUrl() . $path;
        return [$path];
    }

    /**
     * Lấy domain API hệ thống
     * @return string
     */
    public function siteUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol . $domainName;
    }


    /**
     * Xuất dòng tiền người dùng
     * @param $data Dữ liệu xuất
     */
    public function userFinance($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $value) {
                $export[] = [
                    $value['uid'],
                    $value['nickname'],
                    $value['pm'] == 0 ? '-' . $value['number'] : $value['number'],
                    $value['title'],
                    $value['mark'],
                    $value['add_time'],
                ];
            }
        }
        $header = ['ID thành viên', 'Biệt danh', 'Số tiền/Điểm thưởng', 'Loại', 'Ghi chú', 'Thời gian tạo'];
        $title = ['Theo dõi tài chính', 'Theo dõi tài chính', date('Y-m-d H:i:s', time())];
        $filename = 'Theo dõi tài chính_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất hoa hồng người dùng
     * @param $data Dữ liệu xuất
     */
    public function userCommission($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as &$value) {
                $export[] = [
                    $value['nickname'],
                    $value['sum_number'],
                    $value['now_money'],
                    $value['brokerage_price'],
                    $value['extract_price'],
                ];
            }
        }
        $header = ['Biệt danh/Họ tên', 'Tổng tiền hoa hồng', 'Số dư tài khoản', 'Hoa hồng trong tài khoản', 'Hoa hồng đã rút về tài khoản'];
        $title = ['Lịch sử hoa hồng', 'Lịch sử hoa hồng' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Lịch sử hoa hồng_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất điểm thưởng người dùng
     * @param $data Dữ liệu xuất
     */
    public function userPoint($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $export[] = [
                    $item['id'],
                    $item['title'],
                    $item['balance'],
                    $item['number'],
                    $item['mark'],
                    $item['nickname'],
                    $item['add_time'],
                ];
            }
        }
        $header = ['Mã số', 'Tiêu đề', 'Điểm thưởng trước thay đổi', 'Biến động điểm thưởng', 'Ghi chú', 'Biệt danh WeChat của người dùng', 'Thời gian thêm'];
        $title = ['Nhật ký điểm thưởng', 'Nhật ký điểm thưởng' . time(), 'Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Nhật ký điểm thưởng_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất lịch sử nạp tiền người dùng
     * @param $data Dữ liệu xuất
     */
    public function userRecharge($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                $item['_pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : 'Chưa có';
                $item['_add_time'] = $item['add_time'] ? date('Y-m-d H:i:s', $item['add_time']) : 'Chưa có';
                $item['paid_type'] = $item['paid'] ? 'Đã thanh toán' : 'Chưa thanh toán';

                $export[] = [
                    $item['nickname'],
                    $item['order_id'],
                    $item['price'],
                    $item['paid_type'],
                    $item['_recharge_type'],
                    $item['_pay_time'],
                    $item['paid'] == 1 && $item['refund_price'] == $item['price'] ? 'Đã hoàn tiền' : 'Chưa hoàn tiền'
                ];
            }
        }
        $header = ['Biệt danh/Họ tên', 'Mã đơn hàng', 'Số tiền nạp', 'Đã thanh toán', 'Loại nạp tiền', 'Thời gian thanh toán', 'Đã hoàn tiền'];
        $title = ['Lịch sử nạp tiền', 'Lịch sử nạp tiền' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Lịch sử nạp tiền_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất dữ liệu giới thiệu người dùng
     * @param $data Dữ liệu xuất
     */
    public function userAgent($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $index => $item) {
                $export[] = [
                    $item['uid'],
                    $item['nickname'],
                    $item['phone'],
                    $item['spread_count'],
                    $item['spread_order']['order_count'],
                    $item['spread_order']['order_price'],
                    $item['brokerage_money'],
                    $item['extract_count_price'],
                    $item['extract_count_num'],
                    $item['brokerage_price'],
                    $item['spread_name'],
                ];
            }
        }
        $header = ['ID người dùng', 'Biệt danh', 'Số điện thoại', 'Số người dùng được giới thiệu', 'Số đơn hàng giới thiệu', 'Giá trị đơn hàng giới thiệu', 'Số tiền hoa hồng', 'Số tiền đã rút', 'Số lần rút tiền', 'Số tiền chưa rút', 'Người giới thiệu cấp trên'];
        $title = ['Cộng tác viên', 'Xuất cộng tác viên' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Cộng tác viên_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất người dùng WeChat
     * @param $data Dữ liệu xuất
     */
    public function wechatUser($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $index => $item) {
                $export[] = [
                    $item['nickname'],
                    $item['sex'],
                    $item['country'] . $item['province'] . $item['city'],
                    $item['subscribe'] == 1 ? 'Đã theo dõi' : 'Chưa theo dõi',
                ];
            }
        }
        $header = ['Tên', 'Giới tính', 'Khu vực', 'Theo dõi OA WeChat'];
        $title = ['Xuất người dùng WeChat', 'Xuất người dùng WeChat' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Xuất người dùng WeChat_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất dữ liệu tài chính đơn hàng
     * @param $data Dữ liệu xuất
     */
    public function orderFinance($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $info) {
                $time = $info['pay_time'];
                $price = $info['total_price'] + $info['pay_postage'];
                $zhichu = $info['coupon_price'] + $info['deduction_price'] + $info['cost'];
                $profit = ($info['total_price'] + $info['pay_postage']) - ($info['coupon_price'] + $info['deduction_price'] + $info['cost']);
                $deduction = $info['deduction_price'];//Khấu trừ bằng điểm thưởng
                $coupon = $info['coupon_price'];//Ưu đãi
                $cost = $info['cost'];//Giá vốn
                $export[] = [$time, $price, $zhichu, $cost, $coupon, $deduction, $profit];
            }
        }
        $header = ['Thời gian', 'Doanh thu (đ)', 'Khoản chi (đ)', 'Giá vốn', 'Ưu đãi', 'Khấu trừ bằng điểm thưởng', 'Lợi nhuận (đ)'];
        $title = ['Thống kê tài chính', 'Thống kê tài chính', date('Y-m-d H:i:s', time())];
        $filename = 'Thống kê tài chính_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất hoạt động săn giảm giá của shop
     * @param $data Dữ liệu xuất
     */
    public function storeBargain($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $index => $item) {
                $export[] = [
                    $item['title'],
                    $item['info'],
                    '￥' . $item['price'],
                    $item['bargain_num'],
                    $item['status'] ? 'Bật' : 'Tắt',
                    empty($item['start_time']) ? '' : date('Y-m-d H:i:s', (int)$item['start_time']),
                    empty($item['stop_time']) ? '' : date('Y-m-d H:i:s', (int)$item['stop_time']),
                    $item['sales'],
                    $item['quota'],
                    empty($item['add_time']) ? '' : $item['add_time'],
                ];
            }
        }
        $header = ['Tên chương trình săn giảm giá', 'Giới thiệu chương trình săn giảm giá', 'Số tiền săn giảm giá', 'Số lần săn giảm giá của mỗi người dùng', 'Trạng thái săn giảm giá', 'Thời gian bắt đầu săn giảm giá', 'Thời gian kết thúc săn giảm giá', 'Lượt bán', 'Giới hạn số lượng', 'Thời gian thêm'];
        $title = ['Xuất sản phẩm săn giảm giá', 'Thông tin sản phẩm' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Xuất sản phẩm săn giảm giá_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất dữ liệu mua chung của cửa hàng
     * @param $data Dữ liệu xuất
     */
    public function storeCombination($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                $export[] = [
                    $item['id'],
                    $item['title'],
                    $item['ot_price'],
                    $item['price'],
                    $item['quota'],
                    $item['count_people'],
                    $item['count_people_all'],
                    $item['count_people_pink'],
                    $item['sales'] ?? 0,
                    $item['is_show'] ? 'Bật' : 'Tắt',
                    empty($item['stop_time']) ? '' : date('Y/m/d H:i:s', (int)$item['stop_time'])
                ];
            }
        }
        $header = ['Mã số', 'Tên mua chung', 'Giá gốc', 'Giá mua chung', 'Giới hạn số lượng', 'Số người mua chung', 'Số người tham gia', 'Số nhóm thành công', 'Lượt bán', 'Trạng thái sản phẩm', 'Thời gian kết thúc'];
        $title = ['Xuất sản phẩm mua chung', 'Thông tin sản phẩm' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Xuất sản phẩm mua chung_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất dữ liệu chương trình flash sale của cửa hàng
     * @param $data Dữ liệu xuất
     */
    public function storeSeckill($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                if ($item['status']) {
                    if ($item['start_time'] > time())
                        $item['start_name'] = 'Chương trình chưa bắt đầu';
                    else if ($item['stop_time'] < time())
                        $item['start_name'] = 'Chương trình đã kết thúc';
                    else if ($item['stop_time'] > time() && $item['start_time'] < time())
                        $item['start_name'] = 'Đang diễn ra';
                } else {
                    $item['start_name'] = 'Chương trình đã kết thúc';
                }
                $export[] = [
                    $item['id'],
                    $item['title'],
                    $item['info'],
                    $item['ot_price'],
                    $item['price'],
                    $item['quota'],
                    $item['sales'],
                    $item['start_name'],
                    $item['stop_time'] ? date('Y-m-d H:i:s', $item['stop_time']) : '/',
                    $item['status'] ? 'Bật' : 'Tắt',
                ];
            }
        }
        $header = ['Mã số', 'Tiêu đề chương trình', 'Giới thiệu chương trình', 'Giá gốc', 'Giá flash sale', 'Giới hạn số lượng', 'Lượt bán', 'Trạng thái flash sale', 'Thời gian kết thúc', 'Trạng thái'];
        $title = ['Xuất sản phẩm flash sale', ' ', ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Xuất sản phẩm flash sale_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất dữ liệu sản phẩm của cửa hàng
     * @param $data Dữ liệu xuất
     */
    public function storeProduct($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $index => $item) {
                $export[] = [
                    $item['store_name'],
                    $item['store_info'],
                    $item['cate_name'],
                    '￥' . $item['price'],
                    $item['stock'],
                    $item['sales'],
                    $item['visitor'],
                ];
            }
        }
        $header = ['Tên sản phẩm', 'Mô tả ngắn sản phẩm', 'Danh mục sản phẩm', 'Giá', 'Tồn kho', 'Lượt bán', 'Lượt xem'];
        $title = ['Xuất sản phẩm', 'Thông tin sản phẩm' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Xuất sản phẩm_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }


    /**
     * Xuất dữ liệu điểm nhận hàng của cửa hàng
     * @param $data Dữ liệu xuất
     */
    public function storeMerchant($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $index => $item) {
                $export[] = [
                    $item['name'],
                    $item['phone'],
                    $item['address'] . '' . $item['detailed_address'],
                    $item['day_time'],
                    $item['is_show'] ? 'Bật' : 'Tắt'
                ];
            }
        }
        $header = ['Tên điểm nhận hàng', 'Điểm nhận hàng', 'Địa chỉ', 'Giờ mở cửa', 'Trạng thái'];
        $title = ['Xuất điểm nhận hàng', 'Thông tin điểm nhận hàng' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Xuất điểm nhận hàng_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    public function memberCard($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data['data'] as $index => $item) {
                $export[] = [
                    $item['card_number'],
                    $item['card_password'],
                    $item['user_name'],
                    $item['user_phone'],
                    $item['use_time'],
                    $item['use_uid'] ? 'Đã nhận' : 'Chưa nhận'
                ];
            }
        }
        $header = ['Số thẻ thành viên', 'Mật khẩu', 'Người nhận', 'Số điện thoại người nhận', 'Thời gian nhận', 'Đã sử dụng'];
        $title = ['Xuất thẻ thành viên', 'Xuất thẻ thành viên' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = $data['title'] ? ("Thẻ kích hoạt thành viên_" . trim(str_replace(["\r\n", "\r", "\\", "\n", "/", "<", ">", "=", " "], '', $data['title']))) : "";
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    public function tradeData($data = [], $tradeTitle = "Thống kê giao dịch")
    {
        $export = $header = [];
        if (!empty($data)) {
            $header = ['Thời gian'];
            $headerArray = array_column($data['series'], 'name');
            $header = array_merge($header, $headerArray);
            $export = [];
            foreach ($data['series'] as $index => $item) {
                foreach ($data['x'] as $k => $v) {
                    $export[$v]['time'] = $v;
                    $export[$v][] = $item['value'][$k];
                }
            }
        }
        $title = [$tradeTitle, $tradeTitle, ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = $tradeTitle;
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }


    /**
     * Thống kê sản phẩm
     * @param $data Dữ liệu xuất
     */
    public function productTrade($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as &$value) {
                $export[] = [
                    $value['time'],
                    $value['browse'],
                    $value['user'],
                    $value['cart'],
                    $value['order'],
                    $value['payNum'],
                    $value['pay'],
                    $value['cost'],
                    $value['refund'],
                    $value['refundNum'],
                    $value['changes'] . '%'
                ];
            }
        }
        $header = ['Ngày/Giờ', 'Lượt xem sản phẩm', 'Số khách truy cập sản phẩm', 'Số lượng thêm vào giỏ', 'Số lượng đặt hàng', 'Số lượng thanh toán', 'Số tiền thanh toán', 'Tổng giá vốn', 'Số tiền hoàn', 'Số lượng hoàn tiền', 'Tỷ lệ chuyển đổi khách truy cập - thanh toán'];
        $title = ['Thống kê sản phẩm', 'Thống kê sản phẩm' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Thống kê sản phẩm_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    public function userTrade($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as &$value) {
                $export[] = [
                    $value['time'],
                    $value['user'],
                    $value['browse'],
                    $value['new'],
                    $value['paid'],
                    $value['vip'],
                ];
            }
        }
        $header = ['Ngày/Giờ', 'Số khách truy cập', 'Lượt xem', 'Số người dùng mới', 'Số khách hàng đã mua', 'Số thành viên trả phí'];
        $title = ['Thống kê người dùng', 'Thống kê người dùng' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Thống kê người dùng_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }

    /**
     * Xuất lịch sử xác nhận sử dụng
     * @param array $data
     * @return mixed|string[]
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/9/9
     */
    public function verifyOrder($data = [])
    {
        $export = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                $productName = '';
                foreach ($item['_info'] as $productInfo) {
                    $productName .= $productInfo['cart_info']['productInfo']['store_name'] . ' ';
                }
                $export[] = [
                    $item['order_id'],
                    $item['real_name'] . '/' . $item['uid'],
                    $productName,
                    $item['pay_price'],
                    $item['clerk_name'],
                    $item['store_name'],
                    $item['pay_type_name'],
                    $item['status_name']['status_name'],
                    $item['add_time'],
                ];
            }
        }
        $header = ['Mã đơn hàng', 'Thông tin người dùng', 'Thông tin sản phẩm', 'Số tiền thanh toán', 'Nhân viên xác nhận', 'Cửa hàng xác nhận sử dụng', 'Trạng thái thanh toán', 'Trạng thái đơn hàng', 'Thời gian đặt hàng'];
        $title = ['Xuất lịch sử xác nhận sử dụng', 'Xuất lịch sử xác nhận sử dụng' . time(), ' Thời gian tạo:' . date('Y-m-d H:i:s', time())];
        $filename = 'Lịch sử xác nhận sử dụng_' . date('YmdHis', time());
        $suffix = 'xlsx';
        $is_save = true;
        return $this->export($header, $title, $export, $filename, $suffix, $is_save);
    }
}
