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

namespace crmeb\services\app;

use app\services\order\StoreOrderTakeServices;
use app\services\pay\PayServices;
use app\services\system\SystemPemServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\easywechat\orderShipping\MiniOrderService;
use crmeb\services\SystemConfigService;
use app\services\pay\PayNotifyServices;
use crmeb\services\easywechat\Application;
use EasyWeChat\Payment\Order;
use think\facade\Env;
use think\facade\Event;
use think\facade\Log;
use crmeb\utils\Hook;
use think\Response;

/**
 * API WeChat Mini Program
 * Class WechatMinService
 * @package service
 */
class MiniProgramService
{
    const MSG_CODE = [
        '1' => 'Chưa tạo phòng livestream',
        '1003' => 'ID sản phẩm không tồn tại',
        '47001' => 'Định dạng tham số đầu vào không đúng quy chuẩn',
        '200002' => 'Tham số đầu vào không hợp lệ',
        '300001' => 'Cấm tạo/cập nhật sản phẩm hoặc cấm sửa & cập nhật phòng',
        '300002' => 'Độ dài tên không hợp lệ',
        '300006' => 'Tải ảnh lên thất bại',
        '300022' => 'Mã phòng này không tồn tại',
        '300023' => 'Bị chặn do trạng thái phòng',
        '300024' => 'Sản phẩm không tồn tại',
        '300025' => 'Sản phẩm không được duyệt',
        '300026' => 'Số lượng sản phẩm trong phòng đã đạt tối đa',
        '300027' => 'Nhập sản phẩm thất bại',
        '300028' => 'Tên phòng vi phạm quy định',
        '300029' => 'Biệt danh streamer vi phạm quy định',
        '300030' => 'ID WeChat của streamer không hợp lệ',
        '300031' => 'Ảnh bìa phòng livestream không hợp lệ',
        '300032' => 'Ảnh chia sẻ phòng livestream vi phạm quy định',
        '300033' => 'Số sản phẩm thêm vào vượt quá giới hạn của phòng livestream',
        '300034' => 'Độ dài biệt danh WeChat của streamer không đạt yêu cầu',
        '300035' => 'ID WeChat của streamer không tồn tại',
        '300036' => 'ID WeChat của streamer chưa xác minh danh tính',
        '300037' => 'Ảnh bìa kênh livestream mua sắm không hợp lệ',
        '300038' => 'Chưa cấu hình CSKH trong trang quản trị Mini Program',
        '9410000' => 'Danh sách phòng livestream trống',
        '9410001' => 'Lấy thông tin phòng thất bại',
        '9410002' => 'Lấy sản phẩm thất bại',
        '9410003' => 'Lấy video phát lại thất bại',
        '300003' => 'Giá nhập vào không hợp lệ',
        '300004' => 'Tên sản phẩm chứa nội dung vi phạm hoặc trái pháp luật',
        '300005' => 'Ảnh sản phẩm chứa nội dung vi phạm hoặc trái pháp luật',
        '300007' => 'Phiên bản Mini Program đang hoạt động không có liên kết này',
        '300008' => 'Thêm sản phẩm thất bại',
        '300009' => 'Rút lại yêu cầu duyệt sản phẩm thất bại',
        '300010' => 'Trạng thái duyệt sản phẩm không đúng',
        '300011' => 'Thao tác không hợp lệ',
        '300012' => 'Không còn hạn mức gửi duyệt',
        '300013' => 'Gửi duyệt thất bại',
        '300014' => 'Đang duyệt, không thể xóa',
        '300017' => 'Sản phẩm chưa được gửi duyệt',
        '300018' => 'Kích thước ảnh không đạt yêu cầu',
        '300021' => 'Thêm sản phẩm thành công, duyệt thất bại',
        '40001' => 'AppSecret sai hoặc AppSecret không thuộc Mini Program này, vui lòng kiểm tra lại tính chính xác của AppSecret',
        '40002' => 'Vui lòng đảm bảo giá trị trường grant_type là client_credential',
        '40013' => 'AppID không hợp lệ, vui lòng kiểm tra lại tính chính xác của AppID, tránh ký tự bất thường, chú ý phân biệt chữ hoa chữ thường',
        '40125' => 'Cấu hình Mini Program không hợp lệ, vui lòng kiểm tra cấu hình',
        '41002' => 'Thiếu tham số appid',
        '41004' => 'Thiếu tham số secret',
        '43104' => 'appid và openid không khớp',
        '48001' => 'Chưa có quyền gọi API WeChat này, vui lòng xin cấp quyền trước',
        '-1' => 'Lỗi hệ thống',
    ];
    /**
     * @var Application
     */
    protected static $instance;

