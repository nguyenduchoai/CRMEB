module.exports = {
	// Cấu hình request cho Mini Program / APP
	// #ifdef MP || APP-PLUS
	// Domain request, định dạng: https://domain-của-bạn
	HTTP_REQUEST_URL: `https://demo.crmeb.com`,
	// #endif

	// Cấu hình request cho H5
	// #ifdef H5
	// API H5 là địa chỉ trình duyệt, nếu không triển khai riêng thì không cần sửa
	HTTP_REQUEST_URL: window.location.protocol + "//" + window.location.host,
	// #endif 

	// Các cấu hình dưới đây, nếu không phát triển tùy biến thêm thì không cần sửa gì
	HEADER: {
		'content-type': 'application/json',
		//#ifdef H5
		'Form-type': navigator.userAgent.toLowerCase().indexOf("micromessenger") !== -1 ? 'wechat' : 'h5',
		//#endif
		//#ifdef MP
		'Form-type': 'routine',
		//#endif
		//#ifdef APP-VUE
		'Form-type': 'app',
		//#endif
	},
	// Tên key phiên (session), vui lòng không sửa cấu hình này
	TOKENNAME: 'Authori-zation',
	// Thời gian cache, 0 là vĩnh viễn
	EXPIRE: 0,
	//Số dòng hiển thị tối đa khi phân trang
	LIMIT: 10,
	// Giới hạn timeout request, mặc định 10 giây
	TIMEOUT: 10000
}