<?php

namespace app\services\system\lang;

use app\dao\system\lang\LangCountryDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

class LangCountryServices extends BaseServices
{
    /**
     * @param LangCountryDao $dao
     */
    public function __construct(LangCountryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách ngôn ngữ theo khu vực
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function LangCountryList(array $where = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit, 'id desc', [], true)->toArray();
        /** @var LangTypeServices $langTypeServices */
        $langTypeServices = app()->make(LangTypeServices::class);
        $langTypeList = $langTypeServices->getColumn([], 'language_name,file_name,id', 'id');
        foreach ($list as &$item) {
            if (isset($langTypeList[$item['type_id']])) {
                $item['link_lang'] = $langTypeList[$item['type_id']]['language_name'] . '(' . $langTypeList[$item['type_id']]['file_name'] . ')';
            } else {
                $item['link_lang'] = 'Chưa có';
            }
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Biểu mẫu thêm khu vực ngôn ngữ
     * @param $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function langCountryForm($id)
    {
        if ($id) $info = $this->dao->get($id);
        $field = [];
        $field[] = Form::input('name', 'Khu vực', $info['name'] ?? '')->required('Vui lòng nhập khu vực')->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Ví dụ: Trung Quốc, Hồng Kông, Đức']
        ]);
        $field[] = Form::input('code', 'Mã nhận dạng ngôn ngữ', $info['code'] ?? '')->required('Vui lòng nhập mã nhận dạng ngôn ngữ của trình duyệt')->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Mã nhận dạng ngôn ngữ của trình duyệt']
        ]);
        /** @var LangTypeServices $langTypeServices */
        $langTypeServices = app()->make(LangTypeServices::class);
        $list = $langTypeServices->getColumn(['is_del' => 0, 'status' => 1], 'language_name,file_name,id', 'id');
        $setOption = function () use ($list) {
            $menus = [];
            foreach ($list as $item) {
                $menus[] = ['value' => $item['id'], 'label' => $item['language_name'] . '(' . $item['file_name'] . ')'];
            }
            return $menus;
        };
        $field[] = Form::select('type_id', 'Ngôn ngữ liên kết', $info['type_id'] ?? 0)->setOptions(Form::setOptions($setOption))->filterable(true)->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Vui lòng chọn ngôn ngữ liên kết, loại ngôn ngữ do bạn tự thêm']
        ]);
        return create_form($id ? 'Sửa khu vực ngôn ngữ' : 'Thêm khu vực ngôn ngữ', $field, Url::buildUrl('/setting/lang_country/save/' . $id), 'POST');
    }

    /**
     * Lưu khu vực ngôn ngữ
     * @param $id
     * @param $typeId
     * @return bool
     */
    public function LangCountrySave($id, $data)
    {
        if ($id) {
            $res = $this->dao->update(['id' => $id], $data);
        } else {
            $res = $this->dao->save($data);
        }
        if (!$res) throw new AdminException(100007);
        CacheService::clear();
        return true;
    }

    /**
     * Xóa khu vực ngôn ngữ
     * @param $id
     * @return bool
     */
    public function langCountryDel($id)
    {
        $res = $this->dao->delete($id);
        if (!$res) throw new AdminException(100008);
        CacheService::clear();
        return true;
    }
}
