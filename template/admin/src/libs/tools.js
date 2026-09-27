// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

export const forEach = (arr, fn) => {
  if (!arr.length || !fn) return;
  let i = -1;
  let len = arr.length;
  while (++i < len) {
    let item = arr[i];
    fn(item, i, arr);
  }
};

/**
 * @param {Array} arr1
 * @param {Array} arr2
 * @description Lấy giao của hai mảng, phần tử của hai mảng là số hoặc chuỗi
 */
export const getIntersection = (arr1, arr2) => {
  let len = Math.min(arr1.length, arr2.length);
  let i = -1;
  let res = [];
  while (++i < len) {
    const item = arr2[i];
    if (arr1.indexOf(item) > -1) res.push(item);
  }
  return res;
};

/**
 * @param {Array} arr1
 * @param {Array} arr2
 * @description Lấy hợp của hai mảng, phần tử của hai mảng là số hoặc chuỗi
 */
export const getUnion = (arr1, arr2) => {
  return Array.from(new Set([...arr1, ...arr2]));
};

/**
 * @param {Array} target Mảng mục tiêu
 * @param {Array} arr Mảng cần kiểm tra
 * @description Kiểm tra mảng cần kiểm tra có ít nhất một phần tử nằm trong mảng mục tiêu không
 */
export const hasOneOf = (targetarr, arr) => {
  return targetarr.some((_) => arr.indexOf(_) > -1);
};

/**
 * @param {String|Number} value Chuỗi hoặc số cần kiểm tra
 * @param {*} validList Danh sách dùng để kiểm tra
 */
export function oneOf(value, validList) {
  for (let i = 0; i < validList.length; i++) {
    if (value === validList[i]) {
      return true;
    }
  }
  return false;
}

/**
 * @param {Number} timeStamp Kiểm tra định dạng timestamp có phải là milli giây không
 * @returns {Boolean}
 */
const isMillisecond = (timeStamp) => {
  const timeStr = String(timeStamp);
  return timeStr.length > 10;
};

/**
 * @param {Number} timeStamp Timestamp truyền vào
 * @param {Number} currentTime Timestamp thời gian hiện tại
 * @returns {Boolean} Timestamp truyền vào có sớm hơn timestamp hiện tại không
 */
const isEarly = (timeStamp, currentTime) => {
  return timeStamp < currentTime;
};

/**
 * @param {Number} num Giá trị số
 * @returns {String} Chuỗi sau khi xử lý
 * @description Nếu giá trị truyền vào nhỏ hơn 10, tức chỉ có 1 chữ số, thì thêm 0 ở phía trước
 */
const getHandledValue = (num) => {
  return num < 10 ? '0' + num : num;
};

/**
 * @param {Number} timeStamp Timestamp truyền vào
 * @param {Number} startType Loại định dạng chuỗi thời gian cần trả về, truyền 'year' thì trả về thời gian đầy đủ bắt đầu bằng năm
 */
const getDate = (timeStamp, startType) => {
  const d = new Date(timeStamp * 1000);
  const year = d.getFullYear();
  const month = getHandledValue(d.getMonth() + 1);
  const date = getHandledValue(d.getDate());
  const hours = getHandledValue(d.getHours());
  const minutes = getHandledValue(d.getMinutes());
  const second = getHandledValue(d.getSeconds());
  let resStr = '';
  if (startType === 'year') resStr = year + '-' + month + '-' + date + ' ' + hours + ':' + minutes + ':' + second;
  else resStr = month + '-' + date + ' ' + hours + ':' + minutes;
  return resStr;
};

/**
 * @param {String|Number} timeStamp Dấu thời gian
 * @returns {String} Chuỗi thời gian tương đối
 */
