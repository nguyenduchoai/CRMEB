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
namespace app\services\message;


use app\dao\other\TemplateMessageDao;
use app\services\BaseServices;

/**
 * Class quản lý tin nhắn mẫu
 * Class TemplateMessageServices
 * @package app\services\other
 * @method getOne(array $where, ?string $field = '*')  Lấy một thông tin
 * @method save(array $data) Thêm
 * @method get(int $id, ?array $field = []) Lấy một thông tin
 * @method update($id, array $data, ?string $key = null) Cập nhật dữ liệu
 * @method delete($id, ?string $key = null) Xóa
 */
class TemplateMessageServices extends BaseServices
{
    /**
     * Tin nhắn mẫu
     * TemplateMessageServices constructor.
     * @param TemplateMessageDao $dao
     */
    public function __construct(TemplateMessageDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách tin nhắn mẫu
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTemplateList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getTemplateList($where, $page, $limit);
        foreach ($list as &$item) {
            if ($item['content']) $item['content'] = explode("\n", $item['content']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy id tin nhắn mẫu
     * @param string $templateId
     * @param int $type
     * @return mixed
     */
    public function getTempId(string $templateId, int $type = 0)
    {
        return $this->dao->value(['type' => $type, 'tempkey' => $templateId, 'status' => 1], 'tempid');
    }

}
