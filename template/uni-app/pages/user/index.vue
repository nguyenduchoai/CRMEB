<template>
  <view class="new-users copy-data" :style="{ height: pageHeight }">
    <view class="top" :style="colorStyle">
      <!-- #ifdef MP || APP-PLUS -->
      <view class="sys-head">
        <view class="sys-bar" :style="{ height: sysHeight }"></view>
        <!-- #ifdef MP -->
        <view
          class="sys-title"
          :style="member_style == 3 ? 'color:#333' : ''"
          >{{ $t("Trang cá nhân") }}</view
        >
        <!-- #endif -->
        <view
          class="bg"
          :style="member_style == 3 ? 'background:#f5f5f5' : ''"
        ></view>
      </view>
      <!-- #endif -->
    </view>

    <PageDesign
      :style="colorStyle"
      :diyData="currentDiyData"
      :isHome="false"
      :isScrolled="isScrolled"
      :isFixed="isFixed"
      :belongIndex="belongIndex"
      @bindSortId="bindSortId"
      @bindHeight="bindHeighta"
      @storeTap="storeTap"
      @changeLogin="changeLogin"
      @changeBarg="changeBarg"
      @goDetail="goDetail"
    ></PageDesign>
    <image :src="copyRightPic" alt="" class="support"></image>
    <editUserModal
      :isShow="editModal"
      @closeEdit="closeEdit"
      @editSuccess="editSuccess"
    ></editUserModal>
    <pageFooter :style="colorStyle"></pageFooter>
  </view>
</template>
<script>
let sysHeight = uni.getWindowInfo().statusBarHeight + "px";
import {
  getMenuList,
  getUserInfo,
  setVisit,
  mpBindingPhone,
} from "@/api/user.js";
import { getThemeInfo } from "@/api/api.js";
import { wechatAuthV2, silenceAuth } from "@/api/public.js";
import { toLogin } from "@/libs/login.js";
import { mapState, mapGetters } from "vuex";
// #ifdef H5
import Auth from "@/libs/wechat";
// #endif
const app = getApp();
import dayjs from "@/plugin/dayjs/dayjs.min.js";
import Routine from "@/libs/routine";
import colors from "@/mixins/color";
import pageFooter from "@/components/pageFooter/index.vue";
import { getCustomer } from "@/utils/index.js";
import editUserModal from "@/components/eidtUserModal/index.vue";
import couponWindow from "@/components/couponWindow/index";
import waterfallsFlow from "@/components/WaterfallsFlow/WaterfallsFlow.vue";
import emptyPage from "@/components/emptyPage.vue";
import Loading from "@/components/Loading/index.vue";
import { getCrmebCopyRight } from "@/api/api.js";
import { goShopDetail } from "@/libs/order.js";
import PageDesign from "@/subpackage/diyComponents/pageDesign.vue";

