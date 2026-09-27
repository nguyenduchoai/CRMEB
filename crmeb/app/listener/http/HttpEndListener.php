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

namespace app\listener\http;

use think\facade\Log;
use think\Response;

/**
 * Event kết thúc request
 * Class Create
 * @package app\listener\http
 */
class HttpEndListener
{
    public function handle(Response $response): void
    {
        if (!is_array($response->getData())) return;
        //Lưu riêng thành công và thất bại của nghiệp vụ
        $status = $response->getData()["status"] ?? 0;
        if ($status == 200) {
            //Công tắc log khi nghiệp vụ thành công
            if (!config("log.success_log")) return;
            $type = "success";
        } else {
            //Công tắc log khi nghiệp vụ thất bại
            if (!config("log.fail_log")) return;
            $type = "fail";
        }

        //Định danh người dùng hiện tại
        if (!empty(request()->uid())) {
            $uid = request()->uid();
        } elseif (!empty(request()->adminId())) {
            $uid = request()->adminId();
        } elseif (!empty(request()->kefuId())) {
            $uid = request()->kefuId();
        } else {
            $uid = 0;
        }

        //Nội dung log
        $log = [
            $uid,                                                                                 //ID người dùng
            request()->ip(),                                                                      //IP khách hàng
            ceil(msectime() - (request()->time(true) * 1000)),                                    //Thời gian xử lý (mili giây)
            request()->rule()->getMethod(),                                                       //Loại request
            str_replace("/", "", request()->rootUrl()),                                           //Ứng dụng
            request()->baseUrl(),                                                                 //Đường dẫn
            json_encode(request()->param(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),     //Tham số request
            json_encode($response->getData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),   //Dữ liệu phản hồi

        ];
        Log::write(implode("|", $log), $type);
    }
}
