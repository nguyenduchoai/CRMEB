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
declare (strict_types=1);

namespace app\services\product\product;

use app\services\BaseServices;
use app\services\serve\ServeServices;
use app\services\system\attachment\SystemAttachmentCategoryServices;
use app\services\system\attachment\SystemAttachmentServices;
use crmeb\exceptions\AdminException;
use app\services\other\UploadService;
use think\facade\Config;

/**
 *
 * Class CopyTaobaoServices
 * @package app\services\product\product
 */
class CopyTaobaoServices extends BaseServices
{
    /**
     * @var bool
     */
    protected $errorInfo = true;

    /**
     * @var string
     */
    public $AttachmentCategoryName = 'Tải về từ xa';

    /**
     * @var string[]
     */
    protected $host = ['taobao', 'tmall', 'jd', 'pinduoduo', 'suning', 'yangkeduo', '1688'];

    /**
     * @param $type
     * @param $id
     * @param $shopid
     * @param $url
     * @return array
     */
    public function copyProduct($type, $id, $shopid, $url)
    {
        $result = [];
        switch ((int)sys_config('system_product_copy_type')) {
            case 1://Nền tảng
                /** @var ServeServices $services */
                $services = app()->make(ServeServices::class);
                $resultData = $services->copy('copy')->goods($url);
                if (isset($resultData['description_image']) && is_string($resultData['description_image'])) {
                    $resultData['description_image'] = json_decode($resultData['description_image'], true);
                }
                if (isset($resultData['slider_image']) && is_string($resultData['slider_image'])) {
                    $resultData['slider_image'] = json_decode($resultData['slider_image'], true);
                }
                $result['status'] = 200;
                $result['data'] = $resultData;
                break;
            case 2://99API
                $apikey = sys_config('copy_product_apikey');
                if (!$apikey) throw new AdminException('Vui lòng cấu hình khóa API trước');
                /** @var ServeServices $services */
                $services = app()->make(ServeServices::class);
                $result = $services->copy('copy99api')->goods($url, [
                    'apikey' => $apikey,
                ]);
                break;
        }
        if (isset($result['status']) && $result['status']) {

            /** @var StoreProductServices $ProductServices */
            $ProductServices = app()->make(StoreProductServices::class);
            /** @var StoreCategoryServices $storeCatecoryService */
            $storeCatecoryService = app()->make(StoreCategoryServices::class);
            $data = [];
            $productInfo = $result['data'];
            if (count($productInfo['slider_image'])) {
                $productInfo['slider_image'] = array_map(function ($item) {
                    $item = str_replace('\\', '/', $item);
                    return $item;
                }, $productInfo['slider_image']);
            }
            $data['tempList'] = $ProductServices->getTemp();
            $menus = [];
            foreach ($storeCatecoryService->getTierList(1) as $menu) {
                $menus[] = ['value' => $menu['id'], 'label' => $menu['html'] . $menu['cate_name'], 'disabled' => $menu['pid'] == 0 ? 0 : 1];//,'disabled'=>$menu['pid']== 0];
            }
            $data['cateList'] = $menus;
            $productInfo['attrs'] = $result['data']['info']['value'];
            foreach ($productInfo['attrs'] as $attrs_k => $attrs_v) {
                $productInfo['attrs'][$attrs_k]['attr_arr'] = array_values($attrs_v['detail']);
                $productInfo['attrs'][$attrs_k]['is_show'] = 1;
            }
            $productInfo['activity'] = ['Mặc định', 'Flash sale', 'Săn giảm giá', 'Mua chung'];
            $productInfo['bar_code'] = '';
            $productInfo['browse'] = 0;
            $productInfo['cate_id'] = [];
            $productInfo['code_path'] = '';
            $productInfo['command_word'] = '';
            $productInfo['coupons'] = [];
            $productInfo['is_bargain'] = '';
            $productInfo['is_benefit'] = 0;
            $productInfo['is_best'] = 0;
            $productInfo['is_del'] = 0;
            $productInfo['is_good'] = 0;
            $productInfo['is_hot'] = 0;
            $productInfo['is_new'] = 0;
            $productInfo['is_postage'] = 0;
            $productInfo['is_seckill'] = 0;
            $productInfo['is_show'] = 0;
            $productInfo['is_sub'] = [];
            $productInfo['is_vip'] = 0;
            $productInfo['label_id'] = [];
            $productInfo['mer_id'] = 0;
            $productInfo['mer_use'] = 0;
            $productInfo['recommend_image'] = '';
            $productInfo['sales'] = '';
            $productInfo['sort'] = 0;
            $productInfo['spec_type'] = 1;
            $productInfo['is_virtual'] = 0;
            $productInfo['virtual_type'] = 0;
            $productInfo['spu'] = '';
            $productInfo['id'] = 0;
            $productInfo['temp_id'] = 1;
            $productInfo['freight'] = 3;
            $productInfo['recommend'] = [];
            $productInfo['logistics'] = ['1', '2'];
            $productInfo['params_list'] = [];
            $productInfo['label_list'] = [];
            $productInfo['protection_list'] = [];
            foreach ($productInfo['items'] as &$items) {
                $details = [];
                foreach ($items['detail'] as $detail) {
                    $details[] = [
                        'value' => $detail,
                        'pic' => '',
                    ];
                }
                $items['detail'] = $details;
                $items['add_pic'] = 0;
            }
            $data['productInfo'] = $productInfo;
            return $data;
        } else {
            throw new AdminException($result['msg']);
        }
    }

