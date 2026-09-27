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

namespace app\services;

use app\services\user\UserServices;
use crmeb\exceptions\ApiException;
use crmeb\utils\JwtAuth;
use think\facade\Db;
use think\facade\Config;
use think\facade\Route as Url;
use think\Model;

/**
 * Class BaseServices
 * @package app\services
 * @method array|Model|null get($id, ?array $field = []) Lấy một dòng dữ liệu
 * @method array|Model|null getOne(array $where, ?string $field = '*') Lấy một dữ liệu (không qua bộ lọc)
 * @method string|null batchUpdate(array $ids, array $data, ?string $key = null) Sửa hàng loạt
 * @method float sum(array $where, string $field, bool $search = false) Tính tổng
 * @method mixed update($id, array $data, ?string $field = '') Dữ liệu cần chỉnh sửa
 * @method bool be($map, string $field = '') Truy vấn xem một dữ liệu có tồn tại không
 * @method mixed value(array $where, string $field) Lấy dữ liệu theo điều kiện chỉ định
 * @method int count(array $where = []) Đọc số lượng dữ liệu
 * @method int getCount(array $where = []) Lấy tổng số theo một số điều kiện (không qua bộ lọc)
 * @method array getColumn(array $where, string $field, string $key = '') Lấy mảng của một trường (không qua bộ lọc)
 * @method mixed delete($id, ?string $key = null) Xóa
 * @method mixed save(array $data) Lưu dữ liệu
 * @method mixed saveAll(array $data) Lưu dữ liệu theo lô
 * @method Model selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false) Lấy danh sách
 * @method bool bcInc($key, string $incField, string $inc, string $keyField = null, int $acc = 2) Phép cộng độ chính xác cao
 * @method bool bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2) Phép trừ độ chính xác cao
 * @method mixed decStockIncSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales') Giảm tồn kho, tăng lượt bán
 * @method mixed incStockDecSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales') Tăng tồn kho, giảm lượt bán
 */
abstract class BaseServices
{

    /**
     * Inject model
     * @var object
     */
    protected $dao;

    /**
     * Lấy cấu hình phân trang
     * @param bool $isPage
     * @param bool $isRelieve
     * @return int[]
     */
    public function getPageValue(bool $isPage = true, bool $isRelieve = true)
    {
        $page = $limit = 0;
        if ($isPage) {
            $page = app()->request->param(Config::get('database.page.pageKey', 'page') . '/d', 0);
            $limit = app()->request->param(Config::get('database.page.limitKey', 'limit') . '/d', 0);
        }
        $limitMax = Config::get('database.page.limitMax');
        $defaultLimit = Config::get('database.page.defaultLimit', 10);
        if ($limit > $limitMax && $isRelieve) {
            $limit = $limitMax;
        }
        return [(int)$page, (int)$limit, (int)$defaultLimit];
    }

    /**
     * Thao tác transaction database
     * @param callable $closure
     * @param bool $isTran
     * @return mixed
     */
    public function transaction(callable $closure, bool $isTran = true)
    {
        return $isTran ? Db::transaction($closure) : $closure();
    }

    /**
     * Tạo token
     * @param int $id
     * @param $type
     * @param string $pwd
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createToken(int $id, $type, $pwd = '')
    {
        /** @var JwtAuth $jwtAuth */
        $jwtAuth = app()->make(JwtAuth::class);
        if ($type == 'api' && !app()->make(UserServices::class)->value(['uid' => $id], 'status')) {
            throw new ApiException('Bạn đã bị cấm đăng nhập, vui lòng liên hệ quản trị viên');
        }
        if ($type == 'api') {
            $user = app()->make(UserServices::class)->get($id);
            $user = $user->toArray();
            //Tin nhắn tùy chỉnh - đăng nhập thành công
            $user['last_time'] = date('Y-m-d H:i:s', $user['last_time']);
            $user['time'] = date('Y-m-d H:i:s');
            event('CustomNoticeListener', [$id, $user, 'login_success']);

            //Event tùy chỉnh - người dùng đăng nhập
            event('CustomEventListener', ['user_login', [
                'uid' => $user['uid'],
                'nickname' => $user['nickname'],
                'phone' => $user['phone'],
                'add_time' => date('Y-m-d H:i:s', $user['add_time']),
                'login_time' => date('Y-m-d H:i:s'),
                'time' => $user['time'],
                'last_time' => $user['last_time'],
                'user_type' => $user['user_type']
            ]]);
        }
        return $jwtAuth->createToken($id, $type, ['pwd' => md5($pwd)]);
    }

    /**
     * Lấy địa chỉ route
     * @param string $path
     * @param array $params
     * @param bool $suffix
     * @param bool $isDomain
     * @return \think\route\Url
     */
    public function url(string $path, array $params = [], bool $suffix = false, bool $isDomain = false)
    {
        return Url::buildUrl($path, $params)->suffix($suffix)->domain($isDomain)->build();
    }

    /**
     * Mã hóa hash mật khẩu
     * @param string $password
     * @return false|string|null
     */
    public function passwordHash(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return call_user_func_array([$this->dao, $name], $arguments);
    }

    /**
     * Xử lý dữ liệu thành phố
     * @param $address
     * @return array
     */
    public function addressHandle($address)
    {
        if ($address) {
            try {
                preg_match('/(.*?(省|自治区|北京市|天津市|上海市|重庆市|澳门特别行政区|香港特别行政区))/', $address, $matches);
                if (count($matches) > 1) {
                    $province = $matches[count($matches) - 2];
                    $address = preg_replace('/(.*?(省|自治区|北京市|天津市|上海市|重庆市|澳门特别行政区|香港特别行政区))/', '', $address, 1);
                }
                preg_match('/(.*?(市|自治州|地区|区划|县))/', $address, $matches);
                if (count($matches) > 1) {
                    $city = $matches[count($matches) - 2];
                    $address = str_replace($city, '', $address);
                }
                preg_match('/(.*?(区|县|镇|乡|街道))/', $address, $matches);
                if (count($matches) > 1) {
                    $area = $matches[count($matches) - 2];
                    $address = str_replace($area, '', $address);
                }
            } catch (\Throwable $e) {
            }
        }
        return [
            'province' => $province ?? '',
            'city' => $city ?? '',
            'district' => $area ?? '',
            "address" => $address
        ];
    }
}
