<template>
  <view class="deliver-goods">
    <header class="header">
      <view class="order-num">
        <view class="num line1">Mã đơn hàng: {{ order_id }}</view>
      </view>
      <view class="address">
        <view class="name">
          <text class="iconfont icon-ic_location4"></text>
          {{ delivery.real_name }}
          <text class="phone">{{ delivery.user_phone }}</text>
        </view>
        <view>Địa chỉ: {{ delivery.user_address }}</view>
      </view>
      <view class="line">
        <image src="@/static/images/line.jpg" />
      </view>
    </header>
    <view class="wrapper">
      <view class="item acea-row row-between-wrapper">
        <view>Hình thức giao hàng</view>
        <view class="mode acea-row row-middle row-right">
          <template v-for="(item, index) in types">
            <view
              v-if="item.show"
              class="goods"
              :class="active === index || productType == 3 ? 'on' : ''"
              :key="index"
              @click="changeType(item, index)"
            >
              <text
                :class="[
                  'iconfont',
                  active === index ? 'icon-ic_Selected' : 'icon-ic_unselect',
                ]"
              ></text>
              {{ item.title }}
            </view>
          </template>
        </view>
      </view>
      <block v-if="logistics.length > 0">
        <view class="list" v-show="active === 0">
          <view
            class="item acea-row row-middle"
            v-if="delivery.config_export_open == 1"
          >
            <view>Hình thức giao hàng</view>
            <view class="mode acea-row row-middle row-right">
              <view
                class="goods"
                :class="curExpress === item.key ? 'on' : ''"
                v-for="(item, index) in expressType"
                :key="index"
                @click="changeExpTpe(item, index)"
              >
                <text
                  :class="[
                    'iconfont',
                    curExpress === item.key
                      ? 'icon-ic_Selected'
                      : 'icon-ic_unselect',
                  ]"
                ></text>
                {{ item.title }}
              </view>
            </view>
          </view>
          <block v-if="curExpress == 1">
            <view class="item acea-row row-middle">
              <view>Đơn vị vận chuyển</view>
              <view class="select-box">
                <picker
                  class="pickerBox"
                  @change="bindPickerChange"
                  :value="seIndex"
                  :range="logistics"
                  range-key="name"
                >
                  <view class="uni-input">{{ logistics[seIndex].name }}</view>
                </picker>
              </view>
              <text class="iconfont icon-ic_rightarrow"></text>
            </view>
            <view class="item acea-row row-middle">
              <view>Mã vận đơn</view>
              <input
                type="text"
                placeholder="Vui lòng nhập"
                v-model="delivery_id"
                class="mode"
              />
              <!-- #ifdef MP -->
              <text class="iconfont icon-xiangji" @click="scanCode"></text>
              <!-- #endif -->
              <!-- #ifdef H5 -->
              <text
                v-if="isWeixin"
                class="iconfont icon-xiangji"
                @click="scanCode"
              ></text>
              <!-- #endif -->
            </view>
            <view class="item">
              <view class="trip" v-if="curExpress == 1"
                >Với SF Express, vui lòng nhập mã vận đơn: 4 số cuối số điện thoại người nhận hoặc người gửi</view
              >
              <view class="trip" v-if="curExpress == 1"
                >Ví dụ: SF000000000000:3941</view
              >
            </view>
          </block>
          <block v-if="curExpress == 2">
            <view class="item acea-row row-between-wrapper">
              <view>Họ tên người gửi</view>
              <input
                type="text"
                placeholder="Điền họ tên người gửi"
                v-model="to_name"
                class="mode"
              />
            </view>
            <view class="item acea-row row-between-wrapper">
              <view>Số điện thoại người gửi</view>
              <input
                type="text"
                placeholder="Điền số điện thoại người gửi"
                v-model="to_tel"
                class="mode"
              />
            </view>
            <view class="item acea-row row-between-wrapper">
              <view>Địa chỉ người gửi</view>
              <input
                type="text"
                placeholder="Điền địa chỉ người gửi"
                v-model="to_addr"
                class="mode"
              />
            </view>
            <view class="item acea-row row-between-wrapper">
              <view>Đơn vị vận chuyển</view>
              <view class="select-box">
                <picker
                  class="pickerBox"
                  @change="bindPickerChange"
                  :value="seIndex"
                  :range="logistics"
                  range-key="name"
                >
                  <!-- <view></view> -->
                  <view class="uni-input">{{ logistics[seIndex].name }}</view>
                </picker>
              </view>
            </view>
            <view
              class="item acea-row row-between-wrapper"
              v-if="expTemp.length > 0"
            >
              <view>Vận đơn điện tử</view>
              <view class="picker-add">
                <picker
                  class="pickerBox"
                  @change="bindTempChange"
                  :value="expIndex"
                  :range="expTemp"
                  range-key="title"
                >
                  <view class="uni-input">{{ expTemp[expIndex].title }}</view>
                </picker>
                <view class="look" @click="previewImage">Xem trước</view>
              </view>
            </view>
          </block>
        </view>
      </block>

      <view class="list" v-show="active === 1">
        <view class="item acea-row row-between-wrapper">
          <view>Người giao hàng</view>
          <view class="select-box" v-if="postPeople.length > 0">
            <picker
              class="pickerBox"
              @change="bindPostChange"
              :value="postIndex"
              :range="postPeople"
              range-key="wx_name"
            >
              <view class="acea-row row-middle">
                <view class="uni-input">{{
                  postPeople[postIndex].wx_name
                }}</view>
                <text class="iconfont icon-ic_rightarrow"></text>
              </view>
            </picker>
          </view>
        </view>
        <view
          class="item acea-row row-between-wrapper"
          v-if="postPeople[postIndex]"
        >
          <view>Số điện thoại</view>
          <view class="select-box acea-row row-middle row-right">{{
            postPeople[postIndex].phone
          }}</view>
        </view>
      </view>
      <textarea
        v-show="active === 2"
        v-model="fictitious_content"
        class="textarea"
        @blur="bindTextAreaBlur"
        placeholder="Ghi chú"
        :maxlength="500"
        auto-height
      />
    </view>
    <!-- <view class="split-wrapper" v-if="totalNum > 1">
      <view class="split-switch acea-row row-between-wrapper">
        <view>Giao hàng tách đơn</view>
        <view
          class="switch"
          :class="{ on: curGoods }"
          @click="changeGoods"
        ></view>
      </view>
      <splitOrder
        :select_all="false"
        :splitGoods="splitGoods"
        @getList="getList"
        v-if="curGoods"
      ></splitOrder>
    </view> -->
    <view class="height-add"></view>
    <view class="confirm-wrapper">
      <view class="confirm" @click="saveInfo">Xác nhận gửi</view>
    </view>
  </view>
