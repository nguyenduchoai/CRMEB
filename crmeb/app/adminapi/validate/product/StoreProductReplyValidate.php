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
namespace app\adminapi\validate\product;

use think\Validate;

class StoreProductReplyValidate extends Validate
{
    public function __construct()
    {
        parent::__construct();

        /**
         * Định nghĩa thông báo lỗi
         * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
         *
         * @var array
         */
        $this->message = [
            'product_id.require' => '400337',
            'avatar.require' => '400000',
            'nickname.require' => '400001',
            'comment.require' => '400002',
            'product_score.require' => '400003',
            'service_score.require' => '400004',
            'product_score.In' => '400005',
            'service_score.In' => '400006',
        ];
    }

    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'product_id' => 'require',
        'avatar' => 'require',
        'nickname' => 'require',
        'comment' => 'require',
        'product_score' => ['require','In:1,2,3,4,5'],
        'service_score' => ['require','In:1,2,3,4,5'],
    ];

    protected $scene = [
        'save' => ['product_id', 'nickname', 'comment', 'avatar', 'product_score', 'service_score'],
    ];
}
