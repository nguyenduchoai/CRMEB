<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo $Title; ?> - <?php echo $Powered; ?></title>
<link rel="stylesheet" href="./css/install.css?v=9.0" />
<link rel="stylesheet" href="./css/step1.css?v=9.0" />
<link rel="stylesheet" href="./css/theme-chalk.css">
    <script src="./js/vue2.6.11.js"></script>
<script src="./js/element-ui.js?v=9.0"></script>
</head>
<body>
<div class="wrap" id="step1">
<!--  --><?php //require './templates/header.php';?>
  <div class="title">
      <img class="logo" src="./images/install/logo-step1.png" alt="">
      <h1>Chào mừng bạn đến với CRMEB phiên bản Tiêu chuẩn</h1>
      <div class="df agreement cp">
          <div class="radio-box" :class="{'is-shock': isShock}" @click="radio = !radio">
              <img v-if="radio" src="./images/install/success.png" alt="">
          </div>
          <span @click="radio = !radio">Tôi đã đọc kỹ và đồng ý</span>
          <span class="agreements" @click.stop="isShow = 1">“Thỏa thuận sử dụng phần mềm”</span>
      </div>
      <div class="bottom tac"> <span class="btn" :class="{'more-text': radio}" @click="jump">
              Bắt đầu cài đặt</span> </div>
      <img class="solgen" src="./images/install/solgen.png" alt="">
  </div>
  <div class="section" v-if="isShow">
      <div class="main cc">
          <pre class="pact" readonly="readonly">
          <h1 class="title">Thỏa thuận cấp phép phần mềm</h1>
Điều khoản lưu ý:
    <strong>Thỏa thuận này được ký kết giữa bạn và Xi'an Zhongbang Network Technology Co., Ltd.</strong>
    Hệ thống Quản lý khách hàng + Thương mại điện tử CRMEB (sau đây gọi là “CRMEB”) do Xi'an Zhongbang Network Technology Co., Ltd. (sau đây gọi là “Zhongbang Technology”) độc lập phát triển, bản quyền Copyright (c)2014-2024, Zhongbang Technology bảo lưu mọi quyền. CRMEB là một trong những giải pháp nền tảng thương mại điện tử Internet ổn định nhất, mạnh mẽ nhất và tiên tiến nhất tại Trung Quốc, được xây dựng trên công nghệ PHP + MySQL và phát triển bằng framework ThinkPHP. CRMEB chính thức giữ quyền sửa đổi và quyền giải thích cuối cùng đối với nội dung này.
