<template>
	<view :style="colorStyle">
		<view class='order-details'>
			<view v-if="orderInfo && orderInfo.invoice" class='header bg-color acea-row row-middle'>
				<view class='iconfont icon-fapiao1'></view>
				<view class='data'>
					<view class='state'>{{orderInfo.invoice.is_invoice ? $t(`Đã xuất hóa đơn`) : $t(`Chưa xuất hóa đơn`)}}</view>
					<view>{{orderInfo.invoice.add_time}}</view>
				</view>
			</view>
			<view v-if="orderInfo && orderInfo.invoice" class="wrapper">
				<view class="item acea-row row-between-wrapper">
					<view>{{$t(`Loại hóa đơn`)}}</view>
					<view class="conter">{{orderInfo.invoice.type === 1 ? $t(`Hóa đơn điện tử VAT thông thường`) : $t(`Hóa đơn điện tử VAT chuyên dụng`)}}</view>
				</view>
				<view class="item acea-row row-between-wrapper">
					<view>{{$t(`Tiêu đề hóa đơn`)}}</view>
					<view class="conter">{{orderInfo.invoice.name}}</view>
				</view>
				<view v-if="orderInfo.invoice.duty_number" class="item acea-row row-between-wrapper">
					<view>{{$t(`Mã số thuế`)}}</view>
					<view class="conter">{{orderInfo.invoice.duty_number}}</view>
				</view>
				<view class="item acea-row row-between-wrapper">
					<view>{{$t(`Số điện thoại`)}}</view>
					<view class="conter">{{orderInfo.invoice.drawer_phone}}</view>
				</view>
				<view class="item acea-row row-between-wrapper">
					<view>{{$t(`Email`)}}</view>
					<view class="conter">{{orderInfo.invoice.email}}</view>
				</view>
				<template v-if="orderInfo.invoice.type === 2">
					<view class="item acea-row row-between-wrapper">
						<view>{{$t(`Ngân hàng mở tài khoản`)}}</view>
						<view class="conter">{{orderInfo.invoice.bank}}</view>
					</view>
					<view class="item acea-row row-between-wrapper">
						<view>{{$t(`Số tài khoản ngân hàng`)}}</view>
						<view class="conter">{{orderInfo.invoice.card_number}}</view>
					</view>
					<view class="item acea-row row-between-wrapper">
						<view>{{$t(`Địa chỉ doanh nghiệp`)}}</view>
						<view class="conter">{{orderInfo.invoice.address}}</view>
					</view>
					<view class="item acea-row row-between-wrapper">
						<view>{{$t(`Điện thoại doanh nghiệp`)}}</view>
						<view class="conter">{{orderInfo.invoice.tell}}</view>
					</view>
				</template>
				<view class="item acea-row row-between-wrapper" v-if='orderInfo.invoice.invoice_number'>
					<view>{{$t(`Số hóa đơn`)}}</view>
					<view class="conter">{{orderInfo.invoice.invoice_number}}</view>
				</view>
				<view class="item acea-row row-between-wrapper" v-if='orderInfo.invoice.remark'>
					<view>{{$t(`Ghi chú hóa đơn`)}}</view>
					<view class="conter">{{orderInfo.invoice.remark}}</view>
				</view>
			</view>
			<orderGoods :evaluate='evaluate' :orderId="order_id" :cartInfo="cartInfo" :jump="true" :paid="orderInfo.paid" :oid="orderInfo.id" :isShow="false" :statusType="status.type"></orderGoods>
			<view class='wrapper'>
				<view class='item acea-row row-between'>
					<view>{{$t(`Mã đơn hàng`)}}：</view>
					<view class='conter acea-row row-middle row-right'>{{orderInfo.order_id}}
						<!-- #ifndef H5 -->
						<text class='copy' @tap='copy'>{{$t(`Sao chép`)}}</text>
						<!-- #endif -->
						<!-- #ifdef H5 -->
						<text class='copy copy-data' :data-clipboard-text="orderInfo.order_id">{{$t(`Sao chép`)}}</text>
						<!-- #endif -->
					</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Thời gian đặt hàng`)}}：</view>
					<view class='conter'>{{(orderInfo.add_time_y || '') +' '+(orderInfo.add_time_h || 0)}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Trạng thái thanh toán`)}}：</view>
					<view class='conter' v-if="orderInfo.paid">{{$t(`Đã thanh toán`)}}</view>
					<view class='conter' v-else>{{$t(`Chưa thanh toán`)}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Phương thức thanh toán`)}}：</view>
					<view class='conter'>{{orderInfo._status._payType}}</view>
				</view>
				<view class='item acea-row row-between' v-if="orderInfo.mark">
					<view>{{$t(`Lời nhắn của người mua`)}}：</view>
					<view class='conter'>{{orderInfo.mark}}</view>
				</view>
				<view class='item acea-row row-between' v-if="orderInfo.fictitious_content">
					<view>{{$t(`Ghi chú`)}}：</view>
					<view class='conter'>{{orderInfo.fictitious_content}}</view>
				</view>
			</view>
			<!-- Chi tiết đơn hoàn tiền -->
			<view class='wrapper' v-if="isGoodsReturn">
				<view class='item acea-row row-between'>
					<view>{{$t(`Người nhận hàng`)}}：</view>
					<view class='conter'>{{orderInfo.real_name}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Số điện thoại liên hệ`)}}：</view>
					<view class='conter'>{{orderInfo.user_phone}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Địa chỉ nhận hàng`)}}：</view>
					<view class='conter'>{{orderInfo.user_address}}</view>
				</view>
			</view>
			<view v-if="orderInfo.status!=0">
				<view class='wrapper' v-if='orderInfo.delivery_type=="express"'>
					<view class='item acea-row row-between'>
						<view>{{$t(`Phương thức giao hàng`)}}：</view>
						<view class='conter'>{{$t(`Giao hàng`)}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>{{$t(`Đơn vị vận chuyển`)}}：</view>
						<view class='conter'>{{orderInfo.delivery_name || ''}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>{{$t(`Mã vận đơn`)}}：</view>
						<view class='conter'>{{orderInfo.delivery_id || ''}}</view>
					</view>
				</view>
				<view class='wrapper' v-else-if='orderInfo.delivery_type=="send"'>
					<view class='item acea-row row-between'>
						<view>{{$t(`Phương thức giao hàng`)}}：</view>
						<view class='conter'>{{$t(`Cửa hàng tự giao`)}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>{{$t(`Tên nhân viên giao hàng`)}}：</view>
						<view class='conter'>{{orderInfo.delivery_name || ''}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>{{$t(`Số điện thoại liên hệ`)}}：</view>
						<view class='conter acea-row row-middle row-right'>{{orderInfo.delivery_id || ''}}<text class='copy' @tap='goTel'>{{$t(`dial`)}}</text></view>
					</view>
				</view>
				<view class='wrapper' v-else-if='orderInfo.delivery_type=="fictitious"'>
					<view class='item acea-row row-between'>
						<view>{{$t(`Giao hàng ảo`)}}：</view>
						<view class='conter'>{{$t(`Đã giao hàng, vui lòng chú ý nhận hàng`)}}</view>
					</view>
				</view>
			</view>
			<view class='wrapper'>
				<view class='item acea-row row-between'>
					<view>{{$t(`Số tiền thanh toán`)}}：</view>
					<view class='conter'>{{$t(`￥`)}}{{orderInfo.pay_price}}</view>
				</view>
				<!-- <view class='item acea-row row-between' v-if='orderInfo.coupon_id'>
					<view>{{$t(`Khấu trừ phiếu giảm giá`)}}:</view>
					<view class='conter'>-{{$t(`￥`)}}{{orderInfo.coupon_price}}</view>
				</view>
				<view class='item acea-row row-between' v-if="orderInfo.use_integral > 0">
					<view>{{$t(`Khấu trừ điểm thưởng`)}}:</view>
					<view class='conter'>-{{$t(`￥`)}}{{orderInfo.deduction_price}}</view>
				</view>
				<view class='item acea-row row-between' v-if="orderInfo.pay_postage > 0">
					<view>{{$t(`Phí vận chuyển`)}}:</view>
					<view class='conter'>{{$t(`￥`)}}{{orderInfo.pay_postage}}</view>
				</view> -->
			</view>
		</view>
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
	</view>
</template>
<style scoped lang="scss">
	.goodCall {
		color: #e93323;
		text-align: center;
		width: 100%;
		height: 86rpx;
		padding: 0 30rpx;
		border-bottom: 1rpx solid #eee;
		font-size: 30rpx;
		line-height: 86rpx;
		background: #fff;

		.icon-kefu {
			font-size: 36rpx;
			margin-right: 15rpx;
		}

		/* #ifdef MP */
		button {
			display: flex;
			align-items: center;
			justify-content: center;
			height: 86rpx;
			font-size: 30rpx;
			color: #e93323;
		}

		/* #endif */
	}

	.order-details .header {
		padding: 0 30rpx;
		height: 150rpx;
	}

	.order-details .header.on {
		background-color: #666 !important;
	}

	.order-details .header .iconfont {
		font-size: 70rpx;
		color: #fff;
	}

	.order-details .header .data {
		color: rgba(255, 255, 255, 0.8);
		font-size: 24rpx;
		margin-left: 27rpx;
	}

	.order-details .header .data.on {
		margin-left: 0;
	}

	.order-details .header .data .state {
		font-size: 30rpx;
		font-weight: bold;
		color: #fff;
		margin-bottom: 7rpx;
	}

	.order-details .header .data .time {
		margin-left: 20rpx;
	}

	.order-details .nav {
		background-color: #fff;
		font-size: 26rpx;
		color: #282828;
		padding: 25rpx 0;
	}

	.order-details .nav .navCon {
		padding: 0 40rpx;
	}

	.order-details .nav .on {
		color: #e93323;
	}

	.order-details .nav .progress {
		padding: 0 65rpx;
		margin-top: 10rpx;
	}

	.order-details .nav .progress .line {
		width: 100rpx;
		height: 2rpx;
		background-color: #939390;
	}

	.order-details .nav .progress .iconfont {
		font-size: 25rpx;
		color: #939390;
		margin-top: -2rpx;
	}

	.order-details .address {
		font-size: 26rpx;
		color: #868686;
		background-color: #fff;
		margin-top: 13rpx;
		padding: 35rpx 30rpx;
	}

	.order-details .address .name {
		font-size: 30rpx;
		color: #282828;
		margin-bottom: 15rpx;
	}

	.order-details .address .name .phone {
		margin-left: 40rpx;
	}

	.order-details .line {
		width: 100%;
		height: 3rpx;
	}

	.order-details .line image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-details .wrapper {
		background-color: #fff;
		margin-top: 12rpx;
		padding: 30rpx;
	}

	.order-details .wrapper .item {
		font-size: 28rpx;
		color: #282828;
	}

	.order-details .wrapper .item~.item {
		margin-top: 20rpx;
	}

	.order-details .wrapper .item .conter {
		color: #868686;
		width: 460rpx;
		text-align: right;
	}

	.order-details .wrapper .item .conter .copy {
		font-size: 20rpx;
		color: #333;
		border-radius: 3rpx;
		border: 1rpx solid #666;
		padding: 3rpx 15rpx;
		margin-left: 24rpx;
	}

	.order-details .wrapper .actualPay {
		border-top: 1rpx solid #eee;
		margin-top: 30rpx;
		padding-top: 30rpx;
	}

	.order-details .wrapper .actualPay .money {
		font-weight: bold;
		font-size: 30rpx;
	}

	.order-details .footer {
		width: 100%;
		height: 100rpx;
		position: fixed;
		bottom: 0;
		left: 0;
		background-color: #fff;
		padding: 0 30rpx;
		box-sizing: border-box;
	}

	.order-details .footer .bnt {
		width: 176rpx;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
		border-radius: 50rpx;
		color: #fff;
		font-size: 27rpx;
	}

	.order-details .footer .bnt.cancel {
		color: #aaa;
		border: 1rpx solid #ddd;
	}

	.order-details .footer .bnt~.bnt {
		margin-left: 18rpx;
	}

	.order-details .writeOff {
		background-color: #fff;
		margin-top: 13rpx;
		padding-bottom: 30rpx;
	}

	.order-details .writeOff .title {
		font-size: 30rpx;
		color: #282828;
		height: 87rpx;
		border-bottom: 1px solid #f0f0f0;
		padding: 0 30rpx;
		line-height: 87rpx;
	}

	.order-details .writeOff .grayBg {
		background-color: #f2f5f7;
		width: 590rpx;
		height: 384rpx;
		border-radius: 20rpx 20rpx 0 0;
		margin: 50rpx auto 0 auto;
		padding-top: 55rpx;
		position: relative;
	}

	.order-details .writeOff .grayBg .written {
		position: absolute;
		top: 0;
		right: 0;
		width: 60rpx;
		height: 60rpx;
	}

	.order-details .writeOff .grayBg .written image {
		width: 100%;
		height: 100%;
	}

	.order-details .writeOff .grayBg .pictrue {
		width: 290rpx;
		height: 290rpx;
		margin: 0 auto;
	}

	.order-details .writeOff .grayBg .pictrue image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-details .writeOff .gear {
		width: 590rpx;
		height: 30rpx;
		margin: 0 auto;
	}

	.order-details .writeOff .gear image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-details .writeOff .num {
		background-color: #f0c34c;
		width: 590rpx;
		height: 84rpx;
		color: #282828;
		font-size: 48rpx;
		margin: 0 auto;
		border-radius: 0 0 20rpx 20rpx;
		text-align: center;
		padding-top: 4rpx;
	}

	.order-details .writeOff .rules {
		margin: 46rpx 30rpx 0 30rpx;
		border-top: 1px solid #f0f0f0;
		padding-top: 10rpx;
	}

	.order-details .writeOff .rules .item {
		margin-top: 20rpx;
	}

	.order-details .writeOff .rules .item .rulesTitle {
		font-size: 28rpx;
		color: #282828;
	}

	.order-details .writeOff .rules .item .rulesTitle .iconfont {
		font-size: 30rpx;
		color: #333;
		margin-right: 8rpx;
		margin-top: 5rpx;
	}

	.order-details .writeOff .rules .item .info {
		font-size: 28rpx;
		color: #999;
		margin-top: 7rpx;
	}

	.order-details .writeOff .rules .item .info .time {
		margin-left: 20rpx;
	}

	.order-details .map {
		height: 86rpx;
		font-size: 30rpx;
		color: #282828;
		line-height: 86rpx;
		border-bottom: 1px solid #f0f0f0;
		margin-top: 13rpx;
		background-color: #fff;
		padding: 0 30rpx;
	}

	.order-details .map .place {
		font-size: 26rpx;
		width: 176rpx;
		height: 50rpx;
		border-radius: 25rpx;
		line-height: 50rpx;
		text-align: center;
	}

	.order-details .map .place .iconfont {
		font-size: 27rpx;
		height: 27rpx;
		line-height: 27rpx;
		margin: 2rpx 3rpx 0 0;
	}

	.order-details .address .name .iconfont {
		font-size: 34rpx;
		margin-left: 10rpx;
	}

	.refund {
		padding: 0 30rpx 30rpx;
		margin-top: 24rpx;
		background-color: #fff;

		.title {
			display: flex;
			align-items: center;
			font-size: 30rpx;
			color: #333;
			height: 86rpx;
			border-bottom: 1px solid #f5f5f5;

			image {
				width: 32rpx;
				height: 32rpx;
				margin-right: 10rpx;
			}
		}

		.con {
			padding-top: 25rpx;
			font-size: 28rpx;
			color: #868686;
		}
	}
</style>

<script>
	import {
		orderInvoiceDetail,
		orderAgain,
		orderTake,
		orderDel,
		orderCancel
	} from '@/api/order.js';
	import {
		openOrderRefundSubscribe
	} from '@/utils/SubscribeMessage.js';
	import home from '@/components/home';
	import payment from '@/components/payment';
	import orderGoods from "@/components/orderGoods";
	import ClipboardJS from "@/plugin/clipboard/clipboard.js";
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
			payment,
			home,
			orderGoods,
			// #ifdef MP
			authorize
			// #endif
		},
		mixins: [colors],
		data() {
			return {
				order_id: '',
				evaluate: 0,
				cartInfo: [], //Sản phẩm trong giỏ hàng
				orderInfo: {
					system_store: {},
					_status: {}
				}, //Chi tiết đơn hàng
				system_store: {},
				isGoodsReturn: false, //Có phải đơn hoàn tiền hay không
				status: {}, //Trạng thái nút ở dưới đơn hàng
				isClose: false,
				pay_close: false,
				pay_order_id: '',
				totalPrice: '0',
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false //Có ẩn ủy quyền hay không
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad: function(options) {
			if (options.order_id) {
				this.$set(this, 'order_id', options.order_id);
			}
		},
		onShow() {
			if (this.isLogin) {
				this.getOrderInfo();
			} else {
				toLogin();
			}
		},
		onHide: function() {
			this.isClose = true;
		},
		onReady: function() {
			// #ifdef H5
			this.$nextTick(function() {
				const clipboard = new ClipboardJS(".copy-data");
				clipboard.on("success", () => {
					this.$util.Tips({
						title: this.$t(`Sao chép thành công`)
					});
				});
			});
			// #endif
		},
		methods: {
			goGoodCall() {
				let self = this
				uni.navigateTo({
					url: `/pages/extension/customer_list/chat?orderId=${self.order_id}`
				})
			},
			openSubcribe: function(e) {
				let page = e;
				uni.showLoading({
					title: this.$t(`Đang tải`),
				})
				openOrderRefundSubscribe().then(res => {
					uni.hideLoading();
					uni.navigateTo({
						url: page,
					});
				}).catch(() => {
					uni.hideLoading();
				});
			},
			/**
			 * Callback sự kiện
			 * 
			 */
			onChangeFun: function(e) {
				let opt = e;
				let action = opt.action || null;
				let value = opt.value != undefined ? opt.value : null;
				(action && this[action]) && this[action](value);
			},
			/**
			 * Gọi điện
			 */
			makePhone: function() {
				uni.makePhoneCall({
					phoneNumber: this.system_store.phone
				})
			},
			/**
			 * Mở bản đồ
			 * 
			 */
			showMaoLocation: function() {
				if (!this.system_store.latitude || !this.system_store.longitude) return this.$util.Tips({
					title: this.$t(`Thiếu thông tin kinh độ, vĩ độ nên không thể xem bản đồ!`)
				});
				uni.openLocation({
					latitude: parseFloat(this.system_store.latitude),
					longitude: parseFloat(this.system_store.longitude),
					scale: 8,
					name: this.system_store.name,
					address: this.system_store.address + this.system_store.detailed_address,
					success: function() {

					},
				});
			},
			/**
			 * Callback ủy quyền đăng nhập
			 * 
			 */
			onLoadFun: function() {
				this.getOrderInfo();
			},
			/**
			 * Lấy thông tin chi tiết đơn hàng
			 * 
			 */
			getOrderInfo: function() {
				let that = this;
				uni.showLoading({
					title: that.$t(`Đang tải`)
				});
				orderInvoiceDetail(this.order_id).then(res => {
					let _type = res.data._status._type;
					uni.hideLoading();
					that.$set(that, 'orderInfo', res.data);
					that.$set(that, 'cartInfo', res.data.cartInfo);
					that.$set(that, 'evaluate', _type == 3 ? 3 : 0);
					that.$set(that, 'system_store', res.data.system_store);
					if (this.orderInfo.refund_status != 0) {
						this.isGoodsReturn = true;
					}
					that.getOrderStatus();
				}).catch(err => {
					uni.hideLoading();
					that.$util.Tips({
						title: err
					});
				});
			},
			/**
			 * 
			 * Cắt mã đơn hàng
			 */
			// #ifndef H5
			copy: function() {
				let that = this;
				uni.setClipboardData({
					data: this.orderInfo.order_id
				});
			},
			// #endif
			/**
			 * Gọi điện
			 */
			goTel: function() {
				uni.makePhoneCall({
					phoneNumber: this.orderInfo.delivery_id
				})
			},
			/**
			 * Đặt nút ở dưới
			 * 
			 */
			getOrderStatus: function() {
				let orderInfo = this.orderInfo || {},
					_status = orderInfo._status || {
						_type: 0
					},
					status = {};
				let type = parseInt(_status._type),
					delivery_type = orderInfo.delivery_type,
					seckill_id = orderInfo.seckill_id ? parseInt(orderInfo.seckill_id) : 0,
					bargain_id = orderInfo.bargain_id ? parseInt(orderInfo.bargain_id) : 0,
					combination_id = orderInfo.combination_id ? parseInt(orderInfo.combination_id) : 0;
				status = {
					type: type == 9 ? -9 : type,
					class_status: 0
				};
				if (type == 1 && combination_id > 0) status.class_status = 1; //Xem nhóm mua chung
				if (type == 2 && delivery_type == 'express') status.class_status = 2; //Xem vận chuyển
				if (type == 2) status.class_status = 3; //Xác nhận đã nhận hàng
				if (type == 4 || type == 0) status.class_status = 4; //Xóa đơn hàng
				if (!seckill_id && !bargain_id && !combination_id && (type == 3 || type == 4)) status.class_status = 5; //Mua lại
				this.$set(this, 'status', status);
			},
			/**
			 * Đến chi tiết mua chung
			 * 
			 */
			goJoinPink: function() {
				uni.navigateTo({
					url: '/pages/activity/goods_combination_status/index?id=' + this.orderInfo.pink_id,
				});
			},
			/**
			 * Mua lại
			 * 
			 */
			goOrderConfirm: function() {
				let that = this;
				orderAgain(that.orderInfo.order_id).then(res => {
					return uni.navigateTo({
						url: '/pages/goods/order_confirm/index?new=1&cartId=' + res.data.cateId
					});
				});
			},
			confirmOrder: function() {
				let that = this;
				uni.showModal({
					title: this.$t(`Xác nhận đã nhận hàng`),
					content: this.$t(`Để bảo vệ quyền lợi của bạn, vui lòng chỉ xác nhận đã nhận hàng sau khi đã nhận và kiểm tra hàng không có vấn đề`),
					success: function(res) {
						if (res.confirm) {
							orderTake(that.order_id).then(res => {
								return that.$util.Tips({
									title: that.$t(`Thao tác thành công`),
									icon: 'success'
								}, function() {
									that.getOrderInfo();
								});
							}).catch(err => {
								return that.$util.Tips({
									title: err
								});
							})
						}
					}
				})
			},
			/**
			 * 
			 * Xóa đơn hàng
			 */
			delOrder: function() {
				let that = this;
				orderDel(this.order_id).then(res => {
					return that.$util.Tips({
						title: that.$t(`Xóa thành công`),
						icon: 'success'
					}, {
						tab: 3,
						url: 1
					});
				}).catch(err => {
					return that.$util.Tips({
						title: err
					});
				});
			},
			cancelOrder() {
				let self = this
				uni.showModal({
					title: that.$t(`Thông báo`),
					content: that.$t(`Xác nhận hủy đơn hàng này`),
					success: function(res) {
						if (res.confirm) {
							orderCancel(self.orderInfo.order_id)
								.then((data) => {
									self.$util.Tips({
										title: data.msg
									}, {
										tab: 3
									})
								})
								.catch(() => {
									self.getDetail();
								});
						} else if (res.cancel) {
						}
					}
				});
			},
		}
	}
</script>

<style>
	.qs-btn {
		width: auto;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
		border-radius: 50rpx;
		color: #fff;
		font-size: 27rpx;
		padding: 0 3%;
		color: #aaa;
		border: 1px solid #ddd;
		margin-right: 20rpx;
	}
	.acea-row {
		flex-wrap: nowrap;
	}
	.wrapper .item .conter {
		width: 396rpx;
	}
</style>
