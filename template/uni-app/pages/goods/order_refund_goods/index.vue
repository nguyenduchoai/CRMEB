<template>
	<view :style="colorStyle">
		<form @submit="subRefund">
			<view class='apply-return'>
				<view class='goodsStyle acea-row row-between' v-for="(item,index) in orderInfo.cart_info" :key="index">
					<view class='pictrue'>
						<image :src='item.productInfo.attrInfo?item.productInfo.attrInfo.image:item.productInfo.image'>
						</image>
					</view>
					<view class='text acea-row row-between'>
						<view class='name line2'>{{item.productInfo.store_name}}</view>
						<view class='money'>
							<view>{{$t(`￥`)}}{{item.truePrice }}</view>
							<view class='num'>x{{item.cart_num }}</view>
						</view>
					</view>
				</view>
				<view class='list'>
					<view class='item acea-row row-between-wrapper' v-if="expressList.length">
						<view>{{$t(`Đơn vị vận chuyển`)}}</view>
						<picker class='num' @change="bindPickerChange" :value="seIndex" :range="expressList"
							range-key="name">
							<view class="picker acea-row row-between-wrapper">
								<view class='reason'>{{expressList[seIndex].name}}</view>
								<text class='iconfont icon-jiantou'></text>
							</view>
						</picker>
					</view>
					<view class='item acea-row row-between-wrapper'>
						<view>{{$t(`Mã vận đơn`)}}</view>
						<input class="input" type="text" :placeholder="$t(`Điền mã vận đơn`)" placeholder-class='placeholder'
							v-model="refundInfo.refund_express" />
					</view>
					<view class='item acea-row row-between-wrapper'>
						<view>{{$t(`Số điện thoại liên hệ`)}}</view>
						<input class="input" type="number" :placeholder="$t(`Vui lòng nhập số điện thoại`)" placeholder-class='placeholder'
							v-model="refundInfo.refund_phone" />
					</view>
					<view class='item textarea acea-row row-between'>
						<view>{{$t(`Ghi chú`)}}</view>
						<textarea :placeholder='$t(`Nhập ghi chú, tối đa 100 ký tự`)' class='num'
							v-model="refundInfo.refund_explain"></textarea>
					</view>
					<view class='item acea-row row-between'>
						<view class='title acea-row row-between-wrapper'>
							<view>{{$t(`Tải lên ảnh`)}}</view>
							<view class='tip'>{{$t(`Tải lên tối đa 3 ảnh`)}}</view>
						</view>
						<view class='upload acea-row row-middle'>
							<view class='pictrue' v-for="(item,index) in refund_reason_wap_img" :key="index">
								<image :src='item'></image>
								<view class='iconfont icon-guanbi1 font-num' @tap='DelPic(index)'></view>
							</view>
							<view class='pictrue acea-row row-center-wrapper row-column' @tap='uploadpic'
								v-if="refund_reason_wap_img.length < 3">
								<text class='iconfont icon-icon25201'></text>
								<view>{{$t(`Tải lên ảnh`)}}</view>
							</view>
						</view>
					</view>
				</view>
				<button class='returnBnt bg-color' form-type="submit">{{$t(`Gửi`)}}</button>
			</view>
		</form>
	</view>
