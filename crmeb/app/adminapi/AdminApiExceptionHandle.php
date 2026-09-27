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

namespace app\adminapi;


use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\exceptions\AuthException;
use think\db\exception\DbException;
use think\exception\Handle;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Env;
use think\facade\Log;
use think\Response;
use Throwable;

class AdminApiExceptionHandle extends Handle
{
    /**
     * Danh sách lớp ngoại lệ không cần ghi thông tin (log)
     * @var array
     */
    protected $ignoreReport = [
        ValidateException::class,
        AuthException::class,
        AdminException::class,
        ApiException::class,
    ];

    /**
     * Ghi lại thông tin ngoại lệ (bao gồm log hoặc cách ghi khác)
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        if (!$this->isIgnoreReport($exception)) {
            try {
                $data = [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'message' => $this->getMessage($exception),
                    'code' => $this->getCode($exception),
                ];

                //Nội dung log
                $log = [
                    request()->adminId(),                                                                 //ID quản trị viên
                    request()->ip(),                                                                      //IP khách hàng
                    ceil(msectime() - (request()->time(true) * 1000)),                               //Thời gian xử lý (mili giây)
                    request()->rule()->getMethod(),                                                       //Loại request
                    str_replace("/", "", request()->rootUrl()),                             //Ứng dụng
                    request()->baseUrl(),                                                                 //Đường dẫn
                    json_encode(request()->param(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),//Tham số request
                    json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),             //Dữ liệu báo lỗi

                ];
                Log::write(implode("|", $log), "error");
            } catch (\Throwable $e) {
                Log::write($e->getMessage(), "error");
            }
        }
    }

    /**
     * Render an exception into an HTTP response.
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof HttpResponseException) {
            return parent::render($request, $e);
        }
        $massageData = Env::get('app_debug', false) ? [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
            'previous' => $e->getPrevious(),
        ] : [];
        $message = $e->getMessage();
        // Thêm cơ chế xử lý ngoại lệ tùy chỉnh
        if ($e instanceof AuthException || $e instanceof AdminException || $e instanceof ApiException || $e instanceof ValidateException) {
            return app('json')->make($e->getCode() ?: 400, $message, $massageData);
        } else {
            return app('json')->fail($message, $massageData);
        }
    }

}
