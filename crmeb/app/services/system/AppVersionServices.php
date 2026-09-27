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
declare (strict_types=1);

namespace app\services\system;

use app\dao\system\AppVersionDao;
use app\services\BaseServices;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 * Class AppVersionServices
 * @package app\services\system
 */
class AppVersionServices extends BaseServices
{
    /**
     * DiyServices constructor.
     * @param AppVersionDao $dao
     */
    public function __construct(AppVersionDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách phiên bản
     * @param $platform
     * @return array
     */
    public function versionList($platform)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->versionList($platform, $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = $this->dao->count(['platform' => $platform]);
        return compact('list', 'count');
    }

    /**
     * Form thêm phiên bản
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm($id = 0)
    {
        if ($id) {
            $info = $this->dao->get($id);
        }
        $field[] = Form::hidden('id', $info['id'] ?? 0);
        $field[] = Form::input('version', 'Số phiên bản', $info['version'] ?? '')->col(24);
        $field[] = Form::radio('platform', 'Loại nền tảng', $info['platform'] ?? 1)->options([['label' => 'Android', 'value' => 1], ['label' => 'IOS', 'value' => 2]]);
        $field[] = Form::input('info', 'Giới thiệu phiên bản', $info['info'] ?? '')->type('textarea');
        $field[] = Form::input('url', 'Liên kết tải xuống', $info['url'] ?? '')->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Nhập liên kết tải xuống, với Android là địa chỉ url của gói nén, khi bấm nâng cấp sẽ tự động tải gói nén về và cài đặt thay thế, ví dụ: tên-miền/xxx.zip; với IOS là liên kết App Store, chuyển thẳng đến AppStore, ví dụ: itms-apps://itunes.apple.com/cn/app/id1234567890']
        ]);
        $field[] = Form::radio('is_force', 'Bắt buộc cập nhật', $info['is_force'] ?? 1)->options([['label' => 'Bật', 'value' => 1], ['label' => 'Tắt', 'value' => 0]]);
        $field[] = Form::radio('is_new', 'Bản mới nhất', $info['is_new'] ?? 1)->options([['label' => 'Có', 'value' => 1], ['label' => 'Không', 'value' => 0]]);
        return create_form('Thêm thông tin phiên bản', $field, Url::buildUrl('/system/version_save'), 'POST');

    }

    /**
     * Lưu dữ liệu
     * @param $id
     * @param $data
     * @return mixed
     */
    public function versionSave($id, $data)
    {
        if ($id) {
            return $this->transaction(function () use ($data, $id) {
                if ($data['is_new']) {
                    $this->dao->update(['platform' => $data['platform']], ['is_new' => 0]);
                }
                return $this->dao->update($id, $data);
            });
        } else {
            $data['is_del'] = 0;
            $data['add_time'] = time();
            return $this->transaction(function () use ($data) {
                $this->dao->update(['platform' => $data['platform']], ['is_new' => 0]);
                return $this->dao->save($data);
            });
        }
    }

    /**
     * Lấy thông tin phiên bản mới nhất của hệ thống
     * @param $platform
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNewInfo($platform)
    {
        $res = $this->dao->get(['platform' => $platform, 'is_new' => 1]);
        if ($res) {
            $res = $res->toArray();
            $res['time'] = date('Y-m-d H:i:s', $res['add_time']);
            return $res;
        } else {
            return [];
        }
    }
}
