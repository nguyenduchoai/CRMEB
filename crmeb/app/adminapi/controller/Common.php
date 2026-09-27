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
namespace app\adminapi\controller;

use app\services\system\config\SystemConfigServices;
use app\services\system\config\SystemConfigTabServices;
use app\services\system\SystemAuthServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;
use app\services\product\product\StoreProductReplyServices;
use app\services\system\UpgradeServices;
use app\services\user\UserExtractServices;
use app\services\product\sku\StoreProductAttrValueServices;
use app\services\system\SystemMenusServices;
use app\services\user\UserServices;
use crmeb\services\CacheService;
use crmeb\services\HttpService;
use think\facade\Config;

/**
 * Lớp cơ sở API công dùng, chủ yếu chứa các API công dùng
 * Class Common
 * @package app\adminapi\controller
 */
class Common extends AuthController
{
    /**
     * Lấy logo
     * @return mixed
     */
    public function getLogo()
    {
        return app('json')->success([
            'logo' => sys_config('site_logo'),
            'logo_square' => sys_config('site_logo_square'),
            'site_name' => sys_config('site_name')
        ]);
    }

    /**
     * Lấy thông tin ủy quyền
     * @return mixed
     */
    public function auth()
    {
        $version = get_crmeb_version();
        $host = $this->request->host();
        // Tên miền bình thường
        $res = HttpService::request('http://authorize.crmeb.net/api/auth_cert_query', 'post', [
            'domain_name' => $host,
            'label' => 34,
            'version' => $version
        ]);
        $res = $res ? json_decode($res, true) : [];
        $status = $res['data']['status'] ?? -9;
        switch ((int)$status) {
            case 1:
                //Duyệt thành công
                $authCode = $res['data']['auth_code'] ?? '';
                $autoContent = $res['data']['auto_content'] ?? '';
                try {
                    /** @var SystemConfigServices $services */
                    $services = app()->make(SystemConfigServices::class);
                    if ($services->count(['menu_name' => 'cert_crmeb'])) {
                        $services->update(['menu_name' => 'cert_crmeb'], ['value' => json_encode($autoContent . ',' . $authCode)]);
                    } else {
                        $services->save([
                            'menu_name' => 'cert_crmeb',
                            'type' => 'text',
                            'input_type' => 'input',
                            'config_tab_id' => 1,
                            'value' => json_encode($autoContent . ',' . $authCode),
                            'status' => 2,
                            'info' => 'Khóa cấp phép'
                        ]);
                    }
                } catch (\Throwable $e) {
                    return app('json')->fail('Cấp phép thành công nhưng ghi vào cơ sở dữ liệu thất bại, vui lòng kiểm tra cấu hình kết nối cơ sở dữ liệu');
                }
                return app('json')->success(['status' => 1, 'copyright' => $res['data']['copyright'], 'authCode' => $authCode, 'day' => 0, 'force_reminder' => $upgradeStatus['force_reminder'] ?? 0]);
            default:
                return app('json')->success(['status' => -9, 'force_reminder' => $upgradeStatus['force_reminder'] ?? 0]);
        }
    }

    /**
     * Yêu cầu cấp phép
     * @return mixed
     */
    public function auth_apply(SystemAuthServices $services)
    {
        $data = $this->request->postMore([
            ['company_name', ''],
            ['domain_name', ''],
            ['order_id', ''],
            ['phone', ''],
            ['label', 19],
            ['captcha', ''],
        ]);
        if (!$data['company_name']) {
            return app('json')->fail('Vui lòng điền tên công ty');
        }
        if (!$data['domain_name']) {
            return app('json')->fail('Vui lòng điền tên miền được cấp phép');
        }

        if (!$data['phone']) {
            return app('json')->fail('Vui lòng điền số điện thoại');
        }
        if (!$data['order_id']) {
            return app('json')->fail('Vui lòng điền ID đơn hàng');
        }
        if (!$data['captcha']) {
            return app('json')->fail('Vui lòng điền mã xác thực');
        }
        $services->authApply($data);
        return app('json')->success('Yêu cầu cấp phép thành công');

    }

