<template>
	<view>
		<view class='coupon-list-window' :class='coupon.coupon==true?"on":""'>
			<view v-if="coupon.count" class="nav acea-row row-around">
				<view v-if="coupon.count[2]" :class="['acea-row', 'row-middle', coupon.type === 2 ? 'on' : '']"
					@click="setType(2)">{{$t(`Phiếu theo sản phẩm`)}}</view>
				<view v-if="coupon.count[1]" :class="['acea-row', 'row-middle', coupon.type === 1 ? 'on' : '']"
					@click="setType(1)">{{$t(`Phiếu theo danh mục`)}}</view>
				<view v-if="coupon.count[0]" :class="['acea-row', 'row-middle', coupon.type === 0 ? 'on' : '']"
					@click="setType(0)">{{$t(`Phiếu toàn cửa hàng`)}}</view>
			</view>
			<view class='title' v-else>{{$t(`Phiếu giảm giá`)}}<text class='iconfont icon-guanbi' @click='close'></text></view>
			<view v-if="coupon.count" class="occupy"></view>
			<view class='coupon-list' v-if="coupon.list.length">
				<view class='item acea-row row-center-wrapper' v-for="(item,index) in coupon.list"
					@click="getCouponUser(index,item.id)" :key='index' :class="{svip: item.receive_type === 4}">
					<view class="moneyCon acea-row row-center-wrapper">
						<view class='money acea-row row-column row-center-wrapper'
							:class='item.is_use >= item.receive_limit && coupon.count?"moneyGray":""'>
							<view>{{$t(`￥`)}}<text class='num'>{{item.coupon_price}}</text></view>
							<view class="pic-num" v-if="item.use_min_price > 0">
								{{$t(`Đơn từ`)}}{{item.use_min_price}}{{$t(`đ có thể dùng`)}}</view>
							<view class="pic-num" v-else>{{$t(`Phiếu không điều kiện`)}}</view>
						</view>
					</view>
					<view class='text'>
						<view class='condition line2' :class="coupon.count?'':'order'">
							<span class='line-title' :class='item.is_use >= item.receive_limit && coupon.count?"gray":""'
								v-if='item.type===0'>{{$t(`Phiếu toàn cửa hàng`)}}</span>
							<span class='line-title' :class='item.is_use >= item.receive_limit && coupon.count?"gray":""'
								v-else-if='item.type===1'>{{$t(`Phiếu theo danh mục`)}}</span>
							<span class='line-title' :class='item.is_use >= item.receive_limit && coupon.count?"gray":""'
								v-else>{{$t(`Phiếu theo sản phẩm`)}}</span>
							<image src='../../static/images/fvip.png' class="pic" v-if="item.receive_type===4"></image>
							<span class='name'>{{$t(item.title)}}</span>
						</view>
						<view class='data acea-row row-between-wrapper'>
							<view v-if="item.coupon_time">{{$t(`Dùng trong vòng`)}}{{item.coupon_time}}{{$t(`ngày sau khi nhận`)}}</view>
							<view v-else>{{ item.start_use_time ? item.start_use_time + "-" : ""}}{{ item.end_use_time }}</view>
							<view v-if="coupon.count">
								<view class='bnt gray' v-if="item.is_use >= item.receive_limit">
									{{item.use_title || $t(`Đã nhận`)}}</view>
								<view class='bnt bg-color' v-else>{{coupon.statusTile || $t(`Nhận ngay`)}}</view>
							</view>
							<view v-else class="orderCou">
								<view class="iconfont icon-xuanzhong11"
									:class="item.receive_type === 4?'svip':'font-num'" v-if="item.is_use"></view>
								<view class="iconfont icon-weixuan" v-else></view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<!-- Không có phiếu giảm giá -->
			<view class='pictrue' v-else>
				<image :src="imgHost + '/statics/images/noCoupon.png'"></image>
			</view>
		</view>
		<view class='mask' catchtouchmove="true" :hidden='coupon.coupon==false' @click='close'></view>
	</view>
</template>

