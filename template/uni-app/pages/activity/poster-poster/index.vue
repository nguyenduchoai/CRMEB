<template>
	<view class="posterCon" :style="colorStyle">
		<view class="poster-poster">
			<view class="tip">
				<text class="iconfont icon-shuoming"></text>
				{{ $t(`Lưu ý: nhấn vào ảnh để lưu vào album ảnh trên điện thoại`) }}
			</view>
			<view class="pictrue">
				<!-- <image :src='image' mode="widthFix"></image> -->
				<image class="canvas" :src="posterImage" v-if="posterImage" @click="savePosterPathMp(posterImage)"></image>
				<canvas class="canvas" canvas-id="myCanvas" v-else></canvas>
			</view>
		</view>
		<!-- #ifdef H5 || APP-PLUS -->
		<zb-code
			ref="qrcodes"
			:show="codeShow"
			:cid="cid"
			:val="val"
			:size="size"
			:unit="unit"
			:background="background"
			:foreground="foreground"
			:pdground="pdground"
			:icon="icon"
			:iconSize="iconsize"
			:onval="onval"
			:loadMake="loadMake"
			@result="qrR"
		/>
		<!-- #endif -->
	</view>
</template>

<script>
import zbCode from '@/components/zb-code/zb-code.vue';
import { getBargainPoster, getCombinationPoster, getBargainPosterData, getCombinationPosterData } from '@/api/activity.js';
import { getUserInfo, imgToBase, routineCode } from '@/api/user.js';
// #ifdef APP-PLUS
import { TOKENNAME, HTTP_REQUEST_URL } from '@/config/app.js';
// #endif
import colors from '@/mixins/color.js';
export default {
	components: {
		zbCode
	},
	mixins: [colors],
	// Component dùng props để truyền tham số
	props: {
		comType: {
			type: String,
			default: '0'
		},
		comId: {
			type: String,
			default: '0'
		},
		comBargain: {
			type: String,
			default: '0'
		}
	},
	data() {
		return {
			canvasStatus: true,
			posterImage: '',
			parameter: {
				navbar: '1',
				return: '1',
				title: this.$t(`Poster mua chung`),
				color: true,
				class: '0'
			},
			type: 0,
			id: 0,
			bargain: 0,
			image: '',
			from: '',
			uid: '',
			//Tham số mã QR
			codeShow: false,
			cid: '1',
			ifShow: true,
			val: '', // Giá trị mã QR cần tạo
			size: 200, // Kích thước mã QR
			unit: 'upx', // Đơn vị
			background: '#FFF', // Màu nền
			foreground: '#000', // Màu nền trước (foreground)
			pdground: '#000', // Màu badge góc
			icon: '', // Icon mã QR
			iconsize: 40, // Kích thước icon mã QR
			lv: 3, // Mức chịu lỗi mã QR, thường không cần đặt, để mặc định là được
			onval: true, // Khi giá trị val thay đổi thì tự tạo lại mã QR
			loadMake: true, // Sau khi component tải xong thì tự tạo mã QR
			src: '', // Địa chỉ ảnh hoặc base64 của mã QR sau khi tạo
			codeSrc: '',
			wd: 0,
			hg: 0,
			posterBag: '../static/posterBag.png',
			mpUrl: ''
		};
	},
	onLoad(options) {
		if (this.comType != 1) {
			// #ifdef MP
			this.from = 'routine';
			// #endif
			// #ifdef H5 || APP-PLUS
			this.from = 'wechat';
			// #endif

			var that = this;
			if (options.hasOwnProperty('type') && options.hasOwnProperty('id')) {
				this.type = options.type;
				this.id = options.id;
				if (options.type == 1) {
					this.bargain = options.bargain;
					uni.setNavigationBarTitle({
						title: that.$t(`Poster săn giảm giá`)
					});
				} else {
					uni.setNavigationBarTitle({
						title: that.$t(`Poster mua chung`)
					});
				}
			} else {
				return app.Tips(
					{
						title: that.$t(`Tham số không hợp lệ`),
						icon: 'none'
					},
					{
						tab: 3,
						url: 1
					}
				);
			}
		}
	},
	mounted() {
		if (this.comType == 1) {
			// #ifdef MP
			this.from = 'routine';
			// #endif
			// #ifdef H5 || APP-PLUS
			this.from = 'wechat';
			// #endif

			var that = this;
			uni.setNavigationBarTitle({
				title: that.$t(`Poster săn giảm giá`)
			});
			this.type = this.comType;
			this.id = this.comId;
			this.bargain = this.comBargain;

			// #ifdef H5
			this.val =
				window.location.origin + '/pages/activity/goods_bargain_details/index?id=' + this.id + '&bargain=' + this.$store.state.app.uid + '&spread=' + this.$store.state.app.uid;
			// #endif
			// #ifdef APP-PLUS
			this.val = HTTP_REQUEST_URL + '/pages/activity/goods_bargain_details/index?id=' + this.id + '&bargain=' + this.$store.state.app.uid + '&spread=' + this.$store.state.app.uid;
			// #endif

			this.$nextTick(() => {
				uni
					.createSelectorQuery()
					.in(this)
					.select('.pictrue')
					.boundingClientRect((data) => {
						this.wd = data.width;
						this.hg = data.height;
					})
					.exec();
			});
			this.routineCode();
			setTimeout((e) => {
				this.getPosterInfo();
			}, 200);
		}
	},
	onReady() {
		if (this.comType != 1) {
			// #ifdef H5
			if (this.type == 1) {
				this.val =
					window.location.origin + '/pages/activity/goods_bargain_details/index?id=' + this.id + '&bargain=' + this.$store.state.app.uid + '&spread=' + this.$store.state.app.uid;
			} else if (this.type == 2) {
				this.val = window.location.origin + '/pages/activity/goods_combination_status/index?id=' + this.id + '&spread=' + this.$store.state.app.uid;
			}
			// #endif
			// #ifdef APP-PLUS
			if (this.type == 1) {
				this.val =
					HTTP_REQUEST_URL + '/pages/activity/goods_bargain_details/index?id=' + this.id + '&bargain=' + this.$store.state.app.uid + '&spread=' + this.$store.state.app.uid;
			} else if (this.type == 2) {
				this.val = HTTP_REQUEST_URL + '/pages/activity/goods_combination_status/index?id=' + this.id + '&spread=' + this.$store.state.app.uid;
			}
			// #endif
			setTimeout((e) => {}, 200);
			this.$nextTick(function () {
				let selector = uni.createSelectorQuery().select('.pictrue');
				selector
					.fields(
						{
							size: true
						},
						(data) => {
							this.wd = data.width;
							this.hg = data.height;
							this.getPosterInfo();
						}
					)
					.exec();
			});
			this.routineCode();
		}
	},
	methods: {
		async getPosterInfo() {
			var that = this,
				url = '';
			let data = {
				id: that.id,
				from: that.from
			};
			let userData = await getUserInfo();
			this.uid = userData.data.uid;
			let goods_img, mp_code, resData, arr, mpUrl;
			uni.showLoading({
				title: that.$t(`Đang tạo poster`),
				mask: true
			});
			if (that.type == 1) {
				await getBargainPosterData(that.id)
					.then((res) => {
						resData = res.data;
					})
					.catch((err) => {
						that.$util.Tips({
							title: that.$t(`Lấy ảnh poster thất bại`)
						});
						return;
					});
			} else {
				await getCombinationPosterData(that.id)
					.then((res) => {
						resData = res.data;
					})
					.catch((err) => {
						that.$util.Tips({
							title: that.$t(`Lấy ảnh poster thất bại`)
						});
						return;
					});
			}

			// #ifdef H5 || APP-PLUS
			let imgData = await this.imgToBase(resData.image, resData.url);
			arr = [this.posterBag, imgData.image, imgData.code || this.codeSrc];
			// #endif
			// #ifdef MP
			resData.image = that.setDomain(resData.image);
			mpUrl = resData.url ? await this.downloadFilestoreImage(resData.url) : await this.downloadFilestoreImage(this.mpUrl);
			arr = [this.posterBag, await this.downloadFilestoreImage(resData.image), mpUrl];
			// #endif
			this.$nextTick((e) => {
				that.$util.bargainPosterCanvas(arr, resData.title, resData.label, resData.msg, resData.price, this.wd, this.hg, (tempFilePath) => {
					this.posterImage = tempFilePath;
					this.$emit('getPosterImgae', tempFilePath);
				});
			});
		},
		async routineCode() {
			let res = await routineCode();
			this.mpUrl = res.data.url;
		},
		//Chuyển ảnh sang đường dẫn phù hợp domain an toàn
		downloadFilestoreImage(url) {
			return new Promise((resolve, reject) => {
				let that = this;
				uni.downloadFile({
					url: url,
					success: function (res) {
						resolve(res.tempFilePath);
					},
					fail: function () {
						return that.$util.Tips({
							title: ''
						});
					}
				});
			});
		},
		//Thay domain an toàn
		setDomain: function (url) {
			url = url ? url.toString() : '';
			//Mở khi debug local, khi lên production hãy comment lại
			if (url.indexOf('https://') > -1) return url;
			else return url.replace('http://', 'https://');
		},
		async imgToBase(image, url) {
			let res = await imgToBase({
				image: image,
				code: url
			});

			return res.data;
		},
		downloadImg() {},
		savePosterPathMp(url) {
			let that = this;
			// #ifdef APP-PLUS
			uni.saveImageToPhotosAlbum({
				filePath: url,
				success: function (res) {
					that.$util.Tips({
						title: that.$t(`Lưu thành công`),
						icon: 'success'
					});
				},
				fail: function (res) {
					that.$util.Tips({
						title: that.$t(`Lưu thất bại`)
					});
				}
			});
			// #endif
			// #ifdef MP
			uni.getSetting({
				success(res) {
					if (!res.authSetting['scope.writePhotosAlbum']) {
						uni.authorize({
							scope: 'scope.writePhotosAlbum',
							success() {
								uni.saveImageToPhotosAlbum({
									filePath: url,
									success: function (res) {
										that.$util.Tips({
											title: that.$t(`Lưu thành công`),
											icon: 'success'
										});
									},
									fail: function (res) {
										that.$util.Tips({
											title: that.$t(`Lưu thất bại`)
										});
									}
								});
							},
							fail: function (res) {
								that.$util.Tips({
									title: that.$t(`Lưu thất bại`)
								});
							}
						});
					} else {
						uni.saveImageToPhotosAlbum({
							filePath: url,
							success: function (res) {
								that.$util.Tips({
									title: that.$t(`Lưu thành công`),
									icon: 'success'
								});
							},
							fail: function (res) {
								that.$util.Tips({
									title: that.$t(`Lưu thất bại`)
								});
							}
						});
					}
				}
			});
			// #endif
			// #ifdef H5
			// Tạo link ẩn có thể tải xuống
			var eleLink = document.createElement('a');
			eleLink.download = that.$t(`Poster`);
			eleLink.href = url;
			// Kích hoạt click
			document.body.appendChild(eleLink);
			eleLink.click();
			// #endif
		},
		qrR(res) {
			this.codeSrc = res;
		},
		// #ifdef MP
		savePosterPath: function () {
			let that = this;
			uni.getSetting({
				success(res) {
					if (!res.authSetting['scope.writePhotosAlbum']) {
						uni.authorize({
							scope: 'scope.writePhotosAlbum',
							success() {
								uni.saveImageToPhotosAlbum({
									filePath: that.posterImage,
									success: function (res) {
										that.posterImageClose();
										that.$util.Tips({
											title: that.$t(`Lưu thành công`),
											icon: 'success'
										});
									},
									fail: function (res) {
										that.$util.Tips({
											title: that.$t(`Lưu thất bại`)
										});
									}
								});
							}
						});
					} else {
						uni.saveImageToPhotosAlbum({
							filePath: that.posterImage,
							success: function (res) {
								that.posterImageClose();
								that.$util.Tips({
									title: that.$t(`Lưu thành công`),
									icon: 'success'
								});
							},
							fail: function (res) {
								that.$util.Tips({
									title: that.$t(`Lưu thất bại`)
								});
							}
						});
					}
				}
			});
		}
		// #endif
	}
};
</script>

<style>
.posterCon {
	position: fixed;
	top: 0;
	width: 100%;
	left: 0;
	height: 100%;
	background-color: var(--view-theme);
	bottom: 0;
	overflow-y: auto;
}

.poster-poster .tip {
	height: 80rpx;
	font-size: 26rpx;
	color: #e8c787;
	text-align: center;
	line-height: 80rpx;
	user-select: none;
}

.poster-poster .tip .iconfont {
	font-size: 36rpx;
	vertical-align: -4rpx;
	margin-right: 18rpx;
}

.canvas {
	width: 100%;
	height: 1100rpx;
}

.poster-poster .pictrue {
	width: 700rpx;
	/* height: 100%; */
	margin: 0 auto 50rpx auto;
	display: flex;
	justify-content: center;
}

.poster-poster .pictrue image {
	width: 100%;
	/* height: 100%; */
}
</style>
