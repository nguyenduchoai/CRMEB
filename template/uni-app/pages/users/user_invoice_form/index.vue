<template>
	<view :style="colorStyle">
		<form @submit="formSubmit">
			<view class="panel">
				<view class="acea-row row-middle">
					<view>{{$t(`Loại tiêu đề hóa đơn`)}}</view>
					<radio-group name="header_type" @change="changeTitleType">
						<label>
							<radio class="disabled" value="1" :checked="header_type === '1'" /><text>{{$t(`Cá nhân`)}}</text>
						</label>
						<label>
							<radio value="2" :checked="header_type === '2'" /><text>{{$t(`Doanh nghiệp`)}}</text>
						</label>
					</radio-group>
				</view>
				<view class="acea-row row-middle" v-if="basicConfigData.special_invoice_status === '1' && header_type === '2'">
					<view>{{$t(`Loại hóa đơn`)}}</view>
					<text class="w-fill" @click="callType">
						<text>{{ type === '2' ? $t(`Hóa đơn điện tử VAT chuyên dụng`) : $t(`Hóa đơn điện tử VAT thông thường`) }} </text>
						<text class="iconfont icon-xiangyou"></text>
					</text>

				</view>
				<view class="acea-row row-middle">
					<view>{{$t(`Tiêu đề hóa đơn`)}}</view>
					<input name="name" :value="name" :placeholder="header_type === '1' ? $t(`Họ tên cần xuất hóa đơn`) : $t(`Tên doanh nghiệp cần xuất hóa đơn`)" />
				</view>
				<view v-show="header_type === '2'" class="acea-row row-middle">
					<view>{{$t(`Mã số thuế`)}}</view>
					<input name="duty_number" :value="duty_number" :placeholder="$t(`Mã số thuế`)" />
				</view>
				<view class="acea-row row-middle">
					<view>{{$t(`Số điện thoại`)}}</view>
					<input name="drawer_phone" :value="drawer_phone" :placeholder="$t(`Số điện thoại của bạn`)" />
				</view>
				<view class="acea-row row-middle">
					<view>{{$t(`Email`)}}</view>
					<input name="email" :value="email" :placeholder="$t(`Email liên hệ của bạn`)" />
				</view>
			</view>
			<view v-show="type === '2'" class="panel">
				<view class="acea-row row-middle">
					<view>{{$t(`Ngân hàng mở tài khoản`)}}</view>
					<input name="bank" :value="bank" :placeholder="$t(`Ngân hàng mở tài khoản của bạn`)" />
				</view>
				<view class="acea-row row-middle">
					<view>{{$t(`Số tài khoản ngân hàng`)}}</view>
					<input name="card_number" :value="card_number" :placeholder="$t(`Số tài khoản ngân hàng của bạn`)" />
				</view>
				<view class="acea-row row-middle">
					<view>{{$t(`Địa chỉ doanh nghiệp`)}}</view>
					<input name="address" :value="address" :placeholder="$t(`Địa chỉ doanh nghiệp của bạn`)" />
				</view>
				<view class="acea-row row-middle">
					<view>{{$t(`Điện thoại doanh nghiệp`)}}</view>
					<input name="tell" :value="tell" :placeholder="$t(`Số điện thoại doanh nghiệp của bạn`)" />
				</view>
			</view>
			<checkbox-group class="acea-row row-middle panel" name="is_default">
				<label>
					<checkbox :checked="is_default.length !== 0" /><text>{{$t(`Đặt làm tiêu đề hóa đơn mặc định`)}}</text>
				</label>
			</checkbox-group>
			<view class="button-section">
				<button class="button" form-type="submit">{{$t(`Lưu`)}}</button>
				<navigator class="navigator" :url="backUrl" hover-class="none">{{$t(`Hủy`)}}</navigator>
			</view>
		</form>
		<view :class="{ mask: popupType }"></view>
		<view class="popup" :class="{ on: popupType }">
			<view class="title">{{$t(`Chọn loại hóa đơn`)}}<text class="iconfont icon-guanbi" @click="closeType"></text></view>
			<scroll-view scroll-y="true">
				<radio-group name="invoice-type" @change="changeType">
					<template v-for="item in invoiceTypeList">
						<label v-if="item.value === '1' || item.value === '2' && specialInvoice" :key="item.type"
							class="acea-row row-middle">
							<view class="text">
								<view>{{ $t(item.name) }}</view>
								<view class="info">{{ $t(item.info) }}</view>
							</view>
							<radio :value="item.value" :checked="item.value === type" />
						</label>
					</template>
				</radio-group>
			</scroll-view>
		</view>
		<!-- #ifndef MP -->
		<home></home>
		<!-- #endif -->
	</view>
