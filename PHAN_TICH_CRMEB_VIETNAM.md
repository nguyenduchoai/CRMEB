# Phân tích CRMEB (v5.6.3) cho mục tiêu Platform TMĐT tại Việt Nam

> Người đọc: Founder và đội kỹ thuật Bizino.ai · Phạm vi: toàn bộ repo `nguyenduchoai/CRMEB` (nhánh `master`, bản CRMEB-KY v5.6.3)

## Kết luận nhanh

| Mục tiêu | Có nên dựa trên CRMEB? | Lý do chính |
|---|---|---|
| Triển khai **web/app bán hàng cho từng SME** (mỗi khách hàng một instance) | **Được**, nếu làm đủ phần bản địa hóa và có license thương mại | Tính năng bán lẻ và marketing rất đầy đủ, giao diện DIY, loyalty mạnh. Chạy được ngay sau khi Việt hóa và thay các tích hợp Trung Quốc. |
| Xây **"Bizino Platform"**: SaaS nhiều khách hàng dùng chung hoặc sàn nhiều người bán | **Không nên build trực tiếp lên CRMEB** | (1) Rủi ro license: điều 2.3 cấm kinh doanh dịch vụ cùng loại. (2) Kiến trúc single-tenant. (3) Stack đã hết vòng đời (PHP 7.1–7.4, Vue 2). (4) Gắn chặt hệ sinh thái WeChat/Alipay. |

**Khuyến nghị: đi theo hướng "Hybrid" (mục 5).**
- Dùng CRMEB đã Việt hóa để chạy thử nhanh với 1–3 khách hàng, kiểm chứng nhu cầu.
- Song song, xây lõi **Bizino Commerce** bằng Go + Next.js (đúng stack ưu tiên). Lõi này multi-tenant ngay từ đầu, lấy CRMEB làm tài liệu tham khảo về nghiệp vụ và mô hình dữ liệu. Chỉ tham khảo ý tưởng, **không sao chép code** vì lý do license.

---

## 1. Tổng quan kỹ thuật

| Thành phần | Công nghệ | Quy mô |
|---|---|---|
| Backend API | PHP 7.1–7.4, ThinkPHP 6.1, MySQL 5.7/8.0, Redis, Workerman (websocket, timer), think-queue | ~177 nghìn dòng PHP (chưa kể `vendor/`), 154 bảng DB |
| Trang quản trị | Vue 2 + Element UI 2 + vxe-table + form-create, build bằng vue-cli 3/webpack 4 | ~207 nghìn dòng, 659 file có chữ Trung |
| App bán hàng | UniApp (Vue 2), build ra H5, WeChat Mini Program, App (Android/iOS) | ~124 nghìn dòng |
| Kiến trúc | Controller → Service → DAO → Model; driver pattern cho thanh toán, SMS, lưu trữ, vận chuyển, in hóa đơn | Tách lớp rõ ràng, dễ đọc |

**Module nghiệp vụ có sẵn**
- **Sản phẩm:** SKU, thuộc tính, nhãn, tham số, cam kết dịch vụ, đánh giá, sao chép sản phẩm từ Taobao/JD.
- **Đơn hàng:** tách đơn, hoàn tiền/trả hàng, hóa đơn, giao hàng bởi nhân viên, nhận tại cửa hàng (quét mã xác nhận).
- **Marketing:** coupon, flash sale, mua chung, săn giảm giá (bargain), đặt trước, vòng quay may mắn, điểm danh, cửa hàng đổi điểm, livestream (WeChat).
- **Khách hàng:** hạng thành viên, thành viên trả phí (SVIP), nhãn/nhóm khách, số dư, nạp tiền, rút tiền.
- **Tiếp thị liên kết (affiliate):** 2 cấp, hạng CTV, đại lý khu vực.
- **Nội dung & giao diện:** DIY kéo thả trang chủ, bài viết (CMS).
- **Vận hành:** CSKH chat realtime, thống kê, phân quyền theo menu/nút, sinh code CRUD, tác vụ định kỳ, sao lưu DB, **đa ngôn ngữ lưu trong DB** (bảng `eb_lang_type`, `eb_lang_code`, `eb_lang_country`).

## 2. Điểm mạnh (nên tận dụng)

