<template>
	<view :style="colorStyle">
		<view class="shoppingCart copy-data" v-if="canShow">
			<view class="labelNav acea-row row-around row-middle">
				<view class="item">
					<text class="iconfont icon-xuanzhong"></text>
					{{ $t(`Cam kết 100% chính hãng`) }}
				</view>
				<view class="item">
					<text class="iconfont icon-xuanzhong"></text>
					{{ $t(`Mọi sản phẩm đều được tuyển chọn kỹ lưỡng`) }}
				</view>
				<view class="item">
					<text class="iconfont icon-xuanzhong"></text>
					{{ $t(`Hậu mãi an tâm`) }}
				</view>
			</view>
			<view class="nav acea-row row-between-wrapper">
				<view>
					{{ $t(`Số lượng mua`) }}
					<text class="num font-num">{{ cartCount }}</text>
				</view>
				<view v-if="cartList.valid.length > 0 || cartList.invalid.length > 0" class="administrate acea-row row-center-wrapper" @click="manage">
					{{ footerswitch ? $t(`Quản lý`) : $t(`Hủy`) }}
				</view>
			</view>
			<view v-if="(cartList.valid.length > 0 || cartList.invalid.length > 0) && canShow">
				<view class="list">
					<checkbox-group @change="checkboxChange">
						<block v-for="(item, index) in cartList.valid" :key="index">
							<view class="item acea-row row-between-wrapper">
								<!-- #ifndef MP -->
								<checkbox :value="item.id.toString()" :checked="item.checked" :disabled="!item.attrStatus && footerswitch" />
								<!-- <checkbox :value="(item.id).toString()" :checked="item.checked" :disabled="item.attrStatus?false:true" /> -->
								<!-- #endif -->
								<!-- #ifdef MP -->
								<checkbox :value="item.id" :checked="item.checked" :disabled="!item.attrStatus && footerswitch" />
								<!-- #endif -->
								<navigator :url="'/pages/goods_details/index?id=' + item.product_id" hover-class="none" class="picTxt acea-row row-between-wrapper">
									<view class="pictrue">
										<image v-if="item.productInfo.attrInfo" :src="item.productInfo.attrInfo.image"></image>
										<image v-else :src="item.productInfo.image"></image>
									</view>
									<view class="text">
										<view class="line2" :class="item.attrStatus ? '' : 'reColor'">
											{{ item.productInfo.store_name }}
										</view>
										<view class="infor line1" v-if="item.productInfo.attrInfo">{{ $t(`Thuộc tính`) }}：{{ item.productInfo.attrInfo.suk }}</view>
										<view class="money" v-if="item.attrStatus">{{ $t(`￥`) }}{{ item.truePrice }}</view>
										<view class="reElection acea-row row-between-wrapper" v-else>
											<view class="title">{{ $t(`Vui lòng chọn lại phân loại sản phẩm`) }}</view>
											<view class="reBnt cart-color acea-row row-center-wrapper" @click.stop="reElection(item)">{{ $t(`Chọn lại`) }}</view>
										</view>
									</view>
									<view class="carnum acea-row row-center-wrapper" v-if="item.attrStatus">
										<view class="reduce" :class="item.numSub && !disabledChangeNumber ? 'on' : ''" @click.stop="subCart(index)">-</view>
										<!-- <view class='num'>{{item.cart_num}}</view> -->
										<view class="num">
											<input type="number" v-model="item.cart_num" @click.stop @input="iptCartNum(index)" @blur="blurInput(index)" />
										</view>
										<view class="plus" :class="item.numAdd && !disabledChangeNumber ? 'on' : ''" @click.stop="addCart(index)">+</view>
									</view>
								</navigator>
							</view>
						</block>
					</checkbox-group>
				</view>
				<view class="invalidGoods" v-if="cartList.invalid.length > 0">
					<view class="goodsNav acea-row row-between-wrapper">
						<view @click="goodsOpen">
							<text class="iconfont" :class="goodsHidden == true ? 'icon-xiangxia' : 'icon-xiangshang'"></text>
							{{ $t(`Sản phẩm không còn hiệu lực`) }}
						</view>
						<view class="del" @click="unsetCart">
							<text class="iconfont icon-shanchu1"></text>
							{{ $t(`Xóa hết`) }}
						</view>
					</view>
					<view class="goodsList" :hidden="goodsHidden">
						<block v-for="(item, index) in cartList.invalid" :key="index">
							<view class="item acea-row row-between-wrapper">
								<view class="invalid">{{ $t(`Hết hiệu lực`) }}</view>
								<view class="pictrue">
									<image v-if="item.productInfo.attrInfo" :src="item.productInfo.attrInfo.image"></image>
									<image v-else :src="item.productInfo.image"></image>
								</view>
								<view class="text acea-row row-column-between">
									<view class="line1 name">{{ item.productInfo.store_name }}</view>
									<view class="infor line1" v-if="item.productInfo.attrInfo">{{ $t(`Thuộc tính`) }}：{{ item.productInfo.attrInfo.suk }}</view>
									<view class="acea-row row-between-wrapper">
										<!-- <view>￥{{item.truePrice}}</view> -->
										<view class="end">{{ $t(`Sản phẩm này không còn hiệu lực`) }}</view>
									</view>
								</view>
							</view>
						</block>
					</view>
				</view>
				<!-- <view class='loadingicon acea-row row-center-wrapper' v-if="cartList.valid.length&&!loadend">
					<text class='loading iconfont icon-jiazai' :hidden='loading==false'></text>{{loadTitle}}
				</view> -->
				<view class="loadingicon acea-row row-center-wrapper" v-if="cartList.invalid.length && loadend">
					<text class="loading iconfont icon-jiazai" :hidden="loadingInvalid == false"></text>
					{{ loadTitleInvalid }}
				</view>
			</view>
			<view class="noCart" v-if="cartList.valid.length == 0 && cartList.invalid.length == 0 && canShow">
				<view class="emptyBox">
					<image :src="imgHost + '/statics/images/no-thing.png'"></image>
					<view class="tips">{{ $t(`Chưa có sản phẩm`) }}</view>
				</view>
				<recommend v-if="hostProduct.length" :hostProduct="hostProduct"></recommend>
			</view>
			<view style="height: 120rpx; color: #f5f5f5"></view>
			<view class="footer acea-row row-between-wrapper" v-if="cartList.valid.length > 0 && canShow" :class="is_diy && is_diy_set ? 'on' : ''">
				<view>
					<checkbox-group @change="checkboxAllChange">
						<checkbox value="all" :checked="!!isAllSelect" />
						<text class="checkAll">{{ $t(`Chọn tất cả`) }}({{ selectValue.length }})</text>
					</checkbox-group>
				</view>
				<view class="money acea-row row-middle" v-if="footerswitch == true">
					<text class="font-color">{{ $t(`￥`) }}{{ selectCountPrice }}</text>
					<form @submit="subOrder">
						<button class="placeOrder bg-color" formType="submit">{{ $t(`Đặt hàng ngay`) }}</button>
					</form>
				</view>
				<view class="button acea-row row-middle" v-else>
					<form @submit="subCollect">
						<button class="bnt" formType="submit">{{ $t(`Yêu thích`) }}</button>
					</form>
					<form @submit="subDel">
						<button class="bnt cart-color" formType="submit">{{ $t(`Xóa`) }}</button>
					</form>
				</view>
			</view>
		</view>
		<productWindow
			:attr="attr"
			:isShow="1"
			:iSplus="1"
			:iScart="1"
			@myevent="onMyEvent"
			@ChangeAttr="ChangeAttr"
			@ChangeCartNum="ChangeCartNum"
			@attrVal="attrVal"
			@iptCartNum="iptCartNum"
			@goCat="reGoCat"
			id="product-window"
		></productWindow>
		<!-- #ifdef MP -->
		<!-- <authorize :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
		<!-- #endif -->
		<!-- <view class="uni-p-b-96"></view> -->
		<view class="uni-p-b-98"></view>
		<pageFooter @newDataStatus="newDataStatus"></pageFooter>
	</view>
