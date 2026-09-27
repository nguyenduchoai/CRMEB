// Địa chỉ API request, nếu không cấu hình thì tự động lấy đường dẫn URL hiện tại
const VUE_APP_API_URL = process.env.VUE_APP_API_URL || `${location.origin}/adminapi`;

const Setting = {
  // Tiền tố route
  routePre: '/admin',
  // Địa chỉ yêu cầu API
  apiBaseURL: VUE_APP_API_URL,
  // Chế độ route, giá trị có thể chọn là history hoặc hash
  routerMode: 'history',
  // Khi chuyển trang, có hiển thị thanh tiến trình giả hay không
  showProgressBar: true,
};

export default Setting;
