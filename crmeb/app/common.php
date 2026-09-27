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

// File dùng chung của ứng dụng
use app\services\pay\PayServices;
use crmeb\services\CacheService;
use crmeb\services\HttpService;
use Fastknife\Service\ClickWordCaptchaService;
use think\exception\ValidateException;
use crmeb\services\FormBuilder as Form;
use app\services\other\UploadService;
use Fastknife\Service\BlockPuzzleCaptchaService;
use app\services\system\lang\LangTypeServices;
use app\services\system\lang\LangCodeServices;
use app\services\system\lang\LangCountryServices;
use think\facade\Config;
use think\facade\Log;
use think\facade\Db;

if (!function_exists('crmebLog')) {
    /**
     * Log của CRMEB
     * @param $msg
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/03
     */
    function crmebLog($msg)
    {
        Log::write($msg, 'crmeb');
    }
}

if (!function_exists('getWorkerManUrl')) {

    /**
     * Lấy dữ liệu CSKH
     * @return mixed
     */
    function getWorkerManUrl()
    {
        $ws = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'wss://' : 'ws://';
        $host = $_SERVER['HTTP_HOST'];
        $data['admin'] = $ws . $host . '/notice';
        $data['chat'] = $ws . $host . '/msg';
        return $data;
    }
}
if (!function_exists('object2array')) {

    /**
     * Chuyển object thành mảng
     * @param $object
     * @return array|mixed
     */
    function object2array($object)
    {
        $array = [];
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }
}

if (!function_exists('exception')) {
    /**
     * Xử lý throw exception
     * @param $msg
     * @param int $code
     * @param string $exception
     * @throws \think\Exception
     */
    function exception($msg, $code = 0, $exception = '')
    {
        $e = $exception ?: '\think\Exception';
        throw new $e($msg, $code);
    }
}

if (!function_exists('sys_config')) {
    /**
     * Lấy một cấu hình hệ thống
     * @param string $name
     * @param string $default
     * @return string
     */
    function sys_config(string $name, $default = '')
    {
        if (empty($name))
            return $default;
        $sysConfig = app('sysConfig')->get($name);
        if (is_array($sysConfig)) {
            foreach ($sysConfig as &$item) {
                if (!is_array($item)) {
                    if (strpos($item, '/uploads/system/') !== false || strpos($item, '/statics/system_images/') !== false) $item = set_file_url($item);
                }
            }
        } else {
            if (strpos($sysConfig, '/uploads/system/') !== false || strpos($sysConfig, '/statics/system_images/') !== false) $sysConfig = set_file_url($sysConfig);
        }
        $config = is_array($sysConfig) ? $sysConfig : trim($sysConfig);
        if ($config === '' || $config === false) {
            return $default;
        } else {
            return $config;
        }
    }
}

if (!function_exists('sys_data')) {
    /**
     * Lấy một dữ liệu hệ thống
     * @param string $name
     * @return string
     */
    function sys_data(string $name, int $limit = 0)
    {
        return app('sysGroupData')->getData($name, $limit);
    }
}

