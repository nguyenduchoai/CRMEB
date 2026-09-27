<template>
  <view class="product-con" :style="colorStyle">
    <view class="product-con">
      <!-- #ifndef APP-PLUS -->
      <view class="navbar" :style="{ height: navH + 'rpx', opacity: opacity }">
        <view class="navbarH" :style="'height:' + navH + 'rpx;'">
          <view
            class="navbarCon acea-row row-center-wrapper"
            :style="{ paddingRight: navbarRight - 20 + 'px' }"
          >
            <view class="header acea-row row-center-wrapper">
              <view class="item on">{{ $t(`Chi tiết sản phẩm`) }}</view>
            </view>
          </view>
        </view>
      </view>
      <!-- #endif -->
      <!-- <view class='iconfont icon-xiangzuo' :style="{top:navH/2+'rpx',opacity:(1-opacity)}" @tap='returns'></view> -->
      <!-- #ifndef APP-PLUS -->
      <view
        id="home"
        class="home acea-row row-center-wrapper"
        :class="[opacity > 0.5 ? 'on' : '']"
        :style="{ top: homeTop + 'rpx' }"
      >
        <view class="iconfont icon-fanhui2" @tap="returns"></view>
        <!-- #ifdef MP -->
        <view class="line"></view>
        <view class="iconfont icon-gengduo5" @click="moreNav"></view>
        <!-- #endif -->
      </view>
      <!-- #endif -->
      <!-- #ifdef H5 -->
      <view
        id="home"
        class="home right acea-row row-center-wrapper"
        :class="[opacity > 0.5 ? 'on' : '']"
        :style="{ top: homeTop + 'rpx' }"
      >
        <!-- #endif -->
        <!-- #ifdef H5 -->
        <view class="iconfont icon-gengduo2" @click="moreNav"> </view>
        <homeList
          :navH="navH"
          :returnShow="returnShow"
          :currentPage="currentPage"
          :sysHeight="sysHeight"
        ></homeList>
      </view>
      <!-- #endif -->

      <view>
        <view id="past0">
          <!-- #ifdef APP-PLUS || MP -->
          <view class="" :style="'width:100%;' + 'height:' + sysHeight"></view>
          <!-- #endif -->
          <PageDesign
            :diyData="diyData"
            :productData="storeInfo"
            :priceData="realPriceData"
            :skuList="skuArr"
            :reply="reply"
            :replyCount="replyCount"
            :replyChance="replyChance"
            :productId="id"
            :couponList="couponList"
            :activity="activity"
            :attr="attr"
            :attrTxt="attrTxt"
            :attrValue="attrValue"
            :isShowPaidVip="isShowPaidVip"
            @bindSortId="bindSortId"
            @changeSpec="onChangeSpecFromPageDesign"
            @showSpecModal="onShowSpecModalFromPageDesign"
            @share="listenerActionSheet"
            @showCoupon="couponTap"
            @openModal="openModal"
            @goActivity="goActivity"
          ></PageDesign>
        </view>
        <view class="uni-p-b-98"></view>
      </view>

      <productBottom
        :diyData="diyData"
        :storeInfo="storeInfo"
        :is_gift="is_gift"
        :CartCount="CartCount"
        :noGoods="noGoods"
        :attr="attr"
        :presale_pay_status="presale_pay_status"
        :animated="animated"
        @setCollect="setCollect"
        @goCart="goCart"
        @goGift="goGift"
        @joinCart="joinCart"
        @goBuy="goBuy"
        @share="listenerActionSheet"
      ></productBottom>
      <shareRedPackets
        :sharePacket="sharePacket"
        @listenerActionSheet="listenerActionSheet"
        @closeChange="closeChange"
        :showAnimate="showAnimate"
        @boxStatus="boxStatus"
      ></shareRedPackets>
      <!-- Thành phần -->
      <productWindow
        :attr="attr"
        :isShow="1"
        :iSplus="1"
        :limitNum="storeInfo.limit_num"
        :minQty="storeInfo.min_qty"
        :unitName="storeInfo.unit_name"
        @myevent="onMyEvent"
        @ChangeAttr="ChangeAttr"
        @ChangeCartNum="ChangeCartNum"
        @attrVal="attrVal"
        @iptCartNum="iptCartNum"
        id="product-window"
        :is_vip="is_vip"
        @getImg="showImg"
        :is_virtual="storeInfo.is_virtual"
      ></productWindow>
      <cus-previewImg
        ref="cusPreviewImg"
        :list="skuArr"
        @changeSwitch="changeSwitch"
        @shareFriend="listenerActionSheet"
      />
      <swiperPrevie
        ref="cusSwiperImg"
        :list="storeInfo.slider_image"
      ></swiperPrevie>
      <couponListWindow
        :coupon="coupon"
        v-if="coupon"
        @ChangCouponsClone="ChangCouponsClone"
        @ChangCoupons="ChangCoupons"
        @ChangCouponsUseState="ChangCouponsUseState"
        @tabCouponType="tabCouponType"
      ></couponListWindow>
      <!-- Nút chia sẻ -->
      <view
        class="generate-posters acea-row row-middle"
        :class="posters ? 'on' : ''"
      >
        <!-- #ifndef MP -->
        <button
          class="item"
          hover-class="none"
          v-if="weixinStatus === true"
          @click="H5ShareBox = true"
        >
          <view class="iconfont icon-weixin3"></view>
          <view class="">{{ $t(`Gửi cho bạn bè`) }}</view>
        </button>
        <!-- #endif -->
        <!-- #ifdef MP -->
        <button
          class="item"
          open-type="share"
          hover-class="none"
          @click="goFriend"
        >
          <view class="iconfont icon-weixin3"></view>
          <view class="">{{ $t(`Gửi cho bạn bè`) }}</view>
        </button>
        <!-- #endif -->
        <!-- #ifdef H5  -->
        <div
          class="item copy-data"
          v-if="storeInfo.command_word != ''"
          :data-clipboard-text="storeInfo.command_word"
        >
          <view class="iconfont icon-fuzhikouling"></view>
          <text>{{ $t(`Sao chép mã chia sẻ`) }}</text>
        </div>
        <!-- #endif -->
        <!-- #ifdef APP-PLUS -->
        <view class="item" @click="appShare('WXSceneSession')">
          <view class="iconfont icon-weixin3"></view>
          <view class="">{{ $t(`Bạn bè WeChat`) }}</view>
        </view>
        <view class="item" @click="appShare('WXSenceTimeline')">
          <view class="iconfont icon-pengyouquan"></view>
          <view class="">{{ $t(`Khoảnh khắc WeChat`) }}</view>
        </view>
        <!-- #endif -->
        <button class="item" hover-class="none" @click="goPoster">
          <view class="iconfont icon-haibao"></view>
          <view class="">{{ $t(`Tạo poster`) }}</view>
        </button>
      </view>
      <view class="mask" v-if="posters" @click="listenerActionClose"></view>
      <!-- #ifdef MP -->
      <!-- <authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize> -->
      <!-- #endif -->
      <!-- Hiển thị poster -->
      <view class="poster-pop" v-if="posterImageStatus">
        <image
          src="../../static/images/poster-close.png"
          class="close"
          @click="posterImageClose"
        ></image>
        <image class="poster-img" :src="posterImage"></image>
        <!-- #ifndef H5  -->
        <view class="save-poster" @click="savePosterPath">{{
          $t(`Lưu vào điện thoại`)
        }}</view>
        <!-- #endif -->
        <!-- #ifdef H5 -->
        <view class="keep">{{ $t(`Nhấn giữ ảnh để lưu vào điện thoại`) }}</view>
        <!-- #endif -->
      </view>
      <view class="mask" v-if="posterImageStatus"></view>
      <canvas class="canvas" canvas-id="myCanvas" v-if="canvasStatus"></canvas>
      <!-- Gửi ảnh cho bạn bè -->
      <view class="share-box" v-if="H5ShareBox">
        <image
          :src="imgHost + '/statics/images/share-info.png'"
          @click="H5ShareBox = false"
        ></image>
      </view>
      <kefuIcon
        :ids="parseInt(id)"
        :routineContact="routineContact"
        :storeInfo="storeInfo"
        :goodsCon="1"
      ></kefuIcon>
      <!-- #ifdef H5 || APP-PLUS -->
      <zb-code
        ref="qrcode"
        :show="codeShow"
        :cid="cid"
        :val="codeVal"
        :size="size"
        :unit="unit"
        :background="background"
        :foreground="foreground"
        :pdground="pdground"
        :icon="codeIcon"
        :iconSize="iconsize"
        :onval="onval"
        :loadMake="loadMake"
        @result="qrR"
      />
      <!-- #endif -->
      <specs
        ref="specs"
        :specsInfo="storeInfo.params_list"
        @myevent="mySpecs"
      ></specs>
      <!-- Drawer dịch vụ -->
      <serviceModal
        ref="protection"
        :ensureInfo="storeInfo.protection_list"
      ></serviceModal>
    </view>
  </view>
