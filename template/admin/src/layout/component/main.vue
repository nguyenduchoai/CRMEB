<template>
  <el-main class="layout-main">
    <el-scrollbar
      class="layout-scrollbar"
      ref="layoutScrollbarRef"
      v-show="!currentRouteMeta.isLink && !currentRouteMeta.isIframe"
      :style="{ minHeight: `calc(100vh - ${headerHeight}` }"
    >
      <LayoutParentView />
      <Footers v-if="getThemeConfig.isFooter" />
    </el-scrollbar>
    <Links
      :style="{ height: `calc(100vh - ${headerHeight}` }"
      :meta="currentRouteMeta"
      v-if="currentRouteMeta.isLink && !currentRouteMeta.isIframe"
    />
    <Iframes
      :style="{ height: `calc(100vh - ${headerHeight}` }"
      :meta="currentRouteMeta"
      v-if="currentRouteMeta.isLink && currentRouteMeta.isIframe && isShowLink"
      @getCurrentRouteMeta="onGetCurrentRouteMeta"
    />
  </el-main>
</template>

<script>
import LayoutParentView from '@/layout/routerView/parent.vue';
import Footers from '@/layout/footer/index.vue';
import Links from '@/layout/routerView/link.vue';
import Iframes from '@/layout/routerView/iframes.vue';
export default {
  name: 'layoutMain',
  components: { LayoutParentView, Footers, Links, Iframes },
  data() {
    return {
      headerHeight: '',
      currentRouteMeta: {},
      isShowLink: false,
    };
  },
  computed: {
    // Lấy thông tin cấu hình bố cục
    getThemeConfig() {
      return this.$store.state.themeConfig.themeConfig;
    },
  },
  mounted() {
    this.initHeaderHeight();
    this.initCurrentRouteMeta(this.$route.meta);
  },
  methods: {
    // Khởi tạo thông tin meta của route hiện tại
    initCurrentRouteMeta(meta) {
      this.isShowLink = false;
      this.currentRouteMeta = meta;
      setTimeout(() => {
        this.isShowLink = true;
      }, 100);
    },
    // Đặt chiều cao cho main
    initHeaderHeight() {
      let { isTagsview } = this.$store.state.themeConfig.themeConfig;
      if (isTagsview) return (this.headerHeight = `84px`);
      else return (this.headerHeight = `50px`);
    },
    // Thành phần con kích hoạt cập nhật
    onGetCurrentRouteMeta() {
      this.initCurrentRouteMeta(this.$route.meta);
    },
  },
  watch: {
    // Theo dõi thay đổi dữ liệu vuex
    '$store.state.themeConfig.themeConfig': {
      handler(val) {
        this.headerHeight = val.isTagsview ? '84px' : '50px';
        if (val.isFixedHeaderChange !== val.isFixedHeader) {
          if (!this.$refs.layoutScrollbarRef) return false;
          this.$refs.layoutScrollbarRef.update();
        }
      },
      deep: true,
    },
    // Theo dõi thay đổi route
    $route: {
      handler(to) {
        this.initCurrentRouteMeta(to.meta);
        this.$refs.layoutScrollbarRef.wrap.scrollTop = 0;
      },
      deep: true,
    },
  },
};
</script>
