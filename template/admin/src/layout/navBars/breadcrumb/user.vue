<template>
  <div class="layout-navbars-breadcrumb-user" :style="{ flex: layoutUserFlexNum }">
    <div class="layout-navbars-breadcrumb-user-icon" v-db-click @click="refresh">
      <i class="el-icon-refresh-right" :title="$t('message.user.title7')"></i>
    </div>
    <el-popover ref="searchPopover" placement="bottom" title="" width="325" trigger="click">
      <Search ref="searchRef" @close="closePopover" />
      <i
        class="el-icon-search layout-navbars-breadcrumb-user-icon"
        slot="reference"
        :title="$t('message.user.title2')"
      ></i>
    </el-popover>

    <div class="layout-navbars-breadcrumb-user-icon">
      <el-tooltip
        effect="light"
        placement="bottom"
        trigger="click"
        v-model="isShowUserNewsPopover"
        :width="300"
        popper-class="el-tooltip-pupop-user-news"
      >
        <el-badge :is-dot="isDot" v-db-click @click.stop="openNews">
          <i class="el-icon-bell" :title="$t('message.user.title4')"></i>
        </el-badge>
        <transition name="el-zoom-in-top" slot="content">
          <UserNews v-show="isShowUserNewsPopover" @haveNews="initIsDot"></UserNews>
        </transition>
      </el-tooltip>
    </div>
    <div class="layout-navbars-breadcrumb-user-icon" v-db-click @click="onScreenfullClick">
      <i
        :title="isScreenfull ? $t('message.user.title6') : $t('message.user.title5')"
        :class="!isScreenfull ? 'el-icon-full-screen' : 'el-icon-crop'"
      ></i>
    </div>
    <div class="layout-navbars-breadcrumb-user-icon mr10" v-db-click @click="openMobelPage">
      <i title="Trang cửa hàng" class="el-icon-mobile-phone"></i>
    </div>
    <el-dropdown :show-timeout="70" @command="onDropdownCommand">
      <span class="layout-navbars-breadcrumb-user-link">
        <img :src="getUserInfos.head_pic" class="layout-navbars-breadcrumb-user-link-photo mr5" />
        {{ getUserInfos.account === '' ? 'test' : getUserInfos.account }}
        <i class="el-icon-arrow-down el-icon--right"></i>
      </span>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item command="user">{{ $t('message.user.dropdown6') }}</el-dropdown-item>
        <el-dropdown-item divided command="logOut">{{ $t('message.user.dropdown5') }}</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <div class="layout-navbars-breadcrumb-user-icon" v-db-click @click="onLayoutSetingClick">
      <i class="el-icon-setting" :title="$t('message.user.title3')"></i>
    </div>
    <!-- <Search ref="searchRef" /> -->
  </div>
</template>

