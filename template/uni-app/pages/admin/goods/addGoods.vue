<template>
	<view>
		<!-- <NavBar titleText="Thêm sản phẩm" textSize="34rpx" iconColor="#333333" textColor="#333333" isScrolling showBack></NavBar> -->
		<view class="p-20">
			<view class="w-full bg--w111-fff rd-16rpx pt-32 pr-30 pb-32 pl-30">
				<view class="fs-30 fw-500 lh-42rpx">Thông tin sản phẩm</view>
				<view class="mt-30 flex-between-center">
					<text class="fs-30 lh-42rpx">Tên sản phẩm</text>
					<text class="text-24 text--w111-666">{{setFormData.store_name.length}}/40</text>
				</view>
				<view class="w-full bg--w111-f5f5f5 rd-12rpx p-20 mt-12">
					<textarea v-model="setFormData.store_name"
					:maxlength="40"
					placeholder="Vui lòng điền tên sản phẩm" placeholder-class="text--w111-ccc"
					class="fs-30" auto-height />
				</view>
				<view class="mt-40">
					<text class="fs-30 lh-42rpx">Hình ảnh sản phẩm</text>
				</view>
				<view class="fs-22 text--w111-999 mt-12">Đề xuất: kích thước ảnh 750*750px, tải lên tối đa 9 ảnh</view>
				<view class="grid-column-4 grid-gap-8rpx mt-20">
					<view class="relative h-156" v-for="(item,index) in setFormData.slider_image" :key="index">
						<image :src="item" mode="aspectFill" class="w-full h-156 rd-12rpx"></image>
						<view class="abs-rt w-32 h-32 of0b21 flex-center fs-24" @click="DelPic(index)">
							<text class="iconfont icon-iconfontguanbi text--w111-fff fs-20"></text>
						</view>
					</view>
					<view class="h-156 flex-col flex-center upload bg--w111-f5f5f5 text--w111-999 rd-12rpx"
						@click="uploadPicture(9)" v-if="setFormData.slider_image.length < 9">
						<text class="iconfont icon-paizhao fs-40"></text>
						<text class="fs-24 lh-34rpx pt-8">Tải lên ảnh</text>
					</view>
				</view>
			</view>
			<view class="w-full bg--w111-fff rd-16rpx mt-22 px-30">
				<view class="h-106 flex-between-center bb-e" @click="selectCate">
					<text class="fs-30 lh-42rpx">Danh mục sản phẩm</text>
					<view class="flex-y-center">
						<text class="fs-30 text--w111-333 pr-12" v-if="setFormData.cate_id.length">Đã chọn</text>
						<text class="fs-30 text--w111-999 pr-12" v-else>Vui lòng chọn danh mục</text>
						<text class="iconfont icon-ic_rightarrow fs-36 text--w111-999"></text>
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e">
					<text class="fs-30 lh-42rpx">Đơn vị</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="text" maxlength="1" v-model="setFormData.unit_name"  placeholder="Vui lòng điền đơn vị sản phẩm" placeholder-class="text--w111-999 fs-30" class="fs-32 fs-30" />
					</view>
				</view>
			</view>
			<view class="w-full bg--w111-fff rd-16rpx mt-22 pt-32 pr-30 pl-30">
				<view class="fs-30 fw-500 lh-42rpx">Thiết lập quy cách</view>
				<view class="h-106 flex-between-center bb-e">
					<text class="fs-30 lh-42rpx">Giá bán</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="digit" v-model="setFormData.attr.price" placeholder="Vui lòng nhập giá bán" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e">
					<text class="fs-30 lh-42rpx">Giá vốn</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="digit" v-model="setFormData.attr.cost" placeholder="Vui lòng nhập giá vốn" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e">
					<text class="fs-30 lh-42rpx">Giá gốc</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="digit" v-model="setFormData.attr.ot_price"  placeholder="Vui lòng nhập giá gốc" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e">
					<text class="fs-30 lh-42rpx">Tồn kho</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="number" v-model="setFormData.attr.stock" placeholder="Vui lòng nhập tồn kho" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e" v-show="isMore">
					<text class="fs-30 lh-42rpx">Mã sản phẩm</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="number" v-model="setFormData.attr.bar_code" placeholder="Vui lòng nhập mã quy cách" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e" v-show="isMore">
					<text class="fs-30 lh-42rpx">Mã vạch</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="number" v-model="setFormData.attr.bar_code_number" placeholder="Vui lòng nhập mã vạch" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e" v-show="isMore">
					<text class="fs-30 lh-42rpx">Trọng lượng</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="number" v-model="setFormData.attr.weight" placeholder="Vui lòng nhập trọng lượng" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-between-center bb-e" v-show="isMore">
					<text class="fs-30 lh-42rpx">Thể tích</text>
					<view class="flex-1 flex justify-end text-right">
						<input type="number" v-model="setFormData.attr.volume" placeholder="Vui lòng nhập thể tích" placeholder-class=" text--w111-999" class="fs-32" />
					</view>
				</view>
				<view class="h-106 flex-center text--w111-666" @tap="toggleMore">
					<text class="fs-26">{{isMore ? 'Thu gọn' : 'Mở rộng'}}</text>
					<text class="iconfont fs-26" :class="isMore ? 'icon-xiangshang2' : 'icon-xiala3'"></text>
				</view>
			</view>
			<view class="w-full bg--w111-fff rd-16rpx mt-22 pt-32 pr-30 pl-30 pb-32">
				<view class="fs-30 lh-42rpx">Chi tiết sản phẩm</view>
				<view class="fs-22 text--w111-999 mt-12">Đề xuất: kích thước ảnh 750*750px, tải lên tối đa 10 ảnh</view>
				<view class="grid-column-4 grid-gap-8rpx mt-20">
					<view class="relative h-156" v-for="(item,index) in contentPicture" :key="index">
						<image :src="item" mode="aspectFill" class="w-full h-156 rd-12rpx"></image>
						<view class="abs-rt w-32 h-32 of0b21 flex-center fs-24" @click="DelPic(index,'content')">
							<text class="iconfont icon-iconfontguanbi text--w111-fff fs-20"></text>
						</view>
					</view>
					<view class="h-156 flex-col flex-center upload bg--w111-f5f5f5 text--w111-999 rd-12rpx"
						@click="uploadContentPicture(10)" v-if="contentPicture.length < 10">
						<text class="iconfont icon-paizhao fs-40"></text>
						<text class="fs-24 lh-34rpx pt-8">Tải lên ảnh</text>
					</view>
				</view>
			</view>
			<view class="w-full bg--w111-fff rd-16rpx mt-22 pt-32 pr-30 pl-30">
				<view class="fs-30 lh-42rpx fw-500">Cài đặt khác</view>
				<view class="h-106 flex-between-center">
					<view class="fs-30 lh-42rpx">Phương thức giao hàng</view>
					<view class="flex-y-center">
						<checkbox-group class="flex-y-center" @change="deliveryWayChange">
							<label class="ml-48" v-for="(val, i) in deliveryFreeList" :key="val.value">
								<view>
									<checkbox :value="val.value" :checked="setFormData.logistics.includes(val.value)" style="transform:scale(0.8)" />
									<text class="relative top-2">{{ val.name }}</text>
								</view>
							</label>
						</checkbox-group>
					</view>
				</view>
				<view class="h-106 flex-between-center" v-show="setFormData.logistics.includes('1')">
					<view class="fs-30 lh-42rpx">Cài đặt phí vận chuyển</view>
					<radio-group class="flex-y-center" @change="feightChange">
						<label class="flex-y-center fs-30">
							<view>
								<radio value="2" :checked="setFormData.freight == 2" />
							</view>
							<view>Phí vận chuyển cố định</view>
						</label>
						<label class="flex-y-center fs-30 ml-48">
							<view>
								<radio value="3" :checked="setFormData.freight == 3" />
							</view>
							<view>Mẫu phí vận chuyển</view>
						</label>
					</radio-group>
				</view>
				<view v-show="setFormData.logistics.includes('1')">
					<view class="h-106 flex-between-center" v-if="setFormData.freight == 2">
						<text class="fs-30 lh-42rpx">Phí vận chuyển cố định</text>
						<view class="flex-1 flex justify-end text-right">
							<input type="digit" v-model="setFormData.postage" placeholder="Vui lòng nhập số tiền" placeholder-class=" text--w111-999" class="fs-32" />
						</view>
					</view>
					<view class="h-106 flex-between-center"
						v-if="setFormData.freight == 3">
						<text class="fs-30 lh-42rpx">Mẫu phí vận chuyển</text>
						<view class="flex-y-center">
							<picker @change="bindPickerChange" :value="tempIndex" :range="templateList" range-key="name">
								<view class="fs-30">{{templateList[tempIndex].name || 'Vui lòng chọn'}}
								<text class="iconfont icon-ic_rightarrow"></text>
								</view>
							</picker>
						</view>
					</view>
				</view>
			</view>
			<view class="pb-safe">
				<view class="h-200"></view>
			</view>
			<view class="fixed-lb w-full pb-safe bg--w111-fff z-10">
			    <view class="footer-box flex-center">
			        <view class="w-690 h-88 flex-center bg-mer text--w111-fff fs-28 rd-44rpx" @tap="confirmSave">Gửi</view>
			    </view>
			</view>
		</view>
		<classify
		  ref="classify"
		  :type="1"
		  :visible="visibleClass"
		  @closeDrawer="()=>{visibleClass = false}"
		  @successChange="successChange"
		></classify>
		<canvas canvas-id="canvas" v-if="canvasStatus"
			:style="{width: canvasWidth + 'px', height: canvasHeight + 'px',position: 'absolute',left:'-100000px',top:'-100000px'}"></canvas>
	</view>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