</template>

<script>
// #ifdef APP-PLUS
let sysHeight = uni.getSystemInfoSync().statusBarHeight + 'px';
// #endif
// #ifndef APP-PLUS
let sysHeight = 0;
// #endif
import { getCartList, getCartCounts, changeCartNum, cartDel, getResetCart } from '@/api/order.js';
import { getProductHot, collectAll, getProductDetail } from '@/api/store.js';
import { toLogin } from '@/libs/login.js';
import { mapGetters } from 'vuex';
import recommend from '@/components/recommend';
import productWindow from '@/components/productWindow';
// #ifdef MP
import authorize from '@/components/Authorize';
// #endif
import pageFooter from '@/components/pageFooter/index.vue'
import colors from '@/mixins/color';
import { HTTP_REQUEST_URL, DEBOUNCETIME } from '@/config/app';
import { Throttle } from '@/utils/validate.js';

export default {
	components: {
		pageFooter,
		recommend,
		productWindow,
		// #ifdef MP
		authorize
		// #endif
	},
	mixins: [colors],
	data() {
		return {
			imgHost: HTTP_REQUEST_URL,
			is_diy: uni.getStorageSync('is_diy'),
			canShow: false,
			cartCount: 0,
			goodsHidden: true,
			footerswitch: true,
			hostProduct: [],
			cartList: {
				valid: [],
				invalid: []
			},
			isAllSelect: false, //Chọn tất cả
			selectValue: [], //Dữ liệu đã chọn
			selectCountPrice: 0.0,
			isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
			isShowAuth: false, //Có ẩn ủy quyền hay không
			hotScroll: false,
			hotPage: 1,
			hotLimit: 10,
			loading: false,
			loadend: false,
			loadTitle: this.$t(`Đã đến cuối danh sách`), //Câu thông báo
			page: 1,
			limit: 20,
			loadingInvalid: false,
			loadendInvalid: false,
			loadTitleInvalid: this.$t(`Tải thêm`), //Câu thông báo
			pageInvalid: 1,
			limitInvalid: 20,
			attr: {
				cartAttr: false,
				productAttr: [],
				productSelect: {}
			},
			productValue: [], //Thuộc tính hệ thống
			storeInfo: {},
			attrValue: '', //Thuộc tính đã chọn
			attrTxt: this.$t(`Vui lòng chọn`), //Gợi ý trên trang thuộc tính
			cartId: 0,
			product_id: 0,
			sysHeight: sysHeight,
			newData: {},
			activeRouter: '',
			is_diy_set: false,
			adding: false,
			disabledChangeNumber: false,
			isFooter: false,
			pdHeight:0 //Tổng margin trên dưới tùy chỉnh của thanh điều hướng dưới
		};
	},
	computed: mapGetters(['isLogin']),
	onLoad(options) {
		uni.hideTabBar();
		let that = this;
		let routes = getCurrentPages(); // Lấy mảng route các trang đã từng mở
		let curRoute = routes[routes.length - 1].route; //Lấy route trang hiện tại
		this.activeRouter = '/' + curRoute;
	},
	onShow() {
		// #ifndef MP
		if (!this.isLogin) toLogin();
		// #endif

		this.canShow = false;
		if (this.isLogin == true) {
			this.hotPage = 1;
			this.hostProduct = [];
			this.hotScroll = false;
			this.getHostProduct();
			this.loadend = false;
			this.page = 1;
			this.cartList.valid = [];
			this.getCartList(1);
			this.loadendInvalid = false;
			this.pageInvalid = 1;
			this.cartList.invalid = [];
			this.getInvalidList();
			// this.getCartNum();
			this.goodsHidden = true;
			this.footerswitch = true;
			this.hostProduct = [];
			this.hotScroll = false;
			this.hotPage = 1;
			this.hotLimit = 10;
			(this.cartList = {
				valid: [],
				invalid: []
			}),
				(this.isAllSelect = false); //Chọn tất cả
			this.selectValue = []; //Dữ liệu đã chọn
			this.selectCountPrice = 0.0;
			this.cartCount = 0;
			this.isShowAuth = false;
		} else {
			// #ifdef MP
			this.hotPage = 1;
			this.hostProduct = [];
			this.hotScroll = false;
			this.getHostProduct();
			this.loading = false;
			this.canShow = true;
			// #endif
		}
	},
	methods: {
		// Đóng ủy quyền
		authColse: function (e) {
			this.isShowAuth = e;
		},
		newDataStatus(val,num) {
			this.isFooter = val ? true : false;
			this.pdHeight = num;
		},
		// Sửa giỏ hàng
		reGoCat: function () {
			let that = this,
				productSelect = that.productValue[this.attrValue];
			//Nếu có thuộc tính mà chưa chọn, nhắc người dùng chọn
			if (that.attr.productAttr.length && productSelect === undefined)
				return that.$util.Tips({
					title: that.$t(`Sản phẩm không đủ tồn kho, vui lòng chọn sản phẩm khác`)
				});

			let q = {
				id: that.cartId,
				product_id: that.product_id,
				num: that.attr.productSelect.cart_num,
				unique: that.attr.productSelect !== undefined ? that.attr.productSelect.unique : ''
			};
			getResetCart(q)
				.then(function (res) {
					that.attr.cartAttr = false;
					that.$util.Tips({
						title: that.$t(`Đã thêm vào giỏ hàng`),
						success: () => {
							that.loadend = false;
							that.page = 1;
							that.cartList.valid = [];
							that.getCartList();
							that.getCartNum();
						}
					});
				})
				.catch((res) => {
					return that.$util.Tips({
						title: res.msg
					});
				});
		},
		onMyEvent: function () {
			this.$set(this.attr, 'cartAttr', false);
		},
		reElection: function (item) {
			this.getGoodsDetails(item);
		},
		/**
		 * Lấy chi tiết sản phẩm
		 *
		 */
		getGoodsDetails: function (item) {
			uni.showLoading({
				title: this.$t(`Đang tải`),
				mask: true
			});
			let that = this;
			that.cartId = item.id;
			that.product_id = item.product_id;
			getProductDetail(item.product_id)
				.then((res) => {
					uni.hideLoading();
					that.attr.cartAttr = true;
					let storeInfo = res.data.storeInfo;
					that.$set(that, 'storeInfo', storeInfo);
					that.$set(that.attr, 'productAttr', res.data.productAttr);
					that.$set(that, 'productValue', res.data.productValue);
					that.DefaultSelect();
				})
				.catch((err) => {
					uni.hideLoading();
				});
		},
		/**
		 * Gán giá trị khi thuộc tính thay đổi
		 *
		 */
		ChangeAttr: function (res) {
			let productSelect = this.productValue[res];
			if (productSelect && productSelect.stock > 0) {
				this.$set(this.attr.productSelect, 'image', productSelect.image);
				this.$set(this.attr.productSelect, 'price', productSelect.price);
				this.$set(this.attr.productSelect, 'stock', productSelect.stock);
				this.$set(this.attr.productSelect, 'unique', productSelect.unique);
				this.$set(this.attr.productSelect, 'cart_num', 1);
				this.$set(this, 'attrValue', res);
				this.$set(this, 'attrTxt', this.$t(`Đã chọn`));
			} else {
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', 0);
				this.$set(this.attr.productSelect, 'unique', '');
				this.$set(this.attr.productSelect, 'cart_num', 0);
				this.$set(this, 'attrValue', '');
				this.$set(this, 'attrTxt', this.$t(`Vui lòng chọn`));
			}
		},
		/**
		 * Thuộc tính được chọn mặc định
		 *
		 */
		DefaultSelect: function () {
			let productAttr = this.attr.productAttr;
			let value = [];
			for (var key in this.productValue) {
				if (this.productValue[key].stock > 0) {
					value = this.attr.productAttr.length ? key.split(',') : [];
					break;
				}
			}
			for (let i = 0; i < productAttr.length; i++) {
				this.$set(productAttr[i], 'index', value[i]);
			}
			//sort(); hàm sắp xếp: số - chữ Anh - chữ Hán;
			let productSelect = this.productValue[value.sort().join(',')];
			if (productSelect && productAttr.length) {
				this.$set(this.attr.productSelect, 'store_name', this.storeInfo.store_name);
				this.$set(this.attr.productSelect, 'image', productSelect.image);
				this.$set(this.attr.productSelect, 'price', productSelect.price);
				this.$set(this.attr.productSelect, 'stock', productSelect.stock);
				this.$set(this.attr.productSelect, 'unique', productSelect.unique);
				this.$set(this.attr.productSelect, 'cart_num', 1);
				this.$set(this, 'attrValue', value.sort().join(','));
				this.$set(this, 'attrTxt', this.$t(`Đã chọn`));
			} else if (!productSelect && productAttr.length) {
				this.$set(this.attr.productSelect, 'store_name', this.storeInfo.store_name);
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', 0);
				this.$set(this.attr.productSelect, 'unique', '');
				this.$set(this.attr.productSelect, 'cart_num', 0);
				this.$set(this, 'attrValue', '');
				this.$set(this, 'attrTxt', this.$t(`Vui lòng chọn`));
			} else if (!productSelect && !productAttr.length) {
				this.$set(this.attr.productSelect, 'store_name', this.storeInfo.store_name);
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', this.storeInfo.stock);
				this.$set(this.attr.productSelect, 'unique', this.storeInfo.unique || '');
				this.$set(this.attr.productSelect, 'cart_num', 1);
				this.$set(this, 'attrValue', '');
				this.$set(this, 'attrTxt', this.$t(`Vui lòng chọn`));
			}
		},
		attrVal(val) {
			this.$set(this.attr.productAttr[val.indexw], 'index', this.attr.productAttr[val.indexw].attr_values[val.indexn]);
		},
		/**
		 * Tăng và giảm số lượng giỏ hàng
		 *
		 */
		ChangeCartNum: function (changeValue) {
			//changeValue: có tăng hay giảm
			//Lấy thuộc tính vừa thay đổi hiện tại
			let productSelect = this.productValue[this.attrValue];
			//Nếu không có thuộc tính, gán bằng tồn kho mặc định của sản phẩm
			if (productSelect === undefined && !this.attr.productAttr.length) productSelect = this.attr.productSelect;
			//Không có giá trị thuộc tính thì tồn kho là 0; không có tăng giảm;
			if (productSelect === undefined) return;
			let stock = productSelect.stock || 0;
			let num = this.attr.productSelect;
			if (changeValue) {
				num.cart_num++;
				if (num.cart_num > stock) {
					this.$set(this.attr.productSelect, 'cart_num', stock ? stock : 1);
					this.$set(this, 'cart_num', stock ? stock : 1);
				}
			} else {
				num.cart_num--;
				if (num.cart_num < 1) {
					this.$set(this.attr.productSelect, 'cart_num', 1);
					this.$set(this, 'cart_num', 1);
				}
			}
		},
		/**
		 * Điền thủ công vào giỏ hàng
		 *
		 */
		iptCartNum: function (e) {
			this.$set(this.attr.productSelect, 'cart_num', e);
		},
		subDel: function (event) {
			let that = this,
				selectValue = that.selectValue;
			if (selectValue.length > 0)
				cartDel(selectValue).then((res) => {
					that.loadend = false;
					that.page = 1;
					that.cartList.valid = [];
					that.getCartList();
					that.getCartNum();
				});
			else
				return that.$util.Tips({
					title: that.$t(`Vui lòng chọn sản phẩm`)
				});
		},
		getSelectValueProductId: function () {
			let that = this;
			let validList = that.cartList.valid;
			let selectValue = that.selectValue;
			let productId = [];
			if (selectValue.length > 0) {
				for (let index in validList) {
					if (that.inArray(validList[index].id, selectValue)) {
						productId.push(validList[index].product_id);
					}
				}
			}
			return productId;
		},
		subCollect: function (event) {
			let that = this,
				selectValue = that.selectValue;
			if (selectValue.length > 0) {
				let selectValueProductId = that.getSelectValueProductId();
				collectAll(that.getSelectValueProductId().join(','))
					.then((res) => {
						return that.$util.Tips({
							title: res.msg,
							icon: 'success'
						});
					})
					.catch((err) => {
						return that.$util.Tips({
							title: err
						});
					});
			} else {
				return that.$util.Tips({
					title: that.$t(`Vui lòng chọn sản phẩm`)
				});
			}
		},
		subOrder(event) {
			let that = this,
				selectValue = that.selectValue;
			if (selectValue.length > 0) {
				uni.navigateTo({
					url: '/pages/goods/order_confirm/index?cartId=' + selectValue.join(',')
				});
			} else {
				return that.$util.Tips({
					title: that.$t(`Vui lòng chọn sản phẩm`)
				});
			}
		},
		checkboxAllChange: function (event) {
			let value = event.detail.value;
			if (value.length > 0) {
				this.setAllSelectValue(1);
			} else {
				this.setAllSelectValue(0);
			}
		},
		setAllSelectValue: function (status) {
			let that = this;
			let selectValue = [];
			let valid = that.cartList.valid;
			if (valid.length > 0) {
				let newValid = valid.map((item) => {
					if (status) {
						if (that.footerswitch) {
							if (item.attrStatus) {
								item.checked = true;
								selectValue.push(item.id);
							} else {
								item.checked = false;
							}
						} else {
							item.checked = true;
							selectValue.push(item.id);
						}
						that.isAllSelect = true;
					} else {
						item.checked = false;
						that.isAllSelect = false;
					}
					return item;
				});
				that.$set(that.cartList, 'valid', newValid);
				that.selectValue = selectValue;
				that.switchSelect();
			}
		},
		checkboxChange: function (event) {
			let that = this;
			let value = event.detail.value;
			let valid = that.cartList.valid;
			let arr1 = [];
			let arr2 = [];
			let arr3 = [];
			let newValid = valid.map((item) => {
				if (that.inArray(item.id, value)) {
					if (that.footerswitch) {
						if (item.attrStatus) {
							item.checked = true;
							arr1.push(item);
						} else {
							item.checked = false;
						}
					} else {
						item.checked = true;
						arr1.push(item);
					}
				} else {
					item.checked = false;
					arr2.push(item);
				}
				return item;
			});
			if (that.footerswitch) {
				arr3 = arr2.filter((item) => !item.attrStatus);
			}
			// for (let index in valid) {
			// 	if (that.inArray(valid[index].id, value)){
			// 		if(valid[index].attrStatus){
			// 			valid[index].checked = true;
			// 		}else{
			// 			valid[index].checked = false;
			// 		}
			// 	} else {
			// 		valid[index].checked = false;
			// 	}
			// }
			that.$set(that.cartList, 'valid', newValid);
			// let newArr = that.cartList.valid.filter(item => item.attrStatus);
			that.isAllSelect = newValid.length === arr1.length + arr3.length;
			that.selectValue = value;
			that.switchSelect();
		},
		inArray: function (search, array) {
			for (let i in array) {
				if (array[i] == search) {
					return true;
				}
			}
			return false;
		},
		switchSelect: function () {
			let that = this;
			let validList = that.cartList.valid;
			let selectValue = that.selectValue;
			let selectCountPrice = 0.0;
			if (selectValue.length < 1) {
				that.selectCountPrice = selectCountPrice;
			} else {
				for (let index in validList) {
					if (that.inArray(validList[index].id, selectValue)) {
						selectCountPrice = that.$util.$h.Add(selectCountPrice, that.$util.$h.Mul(validList[index].cart_num, validList[index].truePrice));
					}
				}
				that.selectCountPrice = selectCountPrice;
			}
		},
		/**
		 * Điền thủ công vào giỏ hàng
		 *
		 */
		iptCartNum: function (index) {
			let item = this.cartList.valid[index];
			console.log(item.cart_num,'22')
			if (item.cart_num) {
				this.setCartNum(item.id, item.cart_num);
			}
			if (!item.cart_num) {
				item.cart_num = 1;
			}
			this.switchSelect();
		},
		blurInput: function (index) {
			let item = this.cartList.valid[index];
			if (!item.cart_num) {
				item.cart_num = 1;
				this.$set(this.cartList, 'valid', this.cartList.valid);
			}
			console.log(this.cartList.valid)
		},
		subCart: function (index) {
			let that = this;
			if (this.disabledChangeNumber) return;
			let status = false;
			let item = that.cartList.valid[index];
			item.cart_num = Number(item.cart_num) - 1;
			if (item.cart_num < 1) status = true;
			if (item.cart_num <= 1) {
				item.cart_num = 1;
				item.numSub = true;
			} else {
				item.numSub = false;
				item.numAdd = false;
			}
			if (false == status) {
				that.setCartNum(
					item.id,
					item.cart_num,
					function (data) {
						that.cartList.valid[index] = item;
						that.getCartNum();
						that.switchSelect();
					},
					() => {
						item.cart_num = Number(item.cart_num) + 1;
					}
				);
			}
		},
		addCart: function (index) {
			let that = this;
			if (this.adding) return;
			if (this.disabledChangeNumber) return;
			let item = that.cartList.valid[index];
			item.cart_num = Number(item.cart_num) + 1;
			let productInfo = item.productInfo;
			if (productInfo.hasOwnProperty('attrInfo') && item.cart_num >= item.productInfo.attrInfo.stock) {
				item.cart_num = item.productInfo.attrInfo.stock;
				item.numAdd = true;
				item.numSub = false;
			} else {
				item.numAdd = false;
				item.numSub = false;
			}
			Throttle(
				that.setCartNum(
					item.id,
					item.cart_num,
					(data) => {
						that.cartList.valid[index] = item;
						that.getCartNum();
						that.switchSelect();
					},
					() => {
						item.cart_num = Number(item.cart_num) - 1;
					}
				),
				3000
			);
		},
		setCartNum(cartId, cartNum, successCallback, errorCallback) {
			let that = this;
			if (this.disabledChangeNumber) return;
			this.disabledChangeNumber = true;
			changeCartNum(cartId, cartNum)
				.then((res) => {
					successCallback && successCallback(res.data);
				})
				.catch((err) => {
					errorCallback && errorCallback();
					return that.$util.Tips({
						title: err
					});
				})
				.finally((e) => {
					setTimeout((e) => {
						this.disabledChangeNumber = false;
					}, DEBOUNCETIME);
				});
		},
		getCartNum: function () {
			let that = this;
			getCartCounts().then((res) => {
				that.cartCount = res.data.count;
				this.adding = false;
				this.$store.commit('indexData/setCartNum', res.data.count > 99 ? '..' : res.data.count);
				if (res.data.count > 0) {
					wx.setTabBarBadge({
						index: 2,
						text: res.data.count + ''
					});
				} else {
					wx.hideTabBarRedDot({
						index: 2
					});
				}
			});
		},
		getCartData(data) {
			return new Promise((resolve, reject) => {
				getCartList(data)
					.then((res) => {
						resolve(res.data);
					})
					.catch((err) => {
						this.loading = false;
						this.canShow = true;
						this.$util.Tips({
							title: err
						});
					});
			});
		},
		async getCartList(init) {
			uni.showLoading({
				title: this.$t(`Đang tải`),
				mask: true
			});
			let that = this;
			let data = {
				page: that.page,
				limit: that.limit,
				status: 1
			};
			getCartCounts().then(async (c) => {
				that.cartCount = c.data.count;
				if (init) {
					this.adding = false;
					this.$store.commit('indexData/setCartNum', c.data.count > 99 ? '..' : c.data.count);
					if (c.data.count > 0) {
						wx.setTabBarBadge({
							index: 2,
							text: c.data.count + ''
						});
					} else {
						wx.hideTabBarRedDot({
							index: 2
						});
					}
				}
				for (let i = 0; i < Math.ceil(c.data.ids.length / that.limit); i++) {
					let cartList = await this.getCartData(data);
					data.page = data.page + 1;
					let valid = cartList.valid;
					let validList = that.$util.SplitArray(valid, that.cartList.valid);

					let numSub = [
						{
							numSub: true
						},
						{
							numSub: false
						}
					];
					let numAdd = [
							{
								numAdd: true
							},
							{
								numAdd: false
							}
						],
						selectValue = [];
					if (validList.length > 0) {
						for (let index in validList) {
							if (validList[index].cart_num == 1) {
								validList[index].numSub = true;
							} else {
								validList[index].numSub = false;
							}
							let productInfo = validList[index].productInfo;
							if (productInfo.hasOwnProperty('attrInfo') && validList[index].cart_num == validList[index].productInfo.attrInfo.stock) {
								validList[index].numAdd = true;
							} else if (validList[index].cart_num == validList[index].productInfo.stock) {
								validList[index].numAdd = true;
							} else {
								validList[index].numAdd = false;
							}
							if (validList[index].attrStatus) {
								validList[index].checked = true;
								selectValue.push(validList[index].id);
							} else {
								validList[index].checked = false;
							}
						}
					}
					that.$set(that.cartList, 'valid', validList);

					// that.goodsHidden = cartList.valid.length <= 0 ? false : true;
					that.selectValue = selectValue;
					let newArr = validList.filter((item) => item.attrStatus);
					that.isAllSelect = newArr.length == selectValue.length && newArr.length;
					that.switchSelect();
				}
				that.loading = false;
				this.canShow = true;
				uni.hideLoading();
			});
		},
		getInvalidList: function () {
			let that = this;
			if (this.loadendInvalid) return false;
			if (this.loadingInvalid) return false;
			let data = {
				page: that.pageInvalid,
				limit: that.limitInvalid,
				status: 0
			};
			getCartList(data)
				.then((res) => {
					let cartList = res.data,
						invalid = cartList.invalid,
						loadendInvalid = invalid.length < that.limitInvalid;
					let invalidList = that.$util.SplitArray(invalid, that.cartList.invalid);
					that.$set(that.cartList, 'invalid', invalidList);
					that.loadendInvalid = loadendInvalid;
					that.loadTitleInvalid = loadendInvalid ? that.$t(`Đã đến cuối danh sách`) : that.$t(`Tải thêm`);
					that.pageInvalid = that.pageInvalid + 1;
					that.loadingInvalid = false;
				})
				.catch((res) => {
					that.loadingInvalid = false;
					that.loadTitleInvalid = that.$t(`Tải thêm`);
				});
		},
		getHostProduct: function () {
			let that = this;
			if (that.hotScroll) return;
			getProductHot(that.hotPage, that.hotLimit).then((res) => {
				that.hotPage++;
				that.hotScroll = res.data.length < that.hotLimit;
				that.hostProduct = that.hostProduct.concat(res.data);
			});
		},
		goodsOpen: function () {
			let that = this;
			that.goodsHidden = !that.goodsHidden;
		},
		goRouter(item) {
			var pages = getCurrentPages();
			var page = pages[pages.length - 1].$page.fullPath;
			if (item.link == page) return;
			uni.switchTab({
				url: item.link,
				fail(err) {
					uni.redirectTo({
						url: item.link
					});
				}
			});
		},
		manage: function () {
			let that = this;
			that.footerswitch = !that.footerswitch;
			let arr1 = [];
			let arr2 = [];
			let newValid = that.cartList.valid.map((item) => {
				if (that.footerswitch) {
					if (item.attrStatus) {
						if (item.checked) {
							arr1.push(item.id);
						}
					} else {
						item.checked = false;
						arr2.push(item);
					}
				} else {
					if (item.checked) {
						arr1.push(item.id);
					}
				}
				return item;
			});
			that.cartList.valid = newValid;
			if (that.footerswitch) {
				that.isAllSelect = newValid.length === arr1.length + arr2.length;
			} else {
				that.isAllSelect = newValid.length === arr1.length;
			}
			that.selectValue = arr1;
			that.switchSelect();
		},
		unsetCart: function () {
			let that = this,
				ids = [];
			for (let i = 0, len = that.cartList.invalid.length; i < len; i++) {
				ids.push(that.cartList.invalid[i].id);
			}
			cartDel(ids)
				.then((res) => {
					that.$util.Tips({
						title: that.$t(`Xóa thành công`)
					});
					that.$set(that.cartList, 'invalid', []);
					that.getCartNum();
				})
				.catch((res) => {});
		}
	},
	onReachBottom() {
		let that = this;
		if (that.loadend) {
			that.getInvalidList();
		}
		if (that.cartList.valid.length == 0 && that.cartList.invalid.length == 0) {
			that.getHostProduct();
		}
	},
	// Theo dõi cuộn (scroll listener)
	onPageScroll(e) {
		// Truyền vào giá trị scrollTop và kích hoạt sự kiện theo dõi cuộn của tất cả component easy-loadimage
		uni.$emit('scroll');
	}
};
</script>

