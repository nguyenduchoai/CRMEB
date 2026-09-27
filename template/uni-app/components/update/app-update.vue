<template>
	<view class="wrap" v-if="popup_show">
		<view class="popup-bg" :style="getHeight">
			<view class="popup-content" :class="{ 'popup-content-show': popup_show }">
				<view class="update-wrap">
					<image src="./images/img.png" class="top-img"></image>
					<view class="content">
						<text class="title">{{$t(`Có phiên bản mới`)}}{{ update_info.version }}</text>
						<!-- Mô tả cập nhật -->
						<view class="title-sub" v-html="update_info.info"></view>
						<!-- Nút cập nhật -->
						<button class="btn" v-if="downstatus < 1" @click="nowUpdate()">
							{{$t(`Nâng cấp ngay`)}}
						</button>
						<!-- Tiến độ tải xuống -->
						<view class="sche-wrap" v-else>
							<!-- Đang tải gói cập nhật -->
							<view class="sche-bg">
								<view class="sche-bg-jindu" :style="lengthWidth"></view>
							</view>
							<text class="down-text">{{$t(`Tiến độ tải xuống`)}}:{{ (downSize / 1024 / 1024).toFixed(2) }}M/{{
                  (fileSize / 1024 / 1024).toFixed(2)
                }}M</text>
						</view>
					</view>
				</view>
				<image src="./images/close.png" class="close-ioc" @click="closeUpdate()"></image>
			</view>
		</view>
	</view>
</template>

