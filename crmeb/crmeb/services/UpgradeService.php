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
namespace crmeb\services;

class UpgradeService extends FileService
{
    //Domain yêu cầu
    public static $domain = 'http://shop.crmeb.net/';
    //Cập nhật kịp thời thông tin địa chỉ web
    public static $updatewebinfourl = 'index.php/admin/server.upgrade_api/updatewebinfo.html';
    //Địa chỉ API chung: lấy số phiên bản
    public static $isNowVersionUrl = 'index.php/admin/server.upgrade_api/now_version.html';
    //Địa chỉ API chung: lấy chi tiết phiên bản
    public static $isVersionInfo = 'index.php/admin/server.upgrade_api/version_info.html';
    //Địa chỉ API chung: lấy danh sách phiên bản lịch sử
    public static $isList = 'index.php/admin/server.upgrade_api/get_version_list.html';
    //Địa chỉ API chung: ghi thông tin cập nhật phiên bản
    public static $isInsertLog = 'index.php/admin/server.upgrade_api/set_upgrade_info.html';
    //Địa chỉ API chung: lấy tất cả phiên bản lớn hơn phiên bản hiện tại
    public static $isNowVersion = 'index.php/admin/server.upgrade_api/get_now_version.html';
    //Địa chỉ API chung: cập nhật thông tin địa chỉ web
    protected static $UpdateWeBinfo = 'index.php/admin/server.upgrade_api/updatewebinfo.html';
    //Địa chỉ API chung: lấy số phiên bản chưa cập nhật
    public static $NewVersionCount = 'index.php/admin/server.upgrade_api/new_version_count.html';
    //Địa chỉ API chung: kiểm tra có quyền không, trả về 1 là có quyền, 0 là không có quyền
    protected static $Isauth = 'index.php/admin/server.upgrade_api/isauth.html';
    //Thanh toán giãn cách
    private static $seperater = "{&&}";

    //Cập nhật thông tin địa chỉ web
    public function snyweninfo($serverweb)
    {
        return self::request_post(self::$UpdateWeBinfo, $serverweb);
    }

    //Kiểm tra có quyền không, trả về 1 là có quyền, 0 là không có quyền
    public function isauth()
    {
        return self::request_post(self::$Isauth);
    }

    /*
     *Lấy ip token, tạo thời gian hiện tại và thời gian hết hạn
     * @param string ip
     * @param int $valid_peroid Chu kỳ hết hạn 15 ngày
     */
    public static function get_token($ip = '', $valid_peroid = 1296000)
    {
        $request = app('request');
        if (empty($ip)) $ip = $request->ip();
        $to_ken = $request->domain() . self::$seperater . $ip . self::$seperater . time() . self::$seperater . (time() + $valid_peroid) . self::$seperater;
        $token = self::enCode($to_ken);
        return $token;
    }

    private static function getRet($msg, $code = 400)
    {
        return ['msg' => $msg, 'code' => $code];
    }

    /**
     *
     * @param string $url
     * @param array $post_data
     */
    public static function start()
    {
        $pach = app()->getRootPath() . 'version';
        $request = app('request');
        if (!file_exists($pach)) return self::getRet($pach . 'Tệp nâng cấp bị thiếu, vui lòng liên hệ quản trị viên');
        $version = @file($pach);
        if (!isset($version[0])) return self::getRet('Lấy dữ liệu thất bại');
        $lv = self::request_post(self::$isNowVersionUrl, ['token' => self::get_token($request->ip())]);
        if (isset($lv['code']) && $lv['code'] == 200)
            $version_lv = isset($lv['data']['version']) && $lv['data']['version'] ? $lv['data']['version'] : false;
        else
            return isset($lv['msg']) ? self::getRet($lv['msg']) : self::getRet('Lấy dữ liệu thất bại');
        if ($version_lv === false) return self::getRet('Lấy dữ liệu thất bại');
        if (strstr($version[0], '=') !== false) {
            $version = explode('=', $version[0]);
            if ($version[1] != $version_lv) {
                return self::getRet($version_lv, 200);
            }
        }
        return self::getRet('Lấy dữ liệu thất bại');
    }

    public static function getVersion()
    {
        $pach = app()->getRootPath() . '.version';
        if (!file_exists($pach)) return self::getRet($pach . 'Tệp nâng cấp bị thiếu, vui lòng liên hệ quản trị viên');
        $version = @file($pach);
        if (!isset($version[0]) && !isset($version[1])) return self::getRet('Lấy dữ liệu thất bại');
        $arr = [];
        foreach ($version as $val) {
            list($k, $v) = explode('=', $val);
            $arr[$k] = $v;
        }
        return self::getRet($arr, 200);
    }

    /**
     * Giả lập post để gửi yêu cầu url
     * @param string $url
     * @param array $post_data
     */
    public static function request_post($url = '', $post_data = array())
    {
        if (strstr($url, 'http') === false) $url = self::$domain . $url;
        if (empty($url)) {
            return false;
        }
        if (!isset($post_data['token'])) $post_data['token'] = self::get_token();
        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);
        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//Khởi tạo curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);//Lấy nội dung trang web chỉ định
        curl_setopt($ch, CURLOPT_HEADER, 0);//Đặt header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//Yêu cầu kết quả là chuỗi và xuất ra màn hình
        curl_setopt($ch, CURLOPT_POST, 1);//Cách gửi post
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $data = curl_exec($ch);//Chạy curl
        curl_close($ch);
        if ($data) {
            $data = json_decode($data, true);
        }
        return $data;
    }

    /**
     * Kiểm tra file từ xa có tồn tại không, và tải xuống
     * @param string $url Đường dẫn tệp
     * @param string $savefile Địa chỉ lưu
     */
    public static function check_remote_file_exists($url, $savefile)
    {
        $url = self::$domain . 'public' . DS . 'uploads' . DS . 'upgrade' . DS . $url;
        $url = str_replace('\\', '/', $url);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        // Gửi yêu cầu
        $result = curl_exec($curl);
        $found = false;
        // Nếu gửi yêu cầu không thất bại
        if ($result !== false) {
            // Sau đó kiểm tra mã phản hồi http có phải 200 không
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($statusCode == 200) {
                curl_close($curl);

                $fileservice = new self;
                //Tải xuống tệp
                $zip = $fileservice->downRemoteFile($url, $savefile);
                if ($zip['error'] > 0) return false;
                if (!isset($zip['save_path']) && empty($zip['save_path'])) return false;
                if (!file_exists($zip['save_path'])) return false;
                return $zip['save_path'];
            }
        }
        curl_close($curl);
        return $found;
    }

    /**
     * Mã hóa dùng chung
     * @param String $string Chuỗi cần mã hóa
     * @param String $skey EKY mã hóa
     * @return String
     */
    private static function enCode($string = '', $skey = 'fb')
    {
        $skey = array_reverse(str_split($skey));
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach ($skey as $key => $value) {
            $key < $strCount && $strArr[$key] .= $value;
        }
        return str_replace('=', 'O0O0O', join('', $strArr));
    }

    /**
     * Bỏ ký tự carriage return, bỏ khoảng trắng, bỏ xuống dòng, bỏ tab
     * @param String $str Chuỗi cần loại bỏ
     * @return String
     */
    public static function replace($str)
    {
        return trim(str_replace(array("\r", "\n", "\t"), '', $str));
    }
}
