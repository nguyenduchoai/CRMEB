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
/**
 * @author: liaofei<136327134@qq.com>
 * @day: 2020/7/6
 */

namespace app\dao;

use crmeb\basic\BaseModel;
use think\helper\Str;
use think\Model;

/**
 * Class BaseDao
 * @package app\dao
 */
abstract class BaseDao
{
    /**
     * Alias của bảng hiện tại
     * @var string
     */
    protected $alias;

    /**
     * Alias của bảng join
     * @var string
     */
    protected $joinAlis;


    /**
     * Lấy model hiện tại
     * @return string
     */
    abstract protected function setModel(): string;

    /**
     * Thiết lập model bảng liên kết (join)
     * @return string
     */
    protected function setJoinModel(): string
    {
    }

    /**
     * Đọc số lượng dữ liệu
     * @param array $where
     * @param bool $search
     * @return int
     * @throws \ReflectionException
     */
    public function count(array $where = [], bool $search = true)
    {
        return $this->search($where, $search)->count();
    }

    /**
     * Lấy dữ liệu theo một số điều kiện
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @param string $order
     * @param bool $search
     * @return \think\Collection
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function selectList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false)
    {
        return $this->selectModel($where, $field, $page, $limit, $order, $with, $search)->select();
    }

    /**
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @param string $order
     * @param array $with
     * @param bool $search
     * @return BaseModel
     * @throws \ReflectionException
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/14
     */
    public function selectModel(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = '', array $with = [], bool $search = false)
    {
        if ($search) {
            $model = $this->search($where);
        } else {
            $model = $this->getModel()->where($where);
        }
        return $model->field($field)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->when($order !== '', function ($query) use ($order) {
            $query->order($order);
        })->when($with, function ($query) use ($with) {
            $query->with($with);
        });
    }

