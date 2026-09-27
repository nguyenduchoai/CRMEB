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
namespace app\services\system;

use app\dao\system\SystemTicketDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use crmeb\services\printer\Printer;

class SystemTicketServices extends BaseServices
{
    public function __construct(SystemTicketDao $dao)
    {
        $this->dao = $dao;
    }

    public function ticketList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->ticketList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = $this->dao->ticketCount($where);
        return compact('list', 'count');
    }

    public function ticketForm($id)
    {
        $info = $this->dao->get($id) ?? [];
        if ($info) $info = $info->toArray();
        $field[] = Form::input('print_name', 'Tên máy in', $info['print_name'] ?? '')->required('Vui lòng nhập tên máy in')->placeholder('Tên máy in');
        $field[] = Form::radio('type', 'Chọn nền tảng', $info['type'] ?? 1)
            ->options([['label' => 'Yilianyun', 'value' => 1], ['label' => 'Feieyun', 'value' => 2]])
            ->appendControl(1, [
                    Form::input('yly_user_id', 'ID người dùng:', $info['yly_user_id'] ?? '')->required('Vui lòng nhập ID người dùng')->placeholder('ID nhà phát triển Yilianyun'),
                    Form::input('yly_app_id', 'ID ứng dụng:', $info['yly_app_id'] ?? '')->required('Vui lòng nhập ID ứng dụng')->placeholder('ID ứng dụng Yilianyun'),
                    Form::input('yly_app_secret', 'Khóa ứng dụng:', $info['yly_app_secret'] ?? '')->required('Vui lòng nhập khóa ứng dụng')->placeholder('Khóa ứng dụng Yilianyun'),
                    Form::input('yly_sn', 'Mã thiết bị:', $info['yly_sn'] ?? '')->required('Vui lòng nhập mã thiết bị')->placeholder('Mã thiết bị máy in Yilianyun, model máy in: máy in Yilianyun K4 bản không dây'),
                ]
            )->appendControl(2, [
                    Form::input('fey_user', 'Feieyun USER:', $info['fey_user'] ?? '')->required('Vui lòng nhập Feieyun USER')->placeholder('Tài khoản đăng ký trên trang quản trị Feieyun'),
                    Form::input('fey_ukey', 'Feieyun UYEK:', $info['fey_ukey'] ?? '')->required('Vui lòng nhập Feieyun UYEK')->placeholder('UKEY được tạo sau khi đăng ký tài khoản trên trang quản trị Feieyun [Lưu ý: đây không phải KEY của máy in]'),
                    Form::input('fey_sn', 'Feieyun SN:', $info['fey_sn'] ?? '')->required('Vui lòng nhập Feieyun SN')->placeholder('Mã số trên nhãn máy in, bắt buộc phải thêm máy in trong trang quản trị hoặc gọi API'),
                ]
            );
        $field[] = Form::number('times', 'Số liên in', $info['times'] ?? 1)->min(1)->required('Vui lòng nhập số liên in')->placeholder('Số tờ in ra trong mỗi lần in');
        $field[] = Form::radio('print_type', 'Thời điểm in', $info['print_type'] ?? 1)->options([['label' => 'In sau khi thanh toán', 'value' => 1], ['label' => 'In sau khi đặt hàng', 'value' => 2]]);
        $field[] = Form::radio('status', 'Bật/tắt in', $info['status'] ?? 1)->options([['label' => 'Bật', 'value' => 1], ['label' => 'Tắt', 'value' => 0]]);
        return create_form('In biên lai', $field, $this->url('/system/ticket/save/' . $id), 'POST');
    }

    public function ticketSave($id, $data)
    {
        if ($id) {
            $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->dao->save($data);
        }
        return true;
    }

    public function ticketSetStatus($id, $status)
    {
        $this->dao->update($id, ['status' => $status]);
        return true;
    }

    public function ticketDel($id)
    {
        $this->dao->update($id, ['is_del' => 1]);
        return true;
    }

    public function ticketContent($id)
    {
        $print_content = $this->dao->value(['id' => $id], 'print_content');
        return $print_content ? json_decode($print_content, true) : [];
    }

    public function ticketContentSave($id, $data)
    {
        $print_content = json_encode($data);
        $this->dao->update($id, ['print_content' => $print_content]);
        return true;
    }

    public function startPrint($order, $product, $print_type = 1)
    {
        $where = $print_type === true ? ['status' => 1] : ['status' => 1, 'print_type' => $print_type];
        $list = $this->dao->ticketList($where);
        foreach ($list as $item) {
            if ($item['type'] == 1) { //Yilianyun
                $name = 'yi_lian_yun';
                $configData = [
                    'partner' => $item['yly_user_id'],
                    'clientId' => $item['yly_app_id'],
                    'apiKey' => $item['yly_app_secret'],
                    'terminal' => $item['yly_sn']
                ];
                $print_content = json_decode($item['print_content'], true);
                if (is_null($print_content) || !count($print_content)) throw new AdminException('Vui lòng cấu hình nội dung in trước');
                $content = $this->ylyContent($print_content, $order, $product, $item['times'], $print_type);
            } else { //Feieyun
                $name = 'fei_e_yun';
                $configData = [
                    'feyUser' => $item['fey_user'],
                    'feyUkey' => $item['fey_ukey'],
                    'feySn' => $item['fey_sn']
                ];
                $print_content = json_decode($item['print_content'], true);
                if (is_null($print_content) || !count($print_content)) throw new AdminException('Vui lòng cấu hình nội dung in trước');
                $content = $this->feyContent($print_content, $order, $product, $print_type);
            }
            $printer = new Printer($name, $configData);
            $printer->setPrinterContent($content, $item['times'])->startPrinter();
        }
    }

    public function ylyContent($printContent, $orderInfo, $product, $times, $print_type)
    {
        $goodsStr = '<table><tr><td>Tên hàng</td><td>Đơn giá</td><td>SL</td><td>Thành tiền</td></tr>';
        foreach ($product as $item) {
            $goodsStr .= '<tr><td><FH2><FW2>----------------</FW2></FH2></td></tr>';
            $goodsStr .= '<tr>';
            $price = $item['sum_price'];
            $num = $item['cart_num'];
            $prices = bcmul((string)$item['cart_num'], (string)$item['sum_price'], 2);
            $goodsStr .= "<td>{$item['productInfo']['store_name']} | {$item['productInfo']['attrInfo']['suk']}</td><td>{$price}</td><td>{$num}</td><td>{$prices}</td>";
            $goodsStr .= '</tr>';
            if (in_array(1, $printContent['goods'])) {
                $goodsStr .= '<tr>';
                $goodsStr .= "<td>Mã quy cách:{$item['productInfo']['attrInfo']['bar_code']}</td>";
                $goodsStr .= '</tr>';
            }
            unset($price, $num, $prices);
        }
        $goodsStr .= '</table>';
        $total_price = bcadd($orderInfo['total_price'], $orderInfo['pay_postage'], 2);
        $addTime = date('Y-m-d H:i:s', $orderInfo['add_time']);
        $payTime = isset($orderInfo['pay_time']) ? date('Y-m-d H:i:s', $orderInfo['pay_time']) : '';
        $printTime = date('Y-m-d H:i:s', time());

        $content = '';
        $content .= '<MN>' . $times . '</MN>';
        if ($printContent['header']) {
            $content .= '<FS2><center>' . sys_config('site_name') . '</center></FS2>';
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if ($printContent['delivery']) {
            if ($orderInfo['shipping_type'] == 1) {
                $content .= 'Phương thức giao hàng: Cửa hàng tự giao \r';
            } else {
                $content .= 'Phương thức giao hàng: Nhận tại cửa hàng \r';
            }
            $content .= 'Họ tên khách hàng: ' . $orderInfo['real_name'] . ' \r';
            $content .= 'SĐT khách hàng: ' . $orderInfo['user_phone'] . ' \r';
            if ($orderInfo['shipping_type'] == 1) $content .= 'Địa chỉ nhận hàng: ' . $orderInfo['user_address'] . ' \r';
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if ($printContent['buyer_remarks']) {
            $content .= 'Ghi chú của người mua: ' . $orderInfo['mark'] . ' \r';
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if (in_array(0, $printContent['goods'])) {
            $content .= '************Sản phẩm************';
            $content .= '      \r';
            $content .= $goodsStr;
            $content .= '********************************\r';
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
            $content .= '<RA>Tổng cộng:' . $total_price . 'đ</RA>';
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if ($printContent['preferential'] || $printContent['freight']) {
            if ($printContent['freight']) {
                $content .= '<RA>Phí vận chuyển:' . $orderInfo['pay_postage'] . 'đ</RA>';
            }
            if ($printContent['preferential']) {
                $discount_price = bcsub(bcadd($orderInfo['total_price'], $orderInfo['pay_postage'], 2), bcadd($orderInfo['deduction_price'], $orderInfo['pay_price'], 2), 2);
                $content .= '<RA>Giảm giá: -' . $discount_price . 'đ</RA>';
                $content .= '<RA>Khấu trừ: -' . $orderInfo['deduction_price'] . 'đ</RA>';
            }
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if (in_array(0, $printContent['pay'])) {
            if ($print_type == 1) {
                switch ($orderInfo['pay_type']) {
                    case 'weixin':
                        $content .= '<RA>Phương thức thanh toán: WeChat Pay</RA>';
                        break;
                    case 'alipay':
                        $content .= '<RA>Phương thức thanh toán: Alipay</RA>';
                        break;
                    case 'yue':
                        $content .= '<RA>Phương thức thanh toán: Số dư</RA>';
                        break;
                    case 'offline':
                        $content .= '<RA>Phương thức thanh toán: Ngoại tuyến</RA>';
                        break;
                    default:
                        $content .= '<RA>Phương thức thanh toán: Chưa có</RA>';
                        break;
                }
            } else {
                $content .= '<RA>Phương thức thanh toán: Chưa có</RA>';
            }
        }
        if (in_array(1, $printContent['pay'])) {
            $content .= '<RA>Thanh toán thực tế:' . $orderInfo['pay_price'] . 'đ</RA>';
        }
        if (count($printContent['pay'])) {
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if (in_array(0, $printContent['order'])) {
            $content .= 'Mã đơn hàng:' . $orderInfo['order_id'] . '\r';
        }
        if (in_array(1, $printContent['order'])) {
            $content .= 'Thời gian đặt hàng:' . $addTime . '\r';
        }
        if (in_array(2, $printContent['order'])) {
            $content .= 'Thời gian thanh toán:' . $payTime . '\r';
        }
        if (in_array(3, $printContent['order'])) {
            $content .= 'Thời gian in:' . $printTime . '\r';
        }
        if (count($printContent['order'])) {
            $content .= '<FH2><FW2>----------------</FW2></FH2>';
        }
        if ($printContent['code'] && $printContent['code_url']) {
            $content .= '<QR>' . sys_config('site_url') . $printContent['code_url'] . '</QR>';
            $content .= '      \r';
        }
        if ($printContent['show_notice']) {
            $content .= '<center>' . $printContent['notice_content'] . '</center>';
            $content .= '      \r';
        }
        return $content;
    }

    public function feyContent($printContent, $orderInfo, $product, $print_type)
    {
        $printTime = date('Y-m-d H:i:s', time());
        $addTime = date('Y-m-d H:i:s', $orderInfo['add_time']);
        $payTime = isset($orderInfo['pay_time']) ? date('Y-m-d H:i:s', $orderInfo['pay_time']) : '';
        $content = '';
        if ($printContent['header']) {
            $content .= '<CB>' . sys_config('site_name') . '</CB><BR>';
            $content .= '--------------------------------<BR>';
        }
        if ($printContent['delivery']) {
            if ($orderInfo['shipping_type'] == 1) {
                $content .= 'Phương thức giao hàng: Cửa hàng tự giao<BR>';
            } else {
                $content .= 'Phương thức giao hàng: Nhận tại cửa hàng<BR>';
            }
            $content .= 'Họ tên khách hàng: ' . $orderInfo['real_name'] . '<BR>';
            $content .= 'SĐT khách hàng: ' . $orderInfo['user_phone'] . '<BR>';
            if ($orderInfo['shipping_type'] == 1) $content .= 'Địa chỉ nhận hàng:' . $orderInfo['user_address'] . '<BR>';
            $content .= '--------------------------------<BR>';
        }
        if ($printContent['buyer_remarks']) {
            $content .= 'Ghi chú của người mua:' . $orderInfo['mark'] . '<BR>';
            $content .= '--------------------------------<BR>';
        }
        if (in_array(0, $printContent['goods'])) {
            $content .= '<BR>';
            $content .= '************Sản phẩm************<BR>';
            $content .= '<BR>';
            $content .= 'Tên hàng       Đ.giá  SL  T.tiền<BR>';
            foreach ($product as $item) {
                $content .= '--------------------------------<BR>';
                $name = $item['productInfo']['store_name'] . " | " . $item['productInfo']['attrInfo']['suk'];
                $price = $item['sum_price'];
                $num = $item['cart_num'];
                $prices = bcmul((string)$item['cart_num'], (string)$item['sum_price'], 2);
                $kw3 = '';
                $kw1 = '';
                $kw2 = '';
                $kw4 = '';
                $str = $name;
                $blankNum = 14;//Tên giới hạn 14 byte
                $lan = mb_strlen($str, 'utf-8');
                $m = 0;
                $j = 1;
                $blankNum++;
                $result = array();
                if (strlen($price) < 6) {
                    $k1 = 6 - strlen($price);
                    for ($q = 0; $q < $k1; $q++) {
                        $kw1 .= ' ';
                    }
                    $price = $price . $kw1;
                }
                if (strlen($num) < 3) {
                    $k2 = 3 - strlen($num);
                    for ($q = 0; $q < $k2; $q++) {
                        $kw2 .= ' ';
                    }
                    $num = $num . $kw2;
                }
                if (strlen($prices) < 6) {
                    $k3 = 6 - strlen($prices);
                    for ($q = 0; $q < $k3; $q++) {
                        $kw4 .= ' ';
                    }
                    $prices = $prices . $kw4;
                }
                for ($i = 0; $i < $lan; $i++) {
                    $new = mb_substr($str, $m, $j, 'utf-8');
                    $j++;
                    if (mb_strwidth($new, 'utf-8') < $blankNum) {
                        if ($m + $j > $lan) {
                            $m = $m + $j;
                            $tail = $new;
                            $lenght = iconv("UTF-8", "GBK//IGNORE", $new);
                            $k = 14 - strlen($lenght);
                            for ($q = 0; $q < $k; $q++) {
                                $kw3 .= ' ';
                            }
                            if ($m == $j) {
                                $tail .= $kw3 . ' ' . $price . ' ' . $num . ' ' . $prices;
                            } else {
                                $tail .= $kw3 . '<BR>';
                            }
                            break;
                        } else {
                            $next_new = mb_substr($str, $m, $j, 'utf-8');
                            if (mb_strwidth($next_new, 'utf-8') < $blankNum) {
                                continue;
                            } else {
                                $m = $i + 1;
                                $result[] = $new;
                                $j = 1;
                            }
                        }
                    }
                }
                $head = '';
                foreach ($result as $key => $value) {
                    if ($key < 1) {
                        $v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
                        $v_lenght = strlen($v_lenght);
                        if ($v_lenght == 13) $value = $value . " ";
                        $head .= $value . ' ' . $price . ' ' . $num . ' ' . $prices;
                    } else {
                        $head .= $value . '<BR>';
                    }
                }
                $content .= $head . $tail;
                if (in_array(1, $printContent['goods'])) {
                    $content .= 'Mã quy cách:' . $item['productInfo']['attrInfo']['bar_code'] . '<BR>';
                }
                unset($price);
            }
            $content .= '<BR>';
            $content .= '********************************<BR>';
            $content .= '<BR>';
            $content .= '--------------------------------<BR>';
            $total_price = bcadd($orderInfo['total_price'], $orderInfo['pay_postage'], 2);
            $content .= '<RIGHT>Tổng cộng:' . number_format($total_price, 2) . 'đ</RIGHT>';
            $content .= '--------------------------------<BR>';
        }
        if ($printContent['preferential'] || $printContent['freight']) {
            if ($printContent['freight']) {
                $content .= '<RIGHT>Phí vận chuyển:' . number_format($orderInfo['pay_postage'], 2) . 'đ</RIGHT><BR>';
            }
            if ($printContent['preferential']) {
                $discount_price = bcsub(bcadd($orderInfo['total_price'], $orderInfo['pay_postage'], 2), bcadd($orderInfo['deduction_price'], $orderInfo['pay_price'], 2), 2);
                $content .= '<RIGHT>Giảm giá: -' . number_format($discount_price, 2) . 'đ</RIGHT><BR>';
                $content .= '<RIGHT>Khấu trừ: -' . number_format($orderInfo['deduction_price'], 2) . 'đ</RIGHT>';
            }
            $content .= '--------------------------------<BR>';
        }
        if (in_array(0, $printContent['pay'])) {
            if ($print_type == 1) {
                switch ($orderInfo['pay_type']) {
                    case 'weixin':
                        $content .= '<RIGHT>Phương thức thanh toán: WeChat Pay</RIGHT><BR>';
                        break;
                    case 'alipay':
                        $content .= '<RIGHT>Phương thức thanh toán: Alipay</RIGHT><BR>';
                        break;
                    case 'yue':
                        $content .= '<RIGHT>Phương thức thanh toán: Số dư</RIGHT><BR>';
                        break;
                    case 'offline':
                        $content .= '<RIGHT>Phương thức thanh toán: Ngoại tuyến</RIGHT><BR>';
                        break;
                    default:
                        $content .= '<RIGHT>Phương thức thanh toán: Chưa có</RIGHT><BR>';
                        break;
                }
            } else {
                $content .= '<RIGHT>Phương thức thanh toán: Chưa có</RIGHT><BR>';
            }
        }
        if (in_array(1, $printContent['pay'])) {
            $content .= '<RIGHT>Thanh toán thực tế:' . number_format($orderInfo['pay_price'], 2) . 'đ</RIGHT>';
        }
        if (count($printContent['pay'])) {
            $content .= '--------------------------------<BR>';
        }
        if (in_array(0, $printContent['order'])) {
            $content .= 'Mã đơn hàng:' . $orderInfo['order_id'] . '<BR>';
        }
        if (in_array(1, $printContent['order'])) {
            $content .= 'Thời gian đặt hàng: ' . $addTime . '<BR>';
        }
        if (in_array(2, $printContent['order'])) {
            $content .= 'Thời gian thanh toán: ' . $payTime . '<BR>';
        }
        if (in_array(3, $printContent['order'])) {
            $content .= 'Thời gian in: ' . $printTime . '<BR>';
        }
        if (count($printContent['order'])) {
            $content .= '--------------------------------<BR>';
            $content .= '<BR>';
        }
        if ($printContent['code'] && $printContent['code_url']) {
            $content .= '<QR>' . sys_config('site_url') . $printContent['code_url'] . '</QR>';
        }
        if ($printContent['show_notice']) {
            $content .= '<C>' . $printContent['notice_content'] . '</C>';
        }
        return $content;
    }
}
