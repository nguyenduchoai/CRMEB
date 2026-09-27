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

namespace app\dao\article;


use app\dao\BaseDao;
use app\model\article\ArticleCategory;

/**
 * Danh mục bài viết
 * Class ArticleCategoryDao
 * @package app\dao\article
 */
class ArticleCategoryDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ArticleCategory::class;
    }

    /**
     * Lấy danh sách danh mục bài viết
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, int $page = 0, int $limit = 0)
    {
        return $this->search($where)->when(!$page && !$limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * Lấy danh mục bài viết ở phía người dùng
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getArticleCategory()
    {
        return $this->search(['hidden' => 0, 'is_del' => 0, 'status' => 1, 'pid' => 0])->with(['children'])
            ->order('sort DESC,id DESC')
            ->field('id,pid,title')
            ->select()->toArray();
    }

    /**
     * Danh mục bài viết cấp 2
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getArticleTwoCategory()
    {
        return $this->getModel()
            ->where('hidden', 0)
            ->where('is_del', 0)
            ->where('status', 1)
            ->order('sort DESC,id DESC')
            ->field('id,pid,title')
            ->select()->toArray();
    }

    /**
     * Danh sách chọn danh mục cấp trên khi thêm/sửa
     * @param array $where
     * @return array
     * @throws \ReflectionException
     */
    public function getMenus(array $where)
    {
        return $this->search($where)->order('sort desc,id desc')->column('title,pid,id,is_del,status');
    }

    /**
     * Danh sách dạng cây
     * @param array $where
     * @param array $field
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/9/7
     */
    public function getTreeList(array $where, array $field)
    {
        return $this->search($where)->field($field)->order('sort desc,id desc')->select()->toArray();
    }
}
