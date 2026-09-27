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

namespace app\adminapi\controller;

use app\Request;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\activity\coupon\StoreCouponProductServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\system\UpgradeServices;
use app\services\user\UserBillServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserBrokerageFrozenServices;
use think\facade\Db;
use think\facade\Env;


class UpgradeController
{
    /**
     * @var UpgradeServices
     */
    private $services;

    /**
     * UpgradeController constructor.
     * @param UpgradeServices $services
     */
    public function __construct(UpgradeServices $services)
    {
        $this->services = $services;
    }

    /**
     * Trang nâng cấp chương trình
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request)
    {
        $data = $this->upData();
        $Title = "Trình nâng cấp CRMEB";
        $Powered = "Powered by CRMEB";

        //Lấy số phiên bản hiện tại
        $version_now = $this->getversion('.version')['version'];
        $version_new = $data['new_version'];
        $isUpgrade = true;
        $executeIng = false;

        return view('/upgrade/step1', [
            'title' => $Title,
            'powered' => $Powered,
            'version_now' => $version_now,
            'version_new' => $version_new,
            'isUpgrade' => json_encode($isUpgrade),
            'executeIng' => json_encode($executeIng),
            'next' => 1,
            'action' => 'upgrade'
        ]);
    }

    /**
     * Lấy số phiên bản hiện tại
     * @return array
     */
    public function getversion($str)
    {
        $version_arr = [];
        $curent_version = @file(app()->getRootPath() . $str);

        foreach ($curent_version as $val) {
            list($k, $v) = explode('=', $val);
            $version_arr[$k] = $v;
        }
        return $version_arr;
    }

    /**
     * Ghi lại quá trình nâng cấp
     * @param string $field
     * @param int $n
     * @return bool
     */
    public function setIsUpgrade(string $field, int $n = 0)
    {
        $upgrade = parse_ini_file(public_path('upgrade') . '.upgrade');
        if ($n) {
            if (!is_array($upgrade)) {
                $upgrade = [];
            }
            $string = '';
            foreach ($upgrade as $key => $item) {
                $string .= $key . '=' . $item . "\r\n";
            }
            $string .= $field . '=' . $n . "\r\n";
            file_put_contents(public_path('upgrade') . '.upgrade', $string);
            return true;
        } else {
            if (!is_array($upgrade)) {
                return false;
            }
            return isset($upgrade[$field]);
        }
    }

    public function upgrade(Request $request)
    {
        list($sleep, $page, $prefix) = $request->getMore([
            ['sleep', 0],
            ['page', 1],
            ['prefix', 'eb_'],
        ], true);
        $data = $this->upData();
        $code_now = $this->getversion('.version')['version_code'];
        if ($data['new_code'] == $code_now) {
            return app('json')->success(['sleep' => -1]);
        }
        $sql_arr = [];
        foreach ($data['update_sql'] as $items) {
            if ($items['code'] > $code_now) {
                $sql_arr[] = $items;
            }
        }
        //Thực thi sql xong, bắt đầu sửa dữ liệu
        if (!isset($sql_arr[$sleep])) {
//            $limit = 100;
//            if (!$this->setIsUpgrade('money')) {
//                $res = $this->handleMoney((int)$sleep, (int)$page, (int)$limit);
//                return app('json')->success($res);
//            } elseif (!$this->setIsUpgrade('brokerage')) {
//                $res = $this->handleBrokerage((int)$sleep, (int)$page, (int)$limit);
//                return app('json')->success($res);
//            } elseif (!$this->setIsUpgrade('orderRefund')) {
//                $res = $this->handleOrderRefund((int)$sleep, (int)$page, (int)$limit);
//                return app('json')->success($res);
//            } else {
//                file_put_contents(app()->getRootPath() . '.version', "version=" . $data['new_version'] . "\nversion_code=" . $data['new_code']);
//                return app('json')->success(['sleep' => -1]);
//            }
//            $limit = 100;
//            if (!$this->setIsUpgrade('cartInfo')) {
//                $res = $this->handleCartInfo((int)$sleep, (int)$page, (int)$limit);
//                return app('json')->success($res);
//            } else {
//                file_put_contents(app()->getRootPath() . '.version', "version=" . $data['new_version'] . "\nversion_code=" . $data['new_code'] . "\napp_id=ze7x9rxsv09l6pvsyo" . "\napp_key=fuF7U9zaybLa5gageVQzxtxQMFnvU2OI");
//                $this->services->generateSignature();
//                return app('json')->success(['sleep' => -1]);
//            }
//            $limit = 100;
//            if (!$this->setIsUpgrade('coupon')) {
//                $res = $this->handleCoupon((int)$sleep, (int)$page, (int)$limit);
//                return app('json')->success($res);
//            } else {
//                $this->setEnv();
//                file_put_contents(app()->getRootPath() . '.version', "version=" . $data['new_version'] . "\nversion_code=" . $data['new_code'] . "\nplatform=CRMEB\napp_id=ze7x9rxsv09l6pvsyo" . "\napp_key=fuF7U9zaybLa5gageVQzxtxQMFnvU2OI");
//                $this->services->generateSignature();
//                return app('json')->success(['sleep' => -1]);
//            }
            file_put_contents(app()->getRootPath() . '.version', "version=" . $data['new_version'] . "\nversion_code=" . $data['new_code'] . "\nplatform=CRMEB\napp_id=ze7x9rxsv09l6pvsyo" . "\napp_key=fuF7U9zaybLa5gageVQzxtxQMFnvU2OI");
            $this->services->generateSignature();
            return app('json')->success(['sleep' => -1]);
        }
        $sql = $sql_arr[$sleep];
        Db::startTrans();
        try {
            if ($sql['type'] == 1) {
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $table = $prefix . $sql['table'];
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (!empty(Db::query($findSql))) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng đã tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    Db::execute($upSql);
                    $item['table'] = $table;
                    $item['status'] = 1;
                    $item['error'] = $table . 'bảng đã được thêm thành công';
                    $item['sleep'] = $sleep + 1;
                    $item['add_time'] = date('Y-m-d H:i:s', time());
                    Db::commit();
                    return app('json')->success($item);
                }
            } elseif ($sql['type'] == 2) {
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $table = $prefix . $sql['table'];
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (empty(Db::query($findSql))) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng không tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    Db::execute($upSql);
                    $item['table'] = $table;
                    $item['status'] = 1;
                    $item['error'] = $table . 'bảng đã được xóa thành công';
                    $item['sleep'] = $sleep + 1;
                    $item['add_time'] = date('Y-m-d H:i:s', time());
                    Db::commit();
                    return app('json')->success($item);
                }
            } elseif ($sql['type'] == 3) {
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $table = $prefix . $sql['table'];
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (!empty(Db::query($findSql))) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng, trường' . $sql['field'] . 'đã tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    Db::execute($upSql);
                    $item['table'] = $table;
                    $item['status'] = 1;
                    $item['error'] = $table . 'bảng, trường' . $sql['field'] . 'đã được thêm thành công';
                    $item['sleep'] = $sleep + 1;
                    $item['add_time'] = date('Y-m-d H:i:s', time());
                    Db::commit();
                    return app('json')->success($item);
                }
            } elseif ($sql['type'] == 4) {
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $table = $prefix . $sql['table'];
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (empty(Db::query($findSql))) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng, trường' . $sql['field'] . 'không tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    Db::execute($upSql);
                    $item['table'] = $table;
                    $item['status'] = 1;
                    $item['error'] = $table . 'bảng, trường' . $sql['field'] . 'đã được sửa thành công';
                    $item['sleep'] = $sleep + 1;
                    $item['add_time'] = date('Y-m-d H:i:s', time());
                    Db::commit();
                    return app('json')->success($item);
                }
            } elseif ($sql['type'] == 5) {
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $table = $prefix . $sql['table'];
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (empty(Db::query($findSql))) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng, trường' . $sql['field'] . 'không tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    Db::execute($upSql);
                    $item['table'] = $table;
                    $item['status'] = 1;
                    $item['error'] = $table . 'bảng, trường' . $sql['field'] . 'đã được xóa thành công';
                    $item['sleep'] = $sleep + 1;
                    $item['add_time'] = date('Y-m-d H:i:s', time());
                    Db::commit();
                    return app('json')->success($item);
                }
            } elseif ($sql['type'] == 6) {
                $table = $prefix . $sql['table'] ?? '';
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (!empty(Db::query($findSql))) {
                        $item['table'] = $prefix . $sql['table'];
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng, dữ liệu này đã tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    if (isset($sql['whereSql']) && $sql['whereSql']) {
                        $whereTable = $prefix . $sql['whereTable'] ?? '';
                        $whereSql = str_replace('@whereTable', $whereTable, $sql['whereSql']);
                        $tabId = Db::query($whereSql)[0]['tabId'] ?? 0;
                        if (!$tabId) {
                            $item['table'] = $whereTable;
                            $item['status'] = 1;
                            $item['error'] = 'ID cha cần tra cứu không tồn tại';
                            $item['sleep'] = $sleep + 1;
                            $item['add_time'] = date('Y-m-d H:i:s', time());
                            Db::commit();
                            return app('json')->success($item);
                        }
                        $upSql = str_replace('@tabId', $tabId, $upSql);
                    }
                    if (Db::execute($upSql)) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = 'Thêm dữ liệu thành công';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
            } elseif ($sql['type'] == 7) {
                $table = $prefix . $sql['table'] ?? '';
                if (isset($sql['findSql']) && $sql['findSql']) {
                    $findSql = str_replace('@table', $table, $sql['findSql']);
                    if (empty(Db::query($findSql))) {
                        $item['table'] = $prefix . $sql['table'];
                        $item['status'] = 1;
                        $item['error'] = $table . 'bảng, dữ liệu này không tồn tại';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    if (isset($sql['whereSql']) && $sql['whereSql']) {
                        $whereTable = $prefix . $sql['whereTable'] ?? '';
                        $whereSql = str_replace('@whereTable', $whereTable, $sql['whereSql']);
                        $tabId = Db::query($whereSql)[0]['tabId'] ?? 0;
                        if (!$tabId) {
                            $item['table'] = $whereTable;
                            $item['status'] = 1;
                            $item['error'] = 'ID cha cần tra cứu không tồn tại';
                            $item['sleep'] = $sleep + 1;
                            $item['add_time'] = date('Y-m-d H:i:s', time());
                            Db::commit();
                            return app('json')->success($item);
                        }
                        $upSql = str_replace('@tabId', $tabId, $upSql);
                    }
                    if (Db::execute($upSql)) {
                        $item['table'] = $table;
                        $item['status'] = 1;
                        $item['error'] = 'Sửa dữ liệu thành công';
                        $item['sleep'] = $sleep + 1;
                        $item['add_time'] = date('Y-m-d H:i:s', time());
                        Db::commit();
                        return app('json')->success($item);
                    }
                }
            } elseif ($sql['type'] == 8) {

            } elseif ($sql['type'] == -1) {
                $table = $prefix . $sql['table'];
                if (isset($sql['sql']) && $sql['sql']) {
                    $upSql = $sql['sql'];
                    $upSql = str_replace('@table', $table, $upSql);
                    if (isset($sql['new_table']) && $sql['new_table']) {
                        $new_table = $prefix . $sql['new_table'];
                        $upSql = str_replace('@new_table', $new_table, $upSql);
                    }
                    Db::execute($upSql);
                    $item['table'] = $table;
                    $item['status'] = 1;
                    $item['error'] = $table . ' thực thi sql thành công';
                    $item['sleep'] = $sleep + 1;
                    $item['add_time'] = date('Y-m-d H:i:s', time());
                    Db::commit();
                    return app('json')->success($item);
                }
            }
        } catch (\Throwable $e) {
            $item['table'] = $prefix . $sql['table'];
            $item['status'] = 0;
            $item['sleep'] = $sleep + 1;
            $item['add_time'] = date('Y-m-d H:i:s', time());
            $item['error'] = $e->getMessage();
            Db::rollBack();
            return app('json')->success($item);
        }
    }

    /**
     * Ghi lại file .env
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/04
     */
    public function setEnv()
    {
        $unique = uniqid();
        //Đọc file cấu hình, và thay thế bằng dữ liệu cấu hình thực tế 1
        $strConfig = file_get_contents(root_path() . 'public/install/.env');
        $strConfig = str_replace('#DB_HOST#', Env::get('DATABASE.HOSTNAME', ''), $strConfig);
        $strConfig = str_replace('#DB_NAME#', Env::get('DATABASE.DATABASE', ''), $strConfig);
        $strConfig = str_replace('#DB_USER#', Env::get('DATABASE.USERNAME', ''), $strConfig);
        $strConfig = str_replace('#DB_PWD#', Env::get('DATABASE.PASSWORD', ''), $strConfig);
        $strConfig = str_replace('#DB_PORT#', Env::get('DATABASE.HOSTPORT', ''), $strConfig);
        $strConfig = str_replace('#DB_PREFIX#', Env::get('DATABASE.PREFIX', ''), $strConfig);
        $strConfig = str_replace('#DB_CHARSET#', 'utf8', $strConfig);
        $strConfig = str_replace('#CACHE_TYPE#', 'redis', $strConfig);
        $strConfig = str_replace('#CACHE_PREFIX#', 'cache_' . $unique . ':', $strConfig);
        $strConfig = str_replace('#CACHE_TAG_PREFIX#', 'cache_tag_' . $unique . ':', $strConfig);
        $strConfig = str_replace('#RB_HOST#', Env::get('REDIS.REDIS_HOSTNAME', ''), $strConfig);
        $strConfig = str_replace('#RB_PORT#', Env::get('REDIS.PORT', ''), $strConfig);
        $strConfig = str_replace('#RB_PWD#', Env::get('REDIS.REDIS_PASSWORD', ''), $strConfig);
        $strConfig = str_replace('#RB_SELECT#', Env::get('REDIS.SELECT', ''), $strConfig);
        $strConfig = str_replace('#QUEUE_NAME#', $unique, $strConfig);
        @chmod(root_path() . '/.env', 0777); //Đường dẫn file cấu hình cơ sở dữ liệu
        @file_put_contents(root_path() . '/.env', $strConfig); //Đường dẫn file cấu hình cơ sở dữ liệu
    }

    /**
     * Cập nhật coupon theo danh mục
     * @param int $sleep
     * @param int $page
     * @param int $limit
     * @return array
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/04
     */
    public function handleCoupon(int $sleep = 1, int $page = 1, int $limit = 100)
    {
        $list = app()->make(StoreCouponIssueServices::class)->selectList([['category_id', '>', 0]], 'id,category_id', $page, $limit)->toArray();
        if (count($list)) {
            $allData = [];
            foreach ($list as $item) {
                $data = [
                    'coupon_id' => $item['id'],
                    'product_id' => 0,
                    'category_id' => $item['category_id']
                ];
                $allData[] = $data;
            }
            if ($allData) {
                app()->make(StoreCouponProductServices::class)->saveAll($allData);
            }
            $info['table'] = 'store_coupon_product';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu phiếu giảm giá theo danh mục thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = $page + 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        } else {
            $this->setIsUpgrade('coupon', 1);
            $info['table'] = 'store_coupon_product';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu phiếu giảm giá theo danh mục thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        }
    }

    /**
     * Xử lý dữ liệu số dư lịch sử
     * @param int $sleep
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function handleMoney(int $sleep = 1, int $page = 1, int $limit = 100)
    {
        /** @var UserBillServices $userBillServics */
        $userBillServics = app()->make(UserBillServices::class);
        $where = ['category' => 'now_money', 'type' => ['pay_product', 'pay_product_refund', 'system_add', 'system_sub', 'recharge', 'lottery_use', 'lottery_add']];
        $list = $userBillServics->getList($where, '*', $page, $limit, [], 'id asc');
        if ($list) {
            $allData = $data = [];
            foreach ($list as $item) {
                $data = [
                    'uid' => $item['uid'],
                    'link_id' => $item['link_id'],
                    'pm' => $item['pm'],
                    'title' => $item['title'],
                    'type' => $item['type'],
                    'number' => $item['number'],
                    'balance' => $item['balance'],
                    'mark' => $item['mark'],
                    'add_time' => strtotime($item['add_time']),
                ];
                $allData[] = $data;
            }
            if ($allData) {
                /** @var UserMoneyServices $userMoneyServices */
                $userMoneyServices = app()->make(UserMoneyServices::class);
                $userMoneyServices->saveAll($allData);
            }
            $info['table'] = 'user_money';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu số dư thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = $page + 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        } else {
            $this->setIsUpgrade('money', 1);
            $info['table'] = 'user_money';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu số dư thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        }
    }

    /**
     * Xử lý dữ liệu hoa hồng lịch sử
     * @param int $sleep
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function handleBrokerage(int $sleep = 1, int $page = 1, int $limit = 100)
    {
        /** @var UserBillServices $userBillServics */
        $userBillServics = app()->make(UserBillServices::class);
        $where = ['category' => ['', 'now_money'], 'type' => ['brokerage', 'brokerage_user', 'extract', 'refund', 'extract_fail']];
        $list = $userBillServics->getList($where, '*', $page, $limit, [], 'id asc');
        if ($list) {
            $allData = $data = [];
            /** @var  $brokerageFrozenServices */
            $brokerageFrozenServices = app()->make(UserBrokerageFrozenServices::class);
            $frozenList = $brokerageFrozenServices->getColumn([['uill_id', 'in', array_column($list, 'id')], ['frozen_time', '>', time()]], 'uill_id,frozen_time', 'uill_id');
            foreach ($list as $item) {
                if (in_array($item['type'], ['brokerage_user', 'extract', 'refund', 'extract_fail'])) {
                    $type = $item['type'];
                } else {
                    if (strpos($item['mark'], 'Cấp 2')) {
                        $type = 'two_brokerage';
                    } else {
                        $type = 'one_brokerage';
                    }
                }
                $data = [
                    'uid' => $item['uid'],
                    'link_id' => $item['link_id'],
                    'pm' => $item['pm'],
                    'title' => $item['title'],
                    'type' => $type,
                    'number' => $item['number'],
                    'balance' => $item['balance'],
                    'mark' => $item['mark'],
                    'frozen_time' => $frozenList[$item['id']]['frozen_time'] ?? 0,
                    'add_time' => strtotime($item['add_time']),
                ];
                $allData[] = $data;
            }
            if ($allData) {
                /** @var UserBrokerageServices $userBrokerageServices */
                $userBrokerageServices = app()->make(UserBrokerageServices::class);
                $userBrokerageServices->saveAll($allData);
            }
            $info['table'] = 'user_brokerage';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu hoa hồng thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = $page + 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        } else {
            $this->setIsUpgrade('brokerage', 1);
            $info['table'] = 'user_brokerage';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu hoa hồng thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        }
    }

    /**
     * Xử lý dữ liệu hoàn tiền lịch sử
     * @param int $sleep
     * @param int $page
     * @param int $limit
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handleOrderRefund(int $sleep = 1, int $page = 1, int $limit = 100)
    {
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $list = $storeOrderServices->getSplitOrderList(['refund_status' => [1, 2], ['refund_type' => [1, 2, 4, 5, 6]]], ['*'], [], $page, $limit, 'id asc');
        $allData = $refundOrderData = [];
        if ($list) {
            /** @var StoreOrderCreateServices $storeOrderCreateServices */
            $storeOrderCreateServices = app()->make(StoreOrderCreateServices::class);
            /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
            $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
            $time = time();
            foreach ($list as $order) {
                //Tạo đơn hoàn tiền
                $refundOrderData['uid'] = $order['uid'];
                $refundOrderData['store_id'] = $order['store_id'];
                $refundOrderData['store_order_id'] = $order['id'];
                $refundOrderData['order_id'] = $storeOrderCreateServices->getNewOrderId('');
                $refundOrderData['refund_num'] = $order['total_num'];
                $refundOrderData['refund_type'] = $order['refund_type'];
                $refundOrderData['refund_price'] = $order['pay_price'];
                $refundOrderData['refunded_price'] = 0;
                $refundOrderData['refund_explain'] = $order['refund_reason_wap_explain'];
                $refundOrderData['refund_img'] = $order['refund_reason_wap_img'];
                $refundOrderData['refund_reason'] = $order['refund_reason_wap'];
                $refundOrderData['refund_express'] = $order['refund_express'];
                $refundOrderData['refunded_time'] = $order['refund_type'] == 6 ? $order['refund_reason_time'] : 0;
                $refundOrderData['add_time'] = $order['refund_reason_time'];
                $cartInfos = $storeOrderCartInfoServices->getCartColunm(['oid' => $order['id']], 'id,cart_id,cart_num,cart_info');
                foreach ($cartInfos as &$cartInfo) {
                    $cartInfo['cart_info'] = is_string($cartInfo['cart_info']) ? json_decode($cartInfo['cart_info'], true) : $cartInfo['cart_info'];
                }
                $refundOrderData['cart_info'] = json_encode(array_column($cartInfos, 'cart_info'));
                $allData[] = $refundOrderData;
            }
            if ($allData) {
                /** @var StoreOrderRefundServices $storeOrderRefundServices */
                $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
                $storeOrderRefundServices->saveAll($allData);
            }
            $info['table'] = 'store_order_refund';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu hoàn tiền thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = $page + 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        } else {
            $this->setIsUpgrade('orderRefund', 1);
            $info['table'] = 'store_order_refund';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu hoàn tiền thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = 1;
            $info['add_time'] = date('Y-m-d H:i:s', time());
            return $info;
        }
    }

    /**
     * Cập nhật bảng sản phẩm trong đơn hàng
     * @param int $sleep
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function handleCartInfo(int $sleep = 1, int $page = 1, int $limit = 100)
    {
        /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
        $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $list = $storeOrderCartInfoServices->selectList(['uid' => 0], 'id,oid', $page, $limit)->toArray();
        $allData = $cartData = [];
        if ($list) {
            /** @var StoreOrderServices $storeOrderServices */
            $storeOrderServices = app()->make(StoreOrderServices::class);
            $uids = $storeOrderServices->getColumn([['id', 'in', array_column($list, 'oid')]], 'uid', 'id');
            foreach ($list as $cart) {
                $cartData['id'] = $cart['id'];
                $cartData['uid'] = $uids[$cart['oid']] ?? 0;
                $allData[] = $cartData;
            }
            if ($allData) {
                $storeOrderCartInfoServices->saveAll($allData);
            }
            $info['table'] = 'store_order_cart_info';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu sản phẩm trong đơn hàng thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = $page + 1;
        } else {
            $this->setIsUpgrade('cartInfo', 1);
            $info['table'] = 'store_order_cart_info';
            $info['status'] = 1;
            $info['error'] = 'Cập nhật dữ liệu sản phẩm trong đơn hàng thành công';
            $info['sleep'] = $sleep + 1;
            $info['page'] = 1;
        }
        $info['add_time'] = date('Y-m-d H:i:s', time());
        return $info;
    }


    /**
     * Nâng cấp dữ liệu
     * @return mixed
     */
    public function upData()
    {
        $data['new_version'] = 'CRMEB-BZ v5.6.3';
        $data['new_code'] = 563;
        $data['update_sql'] = [
            [
                'code' => 560,
                'type' => 3,
                'table' => "diy",
                'field' => "my_menus_status",
                'findSql' => "show columns from `@table` like 'my_menus_status'",
                'sql' => "ALTER TABLE `@table` ADD `my_menus_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Kiểu hiển thị “Dịch vụ của tôi” tại trang cá nhân' AFTER `my_banner_status`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "diy",
                'field' => "business_status",
                'findSql' => "show columns from `@table` like 'business_status'",
                'sql' => "ALTER TABLE `@table` ADD `business_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Kiểu hiển thị “Quản lý cửa hàng” tại trang cá nhân' AFTER `my_menus_status`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "diy",
                'field' => "is_pro",
                'findSql' => "show columns from `@table` like 'is_pro'",
                'sql' => "ALTER TABLE `@table` ADD `is_pro` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Là phiên bản mới' AFTER `title`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery",
                'field' => "is_use",
                'findSql' => "show columns from `@table` like 'is_use'",
                'sql' => "ALTER TABLE `@table` ADD `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã sử dụng' AFTER `status`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "wechat_order_id",
                'findSql' => "show columns from `@table` like 'wechat_order_id'",
                'sql' => "ALTER TABLE `@table` ADD `wechat_order_id` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'ID đơn hàng' AFTER `id`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "num",
                'findSql' => "show columns from `@table` like 'num'",
                'sql' => "ALTER TABLE `@table` ADD `num` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Điểm thưởng, số dư, số ngày svip, lì xì' AFTER `type`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "channel_type",
                'findSql' => "show columns from `@table` like 'channel_type'",
                'sql' => "ALTER TABLE `@table` ADD `channel_type` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'Nguồn quay thưởng' AFTER `add_time`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "out_bill_no",
                'findSql' => "show columns from `@table` like 'out_bill_no'",
                'sql' => "ALTER TABLE `@table` ADD `out_bill_no` varchar(255) NOT NULL DEFAULT '' COMMENT 'Mã đơn phía merchant' AFTER `channel_type`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "transfer_bill_no",
                'findSql' => "show columns from `@table` like 'transfer_bill_no'",
                'sql' => "ALTER TABLE `@table` ADD `transfer_bill_no` varchar(255) NOT NULL DEFAULT '' COMMENT 'Mã chuyển khoản WeChat' AFTER `out_bill_no`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "state",
                'findSql' => "show columns from `@table` like 'state'",
                'sql' => "ALTER TABLE `@table` ADD `state` varchar(32) NOT NULL DEFAULT '' COMMENT 'Trạng thái chứng từ' AFTER `transfer_bill_no`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "package_info",
                'findSql' => "show columns from `@table` like 'package_info'",
                'sql' => "ALTER TABLE `@table` ADD `package_info` varchar(2000) NOT NULL DEFAULT '' COMMENT 'Thông tin package để chuyển đến trang nhận tiền' AFTER `state`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_lottery_record",
                'field' => "fail_reason",
                'findSql' => "show columns from `@table` like 'fail_reason'",
                'sql' => "ALTER TABLE `@table` ADD `fail_reason` varchar(255) NOT NULL DEFAULT '' COMMENT 'Lý do thất bại' AFTER `package_info`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "luck_prize",
                'field' => "percent",
                'findSql' => "show columns from `@table` like 'percent'",
                'sql' => "ALTER TABLE `@table` ADD `percent` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tỷ lệ trúng thưởng' AFTER `chance`"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "luck_prize",
                'sql' => "ALTER TABLE `@table` CHANGE `num` `num` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Điểm thưởng, điểm kinh nghiệm, số ngày thành viên';"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "page_categroy",
                'sql' => "truncate table `@table`"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "page_categroy",
                'sql' => <<<SQL
INSERT INTO `@table` (`id`, `pid`, `type`, `name`, `sort`, `status`, `add_time`) VALUES
(1, 0, 'link', 'Trang cửa hàng', 100, 1, 1626831994),
(2, 0, 'diy', 'Trang DIY', 95, 1, 1626831994),
(3, 0, 'product', 'Trang sản phẩm', 90, 1, 1626831994),
(4, 0, 'article', 'Trang bài viết', 85, 1, 1626831994),
(5, 0, 'lottery_list', 'Trang quay thưởng', 80, 1, 1626831994),
(6, 0, 'custom', 'Tùy chỉnh', 75, 1, 1626831994),
(7, 1, 'link', 'Liên kết cửa hàng', 100, 1, 1626831994),
(8, 1, 'link', 'Liên kết marketing', 95, 1, 1626831994),
(9, 2, 'special', 'Trang DIY', 100, 1, 1626831994),
(10, 3, 'product_category', 'Danh mục sản phẩm', 100, 1, 1626831994),
(11, 3, 'product', 'Sản phẩm', 95, 1, 1626831994),
(12, 3, 'seckill', 'Sản phẩm flash sale', 90, 1, 1626831994),
(13, 3, 'bargain', 'Sản phẩm săn giảm giá', 85, 1, 1626831994),
(14, 3, 'combination', 'Sản phẩm mua chung', 80, 1, 1626831994),
(15, 3, 'integral', 'Sản phẩm đổi điểm', 75, 1, 1626831994),
(16, 4, 'news', 'Bài viết', 100, 1, 1626831994),
(17, 5, 'lottery_list', 'Quay thưởng bằng điểm', 100, 1, 1626831994),
(18, 6, 'custom', 'Liên kết tùy chỉnh', 100, 1, 1626831994),
(19, 7, 'link', 'Liên kết cơ bản', 100, 1, 1626831994),
(20, 7, 'link', 'Liên kết trang cá nhân', 95, 1, 1626831994),
(21, 8, 'link', 'Liên kết flash sale', 100, 1, 1626831994),
(22, 8, 'link', 'Liên kết săn giảm giá', 95, 1, 1626831994),
(23, 8, 'link', 'Liên kết mua chung', 90, 1, 1626831994),
(24, 8, 'link', 'Liên kết điểm thưởng', 85, 1, 1626831994),
(25, 8, 'link', 'Liên kết quay thưởng', 80, 1, 1626831994),
(26, 8, 'link', 'Liên kết phiếu giảm giá', 75, 1, 1626831994);
SQL
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "page_link",
                'sql' => "truncate table `@table`"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "page_link",
                'sql' => <<<SQL
INSERT INTO `@table` (`id`, `cate_id`, `type`, `name`, `url`, `param`, `example`, `status`, `sort`, `add_time`) VALUES
(1, 19, 1, 'Trang chủ cửa hàng', '/pages/index/index', '', '', 1, 0, 1735111883),
(2, 19, 1, 'Danh mục sản phẩm', '/pages/goods_cate/goods_cate', '', '', 1, 0, 1735112646),
(3, 19, 1, 'Giỏ hàng', '/pages/order_addcart/order_addcart', '', '', 1, 0, 1735112666),
(4, 19, 1, 'Trang cá nhân', '/pages/user/index', '', '', 1, 0, 1735112677),
(5, 19, 1, 'Danh sách sản phẩm', '/pages/goods/goods_list/index', '', '', 1, 0, 1735112726),
(6, 19, 1, 'Đơn hàng của tôi', '/pages/goods/order_list/index', '', '', 1, 0, 1735112748),
(7, 19, 1, 'Danh sách bài viết', '/pages/extension/news_list/index', '', '', 1, 0, 1735112767),
(8, 19, 1, 'Đơn hoàn tiền', '/pages/users/user_return_list/index', '', '', 1, 0, 1735112790),
(9, 20, 1, 'Thông tin người dùng', '/pages/users/user_info/index', '', '', 1, 0, 1735113292),
(10, 20, 1, 'Tài khoản của tôi', '/pages/users/user_money/index', '', '', 1, 0, 1735113357),
(11, 20, 1, 'Phiếu giảm giá của tôi', '/pages/users/user_coupon/index', '', '', 1, 0, 1735113368),
(12, 20, 1, 'Điểm thưởng của tôi', '/pages/users/user_integral/index', '', '', 1, 0, 1735113378),
(13, 20, 1, 'Thành viên trả phí', '/pages/annex/vip_paid/index', '', '', 1, 0, 1735113402),
(14, 20, 1, 'Trung tâm đơn hàng', '/pages/goods/order_list/index', '', '', 1, 0, 1735113414),
(15, 20, 1, 'Địa chỉ của tôi', '/pages/users/user_address_list/index', '', '', 1, 0, 1735113429),
(16, 20, 1, 'Lịch sử săn giảm giá', '/pages/activity/bargain/index', '', '', 1, 0, 1735113439),
(17, 20, 1, 'Lịch sử xem', '/pages/users/visit_list/index', '', '', 1, 0, 1735113451),
(18, 20, 1, 'Quản lý đơn hàng', '/pages/admin/order/index', '', '', 1, 0, 1735113467),
(19, 20, 1, 'Tin nhắn của tôi', '/pages/users/message_center/index', '', '', 1, 0, 1735113479),
(20, 20, 1, 'Giới thiệu của tôi', '/pages/users/user_spread_user/index', '', '', 1, 0, 1735113521),
(21, 20, 1, 'Hạng thành viên của tôi', '/pages/users/user_vip/index', '', '', 1, 0, 1735113554),
(22, 21, 1, 'Danh sách flash sale', '/pages/activity/goods_seckill/index', '', '', 1, 0, 1735114620),
(23, 22, 1, 'Danh sách săn giảm giá', '/pages/activity/goods_bargain/index', '', '', 1, 0, 1735114633),
(24, 23, 1, 'Danh sách mua chung', '/pages/activity/goods_combination/index', '', '', 1, 0, 1735114646),
(25, 24, 1, 'Cửa hàng đổi điểm', '/pages/points_mall/index', '', '', 1, 0, 1735114739),
(26, 24, 1, 'Sản phẩm đổi điểm', '/pages/points_mall/integral_goods_list', '', '', 1, 0, 1735114753),
(27, 24, 1, 'Lịch sử đổi điểm', '/pages/points_mall/exchange_record', '', '', 1, 0, 1735114771),
(28, 25, 1, 'Lịch sử trúng thưởng', '/pages/goods/lottery/grids/record', '', '', 1, 0, 1735114807),
(29, 26, 1, 'Danh sách phiếu giảm giá', '/pages/users/user_get_coupon/index', '', '', 1, 0, 1735114823);
SQL
            ],[
                'code' => 560,
                'type' => 1,
                'table' => "store_activity",
                'findSql' => "select * from information_schema.tables where table_name ='@table'",
                'sql' => "CREATE TABLE IF NOT EXISTS `@table` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: Flash sale',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Tên chương trình',
  `start_day` int(10) NOT NULL DEFAULT '0' COMMENT 'Ngày bắt đầu',
  `end_day` int(10) NOT NULL DEFAULT '0' COMMENT 'Ngày kết thúc',
  `time_ids` varchar(255) NOT NULL DEFAULT '' COMMENT 'Nhiều ID khung giờ',
  `once_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Số lượng mỗi người được mua mỗi ngày trong thời gian diễn ra chương trình, 0 là không giới hạn',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Giới hạn tổng số lượng mỗi người dùng được mua trong toàn bộ thời gian chương trình, 0 là không giới hạn',
  `is_commission` int(11) NOT NULL DEFAULT '0' COMMENT 'Tham gia chia hoa hồng',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Hiển thị',
  `link_id` int(4) unsigned NOT NULL DEFAULT '0' COMMENT 'ID liên kết',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã xóa',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT 'Thời gian thêm',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `start_day` (`start_day`,`end_day`) USING BTREE,
  KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng chương trình'"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_order",
                'field' => "is_gift",
                'findSql' => "show columns from `@table` like 'is_gift'",
                'sql' => "ALTER TABLE `@table` ADD `is_gift` int(1) NOT NULL DEFAULT '0' COMMENT 'Là đơn quà tặng' AFTER `division_brokerage`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_order",
                'field' => "gift_price",
                'findSql' => "show columns from `@table` like 'gift_price'",
                'sql' => "ALTER TABLE `@table` ADD `gift_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Phụ phí quà tặng' AFTER `is_gift`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_order",
                'field' => "gift_uid",
                'findSql' => "show columns from `@table` like 'gift_uid'",
                'sql' => "ALTER TABLE `@table` ADD `gift_uid` int(11) NOT NULL DEFAULT '0' COMMENT 'Người dùng nhận quà (uid)' AFTER `gift_price`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_order",
                'field' => "gift_mark",
                'findSql' => "show columns from `@table` like 'gift_mark'",
                'sql' => "ALTER TABLE `@table` ADD `gift_mark` varchar(255) NOT NULL DEFAULT '' COMMENT 'Lời nhắn quà tặng' AFTER `gift_uid`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_product",
                'field' => "params_list",
                'findSql' => "show columns from `@table` like 'params_list'",
                'sql' => "ALTER TABLE `@table` ADD `params_list` varchar(2000) NOT NULL DEFAULT '' COMMENT 'Thông số sản phẩm' AFTER `default_sku`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_product",
                'field' => "label_list",
                'findSql' => "show columns from `@table` like 'label_list'",
                'sql' => "ALTER TABLE `@table` ADD `label_list` varchar(255) NOT NULL DEFAULT '' COMMENT 'Nhãn sản phẩm' AFTER `params_list`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_product",
                'field' => "protection_list",
                'findSql' => "show columns from `@table` like 'protection_list'",
                'sql' => "ALTER TABLE `@table` ADD `protection_list` varchar(255) NOT NULL DEFAULT '' COMMENT 'Đảm bảo sản phẩm' AFTER `label_list`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_product",
                'field' => "is_gift",
                'findSql' => "show columns from `@table` like 'is_gift'",
                'sql' => "ALTER TABLE `@table` ADD `is_gift` int(1) NOT NULL DEFAULT '0' COMMENT 'Là quà tặng' AFTER `protection_list`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_product",
                'field' => "gift_price",
                'findSql' => "show columns from `@table` like 'gift_price'",
                'sql' => "ALTER TABLE `@table` ADD `gift_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Phụ phí quà tặng' AFTER `is_gift`"
            ],[
                'code' => 560,
                'type' => 1,
                'table' => "store_product_label",
                'findSql' => "select * from information_schema.tables where table_name ='@table'",
                'sql' => "CREATE TABLE IF NOT EXISTS `@table` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã số',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Tên nhãn',
  `cate_id` int(11) NOT NULL DEFAULT '0' COMMENT 'ID danh mục',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT 'Cài đặt hiệu ứng: 0 tùy chỉnh, 1 hình ảnh',
  `font_color` varchar(255) NOT NULL DEFAULT '' COMMENT 'Màu chữ',
  `bg_color` varchar(255) NOT NULL DEFAULT '' COMMENT 'Màu nền',
  `border_color` varchar(255) NOT NULL DEFAULT '' COMMENT 'Màu viền',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Hình ảnh',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT 'Hiển thị trên di động',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT 'Bật',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian thêm',
  `is_del` int(11) NOT NULL DEFAULT '0' COMMENT 'Đã xóa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nhãn sản phẩm'"
            ],[
                'code' => 560,
                'type' => 1,
                'table' => "store_product_label_cate",
                'findSql' => "select * from information_schema.tables where table_name ='@table'",
                'sql' => "CREATE TABLE IF NOT EXISTS `@table` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã số',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Tên',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian thêm',
  `is_del` int(11) NOT NULL DEFAULT '0' COMMENT 'Đã xóa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Danh mục nhãn sản phẩm'"
            ],[
                'code' => 560,
                'type' => 1,
                'table' => "store_product_param",
                'findSql' => "select * from information_schema.tables where table_name ='@table'",
                'sql' => "CREATE TABLE IF NOT EXISTS `@table` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã số',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Tên thông số',
  `value` text COMMENT 'Nội dung thông số',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian thêm',
  `is_del` int(1) NOT NULL DEFAULT '0' COMMENT 'Đã xóa',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT 'Trạng thái thông số',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bảng thông số sản phẩm'"
            ],[
                'code' => 560,
                'type' => 1,
                'table' => "store_product_protection",
                'findSql' => "select * from information_schema.tables where table_name ='@table'",
                'sql' => "CREATE TABLE IF NOT EXISTS `@table` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã số',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
  `content` varchar(2000) NOT NULL DEFAULT '' COMMENT 'Nội dung',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Biểu tượng',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT 'Số lượng sử dụng',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian thêm',
  `is_del` int(1) NOT NULL DEFAULT '0' COMMENT 'Đã xóa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bảng đảm bảo sản phẩm'"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "store_seckill",
                'field' => "activity_id",
                'findSql' => "show columns from `@table` like 'activity_id'",
                'sql' => "ALTER TABLE `@table` ADD `activity_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID chương trình' AFTER `id`"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "store_seckill",
                'sql' => "ALTER TABLE `@table` CHANGE `time_id` `time_id` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'ID khung giờ'"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "system_attachment",
                'field' => "type",
                'findSql' => "show columns from `@table` like 'type'",
                'sql' => "ALTER TABLE `@table` ADD `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Loại, 0 hình ảnh, 1 video' AFTER `scan_token`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "system_attachment_category",
                'field' => "type",
                'findSql' => "show columns from `@table` like 'type'",
                'sql' => "ALTER TABLE `@table` ADD `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Loại, 0 hình ảnh, 1 video' AFTER `enname`"
            ],[
                'code' => 560,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'v3_pay_public_key'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'v3_pay_public_key', 'text', 'input', @tabId, '', 1, '', 100, 0, '\"\"', 'Khóa công khai thanh toán v3', 'Khóa công khai thanh toán v3, vui lòng điền nếu phiên bản mới dùng khóa công khai', 0, 1, 0, 0, 0)"
            ],[
                'code' => 560,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'v3_pay_public_pem'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'v3_pay_public_pem', 'upload', 'input', @tabId, '', 3, '', 0, 0, '\"\"', 'Chứng chỉ khóa công khai thanh toán v3', 'Chứng chỉ khóa công khai thanh toán v3, tải lên chứng chỉ này khi dùng khóa công khai thanh toán phiên bản mới', 0, 1, 0, 0, 0)"
            ],[
                'code' => 560,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'v3_transfer_scene_id'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'v3_transfer_scene_id', 'text', 'input', @tabId, '', '1', '', '0', '0', '\"1000\"', 'Mã kịch bản rút tiền tự động WeChat', 'Mã kịch bản rút tiền tự động WeChat', 0, 1, 0, 0, 0)"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "system_menus",
                'sql' => "truncate table `@table`"
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "system_menus",
                'sql' => <<<SQL
