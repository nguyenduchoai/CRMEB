# Tài liệu phát triển tiếp thị liên kết của hệ thống

## 1. Tổng quan cơ chế tiếp thị liên kết

Hệ thống CRMEB triển khai đầy đủ chức năng tiếp thị liên kết (affiliate), hỗ trợ các chức năng cốt lõi như tiếp thị liên kết 2 cấp, hạng CTV, tính hoa hồng, xét duyệt đơn đăng ký CTV. Hệ thống tiếp thị liên kết dựa trên chuỗi quan hệ giới thiệu để thực hiện việc giới thiệu và phân chia hoa hồng giữa những người dùng với nhau, giúp người bán mở rộng kênh bán hàng và tăng mức độ gắn bó của người dùng.

### 1.1 Chức năng cốt lõi

- **Tiếp thị liên kết 2 cấp**: Hỗ trợ quan hệ tiếp thị liên kết cấp 1 và cấp 2, có thể cấu hình số cấp tiếp thị liên kết
- **Hạng CTV**: Hỗ trợ nhiều hạng CTV, mỗi hạng được hưởng tỷ lệ hoa hồng khác nhau
- **Tính hoa hồng**: Hỗ trợ tính hoa hồng theo tỷ lệ trên giá bán sản phẩm, có thể cấu hình tỷ lệ hoa hồng cấp 1 và cấp 2
- **Trả hoa hồng khi tự mua**: Hỗ trợ chức năng trả hoa hồng khi người dùng tự mua, có thể cấu hình bật hay tắt
- **Đăng ký CTV**: Hỗ trợ người dùng đăng ký trở thành CTV, cần quản trị viên duyệt
- **Quản lý quan hệ tiếp thị liên kết**: Hỗ trợ xem và quản lý chuỗi quan hệ tiếp thị liên kết của người dùng
- **Rút tiền hoa hồng**: Hỗ trợ CTV gửi yêu cầu rút tiền hoa hồng, quản trị viên duyệt xong sẽ chi trả

## 2. Thành phần cốt lõi

### 2.1 Mô hình dữ liệu

- **AgentLevel**: Model hạng CTV, định nghĩa thông tin hạng CTV và tỷ lệ hoa hồng
- **AgentLevelTask**: Model nhiệm vụ hạng CTV, định nghĩa các nhiệm vụ cần hoàn thành để lên hạng
- **AgentLevelTaskRecord**: Model bản ghi nhiệm vụ hạng CTV, ghi lại tình hình hoàn thành nhiệm vụ của người dùng
- **User**: Model người dùng, được mở rộng thêm các trường liên quan đến tiếp thị liên kết (spread_uid, agent_level, v.v.)
- **StoreOrder**: Model đơn hàng, ghi lại thông tin hoa hồng của đơn hàng
- **UserExtract**: Model rút tiền của người dùng, ghi lại yêu cầu rút tiền của người dùng
- **UserBrokerage**: Model hoa hồng của người dùng, ghi lại lịch sử giao dịch hoa hồng của người dùng

### 2.2 Tầng dịch vụ

- **AgentLevelServices**: Dịch vụ hạng CTV, xử lý thêm/xóa/sửa/tra cứu hạng CTV và logic lên hạng
- **AgentManageServices**: Dịch vụ quản lý tiếp thị liên kết, xử lý việc quản lý và thống kê CTV
- **UserServices**: Dịch vụ người dùng, được mở rộng thêm các chức năng liên quan đến tiếp thị liên kết
- **StoreOrderCreateServices**: Dịch vụ tạo đơn hàng, xử lý việc tính hoa hồng cho đơn hàng

### 2.3 Controller

- **AgentManage**: Controller quản lý CTV, cung cấp chức năng danh sách và thống kê CTV
- **AgentLevel**: Controller hạng CTV, cung cấp chức năng quản lý hạng CTV
- **SpreadApply**: Controller đăng ký CTV, xử lý đơn đăng ký CTV của người dùng

## 3. Cấu hình tiếp thị liên kết

### 3.1 Các mục cấu hình hệ thống