    /**
     * @return array
     */
    public static function options()
    {
        $wechat = SystemConfigService::more(['wechat_app_appsecret', 'wechat_app_appid', 'site_url', 'routine_appId', 'routine_appsecret', 'routine_token', 'routine_encodingaeskey']);
        $payment = SystemConfigService::more(['pay_weixin_mchid', 'pay_weixin_key', 'pay_weixin_client_cert', 'pay_weixin_client_key', 'pay_weixin_open', 'pay_new_weixin_open', 'pay_new_weixin_mchid']);
        $config = [];
        if (request()->isApp()) {
            $appId = isset($wechat['wechat_app_appid']) ? trim($wechat['wechat_app_appid']) : '';
            $appsecret = isset($wechat['wechat_app_appsecret']) ? trim($wechat['wechat_app_appsecret']) : '';
        } else {
            $appId = isset($wechat['routine_appId']) ? trim($wechat['routine_appId']) : '';
            $appsecret = isset($wechat['routine_appsecret']) ? trim($wechat['routine_appsecret']) : '';
        }
        $config = [
            'token' => isset($wechat['routine_token']) ? trim($wechat['routine_token']) : '',
            'aes_key' => isset($wechat['routine_encodingaeskey']) ? trim($wechat['routine_encodingaeskey']) : '',
        ];
        $config['mini_program'] = [
            'app_id' => $appId,
            'secret' => $appsecret,
            'token' => isset($wechat['routine_token']) ? trim($wechat['routine_token']) : '',
            'aes_key' => isset($wechat['routine_encodingaeskey']) ? trim($wechat['routine_encodingaeskey']) : ''
        ];
        $config['payment'] = [
            'app_id' => $appId,
            'merchant_id' => empty($payment['pay_new_weixin_open']) ? trim($payment['pay_weixin_mchid']) : trim($payment['pay_new_weixin_mchid']),
            'key' => trim($payment['pay_weixin_key']),
            'cert_path' => self::getPemPath('pay_weixin_client_cert'),
            'key_path' => self::getPemPath('pay_weixin_client_key'),
            'notify_url' => trim($wechat['site_url']) . '/api/pay/notify/routine'
        ];
//        if (Env::get('cache.driver', 'file') == 'redis') {
//            $cache = new \Doctrine\Common\Cache\RedisCache();
//            $cache->setRedis(\think\facade\Cache::store('redis')->handler());
//            $config['cache'] = $cache;
//        }
        return $config;
    }

    public static function getPemPath(string $name)
    {
        $systemPemServices = app()->make(SystemPemServices::class);
        $path = $systemPemServices->getPemPath($name);
        if ($path) return $path;
        $path = sys_config($name);
        if (strstr($path, 'http://') || strstr($path, 'https://')) {
            $path = parse_url($path)['path'] ?? '';
        }
        $path = root_path('runtime/pem') . ltrim($path, '/');
        if (!file_exists($path)) {
            $path = public_path('uploads') . ltrim($path, '/');
        }
        return $path;
    }

    /**
     * Khởi tạo
     * @param bool $cache
     * @return Application
     */
    public static function application($cache = false)
    {
        (self::$instance === null || $cache === true) && (self::$instance = new Application(self::options()));
        return self::$instance;
    }

