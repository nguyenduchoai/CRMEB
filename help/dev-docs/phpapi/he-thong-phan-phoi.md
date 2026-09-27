## 💳 Quy trình rút tiền hoa hồng

### Xử lý yêu cầu rút tiền
```php
public function handleWithdraw($id, $status, $rejectReason = '')
{
    $withdraw = $this->dao->get($id);
    if (!$withdraw) {
        throw new AdminException('Bản ghi rút tiền không tồn tại');
    }

    if ($withdraw['status'] != 0) {
        throw new AdminException('Yêu cầu rút tiền đã được xử lý');
    }

    // Cập nhật trạng thái
    $updateData = [
        'status' => $status,
        'handle_time' => time()
    ];

    if ($status == 2) { // Từ chối
        $updateData['reject_reason'] = $rejectReason;
        $this->dao->update($id, $updateData);

        // Hoàn tiền vào số dư của người dùng
        $this->returnAmountToUser($withdraw);
    } else { // Duyệt
        $this->dao->update($id, $updateData);

        // Thao tác chuyển tiền thực tế
        $this->transferToUser($withdraw);
    }

    // Gửi thông báo
    $this->sendWithdrawResultNotify($withdraw['uid'], $status, $rejectReason);
}
```

### Phương thức chi trả tiền rút
1. **Chi trả thủ công**
   - Quản trị viên chuyển khoản thủ công trên trang quản trị
   - Lưu chứng từ chuyển khoản

2. **Chi trả tự động (cần cấu hình)**
   - Chuyển tiền doanh nghiệp qua WeChat
   - Chuyển khoản qua Alipay
   - Chuyển khoản qua thẻ ngân hàng

## 📊 Thống kê và phân tích dữ liệu

### Báo cáo doanh số tiếp thị liên kết
```sql
SELECT 
    a.uid,
    u.nickname,
    COUNT(DISTINCT o.order_id) AS order_count,
    SUM(o.pay_price) AS order_amount,
    SUM(c.commission_amount) AS total_commission,
    COUNT(DISTINCT s.uid) AS spread_count
FROM 
    eb_user_agent a
LEFT JOIN 
    eb_user u ON a.uid = u.uid
LEFT JOIN 
    eb_store_order o ON o.uid = a.uid AND o.paid = 1
LEFT JOIN 
    eb_agent_commission c ON c.uid = a.uid AND c.status = 1
LEFT JOIN 
    eb_user_spread s ON s.spread_uid = a.uid
GROUP BY 
    a.uid
ORDER BY 
    total_commission DESC
```

### Tra cứu doanh số đội nhóm
```php
public function getTeamPerformance($uid, $startTime, $endTime)
{
    // Lấy ID người dùng trong đội nhóm
    $teamUids = $this->getTeamUids($uid);

    // Tra cứu doanh số đội nhóm
    return $this->dao->getPerformanceByUids(
        $teamUids, 
        $startTime, 
        $endTime
    );
}
```

## 🛠️ API hệ thống tiếp thị liên kết

### Danh sách API cốt lõi
| Tên API | Phương thức yêu cầu | Đường dẫn | Mô tả |
|---------|---------|------|------|
| Đăng ký làm cộng tác viên | POST | /api/agent/apply | Gửi đơn đăng ký tiếp thị liên kết |
| Lấy thông tin tiếp thị liên kết | GET | /api/agent/info | Lấy thông tin cộng tác viên |
| Lấy chi tiết hoa hồng | GET | /api/agent/commission | Tra cứu lịch sử hoa hồng |
| Yêu cầu rút tiền | POST | /api/agent/withdraw | Gửi yêu cầu rút tiền |
| Lấy danh sách đội nhóm | GET | /api/agent/team | Lấy danh sách cộng tác viên cấp dưới |

## 🔍 Xử lý sự cố thường gặp

### 1. Quan hệ tiếp thị liên kết không được thiết lập đúng
- **Điểm cần kiểm tra**:
  - Người dùng có đáp ứng điều kiện tiếp thị liên kết không
  - Tính năng tiếp thị liên kết đã được bật chưa
  - Tham số của liên kết giới thiệu có được truyền đúng không

### 2. Hoa hồng không được tính
- **Điểm cần kiểm tra**:
  - Đơn hàng đã hoàn thành chưa
  - Trạng thái cộng tác viên có bình thường không
  - Tỷ lệ hoa hồng đã được cấu hình chưa

### 3. Rút tiền thất bại
- **Điểm cần kiểm tra**:
  - Số dư có đủ không
  - Thông tin rút tiền có đầy đủ không
  - Có kích hoạt quy tắc kiểm soát rủi ro không

## 📝 Lịch sử cập nhật phiên bản

| Phiên bản | Ngày | Nội dung cập nhật |
|------|------|---------|
| v1.0 | 2024-01-17 | Tạo tài liệu phiên bản đầu tiên |
| v1.1 | 2024-01-18 | Bổ sung cấu hình tiếp thị liên kết nhiều cấp |

---
**Bảo trì tài liệu**: Đội ngũ kỹ thuật  
**Cập nhật lần cuối**: 2024-01-17  

💡 **Lưu ý**: Hệ thống tiếp thị liên kết liên quan đến dòng tiền, vui lòng định kỳ rà soát việc tính hoa hồng và lịch sử rút tiền để đảm bảo dữ liệu tài chính chính xác.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