</template>
<script>
import {
  getAdminOrderDelivery,
  setAdminOrderDelivery,
  getLogistics,
  orderExportTemp,
  orderDeliveryInfo,
  orderOrderDelivery,
  orderSplitInfo,
  orderSplitDelivery,
} from "@/api/admin";
import splitOrder from "../components/splitOrder";
export default {
  name: "GoodsDeliver",
  components: {
    splitOrder,
  },
  props: {},
  data: function () {
    return {
      types: [
        {
          type: "express",
          title: "Giao hàng",
          key: 1,
          show: true,
        },
        {
          type: "send",
          title: "Cửa hàng tự giao",
          key: 2,
          show: true,
        },
        {
          type: "fictitious",
          title: "Không cần vận chuyển",
          key: 3,
          show: true,
        },
      ],
      expressType: [
        {
          title: "Điền thủ công",
          key: 1,
        },
        {
          title: "In vận đơn điện tử",
          key: 2,
        },
      ],
      orderGoods: [
        {
          title: "Bật",
          key: 1,
        },
        {
          title: "Tắt",
          key: 0,
        },
      ],
      curExpress: 1,
      active: 0,
      order_id: "",
      delivery: [],
      logistics: [],
      delivery_type: "1",
      delivery_name: "",
      delivery_id: "",
      seIndex: 0,
      expIndex: 0,
      expTemp: [], // Mẫu vận đơn
      to_name: "", // Tên người gửi hàng
      to_tel: "", // Số điện thoại người gửi hàng
      to_addr: "", // Địa chỉ người gửi hàng
      postPeople: [], //Người giao hàng
      postIndex: 0,
      fictitious_content: "",
      listId: 0,
      curGoods: 0,
      splitGoods: [],
      cartIds: [],
      totalNum: 0,
      productType: 0,
      // #ifdef H5
      isWeixin: this.$wechat.isWeixin(),
      // #endif
    };
  },
  watch: {
    "$route.params.oid": function (newVal) {
      let that = this;
      if (newVal != undefined) {
        that.order_id = newVal;
        that.getIndex();
      }
    },
  },
  onLoad: function (option) {
    this.order_id = option.id;
    this.listId = option.listId;
    this.totalNum = option.totalNum;
    this.comeType = option.comeType;
    this.productType = option.productType;
    if (this.productType == 3) {
      // this.types.splice(0, 2);
      for (let i = 0; i < this.types.length; i++) {
        if (i == 2) {
          this.types[i].show = true;
        } else {
          this.types[i].show = false;
        }
      }
      this.delivery_type = 3;
      this.active = 2;
    }
    if (
      option.orderStatus == 8 ||
      option.orderStatus == 4 ||
      option.orderStatus == 9
    ) {
      this.curGoods = 1;
      this.orderGoods.pop();
      this.splitList();
    }
    this.getIndex();
    this.getLogistics();
    this.orderDeliveryInfo();
    this.geTorderOrderDelivery();
  },
  methods: {
    getList(val) {
      let that = this;
      that.splitGoods = val;
      let cartIds = [];
      val.forEach((item) => {
        if (item.checked) {
          let i = {
            cart_id: item.cart_id,
            cart_num: item.surplus_num,
          };
          cartIds.push(i);
        }
      });
      this.cartIds = cartIds;
    },
    splitList() {
      orderSplitInfo(this.listId)
        .then((res) => {
          let list = res.data;
          list.forEach((item) => {
            item.checked = false;
            item.numShow = item.surplus_num;
          });
          this.splitGoods = list;
        })
        .catch((err) => {
          return this.$util.Tips({
            title: err,
          });
        });
    },
    // Nhấn để lấy danh sách tách đơn
    changeGoods() {
      this.curGoods = this.curGoods ? 0 : 1;
      if (this.curGoods) {
        this.splitList();
      }
    },
    // Quét mã 1D mã vận đơn
    scanCode() {
      // #ifdef MP
      let that = this;
      uni.scanCode({
        scanType: ["barCode"],
        success(res) {
          that.delivery_id = res.result;
        },
      });
      // #endif
      // #ifdef H5
      if (this.$wechat.isWeixin()) {
        this.$wechat
          .wechatEvevt("scanQRCode", {
            needResult: 1,
            scanType: ["barCode"],
          })
          .then((res) => {
            let result = res.resultStr.split(",");
            this.delivery_id = result[1];
          });
      }
      // #endif
    },
    // Xem trước hình ảnh
    previewImage() {
      uni.previewImage({
        urls: [this.expTemp[this.expIndex].pic],
        success: function () {},
        fail: function (error) {},
      });
    },
    // Lấy danh sách nhân viên giao hàng
    geTorderOrderDelivery() {
      orderOrderDelivery().then((res) => {
        this.postPeople = res.data;
      });
    },
    // Chọn nhân viên giao hàng
    bindPostChange(e) {
      this.postIndex = e.detail.value;
    },
    // Chọn loại giao hàng
    changeExpTpe(item, index) {
      this.curExpress = item.key;
      this.getLogistics(index || "");
    },
    changeType: function (item, index) {
      this.active = index;
      this.delivery_type = item.key;
    },
    getIndex: function () {
      let that = this;
      getAdminOrderDelivery(that.order_id).then(
        (res) => {
          that.delivery = res.data;
        },
        (error) => {
          that.$util.Tips({
            title: error,
          });
        }
      );
    },
    getLogistics(status) {
      let that = this;
      getLogistics({
        status,
      }).then(
        (res) => {
          that.logistics = res.data;
          that.getExpTemp(res.data[0].code);
        },
        (error) => {
          that.$util.Tips({
            title: error,
          });
        }
      );
    },
    async saveInfo() {
      let that = this,
        delivery_type = that.delivery_type,
        delivery_name = that.logistics[that.seIndex].name,
        delivery_id = that.delivery_id,
        userName = that.delivery_name,
        save = {};
      save.delivery_type = delivery_type;
      save.delivery_code = that.logistics[that.seIndex].code;
      save.delivery_name = that.logistics[that.seIndex].name;
      save.type = that.active + 1;
      if (delivery_type == 1 && this.curExpress == 1) {
        if (!delivery_id) {
          return this.$util.Tips({
            title: "Vui lòng nhập mã vận đơn",
          });
        }
        save.express_record_type = that.curExpress;
        save.delivery_id = delivery_id;
        if (that.curGoods) {
          that.setSplitInfo(save);
        } else {
          that.setInfo(save);
        }
      }

      if (delivery_type == 1 && this.curExpress == 2) {
        if (!that.to_name) {
          return this.$util.Tips({
            title: "Vui lòng điền họ tên người gửi",
          });
        }
        if (!that.to_tel) {
          return this.$util.Tips({
            title: "Vui lòng điền số điện thoại người gửi",
          });
        }
        if (!/^1[3456789]\d{9}$/.test(that.to_tel)) {
          return this.$util.Tips({
            title: "Vui lòng điền số điện thoại người gửi",
          });
        }
        if (!that.to_addr) {
          return this.$util.Tips({
            title: "Vui lòng điền địa chỉ người gửi",
          });
        }
        if (that.expTemp.length == 0) {
          return this.$util.Tips({
            title: "Vui lòng chọn vận đơn điện tử",
          });
        }
        save.express_record_type = that.curExpress;
        save.to_name = that.to_name;
        save.to_tel = that.to_tel;
        save.to_addr = that.to_addr;
        save.express_temp_id = that.expTemp[that.expIndex].temp_id;
        if (that.curGoods) {
          that.setSplitInfo(save);
        } else {
          that.setInfo(save);
        }
      }
      if (delivery_type == 2) {
        if (!that.postPeople.length) {
          return this.$util.Tips({
            title: "Vui lòng thêm người giao hàng trong trang quản trị",
          });
        }
        let obj = this.postPeople[this.postIndex];
        let params = {};
        params.type = that.delivery_type;
        params.sh_delivery_name = obj.wx_name;
        params.sh_delivery_id = obj.phone;
        params.sh_delivery_uid = obj.uid;
        if (that.curGoods) {
          that.setSplitInfo(params);
        } else {
          that.setInfo(params);
        }
      }
      if (delivery_type == 3) {
        let params = {};
        params.type = that.delivery_type;
        params.fictitious_content = that.fictitious_content;
        if (that.curGoods) {
          that.setSplitInfo(params);
        } else {
          that.setInfo(params);
        }
      }
    },
    setInfo: function (item) {
      let that = this;
      setAdminOrderDelivery(that.delivery.id, item).then(
        (res) => {
          that.$util.Tips({
            title: res.msg,
            icon: "success",
            mask: true,
          });
          setTimeout((res) => {
            if (this.comeType == 2) {
              uni.navigateTo({
                url: "/pages/admin/orderDetail/index?id=" + this.order_id,
              });
            } else {
              uni.navigateTo({
                url: "/pages/admin/orderList/index?types=1",
              });
            }
          }, 2000);
        },
        (error) => {
          that.$util.Tips({
            title: error,
          });
        }
      );
    },
    setSplitInfo(item) {
      if (!this.cartIds.length) {
        return this.$util.Tips({
          title: "Vui lòng chọn sản phẩm cần giao",
        });
      }
      item.cart_ids = this.cartIds;
      orderSplitDelivery(this.delivery.id, item)
        .then((res) => {
          this.$util.Tips({
            title: res.msg,
            icon: "success",
            mask: true,
          });
          setTimeout((res) => {
            if (this.comeType == 2) {
              uni.navigateTo({
                url: "/pages/admin/orderDetail/index?id=" + this.order_id,
              });
            } else {
              uni.navigateTo({
                url: "/pages/admin/orderList/index?types=1",
              });
            }
          }, 2000);
        })
        .catch((err) => {
          this.$util.Tips({
            title: err,
          });
        });
    },
    bindPickerChange(e) {
      this.seIndex = e.detail.value;
      this.getExpTemp(this.logistics[e.detail.value].code);
    },
    bindTempChange(e) {
      this.expIndex = e.detail.value;
    },
    getExpTemp(code) {
      orderExportTemp({
        com: code,
      }).then((res) => {
        this.expTemp = res.data.data;
      });
    },
    // Lấy cấu hình in đơn hàng mặc định
    orderDeliveryInfo() {
      orderDeliveryInfo().then((res) => {
        this.to_name = res.data.to_name;
        this.to_tel = res.data.to_tel;
        this.to_addr = res.data.to_add;
      });
    },
  },
};
</script>

