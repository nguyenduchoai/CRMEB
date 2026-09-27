# CRMEB MCP Server

Máy chủ công cụ CRMEB API dựa trên Model Context Protocol (MCP), đã được tích hợp vào mô-đun outapi của CRMEB, cho phép trợ lý AI gọi các API mở của CRMEB thông qua giao thức chuẩn.

## Tính năng

- 🔗 Tích hợp vào mô-đun outapi của CRMEB, không cần triển khai thêm
- 📦 Quản lý sản phẩm (danh sách, chi tiết, tạo mới)
- 📂 Quản lý danh mục (danh sách, chi tiết, tạo mới)
- 🛒 Quản lý đơn hàng (danh sách, chi tiết, giao hàng)
- 💰 Quản lý hậu mãi (danh sách, chi tiết, đồng ý/từ chối hoàn tiền)
- 🎫 Quản lý phiếu giảm giá
- 👥 Quản lý người dùng (danh sách, chi tiết, tặng số dư/điểm thưởng)

## Yêu cầu môi trường

- Hệ thống CRMEB
- PHP >= 7.4

## Bắt đầu nhanh

### 1. Tạo tài khoản API mở

1. Đăng nhập trang quản trị CRMEB
2. Vào **Cài đặt** -> **Cài đặt hệ thống** -> **API mở**
3. Tạo ứng dụng để lấy tài khoản và mật khẩu

### 2. Cấu hình trong Claude Desktop

Chỉnh sửa tệp cấu hình:

**macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
**Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

```json
{
  "mcpServers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      },
      "disabled": false
    }
  }
}
```

**Ví dụ cấu hình (môi trường demo):**
```json
{
  "mcpServers": {
    "crmebdemo": {
      "url": "https://v5.crmeb.net/outapi/mcp",
      "headers": {
        "account": "ceshi",
        "password": "ceshiceshi",
        "Content-Type": "application/json"
      },
      "disabled": false
    }
  }
}
```

### 3. Cấu hình trong Cursor

Thêm vào phần cài đặt của Cursor:

```json
{
  "mcp.servers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      },
      "disabled": false
    }
  }
}
```

**Mô tả tham số:**

| Tham số | Mô tả | Cách nhận |
|------|------|---------|
| `url` | Địa chỉ API MCP của CRMEB | Giá trị cố định: `http://ten-mien-cua-ban/outapi/mcp` |
| `account` | Tài khoản API mở | Trang quản trị CRMEB -> Cài đặt -> API mở |
| `password` | Mật khẩu API mở | Trang quản trị CRMEB -> Cài đặt -> API mở |
| `disabled` | Có vô hiệu hóa dịch vụ này hay không | Tùy chọn, mặc định là false |

> Lưu ý: chỉ cần cấu hình trực tiếp account và password, hệ thống sẽ tự động hoàn tất xác thực

## Các ứng dụng MCP client được hỗ trợ

Dưới đây là các ứng dụng phổ biến hiện nay có hỗ trợ giao thức MCP, bạn có thể cấu hình và sử dụng dịch vụ CRMEB MCP trong các ứng dụng này.

### 1. Claude Desktop

Ứng dụng desktop chính thức của Anthropic, client đầu tiên hỗ trợ MCP một cách nguyên bản (native).

