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

namespace app\adminapi\controller\v1\setting;


use app\adminapi\controller\AuthController;
use app\Request;
use app\services\system\log\SystemFileServices;
use app\services\system\SystemCrudDataService;
use app\services\system\SystemCrudListServices;
use app\services\system\SystemCrudServices;
use app\services\system\SystemMenusServices;
use app\services\system\SystemRouteServices;
use crmeb\services\CacheService;
use crmeb\services\crud\enum\FormTypeEnum;
use crmeb\services\crud\Make;
use crmeb\services\crud\Service;
use crmeb\services\FileService;
use think\facade\App;
use think\facade\Db;
use think\facade\Env;
use think\helper\Str;
use think\Response;

/**
 * Class SystemCrud
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/6
 * @package app\adminapi\controller\v1\setting
 */
class SystemCrud extends AuthController
{

    /**
     * SystemCrud constructor.
     * @param App $app
     * @param SystemCrudServices $services
     */
    public function __construct(App $app, SystemCrudServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    public function index()
    {
        return app('json')->success($this->services->getList());
    }

    /**
     * Xác thực đường dẫn
     * @param $data
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/2/19
     */
    public function crudVerifyPath($data)
    {
        if (strpos($data['controller'], 'app' . DS . 'adminapi' . DS . 'controller' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['validate'], 'app' . DS . 'adminapi' . DS . 'validate' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['service'], 'app' . DS . 'services' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['dao'], 'app' . DS . 'dao' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['model'], 'app' . DS . 'model' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['route'], 'app' . DS . 'adminapi' . DS . 'route' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['router'], 'router' . DS . 'modules' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['api'], 'api' . DS . 'crud' . DS) !== 0) return false;
        if (strpos($data['pages'], 'pages' . DS . 'crud' . DS) !== 0) return false;
        return true;
    }

    /**
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    public function save(SystemCrudDataService $service, $id = 0)
    {
        $data = $this->request->postMore([
            ['pid', 0],//ID menu cấp trên
            ['menuName', ''],//Tên menu
            ['tableName', ''],//Tên bảng
            ['modelName', ''],//Tên mô-đun
            ['tableComment', ''],//Ghi chú bảng
            ['tableField', []],//Trường của bảng
            ['tableIndex', []],//Chỉ mục
            ['filePath', []],//Vị trí tạo file
            ['isTable', 0],//Có tạo bảng không
            ['deleteField', []],//Trường của bảng đã xóa
        ]);

        if (!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u', $data['menuName'])) return app('json')->fail('Tên menu chỉ được gồm chữ tiếng Trung hoặc tiếng Anh');
        if (!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u', $data['modelName'])) return app('json')->fail('Tên mô-đun chỉ được gồm chữ tiếng Trung hoặc tiếng Anh');
        if (!preg_match('/^[a-zA-Z_]+$/u', $data['tableName'])) return app('json')->fail('Tên bảng chỉ được gồm chữ cái tiếng Anh và dấu gạch dưới');
        if (!$this->crudVerifyPath($data['filePath'])) return app('json')->fail('Vị trí tệp được tạo không đúng, vui lòng kiểm tra rồi tạo lại');


        $fromField = $searchField = $hasOneField = $columnField = $tableIndex = [];

        $dictionaryids = array_column($data['tableField'], 'dictionary_id');
        $dictionaryList = [];
        foreach ($dictionaryids as $dictionaryid) {
            $dictionaryList[$dictionaryid] = $service->selectList(['cid' => $dictionaryid], 'name as label,value')->toArray();
        }

//        if ($dictionaryids) {
//            $dictionaryList = $service->getColumn([['id', 'in', $dictionaryids]], 'value', 'id');
//            foreach ($dictionaryList as &$value) {
//                $value = is_string($value) ? json_decode($value, true) : $value;
//            }
//        } else {
//            $dictionaryList = [];
//        }

        foreach ($data['tableField'] as $item) {
            //Kiểm tra độ dài trường
            if (in_array($item['field_type'], [FormTypeEnum::DATE_TIME, 'timestamp', 'time', 'date', 'year']) && $item['limit'] > 6) {
                return app('json')->fail('Trường' . $item['field'] . 'có độ dài không được lớn hơn 6');
            }
            if ($item['field_type'] == 'enum' && !is_array($item['limit'])) {
                return app('json')->fail('Khi kiểu dữ liệu là enum, độ dài phải là kiểu mảng');
            }
            //Thu thập dữ liệu hiển thị danh sách
            if ($item['is_table'] && !in_array($item['field_type'], ['primaryKey', 'addSoftDelete'])) {
                if (isset($item['primaryKey']) && !$item['primaryKey']) {
                    $columnField[] = [
                        'field' => $item['field'],
                        'name' => $item['table_name'] ?: $item['comment'],
                        'type' => $item['from_type'],
                    ];
                }
            }
            $name = $item['table_name'] ?: $item['comment'];
            $option = $item['options'] ?? (isset($item['dictionary_id']) ? ($dictionaryList[$item['dictionary_id']] ?? []) : []);
            //Thu thập dữ liệu hiển thị form
            if ($item['from_type']) {
                if (!$name) {
                    return app('json')->fail('Tên danh sách không được để trống', [], ['field' => $item['field']]);
                }
                if (!$option && in_array($item['from_type'], [FormTypeEnum::RADIO, FormTypeEnum::SELECT])) {
                    return app('json')->fail('Khi loại biểu mẫu là radio hoặc select, trường options không được để trống');
                }
                $fromField[] = [
                    'field' => $item['field'],
                    'type' => $item['from_type'],
                    'name' => $name,
                    'required' => $item['required'],
                    'option' => $option
                ];
            }

            //Tìm kiếm
            if (!empty($item['search'])) {
                $searchField[] = [
                    'field' => $item['field'],
                    'type' => $item['from_type'],
                    'name' => $name,
                    'search' => $item['search'],
                    'options' => $option
                ];
            }

            //Liên kết
            if (!empty($item['hasOne'])) {
                $hasOneField[] = [
                    'field' => $item['field'],
                    'hasOne' => $item['hasOne'] ?? '',
                    'name' => $name,
                ];
            }

            //Chỉ mục
            if (!empty($item['index'])) {
                $tableIndex[] = $item['field'];
            }
        }
        if (!$fromField) {
            return app('json')->fail('Loại biểu mẫu phải chọn ít nhất một mục');
        }
        if (!$columnField) {
            return app('json')->fail('Tạo dữ liệu hiển thị danh sách thất bại');
        }
        $data['fromField'] = $fromField;
        $data['tableIndex'] = $tableIndex;
        $data['columnField'] = $columnField;
        $data['searchField'] = $searchField;
        $data['hasOneField'] = $hasOneField;
        if (!$data['tableName']) {
            return app('json')->fail('Tên bảng không được để trống');
        }

        $this->services->createCrud($id, $data);

        return app('json')->success('Tạo chức năng thành công');
    }

    /**
     * Lấy vị trí thư mục lưu file được tạo
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    public function getFilePath()
    {
        [$tableName] = $this->request->postMore([
            ['tableName', ''],
        ], true);

        if (!$tableName) {
            return app('json')->fail('Tên bảng không được để trống');
        }

        if (in_array($tableName, SystemCrudServices::NOT_CRUD_TABANAME)) {
            return app('json')->fail('Bảng dữ liệu hệ thống, không thể tạo');
        }

        $routeName = 'crud/' . Str::snake($tableName);

        $key = 'id';
        $tableField = [];

        $field = $this->services->getColumnNamesList($tableName);
        foreach ($field as $item) {
            if ($item['primaryKey']) {
                $key = $item['name'];
            }
            $tableField[] = [
                'field' => $item['name'],
                'field_type' => $item['type'],
                'primaryKey' => (bool)$item['primaryKey'],
                'default' => $item['default'],
                'limit' => $item['limit'],
                'comment' => $item['comment'],
                'required' => false,
                'is_table' => false,
                'table_name' => '',
                'from_type' => '',
            ];
        }

        $make = $this->services->makeFile($tableName, $routeName, false, [
            'menuName' => '',
            'key' => $key,
            'fromField' => [],
            'columnField' => [],
        ]);

        $makePath = [];
        foreach ($make as $k => $item) {
            $makePath[$k] = $item['path'];
        }

        return app('json')->success(compact('makePath', 'tableField'));
    }

    /**
     * @param $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/12
     */
    public function read($id)
    {
        if (!$id) {
            return app('json')->fail('API không tồn tại');
        }

        $info = $this->services->get($id);
        if (!$info) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }

        $routeName = 'crud/' . Str::snake($info->table_name);

        $column = $this->services->getColumnNamesList($info->table_name);
        $key = 'id';
        foreach ($column as $value) {
            if ($value['primaryKey']) {
                $key = $value['name'];
                break;
            }
        }

        $softDelete = false;

        foreach ((array)$info->field['tableField'] as $item) {
            if (isset($item['field_type']) && $item['field_type'] === 'addSoftDelete') {
                $softDelete = true;
                break;
            }
        }

        $make = $this->services->makeFile($info->table_name, $routeName, false, [
            'menuName' => $info->name,
            'modelName' => $info->model_name,
            'tableField' => $info->field['tableField'] ?? [],
            'key' => $key,
            'softDelete' => $softDelete,
            'fromField' => $info->field['fromField'] ?? [],
            'columnField' => $info->field['columnField'] ?? [],
            'searchField' => $info->field['searchField'] ?? [],
            'hasOneField' => $info->field['hasOneField'] ?? [],
        ]);

        $data = [];
        foreach ($make as $key => $item) {
            if (in_array($key, ['pages', 'router', 'api'])) {
                $path = Make::adminTemplatePath() . $item['path'];
            } else {
                $path = app()->getRootPath() . $item['path'];
            }
            $item['name'] = $item['path'];
            try {
                $item['content'] = file_get_contents($path, LOCK_EX);
                $data[$key] = $item;
            } catch (\Throwable $e) {

            }
        }

        //Điều chỉnh thứ tự sắp xếp
        $makeData = [];
        $names = [
            'controller' => 'Controller',
            'validate' => 'Validator',
            'service' => 'Tầng logic',
            'dao' => 'Thao tác cơ sở dữ liệu',
            'model' => 'Tầng model',
            'route' => 'Route backend',
            'router' => 'Route frontend',
            'api' => 'API frontend',
            'pages' => 'Trang frontend'
        ];
        foreach ($names as $name => $value) {
            if (isset($data[$name])) {
                $data[$name]['file_name'] = $value;
                $makeData[] = $data[$name];
            }
        }
        $data = $makeData;

        $info = $info->toArray();
        //Ghi lại dữ liệu trước khi sửa
        foreach ((array)$info['field']['tableField'] as $key => $item) {
            $item['default_field'] = $item['field'];
            $item['default_limit'] = $item['limit'];
            $item['default_field_type'] = $item['field_type'];
            $item['default_comment'] = $item['comment'];
            $item['default_default'] = $item['default'];
            $item['default_default_type'] = $item['default_type'] ?? '1';
            $item['default_type'] = $item['default_type'] ?? '1';
            $item['is_table'] = !!$item['is_table'];
            $item['required'] = !!$item['required'];
            $item['index'] = isset($item['index']) && !!$item['index'];
            $item['primaryKey'] = isset($item['primaryKey']) ? (int)$item['primaryKey'] : 0;
            if (!isset($item['dictionary_id'])) {
                $item['dictionary_id'] = 0;
            }
            $info['field']['tableField'][$key] = $item;
        }
        //So sánh với cơ sở dữ liệu, có trường mới thêm không
        $newColumn = [];
        $fieldAll = array_column($info['field']['tableField'], 'field');
        foreach ($column as $value) {
            if (!in_array($value['name'], $fieldAll)) {
                $newColumn[] = [
                    'field' => $value['name'],
                    'field_type' => $value['type'],
                    'primaryKey' => $value['primaryKey'] ? 1 : 0,
                    'default' => $value['default'],
                    'limit' => $value['limit'],
                    'comment' => $value['comment'],
                    'required' => '',
                    'is_table' => '',
                    'table_name' => '',
                    'from_type' => '',
                    'default_field' => $value['name'],
                    'default_limit' => $value['limit'],
                    'default_field_type' => $value['type'],
                    'default_comment' => $value['comment'],
                    'default_default' => $value['default'],
                ];
            }
        }

        if ($newColumn) {
            $info['field']['tableField'] = array_merge($newColumn, $info['field']['tableField']);
        }

        $keyInfo = $deleteInfo = $createInfo = $updateInfo = [];
        $tableField = [];
        foreach ($info['field']['tableField'] as $item) {
            if ($item['primaryKey']) {
                $keyInfo = $item;
                continue;
            }
            if ($item['field_type'] == 'timestamp' && $item['field'] === 'delete_time') {
                $deleteInfo = $item;
                continue;
            }
            if ($item['field_type'] == 'timestamp' && $item['field'] === 'create_time') {
                $createInfo = $item;
                continue;
            }
            if ($item['field_type'] == 'timestamp' && $item['field'] === 'update_time') {
                $updateInfo = $item;
                continue;
            }
            $tableField[] = $item;
        }
        if ($keyInfo) {
            array_unshift($tableField, $keyInfo);
        }
        if ($createInfo) {
            array_push($tableField, $createInfo);
        }
        if ($updateInfo) {
            array_push($tableField, $updateInfo);
        }
        if ($deleteInfo) {
            array_push($tableField, $deleteInfo);
        }
        $info['field']['tableField'] = $tableField;
        $info['field']['pid'] = (int)$info['field']['pid'];
        return app('json')->success(['file' => $data, 'crudInfo' => $info]);
    }