    /**
     * API Mini Program
     * @return \EasyWeChat\MiniProgram\MiniProgram
     */
    public static function miniprogram()
    {
        return self::application()->mini_program;
    }

    /**
     * Lấy thông tin người dùng, dựa vào code để lấy session_key
     * @param array|string $openid
     * @return $userInfo
     */
    public static function getUserInfo($code)
    {
        try {
            return self::miniprogram()->sns->getSessionKey($code);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * Giải mã dữ liệu đã mã hóa
     * @param $sessionKey
     * @param $iv
     * @param $encryptData
     * @return $userInfo
     */
    public static function encryptor($sessionKey, $iv, $encryptData)
    {
        return self::miniprogram()->encryptor->decryptData($sessionKey, $iv, $encryptData);
    }

    /**
     * API tải lên tư liệu tạm thời
     * @return \EasyWeChat\Material\Temporary
     */
    public static function materialTemporaryService()
    {
        return self::miniprogram()->material_temporary;
    }

    /**
     * API tin nhắn CSKH
     * @param null $to
     * @param null $message
     */
    public static function staffService()
    {
        return self::miniprogram()->staff;
    }

    /**
     * API tạo mã QR WeChat Mini Program
     * @return \EasyWeChat\QRCode\QRCode
     */
    public static function qrcodeService()
    {
        return self::miniprogram()->qrcode;
    }

    /**API tạo mã QR WeChat Mini Program không giới hạn số lượng, vĩnh viễn
     * @param $scene
     * @param null $page
     * @param null $width
     * @param null $autoColor
     * @param array $lineColor
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function appCodeUnlimitService($scene, $page = null, $width = 430, $autoColor = false, $lineColor = ['r' => 0, 'g' => 0, 'b' => 0])
    {
        return self::qrcodeService()->appCodeUnlimit($scene, $page, $width, $autoColor, $lineColor);
    }


    /**
     * API tin nhắn mẫu
     * @return \EasyWeChat\Notice\Notice
     */
    public static function noticeService()
    {
        return self::miniprogram()->notice;
    }

    /**
     * API tin nhắn mẫu đăng ký
     * @return \crmeb\services\subscribe\ProgramSubscribe
     */
    public static function SubscribenoticeService()
    {
        return self::miniprogram()->now_notice;
    }

    /**
     * Gửi tin nhắn đăng ký
     * @param string $touser openid của người nhận (người dùng)
     * @param string $templateId id mẫu đăng ký cần gửi
     * @param array $data Nội dung mẫu, định dạng như { "key1": { "value": any }, "key2": { "value": any } }
     * @param string $link Trang chuyển đến sau khi nhấn vào thẻ mẫu, chỉ giới hạn trang trong Mini Program này. Hỗ trợ kèm tham số, (ví dụ index?foo=bar). Trường này không điền thì mẫu không chuyển trang.
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public static function sendSubscribeTemlate(string $touser, string $templateId, array $data, string $link = '')
    {
        return self::SubscribenoticeService()->to($touser)->template($templateId)->andData($data)->withUrl($link)->send();
    }

    /**
     * Thêm mẫu tin nhắn đăng ký
     * @param string $tid
     * @param array $kidList
     * @param string $sceneDesc
     * @return mixed
     */
    public static function addSubscribeTemplate(string $tid, array $kidList, string $sceneDesc = '')
    {
        try {
            $res = self::SubscribenoticeService()->addTemplate($tid, $kidList, $sceneDesc);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['priTmplId'])) {
                return $res['priTmplId'];
            } else {
                Log::error('Thêm mẫu tin nhắn đăng ký thất bại:' . $res['errmsg']);
            }
        } catch (\Throwable $e) {
            Log::error('Thêm mẫu tin nhắn đăng ký thất bại:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Xóa mẫu tin nhắn đăng ký
     * @param string $tid
     * @param array $kidList
     * @param string $sceneDesc
     * @return mixed
     */
    public static function delSubscribeTemplate(string $priTmplId)
    {
        try {
            $res = self::SubscribenoticeService()->delTemplate($priTmplId);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return true;
            } else {
                Log::error('Xóa mẫu tin nhắn đăng ký thất bại:' . $res['errmsg']);
            }
        } catch (\Throwable $e) {
            Log::error('Xóa mẫu tin nhắn đăng ký thất bại:' . $e->getMessage());
        }
        return true;
    }


    /**
     * Lấy danh sách từ khóa của tiêu đề mẫu
     * @param string $tid
     * @return mixed
     */
    public static function getSubscribeTemplateKeyWords(string $tid)
    {
        try {
            $res = self::SubscribenoticeService()->getPublicTemplateKeywords($tid);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['data'])) {
                return $res['data'];
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException($e);
        }
    }

    /**
     * Lấy danh sách tin nhắn đăng ký
     * @return mixed
     */
    public static function getSubscribeTemplateList()
    {
        try {
            return self::SubscribenoticeService()->getTemplateList();
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * Thanh toán
     * @return \EasyWeChat\Payment\Payment
     */
    public static function paymentService()
    {
        return self::application()->payment;
    }

    /**
     * Tạo đối tượng đơn hàng thanh toán
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return Order
     */
    protected static function paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $total_fee = bcmul($total_fee, 100, 0);
        $order = array_merge(compact('openid', 'out_trade_no', 'total_fee', 'attach', 'body', 'detail', 'trade_type'), $options);
        if ($order['detail'] == '') unset($order['detail']);
        return new Order($order);
    }

    /**
     * Lấy ID đặt hàng
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return mixed
     */
    public static function paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $key = 'pay_' . $out_trade_no;
        $result = CacheService::get($key);
        if ($result) {
            return $result;
        } else {
            $order = self::paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options);
            $result = self::paymentService()->prepare($order);
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
                CacheService::set($key, $result->prepay_id, 7000);
                return $result->prepay_id;
            } else {
                if ($result->return_code == 'FAIL') {
                    exception('WeChat Pay trả về lỗi:' . $result->return_msg);
                } else if (isset($result->err_code)) {
                    exception('WeChat Pay trả về lỗi:' . $result->err_code_des);
                } else {
                    exception('Không lấy được prepay ID của WeChat Pay, vui lòng thực hiện thanh toán lại!');
                }
                exit;
            }
        }
    }

    /**
     * Lấy ID đặt hàng
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return mixed
     */
    public static function newPaymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $options = [])
    {
        $key = 'pay_' . $out_trade_no;
        $result = CacheService::get($key);
        if ($result) {
            return $result;
        } else {
            $order = self::paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $options);
            $result = self::application()->minipay->createorder($order);
            if ($result->errcode === 0) {
                CacheService::set($key, $result->payment_params, 7000);
                return $result->payment_params;
            } else {
                exception('WeChat Pay trả về lỗi:' . '[' . $result->errcode . ']' . $result->errmsg);
                exit;
            }
        }
    }


    /**
     * Lấy tham số thanh toán jsSdk
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return array|string
     */
    public static function jsPay($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        return self::paymentService()->configForJSSDKPayment(self::paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options));
    }

    /**
     * Lấy tham số thanh toán jsSdk
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return array|string
     */
    public static function newJsPay($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $options = [])
    {
        $config = self::newPaymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $options);
        $config['timestamp'] = $config['timeStamp'];
        unset($config['timeStamp']);
        return $config;

    }

    /**
     * Lấy tham số thanh toán App
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return array|string
     */
    public static function appPay($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = Order::APP, $options = [])
    {
        return self::paymentService()->configForAppPayment(self::paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options));
    }

    /**
     * Hoàn tiền bằng mã đơn hàng của merchant
     * @param $orderNo
     * @param $refundNo
     * @param $totalFee
     * @param null $refundFee
     * @param null $opUserId
     * @param string $refundReason
     * @param string $type
     * @param string $refundAccount
     */
    public static function refund($orderNo, $refundNo, $totalFee, $refundFee = null, $opUserId = null, $refundReason = '', $type = 'out_trade_no', $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS')
    {
        $totalFee = floatval($totalFee);
        $refundFee = floatval($refundFee);
        if ($type == 'out_trade_no') {
            return self::paymentService()->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $type, $refundAccount, $refundReason);
        } else {
            return self::paymentService()->refundByTransactionId($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $refundAccount, $refundReason);
        }
    }

    /**
     * Hoàn tiền bằng mã đơn hàng của merchant
     * @param $orderNo
     * @param $refundNo
     * @param $totalFee
     * @param null $refundFee
     * @param null $opUserId
     * @param string $refundReason
     * @param string $type
     * @param string $refundAccount
     */
    public static function miniRefund($orderNo, $totalFee, $refundFee = null, $opt)
    {
        $totalFee = floatval($totalFee);
        $refundFee = floatval($refundFee);

        $order = [
            'openid' => $opt['open_id'],
            'trade_no' => $opt['order_id'],
            'transaction_id' => $opt['trade_no'],
            'refund_no' => $opt['refund_no'],
            'total_amount' => $totalFee,
            'refund_amount' => $refundFee,
        ];
        return self::application()->minipay->refundorder($order);
    }

    /** Hoàn tiền theo mã đơn hàng
     * @param $orderNo
     * @param array $opt
     * @return bool
     */
    public static function payOrderRefund($orderNo, array $opt)
    {
        if (!isset($opt['pay_price'])) throw new AdminException('Thiếu pay_price');
        if (sys_config('pay_weixin_client_key') == '' || sys_config('pay_weixin_client_cert') == '') throw new AdminException('Vui lòng cấu hình chứng chỉ thanh toán');
        $totalFee = floatval(bcmul($opt['pay_price'], 100, 0));
        $refundFee = isset($opt['refund_price']) ? floatval(bcmul($opt['refund_price'], 100, 0)) : null;
        $refundReason = $opt['desc'] ?? '';
        $refundNo = $opt['refund_id'] ?? $orderNo;
        $opUserId = $opt['op_user_id'] ?? null;
        $type = $opt['type'] ?? 'out_trade_no';
        /*Chỉ dùng cho merchant dòng tiền cũ
        REFUND_SOURCE_UNSETTLED_FUNDS---hoàn tiền từ quỹ chưa thanh toán (mặc định dùng quỹ chưa thanh toán để hoàn tiền)
        REFUND_SOURCE_RECHARGE_FUNDS---hoàn tiền từ số dư khả dụng*/
        $refundAccount = $opt['refund_account'] ?? 'REFUND_SOURCE_UNSETTLED_FUNDS';
        try {
            $res = (self::refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $refundReason, $type, $refundAccount));
            if ($res->return_code == 'FAIL') throw new AdminException('Hoàn tiền thất bại: {:msg}', ['msg' => $res->return_msg]);
            if (isset($res->err_code)) throw new AdminException('Hoàn tiền thất bại: {:msg}', ['msg' => $res->err_code_des]);
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
        return true;
    }

    /**
     * API callback khi thanh toán WeChat Pay thành công
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public static function handleNotify()
    {
        return self::paymentService()->handleNotify(function ($notify, $successful) {
            if ($successful && isset($notify->out_trade_no)) {
                if (isset($notify->attach) && $notify->attach) {
                    if (($count = strpos($notify->out_trade_no, '_')) !== false) {
                        $notify->out_trade_no = substr($notify->out_trade_no, $count + 1);
                    }
                    (new Hook(PayNotifyServices::class, 'wechat'))->listen($notify->attach, $notify->out_trade_no, $notify->transaction_id);
                }
                return false;
            }
        });
    }

    /**
     * Gửi dưới dạng tin nhắn CSKH
     * @param $to
     * @param $message
     * @return bool
     */
    public static function staffTo($to, $message)
    {
        $staff = self::staffService();
        $staff = is_callable($message) ? $staff->message($message()) : $staff->message($message);
        $res = $staff->to($to)->send();
        return $res;
    }


    /**
     * Lấy danh sách livestream
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function getLiveInfo(int $page = 1, int $limit = 10)
    {
        try {
            $res = self::miniprogram()->wechat_live->getLiveInfo($page, $limit);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['room_info']) && $res['room_info']) {
                return $res['room_info'];
            } else {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Lấy phát lại livestream
     * @param int $room_id
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public static function getLivePlayback(int $room_id, int $page = 1, int $limit = 10)
    {
        try {
            $res = self::miniprogram()->wechat_live->getLivePlayback($room_id, $page, $limit);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['live_replay'])) {
                return $res['live_replay'];
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Tạo phòng livestream
     * @param array $data
     * @return mixed
     */
    public static function createLiveRoom(array $data)
    {
        try {
            $res = self::miniprogram()->wechat_live->createRoom($data);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['roomId'])) {
                unset($res['errcode']);
                return $res;
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Thêm sản phẩm vào phòng livestream
     * @param int $roomId
     * @param $ids
     * @return bool
     */
    public static function roomAddGoods(int $roomId, $ids)
    {
        try {
            $res = self::miniprogram()->wechat_live->roomAddGoods($roomId, $ids);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return true;
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Lấy danh sách sản phẩm
     * @param int $status
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public static function getGoodsList(int $status = 2, int $page = 1, int $limit = 10)
    {
        try {
            $res = self::miniprogram()->wechat_live->getGoodsList($status, $page, $limit);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['goods'])) {
                return $res['goods'];
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Lấy chi tiết sản phẩm
     * @param $goods_ids
     * @return mixed
     */
    public static function getGooodsInfo($goods_ids)
    {
        try {
            $res = self::miniprogram()->wechat_live->getGooodsInfo($goods_ids);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['goods'])) {
                return $res['goods'];
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Thêm sản phẩm
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param string $url
     * @param $price
     * @param string $price2
     * @return mixed
     */
    public static function addGoods(string $coverImgUrl, string $name, int $priceType, string $url, $price, $price2 = '')
    {
        try {
            $res = self::miniprogram()->wechat_live->addGoods($coverImgUrl, $name, $priceType, $url, $price, $price2);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['goodsId'])) {
                unset($res['errcode']);
                return $res;
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Rút lại yêu cầu duyệt sản phẩm
     * @param int $goodsId
     * @param $auditId
     * @return bool
     */
    public static function resetauditGoods(int $goodsId, $auditId)
    {
        try {
            $res = self::miniprogram()->wechat_live->resetauditGoods($goodsId, $auditId);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return true;
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Gửi lại sản phẩm để duyệt
     * @param int $goodsId
     * @return mixed
     */
    public static function auditGoods(int $goodsId)
    {
        try {
            $res = self::miniprogram()->wechat_live->auditGoods($goodsId);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['auditId'])) {
                return $res['auditId'];
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Xóa sản phẩm
     * @param int $goodsId
     * @return bool
     */
    public static function deleteGoods(int $goodsId)
    {
        try {
            $res = self::miniprogram()->wechat_live->deleteGoods($goodsId);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return true;
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Cập nhật sản phẩm
     * @param int $goodsId
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param string $url
     * @param $price
     * @param string $price2
     * @return bool
     */
    public static function updateGoods(int $goodsId, string $coverImgUrl, string $name, int $priceType, string $url, $price, $price2 = '')
    {
        try {
            $res = self::miniprogram()->wechat_live->updateGoods($goodsId, $coverImgUrl, $name, $priceType, $url, $price, $price2);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return true;
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    /**
     * Cập nhật sản phẩm
     * @param int $goodsId
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param string $url
     * @param $price
     * @param string $price2
     * @return bool
     */
    public static function getRoleList($role = 2, int $page = 0, int $limit = 30, $keyword = '')
    {
        try {
            $res = self::miniprogram()->wechat_live->getRoleList($role, $page, $limit, $keyword);
            if (isset($res['errcode']) && $res['errcode'] == 0 && isset($res['list'])) {
                return $res['list'];
            } else {
                throw new AdminException($res['errmsg']);
            }
        } catch (\Throwable $e) {
            throw new AdminException(self::getValidMessgae($e));
        }
    }

    public static function getValidMessgae(\Throwable $e)
    {
        $message = '';
        if (!isset(self::MSG_CODE[$e->getCode()]) && strstr($e->getMessage(), 'Request AccessToken fail') !== false) {
            $message = str_replace('Request AccessToken fail. response:', '', $e->getMessage());
            $message = json_decode($message, true) ?: [];
            $errcode = $message['errcode'] ?? false;
            if ($errcode) {
                $message = self::MSG_CODE[$errcode] ?? $message;
            }
        }
        return $message ?: self::MSG_CODE[$e->getCode()] ?? $e->getMessage();
    }


    /**
     * @return Response
     * @throws \EasyWeChat\Server\BadRequestException
     */
    public static function serve(): Response
    {
        $wechat = self::application(true);
        $server = $wechat->server;
        self::hook($server);
        $response = $server->serve();
        return response($response->getContent());
    }

    private static function hook($server)
    {
        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    switch (strtolower($message->Event)) {
                        case 'funds_order_pay':  // Quản lý thanh toán Mini Program
                            if (($count = strpos($message['order_info']['trade_no'], '_')) !== false) {
                                $trade_no = substr($message['order_info']['trade_no'], $count + 1);
                            } else {
                                $trade_no = $message['order_info']['trade_no'];
                            }
                            $prefix = substr($trade_no, 0, 2);
                            //Xử lý tham số một chút
                            switch ($prefix) {
                                case 'cp':
                                    $data['attach'] = 'Product';
                                    break;
                                case 'hy':
                                    $data['attach'] = 'Member';
                                    break;
                                case 'cz':
                                    $data['attach'] = 'UserRecharge';
                                    break;
                            }
                            $data['out_trade_no'] = $message['order_info']['trade_no'];
                            $data['transaction_id'] = $message['order_info']['transaction_id'];
                            $data['opneid'] = $message['FromUserName'];
                            if (Event::until('NotifyListener', [$data, PayServices::WEIXIN_PAY])) {
                                $response = 'success';
                            } else {
                                $response = 'faild';
                            }
                            Log::error(['data' => $data, 'res' => $response, 'message' => $message]);
                            break;
                        case 'trade_manage_remind_access_api':  // Khi Mini Program hoàn tất ủy quyền kỳ hạn tài khoản  khi Mini Program phát sinh giao dịch đầu tiên  Mini Program đã có giao dịch nhưng chưa từng giao hàng, mỗi ngày một lần
                            break;
                        case 'trade_manage_remind_shipping':   // Mini Program đã từng giao hàng, khi đơn hàng quá 48 giờ chưa giao
                            break;
                        case 'trade_manage_order_settlement':     // Khi đơn hàng hoàn tất giao hàng  khi đơn hàng thanh toán
                            if (isset($message['confirm_receive_method'])) {  // Khi đơn hàng thanh toán
                                /** @var StoreOrderTakeServices $StoreOrderTakeServices */
                                $storeOrderTakeServices = app()->make(StoreOrderTakeServices::class);
                                $storeOrderTakeServices->miniOrderTakeOrder($message['merchant_trade_no']);
                            }
                            break;
                    };
                    break;
            };
        });
    }

    public static function getUrlScheme($jumpWxa = [], $expireType = -1, $expireNum = 0)
    {
        try {
            $res = self::miniprogram()->mini_scheme->getUrlScheme($jumpWxa, $expireType, $expireNum);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return $res['openlink'];
            } else {
                return '';
            }
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public static function getUrlLink($jumpWxa = [])
    {
        try {
            $res = self::miniprogram()->mini_scheme->getUrlLink($jumpWxa);
            if (isset($res['errcode']) && $res['errcode'] == 0) {
                return $res['url_link'];
            } else {
                return '';
            }
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}
