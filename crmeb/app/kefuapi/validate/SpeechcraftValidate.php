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

namespace app\kefuapi\validate;


use think\Validate;

class SpeechcraftValidate extends Validate
{
    /**
     * @var string[]
     */
    protected $rule = [
        'title' => 'chsAlphaNum|length:0,50',
        'cate_id' => 'require|number',
        'message' => 'require|length:0,500',
        'sort' => 'number',
    ];

    /**
     * @var string[]
     */
    protected $message = [
        'title.chsAlphaNum' => 'Vui lòng nhập chữ Hán, chữ cái hoặc chữ số',
        'title.length' => 'Tiêu đề không được vượt quá 50 ký tự',
        'cate_id.require' => 'Vui lòng chọn danh mục',
        'cate_id.number' => 'Danh mục phải là số',
        'message.require' => 'Vui lòng điền nội dung câu trả lời mẫu',
        'message.length' => 'Câu trả lời mẫu không được vượt quá 500 ký tự',
        'sort.number' => 'Thứ tự sắp xếp phải là số',
    ];
}