</template>

<script>
let sysHeight = uni.getWindowInfo().statusBarHeight + "px";
import {
  getProductDetail,
  getProductCode,
  collectAdd,
  collectDel,
  postCartAdd,
  realPrice,
} from "@/api/store.js";
import { getUserInfo, userShare } from "@/api/user.js";
import { getCoupons, getThemeInfo } from "@/api/api.js";
import { getCartCounts } from "@/api/order.js";
import { toLogin } from "@/libs/login.js";
import { mapGetters } from "vuex";

import cusPreviewImg from "@/components/cusPreviewImg/index.vue";
import swiperPrevie from "@/components/cusPreviewImg/swiperPrevie.vue";
import couponListWindow from "@/components/couponListWindow";
import productWindow from "@/components/productWindow";
import shareRedPackets from "@/components/shareRedPackets";
import kefuIcon from "@/components/kefuIcon";
import menuIcon from "@/components/menuIcon.vue";
import { updateURLParameter } from "@/utils";
import ClipboardJS from "@/plugin/clipboard/clipboard.js";
// #ifdef MP
import authorize from "@/components/Authorize";
// #endif
// #ifdef APP-PLUS
import { TOKENNAME } from "@/config/app.js";
// #endif
import { HTTP_REQUEST_URL } from "@/config/app";
let app = getApp();
import colors from "@/mixins/color";
import { sharePoster } from "@/mixins/sharePoster";
import homeList from "@/components/homeList";
import specs from "./components/specs/index.vue";
import serviceModal from "./components/serviceModal/index.vue";
import PageDesign from "@/subpackage/diyComponents/pageDesign.vue";
import productBottom from "@/subpackage/diyComponents/productBottom.vue";
export default {
  components: {
    couponListWindow,
    productWindow,
    shareRedPackets,
    kefuIcon,
    menuIcon,
    cusPreviewImg,
    swiperPrevie,
    // #ifdef MP
    authorize,
    // #endif
    homeList,
    specs,
    serviceModal,
    PageDesign,
    productBottom,
  },
  directives: {
    trigger: {
      inserted(el, binging) {
        el.click();
      },
    },
  },
  mixins: [colors, sharePoster],
  data() {
    let that = this;
    return {
      diyData: {},
      imgHost: HTTP_REQUEST_URL,
      sysHeight: sysHeight,
      noGoods: false,
      showSkeleton: true, //Hiện/ẩn skeleton screen
      isNodes: 0, //Kiểm soát thời điểm bắt đầu lấy node phần tử, chỉ cần giá trị thay đổi là lấy lại
      Active: false,
      presale_pay_status: 1,
      //Thuộc tính có mở hay không
      coupon: {
        coupon: false,
        type: -1,
        list: [],
        count: [],
      },
      showAnimate: false,
      showMenuIcon: false,
      attrTxt: this.$t(`Vui lòng chọn`), //Gợi ý trên trang thuộc tính
      attrValue: "", //Thuộc tính đã chọn
      animated: false, //Hiệu ứng giỏ hàng
      id: 0, //id sản phẩm
      replyCount: 0, //Tổng số lượng đánh giá
      reply: [], //Danh sách đánh giá
      storeInfo: {}, //Chi tiết sản phẩm
      productValue: [], //Thuộc tính hệ thống
      couponList: [], //Phiếu giảm giá
      cart_num: 1, //Số lượng mua
      isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
      isShowAuth: false, //Có ẩn ủy quyền hay không
      isOpen: false, //Có mở component thuộc tính hay không
      actionSheetHidden: true,
      posterImageStatus: false,
      storeImage: "", //Ảnh sản phẩm trên poster
      PromotionCode: "", //Ảnh mã QR
      canvasStatus: false, //Thẻ vẽ poster
      posterImage: "", //Đường dẫn poster
      posterbackgd: "/static/images/posterbackgd.png",
      sharePacket: {
        isState: true, //Mặc định không hiển thị
      }, //Chi tiết nhà phân phối
      uid: 0, //uid người dùng
      good_list: [],
      replyChance: 0,
      CartCount: 0,
      ot_price: 0,
      isDown: true,
      storeSelfMention: true,
      posters: false,
      weixinStatus: false,
      attr: {
        cartAttr: false,
        productAttr: [],
        productSelect: {},
      },
      description: "",
      H5ShareBox: false, //Ảnh chia sẻ của OA WeChat
      activity: [],
      navH: "",
      opacity: 0,
      scrollY: 0,
      returnShow: true, //Kiểm tra nút quay lại phía trên có xuất hiện hay không
      diff: "",
      is_money_level: 1,
      is_vip: 0, //Có phải thành viên hay không
      navbarRight: 0,
      homeTop: 20,
      routineContact: 0,
      skuArr: [],
      selectSku: {},
      currentPage: false,
      svip_price_open: 1,
      is_gift: 0, // Có hỗ trợ tặng quà hay không
      isGiftOrder: 0,
      realPriceData: {
        is_vip: 0,
        price: 0,
        real_price: 0,
      },
    };
  },
  computed: {
    ...mapGetters(["isLogin", "cartNum"]),
    isShowPaidVip() {
      let s =
        !this.is_money_level &&
        this.storeInfo.vip_price &&
        this.storeInfo.is_vip;
      return !!s;
    },
  },
  watch: {
    isLogin: {
      handler: function (newV, oldV) {
        if (newV == true) {
          this.getCouponList();
          this.getCartCount();
          this.downloadFilePromotionCode();
          // this.ShareInfo();
        }
      },
      deep: true,
    },
    storeInfo: {
      handler: function () {
        this.$nextTick(() => {});
      },
      immediate: true,
    },
  },
  onLoad(options) {
    let that = this;
    var pages = getCurrentPages();
    that.returnShow = pages.length === 1 ? false : true;
    // #ifdef MP
    that.navH = app.globalData.navHeight;
    // #endif
    // #ifdef H5
    that.navH = 76;
    // #endif
    // #ifdef APP-PLUS
    that.navH = 30;
    // #endif
    that.id = options.id;
    uni.getSystemInfo({
      success: function (res) {
        //res.windowHeight: lấy chiều cao toàn bộ window là px, *2 ra rpx; 98 là chiều cao phần đầu trang chiếm;
        // #ifndef APP-PLUS || H5 || MP-ALIPAY
        that.navbarRight =
          res.windowWidth - uni.getMenuButtonBoundingClientRect().left;
        // #endif
      },
    });
    //Xử lý tham số mang theo khi quét mã
    // #ifdef MP
    if (options.scene) {
      let value = that.$util.getUrlParams(decodeURIComponent(options.scene));
      if (value.id) options.id = value.id;
      //Ghi lại uid người giới thiệu
      if (value.pid) app.globalData.spid = value.pid;
    }
    if (!options.id) {
      this.showSkeleton = false;
      return that.$util.Tips(
        {
          title: that.$t(`Thiếu tham số, không thể xem sản phẩm`),
        },
        {
          tab: 3,
          url: 1,
        }
      );
    } else {
      that.id = options.id;
    }
    // #endif
    that.getGoodsDetails();
    that.getDiyData();
  },
  onReady: function () {
    this.isNodes++;
    this.$nextTick(function () {
      // #ifdef MP
      const menuButton = uni.getMenuButtonBoundingClientRect();
      const query = uni.createSelectorQuery().in(this);
      query
        .select("#home")
        .boundingClientRect((data) => {
          this.homeTop =
            menuButton.top * 2 + menuButton.height - data.height || 0;
        })
        .exec();
      // #endif
      // #ifdef H5
      const clipboard = new ClipboardJS(".copy-data");
      clipboard.on("success", () => {
        this.$util.Tips({
          title: this.$t(`Sao chép thành công`),
        });
      });
      // #endif
    });
  },
  /**
   * Người dùng click chia sẻ ở góc trên phải
   */
  // #ifdef MP
  onShareAppMessage: function () {
    let that = this;
    that.$set(that, "actionSheetHidden", !that.actionSheetHidden);
    userShare();
    return {
      title: that.storeInfo.store_name || "",
      imageUrl: that.storeInfo.image || "",
      path: "/pages/goods_details/index?id=" + that.id + "&spid=" + that.uid,
    };
  },
  onShareTimeline() {
    let that = this;
    userShare();
    return {
      title: that.storeInfo.store_name,
      query: {
        id: that.id,
        spid: that.uid || 0,
      },
      imageUrl: that.storeInfo.image,
    };
  },
  // #endif
  onNavigationBarButtonTap(e) {
    this.currentPage = !this.currentPage;
  },
  onPageScroll(e) {
    let scrollY = e.scrollTop;
    let opacity = scrollY / 200;
    opacity = opacity > 1 ? 1 : opacity;
    this.opacity = opacity;
    this.scrollY = scrollY;
    this.showAnimate = false;
    this.showMenuIcon = false;
    this.currentPage = false;
    uni.$emit("scroll");
  },
  methods: {
    // Menu thao tác
    moreNav() {
      this.currentPage = !this.currentPage;
    },
    onChangeSpecFromPageDesign(item) {
      if (item && item.suk) {
        let values = item.suk.split(",");
        if (
          this.attr.productAttr &&
          this.attr.productAttr.length === values.length
        ) {
          for (let i = 0; i < this.attr.productAttr.length; i++) {
            this.$set(this.attr.productAttr[i], "index", values[i]);
          }
        }
        this.ChangeAttr(item.suk);
      }
    },
    onShowSpecModalFromPageDesign() {
      this.$set(this.attr, "cartAttr", true);
      this.$set(this, "isOpen", true);
    },
    getDiyData() {
      let that = this;
      let previewThemeId = uni.getStorageSync("previewThemeId");
      let data = {};
      if (previewThemeId) data.theme_id = previewThemeId;
      getThemeInfo("detail", data).then((res) => {
        that.diyData = res.data;
      });
    },
    jumpUrl(url) {
      uni.switchTab({
        url,
      });
    },
    videoPause() {
      // this.$nextTick(() => {
      //   this.infoScroll();
      // });
    },
    qrR(res) {
      // #ifdef H5
      if (!this.$wechat.isWeixin() || this.shareQrcode != "1") {
        this.PromotionCode = res;
        this.followCode = "";
      }
      // #endif
      // #ifdef APP-PLUS
      this.PromotionCode = res;
      // #endif
    },
    // Chia sẻ app
    // #ifdef APP-PLUS
    appShare(scene) {
      let that = this;
      let routes = getCurrentPages(); // Lấy mảng route các trang đã từng mở
      let curRoute = routes[routes.length - 1].$page.fullPath; // Lấy route trang hiện tại, tức route trang được mở cuối cùng
      uni.share({
        provider: "weixin",
        scene: scene,
        type: 0,
        href: `${HTTP_REQUEST_URL}${curRoute}&spread=${that.uid}`,
        title: that.storeInfo.store_name,
        summary: that.storeInfo.store_info,
        imageUrl: that.storeInfo.small_image,
        success: function (res) {
          // uni.showToast({
          // 	title: that.$t(`Chia sẻ thành công`),
          // 	icon: "success",
          // });
          that.posters = false;
        },
        fail: function (err) {
          uni.showToast({
            title: that.$t(`Chia sẻ thất bại`),
            icon: "none",
            duration: 2000,
          });
          that.posters = false;
        },
      });
    },
    // #endif
    closeChange: function () {
      this.$set(this.sharePacket, "isState", true);
    },
    boxStatus(data) {
      this.showAnimate = data;
    },
    /**
     * Điền thủ công vào giỏ hàng
     *
     */
    iptCartNum: function (e) {
      if (e) {
        let number = this.storeInfo.min_qty;
        if (
          Number.isInteger(parseInt(e)) &&
          parseInt(e) >= this.storeInfo.min_qty
        ) {
          number = parseInt(e);
        }
        this.$nextTick((e) => {
          this.$set(
            this.attr.productSelect,
            "cart_num",
            e < 0 ? this.storeInfo.min_qty : number
          );
        });
      }
    },
    // quay lại
    returns() {
      // #ifdef H5
      return history.back();
      // #endif
      // #ifndef H5
      return uni.navigateBack({
        delta: 1,
      });
      // #endif
    },
    /*
     *Đến trang chi tiết sản phẩm
     */
    goDetail(item) {
      if (item.activity.length == 0) {
        uni.redirectTo({
          url: "/pages/goods_details/index?id=" + item.id,
        });
        return;
      }
      // Săn giảm giá
      if (item.activity && item.activity.type == 2) {
        uni.redirectTo({
          url: `/pages/activity/goods_bargain_details/index?id=${item.activity.id}&bargain=${this.uid}`,
        });
        return;
      }
      // Mua chung
      if (item.activity && item.activity.type == 3) {
        uni.redirectTo({
          url: `/pages/activity/goods_combination_details/index?id=${item.activity.id}`,
        });
        return;
      }
      // Flash sale
      if (item.activity && item.activity.type == 1) {
        uni.redirectTo({
          url: `/pages/activity/goods_seckill_details/index?id=${item.activity.id}&time_id=${item.activity.time_id}`,
        });
        return;
      }
    },
    // Callback đăng nhập WeChat
    onLoadFun: function (e) {
      // this.getUserInfo();
      // this.get_product_collect();
    },
    ChangCouponsClone: function () {
      this.$set(this.coupon, "coupon", false);
    },
    /*
     * Lấy thông tin người dùng
     */
    getUserInfo: function () {
      let that = this;
      getUserInfo().then((res) => {
        that.$set(that, "uid", res.data.uid);
        that.$set(that, "is_money_level", res.data.is_money_level);
      });
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
      if (productSelect === undefined && !this.attr.productAttr.length)
        productSelect = this.attr.productSelect;
      //Không có giá trị thuộc tính thì tồn kho là 0; không có tăng giảm;
      let stock = productSelect.stock || 0;
      let num = this.attr.productSelect;
      if (
        productSelect === undefined ||
        (this.storeInfo.limit_num &&
          num.cart_num >= this.storeInfo.limit_num &&
          changeValue)
      )
        return;
      if (changeValue) {
        num.cart_num++;
        if (num.cart_num > stock) {
          this.$set(
            this.attr.productSelect,
            "cart_num",
            stock ? stock : this.storeInfo.min_qty
          );
          this.$set(this, "cart_num", stock ? stock : 1);
        }
      } else {
        num.cart_num--;
        if (num.cart_num < 1) {
          this.$set(
            this.attr.productSelect,
            "cart_num",
            this.storeInfo.min_qty
          );
          this.$set(this, "cart_num", 1);
        }
      }
    },
    attrVal(val) {
      this.$set(
        this.attr.productAttr[val.indexw],
        "index",
        this.attr.productAttr[val.indexw].attr_values[val.indexn]
      );
    },
    /**
     * Gán giá trị khi thuộc tính thay đổi
     *
     */
    ChangeAttr: function (res) {
      let productSelect = this.productValue[res];
      if (!productSelect) {
        this.$util.Tips({
          title: this.$t(`Chọn lại`),
          success: () => {
            this.noGoods = true;
            this.attr.productSelect.stock = 0;
            this.attr.productSelect.quota = 0;
            this.attr.productSelect.cart_num = 0;
          },
        });
      } else {
        this.noGoods = false;
      }
      this.$set(this, "selectSku", productSelect);
      if (productSelect && productSelect.stock > 0) {
        this.$set(this.attr.productSelect, "image", productSelect.image);
        // this.$set(this.attr.productSelect, 'price', productSelect.price);
        this.$set(this.attr.productSelect, "stock", productSelect.stock);
        this.$set(this.attr.productSelect, "unique", productSelect.unique);
        this.$set(this.attr.productSelect, "cart_num", this.storeInfo.min_qty);
        // this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
        this.$set(this, "attrValue", res);
        this.$set(this, "attrTxt", this.$t(`Đã chọn`));
        this.setRealPrice(this.storeInfo.id, productSelect.unique);
      } else {
        this.$set(this.attr.productSelect, "image", productSelect.image);
        this.$set(this.attr.productSelect, "price", productSelect.price);
        this.$set(this.attr.productSelect, "stock", 0);
        this.$set(this.attr.productSelect, "unique", "");
        this.$set(this.attr.productSelect, "cart_num", 0);
        this.$set(
          this.attr.productSelect,
          "vip_price",
          this.storeInfo.vip_price
        );
        this.$set(this, "attrValue", "");
        this.$set(this, "attrTxt", this.$t(`Vui lòng chọn`));
      }
    },
    setRealPrice(id, unique) {
      realPrice(id, unique).then((res) => {
        this.realPriceData = res.data;
        this.$set(this.attr.productSelect, "price", res.data.real_price);
        this.$set(this.attr.productSelect, "vip_price", res.data.member_price);
        this.ot_price = res.data.ot_price;
      });
    },
    /**
     * Sau khi nhận xong, ẩn hiển thị phiếu giảm giá đã nhận trên trang hiện tại
     */
    ChangCoupons: function (e) {
      let coupon = e;
      let couponList = this.$util.ArrayRemove(this.couponList, "id", coupon.id);
      this.$set(this, "couponList", couponList);
      this.getCouponList();
    },

    /**
     * Lấy chi tiết sản phẩm
     *
     */
    getGoodsDetails() {
      let that = this;
      uni.showLoading({
        title: "Đang tải",
        mask: true,
      });
      getProductDetail(that.id)
        .then((res) => {
          uni.hideLoading();
          let storeInfo = res.data.storeInfo;
          let good_list = res.data.good_list || [];
          this.is_gift = res.data.storeInfo.is_gift;
          that.$set(that, "storeInfo", storeInfo);
          that.$set(
            that,
            "presale_pay_status",
            res.data.storeInfo.presale_pay_status
          ); // 1 chưa bắt đầu; 2 đang diễn ra; 3 đã kết thúc
          that.$set(that, "reply", res.data.reply ? [res.data.reply] : []);
          that.$set(that, "replyCount", res.data.replyCount);
          that.$set(that, "replyChance", res.data.replyChance);
          that.$set(that.attr, "productAttr", res.data.productAttr);
          that.$set(that, "productValue", res.data.productValue);
          that.$set(that, "is_vip", res.data.storeInfo.is_vip);
          that.$set(that.sharePacket, "priceName", res.data.priceName);
          that.$set(
            that.sharePacket,
            "isState",
            res.data.priceName != 0 ? true : false
          );
          that.$set(that, "storeSelfMention", res.data.store_self_mention);
          that.$set(that, "good_list", good_list);

          if (!storeInfo.wechat_code) {
            // #ifdef H5
            this.codeVal =
              window.location.origin +
              "/pages/goods_details/index?id=" +
              this.id +
              "&spid=" +
              this.$store.state.app.uid;
            // #endif
            // #ifdef APP-PLUS
            this.codeVal =
              HTTP_REQUEST_URL +
              "/pages/goods_details/index?id=" +
              this.id +
              "&spid=" +
              this.$store.state.app.uid;
            // #endif
          } else {
            that.$set(that, "PromotionCode", storeInfo.wechat_code);
          }
          that.$set(
            that,
            "activity",
            res.data.activity ? res.data.activity : []
          );
          that.$set(that, "couponList", res.data.coupons);
          that.$set(
            that,
            "routineContact",
            Number(res.data.routine_contact_type)
          );
          uni.setNavigationBarTitle({
            title: storeInfo.store_name.substring(0, 7) + "...",
          });
          for (let key in res.data.productValue) {
            let obj = res.data.productValue[key];
            that.skuArr.push(obj);
          }
          this.$set(this, "selectSku", that.skuArr[0]);
          that.$set(
            that,
            "diff",
            that.$util.$h.Sub(storeInfo.price, storeInfo.vip_price)
          );
          that.$set(that, "storeImage", that.storeInfo.image);
          that.$set(that, "svip_price_open", res.data.svip_price_open);
          if (that.isLogin) {
            that.getUserInfo();
          }
          // #ifdef H5 || APP-PLUS
          this.getImageBase64();
          // #endif
          // #ifdef H5
          if (that.isLogin) {
            that.ShareInfo();
          }
          // #endif
          this.$nextTick(() => {
            if (good_list.length) {
              // #ifndef APP-PLUS

              // #endif
              // #ifdef APP-PLUS

              setTimeout(() => {}, 1000);
              // #endif
            }
          });
          // #ifndef H5
          that.downloadFilestoreImage();
          // #endif
          if (!res.data.productAttr.length) {
            // Một quy cách
            that.DefaultSelect();
            this.setRealPrice(storeInfo.id, res.data.spec_unique);
          } else {
            // Nhiều quy cách
            that.DefaultSelect();
          }
          that.getCartCount();
          this.showAnimate = true;
        })
        .catch((err) => {
          uni.hideLoading();
          //Trạng thái bất thường thì quay về trang trước
          return that.$util.Tips(
            {
              title: err.toString(),
            },
            {
              tab: 3,
              url: 1,
            }
          );
        });
    },
    infoScroll: function () {
      return;
    },
    /**
     * Thuộc tính được chọn mặc định
     *
     */
    DefaultSelect: function () {
      let productAttr = this.attr.productAttr;
      let value = [];
      if (this.storeInfo.default_sku) {
        value = this.storeInfo.default_sku.split(",");
      } else {
        for (var key in this.productValue) {
          if (this.productValue[key].stock > 0) {
            value = this.attr.productAttr.length ? key.split(",") : [];
            break;
          }
        }
      }
      for (let i = 0; i < productAttr.length; i++) {
        this.$set(productAttr[i], "index", value[i]);
      }
      //sort(); hàm sắp xếp: số - chữ Anh - chữ Hán;
      let productSelect = this.productValue[value.join(",")];
      if (productSelect && productAttr.length) {
        this.$set(
          this.attr.productSelect,
          "store_name",
          this.storeInfo.store_name
        );
        this.$set(this.attr.productSelect, "image", productSelect.image);
        // this.$set(this.attr.productSelect, 'price', productSelect.price);
        this.$set(this.attr.productSelect, "stock", productSelect.stock);
        this.$set(this.attr.productSelect, "unique", productSelect.unique);
        this.$set(this.attr.productSelect, "cart_num", this.storeInfo.min_qty);
        this.$set(this, "attrValue", value.join(","));
        // this.$set(this.attr.productSelect, 'vip_price', productSelect.vip_price);
        this.$set(this, "attrTxt", this.$t(`Đã chọn`));
        this.setRealPrice(this.storeInfo.id, productSelect.unique);
      } else if (!productSelect && productAttr.length) {
        this.$set(
          this.attr.productSelect,
          "store_name",
          this.storeInfo.store_name
        );
        this.$set(this.attr.productSelect, "image", this.storeInfo.image);
        this.$set(this.attr.productSelect, "price", this.storeInfo.price);
        this.$set(this.attr.productSelect, "stock", 0);
        this.$set(this.attr.productSelect, "unique", "");
        this.$set(this.attr.productSelect, "cart_num", 0);
        this.$set(
          this.attr.productSelect,
          "vip_price",
          this.storeInfo.vip_price
        );
        this.$set(this, "attrValue", "");
        this.$set(this, "attrTxt", this.$t(`Vui lòng chọn`));
      } else if (!productSelect && !productAttr.length) {
        this.$set(
          this.attr.productSelect,
          "store_name",
          this.storeInfo.store_name
        );
        this.$set(this.attr.productSelect, "image", this.storeInfo.image);
        this.$set(this.attr.productSelect, "price", this.storeInfo.price);
        this.$set(this.attr.productSelect, "stock", this.storeInfo.stock);
        this.$set(
          this.attr.productSelect,
          "unique",
          this.storeInfo.unique || ""
        );
        this.$set(this.attr.productSelect, "cart_num", this.storeInfo.min_qty);
        this.$set(
          this.attr.productSelect,
          "vip_price",
          this.storeInfo.vip_price
        );
        this.$set(this, "attrValue", "");
        this.$set(this, "attrTxt", this.$t(`Vui lòng chọn`));
      }
    },
    /**
     * Lấy phiếu giảm giá
     *
     */
    getCouponList(type) {
      let that = this,
        obj = {
          page: 1,
          limit: 20,
          product_id: that.id,
        };
      if (type !== undefined || type !== null) {
        obj.type = type;
      }
      getCoupons(obj).then((res) => {
        that.$set(that.coupon, "count", res.data.count);
        if (type === undefined || type === null) {
          let count = [...that.coupon.count],
            indexs = "";
          let index = count.findIndex((item) => item);
          let delCount = that.coupon.count,
            newDelCount = [];
          let countIndex = 0;
          delCount.forEach((item, index) => {
            if (item === 0) {
              countIndex = index;
            } else {
              newDelCount.push(item);
            }
          });
          if (newDelCount.length == 3) {
            indexs = 2;
          } else if (newDelCount.length == 2) {
            if (countIndex === 2) {
              indexs = 1;
            } else {
              indexs = 2;
            }
          } else {
            indexs = delCount.findIndex((item) => item === count[index]);
          }
          that.$set(that.coupon, "type", indexs);
          that.getCouponList(indexs);
        } else {
          that.$set(that.coupon, "list", res.data.list);
        }
      });
    },
    ChangCouponsUseState(index) {
      let that = this;
      that.coupon.list[index].is_use++;
      // that.$set(that.coupon, "list", that.coupon.list);
      that.$set(that.coupon, "coupon", false);
    },
    /**
     *
     *
     * Sản phẩm yêu thích
     */
    setCollect: function () {
      if (this.isLogin === false) {
        toLogin();
      } else {
        let that = this;
        if (this.storeInfo.userCollect) {
          collectDel([this.storeInfo.id]).then((res) => {
            that.$set(
              that.storeInfo,
              "userCollect",
              !that.storeInfo.userCollect
            );
            return that.$util.Tips({
              title: res.msg,
            });
          });
        } else {
          collectAdd(this.storeInfo.id).then((res) => {
            that.$set(
              that.storeInfo,
              "userCollect",
              !that.storeInfo.userCollect
            );
            return that.$util.Tips({
              title: res.msg,
            });
          });
        }
      }
    },
    onShowSpecModalFromPageDesign() {
      this.selecAttr();
    },
    onChangeSpecFromPageDesign(item) {
      if (item && item.suk) {
        let values = item.suk.split(",");
        if (
          this.attr.productAttr &&
          this.attr.productAttr.length === values.length
        ) {
          for (let i = 0; i < this.attr.productAttr.length; i++) {
            this.$set(this.attr.productAttr[i], "index", values[i]);
          }
        }
        this.ChangeAttr(item.suk);
      }
    },
    /**
     * Mở plugin thuộc tính
     */
    selecAttr: function () {
      // this.$refs.proSwiper.videoIsPause();
      this.$set(this.attr, "cartAttr", true);
      this.$set(this, "isOpen", true);
    },
    openModal(ref) {
      this.$refs[ref].isShow = true;
    },
    /**
     * Mở plugin phiếu giảm giá
     */
    couponTap: function () {
      let that = this;
      if (that.isLogin === false) {
        toLogin();
      } else {
        // this.$refs.proSwiper.videoIsPause();
        that.getCouponList();
        that.$set(that.coupon, "coupon", true);
      }
    },
    goActivity(item) {
      if (item.type === "1" && this.$permission("seckill")) {
        uni.navigateTo({
          url: `/pages/activity/goods_seckill_details/index?id=${item.id}&time_id=${item.time_id}`,
        });
      } else if (item.type === "2" && this.$permission("bargain")) {
        uni.navigateTo({
          url: `/pages/activity/goods_bargain_details/index?id=${item.id}&bargain=${this.uid}`,
        });
      } else if (item.type === "3" && this.$permission("combination")) {
        uni.navigateTo({
          url: `/pages/activity/goods_combination_details/index?id=${item.id}`,
        });
      }
    },
    onMyEvent: function () {
      this.$set(this.attr, "cartAttr", false);
      this.$set(this, "isOpen", false);
      this.isGiftOrder = 0;
    },
    /**
     * Mở thuộc tính để thêm vào giỏ hàng
     *
     */
    joinCart: function (e) {
      //Có đăng nhập hay không
      if (this.isLogin === false) {
        toLogin();
      } else {
        // this.$refs.proSwiper.videoIsPause();
        this.goCat();
      }
    },
    goCart() {
      uni.reLaunch({
        url: "/pages/order_addcart/order_addcart",
      });
    },
    /*
     * Thêm vào giỏ hàng
     */
    goCat(news) {
      let that = this,
        productSelect = that.productValue[this.attrValue];
      that.currentPage = false;
      //Mở thuộc tính
      if (that.attrValue) {
        //Đã chọn thuộc tính mặc định nhưng chưa từng mở popup thuộc tính thì vẫn tự động mở để người dùng xem thuộc tính được chọn mặc định
        that.attr.cartAttr = !that.isOpen ? true : false;
      } else {
        if (that.isOpen) that.attr.cartAttr = true;
        else that.attr.cartAttr = !that.attr.cartAttr;
      }
      //Chỉ thêm vào giỏ hàng khi đóng popup thuộc tính
      if (that.attr.cartAttr === true && that.isOpen === false)
        return (that.isOpen = true);
      //Nếu có thuộc tính mà chưa chọn, nhắc người dùng chọn
      if (
        that.attr.productAttr.length &&
        productSelect === undefined &&
        that.isOpen === true
      )
        return that.$util.Tips({
          title: that.$t(`Sản phẩm không đủ tồn kho, vui lòng chọn thuộc tính khác`),
        });
      if (that.attr.productSelect.cart_num <= 0) {
        that.attr.productSelect.cart_num = 1;
        that.isOpen = false;
        return that.$util.Tips({
          title: that.$t(`Vui lòng chọn số lượng`),
        });
      }
      let q = {
        productId: that.id,
        cartNum: that.attr.productSelect.cart_num,
        new: news === undefined ? 0 : 1,
        uniqueId:
          that.attr.productSelect !== undefined
            ? that.attr.productSelect.unique
            : "",
        virtual_type: that.storeInfo.virtual_type,
      };
      postCartAdd(q)
        .then((res) => {
          that.isOpen = false;
          that.attr.cartAttr = false;
          if (news) {
            let url =
              "/pages/goods/order_confirm/index?new=1&cartId=" +
              res.data.cartId;
            if (this.isGiftOrder) url += "&is_gift=" + this.isGiftOrder;
            uni.navigateTo({
              url,
            });
          } else {
            that.$util.Tips({
              title: that.$t(`Thêm thành công`),
              success: () => {
                that.getCartCount(true);
              },
            });
          }
          this.isGiftOrder = 0;
        })
        .catch((err) => {
          that.isOpen = false;
          return that.$util.Tips({
            title: err,
          });
        });
    },
    /**
     * Lấy số lượng giỏ hàng
     * @param boolean Có hiển thị hiệu ứng giỏ hàng và reset thuộc tính hay không
     */
    getCartCount: function (isAnima) {
      let that = this;
      const isLogin = that.isLogin;
      if (isLogin) {
        getCartCounts().then((res) => {
          that.CartCount = res.data.count;
          this.$store.commit(
            "indexData/setCartNum",
            that.CartCount > 99 ? "..." : that.CartCount + ""
          );
          // uni.setTabBarBadge({
          // 	index: Number(uni.getStorageSync('FOOTER_ADDCART')) || 2,
          // 	text: that.CartCount + ''
          // })
          //Reset thuộc tính sau khi thêm vào giỏ hàng
          if (isAnima) {
            that.animated = true;
            setTimeout(function () {
              that.animated = false;
            }, 500);
          }
        });
      }
    },
    goGift() {
      this.isGiftOrder = 1;
      this.goBuy();
    },
    /**
     * Mua ngay
     */
    goBuy() {
      if (this.isLogin === false) {
        toLogin();
      } else {
        // this.$refs.proSwiper.videoIsPause();
        this.goCat(true);
      }
    },
    open(data) {
      this.showMenuIcon = data;
    },
    // Đóng ủy quyền
    authColse: function (e) {
      this.isShowAuth = e;
    },
    /**
     * Mở chia sẻ
     *
     */
    listenerActionSheet() {
      this.currentPage = false;
      // #ifdef H5
      if (this.$wechat.isWeixin()) {
        this.weixinStatus = true;
      }
      // #endif

      this.posters = true;
    },
    // Đóng chia sẻ
    listenerActionClose: function () {
      this.posters = false;
      this.posterImageStatus = false;
    },
    //Ẩn poster
    posterImageClose: function () {
      this.posterImageStatus = false;
    },

    // Đóng popup chia sẻ của Mini Program;
    goFriend: function () {
      this.posters = false;
    },
    /*
     * Lưu vào album điện thoại
     */
    // #ifdef MP
    savePosterPath: function () {
      let that = this;
      uni.getSetting({
        success(res) {
          if (!res.authSetting["scope.writePhotosAlbum"]) {
            uni.authorize({
              scope: "scope.writePhotosAlbum",
              success() {
                uni.saveImageToPhotosAlbum({
                  filePath: that.posterImage,
                  success: function (res) {
                    that.posterImageClose();
                    that.$util.Tips({
                      title: that.$t(`Lưu thành công`),
                      icon: "success",
                    });
                  },
                  fail: function (res) {
                    that.$util.Tips({
                      title: that.$t(`Lưu thất bại`),
                    });
                  },
                });
              },
            });
          } else {
            uni.saveImageToPhotosAlbum({
              filePath: that.posterImage,
              success: function (res) {
                that.posterImageClose();
                that.$util.Tips({
                  title: that.$t(`Lưu thành công`),
                  icon: "success",
                });
              },
              fail: function (res) {
                that.$util.Tips({
                  title: that.$t(`Lưu thất bại`),
                });
              },
            });
          }
        },
      });
    },
    // #endif
    //#ifdef APP-PLUS
    savePosterPath() {
      let that = this;
      uni.saveImageToPhotosAlbum({
        filePath: that.posterImage,
        success: function (res) {
          that.posterImageClose();
          that.$util.Tips({
            title: that.$t(`Lưu thành công`),
            icon: "success",
          });
        },
        fail: function (res) {
          that.$util.Tips({
            title: that.$t(`Lưu thất bại`),
          });
        },
      });
    },
    // #endif
    //#ifdef H5
    ShareInfo() {
      let data = this.storeInfo;
      let href = location.href;
      if (this.$wechat.isWeixin()) {
        getUserInfo().then((res) => {
          href =
            href.indexOf("?") === -1
              ? href + "?spread=" + res.data.uid
              : updateURLParameter(href, "spread", res.data.uid);
          let configAppMessage = {
            desc: data.store_info,
            title: data.store_name,
            link: href,
            imgUrl: data.image,
          };
          this.$wechat
            .wechatEvevt(
              [
                "updateAppMessageShareData",
                "updateTimelineShareData",
                "onMenuShareAppMessage",
                "onMenuShareTimeline",
              ],
              configAppMessage
            )
            .then((res) => {})
            .catch((err) => {});
        });
      }
    },
    //#endif
    tabCouponType: function (type) {
      this.$set(this.coupon, "type", type);
      this.getCouponList(type);
    },
    //Click ảnh sku để mở banner trình chiếu
    showImg(index) {
      this.$refs.cusPreviewImg.open(this.selectSku.suk);
    },
    showSwiperImg(index) {
      this.$refs.cusSwiperImg.open(index);
    },
    //Vuốt banner trình chiếu để chọn sản phẩm
    changeSwitch(e) {
      const productSelect = this.skuArr[e];
      if (!productSelect) return;

      this.$set(this, "selectSku", productSelect);

      const skuList = productSelect.suk.split(",");

      // Dùng vòng lặp để đặt index cho tất cả productAttr, tránh lặp code
      skuList.forEach((sku, index) => {
        if (this.attr.productAttr[index]) {
          this.$set(this.attr.productAttr[index], "index", sku);
        }
      });

      // Cập nhật hàng loạt thuộc tính productSelect
      const selectProps = ["image", "price", "stock", "unique", "vipPrice"];
      selectProps.forEach((prop) => {
        this.$set(this.attr.productSelect, prop, productSelect[prop]);
      });

      this.$set(this, "attrTxt", this.$t(`Đã chọn`));
      this.$set(this, "attrValue", productSelect.suk);
    },
    bindSortId(data) {
      if (data.dataType.tabVal == 1) {
        uni.navigateTo({
          url: `/pages/goods/goods_list/index?cid=${data.classPage.id}&title=${data.classPage.name}`,
        });
      } else if (data.text.val == 'Trang chủ') {
        uni.switchTab({
          url: `/pages/index/index`,
        });
      } else {
        uni.navigateTo({
          url: `/pages/annex/special/index?theme_id=${data.microPage.id}`,
        });
      }
    },
  },
};
</script>