export const getRelativeTime = (timeStamp) => {
  // Kiểm tra timestamp truyền vào là định dạng giây hay milli giây
  const IS_MILLISECOND = isMillisecond(timeStamp);
  // Nếu là định dạng milli giây thì chuyển sang định dạng giây
  if (IS_MILLISECOND) Math.floor((timeStamp /= 1000));
  // Timestamp truyền vào có thể là kiểu số hoặc chuỗi, ở đây thống nhất chuyển sang kiểu số
  timeStamp = Number(timeStamp);
  // Lấy timestamp thời gian hiện tại
  const currentTime = Math.floor(Date.parse(new Date()) / 1000);
  // Kiểm tra timestamp truyền vào có sớm hơn timestamp hiện tại không
  const IS_EARLY = isEarly(timeStamp, currentTime);
  // Lấy hiệu giữa hai timestamp
  let diff = currentTime - timeStamp;
  // Nếu IS_EARLY là false thì đảo dấu hiệu số
  if (!IS_EARLY) diff = -diff;
  let resStr = '';
  const dirStr = IS_EARLY ? 'trước' : ' nữa';
  // Nhỏ hơn hoặc bằng 59 giây
  if (diff <= 59) resStr = diff + 'giây' + dirStr;
  // Nhiều hơn 59 giây, nhỏ hơn hoặc bằng 59 phút 59 giây
  else if (diff > 59 && diff <= 3599) resStr = Math.floor(diff / 60) + 'phút' + dirStr;
  // Nhiều hơn 59 phút 59 giây, nhỏ hơn hoặc bằng 23 giờ 59 phút 59 giây
  else if (diff > 3599 && diff <= 86399) resStr = Math.floor(diff / 3600) + 'giờ' + dirStr;
  // Nhiều hơn 23 giờ 59 phút 59 giây, nhỏ hơn hoặc bằng 29 ngày 59 phút 59 giây
  else if (diff > 86399 && diff <= 2623859) resStr = Math.floor(diff / 86400) + 'ngày' + dirStr;
  // Nhiều hơn 29 ngày 59 phút 59 giây, nhỏ hơn 364 ngày 23 giờ 59 phút 59 giây, và timestamp truyền vào sớm hơn hiện tại
  else if (diff > 2623859 && diff <= 31567859 && IS_EARLY) resStr = getDate(timeStamp);
  else resStr = getDate(timeStamp, 'year');
  return resStr;
};

/**
 * @returns {String} Tên trình duyệt hiện tại
 */
export const getExplorer = () => {
  const ua = window.navigator.userAgent;
  const isExplorer = (exp) => {
    return ua.indexOf(exp) > -1;
  };
  if (isExplorer('MSIE')) return 'IE';
  else if (isExplorer('Firefox')) return 'Firefox';
  else if (isExplorer('Chrome')) return 'Chrome';
  else if (isExplorer('Opera')) return 'Opera';
  else if (isExplorer('Safari')) return 'Safari';
};

/**
 * @description Gắn sự kiện on(element, event, handler)
 */
export const on = (function () {
  if (document.addEventListener) {
    return function (element, event, handler) {
      if (element && event && handler) {
        element.addEventListener(event, handler, false);
      }
    };
  } else {
    return function (element, event, handler) {
      if (element && event && handler) {
        element.attachEvent('on' + event, handler);
      }
    };
  }
})();

/**
 * @description Gỡ sự kiện off(element, event, handler)
 */
export const off = (function () {
  if (document.removeEventListener) {
    return function (element, event, handler) {
      if (element && event) {
        element.removeEventListener(event, handler, false);
      }
    };
  } else {
    return function (element, event, handler) {
      if (element && event) {
        element.detachEvent('on' + event, handler);
      }
    };
  }
})();

/**
 * Kiểm tra một đối tượng có tồn tại key không, nếu truyền tham số thứ hai là key thì kiểm tra đối tượng obj đó có thuộc tính key này không
 * Nếu không truyền tham số key, thì kiểm tra đối tượng obj có cặp key-value nào không
 */
export const hasKey = (obj, key) => {
  if (key) return key in obj;
  else {
    let keysArr = Object.keys(obj);
    return keysArr.length;
  }
};

/**
 * @param {*} obj1 Đối tượng
 * @param {*} obj2 Đối tượng
 * @description Kiểm tra hai đối tượng có bằng nhau không, giá trị của hai đối tượng này chỉ có thể là số hoặc chuỗi
 */
export const objEqual = (obj1, obj2) => {
  const keysArr1 = Object.keys(obj1);
  const keysArr2 = Object.keys(obj2);
  if (keysArr1.length !== keysArr2.length) return false;
  else if (keysArr1.length === 0 && keysArr2.length === 0) return true;
  /* eslint-disable-next-line */ else return !keysArr1.some((key) => obj1[key] != obj2[key]);
};

/**
 * Loại bỏ trường hợp phép nhân sinh ra nhiều chữ số thập phân
 * @param arg1 giá trị trả về, arg2 tham số dùng để nhân
 */
export const accMul = (arg1, arg2) => {
  var m = 0,
    s1 = arg1.toString(),
    s2 = arg2.toString();
  try {
    m += s1.split('.')[1].length;
  } catch (e) {}
  try {
    m += s2.split('.')[1].length;
  } catch (e) {}
  return (Number(s1.replace('.', '')) * Number(s2.replace('.', ''))) / Math.pow(10, m);
};