    /**
     * @param Request $request
     * @param SystemFileServices $service
     * @param $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/24
     */
    public function savefile(Request $request, SystemFileServices $service, $id)
    {
        $comment = $request->param('comment');
        $filepath = $request->param('filepath');
        $pwd = $request->param('pwd');

        if ($pwd == '') {
            return app('json')->fail('Vui lòng nhập mật khẩu quản lý tệp');
        }
        if (!is_string($pwd) || !hash_equals((string)config('filesystem.password'), $pwd)) {
            return app('json')->fail('Mật khẩu quản lý tệp không đúng');
        }

        if (empty($filepath) || !$id) {
            return app('json')->fail('Lỗi nền tảng: đã xảy ra sự cố, vui lòng thử lại sau');
        }
        $crudInfo = $this->services->get($id, ['make_path']);
        if (!$crudInfo) {
            return app('json')->fail('Tệp CRUD cần sửa không tồn tại');
        }

        $makeFilepath = '';
        foreach ($crudInfo->make_path as $key => $item) {
            $path = $item;
            if (in_array($key, ['pages', 'router', 'api'])) {
                $item = Make::adminTemplatePath() . $item;
            } else {
                $item = app()->getRootPath() . $item;
            }
            if ($filepath == $path) {
                $makeFilepath = $item;
                break;
            }
        }
        if (!$makeFilepath || !in_array($filepath, $crudInfo->make_path)) {
            return app('json')->fail('Bạn không có quyền sửa tệp này');
        }
        $res = $service->savefile($makeFilepath, $comment);
        if ($res) {
            return app('json')->success('Lưu thành công');
        } else {
            return app('json')->fail('Lưu thất bại');
        }
    }