Trước khi sử dụng Hệ thống Quản lý khách hàng + Thương mại điện tử CRMEB (sau đây gọi là “Phần mềm được cấp phép” hoặc “Phần mềm này”), vui lòng đọc kỹ Thỏa thuận này, đặc biệt là các điều khoản về luật áp dụng và giải quyết tranh chấp; các điều khoản này được in đậm và bạn cần đặc biệt lưu ý. Nếu bạn có bất kỳ thắc mắc nào về Thỏa thuận, vui lòng liên hệ bộ phận chăm sóc khách hàng để được tư vấn. Nếu bạn đã tải xuống, sao chép, cài đặt hoặc sử dụng phần mềm này dưới bất kỳ hình thức nào khác, bạn được xem là đã chấp nhận Thỏa thuận này. Nếu bạn không chấp nhận toàn bộ hoặc một phần các điều khoản của Thỏa thuận này, bạn sẽ không có quyền sử dụng Phần mềm này. Vui lòng lập tức ngừng cài đặt hoặc sử dụng phần mềm này dưới bất kỳ hình thức nào khác, đồng thời xóa mọi thành phần của phần mềm mà bạn đã cài đặt hoặc lưu giữ.
Do Internet phát triển với tốc độ cao, các điều khoản được liệt kê trong Thỏa thuận này mà bạn ký kết với chúng tôi không thể liệt kê đầy đủ và bao quát toàn bộ quyền và nghĩa vụ giữa bạn và chúng tôi, và các thỏa thuận hiện có cũng không thể đảm bảo hoàn toàn đáp ứng nhu cầu phát triển trong tương lai.
Do đó, “Tuyên bố bản quyền” và các quy tắc khác đều là thỏa thuận bổ sung của Thỏa thuận này, không thể tách rời khỏi Thỏa thuận này và có hiệu lực pháp lý tương đương. Nếu bạn sử dụng Phần mềm được cấp phép, bạn được xem là đã đồng ý với các thỏa thuận bổ sung nêu trên. Nếu chúng tôi sửa đổi Thỏa thuận này hoặc các thỏa thuận bổ sung, sau khi các điều khoản được sửa đổi, vui lòng đọc kỹ và chấp nhận thỏa thuận đã sửa đổi trước khi tiếp tục sử dụng Phần mềm được cấp phép.
<br/>
I. Định nghĩa
Phần mềm (Phần mềm được cấp phép hoặc Phần mềm này): “Phần mềm” trong Thỏa thuận này là Hệ thống Quản lý khách hàng + Thương mại điện tử CRMEB, là chương trình xử lý thông tin hoặc tệp hỗ trợ gồm nhiều mô-đun hoặc chức năng, đã hoặc sẽ được tích hợp vào sản phẩm do Zhongbang Technology chỉ định; trong đó, tệp hỗ trợ cụ thể bao gồm mã nguồn, mã đích của phần mềm cũng như toàn bộ hoặc một phần hình ảnh, ảnh chụp, biểu tượng, hoạt ảnh, bản ghi âm, bản ghi hình, âm nhạc, văn bản, mã có trong phần mềm liên quan; đồng thời bao gồm mọi tài liệu dạng giấy hoặc điện tử, tài liệu kỹ thuật, v.v. liên quan đến Phần mềm được cấp phép hoặc sản phẩm của Zhongbang, mô tả chức năng, đặc điểm, nội dung, chất lượng, kiểm thử, hướng dẫn sử dụng, thỏa thuận cấp phép người dùng, v.v.
Bạn: “Bạn” trong Thỏa thuận này là cá nhân hoặc pháp nhân đơn lẻ được Zhongbang Technology cấp phép hợp pháp quyền sử dụng Phần mềm này; pháp nhân bao gồm công ty, doanh nghiệp, cơ quan, tổ chức hoặc đơn vị.
Chúng tôi: “Chúng tôi” trong Thỏa thuận này là CRMEB chính thức, tức Zhongbang Technology, nghĩa là Xi'an Zhongbang Network Technology Co., Ltd. và các công ty liên kết của công ty này.
Phát triển thứ cấp: “Phát triển thứ cấp” trong Thỏa thuận này là việc tùy chỉnh, sửa đổi trên phần mềm hiện có, chẳng hạn như mở rộng chức năng để đạt được chức năng bạn mong muốn; về nguyên tắc không được thay đổi lõi hệ thống gốc và khung (framework) do hệ thống thiết lập của Phần mềm này. Việc phát triển thứ cấp mà chúng tôi cho phép chỉ bao gồm việc lược bỏ, chỉnh sửa hoặc mở rộng một phần giao diện, chức năng của phần mềm, không phải là sửa đổi thực chất đối với lõi và khung.
<br/>
II. Nội dung cấp phép sử dụng phần mềm
Với điều kiện bạn tuân thủ nội dung của Thỏa thuận này, sau khi bạn mua giấy phép thương mại của phần mềm thông qua kênh hợp pháp do chúng tôi chỉ định, các quyền theo giấy phép thương mại mà Zhongbang Technology cấp cho bạn bao gồm:
1. Quyền cài đặt và sử dụng: Bạn có thể cài đặt và sử dụng Phần mềm này cho mục đích thương mại và sử dụng toàn bộ các chức năng mà Phần mềm này cung cấp.
2. Quyền liên kết một tên miền duy nhất: Trước khi cài đặt Phần mềm này, bạn phải tự chuẩn bị một tên miền và thông báo cho chúng tôi để chúng tôi liên kết tên miền đó với Phần mềm này. Tên miền được liên kết là địa chỉ duy nhất gắn với phần mềm được cấp phép thương mại. Bạn phải đảm bảo tính duy nhất và tính hợp lệ của tên miền; một khi đã được liên kết, tên miền không được tùy ý thay đổi. Tên miền do bạn tự chuẩn bị có thể là tên miền cấp cao nhất, cấp hai hoặc cấp ba, và bạn phải chịu trách nhiệm về tính hợp pháp và tính hợp lệ của tên miền. Trong quá trình sử dụng Phần mềm này, nếu cần thay đổi tên miền, bạn phải thông báo bằng văn bản cho chúng tôi trước ba ngày làm việc và trình bày trung thực vấn đề mà tên miền bị thay thế gặp phải; nếu không, chúng tôi có quyền từ chối việc thay đổi.
3. Quyền đăng ký mã cấp phép thương mại: Sau khi mua giấy phép thương mại của phần mềm thông qua kênh hợp pháp do chúng tôi chỉ định, bạn có thể dùng mã đơn hàng để đăng ký mã cấp phép thương mại trên website chính thức của chúng tôi và tải xuống chứng nhận cấp phép từ website chính thức của chúng tôi.
4. Quyền nhận chứng nhận cấp phép thương mại: Chứng nhận cấp phép thương mại tải xuống từ website chính thức của chúng tôi là bằng chứng hợp pháp cho phép bạn sử dụng phần mềm vào mục đích thương mại. Chứng nhận cấp phép này là sự cấp phép vĩnh viễn cho phép bạn sử dụng hợp pháp phần mềm theo phương thức được quy định trong Thỏa thuận này, nhưng chúng tôi không đưa ra cam kết vĩnh viễn về việc sử dụng giấy phép không giới hạn.
5. Quyền sử dụng nội dung được cấp phép: Sau khi được chúng tôi cấp phép, bạn có quyền sử dụng toàn bộ nội dung của website được xây dựng bằng Phần mềm này và tự chịu các nghĩa vụ pháp lý liên quan. Bạn có thể sửa đổi mã nguồn hoặc phong cách giao diện của CRMEB theo các điều kiện ràng buộc và trong phạm vi hạn chế mà Thỏa thuận quy định để phù hợp với yêu cầu website của bạn, nhưng phải giữ lại thông tin bản quyền của chúng tôi. Dù website của bạn sử dụng CRMEB cho toàn bộ hay chỉ cho một số chuyên mục, trang chủ của website có sử dụng CRMEB đều bắt buộc phải có liên kết đến địa chỉ website chính thức của CRMEB (www.CRMEB.com).
6. Chỉ sau khi được cấp phép thương mại, bạn mới có thể sử dụng Phần mềm này vào mục đích thương mại, đồng thời nội dung hỗ trợ kỹ thuật sẽ được xác định theo loại giấy phép đã mua. Người dùng có giấy phép thương mại có quyền phản hồi ý kiến và đưa ra đề xuất; các ý kiến và đề xuất liên quan sẽ được ưu tiên xem xét trong lần nâng cấp phần mềm tiếp theo của chúng tôi, nhưng chúng tôi không cam kết hay bảo đảm về điều này.
7. Quyền tác giả của CRMEB đã được đăng ký tại Cục Bản quyền Quốc gia nước Cộng hòa Nhân dân Trung Hoa (số đăng ký quyền tác giả tại Cục Bản quyền Quốc gia Trung Quốc: 2018SR024463) và được pháp luật cũng như các công ước quốc tế bảo hộ. Khi chưa có sự cho phép bằng văn bản của chúng tôi, không được xóa phần chân trang website và các liên kết chính thức tương ứng. Để mua giấy phép thương mại, vui lòng liên hệ Zhongbang Technology để biết hướng dẫn mới nhất.
8. Môi trường vận hành phù hợp của Phần mềm này đã được nêu rõ trong các tài liệu liên quan của phần mềm; chúng tôi không chịu bất kỳ trách nhiệm nào đối với các sự cố phát sinh do phần mềm được cài đặt trong môi trường vận hành không phù hợp.
<br/>
III. Hạn chế quyền
1. Hạn chế sử dụng đơn lẻ: Mỗi tên miền chỉ được phép liên kết một lần. Giấy phép bạn đã mua chỉ cho phép chính bạn sử dụng, không được cấp phép lại cho bất kỳ bên thứ ba nào sử dụng.
2. Hạn chế chia sẻ phần mềm: Bạn không được chia sẻ toàn bộ hoặc một phần phần mềm để cho phép nhiều người sử dụng một phần hoặc toàn bộ chức năng của phần mềm.
3. Hạn chế tách rời phần mềm: Bạn không được tách rời phần mềm để nhúng các chức năng khác nhau hoặc các phần khác nhau của phần mềm vào các hệ thống phần mềm khác.
4. Hạn chế về tính toàn vẹn của phần mềm: Bạn không được xóa bất kỳ tuyên bố bản quyền hay thông báo nào trong phần mềm, cũng không được bôi xóa, sửa đổi hoặc xóa bỏ bất kỳ nhãn hiệu hay biểu trưng nào xuất hiện trong phần mềm, trừ khi đã được chúng tôi đồng ý bằng văn bản; bạn phải thông báo bằng văn bản cho chúng tôi thông tin chi tiết về các biểu trưng cần sửa đổi để chúng tôi đánh giá nhu cầu của bạn.
5. Hạn chế về đảo ngược kỹ thuật, dịch ngược và tháo rời: Bạn không được đảo ngược kỹ thuật (reverse engineering), dịch ngược (decompile) hoặc tháo rời (disassemble) phần mềm, trừ trường hợp pháp luật quy định rõ ràng cho phép các hành vi này.
6. Hạn chế chuyển nhượng: Khi chưa có sự đồng ý bằng văn bản của Zhongbang Technology, bạn không được công khai, chuyển nhượng, cho thuê, cho mượn, cấp phép lại hoặc phân phối toàn bộ hay bất kỳ phần nào của phần mềm, hoặc bản sao lưu duy nhất của phần mềm, cho bên thứ ba.
7. Hạn chế bảo mật: Khi chưa có sự đồng ý bằng văn bản của Zhongbang Technology, bạn không được tiết lộ hiệu năng của Phần mềm này hoặc bất kỳ kết quả đánh giá, kết quả kiểm thử hay bí mật kỹ thuật nào khác cho bất kỳ bên thứ ba nào.
<br/>
IV. Bảo lưu quyền
1. Theo quy định của pháp luật, Zhongbang Technology bảo lưu mọi quyền khác thuộc về Zhongbang Technology về mặt pháp lý mà chưa được cấp rõ ràng cho bạn trong Thỏa thuận này.
2. Phần mềm này được bảo hộ bởi luật quyền tác giả, các điều ước quốc tế về quyền tác giả cùng các luật hoặc điều ước quốc tế khác về sở hữu trí tuệ. Theo Thỏa thuận này, bạn chỉ được cấp quyền sử dụng phần mềm theo giấy phép thông thường, không độc quyền và không loại trừ, chứ không phải là việc bán hay chuyển nhượng phần mềm.
3. Quyền đối với nhãn hiệu: Thỏa thuận này không cấp cho bạn bất kỳ quyền nào liên quan đến bất kỳ nhãn hiệu hàng hóa hoặc nhãn hiệu dịch vụ nào của Zhongbang Technology hoặc các nhà cung cấp của Zhongbang Technology.
4. Mọi quyền sở hữu trí tuệ liên quan đến Phần mềm này, bao gồm nhưng không giới hạn ở quyền sáng chế, quyền tác giả, quyền đối với nhãn hiệu, bí mật kinh doanh và bí mật kỹ thuật, đều là tài sản của các chủ sở hữu nội dung tương ứng; Zhongbang Technology bảo lưu quyền thu lợi từ các quyền sở hữu trí tuệ mà mình sở hữu.
5. Khi chưa có sự cho phép bằng văn bản của chúng tôi, không được cho thuê, bán, thế chấp hoặc cấp giấy phép thứ cấp đối với Phần mềm này hoặc giấy phép thương mại gắn liền với phần mềm.
6. Khi chưa có sự cho phép bằng văn bản của chúng tôi, nghiêm cấm phát triển bất kỳ phiên bản phái sinh, phiên bản sửa đổi hoặc phiên bản của bên thứ ba nào dựa trên toàn bộ hoặc bất kỳ phần nào của CRMEB nhằm mục đích phân phối lại.
7. Ngay khi bạn bắt đầu xác nhận Thỏa thuận này và cài đặt CRMEB, bạn được xem là đã hoàn toàn hiểu và chấp nhận mọi điều khoản của Thỏa thuận này; đồng thời với việc được hưởng các quyền mà các điều khoản nêu trên trao cho, bạn phải chịu các ràng buộc và hạn chế liên quan. Mọi hành vi nằm ngoài phạm vi cấp phép của Thỏa thuận sẽ trực tiếp vi phạm Thỏa thuận cấp phép này và cấu thành hành vi xâm phạm quyền; chúng tôi có quyền chấm dứt việc cấp phép ngay lập tức, yêu cầu chấm dứt hành vi gây thiệt hại và bảo lưu quyền truy cứu các trách nhiệm liên quan.
<br/>
V. Quyền sở hữu trí tuệ
1. Chúng tôi sở hữu quyền tác giả, bí mật kinh doanh và các quyền sở hữu trí tuệ liên quan khác đối với Phần mềm được cấp phép, bao gồm cả các loại tài liệu liên quan đến Phần mềm được cấp phép. Các biểu trưng liên quan của Phần mềm được cấp phép thuộc quyền sở hữu trí tuệ của chúng tôi và các công ty liên kết của chúng tôi, và được pháp luật có liên quan bảo hộ.
2. Khi chưa được chúng tôi đồng ý rõ ràng, bạn không được sao chép, bắt chước, sử dụng hoặc công bố các biểu tượng nêu trên, cũng không được sửa đổi hoặc xóa bất kỳ biểu trưng, biểu tượng hoặc thông tin nhận dạng nào thể hiện chúng tôi và các công ty liên kết của chúng tôi trong sản phẩm ứng dụng.
3. Khi chưa có sự đồng ý trước bằng văn bản của chúng tôi và các công ty liên kết của chúng tôi, bạn không được tự mình thực hiện, khai thác, chuyển nhượng hoặc cho phép bất kỳ bên thứ ba nào thực hiện, khai thác, chuyển nhượng các quyền sở hữu trí tuệ nêu trên vì bất kỳ mục đích vì lợi nhuận hay phi lợi nhuận nào.
4. Trừ khi được cho phép hoặc trao quyền rõ ràng tại đây, Thỏa thuận này không liên quan đến bất kỳ hoạt động chuyển giao công nghệ nào; mọi quyền, quyền sở hữu và lợi ích mà phần mềm bao gồm và liên quan đều thuộc sở hữu riêng của chúng tôi. Trừ khi được cho phép rõ ràng tại đây, hợp đồng này không chuyển giao bất kỳ công nghệ nào cho bạn.
<br/>
VI. Phiên bản nâng cấp
1. Tùy theo nhu cầu, về sau chúng tôi sẽ thực hiện một loạt đợt nâng cấp miễn phí; bạn chỉ được hưởng quyền lợi nâng cấp phần mềm miễn phí sau khi được cấp giấy phép sử dụng thương mại. Chúng tôi có quyền quyết định thời điểm và phương thức gửi gói nâng cấp cho bạn.
2. Giấy phép đối với phiên bản nâng cấp: Nếu phần mềm được nâng cấp với sự đồng ý của Zhongbang Technology, thì trừ khi phiên bản nâng cấp có thỏa thuận cấp phép phần mềm thay thế, phiên bản nâng cấp vẫn phải tuân theo các điều khoản của Thỏa thuận này.
3. Dù phần mềm có được nâng cấp hay không, bạn đều phải tuân thủ Thỏa thuận này.
<br/>
VII. Không bảo đảm và giới hạn trách nhiệm
1. Ngoài những nội dung được Zhongbang Technology bảo đảm một cách rõ ràng, Zhongbang Technology không đưa ra bất kỳ bảo đảm ngầm định hay rõ ràng nào khác, bao gồm bảo đảm ngầm định, bảo đảm về tính phù hợp cho mục đích cụ thể và khả năng thương mại; mọi rủi ro phát sinh từ đó do bạn tự chịu.
2. Nếu Phần mềm này có tình trạng không phù hợp trong quá trình sử dụng, bạn phải lập tức phản hồi bằng văn bản cho chúng tôi; trong trường hợp công nghệ hiện có của chúng tôi có thể giải quyết, việc xử lý sẽ tuân theo quy định tại chính sách bảo hành tiêu chuẩn cho sản phẩm phần mềm của Zhongbang Technology.
1) Zhongbang Technology không chịu bất kỳ trách nhiệm rõ ràng hay ngầm định nào đối với các tổn thất phát sinh từ việc sử dụng phần mềm trong thời gian dùng thử và phần mềm dùng thử miễn phí.
2) Toàn bộ trách nhiệm mà Zhongbang Technology phải chịu được giới hạn trong số tiền bạn đã thanh toán để mua phần mềm này.
3. Chúng tôi không chịu bất kỳ trách nhiệm nào và cũng không đưa ra bất kỳ bảo đảm nào đối với các vấn đề trong quá trình sử dụng phần mềm phát sinh do sự cố ngoài ý muốn, lạm dụng, sử dụng sai cách hoặc tự ý sửa đổi. Đối với trường hợp phần mềm không thể sử dụng hoặc gây ra tổn thất do sản phẩm phần mềm bị tấn công, do các yếu tố bất khả kháng như thiên tai, hoặc do các nguyên nhân không thuộc về Zhongbang Technology, chúng tôi không chịu bất kỳ trách nhiệm nào và cũng không đưa ra bất kỳ bảo đảm nào.
4. Đối với bất kỳ tổn thất ngẫu nhiên, gián tiếp hoặc mang tính trừng phạt nào khác phát sinh từ việc sử dụng phần mềm, bao gồm nhưng không giới hạn ở tổn thất lợi nhuận kinh doanh, mất mát thông tin hoặc dữ liệu, Zhongbang Technology không chịu bất kỳ trách nhiệm nào, kể cả khi Zhongbang Technology đã được thông báo về khả năng xảy ra thiệt hại đó.
5. Trừ khi pháp luật có quy định rõ ràng, chúng tôi sẽ nỗ lực tối đa để đảm bảo Phần mềm được cấp phép cùng công nghệ và thông tin liên quan được an toàn, hiệu quả, chính xác và đáng tin cậy; tuy nhiên, do giới hạn của công nghệ hiện có, bạn hoàn toàn hiểu rằng chúng tôi không thể bảo đảm điều này. Bạn hiểu rằng chúng tôi không thể chịu trách nhiệm đối với các tổn thất trực tiếp hoặc gián tiếp của bạn phát sinh do nguyên nhân từ chính bạn, do bất khả kháng hoặc do bên thứ ba.
6. Bạn phải tự chịu mọi thiệt hại về thân thể hoặc các khoản bồi thường thiệt hại ngẫu nhiên, gián tiếp, bao gồm nhưng không giới hạn ở bồi thường thiệt hại do mất lợi nhuận, mất dữ liệu, gián đoạn kinh doanh hoặc các khoản bồi thường thiệt hại hay tổn thất thương mại khác, phát sinh từ hoặc liên quan đến bất kỳ trường hợp nào sau đây: sử dụng hoặc không thể sử dụng Phần mềm được cấp phép; bên thứ ba sử dụng Phần mềm được cấp phép hoặc thay đổi dữ liệu của bạn khi chưa được phép; chi phí và tổn thất phát sinh từ các hành vi thực hiện bằng Phần mềm được cấp phép; việc bạn hiểu sai về Phần mềm được cấp phép; các tổn thất khác liên quan đến Phần mềm được cấp phép không phải do nguyên nhân từ phía chúng tôi.
7. Mọi phần mềm khác phái sinh từ Phần mềm được cấp phép mà không do chúng tôi hoặc bên được chúng tôi ủy quyền phát triển và phát hành chính thức đều là bất hợp pháp; việc tải xuống, cài đặt, sử dụng các phần mềm này, hoặc sử dụng khi chưa liên kết với tên miền duy nhất được chỉ định, có thể dẫn đến những rủi ro không lường trước được; mọi trách nhiệm pháp lý và tranh chấp phát sinh từ đó không liên quan đến chúng tôi, và chúng tôi có quyền tạm ngừng, chấm dứt giấy phép sử dụng và/hoặc mọi dịch vụ khác.
8. Khi bạn tương tác với những người dùng khác của Phần mềm được cấp phép thông qua Phần mềm được cấp phép, mọi tổn hại về tâm lý, thể chất cũng như thiệt hại về kinh tế mà bạn phải chịu hoặc có thể phải chịu do bị dẫn dắt sai lệch hoặc bị lừa dối đều do bên vi phạm chịu toàn bộ trách nhiệm theo quy định của pháp luật.
<br/>
VIII. Điều khoản bảo mật
Mỗi bên đều phải giữ bí mật đối với kế hoạch kinh doanh, thông tin khách hàng, công nghệ, sản phẩm, mã, tài liệu và các thông tin bí mật khác thuộc bí mật kinh doanh của bên kia mà mình có thể biết được. Thông tin bí mật bao gồm mọi thông tin hữu hình hoặc vô hình được đánh dấu là bí mật. Thông tin bí mật thuộc sở hữu của bên tiết lộ; trừ khi được bên tiết lộ tuyên bố cho phép, không được tiết lộ hoặc sử dụng thông tin đó.
<br/>
IX. Chấm dứt thỏa thuận và trách nhiệm do vi phạm
1. Nếu bạn không tuân thủ một phần hoặc toàn bộ các điều khoản của Thỏa thuận này, Zhongbang Technology có thể đơn phương chấm dứt Thỏa thuận này bất cứ lúc nào. Sau khi Thỏa thuận chấm dứt, chúng tôi sẽ thu hồi giấy phép sử dụng thương mại đã cấp cho bạn, đồng thời bạn phải ngừng sử dụng phần mềm ngay lập tức và gỡ cài đặt phần mềm đã cài; nếu việc bạn vi phạm các quy định của Thỏa thuận này gây thiệt hại cho Zhongbang Technology, bạn phải chịu trách nhiệm bồi thường thiệt hại.
2. Bạn cần hiểu rằng việc sử dụng Phần mềm được cấp phép trong phạm vi được cấp phép, tôn trọng quyền sở hữu trí tuệ đối với phần mềm và nội dung trong phần mềm, sử dụng phần mềm đúng quy chuẩn và thực hiện nghĩa vụ theo quy định của Thỏa thuận này là điều kiện tiên quyết để bạn được chúng tôi cấp phép sử dụng phần mềm; nếu bạn vi phạm Thỏa thuận này, chúng tôi có quyền chấm dứt giấy phép sử dụng.
3. Việc bạn sử dụng phần mềm phụ thuộc vào các dịch vụ đi kèm do chúng tôi và các công ty liên kết cung cấp cho bạn; nếu bạn vi phạm các điều khoản, thỏa thuận, quy tắc, thông báo và các quy định liên quan khác của chúng tôi hoặc các công ty liên kết của chúng tôi, chúng tôi có quyền chấm dứt giấy phép sử dụng. Nếu việc bạn vi phạm các quy định của Thỏa thuận này gây thiệt hại cho Zhongbang Technology, bạn phải chịu trách nhiệm bồi thường các thiệt hại đã gây ra cho chúng tôi.
4. Bạn hiểu rằng, nhằm duy trì trật tự của hệ thống phần mềm và nền tảng phần mềm, nếu bạn đã đưa ra bất kỳ hình thức cam kết nào với chúng tôi và/hoặc các công ty liên kết của chúng tôi, và công ty liên quan đã xác nhận bạn vi phạm cam kết đó và thông báo cho chúng tôi xử lý theo thỏa thuận liên quan giữa bạn và công ty đó, thì chúng tôi có thể áp dụng các biện pháp hạn chế đối với giấy phép sử dụng của bạn và các quyền lợi khác mà chúng tôi có thể kiểm soát theo cam kết của bạn hoặc theo phương thức đã thỏa thuận, bao gồm tạm ngừng hoặc chấm dứt giấy phép sử dụng của bạn, đồng thời có quyền truy cứu trách nhiệm pháp lý liên quan của bạn.
5. Nếu bạn nhận Phần mềm được cấp phép từ bên thứ ba được chúng tôi ủy quyền và công nhận, bạn phải tuân thủ Thỏa thuận này và các thỏa thuận của bên thứ ba về phương thức và giới hạn sử dụng Phần mềm được cấp phép của bạn; nếu bạn vi phạm Thỏa thuận này và thỏa thuận với bên thứ ba, chúng tôi có quyền chấm dứt giấy phép sử dụng của bạn và truy cứu trách nhiệm pháp lý liên quan của bạn.
6. Bạn phải giữ bí mật các thông tin kỹ thuật như mã, tài liệu, v.v. có được từ Phần mềm này; không được xóa hoặc sửa đổi mã nguồn, tài liệu và framework, không được bẻ khóa các phần đã được mã hóa, không được mua đi bán lại Phần mềm này một cách bất hợp pháp; chúng tôi không chịu bất kỳ trách nhiệm nào đối với hậu quả của việc sử dụng phần mềm bất hợp pháp và có quyền truy cứu trách nhiệm pháp lý của bạn; bạn phải bồi thường các thiệt hại trực tiếp và gián tiếp mà hành vi xâm phạm của bạn gây ra cho chúng tôi.
7. Nếu bạn vi phạm các điều khoản quy định trong Thỏa thuận này thì cấu thành hành vi vi phạm thỏa thuận, và bạn phải chịu khoản tiền phạt vi phạm từ mười đến năm mươi lần giá bán phần mềm; nếu gây thiệt hại cho chúng tôi hoặc người dùng khác, bạn phải chịu toàn bộ trách nhiệm bồi thường (bao gồm thiệt hại trực tiếp và thiệt hại gián tiếp), bao gồm nhưng không giới hạn ở phí tư vấn, án phí, phí thi hành án, phí bảo toàn tài sản, phí bảo hiểm, phí luật sư và các chi phí khác.
<br/>
X. Luật điều chỉnh và tính độc lập của các điều khoản
1.<strong>Hiệu lực, việc giải thích, sửa đổi, thực hiện và giải quyết tranh chấp của Thỏa thuận này đều áp dụng pháp luật nước Cộng hòa Nhân dân Trung Hoa; trường hợp không có quy định pháp luật liên quan thì áp dụng theo thông lệ thương mại quốc tế chung và/hoặc thông lệ ngành. Thỏa thuận này được bạn và chúng tôi ký kết tại quận Lianhu, Xi'an, Shaanxi, nơi đặt máy chủ của chúng tôi. Đối với các tranh chấp phát sinh từ hoặc liên quan đến Thỏa thuận này, bạn có thể thương lượng hữu nghị với chúng tôi; trường hợp thương lượng không thành, tranh chấp sẽ được đưa ra Ủy ban Trọng tài Xi'an để phân xử. Phán quyết trọng tài là chung thẩm và có giá trị ràng buộc đối với cả hai bên.</strong>
2. Nếu bất kỳ điều khoản nào của Thỏa thuận này bị xác định là vô hiệu, điều đó không ảnh hưởng đến hiệu lực của các điều khoản khác hoặc bất kỳ phần nào của chúng, và bạn cùng chúng tôi vẫn phải thực hiện Thỏa thuận này một cách thiện chí.
<br/>
XI. Các lưu ý khác
1.<strong>Sản phẩm CRMEB không thu thập bất kỳ thông tin riêng tư cá nhân nào của người dùng cuối.</strong>
2. Để đảm bảo tính ổn định và tính hợp pháp về bản quyền khi bạn sử dụng sản phẩm và/hoặc dịch vụ của CRMEB, chúng tôi cần thu thập thông tin thiết bị của bạn (hệ điều hành và phiên bản phần mềm, tên miền của trang cài đặt, địa chỉ IP, thông tin trình duyệt).
<br/>
XII. Các điều khoản khác
1. Những nội dung chưa được quy định trong Thỏa thuận này sẽ do hai bên thỏa thuận riêng.
2. Tất cả các tiêu đề trong Thỏa thuận này chỉ nhằm mục đích làm nổi bật và thuận tiện cho việc đọc, bản thân chúng không mang ý nghĩa thực tế và không được dùng làm căn cứ để giải thích ý nghĩa của Thỏa thuận này.
Xi'an Zhongbang Network Technology Co., Ltd.
Ngày công bố thỏa thuận: 01/08/2017
Website chính thức của CRMEB: https://www.crmeb.com

