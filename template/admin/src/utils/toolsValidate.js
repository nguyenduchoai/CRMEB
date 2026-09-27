/**
 * 2020.11.29 lyt biên soạn
 * Tập hợp class tiện ích, dùng cho phát triển thường ngày
 */

/**
 * Kiểm tra tỷ lệ phần trăm (không được là số thập phân)
 * @param val Chuỗi giá trị hiện tại
 * @returns Trả về chuỗi sau khi xử lý
 */
export function verifyNumberPercentage(val) {
  // Khớp khoảng trắng
  let v = val.replace(/(^\s*)|(\s*$)/g, '');
  // Chỉ được là số và dấu chấm thập phân, không được nhập gì khác
  v = v.replace(/[^\d]/g, '');
  // Không được bắt đầu bằng 0
  v = v.replace(/^0/g, '');
  // Số vượt quá 100 thì gán thành giá trị lớn nhất 100
  v = v.replace(/^[1-9]\d\d{1,3}$/, '100');
  // Trả về kết quả
  return v;
}

/**
 * Kiểm tra tỷ lệ phần trăm (được là số thập phân)
 * @param val Chuỗi giá trị hiện tại
 * @returns Trả về chuỗi sau khi xử lý
 */
export function verifyNumberPercentageFloat(val) {
  let v = verifyNumberIntegerAndFloat(val);
  // Số vượt quá 100 thì gán thành giá trị lớn nhất 100
  v = v.replace(/^[1-9]\d\d{1,3}$/, '100');
  // Sau khi vượt quá 100 thì không cho nhập thêm giá trị
  v = v.replace(/^100\.$/, '100');
  // Trả về kết quả
  return v;
}

