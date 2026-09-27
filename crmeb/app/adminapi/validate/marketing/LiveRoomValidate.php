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
namespace app\adminapi\validate\marketing;

use think\Validate;

class LiveRoomValidate extends Validate
{

    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'cover_img' => 'require',
        'share_img' => 'require',
        'anchor_wechat' => 'require',
        'start_time' => 'require|checkStartTime',
        'phone' => 'require|checkPhone',
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '400342',
        'cover_img.require' => '400343',
        'share_img.require' => '400344',
        'anchor_wechat.require' => '400345',
        'start_time.require' => '400346',
        'start_time.checkStartTime' => '400346',
        'phone.require' => '400333',
        'phone.checkPhone' => '400252',
    ];

    protected function checkPhone($value): bool
    {
        return check_phone($value) == true;
    }

    protected function checkStartTime($value): bool
    {
        return count($value) == 2;
    }

    protected $scene = [
        'save' => ['name', 'cover_img', 'share_img', 'anchor_wechat', 'start_time', 'phone'],
    ];
}