<style scoped lang="scss">
.iconfonts {
  color: #fff !important;
  font-size: 28rpx;
}

.mask {
  z-index: 300 !important;
}

.head-bar {
  background: #fff;
}

.generate-posters {
  width: 100%;
  height: 170rpx;
  background-color: #fff;
  position: fixed;
  left: 0;
  bottom: 0;
  z-index: 388;
  transform: translate3d(0, 100%, 0);
  transition: all 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
  border-top: 1rpx solid #eee;
}

.generate-posters.on {
  transform: translate3d(0, 0, 0);
}

.generate-posters .item {
  flex: 1;
  text-align: center;
  font-size: 30rpx;
}

.generate-posters .item .iconfont {
  font-size: 80rpx;
  color: #5eae72;
}

.generate-posters .item .iconfont.icon-haibao {
  color: #5391f1;
}

.generate-posters .item .iconfont.icon-haowuquan1 {
  color: #ff954d;
}

// .product-con .footer {
//   padding: 0 20rpx 0 30rpx;
//   position: fixed;
//   bottom: 0;
//   width: 100%;
//   box-sizing: border-box;
//   background-color: rgba(255, 255, 255, 0.85);
//   backdrop-filter: blur(10px);
//   z-index: 277;
//   border-top: 1rpx solid #f0f0f0;
//   height: 100rpx;
//   height: calc(100rpx + constant(safe-area-inset-bottom)); ///tương thích IOS<11.2/
//   height: calc(100rpx + env(safe-area-inset-bottom)); ///tương thích IOS>11.2/
//   transform: translate3d(0, 100%, 0);
//   transition: all 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
//   .gift-icon {
//     width: 40rpx;
//     height: 40rpx;
//     margin: 5rpx 0 5rpx;
//   }
// }

