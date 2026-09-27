<script>
import { HTTP_REQUEST_URL } from "./config/app";
import {
  getShopConfig,
  silenceAuth,
  getSystemVersion,
  basicConfig,
  remoteRegister,
} from "@/api/public";
import Auth from "@/libs/wechat.js";
import Routine from "./libs/routine.js";
import { silenceBindingSpread } from "@/utils";
import { getCrmebCopyRight, getThemeInfo } from "@/api/api.js";
import { getLangJson, getLangVersion } from "@/api/user.js";
import { mapGetters } from "vuex";
import colors from "@/mixins/color.js";
import Cache from "@/utils/cache";
import { debug } from "util";
import { applyTheme } from "@/utils/theme.js";

export default {
  globalData: {
    spid: 0,
    code: 0,
    isLogin: false,
    userInfo: {},
    MyMenus: [],
    globalData: false,
    isIframe: false,
    tabbarShow: true,
    windowHeight: 0,
    locale: "",
  },
  mixins: [colors],
  computed: mapGetters(["isLogin", "cartNum"]),
  watch: {
    isLogin: {
      deep: true, //Đặt deep watch thành true
      handler: function (newV, oldV) {
        if (newV) {
          // this.getCartNum()
        } else {
          this.$store.commit("indexData/setCartNum", "");
        }
      },
    },
    cartNum(newCart, b) {
      this.$store.commit("indexData/setCartNum", newCart + "");
      if (newCart > 0) {
        uni.setTabBarBadge({
          index: Number(uni.getStorageSync("FOOTER_ADDCART")) || 2,
          text: newCart + "",
        });
      } else {
        uni.hideTabBarRedDot({
          index: Number(uni.getStorageSync("FOOTER_ADDCART")) || 2,
        });
      }
    },
  },
  onShow() {
    const queryData = uni.getEnterOptionsSync(); // Phiên bản uni-app 3.5.1+ hỗ trợ
    if (queryData.query.spread) {
      this.$Cache.set("spread", queryData.query.spread);
      this.globalData.spid = queryData.query.spread;
      this.globalData.pid = queryData.query.spread;
      silenceBindingSpread(this.globalData);
    }
    if (queryData.query.spid) {
      this.$Cache.set("spread", queryData.query.spid);
      this.globalData.spid = queryData.query.spid;
      this.globalData.pid = queryData.query.spid;
      silenceBindingSpread(this.globalData);
    }
    if (queryData.query.agent_id) {
      this.$Cache.set("agent_id", queryData.query.agent_id);
      this.globalData.agent_id = queryData.query.agent_id;
      silenceBindingSpread(this.globalData);
    }
    // #ifdef MP
    if (queryData.query.scene) {
      let param = this.$util.getUrlParams(
        decodeURIComponent(queryData.query.scene)
      );
      if (param.pid) {
        this.$Cache.set("spread", param.pid);
        this.globalData.spid = param.pid;
        this.globalData.pid = param.pid;
      } else {
        switch (queryData.scene) {
          //Quét mã Mini Program
          case 1047:
            this.globalData.code = queryData.query.scene;
            break;
          //Nhấn giữ ảnh để nhận diện mã Mini Program
          case 1048:
            this.globalData.code = queryData.query.scene;
            break;
          //Chọn mã Mini Program từ album ảnh điện thoại
          case 1049:
            this.globalData.code = queryData.query.scene;
            break;
          //Vào Mini Program trực tiếp
          case 1001:
            this.globalData.spid = queryData.query.scene;
            break;
        }
      }
      silenceBindingSpread(this.globalData);
    }
    // #endif
  },
  async onLaunch(option) {
    uni.hideTabBar();
    let that = this;
    basicConfig().then((res) => {
      uni.setStorageSync("BASIC_CONFIG", res.data);
    });
    // #ifdef H5
    if (
      option.query.hasOwnProperty("mdType") &&
      option.query.mdType == "iframeWindow"
    ) {
      this.globalData.isIframe = true;
    } else {
      this.globalData.isIframe = false;
    }
    if (!this.isLogin && option.query.hasOwnProperty("remote_token")) {
      this.remoteRegister(option.query.remote_token);
    }
    // #endif
    let previewThemeId = uni.getStorageSync("previewThemeId");
    applyTheme(previewThemeId);
    getLangVersion().then((res) => {
      let version = res.data.version;
      if (version != uni.getStorageSync("LANG_VERSION")) {
        getLangJson().then((res) => {
          let value = Object.keys(res.data)[0];
          Cache.set("locale", Object.keys(res.data)[0]);
          this.$i18n.setLocaleMessage(value, res.data[value]);
          uni.setStorageSync("localeJson", res.data);
        });
      }
      uni.setStorageSync("LANG_VERSION", version);
    });

    // #ifdef APP-PLUS || H5
    uni.getSystemInfo({
      success: function (res) {
        // Trang chủ không có title thì lấy chiều cao toàn trang, nếu trang có title gốc (native) thì phải trừ đi mới là chiều cao viewport
        // Status bar là động có thể lấy được, title bar là cố định hard-code là 44px
        let height = res.windowHeight - res.statusBarHeight - 44;
        // #ifdef H5 || APP-PLUS
        that.globalData.windowHeight = res.windowHeight + "px";
        // #endif
        // // #ifdef APP-PLUS
        // that.globalData.windowHeight = height + 'px'
        // // #endif
      },
    });
    // #endif
    // #ifdef MP
    if (HTTP_REQUEST_URL == "") {
      console.error(
        "Vui lòng cấu hình 'HTTP_REQUEST_URL' trong tệp config.js ở thư mục gốc\n\nVui lòng vào [Chi tiết] -> [AppID] trong công cụ dành cho nhà phát triển và đổi thành Appid của bạn\n\nVui lòng vào trang quản trị [Mini Program] -> [Cấu hình Mini Program] để điền appId và AppSecret của bạn"
      );
      return false;
    }

    const updateManager = wx.getUpdateManager();
    const startParamObj = wx.getEnterOptionsSync();
    if (wx.canIUse("getUpdateManager") && startParamObj.scene != 1154) {
      const updateManager = wx.getUpdateManager();
      updateManager.onCheckForUpdate(function (res) {
        if (res.hasUpdate) {
          updateManager.onUpdateFailed(function () {
            return that.Tips({
              title: "Tải phiên bản mới thất bại",
            });
          });
          updateManager.onUpdateReady(function () {
            wx.showModal({
              title: "Thông báo cập nhật",
              content: "Phiên bản mới đã được tải xong, bạn có muốn khởi động lại ứng dụng không?",
              success(res) {
                if (res.confirm) {
                  updateManager.applyUpdate();
                }
              },
            });
          });
          updateManager.onUpdateFailed(function () {
            wx.showModal({
              title: "Có phiên bản mới",
              content: "Vui lòng xóa Mini Program hiện tại, sau đó tìm kiếm và mở lại...",
            });
          });
        }
      });
    }
    // #endif
    // Lấy chiều cao thanh điều hướng;
    uni.getSystemInfo({
      success: function (res) {
        that.globalData.navHeight =
          res.statusBarHeight * (750 / res.windowWidth) + 91;
      },
    });
    // #ifdef MP
    let menuButtonInfo = uni.getMenuButtonBoundingClientRect();
    that.globalData.navH = menuButtonInfo.top * 2 + menuButtonInfo.height / 2;
    const version = uni.getSystemInfoSync().SDKVersion;
    if (Routine.compareVersion(version, "2.21.3") >= 0) {
      that.$Cache.set("MP_VERSION_ISNEW", true);
    } else {
      that.$Cache.set("MP_VERSION_ISNEW", false);
    }
    // #endif

    // #ifdef MP
    // Ủy quyền ngầm Mini Program
    // if (!this.$store.getters.isLogin) {
    // 	Routine.getCode()
    // 		.then(code => {
    // 			this.silenceAuth(code);
    // 		})
    // 		.catch(res => {
    // 			uni.hideLoading();
    // 		});
    // }
    // #endif
    // #ifdef H5
    // Thêm thống kê crmeb chat
    // var __s = document.createElement('script');
    // __s.src = `${HTTP_REQUEST_URL}/api/get_script`;
    // document.head.appendChild(__s);

    fetch(`${HTTP_REQUEST_URL}/api/get_script`)
      .then((response) => response.text())
      .then((content) => {
        // Thử phân tích xem có phải là HTML không (có thẻ <script>)
        const isHTML = content.trim().startsWith("<script");

        let externalScripts = [];
        let inlineScripts = [];

        if (isHTML) {
          // Trường hợp 1: có thẻ <script>, dùng DOMParser để phân tích
          const parser = new DOMParser();
          const doc = parser.parseFromString(content, "text/html");
          const scripts = doc.querySelectorAll("script");

          externalScripts = Array.from(scripts).filter((script) => script.src);
          inlineScripts = Array.from(scripts).filter((script) => !script.src);
        } else {
          // Trường hợp 2: không có thẻ <script>, xử lý trực tiếp như script nội tuyến (inline)
          inlineScripts = [
            {
              textContent: content,
            },
          ];
        }

        // 1. Tải tất cả script bên ngoài trước (nếu có)
        const loadExternalScripts = externalScripts.map((script) => {
          return new Promise((resolve, reject) => {
            const newScript = document.createElement("script");
            newScript.src = script.src;
            newScript.onload = resolve;
            newScript.onerror = reject;
            document.body.appendChild(newScript);
          });
        });

        // 2. Sau khi script bên ngoài tải xong, mới thực hiện script nội tuyến (inline)
        Promise.all(loadExternalScripts)
          .then(() => {
            inlineScripts.forEach((script) => {
              const newScript = document.createElement("script");
              newScript.textContent = script.textContent;
              document.body.appendChild(newScript);
            });
          })
          .catch((error) =>
            console.error("Failed to load external scripts:", error)
          );
      })
      .catch((error) => console.error("Error fetching script:", error));

    // #endif
    getCrmebCopyRight().then((res) => {
      uni.setStorageSync("copyRight", res.data);
    });
  },
  onHide() {
    // #ifdef H5
    this.$Cache.clear("snsapiKey");
    // #endif
    this.$Cache.clear("previewThemeId");
  },
  methods: {
    remoteRegister(remote_token) {
      remoteRegister({
        remote_token,
      }).then((res) => {
        let data = res.data;
        if (data.get_remote_login_url) {
          location.href = data.get_remote_login_url;
        } else {
          this.$store.commit("LOGIN", {
            token: data.token,
            time: data.expires_time - this.$Cache.time(),
          });
          this.$store.commit("SETUID", data.userInfo.uid);
          location.reload();
        }
      });
    },
    // Ủy quyền ngầm Mini Program
    // silenceAuth(code) {
    // 	let that = this;
    // 	let spread = that.globalData.spid ? that.globalData.spid : '';
    // 	silenceAuth({
    // 			code: code,
    // 			spread_spid: spread,
    // 			spread_code: that.globalData.code
    // 		})
    // 		.then(res => {
    // 			if (res.data.token !== undefined && res.data.token) {
    // 				uni.hideLoading();
    // 				let time = res.data.expires_time - this.$Cache.time();
    // 				that.$store.commit('LOGIN', {
    // 					token: res.data.token,
    // 					time: time
    // 				});
    // 				that.$store.commit('SETUID', res.data.userInfo.uid);
    // 				that.$store.commit('UPDATE_USERINFO', res.data.userInfo);
    // 			}
    // 		})
    // 		.catch(res => {});
    // },
  },
};
</script>

<style>
@import url("@/plugin/emoji-awesome/css/tuoluojiang.css");
@import url("@/plugin/animate/animate.min.css");
@import "static/css/base.css";
@import "static/iconfont/iconfont.css";
@import "static/css/guildford.css";
@import "static/css/style.scss";
@import "static/css/unocss.css";
@import "static/fonts/font.scss";

view {
  box-sizing: border-box;
}

page {
  font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto,
    Helvetica Neue, Arial, sans-serif;
}

.bg-color-red {
  background-color: var(--view-theme) !important;
}

.syspadding {
  padding-top: var(--status-bar-height);
}

.flex {
  display: flex;
}

.uni-scroll-view::-webkit-scrollbar {
  /* Ẩn scrollbar nhưng vẫn giữ được khả năng cuộn */
  display: none;
}

::-webkit-scrollbar {
  width: 0;
  height: 0;
  color: transparent;
}

.uni-system-open-location .map-content.fix-position {
  height: 100vh;
  top: 0;
  bottom: 0;
}

.open-location {
  width: 100%;
  height: 100vh;
}
</style>