</template>
<script>
	import {
		refundOrderDetail,
		refundExpress
	} from '@/api/order.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	import colors from '@/mixins/color.js';
	export default {
		mixins: [colors],
		data() {
			return {
				expressList: [],
				orderInfo: {},
				seIndex: 0,
				refund_reason_wap_img: [],
				refundInfo: {
					refund_express: '',
					refund_phone: '',
					refund_explain: '',
					id: '',
					refund_express_name: '',
					refund_img: ''
				}
			};
		},
		computed: mapGetters(['isLogin']),
		watch: {
			isLogin: {
				handler: function(newV, oldV) {
					if (newV) {
						this.getOrderInfo();
					}
				},
				deep: true
			}
		},
		onLoad(options) {
			this.orderId = options.orderId;
			if (this.isLogin) {
				this.getOrderInfo();
			} else {
				toLogin();
			}
		},
		methods: {
			/**
			 * Yêu cầu trả hàng
			 */
			subRefund: function(e) {
				let that = this
				if (!that.refundInfo.refund_express) return this.$util.Tips({
					title: that.$t(`Điền mã vận đơn`)
				});
				if (!that.refundInfo.refund_phone) return this.$util.Tips({
					title: that.$t(`Vui lòng nhập số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(that.refundInfo.refund_phone)) return this.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				that.refundInfo.refund_express_name = that.expressList[that.seIndex].name;
				that.refundInfo.refund_img = that.refund_reason_wap_img.join(',');
				refundExpress(that.refundInfo).then(res => {
					return this.$util.Tips({
						title: res.msg,
						icon: 'success'
					}, {
						tab: 5,
						url: '/pages/users/user_return_list/index?isT=1'
					});
				}).catch(err => {
					return this.$util.Tips({
						title: err
					});
				})
			},
			/**
			 * Xóa ảnh
			 * 
			 */
			DelPic: function(e) {
				let index = e,
					that = this,
					pic = this.refund_reason_wap_img[index];
				that.refund_reason_wap_img.splice(index, 1);
				that.$set(that, 'refund_reason_wap_img', that.refund_reason_wap_img);
			},

			/**
			 * Tải lên file
			 * 
			 */
			uploadpic: function() {
				let that = this;
				this.$util.uploadImageOne('upload/image', function(res) {
					that.refund_reason_wap_img.push(res.data.url);
					that.$set(that, 'refund_reason_wap_img', that.refund_reason_wap_img);
				});
			},
			/**
			 * Lấy chi tiết đơn hàng
			 * 
			 */
			getOrderInfo: function() {
				let that = this;
				refundOrderDetail(that.orderId).then(res => {
					that.$set(that, 'orderInfo', res.data);
					that.expressList = res.data.express_list;
					that.refundInfo.id = res.data.id;
				});
			},
			bindPickerChange(e) {
				this.$set(this, 'seIndex', e.detail.value);
			}
		}
	}
</script>

<style scoped lang="scss">
	.apply-return .list {
		background-color: #fff;
		margin-top: 18rpx;
	}

	.apply-return .list .item {
		margin-left: 30rpx;
		padding-right: 30rpx;
		min-height: 90rpx;
		border-bottom: 1rpx solid #eee;
		font-size: 30rpx;
		color: #333;

		.input {
			text-align: right;
		}
	}

	.apply-return .list .item .num {
		color: #282828;
		width: 427rpx;
		text-align: right;
	}

	.apply-return .list .item .num .picker .reason {
		width: 385rpx;
	}

	.apply-return .list .item .num .picker .iconfont {
		color: #666;
		font-size: 30rpx;
		margin-top: 2rpx;
	}

	.apply-return .list .item.textarea {
		padding: 30rpx 30rpx 30rpx 0;
	}

	.apply-return .list .item textarea {
		height: 100rpx;
		font-size: 30rpx;
	}

	.apply-return .list .item .placeholder {
		color: #bbb;
		font-size: 30rpx;
		text-align: right;
	}

	.apply-return .list .item .title {
		height: 95rpx;
		width: 100%;
	}

	.apply-return .list .item .title .tip {
		font-size: 30rpx;
		color: #bbb;
	}

	.apply-return .list .item .upload {
		padding-bottom: 36rpx;
	}

	.apply-return .list .item .upload .pictrue {
		margin: 22rpx 23rpx 0 0;
		width: 156rpx;
		height: 156rpx;
		position: relative;
		font-size: 24rpx;
		color: #bbb;
	}

	.apply-return .list .item .upload .pictrue:nth-of-type(4n) {
		margin-right: 0;
	}

	.apply-return .list .item .upload .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 3rpx;
	}

	.apply-return .list .item .upload .pictrue .icon-guanbi1 {
		position: absolute;
		font-size: 45rpx;
		top: -10rpx;
		right: -10rpx;
	}

	.apply-return .list .item .upload .pictrue .icon-icon25201 {
		color: #bfbfbf;
		font-size: 50rpx;
	}

	.apply-return .list .item .upload .pictrue:nth-last-child(1) {
		border: 1rpx solid #ddd;
		box-sizing: border-box;
	}

	.apply-return .returnBnt {
		font-size: 32rpx;
		color: #fff;
		width: 690rpx;
		height: 86rpx;
		border-radius: 50rpx;
		text-align: center;
		line-height: 86rpx;
		margin: 43rpx auto;
	}

	.goodsStyle .text .name {
		align-self: flex-start;
	}

	.list /deep/ .uni-input-input {
		text-align: right;
	}
</style>