// .product-con .footer .item {
//   display: flex;
//   flex-direction: column;
//   justify-content: center;
//   align-items: center;
//   font-size: 18rpx;
//   color: #666;
// }

// .product-con .footer .item .iconfont {
//   text-align: center;
//   font-size: 40rpx;
// }

// .product-con .footer .item .iconfont.icon-shoucang1 {
//   color: var(--view-theme);
// }

// .product-con .footer .item .iconfont.icon-gouwuche1 {
//   font-size: 40rpx;
//   position: relative;
// }

// .product-con .footer .item .iconfont.icon-gouwuche1 .num {
//   color: #fff;
//   position: absolute;
//   font-size: 18rpx;
//   padding: 2rpx 10rpx 3rpx;
//   border-radius: 200rpx;
//   top: -10rpx;
//   right: -10rpx;
// }

.virbnt {
  // width: 444rpx !important;
  height: 76rpx !important;
  border-radius: 50rpx !important;
  overflow: hidden;
}

.virbnts {
  width: 444rpx !important;
  text-align: center;
  line-height: 76rpx;
  color: #fff;
  font-size: 28rpx;
  background-color: var(--view-bntColor);
  border-radius: 50rpx !important;
}

.product-con .footer .bnt {
  width: 444rpx;
  height: 76rpx;
}

