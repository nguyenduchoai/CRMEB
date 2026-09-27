import config from '../../package.json';

// 1. Cache vĩnh viễn của trình duyệt window.localStorage
export const Local = {
  // Xem changelog phiên bản v2.4.3
  setKey(key) {
    // @ts-ignore
    return `${config.name}:${key}`;
  },
  // Đặt cache vĩnh viễn
  set(key, val) {
    window.localStorage.setItem(Local.setKey(key), JSON.stringify(val));
  },
  // Lấy cache vĩnh viễn
  get(key) {
    let json = window.localStorage.getItem(Local.setKey(key));
    return JSON.parse(json);
  },
  // Xóa cache vĩnh viễn
  remove(key) {
    window.localStorage.removeItem(Local.setKey(key));
  },
  // Xóa toàn bộ cache vĩnh viễn
  clear() {
    window.localStorage.clear();
  },
};

// 2. Cache tạm thời của trình duyệt window.sessionStorage
export const Session = {
  // Đặt cache tạm thời
  set(key, val) {
    window.sessionStorage.setItem(Local.setKey(key), JSON.stringify(val));
  },
  // Lấy cache tạm thời
  get(key) {
    let json = window.sessionStorage.getItem(Local.setKey(key));
    return JSON.parse(json);
  },
  // Xóa cache tạm thời
  remove(key) {
    window.sessionStorage.removeItem(Local.setKey(key));
  },
  // Xóa toàn bộ cache tạm thời
  clear() {
    window.sessionStorage.clear();
  },
};
