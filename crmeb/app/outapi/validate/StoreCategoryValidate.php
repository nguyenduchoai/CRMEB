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
namespace app\outapi\validate;

use think\Validate;

class StoreCategoryValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'pid' => 'number|egt:0',
        'cate_name' => 'require|max:25',
        'pic' => 'max:128',
        'big_pic' => 'max:200',
        'sort' => 'number|egt:0',
        'is_show' => 'in:0,1'
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'pid.number' => 'Tham số ID cha sai kiểu dữ liệu',
        'pid.egt' => 'Tham số ID cha sai kiểu dữ liệu',
        'cate_name.require' => 'Tên danh mục không được để trống',
        'cate_name.max' => 'Tên danh mục không được vượt quá 25 ký tự',
        'pic.max' => 'Biểu tượng danh mục không được vượt quá 128 ký tự',
        'big_pic.max' => 'Ảnh lớn danh mục không được vượt quá 200 ký tự',
        'sort.number' => 'Tham số thứ tự sắp xếp sai kiểu dữ liệu',
        'sort.egt' => 'Thứ tự sắp xếp không được nhỏ hơn 0',
        'is_show.in' => 'Trạng thái phải là số nguyên trong khoảng 0-1',
    ];

    protected $scene = [
        'save' => ['pid', 'cate_name', 'pic', 'big_pic', 'sort', 'is_show'],
    ];
}