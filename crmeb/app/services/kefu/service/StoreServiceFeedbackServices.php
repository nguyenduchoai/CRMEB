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

namespace app\services\kefu\service;


use app\dao\service\StoreServiceFeedbackDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder;

/**
 * Phản hồi CSKH
 * Class StoreServiceFeedbackServices
 * @package app\services\kefu\service
 */
class StoreServiceFeedbackServices extends BaseServices
{

    /**
     * StoreServiceFeedbackServices constructor.
     * @param StoreServiceFeedbackDao $dao
     */
    public function __construct(StoreServiceFeedbackDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách phản hồi
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFeedbackList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $data = $this->dao->getFeedback($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('data', 'count');
    }

    /**
     *
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editForm(int $id)
    {
        $feedInfo = $this->dao->get($id);
        if (!$feedInfo) {
            throw new AdminException(400460);
        }
        $feedInfo = $feedInfo->toArray();
        $field = [
            FormBuilder::textarea('make', 'Ghi chú', $feedInfo['make'])->col(22),
        ];
        if (!$feedInfo['status']) {
            $field[] = FormBuilder::radio('status', 'Trạng thái', 0)->setOptions([
                ['label' => 'Đã xử lý', 'value' => 1],
                ['label' => 'Chưa xử lý', 'value' => 0]
            ]);
        }
        return create_form($feedInfo['status'] ? 'Ghi chú' : 'Xử lý', $field, $this->url('/app/feedback/' . $id), 'PUT');
    }
}
