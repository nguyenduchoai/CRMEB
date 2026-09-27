<template>
	<view :style="colorStyle">
		<view class="commission-details">
			<view class="promoterHeader bg-color">
				<view class="headerCon acea-row row-between-wrapper">
					<view>
						<view class="name">{{ $t(name) }}</view>
						<view class="money" v-if="recordType == 4">
							{{ $t(`￥`) }}
							<text class="num">{{ extractCount }}</text>
						</view>
						<view class="money" v-else>
							{{ $t(`￥`) }}
							<text class="num">{{ recordCount }}</text>
						</view>
					</view>
					<view class="iconfont icon-jinbi1"></view>
				</view>
			</view>
			<view class="sign-record">
				<block v-if="recordList.length > 0">
					<view class="list" v-for="(item, index) in recordList" :key="index">
						<view class="item">
							<view class="data">{{ item.time }}</view>
							<view class="listn">
								<block v-for="(child, indexn) in item.child" :key="indexn">
									<view class="itemn acea-row row-between-wrapper">
										<view class="title">
											<view class="name acea-row line1">
												<view
													v-if="child.pm != 1 && child.type == 'extract'"
													class="extract-tag"
													:class="{ 'bg-success': child.extract_status == 1, 'bg-ing': child.extract_status == 0, 'bg-fail': child.extract_status == -1 }"
												>
													{{ child.extract_status == 1 ? 'Thành công' : child.extract_status == 0 ? 'Đang rút tiền' : 'Thất bại' }}
												</view>
												<view v-if="type == 2 && child.is_frozen == 1" class="extract-tag bg-ing">Đang đóng băng</view>
												{{ type == 1 ? $t(child.mark) : $t(child.title) }}
											</view>
											<view>{{ child.add_time }}</view>
											<view class="fail-msg" v-if="child.fail_msg">{{ $t(`Lý do`) }}：{{ child.fail_msg }}</view>
											<view class="fail-msg" v-if="type == 2 && child.is_frozen == 1">Hoa hồng đang bị đóng băng, thời gian hết đóng băng: {{ child.frozen_time }}</view>
										</view>
										<view class="acea-row row-column items-end">
											<view class="num font-color" v-if="child.pm == 1">+{{ child.number }}</view>
											<view class="num flex-y-center" v-else>-{{ child.number }}</view>
											<template v-if="child.state == 'WAIT_USER_CONFIRM'">
												<view
													class="w-100 h-48 rd-30rpx flex-center mt-16 bg-color fs-26 text--w111-fff"
													@tap="jumpPath('/pages/users/user_spread_money/receiving?type=1&id=' + child.wechat_order_id)"
												>
													Thu tiền
												</view>
											</template>
										</view>
									</view>
								</block>
							</view>
						</view>
					</view>
				</block>
				<view class="loadingicon acea-row row-center-wrapper" v-if="recordList.length">
					<text class="loading iconfont icon-jiazai" :hidden="loading == false"></text>
					{{ loadTitle }}
				</view>
				<view v-if="recordList.length < 1 && page > 1">
					<emptyPage :title="$t(`Chưa có dữ liệu~`)"></emptyPage>
				</view>
			</view>
		</view>
		<!-- #ifdef H5 -->
		<home></home>
		<!-- #endif -->
	</view>
</template>

