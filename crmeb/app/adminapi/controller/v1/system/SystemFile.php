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
namespace app\adminapi\controller\v1\system;

use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\system\log\SystemFileServices;

/**
 * Controller kiểm tra file
 * Class SystemFile
 * @package app\admin\controller\system
 *
 */
class SystemFile extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemFile constructor.
     * @param App $app
     * @param SystemFileServices $services
     */
    public function __construct(App $app, SystemFileServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lịch sử kiểm tra file
     * @return mixed
     */
    public function index()
    {
        return app('json')->success(['list' => $this->services->getFileList()]);
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     *
     * @date 2022/09/07
     * @author yyw
     */
    public function login()
    {
        [$password] = $this->request->postMore([
            'password',
        ], true);

        $adminInfo = $this->request->adminInfo();
        if (!$adminInfo) return app('json')->fail(100101);
        if ($adminInfo['level'] != 0) return app('json')->fail(100101);
        if ($password === '') return app('json')->fail(400256);

        return app('json')->success($this->services->login($password, 'file_edit'));
    }

    //Mở thư mục
    public function opendir()
    {
        [$dir, $fileDir, $superior] = $this->request->getMore([
            ['dir', ''],
            ['filedir', ''],
            ['superior', ''],
        ], true);
        return app('json')->success($this->services->opendir($dir, $fileDir, $superior));
    }

    //Ghi chú tệp
    public function fileMark()
    {
        [$path, $fileToken] = $this->request->postMore([
            ['path', ''],
            ['fileToken', ''],
        ], true);
        if ($path == '') return app('json')->fail(100100);
        return app('json')->success($this->services->markForm($path, $fileToken));
    }

    //Lưu ghi chú file
    public function fileMarkSave()
    {
        [$full_path, $mark] = $this->request->postMore([
            ['full_path', ''],
            ['mark', ''],
        ], true);
        $full_path = $this->request->param('full_path');
        if ($full_path == '') return app('json')->fail(100100);
        $this->services->fileMarkSave($full_path, $mark);
        return app('json')->success(100000);
    }

    //Đọc tệp
    public function openfile()
    {
        $file = $this->request->param('filepath');
        if (empty($file)) return app('json')->fail(410087);
        return app('json')->success($this->services->openfile($file));
    }

    //Lưu tệp
    public function savefile()
    {
        $comment = $this->request->param('comment');
        $filepath = $this->request->param('filepath');
        if (empty($filepath)) {
            return app('json')->fail('Đường dẫn tệp không tồn tại');
        }
        $res = $this->services->savefile($filepath, $comment);
        if ($res) {
            return app('json')->success(100000);
        } else {
            return app('json')->fail(100006);
        }
    }

    /**
     * Tạo thư mục
     * @return mixed
     *
     * @date 2022/09/17
     * @author yyw
     */
    public function createFolder()
    {
        [$path, $name] = $this->request->postMore([
            ['path', ''],
            ['name', '']
        ], true);
        if (empty($path) || empty($name)) {
            return app('json')->fail(410087);
        }
        $data = [];
        try {
            $res = $this->services->createFolder($path, $name);
            if ($res) {
                $data = [
                    'children' => [],
                    'contextmenu' => true,
                    'isDir' => true,
                    'loading' => false,
                    'path' => $path,
                    'pathname' => $path . DS . $name,
                    'title' => $name,
                ];
            } else {
                return app('json')->fail(100005);
            }
        } catch (\Exception $e) {
            return app('json')->fail($e->getMessage());
        }
        return app('json')->success($data);
    }

    /**
     * Tạo tệp
     * @return mixed
     *
     * @date 2022/09/17
     * @author yyw
     */
    public function createFile()
    {
        [$path, $name] = $this->request->postMore([
            ['path', ''],
            ['name', '']
        ], true);
        if (empty($path) || empty($name)) {
            return app('json')->fail(410087);
        }
        $data = [];
        try {
            $res = $this->services->createFile($path, $name);
            if ($res) {
                $data = [
                    'children' => [],
                    'contextmenu' => true,
                    'isDir' => false,
                    'loading' => false,
                    'path' => $path,
                    'pathname' => $path . DS . $name,
                    'title' => $name,
                ];
            } else {
                return app('json')->fail(100005);
            }
        } catch (\Exception $e) {
            return app('json')->fail($e->getMessage());
        }
        return app('json')->success($data);
    }

    /**
     * Xóa file hoặc thư mục
     * @return mixed
     *
     * @date 2022/09/17
     * @author yyw
     */
    public function delFolder()
    {
        [$path] = $this->request->postMore([
            ['path', '']
        ], true);
        if (empty($path)) {
            return app('json')->fail(410087);
        }
        try {
            $this->services->delFolder($path);
        } catch (\Exception $e) {
            return app('json')->fail($e->getMessage());
        }
        return app('json')->success(100010);
    }

    /**
     * Đổi tên file
     * @return mixed
     *
     * @date 2022/09/28
     * @author yyw
     */
    public function rename()
    {
        [$newname, $oldname] = $this->request->postMore([
            ['newname', ''],
            ['oldname', '']
        ], true);
        if (empty($newname) || empty($oldname)) {
            return app('json')->fail(410087);
        }
        try {
            $this->services->rename($newname, $oldname);
        } catch (\Exception $e) {
            return app('json')->fail($e->getMessage());
        }
        return app('json')->success(100010);

    }


    public function copyFolder()
    {
        [$surDir, $toDir] = $this->request->postMore([
            ['surDir', ''],
            ['toDir', '']
        ], true);
        if (empty($surDir) || empty($toDir)) {
            return app('json')->fail(410087);
        }
        try {
            return app('json')->success($this->services->copyFolder($surDir, $toDir));
        } catch (\Exception $e) {
            return app('json')->fail($e->getMessage());
        }
    }
}
