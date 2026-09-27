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
namespace app\outapi\controller;

use think\facade\Db;
use think\Request;

/**
 * Controller MCP (Model Context Protocol)
 * Cung cấp giao diện chuẩn để trợ lý AI gọi API của CRMEB
 *
 * Phương thức xác thực: account + password
 * Xác thực bằng cách truyền tài khoản và mật khẩu qua header yêu cầu
 */
class Mcp extends AuthController
{
    /**
     * Khởi tạo
     * API MCP không đi qua middleware Token, xác thực trực tiếp bằng appid + appsecret
     */
    protected function initialize()
    {
        // Không gọi initialize của lớp cha vì MCP không đi qua middleware Token
        $this->authByAppSecret();
    }

    /**
     * Xác thực bằng appid + appsecret
     * Tham khảo quy trình xác thực của AuthTokenMiddleware
     */
    private function authByAppSecret()
    {
        $account = $this->request->header('account', '');
        $password = $this->request->header('password', '');

        if (empty($account) || empty($password)) {
            $this->authFail('Xác thực thất bại: thiếu account hoặc password');
            return;
        }

        try {
            // Truy vấn thông tin tài khoản
            $accountInfo = Db::name('out_account')
                ->where('appid', $account)
                ->where('is_del', 0)
                ->find();

            // Tài khoản không tồn tại
            if (!$accountInfo) {
                $this->authFail('Tài khoản không tồn tại');
                return;
            }

            // Xác thực mật khẩu
            if (!password_verify($password, $accountInfo['appsecret'])) {
                $this->authFail('Xác minh mật khẩu thất bại');
                return;
            }

            // Kiểm tra trạng thái tài khoản (status=0 hoặc status=2 nghĩa là đã bị vô hiệu hóa)
            if ($accountInfo['status'] == 0 || $accountInfo['status'] == 2) {
                $this->authFail('Tài khoản đã bị vô hiệu hóa');
                return;
            }

            // Xác thực thành công, thiết lập thông tin tài khoản
            $this->outId = (int)$accountInfo['id'];
            $this->outInfo = $accountInfo;

            // Xác thực quyền truy cập API (tham khảo AuthTokenMiddleware)
            // $this->verifyAuth();

        } catch (\crmeb\exceptions\AuthException $e) {
            // Chuyển AuthException thành thông báo lỗi thân thiện
            $this->authFail('Bạn tạm thời không có quyền truy cập');
        } catch (\Exception $e) {
            // Không để lộ thông tin lỗi cụ thể
            $this->authFail('Xác thực thất bại');
        }
    }

    /**
     * Xác thực quyền truy cập API
     * Tham khảo logic verifyAuth của AuthTokenMiddleware
     * API MCP cần kiểm tra quyền theo route
     */
    private function verifyAuth()
    {
        try {
            // Đưa outId và outInfo vào request (mô phỏng hành vi của middleware)
            $outInfo = $this->outInfo;
            $this->request->macro('outId', function () use (&$outInfo) {
                return (int)$outInfo['id'];
            });
            $this->request->macro('outInfo', function () use (&$outInfo) {
                return $outInfo;
            });

            // Gọi service xác thực quyền truy cập API
            $outInterfaceServices = app()->make(\app\services\out\OutInterfaceServices::class);
            $outInterfaceServices->verifyAuth($this->request);

        } catch (\crmeb\exceptions\AuthException $e) {
            // Xác thực quyền thất bại, ném ra thông báo lỗi thân thiện
            throw new \crmeb\exceptions\AuthException(110000); // Không có quyền truy cập
        } catch (\Exception $e) {
            // Các ngoại lệ khác đều trả về không có quyền
            throw new \crmeb\exceptions\AuthException(110000);
        }
    }

    /**
     * Xử lý khi xác thực thất bại
     * Đặt cờ lỗi, phản hồi lỗi sẽ được trả về trong phương thức index
     */
    private function authFail(string $message)
    {
        $this->outId = 0;
        $this->outInfo = ['error' => $message];
    }

