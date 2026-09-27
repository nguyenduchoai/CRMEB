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

namespace app;

use Spatie\Macroable\Macroable;

/**
 * Class Request
 * @package app
 * @method tokenData() Lấy thông tin token
 * @method user(string $key = null) Lấy thông tin người dùng
 * @method uid() Lấy uid người dùng
 * @method isAdminLogin() Trạng thái đăng nhập quản trị
 * @method adminId() ID quản trị viên
 * @method adminInfo() Thông tin quản trị
 * @method kefuId() ID nhân viên CSKH
 * @method kefuInfo() Thông tin chăm sóc khách hàng
 */
class Request extends \think\Request
{
    use Macroable;

    /**
     * Tên biến không lọc
     * @var array
     */
    protected $except = ['menu_path', 'api_url', 'unique_auth',
        'description', 'custom_form', 'params_list', 'content', 'tableField', 'url', 'customCode', 'value', 'refund_reason_wap_img'];

    /**
     * Lấy dữ liệu của request
     * @param array $params
     * @param bool $suffix
     * @param bool $filter
     * @return array
     */
    public function more(array $params, bool $suffix = false, bool $filter = true): array
    {
        $p = [];
        $i = 0;
        foreach ($params as $param) {
            if (!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] = $this->param($param);
            } else {
                if (!isset($param[1])) $param[1] = null;
                if (!isset($param[2])) $param[2] = '';
                if (is_array($param[0])) {
                    $name = is_array($param[1]) ? $param[0][0] . '/a' : $param[0][0] . '/' . $param[0][1];
                    $keyName = $param[0][0];
                } else {
                    $name = is_array($param[1]) ? $param[0] . '/a' : $param[0];
                    $keyName = $param[0];
                }

                $p[$suffix == true ? $i++ : ($param[3] ?? $keyName)] = $this->param($name, $param[1], $param[2]);
            }
        }

        if ($filter && $p) {
            $p = $this->filterArrayValues($p);
        }

        return $p;
    }

    /**
     * Lọc chuỗi trong mảng nhận được
     * @param $str
     * @param bool $filter
     * @return array|mixed|string|string[]
     */
    public function filterArrayValues($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Nếu giá trị là mảng và không thuộc danh sách tên biến không lọc, gọi đệ quy filterArrayValues, ngược lại gán giá trị trực tiếp
                $result[$key] = in_array($key, $this->except) ? $value : $this->filterArrayValues($value);
            } else {
                if (in_array($key, $this->except) || is_int($value) || is_null($value)) {
                    $result[$key] = $value;
                } else {
                    // Nếu giá trị là chuỗi, lọc ký tự đặc biệt
                    $result[$key] = filter_str($value);
                }

            }
        }
        return $result;
    }

    /**
     * Lấy tham số get
     * @param array $params
     * @param bool $suffix
     * @param bool $filter
     * @return array
     */
    public function getMore(array $params, bool $suffix = false, bool $filter = true): array
    {
        return $this->more($params, $suffix, $filter);
    }

    /**
     * Lấy tham số post
     * @param array $params
     * @param bool $suffix
     * @param bool $filter
     * @return array
     */
    public function postMore(array $params, bool $suffix = false, bool $filter = true): array
    {
        return $this->more($params, $suffix, $filter);
    }

    /**
     * Lấy nền tảng truy cập của người dùng
     * @return array|string|null
     */
    public function getFromType()
    {
        return $this->header('Form-type', '');
    }

    /**
     * Nền tảng truy cập hiện tại
     * @param string $terminal
     * @return bool
     */
    public function isTerminal(string $terminal)
    {
        return strtolower($this->getFromType()) === $terminal;
    }

    /**
     * Có phải là nền tảng H5 không
     * @return bool
     */
    public function isH5()
    {
        return $this->isTerminal('h5');
    }

    /**
     * Có phải là nền tảng WeChat không
     * @return bool
     */
    public function isWechat()
    {
        return $this->isTerminal('wechat');
    }

    /**
     * Có phải là nền tảng Mini Program không
     * @return bool
     */
    public function isRoutine()
    {
        return $this->isTerminal('routine');
    }

    /**
     * Có phải là nền tảng app không
     * @return bool
     */
    public function isApp()
    {
        return $this->isTerminal('app');
    }

    /**
     * Có phải là nền tảng pc không
     * @return bool
     */
    public function isPc()
    {
        return $this->isTerminal('pc');
    }
}
