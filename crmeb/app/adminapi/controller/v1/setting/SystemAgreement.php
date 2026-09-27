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
use app\services\other\AgreementServices;
use think\facade\App;

class SystemAgreement extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemCity constructor.
     * @param App $app
     * @param AgreementServices $services
     */
    public function __construct(App $app, AgreementServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy nội dung thỏa thuận
     * @param $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAgreement($type)
    {
        if (!$type) return app('json')->fail('Loại thỏa thuận không tồn tại');
        $info = $this->services->getAgreementBytype($type);
        return app('json')->success($info);
    }

    /**
     * Lưu nội dung thỏa thuận
     * @return mixed
     */
    public function saveAgreement()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['type', 0],
            ['title', ''],
            ['content', ''],
        ]);
        $data['status'] = 1;
        $this->services->saveAgreement($data, $data['id']);
        return app('json')->success('Lưu thành công');
    }
}
