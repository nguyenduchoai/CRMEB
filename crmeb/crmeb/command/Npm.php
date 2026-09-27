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


use crmeb\services\FileService;
use crmeb\utils\Terminal;
use think\console\Command;
use think\console\input\Argument;
use think\console\input\Option;

/**
 * Class Npm
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/13
 * @package crmeb\command
 */
class Npm extends Command
{
    /**
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    protected function configure()
    {
        $this->setName('npm')
            ->addOption('path', 'dp', Option::VALUE_OPTIONAL, 'Đường dẫn mặc định')
            ->addOption('build', 'bu', Option::VALUE_OPTIONAL, 'Đường dẫn lưu bản đóng gói')
            ->setDescription('Công cụ đóng gói NPM');
    }

    /**
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/13
     */
    public function handle()
    {
        $path = $this->input->getOption('path');
        $build = $this->input->getOption('build');
        if (!$build) {
            $build = public_path() . 'admin';
        }

        $terminal = new Terminal();
        $terminal->setOutput($this->output);

        $adminPath = $path ?: $terminal->adminTemplatePath();

        $adminPath = dirname($adminPath);

        if (is_dir($adminPath . DS . 'dist')) {
            $question = $this->output->confirm($this->input, 'Phát hiện tệp đóng gói đã được tạo, có đóng gói lại không?', false);
            if (!$question) {
                $this->output->info('Đã thoát chương trình đóng gói');
                return;
            }
        }

        $dir = $adminPath . DS . 'node_modules';
        if (!is_dir($dir)) {
            $terminal->run('npm-install');
        }


        $terminal->run('npm-build');

        if (!is_dir($adminPath . DS . 'dist')) {
            $this->output->error('Đóng gói thất bại');
            return;
        }

        $this->app->make(FileService::class)->copyDir($adminPath . DS . 'dist', $build);

        $this->output->info('Thực thi thành công');
    }
}
