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

namespace crmeb\services\workerman;


use Workerman\Connection\TcpConnection;

class Response
{
    /**
     * @var TcpConnection
     */
    protected $connection;

    /**
     * Thiết lập người dùng
     *
     * @param TcpConnection $connection
     * @return $this
     */
    public function connection(TcpConnection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Gửi yêu cầu
     *
     * @param string $type
     * @param array|null $data
     * @param bool $close
     * @param array $other
     * @return bool|null
     */
    public function send(string $type, ?array $data = null, bool $close = false, array $other = [])
    {
        $this->connection->lastMessageTime = time();
        $res = compact('type');

        if (!is_null($data)) $res['data'] = $data;
        $data = array_merge($res, $other);

        if ($close)
            $data['close'] = true;

        $json = json_encode($data);

        return $close
            ? ($this->connection->close($json))
            : $this->connection->send($json);
    }

    /**
     * Thành công
     *
     * @param string $message
     * @param array|null $data
     * @return bool|null
     */
    public function success($type = 'success', ?array $data = null)
    {
        if (is_array($type)) {
            $data = $type;
            $type = 'success';
        }
        return $this->send($type, $data);
    }

    /**
     * Thất bại
     *
     * @param string $message
     * @param array|null $data
     * @return bool|null
     */
    public function fail($type = 'error', ?array $data = null)
    {
        if (is_array($type)) {
            $data = $type;
            $type = 'error';
        }
        return $this->send($type, $data);
    }

    /**
     * Đóng kết nối
     *
     * @param string $type
     * @param array|null $data
     * @return bool|null
     */
    public function close($type = 'error', ?array $data = null)
    {
        if (is_array($type)) {
            $data = $type;
            $type = 'error';
        }
        return $this->send($type, $data, true);
    }
}
