<template>
	<view class="orderGoods">
		<view class='total' v-if="is_gift"><text>
				<text class="iconfont icon-ic_gift1 mr-8"></text>
				<text>{{ is_gift== 1 ? 'Tặng bạn bè' : 'Quà của tôi'}}</text>
			</text>
		</view>
		<view class='total' v-else-if="is_behalf"><text>
				{{$t(`Số tiền thanh toán hộ`)}}：
				<text class="pay-price">￥{{pay_price || 0}}</text>
			</text>
		</view>
		<view class='total' v-else-if="!split && !is_behalf">{{$t(`Tổng`)}}{{totalNmu}}{{$t(`sản phẩm`)}}</view>
		<view class='total' v-else-if="split">
			<text>{{$t(`Kiện hàng`)}} {{index + 1}}</text>
			<view class="rig-btn" v-if="status_type === -1">
				<view class="refund">{{$t(`Đang yêu cầu hoàn tiền`)}}</view>
			</view>
			<view class="rig-btn" v-else-if="status_type === -2">
				<view class="refund">{{$t(`Đã hoàn tiền`)}}</view>
			</view>
			<view class="rig-btn" v-else-if="status_type === 4">
				<view class="done">{{$t(`Đã hoàn thành`)}}</view>
			</view>
		</view>

		<view class='goodWrapper pt-24'>
			<view class='' :class="{op:!item.is_valid}" v-for="(item,index) in cartInfo" :key="index"
				@click="jumpCon(item)">
				<view class="item acea-row row-between-wrapper">
					<view class='pictrue' :class="{gray:!item.is_valid}">
						<image :src='item.productInfo.attrInfo.image' v-if="item.productInfo.attrInfo"></image>
						<image :src='item.productInfo.image' v-else></image>
					</view>
					<view class='text'>
						<view class='acea-row row-between-wrapper'>
							<view class='name line2'>{{item.productInfo.store_name}}</view>
							<view class='num'>x {{item.cart_num}}</view>
						</view>
						<view class='attr line1' v-if="item.productInfo.attrInfo">{{item.productInfo.attrInfo.suk}}
						</view>
						<view class='money font-color pic' v-if="item.productInfo.attrInfo">
							<text v-show="is_gift != 2" :class="{gray:!item.is_valid}">
								{{$t(`￥`)}}{{item.productInfo.attrInfo.price}}
							</text>
							<view class="refund" v-if="item.refund_num && statusType !=-2">{{item.refund_num}}{{$t(`sản phẩm đang hoàn tiền`)}}
							</view>
							<text class="valid" v-if="!item.is_valid && shipping_type === 0">{{$t(`Không hỗ trợ giao hàng`)}}</text>
							<text class="valid" v-if="!item.productInfo.store_mention && shipping_type === 1">{{$t(`Không hỗ trợ nhận tại cửa hàng`)}}</text>
						</view>
						<view class='money font-color pic' v-else>
							<text :class="{gray:!item.is_valid}">{{$t(`￥`)}}{{item.productInfo.price}}</text>
							<text class="valid" v-if="!item.is_valid && shipping_type === 0">{{$t(`Chỉ hỗ trợ nhận tại cửa hàng`)}}</text>
							<text class="valid" v-if="!item.productInfo.store_mention && shipping_type === 1">{{$t(`Chỉ hỗ trợ giao hàng`)}}</text>
						</view>
						<view class='evaluate' v-else-if="item.is_reply==1">{{$t(`Đã đánh giá`)}}</view>
					</view>
					
				</view>

				<view class="botton-btn">
					<view class='logistics' v-if="item.is_reply==0 && evaluate==3 && pid != -1 && isShow"
						@click.stop="evaluateTap(item.unique,orderId)">
						{{$t(`Đánh giá`)}}</view>
					<view class='logistics'
						v-if="paid === 1 && refund_status === 0 && item.refund_num !=item.cart_num && !is_confirm && is_refund_available && isShow && (virtualType == 0 || (virtualType > 0 && statusType == 1)) && (is_gift != 2) && gift_uid == 0"
						@click.stop="openSubcribe(item)">
						{{$t(`Yêu cầu hoàn tiền`)}}</view>
					<view class="rig-btn" v-if="status_type === 2 && index === cartInfo.length - 1 || !split">
						<view v-if="delivery_type === 'express'" class="logistics" @click.stop="logistics(orderId)">{{$t(`Xem vận chuyển`)}}
						</view>
						<view class="logistics sure" v-if="status_type === 2" @click.stop="confirmOrder(orderId)">{{$t(`Xác nhận đã nhận hàng`)}}
						</view>
					</view>
				</view>
			</view>

		</view>
	</view>
</template>