<script>
	let vm;
	import {
		getUpdateInfo
	} from '@/api/public.js'

	export default {
		name: "appUpdate",
		//@Có bắt buộc cập nhật hay không
		props: {
			tabbar: {
				type: Boolean,
				default: false, //Có component tabbar gốc (native) hay không
			},
			getVer: {
				type: Boolean,
				default: false, //Có component tabbar gốc (native) hay không
			},
		},
		data() {
			return {
				popup_show: false, //Popup có hiển thị hay không
				platform: "", //ios or android
				version: "1.0.0", //Phiên bản phần mềm hiện tại
				need_update: false, // Có cập nhật hay không
				downing: false, //Có đang tải hay không
				downstatus: 0, //0 chưa tải  1 đã bắt đầu  2 đã kết nối tới tài nguyên  3 đã nhận dữ liệu  4 tải xong
				update_info: {
					os: "", //Hệ điều hành thiết bị
					version: "", //Phiên bản mới nhất
					info: "", //Mô tả cập nhật
				},
				fileSize: 0, //Kích thước file
				downSize: 0, //Kích thước đã tải
				viewObj: null, //View lớp phủ (mask) gốc
			};
		},
		created() {
			vm = this;
			if (!this.getVer) this.update()
		},
		computed: {
			// Tính toán tiến độ tải
			lengthWidth: function() {
				let w = (this.downSize / this.fileSize) * 100;
				if (!w) {
					w = 0;
				} else {
					w = w.toFixed(2);
				}
				return {
					width: w + "%", //return phần trăm chiều rộng
				};
			},
			getHeight() {
				let bottom = 0;
				if (this.tabbar) {
					bottom = 50;
				}
				return {
					bottom: bottom + "px",
					height: "auto",
				};
			},
		},
		methods: {
			// Kiểm tra cập nhật
			update() {
				// #ifdef APP-PLUS
				// Lấy thông tin hệ điều hành điện thoại
				uni.getSystemInfo({
					success: function(res) {
						vm.platform = res.platform; //ios  or android
						console.log("Thông tin hệ thống điện thoại", vm.platform);
					},
				});

				// Lấy số phiên bản
				plus.runtime.getProperty(plus.runtime.appid, function(inf) {
					vm.version = inf.version;
				});
				console.log("Phiên bản hiện tại", vm.version);
				this.getUpdateInfo(); //Lấy thông tin cập nhật
				// #endif
			},

			// Lấy thông tin phiên bản trên server
			getUpdateInfo() {
				//Gửi request tới backend để lấy số phiên bản mới nhất
				getUpdateInfo(this.platform === "ios" ? 2 : 1)
					.then((res) => {
						if(Array.isArray(res.data)){
						 return	this.$emit('isNew')
						}
						const tagDate = uni.getStorageSync('app_update_time') || '',
							nowDate = new Date().toLocaleDateString();
						if (tagDate !== nowDate && !this.getVer) {
							uni.setStorageSync('app_update_time', new Date().toLocaleDateString());
						} else if ((tagDate !== nowDate) && this.getVer) {
							if (!res.data.is_force) return
						} else if (tagDate == nowDate && !this.getVer && !res.data.is_force) {
							return
						}
						// Dữ liệu trả về ở đây theo quy ước với backend
						let data = res.data;
						// Lặp để lấy dữ liệu cập nhật tương ứng với thiết bị hiện tại
						vm.update_info = data;
						if (!vm.update_info.platform) {
							// Backend chưa cấu hình dữ liệu cập nhật cho hệ điều hành hiện tại
						} else {
							vm.checkUpdate(); ///kiểm tra có cập nhật hay không
						}
					})
					.catch((err) => {
						vm.popup_show = false
					});
			},
			// Kiểm tra có cập nhật hay không
			checkUpdate() {
				vm.need_update = vm.compareVersion(vm.version, vm.update_info.version); // Kiểm tra có cần cập nhật hay không
				if (vm.need_update) {
					vm.popup_show = true; //Số phiên bản trên server lớn hơn số phiên bản đang cài, hiển thị khung cập nhật
					if (vm.tabbar) {
						//Trang có component tabbar gốc hay không
						// Tạo view gốc để che sự kiện click của tabbar (nếu không dùng tabbar gốc thì có thể bỏ qua bước này)
						vm.viewObj = new plus.nativeObj.View("viewObj", {
							bottom: "0px",
							left: "0px",
							height: "50px",
							width: "100%",
							backgroundColor: "rgba(0,0,0,.6)",
						});
						vm.viewObj.show(); //Hiển thị lớp phủ gốc
					}
				} else {
					this.$emit('isNew')
				}
			},

			// Hủy cập nhật
			closeUpdate() {
				if (vm.update_info.is_force) {
					// Bắt buộc cập nhật, hủy thoát app
					this.platform == "android" ?
						plus.runtime.quit() :
						plus.ios
						.import("UIApplication")
						.sharedApplication()
						.performSelector("exit");
				} else {
					vm.popup_show = false; //Đóng popup cập nhật
					if (vm.viewObj) vm.viewObj.hide(); //Ẩn lớp phủ gốc
				}
			},
			// Cập nhật ngay
			nowUpdate() {
				if (vm.downing) return false; //Nếu đang tải thì dừng thao tác
				vm.downing = true; //Trạng thái thay đổi: đang tải

				if (/\.apk$/.test(vm.update_info.url)) {
					// Nếu là địa chỉ apk
					vm.download_wgt(); // Cập nhật gói cài đặt/gói nâng cấp
				} else if (/\.wgt$/.test(vm.update_info.url)) {
					// Nếu là gói cập nhật
					vm.download_wgt(); // Cập nhật gói cài đặt/gói nâng cấp
				} else {
					plus.runtime.openURL(vm.update_info.url, function() {
						//Gọi trình duyệt bên ngoài để mở địa chỉ cập nhật
						plus.nativeUI.toast("Lỗi khi mở");
					});
				}
			},
			// Tải gói tài nguyên cập nhật
			download_wgt() {
				plus.nativeUI.showWaiting("Đang tải tệp cập nhật..."); //Đang tải tệp cập nhật...
				let options = {
					method: "get",
				};
				let dtask = plus.downloader.createDownload(
					vm.update_info.url,
					options,
					function(d, status) {}
				);

				dtask.addEventListener("statechanged", function(task, status) {
					if (status === null) {} else if (status == 200) {
						//In log ở đây sẽ chạy liên tục, chú ý, khi lên chính thức nhớ không in gì ở đây///////////////////////////////////////////////////
						vm.downstatus = task.state;
						switch (task.state) {
							case 3: // Đã nhận dữ liệu
								vm.downSize = task.downloadedSize;
								if (task.totalSize) {
									vm.fileSize = task.totalSize; //Server phải trả về content-length đúng thì mới có length
								}
								break;
							case 4:
								vm.installWgt(task.filename); // Cài đặt gói wgt
								break;
						}
					} else {
						plus.nativeUI.closeWaiting();
						plus.nativeUI.toast("Lỗi tải xuống");
						vm.downing = false;
						vm.downstatus = 0;
					}
				});
				dtask.start();
			},

			// Cài đặt file
			installWgt(path) {
				plus.nativeUI.showWaiting("Đang cài đặt tệp cập nhật..."); //Đang cài đặt tệp cập nhật...
				plus.runtime.install(
					path, {},
					function() {
						plus.nativeUI.closeWaiting();
						// Đã tải xong tài nguyên ứng dụng!
						plus.nativeUI.alert("Đã tải xong tài nguyên ứng dụng!", function() {
							plus.runtime.restart();
						});
					},

					function(e) {
						plus.nativeUI.closeWaiting();
						// Cài đặt file cập nhật thất bại
						plus.nativeUI.alert("Cài đặt tệp cập nhật thất bại [" + e.code + "]：" + e.message);
					}
				);
			},
			// So sánh số phiên bản
			compareVersion(ov, nv) {
				if (!ov || !nv || ov == "" || nv == "") {
					return false;
				}
				let b = false,
					ova = ov.split(".", 4),
					nva = nv.split(".", 4);
				for (let i = 0; i < ova.length && i < nva.length; i++) {
					let so = ova[i],
						no = parseInt(so),
						sn = nva[i],
						nn = parseInt(sn);
					if (nn > no || sn.length > so.length) {
						return true;
					} else if (nn < no) {
						return false;
					}
				}
				if (nva.length > ova.length && 0 == nv.indexOf(ov)) {
					return true;
				} else {
					return false;
				}
			},
		},
	};
