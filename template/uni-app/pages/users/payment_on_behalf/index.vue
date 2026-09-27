<template>
	<view class="main" :style="colorStyle">
		<view class="head" v-if="!resData.type && !resData.paid">
			<view class="user-img">
				<image class="" :src="resData.avatar" mode=""></image>
			</view>
			<!-- paid: 0 chưa thanh toán  1 đã thanh toán  type: 0 bản thân  1 bạn bè-->
			<view class="order-status" v-if="!resData.paid && !resData.type">
				{{ $t(`Tạo đơn thanh toán hộ thành công, gửi cho bạn bè để nhờ thanh toán nhé~`) }}
			</view>
		</view>
		<view class="head-other" v-else>
			<view class="user-img">
				<image class="" :src="resData.paid && !resData.type && resData.pay_uid === $store.state.app.uid ? resData.pay_avatar : resData.avatar" mode=""></image>
			</view>
			<view class="order-status">
				<view class="head-other-nickname">
					{{ resData.paid && !resData.type && resData.pay_uid === $store.state.app.uid ? resData.pay_nickname : resData.nickname }}
				</view>
				<view class="head-other-trip" v-if="!resData.paid && resData.type">
					{{ $t(`Thanh toán giúp mình sản phẩm này nhé, cảm ơn~`) }}
				</view>
				<view class="head-other-trip" v-if="resData.pay_uid !== $store.state.app.uid && resData.paid && resData.type">
					{{ $t(`Đã có người thanh toán hộ mình rồi, cảm ơn nhé~`) }}
				</view>
				<view class="head-other-trip" v-if="resData.pay_uid === $store.state.app.uid && resData.paid && resData.type">
					{{ $t(`Cảm ơn bạn đã thanh toán giúp mình, thương bạn nhiều~`) }}
				</view>
				<view class="head-other-trip" v-if="resData.pay_uid !== resData.uid && resData.paid && !resData.type">
					{{ $t(`Mình đã thanh toán hộ bạn thành công, người bán đang khẩn trương giao hàng~`) }}
				</view>
			</view>
		</view>
		<view class="order-msg">
			<view class="pay-success" v-if="resData.paid && !resData.type">
				{{ $t(`Bạn bè đã thanh toán hộ thành công, người bán đang khẩn trương giao hàng~`) }}
			</view>
			<view v-else class="pay--box">
				<view class="order-top">
					{{ $t(`Số tiền thanh toán hộ`) }}
				</view>
				<view class="order-num">
					<text class="icon">{{ $t(`￥`) }}</text>
					{{ resData.pay_price || 0 }}
				</view>
			</view>
			<!-- #ifdef APP-PLUS -->
			<view v-if="!resData.paid && !resData.type" class="order-btn" @click="appShare('WXSceneSession')">
				{{ $t(`Gửi cho bạn bè WeChat`) }}
			</view>
			<!-- #endif -->
			<!-- #ifdef H5 -->
			<view v-if="!resData.paid && !resData.type" class="order-btn" @click="shareFriend">
				{{ $t(`Gửi cho bạn bè WeChat`) }}
			</view>
			<!-- #endif -->
			<!-- #ifdef MP -->
			<button v-if="!resData.paid && !resData.type" class="order-btn" open-type="share" hover-class="none" @click="shareModal = false">
				{{ $t(`Gửi cho bạn bè WeChat`) }}
			</button>
			<!-- #endif -->
			<button v-if="!resData.paid && !resData.type" class="order-btn detail" @click="goOrderDetail()">
				{{ $t(`Xem chi tiết đơn hàng`) }}
			</button>
			<button class="order-btn" v-if="!resData.paid && resData.type" @tap="payOpen()">{{ $t(`Thanh toán ngay`) }}</button>
			<button class="order-btn on-pay" v-if="resData.paid && resData.type">{{ $t(`Đơn hàng đã được thanh toán`) }}</button>
			<button class="order-btn" v-if="resData.paid && !resData.type" @tap="goOrderDetail()">{{ $t(`Xem chi tiết đơn hàng`) }}</button>
			<view class="order-trip" v-if="resData.pay_uid === $store.state.app.uid && resData.type">
				{{ $t(`Nếu đơn hàng được yêu cầu hoàn tiền, số tiền đã thanh toán sẽ được hoàn lại cho bạn theo phương thức thanh toán ban đầu`) }}
			</view>
		</view>
		<view class="order-list">
			<orderGoods :cartInfo="resData.cartInfo" :is_confirm="true" :pay_price="resData.pay_price" :is_behalf="resData.paid && !resData.type ? true : false"></orderGoods>
		</view>
		<view class="share-box" v-if="shareModal">
			<image src="../static/share-info2.png" @click="shareModal = false"></image>
		</view>
		<payment :payMode="payMode" :pay_close="pay_close" :friendPay="true" @onChangeFun="onChangeFun" :order_id="order_id" :totalPrice="resData.pay_price"></payment>
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
	</view>