Các mục cấu hình cốt lõi của chức năng tiếp thị liên kết được quản lý trong cấu hình hệ thống, chủ yếu gồm:

| Mục cấu hình | Mô tả | Giá trị mặc định |
|-------|------|-------|
| brokerage_func_status | Bật/tắt tính năng CTV | 0 |
| store_brokerage_ratio | Tỷ lệ hoa hồng tiếp thị liên kết cấp 1 | 0 |
| store_brokerage_two | Tỷ lệ hoa hồng tiếp thị liên kết cấp 2 | 0 |
| brokerage_level | Số cấp tiếp thị liên kết (1 hoặc 2) | 2 |
| store_brokerage_statu | Bật/tắt đăng ký CTV | 0 |
| store_brokerage_binding_status | Chế độ liên kết CTV | 0 |
| is_self_brokerage | Có mở trả hoa hồng tự mua không | 0 |

### 3.2 Ví dụ cấu hình

```php
// Lấy trạng thái bật/tắt chức năng tiếp thị liên kết
sys_config('brokerage_func_status');

// Lấy tỷ lệ hoa hồng CTV cấp 1
sys_config('store_brokerage_ratio');

// Lấy tỷ lệ hoa hồng CTV cấp 2
sys_config('store_brokerage_two');
```

## 4. Cấp độ CTV

### 4.1 Model hạng

Model hạng CTV định nghĩa thông tin hạng của CTV, bao gồm tỷ lệ hoa hồng, điều kiện lên hạng và các thông tin khác:

```php
class AgentLevel extends BaseModel
{
    protected $pk = 'id';
    protected $name = 'agent_level';
    
    // Liên kết nhiệm vụ theo hạng
    public function task()
    {
        return $this->hasMany(AgentLevelTask::class, 'level_id', 'id')->where('is_del', 0);
    }
}
```

### 4.2 Cơ chế lên hạng

Người dùng có thể nâng hạng CTV bằng cách hoàn thành các nhiệm vụ chỉ định để được hưởng tỷ lệ hoa hồng cao hơn:

```php
// Kiểm tra tình hình hoàn thành nhiệm vụ hạng của người dùng
public function checkUserLevelFinish($uid)
{
    // Lấy hạng hiện tại của người dùng
    $userInfo = app()->make(UserServices::class)->getUserInfo($uid);
    if (!$userInfo) return false;
    
    // Lấy thông tin hạng tiếp theo
    $levelList = $this->dao->getList(['is_del' => 0, 'status' => 1], '*', [], 0, 0, $userInfo['agent_level']);
    
    // Kiểm tra tình hình hoàn thành nhiệm vụ
    foreach ($levelList as $levelInfo) {
        // Lấy nhiệm vụ hạng
        $task_list = $levelInfo['task'] ?? [];
        
        // Kiểm tra từng nhiệm vụ đã hoàn thành chưa
        foreach ($task_list as $task) {
            $levelTaskServices->checkLevelTaskFinish($uid, (int)$task['id'], $task);
        }
        
        // Đếm số nhiệm vụ đã hoàn thành
        $finish_task = $levelTaskRecordServices->count([
            'level_id' => $levelInfo['id'], 
            'uid' => $uid, 
            'task_id' => array_column($task_list, 'id')
        ]);
        
        // Hoàn thành nhiệm vụ thì nâng hạng
        if ($finish_task >= $levelInfo['task_num']) {
            $userServices->update($uid, ['agent_level' => $levelInfo['id']]);
        }
    }
}
```

## 5. Tính hoa hồng

### 5.1 Logic tính hoa hồng

Việc tính hoa hồng được thực hiện khi tạo đơn hàng, dựa trên giá bán sản phẩm và tỷ lệ hoa hồng:

```php
public function computeOrderProductTruePrice($cartInfo, $priceData, $addressId, $uid, $orderInfo)
{
    // Lấy tỷ lệ hoa hồng và những người nhận hoa hồng
    [$storeBrokerageRatio, $storeBrokerageTwo, $spread_one_uid, $spread_two_uid] = $this->getSpreadDate($uid);
    
    // Tính hoa hồng
    if ($storeBrokerageRatio > 0 || $storeBrokerageTwo > 0 || $staffPercent > 0 || $agentPercent > 0 || $divisionPercent > 0) {
        // Tính giá bán sản phẩm
        $price = bcsub((string)$cart['truePrice'], (string)$cart['truePostage'], 2);
        
        // Tính hoa hồng cấp 1
        if ($storeBrokerageRatio > 0) {
            $brokerageRatio = bcdiv($storeBrokerageRatio, 100, 4);
            $oneBrokerage = bcmul((string)$price, (string)$brokerageRatio, 2);
        }
        
        // Tính hoa hồng cấp 2
        if ($storeBrokerageTwo > 0) {
            $brokerageTwo = bcdiv($storeBrokerageTwo, 100, 4);
            $twoBrokerage = bcmul((string)$price, (string)$brokerageTwo, 2);
        }
        
        // Ghi nhận thông tin hoa hồng
        $cart['one_brokerage'] = $oneBrokerage;
        $cart['two_brokerage'] = $twoBrokerage;
    }
    
    return [$cartInfo, [$spread_one_uid, $spread_two_uid]];
}
```

### 5.2 Kiểm soát số cấp tiếp thị liên kết

Hệ thống hỗ trợ cấu hình số cấp tiếp thị liên kết (1 hoặc 2 cấp), có thể điều chỉnh cách tính hoa hồng theo cấu hình:

```php
// Nếu tầng trả hoa hồng là cấp 1 thì đổi uid người dùng cấp 2 và tỷ lệ phân hoa hồng cấp 2 thành 0
if (sys_config('brokerage_level') == 1) {
    $storeBrokerageTwo = $spread_two_uid = 0;
}
```

### 5.3 Trả hoa hồng khi tự mua

Hệ thống hỗ trợ cấu hình chức năng trả hoa hồng khi người dùng tự mua:

```php
// Lấy uid của cấp trên và cấp trên của cấp trên, nếu mở tự mua thì lấy uid của bản thân và cấp trên
$spread_one_uid = $userServices->getSpreadUid($uid, $userInfo);
```

## 6. Đăng ký CTV và xét duyệt

### 6.1 Đăng ký CTV

Người dùng có thể đăng ký trở thành CTV, thông tin đăng ký được lưu trong cơ sở dữ liệu để chờ quản trị viên duyệt:

```php
// API đăng ký làm CTV
public function apply()
{
    // Kiểm tra chức năng tiếp thị liên kết đã bật chưa
    if (!sys_config('brokerage_func_status')) {
        throw new ApiException(100209);
    }
    
    // Kiểm tra đã là CTV chưa
    if ($this->services->checkUserPromoter($this->uid)) {
        throw new ApiException(100210);
    }
    
    // Gửi đăng ký
    $this->services->applySpread($this->uid, $this->request->post());
    
    return app('json')->success(100211);
}
```

### 6.2 Xét duyệt đơn đăng ký

Quản trị viên có thể duyệt đơn đăng ký làm CTV của người dùng, sau khi được duyệt người dùng sẽ trở thành cộng tác viên:

```php
// API duyệt đơn đăng ký CTV
public function applyExamine($id, $uid, $status)
{
    // Kiểm tra tham số
    if (!$id || !$uid || !in_array($status, [0, 1])) {
        throw new AdminException(100026);
    }
    
    // Duyệt đơn đăng ký
    $this->services->examineSpreadApply($id, $uid, $status);
    
    return app('json')->success(100014);
}
```

## 7. Quản lý quan hệ tiếp thị liên kết

### 7.1 Liên kết quan hệ giới thiệu

Khi người dùng đăng ký thông qua liên kết chia sẻ hoặc mã QR, hệ thống sẽ tự động liên kết quan hệ giới thiệu:

```php
// Lưu thông tin người dùng và liên kết quan hệ giới thiệu
public function setUserInfo($user, int $spreadUid = 0, string $userType = 'wechat')
{
    $data = [
        // Thông tin cơ bản của người dùng
        'nickname' => $user['nickname'] ?? '',
        'avatar' => $user['headimgurl'] ?? '',
        // ... Các trường khác
    ];
    
    // Liên kết quan hệ giới thiệu
    if ($spreadUid) {
        $data['spread_uid'] = $spreadUid;
        $data['spread_time'] = time();
    }
    
    // Lưu thông tin người dùng
    $res = $this->dao->save($data);
    
    // Kích hoạt sự kiện đăng ký người dùng
    event('UserRegisterListener', [$spreadUid, $userType, $user['nickname'], $res->uid, 1]);
    
    return $res;
}
```

### 7.2 Tra cứu quan hệ giới thiệu

Hệ thống cung cấp API để tra cứu chuỗi quan hệ giới thiệu của người dùng:

```php
// Lấy người dùng được giới thiệu cấp 1 và cấp 2 của người dùng
public function getSpreadList(int $uid, array $where, bool $is_page = true)
{
    // Lấy người dùng được giới thiệu cấp 1
    $one_list = $this->dao->getSpreadUserList($where, $uid, 1, $is_page);
    
    // Lấy người dùng được giới thiệu cấp 2
    $two_list = $this->dao->getSpreadUserList($where, $uid, 2, $is_page);
    
    return ['one_list' => $one_list, 'two_list' => $two_list];
}
```

## 8. Nhiệm vụ cấp độ CTV

### 8.1 Loại nhiệm vụ

Hệ thống hỗ trợ nhiều loại nhiệm vụ cấp độ CTV:

- **Số người được giới thiệu**: Giới thiệu được số lượng người dùng chỉ định
- **Giá trị đơn hàng**: Hoàn thành đơn hàng đạt giá trị chỉ định
- **Số lượng đơn hàng**: Hoàn thành đủ số lượng đơn hàng chỉ định
- **Giá trị tự mua**: Tự mua sản phẩm đạt giá trị chỉ định
- **Số lượng tự mua**: Tự mua đủ số lượng sản phẩm chỉ định

### 8.2 Kiểm tra hoàn thành nhiệm vụ

Hệ thống sẽ định kỳ kiểm tra tình trạng hoàn thành nhiệm vụ của người dùng và tự động nâng cấp khi đáp ứng điều kiện:

```php
// Kiểm tra tình hình hoàn thành nhiệm vụ
public function checkLevelTaskFinish($uid, $taskId, $task)
{
    // Kiểm tra tình hình hoàn thành theo loại nhiệm vụ
    switch ($task['type']) {
        case 1: // Số người được giới thiệu
            $count = $this->dao->getSpreadUserCount($uid);
            break;
        case 2: // Số tiền đơn hàng
            $count = $this->dao->getUserOrderAmount($uid);
            break;
        case 3: // Số lượng đơn hàng
            $count = $this->dao->getUserOrderCount($uid);
            break;
        // ... Các loại nhiệm vụ khác
    }
    
    // Cập nhật bản ghi hoàn thành nhiệm vụ
    if ($count >= $task['value']) {
        $this->taskRecordServices->updateTaskRecord($uid, $taskId);
    }
}
```

## 9. Các API

### 9.1 API quản lý cộng tác viên

- **GET /api/admin/agent/manage/index**: Danh sách cộng tác viên
- **GET /api/admin/agent/manage/get_badge**: Thống kê cộng tác viên
- **GET /api/admin/agent/manage/get_stair_list**: Danh sách người được giới thiệu

### 9.2 API cấp độ CTV

- **GET /api/admin/agent/level/index**: Danh sách cấp độ CTV
- **POST /api/admin/agent/level/save**: Thêm cấp độ CTV
- **PUT /api/admin/agent/level/update/:id**: Sửa cấp độ CTV

### 9.3 API đăng ký CTV

- **GET /api/admin/agent/spread/apply/list**: Danh sách đơn đăng ký CTV
- **POST /api/admin/agent/spread/apply/examine/:id/:uid/:status**: Duyệt đơn đăng ký CTV

### 9.4 API phía người dùng