</pre>
        </div>
        <div class="bottom" @click="agree">Tôi đã hiểu</div>
    </div>
</div>
<?php require './templates/footer.php';?>

</body>
<script>
    new Vue({
        el: '#step1',
        data() {
            return { radio: 0,isShow: 0,isShock:false }
        },
        methods:{
            jump(){
                if(this.radio==1){
                    window.location.href = "./index.php?step=2";
                } else {
                    // this.$message({
                    //     message: 'Vui lòng đọc và đồng ý “Thỏa thuận sử dụng phần mềm” trước khi thực hiện bước tiếp theo',
                    //     type: 'error'
                    // });
                    this.isShock = true
                    setTimeout(e=>{this.isShock = false},500)
                }
            },
            agree(){
                this.isShow = 0
            }
        }
    })
</script>
<script>
    console.log(`
          CCCCCCCCCCCCC  RRRRRRRRRRRRRRRRR     MMMMMMMM               MMMMMMMM  EEEEEEEEEEEEEEEEEEEEEE  BBBBBBBBBBBBBBBBB
       CCC::::::::::::C  R::::::::::::::::R    M:::::::M             M:::::::M  E::::::::::::::::::::E  B::::::::::::::::B
     CC:::::::::::::::C  R::::::RRRRRR:::::R   M::::::::M           M::::::::M  E::::::::::::::::::::E  B::::::BBBBBB:::::B
    C:::::CCCCCCCC::::C  RR:::::R     R:::::R  M:::::::::M         M:::::::::M  EE::::::EEEEEEEEE::::E  BB:::::B     B:::::B
   C:::::C       CCCCCC    R::::R     R:::::R  M::::::::::M       M::::::::::M    E:::::E       EEEEEE    B::::B     B:::::B
  C:::::C                  R::::R     R:::::R  M:::::::::::M     M:::::::::::M    E:::::E                 B::::B     B:::::B
  C:::::C                  R::::RRRRRR:::::R   M:::::::M::::M   M::::M:::::::M    E::::::EEEEEEEEEE       B::::BBBBBB:::::B
  C:::::C                  R:::::::::::::RR    M::::::M M::::M M::::M M::::::M    E:::::::::::::::E       B:::::::::::::BB
  C:::::C                  R::::RRRRRR:::::R   M::::::M  M::::M::::M  M::::::M    E:::::::::::::::E       B::::BBBBBB:::::B
  C:::::C                  R::::R     R:::::R  M::::::M   M:::::::M   M::::::M    E::::::EEEEEEEEEE       B::::B     B:::::B
  C:::::C                  R::::R     R:::::R  M::::::M    M:::::M    M::::::M    E:::::E                 B::::B     B:::::B
   C:::::C       CCCCCC  R::::R       R:::::R  M::::::M     MMMMM     M::::::M    E:::::E       EEEEEE    B::::B     B:::::B
    C:::::CCCCCCCC::::C  RR:::::R     R:::::R  M::::::M               M::::::M  EE::::::EEEEEEEE:::::E  BB:::::BBBBBB::::::B
     CC:::::::::::::::C  R::::::R     R:::::R  M::::::M               M::::::M  E::::::::::::::::::::E  B:::::::::::::::::B
       CCC::::::::::::C  R::::::R     R:::::R  M::::::M               M::::::M  E::::::::::::::::::::E  B::::::::::::::::B
          CCCCCCCCCCCCC  RRRRRRRR     RRRRRRR  MMMMMMMM               MMMMMMMM  EEEEEEEEEEEEEEEEEEEEEE  BBBBBBBBBBBBBBBBB

  Zhongbang Technology https://www.crmeb.com/
        `)
</script>
</html>