<script>
import { getCommissionInfo, getSpreadInfo } from '@/api/user.js';
import { toLogin } from '@/libs/login.js';
import { mapGetters } from 'vuex';
// #ifdef MP
import authorize from '@/components/Authorize';
// #endif
import emptyPage from '@/components/emptyPage.vue';
import home from '@/components/home';
import colors from '@/mixins/color.js';
export default {
	components: {
		// #ifdef MP
		authorize,
		// #endif
		emptyPage,
		home
	},
	mixins: [colors],
	data() {
		return {
			name: '',
			type: 0,
			page: 1,
			limit: 15,
			loading: false,
			loadend: false,
			loadTitle: this.$t(`Tải thêm`),
			recordList: [],
			recordType: 0,
			recordCount: 0,
			extractCount: 0,
			times: [],
			isWeixin: false
		};
	},
	computed: {
		...mapGetters(['isLogin'])
		// showWithdrawal() {
		// 	//#ifdef H5
		// 	// Chỉ biên dịch cho nền tảng H5
		// 	return this.isWeixin;
		// 	// #endif

		// 	//#ifndef H5
		// 	// Không phải nền tảng H5 thì trả về false ngay
		// 	return true;
		// 	// #endif
		// }
	},
	onLoad(options) {
		if (this.isLogin) {
			this.type = options.type;
		} else {
			toLogin();
		}
	},
	onShow() {
		//#ifdef H5
		this.isWeixin = this.$wechat.isWeixin();
		//#endif
		let type = this.type;
		this.loadend = false;
		this.page = 1;
		this.times = [];
		this.recordList = [];
		if (type == 1) {
			uni.setNavigationBarTitle({
				title: 'Lịch sử rút tiền'
			});
			this.name = 'Tổng tiền đã rút';
			this.recordType = 4;
			this.getRecordList();
			this.getRecordListCount();
		} else if (type == 2) {
			uni.setNavigationBarTitle({
				title: 'Lịch sử hoa hồng'
			});
			this.name = 'Chi tiết hoa hồng';
			this.recordType = 3;
			this.getRecordList();
			this.getRecordListCount();
		} else {
			uni.showToast({
				title: 'Tham số không hợp lệ',
				icon: 'none',
				duration: 1000,
				mask: true,
				success: function (res) {
					setTimeout(function () {
						// #ifndef H5
						uni.navigateBack({
							delta: 1
						});
						// #endif
						// #ifdef H5
						history.back();
						// #endif
					}, 1200);
				}
			});
		}
	},
	methods: {
		jumpPath(url) {
			uni.navigateTo({
				url
			});
		},
		getRecordList: function () {
			let that = this;
			let page = that.page;
			let limit = that.limit;
			let recordType = that.recordType;
			if (that.loading) return;
			if (that.loadend) return;
			that.loading = true;
			that.loadTitle = '';
			getCommissionInfo(
				{
					page: page,
					limit: limit
				},
				recordType
			)
				.then((res) => {
					for (let i = 0; i < res.data.time.length; i++) {
						if (!this.times.includes(res.data.time[i])) {
							this.times.push(res.data.time[i]);
							this.recordList.push({
								time: res.data.time[i],
								child: []
							});
						}
					}
					for (let x = 0; x < this.times.length; x++) {
						for (let j = 0; j < res.data.list.length; j++) {
							if (this.times[x] === res.data.list[j].time_key) {
								this.recordList[x].child.push(res.data.list[j]);
							}
						}
					}
					let loadend = res.data.list.length < that.limit;
					that.loadend = loadend;
					that.loadTitle = loadend ? that.$t(`Đã đến cuối danh sách`) : that.$t(`Tải thêm`);
					that.page += 1;
					that.loading = false;
				})
				.catch((err) => {
					that.loading = false;
					that.loadTitle = that.$t(`Tải thêm`);
				});
		},
		getRecordListCount: function () {
			let that = this;
			getSpreadInfo().then((res) => {
				that.recordCount = res.data.commissionCount;
				that.extractCount = res.data.extractCount;
			});
		}
	},
	onReachBottom: function () {
		this.getRecordList();
	}
};
</script>

<style scoped lang="scss">
.commission-details .promoterHeader .headerCon .money {
	font-size: 36rpx;
}

.commission-details .promoterHeader .headerCon .money .num {
	font-family: 'Guildford Pro';
}
.extract-tag {
	font-size: 24rpx;
	border-radius: 12rpx;
	padding: 4rpx 8rpx;
	background-color: #16ac57;
	color: #fff;
	margin-right: 8rpx;
	&.bg-ing {
		background-color: #ffd600;
		color: #000;
	}
	&.bg-fail {
		background-color: #ff4d4f;
		color: #fff;
	}
}
.sign-record .list .item .listn .itemn .name {
	width: 100%;
	// max-width: 100%;
	white-space: break-spaces;
}
.sign-record .list .item .listn .itemn .title {
	padding-right: 30rpx;
	flex: 1;
}
</style>