1. **Bộ tính năng bán lẻ và loyalty rất đầy đủ.** Khớp với Bizino MiniApp: tích điểm, hạng thành viên, SVIP, điểm danh, quay thưởng, coupon, affiliate. Tự làm lại khối này mất khoảng 6–12 tháng.
2. **Hạ tầng đa ngôn ngữ đã có sẵn.**
   - Thông báo API dùng mã số (`getLang(100000)`).
   - App UniApp dùng `$t()` và tải gói ngôn ngữ từ server.
   - Nhờ vậy Việt hóa được triệt để (đã làm trong PR này).
3. **Driver pattern** (`crmeb/services/{pay,sms,upload,express,printer}`): thêm VNPay/MoMo/GHN… chỉ cần viết driver mới, không phải sửa lõi.
4. **Trang quản trị mạnh:**
   - Phân quyền tới từng nút.
   - Cấu hình động bằng bảng `eb_system_config` và `eb_system_group`.
   - DIY giao diện, sinh CRUD.
5. **Schema DB rõ ràng.** Tiền dùng `decimal(12,2)` (tối đa khoảng 10 tỷ), đủ cho đơn VND của SME.

## 3. Rủi ro và hạn chế

### 3.1 License: rủi ro lớn nhất khi làm "Platform"

Repo có 2 lớp điều khoản:
- **Apache-2.0** ở `LICENSE` (thư mục gốc).
- **Thỏa thuận bổ sung của CRMEB** ở `crmeb/LICENSE.txt`, luật áp dụng là luật Trung Quốc, trọng tài tại Tây An.

Các điều khoản đáng chú ý trong thỏa thuận bổ sung:

| Điều | Nội dung | Tác động tới Bizino |
|---|---|---|
| 2.1–2.2 | Chỉ cho phép dùng/sửa **với mục đích tự dùng**, phải giữ bản quyền | Triển khai cho khách hàng không phải "tự dùng" |
| **2.3** | **Cấm dùng mã nguồn CRMEB để kinh doanh dịch vụ cùng loại, cùng tính chất với CRMEB** | Một platform TMĐT cho SME chính là dịch vụ cùng loại → rủi ro vi phạm cao |
| 2.5 | Không được xóa liên kết/bản quyền ở chân trang khi chưa có văn bản cho phép; muốn gỡ thì mua license thương mại | Ảnh hưởng white-label cho khách hàng |
| Mục 6 | Chỉ bản có license thương mại mới được nhận bản nâng cấp | Cần tính chi phí license vào giá |

**Việc cần làm:**
- Nếu bán giải pháp dựa trên CRMEB cho khách hàng, **mua license thương mại hoặc có văn bản cho phép** từ Zhongbang (CRMEB).
- Nếu làm platform, **tự xây lõi riêng**.
- Nên hỏi luật sư về hiệu lực của thỏa thuận bổ sung so với Apache-2.0. Hai văn bản này mâu thuẫn nhau, nhưng không nên đặt cược doanh nghiệp vào chỗ mâu thuẫn đó.

### 3.2 Kiến trúc single-tenant

- Không có `tenant_id`. Cấu hình, người dùng, sản phẩm, đơn hàng… đều dùng chung một không gian.
- CRMEB có bản **đa thương nhân (multi-merchant)** và bản **Pro** nhưng là sản phẩm thương mại khác, không nằm trong repo này.
- Muốn phục vụ nhiều SME thì có hai cách:
  - **Mỗi khách hàng một instance** (Docker + DB riêng). Làm được, nhưng tốn công vận hành và nâng cấp từng bản.
  - **Viết lại để hỗ trợ multi-tenant:** thêm `tenant_id` vào khoảng 150 bảng, cache, queue, upload, cấu hình. Ước tính từ 3 tháng trở lên và rủi ro cao.

### 3.3 Công nghệ cũ và bảo mật

**Vòng đời công nghệ**
- PHP 7.1–7.4 đã hết hỗ trợ bảo mật từ 11/2022.
- Vue 2 đã EOL từ 31/12/2023. Element UI 2 chỉ còn bảo trì.
- Phiên bản UniApp đang dùng là Vue 2.

**Thư viện đã cũ, có CVE công bố:** Guzzle 6.5.5, PhpSpreadsheet 1.13, EasyWeChat 3.3, firebase/php-jwt 5.0, Workerman 3.5.

