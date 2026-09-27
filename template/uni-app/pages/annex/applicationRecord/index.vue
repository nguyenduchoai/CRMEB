<template>
	<view class="">
		<view class="application-record" v-if="listData.length">
			<view class="card-list" v-for="item in listData" :key="item.mer_intention_id">
				<view class="card-top">
					<view class="title">{{item.mer_name || ''}}</view>
					<view class="time">{{$t(`Thời gian gửi`)}}：{{item.create_time || ''}}</view>
					<view v-if="item.fail_msg" class="reason">{{$t(`Lý do`)}}：{{item.fail_msg || ''}}</view>
				</view>
				<view class="line"></view>
				<view class="card-bottom">
					<view class="card-status">
						<image class="status-icon" v-if="item.status === 0" src="../static/images/pending.png" mode=""></image>
						<image class="status-icon" v-else-if="item.status === 1" src="../static/images/passed.png" mode=""></image>
						<image class="status-icon" v-else-if="item.status === 2" src="../static/images/not-pass.png" mode=""></image>
						<text class="status-text">{{statusText(item.status)}}</text>
					</view>
					<view class="status-btn" @click="jump(item)">{{statusBtn(item.status)}}</view>
				</view>
			</view>
		</view>
		<view class='no-shop' v-if="!listData.length && !loading">
			<view class='pictrue' style="margin: 0 auto;">
				<image src='/static/images/no-shop.png'></image>
				<text>{{$t(`Chưa có lịch sử đăng ký, hãy đăng ký ngay nhé!`)}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	// import {
	// 	getApplicationRecordList
	// } from '@/api/store.js'
	export default {
		data() {
			return {
				loading: false,
				listData: [],
				pageData: {
					page: 1,
					limit: 10,
				}
			}
		},
		onLoad() {
			// this.getListData()
		},
		// Cuộn xuống cuối
		onReachBottom() {
			if (this.count == this.listData.length) {
				uni.showToast({
					title: this.$t(`Không còn dữ liệu nữa`),
					icon: 'none',
					duration: 1000
				});
			} else {
				this.pageData.page += 1
				this.getListData()
			}
		},
		methods: {
			getListData() {
				this.loading = true
				uni.showLoading({
					title: this.$t(`Đang tải`),
				});
				getApplicationRecordList(this.pageData).then(res => {
					this.count = res.data.count
					this.listData = this.listData.concat(res.data.list)
					uni.hideLoading();
					this.loading = false
				})
			},
			// Logic chuyển hướng
			jump(item) {
				if ([0, 2].includes(item.status)) {
					uni.navigateTo({
						url: `/pages/store/settled/index?mer_i_id=${item.mer_intention_id}`
					})
				} else if (item.status === 1) {
					uni.navigateTo({
						url: `/pages/store/merchantDetails/index?mer_i_id=${item.mer_intention_id}&mer_id=${item.mer_id}`
					})
				}
			},
			//Kiểm tra trạng thái
			statusText(number) {
				// Object sử dụng
				let statusData = {
					0: this.$t(`Chờ duyệt`),
					1: this.$t(`Đã duyệt`),
					2: this.$t(`Không được duyệt`),
				};
				return statusData[number]
			},
			// Chữ hiển thị trên button
			statusBtn(number) {
				// Object sử dụng
				let statusData = {
					0: this.$t(`Sửa`),
					1: this.$t(`Xem`),
					2: this.$t(`Gửi lại`),
				};
				return statusData[number]
			},
		}
	}
</script>

<style lang="scss" scoped>
	.main {}

	.application-record {
		display: flex;
		flex-direction: column;
		align-items: center;
		background-color: #F5F5F5;
		padding: 20rpx 30rpx;

		.card-list {
			width: 100%;
			background-color: #fff;
			padding: 20rpx 24rpx;
			margin: 10rpx 20rpx;
			border-radius: 12rpx;

			.card-top {
				height: 140rpx;

				.title {
					font-size: 28rpx;
					font-weight: bold;
					color: #333333;
				}

				.time {
					color: #999999;
					font-size: 24rpx;
					padding: 5rpx 0;
				}

				.reason {
					color: #E93323;
					font-weight: bold;
					font-size: 24rpx;
				}
			}

			.line {
				height: 2rpx;
				margin: 20rpx 0 20rpx 0;
				background-color: #EEEEEE;
			}

			.card-bottom {
				display: flex;
				justify-content: space-between;
				align-items: center;
				color: #333;

				.card-status {
					display: flex;
					align-items: center;

					.status-icon {
						width: 30rpx;
						height: 30rpx;
						margin: 10rpx;
					}

					.status-text {
						font-size: 28rpx;
						font-weight: 500;
					}
				}

				.status-btn {
					font-size: 26rpx;
					color: #555;
					border: 1px solid #999999;
					padding: 8rpx 32rpx;
					border-radius: 40rpx;
				}
			}
		}

	}

	.no-shop {
		width: 100%;
		background-color: #fff;
		height: 100vh;

		.pictrue {
			display: flex;
			flex-direction: column;
			align-items: center;
			color: $uni-nothing-text;

			image {
				width: 414rpx;
				height: 380rpx;
			}
		}
	}
</style>
