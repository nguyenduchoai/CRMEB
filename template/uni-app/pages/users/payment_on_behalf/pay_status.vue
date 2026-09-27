<template>
	<view :style="colorStyle" class="main">
		<view class='payment-status'>
			<view class='iconfont icons icon-duihao2 bg-color'></view>
			<view class='status'>{{$t(`Thanh toán hộ thành công`)}}</view>
			<view class='wrapper'>
				<view class='itemCom'> <text class="rmb">{{$t(`￥`)}}</text> {{resData.pay_price || 0.00}}</view>
			</view>
			<view class="head-other">
				<view class="user-img">
					<image class="" :src="resData.avatar" mode=""></image>
				</view>
				<view class="order-status">
					{{$t(`Cảm ơn bạn đã thanh toán giúp mình, bạn có thể xem thêm các sản phẩm khác nhé~`)}}
				</view>
			</view>
		</view>
		<button @click="goIndex" class='returnBnt' formType="submit" hover-class='none'>{{$t(`Về trang chủ`)}}</button>
	</view>
</template>

<script>
	import {
		getOrderDetail
	} from '@/api/order.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	import colors from "@/mixins/color";
	import {
		friendDetail
	} from '@/api/user.js'
	export default {
		mixins: [colors],
		data() {
			return {
				loading: false,
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				resData: {}
			};
		},
		computed: mapGetters(['isLogin']),
		watch: {
			isLogin: {
				handler: (newV, oldV) => {
					if (newV) {
						this.getDetail();
					}
				},
				deep: true
			}
		},
		onLoad(options) {
			this.options = options
			if (!options.order_id) return this.$util.Tips({
				title: this.$t(`Thiếu tham số, không thể xem trạng thái thanh toán đơn hàng`)
			}, {
				tab: 3,
				url: 1
			});
			this.orderId = options.order_id;
		},
		onShow() {
			if (this.isLogin) {
				this.getDetail();
			} else {
				toLogin();
			}
		},
		methods: {
			getDetail() {
				friendDetail(this.orderId).then(res => {
					if (this.resData.paid == 0) {
						return this.$util.Tips({
							title: this.$t(`Đơn hàng này chưa được thanh toán`)
						}, {
							tab: 3,
							url: 1
						});
					}
					this.resData = res.data.info

				})
			},
			openTap() {
				this.$set(this, 'couponsHidden', !this.couponsHidden);
			},
			onLoadFun() {
				this.getDetail();
			},
			/**
			 * Sau khi thanh toán xong thì kiểm tra trạng thái thanh toán
			 */
			getOrderPayInfo() {
				let that = this;
				uni.showLoading({
					title: that.$t(`Đang tải`)
				});
				getOrderDetail(that.orderId).then(res => {
					uni.hideLoading();
					that.$set(that, 'order_pay_info', res.data);
					uni.setNavigationBarTitle({
						title: res.data.paid ? that.$t(`Thanh toán thành công`) : that.$t(`Chưa thanh toán`)
					});
					this.loading = true
				}).catch(err => {
					this.loading = true
					uni.hideLoading();
				});
			},
			/**
			 * Về trang chủ và đóng tất cả trang hiện tại
			 */
			goIndex(e) {
				uni.switchTab({
					url: '/pages/index/index'
				});
			},

		}
	}
</script>

<style lang="scss" scoped>
	.main {
		width: 100%;
		height: 100vh;
		background-color: #fff;

		.payment-status {
			background-color: #fff;
			margin: 0rpx 30rpx 0 30rpx;
			border-radius: 10rpx;
			padding: 94rpx 0 60rpx 0;
			color: #333;

			.icons {
				font-size: 70rpx;
				width: 140rpx;
				height: 140rpx;
				border-radius: 50%;
				color: #fff;
				text-align: center;
				line-height: 140rpx;
				border: 6rpx solid #f5f5f5;
				margin: 0rpx auto 0 auto;
				background-color: #999;
			}

			.rmb {
				font-size: 33rpx;
			}

			.status {
				font-size: 32rpx;
				font-weight: bold;
				text-align: center;
				margin: 25rpx 0 7rpx 0;
			}

			.wrapper {
				text-align: center;
				color: #333;
				font-size: 66rpx;
				font-weight: bold;
			}

			.head-other {
				display: flex;
				align-items: center;
				justify-content: center;
				padding-top: 20rpx;

				.user-img {
					display: flex;
					align-items: center;

					image {
						width: 50rpx;
						height: 50rpx;
						border-radius: 50%;
					}
				}

				.order-status {
					margin-left: 20rpx;
					color: #666666;
					padding: 6rpx 13rpx;
					font-size: 24rpx;
				}
			}

		}

		.returnBnt {
			width: 80%;
			height: 86rpx;
			border-radius: 50rpx;
			color: #fff;
			font-size: 30rpx;
			text-align: center;
			line-height: 86rpx;
			margin: 0 auto;
			background-color: var(--view-theme);
		}
	}
</style>