    /**
     * Tải hình ảnh chi tiết sản phẩm
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function uploadDescriptionImage(int $id)
    {
        //Truy vấn danh mục tệp đính kèm
        /** @var SystemAttachmentCategoryServices $systemAttachmentCategoryService */
        $systemAttachmentCategoryService = app()->make(SystemAttachmentCategoryServices::class);
        /** @var StoreDescriptionServices $storeDescriptionServices */
        $storeDescriptionServices = app()->make(StoreDescriptionServices::class);
        $AttachmentCategory = $systemAttachmentCategoryService->getOne(['name' => $this->AttachmentCategoryName]);
        //Nếu không tồn tại thì tạo
        if (!$AttachmentCategory) $AttachmentCategory = $systemAttachmentCategoryService->save(['pid' => '0', 'name' => $this->AttachmentCategoryName, 'enname' => '']);
        //Tạo thư mục tệp đính kèm
        try {
            if (make_path('attach', 3, true) === '')
                throw new AdminException('Không thể tạo thư mục, vui lòng kiểm tra quyền của thư mục tải lên');
        } catch (\Exception $e) {
            throw new AdminException('Không thể tạo thư mục, vui lòng kiểm tra quyền của thư mục tải lên');
        }
        $description = $storeDescriptionServices->getDescription(['product_id ' => $id, 'type' => 0]);
        if (!$description) throw new AdminException('Tham số sản phẩm không hợp lệ');
        //Thay thế và tải ảnh trong chi tiết, mặc định tải toàn bộ ảnh
        $description = preg_replace('#<style>.*?</style>#is', '', $description);
        $description = $this->uploadImage([], $description, 1, $AttachmentCategory['id']);
        $storeDescriptionServices->saveDescription((int)$id, $description);
        return true;
    }

    /**
     * Xử lý tải ảnh lên
     * @param array $images
     * @param string $html
     * @param int $uploadType
     * @param int $AttachmentCategoryId
     * @return array|bool|string|string[]|null
     */
    public function uploadImage(array $images = [], $html = '', $uploadType = 0, $AttachmentCategoryId = 0)
    {
        /** @var SystemAttachmentServices $systemAttachmentService */
        $systemAttachmentService = app()->make(SystemAttachmentServices::class);
        $uploadImage = [];
        $siteUrl = sys_config('site_url');
        switch ($uploadType) {
            case 0:
                foreach ($images as $item) {
                    //Tải file ảnh xuống
                    if ($item['w'] && $item['h'])
                        $uploadValue = $this->downloadImage($item['line'], '', 0, 30, $item['w'], $item['h']);
                    else
                        $uploadValue = $this->downloadImage($item['line']);
                    //Tải xuống thành công thì cập nhật cơ sở dữ liệu
                    if (is_array($uploadValue)) {
                        //TODO ghép địa chỉ ảnh
                        if ($uploadValue['image_type'] == 1) $imagePath = $siteUrl . $uploadValue['path'];
                        else $imagePath = $uploadValue['path'];
                        //Ghi vào cơ sở dữ liệu
                        if (!$uploadValue['is_exists'] && $AttachmentCategoryId) {
                            $systemAttachmentService->save([
                                'name' => $uploadValue['name'],
                                'real_name' => $uploadValue['name'],
                                'att_dir' => $imagePath,
                                'satt_dir' => $imagePath,
                                'att_size' => $uploadValue['size'],
                                'att_type' => $uploadValue['mime'],
                                'image_type' => $uploadValue['image_type'],
                                'module_type' => 1,
                                'time' => time(),
                                'pid' => $AttachmentCategoryId
                            ]);
                        }
                        //Lắp ráp mảng
                        if (isset($item['isTwoArray']) && $item['isTwoArray'])
                            $uploadImage[$item['valuename']][] = $imagePath;
                        else
                            $uploadImage[$item['valuename']] = $imagePath;
                    }
                }
                break;
            case 1:
                preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i', $html, $match);
                if (isset($match[1])) {
                    foreach ($match[1] as $item) {
                        if (is_int(strpos($item, 'http')))
                            $arcurl = $item;
                        else
                            $arcurl = 'http://' . ltrim($item, '\//');
                        $uploadValue = $this->downloadImage($arcurl);
                        //Tải xuống thành công thì cập nhật cơ sở dữ liệu
                        if (is_array($uploadValue)) {
                            //TODO ghép địa chỉ ảnh
                            if ($uploadValue['image_type'] == 1) $imagePath = $siteUrl . $uploadValue['path'];
                            else $imagePath = $uploadValue['path'];
                            //Ghi vào cơ sở dữ liệu
                            if (!$uploadValue['is_exists'] && $AttachmentCategoryId) {
                                $systemAttachmentService->save([
                                    'name' => $uploadValue['name'],
                                    'real_name' => $uploadValue['name'],
                                    'att_dir' => $imagePath,
                                    'satt_dir' => $imagePath,
                                    'att_size' => $uploadValue['size'],
                                    'att_type' => $uploadValue['mime'],
                                    'image_type' => $uploadValue['image_type'],
                                    'module_type' => 1,
                                    'time' => time(),
                                    'pid' => $AttachmentCategoryId
                                ]);
                            }
                            //Thay thế ảnh
                            $html = str_replace($item, $imagePath, $html);
                        } else {
                            //Thay thế ảnh chưa tải xuống được
                            $html = preg_replace('#<img.*?src="' . $item . '"*>#i', '', $html);
                        }
                    }
                }
                return $html;
                break;
            default:
                throw new AdminException('Phương thức tải lên không hợp lệ');
                break;
        }
        return $uploadImage;
    }

    /**
     * @param string $url
     * @param string $name
     * @param int $type
     * @param int $timeout
     * @param int $w
     * @param int $h
     * @return array|string
     */
    public function downloadImage($url = '', $name = '', $type = 0, $timeout = 30, $w = 0, $h = 0)
    {
        if (!strlen(trim($url))) return '';

        // Tự động xác định link ảnh hiện tại có thuộc nền tảng cần dùng curl để tải xuống không (Taobao, JD, Tmall, 1688)
        if ($type == 0 && strlen(trim($url))) {
            $antiHotlinkingPlatforms = ['alicdn.com', 'taobao.com', 'tmall.com', 'jd.com', 'jdstatic.com', '1688.com'];
            foreach ($antiHotlinkingPlatforms as $platform) {
                if (stripos($url, $platform) !== false) {
                    $type = 1;
                    break;
                }
            }
        }

        if (!strlen(trim($name))) {
            //TODO lấy tên file cần tải xuống
            $downloadImageInfo = $this->getImageExtname($url);
            $ext = $downloadImageInfo['ext_name'];
            $name = $downloadImageInfo['file_name'];
            if (!strlen(trim($name))) return '';
        } else {
            $ext = $this->getImageExtname($name)['ext_name'];
        }
        if (!in_array($ext, Config::get('upload.fileExt'))) {
            throw new AdminException('Sai định dạng');
        }
        //TODO lấy phương thức dùng để tải file từ xa
        if ($type) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //TODO bỏ qua kiểm tra chứng chỉ
            if (stripos($url, "https://") !== FALSE) curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  //TODO kiểm tra trong chứng chỉ xem có thuật toán mã hóa SSL không
            // Nhận diện nền tảng theo URL và lấy headers chống hotlink tương ứng
            $headers = $this->getAntiHotlinkingHeaders($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//TODO có thu thập trang sau khi chuyển hướng 301, 302 không
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            try {
                ob_start();
                if (substr($url, 0, 2) == '//') {
                    $url = "https:" . $url;
                }
                @readfile($url);
                $content = ob_get_contents();
                ob_end_clean();
            } catch (\Exception $e) {
                throw new AdminException($e->getMessage());
            }
        }
        $size = strlen(trim($content));
        if (!$content || $size <= 2) throw new AdminException('Lấy luồng dữ liệu hình ảnh thất bại');
        $date_dir = date('Y') . '/' . date('m') . '/' . date('d');
        $upload_type = sys_config('upload_type', 1);
        $upload = UploadService::init($upload_type);
        if ($upload->to('attach/' . $date_dir)->validate()->setAuthThumb(false)->stream($content, $name) === false) {
            throw new AdminException($upload->getError());
        }
        $imageInfo = $upload->getUploadInfo();
        $date['path'] = $imageInfo['dir'];
        $date['name'] = $imageInfo['name'];
        $date['size'] = $imageInfo['size'];
        $date['mime'] = $imageInfo['type'];
        $date['image_type'] = $upload_type;
        $date['is_exists'] = false;
        return $date;
    }

    /**
     * Lấy phần mở rộng của ảnh sẽ tải xuống
     * @param string $url
     * @param string $ex
     * @return array|string[]
     */
    public function getImageExtname($url = '', $ex = 'jpg')
    {
        $_empty = ['file_name' => '', 'ext_name' => $ex];
        if (!$url) return $_empty;
        if (strpos($url, '?')) {
            $_tarr = explode('?', $url);
            $url = trim($_tarr[0]);
        }
        $arr = explode('.', $url);
        if (!is_array($arr) || count($arr) <= 1) return $_empty;
        $ext_name = trim($arr[count($arr) - 1]);
        $ext_name = !$ext_name ? $ex : $ext_name;
        return ['file_name' => md5($url) . '.' . $ext_name, 'ext_name' => $ext_name];
    }

    /**
     * SPU
     * @return string
     */
    public function createSpu()
    {
        return substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8) . str_pad((string)mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Tải ảnh từ xa và tải lên
     * @param $image
     * @return false|mixed|string
     * @throws \Exception
     */
    public function downloadCopyImage($image)
    {
        //Truy vấn danh mục tệp đính kèm
        /** @var SystemAttachmentCategoryServices $systemAttachmentCategoryService */
        $systemAttachmentCategoryService = app()->make(SystemAttachmentCategoryServices::class);
        $AttachmentCategory = $systemAttachmentCategoryService->getOne(['name' => 'Tải về từ xa']);
        //Nếu không tồn tại thì tạo
        if (!$AttachmentCategory) {
            $AttachmentCategory = $systemAttachmentCategoryService->save(['pid' => '0', 'name' => 'Tải về từ xa', 'enname' => '']);
        }

        //Tạo thư mục tệp đính kèm
        if (make_path('attach', 3, true) === '') {
            throw new AdminException('Không thể tạo thư mục, vui lòng kiểm tra quyền của thư mục tải lên');
        }

        //Tải lên ảnh
        /** @var SystemAttachmentServices $systemAttachmentService */
        $systemAttachmentService = app()->make(SystemAttachmentServices::class);
        $siteUrl = sys_config('site_url');
        $uploadValue = $this->downloadImage($image);
        if (is_array($uploadValue)) {
            //TODO ghép địa chỉ ảnh
            if ($uploadValue['image_type'] == 1) {
                $imagePath = $siteUrl . $uploadValue['path'];
            } else {
                $imagePath = $uploadValue['path'];
            }
            //Ghi vào cơ sở dữ liệu
            if (!$uploadValue['is_exists'] && $AttachmentCategory['id']) {
                $systemAttachmentService->save([
                    'name' => $uploadValue['name'],
                    'real_name' => $uploadValue['name'],
                    'att_dir' => $imagePath,
                    'satt_dir' => $imagePath,
                    'att_size' => $uploadValue['size'],
                    'att_type' => $uploadValue['mime'],
                    'image_type' => $uploadValue['image_type'],
                    'module_type' => 1,
                    'time' => time(),
                    'pid' => $AttachmentCategory['id']
                ]);
            }
            return $imagePath;
        }
        return false;
    }

    /**
     * Nhận diện nền tảng theo URL và lấy headers chống hotlink tương ứng
     * @param string $url URL ảnh
     * @return array Trả về HTTP headers tương ứng
     */
    public function getAntiHotlinkingHeaders(string $url): array
    {
        // Headers cơ bản
        $baseHeaders = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
        ];

        // Nhận diện nền tảng theo URL
        if (stripos($url, 'alicdn.com') !== false || stripos($url, 'taobao.com') !== false) {
            // Ảnh của Taobao/Tmall/1688 (thường nằm dưới domain alicdn.com)
            return array_merge($baseHeaders, [
                'Referer: https://buyer.taobao.com/',  // Trung tâm người mua Taobao
            ]);
        } elseif (stripos($url, 'tmall.com') !== false) {
            // Ảnh của Tmall
            return array_merge($baseHeaders, [
                'Referer: https://www.tmall.com/',  // Trang chủ Tmall
            ]);
        } elseif (stripos($url, 'jd.com') !== false || stripos($url, 'jdstatic.com') !== false) {
            // Ảnh của JD
            return array_merge($baseHeaders, [
                'Referer: https://www.jd.com/',  // Trang chủ JD
            ]);
        } elseif (stripos($url, '1688.com') !== false) {
            // Ảnh của 1688
            return array_merge($baseHeaders, [
                'Referer: https://www.1688.com/',  // Trang chủ 1688
            ]);
        } elseif (stripos($url, 'baidu.com') !== false) {
            // Ảnh Baidu
            return array_merge($baseHeaders, [
                'Referer: https://image.baidu.com/',  // Ảnh Baidu
            ]);
        } elseif (stripos($url, 'sinaimg.cn') !== false) {
            // Ảnh Sina
            return array_merge($baseHeaders, [
                'Referer: https://weibo.com/',  // Sina Weibo
            ]);
        } else {
            // Headers mặc định, không đặt Referer
            return $baseHeaders;
        }
    }
}
