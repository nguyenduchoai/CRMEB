<template>
	<view class="goodCate">
		<!-- <view>
			<scroll-view scroll-y="true" class="scroll-Y"> -->
		<view class="header acea-row row-center-wrapper">
			<view class="pageIndex" hover-class="none" @click="jumpIndex">
				<text class="iconfont icon-shouye3"></text>
			</view>
			<navigator url="/pages/goods/goods_search/index" class="search acea-row row-center-wrapper" hover-class="none">
				<text class="iconfont icon-xiazai5"></text>
				{{ $t(`Tìm kiếm tên sản phẩm`) }}
			</navigator>
		</view>
		<view class="conter">
			<view class="aside">
				<scroll-view scroll-y="true" scroll-with-animation="true" style="height: calc(100% - 100rpx)">
					<view class="item acea-row row-center-wrapper" :class="index == navActive ? 'on' : ''" v-for="(item, index) in categoryList" :key="index" @click="tapNav(index, item)">
						<text>{{ $t(item.cate_name) }}</text>
					</view>
				</scroll-view>
			</view>
			<view class="wrapper">
				<view class="bgcolor" v-if="iSlong">
					<view class="longTab acea-row row-middle">
						<scroll-view scroll-x="true" style="white-space: nowrap; display: flex; height: 44rpx" scroll-with-animation :scroll-left="tabLeft" show-scrollbar="true">
							<view
								class="longItem"
								:style="'width:' + isWidth + 'px'"
								:class="index === tabClick ? 'click' : ''"
								v-for="(item, index) in categoryErList"
								:key="index"
								@click="longClick(index)"
							>
								{{ $t(item.cate_name) }}
							</view>
						</scroll-view>
					</view>
					<view class="openList" @click="openTap"><text class="iconfont icon-xiala"></text></view>
				</view>
				<view v-else>
					<view class="downTab">
						<view class="title acea-row row-between-wrapper">
							<view>{{ categoryTitle }}</view>
							<view class="closeList" @click="closeTap"><text class="iconfont icon-xiala"></text></view>
						</view>
						<view class="children">
							<view class="acea-row row-middle">
								<view class="item line1" :class="index === tabClick ? 'click' : ''" v-for="(item, index) in categoryErList" :key="index" @click="longClick(index)">
									{{ $t(item.cate_name) }}
								</view>
							</view>
						</view>
					</view>
					<view class="mask" @click="closeTap"></view>
				</view>
				<scroll-view
					scroll-y="true"
					scroll-with-animation="true"
					:scroll-top="scrollTop"
					@scroll="scroll"
					style="height: calc(100vh - 324rpx)"
					:lower-threshold="50"
					@scrolltolower="productslist"
				>
					<goodList
						ref="d_goodClass"
						:tempArr="tempArr"
						:isLogin="isLogin"
						@gocartduo="goCartDuo"
						@gocartdan="goCartDan"
						@ChangeCartNumDan="ChangeCartNumDan"
						@detail="goDetail"
					></goodList>
					<view class="loadingicon acea-row row-center-wrapper">
						<text class="loading iconfont icon-jiazai" :hidden="loading == false"></text>
						{{ loadTitle }}
					</view>
				</scroll-view>
			</view>
		</view>
		<view class="footer acea-row row-between-wrapper">
			<view class="cartIcon acea-row row-center-wrapper" @click="getCartList(0)" v-if="cartCount">
				<view class="iconfont icon-gouwuche-yangshi2"></view>
				<view class="num">{{ cartCount }}</view>
			</view>
			<view class="cartIcon acea-row row-center-wrapper noCart" v-else>
				<view class="iconfont icon-gouwuche-yangshi2"></view>
			</view>
			<view class="money acea-row row-middle">
				<view>
					{{ $t(`￥`) }}
					<text class="num">{{ totalPrice }}</text>
				</view>
				<view class="bnt" @click="subOrder">{{ $t(`Thanh toán`) }}</view>
			</view>
		</view>
		<cartList :cartData="cartData" @closeList="closeList" @ChangeCartNumDan="ChangeCartList" @ChangeSubDel="ChangeSubDel" @ChangeOneDel="ChangeOneDel"></cartList>
		<productWindow
			:attr="attr"
			:isShow="1"
			:iSplus="1"
			:iScart="1"
			:minQty="storeInfo.min_qty"
			@myevent="onMyEvent"
			@ChangeAttr="ChangeAttr"
			@ChangeCartNum="ChangeCartNumDuo"
			@attrVal="attrVal"
			@iptCartNum="iptCartNum"
			@goCat="goCatNum"
			id="product-window"
			:is_vip="is_vip"
		></productWindow>
		<!-- </scroll-view>
		</view> -->
	</view>
