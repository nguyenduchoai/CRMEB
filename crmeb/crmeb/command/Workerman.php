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
namespace crmeb\command;

use app\services\system\config\SystemConfigServices;
use Channel\Server;
use crmeb\services\workerman\chat\ChatService;
use crmeb\services\workerman\WorkermanService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class Workerman extends Command
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Worker
     */
    protected $workerServer;

    /**
     * @var Worker
     */
    protected $chatWorkerServer;

    /**
     * @var Server
     */
    protected $channelServer;

    /**
     * @var Input
     */
    public $input;

    /**
     * @var Output
     */
    public $output;

    protected function configure()
    {
        // Cấu hình lệnh (command)
        $this->setName('workerman')
            ->addArgument('status', Argument::REQUIRED, 'start/stop/reload/status/connections')
            ->addArgument('server', Argument::OPTIONAL, 'admin/chat/channel')
            ->addOption('d', null, Option::VALUE_NONE, 'Khởi động ở chế độ daemon (tiến trình nền)')
            ->setDescription('start/stop/restart workerman');
    }

    protected function init(Input $input, Output $output)
    {
        global $argv;
        $argv[1] = $input->getArgument('status') ?: 'start';
        $server = $input->getArgument('server');
        if ($input->hasOption('d')) {
            $argv[2] = '-d';
        } else {
            unset($argv[2]);
        }

        $this->config = config('workerman');

        return $server;
    }

    protected function execute(Input $input, Output $output)
    {
        $server = $this->init($input, $output);
        /** @var SystemConfigServices $services */
        $services = app()->make(SystemConfigServices::class);
        $sslConfig = $services->getSslFilePath();
//        $confing['wss_open'] = $sslConfig['wssOpen'] ?? 0;
        $confing['wss_open'] = 0;
        $confing['wss_local_cert'] = $sslConfig['wssLocalCert'] ?? '';
        $confing['wss_local_pk'] = $sslConfig['wssLocalpk'] ?? '';
        // Chứng chỉ tốt nhất nên là chứng chỉ đã đăng ký (xin cấp)
        if ($confing['wss_open']) {
            $context = [
                'ssl' => [
                    // Vui lòng dùng đường dẫn tuyệt đối
                    'local_cert' => realpath('public' . $confing['wss_local_cert']), // Cũng có thể là file crt
                    'local_pk' => realpath('public' . $confing['wss_local_pk']),
                    'verify_peer' => false,
                ]
            ];
        } else {
            $context = [];
        }
        Worker::$pidFile = app()->getRootPath() . 'runtime/workerman.pid';
        Worker::$logFile = app()->getRootPath() . 'runtime/workerman.log';
        if (!$server || $server == 'admin') {
            var_dump('admin');
            //Tạo service kết nối lâu dài cho admin
            $this->workerServer = new Worker($this->config['admin']['protocol'] . '://' . $this->config['admin']['ip'] . ':' . $this->config['admin']['port'], $context);
            $this->workerServer->count = $this->config['admin']['serverCount'];
            if ($confing['wss_open']) {
                $this->workerServer->transport = 'ssl';
            }
        }

        if (!$server || $server == 'chat') {
            var_dump('chat');
            //Tạo service kết nối lâu dài cho h5 chat
            $this->chatWorkerServer = new Worker($this->config['chat']['protocol'] . '://' . $this->config['chat']['ip'] . ':' . $this->config['chat']['port'], $context);
            $this->chatWorkerServer->count = $this->config['chat']['serverCount'];
            if ($confing['wss_open']) {
                $this->chatWorkerServer->transport = 'ssl';
            }
        }

        if (!$server || $server == 'channel') {
            var_dump('channel');
            //Tạo service giao tiếp nội bộ
            $this->channelServer = new Server($this->config['channel']['ip'], $this->config['channel']['port']);
        }
        $this->bindHandle();
        try {
            Worker::runAll();
        } catch (\Exception $e) {
            $output->warning($e->getMessage());
        }
    }

    /**
     * Gắn các callback sự kiện của Workerman
     *
     * Phương thức này chịu trách nhiệm gắn “dịch vụ kết nối liên tục của trang quản trị” và “dịch vụ kết nối liên tục của phòng chat” lần lượt với các lớp xử lý nghiệp vụ tương ứng,
     * để khi client kết nối, gửi tin nhắn, tiến trình khởi động hoặc ngắt kết nối thì có thể tự động gọi logic nghiệp vụ tương ứng.
     *
     * 1. Nếu đã tạo dịch vụ admin ($this->workerServer khác null):
     *    - Khởi tạo WorkermanService, truyền vào instance worker hiện tại và instance dịch vụ channel
     *    - Gắn bốn sự kiện onConnect / onMessage / onWorkerStart / onClose vào các phương thức cùng tên của WorkermanService
     *
     * 2. Nếu đã tạo dịch vụ chat ($this->chatWorkerServer khác null):
     *    - Khởi tạo ChatService, truyền vào instance chat worker hiện tại và instance dịch vụ channel
     *    - Tương tự, gắn bốn sự kiện trên vào các phương thức cùng tên của ChatService
     *
     * Bằng cách này, mã nghiệp vụ được tách rời khỏi lõi Workerman, thuận tiện cho việc bảo trì và mở rộng về sau.
     */
    protected function bindHandle()
    {
        // Gắn sự kiện cho dịch vụ admin
        // Chỉ gắn khi instance dịch vụ kết nối liên tục admin đã được tạo ($this->workerServer khác null)
        if (!is_null($this->workerServer)) {
            // Khởi tạo WorkermanService, truyền vào instance admin worker hiện tại và instance dịch vụ channel
            // WorkermanService chịu trách nhiệm xử lý logic nghiệp vụ liên quan đến trang quản trị
            $server = new WorkermanService($this->workerServer, $this->channelServer);
            
            // Gắn bốn sự kiện cốt lõi của Workerman vào các phương thức cùng tên của WorkermanService
            // Kích hoạt khi client kết nối thành công
            $this->workerServer->onConnect = [$server, 'onConnect'];
            // Kích hoạt khi nhận được tin nhắn từ client
            $this->workerServer->onMessage = [$server, 'onMessage'];
            // Kích hoạt khi tiến trình worker khởi động (chỉ một lần trong vòng đời của mỗi tiến trình)
            $this->workerServer->onWorkerStart = [$server, 'onWorkerStart'];
            // Kích hoạt khi client ngắt kết nối
            $this->workerServer->onClose = [$server, 'onClose'];
        }

        // Gắn sự kiện cho dịch vụ chat
        // Chỉ gắn khi instance dịch vụ kết nối liên tục chat đã được tạo ($this->chatWorkerServer khác null)
        if (!is_null($this->chatWorkerServer)) {
            // Khởi tạo ChatService, truyền vào instance chat worker hiện tại và instance dịch vụ channel
            // ChatService chịu trách nhiệm xử lý logic nghiệp vụ liên quan đến phòng chat
            $chatServer = new ChatService($this->chatWorkerServer, $this->channelServer);
            
            // Gắn bốn sự kiện cốt lõi của Workerman vào các phương thức cùng tên của ChatService
            // Kích hoạt khi client kết nối thành công
            $this->chatWorkerServer->onConnect = [$chatServer, 'onConnect'];
            // Kích hoạt khi nhận được tin nhắn từ client
            $this->chatWorkerServer->onMessage = [$chatServer, 'onMessage'];
            // Kích hoạt khi tiến trình worker khởi động (chỉ một lần trong vòng đời của mỗi tiến trình)
            $this->chatWorkerServer->onWorkerStart = [$chatServer, 'onWorkerStart'];
            // Kích hoạt khi client ngắt kết nối
            $this->chatWorkerServer->onClose = [$chatServer, 'onClose'];
        }
    }
}
