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
namespace app\adminapi\controller\v1\notification\sms;

use app\services\system\config\SystemConfigServices;
use app\services\yihaotong\SmsAdminServices;
use app\services\serve\ServeServices;
use app\adminapi\controller\AuthController;
use crmeb\services\CacheService;
use think\facade\App;

/**
 * Cấu hình SMS
 * Class SmsConfig
 * @package app\admin\controller\sms
 */
class SmsConfig extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SmsConfig constructor.
     * @param App $app
     * @param SmsAdminServices $services
     */
    public function __construct(App $app, SmsAdminServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lưu cấu hình SMS
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function save_basics()
    {
        [$account, $token] = $this->request->postMore([
            ['sms_account', ''],
            ['sms_token', '']
        ], true);

        $this->validate(['sms_account' => $account, 'sms_token' => $token], \app\adminapi\validate\notification\SmsConfigValidate::class);

        if ($this->services->login($account, $token)) {
            return app('json')->success('Đăng nhập thành công');
        } else {
            return app('json')->fail('Tài khoản hoặc mật khẩu không đúng');
        }
    }

    /**
     * Kiểm tra đăng nhập
     * @param ServeServices $services
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function is_login(ServeServices $services)
    {
        $configServices = app()->make(SystemConfigServices::class);
        $sms_info = CacheService::get('sms_account');
        $data = ['status' => false, 'info' => ''];
        if ($sms_info) {
            try {
                $result = $services->user()->getUser();
            } catch (\Throwable $e) {
                $result = [];
            }
            if (!$result) {
                $this->logout();
            } else {
                $data['status'] = true;
                $data['info'] = $sms_info;
            }
            return app('json')->success($data);
        } else {
            CacheService::clear();
            $account = sys_config('sms_account');
            $password = sys_config('sms_token');
            //Không đăng xuất, xóa 2 dữ liệu này rồi tự động đăng nhập
            if ($account && $password) {
                $res = $services->user()->login($account, $password);
                if ($res) {
                    CacheService::set('sms_account', $account);
                    $data['status'] = true;
                    $data['info'] = $account;
                }
            }
        }
        return app('json')->success($data);
    }

    /**
     * Thoát
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function logout()
    {
        CacheService::delete('sms_account');
        $this->services->updateSmsConfig('', '');
        CacheService::clear();
        return app('json')->success('Đăng xuất thành công');
    }

    /**
     * Lịch sử gửi SMS
     * @param ServeServices $services
     * @return mixed
     */
    public function record(ServeServices $services)
    {
        [$page, $limit, $status] = $this->request->getMore([
            [['page', 'd'], 0],
            [['limit', 'd'], 10],
            ['type', '', '', 'status'],
        ], true);
        return app('json')->success($services->user()->record($page, $limit, 1, $status));
    }

    /**
     * Lấy thông tin tài khoản SMS đang đăng nhập
     * @return mixed
     */
    public function data()
    {
        return app('json')->success($this->services->getSmsData());
    }
}
