<template>
	<view :style="colorStyle">
		<view class='order-submission'>
			<view class="allAddress" :style="store_self_mention ? '':'padding-top:10rpx'">
				<view class='address acea-row row-between-wrapper' @tap='onAddress' v-if='shippingType == 0'>
					<view class='addressCon' v-if="addressInfo.real_name">
						<view class='name'>{{addressInfo.real_name}}
							<text class='phone'>{{addressInfo.phone}}</text>
						</view>
						<view class="line1">
							<text class='default font-num'
								v-if="addressInfo.is_default">{{$t(`[Mặc định]`)}}</text>{{addressInfo.province}}{{addressInfo.city}}{{addressInfo.district}}{{addressInfo.detail}}
						</view>
						<!-- <view class='setaddress'>Đặt địa chỉ nhận hàng</view> -->
					</view>
					<view class='addressCon' v-else>
						<view class='setaddress'>{{$t(`Thiết lập địa chỉ nhận hàng`)}}</view>
					</view>
					<view class='iconfont icon-jiantou'></view>
				</view>
				<view class='line'>
					<image src='/static/images/line.jpg'></image>
				</view>
			</view>
			<view class="orderGoods">
				<view class='total'>{{$t(`Tổng`)}}{{resData.num}}{{$t(`sản phẩm`)}}</view>
				<view class='goodWrapper'>
					<view class='item acea-row row-between-wrapper' @click="jumpCon(cartInfo.product_id)">
						<view class='pictrue'>
							<image :src='cartInfo.image'></image>
						</view>
						<view class='text'>
							<view class='acea-row row-between-wrapper'>
								<view class='name line1'>{{cartInfo.store_name}}</view>
								<view class='num'>x {{resData.num}}</view>
							</view>
							<view class='attr line1' v-if="cartInfo.suk">{{cartInfo.suk}}
							</view>
							<view class='money font-color'>
								{{cartInfo.price}} {{$t(`Điểm thưởng`)}}
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class='wrapper'>
				<view class='item acea-row row-between-wrapper'>
					<view>{{$t(`Điểm thưởng khả dụng`)}}</view>
					<view class='discount'>{{resData.integral}}
					</view>
				</view>
				<view class='item acea-row row-between-wrapper'>
					<view>{{$t(`Phí vận chuyển`)}}</view>
					<view class='discount'>{{$t(`Miễn phí vận chuyển`)}}
					</view>
				</view>
				<view class='item' v-if="textareaStatus">
					<view>{{$t(`Thông tin ghi chú`)}}</view>
					<view class="placeholder-textarea">
						<textarea ref='getFocus' v-if="coupon.coupon===false" :focus="textFocus" @input='bindHideKeyboard' value=""
							name="mark">
						</textarea>
						<view class="placeholder" @click="clickTextArea" v-show="!mark">
							{{$t(`Vui lòng thêm ghi chú (tối đa 150 ký tự)`)}}
						</view>
					</view>
				</view>
			</view>
			<view style='height:120rpx;'></view>
			<view class='footer acea-row row-between-wrapper'>
				<view>{{$t(`Tổng cộng`)}}：
					<text class='font-color'>{{resData.total_price || 0}}{{$t(`Điểm thưởng`)}}</text>
				</view>
				<view class='settlement' style='z-index:100' @tap="goPay">{{$t(`Đổi ngay`)}}</view>
			</view>
		</view>
		<view class="alipaysubmit" v-html="formContent"></view>
		<couponListWindow :coupon='coupon' @ChangCouponsClone="ChangCouponsClone" :openType='openType' :cartId='cartId'
			@ChangCoupons="ChangCoupons"></couponListWindow>
		<addressWindow ref="addressWindow" @changeTextareaStatus="changeTextareaStatus" :news='news' :address='address'
			:pagesUrl="pagesUrl" @OnChangeAddress="OnChangeAddress" @changeClose="changeClose"></addressWindow>
		<!-- #ifdef MP -->
		<!-- <authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
		<!-- #endif -->
		<home v-show="!invShow"></home>
	</view>