<style lang="scss">
.picker-add {
  display: flex;
  align-items: center;
}

.height-add {
  height: 120upx;
}

/*Giao hàng*/
.deliver-goods {
  padding: 22rpx 20rpx;
}

.deliver-goods .header {
  position: relative;
  padding: 0 32rpx;
  border-radius: 24rpx;
  background: #ffffff;
  overflow: hidden;
}

.deliver-goods .header .order-num {
  padding: 20rpx 0;
  border-bottom: 1px dotted #eeeeee;
}

.deliver-goods .header .order-num .num {
  font-size: 28rpx;
  line-height: 40rpx;
  color: #333333;
  position: relative;
}

.deliver-goods header .order-num .num:after {
}

.deliver-goods header .order-num .name {
  width: 260upx;
  font-size: 26upx;
  color: #282828;
  text-align: center;
}

.deliver-goods header .order-num .name .iconfont {
  font-size: 35upx;
  color: #477ef3;
  vertical-align: middle;
  margin-right: 10upx;
}

.deliver-goods .header .address {
  padding: 20rpx 0 40rpx;
  font-size: 24rpx;
  line-height: 34rpx;
  color: #999999;
}

.deliver-goods .header .address .name {
  font-weight: 500;
  font-size: 30rpx;
  line-height: 42rpx;
  color: #333333;
  margin-bottom: 12rpx;
}

