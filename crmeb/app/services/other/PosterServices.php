<?php

namespace app\services\other;

use dh2y\qrcode\QRcode;
use think\facade\Config;

class PosterServices
{
    /**
     * TODO tạo ảnh chia sẻ săn giảm giá, mua chung
     * @param array $data
     * @param $path
     * @return array|bool|string
     * @throws \Exception
     */
    public static function setShareMarketingPoster($data = array(), $path)
    {
        $config = array(
            'text' => array(
                array(
                    'text' => $data['price'],//TODO giá
                    'left' => 116,
                    'top' => 200,
                    'fontPath' => app()->getRootPath() . 'public/statics/font/Alibaba-PuHuiTi-Regular.otf',     //File font
                    'fontSize' => 50,             //Cỡ chữ
                    'fontColor' => '255,0,0',       //Màu chữ
                    'angle' => 0,
                ),
                array(
                    'text' => $data['label'],//TODO nhãn
                    'left' => 450,
                    'top' => 188,
                    'fontPath' => app()->getRootPath() . 'public/statics/font/Alibaba-PuHuiTi-Regular.otf',     //File font
                    'fontSize' => 24,             //Cỡ chữ
                    'fontColor' => '255,255,255',       //Màu chữ
                    'angle' => 0,
                ),
                array(
                    'text' => $data['msg'],//TODO mô tả ngắn
                    'left' => 80,
                    'top' => 270,
                    'fontPath' => app()->getRootPath() . 'public/statics/font/Alibaba-PuHuiTi-Regular.otf',     //File font
                    'fontSize' => 22,             //Cỡ chữ
                    'fontColor' => '40,40,40',       //Màu chữ
                    'angle' => 0,
                )
            ),
            'image' => array(
                array(
                    'url' => $data['image'],     //Hình ảnh
                    'stream' => 0,
                    'left' => 120,
                    'top' => 340,
                    'right' => 0,
                    'bottom' => 0,
                    'width' => 450,
                    'height' => 450,
                    'opacity' => 100
                ),
                array(
                    'url' => $data['url'],     //Resource mã QR
                    'stream' => 0,
                    'left' => 260,
                    'top' => 890,
                    'right' => 0,
                    'bottom' => 0,
                    'width' => 160,
                    'height' => 160,
                    'opacity' => 100
                )
            ),
            'background' => 'statics/poster/poster.jpg'
        );
        if (!file_exists($config['background'])) exception('Thiếu ảnh nền mặc định của hệ thống');
        if (strlen($data['title']) < 36) {
            $text = array(
                'text' => $data['title'],//TODO tiêu đề
                'left' => 76,
                'top' => 100,
                'fontPath' => app()->getRootPath() . 'public/statics/font/Alibaba-PuHuiTi-Regular.otf',     //File font
                'fontSize' => 32,         //Cỡ chữ
                'fontColor' => '0,0,0',       //Màu chữ
                'angle' => 0,
            );
            array_push($config['text'], $text);
        } else {
            $titleOne = array(
                'text' => mb_strimwidth($data['title'], 0, 24),//TODO tiêu đề
                'left' => 76,
                'top' => 70,
                'fontPath' => app()->getRootPath() . 'public/statics/font/Alibaba-PuHuiTi-Regular.otf',     //File font
                'fontSize' => 32,         //Cỡ chữ
                'fontColor' => '0,0,0',       //Màu chữ
                'angle' => 0,
            );
            $titleTwo = array(
                'text' => mb_strimwidth($data['title'], mb_strlen(mb_strimwidth($data['title'], 0, 24)), 24),//TODO tiêu đề
                'left' => 76,
                'top' => 120,
                'fontPath' => app()->getRootPath() . 'public/statics/font/Alibaba-PuHuiTi-Regular.otf',     //File font
                'fontSize' => 32,         //Cỡ chữ
                'fontColor' => '0,0,0',       //Màu chữ
                'angle' => 0,
            );
            array_push($config['text'], $titleOne);
            array_push($config['text'], $titleTwo);
        }
        return self::setSharePoster($config, $path);
    }

