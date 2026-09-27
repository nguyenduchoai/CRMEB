<?php

namespace app\services\system;

use app\dao\system\SystemCrudListDao;
use app\services\BaseServices;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

class SystemCrudListServices extends BaseServices
{
    /**
     * SystemCrudListServices constructor.
     * @param SystemCrudListDao $dao
     */
    public function __construct(SystemCrudListDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách từ điển dữ liệu
     * @param $where
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit, '', [], true)->toArray();
        $count = $this->dao->count($where);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        return compact('list', 'count');
    }

    /**
     * Thêm mới/sửa từ điển dữ liệu
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryListCreate($id = 0)
    {
        $info = $this->dao->get($id);
        $field = [];
        $field[] = Form::input('name', 'Tên từ điển', $info['name'] ?? '')->required();
        $field[] = Form::input('mark', 'Mã định danh từ điển', $info['mark'] ?? '')->required();
        $field[] = Form::radio('level', 'Phân cấp', $info['level'] ?? 0)->options([['value' => 1, 'label' => 'Nhiều cấp'], ['value' => 0, 'label' => 'Cấp 1']]);
        $field[] = Form::radio('status', 'Trạng thái', $info['status'] ?? 1)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return create_form($id ? 'Sửa' : 'Thêm mới', $field, Url::buildUrl('/system/crud/data_dictionary_list/save/' . $id), 'POST');
    }

    /**
     * Lưu từ điển dữ liệu
     * @param int $id
     * @param array $data
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryListSave($id = 0, $data = [])
    {
        if ($id) {
            $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->dao->save($data);
        }
        return true;
    }

    /**
     * Xóa từ điển dữ liệu
     * @param $id
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function dataDictionaryListDel($id)
    {
        $res1 = $this->dao->delete($id);
        $res2 = app()->make(SystemCrudDataService::class)->delete(['cid' => $id]);
        return $res1 && $res2;
    }
}