// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import { Message } from 'element-ui';
export function importAll(r) {
  let __modules = {};
  r.keys().forEach((key) => {
    let m = r(key).default;
    let n = m.name;
    __modules[n] = m;
  });
  return __modules;
}

export function isPicUpload(file) {
  const typeArry = ['.jpg', '.png', '.jpeg', '.JPG', '.PNG', '.JPEG', '.gif', '.GIF', '.webp', '.WEBP'];
  const type = file.name.substring(file.name.lastIndexOf('.'));
  const isImage = typeArry.indexOf(type) > -1;
  if (!isImage) {
    Message.error('Định dạng ảnh tải lên không đúng');
  }
  return isImage;
}

export function isVoiceUpload(file) {
  const typeArry = ['.mp3', '.MP3'];
  const type = file.name.substring(file.name.lastIndexOf('.'));
  const isImage = typeArry.indexOf(type) > -1;
  if (!isImage) {
    Message.error('Định dạng âm thanh tải lên không đúng');
  }
  return isImage;
}

export function isVideoUpload(file) {
  const typeArry = ['.mp4', '.MP4'];
  const type = file.name.substring(file.name.lastIndexOf('.'));
  const isImage = typeArry.indexOf(type) > -1;
  if (!isImage) {
    Message.error('Tệp tải lên phải là video định dạng mp4');
  }
  return isImage;
}

export function isFileUpload(file) {
  const typeArry = ['.doc', '.DOC', '.docx', '.xls', '.xlsx'];
  const type = file.name.substring(file.name.lastIndexOf('.'));
  const isFile = typeArry.indexOf(type) > -1;
  if (!isFile) {
    Message.error('Định dạng tệp tải lên không đúng');
  }
  return isFile;
}
export function isXlsUpload(file) {
  const typeArry = ['.xls', '.xlsx'];
  const type = file.name.substring(file.name.lastIndexOf('.'));
  const isFile = typeArry.indexOf(type) > -1;
  if (!isFile) {
    Message.error('Định dạng tệp tải lên không đúng');
  }
  return isFile;
}

export function arraysEqual(arr1, arr2) {
  // Nếu độ dài 2 mảng khác nhau, trả về false ngay
  if (arr1.length !== arr2.length) {
    return false;
  }

  // Sắp xếp 2 mảng riêng biệt
  const sortedArr1 = arr1.slice().sort();
  const sortedArr2 = arr2.slice().sort();

  // So sánh các mảng sau khi sắp xếp
  for (let i = 0; i < sortedArr1.length; i++) {
    if (sortedArr1[i] !== sortedArr2[i]) {
      return false;
    }
  }

  return true;
}
