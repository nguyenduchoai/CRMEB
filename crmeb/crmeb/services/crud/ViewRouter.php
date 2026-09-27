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

namespace crmeb\services\crud;

use crmeb\exceptions\CrudException;
use think\App;
use think\helper\Str;

/**
 * Class ViewRouter
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/1
 * @package crmeb\services\crud
 */
class ViewRouter extends Make
{
    /**
     * @var string
     */
    protected $name = 'router';

    /**
     * @var string
     */
    protected $fileMime = 'js';

    /**
     * ViewRouter constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->basePath = $this->adminTemplatePath;
    }

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/4
     */
    protected function setBaseDir(): string
    {
        return 'router' . DS . 'modules' . DS . 'crud';
    }

    /**
     * @param string $name
     * @param string $path
     * @param array $options
     * @return ViewRouter
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/4
     */
    public function handle(string $name, array $options = [])
    {
        $path = $options['path'] ?? '';
        [$nameData, $content] = $this->getStubContent($name);

        $menus = $options['menuName'] ?? $name;
        $route = $options['route'] ?? Str::snake($name);
        $pagePath = $options['pagePath'] ?? Str::camel($name);
        if (!$route) {
            throw new CrudException('Loại route resource không tồn tại');
        }

        $this->value['MENUS'] = $menus;
        $this->value['NAME'] = $nameData;
        $this->value['ROUTE'] = $route;
        $this->value['PAGE_PATH'] = $pagePath;
        if (isset($this->value['PATH'])) {
            $this->value['PATH'] = $this->getfolderPath($path);
        }

        $contentStr = str_replace($this->var, $this->value, $content);

        $filePath = $this->getFilePathName($path, Str::camel($name));

        $this->setContent($contentStr);
        $this->setPathname($filePath);
        return $this;
    }

    /**
     * @param string $path
     * @param string $name
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/3
     */
    protected function getFilePathName(string $path, string $name): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        return $this->getBasePath($path) . $name . '.' . $this->fileMime;
    }

    /**
     * @param string $type
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/1
     */
    protected function getStub(string $type = '')
    {
        return __DIR__ . DS . 'stubs' . DS . 'view' . DS . 'router' . DS . 'modules' . DS . 'crud.stub';
    }
}
