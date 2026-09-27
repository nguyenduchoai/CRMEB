<?php


namespace app\services\wechat;


use app\dao\wechat\WechatQrcodeCateDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 * Class WechatQrcodeCateServices
 * @package app\services\wechat
 * @method getCateList() Danh sách danh mục
 */
class WechatQrcodeCateServices extends BaseServices
{
    /**
     * WechatQrcodeCateServices constructor.
     * @param WechatQrcodeCateDao $dao
     */
    public function __construct(WechatQrcodeCateDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Biểu mẫu thêm/sửa danh mục
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createForm($id = 0)
    {
        $info = $this->dao->get($id);
        $f[] = Form::hidden('id', $id);
        $f[] = Form::input('cate_name', 'Tên nhóm', $info['cate_name'] ?? '')->maxlength(10)->required();
        return create_form($id ? 'Sửa nhóm' : 'Thêm nhóm', $f, Url::buildUrl('/app/wechat_qrcode/cate/save'), 'POST');
    }

    /**
     * Lưu dữ liệu
     * @param $data
     * @return bool
     */
    public function saveData($data)
    {
        $id = $data['id'];
        $data['add_time'] = time();
        if ($id) {
            unset($data['id']);
            $res = $this->dao->update($id, $data);
        } else {
            $res = $this->dao->save($data);
        }
        if (!$res) throw new AdminException(100006);
        return true;
    }

    /**
     * Xóa danh mục
     * @param int $id
     * @return bool
     */
    public function delCate($id = 0)
    {
        $count = app()->make(WechatQrcodeServices::class)->count(['cate_id' => $id]);
        if ($count) throw new AdminException(400454);
        if (!$id) throw new AdminException(100100);
        $res = $this->dao->update($id, ['is_del' => 1]);
        if (!$res) throw new AdminException(100008);
        return true;
    }

}