<style scoped lang="scss">
.shoppingCart {
	/* #ifdef H5 */
	// padding-bottom: 0;
	// padding-bottom: constant(safe-area-inset-bottom);
	// padding-bottom: env(safe-area-inset-bottom);
	/* #endif */
}

.shoppingCart .labelNav {
	height: 76rpx;
	padding: 0 30rpx;
	font-size: 22rpx;
	color: #8c8c8c;
	position: fixed;
	left: 0;
	width: 100%;
	box-sizing: border-box;
	background-color: #f5f5f5;
	z-index: 5;
	top: 0;
}

.shoppingCart .labelNav .item .iconfont {
	font-size: 25rpx;
	margin-right: 10rpx;
}

.shoppingCart .nav {
	width: 100%;
	height: 80rpx;
	background-color: #fff;
	padding: 0 30rpx;
	box-sizing: border-box;
	font-size: 28rpx;
	color: #282828;
	position: fixed;
	left: 0;
	z-index: 5;
	top: 76rpx;
}

.shoppingCart .nav .num {
	margin-left: 12rpx;
}

.shoppingCart .nav .administrate {
	font-size: 26rpx;
	color: #282828;
	width: 110rpx;
	height: 46rpx;
	border-radius: 6rpx;
	border: 1px solid #a4a4a4;
}

