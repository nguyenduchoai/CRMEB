/**
 * @description Một directive Vue dùng để điều khiển hiển thị/ẩn của thành phần
 * @param {Object} el - Phần tử DOM mà directive gắn vào
 * @param {Object} binding - Đối tượng mà directive gắn vào
 */
const dbClick = {
  inserted(el, binding) {
    el.addEventListener('click', (e) => {
      if (!el.disabled) {
        el.disabled = true;
        el.style.cursor = 'not-allowed';
        setTimeout(() => {
          el.style.cursor = 'pointer';
          el.disabled = false;
        }, 1000);
      }
    });
  },
};

export default dbClick;