    /**
     * Dữ liệu thống kê phần đầu trang chủ
     * @return mixed
     */
    public function homeStatics()
    {
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $info = $orderServices->homeStatics();
        return app('json')->success(compact('info'));
    }

    /**
    * Tính tỷ lệ tăng trưởng
    * Trường hợp đặc biệt:
    * 1. Khi giá trị hiện tại và giá trị kỳ trước đều là 0, trả về 0;
    * 2. Khi giá trị kỳ trước là 0, trả về giá trị hiện tại;
    * 3. Khi giá trị hiện tại là 0, trả về số âm của giá trị kỳ trước.
    *
    * @param float $nowValue Giá trị hiện tại
    * @param float $lastValue Giá trị kỳ trước
    * @return float Tỷ lệ tăng trưởng
    */
    public function growth($nowValue, $lastValue)
    {
       if ($lastValue == 0 && $nowValue == 0) return 0;
       if ($lastValue == 0) return round($nowValue, 2);
       if ($nowValue == 0) return -round($lastValue, 2);
       return bcmul(bcdiv((bcsub($nowValue, $lastValue, 2)), $lastValue, 2), 100, 2);
    }


    /**
     * Biểu đồ đơn hàng
     */
    public function orderChart()
    {
        $cycle = $this->request->param('cycle') ?: 'thirtyday';//Mặc định 30 ngày
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $chartdata = $orderServices->orderCharts($cycle);
        return app('json')->success($chartdata);
    }

    /**
     * Biểu đồ người dùng
     */
    public function userChart()
    {
        /** @var UserServices $uServices */
        $uServices = app()->make(UserServices::class);
        $chartdata = $uServices->userChart();
        return app('json')->success($chartdata);
    }

    /**
     * Xếp hạng giá trị giao dịch
     * @return mixed
     */
    public function purchaseRanking()
    {
        /** @var StoreProductAttrValueServices $valueServices */
        $valueServices = app()->make(StoreProductAttrValueServices::class);
        $list = $valueServices->purchaseRanking();
        return app('json')->success(compact('list'));
    }

    /**
     * Thống kê việc cần xử lý
     * @return mixed
     */
    public function jnotice()
    {
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $data['ordernum'] = $orderServices->storeOrderCount();
        $store_stock = sys_config('store_stock');
        if ($store_stock < 0) $store_stock = 2;
        /** @var StoreProductServices $storeServices */
        $storeServices = app()->make(StoreProductServices::class);
        $data['inventory'] = $storeServices->count(['type' => 5, 'store_stock' => $store_stock]);//Ngưỡng cảnh báo tồn kho
        /** @var StoreProductReplyServices $replyServices */
        $replyServices = app()->make(StoreProductReplyServices::class);
        $data['commentnum'] = $replyServices->replyCount();
        /** @var UserExtractServices $extractServices */
        $extractServices = app()->make(UserExtractServices::class);
        $data['reflectnum'] = $extractServices->userExtractCount();//Rút tiền
        $data['msgcount'] = intval($data['ordernum']) + intval($data['inventory']) + intval($data['commentnum']) + intval($data['reflectnum']);
        $data['newOrderId'] = $orderServices->newOrderId(1);
        if (count($data['newOrderId'])) $orderServices->newOrderUpdate($data['newOrderId']);
        $value = [];
        if ($data['ordernum'] != 0) {
            $value[] = [
                'title' => "Bạn có $data[ordernum] đơn hàng chờ giao hàng",
                'type' => 1,
                'url' => '/' . Config::get('app.admin_prefix', 'admin') . '/order/list?status=1'
            ];
        }
        if ($data['inventory'] != 0) {
            $value[] = [
                'title' => "Bạn có $data[inventory] sản phẩm cảnh báo tồn kho",
                'type' => 2,
                'url' => '/' . Config::get('app.admin_prefix', 'admin') . '/product/product_list?type=5',
            ];
        }
        if ($data['commentnum'] != 0) {
            $value[] = [
                'title' => "Bạn có $data[commentnum] đánh giá chờ phản hồi",
                'type' => 3,
                'url' => '/' . Config::get('app.admin_prefix', 'admin') . '/product/product_reply?is_reply=0'
            ];
        }
        if ($data['reflectnum'] != 0) {
            $value[] = [
                'title' => "Bạn có $data[reflectnum] yêu cầu rút tiền chờ duyệt",
                'type' => 4,
                'url' => '/' . Config::get('app.admin_prefix', 'admin') . '/finance/user_extract/index?status=0',
            ];
        }
        return app('json')->success($this->noticeData($value));
    }

