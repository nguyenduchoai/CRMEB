<template>
  <div class="layout-columns-tra-aside el-menu-horizontal-warp">
    <el-scrollbar ref="elMenuHorizontalScrollRef" @wheel.native.prevent="onElMenuHorizontalScroll">
      <ul>
        <li
          v-for="(v, k) in columnsAsideList"
          :key="k"
          @click="onColumnsAsideMenuClick(v)"
          ref="columnsAsideOffsetLeftRefs"
          class="layout-columns"
          :class="{ 'layout-columns-active': v.k === liIndex }"
          :title="$t(v.title)"
        >
          <div :class="setColumnsAsidelayout" v-if="!v.isLink || (v.isLink && v.isIframe)">
            <!-- <i :class="'el-icon-' + v.icon"></i> -->
            <div class="font14">
              {{
                $t(v.title) && $t(v.title).length >= 4
                  ? $t(v.title).substr(0, setColumnsAsidelayout === 'columns-vertical' ? 4 : 3)
                  : $t(v.title)
              }}
            </div>
          </div>
          <div :class="setColumnsAsidelayout" v-else>
            <a :href="v.isLink" target="_blank">
              <!-- <i :class="'el-icon-' + v.icon"></i> -->
              <div class="font14">
                {{
                  $t(v.title) && $t(v.title).length >= 4
                    ? $t(v.title).substr(0, setColumnsAsidelayout === 'columns-vertical' ? 4 : 3)
                    : $t(v.title)
                }}
              </div>
            </a>
          </div>
        </li>
        <div ref="columnsAsideActiveRef" :class="setColumnsAsideStyle"></div>
      </ul>
    </el-scrollbar>
  </div>
</template>

<script>
import { getMenuSider, getHeaderName, findFirstNonNullChildren } from '@/libs/system';
import Logo from '@/layout/logo/index.vue';

export default {
  name: 'layoutColumnsAside',
  components: { Logo },
  data() {
    return {
      columnsAsideList: [],
      liIndex: 0,
      difference: 0,
      routeSplit: [],
      activePath: '',
    };
  },
  computed: {
    // Đặt kiểu highlight cho bố cục dạng cột
    setColumnsAsideStyle() {
      return this.$store.state.themeConfig.themeConfig.columnsAsideStyle;
    },
    // Đặt kiểu cho bố cục dạng cột
    setColumnsAsidelayout() {
      return this.$store.state.themeConfig.themeConfig.columnsAsideLayout;
    },
    Layout() {
      return this.$store.state.themeConfig.themeConfig.Layout;
    },
    routesList() {
      this.$store.state.routesList.routesList;
    },
  },
  beforeDestroy() {
    this.bus.$off('routesListChange');
  },
  mounted() {
    this.bus.$on('routesListChange', () => {
      this.setFilterRoutes();
    });
    this.setFilterRoutes();
    this.$nextTick((e) => {
      this.initElMenuOffsetLeft();
    });
  },
  methods: {
    // Đặt thanh cuộn ngang có thể cuộn bằng bánh xe chuột
    onElMenuHorizontalScroll(e) {
      const eventDelta = e.wheelDelta || -e.deltaY * 40;
      this.$refs.elMenuHorizontalScrollRef.$refs.wrap.scrollLeft =
        this.$refs.elMenuHorizontalScrollRef.$refs.wrap.scrollLeft + eventDelta / 4;
    },
    // Khởi tạo dữ liệu, khi refresh trang, thanh cuộn sẽ cuộn tới vị trí tương ứng
    initElMenuOffsetLeft() {
      this.$nextTick(() => {
        let els = document.querySelector('.layout-columns.layout-columns-active');
        if (!els) return false;
        this.$refs.elMenuHorizontalScrollRef.$refs.wrap.scrollLeft = els.offsetLeft;
      });
    },
    // Đặt di chuyển vị trí highlight của menu
    setColumnsAsideMove(k) {
      if (k === undefined) return false;
      const els = this.$refs.columnsAsideOffsetLeftRefs;
      this.liIndex = k;
      this.$refs.columnsAsideActiveRef.style.left = `${els[k].offsetLeft + this.difference}px`;
    },
    // Sự kiện bấm highlight menu
    onColumnsAsideMenuClick(v) {
      let { path, redirect } = v;
      if (v.children) {
        this.$router.push(findFirstNonNullChildren(v.children).path);
      } else {
        this.$router.push(path);
      }
      // Một route được đặt tự động thu gọn menu
      if (!v.children || v.children.length <= 1) this.$store.state.themeConfig.themeConfig.isCollapse = true;
      else if (v.children.length > 1) this.$store.state.themeConfig.themeConfig.isCollapse = false;
      // this.bus.$emit('setSendColumnsChildren', getMenuSider(this.columnsAsideList, path));
    },
    // Đặt vị trí highlight động
    onColumnsAsideDown(k) {
      this.$nextTick(() => {
        this.setColumnsAsideMove(k);
      });
    },
    // Đặt/lọc route (route không tĩnh / có hiển thị trong menu hay không)
    setFilterRoutes() {
      if (this.$store.state.routesList.routesList.length <= 0) return false;
      this.columnsAsideList = this.filterRoutesFun(this.$store.state.routesList.routesList);
      //   const resData = getHeaderName(this.$route.path, this.columnsAsideList);
      const resData = this.setSendChildren(getHeaderName(this.$route, this.columnsAsideList));
      if (!resData.children) {
        this.bus.$emit('setSendColumnsChildren', []);
        this.$store.commit('menus/childMenuList', []);

        this.$store.state.themeConfig.themeConfig.isCollapse = true;
        return false;
      }
      this.bus.$emit('oneCatName', resData.item[0].title);
      this.onColumnsAsideDown(resData.item[0].k);
      // Khi refresh, khởi tạo một route đặt tự động thu gọn menu
      resData.children.length > 0
        ? (this.$store.state.themeConfig.themeConfig.isCollapse = false)
        : (this.$store.state.themeConfig.themeConfig.isCollapse = true);
      this.bus.$emit('setSendColumnsChildren', resData?.children || []);
      this.$store.commit('menus/childMenuList', resData?.children || []);
    },
    // Truyền dữ liệu cấp con hiện tại vào menu
    setSendChildren(path) {
      // const currentPathSplit = path.split('/');
      let currentData = {};
      this.columnsAsideList.map((v, k) => {
        v['k'] = k;
        if (v.path === path) {
          currentData['item'] = [{ ...v }];
          //   currentData['children'] = [{ ...v }];
          if (v.children) currentData['children'] = v.children;
        }
      });
      return currentData;
    },
    // Hàm đệ quy lọc route
    filterRoutesFun(arr) {
      return arr
        .filter((item) => item.path)
        .map((item) => {
          item = Object.assign({}, item);
          if (item.children) item.children = this.filterRoutesFun(item.children);
          return item;
        });
    },
    // Khi bấm tagsView, dựa theo route để tìm chỉ số trong columnsAsideList, thực hiện highlight menu bên trái
    setColumnsMenuHighlight(path) {
      // this.routeSplit = path.split('/');
      // this.routeSplit.shift();
      // const routeFirst = `/${this.routeSplit[0]}`;
      const currentSplitRoute = this.columnsAsideList.find((v) => v.path === path);
      if (!currentSplitRoute) {
        this.onColumnsAsideDown(0);
        return false;
      }
      // Lấy giá trị có trễ (delay), tránh trường hợp không lấy được
      setTimeout(() => {
        this.onColumnsAsideDown(currentSplitRoute.k);
      }, 0);
    },
  },
  watch: {
    // Theo dõi thay đổi dữ liệu vuex
    '$store.state': {
      handler(val) {
        val.themeConfig.themeConfig.columnsAsideStyle === 'columnsRound'
          ? (this.difference = 3)
          : (this.difference = 0);
        if (val.routesList.routesList.length === this.columnsAsideList.length) return false;
      },
      deep: true,
    },
    // Theo dõi thay đổi route
    $route: {
      handler(to) {
        this.setColumnsMenuHighlight(to.path);
        // this.setColumnsAsideMove();
        let HeadName = getHeaderName(to, this.columnsAsideList);
        let asideList = getMenuSider(this.columnsAsideList, HeadName)[0]?.children;
        const resData = this.setSendChildren(HeadName);
        if (resData.length <= 0) return false;
        this.onColumnsAsideDown(resData.item[0].k);
        this.bus.$emit('oneCatName', resData.item[0].title);
        this.bus.$emit('setSendColumnsChildren', asideList || []);
        this.$store.commit('menus/childMenuList', asideList || []);
      },
      deep: true,
    },
  },
};
</script>