.deliver-goods .header .address .name .iconfont {
  margin-right: 8rpx;
  font-size: 32rpx;
}

.deliver-goods .header .address .name .phone {
  margin-left: 40rpx;
}

.deliver-goods .header .line {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 4rpx;
}

.deliver-goods .header .line image {
  width: 100%;
  height: 100%;
  display: block;
}

.deliver-goods .wrapper {
  padding: 12rpx 0;
  border-radius: 24rpx;
  margin-top: 20rpx;
  background-color: #fff;
}

.deliver-goods .wrapper .item {
  padding: 0 24rpx;
  height: 80rpx;
  font-size: 28rpx;
  color: #333333;
  position: relative;
}

.deliver-goods .wrapper .item .mode {
  flex: 1;
  height: 100%;
  text-align: right;
}

.deliver-goods .wrapper .item .mode .iconfont {
  font-size: 32rpx;
  margin-right: 12rpx;
  color: #cccccc;
}

.deliver-goods .wrapper .item .mode .goods ~ .goods {
  margin-left: 50rpx;
}

.deliver-goods .wrapper .item .mode .goods {
  color: #999999;
}

.deliver-goods .wrapper .item .mode .goods.on {
  color: #333333;
}

.deliver-goods .wrapper .item .mode .goods.on .iconfont {
  color: $primary-admin;
}