    /**
     * Định dạng trả về của thông báo
     * @param array $data
     * @return array
     */
    public function noticeData(array $data): array
    {
        // Biểu tượng thông báo
        $iconColor = [
            // Thông báo email
            'mail' => [
                'icon' => 'md-mail',
                'color' => '#3391e5'
            ],
            // Thông báo thông thường
            'bulb' => [
                'icon' => 'md-bulb',
                'color' => '#87d068'
            ],
            // Thông báo cảnh báo
            'information' => [
                'icon' => 'md-information',
                'color' => '#fe5c57'
            ],
            // Thông báo theo dõi
            'star' => [
                'icon' => 'md-star',
                'color' => '#ff9900'
            ],
            // Thông báo yêu cầu
            'people' => [
                'icon' => 'md-people',
                'color' => '#f06292'
            ],
        ];
        // Loại thông báo
        $type = array_keys($iconColor);
        // Định dạng dữ liệu mặc định
        $default = [
            'icon' => 'md-bulb',
            'iconColor' => '#87d068',
            'title' => '',
            'url' => '',
            'type' => 'bulb',
            'read' => 0,
            'time' => 0
        ];
        $value = [];
        foreach ($data as $item) {
            $val = array_merge($default, $item);
            if (isset($item['type']) && in_array($item['type'], $type)) {
                $val['type'] = $item['type'];
                $val['iconColor'] = $iconColor[$item['type']]['color'] ?? '';
                $val['icon'] = $iconColor[$item['type']]['icon'] ?? '';
            }
            $value[] = $val;
        }
        return $value;
    }

