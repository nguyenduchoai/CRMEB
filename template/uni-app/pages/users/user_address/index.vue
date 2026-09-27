<template>
	<view :style="colorStyle">
		<form @submit="formSubmit">
			<view class='addAddress'>
				<view class='list'>
					<view class='item acea-row row-between-wrapper'>
						<view class='name'>{{$t(`Họ tên`)}}</view>
						<input type='text' :placeholder='$t(`Vui lòng nhập họ tên`)' name='real_name' :value="userAddress.real_name"
							placeholder-class='placeholder'></input>
					</view>
					<view class='item acea-row row-between-wrapper'>
						<view class='name'>{{$t(`Số điện thoại liên hệ`)}}</view>
						<input type='number' :placeholder='$t(`Vui lòng nhập số điện thoại liên hệ`)' name="phone" :value='userAddress.phone'
							placeholder-class='placeholder' pattern="\d*"></input>
					</view>
					<view class='item acea-row row-between-wrapper'>
						<view class='name'>{{$t(`Khu vực`)}}</view>
						<view class="address">
							<picker mode="multiSelector" @change="bindRegionChange"
								@columnchange="bindMultiPickerColumnChange" :value="valueRegion" :range="multiArray">
								<view class='acea-row'>
									<view class="picker">{{region[0]}}，{{region[1]}}，{{region[2]}}</view>
								</view>
							</picker>
						</view>
					</view>
					<view class='item acea-row row-between-wrapper'>
						<view class='name'>{{$t(`Địa chỉ chi tiết`)}}</view>
						<input type='text' :placeholder='$t(`Vui lòng nhập địa chỉ cụ thể`)' name='detail' placeholder-class='placeholder'
							:value='userAddress.detail'></input>
					</view>
				</view>
				<view class='default acea-row row-middle' @click='ChangeIsDefault'>
					<checkbox-group>
						<checkbox :checked="userAddress.is_default ? true : false" />{{$t(`Đặt làm địa chỉ mặc định`)}}
					</checkbox-group>
				</view>

				<button class='keepBnt bg-color' form-type="submit">{{$t(`Lưu ngay`)}}</button>
				<!-- #ifdef MP -->
				<view class="wechatAddress" v-if="!id" @click="getWxAddress">{{$t(`Nhập địa chỉ từ WeChat`)}}</view>
				<!-- #endif -->
				<!-- #ifdef H5 -->
				<view class="wechatAddress" v-if="this.$wechat.isWeixin() && !id" @click="getAddress">{{$t(`Nhập địa chỉ từ WeChat`)}}
				</view>
				<!-- #endif -->
			</view>
		</form>
		<!-- #ifdef MP -->
		<!-- <authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
		<!-- #endif -->
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		editAddress,
		getAddressDetail
	} from '@/api/user.js';
	import {
		getCity
	} from '@/api/api.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";

	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	import home from '@/components/home';
	import colors from '@/mixins/color.js';
	export default {
		components: {
			// #ifdef MP
			authorize,
			// #endif
			home,
		},
		mixins: [colors],
		data() {
			return {
				regionDval: [this.$t(`Zhejiang`), this.$t(`Hangzhou`), this.$t(`Quận Binjiang`)],
				cartId: '', //ID giỏ hàng
				pinkId: 0, //ID mua chung
				couponId: 0, //ID phiếu giảm giá
				id: 0, //id địa chỉ
				userAddress: {
					is_default: false
				}, //Chi tiết địa chỉ
				region: [this.$t(`Tỉnh`), this.$t(`Thành phố`), this.$t(`Quận/Huyện`)],
				valueRegion: [0, 0, 0],
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				district: [],
				multiArray: [],
				multiIndex: [0, 0, 0],
				cityId: 0,
				defaultRegion: [this.$t(`Guangdong`), this.$t(`Guangzhou`), this.$t(`Quận Panyu`)],
				defaultRegionCode: '110101',
				news: '',
				noCoupon: 0,
				is_gift: 0,
				order_id: 0,
			};
		},
		computed: mapGetters(['isLogin']),
		watch: {
			isLogin: {
				handler: function(newV, oldV) {
					if (newV) {
						this.getUserAddress();
					}
				},
				deep: true
			}
		},
		onLoad(options) {
			if (this.isLogin) {
				this.cartId = options.cartId || '';
				this.pinkId = options.pinkId || 0;
				this.couponId = options.couponId || 0;
				this.id = options.id || 0;
				this.noCoupon = options.noCoupon || 0;
				this.news = options.new || '';
				this.is_gift = options.is_gift || '';
				this.orderId = options.order_id || '';
				uni.setNavigationBarTitle({
					title: options.id ? this.$t(`Sửa địa chỉ`) : this.$t(`Thêm địa chỉ`)
				})
				this.getUserAddress();
				this.getCityList();
			} else {
				toLogin();
			}
		},
		methods: {
			// #ifdef APP-PLUS
			// Lấy khu vực đã chọn
			handleGetRegion(region) {
				this.region = region
			},
			// #endif
			// Lấy lại dữ liệu địa chỉ
			getCityList: function() {
				let that = this;
				getCity().then(res => {
					this.district = res.data
					that.initialize();
				})
			},
			initialize() {
				let that = this,
					province = [],
					city = [],
					area = [];
				let cityChildren = that.district[0].c || [];
				let areaChildren = cityChildren.length ? (cityChildren[0].c || []) : [];
				that.district.forEach((item, i) => {
					province.push(item.n);
					if (item.n === this.region[0]) {
						this.valueRegion[0] = i
						this.multiIndex[0] = i
					}
				});
				that.district[this.valueRegion[0]].c.forEach((item, i) => {
					if (this.region[1] == item.c) {
						this.valueRegion[1] = i
						this.multiIndex[1] = i
					}
					city.push(item.n);
				});
				that.district[this.valueRegion[0]].c[this.valueRegion[1]].c.forEach((item, i) => {
					if (this.region[2] == item.c) {
						this.valueRegion[2] = i
						this.multiIndex[2] = i
					}
					area.push(item.n);
				});
				this.multiArray = [province, city, area]

			},
			bindRegionChange: function(e) {
				let multiIndex = this.multiIndex,
					province = this.district[multiIndex[0]] || {
						c: []
					},
					city = province.c[multiIndex[1]] || {
						v: 0
					},
					multiArray = this.multiArray,
					value = e.detail.value;

				this.region = [multiArray[0][value[0]], multiArray[1][value[1]], multiArray[2][value[2]]]
				// this.$set(this.region,0,multiArray[0][value[0]]);
				// this.$set(this.region,1,multiArray[1][value[1]]);
				// this.$set(this.region,2,multiArray[2][value[2]]);
				this.cityId = city.v
				this.valueRegion = [0, 0, 0]
				this.initialize();
			},
			bindMultiPickerColumnChange: function(e) {
				let that = this,
					column = e.detail.column,
					value = e.detail.value,
					currentCity = this.district[value] || {
						c: []
					},
					multiArray = that.multiArray,
					multiIndex = that.multiIndex;
				multiIndex[column] = value;
				switch (column) {
					case 0:
						let areaList = currentCity.c[0] || {
							c: []
						};
						multiArray[1] = currentCity.c.map((item) => {
							return item.n;
						});
						multiArray[2] = areaList.c.map((item) => {
							return item.n;
						});
						break;
					case 1:
						let cityList = that.district[multiIndex[0]].c[multiIndex[1]].c || [];
						multiArray[2] = cityList.map((item) => {
							return item.n;
						});
						break;
					case 2:

						break;
				}
				// #ifdef MP || APP-PLUS
				this.$set(this.multiArray, 0, multiArray[0]);
				this.$set(this.multiArray, 1, multiArray[1]);
				this.$set(this.multiArray, 2, multiArray[2]);
				// #endif
				// #ifdef H5 
				this.multiArray = multiArray;
				// #endif



				this.multiIndex = multiIndex
				// this.setData({ multiArray: multiArray, multiIndex: multiIndex});
			},
			// Callback ủy quyền
			onLoadFun: function() {
				this.getUserAddress();
			},
			// Đóng ủy quyền
			authColse: function(e) {
				this.isShowAuth = e
			},
			toggleTab(str) {
				this.$refs[str].show();
			},
			// bindRegionChange: function(e) {
			// 	this.$set(this, 'region', e.detail.value);
			// },
			onConfirm(val) {
				this.region = val.checkArr[0] + '-' + val.checkArr[1] + '-' + val.checkArr[2];
			},
			getUserAddress: function() {
				if (!this.id) return false;
				let that = this;
				getAddressDetail(this.id).then(res => {
					// let region = [res.data.province, res.data.city, res.data.district];
					let region = [res.data.province, res.data.city, res.data.district];
					that.$set(that, 'userAddress', res.data);
					that.$set(that, 'region', region);
					that.cityId = res.data.city_id
				});
			},
			// Nhập địa chỉ dùng chung (Mini Program)
			getWxAddress: function() {
				let that = this;
				uni.authorize({
					scope: 'scope.address',
					success: function(res) {
						uni.chooseAddress({
							success: function(res) {
								let addressP = {};
								addressP.province = res.provinceName;
								addressP.city = res.cityName;
								addressP.district = res.countyName;
								editAddress({
									address: addressP,
									is_default: 1,
									real_name: res.userName,
									post_code: res.postalCode,
									phone: res.telNumber,
									detail: res.detailInfo,
									id: 0,
									type: 1,
								}).then(res => {
									setTimeout(function() {
										if (that.cartId) {
											let cartId = that.cartId;
											let pinkId = that.pinkId;
											let couponId = that.couponId;
											that.cartId = '';
											that.pinkId = '';
											that.couponId = '';
											uni.navigateTo({
												url: '/pages/goods/order_confirm/index?cartId=' +
													cartId +
													'&addressId=' + (
														that.id ? that
														.id :
														res.data
														.id) +
													'&pinkId=' +
													pinkId +
													'&couponId=' +
													couponId +
													'&new=' + that
													.news +
													'&noCoupon=' + that
													.noCoupon + '&is_gift=' + that.is_gift
													+
													'&order_id=' + that.orderId
											});
										} else {
											uni.navigateBack({
												delta: 1
											});
										}
									}, 1000);
									return that.$util.Tips({
										title: that.$t(`Thêm thành công`),
										icon: 'success'
									});
								}).catch(err => {
									return that.$util.Tips({
										title: err
									});
								});
							},
							fail: function(res) {
								if (res.errMsg == 'chooseAddress:cancel') return that.$util
									.Tips({
										title: that.$t(`Đã hủy chọn`)
									});
							},
						})
					},
					fail: function(res) {
						uni.showModal({
							title: that.$t(`Bạn đã từ chối quyền nhập địa chỉ WeChat`),
							content: that.$t(`Đi tới quản lý quyền để điều chỉnh cấp quyền?`),
							success(res) {
								if (res.confirm) {
									uni.openSetting({
										success: function(res) {}
									});
								} else if (res.cancel) {
									return that.$util.Tips({
										title: that.$t(`Đã hủy!`)
									});
								}
							}
						})
					},
				})
			},
			// Nhập địa chỉ dùng chung (WeChat);
			getAddress() {
				let that = this;
				that.$wechat.openAddress().then(userInfo => {
					editAddress({
							id: this.id,
							real_name: userInfo.userName,
							phone: userInfo.telNumber,
							address: {
								province: userInfo.provinceName,
								city: userInfo.cityName,
								district: userInfo.countryName
							},
							detail: userInfo.detailInfo,
							is_default: 1,
							post_code: userInfo.postalCode,
							type: 1,
						})
						.then((res) => {
							// close();
							that.$util.Tips({
								title: that.$t(`Thêm thành công`),
								icon: 'success'
							}, () => {
								if (that.cartId) {
									let cartId = that.cartId;
									let pinkId = that.pinkId;
									let couponId = that.couponId;
									that.cartId = '';
									that.pinkId = '';
									that.couponId = '';
									uni.navigateTo({
										url: '/pages/goods/order_confirm/index?cartId=' +
											cartId + '&addressId=' + (that.id ? that.id :
												res.data
												.id) + '&pinkId=' + pinkId +
											'&couponId=' +
											couponId + '&new=' + that.news + '&is_gift=' + that.is_gift + 
						'&order_id=' + that.orderId
									});
								} else {
									uni.navigateTo({
										url: '/pages/users/user_address_list/index'
									})
									// history.back();
								}
							});
						})
						.catch(err => {
							// close();
							return that.$util.Tips({
								title: err || that.$t(`Thêm thất bại`)
							});
						});
				}).catch(err => {});
			},
			/**
			 * Gửi thêm địa chỉ của người dùng
			 * 
			 */
			formSubmit: function(e) {
				let that = this,
					value = e.detail.value;
				if (!value.real_name.trim()) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập họ tên người nhận`)
				});
				if (!value.phone) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập số điện thoại liên hệ`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(value.phone)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				if (that.region[0] == that.$t(`Tỉnh`)) return that.$util.Tips({
					title: that.$t(`Vui lòng chọn khu vực`)
				});
				if (!value.detail.trim()) return that.$util.Tips({
					title: that.$t(`Vui lòng điền địa chỉ chi tiết`)
				});
				value.id = that.id;
				let regionArray = that.region;
				value.address = {
					province: regionArray[0],
					city: regionArray[1],
					district: regionArray[2],
					city_id: that.cityId,
				};
				value.is_default = that.userAddress.is_default ? 1 : 0;

				uni.showLoading({
					title: that.$t(`Đang lưu`),
					mask: true
				})
				editAddress(value).then(res => {
					if (that.id)
						that.$util.Tips({
							title: that.$t(`Sửa thành công`),
							icon: 'success'
						});
					else
						that.$util.Tips({
							title: that.$t(`Thêm thành công`),
							icon: 'success'
						});
					setTimeout(function() {
						if (that.cartId) {
							let cartId = that.cartId;
							let pinkId = that.pinkId;
							let couponId = that.couponId;
							that.cartId = '';
							that.pinkId = '';
							that.couponId = '';
							uni.navigateTo({
								url: '/pages/goods/order_confirm/index?new=' + that.news +
									'&cartId=' + cartId + '&addressId=' + (that.id ? that.id :
										res.data.id) + '&pinkId=' + pinkId + '&couponId=' +
									couponId +
									'&noCoupon=' + that
									.noCoupon + '&is_gift=' + that.is_gift + 
									'&order_id=' + that.orderId
							});
						} else {
							// #ifdef H5
							return history.back();
							// #endif
							// #ifndef H5
							return uni.navigateBack({
								delta: 1,
							})
							// #endif
						}
					}, 1000);
				}).catch(err => {
					return that.$util.Tips({
						title: err
					});
				})
			},
			ChangeIsDefault: function(e) {
				this.$set(this.userAddress, 'is_default', !this.userAddress.is_default);
			}
		}
	}
