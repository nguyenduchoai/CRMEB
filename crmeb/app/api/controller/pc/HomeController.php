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
namespace app\api\controller\pc;


use app\Request;
use app\services\pc\HomeServices;
use app\services\other\QrcodeServices;

/**
 * Class HomeController
 * @package app\api\controller\pc
 */
class HomeController
{
    /**
     *
     * @var HomeServices
     */
    protected $services;

    /**
     * HomeController constructor.
     * @param HomeServices $services
     */
    public function __construct(HomeServices $services)
    {
        $this->services = $services;
    }

    /**
     * Ảnh trình chiếu (banner) trang chủ bản PC
     * @return mixed
     */
    public function getBanner()
    {
        $list = sys_data('pc_home_banner');
        return app('json')->success(compact('list'));
    }

    /**
     * Sản phẩm theo danh mục trên trang chủ
     * @return mixed
     */
    public function getCategoryProduct(Request $request)
    {
        $data = $this->services->getCategoryProduct((int)$request->uid());
        return app('json')->success($data);
    }

    /**
     * Lấy cấu hình url chuyển hướng khi mua trên điện thoại
     * @return string
     */
    public function getProductPhoneBuy()
    {
        $phoneBuy = sys_config('product_phone_buy_url', 1);
        $siteUrl = sys_config('site_url');
        return app('json')->success(['phone_buy' => $phoneBuy, 'sit_url' => $siteUrl]);
    }

    /**
     * Mã QR mua thành viên trả phí
     * @return mixed
     */
    public function getPayVipCode()
    {
        $type = sys_config('product_phone_buy_url', 1);
        $url = '/pages/annex/vip_paid/index';
        $name = "wechat_pay_vip_code.png";
        /** @var QrcodeServices $QrcodeService */
        $QrcodeService = app()->make(QrcodeServices::class);
        if ($type == 1) {
            $codeUrl = $QrcodeService->getWechatQrcodePath($name, $url, false, false);
        } else {
            //Tạo địa chỉ Mini Program
            $codeUrl = $QrcodeService->getRoutineQrcodePath(0, 0, 5, [], false);
        }
        return app('json')->success(['url' => $codeUrl ?: '']);
    }
}