export default {
  components: {
    pageFooter,
    editUserModal,
    PageDesign,
    couponWindow,
    waterfallsFlow,
    emptyPage,
    Loading,
  },
  // computed: mapGetters(['isLogin','cartNum']),
  computed: {
    pdHeights() {
      return { height: "100rpx" };
    },
    ...mapGetters({
      cartNum: "cartNum",
      isLogin: "isLogin",
    }),
  },
  filters: {
    coundTime(val) {
      var setTime = val * 1000;
      var nowTime = new Date();
      var rest = setTime - nowTime.getTime();
      var day = parseInt(rest / (60 * 60 * 24 * 1000));
      // var hour = parseInt(rest/(60*60*1000)%24) //giờ
      return day + this.$t("day");
    },
    dateFormat: function (value) {
      return dayjs(value * 1000).format("YYYY-MM-DD");
    },
  },
  mixins: [colors],
  data() {
    return {
      currentDiyData: {},
      editModal: false, // Sửa thông tin ảnh đại diện
      storeMenu: [], // Quản lý cửa hàng
      orderMenu: [
        {
          img: "icon-daifukuan",
          title: "Chờ thanh toán",
          url: "/pages/goods/order_list/index?status=0",
        },
        {
          img: "icon-daifahuo",
          title: "Chờ giao hàng",
          url: "/pages/goods/order_list/index?status=1",
        },
        {
          img: "icon-daishouhuo",
          title: "Chờ nhận hàng",
          url: "/pages/goods/order_list/index?status=2",
        },
        {
          img: "icon-daipingjia",
          title: "Chờ đánh giá",
          url: "/pages/goods/order_list/index?status=3",
        },
        {
          img: "icon-a-shouhoutuikuan",
          title: "Đổi trả/Hoàn tiền",
          url: "/pages/users/user_return_list/index",
        },
      ],
      imgUrls: [],
      autoplay: true,
      circular: true,
      interval: 3000,
      duration: 500,
      isAuto: false, //Chưa ủy quyền thì sẽ không tự động ủy quyền
      isShowAuth: false, //Có ẩn ủy quyền hay không
      orderStatusNum: {},
      userInfo: {},
      MyMenus: [],
      sysHeight: sysHeight,
      mpHeight: 0,
      showStatus: 1,
      activeRouter: "",
      // #ifdef H5 || MP
      pageHeight: "100%",
      routineContact: 0,
      // #endif
      // #ifdef APP-PLUS
      pageHeight: app.globalData.windowHeight,
      // #endif
      // #ifdef H5
      isWeixin: Auth.isWeixin(),
      //#endif
      footerSee: false,
      my_menus_status: 0,
      business_status: 0,
      member_style: 0,
      my_banner_status: 0,
      is_diy: uni.getStorageSync("is_diy"),
      copyRightPic: require("static/images/support.png"), //Ảnh bản quyền
      belongIndex: 0,
      isScrolled: false,
      isFixed: false,
      product_video_status: false,
      positionTop: 0,
      sortMpTop: 0,
      sortList: [],
      curSort: 0,
      goodList: [],
      loaded: false,
      loading: false,
      isCouponShow: false,
      couponObj: {},
      site_config: "",
      configData: {},
      isFooter: false,
      entryData: { select_store_id: "", store_id: "" },
      sid: 0,
      goodPage: 1,
    };
  },
  onLoad(option) {
    uni.hideTabBar();
    let that = this;
    // #ifdef MP
    // Ủy quyền ngầm Mini Program
    if (!this.$store.getters.isLogin) {
      // Routine.getCode()
      // 	.then(code => {
      // 		Routine.silenceAuth(code).then(res => {
      // 			this.onLoadFun();
      // 		})
      // 	})
      // 	.catch(res => {
      // 		uni.hideLoading();
      // 	});
    }
    // #endif

    // #ifdef H5 || APP-PLUS
    // if (that.isLogin == false) {
    // 	toLogin();
    // }
    //Sau khi lấy thông tin người dùng về thì ủy quyền
    let cacheCode = this.$Cache.get("snsapi_userinfo_code");
    let res1 = cacheCode ? option.code != cacheCode : true;
    if (
      this.isWeixin &&
      option.code &&
      res1 &&
      option.scope === "snsapi_userinfo"
    ) {
      this.$Cache.set("snsapi_userinfo_code", option.code);
      Auth.auth(option.code)
        .then((res) => {
          this.getUserInfo();
        })
        .catch((err) => {});
    }
    // #endif
    // #ifdef APP-PLUS
    that.$set(that, "pageHeight", app.globalData.windowHeight);
    // #endif

    let routes = getCurrentPages(); // Lấy mảng route các trang đã từng mở
    let curRoute = routes[routes.length - 1].route; //Lấy route trang hiện tại
    this.activeRouter = "/" + curRoute;
    this.getCopyRight();
  },
  onReady() {
    let self = this;
    // #ifdef MP
    let info = uni.createSelectorQuery().select(".sys-head");
    info
      .boundingClientRect(function (data) {
        //data - các loại tham số
        self.mpHeight = data.height;
      })
      .exec();
    // #endif
  },
  onShow: function () {
    let that = this;
    // #ifdef APP-PLUS
    uni.getSystemInfo({
      success: function (res) {
        that.pageHeight = res.windowHeight + "px";
      },
    });
    // #endif
    if (that.isLogin) {
      this.getUserInfo();
      this.setVisit();
    }
    this.getMyMenus();
    this.getDiyData();
    this.getCopyRight();
  },
  onPullDownRefresh() {
    this.onLoadFun();
  },
  onPageScroll() {
    uni.$emit("scroll");
  },
  methods: {
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
    storeTap(id) {
      this.entryData.select_store_id = id;
      this.entryData.store_id = "";
      uni.removeStorageSync("rulesStoreId");
    },
    bindHeighta(data) {
      // #ifdef APP-PLUS
      this.sortMpTop = data.top + data.height;
      // #endif
    },
    changeSort(item, index) {
      if (this.curSort == index) return;
      this.curSort = index;
      this.sid = item.id;
      this.goodList = [];
      this.goodPage = 1;
      this.loaded = false;
      if (this.getGoodsList) this.getGoodsList();
    },
    goDetail(item) {
      goShopDetail(item, this.$store.state.app.uid).then((res) => {
        uni.navigateTo({
          url: `/pages/goods_details/index?id=${item.id}`,
        });
      });
    },
    couponClose() {
      this.isCouponShow = false;
      if (!uni.getStorageSync("oldUser") && this.getNewCoupon) {
        this.getNewCoupon();
      }
    },
    goICP(url) {
      // #ifdef H5
      window.open(url);
      // #endif
      // #ifdef MP
      uni.navigateTo({
        url: `/pages/annex/web_view/index?url=${url}`,
      });
      // #endif
    },
    changeBarg(item) {
      if (!this.isLogin) {
        toLogin();
      } else {
        uni.navigateTo({
          url: `/pages/activity/goods_bargain_details/index?id=${item.id}&spid=${this.$store.state.app.uid}`,
        });
      }
    },
    changeLogin() {
      if (!this.isLogin) {
        toLogin();
      }
    },
    objToArr(data) {
      if (!data || typeof data !== "object") return [];
      let obj = Object.keys(data).sort();
      let m = obj.map((key) => data[key]);
      return m;
    },
    getDiyData() {
      let previewThemeId = uni.getStorageSync("previewThemeId");
      let data = {};
      if (previewThemeId) data.theme_id = previewThemeId;
      getThemeInfo("user", data).then((res) => {
        this.currentDiyData = res.data;
      });
    },
    getWechatuserinfo() {
      //#ifdef H5
      Auth.isWeixin() && Auth.toAuth("snsapi_userinfo", "/pages/user/index");
      //#endif
    },
    editSuccess() {
      this.editModal = false;
      this.getUserInfo();
    },
    closeEdit() {
      this.editModal = false;
    },
    // Ghi lại lượt truy cập của thành viên
    setVisit() {
      setVisit({
        url: "/pages/user/index",
      }).then((res) => {});
    },
    // Mở ủy quyền
    openAuto() {
      toLogin();
    },
    // Callback ủy quyền
    onLoadFun() {
      this.getUserInfo();
      this.getMyMenus();
      this.getDiyData();
      this.setVisit();
    },
    Setting: function () {
      uni.openSetting({
        success: function (res) {},
      });
    },
    // Đóng ủy quyền
    authColse: function (e) {
      this.isShowAuth = e;
    },
    // Liên kết số điện thoại
    bindPhone() {
      uni.navigateTo({
        url: "/pages/users/user_phone/index",
      });
    },
    getphonenumber(e) {
      if (e.detail.errMsg == "getPhoneNumber:ok") {
        Routine.getCode()
          .then((code) => {
            let data = {
              code,
              iv: e.detail.iv,
              encryptedData: e.detail.encryptedData,
            };
            mpBindingPhone(data)
              .then((res) => {
                this.getUserInfo();
                this.$util.Tips({
                  title: res.msg,
                  icon: "success",
                });
              })
              .catch((err) => {
                return this.$util.Tips({
                  title: err,
                });
              });
          })
          .catch((error) => {
            uni.hideLoading();
          });
      }
    },
    /**
     * Lấy thông tin người dùng cá nhân
     */
    getUserInfo: function () {
      let that = this;
      getUserInfo().then((res) => {
        that.userInfo = res.data;
        this.$store.commit("UPDATE_USERINFO", res.data);
        that.$store.commit("SETUID", res.data.uid);
        that.orderMenu.forEach((item, index) => {
          switch (item.title) {
            case "Chờ thanh toán":
              item.num = res.data.orderStatusNum.unpaid_count;
              break;
            case "Chờ giao hàng":
              item.num = res.data.orderStatusNum.unshipped_count;
              break;
            case "Chờ nhận hàng":
              item.num = res.data.orderStatusNum.received_count;
              break;
            case "Chờ đánh giá":
              item.num = res.data.orderStatusNum.evaluated_count;
              break;
            case "Đổi trả/Hoàn tiền":
              item.num = res.data.orderStatusNum.refunding_count;
              break;
          }
        });
        uni.stopPullDownRefresh();
      });
    },
    //API ủy quyền Mini Program thay thế getUserInfo
    getUserProfile() {
      toLogin();
    },
    /**
     *
     * Lấy icon trang cá nhân
     */
    switchTab(order) {
      this.orderMenu.forEach((item, index) => {
        switch (item.title) {
          case "Chờ thanh toán":
            item.img = order.dfk;
            break;
          case "Chờ giao hàng":
            item.img = order.dfh;
            break;
          case "Chờ nhận hàng":
            item.img = order.dsh;
            break;
          case "Chờ đánh giá":
            item.img = order.dpj;
            break;
          case "Đổi trả/Hoàn tiền":
            item.img = order.sh;
            break;
        }
      });
    },
    getMyMenus: function () {
      let that = this;
      // if (this.MyMenus.length) return;
      getMenuList().then((res) => {
        this.member_style = Number(res.data.diy_data.value);
        this.my_banner_status = res.data.diy_data.my_banner_status;
        this.my_menus_status = res.data.diy_data.my_menus_status;
        this.business_status = res.data.diy_data.business_status;
        let storeMenu = [];
        let myMenu = [];
        res.data.routine_my_menus.forEach((el, index, arr) => {
          if (
            el.url == "/pages/admin/order/index" ||
            el.url == "/pages/admin/order_cancellation/index" ||
            el.url == "/pages/admin/manage/index" ||
            el.name == "Tiếp nhận CSKH"
          ) {
            storeMenu.push(el);
          } else {
            myMenu.push(el);
          }
        });

        let order01 = {
          dfk: "icon-daifukuan",
          dfh: "icon-daifahuo",
          dsh: "icon-daishouhuo",
          dpj: "icon-daipingjia",
          sh: "icon-a-shouhoutuikuan",
        };
        let order02 = {
          dfk: "icon-daifukuan-lan",
          dfh: "icon-daifahuo-lan",
          dsh: "icon-daishouhuo-lan",
          dpj: "icon-daipingjia-lan",
          sh: "icon-shouhoutuikuan-lan",
        };
        let order03 = {
          dfk: "icon-daifukuan-ju",
          dfh: "icon-daifahuo-ju",
          dsh: "icon-daishouhuo-ju",
          dpj: "icon-daipingjia-ju",
          sh: "icon-shouhoutuikuan-ju",
        };
        let order04 = {
          dfk: "icon-daifukuan-fen",
          dfh: "icon-daifahuo-fen",
          dsh: "icon-daishouhuo-fen",
          dpj: "icon-daipingjia-fen",
          sh: "icon-shouhoutuikuan-fen",
        };
        let order05 = {
          dfk: "icon-daifukuan-lv",
          dfh: "icon-daifahuo-lv",
          dsh: "icon-daishouhuo-lv",
          dpj: "icon-daipingjia-lv",
          sh: "icon-shouhoutuikuan-lv",
        };
        if (this.member_style == 1) {
          this.switchTab(order01);
        } else if (this.member_style == 2) {
          this.switchTab(order02);
        } else if (this.member_style == 3) {
          this.switchTab(order03);
        } else if (this.member_style == 4) {
          this.switchTab(order04);
        } else if (this.member_style == 5) {
          this.switchTab(order05);
        }
        this.storeMenu = storeMenu;
        this.MyMenus = myMenu;
      });
    },
    getCopyRight() {
      getCrmebCopyRight().then((res) => {
        if (res.data.copyrightImage)
          this.copyRightPic = res.data.copyrightImage;
      });
    },
  },
};
</script>

<style lang="scss">
.new-users {
  padding-bottom: 100rpx;
  .sys-head {
    position: relative;
    width: 100%;
    background: #fff;
    z-index: 50;

    .sys-bar {
      width: 100%;
    }

    .sys-title {
      width: 100%;
      height: 43px;
      line-height: 43px;
      text-align: center;
      font-size: 32rpx;
      color: #000;
      font-weight: 500;
      position: relative;
      z-index: 10;
    }

    .bg {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      z-index: 5;
    }
  }
  .support {
    width: 219rpx;
    height: 74rpx;
    margin: 54rpx auto;
    display: block;
  }
}
</style>