</template>
<script>
	import {
		integralOrderConfirm,
		integralOrderCreate,
	} from '@/api/activity.js';
	import {
		getAddressDefault,
		getAddressDetail,
		invoiceList,
		invoiceOrder
	} from '@/api/user.js';
	import {
		storeListApi
	} from '@/api/store.js';
	import {
		CACHE_LONGITUDE,
		CACHE_LATITUDE
	} from '@/config/cache.js';
	import couponListWindow from '@/components/couponListWindow';
	import addressWindow from '@/components/addressWindow';
	import orderGoods from '@/components/orderGoods';
	import home from '@/components/home';
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
			couponListWindow,
			addressWindow,
			orderGoods,
			home,
			// #ifdef MP
			authorize
			// #endif
		},
		mixins: [colors],
		data() {
			return {
				textFocus:false,
				textareaStatus: true,
				//Phương thức thanh toán
				cartArr: [{
						"name": this.$t(`WeChat Pay`),
						"icon": "icon-weixin2",
						value: 'weixin',
						title: this.$t(`Thanh toán nhanh qua WeChat`),
						payStatus: 1,
					},
					{
						"name": this.$t(`Thanh toán Alipay`),
						"icon": "icon-zhifubao",
						value: 'alipay',
						title: this.$t(`Thanh toán trực tuyến qua Alipay`),
						payStatus: 1,
					},
					{
						"name": this.$t(`Thanh toán bằng số dư`),
						"icon": "icon-yuezhifu",
						value: 'yue',
						title: this.$t(`Số dư khả dụng:`),
						payStatus: 1,
					},
					{
						"name": this.$t(`Thanh toán ngoại tuyến`),
						"icon": "icon-yuezhifu1",
						value: 'offline',
						title: this.$t(`Chọn phương thức thanh toán ngoại tuyến`),
						payStatus: 2,
					}
				],
				formContent: '',
				payType: 'weixin', //Phương thức thanh toán
				openType: 1, //Cách mở phiếu giảm giá, 1 = sử dụng
				active: 0, //Chuyển đổi phương thức thanh toán
				coupon: {
					coupon: false,
					list: [],
					statusTile: this.$t(`Dùng ngay`)
				}, //Component phiếu giảm giá
				address: {
					address: false
				}, //Component địa chỉ
				addressInfo: {}, //Thông tin địa chỉ
				pinkId: 0, //ID mua chung
				addressId: 0, //id địa chỉ
				couponId: 0, //ID phiếu giảm giá
				cartId: '', //ID giỏ hàng
				BargainId: 0,
				combinationId: 0,
				seckillId: 0,
				userInfo: {}, //Thông tin người dùng
				coupon_price: 0, //Số tiền giảm từ phiếu giảm giá
				useIntegral: false, //Có sử dụng điểm thưởng hay không
				integral_price: 0, //Số tiền khấu trừ bằng điểm thưởng
				integral: 0,
				ChangePrice: 0, //Số tiền sau khi dùng điểm thưởng để trừ
				formIds: [], //Thu thập formid
				status: 0,
				is_address: false,
				toPay: false, //Sửa lỗi khi vào thanh toán trang bị ẩn thì phải tải lại trang
				shippingType: 0,
				system_store: {},
				storePostage: 0,
				contacts: '',
				contactsTel: '',
				mydata: {},
				storeList: [],
				store_self_mention: 0,
				cartInfo: {},
				priceGroup: {},
				animated: false,
				totalPrice: 0,
				integralRatio: "0",
				pagesUrl: "",
				orderKey: "",
				// usableCoupon: {},
				offlinePostage: "",
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				from: '',
				news: 1,

				invTitle: this.$t(`Không xuất hóa đơn`),
				special_invoice: false,
				invoice_func: false,
				header_type: '',
				invShow: false,
				invList: [],
				invChecked: '',
				urlQuery: '',
				pay_close: false,
				resData: {},
				mark: ''
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad: function(options) {
			// #ifdef H5
			this.from = this.$wechat.isWeixin() ? 'weixin' : 'weixinh5'
			// #endif
			// #ifdef MP
			this.from = 'routine'
			// #endif
			if (!options.unique) return this.$util.Tips({
				title: this.$t(`Vui lòng chọn sản phẩm cần mua`)
			}, {
				tab: 3,
				url: 1
			});
			this.unique = options.unique
			this.num = options.num
			this.couponId = options.couponId || 0;
			this.pinkId = options.pinkId ? parseInt(options.pinkId) : 0;
			this.addressId = options.addressId || 0;
			this.cartId = options.cartId;
			this.is_address = options.is_address ? true : false;
			this.news = !options.new || options.new === '0' ? 0 : 1;
			this.invChecked = options.invoice_id || '';
			this.header_type = options.header_type || '1';
			this.couponTitle = options.couponTitle || this.$t(`Vui lòng chọn`)
			// #ifndef APP-PLUS
			this.textareaStatus = true;
			// #endif
			if (this.isLogin && this.toPay == false) {
				this.getaddressInfo();
				this.getConfirm();
				this.$nextTick(function() {
					this.$refs.addressWindow.getAddressList();
				})
			} else {
				toLogin();
			}
		},
		/**
		 * Hàm lifecycle -- theo dõi khi trang hiển thị
		 */
		onShow: function() {
			let _this = this
			uni.$on("handClick", res => {
				if (res) {
					_this.system_store = res.address
				}
				// Xóa listener
				uni.$off('handClick');
			})
		},
		methods: {
			getInvoiceList() {
				uni.showLoading({
					title: this.$t(`Đang tải...`)
				})
				invoiceList().then(res => {
					uni.hideLoading();
					this.invList = res.data.map(item => {
						item.id = item.id.toString();
						return item;
					});
					const result = this.invList.find(item => item.id == this.invChecked);
					if (result) {
						let name = '';
						name += result.header_type === 1 ? this.$t(`Cá nhân`) : this.$t(`Doanh nghiệp`);
						name += result.type === 1 ? this.$t(`Thường`) : this.$t(`Chuyên dụng`);
						name += this.$t(`Hóa đơn`);
						this.invTitle = name;
					}
				}).catch(err => {
					uni.showToast({
						title: err,
						icon: 'none'
					});
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
				action && this[action] && this[action](value);
			},
			payClose: function() {
				this.pay_close = false;
			},
			goPay() {
				let that = this
				if (!that.addressId) {
					return that.$util.Tips({
						title: that.$t(`Vui lòng chọn địa chỉ nhận hàng`)
					});
				}
				if (parseFloat(that.resData.integral) < parseFloat(that.cartInfo.price))
					return that.$util.Tips({
						title: that.$t(`Điểm thưởng khả dụng không đủ!`)
					});
				let data = {
					addressId: that.addressId,
					mark: that.mark,
					unique: this.cartInfo.unique,
					num: this.resData.num
				}
				integralOrderCreate(data).then(res => {
					uni.redirectTo({
						url: `/pages/points_mall/integral_order_status?order_id=${res.data.result.orderId}`
					})
				}).catch(err => {
					uni.hideLoading();
					return that.$util.Tips({
						title: err
					});
				});
			},
			// Đóng popup địa chỉ;
			changeClose: function() {
				this.$set(this.address, 'address', false);
			},
			computedPrice: function() {
				let shippingType = this.shippingType;
				postOrderComputed(this.orderKey, {
					addressId: this.addressId,
					useIntegral: this.useIntegral ? 1 : 0,
					couponId: this.couponId,
					shipping_type: parseInt(shippingType) + 1,
					payType: this.payType
				}).then(res => {
					let result = res.data.result;
					if (result) {
						this.totalPrice = result.pay_price;
						this.integral_price = result.deduction_price;
						this.coupon_price = result.coupon_price;
						this.integral = this.useIntegral ? result.SurplusIntegral : this.userInfo.integral;
						this.$set(this.priceGroup, 'storePostage', shippingType == 1 ? 0 : result.pay_postage);
						this.$set(this.priceGroup, 'storePostageDiscount', result.storePostageDiscount);
					}
				})
			},
			ChangCouponsClone: function() {
				this.$set(this.coupon, 'coupon', false);
			},
			changeTextareaStatus: function() {
				for (let i = 0, len = this.coupon.list.length; i < len; i++) {
					this.coupon.list[i].use_title = '';
					this.coupon.list[i].is_use = 0;
				}
				this.textareaStatus = true;
				this.status = 0;
				this.$set(this.coupon, 'list', this.coupon.list);
			},
			/**
			 * Sự kiện thay đổi sau khi chọn địa chỉ
			 * @param object e
			 */
			OnChangeAddress: function(e) {
				this.textareaStatus = true;
				this.addressId = e;
				this.address.address = false;
				this.getaddressInfo();
			},
			bindHideKeyboard: function(e) {
				this.mark = e.detail.value;
			},
			/**
			 * Lấy thông tin chi tiết đơn hàng hiện tại
			 * 
			 */
			getConfirm: function() {
				let that = this;
				// return;
				integralOrderConfirm({
					unique: this.unique,
					num: this.num
				}).then(res => {
					that.$set(that, 'resData', res.data);
					that.$set(that, 'cartInfo', res.data.productInfo);
				}).catch(err => {
					return this.$util.Tips({
						title: err
					});
				});
			},
			/*
			 * Trích xuất id săn giảm giá và mua chung
			 */
			getBargainId: function() {
				let that = this;
				let cartINfo = that.cartInfo;
				let BargainId = 0;
				let combinationId = 0;
				cartINfo.forEach(function(value, index, cartINfo) {
					BargainId = cartINfo[index].bargain_id,
						combinationId = cartINfo[index].combination_id
				})
				that.$set(that, 'BargainId', parseInt(BargainId));
				that.$set(that, 'combinationId', parseInt(combinationId));
				if (that.cartArr.length == 3 && (BargainId || combinationId || that.seckillId)) {
					that.cartArr[2].payStatus = 0;
					that.$set(that, 'cartArr', that.cartArr);
				}
			},
			/*
			 * Lấy địa chỉ nhận hàng mặc định hoặc lấy thông tin một địa chỉ nào đó
			 */
			getaddressInfo: function() {
				let that = this;
				if (that.addressId) {
					getAddressDetail(that.addressId).then(res => {
						res.data.is_default = parseInt(res.data.is_default);
						that.addressInfo = res.data || {};
						that.addressId = res.data.id || 0;
						that.address.addressId = res.data.id || 0;
					})
				} else {
					getAddressDefault().then(res => {
						res.data.is_default = parseInt(res.data.is_default);
						that.addressInfo = res.data || {};
						that.addressId = res.data.id || 0;
						that.address.addressId = res.data.id || 0;
					})
				}
			},
			couponTap: function() {
				this.coupon.coupon = true;
				this.coupon.list.forEach((item, index) => {
					if (item.id == this.couponId) {
						item.is_use = 1
					} else {
						item.is_use = 0
					}
				})
				this.$set(this.coupon, 'list', this.coupon.list);
			},
			car: function() {
				let that = this;
				that.animated = false;
			},
			onAddress: function() {
				let that = this;
				that.textareaStatus = false;
				that.address.address = true;
				that.pagesUrl = `/pages/points_mall/user_address?unique=${this.unique}&num=${this.num}`
			},
			clickTextArea() {
				this.textFocus = true
				// this.$nextTick(() => {
				// 	this.$refs.getFocus.focus()
				// })
			},
		}
	}
</script>

<style lang="scss" scoped>
	/deep/uni-checkbox[disabled] .uni-checkbox-input {
		background-color: #eee;
	}

	.alipaysubmit {
		display: none;
	}

	.order-submission .line {
		width: 100%;
		height: 3rpx;
	}

	.order-submission .line image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-submission .address {
		padding: 28rpx 30rpx;
		background-color: #fff;
		box-sizing: border-box;
	}

	.order-submission .address .addressCon {
		width: 610rpx;
		font-size: 26rpx;
		color: #666;
	}

	.order-submission .address .addressCon .name {
		font-size: 30rpx;
		color: #282828;
		font-weight: bold;
		margin-bottom: 10rpx;
	}

	.order-submission .address .addressCon .name .phone {
		margin-left: 50rpx;
	}

	.order-submission .address .addressCon .default {
		margin-right: 12rpx;
	}

	.order-submission .address .addressCon .setaddress {
		color: #333;
		font-size: 28rpx;
	}

	.order-submission .address .iconfont {
		font-size: 35rpx;
		color: #707070;
	}

	.order-submission .allAddress {
		width: 100%;
		background: linear-gradient(to bottom, var(--view-theme) 0%, #f5f5f5 100%);
		padding-top: 100rpx;
	}

	.order-submission .allAddress .nav {
		width: 710rpx;
		margin: 0 auto;
	}

	.order-submission .allAddress .nav .item {
		width: 355rpx;
	}

	.order-submission .allAddress .nav .item.on {
		position: relative;
		width: 250rpx;
	}

	.order-submission .allAddress .nav .item.on::before {
		position: absolute;
		bottom: 0;
		content: "Giao qua đơn vị vận chuyển";
		font-size: 28rpx;
		display: block;
		height: 0;
		width: 336rpx;
		border-width: 0 20rpx 80rpx 0;
		border-style: none solid solid;
		border-color: transparent transparent #fff;
		z-index: 2;
		border-radius: 7rpx 30rpx 0 0;
		text-align: center;
		line-height: 80rpx;
	}

	.order-submission .allAddress .nav .item:nth-of-type(2).on::before {
		content: "Nhận tại cửa hàng";
		border-width: 0 0 80rpx 20rpx;
		border-radius: 30rpx 7rpx 0 0;
	}

	.order-submission .allAddress .nav .item.on2 {
		position: relative;
	}

	.order-submission .allAddress .nav .item.on2::before {
		position: absolute;
		bottom: 0;
		content: "Nhận tại cửa hàng";
		font-size: 28rpx;
		display: block;
		height: 0;
		width: 400rpx;
		border-width: 0 0 60rpx 60rpx;
		border-style: none solid solid;
		border-color: transparent transparent #f7c1bd;
		border-radius: 40rpx 6rpx 0 0;
		text-align: center;
		line-height: 60rpx;
	}

	.order-submission .allAddress .nav .item:nth-of-type(1).on2::before {
		content: "Giao qua đơn vị vận chuyển";
		border-width: 0 60rpx 60rpx 0;
		border-radius: 6rpx 40rpx 0 0;
	}

	.order-submission .allAddress .address {
		width: 710rpx;
		height: 150rpx;
		margin: 0 auto;
	}

	.order-submission .allAddress .line {
		width: 710rpx;
		margin: 0 auto;
	}

	.order-submission .wrapper .item .discount .placeholder {
		color: #ccc;
	}

	.placeholder-textarea {
		position: relative;

		.placeholder {
			position: absolute;
			color: #ccc;
			top: 26rpx;
			left: 30rpx;
		}
	}

	.order-submission .wrapper {
		background-color: #fff;
		margin-top: 13rpx;
	}

	.order-submission .wrapper .item {
		padding: 27rpx 30rpx;
		font-size: 30rpx;
		color: #282828;
		border-bottom: 1px solid #f0f0f0;
	}

	.order-submission .wrapper .item .discount {
		font-size: 30rpx;
		color: #999;
	}

	.order-submission .wrapper .item .discount input {
		text-align: end;
	}

	.order-submission .wrapper .item .discount .iconfont {
		color: #515151;
		font-size: 30rpx;
		margin-left: 15rpx;
	}

	.order-submission .wrapper .item .discount .num {
		font-size: 32rpx;
		margin-right: 20rpx;
	}

	.order-submission .wrapper .item .shipping {
		font-size: 30rpx;
		color: #999;
		position: relative;
		padding-right: 58rpx;
	}

	.order-submission .wrapper .item .shipping .iconfont {
		font-size: 35rpx;
		color: #707070;
		position: absolute;
		right: 0;
		top: 50%;
		transform: translateY(-50%);
		margin-left: 30rpx;
	}

	.order-submission .wrapper .item textarea {
		background-color: #f9f9f9;
		width: 690rpx;
		height: 140rpx;
		border-radius: 3rpx;
		margin-top: 30rpx;
		padding: 25rpx 28rpx;
		box-sizing: border-box;
	}

	.order-submission .wrapper .item .placeholder {
		color: #ccc;
	}

	.order-submission .wrapper .item .list {
		margin-top: 35rpx;
	}


	.order-submission .moneyList {
		margin-top: 12rpx;
		background-color: #fff;
		padding: 30rpx;
	}

	.order-submission .moneyList .item {
		font-size: 28rpx;
		color: #282828;
	}

	.order-submission .moneyList .item~.item {
		margin-top: 20rpx;
	}

	.order-submission .moneyList .item .money {
		color: #868686;
	}

	.order-submission .footer {
		width: 100%;
		height: 100rpx;
		background-color: #fff;
		padding: 0 30rpx;
		font-size: 28rpx;
		color: #333;
		box-sizing: border-box;
		position: fixed;
		bottom: 0;
		left: 0;
	}

	.order-submission .footer .settlement {
		font-size: 30rpx;
		color: #fff;
		width: 240rpx;
		height: 70rpx;
		background-color: var(--view-theme);
		border-radius: 50rpx;
		text-align: center;
		line-height: 70rpx;
	}

	.footer .transparent {
		opacity: 0
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

	.pictrue image {
		width: 130rpx;
		height: 130rpx;
	}
</style>