</template>

<script>
import orderGoods from '@/components/orderGoods';
import colors from '@/mixins/color';
import payment from '@/components/payment';
import home from '@/components/home/index.vue';
import { friendDetail } from '@/api/user.js';
import { HTTP_REQUEST_URL } from '@/config/app.js';
import { toLogin } from '@/libs/login.js';
import { mapGetters } from 'vuex';

export default {
	mixins: [colors],
	components: {
		orderGoods,
		payment,
		home
	},
	computed: mapGetters(['isLogin']),
	data() {
		return {
			shareModal: false,
			cartInfo: [],
			resData: {},
			payMode: [
				{
					name: this.$t(`WeChat Pay`),
					icon: 'icon-weixinzhifu',
					value: 'weixin',
					title: this.$t(`Thanh toán nhanh qua WeChat`),
					payStatus: true
				}
				// #ifdef H5 || APP-PLUS
				// {
				// 	name: 'Thanh toán Alipay',
				// 	icon: 'icon-zhifubao',
				// 	value: 'alipay',
				// 	title: 'Sử dụng thanh toán Alipay online',
				// 	payStatus: true
				// },
				// #endif
			],
			pay_close: false,
			oid: '',
			order_id: ''
		};
	},
	watch: {
		isLogin: {
			handler: function (newV, oldV) {
				if (newV == true) {
					this.getDetail();
				}
			},
			deep: true
		}
	},
	onReady() {
		// uni.setNavigationBarTitle({
		// 	title: 'Bạn bè thanh toán hộ' || 'Thanh toán thành công'
		// });
	},
	onLoad(option) {
		this.oid = option.oid;
	},
	onShow() {
		if (this.isLogin) {
			this.getDetail();
		} else {
			toLogin();
		}
	},
	// #ifdef MP
	onShareAppMessage() {
		let that = this;
		return {
			title: '',
			imageUrl: '',
			path: '/pages/users/payment_on_behalf/index?oid=' + that.oid + '&spread=' + this.$store.state.app.uid
		};
	},
	// #endif
	methods: {
		/**
		 * Mở component thanh toán
		 *
		 */
		payOpen() {
			this.pay_close = true;
		},
		getDetail() {
			let that = this;
			friendDetail(this.oid)
				.then((res) => {
					this.resData = res.data.info;
					this.order_id = res.data.info.order_id;
					if (this.resData.paid && !this.resData.type) {
						this.goOrderDetail();
					}
					//#ifdef H5
					this.ShareInfo(this.resData);
					//#endif
				})
				.catch((err) => {
					that.$util.Tips(
						{
							title: err
						},
						{
							tab: 4,
							url: '/pages/index/index'
						}
					);
				});
		},
		//#ifdef H5
		ShareInfo(data) {
			let href = location.href;
			if (this.$wechat.isWeixin()) {
				let configAppMessage = {
					desc: this.$t(`Thanh toán giúp mình sản phẩm này nhé, cảm ơn~`),
					title: this.$t(`Bạn bè thanh toán hộ`),
					link: href,
					imgUrl: data.avatar
				};
				this.$wechat
					.wechatEvevt(['updateAppMessageShareData', 'updateTimelineShareData', 'onMenuShareAppMessage', 'onMenuShareTimeline'], configAppMessage)
					.then((res) => {})
					.catch((err) => {});
			}
		},
		//#endif
		// #ifdef APP-PLUS
		appShare(scene) {
			let that = this;
			let routes = getCurrentPages(); // Lấy mảng route các trang đã từng mở
			let curRoute = routes[routes.length - 1].$page.fullPath; // Lấy route trang hiện tại, tức route trang được mở cuối cùng
			uni.share({
				provider: 'weixin',
				scene: scene,
				type: 0,
				href: `${HTTP_REQUEST_URL}${curRoute}`,
				title: that.$t(`Bạn bè thanh toán hộ`),
				summary: that.$t(`Thanh toán giúp mình sản phẩm này nhé, cảm ơn~`),
				imageUrl: that.resData.paid && !that.resData.type && that.resData.pay_uid === that.$store.state.app.uid ? that.resData.pay_avatar : that.resData.avatar,
				success: function (res) {
					uni.showToast({
						title: that.$t(`Chia sẻ thành công`),
						icon: 'success',
						duration: 2000
					});
				},
				fail: function (err) {
					uni.showToast({
						title: that.$t(`Chia sẻ thất bại`),
						icon: 'none',
						duration: 2000
					});
				}
			});
		},
		// #endif
		shareFriend() {
			// #ifndef MP
			this.shareModal = true;
			// #endif
		},
		/**
		 * Callback sự kiện
		 *
		 */
		onChangeFun(e) {
			let opt = e;
			let action = opt.action || null;
			let value = opt.value != undefined ? opt.value : null;
			action && this[action] && this[action](value);
		},
		/**
		 * Đóng component thanh toán
		 */
		payClose() {
			this.pay_close = false;
		},
		/**
		 * Callback thanh toán thất bại
		 */
		pay_fail() {
			this.pay_close = false;
		},
		/**
		 * Callback thanh toán thành công
		 */
		pay_complete() {
			this.pay_close = false;
			this.getDetail();
			uni.navigateTo({
				url: '/pages/users/payment_on_behalf/pay_status?order_id=' + this.oid
			});
		},
		goOrderDetail() {
			uni.navigateTo({
				url: '/pages/goods/order_details/index?order_id=' + this.order_id
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.main {
	background-color: #f5f5f5;

	.head {
		background-color: var(--view-theme);
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 60rpx 0 62rpx 0;

		.user-img {
			image {
				width: 68rpx;
				height: 68rpx;
				border-radius: 50%;
			}
		}

		.order-status {
			margin-top: 20rpx;
			font-size: 28rpx;
			color: #fff;
		}
	}

	.head-other {
		background-color: var(--view-theme);
		display: flex;
		align-items: center;
		padding: 60rpx 0 60rpx 30rpx;

		.user-img {
			display: flex;
			align-items: center;

			image {
				width: 100rpx;
				height: 100rpx;
				border-radius: 50%;
			}
		}

		.order-status {
			margin-left: 20rpx;
			color: #fff;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			height: 100rpx;
			position: relative;

			.head-other-nickname {
				font-size: 28rpx;
			}

			.head-other-trip {
				padding: 6rpx 13rpx;
				font-size: 26rpx;
				background-color: rgba(255, 255, 255, 0.2);
				border-radius: 6rpx;
			}

			.head-other-trip::after {
				content: '';
				width: 0rpx;
				height: 0rpx;
				border: 10rpx solid rgba(255, 255, 255, 0.2);
				border-bottom: 10rpx solid transparent;
				border-left: 10rpx solid transparent;
				border-top: 10rpx solid transparent;
				position: absolute;
				left: -20rpx;
				bottom: 15rpx;
			}
		}
	}

	.order-msg {
		background-color: #fff;
		border-radius: 14rpx 14rpx 0 0;
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 40rpx 0;
		margin: -30rpx 30rpx 0 30rpx;

		.pay--box {
			text-align: center;
		}

		.pay-success {
			font-size: 30rpx;
			color: #333;
			font-weight: bold;
		}

		.order-num {
			.icon {
				font-size: 30rpx;
			}

			font-size: 66rpx;
			color: #333;
			font-weight: bold;
		}

		.order-btn {
			width: 90%;
			background-color: var(--view-theme);
			border-radius: 80rpx;
			padding: 26rpx 0;
			color: #fff;
			font-size: 30rpx;
			text-align: center;
			margin-top: 60rpx;
		}

		.order-btn.detail {
			margin-top: 20rpx;
			color: var(--view-theme);
			background-color: #fff;
			border: 1px solid var(--view-theme);
		}

		.order-btn.on-pay {
			background-color: #ccc;
		}

		.order-trip {
			color: #999;
			font-size: 26rpx;
			margin-top: 32rpx;
		}
	}

	.order-list {
		margin: 30rpx;
		border-radius: 14rpx;
		overflow: hidden;

		.orderGoods {
			margin-top: 0;
		}
		::v-deep .text .name {
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
	}

	.share-box {
		z-index: 1000;
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;

		image {
			width: 100%;
			height: 100%;
		}
	}
}
</style>
