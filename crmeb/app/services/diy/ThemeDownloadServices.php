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
namespace app\services\diy;

use app\dao\diy\ThemeDownloadDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Lớp service bản ghi tải xuống theme
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2026/3/10
 */
class ThemeDownloadServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * @param ThemeDownloadDao $dao
     */
    public function __construct(ThemeDownloadDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách bản ghi tải xuống
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function getDownloadList(array $where, int $page = 0, int $limit = 0): array
    {
        $list = $this->dao->themeDownloadList($where, '*', $page, $limit);
        $count = $this->dao->themeDownloadCount($where);
        return compact('list', 'count');
    }

    /**
     * Lấy chi tiết bản ghi tải xuống
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function getDownloadInfo(int $id): array
    {
        $info = $this->dao->get($id);
        if (!$info) {
            throw new AdminException('Bản ghi tải xuống không tồn tại');
        }
        return $info->toArray();
    }

    /**
     * Thêm bản ghi tải xuống
     * @param int $tid ID chủ đề
     * @param string $title Tên chủ đề
     * @param string $downloadUrl Địa chỉ tải xuống
     * @return int ID bản ghi mới
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function addDownloadRecord(int $tid, string $title, string $downloadUrl): int
    {
        return $this->dao->insertGetId([
            'tid'           => $tid,
            'title'         => $title,
            'download_time' => time(),
            'download_url'  => $downloadUrl,
        ]);
    }

    /**
     * Xóa bản ghi tải xuống
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function deleteDownloadRecord(int $id): bool
    {
        if (!$this->dao->get($id)) {
            throw new AdminException('Bản ghi tải xuống không tồn tại');
        }
        return (bool)$this->dao->delete($id);
    }

    /**
     * Cập nhật địa chỉ tải xuống
     * @param int $id ID bản ghi
     * @param string $downloadUrl Địa chỉ tải xuống
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function updateDownloadUrl(int $id, string $downloadUrl): bool
    {
        return (bool)$this->dao->update($id, ['download_url' => $downloadUrl]);
    }
}
