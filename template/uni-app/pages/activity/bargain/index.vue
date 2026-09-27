<template>
	<view :style="colorStyle">
		<block v-if="bargain.length>0">
			<div class="bargain-record" ref="container">
				<div class="item" v-for="(item, index) in bargain" :key="index">
					<div class="picTxt acea-row row-between-wrapper">
						<div class="pictrue">
							<image :src="item.image" />
						</div>
						<div class="text acea-row row-column-around">
							<div class="line1" style="width: 100%;">{{ item.title }}</div>
							<count-down :justify-left="'justify-content:left'" :is-day="true" :tip-text="$t(`Đếm ngược`) "
								:day-text=" $t(`ngày`) " :hour-text=" $t(`giờ`) " :minute-text=" $t(`phút`) " :second-text=" $t(`giây`)"
								:datatime="item.datatime" v-if="item.status === 1"></count-down>
							<div class="successTxt font-num" v-else-if="item.status === 3">{{$t(`Săn giảm giá thành công`)}}</div>
							<div class="endTxt" v-else>{{$t(`Chương trình đã kết thúc`)}}</div>
							<div class="money font-num">
								{{$t(`Đã giảm còn`)}}<span class="symbol">{{$t(`￥`)}}</span><span class="num">{{ item.residue_price }}</span>
							</div>
						</div>
					</div>
					<div class="bottom acea-row row-between-wrapper">
						<div class="purple" v-if="item.status === 1">{{$t(`Đang diễn ra`)}}</div>
						<div class="success" v-if="item.status === 3">{{$t(`Săn giảm giá thành công`)}}</div>
						<div class="end" v-if="item.status === 2">{{$t(`Chương trình đã kết thúc`)}}</div>
						<div class="acea-row row-middle row-right">
							<div class="bnt cancel" v-if="item.status === 1"
								@click="getBargainUserCancel(item.bargain_id)">
								{{$t(`Hủy hoạt động`)}}
							</div>
							<div class="bnt bg-color-red" v-if="item.status === 1" @click="goDetail(item.bargain_id)">
								{{$t(`Tiếp tục săn giảm giá`)}}
							</div>
						</div>
						<div class="acea-row row-middle row-right success"  v-if="item.status === 3">
							{{item.success_time}}
						</div>
					</div>
				</div>
				<Loading :loaded="status" :loading="loadingList"></Loading>
			</div>
		</block>
		<block v-if="bargain.length == 0">
			<emptyPage :title="$t(`Chưa có lịch sử săn giảm giá`)"></emptyPage>
		</block>
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
	</view>
</template>
<script>
	import CountDown from "@/components/countDown";
	import emptyPage from '@/components/emptyPage.vue'
	import {
		getBargainUserList,
		getBargainUserCancel
	} from "@/api/activity";
	import {
		getUserInfo
	} from '@/api/user.js';
	import Loading from "@/components/Loading";
	import home from '@/components/home';
	import colors from "@/mixins/color";
	export default {
		name: "BargainRecord",
		components: {
			CountDown,
			Loading,
			emptyPage,
			home
		},
		props: {},
		mixins: [colors],
		data: function() {
			return {
				bargain: [],
				status: false, //Danh sách săn giảm giá đã lấy xong hay chưa, false là chưa xong, true là xong
				loadingList: false, //API hiện tại đã request xong hay chưa, false là xong, true là chưa xong
				page: 1, //Số trang
				limit: 20, //Số lượng
				userInfo: {}
			};
		},
		onLoad: function() {
			this.getBargainUserList();
			this.getUserInfo();
		},
		methods: {
			goDetail: function(id) {
				uni.navigateTo({
					url: `/pages/activity/goods_bargain_details/index?id=${id}&bargain=${this.userInfo.uid}`
				})
			},
			// Danh sách săn giảm giá
			goList: function() {
				uni.navigateTo({
					url: '/pages/activity/goods_bargain/index'
				})
			},
			getBargainUserList: function() {
				var that = this;
				if (that.loadingList) return;
				if (that.status) return;
				getBargainUserList({
						page: that.page,
						limit: that.limit
					})
					.then(res => {
						that.status = res.data.length < that.limit;
						that.bargain.push.apply(that.bargain, res.data);
						that.page++;
						that.loadingList = false;
					})
					.catch(res => {
						that.$util.Tips({
							title: res
						})
					});
			},
			/**
		 * Hủy chương trình săn giảm giá
		 * @param {number} bargainId - ID chương trình săn giảm giá
		 */
		getBargainUserCancel: function(bargainId) {
				var that = this;
				getBargainUserCancel({
						bargainId: bargainId
					})
					.then(res => {
						that.status = false;
						that.loadingList = false;
						that.page = 1;
						that.bargain = [];
						that.getBargainUserList();
						that.$util.Tips({
							title: res.msg
						})
					})
					.catch(res => {
						that.$util.Tips({
							title: res
						})
					});
			},
			/**
			 * Lấy thông tin người dùng cá nhân
			 */
			getUserInfo: function() {
				let that = this;
				getUserInfo().then(res => {
					that.userInfo = res.data;
				});
			},
		},
		onReachBottom() {
			this.getBargainUserList();
		}
	};
</script>

<style lang="scss">
	/*Lịch sử săn giảm giá*/
	.bargain-record .item .picTxt .text .time .styleAll {
		color: #fc4141;
		font-size: 24rpx;
	}

	.bargain-record .item .picTxt .text .time .red {
		color: #999;
		font-size: 24rpx;
	}

	.bargain-record .item {
		background-color: #fff;
		margin-bottom: 12upx;
	}

	.bargain-record .item .picTxt {
		height: 210upx;
		border-bottom: 1px solid #f0f0f0;
		padding: 0 30upx;
	}

	.bargain-record .item .picTxt .pictrue {
		width: 150upx;
		height: 150upx;
	}

	.bargain-record .item .picTxt .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 6upx;
	}

	.bargain-record .item .picTxt .text {
		width: 515upx;
		font-size: 30upx;
		color: #282828;
		height: 150upx;
	}

	.bargain-record .item .picTxt .text .time {
		font-size: 24upx;
		color: #868686;
		justify-content: left !important;
	}

	.bargain-record .item .picTxt .text .successTxt {
		font-size: 24rpx;
	}

	.bargain-record .item .picTxt .text .endTxt {
		font-size: 24rpx;
		color: #999;
	}

	.bargain-record .item .picTxt .text .money {
		font-size: 24upx;
	}

	.bargain-record .item .picTxt .text .money .num {
		font-size: 32upx;
		font-weight: bold;
	}

	.bargain-record .item .picTxt .text .money .symbol {
		font-weight: bold;
	}

	.bargain-record .item .bottom {
		height: 100upx;
		padding: 0 30upx;
		font-size: 27upx;
	}

	.bargain-record .item .bottom .purple {
		color: #f78513;
	}

	.bargain-record .item .bottom .end {
		color: #999;
	}

	.bargain-record .item .bottom .success {
		color: #e93323;
	}

	.bargain-record .item .bottom .bnt {
		font-size: 27upx;
		color: #fff;
		width: 176upx;
		height: 60upx;
		border-radius: 32upx;
		text-align: center;
		line-height: 60upx;
	}

	.bargain-record .item .bottom .bnt.cancel {
		color: #aaa;
		border: 1px solid #ddd;
	}

	.bargain-record .item .bottom .bnt~.bnt {
		margin-left: 18upx;
	}
</style>
