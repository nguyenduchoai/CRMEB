<template>
	<view class="aleart" v-if="aleartStatus" :style="colorStyle">
		<view class="icon-top">
			<text class="iconfont icon-fapiao2"
				:style="invoiceData.is_invoice?'background-color: var(--view-theme)':'background-color: #999'"></text>
			<view class="bill">
				{{invoiceData.is_invoice?$t(`Đã xuất hóa đơn`): $t(`Chưa xuất hóa đơn`)}}
			</view>
		</view>

		<view class="aleart-body">
			<view class="body-head">{{$t(`Thông tin hóa đơn`)}}</view>
			<view class="label">
				<view class="">
					{{$t(`Tiêu đề hóa đơn`)}}
				</view>
				<view class="label-value">
					{{invoiceData.name}}
				</view>
			</view>
			<view class="label">
				<view class="">
					{{$t(`Loại tiêu đề hóa đơn`)}}
				</view>
				<view class="label-value">
					{{invoiceData.header_type == 1?$t(`Cá nhân`):$t(`Doanh nghiệp`)}}
				</view>
			</view>
			<view class="label">
				<view class="">
					{{$t(`Loại hóa đơn`)}}
				</view>
				<view class="label-value">
					{{invoiceData.type==1?$t(`Hóa đơn điện tử thông thường`):$t(`Hóa đơn điện tử chuyên dụng`)}}
				</view>
			</view>
			<view class="label" v-if="invoiceData.duty_number">
				<view class="">
					{{$t(`Mã số thuế doanh nghiệp`)}}
				</view>
				<view class="label-value">
					{{invoiceData.duty_number}}
				</view>
			</view>

			<view class="body-head">{{$t(`Thông tin liên hệ`)}}</view>
			<view class="label">
				<view class="">
					{{$t(`Họ tên`)}}
				</view>
				<view class="label-value">
					{{invoiceData.name}}
				</view>
			</view>
			<view class="label">
				<view class="">
					{{$t(`Số điện thoại liên hệ`)}}
				</view>
				<view class="label-value">
					{{invoiceData.drawer_phone}}
				</view>
			</view>
			<view class="label">
				<view class="">
					{{$t(`Email liên hệ`)}}
				</view>
				<view class="label-value">
					{{invoiceData.email}}
				</view>
			</view>
			<view class="label">
				<view class="">
					{{$t(`Ghi chú hóa đơn`)}}
				</view>
				<view class="label-value">
					{{invoiceData.remark}}
				</view>
			</view>
		</view>
		<view class="btn" @click="close">
{{$t(`Xác nhận`)}}
		</view>
	</view>
</template>

<script>
	import colors from '@/mixins/color.js';
	export default ({
		data() {
			return {

			}
		},
		mixins: [colors],
		props: {
			aleartStatus: {
				type: Boolean,
				default: false
			},
			invoiceData: {
				type: Object,
				default: () => {}
			}
		},
		methods: {
			close() {
				this.$emit('close')
			},
		}
	})
</script>

<style lang="scss" scoped>
	.aleart {
		width: 80%;
		// height: 714rpx;
		position: fixed;
		left: 50%;
		transform: translateX(-50%);
		z-index: 9999;
		top: 45%;
		margin-top: -357rpx;
		background-color: #fff;
		padding: 30rpx;
		border-radius: 12rpx;
		background-image: -webkit-gradient(linear, //Biểu thị gradient là đường thẳng (linear), giá trị khác là radial
				50% 0, //Vị trí điểm bắt đầu của gradient dạng thẳng. Phía sau có thuộc tính background-size quy định kích thước nền, 30 X 15px  50% 0 đều nhân với chiều rộng/cao của phần tử cha. 
				0 100%, //Vị trí điểm kết thúc, tương tự như trên
				from(transparent), //Màu điểm bắt đầu
				color-stop(.5, transparent), //Một điểm giữa nào đó phải đạt màu này, biểu thị quá trình chuyển đổi. .5b biểu thị 50% tổng chiều dài phạm vi gradient này
				color-stop(.5, #999999), //Như trên
				to(#999999)), //Màu đoạn kết thúc
			//Một khối nền được chia thành hai phần 15x15 tạo thành.

			-webkit-gradient(linear, 50% 0, 100% 100%, from(transparent),
				color-stop(.5, transparent),
				color-stop(.5, #999999),
				to(#999999));
		background-size: 20rpx 10rpx;
		background-repeat: repeat-x;
		background-position: 0 100%;

		.icon-top {
			margin-left: calc(50% - 40rpx);
			margin-top: -40rpx;
			display: flex;
			flex-direction: column;
			align-items: center;
			border-radius: 50%;
			width: 100rpx;
			height: 100rpx;

			.icon-fapiao2 {
				text-align: center;
				border-radius: 50%;
				font-size: 80rpx;
				color: #fff;
				background-color: var(--view-theme);
				padding: 20rpx;
				border: 4rpx solid #fff;
				margin-top: -40rpx;
			}
			.bill {
				width: 172rpx;
				text-align: center;
			}
		}

		.title {
			font-size: 34rpx;
			color: var(--view-theme);
			font-weight: bold;
			text-align: center;
			padding-bottom: 10rpx;
			border-bottom: 1px solid var(--view-op-ten);
		}

		.aleart-body {
			display: flex;
			justify-content: center;
			flex-direction: column;
			padding: 60rpx 0;

			.body-head {
				font-size: 30rpx;
				font-weight: bold;
				padding-bottom: 10rpx;
				border-bottom: 1px solid #EEEEEE;
				margin: 10rpx 0;
			}

			.label {
				width: 100%;
				display: flex;
				justify-content: space-between;
				margin-bottom: 15rpx;
				color: #333333;
				font-size: 28rpx;

				.label-value {
					color: #666666;
				}
			}
		}

		.btn {
			width: 100%;
			padding: 15rpx 0;
			color: #fff;
			background: var(--view-theme);
			border-radius: 20px;
			text-align: center;
			margin-bottom: 30rpx;
		}
	}
</style>