    /**
     * TODO tạo ảnh mã QR chia sẻ
     * @param array $config
     * @param $path
     * @return array|bool|string
     * @throws \Exception
     */
    public static function setSharePoster($config = array(), $path, $name = '')
    {
        $imageDefault = array(
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 100,
            'height' => 100,
            'opacity' => 100
        );
        $textDefault = array(
            'text' => '',
            'left' => 0,
            'top' => 0,
            'fontSize' => 32,       //Cỡ chữ
            'fontColor' => '255,255,255', //Màu chữ
            'angle' => 0,
        );
        $background = $config['background'];//Lớp nền dưới cùng của ảnh chia sẻ
        if (substr($background, 0, 1) === '/') {
            $background = substr($background, 1);
        }
        $background = str_replace('https://', 'http://', $background);
        $backgroundInfo = getimagesize($background);
        $background = imagecreatefromstring(file_get_contents($background));
        $backgroundWidth = $backgroundInfo[0];  //Chiều rộng nền
        $backgroundHeight = $backgroundInfo[1];  //Chiều cao nền
        $imageRes = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);
        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));
        if (!empty($config['image'])) {
            foreach ($config['image'] as $key => $val) {
                $val = array_merge($imageDefault, $val);
                $val['url'] = str_replace('https', 'http', $val['url']);
                $info = getimagesize($val['url']);
                $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
                if ($val['stream']) {
                    $info = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res = $function($val['url']);
                $resWidth = $info[0];
                $resHeight = $info[1];
                $canvas = imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) - $val['width'] : $val['left'];
                $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
                imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']);//Trái, trên, phải, dưới, chiều rộng, chiều cao, độ trong suốt
            }
        }
        if (isset($config['text']) && !empty($config['text'])) {
            foreach ($config['text'] as $key => $val) {
                $val = array_merge($textDefault, $val);
                list($R, $G, $B) = explode(',', $val['fontColor']);
                $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
                $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) : $val['left'];
                $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
                imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);
            }
        }
        ob_start();
        imagejpeg($imageRes);
        imagedestroy($imageRes);
        $res = ob_get_contents();
        ob_end_clean();
        if ($name == '') {
            $key = substr(md5(rand(0, 9999)), 0, 5) . date('YmdHis') . rand(0, 999999) . '.jpg';
        } else {
            $key = $name;
        }
        $uploadType = (int)sys_config('upload_type', 1);
        $upload = UploadService::init();
        $res = $upload->to($path)->validate()->setAuthThumb(false)->stream($res, $key);
        if ($res === false) {
            return $upload->getError();
        } else {
            $info = $upload->getUploadInfo();
            $info['image_type'] = $uploadType;
            return $info;
        }
    }


    /**
     * TODO lấy trạng thái đã tạo mã QR Mini Program hay chưa
     * @param $url
     * @return array
     */
    public static function remoteImage($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $result = json_decode($result, true);
        if (is_array($result)) return ['status' => false, 'msg' => $result['errcode'] . '---' . $result['errmsg']];
        return ['status' => true];
    }

    /**
     * TODO sửa https và http, chuyển vào common
     * @param $url $url Tên miền
     * @param int $type 0 Trả về https, 1 thì trả về http
     * @return string
     */
    public static function setHttpType($url, $type = 0)
    {
        $domainTop = substr($url, 0, 5);
        if ($type) {
            if ($domainTop == 'https') $url = 'http' . substr($url, 5, strlen($url));
        } else {
            if ($domainTop != 'https') $url = 'https:' . substr($url, 5, strlen($url));
        }
        return $url;
    }


    /**
     * Lấy mã QR
     * @param $url
     * @param $name
     * @return array|bool|string
     */
    public static function getQRCodePath($url, $name)
    {
        if (!strlen(trim($url)) || !strlen(trim($name))) return false;
        try {
            $uploadType = sys_config('upload_type');
            //TODO nếu không chọn thì mặc định dùng tải lên cục bộ
            if (!$uploadType) $uploadType = 1;
            $uploadType = (int)$uploadType;
            $siteUrl = sys_config('site_url');
            if (!$siteUrl) return 'Vui lòng vào Cài đặt quản trị->Cài đặt hệ thống->Tên miền website để điền tên miền của bạn, định dạng: http://tên-miền';
            $info = [];
            $outfiles = Config::get('qrcode.cache_dir');
            $code = new QRcode();
            if (!file_exists($outfiles)) mkdir($outfiles, 0775, true);
            $wapCodePath = $code->png($url, $outfiles . '/' . $name)->getPath(); //Lấy địa chỉ tạo mã QR
            $content = file_get_contents('.' . $wapCodePath);
            if ($uploadType === 1) {
                $info["code"] = 200;
                $info["name"] = $name;
                $info["dir"] = '/' . $outfiles . '/' . $name;
                $info["time"] = time();
                $info['size'] = 0;
                $info['type'] = 'image/png';
                $info["image_type"] = 1;
                $info['thumb_path'] = '/' . $outfiles . '/' . $name;
                return $info;
            } else {
                $upload = UploadService::init($uploadType);
                $res = $upload->to($outfiles)->validate()->setAuthThumb(false)->stream($content, $name);
                if ($res === false) {
                    return $upload->getError();
                }
                $info = $upload->getUploadInfo();
                $info['image_type'] = $uploadType;
                return $info;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**Trả về tất cả ID danh mục cấp dưới theo từng cấp
     * @param $data
     * @param string $children
     * @param string $field
     * @param string $pk
     * @return string
     */
    public static function getChildrenPid($data, $pid, $field = 'pid', $pk = 'id')
    {
        static $pids = '';
        foreach ($data as $k => $res) {
            if ($res[$field] == $pid) {
                $pids .= ',' . $res[$pk];
                self::getChildrenPid($data, $res[$pk], $field, $pk);
            }
        }
        return $pids;
    }
}