INSERT INTO `@table` (`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `api_url`, `methods`, `params`, `sort`, `is_show`, `is_show_path`, `access`, `menu_path`, `path`, `auth_type`, `header`, `is_header`, `unique_auth`, `is_del`, `mark`) VALUES
(1, 0, 's-shop', 'Sản phẩm', 'admin', 'product', 'index', '', '', '[]', 115, 1, 1, 1, '/product', '', 1, '0', 1, 'admin-product', 0, 'Sản phẩm'),
(2, 1, '', 'Quản lý sản phẩm', 'admin', 'product.product', 'index', '', '', '[]', 1, 1, 1, 1, '/product/product_list', '', 1, '', 0, 'admin-store-storeProuduct-index', 0, 'Quản lý sản phẩm'),
(3, 1, '', 'Danh mục sản phẩm', 'admin', 'product.storeCategory', 'index', '', '', '[]', 1, 1, 1, 1, '/product/product_classify', '', 1, 'product', 0, 'admin-store-storeCategory-index', 0, 'Danh mục sản phẩm'),
(4, 0, 's-order', 'Đơn hàng', 'admin', 'order', 'index', '', '', '[]', 120, 1, 1, 1, '/order', '', 1, 'home', 1, 'admin-order', 0, 'Đơn hàng'),
(5, 4, '', 'Quản lý đơn hàng', 'admin', 'order.store_order', 'index', '', '', '[]', 10, 1, 1, 1, '/order/list', '4', 1, 'order', 0, 'admin-order-storeOrder-index', 0, 'Quản lý đơn hàng'),
(6, 1, '', 'Đánh giá sản phẩm', 'admin', 'store.store_product_reply', 'index', '', '', '[]', 0, 1, 1, 1, '/product/product_reply', '', 1, 'product', 0, 'product-product-reply', 0, 'Đánh giá sản phẩm'),
(7, 0, 's-home', 'Trang chủ', 'admin', 'index', '', '', '', '[]', 127, 1, 1, 1, '/index', '', 1, 'home', 1, 'admin-home', 0, 'Trang chủ'),
(9, 0, 'user-solid', 'Người dùng', 'admin', 'user.user', '', '', '', '[]', 125, 1, 1, 1, '/user', '', 1, 'user', 1, 'admin-user', 0, 'Người dùng'),
(10, 9, '', 'Quản lý người dùng', 'admin', 'user.user', 'index', '', '', '[]', 10, 1, 1, 1, '/user/list', '', 1, 'user', 0, 'admin-user-user-index', 0, 'Quản lý người dùng'),
(11, 9, '', 'Hạng người dùng', 'admin', 'user.user_level', 'index', '', '', '[]', 7, 1, 1, 1, '/user/level', '', 1, 'user', 0, 'user-user-level', 0, 'Hạng người dùng'),
(12, 0, 's-tools', 'Cài đặt', 'admin', 'setting.system_config', 'index', '', '', '[]', 1, 1, 1, 1, '/setting', '', 1, 'setting', 1, 'admin-setting', 0, 'Cài đặt'),
(14, 12, '', 'Quản lý quyền', 'admin', 'setting.system_admin', '', '', '', '[]', 0, 1, 1, 1, '/setting/auth/list', '', 1, 'setting', 0, 'setting-system-admin', 0, 'Quản lý quyền'),
(19, 14, '', 'Quản lý vai trò', 'admin', 'setting.system_role', 'index', '', '', '[]', 1, 1, 1, 1, '/setting/system_role/index', '', 1, 'setting', 0, 'setting-system-role', 0, 'Quản lý vai trò'),
(20, 14, '', 'Danh sách quản trị viên', 'admin', 'setting.system_admin', 'index', '', '', '[]', 1, 1, 1, 1, '/setting/system_admin/index', '', 1, 'setting', 0, 'setting-system-list', 0, 'Danh sách quản trị viên'),
(21, 14, '', 'Cài đặt quyền', 'admin', 'setting.system_menus', 'index', '', '', '[]', 1, 1, 1, 1, '/setting/system_menus/index', '12/14', 1, 'setting', 1, 'setting-system-menus', 0, 'Quản lý menu'),
(23, 12, '', 'Cài đặt hệ thống', 'admin', 'setting.system_config', 'index', '', '', '[]', 10, 1, 1, 1, '/setting/system_config/2/129', '12', 1, 'setting', 1, 'setting-system-config', 0, 'Cài đặt hệ thống'),
(25, 0, 'cpu', 'Bảo trì', 'admin', 'system', '', '', '', '[]', 0, 1, 1, 1, '/system', '', 1, 'setting', 1, 'admin-system', 0, 'Bảo trì'),
(26, 0, 's-promotion', 'Tiếp thị liên kết', 'admin', 'agent', '', '', '', '[]', 104, 1, 1, 1, '/agent', '', 1, 'user', 1, 'admin-agent', 0, 'Tiếp thị liên kết'),
(27, 0, 's-marketing', 'Marketing', 'admin', 'marketing', '', '', '', '[]', 110, 1, 1, 1, '/marketing', '', 1, 'home', 1, 'admin-marketing', 0, 'Marketing'),
(28, 26, '', 'Cài đặt tiếp thị liên kết', 'admin', 'setting.system_config', '', '', '', '[]', 1, 1, 1, 1, '/setting/system_config_retail/2/9', '', 1, 'setting', 0, 'setting-system-config', 0, 'Cài đặt tiếp thị liên kết'),
(29, 26, '', 'Quản lý cộng tác viên', 'admin', 'agent.agent_manage', 'index', '', '', '[]', 99, 1, 1, 1, '/agent/agent_manage/index', '', 1, 'user', 0, 'agent-agent-manage', 0, 'Quản lý cộng tác viên'),
(30, 27, '', 'Phiếu giảm giá', 'admin', 'marketing.store_coupon', '', '', '', '[]', 100, 1, 1, 1, '/marketing/store_coupon', '27', 1, 'marketing', 0, 'marketing-store_coupon-index', 0, 'Phiếu giảm giá'),
(31, 27, '', 'Quản lý săn giảm giá', 'admin', 'marketing.store_bargain', '', '', '', '[]', 85, 1, 1, 1, '/marketing/store_bargain', '27', 1, 'marketing', 0, 'marketing-store_bargain-index', 0, 'Quản lý săn giảm giá'),
(32, 27, '', 'Quản lý mua chung', 'admin', 'marketing.store_combination', '', '', '', '[]', 80, 1, 1, 1, '/marketing/store_combination', '27', 1, 'marketing', 0, 'marketing-store_combination-index', 0, 'Quản lý mua chung'),
(33, 27, '', 'Quản lý flash sale', 'admin', 'marketing.store_seckill', '', '', '', '[]', 75, 1, 1, 1, '/marketing/store_seckill', '27', 1, 'marketing', 0, 'marketing-store_seckill-index', 0, 'Quản lý flash sale'),
(34, 27, '', 'Quản lý điểm thưởng', 'admin', 'marketing.user_point', '', '', '', '[]', 95, 1, 1, 1, '/marketing/user_point', '27', 1, 'marketing', 0, 'marketing-user_point-index', 0, 'Quản lý điểm thưởng'),
(35, 0, 's-finance', 'Tài chính', 'admin', 'finance', '', '', '', '[]', 90, 1, 1, 1, '/finance', '', 1, 'home', 1, 'admin-finance', 0, 'Tài chính'),
(36, 35, '', 'Thao tác tài chính', 'admin', 'finance', '', '', '', '[]', 1, 1, 1, 1, '/finance/user_extract', '', 1, 'finance', 0, 'finance-user_extract-index', 0, 'Thao tác tài chính'),
(37, 35, '', 'Lịch sử tài chính', 'admin', 'finance', '', '', '', '[]', 1, 1, 1, 1, '/finance/user_recharge', '', 1, 'finance', 0, 'finance-user-recharge-index', 0, 'Lịch sử tài chính'),
(38, 35, '', 'Lịch sử hoa hồng', 'admin', 'finance', '', '', '', '[]', 1, 1, 1, 1, '/finance/finance', '', 1, 'finance', 0, 'finance-finance-index', 0, 'Lịch sử hoa hồng'),
(39, 36, '', 'Yêu cầu rút tiền', 'admin', 'finance.user_extract', '', '', '', '[]', 1, 1, 1, 1, '/finance/user_extract/index', '', 1, 'finance', 0, 'finance-user_extract', 0, 'Yêu cầu rút tiền'),
(40, 37, '', 'Lịch sử nạp tiền', 'admin', 'finance.user_recharge', '', '', '', '[]', 1, 1, 1, 1, '/finance/user_recharge/index', '', 1, 'finance', 0, 'finance-user-recharge', 0, 'Lịch sử nạp tiền'),
(42, 38, '', 'Lịch sử hoa hồng', 'admin', 'finance.finance', '', '', '', '[]', 1, 1, 1, 1, '/finance/finance/commission', '', 1, 'finance', 0, 'finance-finance-commission', 0, 'Lịch sử hoa hồng'),
(43, 0, 's-management', 'Nội dung', 'admin', 'cms', '', '', '', '[]', 85, 1, 1, 1, '/cms', '', 1, 'home', 1, 'admin-cms', 0, 'Nội dung'),
(44, 43, '', 'Quản lý bài viết', 'admin', 'cms.article', 'index', '', '', '[]', 1, 1, 1, 1, '/cms/article/index', '', 1, 'cms', 0, 'cms-article-index', 0, 'Quản lý bài viết'),
(45, 43, '', 'Danh mục bài viết', 'admin', 'cms.article_category', 'index', '', '', '[]', 1, 1, 1, 1, '/cms/article_category/index', '', 1, 'cms', 0, 'cms-article-category', 0, 'Danh mục bài viết'),
(47, 65, '', 'Nhật ký hệ thống', 'admin', 'system.system_log', 'index', '', '', '[]', 0, 1, 1, 1, '/system/maintain/system_log/index', '', 1, 'system', 0, 'system-maintain-system-log', 0, 'Nhật ký hệ thống'),
(56, 25, '', 'Cấu hình phát triển', 'admin', 'system', '', '', '', '[]', 10, 1, 1, 1, '/system/config', '', 1, 'system', 0, 'system-config-index', 0, 'Cấu hình phát triển'),
(57, 65, '', 'Làm mới bộ nhớ đệm', 'admin', 'system', 'clear', '', '', '[]', 1, 1, 1, 1, '/system/maintain/clear/index', '', 1, 'system', 0, 'system-clear', 0, 'Làm mới bộ nhớ đệm'),
(65, 25, '', 'Bảo trì bảo mật', 'admin', 'system', '', '', '', '[]', 7, 1, 1, 1, '/system/maintain', '', 1, 'system', 0, 'system-maintain-index', 0, 'Bảo trì bảo mật'),
(66, 1073, '', 'Xóa dữ liệu', 'admin', 'system.system_cleardata', 'index', '', '', '[]', 0, 1, 1, 1, '/system/maintain/system_cleardata/index', '25/1073', 1, 'system', 0, 'system-maintain-system-cleardata', 0, 'Xóa dữ liệu'),
(67, 1695, '', 'Quản lý cơ sở dữ liệu', 'admin', 'system.system_databackup', 'index', '', '', '[]', 0, 1, 1, 1, '/system/maintain/system_databackup/index', '25/1695', 1, 'system', 1, 'system-maintain-system-databackup', 0, 'Quản lý cơ sở dữ liệu'),
(69, 135, '', 'OA WeChat', 'admin', 'wechat', '', '', '', '[]', 4, 1, 1, 1, '/app/wechat', '135', 1, 'app', 0, 'admin-wechat', 0, 'OA WeChat'),
(71, 30, '', 'Danh sách phiếu giảm giá', 'admin', 'marketing.store_coupon_issue', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/store_coupon_issue/index', '', 1, 'marketing', 0, 'marketing-store_coupon_issue', 0, 'Danh sách phiếu giảm giá'),
(72, 30, '', 'Lịch sử nhận của người dùng', 'admin', 'marketing.store_coupon_user', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/store_coupon_user/index', '', 1, 'marketing', 0, 'marketing-store_coupon_user', 0, 'Lịch sử nhận của người dùng'),
(74, 31, '', 'Sản phẩm săn giảm giá', 'admin', 'marketing.store_bargain', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/store_bargain/index', '', 1, 'marketing', 0, 'marketing-store_bargain', 0, 'Sản phẩm săn giảm giá'),
(75, 32, '', 'Sản phẩm mua chung', 'admin', 'marketing.store_combination', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/store_combination/index', '', 1, 'marketing', 0, 'marketing-store_combination', 0, 'Sản phẩm mua chung'),
(76, 32, '', 'Danh sách mua chung', 'admin', 'marketing.store_combination', 'combina_list', '', '', '[]', 0, 1, 1, 1, '/marketing/store_combination/combina_list', '', 1, 'marketing', 0, 'marketing-store_combination-combina_list', 0, 'Danh sách mua chung'),
(77, 33, '', 'Sản phẩm flash sale', 'admin', 'marketing.store_seckill', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/store_seckill/index', '', 1, 'marketing', 0, 'marketing-store_seckill', 0, 'Sản phẩm flash sale'),
(78, 33, '', 'Cấu hình flash sale', 'admin', 'marketing.store_seckill', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/store_seckill_data/index/49', '', 1, 'marketing', 0, 'marketing-store_seckill-data', 0, 'Cấu hình flash sale'),
(79, 34, '', 'Cấu hình điểm thưởng', 'admin', 'setting.system_config/index.html', 'index', '', '', '[]', 0, 1, 1, 1, '/marketing/integral/system_config/2/11', '27/34', 1, 'marketing', 1, 'marketing-integral-system_config', 0, 'Cấu hình điểm thưởng'),
(92, 69, '', 'Menu WeChat', 'admin', 'application.wechat_menus', 'index', '', '', '[]', 0, 1, 1, 1, '/app/wechat/setting/menus/index', '', 1, 'app', 0, 'application-wechat-menus', 0, 'Menu WeChat'),
(94, 3417, '', 'Trang Yihaotong', 'admin', 'setting.sms_config', '', '', '', '[]', 8, 1, 1, 1, '/setting/sms/sms_config/index', '12/1056/3417', 1, 'setting', 1, 'setting-sms', 0, 'Yihaotong'),
(99, 1, '', 'Quy cách sản phẩm', 'admin', 'store.store_product', 'index', '', '', '[]', 1, 1, 1, 1, '/product/product_attr', '', 1, 'product', 0, 'product-product-attr', 0, 'Quy cách sản phẩm'),
(109, 69, '', 'Quản lý tin bài', 'admin', 'wechat.wechat_news_category', 'index', '', '', '[]', 0, 1, 1, 1, '/app/wechat/news_category/index', '', 1, 'app', 0, 'wechat-wechat-news-category-index', 0, 'Quản lý tin bài'),
(111, 56, '', 'Danh mục cấu hình', 'admin', 'setting.system_config_tab', 'index', '', '', '[]', 99, 1, 1, 1, '/system/config/system_config_tab/index', '25/56', 1, 'system', 0, 'system-config-system_config-tab', 0, 'Danh mục cấu hình'),
(112, 56, '', 'Dữ liệu tổ hợp', 'admin', 'setting.system_group', 'index', '', '', '[]', 98, 1, 1, 1, '/system/config/system_group/index', '25/56', 1, 'system', 0, 'system-config-system_config-group', 0, 'Dữ liệu tổ hợp'),
(113, 114, '', 'Trả lời khi theo dõi', 'admin', 'wechat.reply', 'index', '', '', '[]', 0, 1, 1, 1, '/app/wechat/reply/follow/subscribe', '135/69/114', 1, 'app', 0, 'wechat-wechat-reply-subscribe', 0, 'Trả lời khi theo dõi'),
(114, 69, '', 'Trả lời tự động', 'admin', 'wechat.reply', '', '', '', '[]', 0, 1, 1, 1, '/app/wechat/reply', '', 1, 'app', 0, 'wechat-wechat-reply-index', 0, 'Trả lời tự động'),
(115, 114, '', 'Trả lời theo từ khóa', 'admin', 'wechat.reply', 'keyword', '', '', '[]', 0, 1, 1, 1, '/app/wechat/reply/keyword', '', 1, 'app', 0, 'wechat-wechat-reply-keyword', 0, 'Trả lời theo từ khóa'),
(116, 114, '', 'Trả lời từ khóa không khớp', 'admin', 'wechat.reply', 'index', '', '', '[]', 0, 1, 1, 1, '/app/wechat/reply/index/default', '135/69/114', 1, 'app', 0, 'wechat-wechat-reply-default', 0, 'Trả lời từ khóa không khớp'),
(128, 656, '', 'Cấu hình dữ liệu', 'admin', 'setting.system_group_data', 'index', '', '', '[]', 2, 1, 1, 1, '/setting/system_visualization_data', '12/656', 1, 'system', 0, 'admin-setting-system_visualization_data', 0, 'Cấu hình dữ liệu'),
(135, 0, 'menu', 'Ứng dụng', 'admin', 'app', 'index', '', '', '[]', 70, 1, 1, 1, '/app', '', 1, 'app', 1, 'admin-app', 0, 'Ứng dụng'),
(144, 303, '', 'Cài đặt điểm nhận hàng', 'admin', 'merchant.system_store', 'index', '', '', '[]', 5, 1, 1, 1, '/setting/merchant/system_store/index', '', 1, '', 0, 'setting-system-config-merchant', 0, 'Cài đặt điểm nhận hàng'),
(145, 1073, '', 'Đơn vị vận chuyển', 'admin', 'freight.express', 'index', '', '', '[]', 4, 1, 1, 1, '/setting/freight/express/index', '25/1073', 1, '', 0, 'setting-freight-express', 0, 'Đơn vị vận chuyển'),
(165, 0, 'message-solid', 'CSKH', 'admin', 'setting.storeService', 'index', '', '', '[]', 104, 1, 1, 1, '/kefu', '', 1, '', 0, 'setting-store-service', 0, 'CSKH'),
(227, 9, '', 'Nhóm người dùng', 'admin', 'user.user_group', 'index', '', '', '[]', 9, 1, 1, 1, '/user/group', '', 1, 'user', 0, 'user-user-group', 0, 'Nhóm người dùng'),
(229, 1073, '', 'Dữ liệu thành phố', 'admin', 'setting.system_city', '', '', '', '[]', 1, 1, 1, 1, '/setting/freight/city/list', '25/1073', 1, 'setting', 0, 'setting-system-city', 0, 'Dữ liệu thành phố'),
(230, 303, '', 'Mẫu phí vận chuyển', 'admin', 'setting.shipping_templates', '', '', '', '[]', 0, 1, 1, 1, '/setting/freight/shipping_templates/list', '', 1, 'setting', 0, 'setting-shipping-templates', 0, 'Mẫu phí vận chuyển'),
(300, 144, '', 'Điểm nhận hàng', 'admin', 'merchant.system_store', 'index', '', '', '[]', 0, 1, 1, 1, '/setting/merchant/system_store/list', '', 1, 'setting', 0, 'setting-merchant-system-store', 0, 'Điểm nhận hàng'),
(301, 144, '', 'Nhân viên xác nhận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/merchant/system_store_staff/index', '', 1, 'setting', 0, 'setting-merchant-system-store-staff', 0, 'Nhân viên xác nhận'),
(302, 4, '', 'Lịch sử xác nhận sử dụng', 'admin', '', 'index', '', '', '[]', 0, 1, 1, 1, '/setting/merchant/system_verify_order/index', '4', 1, 'setting', 1, 'setting-merchant-system-verify-order', 0, 'Đơn hàng xác nhận sử dụng'),
(303, 12, '', 'Cài đặt giao hàng', 'admin', 'setting', 'index', '', '', '[]', 0, 1, 1, 1, '/setting/freight', '12', 1, '', 0, 'admin-setting-freight', 0, 'Cài đặt giao hàng'),
(566, 656, '', 'Quản lý tư liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/file', '12/656', 1, '', 0, 'system-file', 0, 'Quản lý tư liệu'),
(589, 9, '', 'Nhãn người dùng', 'admin', 'user.user_label', 'index', '', '', '[]', 8, 1, 1, 1, '/user/label', '', 1, 'user', 0, 'user-user-label', 0, 'Nhãn người dùng'),
(605, 25, '', 'Thông tin hệ thống', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/maintain/auth', '', 1, '', 0, 'system-maintain-auth', 0, 'Thông tin hệ thống'),
(655, 65, '', 'Nâng cấp trực tuyến', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/onlineUpgrade/index', '25/65', 1, '', 0, 'system-onlineUpgrade-index', 0, 'Nâng cấp trực tuyến'),
(656, 0, 's-open', 'Thiết kế giao diện', 'admin', '', '', '', '', '[]', 80, 1, 1, 1, '/setting/pages', '', 1, '', 0, 'admin-setting-pages', 0, 'Thiết kế giao diện'),
(657, 656, '', 'Thiết kế trang chủ', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/setting/pages/devise/0', '12/656', 1, '', 0, 'admin-setting-pages-devise', 0, 'Thiết kế trang'),
(678, 165, '', 'Danh sách nhân viên CSKH', 'admin', '', '', '', '', '[]', 10, 1, 1, 1, '/setting/store_service/index', '165', 1, '', 0, 'admin-setting-store_service-index', 0, 'Danh sách nhân viên CSKH'),
(679, 165, '', 'Câu trả lời mẫu CSKH', 'admin', '', '', '', '', '[]', 9, 1, 1, 1, '/setting/store_service/speechcraft', '165', 1, '', 0, 'admin-setting-store_service-speechcraft', 0, 'Câu trả lời mẫu CSKH'),
(686, 27, '', 'Quản lý livestream', 'admin', '', '', '', '', '[]', 65, 1, 1, 1, '/marketing/live', '27', 1, '', 0, 'admin-marketing-live', 0, 'Quản lý livestream'),
(687, 686, '', 'Quản lý phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/live/live_room', '', 1, '', 0, 'admin-marketing-live-live_room', 0, 'Quản lý phòng livestream'),
(688, 686, '', 'Quản lý sản phẩm livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/live/live_goods', '', 1, '', 0, 'admin-marketing-live-live_goods', 0, 'Quản lý sản phẩm livestream'),
(689, 686, '', 'Quản lý streamer', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/live/anchor', '', 1, '', 0, 'admin-marketing-live-anchor', 0, 'Quản lý streamer'),
(717, 1, '', 'Thống kê sản phẩm', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/statistic/product', '1', 1, '', 0, 'admin-statistic', 0, 'Thống kê sản phẩm'),
(718, 9, '', 'Thống kê người dùng', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/statistic/user', '9', 1, '', 0, 'admin-statistic', 0, 'Thống kê người dùng'),
(720, 303, '', 'Quản lý nhân viên giao hàng', 'admin', '', '', '', '', '[]', 10, 1, 1, 1, '/setting/delivery_service/index', '', 1, '', 0, 'setting-delivery-service', 0, 'Quản lý nhân viên giao hàng'),
(731, 27, '', 'Thành viên trả phí', 'admin', '', '', '', '', '[]', 70, 1, 1, 1, '/user/grade', '27', 1, '', 0, 'user-user-grade', 0, 'Thành viên trả phí'),
(738, 165, '', 'Lời nhắn của người dùng', 'admin', '', '', '', '', '[]', 8, 1, 1, 1, '/setting/store_service/feedback', '165', 1, '', 0, 'admin-setting-store_service-feedback', 0, 'Lời nhắn của người dùng'),
(751, 731, '', 'Loại thành viên', 'admin', '', '', '', '', '[]', 5, 1, 1, 1, '/user/grade/type', '', 1, '', 0, 'admin-user-member-type', 0, 'Loại thành viên'),
(755, 31, '', 'Danh sách săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/store_bargain/bargain_list', '', 1, '', 0, 'marketing-store_bargain-bargain_list', 0, 'Danh sách săn giảm giá'),
(760, 4, '', 'Đơn thu ngân', 'admin', '', '', '', '', '[]', 8, 1, 1, 1, '/order/offline', '4', 1, '', 0, 'admin-order-offline', 0, 'Đơn thu ngân'),
(762, 731, '', 'Thẻ kích hoạt thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/user/grade/card', '', 1, '', 0, 'admin-user-grade-card', 0, 'Thẻ kích hoạt thành viên'),
(763, 731, '', 'Lịch sử thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/user/grade/record', '', 1, '', 0, 'admin-user-grade-record', 0, 'Lịch sử thành viên'),
(765, 731, '', 'Quyền lợi thành viên', 'admin', '', '', '', '', '[]', 4, 1, 1, 1, '/user/grade/right', '', 1, '', 0, 'admin-user-grade-right', 0, 'Quyền lợi thành viên'),
(766, 35, '', 'Thống kê giao dịch', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/statistic/transaction', '35', 1, '', 0, 'admin-statistic', 0, 'Thống kê giao dịch'),
(767, 36, '', 'Quản lý hóa đơn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/order/invoice/list', '', 1, '', 0, 'admin-order-startOrderInvoice-index', 0, 'Quản lý hóa đơn'),
(896, 26, '', 'Cấp độ CTV', 'admin', '', '', '', '', '[]', 95, 1, 1, 1, '/setting/membership_level/index', '26', 1, '', 0, 'admin-setting-membership_level-index', 0, 'Cấp độ CTV'),
(897, 4, '', 'Đơn đổi trả', 'admin', '', '', '', '', '[]', 9, 1, 1, 1, '/order/refund', '4', 1, '', 0, 'admin-order-refund', 0, 'Đơn đổi trả'),
(898, 12, '', 'Quản lý thông báo', 'admin', '', '', '', '', '[]', 9, 1, 1, 1, '/setting/notification/index', '12', 1, '', 0, 'setting-notification', 0, 'Quản lý thông báo'),
(902, 656, '', 'Chủ đề giao diện', 'admin', '', '', '', '', '[]', 2, 1, 1, 1, '/setting/theme_style', '12/656', 1, '', 0, 'admin-setting-theme_style', 0, 'Chủ đề giao diện'),
(903, 1008, '', 'Thiết kế giao diện PC', 'admin', '', '', '', '', '[]', 2, 1, 1, 1, '/setting/pc_group_data', '12/656', 1, '', 0, 'setting-system-pc_data', 0, 'Cửa hàng PC'),
(905, 34, '', 'Sản phẩm đổi điểm', 'admin', '', '', '', '', '[]', 95, 1, 1, 1, '/marketing/store_integral/index', '27/34', 1, '', 0, 'marketing-store_integral', 0, 'Sản phẩm đổi điểm'),
(909, 27, '', 'Quản lý quay thưởng', 'admin', '', '', '', '', '[]', 90, 1, 1, 1, '/marketing/lottery/index', '27', 1, '', 0, 'marketing-lottery-index', 0, 'Quản lý quay thưởng'),
(912, 34, '', 'Đơn đổi điểm', 'admin', '', '', '', '', '[]', 90, 1, 1, 1, '/marketing/store_integral/order_list', '27/34', 1, '', 0, 'marketing-store_integral-order', 0, 'Đơn đổi điểm'),
(993, 135, '', 'Mini Program', 'admin', '', '', '', '', '[]', 3, 1, 1, 1, '/app/routine', '135', 1, '', 0, 'admin-routine', 0, 'Mini Program'),
(994, 993, '', 'Tải xuống Mini Program', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/app/routine/download', '135/993', 1, '', 0, 'routine-download', 0, 'Tải xuống Mini Program'),
(997, 4, '', 'Thống kê đơn hàng', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/statistic/order', '4', 1, '', 0, 'admin-statistic', 0, 'Thống kê đơn hàng'),
(998, 37, '', 'Dòng tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/finance/capital_flow/index', '35/37', 1, '', 0, 'finance-capital_flow-index', 0, 'Dòng tiền'),
(999, 37, '', 'Lịch sử sao kê', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/finance/billing_records/index', '35/37', 1, '', 0, 'finance-billing_records-index', 0, 'Lịch sử sao kê'),
(1001, 34, '', 'Lịch sử điểm thưởng', 'admin', '', '', '', '', '[]', 85, 1, 1, 1, '/marketing/point_record', '27/34', 1, '', 0, 'marketing-point_record-index', 0, 'Lịch sử điểm thưởng'),
(1002, 34, '', 'Thống kê điểm thưởng', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/marketing/point_statistic', '27/34', 1, '', 0, 'marketing-point_statistic-index', 0, 'Thống kê điểm thưởng'),
(1003, 35, '', 'Lịch sử số dư', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/finance/balance', '35', 1, '', 0, 'finance-balance-index', 0, 'Lịch sử số dư'),
(1004, 1003, '', 'Lịch sử số dư', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/finance/balance/balance', '35/1003', 1, '', 0, 'finance-user-balance', 0, 'Lịch sử số dư'),
(1005, 1003, '', 'Thống kê số dư', 'admin', '', '', '', '', '[]', 100, 1, 1, 1, '/statistic/balance', '35/1003', 1, '', 0, 'admin-statistic', 0, 'Thống kê số dư'),
(1006, 69, '', 'Cấu hình OA WeChat', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/wechat_config/2/2', '135/69', 1, '', 0, 'setting-system-config', 0, 'Cấu hình OA WeChat'),
(1007, 993, '', 'Cấu hình Mini Program', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/routine_config/2/7', '135/993', 1, '', 0, 'setting-system-config', 0, 'Cấu hình Mini Program'),
(1008, 135, '', 'PC', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/app/pc', '135', 1, '', 0, 'admin-pc', 0, 'PC'),
(1009, 135, '', 'APP', 'admin', '', '', '', '', '[]', 2, 1, 1, 1, '/app/app', '135', 1, '', 0, 'admin-app', 0, 'APP'),
(1010, 1008, '', 'Cấu hình PC', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/pc_config/2/75', '135/1008', 1, '', 0, 'setting-system-config', 0, 'Cấu hình PC'),
(1011, 1009, '', 'Cấu hình APP', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/app_config/2/77', '135/1009', 1, '', 0, 'setting-system-config', 0, 'Cấu hình APP'),
(1012, 1056, '', 'Cấu hình lưu trữ hệ thống', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/storage', '12', 1, '', 0, 'setting-storage', 0, 'Cấu hình lưu trữ hệ thống'),
(1013, 26, '', 'Đại lý khu vực', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/agent/division', '26', 1, '', 0, 'agent-division', 0, 'Đại lý khu vực'),
(1014, 1013, '', 'Danh sách đại lý khu vực', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/division/index', '26/1013', 1, '', 0, 'agent-division-index', 0, 'Danh sách đại lý khu vực'),
(1015, 1013, '', 'Danh sách đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/division/agent/index', '26/1013', 1, '', 0, 'agent-division-agent-index', 0, 'Danh sách đại lý'),
(1016, 1013, '', 'Đăng ký đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/division/agent/applyList', '26/1013', 1, '', 0, 'agent-division-agent-applyList', 0, 'Đăng ký đại lý'),
(1023, 27, '', 'Mã kênh', 'admin', '', '', '', '', '[]', 55, 1, 1, 1, '/marketing/channel_code/channelCodeIndex', '27', 1, '', 0, 'marketing-channel_code-index', 0, 'Mã kênh'),
(1053, 3420, '', 'Cài đặt số tiền', 'admin', '', '', '', '', '[]', 60, 1, 1, 1, '/marketing/recharge', '27/3420', 1, '', 0, 'marketing-recharge-index', 0, 'Cấu hình nạp tiền'),
(1055, 1009, '', 'Quản lý phiên bản', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/app/app/version', '135/1009', 1, '', 0, 'admin-app-version', 0, 'Quản lý phiên bản'),
(1056, 12, '', 'Cấu hình API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/other_config', '12', 1, '', 0, 'setting-other', 0, 'Cấu hình API'),
(1058, 1056, '', 'Cấu hình thu thập sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/other_config/copy/2/41', '12/1056', 1, '', 0, 'setting-other-copy', 0, 'Cấu hình thu thập sản phẩm'),
(1059, 1056, '', 'Cấu hình tra cứu vận chuyển', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/other_config/logistics/2/64', '12/1056', 1, '', 0, 'setting-other-logistics', 0, 'Cấu hình tra cứu vận chuyển'),
(1060, 1056, '', 'Cấu hình vận đơn điện tử', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/other_config/electronic/2/66', '12/1056', 1, '', 0, 'setting-other-electronic', 0, 'Cấu hình vận đơn điện tử'),
(1061, 12, '', 'Cài đặt thỏa thuận', 'admin', '', '', '', '', '[]', 9, 1, 1, 1, '/setting/agreement', '12', 1, '', 0, 'setting-agreement', 0, 'Cài đặt thỏa thuận'),
(1062, 1056, '', 'Cấu hình API SMS', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/other_config/sms/2/96', '12/1056', 1, '', 0, 'setting-other-sms', 0, 'Cấu hình API SMS'),
(1063, 1056, '', 'Cấu hình thanh toán cửa hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/other_config/pay/2/23', '12/1056', 1, '', 0, 'setting-other-pay', 0, 'Cấu hình thanh toán cửa hàng'),
(1064, 25, '', 'API bên ngoài', 'admin', '', '', '', '', '[]', 6, 1, 1, 1, '/setting/other_out_config', '25', 1, '', 0, 'setting-other-out', 0, 'API bên ngoài'),
(1066, 1064, '', 'Quản lý tài khoản', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/system_out_account/index', '25/1064', 1, '', 0, 'setting-system-out-account-index', 0, 'Quản lý tài khoản'),
(1067, 25, '', 'Cài đặt ngôn ngữ', 'admin', '', '', '', '', '[]', 5, 1, 1, 1, '/setting/lang', '25', 1, '', 0, 'admin-lang', 0, 'Cài đặt ngôn ngữ'),
(1068, 1067, '', 'Danh sách ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/lang/list', '12/1067', 1, '', 0, 'admin-lang-list', 0, 'Danh sách ngôn ngữ'),
(1069, 1067, '', 'Chi tiết ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/lang/info', '12/1067', 1, '', 0, 'admin-lang-info', 0, 'Chi tiết ngôn ngữ'),
(1070, 1067, '', 'Danh sách khu vực', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/lang/country', '25/1067', 1, '', 0, 'admin-lang-country', 0, 'Danh sách khu vực'),
(1071, 1695, '', 'Quản lý tệp', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/maintain/system_file/opendir', '25/1695', 1, '', 0, 'system-maintain-system-file', 0, 'Quản lý tệp'),
(1073, 25, '', 'Bảo trì dữ liệu', 'admin', '', '', '', '', '[]', 7, 1, 1, 1, 'system/database/index', '25', 1, '', 0, 'system-database-index', 0, 'Bảo trì dữ liệu'),
(1075, 731, '', 'Cấu hình thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/member_config/2/67', '27/731', 1, '', 0, 'setting-member-config', 0, 'Cấu hình thành viên'),
(1076, 56, '', 'Tác vụ định kỳ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/crontab', '25/56', 1, '', 0, 'system-crontab-index', 0, 'Tác vụ định kỳ'),
(1078, 1695, '', 'Quản lý API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/backend_routing', '25/1695', 1, '', 0, 'system-config-backend-routing', 0, 'Quản lý API'),
(1101, 1695, '', 'Tạo mã nguồn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/code_generation_list', '25/1695', 1, '', 0, 'system-config-code-generation-list', 0, 'Tạo mã nguồn'),
(1695, 25, '', 'Công cụ phát triển', 'admin', '', '', '', '', '[]', 1, 1, 1, 1, '/tool', '25', 1, '', 0, 'admin-tool', 0, 'Công cụ phát triển'),
(2450, 69, '', 'Thêm tin bài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/app/wechat/news_category/save', '135/69', 3, '', 0, 'wechat-wechat-news-category-save', 0, 'Thêm tin bài'),
(2451, 43, '', 'Thêm bài viết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/cms/article/add_article', '43', 3, '', 0, 'cms-article-creat', 0, 'Thêm bài viết'),
(2452, 32, '', 'Thêm mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/store_combination/create', '27/32', 3, '', 0, 'marketing-store_combination-create', 0, 'Thêm mua chung'),
(2453, 30, '', 'Thêm phiếu giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/store_coupon_issue/create', '27/30', 3, '', 0, 'marketing-store_coupon_issue-create', 0, 'Thêm phiếu giảm giá'),
(2454, 1, '', 'Thêm sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/product/add_product', '1', 3, '', 0, 'admin-store-storeProuduct-index', 0, 'Thêm sản phẩm'),
(2455, 31, '', 'Thêm săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/store_bargain/create', '27/31', 3, '', 0, 'marketing-store_bargain-create', 0, 'Thêm săn giảm giá'),
(2456, 33, '', 'Thêm flash sale', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/store_seckill/create', '27/33', 3, '', 0, 'marketing-store_seckill-create', 0, 'Thêm flash sale'),
(2457, 34, '', 'Thêm sản phẩm đổi điểm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/store_integral/create', '27/34', 3, '', 0, 'marketing-store_integral-create', 0, 'Thêm sản phẩm đổi điểm'),
(2458, 686, '', 'Thêm phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/live/add_live_room', '27/686', 3, '', 0, 'admin-marketing-live-add_live_room', 0, 'Thêm phòng livestream'),
(2459, 686, '', 'Quản lý sản phẩm livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/live/add_live_goods', '27/686', 3, '', 0, 'admin-marketing-live-add_live_goods', 0, 'Quản lý sản phẩm livestream'),
(2460, 27, '', 'Thêm mã kênh', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/channel_code/create', '27', 3, '', 0, 'marketing-channel_code-create', 0, 'Thêm mã kênh'),
(2461, 656, '', 'Thiết kế giao diện trang', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/pages/diy', '656', 3, '', 0, 'admin-setting-pages-diy', 0, 'Thiết kế giao diện trang'),
(2462, 1695, '', 'Tạo mã nguồn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/code_generation', '25/1695', 3, '', 0, 'system-config-code-generation', 0, 'Tạo mã nguồn'),
(2463, 1695, '', 'Lối vào quản lý tệp', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/maintain/system_file/login', '25/1695', 3, '', 0, 'system-maintain-system-file', 0, 'Lối vào quản lý tệp'),
(2472, 56, '', 'Bảo trì quyền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/system_menus/index', '25/56', 1, '', 0, 'system_menus-index', 0, 'Quy tắc quyền'),
(2475, 10, '', 'Thêm người dùng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-create', 0, 'Thêm người dùng'),
(2476, 2475, '', 'Thông tin khi thêm/sửa thông tin người dùng', '', '', '', 'user/user/user_save_info/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user-user_save_info', 0, 'Thông tin khi thêm/sửa thông tin người dùng'),
(2477, 2475, '', 'Biểu mẫu thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-add', 0, 'Biểu mẫu thêm hoặc sửa nhãn người dùng'),
(2478, 2475, '', 'Thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-save', 0, 'Thêm hoặc sửa nhãn người dùng'),
(2479, 2475, '', 'Lưu người dùng', '', '', '', 'user/user', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user', 0, 'Lưu người dùng'),
(2480, 2475, '', 'Lấy biểu mẫu sửa người dùng', '', '', '', 'user/user/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user-edit', 0, 'Lấy biểu mẫu sửa người dùng'),
(2481, 2475, '', 'Sửa người dùng', '', '', '', 'user/user/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user', 0, 'Sửa người dùng'),
(2482, 2475, '', 'Thêm người dùng', '', '', '', 'user/user/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user-save', 0, 'Thêm người dùng'),
(2483, 10, '', 'Gửi phiếu giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-send-coupon', 0, 'Gửi phiếu giảm giá'),
(2484, 2483, '', 'Danh sách phiếu giảm giá để gửi', '', '', '', 'marketing/coupon/grant', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-grant', 0, 'Danh sách phiếu giảm giá để gửi'),
(2485, 2483, '', 'Gửi phiếu giảm giá', '', '', '', 'marketing/coupon/user/grant', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-user-grant', 0, 'Gửi phiếu giảm giá'),
(2486, 10, '', 'Đặt nhóm hàng loạt', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-batch-set-group', 0, 'Đặt nhóm hàng loạt'),
(2487, 2486, '', 'Biểu mẫu nhóm người dùng', '', '', '', 'user/set_group', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-set_group', 0, 'Biểu mẫu nhóm người dùng'),
(2488, 2486, '', 'Đặt nhóm người dùng', '', '', '', 'user/save_set_group', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-save_set_group', 0, 'Đặt nhóm người dùng'),
(2489, 10, '', 'Đặt nhãn hàng loạt', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-batch-set-label', 0, 'Đặt nhãn hàng loạt'),
(2490, 2489, '', 'Lấy nhãn người dùng', '', '', '', 'user/label/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-label', 0, 'Lấy nhãn người dùng'),
(2491, 2489, '', 'Lưu nhãn người dùng', '', '', '', 'user/save_set_label', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-save_set_label', 0, 'Lưu nhãn người dùng'),
(2492, 10, '', 'Xuất người dùng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-export', 0, 'Xuất người dùng'),
(2493, 2492, '', 'Xuất danh sách người dùng', '', '', '', 'export/user_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-user_list', 0, 'Xuất danh sách người dùng'),
(2494, 10, '', 'Chi tiết người dùng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-info', 0, 'Chi tiết người dùng'),
(2495, 2494, '', 'Lấy chi tiết người dùng', '', '', '', 'user/user/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user-6465801810568', 0, 'Lấy chi tiết người dùng'),
(2496, 2494, '', 'Sửa người dùng', '', '', '', 'user/user/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user-646580181056f', 0, 'Sửa người dùng'),
(2497, 2494, '', 'Thông tin khi thêm/sửa thông tin người dùng', '', '', '', 'user/user/user_save_info/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user-user_save_info-6465801810573', 0, 'Thông tin khi thêm/sửa thông tin người dùng'),
(2498, 2494, '', 'Lấy thông tin người dùng chỉ định', '', '', '', 'user/one_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-one_info', 0, 'Lấy thông tin người dùng chỉ định'),
(2499, 2494, '', 'Biểu mẫu thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-add-6465802de8e2c', 0, 'Biểu mẫu thêm hoặc sửa nhãn người dùng'),
(2500, 2494, '', 'Thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-save-6465802de8e30', 0, 'Thêm hoặc sửa nhãn người dùng'),
(2501, 10, '', 'Điểm thưởng và số dư', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-set-balance', 0, 'Điểm thưởng và số dư'),
(2502, 2501, '', 'Biểu mẫu sửa điểm thưởng và số dư', '', '', '', 'user/edit_other/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-edit_other', 0, 'Biểu mẫu sửa điểm thưởng và số dư'),
(2503, 2501, '', 'Sửa điểm thưởng và số dư', '', '', '', 'user/update_other/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-update_other', 0, 'Sửa điểm thưởng và số dư'),
(2504, 10, '', 'Tặng gói thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-set-level-time', 0, 'Tặng gói thành viên'),
(2505, 2504, '', 'Tặng thời hạn thành viên trả phí', '', '', '', 'user/give_level_time/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-give_level_time', 0, 'Tặng thời hạn thành viên trả phí'),
(2506, 2504, '', 'Thực hiện tặng thời hạn thành viên trả phí', '', '', '', 'user/save_give_level_time/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-save_give_level_time', 0, 'Thực hiện tặng thời hạn thành viên trả phí'),
(2507, 10, '', 'Đặt nhóm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-set-group', 0, 'Đặt nhóm'),
(2508, 2507, '', 'Biểu mẫu nhóm người dùng', '', '', '', 'user/set_group', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-set_group-646585b911c0b', 0, 'Biểu mẫu nhóm người dùng'),
(2509, 2507, '', 'Đặt nhóm người dùng', '', '', '', 'user/save_set_group', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-save_set_group-646585b911c11', 0, 'Đặt nhóm người dùng'),
(2510, 10, '', 'Đặt nhãn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-set-label', 0, 'Đặt nhãn'),
(2511, 2510, '', 'Lấy nhãn người dùng', '', '', '', 'user/label/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-label-646585fd46ff0', 0, 'Lấy nhãn người dùng'),
(2512, 2510, '', 'Đặt và hủy nhãn người dùng', '', '', '', 'user/label/<uid>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-label-646585fd46ff6', 0, 'Đặt và hủy nhãn người dùng'),
(2513, 10, '', 'Sửa người giới thiệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/10', 3, '', 0, 'user-set-spread', 0, 'Sửa người giới thiệu'),
(2514, 2513, '', 'Danh sách chọn người dùng khi thêm CSKH', '', '', '', 'app/wechat/kefu/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-create', 0, 'Danh sách chọn người dùng khi thêm CSKH'),
(2515, 2513, '', 'Sửa người giới thiệu', '', '', '', 'agent/spread', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-spread', 0, 'Sửa người giới thiệu'),
(2516, 227, '', 'Thêm nhóm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/227', 3, '', 0, 'user-group-add', 0, 'Thêm nhóm'),
(2517, 227, '', 'Sửa nhóm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/227', 3, '', 0, 'user-group-update', 0, 'Sửa nhóm'),
(2518, 227, '', 'Xóa nhóm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/227', 3, '', 0, 'user-group-delete', 0, 'Xóa nhóm'),
(2519, 2516, '', 'Biểu mẫu thêm/sửa nhóm', '', '', '', 'user/user_group/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_group-add', 0, 'Biểu mẫu thêm/sửa nhóm'),
(2520, 2516, '', 'Lưu dữ liệu biểu mẫu nhóm', '', '', '', 'user/user_group/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_group-save', 0, 'Lưu dữ liệu biểu mẫu nhóm'),
(2521, 2517, '', 'Lưu dữ liệu biểu mẫu nhóm', '', '', '', 'user/user_group/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_group-save-646586e1d4557', 0, 'Lưu dữ liệu biểu mẫu nhóm'),
(2522, 2517, '', 'Biểu mẫu thêm/sửa nhóm', '', '', '', 'user/user_group/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_group-add-646586e1d455e', 0, 'Biểu mẫu thêm/sửa nhóm'),
(2523, 2518, '', 'Xóa dữ liệu nhóm người dùng', '', '', '', 'user/user_group/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_group-del', 0, 'Xóa dữ liệu nhóm người dùng'),
(2525, 589, '', 'Danh mục nhãn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/589', 3, '', 0, 'user-label-cate', 0, 'Danh mục nhãn'),
(2526, 589, '', 'Thêm nhãn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/589', 3, '', 0, 'user-label-add', 0, 'Thêm nhãn'),
(2527, 589, '', 'Sửa nhãn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/589', 3, '', 0, 'user-label-update', 0, 'Sửa nhãn'),
(2528, 589, '', 'Xóa nhãn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/589', 3, '', 0, 'user-label-delete', 0, 'Xóa nhãn'),
(2529, 2525, '', 'Lấy biểu mẫu danh mục nhãn', '', '', '', 'user/user_label_cate/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label_cate-create', 0, 'Lấy biểu mẫu danh mục nhãn'),
(2530, 2525, '', 'Lưu danh mục nhãn', '', '', '', 'user/user_label_cate', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label_cate', 0, 'Lưu danh mục nhãn'),
(2531, 2525, '', 'Lấy biểu mẫu sửa danh mục nhãn', '', '', '', 'user/user_label_cate/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label_cate-edit', 0, 'Lấy biểu mẫu sửa danh mục nhãn'),
(2532, 2525, '', 'Sửa danh mục nhãn', '', '', '', 'user/user_label_cate/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label_cate', 0, 'Sửa danh mục nhãn'),
(2533, 2525, '', 'Xóa danh mục nhãn', '', '', '', 'user/user_label_cate/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label_cate', 0, 'Xóa danh mục nhãn'),
(2534, 2526, '', 'Thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-save-6465897098557', 0, 'Thêm hoặc sửa nhãn người dùng'),
(2535, 2526, '', 'Biểu mẫu thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-add-646589709855d', 0, 'Biểu mẫu thêm hoặc sửa nhãn người dùng'),
(2536, 2527, '', 'Thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-save-6465897903b20', 0, 'Thêm hoặc sửa nhãn người dùng'),
(2537, 2527, '', 'Biểu mẫu thêm hoặc sửa nhãn người dùng', '', '', '', 'user/user_label/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-add-6465897903b2c', 0, 'Biểu mẫu thêm hoặc sửa nhãn người dùng'),
(2538, 2528, '', 'Xóa nhãn người dùng', '', '', '', 'user/user_label/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_label-del', 0, 'Xóa nhãn người dùng'),
(2539, 11, '', 'Thêm hạng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/11', 3, '', 0, 'user-level-add', 0, 'Thêm hạng'),
(2540, 11, '', 'Sửa hạng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/11', 3, '', 0, 'user-level-update', 0, 'Sửa hạng'),
(2541, 11, '', 'Xóa hạng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/11', 3, '', 0, 'user-level-delete', 0, 'Xóa hạng'),
(2542, 11, '', 'Đặt trạng thái hạng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '9/11', 3, '', 0, 'user-level-status', 0, 'Đặt trạng thái hạng'),
(2543, 2539, '', 'Thêm hoặc sửa hạng người dùng', '', '', '', 'user/user_level', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_level', 0, 'Thêm hoặc sửa hạng người dùng'),
(2544, 2539, '', 'Biểu mẫu thêm hạng người dùng', '', '', '', 'user/user_level/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_level-create', 0, 'Biểu mẫu thêm hạng người dùng'),
(2545, 2540, '', 'Biểu mẫu thêm hạng người dùng', '', '', '', 'user/user_level/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_level-create-64658a1262574', 0, 'Biểu mẫu thêm hạng người dùng'),
(2546, 2540, '', 'Thêm hoặc sửa hạng người dùng', '', '', '', 'user/user_level', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_level-64658a126257b', 0, 'Thêm hoặc sửa hạng người dùng'),
(2547, 2541, '', 'Xóa hạng người dùng', '', '', '', 'user/user_level/delete/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_level-delete', 0, 'Xóa hạng người dùng'),
(2548, 2542, '', 'Hiện/ẩn hạng người dùng', '', '', '', 'user/user_level/set_show/<id>/<is_show>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-user_level-set_show', 0, 'Hiện/ẩn hạng người dùng'),
(2549, 5, '', 'Xóa đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-delete', 0, 'Xóa đơn hàng'),
(2550, 5, '', 'Xác nhận sử dụng đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-write-off', 0, 'Xác nhận sử dụng đơn hàng'),
(2551, 5, '', 'Xuất đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-export', 0, 'Xuất đơn hàng'),
(2554, 2549, '', 'Xóa một đơn hàng', '', '', '', 'order/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-del', 0, 'Xóa một đơn hàng'),
(2555, 2549, '', 'Xóa đơn hàng hàng loạt', '', '', '', 'order/dels', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-dels', 0, 'Xóa đơn hàng hàng loạt'),
(2556, 2550, '', 'Xác nhận sử dụng đơn hàng', '', '', '', 'order/write', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-write', 0, 'Xác nhận sử dụng đơn hàng'),
(2557, 2550, '', 'Xác nhận sử dụng theo mã đơn hàng', '', '', '', 'order/write_update/<order_id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-write_update', 0, 'Xác nhận sử dụng theo mã đơn hàng'),
(2558, 2551, '', 'Xuất danh sách đơn hàng', '', '', '', 'export/order_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-order_list', 0, 'Xuất danh sách đơn hàng'),
(2559, 5, '', 'Sửa đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-edit', 0, 'Sửa đơn hàng'),
(2560, 5, '', 'Giao đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-send', 0, 'Giao đơn hàng'),
(2561, 5, '', 'Xác nhận thanh toán ngoại tuyến', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-offline-confirm', 0, 'Xác nhận thanh toán ngoại tuyến'),
(2562, 5, '', 'Chi tiết đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-info', 0, 'Chi tiết đơn hàng'),
(2563, 5, '', 'Lịch sử đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-record', 0, 'Lịch sử đơn hàng'),
(2564, 5, '', 'In vận đơn điện tử', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-electron', 0, 'In vận đơn điện tử'),
(2565, 5, '', 'In biên lai', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-tips', 0, 'In biên lai'),
(2566, 5, '', 'Ghi chú đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-mark', 0, 'Ghi chú đơn hàng'),
(2567, 5, '', 'Xác nhận đã nhận hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-take', 0, 'Xác nhận đã nhận hàng'),
(2568, 5, '', 'Xóa đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-delete', 0, 'Xóa đơn hàng'),
(2569, 5, '', 'In vận đơn chuyển phát', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/5', 3, '', 0, 'order-express', 0, 'In vận đơn chuyển phát'),
(2570, 2559, '', 'Lấy biểu mẫu sửa đơn hàng', '', '', '', 'order/edit/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-edit-6465a3050171f', 0, 'Lấy biểu mẫu sửa đơn hàng'),
(2571, 2559, '', 'Chỉnh sửa đơn hàng', '', '', '', 'order/update/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-update', 0, 'Chỉnh sửa đơn hàng'),
(2572, 2560, '', 'Thực hiện giao đơn hàng', '', '', '', 'order/delivery/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery', 0, 'Thực hiện giao đơn hàng'),
(2573, 2560, '', 'Lấy danh sách sản phẩm có thể tách của đơn hàng', '', '', '', 'order/split_cart_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-split_cart_info', 0, 'Lấy danh sách sản phẩm có thể tách của đơn hàng'),
(2574, 2560, '', 'Tách đơn để giao hàng', '', '', '', 'order/split_delivery/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-split_delivery', 0, 'Tách đơn để giao hàng'),
(2575, 2560, '', 'Lấy danh sách đơn con đã tách của đơn hàng', '', '', '', 'order/split_order/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-split_order', 0, 'Lấy danh sách đơn con đã tách của đơn hàng'),
(2576, 2560, '', 'Lấy biểu mẫu thông tin giao hàng', '', '', '', 'order/distribution/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-distribution', 0, 'Lấy biểu mẫu thông tin giao hàng'),
(2577, 2560, '', 'Lấy đơn vị vận chuyển', '', '', '', 'order/express_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-express_list', 0, 'Lấy đơn vị vận chuyển'),
(2578, 2560, '', 'Lấy thông tin vận chuyển', '', '', '', 'order/express/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-express-6465a3438b950', 0, 'Lấy thông tin vận chuyển'),
(2579, 2560, '', 'Thông tin cấu hình mặc định của vận đơn', '', '', '', 'order/sheet_info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-sheet_info', 0, 'Thông tin cấu hình mặc định của vận đơn'),
(2580, 2560, '', 'Lấy nhân viên giao hàng cho danh sách đơn hàng', '', '', '', 'order/delivery/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-list', 0, 'Lấy nhân viên giao hàng cho danh sách đơn hàng'),
(2581, 2560, '', 'Danh sách mẫu vận đơn điện tử', '', '', '', 'order/expr/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-expr-temp', 0, 'Danh sách mẫu vận đơn điện tử'),
(2582, 2560, '', 'Mẫu vận đơn điện tử của đơn vị vận chuyển', '', '', '', 'order/express/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-express-temp', 0, 'Mẫu vận đơn điện tử của đơn vị vận chuyển'),
(2583, 2560, '', 'Thao tác khác: in vận đơn điện tử', '', '', '', 'order/order_dump/<order_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-order_dump', 0, 'Thao tác khác: in vận đơn điện tử'),
(2584, 2561, '', 'Thanh toán ngoại tuyến', '', '', '', 'order/pay_offline/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-pay_offline', 0, 'Thanh toán ngoại tuyến'),
(2585, 2562, '', 'Chi tiết đơn hàng', '', '', '', 'order/info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-info-6465a369a2c3b', 0, 'Chi tiết đơn hàng'),
(2586, 2563, '', 'Lấy trạng thái đơn hàng', '', '', '', 'order/status/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-status', 0, 'Lấy trạng thái đơn hàng'),
(2587, 2564, '', 'Thông tin cấu hình mặc định của vận đơn', '', '', '', 'order/sheet_info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-sheet_info-6465a3eb77bb4', 0, 'Thông tin cấu hình mặc định của vận đơn'),
(2588, 2564, '', 'Mẫu vận đơn điện tử của đơn vị vận chuyển', '', '', '', 'order/express/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-express-temp-6465a3eb77bbf', 0, 'Mẫu vận đơn điện tử của đơn vị vận chuyển'),
(2589, 2564, '', 'Thao tác khác: in vận đơn điện tử', '', '', '', 'order/order_dump/<order_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-order_dump-6465a3eb77bc6', 0, 'Thao tác khác: in vận đơn điện tử'),
(2590, 2564, '', 'Danh sách mẫu vận đơn điện tử', '', '', '', 'order/expr/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-expr-temp-6465a3eb77bcd', 0, 'Danh sách mẫu vận đơn điện tử'),
(2591, 2565, '', 'In đơn hàng', '', '', '', 'order/print/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-print', 0, 'In đơn hàng'),
(2592, 2566, '', 'Sửa thông tin ghi chú', '', '', '', 'order/remark/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-remark', 0, 'Sửa thông tin ghi chú'),
(2593, 2567, '', 'Xác nhận đã nhận hàng', '', '', '', 'order/take/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-take-6465a61b2ebdb', 0, 'Xác nhận đã nhận hàng'),
(2594, 2568, '', 'Xóa một đơn hàng', '', '', '', 'order/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-del-6465a66347928', 0, 'Xóa một đơn hàng'),
(2595, 2568, '', 'Xóa đơn hàng hàng loạt', '', '', '', 'order/dels', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-dels-6465a66347931', 0, 'Xóa đơn hàng hàng loạt'),
(2596, 2569, '', 'Thao tác khác: in vận đơn điện tử', '', '', '', 'order/order_dump/<order_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-order_dump-6465a69038c44', 0, 'Thao tác khác: in vận đơn điện tử'),
(2597, 2569, '', 'Mẫu vận đơn điện tử của đơn vị vận chuyển', '', '', '', 'order/express/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-express-temp-6465a69038c4c', 0, 'Mẫu vận đơn điện tử của đơn vị vận chuyển'),
(2598, 2569, '', 'Danh sách mẫu vận đơn điện tử', '', '', '', 'order/expr/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-expr-temp-6465a69038c51', 0, 'Danh sách mẫu vận đơn điện tử'),
(2599, 897, '', 'Chi tiết đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/897', 3, '', 0, 'refund-info', 0, 'Chi tiết đơn hàng'),
(2600, 897, '', 'Ghi chú đổi trả', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/897', 3, '', 0, 'refund-mark', 0, 'Ghi chú đổi trả'),
(2601, 897, '', 'Hoàn tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/897', 3, '', 0, 'refund-yes', 0, 'Hoàn tiền'),
(2602, 897, '', 'Từ chối hoàn tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/897', 3, '', 0, 'refund-no', 0, 'Từ chối hoàn tiền'),
(2603, 2599, '', 'Lấy chi tiết đơn hoàn tiền', '', '', '', 'refund/info/<uni>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-info-6465c125e62a0', 0, 'Lấy chi tiết đơn hoàn tiền'),
(2604, 2600, '', 'Ghi chú đơn đổi trả', '', '', '', 'refund/remark/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-remark', 0, 'Ghi chú đơn đổi trả'),
(2605, 2601, '', 'Người bán đồng ý hoàn tiền, chờ người dùng trả hàng', '', '', '', 'refund/agree/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-agree', 0, 'Người bán đồng ý hoàn tiền, chờ người dùng trả hàng'),
(2606, 2601, '', 'Biểu mẫu hoàn tiền đơn đổi trả', '', '', '', 'refund/refund/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-refund', 0, 'Biểu mẫu hoàn tiền đơn đổi trả'),
(2607, 2601, '', 'Hoàn tiền đơn đổi trả', '', '', '', 'refund/refund/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-refund', 0, 'Hoàn tiền đơn đổi trả'),
(2608, 2602, '', 'Sửa lý do từ chối hoàn tiền', '', '', '', 'refund/no_refund/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-no_refund', 0, 'Sửa lý do từ chối hoàn tiền'),
(2609, 2602, '', 'Lấy biểu mẫu từ chối hoàn tiền', '', '', '', 'refund/no_refund/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'refund-no_refund', 0, 'Lấy biểu mẫu từ chối hoàn tiền'),
(2610, 760, '', 'Mã QR nhận tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '4/760', 3, '', 0, 'order-offline-qrcode', 0, 'Mã QR nhận tiền'),
(2611, 2610, '', 'Lấy mã QR thanh toán ngoại tuyến', '', '', '', 'order/offline_scan', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-offline_scan', 0, 'Lấy mã QR thanh toán ngoại tuyến'),
(2612, 2, '', 'Thêm sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-add', 0, 'Thêm sản phẩm'),
(2613, 2, '', 'Thu thập sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-copy', 0, 'Thu thập sản phẩm'),
(2614, 2, '', 'Sửa hàng loạt', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-batch-edit', 0, 'Sửa hàng loạt'),
(2615, 2, '', 'Đăng bán/ngừng bán sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-batch-status', 0, 'Đăng bán/ngừng bán sản phẩm'),
(2616, 2, '', 'Xuất sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-export', 0, 'Xuất sản phẩm'),
(2617, 2, '', 'Xem sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-info', 0, 'Xem sản phẩm'),
(2618, 2, '', 'Sửa sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-edit', 0, 'Sửa sản phẩm'),
(2619, 2, '', 'Đánh giá sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-reply', 0, 'Đánh giá sản phẩm'),
(2620, 2, '', 'Thùng rác sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/2', 3, '', 0, 'product-recycle', 0, 'Thùng rác sản phẩm'),
(2621, 2612, '', 'Lấy dữ liệu chưa lưu khi thoát', '', '', '', 'product/cache', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-cache', 0, 'Lấy dữ liệu chưa lưu khi thoát'),
(2622, 2612, '', 'Lưu dữ liệu chưa gửi', '', '', '', 'product/cache', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-cache', 0, 'Lưu dữ liệu chưa gửi'),
(2623, 2612, '', 'Lấy quy cách sản phẩm', '', '', '', 'product/product/attrs/<id>/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-attrs', 0, 'Lấy quy cách sản phẩm'),
(2624, 2612, '', 'Tạo mới hoặc sửa sản phẩm', '', '', '', 'product/product/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product', 0, 'Tạo mới hoặc sửa sản phẩm'),
(2625, 2612, '', 'Sửa nhanh sản phẩm', '', '', '', 'product/product/set_product/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-set_product', 0, 'Sửa nhanh sản phẩm'),
(2626, 2612, '', 'Danh sách mẫu quy cách sản phẩm', '', '', '', 'product/product/rule', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule', 0, 'Danh sách mẫu quy cách sản phẩm'),
(2627, 2612, '', 'Tạo mới hoặc sửa mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule', 0, 'Tạo mới hoặc sửa mẫu quy cách sản phẩm'),
(2628, 2612, '', 'Tạo danh sách quy cách sản phẩm', '', '', '', 'product/generate_attr/<id>/<type>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-generate_attr', 0, 'Tạo danh sách quy cách sản phẩm'),
(2629, 2612, '', 'Lấy mẫu thuộc tính quy cách sản phẩm', '', '', '', 'product/product/get_rule', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_rule', 0, 'Lấy mẫu thuộc tính quy cách sản phẩm'),
(2630, 2612, '', 'Lấy mẫu phí vận chuyển', '', '', '', 'product/product/get_template', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_template', 0, 'Lấy mẫu phí vận chuyển'),
(2631, 2612, '', 'API khóa tải lên video', '', '', '', 'product/product/get_temp_keys', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_temp_keys', 0, 'API khóa tải lên video'),
(2632, 2612, '', 'Nhập mã thẻ sản phẩm ảo', '', '', '', 'product/product/import_card', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-import_card', 0, 'Nhập mã thẻ sản phẩm ảo'),
(2633, 2613, '', 'Lấy dữ liệu sản phẩm thu thập', '', '', '', 'product/crawl', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-crawl', 0, 'Lấy dữ liệu sản phẩm thu thập'),
(2634, 2613, '', 'Lấy cấu hình sao chép sản phẩm', '', '', '', 'product/copy_config', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-copy_config', 0, 'Lấy cấu hình sao chép sản phẩm'),
(2635, 2613, '', 'Lưu dữ liệu sản phẩm thu thập', '', '', '', 'product/crawl/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-crawl-save', 0, 'Lưu dữ liệu sản phẩm thu thập'),
(2636, 2613, '', 'Sao chép sản phẩm từ nền tảng khác', '', '', '', 'product/copy', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-copy-6465c40d4430f', 0, 'Sao chép sản phẩm từ nền tảng khác'),
(2637, 2613, '', 'Lưu dữ liệu chưa gửi', '', '', '', 'product/cache', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-cache-6465c40d44313', 0, 'Lưu dữ liệu chưa gửi'),
(2638, 2613, '', 'Lấy dữ liệu chưa lưu khi thoát', '', '', '', 'product/cache', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-cache-6465c40d44317', 0, 'Lấy dữ liệu chưa lưu khi thoát'),
(2639, 2613, '', 'Lấy quy cách sản phẩm', '', '', '', 'product/product/attrs/<id>/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-attrs-6465c40d4431b', 0, 'Lấy quy cách sản phẩm'),
(2640, 2613, '', 'Tạo mới hoặc sửa sản phẩm', '', '', '', 'product/product/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-6465c40d44320', 0, 'Tạo mới hoặc sửa sản phẩm'),
(2641, 2613, '', 'Sửa nhanh sản phẩm', '', '', '', 'product/product/set_product/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-set_product-6465c40d44324', 0, 'Sửa nhanh sản phẩm'),
(2642, 2613, '', 'Danh sách mẫu quy cách sản phẩm', '', '', '', 'product/product/rule', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465c40d44328', 0, 'Danh sách mẫu quy cách sản phẩm'),
(2643, 2613, '', 'Tạo mới hoặc sửa mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465c40d4432c', 0, 'Tạo mới hoặc sửa mẫu quy cách sản phẩm'),
(2644, 2613, '', 'Chi tiết mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465c40d4432f', 0, 'Chi tiết mẫu quy cách sản phẩm'),
(2645, 2613, '', 'Tạo danh sách quy cách sản phẩm', '', '', '', 'product/generate_attr/<id>/<type>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-generate_attr-6465c40d44333', 0, 'Tạo danh sách quy cách sản phẩm'),
(2646, 2613, '', 'Lấy mẫu thuộc tính quy cách sản phẩm', '', '', '', 'product/product/get_rule', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_rule-6465c40d44337', 0, 'Lấy mẫu thuộc tính quy cách sản phẩm'),
(2647, 2613, '', 'Lấy mẫu phí vận chuyển', '', '', '', 'product/product/get_template', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_template-6465c40d4433b', 0, 'Lấy mẫu phí vận chuyển'),
(2648, 2613, '', 'API khóa tải lên video', '', '', '', 'product/product/get_temp_keys', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_temp_keys-6465c40d4433f', 0, 'API khóa tải lên video'),
(2649, 2614, '', 'Cài đặt sản phẩm hàng loạt', '', '', '', 'product/batch/setting', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-batch-setting', 0, 'Cài đặt sản phẩm hàng loạt'),
(2650, 2615, '', 'Ngừng bán sản phẩm hàng loạt', '', '', '', 'product/product/product_unshow', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-product_unshow', 0, 'Ngừng bán sản phẩm hàng loạt'),
(2651, 2615, '', 'Đăng bán sản phẩm hàng loạt', '', '', '', 'product/product/product_show', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-product_show', 0, 'Đăng bán sản phẩm hàng loạt'),
(2652, 2615, '', 'Sửa trạng thái sản phẩm', '', '', '', 'product/product/set_show/<id>/<is_show>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-set_show', 0, 'Sửa trạng thái sản phẩm'),
(2653, 2616, '', 'Xuất danh sách sản phẩm', '', '', '', 'export/product_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-product_list', 0, 'Xuất danh sách sản phẩm'),
(2654, 2617, '', 'Chi tiết sản phẩm', '', '', '', 'product/product/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-6465c46cedb2c', 0, 'Chi tiết sản phẩm'),
(2655, 2618, '', 'Lấy dữ liệu chưa lưu khi thoát', '', '', '', 'product/cache', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-cache-6465c48f616d4', 0, 'Lấy dữ liệu chưa lưu khi thoát'),
(2656, 2618, '', 'Lưu dữ liệu chưa gửi', '', '', '', 'product/cache', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-cache-6465c48f616dd', 0, 'Lưu dữ liệu chưa gửi'),
(2657, 2618, '', 'Lấy quy cách sản phẩm', '', '', '', 'product/product/attrs/<id>/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-attrs-6465c48f616e5', 0, 'Lấy quy cách sản phẩm'),
(2658, 2618, '', 'Tạo mới hoặc sửa sản phẩm', '', '', '', 'product/product/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-6465c48f616eb', 0, 'Tạo mới hoặc sửa sản phẩm'),
(2659, 2618, '', 'Chi tiết sản phẩm', '', '', '', 'product/product/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-6465c48f616f0', 0, 'Chi tiết sản phẩm'),
(2660, 2618, '', 'Sửa nhanh sản phẩm', '', '', '', 'product/product/set_product/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-set_product-6465c48f616f5', 0, 'Sửa nhanh sản phẩm'),
(2661, 2618, '', 'Danh sách mẫu quy cách sản phẩm', '', '', '', 'product/product/rule', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465c48f616fb', 0, 'Danh sách mẫu quy cách sản phẩm'),
(2662, 2618, '', 'Tạo mới hoặc sửa mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465c48f61700', 0, 'Tạo mới hoặc sửa mẫu quy cách sản phẩm'),
(2663, 2618, '', 'Chi tiết mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465c48f61705', 0, 'Chi tiết mẫu quy cách sản phẩm'),
(2664, 2618, '', 'Tạo danh sách quy cách sản phẩm', '', '', '', 'product/generate_attr/<id>/<type>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-generate_attr-6465c48f6170b', 0, 'Tạo danh sách quy cách sản phẩm'),
(2665, 2618, '', 'Lấy mẫu thuộc tính quy cách sản phẩm', '', '', '', 'product/product/get_rule', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_rule-6465c48f61710', 0, 'Lấy mẫu thuộc tính quy cách sản phẩm'),
(2666, 2618, '', 'Lấy mẫu phí vận chuyển', '', '', '', 'product/product/get_template', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_template-6465c48f61715', 0, 'Lấy mẫu phí vận chuyển'),
(2667, 2618, '', 'API khóa tải lên video', '', '', '', 'product/product/get_temp_keys', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-get_temp_keys-6465c48f6171a', 0, 'API khóa tải lên video'),
(2668, 2618, '', 'Nhập mã thẻ sản phẩm ảo', '', '', '', 'product/product/import_card', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-import_card-6465c48f6171f', 0, 'Nhập mã thẻ sản phẩm ảo'),
(2669, 2620, '', 'Chuyển sản phẩm vào thùng rác', '', '', '', 'product/product/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-6465c51a7665e', 0, 'Chuyển sản phẩm vào thùng rác'),
(2670, 3, '', 'Thêm danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/3', 3, '', 0, 'product-cate-add', 0, 'Thêm danh mục'),
(2671, 3, '', 'Sửa danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/3', 3, '', 0, 'product-cate-edit', 0, 'Sửa danh mục'),
(2672, 3, '', 'Xóa danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/3', 3, '', 0, 'product-cate-delete', 0, 'Xóa danh mục'),
(2673, 3, '', 'Trạng thái danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/3', 3, '', 0, 'product-cate-status', 0, 'Trạng thái danh mục'),
(2674, 2670, '', 'Thêm danh mục sản phẩm', '', '', '', 'product/category', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-category', 0, 'Thêm danh mục sản phẩm'),
(2675, 2670, '', 'Biểu mẫu thêm danh mục sản phẩm', '', '', '', 'product/category/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-category-create', 0, 'Biểu mẫu thêm danh mục sản phẩm'),
(2676, 2671, '', 'Biểu mẫu sửa danh mục sản phẩm', '', '', '', 'product/category/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-category-6465c612c552f', 0, 'Biểu mẫu sửa danh mục sản phẩm'),
(2677, 2671, '', 'Sửa danh mục sản phẩm', '', '', '', 'product/category/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-category-6465c612c5536', 0, 'Sửa danh mục sản phẩm'),
(2678, 2672, '', 'Xóa danh mục sản phẩm', '', '', '', 'product/category/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-category-6465c638c6d16', 0, 'Xóa danh mục sản phẩm'),
(2679, 2673, '', 'Sửa trạng thái danh mục sản phẩm', '', '', '', 'product/category/set_show/<id>/<is_show>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-category-set_show', 0, 'Sửa trạng thái danh mục sản phẩm'),
(2680, 99, '', 'Thêm quy cách', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/99', 3, '', 0, 'product-rule-add', 0, 'Thêm quy cách'),
(2681, 99, '', 'Sửa quy cách', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/99', 3, '', 0, 'product-rule-edit', 0, 'Sửa quy cách'),
(2682, 99, '', 'Xóa quy cách', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/99', 3, '', 0, 'product-rule-delete', 0, 'Xóa quy cách'),
(2683, 2680, '', 'Chi tiết mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465d8779407b', 0, 'Chi tiết mẫu quy cách sản phẩm'),
(2684, 2680, '', 'Tạo mới hoặc sửa mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465d87794082', 0, 'Tạo mới hoặc sửa mẫu quy cách sản phẩm'),
(2685, 2681, '', 'Tạo mới hoặc sửa mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465d88b800ca', 0, 'Tạo mới hoặc sửa mẫu quy cách sản phẩm'),
(2686, 2681, '', 'Chi tiết mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-6465d88b800d4', 0, 'Chi tiết mẫu quy cách sản phẩm'),
(2687, 2682, '', 'Xóa mẫu quy cách sản phẩm', '', '', '', 'product/product/rule/delete', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-product-rule-delete', 0, 'Xóa mẫu quy cách sản phẩm'),
(2688, 6, '', 'Thêm đánh giá tự tạo', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/6', 3, '', 0, 'product-reply-add', 0, 'Thêm đánh giá tự tạo'),
(2689, 6, '', 'Trả lời đánh giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/6', 3, '', 0, 'product-reply-reply', 0, 'Trả lời đánh giá'),
(2690, 6, '', 'Xóa đánh giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '1/6', 3, '', 0, 'product-reply-delete', 0, 'Xóa đánh giá'),
(2691, 2688, '', 'Biểu mẫu đánh giá ảo', '', '', '', 'product/reply/fictitious_reply/<product_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-reply-fictitious_reply', 0, 'Biểu mẫu đánh giá ảo'),
(2692, 2688, '', 'Lưu đánh giá ảo', '', '', '', 'product/reply/save_fictitious_reply', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-reply-save_fictitious_reply', 0, 'Lưu đánh giá ảo'),
(2693, 2689, '', 'Trả lời đánh giá sản phẩm', '', '', '', 'product/reply/set_reply/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-reply-set_reply', 0, 'Trả lời đánh giá sản phẩm'),
(2694, 2690, '', 'Xóa đánh giá sản phẩm', '', '', '', 'product/reply/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'product-reply-6465d92d1ba2d', 0, 'Xóa đánh giá sản phẩm'),
(2695, 71, '', 'Thêm/sao chép phiếu giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/30/71', 3, '', 0, 'coupon-add', 0, 'Thêm/sao chép phiếu giảm giá'),
(2696, 71, '', 'Xóa phiếu giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/30/71', 3, '', 0, 'coupon-delete', 0, 'Xóa phiếu giảm giá'),
(2697, 71, '', 'Lịch sử nhận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/30/71', 3, '', 0, 'coupon-receive', 0, 'Lịch sử nhận'),
(2698, 2695, '', 'Thêm phiếu giảm giá', '', '', '', 'marketing/coupon/save_coupon', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-save_coupon', 0, 'Thêm phiếu giảm giá'),
(2699, 2695, '', 'Sao chép nhanh phiếu giảm giá', '', '', '', 'marketing/coupon/copy/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-copy', 0, 'Sao chép nhanh phiếu giảm giá'),
(2700, 2696, '', 'Xóa phiếu giảm giá đã phát hành', '', '', '', 'marketing/coupon/released/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-released', 0, 'Xóa phiếu giảm giá đã phát hành'),
(2701, 2697, '', 'Lịch sử nhận phiếu giảm giá đã phát hành', '', '', '', 'marketing/coupon/released/issue_log/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-released-issue_log', 0, 'Lịch sử nhận phiếu giảm giá đã phát hành'),
(2702, 2697, '', 'Lịch sử nhận của thành viên', '', '', '', 'marketing/coupon/user', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-coupon-user', 0, 'Lịch sử nhận của thành viên'),
(2713, 905, '', 'Thêm sản phẩm đổi điểm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/905', 3, '', 0, 'point-product-add', 0, 'Thêm sản phẩm đổi điểm'),
(2714, 905, '', 'Sửa sản phẩm đổi điểm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/905', 3, '', 0, 'point-product-edit', 0, 'Sửa sản phẩm đổi điểm'),
(2715, 905, '', 'Xóa sản phẩm đổi điểm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/905', 3, '', 0, 'point-product-delete', 0, 'Xóa sản phẩm đổi điểm'),
(2716, 905, '', 'Trạng thái sản phẩm đổi điểm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/905', 3, '', 0, 'point-product-status', 0, 'Trạng thái sản phẩm đổi điểm'),
(2717, 905, '', 'Lịch sử đổi thưởng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/905', 3, '', 0, 'point-product-record', 0, 'Lịch sử đổi thưởng'),
(2718, 2713, '', 'Thêm mới hoặc sửa sản phẩm đổi điểm', '', '', '', 'marketing/integral/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral', 0, 'Thêm mới hoặc sửa sản phẩm đổi điểm'),
(2719, 2714, '', 'Thêm mới hoặc sửa sản phẩm đổi điểm', '', '', '', 'marketing/integral/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-6465e598dac7e', 0, 'Thêm mới hoặc sửa sản phẩm đổi điểm'),
(2720, 2714, '', 'Chi tiết sản phẩm đổi điểm', '', '', '', 'marketing/integral/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-6465e598dac85', 0, 'Chi tiết sản phẩm đổi điểm'),
(2721, 2715, '', 'Xóa sản phẩm đổi điểm', '', '', '', 'marketing/integral/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-6465e5a7d7e0b', 0, 'Xóa sản phẩm đổi điểm'),
(2722, 2716, '', 'Sửa trạng thái sản phẩm đổi điểm', '', '', '', 'marketing/integral/set_show/<id>/<is_show>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-set_show', 0, 'Sửa trạng thái sản phẩm đổi điểm'),
(2723, 912, '', 'Giao đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/912', 3, '', 0, 'point-order-send', 0, 'Giao đơn hàng'),
(2724, 912, '', 'Chi tiết đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/912', 3, '', 0, 'point-order-info', 0, 'Chi tiết đơn hàng'),
(2725, 912, '', 'Lịch sử đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/912', 3, '', 0, 'point-order-record', 0, 'Lịch sử đơn hàng'),
(2726, 912, '', 'In biên lai', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/912', 3, '', 0, 'point-order-print', 0, 'In biên lai'),
(2727, 912, '', 'Ghi chú đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/912', 3, '', 0, 'point-order-mark', 0, 'Ghi chú đơn hàng'),
(2728, 912, '', 'Xác nhận đã nhận hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/912', 3, '', 0, 'point-order-take', 0, 'Xác nhận đã nhận hàng'),
(2729, 2723, '', 'Giao đơn đổi điểm', '', '', '', 'marketing/integral/order/delivery/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-delivery', 0, 'Giao đơn đổi điểm'),
(2730, 2723, '', 'Lấy biểu mẫu thông tin giao hàng đơn đổi điểm', '', '', '', 'marketing/integral/order/distribution/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-distribution', 0, 'Lấy biểu mẫu thông tin giao hàng đơn đổi điểm'),
(2731, 2723, '', 'Sửa thông tin giao hàng đơn đổi điểm', '', '', '', 'marketing/integral/order/distribution/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-distribution', 0, 'Sửa thông tin giao hàng đơn đổi điểm'),
(2732, 2723, '', 'Lấy đơn vị vận chuyển cho đơn đổi điểm', '', '', '', 'marketing/integral/order/express_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-express_list', 0, 'Lấy đơn vị vận chuyển cho đơn đổi điểm'),
(2733, 2723, '', 'Mẫu vận đơn điện tử của đơn vị vận chuyển cho đơn đổi điểm', '', '', '', 'marketing/integral/order/express/temp', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-express-temp', 0, 'Mẫu vận đơn điện tử của đơn vị vận chuyển cho đơn đổi điểm'),
(2734, 2723, '', 'Lấy nhân viên giao hàng cho danh sách đơn đổi điểm', '', '', '', 'marketing/integral/order/delivery/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-delivery-list', 0, 'Lấy nhân viên giao hàng cho danh sách đơn đổi điểm'),
(2735, 2723, '', 'Lấy thông tin cấu hình vận đơn mặc định cho đơn đổi điểm', '', '', '', 'marketing/integral/order/sheet_info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-sheet_info', 0, 'Lấy thông tin cấu hình vận đơn mặc định cho đơn đổi điểm'),
(2736, 2723, '', 'Lấy thông tin vận chuyển đơn đổi điểm', '', '', '', 'marketing/integral/order/express/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-express', 0, 'Lấy thông tin vận chuyển đơn đổi điểm'),
(2737, 2724, '', 'Dữ liệu chi tiết đơn hàng cửa hàng đổi điểm', '', '', '', 'marketing/integral/order/info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-info', 0, 'Dữ liệu chi tiết đơn hàng cửa hàng đổi điểm'),
(2738, 2725, '', 'Lấy trạng thái đơn đổi điểm', '', '', '', 'marketing/integral/order/status/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-status', 0, 'Lấy trạng thái đơn đổi điểm'),
(2739, 2726, '', 'In đơn đổi điểm', '', '', '', 'marketing/integral/order/print/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-print', 0, 'In đơn đổi điểm'),
(2740, 2727, '', 'Ghi chú danh sách lịch sử điểm thưởng', '', '', '', 'marketing/point_record/remark/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-point_record-remark', 0, 'Ghi chú danh sách lịch sử điểm thưởng'),
(2741, 2728, '', 'Xác nhận đã nhận hàng đơn đổi điểm', '', '', '', 'marketing/integral/order/take/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-integral-order-take', 0, 'Xác nhận đã nhận hàng đơn đổi điểm'),
(2742, 1001, '', 'Ghi chú lịch sử điểm thưởng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/34/1001', 3, '', 0, 'point-record', 0, 'Ghi chú lịch sử điểm thưởng'),
(2743, 2742, '', 'Ghi chú danh sách lịch sử điểm thưởng', '', '', '', 'marketing/point_record/remark/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-point_record-remark-6465fd1a75f76', 0, 'Ghi chú danh sách lịch sử điểm thưởng'),
(2750, 74, '', 'Thêm săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/31/74', 3, '', 0, 'bargain-add', 0, 'Thêm săn giảm giá'),
(2751, 74, '', 'Xuất săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/31/74', 3, '', 0, 'bargain-export', 0, 'Xuất săn giảm giá'),
(2752, 74, '', 'Sửa săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/31/74', 3, '', 0, 'bargain-edit', 0, 'Sửa săn giảm giá'),
(2753, 74, '', 'Xóa săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/31/74', 3, '', 0, 'bargain-delete', 0, 'Xóa săn giảm giá'),
(2754, 74, '', 'Thống kê săn giảm giá', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/31/74', 3, '', 0, 'bargain-statistics', 0, 'Thống kê săn giảm giá'),
(2755, 2750, '', 'Thêm mới hoặc sửa sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain', 0, 'Thêm mới hoặc sửa sản phẩm săn giảm giá'),
(2756, 2750, '', 'Chi tiết sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain', 0, 'Chi tiết sản phẩm săn giảm giá'),
(2757, 2750, '', 'Sửa trạng thái sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain-set_status', 0, 'Sửa trạng thái sản phẩm săn giảm giá'),
(2758, 2751, '', 'Xuất danh sách sản phẩm săn giảm giá', '', '', '', 'export/bargain_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-bargain_list', 0, 'Xuất danh sách sản phẩm săn giảm giá'),
(2759, 2752, '', 'Chi tiết sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain-6465ff00a7c2b', 0, 'Chi tiết sản phẩm săn giảm giá'),
(2760, 2752, '', 'Thêm mới hoặc sửa sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain-6465ff00a7c33', 0, 'Thêm mới hoặc sửa sản phẩm săn giảm giá'),
(2761, 2752, '', 'Sửa trạng thái sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain-set_status-6465ff00a7c39', 0, 'Sửa trạng thái sản phẩm săn giảm giá'),
(2762, 2753, '', 'Xóa sản phẩm săn giảm giá', '', '', '', 'marketing/bargain/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain-6465ff10afa4e', 0, 'Xóa sản phẩm săn giảm giá'),
(2763, 755, '', 'Xem chi tiết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/31/755', 3, '', 0, 'bargain-list-info', 0, 'Xem chi tiết'),
(2764, 2763, '', 'Danh sách tham gia săn giảm giá', '', '', '', 'marketing/bargain_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain_list', 0, 'Danh sách tham gia săn giảm giá'),
(2765, 2763, '', 'Danh sách người giúp săn giảm giá', '', '', '', 'marketing/bargain_list_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-bargain_list_info', 0, 'Danh sách người giúp săn giảm giá'),
(2766, 75, '', 'Thêm mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/32/75', 3, '', 0, 'combination-add', 0, 'Thêm mua chung'),
(2767, 75, '', 'Xuất mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/32/75', 3, '', 0, 'combination-export', 0, 'Xuất mua chung'),
(2768, 75, '', 'Sửa mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/32/75', 3, '', 0, 'combination-edit', 0, 'Sửa mua chung'),
(2769, 75, '', 'Xóa mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/32/75', 3, '', 0, 'combination-delete', 0, 'Xóa mua chung'),
(2770, 75, '', 'Thống kê mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/32/75', 3, '', 0, 'combination-statistics', 0, 'Thống kê mua chung'),
(2771, 2766, '', 'Chi tiết sản phẩm mua chung', '', '', '', 'marketing/combination/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination', 0, 'Chi tiết sản phẩm mua chung'),
(2772, 2766, '', 'Thêm mới hoặc sửa sản phẩm mua chung', '', '', '', 'marketing/combination/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination', 0, 'Thêm mới hoặc sửa sản phẩm mua chung'),
(2773, 2766, '', 'Sửa trạng thái sản phẩm mua chung', '', '', '', 'marketing/combination/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-set_status', 0, 'Sửa trạng thái sản phẩm mua chung'),
(2774, 2767, '', 'Xuất danh sách sản phẩm mua chung', '', '', '', 'export/combination_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-combination_list', 0, 'Xuất danh sách sản phẩm mua chung'),
(2775, 2768, '', 'Thêm mới hoặc sửa sản phẩm mua chung', '', '', '', 'marketing/combination/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-6466cb2165ea1', 0, 'Thêm mới hoặc sửa sản phẩm mua chung'),
(2776, 2768, '', 'Sửa trạng thái sản phẩm mua chung', '', '', '', 'marketing/combination/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-set_status-6466cb2165ea7', 0, 'Sửa trạng thái sản phẩm mua chung'),
(2777, 2768, '', 'Chi tiết sản phẩm mua chung', '', '', '', 'marketing/combination/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-6466cb2165eab', 0, 'Chi tiết sản phẩm mua chung'),
(2778, 2769, '', 'Xóa sản phẩm mua chung', '', '', '', 'marketing/combination/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-6466cb2e9b9a8', 0, 'Xóa sản phẩm mua chung'),
(2779, 76, '', 'Chi tiết mua chung', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/32/76', 3, '', 0, 'combination-list-info', 0, 'Chi tiết mua chung'),
(2780, 2779, '', 'Danh sách tham gia mua chung', '', '', '', 'marketing/combination/combine/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-combine-list', 0, 'Danh sách tham gia mua chung'),
(2781, 2779, '', 'Danh sách người mua chung', '', '', '', 'marketing/combination/order_pink/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-combination-order_pink', 0, 'Danh sách người mua chung'),
(2782, 77, '', 'Thêm flash sale', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/33/77', 3, '', 0, 'seckill-add', 0, 'Thêm flash sale'),
(2783, 77, '', 'Xuất flash sale', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/33/77', 3, '', 0, 'seckill-export', 0, 'Xuất flash sale'),
(2784, 77, '', 'Sửa flash sale', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/33/77', 3, '', 0, 'seckill-edit', 0, 'Sửa flash sale'),
(2785, 77, '', 'Xóa flash sale', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/33/77', 3, '', 0, 'seckill-delete', 0, 'Xóa flash sale'),
(2786, 77, '', 'Thống kê flash sale', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/33/77', 3, '', 0, 'seckill-statistics', 0, 'Thống kê flash sale'),
(2787, 2782, '', 'Chi tiết sản phẩm flash sale', '', '', '', 'marketing/seckill/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill', 0, 'Chi tiết sản phẩm flash sale'),
(2788, 2782, '', 'Thêm mới hoặc sửa sản phẩm flash sale', '', '', '', 'marketing/seckill/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill', 0, 'Thêm mới hoặc sửa sản phẩm flash sale'),
(2789, 2782, '', 'Sửa trạng thái sản phẩm flash sale', '', '', '', 'marketing/seckill/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-set_status', 0, 'Sửa trạng thái sản phẩm flash sale'),
(2790, 2783, '', 'Xuất danh sách sản phẩm flash sale', '', '', '', 'export/seckill_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-seckill_list', 0, 'Xuất danh sách sản phẩm flash sale'),
(2791, 2784, '', 'Chi tiết sản phẩm flash sale', '', '', '', 'marketing/seckill/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-6466d624dd816', 0, 'Chi tiết sản phẩm flash sale'),
(2792, 2784, '', 'Thêm mới hoặc sửa sản phẩm flash sale', '', '', '', 'marketing/seckill/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-6466d624dd820', 0, 'Thêm mới hoặc sửa sản phẩm flash sale'),
(2793, 2784, '', 'Sửa trạng thái sản phẩm flash sale', '', '', '', 'marketing/seckill/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-set_status-6466d624dd826', 0, 'Sửa trạng thái sản phẩm flash sale'),
(2794, 2785, '', 'Xóa sản phẩm flash sale', '', '', '', 'marketing/seckill/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-6466d630e8c12', 0, 'Xóa sản phẩm flash sale'),
(2795, 2786, '', 'Người tham gia flash sale', '', '', '', 'marketing/seckill/statistics/order/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-statistics-order', 0, 'Người tham gia flash sale'),
(2796, 2786, '', 'Thống kê flash sale', '', '', '', 'marketing/seckill/statistics/head/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-statistics-head', 0, 'Thống kê flash sale'),
(2797, 2786, '', 'Người tham gia flash sale', '', '', '', 'marketing/seckill/statistics/people/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'marketing-seckill-statistics-people', 0, 'Người tham gia flash sale'),
(2798, 751, '', 'Thêm loại thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/751', 3, '', 0, 'member-add', 0, 'Thêm loại thành viên'),
(2799, 751, '', 'Sửa loại thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/751', 3, '', 0, 'member-edit', 0, 'Sửa loại thành viên'),
(2800, 751, '', 'Xóa loại thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/751', 3, '', 0, 'member-delete', 0, 'Xóa loại thành viên'),
(2801, 751, '', 'Trạng thái loại thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/751', 3, '', 0, 'member-status', 0, 'Trạng thái loại thành viên'),
(2802, 2798, '', 'Danh sách loại thành viên', '', '', '', 'user/member/ship', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member-ship', 0, 'Danh sách loại thành viên'),
(2803, 2798, '', 'Sửa trạng thái loại thành viên', '', '', '', 'user/member_ship/set_ship_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-set_ship_status', 0, 'Sửa trạng thái loại thành viên'),
(2804, 2798, '', 'Sửa loại thẻ thành viên', '', '', '', 'user/member_ship/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-save', 0, 'Sửa loại thẻ thành viên'),
(2805, 2799, '', 'Danh sách loại thành viên', '', '', '', 'user/member/ship', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member-ship-6466db17503ca', 0, 'Danh sách loại thành viên'),
(2806, 2799, '', 'Sửa trạng thái loại thành viên', '', '', '', 'user/member_ship/set_ship_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-set_ship_status-6466db17503d2', 0, 'Sửa trạng thái loại thành viên'),
(2807, 2799, '', 'Xóa loại thành viên', '', '', '', 'user/member_ship/delete/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-delete', 0, 'Xóa loại thành viên'),
(2808, 2799, '', 'Sửa loại thẻ thành viên', '', '', '', 'user/member_ship/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-save-6466db17503dc', 0, 'Sửa loại thẻ thành viên'),
(2809, 2800, '', 'Xóa loại thành viên', '', '', '', 'user/member_ship/delete/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-delete-6466db246a1e5', 0, 'Xóa loại thành viên'),
(2810, 2801, '', 'Sửa trạng thái loại thành viên', '', '', '', 'user/member_ship/set_ship_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_ship-set_ship_status-6466db33cfbdf', 0, 'Sửa trạng thái loại thành viên'),
(2811, 765, '', 'Sửa quyền lợi thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/765', 3, '', 0, 'member-right-edit', 0, 'Sửa quyền lợi thành viên'),
(2812, 765, '', 'Trạng thái quyền lợi thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/765', 3, '', 0, 'member-right-status', 0, 'Trạng thái quyền lợi thành viên'),
(2813, 2811, '', 'Chỉnh sửa quyền lợi thành viên', '', '', '', 'user/member_right/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_right-save', 0, 'Chỉnh sửa quyền lợi thành viên'),
(2814, 2812, '', 'Chỉnh sửa quyền lợi thành viên', '', '', '', 'user/member_right/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_right-save-6466e10aa77e5', 0, 'Chỉnh sửa quyền lợi thành viên'),
(2815, 762, '', 'Thêm lô', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/762', 3, '', 0, 'member-card-add', 0, 'Thêm lô'),
(2816, 762, '', 'Tải xuống mã QR', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/762', 3, '', 0, 'member-card-down-qrcode', 0, 'Tải xuống mã QR'),
(2817, 762, '', 'Sửa tên lô', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/762', 3, '', 0, 'member-card-edit', 0, 'Sửa tên lô'),
(2818, 762, '', 'Xem danh sách thẻ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/762', 3, '', 0, 'member-card-scan', 0, 'Xem danh sách thẻ'),
(2819, 762, '', 'Xuất mã thẻ thành viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/731/762', 3, '', 0, 'member-card-export', 0, 'Xuất mã thẻ thành viên'),
(2820, 2815, '', 'Thêm lô thẻ thành viên', '', '', '', 'user/member_batch/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_batch-save', 0, 'Thêm lô thẻ thành viên'),
(2821, 2815, '', 'Danh sách thẻ thành viên', '', '', '', 'user/member_card/index/<card_batch_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_card-index', 0, 'Danh sách thẻ thành viên'),
(2822, 2815, '', 'Sửa nhanh lô thẻ thành viên', '', '', '', 'user/member_batch/set_value/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_batch-set_value', 0, 'Sửa nhanh lô thẻ thành viên'),
(2823, 2815, '', 'Sửa trạng thái thẻ thành viên', '', '', '', 'user/member_card/set_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_card-set_status', 0, 'Sửa trạng thái thẻ thành viên'),
(2824, 2816, '', 'Mã QR đổi thẻ thành viên', '', '', '', 'user/member_scan', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_scan', 0, 'Mã QR đổi thẻ thành viên'),
(2825, 2817, '', 'Sửa nhanh lô thẻ thành viên', '', '', '', 'user/member_batch/set_value/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_batch-set_value-6466f21a4c295', 0, 'Sửa nhanh lô thẻ thành viên'),
(2826, 2817, '', 'Sửa trạng thái thẻ thành viên', '', '', '', 'user/member_card/set_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_card-set_status-6466f21a4c29e', 0, 'Sửa trạng thái thẻ thành viên'),
(2827, 2818, '', 'Danh sách thẻ thành viên', '', '', '', 'user/member_card/index/<card_batch_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'user-member_card-index-6466f23950641', 0, 'Danh sách thẻ thành viên'),
(2828, 2819, '', 'Xuất thẻ thành viên', '', '', '', 'export/member_card/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'export-member_card', 0, 'Xuất thẻ thành viên'),
(2829, 687, '', 'Thêm phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/687', 3, '', 0, 'live-room-add', 0, 'Thêm phòng livestream'),
(2830, 687, '', 'Đồng bộ phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/687', 3, '', 0, 'live-room-sync', 0, 'Đồng bộ phòng livestream'),
(2831, 687, '', 'Chi tiết phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/687', 3, '', 0, 'live-room-info', 0, 'Chi tiết phòng livestream'),
(2832, 687, '', 'Xóa phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/687', 3, '', 0, 'live-room-delete', 0, 'Xóa phòng livestream'),
(2833, 687, '', 'Trạng thái phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/687', 3, '', 0, 'live-room-status', 0, 'Trạng thái phòng livestream'),
(2834, 2829, '', 'Thêm phòng livestream', '', '', '', 'live/room/add', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-add-64671181de1c7', 0, 'Thêm phòng livestream'),
(2835, 2829, '', 'Chi tiết phòng livestream', '', '', '', 'live/room/detail/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-detail', 0, 'Chi tiết phòng livestream'),
(2836, 2830, '', 'Đồng bộ trạng thái phòng livestream', '', '', '', 'live/room/syncRoom', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-syncRoom', 0, 'Đồng bộ trạng thái phòng livestream'),
(2837, 2831, '', 'Chi tiết phòng livestream', '', '', '', 'live/room/detail/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-detail-646711a2b8bd3', 0, 'Chi tiết phòng livestream'),
(2838, 2832, '', 'Xóa phòng livestream', '', '', '', 'live/room/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-del', 0, 'Xóa phòng livestream'),
(2839, 2833, '', 'Đặt hiện/ẩn phòng livestream', '', '', '', 'live/room/set_show/<id>/<is_show>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-set_show', 0, 'Đặt hiện/ẩn phòng livestream'),
(2840, 687, '', 'Thêm sản phẩm vào phòng livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/687', 3, '', 0, 'live-room-add-product', 0, 'Thêm sản phẩm vào phòng livestream'),
(2841, 2840, '', 'Thêm sản phẩm vào phòng livestream', '', '', '', 'live/room/add_goods', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-room-add_goods', 0, 'Thêm sản phẩm vào phòng livestream'),
(2842, 688, '', 'Thêm sản phẩm livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/688', 3, '', 0, 'live-product-add', 0, 'Thêm sản phẩm livestream'),
(2843, 688, '', 'Chi tiết sản phẩm livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/688', 3, '', 0, 'live-product-info', 0, 'Chi tiết sản phẩm livestream'),
(2844, 688, '', 'Xóa sản phẩm livestream', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/688', 3, '', 0, 'live-product-delete', 0, 'Xóa sản phẩm livestream'),
(2845, 2842, '', 'Tạo sản phẩm livestream', '', '', '', 'live/goods/create', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-goods-create', 0, 'Tạo sản phẩm livestream'),
(2846, 2842, '', 'Thêm/sửa sản phẩm livestream', '', '', '', 'live/goods/add', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-goods-add', 0, 'Thêm/sửa sản phẩm livestream'),
(2847, 2843, '', 'Chi tiết sản phẩm livestream', '', '', '', 'live/goods/detail/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-goods-detail', 0, 'Chi tiết sản phẩm livestream'),
(2848, 2844, '', 'Xóa sản phẩm livestream', '', '', '', 'live/goods/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-goods-del', 0, 'Xóa sản phẩm livestream'),
(2849, 689, '', 'Thêm streamer', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/689', 3, '', 0, 'live-anchor-add', 0, 'Thêm streamer'),
(2850, 689, '', 'Sửa streamer', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/689', 3, '', 0, 'live-anchor-edit', 0, 'Sửa streamer'),
(2851, 689, '', 'Xóa streamer', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/686/689', 3, '', 0, 'live-anchor-delete', 0, 'Xóa streamer'),
(2852, 2849, '', 'Lưu dữ liệu streamer', '', '', '', 'live/anchor/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-anchor-save', 0, 'Lưu dữ liệu streamer'),
(2853, 2849, '', 'Biểu mẫu thêm/sửa streamer', '', '', '', 'live/anchor/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-anchor-add-64671b7155864', 0, 'Biểu mẫu thêm/sửa streamer'),
(2854, 2850, '', 'Lưu dữ liệu streamer', '', '', '', 'live/anchor/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-anchor-save-64671b7b408c3', 0, 'Lưu dữ liệu streamer'),
(2855, 2850, '', 'Biểu mẫu thêm/sửa streamer', '', '', '', 'live/anchor/add/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-anchor-add-64671b7b408ca', 0, 'Biểu mẫu thêm/sửa streamer'),
(2856, 2851, '', 'Xóa streamer', '', '', '', 'live/anchor/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'live-anchor-del', 0, 'Xóa streamer'),
(2857, 1023, '', 'Thêm nhóm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-code-add', 0, 'Thêm nhóm'),
(2858, 1023, '', 'Tạo mã QR mới', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-qrcode-add', 0, 'Tạo mã QR mới'),
(2859, 1023, '', 'Sửa mã QR', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-qrcode-edit', 0, 'Sửa mã QR'),
(2860, 1023, '', 'Xóa mã QR', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-qrcode-delete', 0, 'Xóa mã QR'),
(2861, 1023, '', 'Tải xuống mã QR', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-qrcode-down', 0, 'Tải xuống mã QR'),
(2862, 1023, '', 'Thống kê mã QR', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-qrcode-statistics', 0, 'Thống kê mã QR'),
(2863, 1023, '', 'Danh sách người dùng quét mã', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '27/1023', 3, '', 0, 'channel-qrcode-user', 0, 'Danh sách người dùng quét mã'),
(2864, 2857, '', 'Danh sách danh mục mã kênh', '', '', '', 'app/wechat_qrcode/cate/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-cate-list', 0, 'Danh sách danh mục mã kênh'),
(2865, 2857, '', 'Biểu mẫu thêm/sửa danh mục mã kênh', '', '', '', 'app/wechat_qrcode/cate/create/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-cate-create', 0, 'Biểu mẫu thêm/sửa danh mục mã kênh'),
(2866, 2857, '', 'Lưu danh mục mã kênh', '', '', '', 'app/wechat_qrcode/cate/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-cate-save', 0, 'Lưu danh mục mã kênh'),
(2867, 2857, '', 'Xóa danh mục mã kênh', '', '', '', 'app/wechat_qrcode/cate/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-cate-del', 0, 'Xóa danh mục mã kênh'),
(2868, 2858, '', 'Lưu mã kênh', '', '', '', 'app/wechat_qrcode/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-save', 0, 'Lưu mã kênh'),
(2869, 2858, '', 'Chi tiết mã kênh', '', '', '', 'app/wechat_qrcode/info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-info', 0, 'Chi tiết mã kênh'),
(2870, 2859, '', 'Danh sách mã kênh', '', '', '', 'app/wechat_qrcode/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-list', 0, 'Danh sách mã kênh'),
(2871, 2859, '', 'Chi tiết mã kênh', '', '', '', 'app/wechat_qrcode/info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-info-64671d713f4e3', 0, 'Chi tiết mã kênh'),
(2872, 2859, '', 'Lưu mã kênh', '', '', '', 'app/wechat_qrcode/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-save-64671d713f4e9', 0, 'Lưu mã kênh'),
(2873, 2860, '', 'Xóa mã kênh', '', '', '', 'app/wechat_qrcode/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-del', 0, 'Xóa mã kênh'),
(2874, 2862, '', 'Thống kê mã kênh', '', '', '', 'app/wechat_qrcode/statistic/<qid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-statistic', 0, 'Thống kê mã kênh'),
(2875, 2863, '', 'Danh sách người dùng mã kênh', '', '', '', 'app/wechat_qrcode/user_list/<qid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat_qrcode-user_list', 0, 'Danh sách người dùng mã kênh'),
(2876, 29, '', 'Danh sách người được giới thiệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-user-list', 0, 'Danh sách người được giới thiệu'),
(2877, 29, '', 'Đơn hàng giới thiệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-order-list', 0, 'Đơn hàng giới thiệu'),
(2878, 29, '', 'Mã QR giới thiệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-qrcode', 0, 'Mã QR giới thiệu'),
(2879, 29, '', 'Sửa người giới thiệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-edit-spread', 0, 'Sửa người giới thiệu'),
(2880, 29, '', 'Xóa người giới thiệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-delete-spread', 0, 'Xóa người giới thiệu'),
(2881, 29, '', 'Hủy tư cách cộng tác viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-cancel', 0, 'Hủy tư cách cộng tác viên'),
(2882, 29, '', 'Sửa cấp độ CTV', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/29', 3, '', 0, 'spread-edit-level', 0, 'Sửa cấp độ CTV'),
(2883, 2876, '', 'Danh sách người được giới thiệu', '', '', '', 'agent/stair', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-stair', 0, 'Danh sách người được giới thiệu'),
(2884, 2877, '', 'Danh sách đơn hàng giới thiệu', '', '', '', 'agent/stair/order', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-stair-order', 0, 'Danh sách đơn hàng giới thiệu'),
(2885, 2878, '', 'Xem mã QR giới thiệu Mini Program', '', '', '', 'agent/look_xcx_code', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-look_xcx_code', 0, 'Xem mã QR giới thiệu Mini Program'),
(2886, 2878, '', 'Xem mã QR giới thiệu H5', '', '', '', 'agent/look_h5_code', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-look_h5_code', 0, 'Xem mã QR giới thiệu H5'),
(2887, 2878, '', 'Xem mã QR giới thiệu OA WeChat', '', '', '', 'agent/look_code', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-look_code', 0, 'Xem mã QR giới thiệu OA WeChat'),
(2888, 2879, '', 'Sửa người giới thiệu', '', '', '', 'agent/spread', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-spread-64671e9e67c6d', 0, 'Sửa người giới thiệu'),
(2889, 2880, '', 'Gỡ bỏ người giới thiệu', '', '', '', 'agent/stair/delete_spread/<uid>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-stair-delete_spread', 0, 'Gỡ bỏ người giới thiệu'),
(2890, 896, '', 'Thêm cấp độ CTV', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/896', 3, '', 0, 'spread-level-add', 0, 'Thêm cấp độ CTV'),
(2891, 896, '', 'Sửa cấp độ CTV', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/896', 3, '', 0, 'spread-level-edit', 0, 'Sửa cấp độ CTV'),
(2892, 896, '', 'Xóa cấp độ CTV', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/896', 3, '', 0, 'spread-level-delete', 0, 'Xóa cấp độ CTV'),
(2893, 896, '', 'Nhiệm vụ cấp độ CTV', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/896', 3, '', 0, 'spread-level-task', 0, 'Nhiệm vụ cấp độ CTV'),
(2894, 2890, '', 'Lấy biểu mẫu cấp độ CTV', '', '', '', 'agent/level/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level-create', 0, 'Lấy biểu mẫu cấp độ CTV'),
(2895, 2890, '', 'Lưu cấp độ CTV', '', '', '', 'agent/level', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level', 0, 'Lưu cấp độ CTV'),
(2896, 2891, '', 'Chỉnh sửa cấp độ CTV', '', '', '', 'agent/level/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level-64671f51e1c5d', 0, 'Chỉnh sửa cấp độ CTV'),
(2897, 2891, '', 'Lấy biểu mẫu sửa cấp độ CTV', '', '', '', 'agent/level/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level-edit', 0, 'Lấy biểu mẫu sửa cấp độ CTV'),
(2898, 2891, '', 'Sửa trạng thái cấp độ CTV', '', '', '', 'agent/level/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level-set_status', 0, 'Sửa trạng thái cấp độ CTV'),
(2899, 2892, '', 'Xóa cấp độ CTV', '', '', '', 'agent/level/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level-64671f5ca12b7', 0, 'Xóa cấp độ CTV'),
(2900, 2893, '', 'Lấy danh sách nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task', 0, 'Lấy danh sách nhiệm vụ cấp độ CTV'),
(2901, 2893, '', 'Lưu nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task', 0, 'Lưu nhiệm vụ cấp độ CTV'),
(2902, 2893, '', 'Lấy biểu mẫu nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task-create', 0, 'Lấy biểu mẫu nhiệm vụ cấp độ CTV'),
(2903, 2893, '', 'Lấy biểu mẫu sửa nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task-edit', 0, 'Lấy biểu mẫu sửa nhiệm vụ cấp độ CTV'),
(2904, 2893, '', 'Sửa nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task', 0, 'Sửa nhiệm vụ cấp độ CTV'),
(2905, 2893, '', 'Xóa nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task', 0, 'Xóa nhiệm vụ cấp độ CTV'),
(2906, 2893, '', 'Sửa trạng thái nhiệm vụ cấp độ CTV', '', '', '', 'agent/level_task/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-level_task-set_status', 0, 'Sửa trạng thái nhiệm vụ cấp độ CTV'),
(2907, 1014, '', 'Thêm đại lý khu vực', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1014', 3, '', 0, 'division-add', 0, 'Thêm đại lý khu vực'),
(2908, 1014, '', 'Sửa đại lý khu vực', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1014', 3, '', 0, 'division-edit', 0, 'Sửa đại lý khu vực'),
(2909, 1014, '', 'Xóa đại lý khu vực', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1014', 3, '', 0, 'division-delete', 0, 'Xóa đại lý khu vực'),
(2910, 1014, '', 'Xem đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1014', 3, '', 0, 'division-scan-agent', 0, 'Xem đại lý'),
(2911, 2907, '', 'Thêm đại lý khu vực', '', '', '', 'agent/division/create/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-create', 0, 'Thêm đại lý khu vực'),
(2912, 2907, '', 'Lưu đại lý khu vực', '', '', '', 'agent/division/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-save', 0, 'Lưu đại lý khu vực'),
(2913, 2908, '', 'Thêm đại lý khu vực', '', '', '', 'agent/division/create/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-create-646720ac60991', 0, 'Thêm đại lý khu vực'),
(2914, 2908, '', 'Lưu đại lý khu vực', '', '', '', 'agent/division/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-save-646720ac6099a', 0, 'Lưu đại lý khu vực'),
(2915, 2909, '', 'Xóa đại lý', '', '', '', 'agent/division/del/<type>/<uid>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-del', 0, 'Xóa đại lý'),
(2916, 2910, '', 'Danh sách cấp dưới', '', '', '', 'agent/division/down_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-down_list', 0, 'Danh sách cấp dưới'),
(2917, 1015, '', 'Thêm đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1015', 3, '', 0, 'division-agent-add', 0, 'Thêm đại lý'),
(2918, 1015, '', 'Sửa đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1015', 3, '', 0, 'division-agent-edit', 0, 'Sửa đại lý'),
(2919, 1015, '', 'Xóa đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1015', 3, '', 0, 'division-agent-delete', 0, 'Xóa đại lý'),
(2920, 1015, '', 'Xem nhân viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1015', 3, '', 0, 'division-agent-staff', 0, 'Xem nhân viên'),
(2921, 2917, '', 'Thêm đại lý khu vực', '', '', '', 'agent/division/agent/create/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-agent-create', 0, 'Thêm đại lý khu vực'),
(2922, 2917, '', 'Lưu đại lý khu vực', '', '', '', 'agent/division/agent/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-agent-save', 0, 'Lưu đại lý khu vực'),
(2923, 2918, '', 'Thêm đại lý khu vực', '', '', '', 'agent/division/agent/create/<uid>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-agent-create-64672134e5497', 0, 'Thêm đại lý khu vực'),
(2924, 2918, '', 'Lưu đại lý khu vực', '', '', '', 'agent/division/agent/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-agent-save-64672134e54a1', 0, 'Lưu đại lý khu vực'),
(2925, 2919, '', 'Xóa đại lý', '', '', '', 'agent/division/del/<type>/<uid>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-del-64672140f09bd', 0, 'Xóa đại lý'),
(2926, 2920, '', 'Danh sách cấp dưới', '', '', '', 'agent/division/down_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-down_list-6467214aa534c', 0, 'Danh sách cấp dưới'),
(2927, 1016, '', 'Duyệt đại lý', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '26/1013/1016', 3, '', 0, 'division-agent-apply', 0, 'Duyệt đại lý'),
(2928, 2927, '', 'Danh sách đăng ký đại lý', '', '', '', 'agent/division/agent_apply/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-agent_apply-list', 0, 'Danh sách đăng ký đại lý'),
(2929, 2927, '', 'Biểu mẫu duyệt', '', '', '', 'agent/division/examine_apply/<id>/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-examine_apply', 0, 'Biểu mẫu duyệt'),
(2930, 2927, '', 'Gửi duyệt', '', '', '', 'agent/division/apply_agent/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-apply_agent-save', 0, 'Gửi duyệt'),
(2931, 2927, '', 'Xóa yêu cầu duyệt', '', '', '', 'agent/division/del_apply/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'agent-division-del_apply', 0, 'Xóa yêu cầu duyệt'),
(2932, 678, '', 'Thêm nhân viên CSKH', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/678', 3, '', 0, 'service-add', 0, 'Thêm nhân viên CSKH'),
(2933, 678, '', 'Sửa nhân viên CSKH', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/678', 3, '', 0, 'service-edit', 0, 'Sửa nhân viên CSKH'),
(2934, 678, '', 'Xóa nhân viên CSKH', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/678', 3, '', 0, 'service-delete', 0, 'Xóa nhân viên CSKH'),
(2935, 678, '', 'Vào không gian làm việc', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/678', 3, '', 0, 'service-in', 0, 'Vào không gian làm việc'),
(2936, 2932, '', 'Biểu mẫu thêm nhân viên CSKH', '', '', '', 'app/wechat/kefu/add', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-add', 0, 'Biểu mẫu thêm nhân viên CSKH'),
(2937, 2932, '', 'Biểu mẫu sửa nhân viên CSKH', '', '', '', 'app/wechat/kefu/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-edit', 0, 'Biểu mẫu sửa nhân viên CSKH'),
(2938, 2932, '', 'Thêm nhân viên CSKH', '', '', '', 'app/wechat/kefu', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu', 0, 'Thêm nhân viên CSKH'),
(2939, 2932, '', 'Chỉnh sửa nhân viên CSKH', '', '', '', 'app/wechat/kefu/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu', 0, 'Chỉnh sửa nhân viên CSKH'),
(2940, 2932, '', 'Sửa trạng thái nhân viên CSKH', '', '', '', 'app/wechat/kefu/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-set_status', 0, 'Sửa trạng thái nhân viên CSKH'),
(2941, 2933, '', 'Biểu mẫu sửa nhân viên CSKH', '', '', '', 'app/wechat/kefu/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-edit-646723c8147fd', 0, 'Biểu mẫu sửa nhân viên CSKH'),
(2942, 2933, '', 'Chỉnh sửa nhân viên CSKH', '', '', '', 'app/wechat/kefu/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-646723c814806', 0, 'Chỉnh sửa nhân viên CSKH'),
(2943, 2933, '', 'Sửa trạng thái nhân viên CSKH', '', '', '', 'app/wechat/kefu/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-set_status-646723c81480d', 0, 'Sửa trạng thái nhân viên CSKH'),
(2944, 2934, '', 'Xóa nhân viên CSKH', '', '', '', 'app/wechat/kefu/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-646723d59c121', 0, 'Xóa nhân viên CSKH'),
(2945, 2935, '', 'Đăng nhập CSKH', '', '', '', 'app/wechat/kefu/login/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-kefu-login', 0, 'Đăng nhập CSKH'),
(2946, 679, '', 'Thêm danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/679', 3, '', 0, 'service-speechcraft-cate-add', 0, 'Thêm danh mục'),
(2947, 679, '', 'Thêm câu trả lời mẫu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/679', 3, '', 0, 'service-speechcraft-add', 0, 'Thêm câu trả lời mẫu'),
(2948, 679, '', 'Sửa câu trả lời mẫu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/679', 3, '', 0, 'service-speechcraft-edit', 0, 'Sửa câu trả lời mẫu'),
(2949, 679, '', 'Xóa câu trả lời mẫu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/679', 3, '', 0, 'service-speechcraft-delete', 0, 'Xóa câu trả lời mẫu'),
(2950, 2946, '', 'Lấy danh sách danh mục câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraftcate', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraftcate', 0, 'Lấy danh sách danh mục câu trả lời mẫu CSKH'),
(2951, 2946, '', 'Lưu danh mục câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraftcate', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraftcate', 0, 'Lưu danh mục câu trả lời mẫu CSKH'),
(2952, 2946, '', 'Lấy biểu mẫu danh mục câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraftcate/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraftcate-create', 0, 'Lấy biểu mẫu danh mục câu trả lời mẫu CSKH'),
(2953, 2946, '', 'Lấy biểu mẫu sửa danh mục câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraftcate/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraftcate-edit', 0, 'Lấy biểu mẫu sửa danh mục câu trả lời mẫu CSKH'),
(2954, 2946, '', 'Sửa danh mục câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraftcate/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraftcate', 0, 'Sửa danh mục câu trả lời mẫu CSKH'),
(2955, 2947, '', 'Lưu câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraft', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraft', 0, 'Lưu câu trả lời mẫu CSKH'),
(2956, 2947, '', 'Lấy biểu mẫu câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraft/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraft-create', 0, 'Lấy biểu mẫu câu trả lời mẫu CSKH'),
(2957, 2948, '', 'Lấy biểu mẫu sửa câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraft/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraft-edit', 0, 'Lấy biểu mẫu sửa câu trả lời mẫu CSKH'),
(2958, 2948, '', 'Sửa câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraft/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraft-646726ba5bc91', 0, 'Sửa câu trả lời mẫu CSKH'),
(2959, 2949, '', 'Xóa câu trả lời mẫu CSKH', '', '', '', 'app/wechat/speechcraft/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-speechcraft-6467272d0c6c4', 0, 'Xóa câu trả lời mẫu CSKH'),
(2960, 738, '', 'Trả lời lời nhắn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/738', 3, '', 0, 'service-feedback-reply', 0, 'Trả lời lời nhắn'),
(2961, 738, '', 'Xóa lời nhắn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '165/738', 3, '', 0, 'service-feedback-delete', 0, 'Xóa lời nhắn'),
(2962, 2960, '', 'Lấy biểu mẫu sửa phản hồi người dùng', '', '', '', 'app/feedback/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-feedback-edit', 0, 'Lấy biểu mẫu sửa phản hồi người dùng'),
(2963, 2960, '', 'Sửa phản hồi người dùng', '', '', '', 'app/feedback/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-feedback', 0, 'Sửa phản hồi người dùng'),
(2964, 2961, '', 'Xóa phản hồi người dùng', '', '', '', 'app/feedback/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-feedback-64672949545ff', 0, 'Xóa phản hồi người dùng'),
(2965, 39, '', 'Duyệt rút tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/36/39', 3, '', 0, 'extract-status', 0, 'Duyệt rút tiền'),
(2966, 39, '', 'Sửa yêu cầu rút tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/36/39', 3, '', 0, 'extract-edit', 0, 'Sửa yêu cầu rút tiền'),
(2967, 2965, '', 'Từ chối yêu cầu rút tiền', '', '', '', 'finance/extract/refuse/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-extract-refuse', 0, 'Từ chối yêu cầu rút tiền'),
(2968, 2965, '', 'Duyệt yêu cầu rút tiền', '', '', '', 'finance/extract/adopt/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-extract-adopt', 0, 'Duyệt yêu cầu rút tiền'),
(2969, 2966, '', 'Sửa bản ghi rút tiền', '', '', '', 'finance/extract/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-extract', 0, 'Sửa bản ghi rút tiền'),
(2970, 2966, '', 'Biểu mẫu sửa bản ghi rút tiền', '', '', '', 'finance/extract/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-extract-edit', 0, 'Biểu mẫu sửa bản ghi rút tiền'),
(2971, 767, '', 'Sửa hóa đơn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/36/767', 3, '', 0, 'invoice-edit', 0, 'Sửa hóa đơn'),
(2972, 767, '', 'Thông tin đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/36/767', 3, '', 0, 'invoice-order-info', 0, 'Thông tin đơn hàng'),
(2973, 2971, '', 'Danh sách yêu cầu xuất hóa đơn', '', '', '', 'order/invoice/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-invoice-list', 0, 'Danh sách yêu cầu xuất hóa đơn'),
(2974, 2971, '', 'Đặt trạng thái hóa đơn', '', '', '', 'order/invoice/set/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-invoice-set', 0, 'Đặt trạng thái hóa đơn'),
(2975, 2972, '', 'Chi tiết đơn hàng xuất hóa đơn', '', '', '', 'order/invoice_order_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-invoice_order_info', 0, 'Chi tiết đơn hàng xuất hóa đơn'),
(2976, 40, '', 'Xóa nạp tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/37/40', 3, '', 0, 'recharge-delete', 0, 'Xóa nạp tiền'),
(2977, 40, '', 'Hoàn tiền nạp', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/37/40', 3, '', 0, 'recharge-refund', 0, 'Hoàn tiền nạp'),
(2978, 2976, '', 'Xóa bản ghi nạp tiền', '', '', '', 'finance/recharge/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-recharge', 0, 'Xóa bản ghi nạp tiền'),
(2979, 2976, '', 'Danh sách lịch sử nạp tiền', '', '', '', 'finance/recharge', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-recharge', 0, 'Danh sách lịch sử nạp tiền'),
(2980, 2977, '', 'Hoàn tiền nạp', '', '', '', 'finance/recharge/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-recharge-64672c72c2273', 0, 'Hoàn tiền nạp'),
(2981, 2977, '', 'Biểu mẫu hoàn tiền nạp', '', '', '', 'finance/recharge/<id>/refund_edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'finance-recharge-refund_edit', 0, 'Biểu mẫu hoàn tiền nạp'),
(2982, 998, '', 'Ghi chú dòng tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/37/998', 3, '', 0, 'capital-flow-mark', 0, 'Ghi chú dòng tiền'),
(2983, 2982, '', 'Đặt ghi chú', '', '', '', 'statistic/flow/set_mark/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'statistic-flow-set_mark', 0, 'Đặt ghi chú'),
(2984, 999, '', 'Chi tiết sao kê', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/37/999', 3, '', 0, 'billing-info', 0, 'Chi tiết sao kê'),
(2985, 999, '', 'Tải xuống sao kê', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '35/37/999', 3, '', 0, 'billing-down', 0, 'Tải xuống sao kê'),
(2986, 2984, '', 'Lịch sử sao kê', '', '', '', 'statistic/flow/get_record', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'statistic-flow-get_record', 0, 'Lịch sử sao kê'),
(2987, 2985, '', 'Dòng tiền', '', '', '', 'statistic/flow/get_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'statistic-flow-get_list', 0, 'Dòng tiền'),
(2988, 44, '', 'Thêm bài viết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/44', 3, '', 0, 'cms-add', 0, 'Thêm bài viết'),
(2989, 44, '', 'Sửa bài viết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/44', 3, '', 0, 'cms-edit', 0, 'Sửa bài viết'),
(2990, 44, '', 'Xóa bài viết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/44', 3, '', 0, 'cms-delete', 0, 'Xóa bài viết'),
(2991, 44, '', 'Liên kết sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/44', 3, '', 0, 'cms-product', 0, 'Liên kết sản phẩm'),
(2992, 2988, '', 'Lưu bài viết', '', '', '', 'cms/cms', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms', 0, 'Lưu bài viết'),
(2993, 2988, '', 'Lấy biểu mẫu bài viết', '', '', '', 'cms/cms/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-create', 0, 'Lấy biểu mẫu bài viết'),
(2994, 2989, '', 'Lấy biểu mẫu sửa bài viết', '', '', '', 'cms/cms/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-edit', 0, 'Lấy biểu mẫu sửa bài viết'),
(2995, 2989, '', 'Lấy thông tin chi tiết bài viết', '', '', '', 'cms/cms/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-64673315dd264', 0, 'Lấy thông tin chi tiết bài viết'),
(2996, 2989, '', 'Chỉnh sửa bài viết', '', '', '', 'cms/cms/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-64673315dd287', 0, 'Chỉnh sửa bài viết'),
(2997, 2990, '', 'Xóa bài viết', '', '', '', 'cms/cms/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-6467331dc244e', 0, 'Xóa bài viết'),
(2998, 2991, '', 'Hủy liên kết sản phẩm với bài viết', '', '', '', 'cms/cms/unrelation/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-unrelation', 0, 'Hủy liên kết sản phẩm với bài viết'),
(2999, 2991, '', 'Liên kết sản phẩm với bài viết', '', '', '', 'cms/cms/relation/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-cms-relation', 0, 'Liên kết sản phẩm với bài viết'),
(3000, 45, '', 'Thêm danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/45', 3, '', 0, 'cms-cate-add', 0, 'Thêm danh mục'),
(3001, 45, '', 'Sửa danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/45', 3, '', 0, 'cms-cate-edit', 0, 'Sửa danh mục'),
(3002, 45, '', 'Xóa danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/45', 3, '', 0, 'cms-cate-delete', 0, 'Xóa danh mục'),
(3003, 45, '', 'Xem bài viết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '43/45', 3, '', 0, 'cms-cate-cms', 0, 'Xem bài viết'),
(3004, 3000, '', 'Lưu danh mục bài viết', '', '', '', 'cms/category', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-category', 0, 'Lưu danh mục bài viết'),
(3005, 3000, '', 'Lấy biểu mẫu danh mục bài viết', '', '', '', 'cms/category/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-category-create', 0, 'Lấy biểu mẫu danh mục bài viết'),
(3006, 3001, '', 'Sửa danh mục bài viết', '', '', '', 'cms/category/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-category-646733862d224', 0, 'Sửa danh mục bài viết'),
(3007, 3001, '', 'Lấy biểu mẫu sửa danh mục bài viết', '', '', '', 'cms/category/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-category-edit', 0, 'Lấy biểu mẫu sửa danh mục bài viết'),
(3008, 3002, '', 'Xóa danh mục bài viết', '', '', '', 'cms/category/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'cms-category-646733a5e8994', 0, 'Xóa danh mục bài viết'),
(3009, 657, '', 'Trang chủ cửa hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657', 3, '', 0, 'pages-diy-index', 0, 'Trang chủ cửa hàng'),
(3010, 657, '', 'Danh mục sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657', 3, '', 0, 'pages-diy-cate', 0, 'Danh mục sản phẩm'),
(3011, 657, '', 'Trang cá nhân', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657', 3, '', 0, 'pages-diy-user', 0, 'Trang cá nhân'),
(3012, 3009, '', 'Thêm trang chuyên đề', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657/3009', 3, '', 0, 'pages-diy-add', 0, 'Thêm trang chuyên đề'),
(3013, 3009, '', 'Sửa trang', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657/3009', 3, '', 0, 'pages-diy-edit', 0, 'Sửa trang'),
(3014, 3009, '', 'Xóa trang', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657/3009', 3, '', 0, 'pages-diy-delete', 0, 'Xóa trang'),
(3015, 3009, '', 'Đặt làm trang chủ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657/3009', 3, '', 0, 'pages-diy-status', 0, 'Đặt làm trang chủ'),
(3016, 3012, '', 'Thêm mẫu DIY', '', '', '', 'diy/save/<id?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-save', 0, 'Thêm mẫu DIY'),
(3017, 3012, '', 'Thêm mẫu DIY', '', '', '', 'diy/diy_save/<id?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-diy_save', 0, 'Thêm mẫu DIY'),
(3018, 3012, '', 'Lấy đường dẫn trang phía người dùng', '', '', '', 'diy/get_url', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_url', 0, 'Lấy đường dẫn trang phía người dùng'),
(3019, 3012, '', 'Lấy danh mục sản phẩm', '', '', '', 'diy/get_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_category', 0, 'Lấy danh mục sản phẩm'),
(3020, 3012, '', 'Lấy danh sách sản phẩm', '', '', '', 'diy/get_product', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_product', 0, 'Lấy danh sách sản phẩm'),
(3021, 3012, '', 'Lấy trạng thái bật nhận tại cửa hàng', '', '', '', 'diy/get_store_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_store_status', 0, 'Lấy trạng thái bật nhận tại cửa hàng'),
(3022, 3012, '', 'Khôi phục dữ liệu mặc định Diy', '', '', '', 'diy/recovery/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-recovery', 0, 'Khôi phục dữ liệu mặc định Diy'),
(3023, 3012, '', 'Lấy tất cả danh mục cấp 2', '', '', '', 'diy/get_by_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_by_category', 0, 'Lấy tất cả danh mục cấp 2'),
(3024, 3012, '', 'Đặt dữ liệu mặc định Diy', '', '', '', 'diy/set_recovery/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-set_recovery', 0, 'Đặt dữ liệu mặc định Diy'),
(3025, 3012, '', 'Lấy danh sách sản phẩm', '', '', '', 'diy/get_product_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_product_list', 0, 'Lấy danh sách sản phẩm'),
(3026, 3012, '', 'Lấy liên kết trang', '', '', '', 'diy/get_page_link/<cate_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_page_link', 0, 'Lấy liên kết trang'),
(3027, 3012, '', 'Lấy danh mục liên kết trang', '', '', '', 'diy/get_page_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_page_category', 0, 'Lấy danh mục liên kết trang'),
(3028, 3012, '', 'Thêm DIY', '', '', '', 'diy/create', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-create', 0, 'Thêm DIY'),
(3029, 3012, '', 'Biểu mẫu thêm', '', '', '', 'diy/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-create', 0, 'Biểu mẫu thêm'),
(3030, 3012, '', 'Chi tiết dữ liệu mẫu Diy', '', '', '', 'diy/get_diy_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_diy_info', 0, 'Chi tiết dữ liệu mẫu Diy'),
(3031, 3012, '', 'Xóa mẫu DIY', '', '', '', 'diy/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-del', 0, 'Xóa mẫu DIY'),
(3032, 3012, '', 'Chi tiết dữ liệu mẫu Diy', '', '', '', 'diy/get_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_info', 0, 'Chi tiết dữ liệu mẫu Diy'),
(3033, 3013, '', 'Chi tiết dữ liệu mẫu Diy', '', '', '', 'diy/get_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_info-646a1a8c6b2bf', 0, 'Chi tiết dữ liệu mẫu Diy'),
(3034, 3013, '', 'Chi tiết dữ liệu mẫu Diy', '', '', '', 'diy/get_diy_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_diy_info-646a1a8c6b2c6', 0, 'Chi tiết dữ liệu mẫu Diy'),
(3035, 3013, '', 'Sử dụng mẫu DIY', '', '', '', 'diy/set_status/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-set_status', 0, 'Sử dụng mẫu DIY'),
(3036, 3013, '', 'Biểu mẫu thêm', '', '', '', 'diy/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-create-646a1a8c6b2cf', 0, 'Biểu mẫu thêm'),
(3037, 3013, '', 'Thêm DIY', '', '', '', 'diy/create', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-create-646a1a8c6b2d2', 0, 'Thêm DIY'),
(3038, 3013, '', 'Thêm mẫu DIY', '', '', '', 'diy/save/<id?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-save-646a1a8c6b2d6', 0, 'Thêm mẫu DIY'),
(3039, 3013, '', 'Lấy đường dẫn trang phía người dùng', '', '', '', 'diy/get_url', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_url-646a1a8c6b2da', 0, 'Lấy đường dẫn trang phía người dùng'),
(3040, 3013, '', 'Thêm mẫu DIY', '', '', '', 'diy/diy_save/<id?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-diy_save-646a1a8c6b2de', 0, 'Thêm mẫu DIY'),
(3041, 3013, '', 'Lấy danh mục sản phẩm', '', '', '', 'diy/get_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_category-646a1a8c6b2e2', 0, 'Lấy danh mục sản phẩm'),
(3042, 3013, '', 'Lấy danh sách sản phẩm', '', '', '', 'diy/get_product', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_product-646a1a8c6b2e6', 0, 'Lấy danh sách sản phẩm'),
(3043, 3013, '', 'Khôi phục dữ liệu mặc định Diy', '', '', '', 'diy/recovery/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-recovery-646a1a8c6b2ea', 0, 'Khôi phục dữ liệu mặc định Diy'),
(3044, 3013, '', 'Lấy trạng thái bật nhận tại cửa hàng', '', '', '', 'diy/get_store_status', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_store_status-646a1a8c6b2ed', 0, 'Lấy trạng thái bật nhận tại cửa hàng'),
(3045, 3013, '', 'Lấy tất cả danh mục cấp 2', '', '', '', 'diy/get_by_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_by_category-646a1a8c6b2f1', 0, 'Lấy tất cả danh mục cấp 2'),
(3046, 3013, '', 'Đặt dữ liệu mặc định Diy', '', '', '', 'diy/set_recovery/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-set_recovery-646a1a8c6b2f5', 0, 'Đặt dữ liệu mặc định Diy'),
(3047, 3013, '', 'Lấy danh sách sản phẩm', '', '', '', 'diy/get_product_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_product_list-646a1a8c6b2f9', 0, 'Lấy danh sách sản phẩm'),
(3048, 3013, '', 'Lấy danh mục liên kết trang', '', '', '', 'diy/get_page_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_page_category-646a1a8c6b2fd', 0, 'Lấy danh mục liên kết trang'),
(3049, 3013, '', 'Lấy liên kết trang', '', '', '', 'diy/get_page_link/<cate_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_page_link-646a1a8c6b301', 0, 'Lấy liên kết trang'),
(3050, 3014, '', 'Xóa mẫu DIY', '', '', '', 'diy/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-del-646a1a97c43d4', 0, 'Xóa mẫu DIY'),
(3051, 3015, '', 'Sử dụng mẫu DIY', '', '', '', 'diy/set_status/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-set_status-646a1aa862dae', 0, 'Sử dụng mẫu DIY'),
(3052, 3010, '', 'Chuyển đổi trang danh mục', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657/3010', 3, '', 0, 'pages-diy-cate-status', 0, 'Chuyển đổi trang danh mục'),
(3053, 3052, '', 'Lấy cài đặt phong cách', '', '', '', 'diy/get_color_change/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_color_change', 0, 'Lấy cài đặt phong cách'),
(3054, 3052, '', 'Lưu đổi màu và danh mục', '', '', '', 'diy/color_change/<status>/<type>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-color_change', 0, 'Lưu đổi màu và danh mục'),
(3055, 3011, '', 'Lưu trang cá nhân', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/657/3011', 3, '', 0, 'pages-diy-member-save', 0, 'Lưu trang cá nhân'),
(3056, 3055, '', 'Lưu trang cá nhân', '', '', '', 'diy/member_save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-member_save', 0, 'Lưu trang cá nhân'),
(3057, 3055, '', 'Lấy liên kết trang', '', '', '', 'diy/get_page_link/<cate_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_page_link-646a1b603f626', 0, 'Lấy liên kết trang'),
(3058, 3055, '', 'Lấy danh mục liên kết trang', '', '', '', 'diy/get_page_category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_page_category-646a1b603f62f', 0, 'Lấy danh mục liên kết trang'),
(3059, 3055, '', 'Chi tiết trang cá nhân', '', '', '', 'diy/get_member', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_member', 0, 'Chi tiết trang cá nhân'),
(3060, 902, '', 'Đổi chủ đề', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/902', 3, '', 0, 'pages-theme-status', 0, 'Đổi chủ đề'),
(3061, 3060, '', 'Lưu đổi màu và danh mục', '', '', '', 'diy/color_change/<status>/<type>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-color_change-646a1bdde013b', 0, 'Lưu đổi màu và danh mục'),
(3062, 3060, '', 'Lấy cài đặt phong cách', '', '', '', 'diy/get_color_change/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'diy-get_color_change-646a1bdde0144', 0, 'Lấy cài đặt phong cách'),
(3063, 566, '', 'Thêm danh mục tư liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/566', 3, '', 0, 'system-file-cate-add', 0, 'Thêm danh mục tư liệu'),
(3064, 566, '', 'Xóa danh mục tư liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/566', 3, '', 0, 'system-file-cate-delete', 0, 'Xóa danh mục tư liệu'),
(3065, 566, '', 'Tải lên tư liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/566', 3, '', 0, 'system-file-add', 0, 'Tải lên tư liệu'),
(3066, 566, '', 'Xóa tư liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/656/566', 3, '', 0, 'system-file-delete', 0, 'Xóa tư liệu'),
(3067, 3063, '', 'Lấy danh sách danh mục tệp đính kèm', '', '', '', 'file/category', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-category', 0, 'Lấy danh sách danh mục tệp đính kèm'),
(3068, 3063, '', 'Lưu danh mục tệp đính kèm', '', '', '', 'file/category', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-category', 0, 'Lưu danh mục tệp đính kèm'),
(3069, 3063, '', 'Lấy biểu mẫu danh mục tệp đính kèm', '', '', '', 'file/category/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-category-create', 0, 'Lấy biểu mẫu danh mục tệp đính kèm'),
(3070, 3063, '', 'Lấy biểu mẫu sửa danh mục tệp đính kèm', '', '', '', 'file/category/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-category-edit', 0, 'Lấy biểu mẫu sửa danh mục tệp đính kèm'),
(3071, 3063, '', 'Sửa danh mục tệp đính kèm', '', '', '', 'file/category/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-category', 0, 'Sửa danh mục tệp đính kèm'),
(3072, 3064, '', 'Xóa danh mục tệp đính kèm', '', '', '', 'file/category/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-category-646a1ca8e74c5', 0, 'Xóa danh mục tệp đính kèm'),
(3073, 3065, '', 'Danh sách ảnh đính kèm', '', '', '', 'file/file', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-file', 0, 'Danh sách ảnh đính kèm'),
(3074, 3065, '', 'Biểu mẫu chuyển danh mục ảnh', '', '', '', 'file/file/move', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-file-move', 0, 'Biểu mẫu chuyển danh mục ảnh'),
(3075, 3065, '', 'Chuyển danh mục ảnh', '', '', '', 'file/file/do_move', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-file-do_move', 0, 'Chuyển danh mục ảnh'),
(3076, 3065, '', 'Sửa tên ảnh', '', '', '', 'file/file/update/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-file-update', 0, 'Sửa tên ảnh'),
(3077, 3065, '', 'Tải lên ảnh', '', '', '', 'file/upload/<upload_type?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-upload', 0, 'Tải lên ảnh'),
(3078, 3065, '', 'Loại tải lên', '', '', '', 'file/upload_type', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-upload_type', 0, 'Loại tải lên'),
(3079, 3065, '', 'Tải lên video cục bộ theo phân đoạn', '', '', '', 'file/video_upload', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-video_upload', 0, 'Tải lên video cục bộ theo phân đoạn'),
(3080, 3066, '', 'Xóa ảnh', '', '', '', 'file/file/delete', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'file-file-delete', 0, 'Xóa ảnh'),
(3081, 92, '', 'Lưu và đăng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/92', 3, '', 0, 'wechat-menu-save', 0, 'Lưu và đăng'),
(3082, 3081, '', 'Danh sách menu OA WeChat', '', '', '', 'app/wechat/menu', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-menu', 0, 'Danh sách menu OA WeChat'),
(3083, 3081, '', 'Lưu menu OA WeChat', '', '', '', 'app/wechat/menu', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-menu', 0, 'Lưu menu OA WeChat'),
(3084, 109, '', 'Thêm tin bài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/109', 3, '', 0, 'wechat-news-add', 0, 'Thêm tin bài'),
(3085, 109, '', 'Sửa tin bài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/109', 3, '', 0, 'wechat-news-edit', 0, 'Sửa tin bài'),
(3086, 109, '', 'Xóa tin bài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/109', 3, '', 0, 'wechat-news-delete', 0, 'Xóa tin bài'),
(3087, 3084, '', 'Danh sách tin bài', '', '', '', 'app/wechat/news', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news', 0, 'Danh sách tin bài'),
(3088, 3084, '', 'Lưu tin bài', '', '', '', 'app/wechat/news', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news', 0, 'Lưu tin bài'),
(3089, 3084, '', 'Chi tiết tin bài', '', '', '', 'app/wechat/news/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news', 0, 'Chi tiết tin bài'),
(3090, 3085, '', 'Lưu tin bài', '', '', '', 'app/wechat/news', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news-646a1d9c1d008', 0, 'Lưu tin bài'),
(3091, 3085, '', 'Danh sách tin bài', '', '', '', 'app/wechat/news', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news-646a1d9c1d011', 0, 'Danh sách tin bài'),
(3092, 3085, '', 'Chi tiết tin bài', '', '', '', 'app/wechat/news/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news-646a1d9c1d017', 0, 'Chi tiết tin bài'),
(3093, 3086, '', 'Xóa tin bài', '', '', '', 'app/wechat/news/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-news-646a1da7936d6', 0, 'Xóa tin bài'),
(3094, 113, '', 'Lưu và đăng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/114/113', 3, '', 0, 'wechat-follow-save', 0, 'Lưu và đăng'),
(3095, 115, '', 'Lưu và đăng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/114/115', 3, '', 0, 'wechat-keyword-save', 0, 'Lưu và đăng'),
(3096, 116, '', 'Lưu và trả lời', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/69/114/116', 3, '', 0, 'wechat-default-save', 0, 'Lưu và trả lời'),
(3097, 3094, '', 'Danh sách trả lời theo từ khóa', '', '', '', 'app/wechat/keyword', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword', 0, 'Danh sách trả lời theo từ khóa'),
(3098, 3094, '', 'Lưu trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword', 0, 'Lưu trả lời theo từ khóa'),
(3099, 3094, '', 'Chi tiết trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword', 0, 'Chi tiết trả lời theo từ khóa'),
(3100, 3094, '', 'Xóa trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword', 0, 'Xóa trả lời theo từ khóa'),
(3101, 3094, '', 'Sửa trạng thái trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-set_status', 0, 'Sửa trạng thái trả lời theo từ khóa'),
(3102, 3095, '', 'Danh sách trả lời theo từ khóa', '', '', '', 'app/wechat/keyword', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e33e50c2', 0, 'Danh sách trả lời theo từ khóa'),
(3103, 3095, '', 'Lưu trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e33e50cc', 0, 'Lưu trả lời theo từ khóa'),
(3104, 3095, '', 'Chi tiết trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e33e50d2', 0, 'Chi tiết trả lời theo từ khóa'),
(3105, 3095, '', 'Xóa trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e33e50da', 0, 'Xóa trả lời theo từ khóa'),
(3106, 3095, '', 'Sửa trạng thái trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-set_status-646a1e33e50e0', 0, 'Sửa trạng thái trả lời theo từ khóa'),
(3107, 3096, '', 'Danh sách trả lời theo từ khóa', '', '', '', 'app/wechat/keyword', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e43aba07', 0, 'Danh sách trả lời theo từ khóa'),
(3108, 3096, '', 'Lưu trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e43aba11', 0, 'Lưu trả lời theo từ khóa'),
(3109, 3096, '', 'Sửa trạng thái trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-set_status-646a1e43aba18', 0, 'Sửa trạng thái trả lời theo từ khóa'),
(3110, 3096, '', 'Xóa trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e43aba1e', 0, 'Xóa trả lời theo từ khóa'),
(3111, 3096, '', 'Chi tiết trả lời theo từ khóa', '', '', '', 'app/wechat/keyword/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-keyword-646a1e43aba24', 0, 'Chi tiết trả lời theo từ khóa'),
(3112, 994, '', 'Tải xuống mã Mini Program', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/993/994', 3, '', 0, 'routine-down-qrcode', 0, 'Tải xuống mã Mini Program'),
(3113, 994, '', 'Tải xuống gói Mini Program', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '135/993/994', 3, '', 0, 'routine-down-file', 0, 'Tải xuống gói Mini Program'),
(3114, 3112, '', 'Tải xuống dữ liệu trang Mini Program', '', '', '', 'app/routine/info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-routine-info', 0, 'Tải xuống dữ liệu trang Mini Program'),
(3115, 3113, '', 'Tải xuống dữ liệu trang Mini Program', '', '', '', 'app/routine/info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-routine-info-646a1ed2ee6e0', 0, 'Tải xuống dữ liệu trang Mini Program'),
(3116, 3113, '', 'Tải xuống mẫu Mini Program', '', '', '', 'app/routine/download', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-routine-download', 0, 'Tải xuống mẫu Mini Program'),
(3118, 898, '', 'Đồng bộ thông báo', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/898', 3, '', 0, 'notification-sync', 0, 'Đồng bộ thông báo'),
(3119, 898, '', 'Cài đặt thông báo', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/898', 3, '', 0, 'notification-setting', 0, 'Cài đặt thông báo'),
(3120, 898, '', 'Trạng thái thông báo', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/898', 3, '', 0, 'notification-status', 0, 'Trạng thái thông báo'),
(3121, 3118, '', 'Đồng bộ nhanh tin nhắn đăng ký', '', '', '', 'app/routine/syncSubscribe', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-routine-syncSubscribe', 0, 'Đồng bộ nhanh tin nhắn đăng ký'),
(3122, 3118, '', 'Đồng bộ nhanh tin nhắn mẫu', '', '', '', 'app/wechat/syncSubscribe', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'app-wechat-syncSubscribe', 0, 'Đồng bộ nhanh tin nhắn mẫu'),
(3123, 3119, '', 'Danh sách thông báo hệ thống', '', '', '', 'setting/notification/index', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-notification-index', 0, 'Danh sách thông báo hệ thống'),
(3124, 3119, '', 'Lưu cài đặt thông báo', '', '', '', 'setting/notification/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-notification-save', 0, 'Lưu cài đặt thông báo'),
(3125, 3119, '', 'Lấy dữ liệu một thông báo', '', '', '', 'setting/notification/info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-notification-info', 0, 'Lấy dữ liệu một thông báo'),
(3126, 3120, '', 'Sửa trạng thái thông báo', '', '', '', 'setting/notification/set_status/<type>/<status>/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-notification-set_status', 0, 'Sửa trạng thái thông báo'),
(3127, 1061, '', 'Lưu thỏa thuận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1061', 3, '', 0, 'agreement-save', 0, 'Lưu thỏa thuận'),
(3128, 3127, '', 'Lấy nội dung thỏa thuận', '', '', '', 'setting/get_agreement/<type>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-get_agreement', 0, 'Lấy nội dung thỏa thuận'),
(3129, 3127, '', 'Lấy thông tin bản quyền', '', '', '', 'setting/get_version', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-get_version', 0, 'Lấy thông tin bản quyền'),
(3130, 3127, '', 'Đặt nội dung thỏa thuận', '', '', '', 'setting/save_agreement', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-save_agreement', 0, 'Đặt nội dung thỏa thuận'),
(3131, 19, '', 'Thêm vai trò', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/19', 3, '', 0, 'system-role-add', 0, 'Thêm vai trò'),
(3132, 19, '', 'Sửa vai trò', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/19', 3, '', 0, 'system-role-edit', 0, 'Sửa vai trò'),
(3133, 19, '', 'Xóa vai trò', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/19', 3, '', 0, 'system-role-delete', 0, 'Xóa vai trò'),
(3134, 19, '', 'Trạng thái vai trò', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/19', 3, '', 0, 'system-role-status', 0, 'Trạng thái vai trò'),
(3135, 3131, '', 'Danh sách vai trò quản trị viên', '', '', '', 'setting/role', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role', 0, 'Danh sách vai trò quản trị viên'),
(3136, 3131, '', 'Danh sách quyền theo vai trò quản trị viên', '', '', '', 'setting/role/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-create', 0, 'Danh sách quyền theo vai trò quản trị viên'),
(3137, 3131, '', 'Tạo mới hoặc sửa quản trị viên', '', '', '', 'setting/role/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role', 0, 'Tạo mới hoặc sửa quản trị viên'),
(3138, 3132, '', 'Danh sách vai trò quản trị viên', '', '', '', 'setting/role', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-646a206a0dd01', 0, 'Danh sách vai trò quản trị viên'),
(3139, 3132, '', 'Sửa chi tiết quản trị viên', '', '', '', 'setting/role/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-edit', 0, 'Sửa chi tiết quản trị viên'),
(3140, 3132, '', 'Tạo mới hoặc sửa quản trị viên', '', '', '', 'setting/role/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-646a206a0dd15', 0, 'Tạo mới hoặc sửa quản trị viên'),
(3141, 3133, '', 'Danh sách vai trò quản trị viên', '', '', '', 'setting/role', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-646a2078367e6', 0, 'Danh sách vai trò quản trị viên'),
(3142, 3133, '', 'Xóa vai trò quản trị viên', '', '', '', 'setting/role/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-646a2078367ef', 0, 'Xóa vai trò quản trị viên'),
(3143, 3134, '', 'Danh sách vai trò quản trị viên', '', '', '', 'setting/role', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-646a2083c8f0b', 0, 'Danh sách vai trò quản trị viên'),
(3144, 3134, '', 'Sửa trạng thái vai trò quản trị viên', '', '', '', 'setting/role/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-role-set_status', 0, 'Sửa trạng thái vai trò quản trị viên'),
(3145, 20, '', 'Thêm quản trị viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/20', 3, '', 0, 'system-admin-add', 0, 'Thêm quản trị viên'),
(3146, 20, '', 'Sửa quản trị viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/20', 3, '', 0, 'system-admin-edit', 0, 'Sửa quản trị viên'),
(3147, 20, '', 'Xóa quản trị viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/20', 3, '', 0, 'system-admin-delete', 0, 'Xóa quản trị viên'),
(3148, 20, '', 'Trạng thái quản trị viên', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/20', 3, '', 0, 'system-admin-status', 0, 'Trạng thái quản trị viên'),
(3149, 3145, '', 'Lấy danh sách quản trị viên', '', '', '', 'setting/admin', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin', 0, 'Lấy danh sách quản trị viên'),
(3150, 3145, '', 'Lưu quản trị viên', '', '', '', 'setting/admin', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin', 0, 'Lưu quản trị viên'),
(3151, 3145, '', 'Lấy biểu mẫu quản trị viên', '', '', '', 'setting/admin/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-create', 0, 'Lấy biểu mẫu quản trị viên'),
(3152, 3146, '', 'Lấy danh sách quản trị viên', '', '', '', 'setting/admin', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-646a213532f4c', 0, 'Lấy danh sách quản trị viên'),
(3153, 3146, '', 'Lấy biểu mẫu sửa quản trị viên', '', '', '', 'setting/admin/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-edit', 0, 'Lấy biểu mẫu sửa quản trị viên'),
(3154, 3146, '', 'Sửa quản trị viên', '', '', '', 'setting/admin/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-646a213532f5f', 0, 'Sửa quản trị viên'),
(3155, 3147, '', 'Xóa quản trị viên', '', '', '', 'setting/admin/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-646a2141d9a56', 0, 'Xóa quản trị viên'),
(3156, 3147, '', 'Lấy danh sách quản trị viên', '', '', '', 'setting/admin', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-646a2141d9a61', 0, 'Lấy danh sách quản trị viên'),
(3157, 3148, '', 'Lấy danh sách quản trị viên', '', '', '', 'setting/admin', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-admin-646a214f14bed', 0, 'Lấy danh sách quản trị viên'),
(3158, 3148, '', 'Sửa trạng thái quản trị viên', '', '', '', 'setting/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-set_status', 0, 'Sửa trạng thái quản trị viên'),
(3159, 21, '', 'Sửa menu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/14/21', 3, '', 0, 'system-menu-edit', 0, 'Sửa menu'),
(3160, 3159, '', 'Lấy biểu mẫu sửa menu quyền', '', '', '', 'setting/menus/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-edit', 0, 'Lấy biểu mẫu sửa menu quyền'),
(3161, 3159, '', 'Sửa menu quyền', '', '', '', 'setting/menus/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus', 0, 'Sửa menu quyền'),
(3162, 3159, '', 'Xem thông tin menu quyền', '', '', '', 'setting/menus/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus', 0, 'Xem thông tin menu quyền'),
(3163, 720, '', 'Thêm nhân viên giao hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/720', 3, '', 0, 'delivery-service-add', 0, 'Thêm nhân viên giao hàng'),
(3164, 720, '', 'Sửa nhân viên giao hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/720', 3, '', 0, 'delivery-service-edit', 0, 'Sửa nhân viên giao hàng'),
(3165, 720, '', 'Xóa nhân viên giao hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/720', 3, '', 0, 'delivery-service-delete', 0, 'Xóa nhân viên giao hàng'),
(3166, 720, '', 'Trạng thái nhân viên giao hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/720', 3, '', 0, 'delivery-service-status', 0, 'Trạng thái nhân viên giao hàng'),
(3167, 3163, '', 'Danh sách nhân viên giao hàng', '', '', '', 'order/delivery/index', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-index', 0, 'Danh sách nhân viên giao hàng'),
(3168, 3163, '', 'Biểu mẫu thêm nhân viên giao hàng', '', '', '', 'order/delivery/add', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-add', 0, 'Biểu mẫu thêm nhân viên giao hàng'),
(3169, 3163, '', 'Lưu nhân viên giao hàng mới tạo', '', '', '', 'order/delivery/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-save', 0, 'Lưu nhân viên giao hàng mới tạo'),
(3170, 3164, '', 'Biểu mẫu sửa nhân viên giao hàng', '', '', '', 'order/delivery/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-edit', 0, 'Biểu mẫu sửa nhân viên giao hàng'),
(3171, 3164, '', 'Sửa nhân viên giao hàng', '', '', '', 'order/delivery/update/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-update', 0, 'Sửa nhân viên giao hàng'),
(3172, 3165, '', 'Xóa nhân viên giao hàng', '', '', '', 'order/delivery/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-del', 0, 'Xóa nhân viên giao hàng'),
(3173, 3166, '', 'Sửa trạng thái nhân viên giao hàng', '', '', '', 'order/delivery/set_status/<id>/<status>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'order-delivery-set_status', 0, 'Sửa trạng thái nhân viên giao hàng'),
(3174, 300, '', 'Thêm điểm nhận hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/300', 3, '', 0, 'merchant-add', 0, 'Thêm điểm nhận hàng'),
(3175, 300, '', 'Sửa điểm nhận hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/300', 3, '', 0, 'merchant-edit', 0, 'Sửa điểm nhận hàng'),
(3176, 300, '', 'Xóa điểm nhận hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/300', 3, '', 0, 'merchant-delete', 0, 'Xóa điểm nhận hàng'),
(3177, 300, '', 'Trạng thái điểm nhận hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/300', 3, '', 0, 'merchant-status', 0, 'Trạng thái điểm nhận hàng'),
(3178, 3174, '', 'Danh sách cửa hàng', '', '', '', 'merchant/store', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store', 0, 'Danh sách cửa hàng'),
(3179, 3174, '', 'Lưu chỉnh sửa thông tin cửa hàng', '', '', '', 'merchant/store/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store', 0, 'Lưu chỉnh sửa thông tin cửa hàng'),
(3180, 3174, '', 'Chọn vị trí cửa hàng', '', '', '', 'merchant/store/address', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-address', 0, 'Chọn vị trí cửa hàng'),
(3181, 3175, '', 'Danh sách cửa hàng', '', '', '', 'merchant/store', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-646a231547cf8', 0, 'Danh sách cửa hàng'),
(3182, 3175, '', 'Lưu chỉnh sửa thông tin cửa hàng', '', '', '', 'merchant/store/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-646a231547d03', 0, 'Lưu chỉnh sửa thông tin cửa hàng'),
(3183, 3175, '', 'Chi tiết cửa hàng', '', '', '', 'merchant/store/get_info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-get_info', 0, 'Chi tiết cửa hàng'),
(3184, 3175, '', 'Chọn vị trí cửa hàng', '', '', '', 'merchant/store/address', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-address-646a231547d13', 0, 'Chọn vị trí cửa hàng'),
(3185, 3176, '', 'Danh sách cửa hàng', '', '', '', 'merchant/store', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-646a23281a6ee', 0, 'Danh sách cửa hàng'),
(3186, 3176, '', 'Xóa cửa hàng', '', '', '', 'merchant/store/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-del', 0, 'Xóa cửa hàng'),
(3187, 3177, '', 'Danh sách cửa hàng', '', '', '', 'merchant/store', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-646a2337c993f', 0, 'Danh sách cửa hàng'),
(3188, 3177, '', 'Hiện/ẩn cửa hàng', '', '', '', 'merchant/store/set_show/<id>/<is_show>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store-set_show', 0, 'Hiện/ẩn cửa hàng'),
(3189, 301, '', 'Thêm nhân viên xác nhận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/301', 3, '', 0, 'merchant-staff-add', 0, 'Thêm nhân viên xác nhận'),
(3190, 301, '', 'Sửa nhân viên xác nhận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/301', 3, '', 0, 'merchant-staff-edit', 0, 'Sửa nhân viên xác nhận'),
(3191, 301, '', 'Xóa nhân viên xác nhận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/301', 3, '', 0, 'merchant-staff-delete', 0, 'Xóa nhân viên xác nhận'),
(3192, 301, '', 'Trạng thái nhân viên xác nhận', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/144/301', 3, '', 0, 'merchant-staff-status', 0, 'Trạng thái nhân viên xác nhận'),
(3193, 3189, '', 'Lấy danh sách nhân viên cửa hàng', '', '', '', 'merchant/store_staff', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff', 0, 'Lấy danh sách nhân viên cửa hàng'),
(3194, 3189, '', 'Biểu mẫu thêm nhân viên cửa hàng', '', '', '', 'merchant/store_staff/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-create', 0, 'Biểu mẫu thêm nhân viên cửa hàng'),
(3195, 3189, '', 'Lưu nhân viên cửa hàng', '', '', '', 'merchant/store_staff/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-save', 0, 'Lưu nhân viên cửa hàng'),
(3196, 3189, '', 'Danh sách tìm kiếm cửa hàng', '', '', '', 'merchant/store_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_list', 0, 'Danh sách tìm kiếm cửa hàng'),
(3197, 3190, '', 'Lấy danh sách nhân viên cửa hàng', '', '', '', 'merchant/store_staff', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-646a23be65c88', 0, 'Lấy danh sách nhân viên cửa hàng'),
(3198, 3190, '', 'Biểu mẫu sửa nhân viên cửa hàng', '', '', '', 'merchant/store_staff/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-edit', 0, 'Biểu mẫu sửa nhân viên cửa hàng'),
(3199, 3190, '', 'Lưu nhân viên cửa hàng', '', '', '', 'merchant/store_staff/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-save-646a23be65c98', 0, 'Lưu nhân viên cửa hàng'),
(3200, 3190, '', 'Danh sách tìm kiếm cửa hàng', '', '', '', 'merchant/store_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_list-646a23be65c9e', 0, 'Danh sách tìm kiếm cửa hàng'),
(3201, 3191, '', 'Lấy danh sách nhân viên cửa hàng', '', '', '', 'merchant/store_staff', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-646a23cc0a061', 0, 'Lấy danh sách nhân viên cửa hàng'),
(3202, 3191, '', 'Xóa nhân viên cửa hàng', '', '', '', 'merchant/store_staff/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-del', 0, 'Xóa nhân viên cửa hàng'),
(3203, 3192, '', 'Sửa trạng thái nhân viên cửa hàng', '', '', '', 'merchant/store_staff/set_show/<id>/<is_show>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-set_show', 0, 'Sửa trạng thái nhân viên cửa hàng'),
(3204, 3192, '', 'Lấy danh sách nhân viên cửa hàng', '', '', '', 'merchant/store_staff', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'merchant-store_staff-646a23d9d7e44', 0, 'Lấy danh sách nhân viên cửa hàng'),
(3205, 230, '', 'Thêm mẫu phí vận chuyển', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/230', 3, '', 0, 'shipping-temp-add', 0, 'Thêm mẫu phí vận chuyển'),
(3206, 230, '', 'Sửa mẫu phí vận chuyển', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/230', 3, '', 0, 'shipping-temp-edit', 0, 'Sửa mẫu phí vận chuyển'),
(3207, 230, '', 'Xóa mẫu phí vận chuyển', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/303/230', 3, '', 0, 'shipping-temp-delete', 0, 'Xóa mẫu phí vận chuyển'),
(3208, 3205, '', 'Danh sách mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-list', 0, 'Danh sách mẫu phí vận chuyển'),
(3209, 3205, '', 'Thêm mới hoặc sửa mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-save', 0, 'Thêm mới hoặc sửa mẫu phí vận chuyển'),
(3210, 3205, '', 'API dữ liệu thành phố', '', '', '', 'setting/shipping_templates/city_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-city_list', 0, 'API dữ liệu thành phố'),
(3211, 3206, '', 'Danh sách mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-list-646a24602a752', 0, 'Danh sách mẫu phí vận chuyển'),
(3212, 3206, '', 'Sửa dữ liệu mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-edit', 0, 'Sửa dữ liệu mẫu phí vận chuyển'),
(3213, 3206, '', 'Thêm mới hoặc sửa mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-save-646a24602a763', 0, 'Thêm mới hoặc sửa mẫu phí vận chuyển'),
(3214, 3206, '', 'API dữ liệu thành phố', '', '', '', 'setting/shipping_templates/city_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-city_list-646a24602a769', 0, 'API dữ liệu thành phố'),
(3215, 3207, '', 'Xóa mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-del', 0, 'Xóa mẫu phí vận chuyển'),
(3216, 3207, '', 'Danh sách mẫu phí vận chuyển', '', '', '', 'setting/shipping_templates/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-shipping_templates-list-646a246c229e8', 0, 'Danh sách mẫu phí vận chuyển'),
(3217, 111, '', 'Thêm danh mục cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-tab-add', 0, 'Thêm danh mục cấu hình'),
(3218, 111, '', 'Sửa danh mục cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-tab-edit', 0, 'Sửa danh mục cấu hình'),
(3219, 111, '', 'Xóa danh mục cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-tab-delete', 0, 'Xóa danh mục cấu hình'),
(3220, 111, '', 'Xem danh sách cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-list', 0, 'Xem danh sách cấu hình'),
(3221, 111, '', 'Thêm cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-add', 0, 'Thêm cấu hình'),
(3222, 111, '', 'Sửa cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-edit', 0, 'Sửa cấu hình'),
(3223, 111, '', 'Xóa cấu hình', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/111', 3, '', 0, 'config-delete', 0, 'Xóa cấu hình'),
(3224, 3217, '', 'Lấy danh sách danh mục cấu hình hệ thống', '', '', '', 'setting/config_class', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class', 0, 'Lấy danh sách danh mục cấu hình hệ thống'),
(3225, 3217, '', 'Lấy biểu mẫu danh mục cấu hình hệ thống', '', '', '', 'setting/config_class/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-create', 0, 'Lấy biểu mẫu danh mục cấu hình hệ thống'),
(3226, 3217, '', 'Lưu danh mục cấu hình hệ thống', '', '', '', 'setting/config_class', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class', 0, 'Lưu danh mục cấu hình hệ thống'),
(3227, 3218, '', 'Lấy danh sách danh mục cấu hình hệ thống', '', '', '', 'setting/config_class', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-646a2648cbb75', 0, 'Lấy danh sách danh mục cấu hình hệ thống'),
(3228, 3218, '', 'Lấy biểu mẫu sửa danh mục cấu hình hệ thống', '', '', '', 'setting/config_class/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-edit', 0, 'Lấy biểu mẫu sửa danh mục cấu hình hệ thống'),
(3229, 3218, '', 'Sửa danh mục cấu hình hệ thống', '', '', '', 'setting/config_class/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-646a2648cbb97', 0, 'Sửa danh mục cấu hình hệ thống'),
(3230, 3219, '', 'Lấy danh sách danh mục cấu hình hệ thống', '', '', '', 'setting/config_class', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-646a26613d6bb', 0, 'Lấy danh sách danh mục cấu hình hệ thống'),
(3231, 3219, '', 'Xóa danh mục cấu hình hệ thống', '', '', '', 'setting/config_class/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-646a26613d6c6', 0, 'Xóa danh mục cấu hình hệ thống'),
(3232, 3220, '', 'Lấy danh sách danh mục cấu hình hệ thống', '', '', '', 'setting/config_class', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config_class-646a267255df4', 0, 'Lấy danh sách danh mục cấu hình hệ thống'),
(3233, 3220, '', 'Lấy danh sách cấu hình hệ thống', '', '', '', 'setting/config', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config', 0, 'Lấy danh sách cấu hình hệ thống'),
(3234, 3221, '', 'Lấy danh sách cấu hình hệ thống', '', '', '', 'setting/config', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-646a26874c851', 0, 'Lấy danh sách cấu hình hệ thống'),
(3235, 3221, '', 'Lưu cấu hình hệ thống', '', '', '', 'setting/config', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-646a26874c85b', 0, 'Lưu cấu hình hệ thống'),
(3236, 3221, '', 'Lấy biểu mẫu cấu hình hệ thống', '', '', '', 'setting/config/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-create', 0, 'Lấy biểu mẫu cấu hình hệ thống'),
(3237, 3222, '', 'Lấy danh sách cấu hình hệ thống', '', '', '', 'setting/config', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-646a269697340', 0, 'Lấy danh sách cấu hình hệ thống'),
(3238, 3222, '', 'Lấy biểu mẫu sửa cấu hình hệ thống', '', '', '', 'setting/config/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-edit', 0, 'Lấy biểu mẫu sửa cấu hình hệ thống'),
(3239, 3222, '', 'Sửa cấu hình hệ thống', '', '', '', 'setting/config/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-646a269697353', 0, 'Sửa cấu hình hệ thống'),
(3240, 3223, '', 'Lấy danh sách cấu hình hệ thống', '', '', '', 'setting/config', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-646a26a48db02', 0, 'Lấy danh sách cấu hình hệ thống'),
(3241, 3223, '', 'Xóa cấu hình hệ thống', '', '', '', 'setting/config/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-646a26a48db0f', 0, 'Xóa cấu hình hệ thống'),
(3242, 3223, '', 'Sửa trạng thái cấu hình', '', '', '', 'setting/config/set_status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-config-set_status', 0, 'Sửa trạng thái cấu hình'),
(3243, 112, '', 'Thêm nhóm dữ liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-add', 0, 'Thêm nhóm dữ liệu'),
(3244, 112, '', 'Sửa nhóm dữ liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-edit', 0, 'Sửa nhóm dữ liệu'),
(3245, 112, '', 'Xóa nhóm dữ liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-delete', 0, 'Xóa nhóm dữ liệu'),
(3246, 112, '', 'Xem danh sách dữ liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-data-list', 0, 'Xem danh sách dữ liệu'),
(3247, 112, '', 'Thêm dữ liệu tổ hợp', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-data-add', 0, 'Thêm dữ liệu tổ hợp'),
(3248, 112, '', 'Sửa dữ liệu tổ hợp', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-data-edit', 0, 'Sửa dữ liệu tổ hợp'),
(3249, 112, '', 'Xóa dữ liệu tổ hợp', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/112', 3, '', 0, 'system-group-data-delete', 0, 'Xóa dữ liệu tổ hợp'),
(3250, 3243, '', 'Lấy danh sách dữ liệu tổ hợp', '', '', '', 'setting/group', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group', 0, 'Lấy danh sách dữ liệu tổ hợp'),
(3251, 3243, '', 'Lưu dữ liệu tổ hợp', '', '', '', 'setting/group', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group', 0, 'Lưu dữ liệu tổ hợp'),
(3252, 3244, '', 'Lấy danh sách dữ liệu tổ hợp', '', '', '', 'setting/group', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group-646a2784797f3', 0, 'Lấy danh sách dữ liệu tổ hợp'),
(3253, 3244, '', 'Lấy biểu mẫu sửa dữ liệu tổ hợp', '', '', '', 'setting/group/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group-edit', 0, 'Lấy biểu mẫu sửa dữ liệu tổ hợp'),
(3254, 3244, '', 'Sửa dữ liệu tổ hợp', '', '', '', 'setting/group/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group-646a278479805', 0, 'Sửa dữ liệu tổ hợp'),
(3255, 3245, '', 'Lấy danh sách dữ liệu tổ hợp', '', '', '', 'setting/group', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group-646a27906b21a', 0, 'Lấy danh sách dữ liệu tổ hợp'),
(3256, 3245, '', 'Xóa dữ liệu tổ hợp', '', '', '', 'setting/group/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group-646a27906b223', 0, 'Xóa dữ liệu tổ hợp'),
(3257, 3246, '', 'Lấy danh sách dữ liệu tổ hợp', '', '', '', 'setting/group', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group-646a27b813076', 0, 'Lấy danh sách dữ liệu tổ hợp'),
(3258, 3246, '', 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data', 0, 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp'),
(3259, 3247, '', 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-646a27c6e19ae', 0, 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp'),
(3260, 3247, '', 'Lưu dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-646a27c6e19b8', 0, 'Lưu dữ liệu con của dữ liệu tổ hợp'),
(3261, 3247, '', 'Lấy biểu mẫu dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-create', 0, 'Lấy biểu mẫu dữ liệu con của dữ liệu tổ hợp'),
(3262, 3248, '', 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-646a27d6c6cbf', 0, 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp'),
(3263, 3248, '', 'Lấy biểu mẫu sửa dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-edit', 0, 'Lấy biểu mẫu sửa dữ liệu con của dữ liệu tổ hợp'),
(3264, 3248, '', 'Sửa dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-646a27d6c6cd2', 0, 'Sửa dữ liệu con của dữ liệu tổ hợp'),
(3265, 3249, '', 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-646a27e50d1c1', 0, 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp'),
(3266, 3249, '', 'Xóa dữ liệu con của dữ liệu tổ hợp', '', '', '', 'setting/group_data/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-group_data-646a27e50d1ca', 0, 'Xóa dữ liệu con của dữ liệu tổ hợp'),
(3267, 1076, '', 'Thêm tác vụ định kỳ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/1076', 3, '', 0, 'crontab-add', 0, 'Thêm tác vụ định kỳ'),
(3268, 1076, '', 'Sửa tác vụ định kỳ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/1076', 3, '', 0, 'crontab-edit', 0, 'Sửa tác vụ định kỳ'),
(3269, 1076, '', 'Xóa tác vụ định kỳ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/1076', 3, '', 0, 'crontab-delete', 0, 'Xóa tác vụ định kỳ'),
(3270, 1076, '', 'Trạng thái tác vụ định kỳ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/1076', 3, '', 0, 'crontab-status', 0, 'Trạng thái tác vụ định kỳ'),
(3271, 3267, '', 'Danh sách tác vụ định kỳ', '', '', '', 'system/crontab/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-list', 0, 'Danh sách tác vụ định kỳ'),
(3272, 3267, '', 'Loại tác vụ định kỳ', '', '', '', 'system/crontab/mark', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-mark', 0, 'Loại tác vụ định kỳ'),
(3273, 3267, '', 'Thêm/sửa tác vụ định kỳ', '', '', '', 'system/crontab/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-save', 0, 'Thêm/sửa tác vụ định kỳ'),
(3274, 3268, '', 'Danh sách tác vụ định kỳ', '', '', '', 'system/crontab/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-list-646a2860d3394', 0, 'Danh sách tác vụ định kỳ'),
(3275, 3268, '', 'Loại tác vụ định kỳ', '', '', '', 'system/crontab/mark', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-mark-646a2860d339f', 0, 'Loại tác vụ định kỳ'),
(3276, 3268, '', 'Chi tiết tác vụ định kỳ', '', '', '', 'system/crontab/info/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-info', 0, 'Chi tiết tác vụ định kỳ'),
(3277, 3268, '', 'Thêm/sửa tác vụ định kỳ', '', '', '', 'system/crontab/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-save-646a2860d33b0', 0, 'Thêm/sửa tác vụ định kỳ'),
(3278, 3269, '', 'Danh sách tác vụ định kỳ', '', '', '', 'system/crontab/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-list-646a286e08853', 0, 'Danh sách tác vụ định kỳ'),
(3279, 3269, '', 'Xóa tác vụ định kỳ', '', '', '', 'system/crontab/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-del', 0, 'Xóa tác vụ định kỳ'),
(3280, 3270, '', 'Danh sách tác vụ định kỳ', '', '', '', 'system/crontab/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-list-646a287b727dc', 0, 'Danh sách tác vụ định kỳ'),
(3281, 3270, '', 'Công tắc bật/tắt tác vụ định kỳ', '', '', '', 'system/crontab/set_open/<id>/<is_open>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crontab-set_open', 0, 'Công tắc bật/tắt tác vụ định kỳ'),
(3282, 2472, '', 'Thêm quyền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/2472', 3, '', 0, 'system-admin-role-add', 0, 'Thêm quyền'),
(3283, 2472, '', 'Sửa quyền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/2472', 3, '', 0, 'system-admin-role-edit', 0, 'Sửa quyền'),
(3284, 2472, '', 'Xóa quyền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/2472', 3, '', 0, 'system-admin-role-delete', 0, 'Xóa quyền'),
(3285, 2472, '', 'Thêm menu con', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/2472', 3, '', 0, 'system-admin-role-add-menus', 0, 'Thêm menu con'),
(3286, 2472, '', 'Chọn API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/56/2472', 3, '', 0, 'system-admin-role-select', 0, 'Chọn API'),
(3287, 3282, '', 'Lấy danh sách menu quyền', '', '', '', 'setting/menus', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a296711f5a', 0, 'Lấy danh sách menu quyền'),
(3288, 3282, '', 'Lấy biểu mẫu menu quyền', '', '', '', 'setting/menus/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-create', 0, 'Lấy biểu mẫu menu quyền'),
(3289, 3282, '', 'Lưu menu quyền', '', '', '', 'setting/menus', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a296711f72', 0, 'Lưu menu quyền'),
(3290, 3283, '', 'Lấy danh sách menu quyền', '', '', '', 'setting/menus', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a297963e41', 0, 'Lấy danh sách menu quyền'),
(3291, 3283, '', 'Lấy biểu mẫu sửa menu quyền', '', '', '', 'setting/menus/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-edit-646a297963e4d', 0, 'Lấy biểu mẫu sửa menu quyền'),
(3292, 3283, '', 'Xem thông tin menu quyền', '', '', '', 'setting/menus/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a297963e56', 0, 'Xem thông tin menu quyền'),
(3293, 3283, '', 'Sửa menu quyền', '', '', '', 'setting/menus/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a297963e5e', 0, 'Sửa menu quyền'),
(3294, 3284, '', 'Lấy danh sách menu quyền', '', '', '', 'setting/menus', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a29871df51', 0, 'Lấy danh sách menu quyền'),
(3295, 3284, '', 'Xóa menu quyền', '', '', '', 'setting/menus/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a29871df5c', 0, 'Xóa menu quyền'),
(3296, 3285, '', 'Xem thông tin menu quyền', '', '', '', 'setting/menus/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a29b28b70e', 0, 'Xem thông tin menu quyền'),
(3297, 3285, '', 'Lưu menu quyền', '', '', '', 'setting/menus', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a29b28b719', 0, 'Lưu menu quyền'),
(3298, 3285, '', 'Lấy danh sách menu quyền', '', '', '', 'setting/menus', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-menus-646a29b28b720', 0, 'Lấy danh sách menu quyền'),
(3299, 1101, '', 'Thêm chức năng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1101', 3, '', 0, 'code-generation-add', 0, 'Thêm chức năng'),
(3300, 1101, '', 'Xem mã nguồn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1101', 3, '', 0, 'code-generation-scan', 0, 'Xem mã nguồn'),
(3301, 1101, '', 'Tải xuống mã nguồn', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1101', 3, '', 0, 'code-generation-down', 0, 'Tải xuống mã nguồn'),
(3302, 1101, '', 'Sửa chức năng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1101', 3, '', 0, 'code-generation-edit', 0, 'Sửa chức năng'),
(3303, 1101, '', 'Xóa chức năng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1101', 3, '', 0, 'code-generation-delete', 0, 'Xóa chức năng'),
(3304, 3299, '', 'Lưu và tạo CRUD', '', '', '', 'system/crud', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud', 0, 'Lưu và tạo CRUD'),
(3305, 3299, '', 'Lấy dữ liệu menu dạng TREE', '', '', '', 'system/crud/menus', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-menus', 0, 'Lấy dữ liệu menu dạng TREE'),
(3306, 3299, '', 'Lấy vị trí lưu tệp CRUD', '', '', '', 'system/crud/file_path', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-file_path', 0, 'Lấy vị trí lưu tệp CRUD'),
(3307, 3299, '', 'Lấy danh sách CRUD', '', '', '', 'system/crud/column_type', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-column_type', 0, 'Lấy danh sách CRUD'),
(3308, 3299, '', 'Lấy cấu hình CRUD', '', '', '', 'system/crud/config/<tableName>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-config', 0, 'Lấy cấu hình CRUD'),
(3309, 3299, '', 'Lưu tệp CRUD đã sửa', '', '', '', 'system/crud/save_file/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-save_file', 0, 'Lưu tệp CRUD đã sửa'),
(3310, 3300, '', 'Xem CRUD', '', '', '', 'system/crud/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a34d60329f', 0, 'Xem CRUD'),
(3311, 3300, '', 'Lưu tệp CRUD đã sửa', '', '', '', 'system/crud/save_file/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-save_file-646a34d6032aa', 0, 'Lưu tệp CRUD đã sửa'),
(3312, 3301, '', 'Tải xuống tệp đã tạo', '', '', '', 'system/crud/download/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-download', 0, 'Tải xuống tệp đã tạo'),
(3313, 3301, '', 'Lấy danh sách CRUD', '', '', '', 'system/crud', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a34e764096', 0, 'Lấy danh sách CRUD'),
(3314, 3302, '', 'Lưu tệp CRUD đã sửa', '', '', '', 'system/crud/save_file/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-save_file-646a34fe884b9', 0, 'Lưu tệp CRUD đã sửa'),
(3315, 3302, '', 'Lấy cấu hình CRUD', '', '', '', 'system/crud/config/<tableName>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-config-646a34fe884c2', 0, 'Lấy cấu hình CRUD'),
(3316, 3302, '', 'Lấy danh sách CRUD', '', '', '', 'system/crud/column_type', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-column_type-646a34fe884c9', 0, 'Lấy danh sách CRUD'),
(3317, 3302, '', 'Lấy vị trí lưu tệp CRUD', '', '', '', 'system/crud/file_path', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-file_path-646a34fe884d0', 0, 'Lấy vị trí lưu tệp CRUD'),
(3318, 3302, '', 'Xem CRUD', '', '', '', 'system/crud/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a34fe884d6', 0, 'Xem CRUD'),
(3319, 3302, '', 'Lưu và tạo CRUD', '', '', '', 'system/crud', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a34fe884dc', 0, 'Lưu và tạo CRUD'),
(3320, 3302, '', 'Lấy dữ liệu menu dạng TREE', '', '', '', 'system/crud/menus', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-menus-646a34fe884e3', 0, 'Lấy dữ liệu menu dạng TREE'),
(3321, 3302, '', 'Lấy danh sách CRUD', '', '', '', 'system/crud', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a34fe884e9', 0, 'Lấy danh sách CRUD'),
(3322, 3303, '', 'Lấy danh sách CRUD', '', '', '', 'system/crud', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a350c5c14e', 0, 'Lấy danh sách CRUD'),
(3323, 3303, '', 'Xóa CRUD', '', '', '', 'system/crud/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-646a350c5c15b', 0, 'Xóa CRUD'),
(3324, 1078, '', 'Danh mục API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1078', 3, '', 0, 'route-cate', 0, 'Danh mục API'),
(3325, 1078, '', 'Đồng bộ API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1078', 3, '', 0, 'route-sync', 0, 'Đồng bộ API'),
(3326, 1078, '', 'Gỡ lỗi API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1078', 3, '', 0, 'route-test', 0, 'Gỡ lỗi API'),
(3327, 1078, '', 'Sửa API', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1695/1078', 3, '', 0, 'route-edit', 0, 'Sửa API'),
(3328, 3324, '', 'Lấy danh sách danh mục route', '', '', '', 'system/route_cate', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route_cate', 0, 'Lấy danh sách danh mục route'),
(3329, 3324, '', 'Lưu danh mục route', '', '', '', 'system/route_cate', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route_cate', 0, 'Lưu danh mục route'),
(3330, 3324, '', 'Lấy biểu mẫu tạo danh mục route', '', '', '', 'system/route_cate/create', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route_cate-create', 0, 'Lấy biểu mẫu tạo danh mục route'),
(3331, 3324, '', 'Lấy biểu mẫu sửa danh mục route', '', '', '', 'system/route_cate/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route_cate-edit', 0, 'Lấy biểu mẫu sửa danh mục route'),
(3332, 3324, '', 'Sửa danh mục route', '', '', '', 'system/route_cate/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route_cate', 0, 'Sửa danh mục route'),
(3333, 3324, '', 'Xóa danh mục route', '', '', '', 'system/route_cate/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route_cate', 0, 'Xóa danh mục route'),
(3334, 3325, '', 'Đồng bộ route', '', '', '', 'system/route/sync_route/<appName?>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route-sync_route', 0, 'Đồng bộ route'),
(3335, 3325, '', 'Lấy route dạng tree', '', '', '', 'system/route/tree', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route-tree', 0, 'Lấy route dạng tree'),
(3336, 3327, '', 'Lấy route dạng tree', '', '', '', 'system/route/tree', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route-tree-646a35e9baaef', 0, 'Lấy route dạng tree'),
(3337, 3327, '', 'Xem quyền route', '', '', '', 'system/route/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route', 0, 'Xem quyền route'),
(3338, 3327, '', 'Lưu quyền route', '', '', '', 'system/route/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-route', 0, 'Lưu quyền route'),
(3339, 1068, '', 'Thêm ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1068', 3, '', 0, 'lang-list-add', 0, 'Thêm ngôn ngữ'),
(3340, 1068, '', 'Sửa ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1068', 3, '', 0, 'lang-list-edit', 0, 'Sửa ngôn ngữ'),
(3341, 1068, '', 'Xóa ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1068', 3, '', 0, 'lang-list-delete', 0, 'Xóa ngôn ngữ'),
(3342, 1068, '', 'Trạng thái ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1068', 3, '', 0, 'lang-list-status', 0, 'Trạng thái ngôn ngữ'),
(3343, 3339, '', 'Danh sách loại ngôn ngữ', '', '', '', 'setting/lang_type/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-list', 0, 'Danh sách loại ngôn ngữ'),
(3344, 3339, '', 'Biểu mẫu thêm/sửa loại ngôn ngữ', '', '', '', 'setting/lang_type/form/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-form', 0, 'Biểu mẫu thêm/sửa loại ngôn ngữ'),
(3345, 3339, '', 'Lưu thêm/sửa ngôn ngữ', '', '', '', 'setting/lang_type/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-save', 0, 'Lưu thêm/sửa ngôn ngữ'),
(3346, 3340, '', 'Danh sách loại ngôn ngữ', '', '', '', 'setting/lang_type/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-list-646a36876cfbe', 0, 'Danh sách loại ngôn ngữ'),
(3347, 3340, '', 'Biểu mẫu thêm/sửa loại ngôn ngữ', '', '', '', 'setting/lang_type/form/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-form-646a36876cfd9', 0, 'Biểu mẫu thêm/sửa loại ngôn ngữ'),
(3348, 3340, '', 'Lưu thêm/sửa ngôn ngữ', '', '', '', 'setting/lang_type/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-save-646a36876cfe3', 0, 'Lưu thêm/sửa ngôn ngữ'),
(3349, 3341, '', 'Danh sách loại ngôn ngữ', '', '', '', 'setting/lang_type/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-list-646a369c9741e', 0, 'Danh sách loại ngôn ngữ'),
(3350, 3341, '', 'Xóa ngôn ngữ', '', '', '', 'setting/lang_type/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-del', 0, 'Xóa ngôn ngữ'),
(3351, 3342, '', 'Danh sách loại ngôn ngữ', '', '', '', 'setting/lang_type/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-list-646a36a88444d', 0, 'Danh sách loại ngôn ngữ'),
(3352, 3342, '', 'Sửa trạng thái loại ngôn ngữ', '', '', '', 'setting/lang_type/status/<id>/<status>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_type-status', 0, 'Sửa trạng thái loại ngôn ngữ'),
(3353, 1069, '', 'Thêm chi tiết ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1069', 3, '', 0, 'lang-info-add', 0, 'Thêm chi tiết ngôn ngữ'),
(3354, 1069, '', 'Sửa chi tiết ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1069', 3, '', 0, 'lang-info-edit', 0, 'Sửa chi tiết ngôn ngữ'),
(3355, 1069, '', 'Xóa chi tiết ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '12/1067/1069', 3, '', 0, 'lang-info-delete', 0, 'Xóa chi tiết ngôn ngữ'),
(3356, 3353, '', 'Danh sách ngôn ngữ', '', '', '', 'setting/lang_code/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-list', 0, 'Danh sách ngôn ngữ'),
(3357, 3353, '', 'Chi tiết ngôn ngữ', '', '', '', 'setting/lang_code/info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-info', 0, 'Chi tiết ngôn ngữ'),
(3358, 3353, '', 'Lưu chỉnh sửa ngôn ngữ', '', '', '', 'setting/lang_code/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-save', 0, 'Lưu chỉnh sửa ngôn ngữ'),
(3359, 3353, '', 'Dịch máy', '', '', '', 'setting/lang_code/translate', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-translate', 0, 'Dịch máy'),
(3360, 3354, '', 'Danh sách ngôn ngữ', '', '', '', 'setting/lang_code/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-list-646a3717e64bb', 0, 'Danh sách ngôn ngữ'),
(3361, 3354, '', 'Chi tiết ngôn ngữ', '', '', '', 'setting/lang_code/info', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-info-646a3717e64c8', 0, 'Chi tiết ngôn ngữ'),
(3362, 3354, '', 'Lưu chỉnh sửa ngôn ngữ', '', '', '', 'setting/lang_code/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-save-646a3717e64d1', 0, 'Lưu chỉnh sửa ngôn ngữ'),
(3363, 3354, '', 'Dịch máy', '', '', '', 'setting/lang_code/translate', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-translate-646a3717e64db', 0, 'Dịch máy'),
(3364, 3355, '', 'Danh sách ngôn ngữ', '', '', '', 'setting/lang_code/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-list-646a372511b5e', 0, 'Danh sách ngôn ngữ'),
(3365, 3355, '', 'Xóa ngôn ngữ', '', '', '', 'setting/lang_code/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_code-del', 0, 'Xóa ngôn ngữ'),
(3366, 1070, '', 'Thêm khu vực ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1067/1070', 3, '', 0, 'lang-country-add', 0, 'Thêm khu vực ngôn ngữ'),
(3367, 1070, '', 'Sửa khu vực ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1067/1070', 3, '', 0, 'lang-country-edit', 0, 'Sửa khu vực ngôn ngữ'),
(3368, 1070, '', 'Xóa khu vực ngôn ngữ', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1067/1070', 3, '', 0, 'lang-country-delete', 0, 'Xóa khu vực ngôn ngữ'),
(3369, 3366, '', 'Danh sách quốc gia theo ngôn ngữ', '', '', '', 'setting/lang_country/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-list', 0, 'Danh sách quốc gia theo ngôn ngữ'),
(3370, 3366, '', 'Biểu mẫu thêm khu vực ngôn ngữ', '', '', '', 'setting/lang_country/form/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-form', 0, 'Biểu mẫu thêm khu vực ngôn ngữ'),
(3371, 3366, '', 'Lưu khu vực ngôn ngữ', '', '', '', 'setting/lang_country/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-save', 0, 'Lưu khu vực ngôn ngữ'),
(3372, 3367, '', 'Danh sách quốc gia theo ngôn ngữ', '', '', '', 'setting/lang_country/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-list-646a377fb08e5', 0, 'Danh sách quốc gia theo ngôn ngữ'),
(3373, 3367, '', 'Biểu mẫu thêm khu vực ngôn ngữ', '', '', '', 'setting/lang_country/form/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-form-646a377fb08f2', 0, 'Biểu mẫu thêm khu vực ngôn ngữ'),
(3374, 3367, '', 'Lưu khu vực ngôn ngữ', '', '', '', 'setting/lang_country/save/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-save-646a377fb08fc', 0, 'Lưu khu vực ngôn ngữ'),
(3375, 3368, '', 'Danh sách quốc gia theo ngôn ngữ', '', '', '', 'setting/lang_country/list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-list-646a378a8e1b7', 0, 'Danh sách quốc gia theo ngôn ngữ'),
(3376, 3368, '', 'Xóa khu vực ngôn ngữ', '', '', '', 'setting/lang_country/del/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-lang_country-del', 0, 'Xóa khu vực ngôn ngữ'),
(3377, 1066, '', 'Thêm tài khoản API bên ngoài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1064/1066', 3, '', 0, 'out-account-add', 0, 'Thêm tài khoản API bên ngoài'),
(3378, 1066, '', 'Sửa tài khoản API bên ngoài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1064/1066', 3, '', 0, 'out-account-edit', 0, 'Sửa tài khoản API bên ngoài'),
(3379, 1066, '', 'Xóa tài khoản API bên ngoài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1064/1066', 3, '', 0, 'out-account-delete', 0, 'Xóa tài khoản API bên ngoài'),
(3380, 1066, '', 'Cài đặt tài khoản API bên ngoài', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1064/1066', 3, '', 0, 'out-account-setting', 0, 'Cài đặt tài khoản API bên ngoài'),
(3381, 3377, '', 'Thông tin tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/index', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-index', 0, 'Thông tin tài khoản API bên ngoài'),
(3382, 3377, '', 'Thêm tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-save', 0, 'Thêm tài khoản API bên ngoài'),
(3383, 3378, '', 'Thông tin tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/index', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-index-646a385546279', 0, 'Thông tin tài khoản API bên ngoài'),
(3384, 3378, '', 'Thêm tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-save-646a385546286', 0, 'Thêm tài khoản API bên ngoài'),
(3385, 3378, '', 'Sửa tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/update/<id>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-update', 0, 'Sửa tài khoản API bên ngoài'),
(3386, 3379, '', 'Thông tin tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/index', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-index-646a386512a5e', 0, 'Thông tin tài khoản API bên ngoài'),
(3387, 3379, '', 'Xóa tài khoản', '', '', '', 'setting/system_out_account/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account', 0, 'Xóa tài khoản'),
(3388, 3380, '', 'Thông tin tài khoản API bên ngoài', '', '', '', 'setting/system_out_account/index', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-index-646a38719e553', 0, 'Thông tin tài khoản API bên ngoài'),
(3389, 3380, '', 'Cài đặt API đẩy dữ liệu cho tài khoản', '', '', '', 'setting/system_out_account/set_up/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-system_out_account-set_up', 0, 'Cài đặt API đẩy dữ liệu cho tài khoản'),
(3390, 145, '', 'Đồng bộ đơn vị vận chuyển', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1073/145', 3, '', 0, 'express-sync', 0, 'Đồng bộ đơn vị vận chuyển'),
(3391, 145, '', 'Sửa đơn vị vận chuyển', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1073/145', 3, '', 0, 'express-edit', 0, 'Sửa đơn vị vận chuyển'),
(3392, 3390, '', 'Lấy danh sách đơn vị vận chuyển', '', '', '', 'freight/express', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'freight-express', 0, 'Lấy danh sách đơn vị vận chuyển'),
(3393, 3390, '', 'Đồng bộ đơn vị vận chuyển', '', '', '', 'freight/express/sync_express', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'freight-express-sync_express', 0, 'Đồng bộ đơn vị vận chuyển'),
(3394, 3391, '', 'Lấy danh sách đơn vị vận chuyển', '', '', '', 'freight/express', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'freight-express-646a38ef9dbdd', 0, 'Lấy danh sách đơn vị vận chuyển'),
(3395, 3391, '', 'Lấy biểu mẫu sửa đơn vị vận chuyển', '', '', '', 'freight/express/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'freight-express-edit', 0, 'Lấy biểu mẫu sửa đơn vị vận chuyển'),
(3396, 3391, '', 'Sửa đơn vị vận chuyển', '', '', '', 'freight/express/<id>', 'PUT', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'freight-express-646a38ef9dbf3', 0, 'Sửa đơn vị vận chuyển'),
(3398, 229, '', 'Thêm dữ liệu thành phố', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1073/229', 3, '', 0, 'system-city-add', 0, 'Thêm dữ liệu thành phố'),
(3399, 229, '', 'Sửa dữ liệu thành phố', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1073/229', 3, '', 0, 'system-city-edit', 0, 'Sửa dữ liệu thành phố'),
(3400, 229, '', 'Xóa dữ liệu thành phố', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1073/229', 3, '', 0, 'system-city-delete', 0, 'Xóa dữ liệu thành phố'),
(3401, 229, '', 'Xóa bộ nhớ đệm dữ liệu thành phố', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/', '25/1073/229', 3, '', 0, 'system-city-clear-cache', 0, 'Xóa bộ nhớ đệm dữ liệu thành phố'),
(3402, 3398, '', 'Lấy danh sách đầy đủ dữ liệu thành phố', '', '', '', 'setting/city/full_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-full_list', 0, 'Lấy danh sách đầy đủ dữ liệu thành phố'),
(3403, 3398, '', 'Lấy danh sách dữ liệu thành phố', '', '', '', 'setting/city/list/<parent_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-list', 0, 'Lấy danh sách dữ liệu thành phố'),
(3404, 3398, '', 'Biểu mẫu thêm dữ liệu thành phố', '', '', '', 'setting/city/add/<parent_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-add', 0, 'Biểu mẫu thêm dữ liệu thành phố'),
(3405, 3398, '', 'Thêm mới/sửa dữ liệu thành phố', '', '', '', 'setting/city/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-save', 0, 'Thêm mới/sửa dữ liệu thành phố'),
(3406, 3399, '', 'Lấy danh sách đầy đủ dữ liệu thành phố', '', '', '', 'setting/city/full_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-full_list-646a39aa87ef0', 0, 'Lấy danh sách đầy đủ dữ liệu thành phố'),
(3407, 3399, '', 'Lấy danh sách dữ liệu thành phố', '', '', '', 'setting/city/list/<parent_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-list-646a39aa87efe', 0, 'Lấy danh sách dữ liệu thành phố'),
(3408, 3399, '', 'Biểu mẫu sửa dữ liệu thành phố', '', '', '', 'setting/city/<id>/edit', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-edit', 0, 'Biểu mẫu sửa dữ liệu thành phố'),
(3409, 3399, '', 'Thêm mới/sửa dữ liệu thành phố', '', '', '', 'setting/city/save', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-save-646a39aa87f12', 0, 'Thêm mới/sửa dữ liệu thành phố'),
(3410, 3400, '', 'Lấy danh sách đầy đủ dữ liệu thành phố', '', '', '', 'setting/city/full_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-full_list-646a39c191645', 0, 'Lấy danh sách đầy đủ dữ liệu thành phố'),
(3411, 3400, '', 'Lấy danh sách dữ liệu thành phố', '', '', '', 'setting/city/list/<parent_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-list-646a39c191651', 0, 'Lấy danh sách dữ liệu thành phố'),
(3412, 3400, '', 'Xóa dữ liệu thành phố', '', '', '', 'setting/city/del/<city_id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-del', 0, 'Xóa dữ liệu thành phố'),
(3413, 3401, '', 'Lấy danh sách đầy đủ dữ liệu thành phố', '', '', '', 'setting/city/full_list', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-full_list-646a39ceb8c4c', 0, 'Lấy danh sách đầy đủ dữ liệu thành phố'),
(3414, 3401, '', 'Lấy danh sách dữ liệu thành phố', '', '', '', 'setting/city/list/<parent_id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-list-646a39ceb8c57', 0, 'Lấy danh sách dữ liệu thành phố'),
(3415, 3401, '', 'Xóa bộ nhớ đệm dữ liệu thành phố', '', '', '', 'setting/city/clean_cache', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'setting-city-clean_cache', 0, 'Xóa bộ nhớ đệm dữ liệu thành phố'),
(3417, 1056, '', 'Yihaotong', 'admin', '', '', '', '', '[]', 10, 1, 1, 1, '/yihaotong', '12/1056', 1, '', 0, 'setting-yihaotong', 0, ''),
(3418, 3417, '', 'Cấu hình Yihaotong', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/yihaotong_config/3/18', '12/1056/3417', 1, '', 0, 'setting-yihaotong-config', 0, ''),
(3419, 1067, '', 'Cấu hình dịch thuật', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/lang_config/3/106', '25/1067', 1, '', 0, 'setting-lang-config', 0, ''),
(3420, 27, '', 'Nạp tiền người dùng', 'admin', '', '', '', '', '[]', 60, 1, 1, 1, '/marketing/recharge', '27', 1, '', 0, 'admin-marketing-recharge', 0, ''),
(3421, 165, '', 'Cấu hình CSKH', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/kefu_config/2/69', '165', 1, '', 0, 'setting-kefu-config', 0, ''),
(3422, 3420, '', 'Cấu hình nạp tiền', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/recharge_config/2/28', '27/3420', 1, '', 0, 'setting-recharge-config', 0, ''),
(3423, 9, '', 'Cấu hình người dùng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/user_config/2/100', '9', 1, '', 0, '', 0, ''),
(3424, 4, '', 'Cấu hình đơn hàng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/order_config/2/113', '4', 1, '', 0, '', 0, ''),
(3425, 27, '', 'Điểm danh hằng ngày', 'admin', '', '', '', '', '[]', 57, 1, 1, 1, '/markering/sign', '27', 1, '', 0, 'admin-marketing-sign', 0, ''),
(3426, 3425, '', 'Cấu hình điểm danh', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/sign_config/2/126', '27/3425', 1, '', 0, '', 0, ''),
(3427, 3425, '', 'Phần thưởng điểm danh', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/sign_rewards', '27/3425', 1, '', 0, '', 0, ''),
(3429, 165, '', 'Trả lời tự động', 'admin', '', '', '', '', '[]', 7, 1, 1, 1, '/setting/store_service/auto_reply', '165', 1, '', 0, '', 0, ''),
(3430, 1695, '', 'Từ điển dữ liệu', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/code_data_dictionary', '25/1695', 1, '', 0, 'system-code-data_dictionary', 0, ''),
(3431, 3430, '', 'Xem từ điển dữ liệu', '', '', '', 'system/crud/data_dictionary/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary', 0, ''),
(3432, 3430, '', 'Xóa từ điển dữ liệu', '', '', '', 'system/crud/data_dictionary/<id>', 'DELETE', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary', 0, ''),
(3433, 3430, '', 'Sửa hoặc lưu dữ liệu từ điển', '', '', '', 'system/crud/data_dictionary/<id?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary', 0, ''),
(3434, 3430, '', 'Lấy danh sách từ điển dữ liệu', '', '', '', 'system/crud/data_dictionary', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary', 0, ''),
(3435, 3299, '', 'Lấy danh sách từ điển dữ liệu', '', '', '', 'system/crud/data_dictionary', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary-64d491f3da358', 0, ''),
(3436, 3299, '', 'Xem từ điển dữ liệu', '', '', '', 'system/crud/data_dictionary/<id>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary-64d491f3da391', 0, ''),
(3437, 3299, '', 'Sửa hoặc lưu dữ liệu từ điển', '', '', '', 'system/crud/data_dictionary/<id?>', 'POST', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-data_dictionary-64d491f3da3b8', 0, ''),
(3438, 3299, '', 'Lấy tên các bảng có thể liên kết', '', '', '', 'system/crud/association_table', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-association_table', 0, ''),
(3439, 3299, '', 'Lấy thông tin chi tiết của bảng', '', '', '', 'system/crud/association_table/<tableName>', 'GET', '[]', 1, 1, 1, 1, '', '', 2, '', 0, 'system-crud-association_table', 0, ''),
(3440, 656, '', 'Danh mục sản phẩm', 'admin', '', '', '', '', '[]', 95, 1, 1, 1, '/setting/pages/cate_page/1', '656', 1, '', 0, '', 0, ''),
(3441, 656, '', 'Trang cá nhân', 'admin', '', '', '', '', '[]', 90, 1, 1, 1, '/setting/pages/user_page/2', '656', 1, '', 0, '', 0, ''),
(3442, 993, '', 'Liên kết Mini Program', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/app/routine/link', '135/993', 1, '', 0, '', 0, ''),
(3443, 56, '', 'Cấu hình mô-đun', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/integral/system_config/2/134', '25/56', 1, '', 0, 'system-model-system_config', 0, ''),
(3444, 3417, '', 'Hóa đơn điện tử', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/elec_invoice', '12/1056/3417', 1, '', 0, 'setting-elec-invoice', 0, ''),
(3445, 56, '', 'Sự kiện tùy chỉnh', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/system/event', '25/56', 1, '', 0, 'system-event-index', 0, ''),
(3446, 27, '', 'Quà tặng người mới', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/newuser/gift', '27', 1, '', 0, 'admin-marketing-new-user-gift', 0, ''),
(3447, 1, '', 'Cấu hình sản phẩm', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/system_config/2/136', '1', 1, '', 0, 'admin-setting-system_config', 0, ''),
(3448, 12, '', 'Cấu hình biên lai', 'admin', '', '', '', '', '[]', 8, 1, 1, 1, '/setting/ticket', '12', 1, '', 0, 'admi-setting-ticket', 0, ''),
(3449, 3448, '', 'Cài đặt nội dung', 'admin', '', '', '', '', '[]', 0, 0, 0, 1, '/setting/ticket/content', '12/3448', 1, '', 0, 'admin-setting-ticket-content', 0, ''),
(3450, 26, '', 'Đơn đăng ký cộng tác viên', 'admin', '', '', '', '', '[]', 97, 1, 1, 1, '/agent/spread/apply', '26', 1, '', 0, 'admin-agent-spread-apply', 0, ''),
(3451, 909, '', 'Danh sách quay thưởng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/lottery/list', '27/909', 1, '', 0, 'admin-marketing-lottery-list', 0, ''),
(3452, 1, '', 'Thông số sản phẩm', 'admin', '', '', '', '', '[]', 1, 1, 1, 1, '/product/param/list', '1', 1, '', 0, 'admin-product-param-list', 0, ''),
(3453, 656, '', 'Quản lý liên kết', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/setting/pages/link', '656', 1, '', 0, 'admin-setting-pages-link', 0, ''),
(3454, 1, '', 'Nhãn sản phẩm', 'admin', '', '', '', '', '[]', 1, 1, 1, 1, '/product/label/list', '1', 1, '', 0, 'admin-product-label-list', 0, ''),
(3455, 1, '', 'Đảm bảo sản phẩm', 'admin', '', '', '', '', '[]', 1, 1, 1, 1, '/product/protection/list', '1', 1, '', 0, 'admin-product-protection-list', 0, ''),
(3456, 33, '', 'Danh sách flash sale', 'admin', '', '', '', '', '[]', 1, 1, 1, 1, '/marketing/store_seckill/list', '27/33', 1, '', 0, 'marketing-store_seckill-list', 0, ''),
(3457, 909, '', 'Cấu hình quay thưởng', 'admin', '', '', '', '', '[]', 0, 1, 1, 1, '/marketing/lottery/config', '27/909', 1, '', 0, 'admin-marketing-lottery-config', 0, '');
SQL
            ],[
                'code' => 560,
                'type' => -1,
                'table' => "system_notification",
                'sql' => "INSERT INTO `eb_system_notification` (`id`, `mark`, `name`, `title`, `is_system`, `system_title`, `system_text`, `is_wechat`, `wechat_tempkey`, `wechat_content`, `wechat_kid`, `wechat_tempid`, `is_routine`, `routine_tempkey`, `routine_content`, `routine_kid`, `routine_tempid`, `is_sms`, `sms_id`, `sms_text`, `is_ent_wechat`, `ent_wechat_text`, `url`, `is_app`, `app_id`, `variable`, `type`, `add_time`) VALUES (NULL, 'revenue_received', 'Thông báo thu nhập về tài khoản', 'Thông báo cho người dùng khi thu nhập về tài khoản', 0, '', '', 1, '47862', 'Mã đơn hàng: {{character_string9.DATA}}\nLoại giao dịch: {{thing3.DATA}}\nSố tiền giao dịch: {{amount4.DATA}}\nThời gian giao dịch: {{time2.DATA}}', '', '', 1, '1493', 'Mã đơn hàng: {{character_string1.DATA}}\r\nKhoản thu: {{thing7.DATA}}\r\nSố tiền thu nhập: {{amount3.DATA}}\r\nThời gian đơn hàng: {{time10.DATA}}', '', '', 0, '', '', 0, '', '', 0, 0, '', 1, 0)"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "user_extract",
                'field' => "channel_type",
                'findSql' => "show columns from `@table` like 'channel_type'",
                'sql' => "ALTER TABLE `@table` ADD `channel_type` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'Nguồn rút tiền' AFTER `qrcode_url`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "user_extract",
                'field' => "out_bill_no",
                'findSql' => "show columns from `@table` like 'out_bill_no'",
                'sql' => "ALTER TABLE `@table` ADD `out_bill_no` varchar(255) NOT NULL DEFAULT '' COMMENT 'Mã đơn phía merchant' AFTER `channel_type`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "user_extract",
                'field' => "transfer_bill_no",
                'findSql' => "show columns from `@table` like 'transfer_bill_no'",
                'sql' => "ALTER TABLE `@table` ADD `transfer_bill_no` varchar(255) NOT NULL DEFAULT '' COMMENT 'Mã chuyển khoản WeChat' AFTER `out_bill_no`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "user_extract",
                'field' => "state",
                'findSql' => "show columns from `@table` like 'state'",
                'sql' => "ALTER TABLE `@table` ADD `state` varchar(32) NOT NULL DEFAULT '' COMMENT 'Trạng thái chứng từ' AFTER `transfer_bill_no`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "user_extract",
                'field' => "package_info",
                'findSql' => "show columns from `@table` like 'package_info'",
                'sql' => "ALTER TABLE `@table` ADD `package_info` varchar(2000) NOT NULL DEFAULT '' COMMENT 'Thông tin package để chuyển đến trang nhận tiền' AFTER `state`"
            ],[
                'code' => 560,
                'type' => 3,
                'table' => "user_extract",
                'field' => "fail_reason",
                'findSql' => "show columns from `@table` like 'fail_reason'",
                'sql' => "ALTER TABLE `@table` ADD `fail_reason` varchar(255) NOT NULL DEFAULT '' COMMENT 'Lý do thất bại' AFTER `package_info`"
            ],[
                'code' => 560.1,
                'type' => -1,
                'table' => "agreement",
                'sql' => "ALTER TABLE `@table` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nội dung thỏa thuận';"
            ],[
                'code' => 560.1,
                'type' => -1,
                'table' => "article_content",
                'sql' => "ALTER TABLE `@table` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nội dung bài viết';"
            ],[
                'code' => 560.1,
                'type' => -1,
                'table' => "store_bargain",
                'sql' => "ALTER TABLE `@table` CHANGE `rule` `rule` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Quy tắc săn giảm giá';"
            ],[
                'code' => 560.1,
                'type' => -1,
                'table' => "store_product_description",
                'sql' => "ALTER TABLE `@table` CHANGE `description` `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Chi tiết sản phẩm';"
            ],[
                'code' => 561,
                'type' => -1,
                'table' => "store_coupon_issue",
                'sql' => "ALTER TABLE `@table` CHANGE `product_id` `product_id` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'ID sản phẩm áp dụng';"
            ],[
                'code' => 561,
                'type' => -1,
                'table' => "store_coupon_issue",
                'sql' => "ALTER TABLE `@table` CHANGE `category_id` `category_id` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'ID danh mục';"
            ],[
                'code' => 561,
                'type' => -1,
                'table' => "system_config",
                'sql' => "UPDATE `@table` SET `info` = 'JS tùy chỉnh cho thiết bị di động', `desc` = 'JS tùy chỉnh được tải trên thiết bị di động' WHERE `menu_name` = 'statistic_script';"
            ],[
                'code' => 561,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'custom_admin_js'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='statistics_config'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'custom_admin_js', 'textarea', 'input', @tabId, '', 1, '', 100, 10, '\"\"', 'JS tùy chỉnh cho trang quản trị', 'JS tùy chỉnh được tải trên trang quản trị', 0, 1, 0, 0, 0);"
            ],[
                'code' => 561,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'custom_pc_js'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='statistics_config'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'custom_pc_js', 'textarea', 'input', @tabId, '', 1, '', 100, 10, '\"\"', 'JS tùy chỉnh cho PC', 'JS tùy chỉnh được tải trên PC', 0, 1, 0, 0, 0);"
            ],[
                'code' => 561,
                'type' => -1,
                'table' => "system_config_tab",
                'sql' => "UPDATE `@table` SET `title` = 'JS tùy chỉnh' WHERE `eng_title` = 'statistics_config';"
            ],[
                'code' => 562,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'merchant_cert_path'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='ali_pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'merchant_cert_path', 'upload', 'input', @tabId, '', 3, '', 0, 0, '\"\"', 'Chứng chỉ khóa công khai ứng dụng', 'Chứng chỉ khóa công khai ứng dụng được tải xuống sau khi hoàn tất thiết lập ký API Alipay', 0, 1, 1, 489, 1);"
            ],[
                'code' => 562,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'alipay_cert_path'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='ali_pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'alipay_cert_path', 'upload', 'input', @tabId, '', 3, '', 0, 0, '\"\"', 'Chứng chỉ khóa công khai Alipay', 'Chứng chỉ khóa công khai Alipay được tải xuống sau khi hoàn tất thiết lập ký API Alipay', 0, 1, 1, 489, 1);"
            ],[
                'code' => 562,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'alipay_root_cert_path'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='ali_pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'alipay_root_cert_path', 'upload', 'input', @tabId, '', 3, '', 0, 0, '\"\"', 'Chứng chỉ gốc Alipay', 'Chứng chỉ gốc Alipay được tải xuống sau khi hoàn tất thiết lập ký API Alipay', 0, 1, 1, 489, 1);"
            ],[
                'code' => 562,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'alipay_sign_type'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='ali_pay'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'alipay_sign_type', 'radio', 'input', @tabId, '0=>Khóa\n1=>Chứng chỉ', 1, '', 0, 0, '0', 'Kiểu ký API', 'Kiểu ký API: khóa hoặc chứng chỉ', 80, 1, 0, 0, 0);"
            ],[
                'code' => 562,
                'type' => -1,
                'table' => "system_config",
                'sql' => "UPDATE `@table` SET `level` = 1, `link_id` = 489, `link_value` = 0 WHERE `menu_name` = 'alipay_public_key';"
            ],[
                'code' => 562,
                'type' => -1,
                'table' => "system_config",
                'sql' => "UPDATE `@table` SET `sort` = 85 WHERE `menu_name` = 'alipay_merchant_private_key';"
            ],[
                'code' => 562,
                'type' => -1,
                'table' => "system_config",
                'sql' => "UPDATE `@table` SET `sort` = 91 WHERE `menu_name` = 'ali_pay_appid';"
            ],[
                'code' => 562,
                'type' => 6,
                'table' => "system_config",
                'whereTable' => "system_config_tab",
                'findSql' => "select id from @table where `menu_name` = 'image_thumb_status'",
                'whereSql' => "SELECT id as tabId FROM `@whereTable` WHERE `eng_title`='base_config'",
                'sql' => "INSERT INTO `@table` VALUES (null, 'image_thumb_status', 'radio', 'input', @tabId, '1=>Bật\n0=>Tắt', 1, '', 0, 0, '0', 'Bật/tắt ảnh thu nhỏ', 'Có bật ảnh thu nhỏ hay không', 0, 1, 0, 0, 0);"
            ],
        ];
        return $data;
    }

    /**
     * Danh sách nâng cấp
     * @return mixed
     */
    public function upgradeList()
    {
        return app('json')->success([]);
        return app('json')->success($this->services->getUpgradeList());
    }

    /**
     * Danh sách có thể nâng cấp
     * @return mixed
     */
    public function upgradeableList()
    {
        return app('json')->success([]);
        return app('json')->success($this->services->getUpgradeableList());
    }

    /**
     * Danh sách có thể nâng cấp
     * @return mixed
     */
    public function agreement()
    {
        return app('json')->success($this->services->getAgreement());
    }

    /**
     * Tải gói nâng cấp
     * @param $packageKey
     * @return mixed
     */
    public function download($packageKey)
    {
        if (empty($packageKey)) {
            return app('json')->fail(100100);
        }

        $this->services->packageDownload($packageKey);
        return app('json')->success();
    }

    /**
     * Tiến trình nâng cấp
     * @return mixed
     */
    public function progress()
    {
        $result = $this->services->getProgress();
        return app('json')->success($result);
    }

    /**
     * Lấy trạng thái nâng cấp
     * @return mixed
     */
    public function upgradeStatus()
    {
        return app('json')->success([]);
        $data = $this->services->getUpgradeStatus();
        return app('json')->success($data);
    }

    /**
     * Lịch sử nâng cấp
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function upgradeLogList()
    {
        return app('json')->success([]);
        $data = $this->services->getUpgradeLogList();
        return app('json')->success($data);
    }

    /**
     * Xuất bản sao lưu
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function export($id, $type)
    {
        if (!$id || !$type) {
            return app('json')->fail(100026);
        }
        return app('json')->success($this->services->export((int)$id, $type));
    }
}