**CRMEB có nhiều lỗ hổng SQL injection đã công bố**, ví dụ:
- [CVE-2024-36837](https://github.com/advisories/GHSA-294q-5vvf-xj65) (v5.2.2)
- [CVE-2025-25763](https://cvefeed.io/vuln/detail/CVE-2025-25763) (≤ v5.4.0)
- [CVE-2025-15442](https://secalerts.co/vulnerability/CVE-2025-15442) (≤ v5.6.1)

→ Bắt buộc audit bảo mật trước khi chạy production.

**Phát hiện trong repo**

| Mức | Vấn đề | Vị trí | Khuyến nghị |
|---|---|---|---|
| Cao | Bộ cài không sinh `APP_KEY`, nên JWT ký bằng khóa mặc định `'default'`. Rủi ro được giảm nhờ token phải có trong cache, nhưng vẫn là cấu hình yếu. | `crmeb/utils/JwtAuth.php`, `public/install/.env` | Sinh `APP_KEY` ngẫu nhiên khi cài |
| Cao | Trình sửa file online và bộ sinh CRUD có thể ghi file lên server | `adminapi/controller/v1/system/SystemFile.php`, `SystemCrud.php` | Tắt trên production. Giữ `filesystem.password` rỗng. Đặt `crud_make=false`. |
| Trung bình | Tự nâng cấp từ server CRMEB (`.version` chứa `app_id`/`app_key`) | `UpgradeController.php`, `UpgradeServices.php` | Tắt. Chỉ nâng cấp qua Git/CI của Bizino. |
| Trung bình | Lockfile trỏ tới mirror npm Trung Quốc (`registry.npmmirror.com`), bị chặn hoặc chậm từ nhiều mạng | `template/admin/package-lock.json` | Đổi sang `registry.npmjs.org` khi build |
| Thấp | Composer dùng mirror Aliyun | `crmeb/composer.json` | Đổi sang packagist.org |
| Trung bình | Bản uni-app CLI mới **mặc định bật "uni Statistics" (uni-stat)**: gửi dữ liệu thống kê người dùng về máy chủ DCloud (Trung Quốc), có rủi ro theo Luật BVDLCN | `template/uni-app/manifest.json` | **Đã tắt trong PR này** (`uniStatistics.enable = false`) |

### 3.4 Gắn chặt hệ sinh thái Trung Quốc: phải thay khi làm ở Việt Nam

| Hạng mục | CRMEB hiện tại | Cần cho Việt Nam | Công sức ước tính |
|---|---|---|---|
| Thanh toán | WeChat Pay (v2/v3), Alipay, Allinpay, số dư, chuyển khoản | VNPay, MoMo, ZaloPay, **VietQR (Napas 247) + đối soát tự động** (SePay/Casso/PayOS), COD | 3–4 tuần |
| Đăng nhập, kênh khách hàng | WeChat OA, WeChat Mini Program, Douyin/Toutiao | Zalo OA, **Zalo Mini App** (UniApp không build được, phải viết front riêng), ZNS, OTP SMS, Google/Facebook | 4–8 tuần |
| SMS | Aliyun, Tencent, Chuanglan, Yihaotong | eSMS, SpeedSMS, VietGuys, brandname Viettel/VNPT, Zalo ZNS | 1–2 tuần |
| Vận chuyển | Kuaidi100/Yihaotong, 1.101 hãng chuyển phát TQ trong `eb_express` | GHN, GHTK, Viettel Post, VNPost, J&T, Ninja Van, SPX, Best, Ahamove, Grab/Lalamove (tạo vận đơn, tra hành trình, webhook) | 3–4 tuần |
| Địa chỉ | 4 cấp Trung Quốc (3.939 bản ghi `eb_system_city`); picker 3 cột tỉnh/thành/quận | **2 cấp theo sắp xếp hành chính từ 01/07/2025**: 34 tỉnh/thành, khoảng 3.321 xã/phường. Sửa DB, picker UniApp/admin, mẫu phí vận chuyển | 1–2 tuần |
| Tiền tệ | CNY, 2 số lẻ, ký hiệu `￥` đặt trước | VND không có số lẻ, dạng `100.000₫`; làm tròn khi tính giảm giá/hoa hồng | 1 tuần |
| Hóa đơn điện tử | Yihaotong (Trung Quốc) | VNPT Invoice, Viettel S-Invoice, MISA meInvoice, BKAV… (NĐ 123/2020, TT 78/2021, NĐ 70/2025) | 2–3 tuần |
| Lưu trữ file | Aliyun OSS, Tencent COS, Qiniu, JD, Tianyi, Huawei, AWS S3 | Dùng driver S3 có sẵn cho AWS, Cloudflare R2, dịch vụ S3-compatible trong nước | 2–3 ngày |
| Lấy sản phẩm | Taobao/JD/Tmall (99api) | Đồng bộ **Shopee, TikTok Shop, Lazada** (đơn, tồn kho): nhu cầu đa kênh của SME Việt | 6–10 tuần |
| Máy in hóa đơn | FeiEYun, YiLianYun | Máy in nhiệt LAN/Bluetooth, Sunmi, in qua trình duyệt | 1–2 tuần |
| Bản đồ | Tencent Maps (QQ Map) để chọn vị trí cửa hàng, tính khoảng cách | Google Maps, Vietmap, Goong hoặc OpenStreetMap. Bản đồ Trung Quốc thể hiện yêu sách "đường 9 đoạn" | 1 tuần |
| Múi giờ | `Asia/Shanghai` (UTC+8) | `Asia/Ho_Chi_Minh` (UTC+7): **đã sửa trong PR này** | — |

### 3.5 Pháp lý Việt Nam cần tính vào sản phẩm

- **Luật Thương mại điện tử số 122/2025/QH15**: thông qua ngày 10/12/2025, **hiệu lực từ 01/07/2026** ([KPMG](https://kpmg.com/us/en/taxnewsflash/news/2026/06/tnf-vietnam-law-on-e-commerce-2025-effective-july-1-2026.html), [Vietnam Briefing](https://www.vietnam-briefing.com/news/vietnams-e-commerce-law-2025-key-provisions-and-business-implications.html/)). Luật này thay khung nghị định cũ (NĐ 52/2013).
  - Web/app bán hàng phải thông báo với Bộ Công Thương.
  - Mô hình **sàn nhiều người bán** có nghĩa vụ nặng hơn nhiều: định danh người bán, xử lý khiếu nại, livestream, affiliate.
- **Luật Bảo vệ dữ liệu cá nhân số 91/2025/QH15**: hiệu lực từ 01/01/2026, kèm NĐ 356/2025 ([Tilleke & Gibbins](https://www.tilleke.com/insights/vietnams-new-personal-data-protection-law-a-closer-look/)).
  - Platform xử lý dữ liệu khách hàng của SME là **bên xử lý dữ liệu**, nên không được hưởng ưu đãi miễn trừ dành cho doanh nghiệp nhỏ.
  - Cần cơ chế đồng ý (consent), quyền truy cập/xóa dữ liệu, đánh giá tác động (DPIA).
- **NĐ 117/2025/NĐ-CP** (hiệu lực 01/07/2025): sàn TMĐT **có chức năng thanh toán** phải khấu trừ và nộp thay thuế GTGT, TNCN cho hộ/cá nhân kinh doanh ([EY](https://www.ey.com/en_vn/technical/tax/tax-and-law-updates/people-advisory-service-tax-alert-june-2025-decree-117-on-tax-management-for-business-activities-on-e-commerce-platforms-and-digital-platforms)). Nếu Bizino thu hộ tiền cho nhiều người bán thì phải có module này.
- **Affiliate 2 cấp** của CRMEB: nếu tính hoa hồng nhiều tầng thì cẩn trọng với quy định kinh doanh đa cấp (NĐ 40/2018, sửa đổi bởi NĐ 18/2023). Nên cấu hình 1 cấp hoặc hỏi ý kiến luật sư.
- **Chủ quyền biển đảo (bắt buộc xử lý):** dữ liệu địa chỉ Trung Quốc trong CRMEB (`eb_system_city`, `template/admin/src/utils/city.js`) xếp **quần đảo Hoàng Sa, Trường Sa** vào "thành phố Tam Sa, Hải Nam" và một nút "đảo Nam Hải" dưới Đài Loan.
  - Phát hành dữ liệu hoặc bản đồ như vậy tại Việt Nam là vi phạm pháp luật và gây rủi ro nghiêm trọng về uy tín.
  - **PR này đã xóa các mục đó.** Toàn bộ dữ liệu địa chỉ Trung Quốc vẫn phải thay bằng dữ liệu Việt Nam. Bản đồ Tencent cũng phải thay (mục 3.4).
- **Thỏa thuận người dùng và chính sách bảo mật mẫu** trong CRMEB viết theo luật Trung Quốc. Bản dịch trong PR này chỉ để tham khảo; **phải viết lại theo luật Việt Nam** trước khi dùng thật.

## 4. Đánh giá "làm Platform dựa trên CRMEB"

| Tiêu chí | Điểm (1–5) | Ghi chú |
|---|---|---|
| Độ phủ tính năng bán lẻ, loyalty | 5 | Rất tốt |
| Tốc độ ra thị trường (1 SME) | 4 | Khoảng 2–3 tháng sau khi thay tích hợp Việt Nam |
| Phù hợp mô hình SaaS nhiều tenant | 1 | Không có multi-tenant |
| An toàn pháp lý khi kinh doanh platform | 1 | Điều 2.3 của thỏa thuận CRMEB |
| Công nghệ, khả năng bảo trì 3–5 năm | 2 | PHP 7.4/Vue 2 EOL, thư viện cũ, nhiều CVE |
| Phù hợp stack Bizino (Go + Next.js) | 1 | Khác hoàn toàn |
| Phù hợp hệ sinh thái Việt Nam (Zalo, VNPay, GHN…) | 2 | Phải thay gần hết phần tích hợp |

## 5. Lộ trình đề xuất (Hybrid)

1. **Tháng 0–3: Pilot trên CRMEB đã Việt hóa**, 1–3 khách hàng, mỗi khách một instance Docker.
   - Làm trước: VietQR + COD, GHN/GHTK, địa chỉ 2 cấp, định dạng VND, SMS OTP/ZNS, vá bảo mật (APP_KEY, tắt file editor/upgrade, cập nhật thư viện).
   - Mua license thương mại CRMEB nếu thu tiền khách hàng.
2. **Tháng 1–9: Xây Bizino Commerce Core** bằng Go (API, multi-tenant, event/queue) + Next.js (admin, storefront) + Zalo Mini App.
   - Lấy từ CRMEB **ý tưởng** mô hình dữ liệu và luồng nghiệp vụ: đơn hàng, hoàn tiền, coupon, flash sale, mua chung, điểm, hạng thành viên, affiliate, DIY.
   - Tích hợp sẵn Zalo, VNPay/MoMo/VietQR, GHN/GHTK, hóa đơn điện tử, Shopee/TikTok Shop.
   - Tích hợp chặt với Bizino ERP/CRM và Bizino AI (chatbot bán hàng, gợi ý sản phẩm, trả lời CSKH).
3. **Tháng 9 trở đi: chuyển khách pilot sang Bizino Commerce** bằng công cụ migrate dữ liệu từ schema CRMEB.

Nếu cần ra thị trường nhanh hơn mà vẫn muốn mô hình sàn/đa cửa hàng: cân nhắc **mua bản thương mại đa thương nhân của CRMEB** và đàm phán quyền white-label. Hướng này vẫn giữ nguyên nợ kỹ thuật PHP/Vue 2.

## 6. Đã làm trong PR này: Việt hóa 100%

Chi tiết xem mục "Việt hóa" trong `README.md` và mô tả PR.
- Dịch toàn bộ chuỗi giao diện, thông báo API, dữ liệu mẫu SQL, chú thích code, tài liệu từ tiếng Trung sang tiếng Việt, dùng công cụ trích xuất theo AST nên không làm hỏng code.
- Đặt **tiếng Việt (vi-VN) làm ngôn ngữ mặc định**. Chỉ bật Tiếng Việt và English; các ngôn ngữ khác vẫn còn nhưng tắt, có thể bật lại trong trang quản trị.
- Đổi múi giờ sang `Asia/Ho_Chi_Minh`.
- Build lại trang quản trị và H5 từ mã nguồn đã dịch.

**Cố ý giữ nguyên**
- Gói ngôn ngữ zh-CN/zh-TW (ngôn ngữ tùy chọn).
- Văn bản license gốc: đã thêm bản dịch tham khảo `LICENSE.vi.txt`.
- Dữ liệu tỉnh/thành Trung Quốc trong `eb_system_city`: **đã xóa các mục Hoàng Sa/Trường Sa**, phần còn lại cần thay bằng dữ liệu 2 cấp của Việt Nam (mục 3.4).
- Danh sách hãng vận chuyển trong `eb_express`: đã thay bằng các hãng Việt Nam.
- Ảnh quảng cáo có chữ Trung trong `readme/pic`.