<script>
	import { mapGetters } from 'vuex'
	export default {
		computed: mapGetters(['uid']),
		props: {
			// Trạng thái đơn hàng
			statusType: {
				type: Number,
				default: 0,
			},
			virtualType: {
				type: Number,
				default: 0,
			},
			evaluate: {
				type: Number,
				default: 0,
			},
			oid: {
				type: Number,
				default: 0,
			},
			// 1 đã thanh toán, 0 chưa thanh toán
			paid: {
				type: Number,
				default: 0,
			},
			cartInfo: {
				type: Array,
				default: function() {
					return [];
				}
			},
			orderId: {
				type: String,
				default: '',
			},
			shipping_type: {
				type: Number,
				default: -1,
			},
			delivery_type: {
				type: String,
				default: '',
			},
			pay_price: {
				type: String,
				default: '',
			},
			jump: {
				type: Boolean,
				default: false,
			},
			is_confirm: {
				type: Boolean,
				default: false,
			},
			// is_behalf có phải là danh sách thanh toán hộ hay không
			is_behalf: {
				type: Boolean,
				default: false,
			},
			split: {
				type: Boolean,
				default: false,
			},
			jumpDetail: {
				type: Boolean,
				default: false,
			},
			index: {
				type: Number,
				default: 0,
			},
			pid: {
				type: Number,
				default: 0,
			},
			is_gift: {
				type: Number | String,
				default: 0,
			},
			gift_uid: {
				type: Number,
				default: 0,
			},
			refund_status: {
				type: Number,
				default: 0,
			},
			status_type: {
				type: Number,
				default: 0,
			},
			isShow: {
				type: Boolean,
				default: true,
			},
			is_refund_available: {
				type: Boolean,
				default: true,
			},
		},
		data() {
			return {
				totalNmu: 0,
				operationModel: false,
				status: "",
			};
		},
		watch: {
			cartInfo: function(nVal, oVal) {
				let num = 0
				nVal.forEach((item, index) => {
					num += item.cart_num
				})
				this.totalNmu = num
			}
		},
		mounted() {
			let num = 0
			this.$nextTick(() => {
				this.cartInfo.forEach((item, index) => {
					num += item.cart_num
				})
				this.$set(this, 'totalNmu', num)
			})

		},
		methods: {
			evaluateTap: function(unique, orderId) {
				uni.navigateTo({
					url: "/pages/goods/goods_comment_con/index?unique=" + unique + "&uni=" + orderId
				})
			},
			jumpCon(item) {
				if (this.jump) {
					let url = '';
					if (item.type == 0) {
						url = `/pages/goods_details/index?id=${item.product_id}`
					} else if (item.type == 1) {
						url = `/pages/activity/goods_seckill_details/index?id=${item.seckill_id}`
					} else if (item.type == 2) {
						url = `/pages/activity/goods_bargain_details/index?id=${item.bargain_id}&bargain=${this.uid}`
					} else if (item.type == 3) {
						url = `/pages/activity/goods_combination_details/index?id=${item.combination_id}`
					}
					uni.navigateTo({
						url
					})
				} else if (this.jumpDetail) {
					uni.navigateTo({
						url: `/pages/goods/order_details/index?order_id=${this.orderId}`
					})
				}
			},
			logistics(order_id) {
				uni.navigateTo({
					url: '/pages/goods/goods_logistics/index?orderId=' + order_id
				})
			},
			confirmOrder(orderId) {
				this.$emit('confirmOrder', orderId)
			},
			changeOperation() {
				this.operationModel = !this.operationModel
			},
			openSubcribe(item) {
				let cartList = [];
				cartList.push({
					cart_id: item.id,
					cart_num: item.surplus_refund_num
				})
				let obj = JSON.stringify(cartList);
				this.$emit('openSubcribe',
					`/pages/goods/goods_return/index?orderId=${this.orderId}&id=${this.oid}&cartIds=${obj}`)
			},
		}
	}
</script>

<style scoped lang="scss">
	.fontcolor {
		color: #e93323;
	}

	.orderGoods {
		background-color: #fff;
	}

	.orderGoods .total {
		display: flex;
		justify-content: space-between;
		align-items: center;
		width: 100%;
		// height: 86rpx;
		padding: 0 30rpx;
		border-bottom: 2rpx solid #f0f0f0;
		font-size: 30rpx;
		color: #333;
		line-height: 86rpx;
		box-sizing: border-box;
	}
	.botton-btn {
		display: flex;
		align-items: right;
		justify-content: flex-end;
		padding: 0rpx 20rpx 24rpx 20rpx;
	}

	.rig-btn {
		display: flex;
		align-items: center;

		.refund {
			font-size: 26rpx;
			color: #e93323;
		}

		.done {
			font-size: 26rpx;
			color: #F19D2F;
		}
	}

	.logistics {
		// height: 46rpx;
		line-height: 30rpx;
		color: #666666;
		font-size: 20rpx;
		border: 1px solid #CCCCCC;
		border-radius: 30rpx;
		padding: 8rpx 22rpx;
		margin-left: 10rpx;
	}

	.sure {
		color: #e93323;
		border: 1px solid #e93323;
	}

	.more-operation {
		display: flex;
		justify-content: center;
		align-items: center;
		padding: 10rpx 0;
		color: #bbb;
	}

	.b-top {
		margin-left: 30rpx;
		margin-right: 30rpx;
		border-top: 1px solid #f0f0f0
	}

	.fade-enter-active,
	.fade-leave-active {
		transition: all 0.1s;
	}

	.fade-enter,
	.fade-leave-to

	/* .fade-leave-active below version 2.1.8 */
		{
		opacity: 0;
		transform: translateY(-10px);
	}

	.op {
		opacity: 0.5;
		
	}
	
	.gray {
		filter: grayscale(100%);
		filter: gray;
	}

	.pic {
		display: flex;
		justify-content: space-between;
	}

	.valid {
		margin-left: 20rpx;
		font-size: 24rpx;
	}

	.pay-price {
		color: #E93323;
	}

	.refund {
		text-align: right;
		font-size: 26rpx;
		color: var(--view-theme);
	}
</style>
