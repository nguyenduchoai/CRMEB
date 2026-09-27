<template>
	<view :style="colorStyle">
		<view class='cash-withdrawal'>
			<view class='nav acea-row'>
				<view v-for="(item,index) in navList" :key="index" class='item fontcolor' @click="swichNav(item.id)">
					<view class='line bg-color' :class='currentTab==item.id ? "on":""'></view>
					<view class='iconfont' :class='item.icon+" "+(currentTab==item.id ? "on":"")'></view>
					<view>{{item.name}}</view>
				</view>
			</view>
			<view class='wrapper'>
				<view :hidden='currentTab != 0' class='list'>
					<form @submit="subCash">
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Chủ thẻ`)}}</view>
							<view class='input'><input :placeholder='$t(`Vui lòng nhập họ tên chủ thẻ`)' placeholder-class='placeholder'
									name="name"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Số thẻ`)}}</view>
							<view class='input'><input type='number' :placeholder='$t(`Vui lòng nhập số thẻ`)' placeholder-class='placeholder'
									name="cardnum"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Ngân hàng`)}}</view>
							<view class='input'>
								<picker @change="bindPickerChange" :value="index" :range="array">
									<text class='Bank'>{{array[index]}}</text>
									<text class='iconfont icon-qiepian38'></text>
								</picker>
							</view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Rút tiền`)}}</view>
							<view class='input'><input @input='inputNum' :placeholder='$t(`Số tiền rút tối thiểu`)+minPrice'
									placeholder-class='placeholder' name="money" type='digit'></input></view>
						</view>
						<view class='tip'>
							{{$t(`Số tiền có thể rút hiện tại`)}}: <text class="price">{{$t(`￥`)}}{{userInfo.commissionCount}}</text>，{{$t(`Hoa hồng đóng băng`)}}:
							{{$t(`￥`)}}{{userInfo.broken_commission}}
						</view>
						<view class='tip'>
							{{$t(`Phí rút tiền: `)}}<text class="price">{{withdrawal_fee}}%</text>，{{$t(`Thực nhận: `)}}<text
								class="price">{{$t(`￥`)}}{{true_money}}</text>
						</view>
						<view class='tip'>
							{{$t(`Lưu ý: thời gian đóng băng của mỗi khoản hoa hồng là`)}}{{userInfo.broken_day}}{{$t(`ngày, hết thời hạn này mới có thể rút tiền`)}}
						</view>
						<button formType="submit" class='bnt bg-color'>{{$t(`Rút tiền`)}}</button>
					</form>
				</view>
				<view :hidden='currentTab != 1' class='list'>
					<form @submit="subCash">
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Họ tên`)}}</view>
							<view class='input'><input :placeholder='$t(`Vui lòng nhập họ tên thật của bạn`)' placeholder-class='placeholder'
									name="user_name"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper' v-if="!weixinExtractType">
							<view class='name'><text class='red'>*</text> {{$t(`Tài khoản`)}}</view>
							<view class='input'><input :placeholder='$t(`Vui lòng nhập tài khoản WeChat của bạn`)' placeholder-class='placeholder'
									name="name"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Rút tiền`)}}</view>
							<view class='input'><input @input='inputNum' :placeholder='$t(`Rút tối thiểu`)+minPrice+$t(`đ, tối đa 500đ`)'
									placeholder-class='placeholder' name="money" type='digit'></input></view>
						</view>
						<view class='item acea-row row-top row-between' v-if="!weixinExtractType">
							<view class='name pos'>{{$t(`Mã QR nhận tiền`)}}</view>
							<view class="input acea-row">
								<view class="picEwm" v-if="qrcodeUrlW">
									<image :src="qrcodeUrlW"></image>
									<text class='iconfont icon-guanbi1 fontcolor' @click='DelPicW'></text>
								</view>
								<view class='pictrue acea-row row-center-wrapper row-column' @click='uploadpic("W")' v-else>
									<text class='iconfont icon-icon25201'></text>
									<view>{{$t(`Tải lên ảnh`)}}</view>
								</view>
							</view>
						</view>
						<view class='tip'>
							{{$t(`Số tiền có thể rút hiện tại`)}}: <text class="price">{{$t(`￥`)}}{{userInfo.commissionCount}}</text>，{{$t(`Hoa hồng đóng băng`)}}:
							{{$t(`￥`)}}{{userInfo.broken_commission}}
						</view>
						<view class='tip'>
							{{$t(`Phí rút tiền: `)}}<text class="price">{{withdrawal_fee}}%</text>，{{$t(`Thực nhận: `)}}<text
								class="price">{{$t(`￥`)}}{{true_money}}</text>
						</view>
						<view class='tip'>
							{{$t(`Lưu ý: thời gian đóng băng của mỗi khoản hoa hồng là`)}}{{userInfo.broken_day}}{{$t(`ngày, hết thời hạn này mới có thể rút tiền`)}}
						</view>
						<button formType="submit" class='bnt bg-color'>{{$t(`Rút tiền`)}}</button>
					</form>
				</view>
				<view :hidden='currentTab != 2' class='list'>
					<form @submit="subCash">
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Tài khoản`)}}</view>
							<view class='input'><input :placeholder='$t(`Vui lòng nhập tài khoản Alipay của bạn`)' placeholder-class='placeholder'
									name="name"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Họ tên`)}}</view>
							<view class='input'><input :placeholder='$t(`Vui lòng nhập họ tên thật đã liên kết với Alipay`)' placeholder-class='placeholder'
									name="user_name"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Rút tiền`)}}</view>
							<view class='input'><input @input='inputNum' :placeholder='$t(`Số tiền rút tối thiểu`)+minPrice'
									placeholder-class='placeholder' name="money" type='digit'></input></view>
						</view>
						<view class='item acea-row row-top row-between'>
							<view class='name pos'>{{$t(`Mã QR nhận tiền`)}}</view>
							<view class="input acea-row">
								<view class="picEwm" v-if="qrcodeUrlZ">
									<image :src="qrcodeUrlZ"></image>
									<text class='iconfont icon-guanbi1 fontcolor' @click='DelPicZ'></text>
								</view>
								<view class='pictrue acea-row row-center-wrapper row-column' @click='uploadpic("Z")' v-else>
									<text class='iconfont icon-icon25201'></text>
									<view>{{$t(`Tải lên ảnh`)}}</view>
								</view>
							</view>
						</view>
						<view class='tip'>
							{{$t(`Số tiền có thể rút hiện tại`)}}: <text class="price">{{$t(`￥`)}}{{userInfo.commissionCount}}</text>，{{$t(`Hoa hồng đóng băng`)}}:
							{{$t(`￥`)}}{{userInfo.broken_commission}}
						</view>
						<view class='tip'>
							{{$t(`Phí rút tiền: `)}}<text class="price">{{withdrawal_fee}}%</text>，{{$t(`Thực nhận: `)}}<text
								class="price">{{$t(`￥`)}}{{true_money}}</text>
						</view>
						<view class='tip'>
							{{$t(`Lưu ý: thời gian đóng băng của mỗi khoản hoa hồng là`)}}{{userInfo.broken_day}}{{$t(`ngày, hết thời hạn này mới có thể rút tiền`)}}
						</view>
						<button formType="submit" class='bnt bg-color'>{{$t(`Rút tiền`)}}</button>
					</form>
				</view>
				<view :hidden='currentTab != 3' class='list'>
					<form @submit="importNowMoney">
						<view class='item acea-row row-between-wrapper'>
							<view class='name'><text class='red'>*</text> {{$t(`Rút tiền`)}}</view>
							<view class='input'><input @input='inputNum' placeholder='Vui lòng nhập số tiền rút' placeholder-class='placeholder'
									name="money" type='digit'></input></view>
						</view>
						<view class='tip'>
							{{$t(`Số tiền có thể rút hiện tại`)}}: <text class="price">{{$t(`￥`)}}{{userInfo.commissionCount}}</text>，{{$t(`Hoa hồng đóng băng`)}}:
							{{$t(`￥`)}}{{userInfo.broken_commission}}
						</view>
						<button formType="submit" class='bnt bg-color'>{{$t(`Rút tiền`)}}</button>
					</form>
				</view>
			</view>
		</view>
		<!-- #ifdef MP -->
		<!-- <authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		extractCash,
		extractBank,
		getUserInfo,
		recharge
	} from '@/api/user.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	import {
		openRevenueSubscribe
	} from '@/utils/SubscribeMessage.js';
	// #endif
	import colors from '@/mixins/color.js';
	export default {
		components: {
			// #ifdef MP
			authorize
			// #endif
		},
		mixins: [colors],
		data() {
			return {
				navList: [],
				currentTab: 0,
				index: 0,
				array: [], //Ngân hàng rút tiền
				minPrice: 0.00, //Số tiền rút tối thiểu
				userInfo: [],
				isClone: false,
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				qrcodeUrlW: "",
				qrcodeUrlZ: "",
				prevent: false, //Tránh gửi trùng lặp dẫn đến thành công nhiều lần
				weixinExtractType: 0, // Phương thức nhận hoa hồng
				alipayExtractType: 0, // Phương thức nhận hoa hồng
				withdrawal_fee: 0, //Phí rút tiền
				true_money: 0
			};
		},
		computed: mapGetters(['isLogin']),
		watch: {
			isLogin: {
				handler: function(newV, oldV) {
					if (newV) {
						this.getUserInfo();
						this.getUserExtractBank();
					}
				},
				deep: true
			}
		},
		onLoad() {
			if (this.isLogin) {
				this.getUserInfo();
				this.getUserExtractBank();
			} else {
				toLogin();
			}
		},
		methods: {
			inputNum: function(e) {
				let val = e.detail.value;
				let dot = val.indexOf('.');
				if (dot > -1) {
					this.moneyMaxLeng = dot + 3;
				} else {
					this.moneyMaxLeng = 8
				}
				this.true_money = Math.floor((this.$util.$h.Mul(val, this.$util.$h.Div(this.$util.$h.Sub(100, this
					.withdrawal_fee), 100))) * 100) / 100 || 0;
			},
			// uploadpicW(){
			// 	this.uploadpic(this.qrcodeUrlW);
			// },
			// uploadpicZ(){
			// 	this.uploadpic(this.qrcodeUrlZ);
			// },
			/**
			 * Tải lên file
			 * 
			 */
			uploadpic: function(type) {
				let that = this;
				that.$util.uploadImageOne('upload/image', function(res) {
					if (type === 'W') {
						that.qrcodeUrlW = res.data.url;
					} else {
						that.qrcodeUrlZ = res.data.url;
					}
				});
			},
			/**
			 * Xóa ảnh
			 * 
			 */
			DelPicW: function() {
				this.qrcodeUrlW = "";
			},
			DelPicZ: function() {
				this.qrcodeUrlZ = "";
			},
			onLoadFun: function() {
				this.getUserInfo();
				this.getUserExtractBank();
			},
			// Đóng ủy quyền
			authColse: function(e) {
				this.isShowAuth = e
			},
			getUserExtractBank: function() {
				let that = this;
				extractBank().then(res => {
					let array = res.data.extractBank;
					array.unshift('Vui lòng chọn ngân hàng');
					array.forEach((v, i) => {
						array.splice(i, 1, that.$t(v))
					})
					that.$set(that, 'array', array);
					that.minPrice = res.data.minPrice;
					that.withdrawal_fee = res.data.withdrawal_fee;
					that.alipayExtractType = res.data.alipayExtractType ? parseInt(res.data.alipayExtractType) : 0;
					that.weixinExtractType = res.data.weixinExtractType ? parseInt(res.data.weixinExtractType) : 0;
				});
			},
			/**
			 * Lấy thông tin người dùng cá nhân
			 */
			getUserInfo: function() {
				let that = this;
				getUserInfo().then(res => {
					that.navList = [{
							'name': that.$t(`Thẻ ngân hàng`),
							'icon': 'icon-yinhangqia',
							'id': 0
						},
						{
							'name': that.$t(`WeChat`),
							'icon': 'icon-weixin2',
							'id': 1
						},
						{
							'name': that.$t(`Alipay`),
							'icon': 'icon-icon34',
							'id': 2
						},
						{
							'name': that.$t(`Số dư`),
							'icon': 'icon-qiandai',
							'id': 3
						}
					]
					let list = [];
					that.userInfo = res.data;
					that.navList.forEach((item, index) => {
						if (that.userInfo.extract_type.includes(item.id.toString())) {
							list.push(item)
						}
					})
					this.navList = list
					this.swichNav(this.navList[0].id)
				});
			},
			swichNav: function(current) {
				this.currentTab = current;
			},
			bindPickerChange: function(e) {
				this.index = e.detail.value;
			},
			subCash(e) {
				let that = this,
					value = e.detail.value;
				if (this.prevent) return
				if (that.currentTab == 0) { //Thẻ ngân hàng
					if (!value.name.trim()) return this.$util.Tips({
						title: this.$t(`Vui lòng nhập họ tên chủ thẻ`)
					});
					if (!value.cardnum.trim()) return this.$util.Tips({
						title: this.$t(`Vui lòng nhập số thẻ`)
					});
					if (that.index == 0) return this.$util.Tips({
						title: this.$t(`Vui lòng chọn ngân hàng`)
					});
					value.extract_type = 'bank';
					value.bankname = that.array[that.index];
				} else if (that.currentTab == 1) { //WeChat
					value.extract_type = 'weixin';

					if (!value.user_name.trim()) return this.$util.Tips({
						title: this.$t(`Vui lòng điền họ tên`)
					});
					// Ẩn số tài khoản khi rút tiền tự động
					if (!that.weixinExtractType && !value.name.trim()) return this.$util.Tips({
						title: this.$t(`Vui lòng nhập ID WeChat`)
					});
					value.weixin = value.name;
					value.qrcode_url = that.qrcodeUrlW;
					
				} else if (that.currentTab == 2) { //Alipay
					value.extract_type = 'alipay';
					if (value.name.length == 0) return this.$util.Tips({
						title: this.$t(`Vui lòng nhập tài khoản Alipay`)
					});
					value.alipay_code = value.name;
					value.qrcode_url = that.qrcodeUrlZ;
				}
				if (!value.money.trim()) return this.$util.Tips({
					title: this.$t(`Vui lòng nhập số tiền rút`)
				});
				if (Number(value.money) < Number(that.minPrice)) return this.$util.Tips({
					title: this.$t(`Số tiền rút không được thấp hơn`) + that.minPrice
				});
				if (Number(value.money) > 500) return this.$util.Tips({
					title: this.$t(`Số tiền rút không được cao hơn 500`)
				});
				this.prevent = true
				extractCash(value).then(res => {
					that.getUserInfo();
					// #ifdef MP
					if(this.weixinExtractType == 1 && this.currentTab == 1){
							this.openSubscribe('/pages/users/user_spread_user/index')
						return
					}
					// #endif
					return this.$util.Tips({
						title: res.msg,
						icon: 'success'
					}, {
						url: '/pages/users/user_spread_user/index',
						tab: 2
					});
				}).catch(err => {
					return this.$util.Tips({
						title: err
					});
				}).finally(e =>{
					this.prevent = false
				})
			},
			// #ifdef MP
			openSubscribe(page) {
				// uni.showLoading({
				// 	title: this.$t(`Đang tải`),
				// })
				openRevenueSubscribe().then(res => {
					uni.hideLoading();
					return this.$util.Tips({
						title: res.msg,
						icon: 'success'
					}, {
						url: page,
						tab: 2
					});
				}).catch(() => {
					uni.hideLoading();
				});
			},
			// #endif
			importNowMoney(e) {
				let that = this
				let value = e.detail.value.money;
				if (parseFloat(value) < 0 || parseFloat(value) == NaN || value == undefined || value == "") {
					return that.$util.Tips({
						title: that.$t(`Vui lòng nhập số tiền`)
					});
				}
				uni.showModal({
					title: that.$t(`Rút về số dư`),
					content: that.$t(`Sau khi rút về số dư sẽ không thể chuyển ra lại, bạn có chắc chắn muốn rút về số dư không`),
					success(res) {
						if (res.confirm) {
							recharge({
									price: parseFloat(value),
									type: 1
								})
								.then(res => {
									return that.$util.Tips({
										title: that.$t(`Rút về số dư thành công`),
										icon: 'success'
									}, {
										tab: 5,
										url: '/pages/users/user_spread_user/index'
									});
								}).catch(err => {
									return that.$util.Tips({
										title: err
									})
								});
						} else if (res.cancel) {
							return that.$util.Tips({
								title: that.$t(`Đã hủy`)
							});
						}
					},
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff !important;
	}

	.fontcolor {
		color: var(--view-theme) !important;
	}

	.cash-withdrawal .nav {
		height: 130rpx;
		box-shadow: 0 10rpx 10rpx #f8f8f8;
	}

	.cash-withdrawal .nav .item {
		font-size: 26rpx;
		flex: 1;
		text-align: center;
	}

	.cash-withdrawal .nav .item~.item {
		border-left: 1px solid #f0f0f0;
	}

	.cash-withdrawal .nav .item .iconfont {
		width: 40rpx;
		height: 40rpx;
		border-radius: 50%;
		border: 2rpx solid var(--view-theme);
		text-align: center;
		line-height: 37rpx;
		margin: 0 auto 6rpx auto;
		font-size: 22rpx;
		box-sizing: border-box;
	}

	.cash-withdrawal .nav .item .iconfont.on {
		background-color: var(--view-theme);
		color: #fff;
		border-color: var(--view-theme);
	}

	.cash-withdrawal .nav .item .line {
		width: 2rpx;
		height: 20rpx;
		margin: 0 auto;
		transition: height 0.3s;
	}

	.cash-withdrawal .nav .item .line.on {
		height: 39rpx;
	}

	.cash-withdrawal .wrapper .list {
		padding: 0 30rpx;
	}

	.cash-withdrawal .wrapper .list .item {
		border-bottom: 1rpx solid #eee;
		min-height: 28rpx;
		font-size: 30rpx;
		color: #333;
		padding: 39rpx 0;
	}

	.cash-withdrawal .wrapper .list .item .name {
		width: 130rpx;
	}

	.cash-withdrawal .wrapper .list .item .input {
		width: 505rpx;
	}

	.cash-withdrawal .wrapper .list .item .input .placeholder {
		color: #bbb;
	}

	.cash-withdrawal .wrapper .list .item .picEwm,
	.cash-withdrawal .wrapper .list .item .pictrue {
		width: 140rpx;
		height: 140rpx;
		border-radius: 3rpx;
		position: relative;
		margin-right: 23rpx;
	}

	.cash-withdrawal .wrapper .list .item .picEwm image {
		width: 100%;
		height: 100%;
		border-radius: 3rpx;
	}

	.cash-withdrawal .wrapper .list .item .picEwm .icon-guanbi1 {
		position: absolute;
		right: -14rpx;
		top: -16rpx;
		font-size: 40rpx;
	}

	.cash-withdrawal .wrapper .list .item .pictrue {
		border: 1px solid rgba(221, 221, 221, 1);
		font-size: 22rpx;
		color: #BBBBBB;
	}

	.cash-withdrawal .wrapper .list .item .pictrue .icon-icon25201 {
		font-size: 47rpx;
		color: #DDDDDD;
		margin-bottom: 3px;
	}

	.cash-withdrawal .wrapper .list .tip {
		font-size: 26rpx;
		color: #999;
		margin-top: 25rpx;
	}

	.cash-withdrawal .wrapper .list .bnt {
		font-size: 32rpx;
		color: #fff;
		width: 690rpx;
		height: 90rpx;
		text-align: center;
		border-radius: 50rpx;
		line-height: 90rpx;
		margin: 64rpx auto;
	}

	.cash-withdrawal .wrapper .list .tip2 {
		font-size: 26rpx;
		color: #999;
		text-align: center;
		margin: 44rpx 0 20rpx 0;
	}

	.cash-withdrawal .wrapper .list .value {
		height: 135rpx;
		line-height: 135rpx;
		border-bottom: 1rpx solid #eee;
		width: 690rpx;
		margin: 0 auto;
	}

	.cash-withdrawal .wrapper .list .value input {
		font-size: 80rpx;
		color: #282828;
		height: 135rpx;
		text-align: center;
	}

	.cash-withdrawal .wrapper .list .value .placeholder2 {
		color: #bbb;
	}

	.price {
		color: var(--view-priceColor);
	}
	.pos{
		padding-left: 26rpx;
	}
	.red{
		padding-right: 10rpx;
		color: var(--view-theme) !important;
	}
</style>