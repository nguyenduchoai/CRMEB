<template>
	<view :style="colorStyle">
		<form @submit="editPwd">
			<view class="ChangePassword">
				<view class="list">
					<view class="item">
						<input type='number' :placeholder='$t(`Nhập số điện thoại`)' placeholder-class='placeholder'
							v-model="phone"></input>
					</view>
					<view class="item acea-row row-between-wrapper">
						<input type='number' :placeholder='$t(`Nhập mã xác thực`)' placeholder-class='placeholder' class="codeIput"
							v-model="captcha"></input>
						<button class="code font-num" :class="disabled === true ? 'on' : ''" :disabled='disabled'
							@click="code">
							{{ text }}
						</button>
					</view>
				</view>
				<button form-type="submit" class="confirmBnt bg-color">{{$t(`Xác nhận liên kết`)}}</button>
			</view>
		</form>

		<Verify @success="success" :captchaType="captchaType" :imgSize="{ width: '330px', height: '155px' }"
			ref="verify"></Verify>

	</view>
</template>

<script>
	import sendVerifyCode from "@/mixins/SendVerifyCode";
	import Verify from '../components/verify/index.vue';
	import {
		registerVerify,
		bindingUserPhone,
		verifyCode,
		updatePhone
	} from '@/api/api.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	import colors from '@/mixins/color.js';
	export default {
		mixins: [sendVerifyCode, colors],
		components: {
			// #ifdef MP
			authorize,
			// #endif
			Verify
		},
		data() {
			return {
				phone: '',
				captcha: '',
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				key: '',
				authKey: '',
				type: 0
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad(options) {
			if (this.isLogin) {
				verifyCode().then(res => {
					this.$set(this, 'key', res.data.key)
				});
				this.authKey = options.key || '';
				this.url = options.url || '';
			} else {
				toLogin();
			}
			this.type = options.type || 0
		},
		methods: {
			onLoadFun: function() {},
			// Đóng ủy quyền
			authColse: function(e) {
				this.isShowAuth = e
			},
			editPwd: function() {
				let that = this;
				if (!that.phone) return that.$util.Tips({
					title: that.$t(`Vui lòng điền số điện thoại`)
				});
				if (!(/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.phone))) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				if (!that.captcha) return that.$util.Tips({
					title: that.$t(`Vui lòng điền mã xác thực`)
				});
				if (this.type == 0) {
					bindingUserPhone({
						phone: that.phone,
						captcha: that.captcha
					}).then(res => {
						if (res.data !== undefined && res.data.is_bind) {
							uni.showModal({
								title: that.$t(`Xác nhận liên kết tài khoản`),
								content: res.msg,
								confirmText: that.$t(`Liên kết`),
								success(res) {
									if (res.confirm) {
										bindingUserPhone({
											phone: that.phone,
											captcha: that.captcha,
											step: 1
										}).then(res => {
											return that.$util.Tips({
												title: res.msg,
												icon: 'success'
											}, {
												tab: 5,
												url: '/pages/users/user_info/index'
											});
										}).catch(err => {
											return that.$util.Tips({
												title: err
											});
										})
									} else if (res.cancel) {
										return that.$util.Tips({
											title: that.$t(`Bạn đã hủy liên kết!`)
										}, {
											tab: 5,
											url: '/pages/users/user_info/index'
										});
									}
								}
							});
						} else
							return that.$util.Tips({
								title: that.$t(`Liên kết thành công`),
								icon: 'success'
							}, {
								tab: 5,
								url: '/pages/users/user_info/index'
							});
					}).catch(err => {
						return that.$util.Tips({
							title: err
						});
					})
				} else {
					updatePhone({
						phone: that.phone,
						captcha: that.captcha,
					}).then(res => {
						return that.$util.Tips({
							title: res.msg,
							icon: 'success'
						}, {
							tab: 5,
							url: '/pages/users/user_info/index'
						});
					}).catch(error => {
						return that.$util.Tips({
							title: error,
						});
					})
				}
			},
			success(data) {
				this.$refs.verify.hide()
				let that = this;
				verifyCode().then(res => {
					registerVerify(that.phone, 'reset', res.data.key, this.captchaType, data.captchaVerification)
						.then(res => {
							that.$util.Tips({
								title: res.msg
							});
							that.sendCode();
						}).catch(err => {
							return that.$util.Tips({
								title: err
							});
						});
				});

			},
			/**
			 * Gửi mã xác thực
			 *
			 */
			async code() {
				let that = this;
				if (!that.phone) return that.$util.Tips({
					title: that.$t(`Vui lòng điền số điện thoại`)
				});
				if (!(/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.phone))) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				this.$refs.verify.show();
				return;
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff !important;
	}

	.ChangePassword .phone {
		font-size: 32rpx;
		font-weight: bold;
		text-align: center;
		margin-top: 55rpx;
	}

	.ChangePassword .list {
		width: 580rpx;
		margin: 53rpx auto 0 auto;
	}

	.ChangePassword .list .item {
		width: 100%;
		height: 110rpx;
		border-bottom: 2rpx solid #f0f0f0;
	}

	.ChangePassword .list .item input {
		width: 100%;
		height: 100%;
		font-size: 32rpx;
	}

	.ChangePassword .list .item .placeholder {
		color: #b9b9bc;
	}

	.ChangePassword .list .item input.codeIput {
		width: 340rpx;
	}

	.ChangePassword .list .item .code {
		font-size: 32rpx;
		background-color: #fff;
	}

	.ChangePassword .list .item .code.on {
		color: #b9b9bc !important;
	}

	.ChangePassword .confirmBnt {
		font-size: 32rpx;
		width: 580rpx;
		height: 90rpx;
		border-radius: 45rpx;
		color: #fff;
		margin: 92rpx auto 0 auto;
		text-align: center;
		line-height: 90rpx;
	}
</style>
