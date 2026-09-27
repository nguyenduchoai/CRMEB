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
		return {};
	},
	created() {},
	methods: {
		Debounce(fn, t) {
			const delay = t || 500
			let timer
			return function() {
				const args = arguments
				if (timer) {
					clearTimeout(timer)
				}
				timer = setTimeout(() => {
					timer = null
					fn.apply(this, args)
				}, delay)
			}
		}
	}
};