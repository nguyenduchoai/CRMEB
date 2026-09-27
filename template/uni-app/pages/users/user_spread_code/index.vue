<template>
	<view :style="colorStyle">
		<view class='distribution-posters'>
			<swiper :indicator-dots="indicatorDots" :autoplay="autoplay" :circular="circular" :interval="interval"
				:duration="duration" @change="bindchange" previous-margin="40px" next-margin="40px">
				<block v-for="(item,index) in spreadData" :key="index" class="img-list">
					<swiper-item class="aaa">
						<div class="box" ref="bill" :class="swiperIndex == index ? 'active' : 'quiet'">
							<view class="user-msg">
								<view class="user-code">
									<image class="canvas" :style="{height:hg+'px'}" :src="posterImage[index]"
										v-if="posterImage[index]"></image>
									<canvas class="canvas" :style="{height:hg+'px'}" :canvas-id="'myCanvas'+ index"
										v-else></canvas>
								</view>
							</view>
						</div>
						<!-- <image :src="item.wap_poster" class="slide-image" :class="swiperIndex == index ? 'active' : 'quiet'" mode='aspectFill' /> -->
					</swiper-item>
				</block>
			</swiper>
			<!-- #ifndef H5  -->
			<view class='keep bg-color' @click='savePosterPathMp(posterImage[swiperIndex])'>{{$t(`Lưu poster`)}}</view>
			<!-- #endif -->
			<!-- #ifndef MP || APP-PLUS -->
			<div class="preserve acea-row row-center-wrapper">
				<div class="line"></div>
				<div class="tip">{{$t(`Nhấn giữ để lưu ảnh`)}}</div>
				<div class="line"></div>
			</div>
			<!-- #endif -->
		</view>
		<!-- #ifdef MP -->
		<!-- <authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
		<!-- #endif -->
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
		<view class="qrimg">
			<zb-code ref="qrcode" :show="codeShow" :cid="cid" :val="val" :size="size" :unit="unit"
				:background="background" :foreground="foreground" :pdground="pdground" :icon="icon" :iconSize="iconsize"
				:onval="onval" :loadMake="loadMake" @result="qrR" />
		</view>
	</view>
</template>