    /**
     * Lấy menu dạng tree
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    public function getMenus()
    {
        return app('json')->success(app()->make(SystemMenusServices::class)
            ->getList(['auth_type' => 1, 'is_show' => 1], ['auth_type', 'pid', 'id', 'menu_name as label', 'id as value']));
    }

    /**
     * Lấy tên các bảng có thể liên kết
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/2
     */
    public function getAssociationTable()
    {
        return app('json')->success($this->services->getTableAll());
    }

    /**
     * Lấy thông tin chi tiết của bảng
     * @param string $tableName
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/2
     */
    public function getAssociationTableInfo(string $tableName)
    {
        if (!$tableName) {
            return app('json')->fail('Thiếu tên bảng');
        }
        if (in_array($tableName, SystemCrudServices::NOT_CRUD_TABANAME)) {
            return app('json')->fail('Không được phép xem chi tiết bảng hiện tại');
        }
        $tableInfo = $this->services->getColumnNamesList($tableName);

        $data = [];
        foreach ($tableInfo as $key => $item) {
            $data[] = [
                'label' => $item['comment'] ?: $key,
                'value' => $key,
                'leaf' => true
            ];
        }
        return app('json')->success($data);
    }

    /**
     * Lấy loại dữ liệu khi tạo bảng
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    public function columnType()
    {
        return app('json')->success($this->services->getTabelRule());
    }

    /**
     * @param SystemMenusServices $services
     * @param $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/11
     */
    public function delete(SystemMenusServices $services, $id)
    {
        if (!$id) {
            return app('json')->fail('API không tồn tại');
        }

        $info = $this->services->get($id);
        if (!$info) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }

