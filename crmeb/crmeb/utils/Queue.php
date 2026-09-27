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

namespace crmeb\utils;

use think\facade\Config;
use think\facade\Queue as QueueThink;
use think\facade\Log;

/**
 * Class Queue
 * @package crmeb\utils
 * @method $this do(string $do) Thiết lập phương thức thực thi task
 * @method $this job(string $job) Thiết lập tên class thực thi task
 * @method $this errorCount(int $errorCount) Số lần thực thi thất bại
 * @method $this data(...$data) Dữ liệu thực thi
 * @method $this secs(int $secs) Số giây trì hoãn thực thi
 * @method $this log($log) Ghi log
 */
class Queue
{

    /**
     * Thông tin lỗi
     * @var string
     */
    protected $error;

    /**
     * Đặt thông tin lỗi
     * @param string|null $error
     * @return bool
     */
    protected function setError(?string $error = null)
    {
        $this->error = $error ?: 'Lỗi không xác định';
        return false;
    }

    /**
     * Lấy thông tin lỗi
     * @return string
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = null;
        return $error;
    }

    /**
     * Thực thi task
     * @var string
     */
    protected $do = 'doJob';

    /**
     * Tên phương thức thực thi task mặc định
     * @var string
     */
    protected $defaultDo;

    /**
     * Tên class task
     * @var string
     */
    protected $job;

    /**
     * Số lần lỗi
     * @var int
     */
    protected $errorCount = 3;

    /**
     * Dữ liệu
     * @var array|string
     */
    protected $data;

    /**
     * Tên hàng đợi
     * @var null
     */
    protected $queueName = null;

    /**
     * Số giây trì hoãn thực thi
     * @var int
     */
    protected $secs = 0;

    /**
     * Ghi log
     * @var string|callable|array
     */
    protected $log;

    /**
     * @var array
     */
    protected $rules = ['do', 'data', 'errorCount', 'job', 'secs', 'log'];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * Queue constructor.
     */
    protected function __construct()
    {
        $this->defaultDo = $this->do;
    }

    /**
     * @return static
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Thiết lập tên cột
     * @param string $queueName
     * @return $this
     */
    public function setQueueName(string $queueName)
    {
        $this->queueName = $queueName;
        return $this;
    }

    /**
     * Đưa vào hàng đợi tin nhắn
     * @param array|null $data
     * @return mixed
     */
    public function push(?array $data = null)
    {
        if (!$this->job) {
            return $this->setError('Lớp hàng đợi cần thực thi phải tồn tại');
        }
        $jodValue = $this->getValues($data);
        $res = QueueThink::{$this->action()}(...$jodValue);
        if (!$res) {
            $res = QueueThink::{$this->action()}(...$jodValue);
            if (!$res) {
                Log::error('Thêm vào hàng đợi thất bại, tham số:' . json_encode($this->getValues($data)));
            }
        }
        $this->clean();
        return $res;
    }

    /**
     * Xóa dữ liệu
     */
    public function clean()
    {
        $this->secs = 0;
        $this->data = [];
        $this->log = null;
        $this->queueName = null;
        $this->errorCount = 3;
        $this->do = $this->defaultDo;
    }

    /**
     * Lấy phương thức task
     * @return string
     */
    protected function action()
    {
        return $this->secs ? 'later' : 'push';
    }

    /**
     * Lấy tham số
     * @param $data
     * @return array
     */
    protected function getValues($data)
    {
        $jobData['data'] = $data ?: $this->data;
        $jobData['do'] = $this->do;
        $jobData['errorCount'] = $this->errorCount;
        $jobData['log'] = $this->log;
        if ($this->do != $this->defaultDo) {
            $this->job .= '@' . Config::get('queue.prefix', 'eb_') . $this->do;
        }
        if ($this->secs) {
            return [$this->secs, $this->job, $jobData, $this->queueName];
        } else {
            return [$this->job, $jobData, $this->queueName];
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->rules)) {
            if ($name === 'data') {
                $this->{$name} = $arguments;
            } else {
                $this->{$name} = $arguments[0] ?? null;
            }
            return $this;
        } else {
            throw new \RuntimeException('Method does not exist' . __CLASS__ . '->' . $name . '()');
        }
    }
}