.product-con .footer .bnt .bnts {
  width: 222rpx;
  text-align: center;
  line-height: 76rpx;
  color: #fff;
  font-size: 28rpx;
}

.product-con .footer .bnt .joinCart {
  border-radius: 50rpx 0 0 50rpx;
  background-color: var(--view-bntColor);
  // background-image: linear-gradient(to right, #fea10f 0%, #fa8013 100%);
}

.product-con .footer .bnt .buy {
  border-radius: 0 50rpx 50rpx 0;
  background-color: var(--view-theme);
  // background-image: linear-gradient(to right, #fa6514 0%, #e93323 100%);
}

button {
  padding: 0;
  margin: 0;
  line-height: normal;
  background-color: #fff;
}

button::after {
  border: 0;
}

action-sheet-item {
  padding: 0;
  height: 240rpx;
  align-items: center;
  display: flex;
}

.contact {
  font-size: 16px;
  width: 50%;
  background-color: #fff;
  padding: 8rpx 0;
  border-radius: 0;
  margin: 0;
  line-height: 2;
}

.contact::after {
  border: none;
}

.action-sheet {
  font-size: 17px;
  line-height: 1.8;
  width: 50%;
  position: absolute;
  top: 0;
  right: 0;
  padding: 25rpx 0;
}

