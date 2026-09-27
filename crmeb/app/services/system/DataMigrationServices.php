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

use think\facade\Log;
use think\facade\Cache;
use app\services\BaseServices;
use app\services\user\UserBillServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserBrokerageFrozenServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\activity\coupon\StoreCouponProductServices;

/**
 * Service di chuyển dữ liệu (migration)
 * Dùng để xử lý migration dữ liệu lịch sử khi nâng cấp vượt phiên bản
 * Class DataMigrationServices
 * @package app\services\system
 */
class DataMigrationServices extends BaseServices
{
    /**
     * Tiền tố cache trạng thái migration
     */
    const MIGRATION_STATUS_PREFIX = 'data_migration_';
    
    /**
     * Kích thước phân trang mặc định
     */
    const DEFAULT_LIMIT = 100;

    /**
     * Kiểm tra migration đã hoàn tất chưa
     * @param string $name Tên migration
     * @return bool
     */
    public function isMigrationCompleted(string $name): bool
    {
        return Cache::get(self::MIGRATION_STATUS_PREFIX . $name) === 'completed';
    }

    /**
     * Đánh dấu migration đã hoàn tất
     * @param string $name Tên migration
     * @return void
     */
    public function markMigrationCompleted(string $name): void
    {
        Cache::set(self::MIGRATION_STATUS_PREFIX . $name, 'completed', 86400 * 30);
    }

    /**
     * Lấy tiến độ migration
     * @param string $name Tên migration
     * @return array
     */
    public function getMigrationProgress(string $name): array
    {
        $page = Cache::get(self::MIGRATION_STATUS_PREFIX . $name . '_page', 1);
        $total = Cache::get(self::MIGRATION_STATUS_PREFIX . $name . '_total', 0);
        $processed = Cache::get(self::MIGRATION_STATUS_PREFIX . $name . '_processed', 0);
        
        return [
            'page' => $page,
            'total' => $total,
            'processed' => $processed
        ];
    }

    /**
     * Cập nhật tiến độ migration
     * @param string $name Tên migration
     * @param int $page Trang hiện tại
     * @param int $processed Số lượng đã xử lý
     * @return void
     */
    protected function updateMigrationProgress(string $name, int $page, int $processed): void
    {
        Cache::set(self::MIGRATION_STATUS_PREFIX . $name . '_page', $page, 86400);
        Cache::set(self::MIGRATION_STATUS_PREFIX . $name . '_processed', $processed, 86400);
    }

