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


use Channel\Client;

class ChannelService
{
    /**
     * @var Client
     */
    protected $channel;

    /**
     * @var
     */
    protected $trigger = 'crmeb';

    /**
     * @var ChannelService
     */
    protected static $instance;

    public function __construct()
    {
        self::connet();
    }

    public static function instance()
    {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    public static function connet()
    {
        $config = config('workerman.channel');
        Client::connect($config['ip'], $config['port']);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setTrigger(string $name)
    {
        $this->trigger = $name;
        return $this;
    }

    /**
     * Gửi tin nhắn
     * @param string $type Loại
     * @param array|null $data Dữ liệu
     * @param array|null $ids ID người dùng, không truyền thì áp dụng cho tất cả người dùng
     */
    public function send(string $type, ?array $data = null, ?array $ids = null)
    {
        $res = compact('type');
        if (!is_null($data))
            $res['data'] = $data;

        if (!is_null($ids) && count($ids))
            $res['ids'] = $ids;

        $this->trigger($this->trigger, $res);
        $this->trigger = 'crmeb';
    }

    public function trigger(string $type, ?array $data = null)
    {
        Client::publish($type, $data);
    }
}
