<template>
	<view :style="colorStyle">
		<view class='payment-status'>
			<!--Khi thất bại: dùng icon-iconfontguanbi fail thay cho icon-duihao2 bg-color-->
			<view class='iconfont icons icon-duihao2 bg-color'></view>
			<!-- Khi thất bại: đổi sản phẩm thất bại -->
			<view class='status' v-if="order_pay_info.pay_type != 'offline'">{{$t(`Đổi sản phẩm thành công`)}}
			</view>
			<view class='status' v-else>{{$t(`Tạo đơn hàng thành công`)}}</view>
			<view class='wrapper'>
				<view class='item acea-row row-between-wrapper'>
					<view>{{$t(`Mã đơn hàng`)}}</view>
					<view class='itemCom'>{{orderId}}</view>
				</view>
				<view class='item acea-row row-between-wrapper'>
					<view>{{$t(`Thời gian đổi`)}}</view>
					<view class='itemCom'>{{order_pay_info.add_time}}</view>
				</view>
				<view class='item acea-row row-between-wrapper'>
					<view>{{$t(`Hình thức đổi`)}}</view>
					<view class='itemCom'>{{$t(`Đổi bằng điểm thưởng`)}}</view>
				</view>
				<view class='item acea-row row-between-wrapper'>
					<view>{{$t(`Điểm thưởng thanh toán`)}}</view>
					<view class='itemCom'>{{order_pay_info.total_price}}</view>
				</view>
				<!--Khi thất bại thì thêm cái này  -->
				<view class='item acea-row row-between-wrapper'
					v-if="order_pay_info.paid==0 && order_pay_info.pay_type != 'offline'">
					<view>{{$t(`Lý do thất bại`)}}</view>
					<view class='itemCom'>{{status==2 ? $t(`Hủy đổi`):msg}}</view>
				</view>
			</view>
			<!--Khi thất bại: mua lại -->
			<view @tap="goOrderDetails">
				<button formType="submit" class='returnBnt bg-color' hover-class='none'>{{$t(`Xem chi tiết`)}}</button>
			</view>
			<button @click="goIndex" class='returnBnt cart-color' formType="submit" hover-class='none'>{{$t(`Về trang chủ`)}}</button>
		</view>
		<!-- #ifdef MP -->
		<!-- <authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		integralOrderDetails,
	} from '@/api/activity.js';
	import {
		openOrderSubscribe
	} from '@/utils/SubscribeMessage.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	import colors from "@/mixins/color";
	export default {
		components: {
			// #ifdef MP
			authorize
			// #endif
		},
		mixins: [colors],
		data() {
			return {
				orderId: '',
				order_pay_info: {
					paid: 1,
					_status: {}
				},
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				status: 0,
				msg: '',
				couponsHidden: true,
				couponList: []
			};
		},
		computed: mapGetters(['isLogin']),
		watch: {
			isLogin: {
				handler: function(newV, oldV) {
					if (newV) {
						this.getOrderPayInfo();
					}
				},
				deep: true
			}
		},
		onLoad: function(options) {
			if (!options.order_id) return this.$util.Tips({
				title: this.$t(`Thiếu tham số, không thể xem trạng thái đổi thưởng của đơn hàng`)
			}, {
				tab: 3,
				url: 1
			});
			this.orderId = options.order_id;
			this.status = options.status || 0;
			this.msg = options.msg || '';
			if (this.isLogin) {
				this.getOrderPayInfo();
			} else {
				toLogin();
			}
			// #ifdef H5
			document.addEventListener('visibilitychange', (e) => {
				let state = document.visibilityState
				if (state == 'hidden') {
					console.log(this.$t(`Người dùng đã rời đi`));
				}
				if (state == 'visible') {
					this.getOrderPayInfo();
				}
			});
			// #endif
		},
		methods: {
			openTap() {
				this.$set(this, 'couponsHidden', !this.couponsHidden);
			},
			onLoadFun: function() {
				this.getOrderPayInfo();
			},
			/**
			 * 
			 * Sau khi đổi xong thì kiểm tra trạng thái đổi
			 * 
			 */
			getOrderPayInfo: function() {
				let that = this;
				uni.showLoading({
					title: that.$t(`Đang tải`)
				});
				integralOrderDetails(that.orderId).then(res => {
					uni.hideLoading();
					that.$set(that, 'order_pay_info', res.data);
					uni.setNavigationBarTitle({
						title: that.$t(`Đổi thành công`)
					});
					that.getOrderCoupon();
				}).catch(err => {
					uni.hideLoading();
				});
			},
			getOrderCoupon() {
				let that = this;
				orderCoupon(that.orderId).then(res => {
					that.couponList = res.data;
				})
			},
			/**
			 * Về trang chủ và đóng tất cả trang hiện tại
			 */
			goIndex: function(e) {
				uni.switchTab({
					url: '/pages/index/index'
				})
			},
			/**
			 * 
			 * Đến trang chi tiết đơn hàng
			 */
			goOrderDetails: function(e) {
				let that = this;
				uni.navigateTo({
					url: '/pages/points_mall/integral_order_details?order_id=' + that.orderId
				})
			}
		}
	}