    /**
     * Thực thi handler migration dữ liệu
     * @param array $handler Cấu hình handler
     * @return array ['success' => bool, 'message' => string, 'completed' => bool]
     */
    public function executeHandler(array $handler): array
    {
        $name = $handler['name'] ?? '';
        $method = $handler['handler'] ?? '';
        $limit = $handler['limit'] ?? self::DEFAULT_LIMIT;
        $title = $handler['title'] ?? $name;
        
        if (!$name || !$method) {
            return ['success' => false, 'message' => 'Lỗi cấu hình di chuyển dữ liệu', 'completed' => false];
        }
        
        // Kiểm tra đã hoàn tất chưa
        if ($this->isMigrationCompleted($name)) {
            return ['success' => true, 'message' => $title . ' đã hoàn thành', 'completed' => true, 'skipped' => true];
        }
        
        // Kiểm tra phương thức có tồn tại không
        if (!method_exists($this, $method)) {
            return ['success' => false, 'message' => 'Phương thức di chuyển không tồn tại: ' . $method, 'completed' => false];
        }
        
        try {
            // Lấy tiến độ hiện tại
            $progress = $this->getMigrationProgress($name);
            $page = $progress['page'];
            
            // Thực thi phương thức migration
            $result = $this->$method($page, $limit);
            
            if ($result['completed']) {
                $this->markMigrationCompleted($name);
                return [
                    'success' => true,
                    'message' => $title . ' di chuyển hoàn tất',
                    'completed' => true,
                    'processed' => $result['processed'] ?? 0
                ];
            } else {
                // Cập nhật tiến độ
                $this->updateMigrationProgress($name, $page + 1, ($progress['processed'] ?? 0) + ($result['count'] ?? 0));
                return [
                    'success' => true,
                    'message' => $title . ' đang xử lý (trang ' . $page . ')',
                    'completed' => false,
                    'processed' => $result['count'] ?? 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('Di chuyển dữ liệu thất bại: ' . $e->getMessage(), ['handler' => $handler]);
            return ['success' => false, 'message' => $title . ' di chuyển thất bại: ' . $e->getMessage(), 'completed' => false];
        }
    }

    /**
     * Thực thi tất cả handler migration dữ liệu (lặp cho đến khi hoàn tất toàn bộ)
     * @param array $handlers Danh sách handler
     * @return array
     */
    public function executeAllHandlers(array $handlers): array
    {
        $results = [];
        $allCompleted = true;
        
        foreach ($handlers as $handler) {
            $name = $handler['name'] ?? '';
            
            // Lặp thực thi cho đến khi hoàn tất
            while (!$this->isMigrationCompleted($name)) {
                $result = $this->executeHandler($handler);
                
                if (!$result['success']) {
                    $results[$name] = $result;
                    $allCompleted = false;
                    break; // Thất bại thì bỏ qua handler này
                }
                
                if ($result['completed']) {
                    $results[$name] = $result;
                    break;
                }
            }
            
            if (!isset($results[$name])) {
                $results[$name] = ['success' => true, 'message' => ($handler['title'] ?? $name) . ' đã bỏ qua', 'completed' => true, 'skipped' => true];
            }
        }
        
        return [
            'success' => $allCompleted,
            'results' => $results
        ];
    }

    // ==================== Phương thức migration dữ liệu ====================

    /**
     * Xử lý dữ liệu số dư lịch sử
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function handleMoney(int $page = 1, int $limit = 100): array
    {
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $where = ['category' => 'now_money', 'type' => ['pay_product', 'pay_product_refund', 'system_add', 'system_sub', 'recharge', 'lottery_use', 'lottery_add']];
        $list = $userBillServices->getList($where, '*', $page, $limit, [], 'id asc');
        
        if (empty($list)) {
            return ['completed' => true, 'processed' => 0];
        }
        
        $allData = [];
        foreach ($list as $item) {
            $allData[] = [
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
        }
        
        if ($allData) {
            /** @var UserMoneyServices $userMoneyServices */
            $userMoneyServices = app()->make(UserMoneyServices::class);
            $userMoneyServices->saveAll($allData);
        }
        
        Log::notice(['type' => 'data_migration', 'handler' => 'handleMoney', 'page' => $page, 'count' => count($list)]);
        
        return ['completed' => false, 'count' => count($list)];
    }

    /**
     * Xử lý dữ liệu hoa hồng lịch sử
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function handleBrokerage(int $page = 1, int $limit = 100): array
    {
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $where = ['category' => ['', 'now_money'], 'type' => ['brokerage', 'brokerage_user', 'extract', 'refund', 'extract_fail']];
        $list = $userBillServices->getList($where, '*', $page, $limit, [], 'id asc');
        
        if (empty($list)) {
            return ['completed' => true, 'processed' => 0];
        }
        
        $allData = [];
        /** @var UserBrokerageFrozenServices $brokerageFrozenServices */
        $brokerageFrozenServices = app()->make(UserBrokerageFrozenServices::class);
        $frozenList = $brokerageFrozenServices->getColumn([['uill_id', 'in', array_column($list, 'id')], ['frozen_time', '>', time()]], 'uill_id,frozen_time', 'uill_id');
        
        foreach ($list as $item) {
            if (in_array($item['type'], ['brokerage_user', 'extract', 'refund', 'extract_fail'])) {
                $type = $item['type'];
            } else {
                $type = strpos($item['mark'], 'Cấp 2') !== false ? 'two_brokerage' : 'one_brokerage';
            }
            
            $allData[] = [
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
        }
        
        if ($allData) {
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $userBrokerageServices->saveAll($allData);
        }
        
        Log::notice(['type' => 'data_migration', 'handler' => 'handleBrokerage', 'page' => $page, 'count' => count($list)]);
        
        return ['completed' => false, 'count' => count($list)];
    }

