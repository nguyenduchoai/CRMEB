<template>
	<view :style="colorStyle">
		<view class="record" v-if="lotteryList.length">
			<view class="record-list" v-for="item in lotteryList" :key="item.id">
				<image class="goods-img" :src="item.prize.image" mode=""></image>
				<view class="right-data">
					<view class="title line1">
						{{ item.prize.name }}
					</view>
					<view class="goods-msg">
						{{ $t(`Loại giải thưởng`) }}：
						<text class="num">
							{{ item.prize.type | typeName }}
						</text>
					</view>
					<view class="goods-msg exchange" v-if="item.type == 6 && !item.receive_time" @click="fromAddress(item)">
						{{ $t(`Đổi ngay`) }}
					</view>
					<view class="goods-msg exchange" v-if="item.type == 4 && item.state == 'WAIT_USER_CONFIRM'"
						@click="goDetail(item)">
						{{ $t(`Nhận ngay`) }}
					</view>
					<view class="goods-msg fail-btn" v-if="item.type == 4 && item.state == 'FAIL'">
						{{ $t(`Đã hết hiệu lực`) }}
					</view>
					<view class="goods-msg" v-else>
						{{ $t(`Thời gian đổi`) }}：
						{{ item.receive_time || '--' }}
					</view>

					<view class="goods-msg" v-if="item.deliver_info.deliver_name">
						{{ $t(`Đơn vị vận chuyển`) }}：
						{{ item.deliver_info.deliver_name || '--' }}
					</view>
					<view class="goods-msg" v-if="item.deliver_info.deliver_number">
						{{ $t(`Mã vận đơn`) }}：
						{{ item.deliver_info.deliver_number || '--' }}
						<!-- #ifndef H5 -->
						<view v-if="item.deliver_info.deliver_number" class="copy"
							@tap="copyOrderId(item.deliver_info.deliver_number)">{{ $t(`Sao chép`) }}</view>
						<!-- #endif -->
						<!-- #ifdef H5 -->
						<view v-if="item.deliver_info.deliver_number" class="copy copy-data"
							:data-clipboard-text="item.deliver_info.deliver_number">{{ $t(`Sao chép`) }}</view>
						<!-- #endif -->
						<!-- <view v-if="item.deliver_info.deliver_number" class='copy' @tap='copyOrderId(item.deliver_info.deliver_number)'>Sao chép</view> -->
					</view>
				</view>
			</view>
			<view class="loadingicon acea-row row-center-wrapper" v-if="lotteryList.length > 0">
				<text class="loading iconfont icon-jiazai" :hidden="loading == false"></text>
				{{ loadTitle }}
			</view>
		</view>
		<block v-if="lotteryList.length === 0 && !loading">
			<emptyPage :title="$t(`Chưa có lịch sử trúng thưởng`)"></emptyPage>
		</block>
		<userAddress :aleartStatus="addressModel" @getAddress="getAddress" @close="
				() => {
					addressModel = false;
				}
			"></userAddress>
		<view v-if="addressModel" class="mask" @close="
				() => {
					addressModel = false;
				}
			"></view>
	</view>
</template>