**Các bước cấu hình:**
1. Tải xuống và cài đặt [Claude Desktop](https://claude.ai/download)
2. Tìm tệp cấu hình:
   - **macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
   - **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`
3. Thêm cấu hình CRMEB MCP:
```json
{
  "mcpServers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      },
      "disabled": false
    }
  }
}
```
4. Khởi động lại Claude Desktop

**Cách sử dụng:**
Hỏi trực tiếp trong cuộc trò chuyện, ví dụ:
```
Giúp tôi tra cứu danh sách danh mục sản phẩm trong CRMEB
```

### 2. Cursor

Trình soạn thảo mã nguồn vận hành bằng AI, tích hợp sẵn hỗ trợ MCP.

**Các bước cấu hình:**
1. Tải xuống và cài đặt [Cursor](https://cursor.sh/)
2. Mở phần cài đặt (Settings -> Features -> Model Context Protocol)
3. Thêm cấu hình máy chủ MCP:
```json
{
  "mcp.servers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      },
      "disabled": false
    }
  }
}
```
4. Hoặc chỉnh sửa trực tiếp tệp cấu hình:
   - **macOS/Linux**: `~/.cursor/mcp.json`
   - **Windows**: `%APPDATA%\Cursor\mcp.json`

**Cách sử dụng:**
Sử dụng trực tiếp trong cửa sổ chat AI của Cursor:
```
Tra cứu danh sách đơn hàng trong CRMEB
```

### 3. Cline (tiện ích mở rộng VS Code)

Tiện ích mở rộng trợ lý lập trình AI tự hành (autonomous) trong VS Code.

**Các bước cấu hình:**
1. Trong VS Code, cài đặt [tiện ích mở rộng Cline](https://marketplace.visualstudio.com/items?itemName=saoudrizwan.claude-dev)
2. Mở phần cài đặt VS Code
3. Tìm kiếm "Cline MCP" hoặc tìm phần cấu hình MCP trong bảng điều khiển Cline
4. Thêm máy chủ MCP:
```json
{
  "mcpServers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      },
      "disabled": false
    }
  }
}
```

**Cách sử dụng:**
Nhập lệnh trong bảng điều khiển Cline:
```
Lấy chi tiết sản phẩm CRMEB có ID là 1
```

### 4. Windsurf

IDE thuần AI (AI-native) do Codeium ra mắt.

**Các bước cấu hình:**
1. Tải xuống và cài đặt [Windsurf](https://codeium.com/windsurf)
2. Mở phần cài đặt -> Developer Settings -> MCP Servers
3. Thêm cấu hình:
```json
{
  "mcpServers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      }
    }
  }
}
```

### 5. Continue (tiện ích mở rộng VS Code/JetBrains)

Tiện ích mở rộng trợ lý viết mã AI mã nguồn mở.

**Các bước cấu hình:**
1. Cài đặt tiện ích mở rộng Continue
   - [VS Code](https://marketplace.visualstudio.com/items?itemName=Continue.continue)
   - [JetBrains](https://plugins.jetbrains.com/plugin/22707-continue)
2. Mở tệp cấu hình của Continue (`~/.continue/config.json`)
3. Thêm cấu hình MCP:
```json
{
  "models": [...],
  "mcpServers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      }
    }
  }
}
```

### 6. Zed

Trình soạn thảo mã hiệu năng cao, hỗ trợ giao thức MCP.

**Các bước cấu hình:**
1. Tải xuống và cài đặt [Zed](https://zed.dev/)
2. Mở tệp cấu hình (`~/.zed/settings.json`)
3. Thêm cấu hình máy chủ MCP:
```json
{
  "mcp_servers": {
    "crmeb": {
      "url": "http://your-domain/outapi/mcp",
      "headers": {
        "account": "your_account",
        "password": "your_password",
        "Content-Type": "application/json"
      }
    }
  }
}
```

### 7. Các ứng dụng khác hỗ trợ MCP

Các ứng dụng sau cũng đang dần hỗ trợ giao thức MCP:

- **Codeium**: Công cụ tự động hoàn thành mã bằng AI
- **Tabnine**: Trợ lý viết mã AI
- **Sourcegraph Cody**: Trợ lý mã nguồn thông minh

> 💡 **Mẹo**: MCP là một giao thức mở, ngày càng có nhiều ứng dụng AI hỗ trợ. Nếu ứng dụng của bạn hỗ trợ MCP, thông thường bạn có thể tìm thấy các mục cấu hình liên quan đến MCP hoặc Model Context Protocol trong phần cài đặt.

## Công cụ khả dụng

### Quản lý danh mục

| Tên công cụ | Mô tả | Tham số bắt buộc | Tham số tùy chọn |
|---------|------|---------|---------|
| `crmeb_category_list` | Lấy danh sách danh mục | - | page, limit |
| `crmeb_category_detail` | Lấy chi tiết danh mục | id | - |
| `crmeb_category_create` | Tạo danh mục | name | pid, sort |

### Quản lý sản phẩm

| Tên công cụ | Mô tả | Tham số bắt buộc | Tham số tùy chọn |
|---------|------|---------|---------|
| `crmeb_product_list` | Lấy danh sách sản phẩm | - | page, limit, cate_id, keyword, stock_min, stock_max |
| `crmeb_product_detail` | Lấy chi tiết sản phẩm | id | - |
| `crmeb_product_create` | Tạo sản phẩm | name, cate_id, price, stock | image, unit |

### Quản lý đơn hàng

| Tên công cụ | Mô tả | Tham số bắt buộc | Tham số tùy chọn |
|---------|------|---------|---------|
| `crmeb_order_list` | Lấy danh sách đơn hàng | - | page, limit, status, keyword |
| `crmeb_order_detail` | Lấy chi tiết đơn hàng | order_id | - |
| `crmeb_order_delivery` | Giao đơn hàng | order_id, delivery_type | delivery_name, delivery_id |
| `crmeb_order_express_list` | Lấy danh sách đơn vị vận chuyển | - | - |

### Quản lý hậu mãi

| Tên công cụ | Mô tả | Tham số bắt buộc | Tham số tùy chọn |
|---------|------|---------|---------|
| `crmeb_refund_list` | Lấy danh sách đơn hậu mãi | - | page, limit |
| `crmeb_refund_detail` | Lấy chi tiết đơn hậu mãi | order_id | - |
| `crmeb_refund_agree` | Đồng ý hoàn tiền | order_id | - |
| `crmeb_refund_refuse` | Từ chối hoàn tiền | order_id, refuse_reason | - |

### Quản lý phiếu giảm giá

| Tên công cụ | Mô tả | Tham số bắt buộc | Tham số tùy chọn |
|---------|------|---------|---------|
| `crmeb_coupon_list` | Lấy danh sách phiếu giảm giá | - | page, limit |

### Quản lý người dùng

| Tên công cụ | Mô tả | Tham số bắt buộc | Tham số tùy chọn |
|---------|------|---------|---------|
| `crmeb_user_list` | Lấy danh sách người dùng | - | page, limit, keyword |
| `crmeb_user_detail` | Lấy chi tiết người dùng | uid | - |
| `crmeb_user_give_balance` | Tặng số dư | uid, balance | title |
| `crmeb_user_give_point` | Tặng điểm thưởng | uid, point | title |

## Ví dụ sử dụng

Trong Claude hoặc Cursor, bạn có thể sử dụng như sau:

```
Giúp tôi tra cứu danh sách sản phẩm trong CRMEB
```

```
Tạo một sản phẩm mới: tên"Sản phẩm thử nghiệm"，ID danh mục 1, giá 99.00, tồn kho 100
```

```
Tra cứu chi tiết mã đơn hàng 202403130001
```

```
Tặng 100 điểm thưởng cho người dùng có ID là 1
```

## Kiểm thử API

```bash
# Kiểm thử khởi tạo MCP
curl -X POST "http://localhost:8011/outapi/mcp" \
  -H "Content-Type: application/json" \
  -H "account: your_account" \
  -H "password: your_password" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{}}'