.canvas {
  z-index: 300;
  width: 750px;
  height: 1190px;
}

.poster-pop {
  width: 450rpx;
  height: 714rpx;
  position: fixed;
  left: 50%;
  transform: translateX(-50%);
  z-index: 399;
  top: 50%;
  margin-top: -377rpx;
  .poster-img {
    border-radius: 6px;
  }
}

.poster-pop image {
  width: 100%;
  height: 100%;
  display: block;
}

.poster-pop .close {
  width: 46rpx;
  height: 75rpx;
  position: fixed;
  right: 0;
  top: -73rpx;
  display: block;
}

.poster-pop .save-poster {
  background-color: #df2d0a;
  font-size: ：22rpx;
  color: #fff;
  text-align: center;
  height: 76rpx;
  line-height: 76rpx;
  width: 100%;
  border-radius: 40rpx;
  margin-top: 28rpx;
}

.poster-pop .keep {
  color: #fff;
  text-align: center;
  font-size: 25rpx;
  margin-top: 10rpx;
}

.mask {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.6);
  z-index: 9;
}

.navbar .header {
  height: 96rpx;
  font-size: 30rpx;
  color: #050505;
  background-color: #fff;
  /* #ifdef APP-PLUS */
  width: 100%;
  /* #endif */
}

.home {
  /* #ifdef H5 */
  top: 20rpx !important;
  /* #endif */
}

