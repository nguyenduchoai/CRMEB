import { Local } from '@/utils/storage.js';

/**
 * Kiểm tra key truyền vào có tồn tại trong mảng arr không
 * @param {string} key - Chuỗi cần kiểm tra
 * @returns {boolean} - Trả về boolean, thể hiện có quyền hay không
 */
export default function checkArray(key) {
  // seckill flash sale bargain săn giảm giá combination mua chung
  let arr = Local.get('PERMISSIONS') || ['seckill', 'bargain', 'combination']; // Định nghĩa một mảng, chứa ba loại
  let index = arr.indexOf(key); // Lấy chỉ số (index) của key trong mảng
  if (index > -1) {
    // Nếu chỉ số lớn hơn -1 thì nghĩa là key tồn tại trong mảng
    return true; // Có quyền
  } else {
    return false; // Không có quyền
  }
}