<script>
	import zbCode from '@/components/zb-code/zb-code.vue'
	import {
		getUserInfo,
		spreadBanner,
		userShare,
		routineCode,
		spreadMsg,
		imgToBase
	} from '@/api/user.js';
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
	import {
		TOKENNAME,
		HTTP_REQUEST_URL
	} from '@/config/app.js';
	import colors from '@/mixins/color.js';
	export default {
		components: {
			// #ifdef MP
			authorize,
			// #endif
			home,
			zbCode
		},
		mixins: [colors],
		data() {
			return {
				imgUrls: [],
				indicatorDots: false,
				posterImageStatus: true,
				circular: false,
				autoplay: false,
				interval: 3000,
				duration: 500,
				swiperIndex: 0,
				spreadList: [],
				userInfo: {},
				poster: '',
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				spreadData: [{}], //Dữ liệu poster mới
				nickName: "",
				siteName: "",
				mpUrl: "",
				canvasImageUrl: '',
				posterImage: [],
				//Tham số mã QR
				codeShow: false,
				cid: '1',
				ifShow: true,
				val: "", // Giá trị mã QR cần tạo
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
				codeSrc: "",
				wd: 0,
				hg: 0,
				qrcode: ""
			};
		},
		computed: mapGetters({
			'isLogin': 'isLogin',
			'userData': 'userInfo',
			'uid': 'uid'
		}),
		watch: {
			isLogin: {
				handler: function(newV, oldV) {
					if (newV) {
						this.userSpreadBannerList();
					}
				},
				deep: true
			},
			userData: {
				handler: function(newV, oldV) {
					if (newV) {
						this.$set(this, 'userInfo', newV);
					}
				},
				deep: true
			}
		},
		async onReady() {
			if (this.isLogin) {
				this.val = `${HTTP_REQUEST_URL}?spid=${this.uid}`
				await this.getUser()
			} else {
				toLogin();

			}
			this.$nextTick(() => {
				let selector = uni.createSelectorQuery().select('.aaa');
				selector.fields({
					size: true
				}, data => {
					this.wd = data.width
					this.hg = data.height
				}).exec();
			})
		},
		onShow() {

		},
		onHide() {
			uni.hideLoading();
		},
		/**
		 * Người dùng click chia sẻ ở góc trên phải
		 */
		// #ifdef MP
		onShareAppMessage() {
			return {
				title: this.userInfo.nickname + '-' + this.$t(`Poster tiếp thị liên kết`),
				imageUrl: this.spreadList[0],
				path: '/pages/index/index?spread=' + this.userInfo.uid,
			};
		},
		// #endif
		methods: {
			getUser() {
				getUserInfo().then(res => {
					this.userInfo = res.data
				})
			},
			onLoadFun: function(e) {
				this.$set(this, 'userInfo', e);
				this.userSpreadBannerList();
			},
			qrR(res) {
				this.codeSrc = res
				this.spreadMsgs()
			},
			//Lấy hình ảnh
			async spreadMsgs() {

				let res = await spreadMsg()
				this.spreadData = res.data.spread
				this.nickName = res.data.nickname
				this.siteName = res.data.site_name
				// #ifdef MP
				this.qrcode = await this.imgToBase(res.data.qrcode)
				// #endif
				// #ifdef MP
				await this.routineCode()
				let mpUrl = await this.downloadFilestoreImage(this.mpUrl)
				// #endif
				uni.showLoading({
					title: this.$t(`Đang tạo poster`),
					mask: true
				});

				for (let i = 0; i < res.data.spread.length; i++) {
					let that = this
					let arr2, img
					// #ifdef MP	
					arr2 = [mpUrl, await this.downloadFilestoreImage(res.data.spread[i].pic)]
					// #endif
					// #ifdef H5
					img = await this.imgToBase(res.data.spread[i].pic, res.data.qrcode)
					arr2 = [img.code || this.codeSrc, img.image]
					// #endif
					// #ifdef APP-PLUS
					img = await this.imgToBase(res.data.spread[i].pic, res.data.qrcode)
					arr2 = [img.code || this.codeSrc, res.data.spread[i].pic]
					// #endif
					that.$util.userPosterCanvas(arr2, res.data.nickname, res.data.site_name, i, this
						.wd,
						this.hg, (
							tempFilePath) => {
							that.$set(that.posterImage, i, tempFilePath);
							// #ifdef MP
							if (!that.posterImage.length) {
								return that.$util.Tips({
									title: that.$t(`Chỉ lấy được mã QR Mini Program sau khi phát hành phiên bản chính thức`)
								});
							}
							// #endif
						});
				}
				uni.hideLoading();

			},
			downloadImg() {
				uni.saveImageToPhotosAlbum({
					filePath: this.posterImage[this.swiperIndex],
					success: function() {}
				});
			},
			async routineCode() {
				let res = await routineCode()
				this.mpUrl = res.data.url
			},
			async imgToBase(url, code) {
				let res = await imgToBase({
					image: url,
					code: code
				})
				return res.data
			},
			// Tạo mã QR
			codeImg() {
				// http://domain hiện tại+"?spread="+uid người dùng
			},
			// Đóng ủy quyền
			authColse: function(e) {
				this.isShowAuth = e
			},
			bindchange(e) {
				let spreadList = this.spreadList;
				this.swiperIndex = e.detail.current;
				// this.$set(this, 'poster', spreadList[e.detail.current].poster);
			},
			// #ifdef MP
			savePosterPathMp(url) {
				let that = this;
				uni.getSetting({
					success(res) {
						if (!res.authSetting['scope.writePhotosAlbum']) {
							uni.authorize({
								scope: 'scope.writePhotosAlbum',
								success() {
									uni.saveImageToPhotosAlbum({
										filePath: url,
										success: function(res) {
											that.$util.Tips({
												title: that.$t(`Lưu thành công`),
												icon: 'success'
											});
										},
										fail: function(res) {
											that.$util.Tips({
												title: that.$t(`Lưu thất bại`),
											});
										}
									});
								}
							});
						} else {
							uni.saveImageToPhotosAlbum({
								filePath: url,
								success: function(res) {
									that.$util.Tips({
										title: that.$t(`Lưu thành công`),
										icon: 'success'
									});
								},
								fail: function(res) {
									that.$util.Tips({
										title: that.$t(`Lưu thất bại`),
									});
								}
							});
						}
					}
				});
			},
			// #endif
			// #ifdef APP-PLUS
			savePosterPathMp(url) {
				let that = this;
				uni.saveImageToPhotosAlbum({
					filePath: url,
					success: function(res) {
						that.$util.Tips({
							title: that.$t(`Lưu thành công`),
							icon: 'success'
						});
					},
					fail: function(res) {
						that.$util.Tips({
							title: that.$t(`Lưu thất bại`),
						});
					}
				});
			},
			// #endif
			//Chuyển ảnh sang đường dẫn phù hợp domain an toàn
			downloadFilestoreImage(url) {
				return new Promise((resolve, reject) => {
					let that = this;
					uni.downloadFile({
						url: url,
						success: function(res) {
							resolve(res.tempFilePath);
						},
						fail: function() {
							return that.$util.Tips({
								title: ''
							});
						}
					});
				})
			},
			setShareInfoStatus: function() {
				if (this.$wechat.isWeixin()) {
					if (this.isLogin) {
						getUserInfo().then(res => {
							let configAppMessage = {
								desc: this.$t(`Poster tiếp thị liên kết`),
								title: res.data.nickname + '-' + this.$t(`Poster tiếp thị liên kết`),
								link: '/pages/index/index?spread=' + res.data.uid,
								imgUrl: this.spreadList[0]
							};
							this.$wechat.wechatEvevt(["updateAppMessageShareData", "updateTimelineShareData"],
								configAppMessage)
						});
					} else {
						toLogin();
					}

				}
			},
			userSpreadBannerList: function() {
				let that = this;
				uni.showLoading({
					title: that.$t(`Đang tải`),
					mask: true,
				})
				spreadBanner().then(res => {
					uni.hideLoading();
					that.$set(that, 'spreadList', res.data);
					that.$set(that, 'poster', res.data[0].poster);
					// #ifdef H5
					that.setShareInfoStatus();
					// #endif
				}).catch(err => {
					uni.hideLoading();
				});
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #a3a3a3 !important;
	}

	.canvas {
		width: 100%;
		// height: 550px;
	}

	.box {
		width: 100%;
		height: 100%;
		position: relative;
		border-radius: 18rpx;
		overflow: hidden;

		.user-msg {
			position: absolute;
			width: 100%;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: center;

			.user-code {
				width: 100%;
				// height: 100%;
				display: flex;
				align-items: center;
				justify-content: center;
				justify-content: space-between;

				image {
					width: 100%;
				}
			}
		}
	}

	.img-list {
		margin-right: 40px;
	}

	.distribution-posters swiper {
		width: 100%;
		height: 1000rpx;
		position: relative;
		margin-top: 40rpx;
	}

	.distribution-posters .slide-image {
		width: 100%;
		height: 100%;
		margin: 0 auto;
		border-radius: 15rpx;
	}

	.distribution-posters ::v-deep.active {
		transform: none;
		transition: all 0.2s ease-in 0s;
	}

	.distribution-posters ::v-deep .quiet {
		transform: scale(0.8333333);
		transition: all 0.2s ease-in 0s;
	}

	.distribution-posters .keep {
		font-size: 30rpx;
		color: #fff;
		width: 600rpx;
		height: 80rpx;
		border-radius: 50rpx;
		text-align: center;
		line-height: 80rpx;
		margin: 38rpx auto;
	}

	.distribution-posters .preserve {
		color: #fff;
		text-align: center;
		margin-top: 38rpx;
	}

	.distribution-posters .preserve .line {
		width: 100rpx;
		height: 1px;
		background-color: #fff;
	}

	.distribution-posters .preserve .tip {
		margin: 0 30rpx;
	}
</style>
