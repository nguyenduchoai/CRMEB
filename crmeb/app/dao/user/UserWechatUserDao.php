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
declare (strict_types=1);

namespace app\dao\user;

use think\model;
use app\dao\BaseDao;
use app\model\user\User;
use app\model\wechat\WechatUser;

/**
 *
 * Class UserWechatUserDao
 * @package app\dao\user
 */
class UserWechatUserDao extends BaseDao
{
    /**
     * @var string
     */
    protected $alias = '';

    /**
     * @var string
     */
    protected $join_alis = '';

    /**
     * Danh sách trắng tìm kiếm chính xác
     * @var string[]
     */
    protected $withField = ['uid', 'nickname', 'user_type', 'phone'];

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return User::class;
    }

    public function joinModel(): string
    {
        return WechatUser::class;
    }

    /**
     * Model liên kết
     * @param string $alias
     * @param string $join_alias
     * @return \crmeb\basic\BaseModel
     */
    public function getModel(string $alias = 'u', string $join_alias = 'w', $join = 'left')
    {
        $this->alias = $alias;
        $this->join_alis = $join_alias;
        /** @var WechatUser $wechcatUser */
        $wechcatUser = app()->make($this->joinModel());
        $table = $wechcatUser->getName();
        return parent::getModel()->alias($alias)->join($table . ' ' . $join_alias, $alias . '.uid = ' . $join_alias . '.uid', $join);
    }

    public function getList(array $where, $field = '*', int $page, int $limit)
    {
        return $this->getModel()->where($where)->field($field)->page($page, $limit)->select()->toArray();
    }

    /**
     * Lấy tổng số
     * @param array $where
     * @return int
     */
    public function getCount(array $where): int
    {
        return $this->getModel()->where($where)->count();
    }

    /**
     * Số lượng bản ghi model theo điều kiện tổ hợp
     * @param Model $model
     * @return int
     */
    public function getCountByWhere(array $where): int
    {
        return $this->searchWhere($where)->group($this->alias . '.uid')->count();
    }

    /**
     * Truy vấn danh sách model theo điều kiện tổ hợp
     * @param Model $model
     * @return array
     */
    public function getListByModel(array $where, string $field = '', string $order = '', int $page, int $limit): array
    {
        return $this->searchWhere($where)->field($field)->page($page, $limit)->group($this->alias . '.uid')->order(($order ? $order . ' ,' : '') . $this->alias . '.uid desc')->select()->toArray();
    }

    /**
     * @param $where
     * @param array|null $field
     * @param int $page
     * @param int $limit
     * @return \crmeb\basic\BaseModel
     */
    public function searchWhere($where, ?array $field = [])
    {
        $model = $this->getModel();
        $userAlias = $this->alias . '.';
        $wechatUserAlias = $this->join_alis . '.';
        // Thời gian truy cập của người dùng
        if (isset($where['user_time_type']) && isset($where['user_time'])) {
            //Thời gian truy cập lần cuối
            if ($where['user_time_type'] == 'visitno' && $where['user_time'] != '') {
                list($startTime, $endTime) = explode('-', $where['user_time']);
                if ($startTime && $endTime) {
                    $endTime = strtotime($endTime) + 24 * 3600;
                    $model = $model->where($userAlias . "last_time < " . strtotime($startTime) . " OR " . $userAlias . "last_time > " . $endTime);
                }
            }
            //Thời gian truy cập
            if ($where['user_time_type'] == 'visit' && $where['user_time'] != '') {
                list($startTime, $endTime) = explode('-', $where['user_time']);
                if ($startTime && $endTime) {
                    $model = $model->where($userAlias . 'last_time', '>', strtotime($startTime));
                    $model = $model->where($userAlias . 'last_time', '<', strtotime($endTime) + 24 * 3600);
                }
            }
            //Thời gian thêm
            if ($where['user_time_type'] == 'add_time' && $where['user_time'] != '') {
                list($startTime, $endTime) = explode('-', $where['user_time']);
                if ($startTime && $endTime) {
                    $model = $model->where($userAlias . 'add_time', '>', strtotime($startTime));
                    $model = $model->where($userAlias . 'add_time', '<', strtotime($endTime) + 24 * 3600);
                }
            }
        }
        //Số lần mua
        if (isset($where['pay_count']) && $where['pay_count'] != '') {
            if ($where['pay_count'] == '-1') {
                $model = $model->where($userAlias . 'pay_count', 0);
            } else {
                $model = $model->where($userAlias . 'pay_count', '>', $where['pay_count']);
            }
        }
        if (isset($where['pay_count_num']) && count($where['pay_count_num']) == 2) {
            if ($where['pay_count_num'][0] != '' && $where['pay_count_num'][1] != '') {
                $model = $model->whereBetween($userAlias . 'pay_count', $where['pay_count_num']);
            } elseif ($where['pay_count_num'][0] != '' && $where['pay_count_num'][1] == '') {
                $model = $model->where($userAlias . 'pay_count', '>', $where['pay_count_num'][0]);
            } elseif ($where['pay_count_num'][0] == '' && $where['pay_count_num'][1] != '') {
                $model = $model->where($userAlias . 'pay_count', '<', $where['pay_count_num'][1]);
            }
        }

        //Số dư nạp trước
        if (isset($where['balance']) && count($where['balance']) == 2) {
            if ($where['balance'][0] != '' && $where['balance'][1] != '') {
                $model = $model->whereBetween($userAlias . 'now_money', $where['balance']);
            } elseif ($where['balance'][0] != '' && $where['balance'][1] == '') {
                $model = $model->where($userAlias . 'now_money', '>', $where['balance'][0]);
            } elseif ($where['balance'][0] == '' && $where['balance'][1] != '') {
                $model = $model->where($userAlias . 'now_money', '<', $where['balance'][1]);
            }
        }

        //Điểm thưởng còn lại
        if (isset($where['integral']) && count($where['integral']) == 2) {
            if ($where['integral'][0] != '' && $where['integral'][1] != '') {
                $model = $model->whereBetween($userAlias . 'integral', $where['integral']);
            } elseif ($where['integral'][0] != '' && $where['integral'][1] == '') {
                $model = $model->where($userAlias . 'integral', '>', $where['integral'][0]);
            } elseif ($where['integral'][0] == '' && $where['integral'][1] != '') {
                $model = $model->where($userAlias . 'integral', '<', $where['integral'][1]);
            }
        }

        //Hạng người dùng
        if (isset($where['level']) && $where['level']) {
            $model = $model->where($userAlias . 'level', $where['level']);
        }
        //Nhóm người dùng
        if (isset($where['group_id']) && $where['group_id']) {
            $model = $model->where($userAlias . 'group_id', $where['group_id']);
        }
        //Trạng thái người dùng
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where($userAlias . 'status', $where['status']);
        }
        //Người dùng có phải là cộng tác viên hay không
        if (isset($where['is_promoter']) && $where['is_promoter'] != '') {
            $model = $model->where($userAlias . 'is_promoter', $where['is_promoter']);
        }
        //Nhãn người dùng
        if (isset($where['label_id']) && $where['label_id']) {
            $model = $model->whereIn($userAlias . 'uid', function ($query) use ($where) {
                if (is_array($where['label_id'])) {
                    $query->name('user_label_relation')->whereIn('label_id', $where['label_id'])->field('uid')->select();
                } else {
                    if (strpos($where['label_id'], ',') !== false) {
                        $query->name('user_label_relation')->whereIn('label_id', explode(',', $where['label_id']))->field('uid')->select();
                    } else {
                        $query->name('user_label_relation')->where('label_id', (int)$where['label_id'])->field('uid')->select();
                    }
                }
            });
        }
        //Là thành viên trả phí
        if (isset($where['isMember']) && $where['isMember'] != '') {
            if ($where['isMember'] == 0) {
                $model = $model->where($userAlias . 'is_money_level', 0);
            } else {
                $model = $model->where($userAlias . 'is_money_level', '>', 0);
            }

        }
        //Tìm kiếm theo biệt danh, uid, số điện thoại người dùng
        $fieldKey = $where['field_key'] ?? '';
        $nickname = $where['nickname'] ?? '';
        if ($fieldKey && $nickname && in_array($fieldKey, $this->withField)) {
            switch ($fieldKey) {
                case "nickname":
                case "phone":
                    $model = $model->where($userAlias . trim($fieldKey), 'like', "%" . trim($nickname) . "%");
                    break;
                case "uid":
                    $model = $model->where($userAlias . trim($fieldKey), trim($nickname));
                    break;
            }
        } else if (!$fieldKey && $nickname) {
            $model = $model->where($userAlias . 'nickname|' . $userAlias . 'uid|' . $userAlias . 'phone', 'LIKE', "%$where[nickname]%");
        }
        //Thành phố hiện tại
        if (isset($where['country']) && $where['country']) {
            if ($where['country'] == 'domestic') {
                $model = $model->where($wechatUserAlias . 'country', 'in', ['Trung Quốc', 'China']);
            } else if ($where['country'] == 'abroad') {
                $model = $model->where($wechatUserAlias . 'country', 'not in', ['Trung Quốc', '']);
            }
        }
        //Loại người dùng
        if (isset($where['user_type']) && $where['user_type']) {
            if ($where['user_type'] == 'app') {
                $model = $model->whereIn($userAlias . 'user_type', ['app', 'apple']);
            } else {
                $model = $model->where($userAlias . 'user_type', $where['user_type']);
            }
        }
        //Giới tính người dùng
        if (isset($where['sex']) && $where['sex'] !== '' && in_array($where['sex'], [0, 1, 2])) {
            $model = $model->where($wechatUserAlias . 'sex', $where['sex']);
        }
        //Tỉnh hiện tại
        if (isset($where['province']) && $where['province']) {
            $model = $model->where($wechatUserAlias . 'province', $where['province']);
        }
        //Thành phố hiện tại
        if (isset($where['city']) && $where['city']) {
            $model = $model->where($wechatUserAlias . 'city', $where['city']);
        }

        if (isset($where['time'])) {
            $model->withSearch(['time'], ['time' => $where['time'], 'timeKey' => 'u.add_time']);
        }

        if (isset($where['is_del'])) {
            $model->where($userAlias . 'is_del', $where['is_del']);
        }

        if (isset($where['ids']) && count($where['ids'])) {
            $model->whereIn($userAlias . 'uid', $where['ids']);
        }

        if (isset($where['agent_level']) && $where['agent_level'] != '') {
            $model->where($userAlias . 'agent_level', $where['agent_level']);
        }

        return $field ? $model->field($field) : $model;
    }

    /**
     * Lấy giới tính người dùng
     * @param $time
     * @param $userType
     * @return mixed
     */
    public function getSex($time, $userType)
    {
        return $this->getModel()->when($userType != '', function ($query) use ($userType) {
            $query->where($this->join_alis . '.user_type', $userType);
        })->where(function ($query) use ($time) {
            if ($time[0] == $time[1]) {
                $query->whereDay($this->join_alis . '.add_time', $time[0]);
            } else {
                $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                $query->whereTime($this->join_alis . '.add_time', 'between', $time);
            }
        })->field('count(' . $this->alias . '.uid) as value,' . $this->join_alis . '.sex as name')
            ->group($this->join_alis . '.sex')->select()->toArray();
    }
}
