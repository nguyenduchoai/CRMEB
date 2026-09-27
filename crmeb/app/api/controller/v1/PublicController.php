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
namespace app\api\controller\v1;


use app\services\activity\combination\StorePinkServices;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\activity\lottery\LuckLotteryRecordServices;
use app\services\article\ArticleServices;
use app\services\diy\DiyServices;
use app\services\diy\ThemeServices;
use app\services\kefu\service\StoreServiceServices;
use app\services\message\MessageSystemServices;
use app\services\order\DeliveryServiceServices;
use app\services\order\StoreCartServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\other\AgreementServices;
use app\services\other\CacheServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductRelationServices;
use app\services\product\product\StoreProductServices;
use app\services\shipping\ExpressServices;
use app\services\shipping\SystemCityServices;
use app\services\system\AppVersionServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\system\config\SystemConfigServices;
use app\services\system\config\SystemStorageServices;
use app\services\system\lang\LangCodeServices;
use app\services\system\lang\LangCountryServices;
use app\services\system\lang\LangTypeServices;
use app\services\system\store\SystemStoreServices;
use app\services\system\store\SystemStoreStaffServices;
use app\services\user\UserBillServices;
use app\services\user\UserExtractServices;
use app\services\user\UserInvoiceServices;
use app\services\user\UserServices;
use app\services\wechat\RoutineSchemeServices;
use app\services\wechat\WechatUserServices;
use app\Request;
use crmeb\services\CacheService;
use app\services\other\UploadService;
use crmeb\services\pay\Pay;
use crmeb\services\workerman\ChannelService;

/**
 * Lớp dùng chung
 * Class PublicController
 * @package app\api\controller
 */
