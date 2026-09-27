<template>
	<view class="aleart" v-if="aleartStatus">
		<image src="../../../../static/images/poster-close.png" class="close" @click="posterImageClose"></image>
		<view class="title">
			<image class="title-img" src="../../static/address-aleart-header.png" mode=""></image>
		</view>
		<view class="aleart-body">
			<form @submit="formSubmit" class="form-data">
				<view class='addAddress'>
					<view class='list'>
						<view class='item acea-row row-between-wrapper'>
							<input type='text' :placeholder='$t(`Vui lòng nhập họ tên`)' name='name' :value="userAddress.name"
								placeholder-class='placeholder'></input>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<input type='number' :placeholder='$t(`Vui lòng nhập số điện thoại`)' name="phone" :value='userAddress.phone'
								placeholder-class='placeholder' pattern="\d*"></input>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class="address">
								<picker mode="multiSelector" @change="bindRegionChange"
									@columnchange="bindMultiPickerColumnChange" :value="valueRegion"
									:range="multiArray">
									<view class='acea-row'>
										<view class="picker">{{region[0]}}，{{region[1]}}，{{region[2]}}</view>
									</view>
								</picker>
							</view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<input type='text' :placeholder='$t(`Vui lòng nhập địa chỉ cụ thể`)' name='detail' placeholder-class='placeholder'
								:value='userAddress.detail'></input>
						</view>
					</view>
					<button class='keepBnt' form-type="submit">{{$t(`Gửi`)}}</button>
					<!-- #ifdef MP -->
					<!-- <view class="wechatAddress" v-if="!id" @click="getWxAddress">Nhập địa chỉ WeChat</view> -->
					<!-- #endif -->
					<!-- #ifdef H5 -->
					<!-- <view class="wechatAddress" v-if="this.$wechat.isWeixin() && !id" @click="getAddress">Nhập địa chỉ WeChat</view> -->
					<!-- #endif -->
				</view>
			</form>
		</view>
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
	export default {
		components: {
			// #ifdef MP
			authorize,
			// #endif
			home,
		},
		props: {
			aleartStatus: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				regionDval: ['Zhejiang', 'Hangzhou', 'Quận Binjiang'],
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
				defaultRegion: ['Guangdong', 'Guangzhou', 'Quận Panyu'],
				defaultRegionCode: '110101',
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
		created(options) {
			if (this.isLogin) {
				this.getCityList();
				this.getUserAddress();
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
			// Lấy dữ liệu địa chỉ
			getCityList() {
				let that = this;
				getCity().then(res => {
					this.district = res.data
					that.initialize();
				})
			},
			// Xử lý dữ liệu địa chỉ
			initialize() {
				let that = this,
					province = [],
					city = [],
					area = [];
				if (that.district.length) {
					let cityChildren = that.district[0].c || [];
					let areaChildren = cityChildren.length ? (cityChildren[0].c || []) : [];
					that.district.forEach(function(item) {
						province.push(item.n);
					});
					cityChildren.forEach(function(item) {
						city.push(item.n);
					});
					areaChildren.forEach(function(item) {
						area.push(item.n);
					});
					this.multiArray = [province, city, area]
				}
			},
			bindRegionChange(e) {
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
			bindMultiPickerColumnChange(e) {
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
			onLoadFun() {
				this.getUserAddress();
			},
			// Đóng ủy quyền
			authColse(e) {
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
			getUserAddress() {
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
			getWxAddress() {
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
												url: '/pages/goods/lottery/grids/index?addressId=' +
													that.id ? that
													.id : res.data
													.id
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
										title: that.$t(`Hủy`)
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
										title: that.$t(`Đã hủy`)
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
						.then(() => {
							setTimeout(() => {
								uni.navigateTo({
									url: '/pages/goods/lottery/grids/index?addressId=' +
										that.id ? that.id : res.data.id
								})
							}, 1000);
							// close();
							that.$util.Tips({
								title: that.$t(`Thêm thành công`),
								icon: 'success'
							});
						})
						.catch(err => {
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
			formSubmit(e) {
				let that = this,
					value = e.detail.value;
				if (!value.name) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập họ tên người nhận`)
				});
				if (!value.phone) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(value.phone)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				if (that.region[0] == 'Tỉnh') return that.$util.Tips({
					title: that.$t(`Vui lòng chọn khu vực`)
				});
				if (!value.detail) return that.$util.Tips({
					title: that.$t(`Vui lòng điền địa chỉ chi tiết`)
				});
				let regionArray = that.region;
				value.address = {
					province: regionArray[0],
					city: regionArray[1],
					district: regionArray[2],
					city_id: that.cityId,
				};
				this.$emit('getAddress', value)
			},
			//Ẩn popup
			posterImageClose() {
				this.$emit("close", false)
			},
		}
	}
</script>

<style lang="scss" scoped>
	.aleart {
		width: 500rpx;
		// height: 714rpx;
		position: fixed;
		left: 50%;
		transform: translateX(-50%);
		z-index: 999;
		top: 50%;
		margin-top: -357rpx;
		border-radius: 12rpx;

		.title {
			padding: 0;
			margin: 0;
			height: 110rpx;

			.title-img {
				width: 100%;
				height: 100%;
			}
		}

		.aleart-body {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			background-color: #FDF7E7;

			.form-data {
				width: 100%;
			}

			.goods-img {
				width: 150rpx;
				height: 150rpx;
			}

			.msg {
				font-size: 30rpx;
				color: #282828;
			}
		}

		.close {
			width: 46rpx;
			height: 75rpx;
			position: fixed;
			right: 20rpx;
			top: -73rpx;
			display: block;
		}
	}

	.addAddress {
		width: 100%;
		padding: 30rpx 30rpx;
	}

	.addAddress .list {
		background-color: #FDF7E7;
	}

	.addAddress .list .item {
		border: 1rpx solid #A05644;
		margin-bottom: 24rpx;
		padding: 10rpx;
		border-radius: 6rpx;
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
		// width: 475rpx;
		font-size: 30rpx;

	}

	.addAddress .list .item .placeholder {
		color: #ccc;
	}

	.addAddress .list .item picker {
		// width: 475rpx;
	}

	.addAddress .list .item picker .picker {
		// width: 410rpx;
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
		width: 100%;
		height: 86rpx;
		border-radius: 6rpx;
		text-align: center;
		line-height: 86rpx;
		font-size: 32rpx;
		color: #452015;
		font-weight: bold;
		background: #EEBE6B;
		box-shadow: 0px 5px 9px 0px rgba(220, 166, 72, 0.24);
	}

	.addAddress .wechatAddress {
		height: 86rpx;
		border-radius: 6rpx;
		text-align: center;
		line-height: 86rpx;
		margin: 0 auto;
		font-size: 32rpx;
		color: #fe960f;
		border: 1px solid #fe960f;
	}
</style>