.shoppingCart .noCart {
	margin-top: 171rpx;
	background-color: #fff;
	padding-top: 0.1rpx;
}

.shoppingCart .noCart .pictrue {
	width: 414rpx;
	height: 336rpx;
	margin: 78rpx auto 56rpx auto;
}

.shoppingCart .noCart .pictrue image {
	width: 100%;
	height: 100%;
}

.shoppingCart .list {
	margin-top: 171rpx;
}

.shoppingCart .list .item {
	padding: 25rpx 30rpx;
	background-color: #fff;
	margin-bottom: 15rpx;
}

.shoppingCart .list .item .picTxt {
	width: 627rpx;
	position: relative;
}

.shoppingCart .list .item .picTxt .pictrue {
	width: 160rpx;
	height: 160rpx;
}

.shoppingCart .list .item .picTxt .pictrue image {
	width: 100%;
	height: 100%;
	border-radius: 6rpx;
}

.shoppingCart .list .item .picTxt .text {
	width: 444rpx;
	font-size: 28rpx;
	color: #282828;
}

.shoppingCart .list .item .picTxt .text .reColor {
	color: #999;
}

.shoppingCart .list .item .picTxt .text .reElection {
	margin-top: 20rpx;
}

.shoppingCart .list .item .picTxt .text .reElection .title {
	font-size: 24rpx;
}