- **POST /api/user/spread/apply**: Người dùng đăng ký trở thành cộng tác viên
- **GET /api/user/spread/list**: Lấy danh sách người dùng được giới thiệu
- **GET /api/user/agent/level_task_list**: Lấy danh sách nhiệm vụ cấp độ CTV

## 10. Thực tiễn tốt nhất

### 10.1 Thiết kế cấp độ CTV

- **Thiết lập số lượng cấp độ hợp lý**: Nên thiết lập 3-5 cấp độ CTV, quá nhiều cấp độ sẽ làm tăng độ phức tạp khi quản lý
- **Thiết lập tỷ lệ hoa hồng theo bậc thang**: Tỷ lệ hoa hồng giữa các cấp độ nên có sự chênh lệch rõ rệt để khuyến khích người dùng nâng cấp
- **Thiết lập điều kiện nâng cấp hợp lý**: Điều kiện nâng cấp cần có tính khả thi, tránh đặt ngưỡng quá cao

### 10.2 Cài đặt hoa hồng

- **Thiết lập tỷ lệ hoa hồng hợp lý**: Thiết lập tỷ lệ hoa hồng hợp lý dựa trên tỷ suất lợi nhuận của sản phẩm, tránh ảnh hưởng đến lợi nhuận của người bán
- **Kiểm soát số cấp tiếp thị liên kết**: Nên dùng tiếp thị liên kết 2 cấp để phù hợp với yêu cầu của các quy định pháp luật liên quan của nhà nước
- **Bật trả hoa hồng tự mua**: Giúp nâng cao tính tích cực mua hàng của người dùng, tăng doanh số

### 10.3 Quản lý quan hệ giới thiệu

- **Làm rõ quy tắc giới thiệu**: Xây dựng quy tắc giới thiệu rõ ràng, tránh tranh chấp giữa các người dùng
- **Định kỳ dọn dẹp quan hệ không hợp lệ**: Định kỳ dọn dẹp các quan hệ giới thiệu lâu ngày không hoạt động, giữ dữ liệu gọn gàng
- **Bảo vệ quyền riêng tư của người dùng**: Khi hiển thị quan hệ giới thiệu, chú ý bảo vệ thông tin riêng tư của người dùng

## 11. Sự cố thường gặp

### 11.1 Tính năng tiếp thị liên kết không có hiệu lực

- Kiểm tra công tắc tính năng tiếp thị liên kết đã được bật chưa
- Kiểm tra tỷ lệ hoa hồng đã được thiết lập đúng chưa
- Kiểm tra người dùng có tư cách cộng tác viên hay không

### 11.2 Tính hoa hồng bị sai

- Kiểm tra thiết lập tỷ lệ hoa hồng có đúng không
- Kiểm tra số tiền đơn hàng có đúng không
- Kiểm tra thiết lập số cấp tiếp thị liên kết có đúng không

### 11.3 Liên kết quan hệ giới thiệu thất bại

- Kiểm tra liên kết giới thiệu có đúng không
- Kiểm tra tham số giới thiệu có được truyền đúng không
- Kiểm tra quy trình đăng ký người dùng có đúng không

### 11.4 Cấp độ CTV không được nâng cấp

- Kiểm tra điều kiện nâng cấp đã được thiết lập đúng chưa
- Kiểm tra tình trạng hoàn thành nhiệm vụ có đúng không
- Kiểm tra logic nâng cấp có tồn tại bug không

## 12. Tổng kết

Tính năng tiếp thị liên kết của hệ thống CRMEB cung cấp giải pháp tiếp thị liên kết 2 cấp hoàn chỉnh, hỗ trợ các chức năng cốt lõi như cấp độ CTV, tính hoa hồng, duyệt đơn đăng ký CTV, v.v. Thông qua việc cấu hình và sử dụng hợp lý tính năng tiếp thị liên kết, người bán có thể mở rộng kênh bán hàng, tăng độ gắn kết của người dùng và thúc đẩy tăng trưởng doanh số. Lập trình viên có thể căn cứ vào nhu cầu nghiệp vụ để mở rộng thêm các tính năng tiếp thị liên kết dựa trên framework hiện có, như tiếp thị liên kết 3 cấp, thưởng đội nhóm, v.v.
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
