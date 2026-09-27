<template>
	<view v-if="status == -1 && !inloading" :style="colorStyle">
			<view class='merchantsSettled'>
				<image mode="widthFix" class="merchantBg" :src="headerBg" alt="">
					<!-- <view class="application-record" @click="jumpToList">
						Bản ghi đăng ký
						<text class="iconfont icon-xiangyou"></text>
					</view> -->
					<view class='list' v-if="isAgent">
						<view class="item">
							<view class="acea-row row-middle">
								<!-- <i class="icon iconfont icon-qiye"></i> -->
								<text class="item-name">{{$t(`Tên đại lý`)}}</text>
								<input type="text" maxlength="30" :placeholder="$t(`Vui lòng nhập tên đại lý`)"
									v-model="merchantData.agent_name" @input="validateBtn"
									placeholder-class='placeholder' />
							</view>
						</view>
						<view class="item">
							<view class="acea-row row-middle">
								<!-- <i class="icon iconfont icon-yonghu3"></i> -->
								<text class="item-name">{{$t(`Họ tên người dùng`)}}</text>
								<input type="text" :placeholder="$t(`Vui lòng nhập họ tên`)" v-model="merchantData.name"
									@input="validateBtn" placeholder-class='placeholder' />
							</view>
						</view>
						<view class="item">
							<view class="acea-row row-middle">
								<!-- <i class="icon iconfont icon-shoujihao"></i> -->
								<text class="item-name">{{$t(`Số điện thoại liên hệ`)}}</text>
								<input type="text" :placeholder="$t(`Vui lòng nhập số điện thoại`)" v-model="merchantData.phone"
									@input="validateBtn" placeholder-class='placeholder' />
							</view>
						</view>
						<view class="item rel">
							<view class="acea-row row-middle">
								<!-- <i class="icon iconfont icon-yanzhengma"></i> -->
								<text class="item-name">{{$t(`Mã xác thực`)}}</text>
								<input type="text" :placeholder="$t(`Nhập mã xác thực`)" v-model="merchantData.code"
									@input="validateBtn" class="codeIput" placeholder-class='placeholder' />
								<button class="code" :disabled="disabled" :class="disabled === true ? 'on' : ''"
									@click="code">
									{{ text }}
								</button>

							</view>
						</view>
						<view class="item">
							<view class="acea-row row-middle">
								<!-- <i class="icon iconfont icon-shoujihao"></i> -->
								<text class="item-name">{{$t(`Mã mời`)}}</text>
								<input type="text" :placeholder="$t(`Vui lòng nhập mã mời của đại lý`)" v-model="merchantData.division_invite"
									@input="validateBtn" placeholder-class='placeholder' />
							</view>
						</view>
						<view class="item no-border">
							<view class='acea-row row-middle'>
								<text class="item-title">{{$t(`Vui lòng tải lên ảnh giấy phép kinh doanh và giấy tờ chứng nhận đủ điều kiện liên quan đến ngành nghề`)}}</text>
								<text class="item-desc">({{$t(`Tải lên tối đa 10 ảnh, hỗ trợ định dạng JPG, PNG, JPEG`)}})</text>
								<view class="upload">
									<view class='pictrue' v-for="(item,index) in images" :key="index"
										:data-index="index" @click="getPhotoClickIdx">
										<image :src='item'></image>
										<text class='iconfont icon-guanbi1' @click.stop='DelPic(index)'></text>
									</view>
									<view class='pictrue acea-row row-center-wrapper row-column' @click='uploadpic'
										v-if="images.length < 10">
										<text class='iconfont icon-icon25201'></text>
										<view>{{$t(`Tải lên ảnh`)}}</view>
									</view>
								</view>
							</view>
						</view>

						<view class="item no-border acea-row row-middle">
							<checkbox-group @change='ChangeIsAgree'>
								<checkbox class="checkbox" :checked="isAgree ? true : false" />{{$t(`Đã đọc và đồng ý`)}}
							</checkbox-group>
							<button class="settleAgree" @click="getAgentAgreement">《{{$t(`Thỏa thuận đại lý`)}}》</button>
						</view>
						<button class='submitBtn' :class="isAgree === true ? 'on':''"
							@click="formSubmit">{{$t(`Gửi đăng ký`)}}</button>

					</view>
					<view class='list' v-else>
						<view class="item">
							<view class="acea-row row-middle row-between">
								<!-- <i class="icon iconfont icon-qiye"></i> -->
								<text class="item-name">{{$t(`Biệt danh người dùng`)}}</text>
								<view class="text-right">{{ form.nickname }}</view>
							</view>
						</view>
						<view class="item">
							<view class="acea-row row-middle row-between">
								<!-- <i class="icon iconfont icon-yonghu3"></i> -->
								<text class="item-name">{{$t(`ID người dùng`)}}</text>
								<view class="fs-28 text-right">{{ form.uid }}123</view>
							</view>
						</view>
						<view class="item">
							<view class="acea-row row-middle row-between">
								<!-- <i class="icon iconfont icon-shoujihao"></i> -->
								<text class="item-name">{{$t(`Họ tên CTV`)}}</text>
								<input class="text-right" type="text" :placeholder="$t(`Vui lòng nhập họ tên cộng tác viên`)" v-model="form.real_name"
									@input="validateBtn" placeholder-class='placeholder' />
							</view>
						</view><view class="item">
							<view class="acea-row row-middle row-between">
								<!-- <i class="icon iconfont icon-shoujihao"></i> -->
								<text class="item-name">{{$t(`Số điện thoại liên hệ`)}}</text>
								<input class="text-right" type="text" :placeholder="$t(`Vui lòng nhập số điện thoại`)" v-model="form.phone"
									@input="validateBtn" placeholder-class='placeholder' />
							</view>
						</view>
						<view class="item rel">
							<view class="acea-row row-middle">
								<!-- <i class="icon iconfont icon-yanzhengma"></i> -->
								<text class="item-name">{{$t(`Mã xác thực`)}}</text>
								<input type="text" :placeholder="$t(`Nhập mã xác thực`)" v-model="form.code"
									@input="validateBtn" class="codeIput" placeholder-class='placeholder' />
								<button class="code" :disabled="disabled" :class="disabled === true ? 'on' : ''"
									@click="code">
									{{ text }}
								</button>
						
							</view>
						</view>
						<view class="item">
							<view class="acea-row row-middle row-between">
								<!-- <i class="icon iconfont icon-shoujihao"></i> -->
								<text class="item-name">{{$t(`Lý do đăng ký`)}}</text>
								<textarea class="text-area" :placeholder="$t(`Vui lòng nhập lý do đăng ký`)" v-model="form.content" cols="3" rows="4" placeholder-class='placeholder'></textarea>
							</view>
						</view>
						<view class="item no-border  acea-row row-middle">
							<checkbox-group @change='ChangeIsAgree'>
								<checkbox class="checkbox" :checked="isAgree ? true : false" />{{$t(`Đã đọc và đồng ý`)}}
							</checkbox-group>
							<button class="settleAgree" @click="getAgentAgreement">《{{$t(`Thỏa thuận cộng tác viên`)}}》</button>
						</view>
						<button class='submitBtn' :class="isAgree === true ? 'on':''"
							@click="formSpeadSubmit">{{$t(`Gửi đăng ký`)}}</button>
					</view>
			</view>

		<view class="settlementAgreement" v-if="showProtocol">
			<view class="setAgCount">
				<i class="icon iconfont icon-cha" @click="showProtocol = false"></i>
				<div class="title">{{ $t(isAgent?`Thỏa thuận đăng ký đại lý`:'Hướng dẫn tiếp thị liên kết')}}</div>
				<view class="content">
					<jyf-parser :html="protocol" ref="article" :tag-style="tagStyle"></jyf-parser>
				</view>
			</view>
		</view>
		<view class='loadingicon acea-row row-center-wrapper' v-if="loading">
			<text class='loading iconfont icon-jiazai' :hidden='loading==false'></text>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
		<Verify ref="verify" @success="successVerify" :captchaType="captchaType" :imgSize="{ width: '330px', height: '155px' }"
			></Verify>
	</view>
	<view class="settledSuccessMain" v-else-if='status == 0'>
		<view class="settledSuccessful">
			<image class="image" src="../static/success.png" alt="">
				<view class="title">{{$t(`Chúc mừng, bạn đã gửi thông tin thành công!`)}}</view>
				<view class="goHome" hover-class="none" @click="goHome">
					{{$t(`Về trang chủ`)}}
				</view>
		</view>
	</view>
	<view class="settledSuccessMain" v-else-if='status == 1'>
		<view class="settledSuccessful">
			<image class="image" src="../static/success.png" alt="">
				<view class="title">{{$t(`Chúc mừng, thông tin của bạn đã được duyệt!`)}}</view>
				<view class="goHome" hover-class="none" @click="goHome">
					{{$t(`Về trang chủ`)}}
				</view>
		</view>
	</view>
	<view class="settledSuccessMain" v-else-if='status == 2'>
		<view class="settledSuccessful">
			<image class="image" src="../static/error.png" alt="">
				<view class="title">{{$t(`Đơn đăng ký của bạn không được duyệt!`)}}</view>
				<view class="info" v-if="refusal_reason">{{refusal_reason}}</view>
				<view class="again" hover-class="none" @click="applyAgain">
					{{$t(`Đăng ký lại`)}}
				</view>
				<view class="goHome" hover-class="none" @click="goHome">
					{{$t(`Về trang chủ`)}}
				</view>
		</view>
		
	</view>