.shoppingCart .list .item .picTxt .text .reElection .reBnt {
	// width: 120rpx;
	padding: 0 10rpx;
	// height: 46rpx;
	margin-top: 6rpx;
	border-radius: 23rpx;
	font-size: 26rpx;
}

.shoppingCart .list .item .picTxt .text .infor {
	font-size: 24rpx;
	color: #868686;
	margin-top: 16rpx;
}

.shoppingCart .list .item .picTxt .text .money {
	font-size: 32rpx;
	color: var(--view-theme);
	margin-top: 28rpx;
}

.shoppingCart .list .item .picTxt .carnum {
	height: 47rpx;
	position: absolute;
	bottom: 0rpx;
	right: 0;
}

.shoppingCart .list .item .picTxt .carnum view {
	border: 1rpx solid #a4a4a4;
	width: 66rpx;
	text-align: center;
	height: 100%;
	line-height: 40rpx;
	font-size: 28rpx;
	color: #a4a4a4;
}

.shoppingCart .list .item .picTxt .carnum .reduce {
	border-right: 0;
	border-radius: 3rpx 0 0 3rpx;
}

.shoppingCart .list .item .picTxt .carnum .reduce.on {
	border-color: #e3e3e3;
	color: #dedede;
}

.shoppingCart .list .item .picTxt .carnum .plus {
	border-left: 0;
	border-radius: 0 3rpx 3rpx 0;
}