</template>

<script>
import { getCategoryList, getProductslist, getAttr, postCartNum } from '@/api/store.js';
import { vcartList, getCartCounts, cartDel } from '@/api/order.js';
import productWindow from '@/components/productWindow';
import goodList from '@/components/catGoodList';
import cartList from '@/components/cartList';
import { mapGetters } from 'vuex';
import { goShopDetail } from '@/libs/order.js';
import { toLogin } from '@/libs/login.js';
export default {
	computed: mapGetters(['isLogin', 'uid']),
	components: {
		productWindow,
		goodList,
		cartList
	},
	data() {
		return {
			categoryList: [],
			navActive: 0,
			categoryTitle: '',
			categoryErList: [],
			tabLeft: 0,
			scrollTop: 0,
			old: {
				scrollTop: 0
			},
			isWidth: 0, //Vị trí chiếm chỗ của mỗi tab điều hướng
			tabClick: 0, //Thanh điều hướng bị click
			iSlong: true,
			tempArr: [],
			loading: false,
			loadend: false,
			loadTitle: this.$t(`Tải thêm`),
			page: 1,
			limit: 10,
			cid: 0, //Danh mục cấp 1
			sid: 0, //Danh mục cấp 2
			isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
			isShowAuth: false, //Có ẩn ủy quyền hay không
			attr: {
				cartAttr: false,
				productAttr: [],
				productSelect: {}
			},
			productValue: [],
			attrValue: '', //Thuộc tính đã chọn
			storeName: '', //Tên sản phẩm nhiều thuộc tính
			id: 0,
			cartData: {
				cartList: [],
				iScart: false
			},
			cartCount: 0,
			totalPrice: 0.0,
			is_vip: 0, //Có phải thành viên hay không
			cart_num: 0,
			storeInfo: {}
		};
	},
	mounted() {
		let that = this;
		// Lấy chiều rộng thiết bị
		uni.getSystemInfo({
			success(e) {
				that.isWidth = e.windowWidth / 5;
			}
		});
		if (!that.categoryList.length) {
			this.getAllCategory(1);
			this.getCartNum();
			this.getCartList(1);
		}
		uni.$on('uploadCatData', () => {
			this.getAllCategory(1);
			this.getCartNum();
		});
	},
	methods: {
		jumpIndex() {
			this.$emit('jumpIndex');
		},
		// Tạo đơn hàng;
		subOrder: function () {
			let that = this,
				list = that.cartData.cartList,
				ids = [];
			if (list.length) {
				list.forEach((item) => {
					ids.push(item.id);
				});
				uni.navigateTo({
					url: '/pages/goods/order_confirm/index?cartId=' + ids.join(',')
				});
				that.cartData.iScart = false;
			} else {
				return that.$util.Tips({
					title: this.$t(`Vui lòng chọn sản phẩm`)
				});
			}
		},
		// Tính tổng giá;
		getTotalPrice: function () {
			let that = this,
				list = that.cartData.cartList,
				totalPrice = 0.0;
			list.forEach((item) => {
				if (item.attrStatus && item.status) {
					totalPrice = that.$util.$h.Add(totalPrice, that.$util.$h.Mul(item.cart_num, item.truePrice));
				}
			});
			that.$set(that, 'totalPrice', totalPrice);
		},
		ChangeSubDel: function (event) {
			let that = this,
				list = that.cartData.cartList,
				ids = [];
			list.forEach((item) => {
				ids.push(item.id);
			});
			cartDel(ids.join(',')).then((res) => {
				that.$set(that.cartData, 'cartList', []);
				that.cartData.iScart = false;
				that.totalPrice = 0.0;
				that.page = 1;
				that.loadend = false;
				that.tempArr = [];
				that.productslist();
				that.getCartNum();
			});
		},
		ChangeOneDel: function (id, index) {
			let that = this,
				list = that.cartData.cartList;
			cartDel(id.toString()).then((res) => {
				list.splice(index, 1);
				if (!list.length) {
					that.cartData.iScart = false;
					that.page = 1;
					that.loadend = false;
					that.tempArr = [];
					that.productslist();
				}
				that.getCartNum();
			});
		},
		getCartList(iSshow) {
			let that = this;
			vcartList().then((res) => {
				that.$set(that.cartData, 'cartList', res.data);
				if (res.data.length) {
					that.$set(that.cartData, 'iScart', iSshow ? false : !that.cartData.iScart);
				} else {
					that.$set(that.cartData, 'iScart', false);
				}
				that.getTotalPrice();
			});
		},
		closeList(e) {
			this.$set(this.cartData, 'iScart', e);
			this.page = 1;
			this.loadend = false;
			this.tempArr = [];
			this.productslist();
		},
		getCartNum() {
			let that = this;
			getCartCounts().then((res) => {
				that.cartCount = res.data.count;
				that.$refs.d_goodClass.addIng = false;
			});
		},

		onMyEvent: function () {
			this.$set(this.attr, 'cartAttr', false);
		},
		/**
		 * Thuộc tính được chọn mặc định
		 *
		 */
		DefaultSelect: function () {
			let productAttr = this.attr.productAttr;
			let value = [];
			for (let key in this.productValue) {
				if (this.productValue[key].stock > 0) {
					value = this.attr.productAttr.length ? key.split(',') : [];
					break;
				}
			}
			for (let i = 0; i < productAttr.length; i++) {
				this.$set(productAttr[i], 'index', value[i]);
			}
			//sort(); hàm sắp xếp: số - chữ Anh - chữ Hán;
			let productSelect = this.productValue[value.join(',')];
			if (productSelect && productAttr.length) {
				this.$set(this.attr.productSelect, 'store_name', this.storeName);
				this.$set(this.attr.productSelect, 'image', productSelect.image);
				this.$set(this.attr.productSelect, 'price', productSelect.price);
				this.$set(this.attr.productSelect, 'stock', productSelect.stock);
				this.$set(this.attr.productSelect, 'unique', productSelect.unique);
				this.$set(this.attr.productSelect, 'cart_num', this.storeInfo.min_qty);
				this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
				this.$set(this, 'attrValue', value.join(','));
			} else if (!productSelect && productAttr.length) {
				this.$set(this.attr.productSelect, 'store_name', this.storeName);
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', 0);
				this.$set(this.attr.productSelect, 'unique', '');
				this.$set(this.attr.productSelect, 'cart_num', 0);
				this.$set(this.attr.productSelect, 'vip_price', this.storeInfo.vip_price);
				this.$set(this, 'attrValue', '');
			} else if (!productSelect && !productAttr.length) {
				this.$set(this.attr.productSelect, 'store_name', this.storeName);
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', this.storeInfo.stock);
				this.$set(this.attr.productSelect, 'unique', this.storeInfo.unique || '');
				this.$set(this.attr.productSelect, 'cart_num', this.storeInfo.min_qty);
				this.$set(this, 'attrValue', '');
				this.$set(this.attr.productSelect, 'vip_price', this.storeInfo.vip_price);
			}
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
				this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
				this.$set(this.attr.productSelect, 'cart_num', this.storeInfo.min_qty);
				this.$set(this, 'attrValue', res);
			} else if (productSelect && productSelect.stock == 0) {
				this.$set(this.attr.productSelect, 'image', productSelect.image);
				this.$set(this.attr.productSelect, 'price', productSelect.price);
				this.$set(this.attr.productSelect, 'stock', 0);
				this.$set(this.attr.productSelect, 'unique', '');
				this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
				this.$set(this.attr.productSelect, 'cart_num', 0);
				this.$set(this, 'attrValue', '');
			} else {
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', 0);
				this.$set(this.attr.productSelect, 'unique', '');
				this.$set(this.attr.productSelect, 'vip_price', this.storeInfo.vip_price);
				this.$set(this.attr.productSelect, 'cart_num', 0);
				this.$set(this, 'attrValue', '');
			}
		},
		attrVal(val) {
			this.$set(this.attr.productAttr[val.indexw], 'index', this.attr.productAttr[val.indexw].attr_values[val.indexn]);
		},
		/**
		 * Điền thủ công vào giỏ hàng
		 *
		 */
		iptCartNum: function (e) {
			// this.$set(this.attr.productSelect, 'cart_num', e);
			if (e) {
				let number = this.storeInfo.min_qty;
				if (Number.isInteger(parseInt(e)) && parseInt(e) >= this.storeInfo.min_qty) {
					number = parseInt(e);
				}
				this.$nextTick((e) => {
					this.$set(this.attr.productSelect, 'cart_num', e < 0 ? this.storeInfo.min_qty : number);
				});
			}
		},
		onLoadFun() {},
		// Danh sách sản phẩm
		productslist: function () {
			let that = this;
			if (that.loadend) return;
			if (that.loading) return;
			that.loading = true;
			that.loadTitle = '';
			getProductslist({
				page: that.page,
				limit: that.limit,
				type: 1,
				cid: that.cid,
				sid: that.sid
			})
				.then((res) => {
					let list = res.data,
						loadend = list.length < that.limit;
					that.tempArr = that.$util.SplitArray(list, that.tempArr);
					that.$set(that, 'tempArr', that.tempArr);
					that.loading = false;
					that.loadend = loadend;
					that.loadTitle = loadend ? that.$t(`Không còn nội dung nào nữa~`) : that.$t(`Tải thêm`);
					that.page == 1 && this.goTop();
					that.page = that.page + 1;
				})
				.catch((err) => {
					(that.loading = false), (that.loadTitle = that.$t(`Tải thêm`));
				});
		},
		scroll(e) {
			this.old.scrollTop = e.detail.scrollTop;
		},
		goTop(e) {
			// Giải quyết vấn đề lớp view không đồng bộ
			this.scrollTop = this.old.scrollTop;
			this.$nextTick(() => {
				this.scrollTop = 0;
			});
		},
		// Thay đổi giỏ hàng một thuộc tính
		ChangeCartNumDan(changeValue, index, item) {
			let num = this.tempArr[index];
			let stock = this.tempArr[index].stock;
			this.ChangeCartNum(changeValue, num, stock, 0, item.id);
		},
		// Thay đổi giỏ hàng nhiều thuộc tính
		ChangeCartNumDuo(changeValue) {
			//Lấy thuộc tính vừa thay đổi hiện tại
			let productSelect = this.productValue[this.attrValue];
			//Nếu không có thuộc tính, gán bằng tồn kho mặc định của sản phẩm
			if (productSelect === undefined && !this.attr.productAttr.length) productSelect = this.attr.productSelect;
			//Không có giá trị thuộc tính thì tồn kho là 0; không có tăng giảm;
			if (productSelect === undefined) return;
			let stock = productSelect.stock || 0;
			let num = this.attr.productSelect;
			this.ChangeCartNum(changeValue, num, stock, 1, this.id);
		},
		// Tăng giảm số lượng khi đã có trong giỏ hàng;
		ChangeCartList(changeValue, index) {
			let list = this.cartData.cartList;
			let num = list[index];
			let stock = list[index].trueStock;
			this.ChangeCartNum(changeValue, num, stock, 0, num.product_id, index, 1);
			if (!list.length) {
				this.cartData.iScart = false;
				this.page = 1;
				this.loadend = false;
				this.tempArr = [];
				this.productslist();
			}
		},
		// Hàm tính tăng giảm giỏ hàng
		ChangeCartNum(changeValue, num, stock, isDuo, id, index, cart) {
			this.$refs.d_goodClass.addIng = false;
			if (changeValue) {
				num.cart_num++;
				if (num.cart_num > stock) {
					if (isDuo) {
						this.$set(this.attr.productSelect, 'cart_num', stock ? stock : 1);
						this.$set(this, 'cart_num', stock ? stock : 1);
					} else {
						num.cart_num = stock ? stock : 0;
						this.$set(this, 'tempArr', this.tempArr);
						this.$set(this.cartData, 'cartList', this.cartData.cartList);
					}
					return this.$util.Tips({
						title: this.$t(`Sản phẩm này không còn đủ tồn kho`)
					});
				} else {
					if (!isDuo) {
						if (cart) {
							this.goCat(0, id, 1, 1, num.product_attr_unique, num);
							this.getTotalPrice();
						} else {
							this.goCat(0, id, 1, 0, false, num);
						}
					}
				}
			} else {
				num.cart_num--;
				if (num.cart_num < num.min_qty) {
					this.cartData.cartList.splice(index, 1);
					num.cart_num = 0;
				} else if (num.cart_num == 0) {
					this.cartData.cartList.splice(index, 1);
				}
				if (num.cart_num < 0) {
					if (isDuo) {
						this.$set(this.attr.productSelect, 'cart_num', this.storeInfo.min_qty);
						this.$set(this, 'cart_num', this.storeInfo.min_qty);
					} else {
						num.cart_num = 0;
						this.$set(this, 'tempArr', this.tempArr);
						this.$set(this.cartData, 'cartList', this.cartData.cartList);
					}
				} else {
					if (!isDuo) {
						if (cart) {
							this.goCat(0, id, 0, 1, num.product_attr_unique, num);
							this.getTotalPrice();
						} else {
							this.goCat(0, id, 0, 0, false, num);
						}
					}
				}
			}
		},
		// Thêm vào giỏ hàng nhiều phân loại (SKU);
		goCatNum() {
			this.goCat(1, this.id, 1);
		},
		/*
		 * Thêm vào giỏ hàng
		 */
		goCat: function (duo, id, type, cart, unique, data) {
			let that = this;
			if (duo) {
				let productSelect = that.productValue[this.attrValue];
				//Nếu có thuộc tính mà chưa chọn, nhắc người dùng chọn
				if (that.attr.productAttr.length && productSelect === undefined) {
					return that.$util.Tips({
						title: that.$t(`Sản phẩm này không còn đủ tồn kho`)
					});
				}
				if (that.attr.productSelect.cart_num <= 0) {
					return that.$util.Tips({
						title: that.$t(`Thêm ít nhất 1 sản phẩm`)
					});
				}
			}
			let q = {
				product_id: id,
				type: type,
				unique: duo ? that.attr.productSelect.unique : cart ? unique : ''
			};
			data && data.cart_num < data.min_qty ? (q.num = data.min_qty) : '';
			if (!that.cartData.iScart) q.num = duo ? that.attr.productSelect.cart_num : this.storeInfo.min_qty;
			postCartNum(q)
				.then(function (res) {
					if (duo) {
						that.attr.cartAttr = false;
						that.$util.Tips({
							title: that.$t(`Thêm thành công`)
						});
						// that.page = 1;
						// that.loadend = false;
						that.tempArr.forEach((item, index) => {
							if (item.id == that.id) {
								let arrtStock = that.attr.productSelect.stock;
								let objNum = parseInt(item.cart_num) + parseInt(that.attr.productSelect.cart_num);
								item.cart_num = objNum > arrtStock ? arrtStock : objNum;
							}
						});
						// that.productslist();
					}
					that.getCartNum();
					if (!cart) {
						that.getCartList(1);
					}
				})
				.catch((err) => {
					return that.$util.Tips({
						title: err
					});
				});
		},
		// Click giỏ hàng một thuộc tính mặc định
		goCartDan(item, index) {
			if (!this.isLogin) {
				this.getIsLogin();
			} else {
				if (!item.cart_button) {
					goShopDetail(item, this.uid).then((res) => {
						uni.navigateTo({
							url: `/pages/goods_details/index?id=${item.id}`
						});
					});
					return;
				}
				item.cart_num = item.min_qty;
				this.$set(this, 'tempArr', this.tempArr);
				this.goCat(0, item.id, 1);
			}
		},
		goCartDuo(item) {
			if (!this.isLogin) {
				this.getIsLogin();
			} else {
				if (!item.cart_button) {
					goShopDetail(item, this.uid).then((res) => {
						uni.navigateTo({
							url: `/pages/goods_details/index?id=${item.id}`
						});
					});
					return;
				}
				uni.showLoading({
					title: this.$t(`Đang tải`)
				});
				this.storeName = item.store_name;
				this.getAttrs(item.id);
				this.$set(this, 'id', item.id);
				this.$set(this.attr, 'cartAttr', true);
			}
		},
		getIsLogin() {
			toLogin();
		},
		// API chi tiết sản phẩm;
		getAttrs(id) {
			let that = this;
			getAttr(id, 0).then((res) => {
				uni.hideLoading();
				that.$set(that.attr, 'productAttr', res.data.productAttr);
				that.$set(that, 'productValue', res.data.productValue);
				that.$set(that, 'is_vip', res.data.storeInfo.is_vip);
				that.$set(that, 'storeInfo', res.data.storeInfo);
				that.DefaultSelect();
			});
		},
		// Đến trang chi tiết
		goDetail(item) {
			goShopDetail(item, this.uid).then((res) => {
				uni.navigateTo({
					url: `/pages/goods_details/index?id=${item.id}`
				});
			});
		},

		openTap() {
			this.iSlong = false;
		},
		closeTap() {
			this.iSlong = true;
		},
		getAllCategory: function (type) {
			let that = this;
			if (type || !uni.getStorageSync('CAT2_DATA')) {
				getCategoryList().then((res) => {
					uni.setStorageSync('CAT2_DATA', res.data);
					let data = res.data;
					data.forEach((item) => {
						item.children.unshift({
							id: 0,
							cate_name: that.$t(`Tất cả`)
						});
					});
					that.categoryTitle = data[0].cate_name;
					that.cid = data[0].id;
					that.sid = 0;
					that.navActive = 0;
					that.tabClick = 0;
					that.categoryList = data;
					that.page = 1;
					that.loadend = false;
					that.tempArr = [];
					that.categoryErList = res.data[0].children ? res.data[0].children : [];
					that.productslist();
				});
			} else {
				let data = uni.getStorageSync('CAT2_DATA');
				data.forEach((item) => {
					item.children.unshift({
						id: 0,
						cate_name: that.$t(`Tất cả`)
					});
				});
				if (!that.cid) {
					that.categoryTitle = data[0].cate_name;
					that.cid = data[0].id;
					that.sid = 0;
					that.navActive = 0;
					that.tabClick = 0;
					that.categoryList = data;
					that.categoryErList = data[0].children ? data[0].children : [];
					that.page = 1;
					that.loadend = false;
					that.tempArr = [];
				}
				that.productslist();
			}
		},
		tapNav(index, item) {
			let list = this.categoryList[index];
			this.navActive = index;
			this.categoryTitle = list.cate_name;
			this.categoryErList = item.children ? item.children : [];
			this.tabClick = 0;
			this.tabLeft = 0;
			this.cid = list.id;
			this.sid = 0;
			this.page = 1;
			this.loadend = false;
			this.tempArr = [];
			this.productslist();
		},
		// Click thanh điều hướng
		longClick(index) {
			if (this.categoryErList.length > 3) {
				this.tabLeft = (index - 1) * (this.isWidth + 6); //Đặt vị trí gạch chân
			}
			this.tabClick = index; //Đặt tab điều hướng nào đã được click
			this.iSlong = true;
			this.sid = this.categoryErList[index].id;
			this.page = 1;
			this.loadend = false;
			this.tempArr = [];
			this.productslist();
		}
	},
	onReachBottom() {
		this.productslist();
	}
};
</script>