</template>
<script>
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		create,
		getCodeApi,
		registerVerify,
		getHistoryData,
		updateGoodsRecord,
		getAgentAgreement,
		userSpreadInfo,
		spreadCreateApi
	} from '@/api/store.js';
	import {
		getCaptcha
	} from "@/api/user";
	import {
		mapGetters
	} from "vuex";
	import parser from "@/components/jyf-parser/jyf-parser";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	import colors from "@/mixins/color";
	import Verify from '../components/verify/verify.vue';
	import sendVerifyCode from "@/mixins/SendVerifyCode";
	import { HTTP_REQUEST_URL } from '@/config/app';
	const app = getApp();
	export default {
		components: {
			Verify,
			"jyf-parser": parser,
			// #ifdef MP
			authorize,
			// #endif
		},
		mixins: [sendVerifyCode, colors],
		data() {
			return {
				isAgent: false,
				inloading: true,
				status: -1,
				isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
				isShowAuth: false, //Có ẩn ủy quyền hay không
				text: this.$t(`Lấy mã xác thực`),
				codeUrl: "",
				disabled: false,
				isAgree: false,
				showProtocol: false,
				isShowCode: false,
				loading: false,
				merchantData: {
					agent_name: "",
					name: "",
					phone: "",
					classification: '',
					division_invite: ''
				},
				form: {
					nickname: '',
					uid: '',
					phone: '',
					code: '',
					real_name: ''
				},
				validate: false,
				successful: false,
				keyCode: "",
				codeVal: "",
				protocol: app.globalData.sys_intention_agree,
				timer: "",
				index: 0,
				index1: 0,
				mer_classification: "",
				mer_storeType: '',
				images: [],
				tagStyle: {
					img: 'width:100%;display:block;',
					table: 'width:100%',
					video: 'width:100%'
				},
				mer_i_id: null, // id đăng ký đại lý
				isType: false,
				id: 0,
				refusal_reason: "",
				keyCode: '',
				type: 'agent'
			};
		},
		beforeDestroy() {
			clearTimeout(this.timer)
		},
		computed:{
			...mapGetters(['isLogin']),
			headerBg() {
				return HTTP_REQUEST_URL + `/statics/images/${this.isAgent? 'agent_apply.jpg' :'spread_apply.jpg'}`;
			}
		},
		onLoad(options) {
			if (options.id) {
				this.id = id
				uni.showLoading({
					title: this.$t(`Đang tải`),
				});
			}
			if (this.isLogin) {
				if(options.type) this.isAgent = true
				this.$nextTick(()=> {
					if(!this.isAgent){
						this.getInfo()
					} else {
						this.getHistoryData()
					}
				})
			} else {
				// #ifdef H5 || APP-PLUS
				toLogin();
				// #endif 
				// #ifdef MP
				this.isAuto = true;
				this.$set(this, 'isShowAuth', true)
				// #endif
			}
		
		},
		onShow() {

		},
		methods: {
			getAgentAgreement() {
				if(this.isAgent){
					getAgentAgreement().then(res => {
						this.isType = false;
						this.showProtocol = true;
						this.protocol = res.data.content
					})
				} else {
					this.isType = false;
					this.showProtocol = true;
				}
				
			},
			code() {
				let that = this
				let phone = this.isAgent ? this.merchantData.phone : this.form.phone
				if (!phone) return that.$util.Tips({
					title: that.$t(`Vui lòng điền số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(phone)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				this.$refs.verify.show()
			},
			successVerify(data) {
				this.$refs.verify.hide()
				getCodeApi()
					.then(res => {
						this.keyCode = res.data.key;
						this.getCode(data);
					})
					.catch(res => {
						this.$util.Tips({
							title: res
						});
					});
			},
			async getCode(data) {
				let that = this;
				await registerVerify({
						phone: this.isAgent ? that.merchantData.phone : this.form.phone,
						type: that.type,
						key: that.keyCode,
						captchaType: this.captchaType,
						captchaVerification: data.captchaVerification
					})
					.then(res => {
						this.sendCode()
						that.$util.Tips({
							title: res.msg
						});
					})
					.catch(res => {
						that.$util.Tips({
							title: res
						});
					});
			},
			// Lấy chi tiết dữ liệu đã gửi trước đó
			getHistoryData() {
				getHistoryData().then(res => {
					this.status = res.data.status
					let resData = res.data
					if (res.data.status !== -1) {
						let arr = Object.keys(this.merchantData)
						arr.map(item => {
							this.merchantData[item] = resData[item]
						})
						uni.hideLoading();
					}
					if (this.status === 2) {
						this.refusal_reason = resData.refusal_reason
					}
					this.inloading = false
				})
			},
			//Lấy tên danh mục đại lý
			getCategoryName(id, arr) {
				for (let i = 0; i < arr.length; i++) {
					if (arr[i].merchant_category_id === id) {
						return arr[i]['category_name']
					}
				}
			},
			// Xem trước hình ảnh
			// Lấy idx trong album
			getPhotoClickIdx(e) {
				let _this = this;
				let idx = e.currentTarget.dataset.index;
				_this.imgPreview(_this.images, idx);
			},
			// Xem trước hình ảnh
			imgPreview: function(list, idx) {
				// list: mảng url hình ảnh
				if (list && list.length > 0) {
					uni.previewImage({
						current: list[idx], //  Truyền Number thì bên H5 bị không tương thích 
						urls: list
					});
				}
			},
			// Callback ủy quyền
			onLoadFun: function() {
				this.isShowAuth = false;
			},
			// Đóng ủy quyền
			authColse: function(e) {
				this.isShowAuth = e
			},
			toggleTab(str) {
				this.$refs[str].show();
			},
			// Trang chủ
			goHome() {
				uni.switchTab({
					url: '/pages/index/index'
				});
			},
			applyAgain() {
				this.status = -1
			},
			/**
			 * Tải lên file
			 * 
			 */
			uploadpic: function() {
				let that = this;
				that.$util.uploadImageOne('upload/image', (res) => {
					this.images.push(res.data.url);
					that.$set(that, 'images', that.images);
				});

			},
			/**
			 * Xóa ảnh
			 * 
			 */
			DelPic: function(index) {
				let that = this,
					pic = this.images[index];
				that.images.splice(index, 1);
				that.$set(that, 'images', that.images);
			},

			getcaptcha() {
				let that = this
				getCaptcha().then(data => {
					that.codeUrl = data.data.captcha; //Đường dẫn ảnh
					that.codeVal = data.data.code; //Mã xác thực hình ảnh
					that.codeKey = data.data.key //Key mã xác thực (OTP) dạng ảnh
				})
				that.isShowCode = true;
			},
			sendCode() {
				if (this.disabled) return;
				this.disabled = true;
				let n = 60;
				this.text = n + "s";
				const run = setInterval(() => {
					n = n - 1;
					if (n < 0) {
						clearInterval(run);
					}
					this.text = n + "s";
					if (this.text < 0 + "s") {
						this.disabled = false;
						this.text = this.$t(`Gửi lại mã`);
					}
				}, 1000);
			},
			onConfirm(val) {
				this.region = val.checkArr[0] + '-' + val.checkArr[1] + '-' + val.checkArr[2];
			},
			ChangeIsAgree(e) {
				this.isAgree = !this.isAgree;
				this.validateBtn()
			},
			getInfo() {
				userSpreadInfo()
					.then((res) => {
						let data = res.data.user;
						this.id = data.id || 0;
						this.form.nickname = data.nickname || '';
						this.form.uid = data.uid || '';
						this.form.phone = data.phone || '';
						this.form.real_name = data.real_name || '';
						this.form.content = data.content || '';
						this.status = data.status;
						this.refusal_reason = data.refusal_reason;
						this.protocol = res.data.agreement.content
						this.inloading = false
					})
					.catch((err) => {
						return this.$util.Tips({
							title: err
						});
					}); 
			},
			formSpeadSubmit(){
				if(!this.isAgree) return that.$util.Tips({
					title: that.$t(`Vui lòng đọc và đồng ý với Thỏa thuận cộng tác viên`)
				});
				spreadCreateApi(this.id, this.form)
					.then((res) => {
						this.getInfo()
					})
					.catch((err) => {
						return this.$util.Tips({
							title: err
						});
					});
			},
			formSubmit: function(e) {
				let that = this;
				if (that.validateForm() && that.validate) {
					let requestData = {
						uid: this.$store.state.app.uid,
						phone: that.merchantData.phone,
						agent_name: that.merchantData.agent_name,
						name: that.merchantData.name,
						code: that.merchantData.code,
						division_invite: that.merchantData.division_invite,
						images: that.images,
						id: this.id
					}
					create(requestData).then(data => {
						if (data.status == 200) {
							this.timer = setTimeout(() => {
								that.getHistoryData()
							}, 1000)
						}

					}).catch(res => {
						that.$util.Tips({
							title: res
						});
					})
				}
			},
			validateBtn: function() {
				let that = this,
					value = that.merchantData;
				if (value.agent_name && value.name && value.phone && /^1(3|4|5|7|8|9|6)\d{9}$/i.test(value
						.phone) &&
					value.code && that.isAgree && value.classification) {
					if (!that.isShowCode) {
						that.validate = true;
					} else {
						if (that.codeVal) {
							that.validate = true;
						} else {
							that.validate = false;
						}
					}

				}
			},

			validateForm: function() {
				let that = this,
					value = that.merchantData;

				if (!value.agent_name) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập tên đại lý`)
				});
				if (!value.name) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập họ tên`)
				});
				if (!value.phone) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập số điện thoại`)
				});
				if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(value.phone)) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập đúng số điện thoại`)
				});
				if (!value.code) return that.$util.Tips({
					title: that.$t(`Nhập mã xác thực`)
				});
				if (that.isShowCode && !that.codeVal) return that.$util.Tips({
					title: that.$t(`Vui lòng nhập mã xác thực hình ảnh`)
				});
				if (!that.images.length) return that.$util.Tips({
					title: that.$t(`Vui lòng tải lên giấy phép kinh doanh`)
				});
				if (!that.isAgree) return that.$util.Tips({
					title: that.$t(`Vui lòng đánh dấu và đồng ý với thỏa thuận đăng ký`)
				});
				that.validate = true;
				return true;
			},
			jumpToList() {
				uni.navigateTo({
					url: "/pages/store/applicationRecord/index"
				})
			},

		}
	}
</script>

<style scoped lang="scss">
	.uni-input-placeholder {
		color: #B2B2B2;
	}

	.item-name {
		width: 190rpx;
	}

	.uni-list-cell {
		position: relative;

		.iconfont {
			font-size: 14px;
			color: #7a7a7a;
			position: absolute;
			right: 15px;
			top: 7rpx;
		}

		.icon-guanbi2 {
			right: 35px;
		}
	}

	.merchantsSettled {
		height: 100vh;
		overflow-y: scroll;
		background: linear-gradient(#fd3d1d 0%, #fd151b 100%);
		padding-bottom: 30px;
	}

	.merchantsSettled .merchantBg {
		width: 750rpx;
		width: 100%;
	}

	.merchantsSettled .list {
		background-color: #fff;
		border-radius: 12px;
		padding: 22px 0;
		margin: 0 15px;
		// position: absolute;
		// top: 300rpx;
		position: sticky;
		margin-top: -60px;
		width: calc(100% - 30px);
	}

	.application-record {
		position: absolute;
		display: flex;
		align-items: center;
		top: 240rpx;
		right: 0;
		color: #fff;
		font-size: 22rpx;
		background-color: rgba(0, 0, 0, 0.3);
		padding: 8rpx 18rpx;
		border-radius: 20px 0px 0px 20px;
	}

	.merchantsSettled .list .item {
		padding: 50rpx 0 20rpx;
		border-bottom: 1rpx solid #eee;
		position: relative;
		margin: 0 20px;
		.text-area{
			height: 200rpx;
			margin-top: 10rpx;
			font-size: 24rpx;
		}
		&.no-border {
			border-bottom: none;
			padding-left: 0;
			padding-right: 0;
		}

		.item-title {
			color: #666666;
			font-size: 28rpx;
			display: block;
		}

		.item-desc {
			color: #B2B2B2;
			font-size: 22rpx;
			display: block;
			margin-top: 9rpx;
			line-height: 36rpx;
		}
	}

	.acea-row,
	.upload {
		display: -webkit-box;
		display: -moz-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-lines: multiple;
		-moz-box-lines: multiple;
		-o-box-lines: multiple;
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
	}

	.upload {
		margin-top: 20rpx;
	}

	.acea-row.row-middle {
		-webkit-box-align: center;
		-moz-box-align: center;
		-o-box-align: center;
		-ms-flex-align: center;
		-webkit-align-items: center;
		align-items: center;
		padding-left: 2px;
	}

	.acea-row.row-column {
		-webkit-box-orient: vertical;
		-moz-box-orient: vertical;
		-o-box-orient: vertical;
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		flex-direction: column;
	}

	.acea-row.row-center-wrapper {
		-webkit-box-align: center;
		-moz-box-align: center;
		-o-box-align: center;
		-ms-flex-align: center;
		-webkit-align-items: center;
		align-items: center;
		-webkit-box-pack: center;
		-moz-box-pack: center;
		-o-box-pack: center;
		-ms-flex-pack: center;
		-webkit-justify-content: center;
		justify-content: center;
	}

	.merchantsSettled .list .item .pictrue {
		width: 130rpx;
		height: 130rpx;
		margin: 24rpx 22rpx 0 0;
		position: relative;
		font-size: 11px;
		color: #bbb;

		&:nth-child(4n) {
			margin-right: 0;
		}

		&:nth-last-child(1) {
			border: 0.5px solid #ddd;
			box-sizing: border-box;
		}


		uni-image,
		image {
			width: 100%;
			height: 100%;
			border-radius: 1px;

			img {
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-moz-user-select: none;
				display: block;
				position: absolute;
				top: 0;
				left: 0;
				opacity: 0;
				width: 100%;
				height: 100%;
			}
		}

		.icon-guanbi1 {
			font-size: 33rpx;
			position: absolute;
			top: -10px;
			right: -10px;
		}
	}

	.uni-list-cell-db {
		position: relative;
	}

	.wenhao {
		width: 34rpx;
		height: 34rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 28rpx;
		border-radius: 50%;
		background: #E3E3E3;
		color: #ffffff !important;
		margin-left: 4rpx;
		position: absolute;
		left: 122rpx;
	}

	.merchantsSettled .list .item .imageCode {
		position: absolute;
		top: 7px;
		right: 0;
	}

	.merchantsSettled .list .item .icon {
		font-size: 40rpx;
		color: #b4b1b4;
	}

	.merchantsSettled .list .item input {
		width: 400rpx;
		font-size: 28rpx;
		// margin-left: 30px;
	}

	.merchantsSettled .list .item .placeholder {
		color: #b2b2b2;
		font-size: 28rpx
	}

	.merchantsSettled .default {
		padding: 0 30rpx;
		height: 90rpx;
		background-color: #fff;
		margin-top: 23rpx;
	}

	.merchantsSettled .default checkbox {
		margin-right: 15rpx;
	}

	.merchantsSettled .acea-row uni-image {
		width: 20px;
		height: 20px;
		display: block;
	}

	.merchantsSettled .list .item .codeIput {
		width: 125px;
	}

	.uni-input-input {
		display: block;
		height: 100%;
		background: none;
		color: inherit;
		opacity: 1;
		-webkit-text-fill-color: currentcolor;
		font: inherit;
		line-height: inherit;
		letter-spacing: inherit;
		text-align: inherit;
		text-indent: inherit;
		text-transform: inherit;
		text-shadow: inherit;
	}

	.merchantsSettled .list .item .code {
		position: absolute;
		width: 93px;
		line-height: 27px;
		border: 1px solid #E93323;
		border-radius: 15px;
		color: #E93323;
		text-align: center;
		bottom: 8px;
		right: 0;
		font-size: 12px;
	}

	.merchantsSettled .list .item .code.on {
		background-color: #bbb;
		color: #fff;
		border-color: #bbb;
	}

	.merchantsSettled .submitBtn {
		width: 588rpx;
		margin: 0 auto;
		height: 86rpx;
		border-radius: 25px;
		text-align: center;
		line-height: 86rpx;
		font-size: 15px;
		color: #fff;
		background: #E3E3E3;
		margin-top: 25px;
	}

	.merchantsSettled .submitBtn.on {
		background: #E93323;
	}

	uni-checkbox-group,
	.settleAgree {
		display: inline-block;
		font-size: 24rpx;
	}

	uni-checkbox-group {
		color: #b2b2b2;
	}

	.settleAgree {
		color: #E93323;
		// position: relative;
		// top: 2px;
		// left: 8px;
	}

	.merchantsSettled uni-checkbox .uni-checkbox-wrapper {
		width: 30rpx;
		height: 30rpx;
		border: 2rpx solid #C3C3C3;
		border-radius: 15px;
	}

	.settlementAgreement {
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0;
		left: 0;
		background: rgba(0, 0, 0, .5);
		z-index: 10;
	}

	.settlementAgreement .setAgCount {
		background: #fff;
		width: 656rpx;
		height: 458px;
		position: absolute;
		top: 50%;
		left: 50%;
		border-radius: 12rpx;
		-webkit-border-radius: 12rpx;
		padding: 52rpx;
		-webkit-transform: translate(-50%, -50%);
		-moz-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);
		overflow: hidden;

		.content {
			height: 900rpx;
			overflow-y: scroll;

			/deep/ p {
				font-size: 13px;
				line-height: 22px;
			}

			/deep/ img {
				max-width: 100%;
			}
		}
	}

	.settlementAgreement .setAgCount .icon {
		font-size: 42rpx;
		color: #b4b1b4;
		position: absolute;
		top: 15rpx;
		right: 15rpx;

	}

	.settlementAgreement .setAgCount .title {
		color: #333;
		font-size: 32rpx;
		text-align: center;
		font-weight: bold;
	}

	.settlementAgreement .setAgCount .content {
		margin-top: 32rpx;
		color: #333;
		font-size: 26rpx;
		line-height: 22px;
		text-align: justify;
		text-justify: distribute-all-lines;
		height: 756rpx;
		overflow-y: scroll;
	}

	.settledSuccessMain {
		height: 100vh;
		display: flex;
		flex-direction: column;
		background: #fff;
	}

	.settledSuccessful {
		flex: 1;
		width: 100%;
		padding: 0 56px;
		height: auto;
		background: #fff;
		text-align: center;
	}

	.settledSuccessful .image {
		width: 189px;
		height: 157px;
		margin-top: 66px;
	}

	.settledSuccessful .title {
		color: #333333;
		font-size: 16px;
		font-weight: bold;
		margin-top: 35px;
	}

	.settledSuccessful .info {
		color: #999;
		font-size: 26rpx;
		margin-top: 18rpx;
	}

	.settledSuccessful .goHome {
		margin: 20px auto 0;
		line-height: 43px;
		color: #282828;
		font-size: 15px;
		border: 1px solid #B4B4B4;
		border-radius: 60px;
	}

	.settledSuccessful .again {
		margin: 30px auto 0;
		line-height: 43px;
		color: #fff;
		font-size: 15px;
		background-color: #E93323;
		border-radius: 60px;
	}

	/deep/ uni-checkbox .uni-checkbox-input {
		width: 15px;
		height: 15px;
		position: relative;
	}

	/deep/ uni-checkbox .uni-checkbox-input.uni-checkbox-input-checked:before {
		font-size: 14px;
	}

	/deep/ uni-checkbox .uni-checkbox-input-checked {
		background-color: #fd151b !important;
	}

	.loadingicon {
		height: 100vh;
		overflow: hidden;
		position: absolute;
		top: 0;
		left: 0;
	}

	.icon-xiangyou {
		font-size: 22rpx;
	}

	// #ifdef MP
	checkbox-group {
		display: inline-block;
	}

	// #endif
	.setAgCount {
		/deep/ table {
			border: 1rpx solid #DDD;
			border-bottom: none;
			border-right: none;
		}

		/deep/ td,
		th {
			padding: 5rpx 10rpx;
			border-bottom: 1rpx solid #DDD;
			border-right: 1rpx solid #DDD;
		}
	}
</style>