<style scoped lang="scss">
::v-deep .el-scrollbar__bar.is-horizontal {
  height: 0;
}
.el-menu-horizontal-warp {
  ::v-deep .el-scrollbar__bar.is-vertical {
    display: none;
  }
  ::v-deep .el-scrollbar__wrap {
    overflow-y: hidden !important;
    overflow-x: scroll !important;
  }
  ::v-deepa {
    width: 100%;
  }
  .el-menu.el-menu--horizontal {
    display: flex;
    height: 100%;
    width: 100%;
    box-sizing: border-box;
  }
}

.layout-columns-tra-aside {
  height: 100%;
  background: var(--prev-bg-columnsMenuBar);
  // box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
  overflow-y: hidden;
  // flex: 1;
  ul {
    position: relative;
    display: flex;
    li {
      color: var(--prev-bg-columnsMenuBarColor);
      width: 80px;
      height: 66px;
      text-align: center;
      display: flex;
      cursor: pointer;
      position: relative;
      z-index: 1;
      .columns-vertical {
        margin: auto;
        .columns-vertical-title {
          //   padding-top: 1px;
        }
      }
      .columns-horizontal {
        display: flex;
        height: 50px;
        width: 80px;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
        i {
          margin-right: 5px;
        }
        a {
          display: flex;
          .columns-horizontal-title {
            padding-top: 1px;
          }
        }
      }
      a {
        text-decoration: none;
        color: var(--prev-bg-columnsMenuBarColor);
      }
    }
    // li:hover {
    //   background: var(--prev-bg-menu-hover-ba-color);
    //   color: var(--prev-bg-columnsMenuBarColor);
    // }
    .layout-columns {
      transition: 0.3s ease-in-out;
    }
    .layout-columns-active,
    .layout-columns-active a {
      color: var(--prev-bg-columnsMenuActiveColor);
      transition: 0.3s ease-in-out;
    }
    .columns-round {
      background: var(--prev-color-primary);
      position: absolute;
      left: 0;
      height: 4px;
      width: 80px;
      margin-top: 59px;
      transform: translatey(0%);
      z-index: 0;
      transition: 0.2s ease-in-out;
    }
    .columns-card {
      @extend .columns-round;
      top: 0;
      height: 4px;
      width: 80px;
      border-radius: 0;
      margin-top: 59px;
    }
  }
}
::v-deep .el-scrollbar {
  height: 66px;
}
::v-deep .el-scrollbar__bar.is-horizontal {
  display: none;
}
::v-deep .el-scrollbar__thumb {
  display: none;
}
</style>