<style lang="scss">
.scroll-Y {
	height: 100vh;
}

page {
	background-color: #fff;
}

/deep/.product-window.joinCart {
	z-index: 999;
}

::-webkit-scrollbar {
	width: 0;
	height: 0;
	color: transparent;
	display: none;
}

.goodCate {
	.mask {
		z-index: 99;
	}

	/deep/.attrProduct {
		.mask {
			z-index: 100;
		}
	}

	.header {
		position: fixed;
		height: 128rpx;
		background-color: #fff;
		top: 0;
		left: 0;
		width: 100%;
		z-index: 99;
		border-bottom: 1px solid #f0f0f0;

		.pageIndex {
			width: 68rpx;
			height: 68rpx;
			border-radius: 50%;
			background-color: var(--view-theme);
			text-align: center;
			line-height: 68rpx;

			.iconfont {
				color: #fff;
				font-size: 30rpx;
			}
		}

		.search {
			width: 600rpx;
			height: 68rpx;
			border-radius: 36rpx;
			background-color: #f8f8f8;
			font-size: 26rpx;
			color: #adadad;
			margin-left: 22rpx;

			.iconfont {
				font-size: 30rpx;
				margin: 4rpx 16rpx 0 0;
			}
		}
	}

	.conter {
		margin-top: 128rpx;
		// height: 100vh;
		background-color: #fff;

		.aside {
			position: fixed;
			width: 23%;
			left: 0;
			bottom: 100rpx;
			top: 0;
			background-color: #f7f7f7;
			overflow-y: auto;
			overflow-x: hidden;
			margin-top: 128rpx;
			z-index: 99;

			.item {
				height: 100rpx;
				width: 100%;
				font-size: 26rpx;
				color: #424242;

				&.on {
					background-color: #fff;
					width: 100%;
					text-align: center;
					color: var(--view-theme);
					font-weight: 500;
					position: relative;

					&::after {
						content: '';
						position: absolute;
						width: 6rpx;
						height: 46rpx;
						background: var(--view-theme);
						border-radius: 0 4rpx 4rpx 0;
						left: 0;
					}
				}
			}
		}

		.wrapper {
			margin-top: 104rpx;
			width: 77%;
			float: right;
			background-color: #fff;

			.bgcolor {
				width: 100%;
				background-color: #fff;
			}

			.goodsList {
				margin-top: 0 !important;
			}

			.mask {
				z-index: 9;
			}

			.longTab {
				width: 65%;
				position: fixed;
				top: 0;
				margin-top: 128rpx;
				height: 100rpx;
				z-index: 99;
				background-color: #fff;

				.longItem {
					height: 44rpx;
					display: inline-block;
					line-height: 44rpx;
					text-align: center;
					font-size: 26rpx;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;
					color: #989898;
					background-color: #f7f7f7;
					border-radius: 22rpx;
					margin-left: 12rpx;

					&.click {
						font-weight: bold;
						background-color: var(--view-minorColorT);
						color: var(--view-theme);
					}
				}

				.underlineBox {
					height: 3px;
					width: 20%;
					display: flex;
					align-content: center;
					justify-content: center;
					transition: 0.5s;

					.underline {
						width: 33rpx;
						height: 4rpx;
						background-color: white;
					}
				}
			}

			.openList {
				width: 12%;
				height: 100rpx;
				background-color: #fff;
				line-height: 100rpx;
				padding-left: 30rpx;
				position: fixed;
				right: 0;
				top: 128rpx;
				z-index: 99;

				.iconfont {
					font-size: 22rpx;
					color: #989898;
				}
			}

			.downTab {
				width: 77%;
				position: fixed;
				top: 0;
				margin-top: 128rpx;
				z-index: 99;
				background-color: #fff;
				right: 0;

				.title {
					height: 100rpx;
					font-size: 26rpx;
					color: #adadad;
					padding-left: 20rpx;

					.closeList {
						width: 90rpx;
						height: 100%;
						line-height: 100rpx;
						padding-left: 30rpx;
						transform: rotate(180deg);

						.iconfont {
							font-size: 22rpx;
							color: #989898;
						}
					}
				}

				.children {
					max-height: 500rpx;
					overflow-x: hidden;
					overflow-y: auto;
					padding-bottom: 20rpx;

					.item {
						height: 60rpx;
						background-color: #f7f7f7;
						border-radius: 30rpx;
						line-height: 60rpx;
						padding: 0 15rpx;
						margin: 0 0 20rpx 20rpx;
						width: 165rpx;
						text-align: center;

						&.click {
							font-weight: bold;
							background-color: var(--view-minorColorT);
							color: var(--view-theme);
						}
					}
				}
			}

			.goodsList {
				margin-top: 228rpx;
				padding: 0 30rpx 0 20rpx;

				/deep/.item {
					margin-bottom: 33rpx !important;

					.pictrue {
						// height: 216rpx;
					}

					.text {
						font-size: 26rpx;
					}

					.bottom {
						.sales {
							.money {
								font-size: 34rpx;

								text {
									font-size: 26rpx;
								}
							}
						}

						.cart {
							.pictrue {
								width: 50rpx;
								height: 50rpx;
							}
						}
					}
				}
			}
		}
	}

	.footer {
		position: fixed;
		left: 0;
		bottom: 0;
		width: 100%;
		background-color: #fff;
		box-shadow: 0px -3rpx 16rpx rgba(36, 12, 12, 0.05);
		z-index: 101;
		padding: 12rpx 30rpx;
		box-sizing: border-box;
		padding-bottom: calc(12rpx + constant(safe-area-inset-bottom)); ///Tương thích IOS<11.2/
		padding-bottom: calc(12rpx + env(safe-area-inset-bottom)); ///Tương thích IOS>11.2/

		.cartIcon {
			width: 96rpx;
			height: 96rpx;
			border-radius: 50%;
			position: relative;
			margin-top: -36rpx;

			.iconfont {
				font-size: 115rpx;
				margin-top: -5px;
				color: var(--view-theme);
			}

			&.noCart {
				.iconfont {
					color: #cbcbcb;
				}
			}

			.num {
				background-color: var(--view-priceColor);
				min-width: 12rpx;
				color: #fff;
				border-radius: 15px;
				position: absolute;
				right: -6rpx;
				top: -10rpx;
				font-size: 22rpx;
				padding: 0 10rpx;
				height: 34rpx;
				line-height: 34rpx;
			}
		}

		.money {
			color: var(--view-priceColor);
			font-size: 28rpx;
			font-weight: bold;

			.num {
				font-size: 42rpx;
			}

			.bnt {
				width: 222rpx;
				height: 76rpx;
				background: var(--view-theme);
				border-radius: 46rpx;
				line-height: 76rpx;
				text-align: center;
				color: #fff;
				margin-left: 24rpx;
			}
		}
	}
}
</style>
