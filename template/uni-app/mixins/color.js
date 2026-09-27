// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

export default {
	data() {
		return {
			colorStyle: '',
			colorStatus: ''
		};
	},
	created() {
		this.colorStyle = uni.getStorageSync('viewColor')
		uni.$on('ok', (data, status) => {
			this.colorStyle = data
			this.colorStatus = status
		})
	},
	methods: {}
};
