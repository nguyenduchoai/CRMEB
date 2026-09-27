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

namespace app\model\wechat;


use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Model mã kênh
 * Class WechatKey
 * @package app\model\wechat
 */
class WechatQrcode extends BaseModel
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
    protected $name = 'wechat_qrcode';

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->bind(['nickname', 'avatar']);
    }

    public function record()
    {
        return $this->hasOne(WechatQrcodeRecord::class, 'qid', 'id');
    }

    public function searchUidAttr($query, $value)
    {
        if ($value != '') $query->where('uid', $value);
    }

    public function searchCateIdAttr($query, $value)
    {
        if ($value != '') $query->where('cate_id', $value);
    }

    public function searchNameAttr($query, $value)
    {
        if ($value != '') $query->whereLike('name', '%' . $value . '%');
    }

    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '') $query->where('is_del', $value);
    }
}
