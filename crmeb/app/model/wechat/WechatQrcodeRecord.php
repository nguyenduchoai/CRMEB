<?php


namespace app\model\wechat;


use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class WechatQrcodeRecord extends BaseModel
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
    protected $name = 'wechat_qrcode_record';

    /**
     * Liên kết user
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    public function searchQidAttr($query, $value)
    {
        if ($value) $query->where('qid', $value);
    }

    public function searchIsFollowAttr($query, $value)
    {
        if ($value !== '') $query->where('is_follow', $value);
    }
}