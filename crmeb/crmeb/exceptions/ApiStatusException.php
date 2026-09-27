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

namespace crmeb\exceptions;

/**
 * Thông tin lỗi ứng dụng API
 * Class ApiException
 * @package crmeb\exceptions
 */
class ApiStatusException extends \RuntimeException
{
    protected $apiStatus;
    protected $apiData;

    public function __construct($status, $message, $data = [], $replace = [], $code = 0, \Throwable $previous = null)
    {
        if (is_array($message)) {
            $errInfo = $message;
            $message = $errInfo[1] ?? 'Lỗi không xác định';
            if ($code === 0) {
                $code = $errInfo[0] ?? 400;
            }
        }

        if (is_numeric($message)) {
            $code = $message;
            $message = getLang($message, $replace);
        }

        $this->apiData = $data;
        $this->apiStatus = $status;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getApiStatus()
    {
        return $this->apiStatus;
    }

    /**
     * @return array|mixed
     */
    public function getApiData()
    {
        return $this->apiData;
    }
}
