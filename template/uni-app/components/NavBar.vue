<template>
	<view class="navbar">
		<view class="content" :style="{ background: isScrolling ? '#fff' : bagColor,'padding-top': sysHeight + 'px' }">
			<view class="flex-center h-80 px-20">
				<view class="w-40 h-40 flex-center">
					<view
						v-show="showBack"
						@click="back"
						class="iconfont icon-fanhui"
						:style="{ color: `${iconColor}`, fontSize: `${iconSize}`, fontWeight: `${iconWeight}` }"
					></view>
				</view>
				<view class="flex-1 text-center"
				:style="{ color: `${textColor}`, fontSize: `${textSize}`, fontWeight: `${textWeight}` }"
				v-if="!custom">{{ titleText }}</view>
				<view class="flex-1" v-else>
					<slot></slot>
				</view>
				<view class="w-40 h-40 flex-center">
					<view v-show="showRight" class="right-icon"></view>
				</view>
			</view>
		</view>
		<view :style="{'padding-top': sysHeight + 'px'}" v-if="showEmpty">
			<view class="h-80"></view>
		</view>
	</view>
</template>

<script>
let sysHeight = uni.getWindowInfo().statusBarHeight;
export default {
	name: 'navbar',
	props: {
		custom:{
			type: Boolean,
			default: false
		},
		fixed: {
			type: Boolean,
			default: true
		},
		showEmpty:{
			type: Boolean,
			default: true
		},
		// Cuộn xuống phía dưới
		isScrolling: {
			type: Boolean,
			default: false
		},
		// Có hiển thị icon quay lại không
		showBack: {
			type: Boolean,
			default: false
		},
		// Title
		titleText: {
			type: String,
			default: ''
		},
		// Màu icon
		iconColor: {
			type: String,
			default: '#000000'
		},
		// Cỡ chữ icon
		iconSize: {
			type: String,
			default: '40rpx'
		},
		// Độ đậm icon
		iconWeight: {
			type: String,
			default: 'bold'
		},
		// Màu Title
		textColor: {
			type: String,
			default: '#333'
		},
		// Cỡ chữ Title
		textSize: {
			type: String,
			default: '34rpx'
		},
		// Độ đậm chữ Title
		textWeight: {
			type: String,
			default: '500'
		},
		// Màu nền
		bagColor: {
			type: String,
			default: 'transparent'
		},
		showRight:{
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			sysHeight:sysHeight,
		};
	},
	methods: {
		back() {
			let pages = getCurrentPages(); // Lấy số route của các trang đã mở hiện tại,
			if (pages.length > 1) {
				uni.navigateBack()
			} else {
				uni.switchTab({
					url: '/pages/index/index'
				});
			}
		}
	}
};
</script>

<style lang="scss">
.navbar {
	position: relative;
	color: #333;
	.content {
		position: fixed;
		top: 0;
		right: 0;
		left: 0;
		z-index: 998;
		background-color: var(--view-theme);
		font-weight: 500;
		font-size: 34rpx;
		color: #ffffff;
	}
}
</style>