.shoppingCart .list .item .picTxt .carnum .plus.on {
	border-color: #e3e3e3;
	color: #dedede;
}

.shoppingCart .list .item .picTxt .carnum .num {
	color: #282828;
}

.shoppingCart .invalidGoods {
	background-color: #fff;
}

.shoppingCart .invalidGoods .goodsNav {
	width: 100%;
	height: 66rpx;
	padding: 0 30rpx;
	box-sizing: border-box;
	font-size: 28rpx;
	color: #282828;
}

.shoppingCart .invalidGoods .goodsNav .iconfont {
	color: #424242;
	font-size: 28rpx;
	margin-right: 17rpx;
}

.shoppingCart .invalidGoods .goodsNav .del {
	font-size: 26rpx;
	color: #999;
}

.shoppingCart .invalidGoods .goodsNav .del .icon-shanchu1 {
	color: #999;
	font-size: 33rpx;
	vertical-align: -2rpx;
	margin-right: 8rpx;
}

.shoppingCart .invalidGoods .goodsList .item {
	padding: 20rpx 30rpx;
	border-top: 1rpx solid #f5f5f5;
}

.shoppingCart .invalidGoods .goodsList .item .invalid {
	font-size: 22rpx;
	color: #fff;
	width: 70rpx;
	height: 36rpx;
	background-color: #aaa;
	border-radius: 3rpx;
	text-align: center;
	line-height: 36rpx;
}