    /**
     * Định dạng menu
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function menusList()
    {
        /** @var SystemMenusServices $menusServices */
        $menusServices = app()->make(SystemMenusServices::class);
        $list = $menusServices->getSearchList();
        $counts = $menusServices->getColumn([
            ['is_show', '=', 1],
            ['auth_type', '=', 1],
            ['is_del', '=', 0],
            ['is_show_path', '=', 0],
        ], 'pid');
        $data = [];
        foreach ($list as $key => $item) {
            $pid = $item->getData('pid');
            $data[$key] = json_decode($item, true);
            $data[$key]['pid'] = $pid;
            $data[$key]['menu_path'] = '/' . config('app.admin_prefix', 'admin') . $item['menu_path'];
            if (in_array($item->id, $counts)) {
                $data[$key]['type'] = 1;
            } else {
                $data[$key]['type'] = 0;
            }
        }
        return app('json')->success(sort_list_tier($data));
    }

    /**
     * Truy vấn mua bản quyền
     * @return mixed
     */
    public function copyright()
    {
        $copyrightContext = sys_config('nncnL_crmeb_copyright', '');
        $copyrightImage = sys_config('nncnL_crmeb_copyright_image', '');
        return app('json')->success(compact('copyrightContext', 'copyrightImage'));
    }

    /**
     * Lưu bản quyền
     * @return mixed
     */
    public function saveCopyright()
    {
        [$copyright, $copyrightImg] = $this->request->postMore(['copyright', 'copyright_img',], true);
        /** @var SystemConfigServices $services */
        $services = app()->make(SystemConfigServices::class);
        if ($services->count(['menu_name' => 'nncnL_crmeb_copyright'])) {
            $services->update(['menu_name' => 'nncnL_crmeb_copyright'], ['value' => json_encode($copyright)]);
        } else {
            $services->save([
                'menu_name' => 'nncnL_crmeb_copyright',
                'type' => 'text',
                'input_type' => 'input',
                'config_tab_id' => 1,
                'value' => json_encode($copyright),
                'status' => 2,
                'info' => ''
            ]);
        }
        if ($services->count(['menu_name' => 'nncnL_crmeb_copyright_image'])) {
            $services->update(['menu_name' => 'nncnL_crmeb_copyright_image'], ['value' => json_encode($copyrightImg)]);
        } else {
            $services->save([
                'menu_name' => 'nncnL_crmeb_copyright_image',
                'type' => 'text',
                'input_type' => 'input',
                'config_tab_id' => 1,
                'value' => json_encode($copyrightImg),
                'status' => 2,
                'info' => ''
            ]);
        }
        CacheService::clear();
        return app('json')->success('Lưu thành công');
    }

    /**
     * Menu tìm kiếm hệ thống
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2024/2/1
     */
    public function menusSearch()
    {
        // Lấy từ khóa từ request
        [$keyword] = $this->request->postMore([
            ['keyword', ''],
        ], true);
        if (empty($keyword)) {
           return app('json')->fail('Vui lòng nhập từ khóa', 'Từ khóa không được để trống');
        }

        // Lấy instance service menu hệ thống
        $menusServices = app()->make(SystemMenusServices::class);
        // Truy vấn danh sách menu
        $menusList = $menusServices->selectList([['menu_name', 'like', '%' . $keyword . '%'], ['auth_type', '=', 1]], 'menu_name as title,menu_path as path,id')->toArray();
        // Lấy instance service cấu hình hệ thống
        $configServices = app()->make(SystemConfigServices::class);
        // Lấy instance service nhãn cấu hình hệ thống
        $configTabServices = app()->make(SystemConfigTabServices::class);
        // Truy vấn danh sách mục cấu hình
        $configList = $configServices->selectList([['info', 'like', '%' . $keyword . '%']], 'info as title,config_tab_id')->toArray();
        // Truy vấn danh sách nhãn mục cấu hình
        $configTabList = $configTabServices->selectList([['title', 'like', '%' . $keyword . '%']], 'title,id as config_tab_id')->toArray();
        // Gộp danh sách mục cấu hình và danh sách nhãn mục cấu hình
        $configAllList = array_merge($configList, $configTabList);
        // Lấy ID nhãn tương ứng của mục cấu hình
        $tabIds = array_unique(array_column($configAllList, 'config_tab_id'));
        // Truy vấn danh sách nhãn mục cấu hình
        $tabList = $configTabServices->getColumn([['id', 'in', $tabIds]], 'menus_id', 'id');

        // Ánh xạ ID menu trong danh sách nhãn mục cấu hình với ID menu trong danh sách mục cấu hình
        foreach ($configAllList as &$item1) {
            $item1['menus_id'] = $tabList[$item1['config_tab_id']] ?? 0;
        }
        // Lấy ID menu tương ứng của nhãn mục cấu hình
        $configTabIds = array_values($tabList);
        // Truy vấn danh sách menu tương ứng của nhãn mục cấu hình
        $configMenusList = $menusServices->getColumn([['id', 'in', $configTabIds]], 'menu_name as title,menu_path as path,id', 'id');

        // Ánh xạ ID menu trong danh sách mục cấu hình với ID menu tương ứng của nhãn mục cấu hình
        foreach ($configAllList as $item2) {
            if ($item2['menus_id'] == 0) {
                continue;
            }
            $menusList[] = [
                'title' => $configMenusList[$item2['menus_id']]['title'] . '-' . $item2['title'],
                'path' => $configMenusList[$item2['menus_id']]['path'] . '?tab_id=' . $item2['config_tab_id'],
                'id' => $configMenusList[$item2['menus_id']]['id']
            ];
        }
        // Thêm tiền tố đường dẫn trong danh sách menu vào thuộc tính path của mỗi mục menu
        $adminPrefix = '/' . Config::get('app.admin_prefix', 'admin');
        foreach ($menusList as &$item) {
           if (strpos($item['path'], '/') !== 0) {
               $item['path'] = $adminPrefix . '/' . ltrim($item['path'], '/');
           } else {
               $item['path'] = $adminPrefix . $item['path'];
           }
        }
        // Trả về danh sách menu dạng JSON
        return app('json')->success($menusList);
    }
}
