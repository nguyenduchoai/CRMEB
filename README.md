> **Bản Việt hóa CRMEB (v5.6.3)**
>
> - Giao diện quản trị, H5/App, trình cài đặt, thông báo API, dữ liệu mẫu SQL, chú thích code và tài liệu đã được dịch sang **tiếng Việt**.
>   - Ngôn ngữ mặc định: **vi-VN**. Múi giờ: **Asia/Ho_Chi_Minh**.
>   - Các gói ngôn ngữ khác vẫn còn và có thể bật lại trong trang quản trị.
> - Đánh giá khả năng dùng CRMEB làm nền tảng TMĐT tại Việt Nam: [`PHAN_TICH_CRMEB_VIETNAM.md`](PHAN_TICH_CRMEB_VIETNAM.md).
> - Mã nguồn gốc thuộc bản quyền CRMEB (Xi'an Zhongbang). Văn bản license gốc giữ nguyên; bản dịch tham khảo nằm ở `crmeb/LICENSE.vi.txt`.
> - Build lại frontend:
>   - Admin: `cd template/admin && npm ci && NODE_OPTIONS=--openssl-legacy-provider npm run build`, sau đó chép `dist/` vào `crmeb/public/admin/`.
>   - UniApp: dùng HBuilderX như trước, hoặc build bằng uni-app CLI (Vue 2). Nếu dùng dart-sass, đổi `/deep/` thành `::v-deep`.


<div align="center" >
    <img src="https://www.crmeb.com/static/images/dark_logo.png" />
</div>

<div align="center" style="font-size: 15px;">

Hệ thống thương mại điện tử mã nguồn mở CRMEB (phiên bản PHP)  

</div>

<div align="center" style="font-size: 15px;">
  Chúng tôi tận tâm làm mã nguồn mở và rất cần sự khích lệ của bạn! Star🌟 ở góc trên bên phải đang chờ bạn thắp sáng
</div>


####


<div align="center">


[Website chính thức](https://www.crmeb.com/) |
[Trải nghiệm trực tuyến](http://v4.crmeb.net/admin/) |
[Tài liệu hướng dẫn](https://doc.crmeb.com/single_open) |
[Chợ ứng dụng](https://www.crmeb.com/market/) |
[Cộng đồng kỹ thuật](https://www.crmeb.com/ask/thread/list/147)




</div>





---

### 📝 **Giới thiệu dự án**

**Mã nguồn mở tự do**

Mã nguồn của hệ thống thương mại điện tử mã nguồn mở CRMEB được công khai 100%, cho phép sử dụng thương mại miễn phí theo **giấy phép Apache-2.0**, không có bất kỳ chi phí ẩn hay giới hạn tính năng nào, thực sự mang lại khả năng triển khai “không tốn chi phí” và tự do phát triển tùy biến!

**Kiến trúc kỹ thuật**

Sử dụng bộ công nghệ **ThinkPHP 6 + ElementUI + UniApp**, thiết kế tách biệt frontend và backend, hỗ trợ phát triển theo module và bảo trì hiệu quả. Frontend tương thích nhiều nền tảng như WeChat Mini Program, H5, APP, PC, còn backend quản lý thống nhất dữ liệu của toàn bộ nền tảng, đảm bảo trải nghiệm mượt mà và hiệu năng xử lý đồng thời cao.

**Bao phủ mọi kịch bản**

Kết nối liền mạch OA WeChat, Mini Program, H5, APP và PC, dữ liệu liên thông theo thời gian thực, giúp người bán vận hành kinh doanh đa kênh tại một nơi, đáp ứng nhu cầu thương mại điện tử cho mọi kịch bản.

**Công cụ marketing tích hợp sẵn**

Tích hợp sẵn **20+ module marketing cốt lõi** (mua chung, săn giảm giá, flash sale, phiếu giảm giá, hệ thống điểm thưởng, livestream bán hàng, thành viên trả phí, thành viên theo hạng, nạp tiền tài khoản, tiếp thị liên kết lan truyền, mã kênh, quà tặng người mới, v.v.), hỗ trợ tùy chỉnh quy tắc chương trình khuyến mãi mà không cần plugin. Với **tính năng DIY trang chủ**, người bán có thể thiết kế trang chủ cửa hàng bằng thao tác kéo thả, nhanh chóng xây dựng các kịch bản có tỷ lệ chuyển đổi cao mà không cần kiến thức kỹ thuật, đạt hiệu quả vận hành theo kiểu “thấy gì được nấy”.

**Kế hoạch cùng xây dựng cộng đồng**

Chúng tôi cam kết xây dựng hệ sinh thái thân thiện với lập trình viên, công khai mã nguồn, liên tục cập nhật các module tính năng, đồng thời hoan nghênh lập trình viên gửi đề xuất tối ưu hoặc đóng góp mã nguồn. Thông qua việc chia sẻ thành quả kỹ thuật, chúng tôi góp phần giảm chi phí “phát minh lại bánh xe” trong ngành, thúc đẩy sự phát triển bền vững của các hệ thống thương mại điện tử mã nguồn mở.


🔗 <a href="https://doc.crmeb.com/single_open/open_v54/19855" target="_blank">Danh sách tính năng</a> | 📩 <a href="https://github.com/crmeb/CRMEB/issues" target="_blank">Gửi phản hồi</a> | 📩 <a href="https://github.com/crmeb/CRMEB/pulls" target="_blank">Đóng góp mã nguồn</a>



---

### 🫧 Đặc điểm kỹ thuật

~~~
Về phát triển tùy biến:
1.Chuẩn mã nguồn: tuân thủ quy chuẩn đặt tên PSR-2, API theo chuẩn Restful, phân lớp mã nguồn chặt chẽ, chú thích đầy đủ, mã lỗi thống nhất;
2.Quản lý quyền: tích hợp sẵn cơ chế quản lý quyền mạnh mẽ, linh hoạt, có thể kiểm soát đến từng menu;
3.Cấu hình phát triển: thêm cấu hình theo hướng low-code, module dữ liệu tổ hợp của hệ thống;
4.Sinh mã: tạo nhanh menu và trang trong hệ thống quản trị, nhanh chóng hiện thực các thao tác thêm, xóa, sửa, tra cứu;
5.Tác vụ định kỳ: hệ thống tích hợp sẵn 10 loại tác vụ định kỳ, ngoài ra còn có tác vụ tùy chỉnh, có thể tự thiết lập chu kỳ thực thi và mã thực thi, tương thích hoàn hảo;
6.Sự kiện hệ thống: cài sẵn 30+ điểm neo sự kiện hệ thống, có thể thêm sự kiện ngay trên trang quản trị;
7.Chỉnh sửa trực tuyến: có thể chỉnh sửa mã nguồn hệ thống ngay trong trang quản trị mà không cần đăng nhập vào máy chủ để sửa tệp mã nguồn, tiện lợi và nhanh chóng;
8.Quản lý API: trang quản trị hiển thị toàn bộ dữ liệu API trong hệ thống, đồng thời cho phép gỡ lỗi API trực tuyến;
9.Hiệu quả phát triển tùy biến: sử dụng form-builder PHP để tạo biểu mẫu nhanh chóng;
10.Nhanh chóng làm quen: quản lý API trong trang quản trị, từ điển cơ sở dữ liệu trong trang quản trị, ghi chú quản lý tệp hệ thống, chú thích mã nguồn, cài đặt bằng một cú nhấp;
~~~
~~~
Hiệu năng và khả năng mở rộng:
1.Bảo mật hệ thống: nhật ký thao tác hệ thống, nhật ký vận hành hệ thống, kiểm tra tệp, sao lưu dữ liệu;
2.Hiệu năng cao: hỗ trợ bộ nhớ đệm Redis, hàng đợi, kết nối liên tục, nhiều loại lưu trữ đám mây, hỗ trợ triển khai dạng cụm (cluster);
3.Đa ngôn ngữ: hỗ trợ tự động nhận diện ngôn ngữ của trình duyệt để hiển thị đa ngôn ngữ;
4.Mở rộng driver: hỗ trợ nhiều phương thức thanh toán, nhiều nhà cung cấp SMS, nhiều dịch vụ lưu trữ đám mây, v.v.;
5.Lưu trữ đám mây: hỗ trợ lưu trữ hình ảnh và video từ xa trên đám mây, hỗ trợ Alibaba Cloud, Tencent Cloud, Qiniu Cloud, JD Cloud, Tianyi Cloud, Huawei Cloud
6.Yihaotong: tiện ích mở rộng bên thứ ba dùng chung, hỗ trợ SMS, tra cứu vận chuyển, vận đơn điện tử, hóa đơn điện tử, thu thập sản phẩm, người bán gửi hàng
~~~

---

###  📖 Tính năng hệ thống

![Mô tả hình ảnh](readme/pic/tinh-nang-cot-loi.jpg)

---

###  📖 Minh họa giao diện UI

![Mô tả hình ảnh](readme/pic/PHP_06.jpg)



---

###  📖 Minh họa giao diện trang quản trị

![Mô tả hình ảnh](readme/pic/PHP_05.jpg)


---


###  📱 Demo hệ thống

![Mô tả hình ảnh](readme/pic/contact.jpg)

Trang quản trị: http://v5.crmeb.net/admin

Tài khoản: demo Mật khẩu: crmeb.com

Bản H5: http://v5.crmeb.net/ (mở trên thiết bị di động)

Bản PC: http://v5.crmeb.net/ (mở trên máy tính)

Tải APP: http://app.crmeb.cn/bzv (với iPhone, tìm CRMEB trực tiếp trên APP Store để tải về)

> Nghe nói cao thủ như bạn muốn xem toàn bộ khung kiến trúc của dự án mã nguồn mở CRMEB? <a href="https://doc.crmeb.com/single/v5/7712" target="_blank">Nhấn vào đây để nhận ngay!</a>





---





###  🔐 **Môi trường vận hành**


| **Môi trường vận hành**         | **Yêu cầu**                                                                 |
|------------------|------------------------------------------------------------------------|
| **Hệ điều hành**     | Linux / Windows                                                        |
| **Máy chủ WEB**   | Nginx / Apache / IIS                                                      |
| **Phiên bản PHP**     | PHP 7.1 ~ 7.4                                                          |
| **Cơ sở dữ liệu**       | MySQL 5.7 ~ 8.0 (engine: InnoDB)                                         |
| **Bộ nhớ đệm**         | Redis (tùy chọn, nếu không cài đặt sẽ dùng bộ nhớ đệm bằng tệp)                                      |
| **Trình quản lý**       | Supervisor (dùng để quản lý hàng đợi tin nhắn)                                          |
| **Công cụ khuyên dùng**     | BT Panel (đơn giản, dễ sử dụng)                                                    |
| **Máy chủ đám mây**     | Alibaba Cloud ECS / Tencent Cloud CVM / JD Cloud ECS                                                |
| **Cổng cần mở**     | 80, 21, 8888, 888, 443, 3306, 6379 (đối tượng cấp quyền: `0.0.0.0/0`)              |
| **Tiện ích mở rộng PHP**     | fileinfo (tùy chọn), redis (tùy chọn)                               |
| **Hàm cần bỏ vô hiệu hóa**     | `proc_open`, `pcntl_signal`, `pcntl_signal_dispatch`, `pcntl_fork`, `pcntl_wait`, `pcntl_alarm` |
| **Hàng đợi tin nhắn**     | Lệnh chạy: `php think queue:listen --queue`    (dùng Supervisor)                          |
| **Kết nối liên tục**       | Lệnh chạy: `sudo -u www php think workerman start --d`     (chạy bằng dòng lệnh)              |
| **Tác vụ định kỳ**     | Lệnh chạy: `php think timer start --d`            (chạy bằng dòng lệnh)                       |
> Lưu ý: không hỗ trợ hosting ảo, khuyên dùng BT Panel (Baota), về máy chủ khuyên dùng máy chủ JD Cloud: <a href="https://partner.jdcloud.com/partner/notice/b06c3232b6394fdfa496923b8e00b286" target="_blank">Đăng ký là được hưởng ưu đãi độc quyền giảm 35%, nhấn vào đây để nhận!</a>

---
###  📺 **Môi trường phát triển và công nghệ sử dụng**

### **Môi trường phát triển:**
| Công cụ          | Phiên bản               | Liên kết tải xuống                                                                 |
|--------------|--------------------|-------------------------------------------------------------------------|
| **PHP**      | 7.1-7.4            | [Tải PHP từ trang chính thức](https://www.php.net/downloads.php)                           |
| **MySQL**    | 5.7                | [Website chính thức MySQL](https://www.mysql.com/)                                       |
| **Redis**    | 7.0                | [Website chính thức Redis](https://redis.io/download)                                     |
| **Nginx**    | 1.22               | [Website chính thức Nginx](http://nginx.org/en/download.html)                             |
| **Apache**   | 2.4                | [Apache HTTP Server](https://httpd.apache.org/download.cgi)              |
| **Node.js**  | 14/18              | [Node.js phiên bản LTS](https://nodejs.org/en/download/releases/)                |

### Bộ công nghệ backend
| **Công nghệ**            | **Tên**                                                                 | **Địa chỉ web**                                                                 |
|---------------------|-----------------------------------------------------------------------------|-------------------------------------------------------------------------|
| Thư viện mở rộng php          | Môi trường chạy cơ bản của PHP, xử lý dữ liệu JSON, tính toán số học độ chính xác cao, v.v.                                | https://www.php.net/                                                   |
| topthink          | Template engine cho view của ThinkPHP, thành phần tạo mã xác thực (captcha), hỗ trợ tác vụ hàng đợi, công cụ migration cơ sở dữ liệu                 | https://www.thinkphp.cn/                     |
| overtrue          | Phát triển cho hệ sinh thái WeChat (OA WeChat/Mini Program/thanh toán)                                               | https://github.com/w7corp/easywechat                                     |
| php-jwt           | Tạo và xác thực token JWT                                                             | https://github.com/firebase/php-jwt                                    |
| var-dumper        | Công cụ xuất thông tin gỡ lỗi (định dạng biến)                                                     | https://symfony.com/doc/current/components/var_dumper.html            |
| phpoffice         | Xử lý tệp                                                                    | https://github.com/PHPOffice/PhpSpreadsheet                           |
| guzzlehttp｜psr7  | Thư viện HTTP client, triển khai interface thông điệp HTTP theo PSR-7                                        | https://guzzle-cn.readthedocs.io/zh-cn/latest                        |
| form-builder      | Công cụ UI giúp xây dựng biểu mẫu nhanh chóng                                                      | https://form-create.com                                  |
| workerman         | Framework máy chủ Socket hiệu năng cao, lập lịch tác vụ định kỳ                                        | https://www.workerman.net                                   |

### Bộ công nghệ phía di động
| **Công nghệ** | **Tên** | **Website chính thức** |
| --- | --- | --- |
| uniapp | Framework đa nền tảng | https://uniapp.dcloud.net.cn/ |
| vuex | Thư viện quản lý trạng thái | https://vuex.vuejs.org/ |
| socket | Giao tiếp WebSocket | https://socket.io/ |
| dayjs | Thư viện xử lý thời gian | https://day.js.org/ |
| animate | Thư viện hiệu ứng động CSS | https://animate.style/ |
| easy-loadimage | Tải ảnh trì hoãn (lazy load) | https://github.com/TSjianjiao/easy-loadimage |

### Bộ công nghệ phía Admin
| **Công nghệ** | **Tên** | **Website chính thức** |
| --- | --- | --- |
| vue2 | Framework Vue | https://v2.vuejs.org/ |
| vuex | Thư viện quản lý trạng thái | https://vuex.vuejs.org/ |
| element-ui | Framework UI | https://element.eleme.io/ |
| axios | HTTP client | https://axios-http.com/ |
| vxe-table | Thành phần bảng nâng cao | https://vxetable.cn/ |
| wangeditor | Trình soạn thảo văn bản định dạng | https://www.wangeditor.com/ |
| qs | Phân tích chuỗi truy vấn (query string) | https://github.com/ljharb/qs |
| xlsx | Thư viện xử lý Excel | https://sheetjs.com/ |
| sass | Bộ tiền xử lý CSS | https://sass-lang.com/ |
| prettier | Định dạng mã nguồn | https://prettier.io/ |
| v-viewer | Trình xem ảnh | https://github.com/mirari/v-viewer |

### Bộ công nghệ phía PC
| **Công nghệ** | **Tên** | **Website chính thức** |
| --- | --- | --- |
| nuxt | Framework render phía máy chủ (SSR) cho Vue | https://nuxtjs.org/ |
| element | Framework UI | https://element.eleme.io/ |
| axios | HTTP client | https://axios-http.com/ |
| sass | Bộ tiền xử lý CSS | https://sass-lang.com/ |
| cookie-universal-nuxt | Xử lý Cookie cho Nuxt | https://github.com/microcipcip/cookie-universal |
| postcss | Công cụ chuyển đổi CSS | https://postcss.org/ |
| qs | Phân tích chuỗi truy vấn (query string) | https://github.com/ljharb/qs |



### Muốn cài đặt nhanh, đã có hướng dẫn hỗ trợ!

Cài đặt và triển khai nhanh bằng một cú nhấp: https://doc.crmeb.com/single_open/open_v54/20366

Cài đặt với cấu hình thủ công: https://doc.crmeb.com/single_open/open_v54/20389

Triển khai bằng một cú nhấp với docker-compose: https://doc.crmeb.com/single_open/open_v54/20145

Cài đặt bằng một cú nhấp trên môi trường BT Panel: https://doc.crmeb.com/single_open/open_v54/19892

### Hỗ trợ phát triển tùy biến:
Tài liệu sử dụng: https://doc.crmeb.com/single_open/open_v54/19849

Tài liệu API: https://doc.crmeb.com/single_open/open_v54/21040

Từ điển dữ liệu: https://doc.crmeb.com/single_open/open_v54/20136

Sinh mã: https://doc.crmeb.com/single_open/open_v54/20135

Tài liệu phát triển tùy biến: https://doc.crmeb.com/single_open/open_v54/19851

Video hướng dẫn: https://www.bilibili.com/video/BV1kh4y1872K/

Cộng đồng kỹ thuật: https://www.crmeb.com/ask/thread/list/147

---

###  📞 Tương tác cùng CRMEB
#### Nhóm trao đổi kỹ thuật mã nguồn mở CRMEB (quét mã để vào nhóm và nhận tài liệu API bản mã nguồn mở, danh sách tính năng sản phẩm, bản thiết kế UI độ nét cao, sơ đồ tư duy!)
![Mô tả hình ảnh](readme/pic/nhom-ma-nguon-mo.jpg)
#### Cộng đồng kỹ thuật! Tìm giải pháp, báo bug, xem tin tức chính thức, nhận giải thưởng lớn cho thành viên tích cực! <a href="https://www.crmeb.com/ask" target="_blank">Cộng đồng kỹ thuật CRMEB</a> có đầy đủ tất cả




---

###  📕 CRMEB bản PRO

[![Mô tả hình ảnh](readme/pic/ban-pro-1.jpg)](https://www.crmeb.com/index/pro)



###  📕 CRMEB bản đa người bán

[![Mô tả hình ảnh](readme/pic/duoshanghu.jpg)](https://www.crmeb.com/index/merchant)


