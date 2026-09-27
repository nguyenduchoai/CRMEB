<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace crmeb\utils;

use think\Response;
use think\console\Output;

/**
 * Thực thi lệnh (command)
 * Class Terminal
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/13
 * @package crmeb\utils
 */
class Terminal
{
    /**
     * Lệnh (command)
     * @var \string[][]
     */
    protected $command = [
        'npm-build' => [
            'run_root' => '',
            'command' => 'npm run build',
        ],
        'npm-install' => [
            'run_root' => '',
            'command' => 'npm run install',
        ],
    ];

    /**
     * Địa chỉ lưu nội dung thực thi
     * @var string
     */
    protected $outputFile;

    /**
     * Trạng thái thực thi
     * @var integer
     */
    protected $procStatus;

    /**
     * Nội dung phản hồi
     * @var string
     */
    protected $outputContent;

    /**
     * @var
     */
    protected $output;

    /**
     * Terminal constructor.
     */
    public function __construct()
    {
        $this->command['npm-build']['run_root'] = str_replace('DS', DS, config('app.admin_template_path'));
        $this->command['npm-install']['run_root'] = str_replace('DS', DS, config('app.admin_template_path'));

        $outputDir = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'terminal';
        $this->outputFile = $outputDir . DIRECTORY_SEPARATOR . 'exec.log';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        file_put_contents($this->outputFile, '');
    }

    /**
     * @param Output $output
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    public function setOutput(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    public function adminTemplatePath()
    {
        return $this->command['npm-install']['run_root'];
    }

    /**
     * Thực thi
     * @param string $name
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    public function run(string $name)
    {
        if (!function_exists('proc_open')) {
            throw new \RuntimeException('Thiếu hàm proc_open, không thể chạy');
        }

        if (!isset($this->command[$name])) {
            throw new \RuntimeException('Lệnh cần chạy không tồn tại');
        }

        $command = $this->command[$name];

        $descriptorspec = [0 => ['pipe', 'r'], 1 => ['file', $this->outputFile, 'w'], 2 => ['file', $this->outputFile, 'w']];
        $process = proc_open($command['command'], $descriptorspec, $pipes, $command['run_root'], null, ['suppress_errors' => true]);
        if (is_resource($process)) {
            while ($this->getProcStatus($process)) {
                $contents = file_get_contents($this->outputFile);
                if (strlen($contents) && $this->outputContent != $contents) {
                    $newOutput = str_replace($this->outputContent, '', $contents);
                    if (preg_match('/\r\n|\r|\n/', $newOutput)) {
                        $this->echoOutputFlag($newOutput);
                        $this->outputContent = $contents;
                    }
                }
                usleep(500000);
            }
            foreach ($pipes as $pipe) {
                fclose($pipe);
            }
            proc_close($process);
        }

        return $this->output('run success');
    }

    /**
     * Kiểm tra trạng thái
     * @param $process
     * @return bool
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    public function getProcStatus($process): bool
    {
        $status = proc_get_status($process);
        if ($status['running']) {
            $this->procStatus = 1;
            return true;
        } elseif ($this->procStatus === 1) {
            $this->procStatus = 0;
            $this->output('exit: ' . $status['exitcode']);
            if ($status['exitcode'] === 0) {
                $this->echoOutputFlag('success');
            } else {
                $this->echoOutputFlag('error');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Nhập phản hồi trực tiếp
     * @param string $message
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    public function echoOutputFlag(string $message)
    {
        if ($this->output && $this->output instanceof Output) {
            $this->output->info($message);
        } else {
            echo $this->output($message);
            @ob_flush();
        }
    }

    /**
     * Trả về nội dung phản hồi
     * @param string $data
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    private function output($data)
    {
        $data = [
            'message' => $data,
        ];
        return Response::create($data, 'json')->getContent();
    }
}