if (!function_exists('filter_emoji')) {

    // Lọc bỏ emoji
    function filter_emoji($str)
    {
        $str = preg_replace_callback(    //Thực hiện tìm kiếm bằng regex và dùng callback để thay thế
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }
}


if (!function_exists('str_middle_replace')) {
    /** TODO Hệ thống chưa dùng
     * @param string $string Chuỗi cần thay thế
     * @param int $start Giữ lại mấy ký tự đầu
     * @param int $end Giữ lại mấy ký tự cuối
     * @return string
     */
    function str_middle_replace($string, $start, $end)
    {
        $strlen = mb_strlen($string, 'UTF-8');//Lấy độ dài chuỗi
        $firstStr = mb_substr($string, 0, $start, 'UTF-8');//Lấy ký tự đầu tiên
        $lastStr = mb_substr($string, -1, $end, 'UTF-8');//Lấy ký tự cuối cùng
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($string, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;

    }
}


if (!function_exists('sensitive_words_filter')) {

    /**
     * Lọc từ nhạy cảm
     *
     * @param string
     * @return string
     */
    function sensitive_words_filter($str)
    {
        if (!$str) return '';
        $file = app()->getAppPath() . 'public/statics/plug/censorwords/CensorWords';
        $words = file($file);
        foreach ($words as $word) {
            $word = str_replace(array("\r\n", "\r", "\n", "/", "<", ">", "=", " "), '', $word);
            if (!$word) continue;

            $ret = preg_match("/$word/", $str, $match);
            if ($ret) {
                return $match[0];
            }
        }
        return '';
    }
}

if (!function_exists('make_path')) {

    /**
     * Chuyển đổi đường dẫn tải lên, đường dẫn mặc định
     * @param $path
     * @param int $type
     * @param bool $force
     * @return string
     */
    function make_path($path, int $type = 2, bool $force = false)
    {
        $path = DS . ltrim(rtrim($path));
        switch ($type) {
            case 1:
                $path .= DS . date('Y');
                break;
            case 2:
                $path .= DS . date('Y') . DS . date('m');
                break;
            case 3:
                $path .= DS . date('Y') . DS . date('m') . DS . date('d');
                break;
        }
        try {
            if (is_dir(app()->getRootPath() . 'public' . DS . 'uploads' . $path) == true || mkdir(app()->getRootPath() . 'public' . DS . 'uploads' . $path, 0777, true) == true) {
                return trim(str_replace(DS, '/', $path), '.');
            } else return '';
        } catch (\Exception $e) {
            if ($force)
                throw new \Exception($e->getMessage());
//            return 'Không thể tạo thư mục, vui lòng kiểm tra quyền thư mục tải lên của bạn:' . app()->getRootPath() . 'public' . DS . 'uploads' . DS . 'attach' . DS;
            return '';
        }

    }
}


if (!function_exists('curl_file_exist')) {
    /**
     * Dùng CURL kiểm tra file từ xa có tồn tại không
     * @param $url
     * @return bool
     */
    function curl_file_exist($url)
    {
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $contents = curl_exec($ch);
            if (preg_match("/404/", $contents)) return false;
            if (preg_match("/403/", $contents)) return false;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
if (!function_exists('set_file_url')) {
    /**
     * Thiết lập đường dẫn bổ sung
     * @param $url
     * @return bool
     */
    function set_file_url($image, $siteUrl = '')
    {
        if (!strlen(trim($siteUrl))) $siteUrl = sys_config('site_url');
        if (!$image) return $image;
        if (is_array($image)) {
            foreach ($image as &$item) {
                $domainTop1 = substr($item, 0, 4);
                $domainTop2 = substr($item, 0, 2);
                if ($domainTop1 != 'http' && $domainTop2 != '//')
                    $item = $siteUrl . str_replace('\\', '/', $item);
            }
        } else {
            $domainTop1 = substr($image, 0, 4);
            $domainTop2 = substr($image, 0, 2);
            if ($domainTop1 != 'http' && $domainTop2 != '//')
                $image = $siteUrl . str_replace('\\', '/', $image);
        }
        return $image;
    }
}

if (!function_exists('set_http_type')) {
    /**
     * Sửa https và http
     * @param $url $url Tên miền
     * @param int $type 0 Trả về https, 1 thì trả về http
     * @return string
     */
    function set_http_type($url, $type = 0)
    {
        $domainTop = substr($url, 0, 5);
        if ($type) {
            if ($domainTop == 'https') $url = 'http' . substr($url, 5, strlen($url));
        } else {
            if ($domainTop != 'https') $url = 'https:' . substr($url, 5, strlen($url));
        }
        return $url;
    }

}

if (!function_exists('check_card')) {
    /**
     * Xác thực CCCD/CMND
     * @param $card
     * @return bool
     */
    function check_card($card)
    {
        $city = [11 => "Beijing", 12 => "Tianjin", 13 => "Hebei", 14 => "Shanxi", 15 => "Inner Mongolia", 21 => "Liaoning", 22 => "Jilin", 23 => "Heilongjiang ", 31 => "Shanghai", 32 => "Jiangsu", 33 => "Zhejiang", 34 => "Anhui", 35 => "Fujian", 36 => "Jiangxi", 37 => "Shandong", 41 => "Henan", 42 => "Hubei ", 43 => "Hunan", 44 => "Guangdong", 45 => "Guangxi", 46 => "Hainan", 50 => "Chongqing", 51 => "Sichuan", 52 => "Guizhou", 53 => "Yunnan", 54 => "Tibet ", 61 => "Shaanxi", 62 => "Gansu", 63 => "Qinghai", 64 => "Ningxia", 65 => "Xinjiang", 71 => "Taiwan", 81 => "Hong Kong", 82 => "Macau", 91 => "Nước ngoài "];
        $tip = "";
        $match = "/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/";
        $pass = true;
        if (!$card || !preg_match($match, $card)) {
            //Sai định dạng CCCD/CMND
            $pass = false;
        } else if (!$city[substr($card, 0, 2)]) {
            //Địa chỉ không hợp lệ
            $pass = false;
        } else {
            //CCCD/CMND 18 số cần xác thực ký tự kiểm tra cuối cùng
            if (strlen($card) == 18) {
                $card = str_split($card);
                //∑(ai×Wi)(mod 11)
                //Hệ số trọng số
                $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                //Ký tự kiểm tra
                $parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
                $sum = 0;
                $ai = 0;
                $wi = 0;
                for ($i = 0; $i < 17; $i++) {
                    $ai = $card[$i];
                    $wi = $factor[$i];
                    $sum += $ai * $wi;
                }
                $last = $parity[$sum % 11];
                if ($parity[$sum % 11] != $card[17]) {
                    //                        $tip = "Sai ký tự kiểm tra";
                    $pass = false;
                }
            } else {
                $pass = false;
            }
        }
        if (!$pass) return false;/* Sai định dạng CCCD/CMND*/
        return true;/* Đúng định dạng CCCD/CMND*/
    }
}
if (!function_exists('check_link')) {
    /**
     * Xác thực địa chỉ
     * @param string $link
     * @return false|int
     */
    function check_link(string $link)
    {
        return preg_match("/^(http|https|ftp):\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\”])*$/", $link);
    }
}
if (!function_exists('check_phone')) {
    /**
     * Xác thực số điện thoại
     * @param $phone
     * @return false|int
     */
    function check_phone($phone)
    {
        return preg_match("/^1[3456789]\d{9}$/", $phone);
    }
}
if (!function_exists('anonymity')) {
    /**
     * Xử lý ẩn danh biệt danh người dùng
     * @param $name
     * @return string
     */
    function anonymity($name, $type = 1)
    {
        if ($type == 1) {
            return mb_substr($name, 0, 1, 'UTF-8') . '**' . mb_substr($name, -1, 1, 'UTF-8');
        } else {
            $strLen = mb_strlen($name, 'UTF-8');
            $min = 3;
            if ($strLen <= 1)
                return '*';
            if ($strLen <= $min)
                return mb_substr($name, 0, 1, 'UTF-8') . str_repeat('*', $min - 1);
            else
                return mb_substr($name, 0, 1, 'UTF-8') . str_repeat('*', $strLen - 1) . mb_substr($name, -1, 1, 'UTF-8');
        }
    }
}
if (!function_exists('sort_list_tier')) {
    /**
     * Sắp xếp theo cấp
     * @param $data
     * @param int $pid
     * @param string $field
     * @param string $pk
     * @param string $html
     * @param int $level
     * @param bool $clear
     * @return array
     */
    function sort_list_tier($data, $pid = 0, $field = 'pid', $pk = 'id', $html = '|-----', $level = 1, $clear = true)
    {
        static $list = [];
        if ($clear) $list = [];
        foreach ($data as $k => $res) {
            if ($res[$field] == $pid) {
                $res['html'] = str_repeat($html, $level);
                $list[] = $res;
                unset($data[$k]);
                sort_list_tier($data, $res[$pk], $field, $pk, $html, $level + 1, false);
            }
        }
        return $list;
    }
}

if (!function_exists('sort_city_tier')) {
    /**
     * Chuẩn hóa dữ liệu thành phố
     * @param $data
     * @param int $pid
     * @param string $field
     * @param string $pk
     * @param string $html
     * @param int $level
     * @param bool $clear
     * @return array
     */
    function sort_city_tier($data, $pid = 0, $navList = [])
    {
        foreach ($data as $k => $menu) {
            if ($menu['parent_id'] == $pid) {
                unset($menu['parent_id']);
                unset($data[$k]);
                $menu['c'] = sort_city_tier($data, $menu['v']);
                $navList[] = $menu;
            }
        }
        return $navList;
    }
}

if (!function_exists('time_tran')) {
    /**
     * Chuyển đổi timestamp sang dạng dễ đọc
     * @param $time
     * @return string
     */
    function time_tran($time)
    {
        $t = time() - $time;
        $f = array(
            '31536000' => 'năm',
            '2592000' => 'tháng',
            '604800' => 'tuần',
            '86400' => 'ngày',
            '3600' => 'giờ',
            '60' => 'phút',
            '1' => 'giây'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . 'trước';
            }
        }
    }
}

if (!function_exists('url_to_path')) {
    /**
     * Chuyển url thành đường dẫn
     * @param $url
     * @return string
     */
    function url_to_path($url)
    {
        $path = trim(str_replace('/', DS, $url), DS);
        if (0 !== strripos($path, 'public'))
            $path = 'public' . DS . $path;
        return app()->getRootPath() . $path;
    }
}

if (!function_exists('path_to_url')) {
    /**
     * Chuyển đường dẫn thành đường dẫn url
     * @param $path
     * @return string
     */
    function path_to_url($path)
    {
        return trim(str_replace(DS, '/', $path), '.');
    }
}

if (!function_exists('image_to_base64')) {
    /**
     * Lấy ảnh và chuyển thành base64
     * @param string $avatar
     * @return bool|string
     */
    function image_to_base64($avatar = '', $timeout = 9)
    {
        $avatar = str_replace('https', 'http', $avatar);
        try {
            $url = parse_url($avatar);
            if ($url['scheme'] . '://' . $url['host'] == sys_config('site_url')) {
                $pattern = '/<\?php(.*?)\?>/s';
                $imgData = preg_replace($pattern, '', file_get_contents(public_path() . substr($url['path'], 1)));
                return "data:image/jpeg;base64," . base64_encode($imgData);
            }
            $url = $url['host'];
            $header = [
                'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
                'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
                'Accept-Encoding: gzip, deflate, br',
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'Host:' . $url
            ];
            $dir = pathinfo($url);
            $host = $dir['dirname'];
            $refer = $host . '/';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_REFERER, $refer);
            curl_setopt($curl, CURLOPT_URL, $avatar);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            $data = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($code == 200) {
                return "data:image/jpeg;base64," . base64_encode($data);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('put_image')) {
    /**
     * Lấy ảnh và chuyển thành base64
     * @param string $avatar
     * @return bool|string
     */
    function put_image($url, $filename = '')
    {

        if ($url == '') {
            return false;
        }
        try {
            if ($filename == '') {
                $ext = pathinfo($url, PATHINFO_EXTENSION);
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    return false;
                }
                $filename = time() . "." . $ext;
            }

            // Lưu file vào thư mục được chỉ định
            $imgData = file_get_contents($url);
            $pattern = '/<\?php(.*?)\?>/s';
            $imgData = preg_replace($pattern, '', $imgData);
            if ($imgData !== false) {
                $path = 'uploads' . DS . 'qrcode' . DS . $filename;
                if (file_put_contents($path, $imgData) !== false) {
                    return $path;
                }
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}


if (!function_exists('debug_file')) {
    /**
     * Debug file
     * @param $content
     */
    function debug_file($content, string $fileName = 'error', string $ext = 'txt')
    {
        $msg = '[' . date('Y-m-d H:i:s', time()) . '] [ DEBUG ] ';
        $pach = app()->getRuntimePath();
        file_put_contents($pach . $fileName . '.' . $ext, $msg . print_r($content, true) . "\r\n", FILE_APPEND);
    }
}


if (!function_exists('sql_filter')) {
    /**
     * Lọc tham số sql
     * @param string $str
     * @return mixed
     */
    function sql_filter(string $str)
    {
        $filter = ['select ', 'insert ', 'update ', 'delete ', 'drop', 'truncate ', 'declare', 'xp_cmdshell', '/add', ' or ', 'exec', 'create', 'chr', 'mid', ' and ', 'execute'];
        $toupper = array_map(function ($str) {
            return strtoupper($str);
        }, $filter);
        return str_replace(array_merge($filter, $toupper, ['%20']), '', $str);
    }
}

if (!function_exists('filter_str')) {
    /**
     * Lọc ký tự nhạy cảm trong chuỗi
     * @param $str
     * @return array|mixed|string|string[]|null
     */
    function filter_str($str)
    {
        $param_filter_type = sys_config('param_filter_type');
        if ($param_filter_type != 0) {
            $rules = preg_split('/\r\n|\r|\n/', base64_decode(sys_config('param_filter_data')));
            if ($param_filter_type == 1) {
                foreach ($rules as $item) {
                    if (preg_match($item, $str)) {
                        throw new \Exception('Yêu cầu API thất bại: thao tác không hợp lệ!');
                    }
                }
            }
            if (filter_var($str, FILTER_VALIDATE_URL)) {
                $url = parse_url($str);
                if (!isset($url['scheme'])) return $str;
                $host = $url['scheme'] . '://' . $url['host'];
                $str = $host . preg_replace($rules, '', str_replace($host, '', $str));
            } else {
                $str = preg_replace($rules, '', $str);
            }
        }
        return $str;
    }
}

if (!function_exists('is_brokerage_statu')) {

    /**
     * Có thể trở thành người giới thiệu không
     * @param float $price
     * @return bool
     */
    function is_brokerage_statu(float $price)
    {
        if (!sys_config('brokerage_func_status')) {
            return false;
        }
        $storeBrokerageStatus = sys_config('store_brokerage_statu', 1);
        if ($storeBrokerageStatus == 1) {
            return false;
        } else if ($storeBrokerageStatus == 2) {
            return true;
        } else {
            $storeBrokeragePrice = sys_config('store_brokerage_price', 0);
            return $price >= $storeBrokeragePrice;
        }
    }
}

if (!function_exists('array_unique_fb')) {
    /**
     * Loại bỏ giá trị trùng trong mảng 2 chiều
     * @param $array
     * @return array
     */
    function array_unique_fb($array)
    {
        $out = array();
        foreach ($array as $key => $value) {
            if (!in_array($value, $out)) {
                $out[$key] = $value;
            }
        }
        $out = array_values($out);
        return $out;
    }
}


if (!function_exists('get_crmeb_version')) {
    /**
     * Lấy số phiên bản hệ thống CRMEB
     * @param string $default
     * @return string
     */
    function get_crmeb_version($default = 'v1.0.0')
    {
        try {
            $version = parse_ini_file(app()->getRootPath() . '.version');
            return $version['version'] ?? $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (!function_exists('get_file_link')) {
    /**
     * Lấy đường dẫn đầy đủ của file kèm tên miền
     * @param string $link
     * @return string
     */
    function get_file_link(string $link)
    {
        if (!$link) {
            return '';
        }
        if (substr($link, 0, 4) === "http" || substr($link, 0, 2) === "//") {
            return $link;
        } else {
            return app()->request->domain() . $link;
        }
    }
}

if (!function_exists('tidy_tree')) {
    /**
     * Định dạng danh mục
     * @param $menusList
     * @param int $pid
     * @param array $navList
     * @return array
     */
    function tidy_tree($menusList, $pid = 0, $navList = [])
    {
        foreach ($menusList as $k => $menu) {
            if ($menu['parent_id'] == $pid) {
                unset($menusList[$k]);
                $menu['children'] = tidy_tree($menusList, $menu['id']);
                if ($menu['children']) $menu['expand'] = true;
                $navList[] = $menu;
            }
        }
        return $navList;
    }
}

if (!function_exists('create_form')) {
    /**
     * Phương thức tạo form
     * @param string $title
     * @param array $field
     * @param $url
     * @param string $method
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    function create_form(string $title, array $field, $url, string $method = 'POST')
    {
        $form = Form::createForm((string)$url);//Địa chỉ gửi
        $form->setMethod($method);//Phương thức gửi
        $form->setRule($field);//Trường form
        $form->setTitle($title);//Tiêu đề biểu mẫu
        $rules = $form->formRule();
        $title = $form->getTitle();
        $action = $form->getAction();
        $method = $form->getMethod();
        $info = '';
        $status = true;
        $methodData = ['POST', 'PUT', 'GET', 'DELETE'];
        if (!in_array(strtoupper($method), $methodData)) {
            throw new ValidateException('Phương thức yêu cầu không đúng');
        }
        return compact('rules', 'title', 'action', 'method', 'info', 'status');
    }
}

if (!function_exists('msectime')) {
    /**
     * Lấy số mili giây
     * @return float
     */
    function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}


if (!function_exists('array_bc_sum')) {
    /**
     * Lấy tổng độ chính xác cao của mảng 1 chiều
     * @param array $data
     * @return string
     */
    function array_bc_sum(array $data)
    {
        $sum = '0';
        foreach ($data as $item) {
            $sum = bcadd($sum, (string)$item, 2);
        }
        return $sum;
    }
}

if (!function_exists('get_tree_children')) {
    /**
     * Menu con dạng tree
     * @param array $data Dữ liệu
     * @param string $childrenname Tên dữ liệu con
     * @param string $keyName Tên key dữ liệu
     * @param string $pidName Tên key cấp trên của dữ liệu
     * @return array
     */
    function get_tree_children(array $data, string $childrenname = 'children', string $keyName = 'id', string $pidName = 'pid')
    {
        $list = array();
        foreach ($data as $value) {
            $list[$value[$keyName]] = $value;
        }
        $tree = array(); //Cây đã được định dạng
        foreach ($list as $item) {
            if (isset($list[$item[$pidName]])) {
                $list[$item[$pidName]][$childrenname][] = &$list[$item[$keyName]];
            } else {
                $tree[] = &$list[$item[$keyName]];
            }
        }
        return $tree;
    }
}

if (!function_exists('get_tree_children_value')) {

    function get_tree_children_value(array $data, $value, string $childrenname = 'children', string $keyName = 'id')
    {
        static $childrenValue = [];
        foreach ($data as $item) {
            $childrenData = $item[$childrenname] ?? [];
            if (count($childrenData)) {
                return get_tree_children_value($childrenData, $childrenname, $keyName);
            } else {
                if ($item[$keyName] == $value) {
                    $childrenValue[] = $item['value'];
                }
            }
        }
        return $childrenValue;
    }
}


if (!function_exists('get_tree_value')) {
    /**
     * Lấy
     * @param array $data
     * @param int|string $value
     * @return array
     */
    function get_tree_value(array $data, $value)
    {
//        static $childrenValue = [];
//        foreach ($data as &$item) {
//            if ($item['value'] == $value) {
//                $childrenValue[] = $item['value'];
//                if ($item['pid']) {
//                    $value = $item['pid'];
//                    unset($item);
//                    return get_tree_value($data, $value);
//                }
//            }
//        }
//        return $childrenValue;
        $childrenValue = []; // Mảng dùng để lưu các giá trị con tìm được
        foreach ($data as $item) {
            if ($item['value'] == $value) { // Nếu khóa 'value' của phần tử hiện tại khớp với giá trị đã cho
                $childrenValue[] = $item['value']; // Thêm giá trị hiện tại vào mảng giá trị con
                if ($item['pid']) { // Nếu phần tử hiện tại có giá trị 'pid', nghĩa là có phần tử cha
                    // Gọi đệ quy hàm get_tree_value, và dùng giá trị 'pid' của phần tử cha làm tham số $value mới
                    $childrenValue = array_merge($childrenValue, get_tree_value($data, $item['pid']));
                }
            }
        }
        return $childrenValue; // Trả về mảng chứa tất cả giá trị con
    }
}

if (!function_exists('get_image_thumb')) {
    /**
     * Lấy ảnh thu nhỏ
     * @param $filePath
     * @param string $type all|big|mid|small
     * @param bool $is_remote_down
     * @return mixed|string|string[]
     */
    function get_image_thumb($filePath, string $type = 'all', bool $is_remote_down = false)
    {
        if (!$filePath || !is_string($filePath) || strpos($filePath, '?') !== false) return $filePath;
        try {
            $upload = UploadService::getOssInit($filePath, $is_remote_down);
            //TODO
            $fileArr = explode('/', $filePath);
            $data = $upload->thumb($filePath, end($fileArr), $type);
            $image = $type == 'all' ? $data : $data[$type] ?? $filePath;
        } catch (\Throwable $e) {
            $image = $filePath;
        }
        $data = parse_url($image);
        if (!isset($data['host']) && (substr($image, 0, 2) == './' || substr($image, 0, 1) == '/')) {//Không phải địa chỉ đầy đủ
            $image = sys_config('site_url') . $image;
        }
        //Request là https nhưng ảnh là http, cần đổi địa chỉ ảnh
        //TODO Có cần đọc url cấu hình từ quản trị không
        if (strpos(request()->domain(), 'https:') !== false && strpos($image, 'https:') === false) {
            $image = str_replace('http:', 'https:', $image);
        }
        return $image;
    }
}

if (!function_exists('get_thumb_water')) {
    /**
     * Xử lý mảng để lấy ảnh thu nhỏ, watermark
     * @param $list
     * @param string $type
     * @param array|string[] $field 1、['image','images'] type Tham số giá trị: type 2, ['small'=>'image','mid'=>'images'] type lấy key của mảng field
     * @param bool $is_remote_down
     * @return array|mixed|string|string[]
     */
    function get_thumb_water($list, string $type = 'small', array $field = ['image'], bool $is_remote_down = false)
    {
        // Chưa mở tính năng ảnh thu nhỏ, trả về dữ liệu gốc trực tiếp
        if (!sys_config('image_thumb_status', 0)) {
            return $list;
        }
        if (!$list || !$field) return $list;
        $baseType = $type;
        $data = $list;
        if (is_string($list)) {
            $field = [$type => 'image'];
            $data = ['image' => $list];
        }
        if (is_array($data)) {
            foreach ($field as $type => $key) {
                if (is_integer($type)) {//Mảng chỉ mục, type mặc định
                    $type = $baseType;
                }
                //Mảng 1 chiều
                if (isset($data[$key])) {
                    if (is_array($data[$key])) {
                        $path_data = [];
                        foreach ($data[$key] as $k => $path) {
                            $path_data[] = get_image_thumb($path, $type, $is_remote_down);
                        }
                        $data[$key] = $path_data;
                    } else {
                        $data[$key] = get_image_thumb($data[$key], $type, $is_remote_down);
                    }
                } else {
                    foreach ($data as &$item) {
                        if (!isset($item[$key]))
                            continue;
                        if (is_array($item[$key])) {
                            $path_data = [];
                            foreach ($item[$key] as $k => $path) {
                                $path_data[] = get_image_thumb($path, $type, $is_remote_down);
                            }
                            $item[$key] = $path_data;
                        } else {
                            $item[$key] = get_image_thumb($item[$key], $type, $is_remote_down);
                        }
                    }
                }
            }
        }
        return is_string($list) ? ($data['image'] ?? '') : $data;
    }
}

if (!function_exists('getLang')) {
    /**
     * Đa ngôn ngữ
     * @param $code
     * @param array $replace
     * @return array|string|string[]
     */
    function getLang($code, array $replace = [])
    {
        //Đảm bảo không báo lỗi khi lấy ngôn ngữ
        try {

            /** @var LangCountryServices $langCountryServices */
            $langCountryServices = app()->make(LangCountryServices::class);
            /** @var LangTypeServices $langTypeServices */
            $langTypeServices = app()->make(LangTypeServices::class);
            /** @var LangCodeServices $langCodeServices */
            $langCodeServices = app()->make(LangCodeServices::class);

            $request = app()->request;
            //Lấy loại ngôn ngữ truyền vào API
            if (!$range = $request->header('cb-lang')) {
                //Nếu không truyền vào thì hiển thị theo ngôn ngữ mặc định của hệ thống
                $range = CacheService::remember('range_name', function () use ($langTypeServices) {
                    return $langTypeServices->value(['is_default' => 1], 'file_name');
                });
                if (!$range) {
                    //Nếu hệ thống chưa cài đặt ngôn ngữ mặc định thì hiển thị theo ngôn ngữ của trình duyệt, nếu không tìm thấy ngôn ngữ của trình duyệt trong thư viện thì dùng tiếng Trung giản thể
                    if ($request->header('accept-language') !== null) {
                        $range = explode(',', $request->header('accept-language'))[0];
                    } else {
                        $range = 'zh-CN';
                    }
                }
            }

            // Lấy type_id
            $typeId = CacheService::remember('type_id_' . $range, function () use ($langCountryServices, $range) {
                return $langCountryServices->value(['code' => $range], 'type_id') ?: 1;
            }, 3600);

            // Lấy loại
            $langData = CacheService::remember('lang_type_data', function () use ($langTypeServices) {
                return $langTypeServices->getColumn(['status' => 1, 'is_del' => 0], 'file_name', 'id');
            }, 3600);

            // Lấy key cache
            $langStr = 'lang_' . str_replace('-', '_', $langData[$typeId]);

            //Đọc gói ngôn ngữ của ngôn ngữ hiện tại
            $lang = CacheService::remember($langStr, function () use ($typeId, $range, $langCodeServices) {
                return $langCodeServices->getColumn(['type_id' => $typeId, 'is_admin' => 1], 'lang_explain', 'code');
            }, 3600);
            //Lấy chữ trả về
            $message = (string)($lang[$code] ?? 'Code Error');

            //Thay thế biến
            if (!empty($replace) && is_array($replace)) {
                // Phân giải chỉ mục liên kết
                $key = array_keys($replace);
                foreach ($key as &$v) {
                    $v = "{:{$v}}";
                }
                $message = str_replace($key, $replace, $message);
            }

            return $message;
        } catch (\Throwable $e) {
            Log::error('Lấy code ngôn ngữ:' . $code . 'xảy ra lỗi, nguyên nhân lỗi là:' . json_encode([
                    'file' => $e->getFile(),
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]));
            return $code;
        }
    }
}

if (!function_exists('aj_captcha_check_one')) {
    /**
     * Xác thực trượt - Xác thực 1 lần
     * @param string $token
     * @param string $pointJson
     * @return bool
     */
    function aj_captcha_check_one(string $captchaType, string $token, string $pointJson)
    {
        aj_get_serevice($captchaType)->check($token, $pointJson);
        return true;
    }
}

if (!function_exists('aj_captcha_check_two')) {
    /**
     * Xác thực trượt - Xác thực 2 lần
     * @param string $token
     * @param string $pointJson
     * @return bool
     */
    function aj_captcha_check_two(string $captchaType, string $captchaVerification)
    {
        aj_get_serevice($captchaType)->verificationByEncryptCode($captchaVerification);
        return true;
    }
}


if (!function_exists('aj_captcha_create')) {
    /**
     * Tạo mã xác thực
     * @return array
     */
    function aj_captcha_create(string $captchaType)
    {
        return aj_get_serevice($captchaType)->get();
    }
}

if (!function_exists('aj_get_serevice')) {

    /**
     * @param string $captchaType
     * @return ClickWordCaptchaService|BlockPuzzleCaptchaService
     */
    function aj_get_serevice(string $captchaType)
    {
        $config = Config::get('ajcaptcha');
        switch ($captchaType) {
            case "clickWord":
                $service = new ClickWordCaptchaService($config);
                break;
            case "blockPuzzle":
                $service = new BlockPuzzleCaptchaService($config);
                break;
            default:
                throw new ValidateException('Tham số captchaType không đúng!');
        }
        return $service;
    }
}

if (!function_exists('out_push')) {
    /**
     * Đẩy dữ liệu mặc định
     * @param string $pushUrl
     * @param array $data
     * @param string $tip
     * @return bool
     */
    function out_push(string $pushUrl, array $data, string $tip = ''): bool
    {
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res = HttpService::postRequest($pushUrl, $param, ['Content-Type:application/json', 'Content-Length:' . strlen($param)]);
        $res = $res ? json_decode($res, true) : [];
        if (!$res || !isset($res['code']) || $res['code'] != 0) {
            \think\facade\Log::error(['msg' => $tip . 'đẩy dữ liệu thất bại', 'data' => $res]);
            return false;
        }
        return true;
    }
}

if (!function_exists('dump_sql')) {
    /**
     * In sql
     * @param string $pushUrl
     * @param array $data
     * @param string $tip
     * @return bool
     */
    function dump_sql()
    {
        Db::listen(function ($sql) {
            var_dump($sql);
        });
    }
}

if (!function_exists('toIntArray')) {

    /**
     * Xử lý ids... và lọc tham số
     * @param $data
     * @param string $separator
     * @return array
     */
    function toIntArray($data, string $separator = ',')
    {
        if (!is_string($data)) {
            return array_unique(array_diff(array_map('intval', $data), [0]));
        } else {
            return !empty($data) ? array_unique(array_diff(array_map('intval', explode($separator, $data)), [0])) : [];
        }
    }
}
