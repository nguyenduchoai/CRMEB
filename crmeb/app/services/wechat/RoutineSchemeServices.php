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

use app\dao\wechat\RoutineSchemeDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\app\MiniProgramService;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

class RoutineSchemeServices extends BaseServices
{
    public function __construct(RoutineSchemeDao $dao)
    {
        $this->dao = $dao;
    }

    public function schemeList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit, 'id desc', [], true);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['expire_time'] = $item['expire_time'] == 0 ? 'Vĩnh viễn' : date('Y-m-d H:i:s', $item['expire_time']);
            $item['http_url'] = sys_config('site_url') . '/surl/' . $item['id'];
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    public function schemeForm($id = 0)
    {
        if ($id) {
            $info = $this->dao->get($id);
            if ($info) $info = $info->toArray();
        } else {
            $info = [];
        }
        $field = [];
        $field[] = Form::input('title', 'Tên', $info['title'] ?? '')->placeholder('Vui lòng nhập tên liên kết');
        $field[] = Form::input('path', 'Trang Mini Program', $info['path'] ?? '')->placeholder('Vui lòng nhập đường dẫn trang Mini Program, có thể kèm tham số, ví dụ: /pages/index/index?a=1&b=2');
        $field[] = Form::radio('expire_type', 'Loại thời hạn', $info['expire_type'] ?? -1)->appendControl(0, [
            Form::dateTime('expire_num', 'Thời gian hết hạn', $info['expire_time'] ?? 0)->appendRule('suffix', [
                'type' => 'div',
                'class' => 'tips-info',
                'domProps' => ['innerHTML' => 'Thời hạn hiệu lực phải sau thời điểm hiện tại 1 phút và trong vòng 30 ngày']
            ]),
        ])->appendControl(1, [
            Form::input('expire_num', 'Số ngày hiệu lực', $info['expire_interval'] ?? 0),
        ])->options([['label' => 'Vĩnh viễn', 'value' => -1], ['label' => 'Thời gian hết hạn', 'value' => 0], ['label' => 'Số ngày hiệu lực', 'value' => 1]]);
        return create_form('Liên kết Mini Program', $field, Url::buildUrl('/app/routine/scheme_save/' . $id), 'POST');
    }

    public function schemeSave($id, $data)
    {
        $path = explode('?', $data['path']);
        $jumpWxa = [
            'path' => $path[0],
            'query' => $path[1] ?? '',
        ];
        $expireNum = $data['expire_type'] == 0 ? strtotime($data['expire_num']) : $data['expire_num'];
        $url = MiniProgramService::getUrlScheme($jumpWxa, $data['expire_type'], $expireNum);
//        $url = MiniProgramService::getUrlLink($jumpWxa);
        $saveData = [];
        $saveData['title'] = $data['title'];
        $saveData['path'] = $data['path'];
        $saveData['url'] = $url;
        $saveData['add_time'] = time();
        $saveData['expire_type'] = $data['expire_type'];
        if ($data['expire_type'] == -1) {
            $saveData['expire_interval'] = $saveData['expire_time'] = 0;
        } elseif ($data['expire_type'] == 0) {
            $saveData['expire_interval'] = 0;
            $saveData['expire_time'] = $expireNum;
        } else {
            $saveData['expire_interval'] = $expireNum;
            $saveData['expire_time'] = time() + ($expireNum * 86400);
        }
        if ($id) {
            $res = $this->dao->update($id, $saveData);
        } else {
            $res = $this->dao->save($saveData);
        }
        if ($res) return true;
        throw new AdminException('Tạo liên kết Mini Program thất bại');
    }
}