    /**
     * Lấy danh sách định nghĩa công cụ MCP
     * Định nghĩa tất cả công cụ mà trợ lý AI có thể gọi cùng cấu trúc tham số của chúng
     *
     * @return array Mảng định nghĩa công cụ
     */
    private function getTools(): array
    {
        return [
            // Quản lý danh mục
            [
                'name' => 'crmeb_category_list',
                'description' => 'Lấy danh sách danh mục sản phẩm, hỗ trợ hiển thị dạng cây',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'number', 'description' => 'Số trang (có hiệu lực khi không ở chế độ cây)'],
                        'limit' => ['type' => 'number', 'description' => 'Số lượng mỗi trang (có hiệu lực khi không ở chế độ cây)'],
                        'tree' => ['type' => 'boolean', 'description' => 'Có trả về cấu trúc cây hay không, mặc định là true'],
                        'pid' => ['type' => 'number', 'description' => 'ID cấp cha, nếu chỉ định thì chỉ trả về các danh mục thuộc cấp cha đó'],
                    ],
                ],
            ],
            [
                'name' => 'crmeb_category_detail',
                'description' => 'Lấy chi tiết danh mục',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ID danh mục'],
                    ],
                    'required' => ['id'],
                ],
            ],

            // Quản lý sản phẩm
            [
                'name' => 'crmeb_product_list',
                'description' => 'Lấy danh sách sản phẩm',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'number', 'description' => 'Số trang'],
                        'limit' => ['type' => 'number', 'description' => 'Số lượng mỗi trang'],
                        'cate_id' => ['type' => 'number', 'description' => 'ID danh mục'],
                        'keyword' => ['type' => 'string', 'description' => 'Từ khóa tìm kiếm'],
                        'stock_min' => ['type' => 'number', 'description' => 'Tồn kho tối thiểu'],
                        'stock_max' => ['type' => 'number', 'description' => 'Tồn kho tối đa'],
                    ],
                ],
            ],
            [
                'name' => 'crmeb_product_detail',
                'description' => 'Lấy chi tiết sản phẩm',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ID sản phẩm'],
                    ],
                    'required' => ['id'],
                ],
            ],

            // Quản lý đơn hàng
            [
                'name' => 'crmeb_order_list',
                'description' => 'Lấy danh sách đơn hàng',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'number', 'description' => 'Số trang'],
                        'limit' => ['type' => 'number', 'description' => 'Số lượng mỗi trang'],
                        'status' => ['type' => 'number', 'description' => 'Trạng thái đơn hàng'],
                        'keyword' => ['type' => 'string', 'description' => 'Từ khóa tìm kiếm'],
                    ],
                ],
            ],
            [
                'name' => 'crmeb_order_detail',
                'description' => 'Lấy chi tiết đơn hàng',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'order_id' => ['type' => 'string', 'description' => 'Mã đơn hàng'],
                    ],
                    'required' => ['order_id'],
                ],
            ],
            [
                'name' => 'crmeb_order_express_list',
                'description' => 'Lấy danh sách đơn vị vận chuyển',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],

            // Quản lý hậu mãi
            [
                'name' => 'crmeb_refund_list',
                'description' => 'Lấy danh sách đơn hậu mãi',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'number', 'description' => 'Số trang'],
                        'limit' => ['type' => 'number', 'description' => 'Số lượng mỗi trang'],
                    ],
                ],
            ],
            [
                'name' => 'crmeb_refund_detail',
                'description' => 'Lấy chi tiết đơn đổi trả',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'order_id' => ['type' => 'string', 'description' => 'Mã đơn đổi trả'],
                    ],
                    'required' => ['order_id'],
                ],
            ],

            // Quản lý phiếu giảm giá
            [
                'name' => 'crmeb_coupon_list',
                'description' => 'Lấy danh sách phiếu giảm giá',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'number', 'description' => 'Số trang'],
                        'limit' => ['type' => 'number', 'description' => 'Số lượng mỗi trang'],
                    ],
                ],
            ],

            // Quản lý người dùng
            [
                'name' => 'crmeb_user_list',
                'description' => 'Lấy danh sách người dùng',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'page' => ['type' => 'number', 'description' => 'Số trang'],
                        'limit' => ['type' => 'number', 'description' => 'Số lượng mỗi trang'],
                        'keyword' => ['type' => 'string', 'description' => 'Từ khóa tìm kiếm'],
                    ],
                ],
            ],
            [
                'name' => 'crmeb_user_detail',
                'description' => 'Lấy chi tiết người dùng',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'uid' => ['type' => 'number', 'description' => 'ID người dùng'],
                    ],
                    'required' => ['uid'],
                ],
            ],
        ];
    }

    /**
     * Xử lý lời gọi công cụ
     * Phân phối đến phương thức xử lý tương ứng theo tên công cụ
     *
     * @param string $name Tên công cụ
     * @param array $args Tham số công cụ
     * @return array Kết quả xử lý
     * @throws \Exception Ném ngoại lệ khi công cụ không xác định hoặc tham số sai
     */
    private function handleToolCall(string $name, array $args = [])
    {
        switch ($name) {
            // Quản lý danh mục
            case 'crmeb_category_list':
                return $this->categoryList($args);
            case 'crmeb_category_detail':
                if (!isset($args['id']) || !is_numeric($args['id'])) {
                    throw new \Exception('Tham số không hợp lệ: thiếu id hoặc sai định dạng');
                }
                return $this->categoryDetail((int)$args['id']);

            // Quản lý sản phẩm
            case 'crmeb_product_list':
                return $this->productList($args);
            case 'crmeb_product_detail':
                if (!isset($args['id']) || !is_numeric($args['id'])) {
                    throw new \Exception('Tham số không hợp lệ: thiếu id hoặc sai định dạng');
                }
                return $this->productDetail((int)$args['id']);

            // Quản lý đơn hàng
            case 'crmeb_order_list':
                return $this->orderList($args);
            case 'crmeb_order_detail':
                if (empty($args['order_id'])) {
                    throw new \Exception('Tham số không hợp lệ: thiếu order_id');
                }
                return $this->orderDetail($args['order_id']);
            case 'crmeb_order_express_list':
                return $this->orderExpressList();

            // Quản lý hậu mãi
            case 'crmeb_refund_list':
                return $this->refundList($args);
            case 'crmeb_refund_detail':
                if (empty($args['order_id'])) {
                    throw new \Exception('Tham số không hợp lệ: thiếu order_id');
                }
                return $this->refundDetail($args['order_id']);

            // Quản lý phiếu giảm giá
            case 'crmeb_coupon_list':
                return $this->couponList($args);

            // Quản lý người dùng
            case 'crmeb_user_list':
                return $this->userList($args);
            case 'crmeb_user_detail':
                if (!isset($args['uid']) || !is_numeric($args['uid'])) {
                    throw new \Exception('Tham số không hợp lệ: thiếu uid hoặc sai định dạng');
                }
                return $this->userDetail((int)$args['uid']);

            default:
                throw new \Exception("Công cụ không xác định: {$name}");
        }
    }

    // ==================== Quản lý danh mục ====================

    /**
     * Lấy danh sách danh mục sản phẩm
     *
     * @param array $args Tham số truy vấn
     *   - page: số trang, mặc định 1 (có hiệu lực khi không ở chế độ cây)
     *   - limit: số lượng mỗi trang, mặc định 10, tối đa 100 (có hiệu lực khi không ở chế độ cây)
     *   - tree: có trả về cấu trúc cây không, mặc định false
     *   - pid: ID cấp cha, nếu chỉ định thì chỉ trả về các danh mục thuộc cấp cha đó
     * @return array Danh sách danh mục và tổng số
     */
    private function categoryList(array $args): array
    {
        $page = max(1, (int)($args['page'] ?? 1));
        $limit = min(100, max(1, (int)($args['limit'] ?? 10))); // Giới hạn tối đa 100
        $isTree = $args['tree'] ?? true;
        $pid = $args['pid'] ?? null;

        // Xây dựng truy vấn cơ bản
        $query = Db::name('store_category')->where('is_show', 1);

        // Nếu đã chỉ định ID cấp cha
        if ($pid !== null) {
            $query = $query->where('pid', $pid);
        }

        // Chế độ cây: lấy tất cả danh mục và dựng cây
        if ($isTree) {
            $allList = Db::name('store_category')
                ->where('is_show', 1)
                ->order('sort desc, id desc')
                ->select()
                ->toArray();

            // Nếu có chỉ định pid thì dựng cây bắt đầu từ node đó
            if ($pid !== null) {
                $tree = $this->buildCategoryTree($allList, $pid);
                return ['list' => $tree, 'count' => count($tree)];
            }

            // Nếu không thì dựng cây đầy đủ (bắt đầu từ node gốc pid=0)
            $tree = $this->buildCategoryTree($allList, 0);
            return ['list' => $tree, 'count' => count($tree)];
        }

        // Chế độ danh sách thông thường
        $list = $query
            ->order('sort desc, id desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = $query->count();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * Xây dựng cấu trúc cây danh mục
     *
     * @param array $list Toàn bộ dữ liệu danh mục
     * @param int $pid ID cấp cha
     * @return array Cấu trúc cây
     */
    private function buildCategoryTree(array $list, int $pid): array
    {
        $tree = [];
        foreach ($list as $item) {
            if ($item['pid'] == $pid) {
                $children = $this->buildCategoryTree($list, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * Lấy chi tiết danh mục
     *
     * @param int $id ID danh mục
     * @return array Thông tin chi tiết danh mục
     * @throws \Exception Ném ngoại lệ khi danh mục không tồn tại
     */
    private function categoryDetail(int $id): array
    {
        $info = Db::name('store_category')->where('id', $id)->find();
        if (!$info) {
            throw new \Exception('Danh mục không tồn tại');
        }
        return $info;
    }

    // ==================== Quản lý sản phẩm ====================

    /**
     * Lấy danh sách sản phẩm
     * Hỗ trợ lọc theo danh mục, từ khóa, khoảng tồn kho
     *
     * @param array $args Tham số truy vấn
     *   - page: số trang, mặc định 1
     *   - limit: số lượng mỗi trang, mặc định 10, tối đa 100
     *   - cate_id: ID danh mục (tùy chọn)
     *   - keyword: từ khóa tìm kiếm (tùy chọn)
     *   - stock_min: tồn kho tối thiểu (tùy chọn)
     *   - stock_max: tồn kho tối đa (tùy chọn)
     * @return array Danh sách sản phẩm và tổng số
     */
    private function productList(array $args): array
    {
        $page = max(1, (int)($args['page'] ?? 1));
        $limit = min(100, max(1, (int)($args['limit'] ?? 10))); // Giới hạn tối đa 100

        $where = [['is_show', '=', 1]];

        // Lọc theo danh mục: truy vấn qua bảng liên kết
        if (!empty($args['cate_id'])) {
            $cateId = (int)$args['cate_id'];
            // Kiểm tra danh mục có tồn tại không
            $categoryExists = Db::name('store_category')->where('id', $cateId)->where('is_show', 1)->count();
            if (!$categoryExists) {
                throw new \Exception('Danh mục không tồn tại');
            }
            // Truy vấn ID sản phẩm qua bảng liên kết
            $productIds = Db::name('store_product_cate')
                ->where('cate_id', $cateId)
                ->column('product_id');
            if (empty($productIds)) {
                return ['list' => [], 'count' => 0];
            }
            $where[] = ['id', 'in', $productIds];
        }

        // Tìm kiếm theo từ khóa: escape ký tự đại diện để chống injection
        if (!empty($args['keyword'])) {
            $keyword = addcslashes($args['keyword'], '%_');
            $where[] = ['store_name', 'like', '%' . $keyword . '%'];
        }

        if (isset($args['stock_min'])) {
            $where[] = ['stock', '>=', (int)$args['stock_min']];
        }
        if (isset($args['stock_max'])) {
            $where[] = ['stock', '<=', (int)$args['stock_max']];
        }

        $list = Db::name('store_product')
            ->where($where)
            ->field('id,store_name,cate_id,price,stock,image,sales,is_show')
            ->order('id desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = Db::name('store_product')->where($where)->count();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * Lấy chi tiết sản phẩm
     *
     * @param int $id ID sản phẩm
     * @return array Thông tin chi tiết sản phẩm (đã lọc các trường nhạy cảm)
     * @throws \Exception Ném ngoại lệ khi sản phẩm không tồn tại
     */
    private function productDetail(int $id): array
    {
        $info = Db::name('store_product')->where('id', $id)->find();
        if (!$info) {
            throw new \Exception('Sản phẩm không tồn tại');
        }

        // Lọc các trường nhạy cảm, chỉ trả về thông tin cần thiết
        return [
            'id' => $info['id'],
            'store_name' => $info['store_name'] ?? '',
            'cate_id' => $info['cate_id'] ?? 0,
            'price' => $info['price'] ?? 0,
            'stock' => $info['stock'] ?? 0,
            'image' => $info['image'] ?? '',
            'slider_image' => $info['slider_image'] ?? '',
            'sales' => $info['sales'] ?? 0,
            'unit_name' => $info['unit_name'] ?? '',
            'content' => $info['content'] ?? '',
            'is_show' => $info['is_show'] ?? 1,
        ];
    }

    // ==================== Quản lý đơn hàng ====================

    /**
     * Lấy danh sách đơn hàng
     * Hỗ trợ lọc theo trạng thái và từ khóa
     *
     * @param array $args Tham số truy vấn
     *   - page: số trang, mặc định 1
     *   - limit: số lượng mỗi trang, mặc định 10, tối đa 100
     *   - status: trạng thái đơn hàng (tùy chọn)
     *   - keyword: từ khóa tìm kiếm, khớp theo mã đơn hàng/họ tên/số điện thoại (tùy chọn)
     * @return array Danh sách đơn hàng và tổng số
     */
    private function orderList(array $args): array
    {
        $page = max(1, (int)($args['page'] ?? 1));
        $limit = min(100, max(1, (int)($args['limit'] ?? 10))); // Giới hạn tối đa 100

        $where = [['is_del', '=', 0]];
        if (isset($args['status'])) {
            $where[] = ['status', '=', (int)$args['status']];
        }
        // Tìm kiếm theo từ khóa: escape ký tự đại diện để chống injection
        if (!empty($args['keyword'])) {
            $keyword = addcslashes($args['keyword'], '%_');
            $where[] = ['order_id|real_name|user_phone', 'like', '%' . $keyword . '%'];
        }

        $list = Db::name('store_order')
            ->where($where)
            ->field('id,order_id,uid,total_price,pay_price,paid,status,delivery_type,add_time')
            ->order('id desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = Db::name('store_order')->where($where)->count();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * Lấy chi tiết đơn hàng
     *
     * @param string $orderId Mã đơn hàng
     * @return array Thông tin chi tiết đơn hàng (đã lọc các trường nhạy cảm)
     * @throws \Exception Ném ngoại lệ khi đơn hàng không tồn tại
     */
    private function orderDetail(string $orderId): array
    {
        $info = Db::name('store_order')->where('order_id', $orderId)->find();
        if (!$info) {
            throw new \Exception('Đơn hàng không tồn tại');
        }

        // Lọc các trường nhạy cảm, chỉ trả về thông tin cần thiết
        return [
            'id' => $info['id'],
            'order_id' => $info['order_id'],
            'uid' => $info['uid'],
            'real_name' => $info['real_name'] ?? '',
            'user_phone' => $info['user_phone'] ?? '',
            'user_address' => $info['user_address'] ?? '',
            'total_price' => $info['total_price'] ?? 0,
            'pay_price' => $info['pay_price'] ?? 0,
            'pay_type' => $info['pay_type'] ?? '',
            'paid' => $info['paid'] ?? 0,
            'status' => $info['status'] ?? 0,
            'delivery_type' => $info['delivery_type'] ?? '',
            'delivery_name' => $info['delivery_name'] ?? '',
            'delivery_id' => $info['delivery_id'] ?? '',
            'refund_status' => $info['refund_status'] ?? 0,
            'add_time' => $info['add_time'] ?? 0,
        ];
    }

    /**
     * Lấy danh sách đơn vị vận chuyển
     * Trả về thông tin tất cả đơn vị vận chuyển đang bật
     *
     * @return array Danh sách đơn vị vận chuyển
     */
    private function orderExpressList(): array
    {
        $list = Db::name('express')->where('is_show', 1)->field('id,name,code')->select()->toArray();
        return ['list' => $list];
    }

    // ==================== Quản lý hậu mãi ====================

    /**
     * Lấy danh sách đơn hậu mãi
     * Trả về tất cả đơn hàng có trạng thái hoàn tiền
     *
     * @param array $args Tham số truy vấn
     *   - page: số trang, mặc định 1
     *   - limit: số lượng mỗi trang, mặc định 10, tối đa 100
     * @return array Danh sách đơn hàng hậu mãi và tổng số
     */
    private function refundList(array $args): array
    {
        $page = max(1, (int)($args['page'] ?? 1));
        $limit = min(100, max(1, (int)($args['limit'] ?? 10))); // Giới hạn tối đa 100

        $list = Db::name('store_order')
            ->where('refund_status', '>', 0)
            ->field('id,order_id,uid,total_price,pay_price,refund_status,refund_reason')
            ->order('id desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = Db::name('store_order')->where('refund_status', '>', 0)->count();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * Lấy chi tiết đơn đổi trả
     *
     * @param string $orderId Mã đơn đổi trả
     * @return array Thông tin chi tiết đơn hàng hậu mãi (đã lọc các trường nhạy cảm)
     * @throws \Exception Ném ngoại lệ khi đơn hàng hậu mãi không tồn tại
     */
    private function refundDetail(string $orderId): array
    {
        $info = Db::name('store_order')
            ->where('order_id', $orderId)
            ->where('refund_status', '>', 0)
            ->find();
        if (!$info) {
            throw new \Exception('Đơn đổi trả không tồn tại');
        }

        // Lọc các trường nhạy cảm, chỉ trả về thông tin cần thiết
        return [
            'id' => $info['id'],
            'order_id' => $info['order_id'],
            'uid' => $info['uid'],
            'total_price' => $info['total_price'] ?? 0,
            'pay_price' => $info['pay_price'] ?? 0,
            'refund_status' => $info['refund_status'] ?? 0,
            'refund_reason' => $info['refund_reason'] ?? '',
            'refund_price' => $info['refund_price'] ?? 0,
            'refund_explain' => $info['refund_explain'] ?? '',
            'refund_img' => $info['refund_img'] ?? '',
            'add_time' => $info['add_time'] ?? 0,
        ];
    }

    // ==================== Quản lý phiếu giảm giá ====================

    /**
     * Lấy danh sách phiếu giảm giá
     *
     * @param array $args Tham số truy vấn
     *   - page: số trang, mặc định 1
     *   - limit: số lượng mỗi trang, mặc định 10, tối đa 100
     * @return array Danh sách phiếu giảm giá và tổng số
     */
    private function couponList(array $args): array
    {
        $page = max(1, (int)($args['page'] ?? 1));
        $limit = min(100, max(1, (int)($args['limit'] ?? 10))); // Giới hạn tối đa 100

        $list = Db::name('store_coupon_issue')
            ->where('is_del', 0)
            ->field('id,coupon_title,coupon_price,use_min_price,start_time,end_time')
            ->order('id desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = Db::name('store_coupon_issue')->where('is_del', 0)->count();

        return ['list' => $list, 'count' => $count];
    }

    // ==================== Quản lý người dùng ====================

    /**
     * Lấy danh sách người dùng
     * Hỗ trợ tìm kiếm theo biệt danh hoặc số điện thoại
     *
     * @param array $args Tham số truy vấn
     *   - page: số trang, mặc định 1
     *   - limit: số lượng mỗi trang, mặc định 10, tối đa 100
     *   - keyword: từ khóa tìm kiếm, khớp theo biệt danh/số điện thoại (tùy chọn)
     * @return array Danh sách người dùng và tổng số
     */
    private function userList(array $args): array
    {
        $page = max(1, (int)($args['page'] ?? 1));
        $limit = min(100, max(1, (int)($args['limit'] ?? 10))); // Giới hạn tối đa 100

        $where = [];
        // Tìm kiếm theo từ khóa: escape ký tự đại diện để chống injection
        if (!empty($args['keyword'])) {
            $keyword = addcslashes($args['keyword'], '%_');
            $where[] = ['nickname|phone', 'like', '%' . $keyword . '%'];
        }

        $list = Db::name('user')
            ->where($where)
            ->field('uid,nickname,avatar,phone,balance,integral,add_time')
            ->order('uid desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $count = Db::name('user')->where($where)->count();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * Lấy chi tiết người dùng
     *
     * @param int $uid ID người dùng
     * @return array Thông tin chi tiết người dùng (đã lọc các trường nhạy cảm)
     * @throws \Exception Ném ngoại lệ khi người dùng không tồn tại
     */
    private function userDetail(int $uid): array
    {
        $info = Db::name('user')->where('uid', $uid)->find();
        if (!$info) {
            throw new \Exception('Người dùng không tồn tại');
        }

        // Lọc các trường nhạy cảm, chỉ trả về thông tin cần thiết
        return [
            'uid' => $info['uid'],
            'nickname' => $info['nickname'] ?? '',
            'avatar' => $info['avatar'] ?? '',
            'phone' => $info['phone'] ?? '',
            'now_money' => $info['now_money'] ?? 0,
            'integral' => $info['integral'] ?? 0,
            'level' => $info['level'] ?? 0,
            'add_time' => $info['add_time'] ?? 0,
            'last_time' => $info['last_time'] ?? 0,
        ];
    }

    // ==================== API MCP ====================

    /**
     * Phương thức điểm vào của service MCP
     * Xử lý tất cả request giao thức MCP, bao gồm:
     * - initialize: Khởi tạo kết nối, trả về thông tin và capabilities của service
     * - tools/list: Lấy danh sách công cụ khả dụng
     * - tools/call: Gọi công cụ chỉ định để thực hiện thao tác
     *
     * @param Request $request Đối tượng HTTP request
     * @return \think\response\Json JSON-RPC 2.0 phản hồi theo định dạng
     */
    public function index(Request $request)
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $id = $data['id'] ?? null;

        if (!$data) {
            return json(['jsonrpc' => '2.0', 'error' => ['code' => -32700, 'message' => 'Parse error'], 'id' => null]);
        }

        // Kiểm tra xác thực
        if (empty($this->outId)) {
            $errorMsg = $this->outInfo['error'] ?? 'Xác thực thất bại';
            return json([
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => ['code' => -32600, 'message' => $errorMsg]
            ]);
        }

        $method = $data['method'] ?? '';
        $params = $data['params'] ?? [];

        try {
            switch ($method) {
                case 'initialize':
                    return json([
                        'jsonrpc' => '2.0',
                        'id' => $id,
                        'result' => [
                            'protocolVersion' => '2024-11-05',
                            'capabilities' => ['tools' => new \stdClass()],
                            'serverInfo' => [
                                'name' => 'crmeb-mcp-server',
                                'version' => '1.0.0',
                            ],
                        ],
                    ]);

                case 'tools/list':
                    return json([
                        'jsonrpc' => '2.0',
                        'id' => $id,
                        'result' => ['tools' => $this->getTools()],
                    ]);

                case 'tools/call':
                    $toolName = $params['name'] ?? '';
                    $toolArgs = $params['arguments'] ?? [];

                    $result = $this->handleToolCall($toolName, $toolArgs);

                    return json([
                        'jsonrpc' => '2.0',
                        'id' => $id,
                        'result' => [
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                                ],
                            ],
                        ],
                    ]);

                default:
                    return json([
                        'jsonrpc' => '2.0',
                        'id' => $id,
                        'error' => ['code' => -32601, 'message' => "Method not found: {$method}"],
                    ]);
            }
        } catch (\Exception $e) {
            // Môi trường production trả về thông báo lỗi chung, tránh lộ chi tiết nội bộ
            $errorMessage = $e->getMessage();
            // Với ngoại lệ nghiệp vụ (như "Sản phẩm không tồn tại"), trả về lỗi cụ thể
            // Với ngoại lệ hệ thống (như lỗi SQL), trả về lỗi chung
            $safeErrors = ['Sản phẩm không tồn tại', 'Đơn hàng không tồn tại', 'Đơn đổi trả không tồn tại', 'Người dùng không tồn tại', 'Danh mục không tồn tại', 'Danh mục cha không tồn tại',
                          'Đã tồn tại danh mục cùng tên trong cùng cấp', 'Tên danh mục không được vượt quá 50 ký tự',
                          'Tham số không hợp lệ', 'Công cụ không xác định'];
            $isSafeError = false;
            foreach ($safeErrors as $safeError) {
                if (strpos($errorMessage, $safeError) !== false) {
                    $isSafeError = true;
                    break;
                }
            }

            return json([
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => ['code' => -32603, 'message' => $isSafeError ? $errorMessage : 'Lỗi máy chủ nội bộ'],
            ]);
        }
    }
}
