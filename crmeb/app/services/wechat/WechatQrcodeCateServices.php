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
        if (!$res) throw new AdminException('Lưu thất bại');
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
        if ($count) throw new AdminException('Danh mục này có danh mục con, không thể xóa');
        if (!$id) throw new AdminException('Tham số không hợp lệ');
        $res = $this->dao->update($id, ['is_del' => 1]);
        if (!$res) throw new AdminException('Xóa thất bại');
        return true;
    }

}