</script>

<style lang="scss" scoped>
	.popup-bg {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		position: fixed;
		top: 0;
		left: 0rpx;
		right: 0;
		bottom: 0;
		width: 750rpx;
		background-color: rgba(0, 0, 0, 0.6);
		z-index: 10000;
	}

	.popup-content {
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.popup-content-show {
		animation: mymove 500ms;
		transform: scale(1);
	}

	@keyframes mymove {
		0% {
			transform: scale(0);
			/*Bắt đầu bằng kích thước gốc*/
		}

		100% {
			transform: scale(1);
		}
	}

	.update-wrap {
		width: 580rpx;
		border-radius: 18rpx;
		position: relative;
		display: flex;
		flex-direction: column;
		background-color: #ffffff;
		padding: 170rpx 30rpx 0;

		.top-img {
			position: absolute;
			left: 0;
			width: 100%;
			height: 256rpx;
			top: -128rpx;
		}

		.content {
			display: flex;
			flex-direction: column;
			align-items: center;
			padding-bottom: 40rpx;

			.title {
				font-size: 32rpx;
				font-weight: bold;
				color: #6526f3;
			}

			.title-sub {
				text-align: center;
				font-size: 24rpx;
				color: #666666;
				padding: 30rpx 0;
			}

			.btn {
				width: 460rpx;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #ffffff;
				font-size: 30rpx;
				height: 80rpx;
				line-height: 80rpx;
				border-radius: 100px;
				background-color: #6526f3;
				margin-top: 20rpx;
			}
		}
	}

	.close-ioc {
		width: 70rpx;
		height: 70rpx;
		margin-top: 30rpx;
	}

	.sche-wrap {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: flex-end;
		padding: 10rpx 50rpx 0;

		.sche-wrap-text {
			font-size: 24rpx;
			color: #666;
			margin-bottom: 20rpx;
		}

		.sche-bg {
			position: relative;
			background-color: #cccccc;
			height: 30rpx;
			border-radius: 100px;
			width: 480rpx;
			display: flex;
			align-items: center;

			.sche-bg-jindu {
				position: absolute;
				left: 0;
				top: 0;
				height: 30rpx;
				min-width: 40rpx;
				border-radius: 100px;
				background: url(images/round.png) #5775e7 center right 4rpx no-repeat;
				background-size: 26rpx 26rpx;
			}
		}

		.down-text {
			font-size: 24rpx;
			color: #5674e5;
			margin-top: 16rpx;
		}
	}
</style>
