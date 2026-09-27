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

namespace app\model\wechat;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Từ khóa
 * Class WechatReply
 * @package app\model\wechat
 */
class WechatReply extends BaseModel
{
    use ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'wechat_reply';

    /**
     * Loại thông báo
     * @var string[]
     */
    public static $replyType = ['text', 'image', 'news', 'voice'];

    /**
     * Liên kết tự động trả lời OA WeChat
     * @return \think\model\relation\HasMany
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function wechatKeys()
    {
        return $this->hasMany(WechatKey::class, 'reply_id', 'id');
    }

    /**
     * Liên kết tự động trả lời CSKH
     * @return \think\model\relation\HasOne
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function kefuKey()
    {
        return $this->hasOne(WechatKey::class, 'reply_id', 'id')->bind(['keys']);
    }
}
