<template>
  <div ref="tagsViews" class="layout-navbars-tagsview">
    <i v-if="scrollTagIcon" class="direction el-icon-arrow-left" v-db-click @click="scrollTag('left')"></i>
    <el-scrollbar ref="scrollbarRef" @wheel.native.prevent="onHandleScroll">
      <ul class="layout-navbars-tagsview-ul" :class="setTagsStyle" ref="tagsUlRef">
        <li
          v-for="(v, k) in tagsViewList"
          :key="k"
          class="layout-navbars-tagsview-ul-li"
          :data-name="v.name"
          :class="{ 'is-active': v.path === tagsRoutePath }"
          @contextmenu.prevent="onContextmenu(v, $event)"
          v-db-click
          @click="onTagsClick(v, k)"
          ref="tagsRefs"
        >
          <i
            class="layout-navbars-tagsview-ul-li-iconfont font14 is-tagsview-icon"
            :class="v.icon"
            v-if="v.path !== tagsRoutePath && getThemeConfig.isTagsviewIcon"
          ></i>
          <span>{{ $t(v.meta.title) }}</span>
          <!-- <i
            class="el-icon-refresh-right layout-navbars-tagsview-ul-li-icon ml5"
            v-if="v.path === tagsRoutePath"
            v-db-click @click.stop="refreshCurrentTagsView(v.path)"
          ></i> -->
          <i
            class="el-icon-close layout-navbars-tagsview-ul-li-icon ml5"
            v-if="!v.isAffix"
            v-db-click
            @click.stop="closeCurrentTagsView(v.path)"
          ></i>
        </li>
      </ul>
    </el-scrollbar>
    <i v-if="scrollTagIcon" class="direction el-icon-arrow-right" v-db-click @click="scrollTag('right')"></i>
    <el-dropdown @command="clickDropdown" v-if="tagsViewList.length > 2">
      <span class="setting-tag el-dropdown-link"><i class="el-icon-menu"></i></span>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item v-for="item in dropdownList" :command="item.id" :key="item.id">
          <i :class="item.icon"></i>
          {{ $t(item.txt) }}</el-dropdown-item
        >
      </el-dropdown-menu>
    </el-dropdown>
    <Contextmenu :dropdown="tagsDropdown" ref="tagsContextmenu" @currentContextmenuClick="onCurrentContextmenuClick" />
  </div>
</template>

<script>
import Contextmenu from '@/layout/navBars/tagsView/contextmenu';
import { Session } from '@/utils/storage.js';
import { mapMutations } from 'vuex';
import setting from '@/setting';

