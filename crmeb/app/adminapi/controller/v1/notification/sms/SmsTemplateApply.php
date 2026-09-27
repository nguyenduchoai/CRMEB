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
namespace app\adminapi\controller\v1\notification\sms;

use app\adminapi\controller\AuthController;
use app\services\serve\ServeServices;
use think\facade\App;

/**
 * Đăng ký mẫu SMS
 * Class SmsTemplateApply
 * @package app\admin\controller\sms
 */
class SmsTemplateApply extends AuthController
{
    /**
     * @param App $app
     * @param ServeServices $services
     */
    public function __construct(App $app, ServeServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách mẫu theo cách bất đồng bộ
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['type', 'd'], 0],
            ['status', ''],
            ['title', ''],
            [['page', 'd'], 1],
            [['limit', 'd'], 20],
        ]);
        $where['temp_type'] = $where['type'];
        $templateList = $this->services->sms()->temps($where['page'], $where['limit'], $where['type']);
        $templateList['data'] = $templateList['data'] ?? [];
        foreach ($templateList['data'] as $key => &$item) {
            $item['templateid'] = $item['temp_id'];
            switch ((int)$item['temp_type']) {
                case 1:
                    $item['type'] = 'Mã xác thực';
                    break;
                case 2:
                    $item['type'] = 'Thông báo';
                    break;
                case 30:
                    $item['type'] = 'SMS marketing';
                    break;
            }
        }
        return app('json')->success($templateList);
    }

    /**
     * Hiển thị trang form tạo resource
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->services->getSmsTemplateForm());
    }

    /**
     * Lưu resource mới tạo
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['content', ''],
            ['type', 0]
        ]);
        if (!strlen(trim($data['title']))) {
            return app('json')->fail(400142);
        }
        if (!strlen(trim($data['content']))) {
            return app('json')->fail(400143);
        }
        $this->services->sms()->apply($data['title'], $data['content'], $data['type']);
        return app('json')->success(100027);
    }
}
