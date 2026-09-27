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
use app\services\system\config\SystemConfigServices;
use app\services\system\config\SystemStorageServices;
use app\services\other\UploadService;
use think\facade\App;

/**
 * Class SystemStorage
 * @package app\adminapi\controller\v1\setting
 */
class SystemStorage extends AuthController
{

    /**
     * SystemStorage constructor.
     * @param App $app
     * @param SystemStorageServices $services
     */
    public function __construct(App $app, SystemStorageServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return app('json')->success($this->services->getList(['type' => $this->request->get('type')]));
    }

    /**
     * Lấy form tạo dữ liệu
     * @param $type
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create($type)
    {
        if (!$type) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($this->services->getFormStorage((int)$type));
    }

    /**
     * Lấy form cấu hình
     * @param $type
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function getConfigForm($type)
    {
        return app('json')->success($this->services->getFormStorageConfig((int)$type));
    }

    /**
     * Lấy loại cấu hình
     * @return mixed
     */
    public function getConfig()
    {
        return app('json')->success(['type' => (int)sys_config('upload_type', 1)]);
    }

    /**
     * @return mixed
     */
    public function saveConfig( )
    {
        $type = (int)$this->request->post('type', 0);

        $data = $this->request->postMore([
            ['accessKey', ''],
            ['secretKey', ''],
            ['appid', ''],
            ['storageRegion', ''],
        ]);

        $this->services->saveConfig((int)$type, $data);

        return app('json')->success('Lưu thành công');
    }

    /**
     * @param $type
     * @return mixed
     */
    public function synch($type)
    {
        $this->services->synchronization((int)$type);
        return app('json')->success('Đồng bộ thành công');
    }

    /**
     * Lưu loại
     * @param $type
     * @return mixed
     */
    public function save($type)
    {
        $data = $this->request->postMore([
            ['accessKey', ''],
            ['secretKey', ''],
            ['appid', ''],
            ['name', ''],
            ['region', ''],
            ['acl', ''],
        ]);
        $type = (int)$type;
        if ($type === 4) {
            if (!$data['appid'] && !sys_config('tengxun_appid')) {
                return app('json')->fail('Thiếu APPID');
            }
        }
        if (!$data['accessKey']) {
            unset($data['accessKey'], $data['secretKey'], $data['appid']);
        }
        $this->services->saveStorage((int)$type, $data);

        return app('json')->success('Thêm thành công');
    }

    /**
     * Sửa trạng thái
     * @param SystemConfigServices $services
     * @param $id
     * @return mixed
     */
    public function status(SystemConfigServices $services, $id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }

        $info = $this->services->get($id);
        $info->status = 1;
        if (!$info->domain) {
            return app('json')->fail('Vui lòng cài đặt tên miền không gian lưu trữ trước');
        }
//        $services->update('upload_type', ['value' => json_encode($info->type)], 'menu_name');
        \crmeb\services\CacheService::clear();

        //Thiết lập quy tắc CORS
        try {
            $upload = UploadService::init($info->type);
            $res = $upload->setBucketCors($info->name, $info->region);
            if (false === $res) {
                return app('json')->fail($upload->getError());
            }
        } catch (\Throwable $e) {
        }

        //Sửa trạng thái
        $this->services->transaction(function () use ($id, $info) {
//            $this->services->update(['status' => 1, 'is_delete' => 0], ['status' => 0]);
            $this->services->update(['type' => $info->type], ['status' => 0]);
            $info->save();
        });
        return app('json')->success('Sửa thành công');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function getUpdateDomainForm($id)
    {
        return app('json')->success($this->services->getUpdateDomainForm((int)$id));
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateDomain($id)
    {
        $domain = $this->request->post('domain', '');
        $cdn = $this->request->post('cdn', '');
        $data = $this->request->postMore([
            ['pri', ''],
            ['ca', '']
        ]);
        if (!$domain) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        if (strstr($domain, 'https://') === false && strstr($domain, 'http://') === false) {
            return app('json')->fail('Sai định dạng, vui lòng nhập theo định dạng: http://tên miền');

        }
//        if (strstr($domain, 'https://') !== false && !$data['pri']) {
//            return app('json')->fail('Khi tên miền truy cập bằng HTTPS, phải điền chứng chỉ');
//        }

        $this->services->updateDomain($id, $domain, ['cdn' => $cdn]);

        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }

        if ($this->services->deleteStorage($id)) {
            return app('json')->success('Xóa thành công');
        } else {
            return app('json')->fail('Xóa thất bại');
        }
    }

    /**
     * Chuyển đổi loại lưu trữ
     * @param SystemConfigServices $services
     * @param $type
     * @return mixed
     */
    public function uploadType(SystemConfigServices $services, $type)
    {
        $status = $this->services->count(['type' => $type, 'status' => 1]);
        if (!$status && $type != 1) {
            return app('json')->success('Không có không gian lưu trữ nào đang được sử dụng');
        }
        $services->update('upload_type', ['value' => json_encode($type)], 'menu_name');
        \crmeb\services\CacheService::clear();
        if ($type != 1) {
            $msg = 'Chuyển sang lưu trữ đám mây thành công, vui lòng kiểm tra đã bật sử dụng không gian lưu trữ chưa';
        } else {
            $msg = 'Chuyển sang lưu trữ cục bộ thành công';
        }
        return app('json')->success($msg);
    }
}
