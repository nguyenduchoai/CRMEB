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

namespace app\services\system;


use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use think\facade\Config;
use think\facade\Db;

/**
 * Xóa dữ liệu
 * Class SystemClearServices
 * @package app\services\system
 */
class SystemClearServices extends BaseServices
{
    /**
     * Xóa dữ liệu bảng
     * @param string|array $table_name
     * @param $status
     */
    public function clearData($table_name, bool $status)
    {
        $prefix = config('database.connections.' . config('database.default'))['prefix'];
        if (is_string($table_name)) {
            $clearData = [$table_name];
        } else {
            $clearData = $table_name;
        }
        foreach ($clearData as $name) {
            if ($status) {
                Db::execute('TRUNCATE TABLE ' . $prefix . $name);
            } else {
                Db::execute('DELETE FROM' . $prefix . $name);
            }
        }
    }

    /**
     * Xóa file theo cách đệ quy, chỉ có thể xóa file trong public/uploads
     * @param $dirName
     * @param bool $subdir
     */
    public function delDirAndFile(string $dirName, $subdir = true)
    {
        if (strstr($dirName, 'public/uploads') === false) {
            return true;
        }
        if ($handle = @opendir("$dirName")) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dirName/$item"))
                        $this->delDirAndFile("$dirName/$item", false);
                    else
                        @unlink("$dirName/$item");
                }
            }
            closedir($handle);
            if (!$subdir) @rmdir($dirName);
        }
    }

    /**
     * Thay thế tên miền
     * @param string $url
     * @return mixed
     */
    public function replaceSiteUrl(string $url)
    {
        // Lấy URL trang web
        $siteUrl = sys_config('site_url');
        // Phân tích protocol của URL trang web
        $siteUrlScheme = parse_url($siteUrl)['scheme'];
        // Thay protocol trong URL trang web thành định dạng JSON
        $siteUrlJson = str_replace($siteUrlScheme . '://', $siteUrlScheme . ':\\\/\\\/', $siteUrl);

        // Lấy protocol của URL hiện tại
        $urlScheme = parse_url($url)['scheme'];
        // Thay protocol trong URL hiện tại thành định dạng JSON
        $urlJson = str_replace($urlScheme . '://', $urlScheme . ':\\\/\\\/', $url);
        // Lấy tiền tố bảng cơ sở dữ liệu
        $prefix = Config::get('database.connections.' . Config::get('database.default') . '.prefix');

        // Xây dựng mảng câu lệnh SQL
        $sql = [
            "UPDATE `{$prefix}agent_level` SET `image` = replace(`image` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}agreement` SET `content` = replace(content ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}article` SET `image_input` = replace(`image_input` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}article_category` SET `image` = replace(`image` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}article_content` SET `content` = replace(`content` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}cache` SET `result` = replace(result ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}delivery_service` SET `avatar` = replace(`avatar` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}division_agent_apply` SET `images` = replace(images ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}diy` SET `value` = replace(value ,'{$siteUrlJson}','{$urlJson}'),`default_value` = replace(default_value ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}diy` SET `value` = replace(value ,'{$siteUrl}','{$url}'),`default_value` = replace(default_value ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}live_anchor` SET `cover_img` = replace(`cover_img` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}live_goods` SET `cover_img` = replace(`cover_img` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}live_room` SET `cover_img` = replace(`cover_img` ,'{$siteUrl}','{$url}'),`share_img` = replace(`share_img` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}luck_lottery` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`content` = replace(content ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}luck_prize` SET `image` = replace(image ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}member_card_batch` SET `qrcode` = replace(qrcode ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}member_right` SET `image` = replace(image ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}out_interface` SET `return_params` = replace(return_params ,'{$siteUrl}','{$url}'),`request_example` = replace(request_example ,'{$siteUrl}','{$url}'),`return_example` = replace(return_example ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}qrcode` SET `url` = replace(url ,'{$siteUrl}','{$url}'),`qrcode_url` = replace(qrcode_url ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_bargain` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`images` = replace(images,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_category` SET `pic` = replace(`pic` ,'{$siteUrl}','{$url}'),`big_pic` = replace(`big_pic` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_combination` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`images` = replace(images,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_integral` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`images` = replace(images,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_integral_order` SET `image` = replace(`image` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_order_cart_info` SET `cart_info` = replace(cart_info ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_order_refund` SET `refund_img` = replace(refund_img ,'{$siteUrl}','{$url}'),`cart_info` = replace(cart_info,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_pink` SET `avatar` = replace(`avatar` ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_product` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`slider_image` = replace(slider_image ,'{$siteUrlJson}','{$urlJson}'),`recommend_image` = replace(recommend_image ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_product_attr_result` SET `result` = replace(result ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_product_attr_value` SET `image` = replace(image ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_product_description` SET `description`= replace(description,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_product_reply` SET `avatar` = replace(avatar ,'{$siteUrl}','{$url}'),`pics` = replace(pics,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_seckill` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`images` = replace(images,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_service` SET `avatar` = replace(avatar ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}store_service_log` SET `msn` = replace(msn ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}store_service_record` SET `avatar` = replace(avatar ,'{$siteUrl}','{$url}'),`message` = replace(message,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}system_admin` SET `head_pic` = replace(head_pic ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}system_attachment` SET `att_dir` = replace(att_dir ,'{$siteUrl}','{$url}'),`satt_dir` = replace(satt_dir ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}system_config` SET `value` = replace(value ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}system_config` SET `value` = replace(value ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}system_group_data` SET `value` = replace(value ,'{$siteUrlJson}','{$urlJson}')",
            "UPDATE `{$prefix}system_store` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`oblong_image` = replace(oblong_image ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}system_store_staff` SET `avatar` = replace(avatar ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}system_user_level` SET `image` = replace(image ,'{$siteUrl}','{$url}'),`icon` = replace(icon ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}user` SET `avatar` = replace(avatar ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}user_extract` SET `qrcode_url` = replace(qrcode_url ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}wechat_qrcode` SET `image` = replace(image ,'{$siteUrl}','{$url}')",
            "UPDATE `{$prefix}wechat_user` SET `headimgurl` = replace(headimgurl ,'{$siteUrl}','{$url}')",
        ];

        // Thực thi câu lệnh SQL
        return $this->transaction(function () use ($sql) {
            try {
                foreach ($sql as $item) {
                    Db::execute($item);
                }
            } catch (\Throwable $e) {
                throw new AdminException(400612, ['msg' => $e->getMessage()]);
            }
        });
    }
}
