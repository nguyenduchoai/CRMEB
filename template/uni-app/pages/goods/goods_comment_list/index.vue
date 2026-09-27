<template>
	<view :style="colorStyle">
		<view class='evaluate-list'>
			<view class='generalComment acea-row row-between-wrapper'>
				<view class='acea-row row-middle'>
					<view class='evaluate'>{{$t(`Điểm đánh giá`)}}</view>
					<view class='start' :class="'star'+replyData.reply_star"></view>
				</view>
				<view>{{$t(`Tỷ lệ đánh giá tốt`)}}<text class='font-num'>{{replyData.reply_chance}}%</text></view>
			</view>
			<view class='nav acea-row row-middle'>
				<view class='item' :class='type==0 ? "bg-color":""' @click='changeType(0)'>
					{{$t(`Tất cả`)}}({{replyData.sum_count}})
				</view>
				<view class='item' :class='type==1 ? "bg-color":""' @click='changeType(1)'>
					{{$t(`Đánh giá tốt`)}}({{replyData.good_count}})
				</view>
				<view class='item' :class='type==2 ? "bg-color":""' @click='changeType(2)'>
					{{$t(`Đánh giá trung bình`)}}({{replyData.in_count}})
				</view>
				<view class='item' :class='type==3 ? "bg-color":""' @click='changeType(3)'>
					{{$t(`Đánh giá kém`)}}({{replyData.poor_count}})
				</view>
			</view>
			<userEvaluation :reply="reply"></userEvaluation>
			<view class='loadingicon acea-row row-center-wrapper' v-if="reply.length>0">
				<text class='loading iconfont icon-jiazai' :hidden='loading==false'></text>{{loadTitle}}
			</view>
			<view class='noCommodity' v-if="reply.length==0">
				<view class='emptyBox'>
					<image :src="imgHost + '/statics/images/noMessage.png'"></image>
				</view>
				<view class="text">
					{{$t(`Chưa có đánh giá`)}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		getReplyList,
		getReplyConfig
	} from '@/api/store.js';
	import userEvaluation from '@/components/userEvaluation';
	import colors from '@/mixins/color.js';
	import {
		HTTP_REQUEST_URL
	} from '@/config/app';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	export default {
		components: {
			userEvaluation
		},
		mixins: [colors],
		computed: mapGetters(['isLogin']),
		data() {
			return {
				imgHost: HTTP_REQUEST_URL,
				replyData: {},
				product_id: 0,
				reply: [],
				type: 0,
				loading: false,
				loadend: false,
				loadTitle: this.$t(`Tải thêm`),
				page: 1,
				limit: 20
			};
		},
		/**
		 * Hàm lifecycle -- theo dõi khi trang load
		 */
		onLoad(options) {
			let that = this;
			if (!options.product_id) return that.$util.Tips({
				title: that.$t(`Thiếu tham số`)
			}, {
				tab: 3,
				url: 1
			});
			that.product_id = options.product_id;
		},
		onShow() {
			if (this.isLogin) {
				this.getProductReplyCount();
				this.getProductReplyList();
			} else {
				toLogin()
			}
		},
		methods: {
			/**
			 * Lấy dữ liệu thống kê đánh giá
			 * 
			 */
			getProductReplyCount: function() {
				let that = this;
				getReplyConfig(that.product_id).then(res => {
					that.$set(that, 'replyData', res.data);
				});
			},
			/**
			 * Lấy đánh giá theo trang
			 */
			getProductReplyList: function() {
				let that = this;
				if (that.loadend) return;
				if (that.loading) return;
				that.loading = true;
				that.loadTitle = '';
				getReplyList(that.product_id, {
					page: that.page,
					limit: that.limit,
					type: that.type,
				}).then(res => {
					let list = res.data,
						loadend = list.length < that.limit;
					that.reply = that.$util.SplitArray(list, that.reply);
					that.$set(that, 'reply', that.reply);
					that.loading = false;
					that.loadend = loadend;
					that.loadTitle = loadend ? that.$t(`Không còn nội dung nào nữa~`) : that.$t(`Tải thêm`);
					that.page = that.page + 1;
				}).catch(err => {
					that.loading = false,
						that.loadTitle = that.$t(`Tải thêm`)
				});
			},
			/*
			 * Chuyển đổi khi click
			 * */
			changeType: function(e) {
				let type = parseInt(e);
				if (type == this.type) return;
				this.type = type;
				this.page = 1;
				this.loadend = false;
				this.$set(this, 'reply', []);
				this.getProductReplyList();
			}
		},
		/**
		 * Hàm xử lý sự kiện kéo lên đến đáy trang
		 */
		onReachBottom: function() {
			this.getProductReplyList();
		},
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}

	.evaluate-list .generalComment {
		height: 94rpx;
		padding: 0 30rpx;
		margin-top: 1rpx;
		background-color: #fff;
		font-size: 28rpx;
		color: #808080;
	}

	.evaluate-list .generalComment .evaluate {
		margin-right: 7rpx;
	}

	.evaluate-list .nav {
		font-size: 24rpx;
		color: #282828;
		padding: 0 30rpx 32rpx 30rpx;
		background-color: #fff;
		border-bottom: 1rpx solid #f5f5f5;
	}

	.evaluate-list .nav .item {
		font-size: 24rpx;
		color: #282828;
		border-radius: 6rpx;
		height: 54rpx;
		padding: 0 20rpx;
		background-color: #f4f4f4;
		line-height: 54rpx;
		margin-right: 17rpx;
	}

	.evaluate-list .nav .item.bg-color {
		color: #fff;
	}

	.noCommodity {
		background-color: #fff;
		padding-bottom: 30rpx;
		padding-top: 100rpx;

		.text {
			padding-top: 40rpx;
			text-align: center;
			color: #aaa;
		}

		.emptyBox {
			text-align: center;
			padding-top: 20rpx;

			.tips {
				color: #aaa;
				font-size: 26rpx;
				text-align: center;
			}

			image {
				width: 414rpx;
				height: 304rpx;
			}
		}
	}
</style>