</template>

<script>
	import home from '@/components/home';
	import {
		invoiceSave,
		invoiceDetail
	} from '@/api/user.js';
	import colors from '@/mixins/color.js';
	export default {
		components: {
			home
		},
		mixins: [colors],
		data() {
			return {
				invoiceTypeList: [{
						name: this.$t(`Hóa đơn điện tử VAT thông thường`),
						value: '1',
						info: this.$t(`Hóa đơn giấy sau khi xuất sẽ được gửi qua đường bưu điện`)
					},
					{
						name: this.$t(`Hóa đơn điện tử VAT chuyên dụng`),
						value: '2',
						info: this.$t(`Hóa đơn giấy sau khi xuất sẽ được gửi qua đường bưu điện`)
					}
				],
				id: '', // Là tham số bắt buộc khi sửa
				header_type: '1', // Loại thông tin xuất hóa đơn 1: cá nhân 2: doanh nghiệp
				type: '1', // Loại hóa đơn 1: thông thường, 2: chuyên dụng
				drawer_phone: '', // Số điện thoại người yêu cầu xuất hóa đơn
				name: '', // Tên (thông tin xuất hóa đơn)
				duty_number: '', // Mã số thuế (không bắt buộc với cá nhân, bắt buộc với doanh nghiệp)
				tell: '', // Số điện thoại đăng ký công ty
				address: '', // Địa chỉ đăng ký
				bank: '', // Ngân hàng mở tài khoản
				card_number: '', // Số thẻ ngân hàng
				is_default: [], // Đặt làm mặc định
				email: '', // Email
				popupType: false,
				typeName: '',
				urlQuery: '',
				from: '',
				specialInvoice: true,
				order_id: '',
				basicConfigData: uni.getStorageSync('BASIC_CONFIG') || ''
			};
		},
		computed: {
			backUrl() {
				switch (this.from) {
					case 'order_confirm':
						return `/pages/goods/order_confirm/index${this.urlQuery}`;
						break;
					default:
						return '/pages/users/user_invoice_list/index?from=invoice_form';
						break;
				}
			}
		},
		onHide() {
			this.from = ''
		},
		onLoad(options) {

			if (options.id) uni.setNavigationBarTitle({
				title: 'Sửa hóa đơn'
			})
			for (let key in options) {
				switch (key) {
					case 'couponTitle':
					case 'new':
					case 'cartId':
					case 'pinkId':
					case 'couponId':
					case 'addressId':
						this.urlQuery += `${this.urlQuery ? '&' : '?'}${key}=${options[key]}`;
						break;
					case 'from':
						this.from = options[key];
						break;
					case 'header_type':
						this.header_type = options[key];
						break;
					case 'id':
						this.id = options[key];
						this.getInvoiceDetail();
						break;
					case 'specialInvoice':
						if (options[key] === 'false') {
							this.specialInvoice = false;
						}
						break;
				}
			}
			if (options.order_id)
				this.order_id = options.order_id
			const invoiceItem = this.invoiceTypeList.find(item => item.value === this.type);
			this.typeName = invoiceItem.name;
		},
		methods: {
			// Lấy dữ liệu hóa đơn
			getInvoiceDetail() {
				uni.showLoading({
					title: this.$t(`Đang tải`)
				});
				invoiceDetail(this.id).then(res => {
					uni.hideLoading();
					this.header_type = res.data.header_type.toString();
					this.type = res.data.type.toString();
					const invoiceItem = this.invoiceTypeList.find(item => item.value === this.type);
					this.typeName = invoiceItem.name;
					this.name = res.data.name;
					this.drawer_phone = res.data.drawer_phone;
					this.email = res.data.email;
					this.duty_number = res.data.duty_number;
					this.bank = res.data.bank;
					this.card_number = res.data.card_number;
					this.address = res.data.address;
					this.tell = res.data.tell;
					this.is_default = res.data.is_default ? [''] : [];
				}).catch(err => {
					uni.showToast({
						title: err,
						icon: 'none'
					});
				});
			},
			// Lưu
			formSubmit(e) {
				let that = this;
				const formData = e.detail.value;
				formData.type = this.type;
				if (formData.header_type === '1') {
					if (!formData.name) {
						return uni.showToast({
							title: that.$t(`Vui lòng nhập họ tên cần xuất hóa đơn`),
							icon: 'none'
						});
					}
					if (!formData.drawer_phone) {
						return uni.showToast({
							title: that.$t(`Vui lòng nhập số điện thoại của bạn`),
							icon: 'none'
						});
					}
					if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(formData.drawer_phone)) {
						return uni.showToast({
							title: that.$t(`Vui lòng nhập đúng số điện thoại của bạn`),
							icon: 'none'
						});
					}
					if (!formData.email) {
						return uni.showToast({
							title: that.$t(`Vui lòng nhập email liên hệ của bạn`),
							icon: 'none'
						});
					}
					if (!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(formData.email)) {
						return uni.showToast({
							title: that.$t(`Vui lòng nhập đúng email liên hệ của bạn`),
							icon: 'none'
						});
					}
				}
				if (formData.header_type === '2') {
					if (formData.type === '1') {
						if (!formData.name) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập tên doanh nghiệp cần xuất hóa đơn`),
								icon: 'none'
							});
						}
						if (!formData.duty_number) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập mã số thuế`),
								icon: 'none'
							});
						}
						if (!/[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10}/.test(formData.duty_number)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng mã số thuế`),
								icon: 'none'
							});
						}
						if (!formData.drawer_phone) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập số điện thoại của bạn`),
								icon: 'none'
							});
						}
						if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(formData.drawer_phone)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng số điện thoại của bạn`),
								icon: 'none'
							});
						}
						if (!formData.email) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập email liên hệ của bạn`),
								icon: 'none'
							});
						}
						if (!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(formData.email)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng email liên hệ của bạn`),
								icon: 'none'
							});
						}
					}
					if (formData.type === '2') {
						if (!formData.name) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập tên doanh nghiệp cần xuất hóa đơn`),
								icon: 'none'
							});
						}
						if (!formData.duty_number) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập mã số thuế`),
								icon: 'none'
							});
						}
						if (!/[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10}/.test(formData.duty_number)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng mã số thuế`),
								icon: 'none'
							});
						}
						if (!formData.drawer_phone) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập số điện thoại của bạn`),
								icon: 'none'
							});
						}
						if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(formData.drawer_phone)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng số điện thoại của bạn`),
								icon: 'none'
							});
						}
						if (!formData.email) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập email liên hệ của bạn`),
								icon: 'none'
							});
						}
						if (!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(formData.email)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng email liên hệ của bạn`),
								icon: 'none'
							});
						}
						if (!formData.bank) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập ngân hàng mở tài khoản của bạn`),
								icon: 'none'
							});
						}
						if (!formData.card_number) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập số tài khoản ngân hàng của bạn`),
								icon: 'none'
							});
						}
						if (!/^\d{16}|\d{19}$/.test(formData.card_number)) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập đúng số tài khoản ngân hàng của bạn`),
								icon: 'none'
							});
						}
						if (!formData.address) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập địa chỉ doanh nghiệp của bạn`),
								icon: 'none'
							});
						}
						if (!formData.tell) {
							return uni.showToast({
								title: that.$t(`Vui lòng nhập số điện thoại doanh nghiệp của bạn`),
								icon: 'none'
							});
						}
					}
				}
				formData.is_default = formData.is_default.length;
				formData.id = this.id;

				uni.showLoading({
					title: that.$t(`Đang lưu`)
				});
				invoiceSave(formData).then(res => {
					uni.showToast({
						title: res.msg,
						icon: 'success',
					});
					setTimeout(e => {
						switch (that.from) {
							case 'order_confirm':
								if (that.id) {
									uni.navigateTo({
										url: `/pages/goods/order_confirm/index${that.urlQuery}&invoice_id=${that.id}&invoice_type=${formData.type}&header_type=${this.header_type}`
									})
								} else {
									uni.navigateTo({
										url: `/pages/goods/order_confirm/index${that.urlQuery}&invoice_id=${res.data.id}&invoice_type=${formData.type}&header_type=${this.header_type}`
									})
								}
								break;
							case 'order_details':
								if (that.id) {
									uni.navigateTo({
										url: `/pages/goods/order_details/index?order_id=${that.order_id}&invoice_id=${that.id}&header_type=${this.header_type}`
									})
								} else {
									uni.navigateTo({
										url: `/pages/goods/order_details/index?order_id=${that.order_id}&invoice_id=${res.data.id}&header_type=${this.header_type}`
									})
								}
								break;
							default:
								uni.navigateTo({
									url: '/pages/users/user_invoice_list/index?from=invoice_form'
								});
								break;
						}
					}, 1000)
				}).catch(err => {
					uni.showToast({
						title: err,
						icon: 'none'
					});
				});
			},
			// Mở popup chọn loại hóa đơn
			callType() {
				if (this.header_type == 2) {
					this.popupType = true;
				} else {
					uni.showToast({
						title: this.$t(`Cá nhân chỉ được xuất hóa đơn thông thường`),
						icon: 'none'
					});
				}
			},
			// Chọn loại hóa đơn
			changeType(e) {
				const type = e.detail.value,
					invoiceItem = this.invoiceTypeList.find(item => item.value === type);
				if (type === '2' && this.header_type === '1') {
					this.header_type = '2';
				}
				this.typeName = invoiceItem.name;
				this.type = type;
				this.popupType = false;
			},
			// Đóng popup hóa đơn
			closeType() {
				this.popupType = false;
			},
			// Chọn loại thông tin xuất hóa đơn
			changeTitleType(e) {
				this.header_type = e.detail.value;
				this.type = '1';
			}
		}
	}
