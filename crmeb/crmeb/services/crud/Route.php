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
use crmeb\services\crud\enum\ActionEnum;
use think\helper\Str;

class Route extends Make
{
    /**
     * @var string
     */
    protected $name = 'route';

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/4
     */
    protected function setBaseDir(): string
    {
        return 'app' . DS . 'adminapi' . DS . 'route' . DS . 'crud';
    }

    /**
     * @param string $name
     * @param array $options
     * @return Route
     */
    public function handle(string $name, array $options = [])
    {
        $path = $options['path'] ?? '';
        $route = $options['route'] ?? '';
        $controller = $options['controller'] ?? $name;
        $routePath = $options['routePath'] ?? '';
        $menus = $options['menus'] ?? '';
        if (!$route) {
            throw new CrudException('Loại route resource không tồn tại');
        }

        return $this->setRouteContent($route, $routePath, $controller, $menus)
            ->setRoute($name, $path);
    }

    /**
     * Đặt nội dung mẫu route
     * @param string $name
     * @param string $path
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function setRoute(string $name, string $path)
    {
        $content = file_get_contents($this->getStub());

        $contentStr = str_replace($this->var, $this->value, $content);

        $filePath = $this->getFilePathName($path, strtolower($name));

        $this->setPathname($filePath);
        $this->setContent($contentStr);

        return $this;
    }

    /**
     * Đặt nội dung trang route
     * @param string $route
     * @param string $routePath
     * @param string $controller
     * @param string $menus
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function setRouteContent(string $route, string $routePath, string $controller, string $menus)
    {
        $var = [
            '{%ROUTE%}',
            '{%CONTROLLER%}',
            '{%ROUTE_PATH%}',
            '{%MENUS%}',
        ];

        $value = [
            $route,
            $routePath,
            $controller ? ($routePath ? '.' : '') . Str::studly($controller) : '',
            $menus
        ];

        $routeContent = "";
        foreach (ActionEnum::ACTION_ALL as $item) {
            $routeContent .= file_get_contents($this->getStub($item)) . "\r\n";
        }

        $this->value['CONTENT_PHP'] = str_replace($var, $value, $routeContent);

        return $this;
    }

    /**
     * @param string $path
     * @param string $name
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    protected function getFilePathName(string $path, string $name): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        return $this->getBasePath($path) . $name . '.' . $this->fileMime;
    }

    /**
     * Đặt mẫu
     * @param string $type
     * @return string|string[]
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/3/14
     */
    protected function getStub(string $type = 'route')
    {
        $routePath = __DIR__ . DS . 'stubs' . DS . 'route' . DS;

        $stubs = [
            'index' => $routePath . 'index.stub',
            'create' => $routePath . 'create.stub',
            'save' => $routePath . 'save.stub',
            'edit' => $routePath . 'edit.stub',
            'update' => $routePath . 'update.stub',
            'status' => $routePath . 'status.stub',
            'delete' => $routePath . 'delete.stub',
            'route' => $routePath . 'route.stub',
            'read' => $routePath . 'read.stub',
        ];

        return $type ? $stubs[$type] : $stubs;
    }
}
