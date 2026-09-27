<template>
	<view :class="'wf-page wf-page' + type">
		<!--    left    -->
		<view>
			<view id="left" v-if="leftList.length">
				<view v-for="(item, index) in leftList" :key="index" class="wf-item" @tap="itemTap(item)">
					<WaterfallsFlowItem :item="item" :isStore="isStore" :type="type" :recommend="recommend" :goDetail="goDetail" />
				</view>
			</view>
		</view>
		<!--    right    -->
		<view>
			<view id="right" v-if="rightList.length">
				<view v-for="(item, index) in rightList" :key="index" class="wf-item" @tap="itemTap(item)">
					<WaterfallsFlowItem :item="item" :isStore="isStore" :type="type" :recommend="recommend" :goDetail="goDetail" />
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import WaterfallsFlowItem from './WaterfallsFlowItem.vue';
export default {
	components: {
		WaterfallsFlowItem
	},
	props: {
		// Danh sách dạng thác nước (waterfall)
		wfList: {
			type: Array,
			require: true
		},
		updateNum: {
			type: Number,
			default: 10
		},
		type: {
			type: Number,
			default: 0
		},
		isStore: {
			type: [String, Number],
			default: '1'
		},
		recommend: {
			type: Boolean,
			default: false
		},
		goDetail: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			allList: [], // Toàn bộ danh sách
			leftList: [], // Danh sách bên trái
			rightList: [], // Danh sách bên phải
			mark: 0, // Đánh dấu danh sách
			boxHeight: [] // Index 0 và 1 lần lượt là chiều cao cột trái và cột phải
		};
	},
	watch: {
		// Theo dõi thay đổi dữ liệu danh sách
		wfList: {
			handler(nVal, oVal) {
				// Nếu dữ liệu rỗng hoặc dữ liệu danh sách mới ít hơn dữ liệu danh sách cũ (thường do kéo để làm mới hoặc đổi sắp xếp hoặc dùng bộ lọc), khởi tạo lại biến

				if (!this.wfList.length || (this.wfList.length === this.updateNum && this.wfList.length <= this.allList.length)) {
					this.allList = [];
					this.leftList = [];
					this.rightList = [];
					this.boxHeight = [];
					this.mark = 0;
				}

				// Nếu danh sách có giá trị, gọi phương thức waterfall

				if (this.wfList.length) {
					this.allList = this.wfList;
					this.leftList = [];
					this.rightList = [];
					this.boxHeight = [];
					this.allList.forEach((v, i) => {
						if (this.allList.length < 3 || (this.allList.length <= 7 && this.allList.length - i > 1) || (this.allList.length > 7 && this.allList.length - i > 2)) {
							if (i % 2) {
								this.rightList.push(v);
							} else {
								this.leftList.push(v);
							}
						}
					});
					if (this.allList.length < 3) {
						this.mark = this.allList.length + 1;
					} else if (this.allList.length <= 7) {
						this.mark = this.allList.length - 1;
					} else {
						this.mark = this.allList.length - 2;
					}
					if (this.mark < this.allList.length) {
						this.waterFall();
					}
				}
			},
			immediate: true,
			deep: true
		},
		mounted() {},

		// Theo dõi đánh dấu, khi đánh dấu thay đổi thì thực hiện sắp xếp item tiếp theo
		mark() {
			const len = this.allList.length;
			if (this.mark < len && this.mark !== 0 && this.boxHeight.length) {
				this.waterFall();
			}
		}
	},
	methods: {
		// Sắp xếp dạng thác nước (waterfall)
		waterFall() {
			const i = this.mark;
			if (i == 0) {
				// Khởi tạo, chèn bắt đầu từ bên trái
				this.leftList.push(this.allList[i]);
				// Cập nhật chiều cao danh sách bên trái
				this.getViewHeight(0);
			} else if (i == 1) {
				// Chèn item thứ hai, mặc định chèn bên phải
				this.rightList.push(this.allList[i]);
				// Cập nhật chiều cao danh sách bên phải
				this.getViewHeight(1);
			} else {
				// Dựa vào chiều cao danh sách trái phải để xác định item tiếp theo nên chèn vào bên nào
				if (!this.boxHeight.length) {
					this.rightList.length < this.leftList.length ? this.rightList.push(this.allList[i]) : this.leftList.push(this.allList[i]);
				} else {
					const leftOrRight = this.boxHeight[0] > this.boxHeight[1] ? 1 : 0;
					if (leftOrRight) {
						this.rightList.push(this.allList[i]);
					} else {
						this.leftList.push(this.allList[i]);
					}
				}
				// Cập nhật chiều cao danh sách sau khi chèn
				this.getViewHeight();
			}
		},
		// Lấy chiều cao danh sách
		getViewHeight() {
			// Dùng nextTick, đảm bảo sau khi trang cập nhật xong mới request chiều cao
			this.$nextTick(() => {
				setTimeout(() => {
					uni
						.createSelectorQuery()
						.in(this)
						.select('#right')
						.boundingClientRect((res) => {
							res ? (this.boxHeight[1] = res.height) : '';
							uni
								.createSelectorQuery()
								.in(this)
								.select('#left')
								.boundingClientRect((res) => {
									res ? (this.boxHeight[0] = res.height) : '';
									this.mark = this.mark + 1;
								})
								.exec();
						})
						.exec();
				}, 100);
			});
		},
		// Click item
		itemTap(item) {
			this.$emit('itemTap', item);
		},
		// Click item

		goShop(item) {
			this.$emit('goShop', item);
		}
	}
};
</script>

<style lang="scss" scoped>
$page-padding: 10px;
$grid-gap: 10px;

.wf-page {
	display: grid;
	grid-template-columns: 1fr 1fr;
	grid-gap: $grid-gap;
}
.wf-item {
	width: calc((100vw - 2 * #{$page-padding} - #{$grid-gap}) / 2);
	padding-bottom: $grid-gap;
}
.wf-page1 .wf-item {
	margin-top: 20rpx;
	background-color: #fff;
	border-radius: 20rpx;
	padding-bottom: 0;
}
</style>
