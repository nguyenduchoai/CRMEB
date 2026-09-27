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
			disabled: false,
			text: this.$t('Mã xác thực'),
			runTime: undefined,
			captchaType: 'clickWord'
		};
	},
	methods: {
		sendCode() {
			if (this.disabled) return;
			this.disabled = true;
			let n = 60;
			this.text = this.$t('Còn lại') + n + "s";
			this.runTime = setInterval(() => {
				n = n - 1;
				if (n < 0) {
					clearInterval(this.runTime);
					this.disabled = false;
					this.text = this.$t('Gửi lại mã');
					return
				}
				this.text = this.$t('Còn lại') + n + "s";
			}, 1000);
		}
	},
	onHide() {
		clearInterval(this.runTime);
	}
};