        $menusServices = app()->make(SystemMenusServices::class);
        if ($info->menu_ids) {
            $menusServices->deleteMenus($info->menu_ids);
        }
        if ($info->menu_id) {
            $menusServices->deleteMenus([$info->menu_id]);
        }

        $routeServices = app()->make(SystemRouteServices::class);
        if ($info->route_ids) {
            $routeServices->deleteRoutes($info->route_ids);
        }

        Db::query("DROP TABLE `" . Env::get('database.prefix', 'eb_') . $info->table_name . "`");

        if ($info->make_path) {
            $errorFile = [];
            foreach ($info->make_path as $key => $item) {
                if (in_array($key, ['pages', 'router', 'api'])) {
                    $item = Make::adminTemplatePath() . $item;
                } else {
                    $item = app()->getRootPath() . $item;
                }
                try {
                    unlink($item);
                } catch (\Throwable $e) {
                    $errorFile[] = $item;
                }
            }
            if ($errorFile) {
                return app('json')->success('Xóa tệp thất bại, lý do: {:message}', [], [
                    'message' => 'Tệp:' . implode("\n", $errorFile) . '; không thể xóa!'
                ]);
            }
        }

        $info->delete();


        return app('json')->success('Xóa thành công');
    }

    /**
     * Tải xuống tệp
     * @param $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/15
     */
    public function download($id)
    {
        if (!$id) {
            return app('json')->fail('API không tồn tại');
        }

        $info = $this->services->get($id);
        if (!$info) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }
        $zipPath = app()->getRootPath() . 'backup' . DS . Str::camel($info->table_name);
        $zipName = app()->getRootPath() . 'backup' . DS . Str::camel($info->table_name) . '.zip';
        if (is_file($zipName)) {
            unlink($zipName);
        }
        $makePath = $info->make_path ?? [];

        foreach ($makePath as $key => $item) {
            if (in_array($key, ['pages', 'router', 'api'])) {
                $item = $zipPath . str_replace(dirname(app()->getRootPath()), '', Make::adminTemplatePath()) . $item;
            } else {
                $item = $zipPath . DS . 'crmeb' . DS . $item;
            }
            $makePath[$key] = $item;
        }

        $routeName = 'crud/' . Str::snake($info->table_name);

        $column = $this->services->getColumnNamesList($info->table_name);
        $key = 'id';
        foreach ($column as $value) {
            if ($value['primaryKey']) {
                $key = $value['name'];
                break;
            }
        }

        $softDelete = false;

        foreach ((array)$info->field['tableField'] as $item) {
            if (isset($item['field_type']) && $item['field_type'] === 'addSoftDelete') {
                $softDelete = true;
                break;
            }
        }

        $this->services->makeFile($info->table_name, $routeName, true, [
            'menuName' => $info->name,
            'tableFields' => $info->field['tableField'] ?? [],
            'key' => $key,
            'softDelete' => $softDelete,
            'fromField' => $info->field['fromField'] ?? [],
            'columnField' => $info->field['columnField'] ?? [],
            'searchField' => $info->field['searchField'] ?? [],
            'hasOneField' => $info->field['hasOneField'] ?? [],
        ], $makePath, $zipPath);

        if (!extension_loaded('zip')) {
            return app('json')->fail('Chưa cài đặt extension zip');
        }

        $fileService = new FileService();
        $fileService->addZip($zipPath, $zipName, app()->getRootPath() . 'backup');

        $key = md5($zipName);
        CacheService::set($key, [
            'path' => $zipName,
            'fileName' => Str::camel($info->table_name) . '.zip',
        ], 300);
        return app('json')->success(['download_url' => sys_config('site_url') . '/adminapi/download/' . $key]);
    }

    /**
     * Lấy route quyền
     * @param $tableName
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/20
     */
    public function getRouteList($tableName)
    {
        $info = $this->services->get(['table_name' => $tableName]);
        if (!$info) {
            return app('json')->fail('Tra cứu chi tiết crud thất bại');
        }

        $routeList = app()->make(SystemMenusServices::class)->getColumn([
            ['id', 'in', $info->menu_ids],
            ['auth_type', '=', 2]
        ], 'methods,api_url');

        $newRoute = [];
        foreach ($routeList as $item) {
            if ($item['methods'] == 'GET') {
                if (strstr($item['api_url'], 'create')) {
                    $newRoute['create'] = $item['api_url'];
                } else if (strstr($item['api_url'], 'edit')) {
                    $newRoute['edit'] = $item['api_url'];
                } else if (strstr($item['api_url'], 'status')) {
                    $newRoute['status'] = $item['api_url'];
                } else {
                    if (strstr($item['api_url'], '<id>')) {
                        $newRoute['read'] = $item['api_url'];
                    } else {
                        $newRoute['index'] = $item['api_url'];
                    }
                }
            } else if ($item['methods'] == 'DELETE') {
                $newRoute['delete'] = $item['api_url'];
            } else if ($item['methods'] == 'PUT' && strstr($item['api_url'], 'status')) {
                $newRoute['status'] = $item['api_url'];
            }
        }

        $column = $this->services->getColumnNamesList($info->table_name);
        $key = 'id';
        foreach ($column as $value) {
            if ($value['primaryKey']) {
                $key = $value['name'];
                break;
            }
        }

        $columns = [
            [
                'title' => 'ID',
                'key' => $key,
                'from_type' => '',
            ]
        ];

        $readFields = [
            'name' => $info->field['modelName'] ?: $info->field['menuName'],
            'all' => [],
        ];
        foreach ((array)$info->field['tableField'] as $item) {
            if (isset($item['primaryKey']) && $item['primaryKey']) {
                continue;
            }

            $prefix = app()->make(Service::class)->getAttrPrefix();
            $readFields['all'][] = [
                'field' => in_array($item['from_type'], [FormTypeEnum::FRAME_IMAGES,
                    FormTypeEnum::DATE_TIME_RANGE,
                    FormTypeEnum::RADIO,
                    FormTypeEnum::SELECT,
                    FormTypeEnum::CHECKBOX]) ? $item['field'] . $prefix : $item['field'],
                'comment' => $item['comment'],
                'from_type' => $item['from_type'],
            ];

            if (isset($item['is_table']) && $item['is_table']) {
                $label = '';
                if (in_array($item['from_type'], [FormTypeEnum::SWITCH, FormTypeEnum::DATE_TIME_RANGE, FormTypeEnum::FRAME_IMAGE_ONE, FormTypeEnum::FRAME_IMAGES])) {
                    $keyName = 'slot';
                    if ($item['from_type'] == FormTypeEnum::FRAME_IMAGES) {
                        $label = $prefix;
                    } else if ($item['from_type'] == FormTypeEnum::DATE_TIME_RANGE) {
                        $label = $prefix;
                    }
                } elseif (in_array($item['from_type'], [FormTypeEnum::RADIO, FormTypeEnum::SELECT, FormTypeEnum::CHECKBOX])) {
                    $label = $prefix;
                    $keyName = 'key';
                } else {
                    $keyName = 'key';
                }

                $columns[] = [
                    'title' => $item['table_name'] ?: $item['comment'],
                    $keyName => $item['field'] . $label,
                    'from_type' => $item['from_type'],
                ];
            }
        }

        $searchField = $info->field['searchField'] ?? [];

        $search = [];
        foreach ((array)$searchField as $item) {
            if (!$item['type']) {
                $item['type'] = FormTypeEnum::INPUT;
            }
            if ($item['search'] == 'BETWEEN') {
                $item['type'] = 'date-picker';
            } else {
                if (in_array($item['type'], [FormTypeEnum::CHECKBOX, FormTypeEnum::RADIO, FormTypeEnum::SELECT])) {
                    $item['type'] = FormTypeEnum::SELECT;
                } else {
                    $item['type'] = FormTypeEnum::INPUT;
                }
            }

            $search[] = [
                'field' => $item['field'],
                'type' => $item['type'],
                'name' => $item['name'],
                'option' => $item['options'] ?? [],
            ];
        }

        $route = $newRoute;
        return app('json')->success(compact('key', 'route', 'columns', 'readFields', 'search'));
    }

    /**
     * Sửa hoặc lưu dữ liệu từ điển
     * @param SystemCrudDataService $service
     * @param int $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/1
     */
    public function saveDataDictionary(SystemCrudDataService $service, $id = 0)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['value', []],
        ]);

        if (!$data['name']) {
            return app('json')->fail('Tên trường dữ liệu không được để trống');
        }
        if (!$data['value']) {
            return app('json')->fail('Nội dung trường dữ liệu không được để trống');
        }
        $data['value'] = json_encode($data['value']);
        if ($id) {
            $service->update($id, $data);
        } else {
            $service->save($data);
        }

        return app('json')->success($id ? 'Sửa thành công' : 'Thêm thành công');
    }

    /**
     * Xem từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @param $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/7
     */
    public function getDataDictionaryOne(SystemCrudDataService $service, $id)
    {
        if (!$id) {
            return app('json')->fail('Thiếu tham số');
        }
        $info = $service->get($id);
        if (!$info) {
            return app('json')->fail('Không tìm thấy dữ liệu');
        }
        return app('json')->success($info->toArray());
    }

    /**
     * Lấy danh sách từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @return Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/1
     */
    public function getDataDictionary(SystemCrudDataService $service)
    {
        $name = $this->request->get('name', '');
        $data = $service->getlistAll($name);
        return app('json')->success($data);
    }

    /**
     * Xóa từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @param $id
     * @return Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/4
     */
    public function deleteDataDictionary(SystemCrudDataService $service, $id)
    {
        if (!$id) {
            return app('json')->fail('Thiếu tham số');
        }
        if ($service->delete($id)) {
            return app('json')->success('Xóa thành công');
        } else {
            return app('json')->fail('Xóa thất bại');
        }
    }




    /** Mới - Từ điển dữ liệu */

    /**
     * Lấy danh sách từ điển dữ liệu
     * @param SystemCrudListServices $service
     * @return Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryList(SystemCrudListServices $service)
    {
        $where = $this->request->getMore([
            ['status', ''],
        ]);
        return app('json')->success($service->dataDictionaryList($where));
    }

    /**
     * Lấy biểu mẫu thêm/sửa từ điển dữ liệu
     * @param SystemCrudListServices $service
     * @param $id
     * @return Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryListCreate(SystemCrudListServices $service, $id)
    {
        return app('json')->success($service->dataDictionaryListCreate($id));
    }

    /**
     * Lưu từ điển dữ liệu
     * @param SystemCrudListServices $service
     * @param $id
     * @return Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryListSave(SystemCrudListServices $service, $id)
    {
        $data = $this->request->getMore([
            ['name', ''],
            ['mark', ''],
            ['level', ''],
            ['status', ''],
        ]);
        $service->dataDictionaryListSave($id, $data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Xóa từ điển dữ liệu
     * @param SystemCrudListServices $service
     * @param $id
     * @return Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryListDel(SystemCrudListServices $service, $id)
    {
        $service->dataDictionaryListDel($id);
        return app('json')->success('Xóa thành công');
    }

    /**
     * Danh sách nội dung từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @param $cid
     * @return Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryInfoList(SystemCrudDataService $service, $cid)
    {
        return app('json')->success($service->dataDictionaryInfoList($cid));
    }

    /**
     * Form thêm/sửa nội dung từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @param $cid
     * @param $id
     * @param $pid
     * @return Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryInfoCreate(SystemCrudDataService $service, $cid, $id, $pid)
    {
        return app('json')->success($service->dataDictionaryInfoCreate($cid, (int)$id, (int)$pid));
    }

    /**
     * Lưu nội dung từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @param $cid
     * @param $id
     * @return Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryInfoSave(SystemCrudDataService $service, $cid, $id)
    {
        $data = $this->request->getMore([
            ['name', ''],
            ['pid', 0],
            ['value', ''],
            ['sort', 0],
        ]);
        $service->dataDictionaryInfoSave($cid, $id, $data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Xóa nội dung từ điển dữ liệu
     * @param SystemCrudDataService $service
     * @param $id
     * @return Response
     * @throws \ReflectionException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryInfoDel(SystemCrudDataService $service, $id)
    {
        $service->dataDictionaryInfoDel($id);
        return app('json')->success('Xóa thành công');
    }
}
