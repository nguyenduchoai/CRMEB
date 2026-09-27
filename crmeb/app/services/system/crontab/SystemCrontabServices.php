<?php

namespace app\services\system\crontab;

use app\dao\system\crontab\SystemCrontabDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use think\facade\Cache;
use think\helper\Str;
use Workerman\Crontab\Crontab;

class SystemCrontabServices extends BaseServices
{
    public function __construct(SystemCrontabDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách tác vụ định kỳ
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTimerList(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit, 'id desc', [], true);
        foreach ($list as &$item) {
            $item['next_execution_time'] = date('Y-m-d H:i:s', $item['next_execution_time']);
            $item['last_execution_time'] = $item['last_execution_time'] != 0 ? date('Y-m-d H:i:s', $item['last_execution_time']) : 'Chưa thực thi';
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Chi tiết tác vụ định kỳ
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTimerInfo($id)
    {
        $info = $this->dao->get($id);
        $info['customCode'] = "<?php\n\n" . json_decode($info['customCode']);
        if (!$info) throw new AdminException(100026);
        return $info->toArray();
    }

    /**
     * Loại tác vụ định kỳ
     * @return string[]
     */
    public function getMarkList(): array
    {
        return app()->make(CrontabRunServices::class)->markList;
    }

    /**
     * Lưu tác vụ định kỳ
     * @param array $data
     * @return bool
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveTimer(array $data = [])
    {
        if (!$data['id'] && $this->dao->getCount(['mark' => $data['mark'], 'is_del' => 0]) && $data['mark'] != 'customTimer') {
            throw new AdminException('Tác vụ định kỳ này đã tồn tại, vui lòng không thêm trùng lặp');
        }
        if ($data['mark'] != 'customTimer') $data['name'] = $this->getMarkList()[$data['mark']];
        $data['customCode'] = json_encode(preg_replace('/<\?php\s*\n/', '', $data['customCode']));
        $data['timeStr'] = $this->getTimerStr([
            'type' => $data['type'],
            'month' => $data['month'],
            'week' => $data['week'],
            'day' => $data['day'],
            'hour' => $data['hour'],
            'minute' => $data['minute'],
            'second' => $data['second'],
        ]);
        if (!$data['id']) {
            unset($data['id']);
            $data['add_time'] = $data['update_time'] = time();
            $res = $this->dao->save($data);
        } else {
            $data['update_time'] = time();
            $res = $this->dao->update(['id' => $data['id']], $data);
        }
        if (!$res) throw new AdminException(100006);
        Cache::delete('crontabCache');
        Cache::set('crontabCache', $this->dao->selectList(['is_open' => 1, 'is_del' => 0])->toArray());
        return true;
    }

    /**
     * Xóa tác vụ định kỳ
     * @param $id
     * @return bool
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delTimer($id)
    {
        $data['update_time'] = time();
        $data['is_del'] = 1;
        $res = $this->dao->update(['id' => $id], $data);
        if (!$res) throw new AdminException(100008);
        Cache::delete('crontabCache');
        Cache::set('crontabCache', $this->dao->selectList(['is_open' => 1, 'is_del' => 0])->toArray());
        return true;
    }

    /**
     * Thiết lập trạng thái tác vụ định kỳ
     * @param $id
     * @param $is_open
     * @return bool
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setTimerStatus($id, $is_open)
    {
        $data['update_time'] = time();
        $data['is_open'] = $is_open;
        $res = $this->dao->update(['id' => $id], $data);
        if (!$res) throw new AdminException(100014);
        Cache::delete('crontabCache');
        Cache::set('crontabCache', $this->dao->selectList(['is_open' => 1, 'is_del' => 0])->toArray());
        return true;
    }

    /**
     * Tính thời gian thực thi lần sau của tác vụ định kỳ
     * @param $data
     * @param int $time
     * @return false|float|int|mixed
     */
    public function getTimerCycleTime($data, $time = 0)
    {
        if (!$time) $time = time();
        switch ($data['type']) {
            case 1: // Cách mấy giây
                $cycle_time = $time + $data['second'];
                break;
            case 2: // Cách mấy phút
                $cycle_time = $time + ($data['minute'] * 60);
                break;
            case 3: // Cách mấy giờ
                $cycle_time = $time + ($data['hour'] * 3600) + ($data['minute'] * 60);
                break;
            case 4: // Cách mấy ngày
                $cycle_time = $time + ($data['day'] * 86400) + ($data['hour'] * 3600) + ($data['minute'] * 60);
                break;
            case 5: // Mỗi ngày vào giờ:phút:giây nào
                $cycle_time = strtotime(date('Y-m-d ' . $data['hour'] . ':' . $data['minute'] . ':' . $data['second'], time()));
                if ($time >= $cycle_time) {
                    $cycle_time = $cycle_time + 86400;
                }
                break;
            case 6: // Mỗi tuần vào thứ mấy, giờ:phút:giây nào
                $todayStart = strtotime(date('Y-m-d 00:00:00', time()));
                $w = date("w");
                if ($w > $data['week']) {
                    $cycle_time = $todayStart + ((7 - $w + $data['week']) * 86400) + ($data['hour'] * 3600) + ($data['minute'] * 60) + $data['second'];
                } else if ($w == $data['week']) {
                    $cycle_time = $todayStart + ($data['hour'] * 3600) + ($data['minute'] * 60) + $data['second'];
                    if ($time >= $cycle_time) {
                        $cycle_time = $cycle_time + (7 * 86400);
                    }
                } else {
                    $cycle_time = $todayStart + (($data['week'] - $w) * 86400) + ($data['hour'] * 3600) + ($data['minute'] * 60) + $data['second'];
                }
                break;
            case 7: // Mỗi tháng vào ngày mấy, giờ:phút:giây nào
                $currentMonth = date("n");
                $currentYear = date("Y");
                if ($currentMonth == 12) {
                    $nextMonth = 1;
                    $nextYear = $currentYear + 1;
                } else {
                    $nextMonth = $currentMonth + 1;
                    $nextYear = $currentYear;
                }
                $cycle_time = mktime($data['hour'], $data['minute'], $data['second'], $nextMonth, $data['day'], $nextYear);
                break;
            case 8: // Mỗi năm vào tháng mấy ngày mấy, giờ:phút:giây nào
                $cycle_time = mktime($data['hour'], $data['minute'], $data['second'], $data['month'], $data['day'], date("Y") + 1);
                break;
            default:
                $cycle_time = 0;
                break;
        }
        return $cycle_time;
    }

    /**
     * Interface thực thi tác vụ
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/17
     */
    public function crontabApiRun()
    {
        $crontabRunServices = app()->make(CrontabRunServices::class);
        $time = time();
        file_put_contents(root_path() . 'runtime/.timer', $time); //Kiểm tra tác vụ định kỳ có bình thường không
        $list = $this->dao->selectList(['is_open' => 1, 'is_del' => 0])->toArray();
        foreach ($list as $item) {
            if ($item['next_execution_time'] < $time) {
                //Chuyển tên phương thức sang camelCase
                $functionName = Str::camel($item['mark']);
                //Thực thi tác vụ định kỳ
                if (strpos($functionName, 'customTimer') === 0) {
                    $crontabRunServices->customTimer(json_decode($item['customCode']));
                } else {
                    $crontabRunServices->$functionName();
                }
                //Ghi thời gian thực thi lần này và lần sau
                $this->dao->update(['mark' => $item['mark']], ['last_execution_time' => $time, 'next_execution_time' => $this->getTimerCycleTime($item)]);
            }
        }
    }

    /**
     * Chạy tác vụ định kỳ
     *
     * @param object $task Đối tượng tác vụ
     * @return void
     */
    public function crontabCommandRun($task)
    {
        file_put_contents(root_path() . 'runtime/.timer', time());
        // Lấy instance của CrontabRunServices
        $crontabRunServices = app()->make(CrontabRunServices::class);
        // Tạo một tác vụ định kỳ thực thi mỗi giây một lần
        new Crontab('*/1 * * * * *', function () use ($task, $crontabRunServices) {
            // Ghi timestamp, dùng để kiểm tra tác vụ định kỳ có thực thi bình thường không
            $timerTime = file_get_contents(root_path() . 'runtime/.timer');
            if ($timerTime < (time() - 60)) {
                file_put_contents(root_path() . 'runtime/.timer', time());
            }
            // Lấy danh sách tác vụ định kỳ từ cache
            $list = Cache::get('crontabCache');
            if (!$list) {
                $list = $this->dao->selectList(['is_open' => 1, 'is_del' => 0])->toArray();
                Cache::set('crontabCache', $list);
            }
            // Duyệt qua danh sách tác vụ định kỳ
            foreach ($list as &$item) {
                // Lấy tên hàm
                $functionName = Str::camel($item['mark']);
                if ($functionName == 'customTimer') {
                    $functionName = 'customTimer_' . $item['id'];
                }
                // Nếu thời gian cập nhật không tồn tại thì đặt bằng thời gian thêm
                $item['update_time'] = $item['update_time'] ?: $item['add_time'];
                // Nếu tác vụ đã được thực thi thì bỏ qua vòng lặp này
                if (isset($task->task_ids[$functionName]) && $task->task_ids[$functionName]['time'] == $item['update_time']) {
                    continue;
                }
                // Lấy chuỗi bộ đếm thời gian
                $timeStr = $item['timeStr'] != '' ? $item['timeStr'] : $this->getTimerStr($item);
                // Lấy mã tùy chỉnh
                $customCode = json_decode($item['customCode']);
                // Nếu tác vụ đã được thực thi và thời gian hiện tại khác thời gian thực thi lần trước thì hủy tác vụ định kỳ trước đó
                if (isset($task->task_ids[$functionName]) && $task->task_ids[$functionName]['time'] != $item['update_time'] && isset($task->task_ids[$functionName]['crontab']) && $task->task_ids[$functionName]['crontab'] instanceof Crontab) {
                    $task->task_ids[$functionName]['crontab']->destroy();
                    unset($task->task_ids[$functionName]);
                }
                // Nếu tác vụ đang ở trạng thái mở thì tạo một tác vụ định kỳ mới
                if ($item['is_open'] == 1) {
                    $crontab = new Crontab($timeStr, function () use ($crontabRunServices, $functionName, $customCode) {
                        // Theo tên hàm, gọi phương thức tương ứng
                        if (strpos($functionName, 'customTimer_') === 0) {
                            $crontabRunServices->customTimer($customCode);
                        } else {
                            $crontabRunServices->$functionName();
                        }
                    });
                    $task->task_ids[$functionName] = ['crontab' => $crontab, 'time' => $item['update_time']];
                }
            }
        });
    }

    /**
     * Lấy biểu thức thời gian của tác vụ định kỳ
     * 0   1   2   3   4   5
     * |   |   |   |   |   |
     * |   |   |   |   |   +------ day of week (0 - 6) (Sunday=0)
     * |   |   |   |   +------ month (1 - 12)
     * |   |   |   +-------- day of month (1 - 31)
     * |   |   +---------- hour (0 - 23)
     * |   +------------ min (0 - 59)
     * +-------------- sec (0-59)[có thể bỏ qua, nếu không có vị trí 0 thì độ chi tiết thời gian nhỏ nhất là phút]
     * @param $data
     * @return string
     */
    public function getTimerStr($data): string
    {
        $timeStr = '';
        switch ($data['type']) {
            case 1:// Cách mấy giây
                $timeStr = '*/' . $data['second'] . ' * * * * *';
                break;
            case 2:// Cách mấy phút
                $timeStr = '0 */' . $data['minute'] . ' * * * *';
                break;
            case 3:// Cách mấy giờ thì thực thi vào phút thứ mấy
                $timeStr = '0 ' . $data['minute'] . ' */' . $data['hour'] . ' * * *';
                break;
            case 4:// Cách mấy ngày thì thực thi vào giờ thứ mấy phút thứ mấy
                $timeStr = '0 ' . $data['minute'] . ' ' . $data['hour'] . ' */' . $data['day'] . ' * *';
                break;
            case 5:// Mỗi ngày vào giờ:phút:giây nào
                $timeStr = $data['second'] . ' ' . $data['minute'] . ' ' . $data['hour'] . ' * * *';
                break;
            case 6:// Mỗi tuần vào thứ mấy, giờ:phút:giây nào
                $timeStr = $data['second'] . ' ' . $data['minute'] . ' ' . $data['hour'] . ' * * ' . ($data['week'] == 7 ? 0 : $data['week']);
                break;
            case 7:// Mỗi tháng vào ngày mấy, giờ:phút:giây nào
                $timeStr = $data['second'] . ' ' . $data['minute'] . ' ' . $data['hour'] . ' ' . $data['day'] . ' * *';
                break;
            case 8:// Mỗi năm vào tháng mấy ngày mấy, giờ:phút:giây nào
                $timeStr = $data['second'] . ' ' . $data['minute'] . ' ' . $data['hour'] . ' ' . $data['day'] . ' ' . $data['month'] . ' *';
                break;
        }
        return $timeStr;
    }
}