// Số thập phân hoặc số nguyên (không được là số âm)
export function verifyNumberIntegerAndFloat(val) {
  // Khớp khoảng trắng
  let v = val.replace(/(^\s*)|(\s*$)/g, '');
  // Chỉ được là số và dấu chấm thập phân, không được nhập gì khác
  v = v.replace(/[^\d.]/g, '');
  // Bắt đầu bằng 0 thì chỉ được nhập một số
  v = v.replace(/^0{2}$/g, '0');
  // Đảm bảo ký tự đầu chỉ được là số, không được là dấu chấm
  v = v.replace(/^\./g, '');
  // Phần thập phân chỉ được xuất hiện 1 chữ số
  v = v.replace('.', '$#$').replace(/\./g, '').replace('$#$', '.');
  // Giữ 2 chữ số sau dấu chấm thập phân
  v = v.replace(/^(\\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');
  // Trả về kết quả
  return v;
}

// Kiểm tra số nguyên dương
export function verifiyNumberInteger(val) {
  // Khớp khoảng trắng
  let v = val.replace(/(^\s*)|(\s*$)/g, '');
  // Bỏ '.', tránh lỗi khi dán, ví dụ 0.1.12.12
  v = v.replace(/[\\.]*/g, '');
  // Bỏ số đứng sau các số 0 ở đầu, tránh lỗi khi dán, ví dụ 00121323
  v = v.replace(/(^0[\d]*)$/g, '0');
  // Ký tự đầu là 0 thì chỉ được xuất hiện một lần
  v = v.replace(/^0\d$/g, '0');
  // Chỉ khớp số
  v = v.replace(/[^\d]/g, '');
  // Trả về kết quả
  return v;
}

// Bỏ chữ Hán và khoảng trắng
export function verifyCnAndSpace(val) {
  // Khớp chữ Hán và khoảng trắng
  let v = val.replace(/[\u4e00-\u9fa5\s]+/g, '');
  // Khớp khoảng trắng
  v = v.replace(/(^\s*)|(\s*$)/g, '');
  // Trả về kết quả
  return v;
}

// Bỏ chữ Anh và khoảng trắng
export function verifyEnAndSpace(val) {
  // Khớp chữ Anh và khoảng trắng
  let v = val.replace(/[a-zA-Z]+/g, '');
  // Khớp khoảng trắng
  v = v.replace(/(^\s*)|(\s*$)/g, '');
  // Trả về kết quả
  return v;
}

// Cấm nhập khoảng trắng
export function verifyAndSpace(val) {
  // Khớp khoảng trắng
  let v = val.replace(/(^\s*)|(\s*$)/g, '');
  // Trả về kết quả
  return v;
}

// Số tiền phân tách bằng `,`
export function verifyNumberComma(val) {
  // Gọi phương thức số thập phân hoặc số nguyên (không được là số âm)
  let v = verifyNumberIntegerAndFloat(val);
  // Chuyển chuỗi thành mảng
  v = v.toString().split('.');
  // \B khớp ranh giới không phải từ, hai bên đều là ký tự từ hoặc hai bên đều không phải ký tự từ
  v[0] = v[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  // Chuyển mảng thành chuỗi
  v = v.join('.');
  // Trả về kết quả
  return v;
}

// Khớp chữ đổi màu (khi tìm kiếm)
export function verifyTextColor(val, text = '', color = 'red') {
  // Trả về nội dung, thêm màu
  let v = text.replace(new RegExp(val, 'gi'), `<span style='color: ${color}'>${val}</span>`);
  // Trả về kết quả
  return v;
}

// Đọc số tiền thành chữ (tiếng Việt)
export function verifyNumberCnUppercase(val) {
  // Đọc số tiền VND thành chữ, ví dụ 1250000 -> "Một triệu hai trăm năm mươi nghìn đồng"
  const digits = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
  const units = ['', ' nghìn', ' triệu', ' tỷ', ' nghìn tỷ', ' triệu tỷ'];
  let n = Math.floor(Math.abs(Number(val) || 0));
  if (n === 0) return 'Không đồng';
  const readTriple = (num, full) => {
    const h = Math.floor(num / 100);
    const t = Math.floor((num % 100) / 10);
    const o = num % 10;
    const out = [];
    if (full || h > 0) out.push(digits[h] + ' trăm');
    if (t > 1) {
      out.push(digits[t] + ' mươi');
      if (o === 1) out.push('mốt');
      else if (o === 5) out.push('lăm');
      else if (o > 0) out.push(digits[o]);
    } else if (t === 1) {
      out.push('mười');
      if (o === 5) out.push('lăm');
      else if (o > 0) out.push(digits[o]);
    } else if (o > 0) {
      if (full || h > 0) out.push('lẻ');
      out.push(digits[o]);
    }
    return out.join(' ');
  };
  const groups = [];
  while (n > 0) {
    groups.push(n % 1000);
    n = Math.floor(n / 1000);
  }
  const parts = [];
  for (let i = groups.length - 1; i >= 0; i--) {
    if (groups[i] === 0) continue;
    parts.push(readTriple(groups[i], i < groups.length - 1) + units[i]);
  }
  const text = parts.join(' ') + ' đồng';
  return text.charAt(0).toUpperCase() + text.slice(1);
}

// Số điện thoại
export function verifyPhone(val) {
  // false: số điện thoại không đúng
  if (!/^((12[0-9])|(13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/.test(val)) return false;
  // true: số điện thoại đúng
  else return true;
}

// Số điện thoại trong nước
export function verifyTelPhone(val) {
  // false: số điện thoại trong nước không đúng
  if (!/\d{3}-\d{8}|\d{4}-\d{7}/.test(val)) return false;
  // true: số điện thoại trong nước đúng
  else return true;
}

// Tài khoản đăng nhập (bắt đầu bằng chữ, cho phép 5-16 byte, cho phép chữ số và gạch dưới)
export function verifyAccount(val) {
  // false: tài khoản đăng nhập không đúng
  if (!/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/.test(val)) return false;
  // true: tài khoản đăng nhập đúng
  else return true;
}

// Mật khẩu (bắt đầu bằng chữ, độ dài từ 6~16, chỉ được chứa chữ, số và gạch dưới)
export function verifyPassword(val) {
  // false: mật khẩu không đúng
  if (!/^[a-zA-Z]\w{5,15}$/.test(val)) return false;
  // true: mật khẩu đúng
  else return true;
}

// Mật khẩu mạnh (chữ + số + ký tự đặc biệt, độ dài từ 6-16)
export function verifyPasswordPowerful(val) {
  // false: mật khẩu mạnh không đúng
  if (
    !/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&\\.*]+$)(?![a-zA-z\d]+$)(?![a-zA-z!@#$%^&\\.*]+$)(?![\d!@#$%^&\\.*]+$)[a-zA-Z\d!@#$%^&\\.*]{6,16}$/.test(
      val,
    )
  )
    return false;
  // true: mật khẩu mạnh đúng
  else return true;
}

// Độ mạnh của mật khẩu
export function verifyPasswordStrength(val) {
  let v = '';
  // Yếu: toàn số, toàn chữ, toàn ký tự đặc biệt
  if (/^(?:\d+|[a-zA-Z]+|[!@#$%^&\\.*]+){6,16}$/.test(val)) v = 'Yếu';
  // Trung bình: chữ+số, chữ+ký tự đặc biệt, số+ký tự đặc biệt
  if (/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&\\.*]+$)[a-zA-Z\d!@#$%^&\\.*]{6,16}$/.test(val)) v = 'Chính giữa';
  // Mạnh: chữ+số+ký tự đặc biệt
  if (
    /^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&\\.*]+$)(?![a-zA-z\d]+$)(?![a-zA-z!@#$%^&\\.*]+$)(?![\d!@#$%^&\\.*]+$)[a-zA-Z\d!@#$%^&\\.*]{6,16}$/.test(
      val,
    )
  )
    v = 'Mạnh';
  // Trả về kết quả
  return v;
}

// Địa chỉ IP
export function verifyIPAddress(val) {
  // false: địa chỉ IP không đúng
  if (
    !/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/.test(
      val,
    )
  )
    return false;
  // true: địa chỉ IP đúng
  else return true;
}

// Email
export function verifyEmail(val) {
  // false: email không đúng
  if (
    !/^(([^<>()\\[\]\\.,;:\s@"]+(\.[^<>()\\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
      val,
    )
  )
    return false;
  // true: email đúng
  else return true;
}

// CCCD/CMND
export function verifyIdCard(val) {
  // false: CCCD/CMND không đúng
  if (!/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/.test(val)) return false;
  // true: CCCD/CMND đúng
  else return true;
}

// Họ tên
export function verifyFullName(val) {
  // false: họ tên không đúng
  if (!/^[\u4e00-\u9fa5]{1,6}(·[\u4e00-\u9fa5]{1,6}){0,2}$/.test(val)) return false;
  // true: họ tên đúng
  else return true;
}

// Mã bưu chính
export function verifyPostalCode(val) {
  // false: mã bưu chính không đúng
  if (!/^[1-9][0-9]{5}$/.test(val)) return false;
  // true: mã bưu chính đúng
  else return true;
}

// url
export function verifyUrl(val) {
  // false: url không đúng
  if (
    !/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(
      val,
    )
  )
    return false;
  // true: url đúng
  else return true;
}

// Biển số xe
export function verifyCarNum(val) {
  // false: biển số xe không đúng
  if (
    !/^(([京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领][A-Z](([0-9]{5}[DF])|([DF]([A-HJ-NP-Z0-9])[0-9]{4})))|([京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领][A-Z][A-HJ-NP-Z0-9]{4}[A-HJ-NP-Z0-9挂学警港澳使领]))$/.test(
      val,
    )
  )
    return false;
  // true: biển số xe đúng
  else return true;
}
