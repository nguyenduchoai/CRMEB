<template>
	<view :style="colorStyle">
		<view class="px-20">
			<template v-if="!showBack">
				<view class="w-full h-120 rd-24rpx bg--w111-fff px-32 mt-40 flex-between-center fs-28">
					<text>Phương thức nhận tiền</text>
					<view class="flex-y-center">
						<image src="../static/weixin.png" class="w-42 h-42"></image>
						<text class="pl-16">Nhận tiền về WeChat</text>
					</view>
				</view>
				<view class="w-full rd-24rpx bg--w111-fff px-32 mt-20 fs-28">
					<view class="fs-28 lh-40rpx pt-40">Số tiền nhận</view>
					<view class="mt-36 pb-40">
						<baseMoney :money="infoData.true_extract_price" symbolSize="48" integerSize="72" decimalSize="72" color="#333333" weight></baseMoney>
					</view>

					<!-- <view class="pt-20 pb-24 fs-26 text--w111-999">Phí rút tiền {{withdraw_fee}}%</view> -->
				</view>
			</template>
			<view class="w-full rd-24rpx bg--w111-fff mt-40 py-82 flex-col flex-between-center fs-28" v-if="showBack">
				<view class="flex-y-center">
					<image src="../static/receiving_success.png" class="w-60 h-60"></image>
					<text class="pl-16 fs-32 fw-500">Nhận tiền thành công</text>
				</view>
				<view class="mt-40 pb-32">
					<baseMoney :money="infoData.true_extract_price" symbolSize="48" integerSize="72" decimalSize="72" color="#333333" weight></baseMoney>
				</view>
				<view class="fs-26 text--w111-999">Có thể xem chi tiết tại “WeChat Pay - Dịch vụ - Ví - Giao dịch”</view>
				<view class="w-504 h-80 rd-40rpx flex-center bg-color fs-28 text--w111-fff mt-52" @tap="backList">Quay lại danh sách</view>
			</view>
			<view class="fixed-lb w-full pb-safe" v-if="!showBack">
				<view class="w-full h-128 px-20 flex-center">
					<view class="w-full h-80 rd-40rpx flex-center bg-color fs-28 text--w111-fff" @tap="confrimTap">Nhận tiền ngay</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
import colors from '@/mixins/color.js';
import { toLogin } from '@/libs/login.js';
import { transferInfoApi } from '@/api/user';
import { mapGetters } from 'vuex';
export default {
	mixins: [colors],
	data() {
		return {
			id: 0,
			type: 1,
			infoData: {
				true_extract_price: '',
				package_info: '',
				mchid: '',
				wechat_appid: ''
			},
			withdraw_fee: '',
			showBack: false
		};
	},
	computed: mapGetters(['isLogin']),
	onLoad(options) {
		if (options.id) {
			this.id = options.id;
			this.type = options.type;
			this.getInfo();
		}
	},
	methods: {
		getInfo() {
			transferInfoApi({
				order_id: this.id,
				type: this.type
			})
				.then((res) => {
					this.infoData = res.data;
				})
				.catch((err) => {
					return this.$util.Tips(
						{
							title: err
						},
						{
							tab: 3
						}
					);
				});
		},
		confrimTap() {
			if (this.infoData.state === 'FAIL')
				return wx.showToast({
					title: 'Giao dịch chuyển tiền đã hết hiệu lực, vui lòng thực hiện lại'
				});
			let that = this;
			// #ifdef MP-WEIXIN
			if (wx.canIUse('requestMerchantTransfer')) {
				wx.requestMerchantTransfer({
					mchId: that.infoData.mchid,
					appId: wx.getAccountInfoSync().miniProgram.appId,
					package: that.infoData.package_info,
					success: (res) => {
						that.showBack = true;
						// res.err_msg sẽ trả về ok khi hiển thị trang thành công rồi quay lại ứng dụng, không có nghĩa là thanh toán thành công
						console.log('success:', res);
					},
					fail: (res) => {
						console.log('fail:', res);
					}
				});
			} else {
				wx.showToast({
					title: 'Phiên bản WeChat của bạn quá cũ, vui lòng cập nhật lên phiên bản mới nhất.'
				});
			}
			// #endif
			// #ifdef H5
			if (that.$wechat.isWeixin()) {
				let configAppMessage = {
					mchId: that.infoData.mchid,
					appId: that.infoData.wechat_appid,
					package: that.infoData.package_info
				};
				if (WeixinJSBridge) {
					WeixinJSBridge.invoke('requestMerchantTransfer', configAppMessage, function (res) {
						if (res.err_msg === 'requestMerchantTransfer:ok') {
							// res.err_msg sẽ trả về success khi hiển thị trang thành công rồi quay lại ứng dụng, không có nghĩa là thanh toán thành công
							that.showBack = true;
						}
					});
				} else {
					uni.showToast({
						title: 'Phiên bản WeChat của bạn quá cũ, vui lòng cập nhật lên phiên bản mới nhất.'
					});
				}
			}
			// #endif
		},
		backList() {
			let backUrl;
			if (this.type == 1) {
				backUrl = '/pages/users/user_spread_money/index?type=1';
			} else {
				backUrl = '/pages/goods/lottery/grids/record';
			}
			uni.reLaunch({
				url: backUrl
			});
		}
	}
};
</script>
<style lang="scss">
.bb-e {
	border-bottom: 1px solid #eee;
}
.py-82 {
	padding: 82rpx 0;
}
</style>