.shoppingCart .invalidGoods .goodsList .item .pictrue {
	width: 140rpx;
	height: 140rpx;
}

.shoppingCart .invalidGoods .goodsList .item .pictrue image {
	width: 100%;
	height: 100%;
	border-radius: 6rpx;
}

.shoppingCart .invalidGoods .goodsList .item .text {
	width: 433rpx;
	font-size: 28rpx;
	color: #999;
	height: 140rpx;
}

.shoppingCart .invalidGoods .goodsList .item .text .name {
	width: 100%;
}

.shoppingCart .invalidGoods .goodsList .item .text .infor {
	font-size: 24rpx;
}

.shoppingCart .invalidGoods .goodsList .item .text .end {
	font-size: 26rpx;
	color: #bbb;
}

.shoppingCart .footer {
	z-index: 999;
	width: 100%;
	height: 96rpx;
	background-color: rgba(255, 255, 255, 0.85);
	backdrop-filter: blur(10px);
	position: fixed;
	padding: 0 30rpx;
	box-sizing: border-box;
	border-top: 1rpx solid #eee;
	bottom: 98rpx;
	bottom: calc(98rpx + constant(safe-area-inset-bottom)); ///Tương thích IOS<11.2/
	bottom: calc(98rpx + env(safe-area-inset-bottom)); ///Tương thích IOS>11.2/
}

