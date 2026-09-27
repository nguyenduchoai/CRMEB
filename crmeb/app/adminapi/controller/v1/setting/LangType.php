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
namespace app\adminapi\controller\v1\setting;

use app\adminapi\controller\AuthController;
use app\services\system\lang\LangTypeServices;
use crmeb\services\CacheService;
use think\facade\App;

class LangType extends AuthController
{
    /**
     * @param App $app
     * @param LangTypeServices $services
     */
    public function __construct(App $app, LangTypeServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách loại ngôn ngữ
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function langTypeList()
    {
        $where['is_del'] = 0;
        return app('json')->success($this->services->langTypeList($where));
    }

    /**
     * Form thêm loại ngôn ngữ
     * @param int $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function langTypeForm(int $id = 0)
    {
        return app('json')->success($this->services->langTypeForm($id));
    }

    /**
     * Lưu loại ngôn ngữ
     * @return mixed
     */
    public function langTypeSave()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['language_name', ''],
            ['file_name', ''],
            ['is_default', 0],
            ['status', 0]
        ]);
        $this->services->langTypeSave($data);
        CacheService::delete('lang_type_data');
        return app('json')->success(100000);
    }

    /**
     * Sửa trạng thái loại ngôn ngữ
     * @param $id
     * @param $status
     * @return mixed
     */
    public function langTypeStatus($id, $status)
    {
        $this->services->langTypeStatus($id, $status);
        return app('json')->success(100014);
    }

    /**
     * Xóa loại ngôn ngữ
     * @param int $id
     * @return mixed
     */
    public function langTypeDel(int $id = 0)
    {
        $this->services->langTypeDel($id);
        CacheService::delete('lang_type_data');
        return app('json')->success(100002);
    }
}
