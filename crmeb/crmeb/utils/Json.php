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

namespace crmeb\utils;

use think\Response;

/**
 * Class xuất Json
 * Class Json
 * @package crmeb\utils
 */
class Json
{
    private $code = 200;
    private $header = [];

    public function code(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function header(array $header): self
    {
        $this->header = $header;
        return $this;
    }

    public function make(int $status, string $msg, ?array $data = null, ?array $replace = []): Response
    {
        $res = compact('status', 'msg');

        if (!is_null($data)) $res['data'] = $data;

        $res['msg'] = getLang($res['msg'], $replace);

        return Response::create($res, 'json', $this->code)->header($this->header);
    }

    public function success($msg = 'success', ?array $data = null, ?array $replace = []): Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'success';
        }

        return $this->make(200, $msg, $data, $replace);
    }

    public function fail($msg = 'fail', ?array $data = null, ?array $replace = []): Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'fail';
        }

        return $this->make(400, $msg, $data, $replace);
    }

    public function status($status, $msg, $result = [])
    {
        $status = strtoupper($status);
        if (is_array($msg)) {
            $result = $msg;
            $msg = 'success';
        }
        return $this->success($msg, compact('status', 'result'));
    }
}