export default {
  name: 'tagsView',
  components: { Contextmenu },
  data() {
    return {
      userInfo: {},
      // tagsViewList: [],
      tagsDropdown: {
        x: '',
        y: '',
      },
      tagsRefsIndex: 0,
      tagsRoutePath: this.$route.path,
      // tagsViewRoutesList: [],
      dropdownList: [
        { id: 0, txt: 'message.tagsView.refresh', affix: false, icon: 'el-icon-refresh-right' },
        { id: 1, txt: 'message.tagsView.close', affix: false, icon: 'el-icon-close' },
        { id: 2, txt: 'message.tagsView.closeOther', affix: false, icon: 'el-icon-circle-close' },
        { id: 3, txt: 'message.tagsView.closeAll', affix: false, icon: 'el-icon-folder-delete' },
      ],
      scrollTagIcon: false,
    };
  },
  computed: {
    // Lấy thông tin cấu hình bố cục
    getThemeConfig() {
      return this.$store.state.themeConfig.themeConfig;
    },
    // Đặt động kiểu style cho tagsView
    setTagsStyle() {
      return this.$store.state.themeConfig.themeConfig.tagsStyle;
    },
    tagsViewList() {
      return this.$store.state.app.tagNavList;
    },
    tagsViewRoutesList() {
      return this.$store.state.app.tagNavList;
    },
  },
  created() {
    // Theo dõi lệnh gọi từ ngoài trang này: 0 làm mới hiện tại, 1 đóng hiện tại, 2 đóng cái khác, 3 đóng tất cả
    this.bus.$on('onCurrentContextmenuClick', (data) => {
      this.onCurrentContextmenuClick(data);
    });
  },
  mounted() {
    if (!this.$store.state.app.tagNavList.length) {
      this.getTagsViewRoutes();
    }
    if (this.$refs.tagsViews?.offsetWidth < this.$refs.scrollbarRef.$refs.wrap.scrollWidth) {
      this.scrollTagIcon = true;
    }
    window.addEventListener('resize', () => {
      if (this.$refs.tagsViews?.offsetWidth < this.$refs.scrollbarRef.$refs.wrap.scrollWidth) {
        this.scrollTagIcon = true;
      } else {
        this.scrollTagIcon = false;
      }
    });
  },
  methods: {
    ...mapMutations(['setBreadCrumb', 'setTagNavList', 'addTag', 'setLocal', 'setHomeRoute', 'closeTag']),
    clickDropdown(e) {
      let data = { id: e, path: this.$route.path };
      this.onCurrentContextmenuClick(data);
    },
    // Lấy thông tin route
    getRoutesList() {
      return this.$store.state.routesList.routesList;
    },
    // Khi bấm mục tagsView hiện tại
    onTagsClick(v, k) {
      this.tagsRoutePath = v.path;
      this.tagsRefsIndex = k;
      this.$router.push(v);
    },
    // Lấy chỉ số của tagsView: dùng để xử lý cuộn ngang khi bấm tagsView
    getTagsRefsIndex(path) {
      if (this.tagsViewList.length > 0) {
        this.tagsRefsIndex = this.tagsViewList.findIndex((item) => item.path === path);
      }
    },
    // Cuộn bằng bánh xe chuột
    onHandleScroll(e) {
      this.$refs.scrollbarRef.$refs.wrap.scrollLeft += e.wheelDelta / 4;
    },
    scrollTag(production) {
      let scrollRefs = this.$refs.scrollbarRef.$refs.wrap.scrollWidth;
      let scrollLeft = this.$refs.scrollbarRef.$refs.wrap.scrollLeft;
      if (production === 'left') {
        this.$refs.scrollbarRef.$refs.wrap.scrollLeft = scrollLeft - 300 <= 0 ? 0 : scrollLeft - 300;
      } else {
        this.$refs.scrollbarRef.$refs.wrap.scrollLeft = scrollLeft + 300 >= scrollRefs ? scrollRefs : scrollLeft + 300;
      }
    },
    // Cuộn ngang tagsView
    tagsViewmoveToCurrentTag() {
      this.$nextTick(() => {
        const tagsRefs = this.$refs.tagsRefs;
        if (!tagsRefs) return;
        if (tagsRefs.length <= 0) return false;
        // Phần tử li hiện tại
        let liDom = tagsRefs[this.tagsRefsIndex];
        // Chỉ số phần tử li hiện tại
        let liIndex = this.tagsRefsIndex;
        // Tổng số phần tử li trong ul hiện tại
        let liLength = tagsRefs.length;
        // li đầu tiên
        let liFirst = tagsRefs[0];
        // li cuối cùng
        let liLast = tagsRefs[tagsRefs.length - 1];
        // Giá trị thanh cuộn hiện tại
        let scrollRefs = this.$refs.scrollbarRef.$refs.wrap;
        // Chiều rộng cuộn của thanh cuộn hiện tại
        let scrollS = scrollRefs.scrollWidth;
        // Chiều rộng offset của thanh cuộn hiện tại
        let offsetW = scrollRefs.offsetWidth;
        // Khoảng cách offset của thanh cuộn hiện tại
        let scrollL = scrollRefs.scrollLeft;
        // Dom li tags trước đó
        let liPrevTag = tagsRefs[this.tagsRefsIndex - 1];
        // Dom li tags tiếp theo
        let liNextTag = tagsRefs[this.tagsRefsIndex + 1];
        // Khoảng cách offset của dom li tags trước đó
        let beforePrevL = '';
        // Khoảng cách offset của dom li tags tiếp theo
        let afterNextL = '';
        if (liDom === liFirst) {
          // Phần đầu
          scrollRefs.scrollLeft = 0;
        } else if (liDom === liLast) {
          // Phần cuối
          scrollRefs.scrollLeft = scrollS - offsetW;
        } else {
          // Không phải đầu/cuối
          if (liIndex === 0) beforePrevL = liFirst?.offsetLeft - 5;
          else beforePrevL = liPrevTag?.offsetLeft - 5;
          if (liIndex === liLength) afterNextL = liLast?.offsetLeft + liLast.offsetWidth + 5;
          else afterNextL = liNextTag?.offsetLeft + liNextTag.offsetWidth + 5;
          if (afterNextL > scrollL + offsetW) {
            scrollRefs.scrollLeft = afterNextL - offsetW;
          } else if (beforePrevL < scrollL) {
            scrollRefs.scrollLeft = beforePrevL;
          }
        }
        // Cập nhật thanh cuộn, tránh trường hợp không hiển thị
        this.updateScrollbar();
      });
    },
    // Cập nhật hiển thị thanh cuộn
    updateScrollbar() {
      this.$refs.scrollbarRef.update();
    },
    // Đệ quy tìm thông tin thành phần theo đường dẫn hiện tại
    filterCurrentMenu(arr, currentPath, callback) {
      arr.map((item) => {
        if (item.path === currentPath) {
          callback(item);
          return false;
        }
        item = Object.assign({}, item);
        if (item.children) {
          item.children = this.filterCurrentMenu(item.children, currentPath, callback);
        }
      });
    },
    // Loại bỏ trùng lặp trong mảng đối tượng
    duplicate(arr) {
      let newobj = {};
      arr = arr.reduce((preVal, curVal) => {
        newobj[curVal.path] ? '' : (newobj[curVal.path] = preVal.push(curVal));
        return preVal;
      }, []);
      return arr;
    },
    // Lấy danh sách tagsViewRoutes trong vuex
    getTagsViewRoutes() {
      this.tagsRoutePath = this.$route.path;
      this.setTagNavList(this.$store.state.menus.oneLvMenus);

      this.initTagsViewList();
    },
    // Lưu tagsViewList vào cache tạm của trình duyệt, giữ lại khi refresh trang
    addBrowserSetSession(tagNavList) {
      this.setTagNavList(tagNavList);
    },
    // Khởi tạo dữ liệu tagsView đã đặt
    initTagsViewList() {
      // if (Session.get('tagsViewList') && this.$store.state.themeConfig.themeConfig.isCacheTagsView) {
      //   this.tagsViewList = Session.get('tagsViewList');
      // } else {
      let arr = [];
      this.tagsViewRoutesList.map((v) => {
        if (v.meta && v.meta.isAffix) arr.push({ ...v });
      });
      // }
      this.setTagNavList(arr);
      // Khởi tạo chỉ số của phần tử (li) hiện tại
      this.getTagsRefsIndex(this.$route.path);
      // Thêm khởi tạo di chuyển thanh cuộn ngang tới vị trí tương ứng
      this.tagsViewmoveToCurrentTag();
    },
    // Thêm tagsView: mục chưa đặt ẩn (isHide) cũng được thêm vào tagsView
    addTagsView(path, to) {
      if (this.tagsViewList.some((v) => v.path === path)) return false;
      const item = this.tagsViewRoutesList.find((v) => v.path === path);
      if (item.isLink && !item.isIframe) return false;
      item.query = to?.query ? to?.query : this.$route.query;
      this.tagsViewList.push({ ...item });
      this.addBrowserSetSession(this.tagsViewList);
    },
    // Hiển thị danh sách menu khi bấm menu chuột phải
    onContextmenu(v, e) {
      let { clientX, clientY } = e;
      this.tagsDropdown.x = clientX;
      this.tagsDropdown.y = clientY;
      this.$refs.tagsContextmenu.openContextmenu(v);
    },
    onContextmenuIcon(e) {},
    // Bấm menu chuột phải của mục hiện tại
    onCurrentContextmenuClick(data) {
      let { id, path } = data;
      let currentTag = this.tagsViewList.find((v) => v.path === path);
      switch (id) {
        case 0:
          this.refreshCurrentTagsView(path);
          this.$router.push({ path, query: currentTag.query });
          break;
        case 1:
          this.closeCurrentTagsView(path);
          break;
        case 2:
          this.closeOtherTagsView(path, currentTag.query);
          break;
        case 3:
          this.closeAllTagsView(path);
          break;
      }
    },
    refreshIcon() {
      this.$nextTick((e) => {
        if (this.$refs.tagsViews?.offsetWidth < this.$refs.scrollbarRef.$refs.wrap.scrollWidth) {
          this.scrollTagIcon = true;
        } else {
          this.scrollTagIcon = false;
        }
      });
    },
    // 1. Làm mới tagsView hiện tại:
    refreshCurrentTagsView(path) {
      this.bus.$emit('onTagsViewRefreshRouterView', path);
    },
    // 2. Đóng tagsView hiện tại: bấm icon đóng của mục `tags-view` hiện tại, nếu đã đặt cố định (isAffix) thì không thể đóng
    closeCurrentTagsView(path) {
      this.tagsViewList.map((v, k, arr) => {
        if (!v.meta.isAffix) {
          if (v.path === path) {
            this.tagsViewList.splice(k, 1);
            setTimeout(() => {
              // Cái cuối cùng
              if (this.tagsViewList.length === k)
                this.$router.push({ path: arr[arr.length - 1].path, query: arr[arr.length - 1].query });
              // Nếu không thì chuyển tới cái tiếp theo
              else this.$router.push({ path: arr[k].path, query: arr[k].query });
            }, 0);
          }
        }
      });
      this.setTagNavList(this.tagsViewList);
      //   this.addBrowserSetSession(this.tagNavList);
    },
    // 3. Đóng các tagsView khác: nếu đã đặt cố định (isAffix) thì không đóng
    closeOtherTagsView(path, query) {
      let tagsViewList = [];
      this.tagsViewRoutesList.map((v) => {
        if ((v.meta && v.meta.isAffix) || v.path === path) {
          tagsViewList.push({ ...v });
        }
      });
      this.addBrowserSetSession(tagsViewList);
      this.$router.push({ path, query });

      // this.addTagsView(path);
    },
    // 4. Đóng tất cả tagsView: nếu đã đặt cố định (isAffix) thì không đóng
    closeAllTagsView(path) {
      let tagsViewList = [];
      this.tagsViewRoutesList.map((v) => {
        if (v.meta.isAffix) {
          tagsViewList.push({ ...v });
          if (tagsViewList.some((v) => v.path === path)) this.$router.push({ path, query: this.$route.query });
          else this.$router.push({ path: v.path, query: this.$route.query });
        }
      });
      this.addBrowserSetSession(tagsViewList);
    },
  },
  watch: {
    // Theo dõi thay đổi route
    $route: {
      handler(to) {
        this.tagsRoutePath = to.path;
        this.addTagsView(to.path, to);
        this.getTagsRefsIndex(to.path);
        this.tagsViewmoveToCurrentTag();
        this.refreshIcon();
      },
      deep: true,
    },
  },
  destroyed() {
    // Bỏ theo dõi lệnh gọi từ ngoài trang này (fun/tagsView)
    this.bus.$off('onCurrentContextmenuClick');
  },
};
</script>