class PublicController
{
    /**
     * Lấy dữ liệu trang chủ
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $banner = sys_data('routine_home_banner') ?: []; //TODO Ảnh banner trang chủ
        $menus = sys_data('routine_home_menus') ?: []; //TODO Nút trang chủ
        $roll = sys_data('routine_home_roll_news') ?: []; //TODO Tin tức chạy trang chủ
        $activity = sys_data('routine_home_activity', 3) ?: []; //TODO Ảnh khu vực hoạt động trang chủ
        $explosive_money = sys_data('index_categy_images') ?: []; //TODO Sản phẩm hot giá tốt trang chủ
        $site_name = sys_config('site_name');
        $routine_index_page = sys_data('routine_index_page');
        $info['fastInfo'] = $routine_index_page[0]['fast_info'] ?? ''; //TODO Giới thiệu chọn nhanh
        $info['bastInfo'] = $routine_index_page[0]['bast_info'] ?? ''; //TODO Giới thiệu đề xuất sản phẩm chất lượng
        $info['firstInfo'] = $routine_index_page[0]['first_info'] ?? ''; //TODO Giới thiệu sản phẩm mới ra mắt
        $info['salesInfo'] = $routine_index_page[0]['sales_info'] ?? ''; //TODO Giới thiệu sản phẩm khuyến mãi
        $logoUrl = sys_config('routine_index_logo'); //TODO Giới thiệu sản phẩm khuyến mãi
        if (strstr($logoUrl, 'http') === false && $logoUrl) {
            $logoUrl = sys_config('site_url') . $logoUrl;
        }
        $logoUrl = str_replace('\\', '/', $logoUrl);
        $fastNumber = (int)sys_config('fast_number', 0);//TODO Số lượng danh mục chọn nhanh

        /** @var StoreCategoryServices $categoryService */
        $categoryService = app()->make(StoreCategoryServices::class);
        $info['fastList'] = $fastNumber ? $categoryService->byIndexList($fastNumber, 'id,cate_name,pid,pic') : [];//TODO Số lượng danh mục chọn nhanh
        /** @var StoreProductServices $storeProductServices */
        $storeProductServices = app()->make(StoreProductServices::class);
        //Lấy sản phẩm đề xuất
        [$baseList, $firstList, $benefit, $likeInfo, $vipList] = $storeProductServices->getRecommendProductArr((int)$request->uid(), ['is_best', 'is_new', 'is_benefit', 'is_hot']);
        $info['bastList'] = $baseList; //TODO Số lượng đề xuất sản phẩm chất lượng
        $info['firstList'] = $firstList; //TODO Số lượng sản phẩm mới ra mắt
        $info['bastBanner'] = sys_data('routine_home_bast_banner') ?? []; //TODO Ảnh đề xuất sản phẩm chất lượng trang chủ
        $lovely = sys_data('routine_home_new_banner') ?: []; //TODO Ảnh đầu trang sản phẩm mới ra mắt
        if ($request->uid()) {
            /** @var WechatUserServices $wechatUserService */
            $wechatUserService = app()->make(WechatUserServices::class);
            $subscribe = (bool)$wechatUserService->value(['uid' => $request->uid()], 'subscribe');
        } else {
            $subscribe = true;
        }
        $newGoodsBananr = sys_config('new_goods_bananr');
        $tengxun_map_key = sys_config('tengxun_map_key');
        return app('json')->success(compact('banner', 'menus', 'roll', 'info', 'activity', 'lovely', 'benefit', 'likeInfo', 'logoUrl', 'site_name', 'subscribe', 'newGoodsBananr', 'tengxun_map_key', 'explosive_money'));
    }

    /**
     * Lấy cấu hình chia sẻ
     * @return mixed
     */
    public function share()
    {
        $data['img'] = sys_config('wechat_share_img');
        if (strstr($data['img'], 'http') === false && $data['img'] != '') {
            $data['img'] = sys_config('site_url') . $data['img'];
        }
        $data['img'] = str_replace('\\', '/', $data['img']);
        $data['title'] = sys_config('wechat_share_title');
        $data['synopsis'] = sys_config('wechat_share_synopsis');
        return app('json')->success($data);
    }

    /**
     * Lấy cấu hình website
     * @return mixed
     */
    public function getSiteConfig()
    {
        $data['record_No'] = sys_config('record_No');
        $data['icp_url'] = sys_config('icp_url');
        $data['network_security'] = sys_config('network_security');
        $data['network_security_url'] = sys_config('network_security_url');
        return app('json')->success($data);
    }

    /**
     * Lấy menu trang cá nhân
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function menu_user(Request $request)
    {
        $menusInfo = sys_data('routine_my_menus') ?? [];
        $uid = 0;
        $userInfo = [];
        if ($request->hasMacro('user')) $userInfo = $request->user();
        if ($request->hasMacro('uid')) $uid = $request->uid();

        //Bật/tắt hạng người dùng
        $vipOpen = sys_config('member_func_status');
        //Bật/tắt tính năng CTV
        $brokerageFuncStatus = sys_config('brokerage_func_status');
        //Bật/tắt tính năng số dư
        $balanceFuncStatus = sys_config('balance_func_status');
        //Bật/tắt thành viên trả phí
        $svipOpen = sys_config('member_card_status');
        $userService = $userOrder = $userVerifyStatus = $deliveryUser = $invoiceStatus = $isUserPromoter = false;
        if ($uid && $userInfo) {
            /** @var StoreServiceServices $storeService */
            $storeService = app()->make(StoreServiceServices::class);
            //Có phải là nhân viên chăm sóc khách hàng không
            $userService = $storeService->checkoutIsService(['uid' => $uid, 'status' => 1]);
            //Có phải là quản lý đơn hàng không
            $userOrder = $storeService->checkoutIsService(['uid' => $uid, 'status' => 1, 'customer' => 1]);
            //Có phải là nhân viên xác nhận sử dụng không
            $userVerifyStatus = app()->make(SystemStoreStaffServices::class)->verifyStatus($uid);
            //Có phải là người giao hàng không
            $deliveryUser = app()->make(DeliveryServiceServices::class)->checkoutIsService($uid);
            //Bật/tắt tính năng hóa đơn
            $invoiceStatus = app()->make(UserInvoiceServices::class)->invoiceFuncStatus(false);
            //Có phải là cộng tác viên không
            $isUserPromoter = app()->make(UserServices::class)->checkUserPromoter($uid, $userInfo);
        }
        $auth = [];
        $auth['/pages/users/user_vip/index'] = $vipOpen;
        $auth['/pages/users/user_spread_user/index'] = $brokerageFuncStatus && $isUserPromoter;
        $auth['/pages/annex/settled/index'] = $brokerageFuncStatus && sys_config('store_brokerage_statu') == 1 && !$isUserPromoter;
        $auth['/pages/users/user_money/index'] = $balanceFuncStatus;
        $auth['/pages/admin/order/index'] = $auth['/pages/admin/manage/index'] = $userOrder;
        $auth['/pages/admin/order_cancellation/index'] = $userVerifyStatus || $deliveryUser;
        $auth['/pages/users/user_invoice_list/index'] = $invoiceStatus;
        $auth['/pages/annex/vip_paid/index'] = $svipOpen;
        $auth['/kefu/mobile_list'] = $userService;
        foreach ($menusInfo as $key => &$value) {
            if (isset($value['is_show']) && $value['is_show'] == 0) {
                unset($menusInfo[$key]);
                continue;
            }
            if ($value['url'] == '/pages/users/user_spread_user/index' && $auth['/pages/annex/settled/index']) {
                $value['name'] = 'Đăng ký CTV';
                $value['url'] = '/pages/annex/settled/index';
            }
            if (isset($auth[$value['url']]) && !$auth[$value['url']]) {
                unset($menusInfo[$key]);
                continue;
            }
            if ($value['url'] == '/kefu/mobile_list') {
                $value['url'] = sys_config('site_url') . $value['url'];
                if ($request->isRoutine()) {
                    $value['url'] = str_replace('http://', 'https://', $value['url']);
                }
            }
        }
        /** @var SystemConfigServices $systemConfigServices */
        $systemConfigServices = app()->make(SystemConfigServices::class);
        $bannerInfo = $systemConfigServices->getSpreadBanner() ?? [];
        $my_banner = sys_data('routine_my_banner');
        $routine_contact_type = sys_config('routine_contact_type', 0);
        /** @var DiyServices $diyServices */
        $diyServices = app()->make(DiyServices::class);
        $diy_data = $diyServices->get(['template_name' => 'member', 'type' => 1], ['value', 'order_status', 'my_banner_status', 'my_menus_status', 'business_status']);
        $diy_data = $diy_data ? $diy_data->toArray() : [];
        return app('json')->success(['routine_my_menus' => array_merge($menusInfo), 'routine_my_banner' => $my_banner, 'routine_spread_banner' => $bannerInfo, 'routine_contact_type' => $routine_contact_type, 'diy_data' => $diy_data]);
    }

    /**
     * Lấy từ khóa tìm kiếm phổ biến
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search()
    {
        $routineHotSearch = sys_data('routine_hot_search') ?? [];
        $searchKeyword = [];
        if (count($routineHotSearch)) {
            foreach ($routineHotSearch as $key => &$item) {
                array_push($searchKeyword, $item['title']);
            }
        }
        return app('json')->success($searchKeyword);
    }


    /**
     * Tải lên ảnh
     * @param Request $request
     * @param SystemAttachmentServices $services
     * @return mixed
     */
    public function upload_image(Request $request, SystemAttachmentServices $services)
    {
        $data = $request->postMore([
            ['filename', 'file'],
        ]);
        if (!$data['filename']) return app('json')->fail('Tham số không hợp lệ');
        if (CacheService::has('start_uploads_' . $request->uid()) && CacheService::get('start_uploads_' . $request->uid()) >= 100) return app('json')->fail('Thao tác không hợp lệ');
        $upload = UploadService::init();
        $info = $upload->to('store/comment')->validate()->move($data['filename']);
        if ($info === false) {
            return app('json')->fail($upload->getError());
        }
        $res = $upload->getUploadInfo();
        $services->attachmentAdd($res['name'], $res['size'], $res['type'], $res['dir'], $res['thumb_path'], 1, (int)sys_config('upload_type', 1), $res['time'], 3);
        if (CacheService::has('start_uploads_' . $request->uid()))
            $start_uploads = (int)CacheService::get('start_uploads_' . $request->uid());
        else
            $start_uploads = 0;
        $start_uploads++;
        CacheService::set('start_uploads_' . $request->uid(), $start_uploads, 86400);
        $res['dir'] = path_to_url($res['dir']);
        if (strpos($res['dir'], 'http') === false) $res['dir'] = $request->domain() . $res['dir'];
        return app('json')->success('Tải ảnh lên thành công', ['name' => $res['name'], 'url' => $res['dir']]);
    }

    /**
     * Đơn vị vận chuyển
     * @return mixed
     */
    public function logistics(ExpressServices $services)
    {
        $expressList = $services->expressList();
        return app('json')->success($expressList ?? []);
    }

    /**
     * Thông báo bất đồng bộ mua SMS
     *
     * @param Request $request
     * @return mixed
     */
    public function sms_pay_notify(Request $request)
    {
        [$order_id, $price, $status, $num, $pay_time, $attach] = $request->postMore([
            ['order_id', ''],
            ['price', 0.00],
            ['status', 400],
            ['num', 0],
            ['pay_time', time()],
            ['attach', 0],
        ], true);
        if ($status == 200) {
            try {
                ChannelService::instance()->send('PAY_SMS_SUCCESS', ['price' => $price, 'number' => $num], [$attach]);
            } catch (\Throwable $e) {
            }
            return app('json')->success('Thao tác thành công');
        }
        return app('json')->fail('Thao tác thất bại');
    }

    /**
     * Ghi nhận chia sẻ của người dùng
     * @param Request $request
     * @param UserBillServices $services
     * @return mixed
     */
    public function user_share(Request $request, UserBillServices $services)
    {
        $uid = (int)$request->uid();
        $services->setUserShare($uid);
        return app('json')->success('Cập nhật thành công');
    }

    /**
     * Lấy base64 của ảnh
     * @param Request $request
     * @return mixed
     */
    public function get_image_base64(Request $request)
    {
        [$imageUrl, $codeUrl] = $request->postMore([
            ['image', ''],
            ['code', ''],
        ], true);
        /** @var SystemStorageServices $systemStorageServices */
        $systemStorageServices = app()->make(SystemStorageServices::class);
        $domainArr = $systemStorageServices->getColumn([], 'domain');
        $domainArr = array_merge($domainArr, [$request->host()]);
        $domainArr = array_unique(array_diff($domainArr, ['']));
        if (count($domainArr)) {
            $domainArr = array_map(function ($item) {
                return str_replace(['https://', 'http://'], '', $item);
            }, $domainArr);
        }
        $domainArr[] = 'mp.weixin.qq.com';
        $imageUrlHost = $imageUrl ? (parse_url($imageUrl)['host'] ?? $imageUrl) : $imageUrl;
        $codeUrlHost = $codeUrl ? (parse_url($codeUrl)['host'] ?? $codeUrl) : $codeUrl;
        if ($domainArr && (($imageUrl && !in_array($imageUrlHost, $domainArr)) || ($codeUrl && !in_array($codeUrlHost, $domainArr)))) {
            return app('json')->success(['code' => false, 'image' => false]);
        }
        if ($imageUrl !== '' && !preg_match('/.*(\.png|\.jpg|\.jpeg|\.gif)$/', $imageUrl) && strpos(strtolower($imageUrl), "phar://") !== false) {
            return app('json')->success(['code' => false, 'image' => false]);
        }
        if ($codeUrl !== '' && !(preg_match('/.*(\.png|\.jpg|\.jpeg|\.gif)$/', $codeUrl) || strpos($codeUrl, 'https://mp.weixin.qq.com/cgi-bin/showqrcode') !== false) && strpos(strtolower($codeUrl), "phar://") !== false) {
            return app('json')->success(['code' => false, 'image' => false]);
        }
        try {
            $code = CacheService::remember($codeUrl, function () use ($codeUrl) {
                $codeTmp = $code = $codeUrl ? image_to_base64($codeUrl) : false;
                if (!$codeTmp) {
                    $putCodeUrl = put_image($codeUrl);
                    //TODO
                    $code = $putCodeUrl ? image_to_base64(app()->request->domain(true) . '/' . $putCodeUrl) : false;
                    if ($putCodeUrl) {
                        unlink($_SERVER["DOCUMENT_ROOT"] . DS . $putCodeUrl);
                    }
                }
                return $code;
            });
            $image = CacheService::remember($imageUrl, function () use ($imageUrl) {
                $imageTmp = $image = $imageUrl ? image_to_base64($imageUrl) : false;
                if (!$imageTmp) {
                    $putImageUrl = put_image($imageUrl);
                    //TODO
                    $image = $putImageUrl ? image_to_base64(app()->request->domain(true) . '/' . $putImageUrl) : false;
                    if ($putImageUrl) {
                        unlink($_SERVER["DOCUMENT_ROOT"] . DS . $putImageUrl);
                    }
                }
                return $image;
            });
            return app('json')->success(compact('code', 'image'));
        } catch (\Exception $e) {
            return app('json')->fail('Thao tác thất bại');
        }
    }

    /**
     * Danh sách cửa hàng
     * @return mixed
     */
    public function store_list(Request $request, SystemStoreServices $services)
    {
        list($latitude, $longitude) = $request->getMore([
            ['latitude', ''],
            ['longitude', ''],
        ], true);
        $data['list'] = $services->getStoreList(['type' => 0], ['id', 'name', 'phone', 'address', 'detailed_address', 'image', 'latitude', 'longitude'], $latitude, $longitude);
        $data['tengxun_map_key'] = sys_config('tengxun_map_key');
        return app('json')->success($data);
    }

    /**
     * Tìm dữ liệu thành phố
     * @param Request $request
     * @return mixed
     */
    public function city_list(Request $request)
    {
        /** @var SystemCityServices $systemCity */
        $systemCity = app()->make(SystemCityServices::class);
        return app('json')->success($systemCity->cityList());
    }

    /**
     * Lấy dữ liệu mua chung
     * @return mixed
     */
    public function pink(StorePinkServices $pink, UserServices $user)
    {
        $data['pink_count'] = $pink->getCount(['is_refund' => 0]);
        $uids = array_flip($pink->getColumn(['is_refund' => 0], 'uid'));
        if (count($uids)) {
            $uids = array_rand($uids, count($uids) < 3 ? count($uids) : 3);
        }
        $data['avatars'] = $uids ? $user->getColumn(is_array($uids) ? [['uid', 'in', $uids]] : ['uid' => $uids], 'avatar') : [];
        foreach ($data['avatars'] as &$avatar) {
            if (strpos($avatar, '/statics/system_images/') !== false) {
                $avatar = set_file_url($avatar);
            }
        }
        return app('json')->success($data);
    }

    /**
     * API sao chép mã chia sẻ
     * @return mixed
     */
    public function copy_words()
    {
        $data['words'] = sys_config('copy_words');
        return app('json')->success($data);
    }

    /**Tạo từ khóa mật lệnh
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function copy_share_words(Request $request)
    {
        list($productId) = $request->getMore([
            ['product_id', ''],
        ], true);
        /** @var StoreProductServices $productService */
        $productService = app()->make(StoreProductServices::class);
        $keyWords['key_words'] = $productService->getProductWords($productId);
        return app('json')->success($keyWords);
    }

    /**
     * Lấy dữ liệu trang
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDiy(DiyServices $services, $id = 0)
    {
        return app('json')->success($services->getDiyInfo((int)$id));
    }

    /**
     * Lấy thanh điều hướng dưới cùng
     * @param DiyServices $services
     * @param string $template_name
     * @return mixed
     */
    public function getNavigation(DiyServices $services, string $template_name = '')
    {
        return app('json')->success($services->getNavigation($template_name));
    }

    /**
     * Dữ liệu sản phẩm trang chủ
     * @param Request $request
     */
    public function home_products_list(Request $request, DiyServices $services)
    {
        $data = $request->getMore([
            ['priceOrder', ''],
            ['newsOrder', ''],
            ['salesOrder', ''],
            [['type', 'd'], 0],
            ['ids', ''],
            [['selectId', 'd'], ''],
            ['selectType', 0],
            ['isType', 0],
        ]);
        $where = [];
        $where['is_show'] = 1;
        $where['is_del'] = 0;
        $where['productId'] = '';
        if ($data['selectType'] == 1) {
            if (!$data['ids']) {
                return app('json')->success('Chưa có dữ liệu');
            }
            $where['ids'] = $data['ids'] ? explode(',', $data['ids']) : [];
            if ($data['type'] != 2 && $data['type'] != 3 && $data['type'] != 8) {
                $where['type'] = 0;
            } else {
                $where['type'] = $data['type'];
            }
        } else {
            $where['priceOrder'] = $data['priceOrder'];
            $where['newsOrder'] = $data['newsOrder'];
            $where['salesOrder'] = $data['salesOrder'];
            $where['type'] = $data['type'];
            if ($data['selectId']) {
                /** @var StoreCategoryServices $storeCategoryServices */
                $storeCategoryServices = app()->make(StoreCategoryServices::class);
                if ($storeCategoryServices->value(['id' => $data['selectId']], 'pid')) {
                    $where['sid'] = $data['selectId'];
                } else {
                    $where['cid'] = $data['selectId'];
                }
            }
        }
        return app('json')->success($services->homeProductList($where, $request->uid()));
    }

    public function getNewAppVersion($platform)
    {
        /** @var AppVersionServices $appService */
        $appService = app()->make(AppVersionServices::class);
        return app('json')->success($appService->getNewInfo($platform));
    }

    public function getCustomerType()
    {
        $data = [];
        $data['customer_type'] = sys_config('customer_type', 0);
        $data['customer_phone'] = sys_config('customer_phone', 0);
        $data['customer_url'] = sys_config('customer_url', 0);
        $data['customer_corpId'] = sys_config('customer_corpId', 0);
        return app('json')->success($data);
    }


    /**
     * Mã thống kê
     * @return array|string
     */
    public function getScript()
    {
        return sys_config('statistic_script', '');
    }

    public function customPcJs()
    {
        return sys_config('custom_pc_js', '');
    }

    /**
     * Lấy tên miền request của workerman
     * @return mixed
     */
    public function getWorkerManUrl()
    {
        return app('json')->success(getWorkerManUrl());
    }

    /**
     * Quảng cáo màn hình khởi động trang chủ
     * @return mixed
     */
    public function getOpenAdv()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $data = $cache->getDbCache('open_adv', '');
        return app('json')->success($data);
    }

    /**
     * Lấy nội dung thỏa thuận người dùng
     * @return mixed
     */
    public function getUserAgreement()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('user_agreement', '');
        return app('json')->success(compact('content'));
    }

    /**
     * Lấy thỏa thuận
     * @param AgreementServices $agreementServices
     * @param $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAgreement(AgreementServices $agreementServices, $type)
    {
        $data = $agreementServices->getAgreementBytype($type);
        return app('json')->success($data);
    }

    /**
     * Truy vấn thông tin bản quyền
     * @return mixed
     */
    public function copyright()
    {
        $copyrightContext = sys_config('nncnL_crmeb_copyright', '');
        $copyrightImage = sys_config('nncnL_crmeb_copyright_image', '');
        $siteName = sys_config('site_name', '');
        $siteLogo = sys_config('wap_login_logo', '');
        return app('json')->success(compact('copyrightContext', 'copyrightImage', 'siteName', 'siteLogo'));
    }

    /**
     * Lấy danh sách loại ngôn ngữ
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLangTypeList()
    {
        /** @var LangTypeServices $langTypeServices */
        $langTypeServices = app()->make(LangTypeServices::class);
        $list = $langTypeServices->langTypeList(['status' => 1, 'is_del' => 0])['list'];
        $data = [];
        foreach ($list as $item) {
            $data[] = ['name' => $item['language_name'], 'value' => $item['file_name']];
        }
        return app('json')->success($data);
    }

    /**
     * Lấy json ngôn ngữ hiện tại
     * @return mixed
     * @throws \Throwable
     */
    public function getLangJson()
    {
        /** @var LangTypeServices $langTypeServices */
        $langTypeServices = app()->make(LangTypeServices::class);
        /** @var LangCountryServices $langCountryServices */
        $langCountryServices = app()->make(LangCountryServices::class);

        $request = app()->request;
        //Lấy loại ngôn ngữ truyền vào API
        if (!$range = $request->header('cb-lang')) {
            //Nếu không truyền vào thì hiển thị theo ngôn ngữ mặc định của hệ thống
            if (!$range = $langTypeServices->value(['is_default' => 1], 'file_name')) {
                //Nếu hệ thống chưa cài đặt ngôn ngữ mặc định thì hiển thị theo ngôn ngữ của trình duyệt, nếu không tìm thấy ngôn ngữ của trình duyệt trong thư viện thì dùng tiếng Trung giản thể
                if ($request->header('accept-language') !== null) {
                    $range = explode(',', $request->header('accept-language'))[0];
                } else {
                    $range = 'zh-CN';
                }
            }
        }
        // Lấy type_id
        $typeId = $langCountryServices->value(['code' => $range], 'type_id') ?: 1;

        // Lấy key cache
        $langData = $langTypeServices->getColumn(['status' => 1, 'is_del' => 0], 'file_name', 'id');
        $langStr = 'api_lang_' . str_replace('-', '_', $langData[$typeId]);

        //Đọc gói ngôn ngữ của ngôn ngữ hiện tại
        $lang = CacheService::remember($langStr, function () use ($typeId, $range) {
            /** @var LangCodeServices $langCodeServices */
            $langCodeServices = app()->make(LangCodeServices::class);
            return $langCodeServices->getColumn(['type_id' => $typeId, 'is_admin' => 0], 'lang_explain', 'code');
        }, 3600);
        return app('json')->success([$range => $lang]);
    }

    /**
     * Lấy loại ngôn ngữ mặc định được thiết lập trong trang quản trị
     * @return mixed
     */
    public function getDefaultLangType()
    {
        /** @var LangTypeServices $langTypeServices */
        $langTypeServices = app()->make(LangTypeServices::class);
        $lang_type = $langTypeServices->value(['is_default' => 1], 'file_name');
        return app('json')->success(compact('lang_type'));
    }

    /**
     * Lấy số phiên bản
     * @return mixed
     */
    public function getVersion()
    {
        $version = parse_ini_file(app()->getRootPath() . '.version');
        return app('json')->success(['version' => $version['version'], 'version_code' => $version['version_code']]);
    }

    /**
     * Lấy cache đa ngôn ngữ
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/06
     */
    public function getLangVersion()
    {
        return app('json')->success(app()->make(LangCodeServices::class)->getLangVersion());
    }

    /**
     * API tổng hợp cấu hình cơ bản của cửa hàng
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/03
     */
    public function getMallBasicConfig()
    {
        $data['site_name'] = sys_config('site_name'); //Tên website
        $data['site_url'] = sys_config('site_url'); //Địa chỉ website
        $data['wap_login_logo'] = sys_config('wap_login_logo'); //Logo đăng nhập trên di động
        $data['record_No'] = sys_config('record_No'); //Số đăng ký website
        $data['icp_url'] = sys_config('icp_url'); //Liên kết mã đăng ký ICP
        $data['network_security'] = sys_config('network_security'); //Đăng ký an ninh mạng
        $data['network_security_url'] = sys_config('network_security_url'); //Liên kết đăng ký an ninh mạng
        $data['store_self_mention'] = sys_config('store_self_mention'); //Có mở nhận tại cửa hàng không
        $data['invoice_func_status'] = sys_config('invoice_func_status'); //Bật chức năng hóa đơn
        $data['special_invoice_status'] = sys_config('special_invoice_status'); //Bật hóa đơn chuyên dụng (VAT)
        $data['member_func_status'] = sys_config('member_func_status'); //Bật hạng người dùng
        $data['balance_func_status'] = sys_config('balance_func_status'); //Bật chức năng số dư
        $data['recharge_switch'] = sys_config('recharge_switch'); //Bật/tắt nạp tiền trên Mini Program
        $data['member_card_status'] = sys_config('member_card_status'); //Có mở thành viên trả phí không
        $data['member_price_status'] = sys_config('member_price_status'); //Bật hiển thị giá chiết khấu thành viên của sản phẩm
        $data['ali_pay_status'] = sys_config('ali_pay_status') != '0'; //Alipay có được bật không
        $data['pay_weixin_open'] = sys_config('pay_weixin_open') != '0'; //WeChat có được bật không
        $data['yue_pay_status'] = sys_config('yue_pay_status') == 1 && sys_config('balance_func_status') != 0; //Số dư có được bật không
        $data['offline_pay_status'] = sys_config('offline_pay_status') == 1; //Thanh toán ngoại tuyến có được bật không
        $data['friend_pay_status'] = sys_config('friend_pay_status') == 1; //Bạn bè có được bật không
        $data['wechat_auth_switch'] = (int)in_array(1, sys_config('routine_auth_type')); //Bật/tắt đăng nhập WeChat
        $data['phone_auth_switch'] = (int)in_array(2, sys_config('routine_auth_type')); //Bật/tắt đăng nhập bằng số điện thoại
        $data['wechat_status'] = sys_config('wechat_appid') != '' && sys_config('wechat_appsecret') != ''; //OA WeChat có được cấu hình không
        $data['site_func'] = sys_config('model_checkbox', ['seckill', 'bargain', 'combination']);
        return app('json')->success($data);
    }

    /**
     * API liên kết chuyển hướng Mini Program
     * @param $id
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/2/26
     */
    public function getSchemeUrl($id)
    {
        $url = app()->make(RoutineSchemeServices::class)->value($id, 'url');
        if ($url) {
            echo '<script>window.location.href="' . $url . '";</script>';
        } else {
            echo '<h1>Không tìm thấy đường dẫn chuyển hướng</h1>';
        }
    }

    /**
     * Thanh toán qua nhà cung cấp dịch vụ WeChat
     * @param Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/4/7
     */
    public function servicePayResult(Request $request)
    {
        [$sub_mch_id, $out_trade_no, $check_code] = $request->getMore([
            ['sub_mch_id', ''],
            ['out_trade_no', ''],
            ['check_code', ''],
        ], true);
        $data['site_name'] = sys_config('site_name'); //Tên website
        $data['site_url'] = sys_config('site_url'); //Địa chỉ website
        $data['site_logo'] = sys_config('wap_login_logo'); //Logo đăng nhập trên di động
        $order = app()->make(StoreOrderServices::class)->getOne(['order_id' => $out_trade_no]);
        $data['goods_name'] = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);
        $data['pay_price'] = $order['pay_price'];
        $data['jump_url'] = sys_config('site_url') . '/pages/goods/order_pay_status/index?order_id=' . $out_trade_no . '&msg=Thanh toán thành công&type=3&totalPrice=' . $data['pay_price'];
        return app('json')->header(['X-Frame-Options' => 'payapp.weixin.qq.com'])->success($data);
    }

    public function getTransferInfo(Request $request, $order_id, $type)
    {
        $extractServices = app()->make(UserExtractServices::class);
        $lotteryRecordServices = app()->make(LuckLotteryRecordServices::class);
        $uid = (int)$request->uid();
        if ($type == 1) {
            $info = $extractServices->getExtractByOrderId($uid, $order_id);
            $info['true_extract_price'] = bcsub($info['extract_price'], $info['extract_fee'], 2);
        } else {
            $info = $lotteryRecordServices->getRecordByOrderId($uid, $order_id);
            $info['true_extract_price'] = $info['num'];
        }
        if ($info['state'] == 'WAIT_USER_CONFIRM') {
            $pay = new Pay('v3_wechat_pay');
            $res = $pay->queryTransferBills($order_id);
            if (isset($res['fail_reason']) && $res['fail_reason'] != '') {
                if ($type == 1) {
                    $extractServices->changeFail($info['id'], $info, 'Rút tiền thất bại, lý do: quá thời hạn nhận tiền');
                    $extractServices->update($info['id'], ['state' => 'FAIL']);
                } else {
                    $lotteryRecordServices->update($info['id'], ['state' => 'FAIL']);
                }
                $info['state'] = 'FAIL';
            }
        }
        switch ($info['channel_type']) {
            case 'wechat':
                $info['wechat_appid'] = sys_config('wechat_appid');
                break;
            case 'routine':
                $info['wechat_appid'] = sys_config('routine_appid');
                break;
            case 'app':
                $info['wechat_appid'] = sys_config('app_appid');
                break;
        }
        $info['mchid'] = sys_config('pay_weixin_mchid');
        return app('json')->success($info);
    }

    /**
     * Lấy thông tin chủ đề
     * @param string $type Loại chủ đề, khi là 'user' sẽ kèm thêm thông tin quyền người dùng và thống kê đơn hàng
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/25
     */
    public function themeInfo(Request $request, $type = '')
    {
        // Lấy tham số ID chủ đề
        [$theme_id] = $request->getMore([
            ['theme_id', 0],
        ], true);
        $themeInfo = app()->make(ThemeServices::class)->getThemeInfo($theme_id, $type);

        if (in_array($type, ['home', 'detail', 'user']) && $themeInfo) {
            foreach ($themeInfo['value'] as &$userDataItem) {
                if ($userDataItem['name'] == 'customerService') {
                    $userDataItem['routine_contact_type'] = (int)sys_config('routine_contact_type');
                }
            }
        }

        // Khi loại là 'user', xử lý cấu hình quyền và menu liên quan đến người dùng
        if ($type == 'user') {
            // Khởi tạo thông tin người dùng
            $uid = 0;
            $userInfo = [];
            if ($request->hasMacro('uid')) $uid = $request->uid();
            if ($request->hasMacro('user')) $userInfo = $request->user();

            // Lấy cấu hình bật/tắt chức năng hệ thống
            //Bật/tắt hạng người dùng
            $levelOpen = (bool)sys_config('member_func_status');
            //Bật/tắt tính năng CTV
            $brokerageOpen = (bool)sys_config('brokerage_func_status');
            //Bật/tắt tính năng số dư
            $balanceOpen = (bool)sys_config('balance_func_status');
            //Bật/tắt thành viên trả phí
            $sVipOpen = (bool)sys_config('member_card_status');
            //Chức năng hóa đơn
            $invoiceOpen = (bool)sys_config('invoice_func_status');

            // Khởi tạo cờ đánh dấu vai trò người dùng
            $userIsService = $userIsOrder = $userIsVerify = $userIsDelivery = $userIsPromoter = false;

            if ($uid && $userInfo) {
                /** @var StoreServiceServices $storeService */
                $storeService = app()->make(StoreServiceServices::class);
                /** @var StoreOrderServices $orderServices */
                $orderServices = app()->make(StoreOrderServices::class);
                /** @var StoreOrderRefundServices $storeOrderRefundServices */
                $orderRefundServices = app()->make(StoreOrderRefundServices::class);

                // Kiểm tra quyền theo vai trò người dùng
                //Có phải là nhân viên chăm sóc khách hàng không
                $userIsService = (bool)$storeService->checkoutIsService(['uid' => $uid, 'status' => 1]);
                //Có phải là quản lý đơn hàng không
                $userIsOrder = (bool)$storeService->checkoutIsService(['uid' => $uid, 'status' => 1, 'customer' => 1]);
                //Có phải là nhân viên xác nhận sử dụng không
                $userIsVerify = (bool)app()->make(SystemStoreStaffServices::class)->verifyStatus($uid);
                //Có phải là người giao hàng không
                $userIsDelivery = (bool)app()->make(DeliveryServiceServices::class)->checkoutIsService($uid);
                //Có phải là cộng tác viên không
                $userIsPromoter = (bool)app()->make(UserServices::class)->checkUserPromoter($uid, $userInfo);

                // Thống kê số lượng đơn hàng theo từng trạng thái, dùng để hiển thị badge trên menu
                $orderAuth = [];
                $countWhere = ['is_del' => 0, 'is_system_del' => 0, 'uid' => $uid];
                $orderAuth['/pages/goods/order_list/index'] = (int)$orderServices->count($countWhere + ['refund_status' => [0, 3], 'pid' => 0]);
                $orderAuth['/pages/goods/order_list/index?status=0'] = (int)$orderServices->count($countWhere + ['status' => 0]);
                $orderAuth['/pages/goods/order_list/index?status=1'] = (int)$orderServices->count($countWhere + ['status' => 1, 'pid' => 0]);
                $orderAuth['/pages/goods/order_list/index?status=2'] = (int)$orderServices->count($countWhere + ['status' => 2, 'pid' => 0]);
                $orderAuth['/pages/goods/order_list/index?status=3'] = (int)$orderServices->count($countWhere + ['status' => 3, 'pid' => 0]);
                $orderAuth['/pages/goods/order_list/index?status=4'] = (int)$orderServices->count($countWhere + ['status' => 4, 'pid' => 0]);
                $orderAuth['/pages/users/user_return_list/index'] = (int)$orderRefundServices->count(['uid' => $uid, 'is_cancel' => 0, 'is_del' => 0, 'refund_type' => [1, 2, 4, 5]]);
            }

            // Cấu hình quyền truy cập của các trang
            $auth = [];
            $auth['/pages/users/user_vip/index'] = $levelOpen;
            $auth['/pages/users/user_spread_user/index'] = $brokerageOpen && $userIsPromoter;
            $auth['/pages/annex/settled/index'] = $brokerageOpen && sys_config('store_brokerage_statu') == 1 && !$userIsPromoter;
            $auth['/pages/users/user_money/index'] = $balanceOpen;
            $auth['/pages/admin/order/index'] = $auth['/pages/admin/manage/index'] = $userIsOrder;
            $auth['/pages/admin/order_cancellation/index'] = $userIsVerify || $userIsDelivery;
            $auth['/pages/users/user_invoice_list/index'] = $invoiceOpen;
            $auth['/pages/annex/vip_paid/index'] = $sVipOpen;
            $auth['/kefu/mobile_list'] = $userIsService;

            // Xử lý cấu hình menu của chủ đề
            if ($themeInfo) {
                foreach ($themeInfo['value'] as &$userDataItem) {
                    if ($userDataItem['name'] == 'menus') {
                        foreach ($userDataItem['menuConfig']['list'] as &$menuDataItem) {
                            // Thiết lập quyền hiển thị menu và số trên badge
                            $menuDataItem['show'] = ($auth[$menuDataItem['info'][1]['value']] ?? true) && $menuDataItem['show'];
                            $menuDataItem['num'] = $orderAuth[$menuDataItem['info'][1]['value']] ?? 0;

                            // Xử lý liên kết CSKH, ghép thành URL đầy đủ
                            if ($menuDataItem['info'][1]['value'] == '/kefu/mobile_list') {
                                $menuDataItem['info'][1]['value'] = sys_config('site_url') . $menuDataItem['info'][1]['value'];
                                // Môi trường Mini Program bắt buộc dùng https
                                if ($request->isRoutine()) {
                                    $menuDataItem['info'][1]['value'] = str_replace('http://', 'https://', $menuDataItem['info'][1]['value']);
                                }
                            }

                            // Xử lý trang chat CSKH, thêm cấu hình loại phương thức liên hệ
                            if ($menuDataItem['info'][1]['value'] == '/pages/extension/customer_list/chat') {
                                if ($request->isRoutine()) {
                                    $menuDataItem['routine_contact_type'] = (int)sys_config('routine_contact_type', 0);
                                }
                            }

                            if ($menuDataItem['info'][1]['value'] == '/pages/users/user_spread_user/index' && $brokerageOpen && sys_config('store_brokerage_statu') == 1 && !$userIsPromoter) {
                                $menuDataItem['info'][0]['value'] = 'Đăng ký CTV';
                                $menuDataItem['info'][1]['value'] = '/pages/annex/settled/index';
                                $menuDataItem['show'] = true;
                            }
                        }
                    }
                }
            }
        }

        return app('json')->success($themeInfo);
    }

    /**
     * Lấy thông tin phiên bản chủ đề
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/25
     */
    public function themeVersion(Request $request)
    {
        [$theme_id] = $request->getMore([
            ['theme_id', 0],
        ], true);
        $themeVersion = app()->make(ThemeServices::class)->getThemeVersion($theme_id);
        return app('json')->success(['version' => $themeVersion]);
    }

    /**
     * Thành phần tùy chỉnh - người dùng
     * @param Request $request
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/12
     */
    public function themeUser(Request $request)
    {
        $userInfo = $request->uid() ? $request->user() : [];
        if (!$userInfo) return app('json')->fail('Chưa có dữ liệu');
        $user = [
            'nickname' => $userInfo['nickname'],
            'uid' => $userInfo['uid'],
            'image' => $userInfo['avatar'],
            'collection_num' => app()->make(StoreProductRelationServices::class)->count(['uid' => $userInfo['uid']]),
            'cart_num' => app()->make(StoreCartServices::class)->count(['uid' => $userInfo['uid']]),
            'order_num' => $userInfo['pay_count'],
            'integral' => $userInfo['integral'],
            'now_money' => $userInfo['now_money'],
            'brokerage_price' => $userInfo['brokerage_price'],
            'unread_msg_num' => app()->make(MessageSystemServices::class)->count(['uid' => $userInfo['uid'], 'look' => 0]),
        ];
        return app('json')->success($user);
    }

    /**
     * Thành phần tùy chỉnh - bài viết
     * @param Request $request
     * @return \think\Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/12
     */
    public function themeArticle(Request $request)
    {
        $where = $request->getMore([
            ['ids', ''],
            ['cid', ''],
            ['order', 0],
            ['sort', 0],
            ['limit', 10],
        ]);
        $data = app()->make(ArticleServices::class)->getThemeArticle($where);
        return app('json')->success($data);
    }

    /**
     * Thành phần tùy chỉnh - phiếu giảm giá
     * @param Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/14
     */
    public function themeCoupon(Request $request)
    {
        $where = $request->getMore([
            ['ids', ''],
            ['type', ''],
            ['user_type', ''],
            ['send_type', ''],
            ['is_min_price', 0],
            ['min_price', 0],
            ['start_time', ''],
            ['end_time', ''],
            ['order', 0],
            ['sort', 0],
            ['limit', 10],
        ]);
        $data = app()->make(StoreCouponIssueServices::class)->getThemeCoupon($where);
        return app('json')->success($data);
    }

    /**
     * Thành phần tùy chỉnh - sản phẩm
     * @param Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/14
     */
    public function themeProduct(Request $request)
    {
        $where = $request->getMore([
            ['ids', ''],
            ['cate_ids', ''],
            ['order', 0],
            ['sort', 0],
            ['limit', 10],
        ]);
        $data = app()->make(StoreProductServices::class)->getThemeProduct($where);
        return app('json')->success($data);
    }

    /**
     * Lấy dữ liệu điều hướng của chủ đề
     * Gọi phương thức themeNavigation trong ThemeServices để lấy cấu hình điều hướng và trả về phản hồi JSON
     * @return mixed
     */
    public function themeNavigation()
    {
        // Khởi tạo instance ThemeServices và gọi phương thức themeNavigation để lấy dữ liệu điều hướng
        $data = app()->make(ThemeServices::class)->themeNavigation();
        // Trả về phản hồi thành công, kèm dữ liệu điều hướng
        return app('json')->success($data);
    }
}
