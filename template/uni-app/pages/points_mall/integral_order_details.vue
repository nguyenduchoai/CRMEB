<template>
	<view :style="colorStyle">
		<view class='order-details'>
			<view>
				<view class='address'>
					<view class='name'>{{cartInfo.real_name}}<text class='phone'>{{cartInfo.user_phone}}</text></view>
					<view>{{cartInfo.user_address}}</view>
				</view>
				<view class='line'>
					<image src='@/static/images/line.jpg'></image>
				</view>
			</view>
			<view class="orderGoods">
				<view class='total'>{{$t(`Tổng`)}}{{cartInfo.total_num}}{{$t(`sản phẩm`)}}</view>
				<view class='goodWrapper'>
					<view class='item acea-row row-between-wrapper' @click="jumpCon(cartInfo.product_id)">
						<view class='pictrue'>
							<image :src='cartInfo.image'></image>
						</view>
						<view class='text'>
							<view class='acea-row row-between-wrapper'>
								<view class='name line1'>{{cartInfo.store_name}}</view>
								<view class='num'>x {{cartInfo.total_num}}</view>
							</view>
							<view class='attr line1'>{{cartInfo.suk}}
							</view>
							<view class='money font-num'>
								{{cartInfo.price}}{{$t(`Điểm thưởng`)}}
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class='wrapper'>
				<view class='item acea-row row-between'>
					<view>{{$t(`Mã đơn hàng`)}}：</view>
					<view class='conter acea-row row-middle row-right'>{{cartInfo.order_id}}
						<!-- #ifndef H5 -->
						<text class='copy' @tap='copy'>{{$t(`Sao chép`)}}</text>
						<!-- #endif -->
						<!-- #ifdef H5 -->
						<text class='copy copy-data' :data-clipboard-text="cartInfo.order_id">{{$t(`Sao chép`)}}</text>
						<!-- #endif -->
					</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Trạng thái đơn hàng`)}}：</view>
					<view class='conter'>{{$t(cartInfo.status_name)}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Thời gian đặt hàng`)}}：</view>
					<view class='conter'>{{cartInfo.add_time}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>{{$t(`Điểm thưởng thanh toán`)}}：</view>
					<view class='conter'>{{cartInfo.total_price}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.mark">
					<view>{{$t(`Ghi chú đơn hàng`)}}：</view>
					<view class='conter'>{{cartInfo.mark}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.remark">
					<view>{{$t(`Ghi chú của người bán`)}}：</view>
					<view class='conter'>{{cartInfo.remark}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.delivery_type === 'express'">
					<view>{{$t(`Mã vận đơn`)}}：</view>
					<view class='conter'>{{cartInfo.delivery_id}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.delivery_type === 'express'">
					<view>{{$t(`Đơn vị vận chuyển`)}}：</view>
					<view class='conter'>{{cartInfo.delivery_name}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.delivery_type === 'send'">
					<view>{{$t(`Số điện thoại người giao hàng`)}}：</view>
					<view class='conter'>{{cartInfo.delivery_id}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.delivery_type === 'send'">
					<view>{{$t(`Tên nhân viên giao hàng`)}}：</view>
					<view class='conter'>{{cartInfo.delivery_name}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.delivery_type === 'fictitious'">
					<view>{{$t(`Giao hàng ảo`)}}：</view>
					<view class='conter'>{{$t(`Đã giao hàng, vui lòng chú ý nhận hàng`)}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.fictitious_content">
					<view>{{$t(`Ghi chú sản phẩm ảo`)}}：</view>
					<view class='conter'>{{cartInfo.fictitious_content}}</view>
				</view>
				<view class='item acea-row row-between' v-if="cartInfo.delivery_type === 'send'">
					<view>{{$t(`Mã xác nhận giao hàng`)}}：</view>
					<view class='conter'>{{cartInfo.verify_code}}</view>
				</view>
			</view>

			<view style='height:120rpx;'></view>
			<view class='footer acea-row row-right row-middle'>
				<view class='bnt bg-color' v-if="cartInfo.status==3" @tap='delOrder'>{{$t(`Xóa đơn hàng`)}}</view>
				<navigator class='bnt cancel' hover-class='none'
					v-if="cartInfo.delivery_id && cartInfo.delivery_type === 'express'"
					:url="'/pages/points_mall/logistics_details?order_id='+ cartInfo.order_id">{{$t(`Xem vận chuyển`)}}
				</navigator>
				<view class='bnt bg-color' v-if="cartInfo.status==2" @tap='confirmOrder'>{{$t(`Xác nhận đã nhận hàng`)}}</view>
			</view>
		</view>
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		integralOrderDetails,
		orderTake,
		orderDel
	} from '@/api/activity.js'
	import {
		openOrderRefundSubscribe
	} from '@/utils/SubscribeMessage.js';
	import {
		getUserInfo
	} from '@/api/user.js';
	import home from '@/components/home';
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
				payMode: [{
						name: this.$t(`WeChat Pay`),
						icon: "icon-weixinzhifu",
						value: 'weixin',
						title: this.$t(`Thanh toán nhanh qua WeChat`),
						payStatus: true,
					},
					// #ifdef H5 || APP-PLUS
					{
						name: this.$t(`Thanh toán Alipay`),
						icon: 'icon-zhifubao',
						value: 'alipay',
						title: this.$t(`Thanh toán trực tuyến qua Alipay`),
						payStatus: true
					},
					// #endif
					{
						name: this.$t(`Thanh toán bằng số dư`),
						icon: "icon-yuezhifu",
						value: 'yue',
						title: this.$t(`Số dư khả dụng hiện tại:`),
						number: 0,
						payStatus: true
					},
				],
				pay_close: false,
				pay_order_id: '',
				totalPrice: '0',
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				routineContact: '0'
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
				// this.getUserInfo();
			} else {
				toLogin();
			}
		},
		onHide: function() {
			this.isClose = true;
		},
		onReady: function() {
			// #ifdef H5 || APP-PLUS
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
			jumpCon(id) {
				uni.navigateTo({
					url: `/pages/points_mall/integral_goods_details?id=${id}`
				})
			},
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
			 * Đóng component thanh toán
			 * 
			 */
			payClose: function() {
				this.pay_close = false;
			},
			/**
			 * Mở component thanh toán
			 * 
			 */
			pay_open: function() {
				this.pay_close = true;
				this.pay_order_id = this.orderInfo.order_id;
				this.totalPrice = this.orderInfo.pay_price;
			},
			/**
			 * Callback thanh toán thành công
			 * 
			 */
			pay_complete: function() {
				this.pay_close = false;
				this.pay_order_id = '';
				this.getOrderInfo();
			},
			/**
			 * Callback thanh toán thất bại
			 * 
			 */
			pay_fail: function() {
				this.pay_close = false;
				this.pay_order_id = '';
			},
			/**
			 * Callback ủy quyền đăng nhập
			 * 
			 */
			onLoadFun: function() {
				this.getOrderInfo();
				this.getUserInfo();
			},
			/**
			 * Lấy thông tin người dùng
			 * 
			 */
			getUserInfo: function() {
				let that = this;
				getUserInfo().then(res => {
					// #ifdef H5 || APP-PLUS
					that.payMode[2].number = res.data.now_money;
					// #endif
					// #ifdef MP
					that.payMode[1].number = res.data.now_money;
					// #endif
					that.$set(that, 'payMode', that.payMode);
				})
			},
			/**
			 * Lấy thông tin chi tiết đơn hàng
			 * 
			 */
			getOrderInfo: function() {
				let that = this;
				uni.showLoading({
					title: this.$t(`Đang tải`)
				});
				integralOrderDetails(this.order_id).then(res => {
					uni.hideLoading();
					that.$set(that, 'cartInfo', res.data);
				}).catch(err => {
					uni.hideLoading();
					that.$util.Tips({
						title: err
					}, '/pages/points_mall/exchange_record');
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
					data: this.cartInfo.order_id
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
				if (!seckill_id && !bargain_id && !combination_id && (type == 3 || type == 4)) status.class_status =
					5; //Mua lại
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
			confirmOrder: function() {
				let that = this;
				uni.showModal({
					title: this.$t(`Xác nhận đã nhận hàng`),
					content: this.$t(`Để bảo vệ quyền lợi của bạn, vui lòng chỉ xác nhận đã nhận hàng sau khi đã nhận và kiểm tra hàng không có vấn đề`),
					success: (res) => {
						if (res.confirm) {
							orderTake({
								order_id: that.order_id
							}).then(res => {
								return that.$util.Tips({
									title: that.$t(`Thao tác thành công`),
									icon: 'success'
								}, () => {
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
				orderDel({
					order_id: that.order_id
				}).then(res => {
					return that.$util.Tips({
						title: that.$t(`Xóa thành công`),
						icon: 'success'
					}, {
						tab: 5,
						url: '/pages/points_mall/exchange_record'
					});
				}).catch(err => {
					return that.$util.Tips({
						title: err
					});
				});
			},
		}
	}
</script>

<style scoped lang="scss">
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

	.order-details .header .pictrue {
		width: 110rpx;
		height: 110rpx;
	}

	.order-details .header .pictrue image {
		width: 100%;
		height: 100%;
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
		max-width: 460rpx;
		height: max-content;
		text-align: right;
		display: flex;
		flex-wrap: wrap;
		white-space: normal;
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

	.orderGoods {
		background-color: #fff;
		margin-top: 12rpx;
	}

	.orderGoods .total {
		width: 100%;
		height: 86rpx;
		padding: 0 30rpx;
		border-bottom: 2rpx solid #f0f0f0;
		font-size: 30rpx;
		color: #282828;
		line-height: 86rpx;
		box-sizing: border-box;
	}
</style>
