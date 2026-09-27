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
namespace app\api\controller\v1\user;

use app\Request;
use app\services\user\UserExtractServices;
use think\facade\Config;

/**
 * Lớp rút tiền
 * Class UserExtractController
 * @package app\api\controller\user
 */
class UserExtractController
{
    protected $services = NUll;

    /**
     * UserExtractController constructor.
     * @param UserExtractServices $services
     */
    public function __construct(UserExtractServices $services)
    {
        $this->services = $services;
    }

    /**
     * Ngân hàng rút tiền
     * @param Request $request
     * @return mixed
     */
    public function bank(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->bank($uid));
    }

    /**
     * Yêu cầu rút tiền
     * @param Request $request
     * @return mixed
     */
    public function cash(Request $request)
    {
        $extractInfo = $request->postMore([
            ['alipay_code', ''],
            ['extract_type', ''],
            ['money', 0],
            ['user_name', ''],
            ['name', ''],
            ['bankname', ''],
            ['cardnum', ''],
            ['weixin', ''],
            ['qrcode_url', ''],
        ]);
        $extractInfo['channel_type'] = $request->getFromType();
        $extractType = Config::get('pay.extractType', []);
        if (!in_array($extractInfo['extract_type'], $extractType))
            return app('json')->fail('Phương thức rút tiền không tồn tại');
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', (float)$extractInfo['money'])) return app('json')->fail('Số tiền rút đã nhập không hợp lệ');
        if (!$extractInfo['cardnum'] == '')
            if (!preg_match('/^([1-9]{1})(\d{15}|\d{16}|\d{18})$/', $extractInfo['cardnum']))
                return app('json')->fail('Số thẻ ngân hàng đã nhập không hợp lệ');
        if ($extractInfo['extract_type'] == 'weixin') {
            if (trim($extractInfo['user_name']) == '') return app('json')->fail('Vui lòng nhập họ tên');
        } elseif ($extractInfo['extract_type'] == 'alipay') {
            if (trim($extractInfo['alipay_code']) == '') return app('json')->fail('Vui lòng nhập tài khoản Alipay');
            if (trim($extractInfo['user_name']) == '') return app('json')->fail('Vui lòng nhập họ tên');
        } elseif ($extractInfo['extract_type'] == 'bank') {
            if (!$extractInfo['cardnum']) return app('json')->fail('Vui lòng nhập số tài khoản ngân hàng');
            if (!$extractInfo['bankname']) return app('json')->fail('Vui lòng nhập thông tin ngân hàng mở tài khoản');
        }
        $uid = (int)$request->uid();
        if ($this->services->cash($uid, $extractInfo))
            return app('json')->success('Gửi yêu cầu rút tiền thành công');
        else
            return app('json')->fail('Gửi yêu cầu rút tiền thất bại');
    }
}