<style scoped lang="scss">
::v-deep .el-scrollbar__bar.is-horizontal {
  height: 0;
}
.el-dropdown-menu {
  width: 130px;
}
.setting-tag {
  padding: 0 10px;
  cursor: pointer;
}
.direction {
  padding: 0 3px;
}
.direction:hover {
  line-height: 34px;
  background-color: #f7f2f2;
  cursor: pointer;
  transition: all 0.3s;
}
.layout-navbars-tagsview {
  flex: 1;
  z-index: 10;
  background-color: var(--prev-bg-white);
  -webkit-box-shadow: 0 1px 4px rgba(113, 128, 165, 0.1);
  box-shadow: 0 1px 4px rgba(113, 128, 165, 0.1);
  display: flex;
  align-items: center;
  & ::v-deep .is-vertical {
    display: none !important;
  }
  &-ul {
    list-style: none;
    margin: 0;
    padding: 0;
    // width: 100%;
    height: 34px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    color: var(--prev-color-text-regular);
    font-size: 12px;
    padding: 0 15px;
    &-li {
      height: 26px;
      line-height: 26px;
      display: flex;
      align-items: center;
      border: 1px solid #ebeef5;
      padding: 0 12px 0 15px;
      margin-right: 5px;
      border-radius: 2px;
      position: relative;
      z-index: 0;
      cursor: pointer;
      justify-content: space-between;
      transition: all 0.3s cubic-bezier(0.2, 1, 0.3, 1);
      &::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: var(--prev-tag-active-color);
        z-index: -1;
        opacity: 0;
        // transform: scale3d(0.7, 1, 1);
        // transition: transform 0.3s, opacity 0.3s;
        // transition-timing-function: cubic-bezier(0.2, 1, 0.3, 1);
      }
      &:hover {
        color: var(--prev-color-primary-light-9);
        transition: all 0.3s cubic-bezier(0.2, 1, 0.3, 1);
        border-color: transparent;
        &::before {
          opacity: 1;
          transform: translate3d(0, 0, 0);
          border-radius: 2px;
        }
        .is-tagsview-icon {
          color: var(--prev-color-primary-light-9);

          transition: all 0.3s cubic-bezier(0.2, 1, 0.3, 1);
        }
      }
      &-iconfont {
        position: relative;
        left: -5px;
        top: 1px;
        color: var(--prev-color-primary-light-9);
      }
      &-icon {
        border-radius: 100%;
        position: relative;
        height: 14px;
        width: 14px;
        text-align: center;
        line-height: 14px;
        top: 0px;
      }
      .is-tagsview-icon {
        color: var(--prev-color-text-regular);
        transition: all 0.3s cubic-bezier(0.2, 1, 0.3, 1);
      }
    }
    .is-active {
      color: var(--prev-color-primary-light-3);
      transition: all 0.3s cubic-bezier(0.2, 1, 0.3, 1);
      border-color: transparent;
      &::before {
        opacity: 1;
        transform: translate3d(0, 0, 0);
        border-radius: 2px;
      }
    }
  }
  & ::-webkit-scrollbar {
    display: none !important;
  }
  // // Kiểu 2
  // .tags-style-two {
  // }
  // // Kiểu 3
  // .tags-style-three {
  // }
  // // Kiểu 4
  // Kiểu 1
  .tags-style-one {
    .is-active {
      background: none !important;
      color: #fff !important;
    }
  }
  // Kiểu 4
  .tags-style-four {
    .layout-navbars-tagsview-ul-li {
      margin-right: 0 !important;
      border: none !important;
      position: relative;
      border-radius: 3px !important;

      .layout-icon-active {
        display: none;
      }
      .layout-icon-three {
        display: block;
      }
      &:hover {
        background: none !important;
      }
    }
    .is-active {
      background: none !important;
      color: #fff !important;
    }
  }
  // Kiểu 5
  .tags-style-five {
    align-items: flex-end;
    .tags-style-five-svg {
      -webkit-mask-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbD0ibm9uZSI+CgogPGc+CiAgPHRpdGxlPkxheWVyIDE8L3RpdGxlPgogIDxwYXRoIHRyYW5zZm9ybT0icm90YXRlKC0wLjEzMzUwNiA1MC4xMTkyIDUwKSIgaWQ9InN2Z18xIiBkPSJtMTAwLjExOTE5LDEwMGMtNTUuMjI4LDAgLTEwMCwtNDQuNzcyIC0xMDAsLTEwMGwwLDEwMGwxMDAsMHoiIG9wYWNpdHk9InVuZGVmaW5lZCIgc3Ryb2tlPSJudWxsIiBmaWxsPSIjRjhFQUU3Ii8+CiAgPHBhdGggZD0ibS0wLjYzNzY2LDcuMzEyMjhjMC4xMTkxOSwwIDAuMjE3MzcsMC4wNTc5NiAwLjQ3Njc2LDAuMTE5MTljMC4yMzIsMC4wNTQ3NyAwLjI3MzI5LDAuMDM0OTEgMC4zNTc1NywwLjExOTE5YzAuMDg0MjgsMC4wODQyOCAwLjM1NzU3LDAgMC40NzY3NiwwbDAuMTE5MTksMGwwLjIzODM4LDAiIGlkPSJzdmdfMiIgc3Ryb2tlPSJudWxsIiBmaWxsPSJub25lIi8+CiAgPHBhdGggZD0ibTI4LjkyMTM0LDY5LjA1MjQ0YzAsMC4xMTkxOSAwLDAuMjM4MzggMCwwLjM1NzU3bDAsMC4xMTkxOWwwLDAuMTE5MTkiIGlkPSJzdmdfMyIgc3Ryb2tlPSJudWxsIiBmaWxsPSJub25lIi8+CiAgPHJlY3QgaWQ9InN2Z180IiBoZWlnaHQ9IjAiIHdpZHRoPSIxLjMxMTA4IiB5PSI2LjgzNTUyIiB4PSItMC4wNDE3MSIgc3Ryb2tlPSJudWxsIiBmaWxsPSJub25lIi8+CiAgPHJlY3QgaWQ9InN2Z181IiBoZWlnaHQ9IjEuNzg3ODQiIHdpZHRoPSIwLjExOTE5IiB5PSI2OC40NTY1IiB4PSIyOC45MjEzNCIgc3Ryb2tlPSJudWxsIiBmaWxsPSJub25lIi8+CiAgPHJlY3QgaWQ9InN2Z182IiBoZWlnaHQ9IjQuODg2NzciIHdpZHRoPSIxOS4wNzAzMiIgeT0iNTEuMjkzMjEiIHg9IjM2LjY2ODY2IiBzdHJva2U9Im51bGwiIGZpbGw9Im5vbmUiLz4KIDwvZz4KPC9zdmc+'),
        url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbD0ibm9uZSI+CiA8Zz4KICA8dGl0bGU+TGF5ZXIgMTwvdGl0bGU+CiAgPHBhdGggdHJhbnNmb3JtPSJyb3RhdGUoLTg5Ljc2MjQgNy4zMzAxNCA1NS4xMjUyKSIgc3Ryb2tlPSJudWxsIiBpZD0ic3ZnXzEiIGZpbGw9IiNGOEVBRTciIGQ9Im02Mi41NzQ0OSwxMTcuNTIwODZjLTU1LjIyOCwwIC0xMDAsLTQ0Ljc3MiAtMTAwLC0xMDBsMCwxMDBsMTAwLDB6IiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPgogIDxwYXRoIGQ9Im0tMC42Mzc2Niw3LjMxMjI4YzAuMTE5MTksMCAwLjIxNzM3LDAuMDU3OTYgMC40NzY3NiwwLjExOTE5YzAuMjMyLDAuMDU0NzcgMC4yNzMyOSwwLjAzNDkxIDAuMzU3NTcsMC4xMTkxOWMwLjA4NDI4LDAuMDg0MjggMC4zNTc1NywwIDAuNDc2NzYsMGwwLjExOTE5LDBsMC4yMzgzOCwwIiBpZD0ic3ZnXzIiIHN0cm9rZT0ibnVsbCIgZmlsbD0ibm9uZSIvPgogIDxwYXRoIGQ9Im0yOC45MjEzNCw2OS4wNTI0NGMwLDAuMTE5MTkgMCwwLjIzODM4IDAsMC4zNTc1N2wwLDAuMTE5MTlsMCwwLjExOTE5IiBpZD0ic3ZnXzMiIHN0cm9rZT0ibnVsbCIgZmlsbD0ibm9uZSIvPgogIDxyZWN0IGlkPSJzdmdfNCIgaGVpZ2h0PSIwIiB3aWR0aD0iMS4zMTEwOCIgeT0iNi44MzU1MiIgeD0iLTAuMDQxNzEiIHN0cm9rZT0ibnVsbCIgZmlsbD0ibm9uZSIvPgogIDxyZWN0IGlkPSJzdmdfNSIgaGVpZ2h0PSIxLjc4Nzg0IiB3aWR0aD0iMC4xMTkxOSIgeT0iNjguNDU2NSIgeD0iMjguOTIxMzQiIHN0cm9rZT0ibnVsbCIgZmlsbD0ibm9uZSIvPgogIDxyZWN0IGlkPSJzdmdfNiIgaGVpZ2h0PSI0Ljg4Njc3IiB3aWR0aD0iMTkuMDcwMzIiIHk9IjUxLjI5MzIxIiB4PSIzNi42Njg2NiIgc3Ryb2tlPSJudWxsIiBmaWxsPSJub25lIi8+CiA8L2c+Cjwvc3ZnPg=='),
        url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg'><rect rx='8' width='100%' height='100%' fill='%23F8EAE7'/></svg>");
      -webkit-mask-size: 18px 30px, 20px 30px, calc(100% - 30px) calc(100% + 17px);
      -webkit-mask-position: right bottom, left bottom, center top;
      -webkit-mask-repeat: no-repeat;
    }
    .layout-navbars-tagsview-ul-li {
      padding: 0 5px;
      border-width: 15px 27px 15px;
      border-style: solid;
      border-color: transparent;
      margin: 0 -15px;
      .layout-icon-active,
      .layout-navbars-tagsview-ul-li-iconfont,
      .layout-navbars-tagsview-ul-li-refresh {
        display: none;
      }
      .layout-icon-three {
        display: block;
      }
      &:hover {
        @extend .tags-style-five-svg;
        background: var(--prev-color-primary-light-9);
        color: unset;
      }
    }
    .is-active {
      @extend .tags-style-five-svg;
      background: var(--prev-color-primary-light-9) !important;
      color: var(--prev-color-primary) !important;
      z-index: 1;
    }
  }
}
</style>