<script>
	import {
		setCouponReceive
	} from '@/api/api.js';
	import {
		HTTP_REQUEST_URL
	} from '@/config/app';
	export default {
		props: {
			//Trạng thái mở 0=nhận phiếu giảm giá, 1=dùng phiếu giảm giá
			openType: {
				type: Number,
				default: 0,
			},
			coupon: {
				type: Object,
				default: function() {
					return {};
				}
			}
		},
		data() {
			return {
				imgHost: HTTP_REQUEST_URL,
				type: 0
			};
		},
		methods: {
			close: function() {
				this.$emit('ChangCouponsClone');
				this.type = 0;
			},
			getCouponUser: function(index, id) {
				let that = this;
				let list = that.coupon.list;
				if (list[index].is_use >= list[index].receive_limit && this.openType == 0) return true;
				switch (this.openType) {
					case 0:
						//Nhận phiếu giảm giá
						setCouponReceive(id).then(res => {
							that.$emit('ChangCouponsUseState', index);
							that.$util.Tips({
								title: "Nhận thành công"
							});
							// that.$emit('ChangCoupons', list[index]);
						}).catch(err => {
							uni.showToast({
								title: err,
								icon: 'none'
							});
						})
						break;
					case 1:
						that.$emit('ChangCoupons', index);
						break;
				}
			},
			setType: function(type) {
				this.type = type;
				this.$emit('tabCouponType', type);
			}
		}
	}
</script>

<style scoped lang="scss">
	.orderCou {
		position: absolute;
		right: 20rpx;
		top: 50%;
		margin-top: -20rpx;
	}

	.orderCou .iconfont {
		font-size: 40rpx;
	}

	.orderCou .svip {
		color: #EDBB75;
	}

	.coupon-list .item .text {
		position: relative;
	}

	.coupon-list .item .text .condition.order {
		width: 350rpx;
	}

	.coupon-list-window .coupon-list .text .condition .pic {
		width: 30rpx;
		height: 30rpx;
		margin-right: 10rpx;
		vertical-align: middle;
	}

	.coupon-list-window .coupon-list .text .condition .name {
		vertical-align: middle;
		font-size: 26rpx;
		font-weight: 500;
	}

	.coupon-list-window {
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100%;
		background-color: #FFFFFF;
		border-radius: 16rpx 16rpx 0 0;
		z-index: 999;
		transform: translate3d(0, 100%, 0);
		transition: all .3s cubic-bezier(.25, .5, .5, .9);
	}

	.coupon-list-window.on {
		transform: translate3d(0, 0, 0);
	}

	.coupon-list-window .title {
		height: 124rpx;
		width: 100%;
		text-align: center;
		line-height: 124rpx;
		font-size: 32rpx;
		font-weight: bold;
		position: relative;
	}

	.coupon-list-window .title .iconfont {
		position: absolute;
		right: 30rpx;
		top: 50%;
		transform: translateY(-50%);
		font-size: 35rpx;
		color: #8a8a8a;
		font-weight: normal;
	}

	.coupon-list-window .coupon-list {
		margin: 0 0 50rpx 0;
		height: 721rpx;
		padding-top: 28rpx;
		overflow: auto;
	}

	.coupon-list-window .pictrue {
		width: 414rpx;
		height: 336rpx;
		margin: 192rpx auto 243rpx auto;
	}

	.coupon-list-window .pictrue image {
		width: 100%;
		height: 100%;
	}

	.pic-num {
		color: #fff;
		font-size: 24rpx;
	}

	.line-title {
		width: 70rpx;
		height: 32rpx !important;
		padding: 0 10rpx;
		line-height: 30rpx;
		text-align: center;
		background: var(--view-minorColorT);
		border: 1px solid var(--view-theme);
		opacity: 1;
		border-radius: 20rpx;
		font-size: 18rpx;
		color: var(--view-theme);
		margin-right: 12rpx;
		box-sizing: border-box;
	}

	.line-title.gray {
		border-color: #C1C1C1 !important;
		color: #C1C1C1 !important;
		background-color: #F7F7F7 !important;
	}

	.nav {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 106rpx;
		border-bottom: 2rpx solid #F5F5F5;
		border-top-left-radius: 16rpx;
		border-top-right-radius: 16rpx;
		background-color: #FFFFFF;
		font-size: 30rpx;
		color: #999999;
	}

	.nav .acea-row {
		border-top: 5rpx solid transparent;
		border-bottom: 5rpx solid transparent;
	}

	.nav .acea-row.on {
		border-bottom-color: var(--view-theme);
		color: #282828;
	}

	.nav .acea-row:only-child {
		border-bottom-color: transparent;
	}

	.occupy {
		height: 106rpx;
	}

	.coupon-list .item {
		margin-bottom: 18rpx;
		box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.06);
	}

	.coupon-list .item .money {
		font-weight: normal;
	}
</style>
