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
use app\model\article\Article;
use think\exception\ValidateException;

/**
 * DAO bài viết
 * Class ArticleDao
 * @package app\dao\article
 */
class ArticleDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return Article::class;
    }

    /**
     * Tìm kiếm bài viết
     * @param array $where
     * @param bool $search
     * @return \crmeb\basic\BaseModel
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/20
     */
    public function search(array $where = [], bool $search = false)
    {
        return parent::search($where, $search)->when(isset($where['ids']) && count($where['ids']), function ($query) use ($where) {
            $query->whereNotIn('id', $where['ids']);
        });
    }

    /**
     * Lấy danh sách bài viết
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, int $page, int $limit)
    {
        return $this->search($where)->with(['content', 'storeInfo', 'cateName'])->page($page, $limit)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * Lấy một dòng dữ liệu
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $data = $this->search()->with(['content', 'storeInfo', 'cateName'])->find($id);
        if (!$data) throw new ValidateException('Bài viết không tồn tại');
        $data['store_info'] = $data['storeInfo'];
        return $data;
    }

    /**
     * Bài viết thuộc danh mục tin tức
     * @param $new_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function articleLists($new_id)
    {
        return $this->getModel()->where('hide', 0)->where('id', 'in', $new_id)->select();
    }

    /**
     * Chi tiết tin bài
     * @param $new_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function articleContentList($new_id)
    {
        return $this->getModel()->where('hide', 0)->where('id', 'in', $new_id)->with('content')->select();
    }
}