import classify from "./components/classify/index.vue";
import { getTemplateOption, productCreate } from "@/api/admin.js"
export default {
	name: "addGoods",
	components: { NavBar, classify },
	data(){
		return {
			canvasWidth: "",
			canvasHeight: "",
			canvasStatus: false,
			setFormData: {
				image: '', //Ảnh chính
				attr: {
					price: "",
					cost: "",
					ot_price: "",
					stock: "",
					bar_code: "",
					bar_code_number: "",
					weight: "",
					volume: "",
					is_default_select: 0,
					is_show : 1,
					pic: ""
				},
				slider_image: [],
				store_name: '',
				cate_id: '',
				unit_name: '',
				spec_type: 0,
				logistics: ['1','2'],
				freight: 2,
				temp_id: 0,
				content: "",
				is_show: 0,
				postage: 0
			},
			deliveryFreeList: [
				{value: '1',name: 'Chuyển phát'},
				{value: '2',name: 'Nhận tại cửa hàng'},
			],
			contentPicture: [],
			isMore: false,
			visibleClass: false,
			templateList: [],
			tempIndex: 0
		}
	},
	onLoad(){
		this.getTemlate();
	},
	methods: {
		uploadPicture(){
			let that = this;
			this.canvasStatus = true
			that.$util.uploadImageChange({ count: 9, url: 'upload/image' }, function(res) {
				that.setFormData.slider_image.push(res.data.url);
				if(that.setFormData.slider_image.length >= 9) that.setFormData.slider_image.length = 9;
				that.setFormData.image = that.setFormData.slider_image[0];
				that.setFormData.attr.pic = that.setFormData.slider_image[0];
			}, (res) => {
				this.canvasStatus = false
			}, (res) => {
				this.canvasWidth = res.w
				this.canvasHeight = res.h
			});
		},
		uploadContentPicture(){
			let that = this;
			this.canvasStatus = true
			that.$util.uploadImageChange({ count: 9, url: 'upload/image' }, function(res) {
				that.contentPicture.push(res.data.url);
			}, (res) => {
				this.canvasStatus = false
			}, (res) => {
				this.canvasWidth = res.w
				this.canvasHeight = res.h
			});
		},
		/**
		 * Chuyển mảng liên kết ảnh thành HTML rich text
		 * @param {string[]} urls Mảng liên kết ảnh
		 * @param {object} opts Tùy chọn
		 * @param {string} opts.wrapTag Thẻ bao bọc, mặc định 'p'
		 * @param {string} opts.className img class, mặc định ''
		 * @param {string} opts.style Style nội tuyến, ví dụ 'max-width:100%;height:auto;'
		 * @param {string} opts.alt Văn bản alt mặc định
		 * @returns {string} Chuỗi HTML rich text
		 */
		buildEditorImageHtml(
		  urls = [],
		  { wrapTag = 'p', className = '', style = 'max-width:100%;height:auto;', alt = 'Hình ảnh' } = {}
		) {
		  const clean = (v) => String(v).replace(/[`'"]/g, '').trim(); // Loại bỏ dấu backtick/dấu nháy/khoảng trắng
		  const isUrl = (u) => /^https?:\/\/.+/i.test(u);              // Kiểm tra đơn giản http/https

		  return urls
		    .filter(Boolean)
		    .map(clean)
		    .filter(isUrl)
		    .map((url) => {
		      const cls = className ? ` class="${className}"` : '';
		      const sty = style ? ` style="${style}"` : '';
		      return `<${wrapTag}><img src="${url}" alt="${alt}"${cls}${sty} /></${wrapTag}>`;
		    })
		    .join('');
		},
		DelPic(index, type) {
			if(type){
				this.contentPicture.splice(index,1);
			}else{
				this.setFormData.slider_image.splice(index, 1);
			}
		},
		selectCate(){
			this.$refs.classify.category('', this.setFormData.cate_id.toString());
			setTimeout(()=> {
			  this.visibleClass = true;
			}, 200);
		},
		successChange(val) {
			this.setFormData.cate_id = val;
			this.visibleClass = false;
		},
		toggleMore(){
			this.isMore = !this.isMore;
		},
		// Chọn phương thức giao hàng
		deliveryWayChange(obj) {
			this.setFormData.logistics = obj.detail.value;
		},
		feightChange(val){
			this.setFormData.freight = val.detail.value;
		},
		getTemlate(){
			getTemplateOption().then(res=>{
				this.templateList = res.data;
			})
		},
		bindPickerChange(e){
			this.tempIndex = e.detail.value;
			this.setFormData.temp_id = this.templateList[this.tempIndex].id;
		},
		confirmSave(){
			if(!this.setFormData.store_name) return this.$util.Tips({title: 'Vui lòng nhập tên sản phẩm'});
			if(!this.setFormData.image) return this.$util.Tips({title: 'Vui lòng tải lên hình ảnh sản phẩm'});
			if(!this.setFormData.cate_id) return this.$util.Tips({title: 'Vui lòng chọn danh mục sản phẩm'});
			if(!this.setFormData.unit_name) return this.$util.Tips({title: 'Vui lòng điền đơn vị sản phẩm'});
			if(!this.setFormData.attr.price) return this.$util.Tips({title: 'Vui lòng điền giá bán sản phẩm'});
			if(!this.setFormData.attr.cost) return this.$util.Tips({title: 'Vui lòng điền giá vốn sản phẩm'});
			if(!this.setFormData.attr.ot_price) return this.$util.Tips({title: 'Vui lòng điền giá gốc sản phẩm'});
			if(!this.setFormData.attr.stock) return this.$util.Tips({title: 'Vui lòng điền tồn kho sản phẩm'});
			if(!this.setFormData.logistics.length) return this.$util.Tips({title: 'Vui lòng chọn phương thức giao hàng'});
			if(this.setFormData.freight == 3 && this.setFormData.temp_id == 0) return this.$util.Tips({title: 'Vui lòng chọn mẫu phí vận chuyển'});
			const html = this.buildEditorImageHtml(this.contentPicture);
			this.$set(this.setFormData,'content',html);
			productCreate(this.setFormData).then(res=>{
				uni.showToast({
					title: "Gửi thành công",
					icon: 'none'
				})
				uni.redirectTo({
					url:'/pages/admin/goods/index'
				})
			}).catch(err=>{
				uni.showToast({
					title: err,
					icon: 'none'
				})
			})
		}
	}
}
</script>

<style lang="scss">
::v-deep uni-radio .uni-radio-input.uni-radio-input-checked,
::v-deep radio .wx-radio-input.wx-radio-input-checked {
 border: 1px solid $primary-admin!important;
 background-color: $primary-admin!important;
}
::v-deep checkbox .uni-checkbox-input.uni-checkbox-input-checked,
::v-deep checkbox .wx-checkbox-input.wx-checkbox-input-checked {
  border: 1px solid $primary-admin!important;
  background-color: $primary-admin!important;
  color: #fff!important;
}
::v-deep uni-checkbox .uni-checkbox-input, wx-checkbox .wx-checkbox-input{
	border-radius: unset !important;
}
.grid-gap-8rpx {
	grid-gap: 8rpx;
	gap: 8rpx;
}
.of0b21{
	background-color: rgba(0,0,0,0.5);
	border-radius: 0 12rpx 0 12rpx;
}
.bb-e{
	border-bottom: 1rpx solid #eee;
}
.icon-a-ic_CompleteSelect{
	color: $primary-admin;
}
.bg-mer{
	background-color: $primary-admin;
}
.h-156{
	height: 156rpx;
}
.pt-8{
	padding-top: 8rpx;
}
.px-30{
	padding: 0 30rpx;
}
.footer-box {
    height: 126rpx;
    .w-690 {
        width: 690rpx;
    }
}
.ml-48{
	margin-left: 48rpx;
}
.top-2{
	top: 2rpx;
}

</style>