.shoppingCart .footer.on {
	// #ifndef H5
	bottom: 0rpx;
	// #endif
}

.shoppingCart .footer .checkAll {
	font-size: 28rpx;
	color: #282828;
	margin-left: 16rpx;
}

// .shoppingCart .footer checkbox .wx-checkbox-input{background-color:#fafafa;}
.shoppingCart .footer .money {
	font-size: 30rpx;
}

.shoppingCart .footer .placeOrder {
	color: #fff;
	font-size: 30rpx;
	width: 226rpx;
	height: 70rpx;
	border-radius: 50rpx;
	text-align: center;
	line-height: 70rpx;
	margin-left: 22rpx;
}

.shoppingCart .footer .button .bnt {
	font-size: 28rpx;
	color: #999;
	border-radius: 50rpx;
	border: 1px solid #999;
	width: 160rpx;
	height: 60rpx;
	text-align: center;
	line-height: 60rpx;
}

.shoppingCart .footer .button form ~ form {
	margin-left: 17rpx;
}

.uni-p-b-96 {
	height: 96rpx;
}

.uni-p-b-98 {
	height: 100rpx;
	/* Tương thích IOS<11.2 */
	height: calc(100rpx + constant(safe-area-inset-bottom));
	/* Tương thích IOS>11.2 */
	height: calc(100rpx + env(safe-area-inset-bottom));
}

.emptyBox {
	text-align: center;
	padding: 80rpx 0;

	.tips {
		color: #aaa;
		font-size: 26rpx;
	}

	image {
		width: 414rpx;
		height: 304rpx;
	}
}
</style>
