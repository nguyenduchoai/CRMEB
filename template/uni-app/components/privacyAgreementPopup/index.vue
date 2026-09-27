<template>
	<view :style="colorStyle">
		<view class="mask" @touchmove.prevent :hidden="isShow === false"></view>
		<view class="product-window" :class="{'on':isShow}">
			<!-- Icon đóng -->
			<!-- <view class="iconfont icon-guanbi" @click="closeAttr"></view> -->
			<view class="mp-data">
				<text class="mp-name">{{mpData.siteName}}{{$t(`Thỏa thuận dịch vụ và quyền riêng tư`)}}</text>
			</view>
			<view class="trip-msg">
				<view class="trip">
					{{$t(`Chào mừng bạn sử dụng ${mpData.siteName}! Vui lòng đọc kỹ nội dung sau và đưa ra lựa chọn phù hợp:`)}}
				</view>
			</view>
			<view class="trip-title">
				{{$t(`Tóm tắt Chính sách bảo mật`)}}
			</view>
			<view class="trip-msg">
				<view class="trip">
					{{$t(`Khi bạn nhấn đồng ý và bắt đầu sử dụng sản phẩm, dịch vụ, điều đó có nghĩa là bạn đã hiểu và đồng ý với nội dung các điều khoản này, và các điều khoản này sẽ có hiệu lực ràng buộc pháp lý đối với bạn. Nếu bạn từ chối, bạn sẽ không thể tiếp tục thao tác tiếp theo.`)}}
				</view>
			</view>
			<view class="main-color" @click.stop="privacy(3)">{{$t(`Nhấn để đọc`)}}{{agreementName}}</view>
			<view class="bottom">
				<button class="save open" type="default" id="agree-btn" open-type="agreePrivacyAuthorization"
					@agreeprivacyauthorization="handleAgree">{{$t(`Đồng ý và tiếp tục`)}}</button>
				<button class="reject" @click="rejectAgreement">
					{{$t(`Hủy`)}}
				</button>
			</view>
		</view>
	</view>

</template>

<script>
	import colors from "@/mixins/color";
	import {
		userEdit,
	} from '@/api/user.js';
	export default {
		mixins: [colors],
		data() {
			return {
				isShow: false,
				agreementName: '',
				mpData: uni.getStorageSync('copyRight'),
			};
		},
		mounted() {
			wx.getPrivacySetting({
				success: res => {
					if (res.needAuthorization) {
						// Cần hiện popup thỏa thuận quyền riêng tư
						this.isShow = true
						this.agreementName = res.privacyContractName
					} else {
						this.$emit('onAgree');
						// Người dùng đã đồng ý thỏa thuận quyền riêng tư nên không cần hiện lại popup, vẫn có thể gọi các API quyền riêng tư đã khai báo
					}
				},
				fail: () => {},
				complete: () => {}
			})
		},
		methods: {
			// Đồng ý
			handleAgree() {
				this.isShow = false
				this.$emit('onAgree');
			},
			// Từ chối
			rejectAgreement() {
				this.isShow = false
				uni.switchTab({
					url: '/pages/index/index'
				})
				this.$emit('onReject');
			},
			closeAttr() {
				this.$emit('onCloseAgePop');
			},
			// Chuyển đến thỏa thuận
			privacy(type) {
				uni.navigateTo({
					url: "/pages/users/privacy/index?type=" + type
				})
			},
		}
	}
</script>
<style>
	.pl-sty {
		color: #999999;
		font-size: 30rpx;
	}
</style>
<style scoped lang="scss">
	.product-window.on {
		transform: translate3d(0, 0, 0);
	}

	.mask {
		z-index: 99;
	}

	.product-window {
		position: fixed;
		bottom: 0;
		width: 100%;
		left: 0;
		background-color: #fff;
		z-index: 1000;
		border-radius: 40rpx 40rpx 0 0;
		transform: translate3d(0, 100%, 0);
		transition: all .3s cubic-bezier(.25, .5, .5, .9);
		padding: 64rpx 40rpx;
		padding-bottom: 38rpx;
		padding-bottom: calc(38rpx + constant(safe-area-inset-bottom)); ///Tương thích IOS<11.2/
		padding-bottom: calc(38rpx + env(safe-area-inset-bottom)); ///Tương thích IOS>11.2/
		box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.06);

		.icon-guanbi {
			position: absolute;
			top: 40rpx;
			right: 40rpx;
			font-size: 24rpx;
			font-weight: bold;
			color: #999;
		}

		.mp-data {
			display: flex;
			align-items: center;
			justify-content: center;
			margin-bottom: 40rpx;

			.mp-name {
				font-size: 34rpx;
				font-weight: 500;
				color: #333333;
				line-height: 48rpx;
			}
		}

		.trip-msg {
			padding-bottom: 32rpx;

			.title {
				font-size: 30rpx;
				font-weight: bold;
				color: #000;
				margin-bottom: 6rpx;
			}

			.trip {
				color: #333333;
				font-size: 28rpx;
				font-family: PingFang SC-Regular, PingFang SC;
				font-weight: 400;
			}
		}

		.trip-title {
			font-size: 28rpx;
			font-weight: 500;
			color: #333333;
			margin-bottom: 8rpx;
		}

		.main-color {
			font-size: 28rpx;
			font-weight: 400;
			color: var(--view-theme);
			margin-bottom: 40rpx;
		}

		.bottom {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;

			.save,
			.reject {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 670rpx;
				height: 80rpx;
				border-radius: 80rpx;
				background-color: #F5F5F5;
				color: #333;
				font-size: 30rpx;
				font-weight: 500;
			}

			.save {
				background-color: var(--view-theme);
				color: #fff;
				margin-bottom: 24rpx;
			}
		}
	}
</style>