    /**
     * Lấy tổng số theo một số điều kiện
     * @param array $where
     * @return int
     */
    public function getCount(array $where)
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * Lấy tổng số đã loại trùng theo một số điều kiện
     * @param array $where
     * @param $field
     * @param bool $search
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDistinctCount(array $where, $field, bool $search = true)
    {
        if ($search) {
            return $this->search($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;
        } else {
            return $this->getModel()->where($where)->field('COUNT(distinct(' . $field . ')) as count')->select()->toArray()[0]['count'] ?? 0;
        }
    }

    /**
     * Lấy model
     * @return BaseModel
     */
    protected function getModel()
    {
        return app()->make($this->setModel());
    }

    /**
     * Lấy khóa chính
     * @return array|string
     */
    protected function getPk()
    {
        return $this->getModel()->getPk();
    }

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/8
     */
    public function getTableName()
    {
        return $this->getModel()->getName();
    }

    /**
     * Lấy một dòng dữ liệu
     * @param $id
     * @param array|null $field
     * @param array|null $with
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get($id, ?array $field = [], ?array $with = [])
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [$this->getPk() => $id];
        }
        return $this->getModel()->where($where)->when(count($with), function ($query) use ($with) {
            $query->with($with);
        })->field($field ?? ['*'])->find();
    }

    /**
     * Truy vấn xem một dữ liệu có tồn tại không
     * @param $map
     * @param string $field
     * @return bool Có tồn tại không
     */
    public function be($map, string $field = '')
    {
        if (!is_array($map) && empty($field)) $field = $this->getPk();
        $map = !is_array($map) ? [$field => $map] : $map;
        return 0 < $this->getModel()->where($map)->count();
    }

    /**
     * Lấy một dữ liệu theo điều kiện
     * @param array $where
     * @param string|null $field
     * @param array $with
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOne(array $where, ?string $field = '*', array $with = [])
    {
        $field = explode(',', $field);
        return $this->get($where, $field, $with);
    }

    /**
     * Lấy giá trị của một trường
     * @param $where
     * @param string|null $field
     * @return mixed
     */
    public function value($where, ?string $field = '')
    {
        $pk = $this->getPk();
        return $this->search($this->setWhere($where))->value($field ?: $pk);
    }

    /**
     * Lấy mảng của một trường
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getColumn(array $where, string $field, string $key = '')
    {
        return $this->getModel()->where($where)->column($field, $key);
    }


    /**
     * Xóa
     * @param int|string|array $id
     * @return mixed
     */
    public function delete($id, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()->where($where)->delete();
    }

    /**
     * Xóa lịch sử
     * @param int $id
     * @param bool $force
     * @return bool
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/15
     */
    public function destroy(int $id, bool $force = false)
    {
        return $this->getModel()->destroy($id, $force);
    }

    /**
     * Cập nhật dữ liệu
     * @param int|string|array $id
     * @param array $data
     * @param string|null $key
     * @return BaseModel
     */
    public function update($id, array $data, ?string $key = null)
    {
        if (is_array($id)) {
            $where = $id;
        } else {
            $where = [is_null($key) ? $this->getPk() : $key => $id];
        }
        return $this->getModel()::update($data, $where);
    }

    /**
     * @param $where
     * @return array|mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    protected function setWhere($where, ?string $key = null)
    {
        if (!is_array($where)) {
            $where = [is_null($key) ? $this->getPk() : $key => $where];
        }
        return $where;
    }

    /**
     * Cập nhật dữ liệu theo lô
     * @param array $ids
     * @param array $data
     * @param string|null $key
     * @return BaseModel
     */
    public function batchUpdate(array $ids, array $data, ?string $key = null)
    {
        return $this->getModel()->whereIn(is_null($key) ? $this->getPk() : $key, $ids)->update($data);
    }

    /**
     * Thêm dữ liệu
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        return $this->getModel()::create($data);
    }

    /**
     * Thêm dữ liệu
     * @param array $data
     * @return \think\Collection
     * @throws \Exception
     */
    public function saveAll(array $data)
    {
        return $this->getModel()->saveAll($data);
    }

    /**
     * Lấy giá trị trong một trường nào đó
     * @param $value
     * @param string $filed
     * @param string|null $valueKey
     * @param array|string[] $where
     * @return mixed
     */
    public function getFieldValue($value, string $filed, ?string $valueKey = '', ?array $where = [])
    {
        return $this->getModel()->getFieldValue($value, $filed, $valueKey, $where);
    }

    /**
     * Lấy searcher và key điều kiện tìm kiếm, cùng mảng điều kiện không thuộc searcher
     * @param array $where
     * @return array[]
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/18
     */
    private function getSearchData(array $where)
    {
        $with = [];
        $otherWhere = [];
        $responses = new \ReflectionClass($this->setModel());
        foreach ($where as $key => $value) {
            $method = 'search' . Str::studly($key) . 'Attr';
            if ($responses->hasMethod($method)) {
                $with[] = $key;
            } else {
                if (!in_array($key, ['timeKey', 'store_stock', 'integral_time'], true)) {
                    if (!is_array($value)) {
                        $otherWhere[] = [$key, '=', $value];
                    } else if (count($value) === 3) {
                        $otherWhere[] = $value;
                    }
                }
            }
        }
        return [$with, $otherWhere];
    }

    /**
     * Lấy nội dung tìm kiếm theo searcher
     * @param $where
     * @param $search
     * @return BaseModel
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/18
     */
    protected function withSearchSelect($where, $search)
    {
        [$with, $otherWhere] = $this->getSearchData($where);
        return $this->getModel()->withSearch($with, $where)->when($search, function ($query) use ($otherWhere) {
            $query->where($this->filterWhere($otherWhere));
        });
    }

    /**
     * Lọc bỏ các trường điều kiện where không tồn tại trong bảng dữ liệu
     * @param array $where
     * @return array
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/11
     */
    protected function filterWhere(array $where = [])
    {
        $fields = $this->getModel()->getTableFields();
        foreach ($where as $key => $item) {
            if (!in_array($item[0], $fields)) {
                unset($where[$key]);
            }
        }
        return $where;
    }

    /**
     * Tìm kiếm
     * @param array $where
     * @param bool $search
     * @return BaseModel
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/18
     */
    public function search(array $where = [], bool $search = true)
    {
        if ($where) {
            return $this->withSearchSelect($where, $search);
        } else {
            return $this->getModel();
        }
    }

    /**
     * Tính tổng
     * @param array $where
     * @param string $field
     * @param bool $search
     * @return float
     * @throws \ReflectionException
     */
    public function sum(array $where, string $field, bool $search = false)
    {
        if ($search) {
            return $this->search($where)->sum($field);
        } else {
            return $this->getModel()->where($where)->sum($field);
        }
    }

    /**
     * Phép cộng độ chính xác cao
     * @param $key
     * @param string $incField
     * @param string $inc
     * @param string|null $keyField
     * @param int $acc
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bcInc($key, string $incField, string $inc, string $keyField = null, int $acc = 2)
    {
        return $this->bc($key, $incField, $inc, $keyField, 1);
    }

    /**
     * Phép trừ độ chính xác cao
     * @param $key
     * @param string $decField
     * @param string $dec
     * @param string|null $keyField
     * @param int $acc
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bcDec($key, string $decField, string $dec, string $keyField = null, int $acc = 2)
    {
        return $this->bc($key, $decField, $dec, $keyField, 2);
    }

    /**
     * Tính toán độ chính xác cao và lưu lại
     * @param $key
     * @param string $incField
     * @param string $inc
     * @param string|null $keyField
     * @param int $type
     * @param int $acc
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bc($key, string $incField, string $inc, string $keyField = null, int $type = 1, int $acc = 2)
    {
        if ($keyField === null) {
            $result = $this->get($key);
        } else {
            $result = $this->getOne([$keyField => $key]);
        }
        if (!$result) return false;
        $new = 0;
        if ($type === 1) {
            $new = bcadd($result[$incField], $inc, $acc);
        } else if ($type === 2) {
            if ($result[$incField] < $inc) return false;
            $new = bcsub($result[$incField], $inc, $acc);
        }
        $result->{$incField} = $new;
        return false !== $result->save();
    }

    /**
     * Giảm tồn kho, tăng lượt bán
     * @param array $where
     * @param int $num
     * @param string $stock
     * @param string $sales
     * @return false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function decStockIncSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales')
    {
        $isQuota = false;
        if (isset($where['type']) && $where['type']) {
            $isQuota = true;
            if (count($where) == 2) {
                unset($where['type']);
            }
        }
        $field = $isQuota ? 'stock,quota' : 'stock';
        $product = $this->getModel()->where($where)->field($field)->find();
        if ($product) {
            return $this->getModel()->where($where)->when($isQuota, function ($query) use ($num) {
                $query->dec('quota', $num);
            })->dec($stock, $num)->inc($sales, $num)->update();
        }
        return false;
    }

    /**
     * Tăng tồn kho, giảm lượt bán
     * @param array $where
     * @param int $num
     * @param string $stock
     * @param string $sales
     * @return mixed
     */
    public function incStockDecSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales')
    {
        $isQuota = false;
        if (isset($where['type']) && $where['type']) {
            $isQuota = true;
            if (count($where) == 2) {
                unset($where['type']);
            }
        }
        $salesOne = $this->getModel()->where($where)->value($sales);
        if ($salesOne) {
            $salesNum = $num;
            if ($num > $salesOne) {
                $salesNum = $salesOne;
            }
            return $this->getModel()->where($where)->when($isQuota, function ($query) use ($num) {
                $query->inc('quota', $num);
            })->inc($stock, $num)->dec($sales, $salesNum)->update();
        }
        return true;
    }

    /**
     * Lấy giá trị lớn nhất của một trường trong dữ liệu theo điều kiện
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function getMax(array $where = [], string $field = '')
    {
        return $this->getModel()->where($where)->max($field);
    }

    /**
     * Lấy giá trị nhỏ nhất của một trường trong dữ liệu theo điều kiện
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function getMin(array $where = [], string $field = '')
    {
        return $this->getModel()->where($where)->min($field);
    }

    /**
     * Lấy bản ghi đầu tiên theo (điều kiện) sắp xếp theo (sort)
     * @param array $where
     * @param string $order
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/9/12
     */
    public function getOrderOne(array $where = [], string $order = 'id desc')
    {
        return $this->getModel()->where($where)->order($order)->find();
    }

    /**
     * Chèn dữ liệu và trả về ID tự tăng
     * @param array $data
     * @return int|string
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/11
     */
    public function insertGetId(array $data)
    {
        return $this->getModel()->insertGetId($data);
    }
}
