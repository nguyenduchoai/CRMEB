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
        'title.chsAlphaNum' => '410105',
        'title.length' => '410106',
        'cate_id.require' => '410107',
        'cate_id.number' => '410108',
        'message.require' => '410109',
        'message.length' => '410110',
        'sort.number' => '410111',
    ];
}
