<template>
	<view v-if="isUp">
		<view class="mobile-bg" @click="close"></view>
		<view class="mobile-mask animated" :class="{slideInUp:isUp}">
			<view class="input-item">
				<input type="text" v-model="account" :placeholder="$t(`Nhập số điện thoại`)" maxlength="11" />
			</view>
			<view class="input-item">
				<input type="text" v-model="codeNum" :placeholder="$t(`Nhập mã xác thực`)" maxlength="6" />
				<button class="code" :disabled="disabled" @click="code">{{text}}</button>
			</view>
			<view class="sub_btn" @click="loginBtn">{{$t(`Đăng nhập ngay`)}}</view>
		</view>

		<Verify @success="success" :captchaType="captchaType" :imgSize="{ width: '330px', height: '155px' }"
			ref="verify"></Verify>
	</view>
</template>

<script>
	const app = getApp();
	import sendVerifyCode from "@/mixins/SendVerifyCode";
	import Routine from '@/libs/routine';
	import Verify from '../verify/index.vue';
	import Cache from '@/utils/cache';
	import {
		loginMobile,
		registerVerify,
		getCodeApi,
		getUserInfo,
		phoneSilenceAuth,
		phoneWxSilenceAuth
	} from "@/api/user";
	import {
		bindingPhone
	} from '@/api/api.js'
	import {
		silenceAuth
	} from '@/api/public.js'
	export default {
		name: 'login_mobile',
		components: {
			Verify
		},
		props: {
			isUp: {
				type: Boolean,
				default: false,
			},
			canClose: {
				type: Boolean,
				default: true,
			},
			authKey: {
				type: String,
				default: '',
			}
		},
		data() {
			return {
				keyCode: '',
				account: '',
				codeNum: ''
			}
		},
		mixins: [sendVerifyCode],
		mounted() {
			this.getCode();
		},
		methods: {
			success(data) {
				let that = this;
				this.$refs.verify.hide()
				getCodeApi().then(res => {
					registerVerify({
						phone: that.account,
						key: res.data.key,
						captchaType: this.captchaType,
						captchaVerification: data.captchaVerification
					}).then(res => {
						that.$util.Tips({
							title: res.msg
						});
						that.sendCode();
					}).catch(err => {
						return that.$util.Tips({
							title: err
						})
					})
				})
			},
			// Lấy mã xác thực
			code() {
				let that = this;
				if (!that.account) return that.$util.Tips({
					title: that.$t(`Vui lòng điền số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.account)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				this.$refs.verify.show();
			},
			// API lấy mã xác thực (OTP)
			getCode() {
				let that = this
				getCodeApi().then(res => {
					that.keyCode = res.data.key;
				}).catch(res => {
					that.$util.Tips({
						title: res
					});
				});
			},
			close(new_user) {
				if (this.canClose) {
					this.$emit('close', new_user)
				}
			},
			// Đăng nhập
			loginBtn() {
				let that = this
				// #ifdef MP
				if (!that.account) return that.$util.Tips({
					title: that.$t(`Vui lòng điền số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.account)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				if (!that.codeNum) return that.$util.Tips({
					title: that.$t(`Vui lòng điền mã xác thực`)
				});
				if (!/^[\w\d]+$/i.test(that.codeNum)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng mã xác thực`)
				});
				uni.showLoading({
					title: that.$t(`Đang đăng nhập`)
				});
				Routine.getCode()
					.then(code => {
						this.phoneSilenceAuth(code);
					})
					.catch(error => {
						uni.hideLoading();
					});
				// #endif
				// #ifdef H5
				if (!that.account) return that.$util.Tips({
					title: that.$t(`Vui lòng điền số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.account)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				if (!that.codeNum) return that.$util.Tips({
					title: that.$t(`Vui lòng điền mã xác thực`)
				});
				if (!/^[\w\d]+$/i.test(that.codeNum)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng mã xác thực`)
				});
				uni.showLoading({
					title: that.$t(`Đang đăng nhập`)
				});
				if (!this.authKey) {
					let key = this.$Cache.get('snsapiKey');
					this.phoneAuth(key)
				} else {
					this.phoneAuth(this.authKey)
				}
				// #endif
			},
			phoneAuth(key) {
				phoneWxSilenceAuth({
					spid: app.globalData.spid,
					spread: app.globalData.code,
					phone: this.account,
					captcha: this.codeNum,
					key
				}).then(res => {
					let time = res.data.expires_time - this.$Cache.time();
					this.$store.commit('LOGIN', {
						token: res.data.token,
						time: time
					});
					this.getUserInfo();
				}).catch(error => {
					uni.hideLoading()
					this.$util.Tips({
						title: error
					})
				})
			},
			// #ifdef MP
			phoneSilenceAuth(code) {
				let self = this
				phoneSilenceAuth({
					code: code,
					spread_spid: app.globalData.spid,
					spread_code: app.globalData.code,
					phone: this.account,
					captcha: this.codeNum
				}).then(res => {
					let time = res.data.expires_time - this.$Cache.time();
					this.$store.commit('LOGIN', {
						token: res.data.token,
						time: time
					});
					this.getUserInfo(res.data.new_user);
				}).catch(error => {
					self.$util.Tips({
						title: error
					})
				})
			},
			// #endif
			/**
			 * Lấy thông tin người dùng cá nhân
			 */
			getUserInfo: function(new_user) {
				let that = this;
				getUserInfo().then(res => {
					uni.hideLoading();
					that.userInfo = res.data
					that.$store.commit("SETUID", res.data.uid);
					that.$store.commit("UPDATE_USERINFO", res.data);
					// #ifdef MP
					if (!new_user) {
						that.$util.Tips({
							title: that.$t(`Đăng nhập thành công`),
							icon: 'success'
						}, {
							tab: 3
						})
					} else {
						that.$util.Tips({
							title: that.$t(`Đăng nhập thành công`),
							icon: 'success'
						})
					}
					that.close(new_user || 0)
					// #endif
					// #ifdef H5
					that.$emit('wechatPhone', true)
					// #endif
				});
			},
		}
	}
</script>

<style lang="stylus">
	.mobile-bg {
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
	}

	.mobile-mask {
		z-index: 20;
		position: fixed;
		left: 0;
		bottom: 0;
		width: 100%;
		padding: 67rpx 30rpx;
		background: #fff;

		.input-item {
			display: flex;
			justify-content: space-between;
			width: 100%;
			height: 86rpx;
			margin-bottom: 38rpx;

			input {
				flex: 1;
				display: block;
				height: 100%;
				padding-left: 40rpx;
				border-radius: 43rpx;
				border: 1px solid #DCDCDC;
			}

			.code {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 220rpx;
				height: 86rpx;
				margin-left: 30rpx;
				background: var(--view-minorColorT);
				font-size: 28rpx;
				color: var(--view-theme);
				border-radius: 43rpx;

				&[disabled] {
					background: rgba(0, 0, 0, 0.05);
					color: #999;
				}
			}
		}

		.sub_btn {
			width: 690rpx;
			height: 86rpx;
			line-height: 86rpx;
			margin-top: 60rpx;
			background: var(--view-theme);
			border-radius: 43rpx;
			color: #fff;
			font-size: 28rpx;
			text-align: center;
		}
	}

	.animated {
		animation-duration: .4s
	}
</style>
