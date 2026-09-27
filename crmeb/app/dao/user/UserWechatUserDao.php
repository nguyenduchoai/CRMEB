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
     * Thiết lập điều kiện tìm kiếm
     * @param $where array Mảng điều kiện
     * @param array|null $field Các trường cần truy vấn
     * @return \crmeb\basic\BaseModel
     */
    public function searchWhere($where, ?array $field = [])
    {
        $model = $this->getModel();
        $userAlias = $this->alias . '.';
        $wechatUserAlias = $this->join_alis . '.';
        
        // --- Module lọc theo thời gian ---
        // Loại lọc: visitno(chưa truy cập), visit(thời gian truy cập), add_time(thời gian đăng ký)
        if (isset($where['user_time_type']) && isset($where['user_time'])) {
            // Lọc người dùng không truy cập trong khoảng thời gian chỉ định
            if ($where['user_time_type'] == 'visitno' && $where['user_time'] != '') {
                list($startTime, $endTime) = explode('-', $where['user_time']);
                if ($startTime && $endTime) {
                    $endTime = strtotime($endTime) + 24 * 3600;
                    // last_time nhỏ hơn thời gian bắt đầu hoặc lớn hơn thời gian kết thúc (tức là không truy cập trong khoảng thời gian đó)
                    $model = $model->where($userAlias . "last_time < " . strtotime($startTime) . " OR " . $userAlias . "last_time > " . $endTime);
                }
            }
            // Lọc người dùng đã truy cập trong khoảng thời gian chỉ định
            if ($where['user_time_type'] == 'visit' && $where['user_time'] != '') {
                list($startTime, $endTime) = explode('-', $where['user_time']);
                if ($startTime && $endTime) {
                    $model = $model->where($userAlias . 'last_time', '>', strtotime($startTime));
                    $model = $model->where($userAlias . 'last_time', '<', strtotime($endTime) + 24 * 3600);
                }
            }
            // Lọc người dùng đăng ký trong khoảng thời gian chỉ định
            if ($where['user_time_type'] == 'add_time' && $where['user_time'] != '') {
                list($startTime, $endTime) = explode('-', $where['user_time']);
                if ($startTime && $endTime) {
                    $model = $model->where($userAlias . 'add_time', '>', strtotime($startTime));
                    $model = $model->where($userAlias . 'add_time', '<', strtotime($endTime) + 24 * 3600);
                }
            }
        }

        // --- Lọc theo số lần mua (một giá trị) ---
        // pay_count: -1(0 lần), giá trị khác(lớn hơn giá trị đó)
        if (isset($where['pay_count']) && $where['pay_count'] != '') {
            if ($where['pay_count'] == '-1') {
                $model = $model->where($userAlias . 'pay_count', 0);
            } else {
                $model = $model->where($userAlias . 'pay_count', '>', $where['pay_count']);
            }
        }

        // --- Lọc theo số lần mua (khoảng) ---
        // pay_count_num: [min, max]
        if (isset($where['pay_count_num']) && count($where['pay_count_num']) == 2) {
            if ($where['pay_count_num'][0] != '' && $where['pay_count_num'][1] != '') {
                // Truy vấn theo khoảng
                $model = $model->whereBetween($userAlias . 'pay_count', $where['pay_count_num']);
            } elseif ($where['pay_count_num'][0] != '' && $where['pay_count_num'][1] == '') {
                // Lớn hơn giá trị nhỏ nhất
                $model = $model->where($userAlias . 'pay_count', '>', $where['pay_count_num'][0]);
            } elseif ($where['pay_count_num'][0] == '' && $where['pay_count_num'][1] != '') {
                // Nhỏ hơn giá trị lớn nhất
                $model = $model->where($userAlias . 'pay_count', '<', $where['pay_count_num'][1]);
            }
        }

        // --- Lọc theo số tiền chi tiêu (khoảng) ---
        // pay_count_money: [min, max] thống kê số tiền thực thanh toán trong bảng store_order
        if (isset($where['pay_count_money']) && count($where['pay_count_money']) == 2) {
            $min = $where['pay_count_money'][0];
            $max = $where['pay_count_money'][1];
            
            if ($min !== '' || $max !== '') {
                $model = $model->where(function ($query) use ($userAlias, $min, $max) {
                    // Truy vấn con: tìm các bản ghi thỏa điều kiện trong bảng store_order
                    $query->whereExists(function ($q) use ($userAlias, $min, $max) {
                        $q->name('store_order')
                            ->whereColumn('uid', $userAlias . 'uid')
                            ->where('paid', 1) // Đã thanh toán
                            ->where('refund_status', 0); // Chưa hoàn tiền
                        
                        // Lọc pay_price theo điều kiện khoảng
                        if ($min !== '' && $max !== '') {
                            $q->whereBetween('pay_price', [$min, $max]);
                        } elseif ($min !== '' && $max === '') {
                            $q->where('pay_price', '>', $min);
                        } elseif ($min === '' && $max !== '') {
                            $q->where('pay_price', '<', $max);
                        }
                    });
                    
                    // Xử lý đặc biệt: nếu giá trị nhỏ nhất là 0 hoặc rỗng thì cần bao gồm cả người dùng không có bản ghi đơn hàng (tức người dùng có số tiền chi tiêu bằng 0)
                    if ($min === '' || $min == 0) {
                        $query->whereOr(function ($q) use ($userAlias) {
                            $q->whereNotExists(function ($sub) use ($userAlias) {
                                $sub->name('store_order')
                                    ->whereColumn('uid', $userAlias . 'uid')
                                    ->where('paid', 1)
                                    ->where('refund_status', 0);
                            });
                        });
                    }
                });
            }
        }

        // --- Lọc theo số lần nạp tiền (khoảng) ---
        // recharge_count: [min, max] thống kê số bản ghi trong bảng user_recharge
        if (isset($where['recharge_count']) && count($where['recharge_count']) == 2) {
            $min = $where['recharge_count'][0];
            $max = $where['recharge_count'][1];
            
            if ($min !== '' || $max !== '') {
                $model = $model->where(function ($query) use ($userAlias, $min, $max) {
                    // Truy vấn con: thống kê số bản ghi nạp tiền bằng cách nhóm (group)
                    $query->whereExists(function ($q) use ($userAlias, $min, $max) {
                        $q->name('user_recharge')
                            ->whereColumn('uid', $userAlias . 'uid')
                            ->field('uid')
                            ->group('uid')
                            ->having('COUNT(*) BETWEEN ' . (int)$min . ' AND ' . (int)$max);
                    });
                    
                    // Xử lý đặc biệt: bao gồm cả người dùng không có bản ghi nạp tiền
                    if ($min === '' || $min == 0) {
                        $query->whereOr(function ($q) use ($userAlias) {
                            $q->whereNotExists(function ($sub) use ($userAlias) {
                                $sub->name('user_recharge')
                                    ->whereColumn('uid', $userAlias . 'uid');
                            });
                        });
                    }
                });
            }
        }

        // --- Lọc theo số dư (khoảng) ---
        // balance: [min, max]
        if (isset($where['balance']) && count($where['balance']) == 2) {
            if ($where['balance'][0] != '' && $where['balance'][1] != '') {
                $model = $model->whereBetween($userAlias . 'now_money', $where['balance']);
            } elseif ($where['balance'][0] != '' && $where['balance'][1] == '') {
                $model = $model->where($userAlias . 'now_money', '>', $where['balance'][0]);
            } elseif ($where['balance'][0] == '' && $where['balance'][1] != '') {
                $model = $model->where($userAlias . 'now_money', '<', $where['balance'][1]);
            }
        }

        // --- Lọc theo điểm thưởng (khoảng) ---
        // integral: [min, max]
        if (isset($where['integral']) && count($where['integral']) == 2) {
            if ($where['integral'][0] != '' && $where['integral'][1] != '') {
                $model = $model->whereBetween($userAlias . 'integral', $where['integral']);
            } elseif ($where['integral'][0] != '' && $where['integral'][1] == '') {
                $model = $model->where($userAlias . 'integral', '>', $where['integral'][0]);
            } elseif ($where['integral'][0] == '' && $where['integral'][1] != '') {
                $model = $model->where($userAlias . 'integral', '<', $where['integral'][1]);
            }
        }

        // --- Lọc theo thuộc tính cơ bản ---
        // Hạng người dùng
        if (isset($where['level']) && $where['level']) {
            $model = $model->where($userAlias . 'level', $where['level']);
        }
        // Nhóm người dùng
        if (isset($where['group_id']) && $where['group_id']) {
            $model = $model->where($userAlias . 'group_id', $where['group_id']);
        }
        // Trạng thái người dùng
        if (isset($where['status']) && $where['status'] != '') {
            $model = $model->where($userAlias . 'status', $where['status']);
        }
        // Có phải cộng tác viên không
        if (isset($where['is_promoter']) && $where['is_promoter'] != '') {
            $model = $model->where($userAlias . 'is_promoter', $where['is_promoter']);
        }
        
        // --- Lọc theo nhãn ---
        // label_id: một ID hoặc mảng ID/chuỗi phân cách bằng dấu phẩy
        if (isset($where['label_id']) && $where['label_id']) {
            $model = $model->whereIn($userAlias . 'uid', function ($query) use ($where) {
                if (is_array($where['label_id'])) {
                    $label_ids = array_map('intval', $where['label_id']);
                    $query->name('user_label_relation')->whereIn('label_id', $label_ids)->field('uid')->select();
                } else {
                    if (strpos($where['label_id'], ',') !== false) {
                        $label_ids = array_map('intval', explode(',', $where['label_id']));
                        $query->name('user_label_relation')->whereIn('label_id', $label_ids)->field('uid')->select();
                    } else {
                        $query->name('user_label_relation')->where('label_id', (int)$where['label_id'])->field('uid')->select();
                    }
                }
            });
        }
        
        // --- Lọc theo trạng thái thành viên ---
        // isMember: 0(không phải thành viên), 1(thành viên)
        if (isset($where['isMember']) && $where['isMember'] != '') {
            if ($where['isMember'] == 0) {
                $model = $model->where($userAlias . 'is_money_level', 0);
            } else {
                $model = $model->where($userAlias . 'is_money_level', '>', 0);
            }
        }

        // --- Tìm kiếm theo từ khóa ---
        // field_key: chỉ định trường tìm kiếm (nickname, phone, uid)
        // nickname: từ khóa tìm kiếm
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
            // Khi không chỉ định trường, tìm kiếm mờ theo biệt danh, UID hoặc số điện thoại
            $model = $model->where($userAlias . 'nickname|' . $userAlias . 'uid|' . $userAlias . 'phone', 'LIKE', "%$where[nickname]%");
        }

        // --- Lọc theo khu vực ---
        // country: domestic(trong nước), abroad(nước ngoài)
        if (isset($where['country']) && $where['country']) {
            if ($where['country'] == 'domestic') {
                $model = $model->where($wechatUserAlias . 'country', 'in', ['Trung Quốc', 'China']);
            } else if ($where['country'] == 'abroad') {
                $model = $model->where($wechatUserAlias . 'country', 'not in', ['Trung Quốc', '']);
            }
        }
        
        // --- Lọc theo loại client ---
        // user_type: app, wechat, routine, v.v.
        if (isset($where['user_type']) && $where['user_type']) {
            if ($where['user_type'] == 'app') {
                $model = $model->whereIn($userAlias . 'user_type', ['app', 'apple']);
            } else {
                $model = $model->where($userAlias . 'user_type', $where['user_type']);
            }
        }

        // --- Lọc theo giới tính ---
        // sex: 1(nam), 2(nữ), 0(không rõ)
        if (isset($where['sex']) && $where['sex'] !== '' && in_array($where['sex'], [0, 1, 2])) {
            $model = $model->where($wechatUserAlias . 'sex', $where['sex']);
        }
        
        // --- Lọc theo tỉnh ---
        if (isset($where['province']) && $where['province']) {
            $model = $model->where($wechatUserAlias . 'province', $where['province']);
        }
        
        // --- Lọc theo thành phố ---
        if (isset($where['city']) && $where['city']) {
            $model = $model->where($wechatUserAlias . 'city', $where['city']);
        }

        // --- Lọc theo thời gian chung ---
        // Dùng bộ lọc tìm kiếm time của model
        if (isset($where['time'])) {
            $model->withSearch(['time'], ['time' => $where['time'], 'timeKey' => 'u.add_time']);
        }

        // --- Lọc theo trạng thái xóa ---
        if (isset($where['is_del'])) {
            $model->where($userAlias . 'is_del', $where['is_del']);
        }

        // --- Lọc theo ID chỉ định ---
        if (isset($where['ids']) && count($where['ids'])) {
            $model->whereIn($userAlias . 'uid', $where['ids']);
        }

        // --- Lọc theo cấp độ đại lý ---
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