</script>

<style scoped lang="scss">
	::v-deep.disabled .uni-radio-input {
		background-color: #F8F8F8;
	}

	form {
		font-size: 28rpx;
		color: #282828;
	}

	form input,
	form radio-group {
		flex: 1;
		margin-left: 30rpx;
		text-align: right;
	}

	form input {
		font-size: 26rpx;
	}

	form label {
		margin-right: 50rpx;
	}

	form radio {
		margin-right: 8rpx;
	}

	form checkbox-group {
		height: 90rpx;
	}

	form checkbox {
		margin-right: 20rpx;
	}

	.panel {
		padding-right: 30rpx;
		padding-left: 30rpx;
		background-color: #FFFFFF;

		.w-fill {
			margin-left: auto;
			font-size: 26rpx;
		}
	}

	.panel~.panel {
		margin-top: 14rpx;
	}

	.panel .acea-row {
		height: 90rpx;
	}

	.panel .acea-row~.acea-row {
		border-top: 1rpx solid #EEEEEE;
	}

	.input-placeholder {
		font-size: 26rpx;
		color: #BBBBBB;
	}

	.icon-xiangyou {
		margin-left: 5rpx;
		font-size: 26rpx;
		color: #BFBFBF;
		margin-top: 2rpx;
	}

	.popup {
		position: fixed;
		bottom: 0;
		left: 0;
		z-index: 99;
		width: 100%;
		padding-bottom: 100rpx;
		border-top-left-radius: 16rpx;
		border-top-right-radius: 16rpx;
		background-color: #F5F5F5;
		overflow: hidden;
		transform: translateY(100%);
		transition: 0.3s;
	}

	.popup.on {
		transform: translateY(0);
	}

	.popup .title {
		position: relative;
		height: 137rpx;
		font-size: 32rpx;
		line-height: 137rpx;
		text-align: center;
	}

	.popup scroll-view {
		height: 466rpx;
		padding-right: 30rpx;
		padding-left: 30rpx;
		box-sizing: border-box;
	}

	.popup label {
		padding: 35rpx 30rpx;
		border-radius: 16rpx;
		margin-bottom: 20rpx;
		background-color: #FFFFFF;
	}

	.popup .text {
		flex: 1;
		min-width: 0;
		font-size: 28rpx;
		color: #282828;
	}

	.popup .info {
		margin-top: 10rpx;
		font-size: 22rpx;
		color: #909090;
	}

	.popup .icon-guanbi {
		position: absolute;
		top: 50%;
		right: 30rpx;
		z-index: 2;
		transform: translateY(-50%);
		font-size: 30rpx;
		color: #707070;
	}

	.popup .text .acea-row {
		display: inline-flex;
		max-width: 100%;
	}

	.popup .name {
		flex: 1;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		font-size: 30rpx;
	}

	.popup .label {
		width: 56rpx;
		height: 28rpx;
		border: 1rpx solid #E93323;
		margin-left: 18rpx;
		font-size: 20rpx;
		line-height: 26rpx;
		text-align: center;
		color: #E93323;
	}

	.popup .type {
		width: 124rpx;
		height: 42rpx;
		margin-top: 14rpx;
		background-color: #FCF0E0;
		font-size: 24rpx;
		line-height: 42rpx;
		text-align: center;
		color: #D67300;
	}

	.popup .type.special {
		background-color: #FDE9E7;
		color: #E93323;
	}

	.button-section {
		/* position: fixed;
		bottom: 0;
		left: 0;
		width: 100%; */
		padding: 58rpx 30rpx;
	}

	.button-section .button {
		height: 86rpx;
		border-radius: 43rpx;
		background-color: var(--view-theme);
		font-size: 30rpx;
		line-height: 86rpx;
		color: #FFFFFF;
	}

	.button-section .navigator {
		height: 86rpx;
		border: 1rpx solid var(--view-theme);
		border-radius: 43rpx;
		margin-top: 26rpx;
		font-size: 30rpx;
		line-height: 86rpx;
		text-align: center;
		color: var(--view-theme);
	}
</style>