<script>
import screenfull from 'screenfull';
import { AccountLogout } from '@/api/account';
import { removeCookies } from '@/libs/util';
import { Session, Local } from '@/utils/storage.js';
import UserNews from '@/layout/navBars/breadcrumb/userNews.vue';
import Search from '@/layout/navBars/breadcrumb/search.vue';
export default {
  name: 'layoutBreadcrumbUser',
  components: { UserNews, Search },
  data() {
    return {
      isScreenfull: false,
      isShowUserNewsPopover: false,
      disabledI18n: 'vi',
      disabledSize: '',
      isDot: false,
    };
  },
  computed: {
    // Lấy thông tin người dùng
    getUserInfos() {
      return this.$store.state.userInfo.userInfo;
    },
    // Đặt bố cục flexbox
    layoutUserFlexNum() {
      let { layout, isClassicSplitMenu } = this.$store.state.themeConfig.themeConfig;
      let num = '';
      if (layout === 'defaults' || (layout === 'classic' && !isClassicSplitMenu) || layout === 'columns') num = 1;
      else num = null;
      return num;
    },
  },
  mounted() {
    if (Local.get('themeConfigPrev')) {
      this.initI18n();
      this.initComponentSize();
    }
  },
  methods: {
    closePopover() {
      this.$refs.searchPopover.doClose();
    },
    /**
     * Khởi tạo thuộc tính isDot
     * @param {boolean} status - Giá trị trạng thái
     */
    initIsDot(status) {
      this.isDot = status;
    },
    openMobelPage() {
      // Lấy domain
      window.open(window.location.origin, '_blank');
    },
    /**
     * Mở popup mới
     */
    openNews() {
      // Chuyển đổi giá trị thuộc tính isShowUserNewsPopover
      this.isShowUserNewsPopover = !this.isShowUserNewsPopover;
      // Đặt thuộc tính isDot thành false
      this.isDot = false;
    },

    // Bấm tìm kiếm
    onSearchClick() {
      this.$refs.searchRef.openSearch();
    },
    // Bấm cấu hình bố cục
    onLayoutSetingClick() {
      this.bus.$emit('openSetingsDrawer');
    },
    refresh() {
      this.bus.$emit('onTagsViewRefreshRouterView', this.$route.path);
    },
    // Bấm toàn màn hình
    onScreenfullClick() {
      if (!screenfull.isEnabled) {
        this.$message.warning('Hiện chưa hỗ trợ toàn màn hình');
        return false;
      }
      screenfull.toggle();
      screenfull.on('change', () => {
        if (screenfull.isFullscreen) this.isScreenfull = true;
        else this.isScreenfull = false;
      });
      // Theo dõi cập nhật chiều cao thanh cuộn của menu horizontal.vue
      this.bus.$emit('updateElScrollBar');
    },
    // Kích thước thành phần thay đổi
    onComponentSizeChange(size) {
      Local.remove('themeConfigPrev');
      this.$store.state.themeConfig.themeConfig.globalComponentSize = size;
      Local.set('themeConfigPrev', this.$store.state.themeConfig.themeConfig);
      this.$ELEMENT.size = size;
      this.initComponentSize();
      window.location.reload();
    },
    // Chuyển ngôn ngữ
    onLanguageChange(lang) {
      Local.remove('themeConfigPrev');
      this.$store.state.themeConfig.themeConfig.globalI18n = lang;
      Local.set('themeConfigPrev', this.$store.state.themeConfig.themeConfig);
      this.$i18n.locale = lang;
      this.initI18n();
    },
    // Khởi tạo đa ngôn ngữ (i18n)
    initI18n() {
      switch (Local.get('themeConfigPrev').globalI18n) {
        case 'vi':
          this.disabledI18n = 'vi';
          break;
        case 'zh-cn':
          this.disabledI18n = 'zh-cn';
          break;
        case 'en':
          this.disabledI18n = 'en';
          break;
        case 'zh-tw':
          this.disabledI18n = 'zh-tw';
          break;
      }
    },
    // Khởi tạo kích thước thành phần toàn cục
    initComponentSize() {
      switch (Local.get('themeConfigPrev').globalComponentSize) {
        case '':
          this.disabledSize = '';
          break;
        case 'medium':
          this.disabledSize = 'medium';
          break;
        case 'small':
          this.disabledSize = 'small';
          break;
        case 'mini':
          this.disabledSize = 'mini';
          break;
      }
    },
    // Bấm mục hiện tại của `dropdown menu thả xuống`
    onDropdownCommand(path) {
      if (path === 'logOut') {
        setTimeout(() => {
          this.$msgbox({
            closeOnClickModal: false,
            closeOnPressEscape: false,
            title: this.$t('message.user.logOutTitle'),
            message: this.$t('message.user.logOutMessage'),
            showCancelButton: true,
            confirmButtonText: this.$t('message.user.logOutConfirm'),
            cancelButtonText: this.$t('message.user.logOutCancel'),
            beforeClose: (action, instance, done) => {
              if (action === 'confirm') {
                instance.confirmButtonLoading = true;
                instance.confirmButtonText = this.$t('message.user.logOutExit');
                AccountLogout()
                  .then((res) => {
                    done();
                    this.$message.success('Bạn đã đăng xuất thành công');
                    this.$store.commit('clearAll');
                    // localStorage.clear();
                    // sessionStorage.clear();
                    removeCookies('token');
                    removeCookies('expires_time');
                    removeCookies('uuid');
                    // this.$router.replace({ path: `${settings.routePre}/login` });
                  })
                  .finally(() => {
                    setTimeout(() => {
                      this.$router.replace({ name: 'login' });
                      instance.confirmButtonLoading = false;
                      done();
                    }, 1500);
                  });
              } else {
                done();
              }
            },
          })
            .then(() => {
              // Xóa cache / token, v.v.
              Session.clear();
              // Khi dùng reload, không cần gọi resetRoute() để reset route
              window.location.reload();
            })
            .catch(() => {});
        }, 150);
      } else if (path === 'user') {
        this.$router.push({ name: 'systemUser' });
      } else {
        this.$router.push(path);
      }
    },
  },
};
</script>

<style scoped lang="scss">
.layout-navbars-breadcrumb-user {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  .el-icon-bell {
    color: var(--prev-bg-topBarColor);
  }
  &-link {
    height: 100%;
    display: flex;
    align-items: center;
    white-space: nowrap;
    &-photo {
      width: 30px;
      height: 30px;
      border-radius: 100%;
    }
  }
  &-icon {
    padding: 0 10px;
    cursor: pointer;
    color: var(--prev-bg-topBarColor);
    height: 50px;
    line-height: 50px;
    display: flex;
    align-items: center;
    font-size: 15px;
    &:hover {
      background: var(--prev-color-hover);
      i {
        display: inline-block;
        animation: logoAnimation 0.3s ease-in-out;
      }
    }
  }
  & ::v-deep .el-dropdown {
    color: var(--prev-bg-topBarColor);
    cursor: pointer;
  }
  & ::v-deep .el-badge {
    height: 40px;
    line-height: 40px;
    display: flex;
    align-items: center;
  }
  & ::v-deep .el-badge__content.is-fixed {
    top: 12px;
  }
}
</style>