.navbar .header .item {
  position: relative;
  margin: 0 25rpx;
}

.navbar .header .item.on:before {
  position: absolute;
  width: 60rpx;
  height: 5rpx;
  background-repeat: no-repeat;
  content: "";
  // background-image: linear-gradient(to right, #ff3366 0%, #ff6533 100%);
  background-color: var(--view-theme);
  bottom: -10rpx;
  left: 50%;
  margin-left: -28rpx;
}

.navbar {
  position: fixed;
  background-color: #fff;
  top: 0;
  left: 0;
  z-index: 99;
  width: 100%;
}

.navbar .navbarH {
  position: relative;
}

.navbar .navbarH .navbarCon {
  position: absolute;
  bottom: 0;
  height: 100rpx;
  width: 100%;
  /* #ifndef APP-PLUS || H5 || MP-ALIPAY */
  // justify-content: flex-end;
  padding-left: 48px;
  /* #endif */
}

.home {
  color: #333;
  position: fixed;
  /* #ifdef MP */
  width: 126rpx;
  left: 15rpx;
  /* #endif */
  /* #ifndef MP */
  width: 56rpx;
  left: 33rpx;
  /* #endif */
  height: 56rpx;
  z-index: 99;
  background: rgba(255, 255, 255, 0.3);
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 40rpx;
  font-size: 33rpx;

  &.right {
    right: 33rpx;
    left: unset;
  }

  &.on {
    background: unset;
    color: #333;
  }

  &.homeIndex {
    /* #ifdef MP */
    width: 98rpx;
    /* #endif */
    /* #ifndef MP */
    border-color: rgba(255, 255, 255, 0);
    /* #endif */
  }
}
.home .iconfont {
  width: 58rpx;
  text-align: center;
}

.home .line {
  width: 1rpx;
  height: 34rpx;
  background: #b3b3b3;
}

.home .icon-xiangzuo {
  font-size: 28rpx;
}

.share-box {
  z-index: 1000;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;

  image {
    width: 100%;
    height: 100%;
  }
}

.presale .bnts {
  width: 444rpx;
  height: 76rpx;
  border-radius: 50rpx 50rpx;
  background-color: var(--view-theme);
  text-align: center;
  line-height: 76rpx;
  color: #fff;
  font-size: 28rpx;
}

.delete-line {
  text-decoration: line-through;
}
</style>