# Lấy danh sách công cụ
curl -X POST "http://localhost:8011/outapi/mcp" \
  -H "Content-Type: application/json" \
  -H "account: your_account" \
  -H "password: your_password" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}'

# Gọi công cụ - Lấy danh sách sản phẩm
curl -X POST "http://localhost:8011/outapi/mcp" \
  -H "Content-Type: application/json" \
  -H "account: your_account" \
  -H "password: your_password" \
  -d '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"crmeb_product_list","arguments":{"page":1,"limit":10}}}'

# Gọi công cụ - Tạo danh mục
curl -X POST "http://localhost:8011/outapi/mcp" \
  -H "Content-Type: application/json" \
  -H "account: your_account" \
  -H "password: your_password" \
  -d '{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"crmeb_category_create","arguments":{"name":"Danh mục mới","sort":100}}}'

# Gọi công cụ - Tra cứu sản phẩm có tồn kho lớn hơn 500
curl -X POST "http://localhost:8011/outapi/mcp" \
  -H "Content-Type: application/json" \
  -H "account: your_account" \
  -H "password: your_password" \
  -d '{"jsonrpc":"2.0","id":5,"method":"tools/call","params":{"name":"crmeb_product_list","arguments":{"stock_min":500}}}'

# Kiểm thử bằng môi trường demo
curl -X POST "https://v5.crmeb.net/outapi/mcp" \
  -H "Content-Type: application/json" \
  -H "account: ceshi" \
  -H "password: ceshiceshi" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"crmeb_category_list","arguments":{}}}'
```

## Cấu trúc dự án

```
crmeb/app/outapi/
├── controller/
│   ├── Mcp.php              # MCP Controller
│   ├── Login.php            # Controller xác thực
│   ├── StoreProduct.php     # Controller sản phẩm
│   ├── StoreCategory.php    # Controller danh mục
│   ├── StoreOrder.php       # Controller đơn hàng
│   ├── RefundOrder.php      # Controller đổi trả
│   ├── StoreCoupon.php      # Controller phiếu giảm giá
│   ├── User.php             # Controller người dùng
│   └── ...
├── route/
│   └── route.php            # Cấu hình route
├── middleware/
│   └── AuthTokenMiddleware.php
├── mcp.md                   # Tài liệu này
└── README.md                # outapi mô tả module
```

## Mô tả giao thức

### Phiên bản giao thức MCP

- Phiên bản hỗ trợ: `2024-11-05`

### Định dạng JSON-RPC 2.0

**Định dạng yêu cầu:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "tools/call",
  "params": {
    "name": "Tên công cụ",
    "arguments": { "Tham số": "Giá trị" }
  }
}
```

**Phản hồi thành công:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "content": [
      {
        "type": "text",
        "text": "{...Dữ liệu kết quả...}"
      }
    ]
  }
}
```

**Phản hồi lỗi:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "error": {
    "code": -32603,
    "message": "Thông tin lỗi"
  }
}
```

## Mô tả mã lỗi

| Mã lỗi | Mô tả |
|-------|------|
| -32700 | Lỗi phân tích JSON |
| -32600 | Yêu cầu không hợp lệ (xác thực thất bại, v.v.) |
| -32601 | phương thức không tồn tại |
| -32603 | Lỗi nội bộ |

## Liên kết liên quan

- [Tài liệu Model Context Protocol](https://modelcontextprotocol.io/)
- [API mở của CRMEB](./README.md)