</script>

<style scoped lang="scss">
	.fontcolor {
		color: var(--view-theme);
	}

	.addAddress .list {
		background-color: #fff;
	}

	.addAddress .list .item {
		padding: 30rpx;
		border-top: 1rpx solid #eee;
	}

	.addAddress .list .item .name {
		width: 195rpx;
		font-size: 30rpx;
		color: #333;
	}

	.addAddress .list .item .address {
		// width: 412rpx;
		flex: 1;
		margin-left: 20rpx;
	}

	.addAddress .list .item input {
		width: 475rpx;
		font-size: 30rpx;
	}

	.addAddress .list .item .placeholder {
		color: #ccc;
	}

	.addAddress .list .item picker {
		width: 475rpx;
	}

	.addAddress .list .item picker .picker {
		width: 410rpx;
		font-size: 30rpx;
	}

	.addAddress .list .item picker .iconfont {
		font-size: 43rpx;
	}

	.addAddress .default {
		padding: 0 30rpx;
		height: 90rpx;
		background-color: #fff;
		margin-top: 23rpx;
	}

	.addAddress .default checkbox {
		margin-right: 15rpx;
	}

	.addAddress .keepBnt {
		width: 690rpx;
		height: 86rpx;
		border-radius: 50rpx;
		text-align: center;
		line-height: 86rpx;
		margin: 50rpx auto;
		font-size: 32rpx;
		color: #fff;
	}

	.addAddress .wechatAddress {
		width: 690rpx;
		height: 86rpx;
		border-radius: 50rpx;
		text-align: center;
		line-height: 86rpx;
		margin: 0 auto;
		font-size: 32rpx;
		color: var(--view-theme);
		border: 1px solid var(--view-theme);
	}
</style>