.deliver-goods .wrapper .item .icon-up {
  position: absolute;
  font-size: 35upx;
  color: #2c2c2c;
  right: 30upx;
}

.deliver-goods .wrapper .item select {
  direction: rtl;
  padding-right: 60upx;
  position: relative;
  z-index: 2;
}

.deliver-goods .wrapper .item input::placeholder {
  color: #bbb;
}

.deliver-goods .confirm-wrapper {
  background-color: #ffffff;
  position: fixed;
  left: 0;
  bottom: 0;
  bottom: constant(safe-area-inset-bottom);
  bottom: env(safe-area-inset-bottom);
  width: 100%;
  padding: 20rpx;
}

.deliver-goods .confirm {
  font-weight: 500;
  font-size: 28rpx;
  color: #fff;
  height: 80rpx;
  border-radius: 40rpx;
  background: #2a7efb;
  text-align: center;
  line-height: 80rpx;
}

.select-box {
  flex: 1;
  height: 100%;

  .pickerBox {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    height: 100%;

    .iconfont {
      margin-left: 8rpx;
      font-size: 24rpx;
      color: #999999;
    }
  }
}

.look {
  margin-left: 20rpx;
  color: $primary-admin;
}

.textarea {
  display: block;
  min-height: 192rpx;
  padding: 24rpx;
  width: 100%;
  // border-bottom: 1px solid #f0f0f0;
  box-sizing: border-box;
}

.icon-xiangji {
  font-size: 35rpx;
  color: $primary-admin;
}

.trip {
  font-size: 22rpx;
  color: #ccc;
  padding: 6rpx 0;
}

.split-wrapper {
  border-radius: 24rpx;
  margin-top: 20rpx;
  background: #ffffff;

  .splitOrder {
    padding: 0 24rpx 46rpx;
    margin: 0;
  }
}

.split-switch {
  padding: 40rpx 24rpx;
  font-size: 28rpx;
  color: #333333;

  .switch {
    position: relative;
    width: 79rpx;
    height: 48rpx;
    padding: 4rpx;
    border-radius: 24rpx;
    background: #dddddd;
    transition: background 0.1s, border 0.1s;

    &::after {
      content: "";
      position: absolute;
      top: 4rpx;
      left: 4rpx;
      width: 40rpx;
      height: 40rpx;
      border-radius: 20rpx;
      background: #ffffff;
      box-shadow: 0 3rpx 6rpx 0 rgba(0, 0, 0, 0.08);
      transition: transform 0.35s cubic-bezier(0.4, 0.4, 0.25, 1.35);
    }

    &.on {
      background: #2a7efb;

      &::after {
        transform: translateX(31rpx);
      }
    }
  }
}
</style>
