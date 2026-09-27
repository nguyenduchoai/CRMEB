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
namespace app\adminapi\controller\v1\merchant;

use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\system\store\SystemStoreServices;

/**
 * Controller quản lý cửa hàng
 * Class SystemAttachment
 * @package app\admin\controller\system
 *
 */
class SystemStore extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemStore constructor.
     * @param App $app
     * @param SystemStoreServices $services
     */
    public function __construct(App $app, SystemStoreServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách cửa hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['type', 'd'], 0]
        ]);
        return app('json')->success($this->services->getStoreList($where));
    }

    /**
     * Lấy phần đầu trang cửa hàng
     * @return mixed
     */
    public function get_header()
    {
        $count = $this->services->getStoreData();
        return app('json')->success(compact('count'));
    }

    /**
     * Cài đặt cửa hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_info()
    {
        [$id] = $this->request->getMore([
            [['id', 'd'], 0],
        ], true);
        $info = $this->services->getStoreDispose($id);
        return app('json')->success(compact('info'));
    }

    /**
     * Chọn vị trí
     * @return mixed
     */
    public function select_address()
    {
        $key = sys_config('tengxun_map_key');
        if (!$key) return app('json')->fail(400124);
        return app('json')->success(compact('key'));
    }

    /**
     * Thiết lập hiện/ẩn cho một cửa hàng
     * @param string $is_show
     * @param string $id
     * @return mixed
     */
    public function set_show($is_show = '', $id = '')
    {
        ($is_show == '' || $id == '') && app('json')->fail(100100);
        $res = $this->services->update((int)$id, ['is_show' => (int)$is_show]);
        if ($res) {
            return app('json')->success(100014);
        } else {
            return app('json')->fail(100015);
        }
    }

    /**
     * Lưu chỉnh sửa thông tin cửa hàng
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['introduction', ''],
            ['image', ''],
            ['oblong_image', ''],
            ['phone', ''],
            ['address', ''],
            ['detailed_address', ''],
            ['latlng', ''],
            ['day_time', []],
        ]);
        $this->validate($data, \app\adminapi\validate\merchant\SystemStoreValidate::class, 'save');

        $data['address'] = implode(',', $data['address']);
        $data['latlng'] = explode(',', $data['latlng']);
        if (!isset($data['latlng'][0]) || !isset($data['latlng'][1])) {
            return app('json')->fail(400125);
        }
        $data['latitude'] = $data['latlng'][0];
        $data['longitude'] = $data['latlng'][1];
        $data['day_time'] = implode(' - ', $data['day_time']);
        unset($data['latlng']);
        if ($data['image'] && strstr($data['image'], 'http') === false) {
            $site_url = sys_config('site_url');
            $data['image'] = $site_url . $data['image'];
        }
        $this->services->saveStore((int)$id, $data);
        return app('json')->success(100014);
    }

    /**
     * Xóa/khôi phục cửa hàng
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        $storeInfo = $this->services->get($id);
        if (!$storeInfo) {
            return app('json')->fail(100026);
        }
        if ($storeInfo->is_del == 1) {
            $storeInfo->is_del = 0;
            if (!$storeInfo->save())
                return app('json')->fail(100041);
            else
                return app('json')->success(100040);
        } else {
            $storeInfo->is_del = 1;
            if (!$storeInfo->save())
                return app('json')->fail(100008);
            else
                return app('json')->success(100002);
        }
    }
}
