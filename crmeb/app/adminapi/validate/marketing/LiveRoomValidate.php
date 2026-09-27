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
        'name.require' => 'Vui lòng nhập tên phòng livestream',
        'cover_img.require' => 'Vui lòng chọn ảnh nền',
        'share_img.require' => 'Vui lòng chọn ảnh chia sẻ',
        'anchor_wechat.require' => 'Vui lòng chọn streamer',
        'start_time.require' => 'Vui lòng chọn thời gian bắt đầu, kết thúc livestream',
        'start_time.checkStartTime' => 'Vui lòng chọn thời gian bắt đầu, kết thúc livestream',
        'phone.require' => 'Vui lòng điền số điện thoại',
        'phone.checkPhone' => 'Số điện thoại sai định dạng',
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
