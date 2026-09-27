// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import {
	getCategoryList,
	getProductslist,
	getAttr,
	postCartNum
} from '@/api/store.js';
import {cartDel} from "@/api/order.js";
import {toLogin} from '@/libs/login.js';
export default {
	data() {
		return {
			attr: {
				cartAttr: false,
				productAttr: [],
				productSelect: {}
			},
			productValue: [],
		};
	},
	created() {

	},
	methods: {
		updateFun(e,num){
			if(e.cartNum){
				this.tempArr.forEach((item)=>{
					if(item.id == e.id){
						item.cart_num = e.cartNum
					}
				})
				// Chỉ gọi khi đến từ trang cửa hàng
				if(num){
					this.getCartNum();
				}
			}
		},
		/**
		 * Thuộc tính được chọn mặc định
		 *
		 */
		DefaultSelect: function() {
			let productAttr = this.attr.productAttr;
			let value = [];
			for (let key in this.productValue) {
				if (this.productValue[key].stock > 0) {
					value = this.attr.productAttr.length ? key.split(",") : [];
					break;
				}
			}
			for (let i = 0; i < productAttr.length; i++) {
				this.$set(productAttr[i], "index", value[i]);
			}
			//sort(); hàm sắp xếp: số - chữ Anh - chữ Hán;
			let productSelect = this.productValue[value.join(",")];
			this.$set(this.attr.productSelect,"store_name",this.storeName);
			if (productSelect && productAttr.length) {
				this.$set(this.attr.productSelect, "image", productSelect.image);
				this.$set(this.attr.productSelect, "price", productSelect.price);
				this.$set(this.attr.productSelect, "stock", productSelect.stock);
				this.$set(this.attr.productSelect, "unique", productSelect.unique);
				this.$set(this.attr.productSelect, "cart_num", 1);
				this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
				this.$set(this, "attrValue", value.join(","));
			} else if (!productSelect && productAttr.length) {
				this.$set(this.attr.productSelect, "image", this.storeInfo.image);
				this.$set(this.attr.productSelect, "price", this.storeInfo.price);
				this.$set(this.attr.productSelect, "stock", 0);
				this.$set(this.attr.productSelect, "unique", "");
				this.$set(this.attr.productSelect, "cart_num", 0);
				this.$set(this, "attrValue", "");
				this.$set(this.attr.productSelect, 'vip_price', this.storeInfo.vip_price);
			} else if (!productSelect && !productAttr.length) {
				this.$set(this.attr.productSelect, "image", this.storeInfo.image);
				this.$set(this.attr.productSelect, "price", this.storeInfo.price);
				this.$set(this.attr.productSelect, "stock", this.storeInfo.stock);
				this.$set(this.attr.productSelect,"unique",this.storeInfo.unique || "");
				this.$set(this.attr.productSelect, "cart_num", 1);
				this.$set(this, "attrValue", "");
				this.$set(this.attr.productSelect, 'vip_price', this.storeInfo.vip_price);
			}
		},
		/**
		 * Gán giá trị khi thuộc tính thay đổi
		 *
		 */
		ChangeAttr: function(res) {
			let productSelect = this.productValue[res];
			if (productSelect && productSelect.stock >= 0) {
				this.$set(this.attr.productSelect, "image", productSelect.image);
				this.$set(this.attr.productSelect, "price", productSelect.price);
				this.$set(this.attr.productSelect, "stock", productSelect.stock);
				this.$set(this.attr.productSelect, "unique", productSelect.unique);
				this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
				this.$set(this.attr.productSelect, "cart_num", 1);
				this.$set(this, "attrValue", res);
			} else {
				this.$set(this.attr.productSelect, 'image', this.storeInfo.image);
				this.$set(this.attr.productSelect, 'price', this.storeInfo.price);
				this.$set(this.attr.productSelect, 'stock', 0);
				this.$set(this.attr.productSelect, 'unique', '');
				this.$set(this.attr.productSelect, 'cart_num', 0);
				this.$set(this.attr.productSelect, 'vip_price', this.storeInfo.vip_price);
				this.$set(this, 'attrValue', '');
			}
		},
		attrVal(val) {
			this.$set(this.attr.productAttr[val.indexw], 'index', this.attr.productAttr[val.indexw].attr_values[val
				.indexn]);
		},
		/**
		 * Điền thủ công vào giỏ hàng
		 *
		 */
		iptCartNum: function(e) {
			this.$set(this.attr.productSelect, 'cart_num', e);
		},
		onMyEvent: function() {
			this.$set(this.attr, 'cartAttr', false);
		},
		// Thay đổi giỏ hàng nhiều thuộc tính
		ChangeCartNumDuo(changeValue) {
			//Lấy thuộc tính vừa thay đổi hiện tại
			let productSelect = this.productValue[this.attrValue];
			//Nếu không có thuộc tính, gán bằng tồn kho mặc định của sản phẩm
			if (productSelect === undefined && !this.attr.productAttr.length)
				productSelect = this.attr.productSelect;
			//Không có giá trị thuộc tính thì tồn kho là 0; không có tăng giảm;
			if (productSelect === undefined) return;
			let stock = productSelect.stock || 0;
			let num = this.attr.productSelect;
			this.ChangeCartNum(changeValue, num, stock, 1);
		},
		// Thay đổi giỏ hàng một thuộc tính
		ChangeCartNumDan(changeValue, index, item) {
			let num = this.tempArr[index];
			let stock = this.tempArr[index].stock;
			this.ChangeCartNum(changeValue, num, stock, 0, item.id);
		},
		ChangeSubDel: function(event) {
			let that = this,
				list = that.cartData.cartList,
				ids = [];
			list.forEach(item => {
				ids.push(item.id)
			});
			cartDel(ids.join(",")).then(res => {
				that.$set(that.cartData, 'cartList', []);
				that.cartData.iScart = false;
				that.totalPrice = 0.00;
				that.page = 1;
				that.loadend = false;
				that.tempArr = [];
				that.productslist();
				that.getCartNum();
			})
		},
		ChangeOneDel: function(id, index) {
			let that = this,
				list = that.cartData.cartList;
			cartDel(id.toString()).then(res => {
				list.splice(index, 1);
				if (!list.length) {
					that.cartData.iScart = false;
					that.page = 1;
					that.loadend = false;
					that.tempArr = [];
					that.productslist();
				};
				that.getCartNum();
			})
		},
		// Thêm vào giỏ hàng nhiều phân loại (SKU);
		goCatNum() {
			this.goCat(1, this.id, 1);
		},
		closeList(e) {
			this.$set(this.cartData, 'iScart', e);
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
			if (changeValue) {
				num.cart_num++;
				if (num.cart_num > stock) {
					if (isDuo) {
						this.$set(this.attr.productSelect, "cart_num", stock ? stock : 1);
						this.$set(this, "cart_num", stock ? stock : 1);
					} else {
						num.cart_num = stock ? stock : 0;
						this.$set(this, 'tempArr', this.tempArr);
						this.$set(this.cartData, 'cartList', this.cartData.cartList);
					}
					return this.$util.Tips({
						title: "Sản phẩm này không còn đủ tồn kho"
					});
				} else {
					if (!isDuo) {
						if (cart) {
							this.goCat(0, id, 1, 1, num.product_attr_unique);
							this.getTotalPrice();
						} else {
							this.goCat(0, id, 1);
						}
					}
				}
			} else {
				num.cart_num--;
				if (num.cart_num == 0) {
					this.cartData.cartList.splice(index, 1);
					if (isDuo) {
						this.$set(this.attr.productSelect, "cart_num", 1);
						this.$set(this, "cart_num", 1);
					}
				}
				if (num.cart_num < 0) {
					if (isDuo) {
						this.$set(this.attr.productSelect, "cart_num", 1);
						this.$set(this, "cart_num", 1);
					} else {
						num.cart_num = 0;
						this.$set(this, 'tempArr', this.tempArr);
						this.$set(this.cartData, 'cartList', this.cartData.cartList);
					}
				} else {
					if (!isDuo) {
						if (cart) {
							this.goCat(0, id, 0, 1, num.product_attr_unique);
							this.getTotalPrice();
						} else {
							this.goCat(0, id, 0);
						}
					}
				}
			}
			this.tempArr.forEach((item)=>{
				if(item.id == id){
					item.cart_num = num.cart_num;
				}
			})
		},
		/*
		 * Thêm vào giỏ hàng
		 */
		goCat: function(duo, id, type, cart, unique) {
			let that = this;

			if (duo) {
				let productSelect = that.productValue[this.attrValue];
				//Nếu có thuộc tính mà chưa chọn, nhắc người dùng chọn
				console.log(productSelect);
				
				if (
					that.attr.productAttr.length &&
					productSelect === undefined
				) {
					return that.$util.Tips({
						title: "Sản phẩm không đủ tồn kho, vui lòng chọn thuộc tính khác"
					});
				}
			}
			let q = {
				product_id: id,
				num: duo ? that.attr.productSelect.cart_num : 1,
				type: type,
				unique: duo ? that.attr.productSelect.unique : cart ? unique : ""
			};

			postCartNum(q)
				.then(function(res) {
					if (duo) {
						that.attr.cartAttr = false;
						// that.page = 1;
						// that.loadend = false;
						that.tempArr.forEach((item, index) => {
							if (item.id == that.id) {
								let arrtStock = that.attr.productSelect.stock
								let objNum = parseInt(item.cart_num) + parseInt(that.attr.productSelect.cart_num);
								item.cart_num = objNum > arrtStock?arrtStock:objNum
							}
						})
						// that.productslist();
					}
					
					that.$util.Tips({
						title: res.msg
					});
					
					that.getCartNum();
					if (!cart) {
						that.getCartList(1);
					}
				})
				.catch(err => {
					return that.$util.Tips({
						title: err
					});
				});
		},
		goCartDuo(item,num) {
			if (!this.isLogin) {
				toLogin();
			} else {
				if(item.cart_button == 0){
					if(item.is_presale_product){
						uni.navigateTo({
							url: `/pages/activity/goods_details/index?id=${item.id}&type=6`
						})
					}else{
						//num: biểu thị click vào từ trang chủ
						let page1 = `/pages/goods_details/index?id=${item.id}&fromType=1`;
						let page2 = `/pages/goods_details/index?id=${item.id}`;
						uni.navigateTo({
							url: num?page2:page1
						})
					}
				}else{
					this.storeName = item.store_name;
					this.getAttrs(item.id);
					this.$set(this, 'id', item.id);
					this.$set(this.attr, 'cartAttr', true);
				}
			}
		},
		// Click giỏ hàng một thuộc tính mặc định
		goCartDan(item, index, num) {
			if (!this.isLogin) {
				toLogin();
			} else {
				if(item.cart_button == 0){
					if(item.is_presale_product){
						uni.navigateTo({
							url: `/pages/activity/goods_details/index?id=${item.id}&type=6`
						})
					}else{
						uni.navigateTo({
							url: `/pages/goods_details/index?id=${item.id}&fromType=1`
						})
					}
				}else{
					this.tempArr[index].cart_num = 1;
					// num biểu thị đến từ cửa hàng;
					// if(num){
					// 	this.$store.commit('indexData/setCartNum', parseInt(this.cartNum)+1)
					// }
					this.$set(this, 'tempArr', this.tempArr);
					this.goCat(0, item.id, 1);
				}
			}
		},
	}
};
