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

namespace app\services\kefu\service;


use app\dao\service\StoreServiceSpeechcraftDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder;
use think\Model;

/**
 * Câu trả lời mẫu
 * Class StoreServiceSpeechcraftServices
 * @package app\services\kefu\service
 * @method array|Model|null get($id, ?array $field = [], ?array $with = []) Lấy một dòng dữ liệu
 * @method update($id, array $data, ?string $key = null) Cập nhật dữ liệu
 */
class StoreServiceSpeechcraftServices extends BaseServices
{

    /**
     * StoreServiceSpeechcraftServices constructor.
     * @param StoreServiceSpeechcraftDao $dao
     */
    public function __construct(StoreServiceSpeechcraftDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSpeechcraftList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getSpeechcraftList($where, $page, $limit);
        foreach ($list as &$item) {
            if (!$item['cate_name']) $item['cate_name'] = 'Mặc định hệ thống';
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Tạo form
     * @return mixed
     */
    public function createForm()
    {
        return create_form('Thêm câu trả lời mẫu', $this->speechcraftForm(), $this->url('/app/wechat/speechcraft'), 'POST');
    }

    /**
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateForm(int $id)
    {
        $info = $this->dao->get($id);
        if (!$info) {
            throw new AdminException('Câu trả lời mẫu bạn muốn sửa không tồn tại');
        }
        return create_form('Sửa câu trả lời mẫu', $this->speechcraftForm($info->toArray()), $this->url('/app/wechat/speechcraft/' . $id), 'PUT');
    }

    /**
     * @param array $infoData
     * @return mixed
     */
    protected function speechcraftForm(array $infoData = [])
    {
        /** @var StoreServiceSpeechcraftCateServices $services */
        $services = app()->make(StoreServiceSpeechcraftCateServices::class);
        $cateList = $services->getCateList(['owner_id' => 0, 'type' => 1]);
        $data = [];
        $data[] = ['value' => 0, 'label' => 'Danh mục mặc định'];
        foreach ($cateList['data'] as $item) {
            $data[] = ['value' => $item['id'], 'label' => $item['name']];
        }
        $form[] = FormBuilder::select('cate_id', 'Danh mục câu trả lời mẫu', $infoData['cate_id'] ?? '')->setOptions($data);
        $form[] = FormBuilder::textarea('title', 'Tiêu đề câu trả lời mẫu', $infoData['title'] ?? '')->required();
        $form[] = FormBuilder::textarea('message', 'Nội dung câu trả lời mẫu', $infoData['message'] ?? '')->required();
        $form[] = FormBuilder::number('sort', 'Thứ tự sắp xếp', (int)($infoData['sort'] ?? 0));
        return $form;
    }
}