</script>

<style lang="scss">
	.coupons {
		.title {
			margin: 30rpx 0 25rpx 0;

			.line {
				width: 70rpx;
				height: 1px;
				background: #DCDCDC;
			}

			.name {
				font-size: 24rpx;
				color: #999;
				margin: 0 10rpx;
			}
		}

		.list {
			padding: 0 20rpx;

			.item {
				margin-bottom: 20rpx;
				box-shadow: 0px 2px 10px 0px rgba(0, 0, 0, 0.06);

				.price {
					width: 236rpx;
					height: 160rpx;
					font-size: 26rpx;
					color: #fff;
					font-weight: 800;

					text {
						font-size: 54rpx;
					}
				}

				.text {
					width: 385rpx;

					.name {
						font-size: #282828;
						font-size: 30rpx;
					}

					.priceMin {
						font-size: 24rpx;
						color: #999;
						margin-top: 10rpx;
					}

					.time {
						font-size: 24rpx;
						color: #999;
						margin-top: 15rpx;
					}
				}
			}

			.open {
				font-size: 24rpx;
				color: #999;
				margin-top: 30rpx;

				.iconfont {
					font-size: 25rpx;
					margin: 5rpx 0 0 10rpx;
				}
			}
		}
	}

	.payment-status {
		background-color: #fff;
		margin: 195rpx 30rpx 0 30rpx;
		border-radius: 10rpx;
		padding: 1rpx 0 28rpx 0;
	}

	.payment-status .icons {
		font-size: 70rpx;
		width: 140rpx;
		height: 140rpx;
		border-radius: 50%;
		color: #fff;
		text-align: center;
		line-height: 140rpx;
		text-shadow: 0px 4px 0px rgba(255,255,255,0.5);
		border: 6rpx solid #f5f5f5;
		margin: -76rpx auto 0 auto;
		background-color: #999;
	}

	.payment-status .icons.icon-iconfontguanbi {
		text-shadow: 0px 4px 0px #6c6d6d;
	}

	.payment-status .iconfont.fail {
		text-shadow: 0px 4px 0px #7a7a7a;
	}

	.payment-status .status {
		font-size: 32rpx;
		font-weight: bold;
		text-align: center;
		margin: 25rpx 0 37rpx 0;
	}

	.payment-status .wrapper {
		border: 1rpx solid #eee;
		margin: 0 30rpx 47rpx 30rpx;
		padding: 35rpx 0;
		border-left: 0;
		border-right: 0;
	}

	.payment-status .wrapper .item {
		font-size: 28rpx;
		color: #282828;
	}

	.payment-status .wrapper .item~.item {
		margin-top: 20rpx;
	}

	.payment-status .wrapper .item .itemCom {
		color: #666;
	}

	.payment-status .returnBnt {
		width: 630rpx;
		height: 86rpx;
		border-radius: 50rpx;
		color: #fff;
		font-size: 30rpx;
		text-align: center;
		line-height: 86rpx;
		margin: 0 auto 20rpx auto;
	}
</style>
