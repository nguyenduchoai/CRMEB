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
use app\services\system\lang\LangCountryServices;
use think\facade\App;

class LangCountry extends AuthController
{
    /**
     * @param App $app
     * @param LangCountryServices $services
     */
    public function __construct(App $app, LangCountryServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách ngôn ngữ theo quốc gia
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function langCountryList()
    {
        $where = $this->request->getMore([
            ['keyword', ''],
        ]);
        return app('json')->success($this->services->langCountryList($where));
    }

    /**
     * Form thiết lập loại ngôn ngữ theo quốc gia
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function langCountryForm($id)
    {
        return app('json')->success($this->services->langCountryForm($id));
    }

    /**
     * Sửa ngôn ngữ theo khu vực
     * @param $id
     * @return mixed
     */
    public function langCountrySave($id)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['code', ''],
            ['type_id', 0],
        ]);
        $this->services->langCountrySave($id, $data);
        return app('json')->success('Lưu thành công');
    }

    public function langCountryDel($id)
    {
        $this->services->langCountryDel($id);
        return app('json')->success('Xóa thành công');
    }
}