<script>
	import ClipboardJS from '@/plugin/clipboard/clipboard.js';
	import {
		getLotteryList,
		receiveLottery
	} from '@/api/lottery.js';
	import userAddress from '../components/userAddress.vue';
	import emptyPage from '@/components/emptyPage.vue';
	import colors from '@/mixins/color.js';
	export default {
		components: {
			emptyPage,
			userAddress
		},
		mixins: [colors],
		data() {
			return {
				loading: false,
				loadend: false,
				addressModel: false,
				where: {
					page: 1,
					limit: 20
				},
				lotteryList: [],
				loadTitle: ''
			};
		},
		onShow() {
			this.where.page = 1
			this.loadend = false
			this.loading = false
			this.lotteryList = [];
			this.getLotteryList();
		},
		filters: {
			typeName(type) {
				if (type == 2) {
					return 'Điểm thưởng';
				} else if (type == 3) {
					return 'Số dư';
				} else if (type == 4) {
					return 'Lì xì';
				} else if (type == 5) {
					return 'Phiếu giảm giá';
				} else if (type == 6) {
					return 'Sản phẩm';
				}
			}
		},

		onReady() {
			// #ifdef H5 || APP-PLUS
			this.$nextTick(function() {
				const clipboard = new ClipboardJS('.copy-data');
				clipboard.on('success', () => {
					this.$util.Tips({
						title: this.$t(`Sao chép thành công`)
					});
				});
			});
			// #endif
		},
		methods: {
			getAddress(data) {
				let addData = data;
				addData.id = this.addid;
				addData.address = data.address.province + data.address.city + data.address.district + data.detail;
				receiveLottery(addData)
					.then((res) => {
						this.$util.Tips({
							title: this.$t(`Nhận thành công`)
						});
						this.addressModel = false;
						this.where.page = 1;
						this.loadend = false;
						this.lotteryList = [];
						this.getLotteryList();
					})
					.catch((err) => {
						this.$util.Tips({
							title: err
						});
					});
			},
			goDetail(item) {
				if (item.state == 'WAIT_USER_CONFIRM') {
					uni.navigateTo({
						url: '/pages/users/user_spread_money/receiving?type=2&id=' + item.wechat_order_id
					});
				}
			},
			fromAddress(item) {
				this.addid = item.id;
				this.addressModel = true;
			},
			// #ifndef H5
			copyOrderId: function(data) {
				let that = this;
				uni.setClipboardData({
					data: data
				});
			},
			// #endif
			getLotteryList() {
				let that = this;
				if (this.loadend) return;
				if (this.loading) return;
				this.loading = true;
				this.loadTitle = '';
				getLotteryList(this.where)
					.then((res) => {
						let list = res.data;
						let lotteryList = this.$util.SplitArray(list, this.lotteryList);
						let loadend = list.length < this.where.limit;
						this.loadend = loadend;
						this.loading = false;
						this.loadTitle = loadend ? that.$t(`Không còn nội dung nào nữa~`) : that.$t(`Tải thêm`);
						this.$set(this, 'lotteryList', lotteryList);
						this.$set(this.where, 'page', this.where.page + 1);
					})
					.catch((err) => {
						that.loading = false;
						that.loadTitle = that.$t(`Tải thêm`);
					});
			}
		},
		onReachBottom() {
			if (this.lotteryList.length > 0) {
				this.getLotteryList();
			} else {
				this.getLotteryList();
			}
		}
	};
</script>

<style lang="scss" scoped>
	.record {
		background-color: #eee;
	}

	.record-list {
		display: flex;
		align-items: center;
		background-color: #fff;
		padding: 30rpx;
		border-bottom: 1px solid #eeeeee;
		height: 100%;

		.goods-img {
			width: 170rpx;
			height: 170rpx;
			border-radius: 6rpx;
			margin-right: 15rpx;
		}

		.right-data {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			min-height: 170rpx;

			.title {
				font-size: 28rpx;
			}

			.goods-msg {
				font-size: 24rpx;
				color: #999;

				.num {
					color: var(--view-theme);
				}

				.copy {
					display: -webkit-inline-box;
					display: -webkit-inline-flex;
					width: 60rpx;
					margin-left: 10rpx;
					padding: 0rpx 4rpx;
					border: 2rpx solid;
				}
			}
			.fail-btn{
				color: #fff;
				background-color: #999;
				border-radius: 30rpx;
				text-align: center;
				padding: 4rpx 0;
			}
			.exchange {
				color: #fff;
				background-color: var(--view-theme);
				border-radius: 30rpx;
				text-align: center;
				padding: 4rpx 0;
			}
		}

		.mask {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0, 0, 0, 0.8);
			z-index: 9;
		}
	}
</style>