    /**
     * Xử lý dữ liệu hoàn tiền lịch sử
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function handleOrderRefund(int $page = 1, int $limit = 100): array
    {
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $list = $storeOrderServices->getSplitOrderList(['refund_status' => [1, 2], ['refund_type' => [1, 2, 4, 5, 6]]], ['*'], [], $page, $limit, 'id asc');
        
        if (empty($list)) {
            return ['completed' => true, 'processed' => 0];
        }
        
        $allData = [];
        /** @var StoreOrderCreateServices $storeOrderCreateServices */
        $storeOrderCreateServices = app()->make(StoreOrderCreateServices::class);
        /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
        $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
        
        foreach ($list as $order) {
            $cartInfos = $storeOrderCartInfoServices->getCartColunm(['oid' => $order['id']], 'id,cart_id,cart_num,cart_info');
            foreach ($cartInfos as &$cartInfo) {
                $cartInfo['cart_info'] = is_string($cartInfo['cart_info']) ? json_decode($cartInfo['cart_info'], true) : $cartInfo['cart_info'];
            }
            
            $allData[] = [
                'uid' => $order['uid'],
                'store_id' => $order['store_id'],
                'store_order_id' => $order['id'],
                'order_id' => $storeOrderCreateServices->getNewOrderId(''),
                'refund_num' => $order['total_num'],
                'refund_type' => $order['refund_type'],
                'refund_price' => $order['pay_price'],
                'refunded_price' => 0,
                'refund_explain' => $order['refund_reason_wap_explain'],
                'refund_img' => $order['refund_reason_wap_img'],
                'refund_reason' => $order['refund_reason_wap'],
                'refund_express' => $order['refund_express'],
                'refunded_time' => $order['refund_type'] == 6 ? $order['refund_reason_time'] : 0,
                'add_time' => $order['refund_reason_time'],
                'cart_info' => json_encode(array_column($cartInfos, 'cart_info'))
            ];
        }
        
        if ($allData) {
            /** @var StoreOrderRefundServices $storeOrderRefundServices */
            $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
            $storeOrderRefundServices->saveAll($allData);
        }
        
        Log::notice(['type' => 'data_migration', 'handler' => 'handleOrderRefund', 'page' => $page, 'count' => count($list)]);
        
        return ['completed' => false, 'count' => count($list)];
    }

    /**
     * Cập nhật UID của bảng sản phẩm trong đơn hàng
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function handleCartInfo(int $page = 1, int $limit = 100): array
    {
        /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
        $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $list = $storeOrderCartInfoServices->selectList(['uid' => 0], 'id,oid', $page, $limit)->toArray();
        
        if (empty($list)) {
            return ['completed' => true, 'processed' => 0];
        }
        
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $uids = $storeOrderServices->getColumn([['id', 'in', array_column($list, 'oid')]], 'uid', 'id');
        
        $allData = [];
        foreach ($list as $cart) {
            $allData[] = [
                'id' => $cart['id'],
                'uid' => $uids[$cart['oid']] ?? 0
            ];
        }
        
        if ($allData) {
            $storeOrderCartInfoServices->saveAll($allData);
        }
        
        Log::notice(['type' => 'data_migration', 'handler' => 'handleCartInfo', 'page' => $page, 'count' => count($list)]);
        
        return ['completed' => false, 'count' => count($list)];
    }

    /**
     * Cập nhật dữ liệu phiếu giảm giá theo danh mục
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function handleCoupon(int $page = 1, int $limit = 100): array
    {
        /** @var StoreCouponIssueServices $couponIssueServices */
        $couponIssueServices = app()->make(StoreCouponIssueServices::class);
        $list = $couponIssueServices->selectList([['category_id', '>', 0]], 'id,category_id', $page, $limit)->toArray();
        
        if (empty($list)) {
            return ['completed' => true, 'processed' => 0];
        }
        
        $allData = [];
        foreach ($list as $item) {
            $allData[] = [
                'coupon_id' => $item['id'],
                'product_id' => 0,
                'category_id' => $item['category_id']
            ];
        }
        
        if ($allData) {
            /** @var StoreCouponProductServices $couponProductServices */
            $couponProductServices = app()->make(StoreCouponProductServices::class);
            $couponProductServices->saveAll($allData);
        }
        
        Log::notice(['type' => 'data_migration', 'handler' => 'handleCoupon', 'page' => $page, 'count' => count($list)]);
        
        return ['completed' => false, 'count' => count($list)];
    }
}
