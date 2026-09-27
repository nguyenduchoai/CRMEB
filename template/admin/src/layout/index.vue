<template>
  <!-- Xác định hiển thị thành phần nào -->
  <Mains v-if="headMenuNoShow" />
  <!-- Dựa theo loại bố cục trong cấu hình theme để xác định hiển thị thành phần nào -->
  <Defaults v-else-if="getThemeConfig.layout === 'defaults'" />
  <Classic v-else-if="getThemeConfig.layout === 'classic'" />
  <Transverse v-else-if="getThemeConfig.layout === 'transverse'" />
  <Columns v-else-if="getThemeConfig.layout === 'columns'" />
</template>

<script>
import { Local } from '@/utils/storage.js';
import { mapMutations } from 'vuex';
import { getNewTagList } from '@/libs/util';

export default {
  name: 'layout',
  components: {
    Defaults: () => import('@/layout/main/defaults.vue'),
    Classic: () => import('@/layout/main/classic.vue'),
    Transverse: () => import('@/layout/main/transverse.vue'),
    Columns: () => import('@/layout/main/columns.vue'),
    Mains: () => import('@/layout/component/main.vue'),
  },
  data() {
    return {
      headMenuNoShow: false,
    };
  },
  computed: {
    // Lấy thông tin cấu hình bố cục
    getThemeConfig() {
      return this.$store.state.themeConfig.themeConfig;
    },
    tagNavList() {
      return this.$store.state.app.tagNavList;
    },
  },
  watch: {
    $route(newRoute) {
      this.headMenuNoShow = this.$route.meta.fullScreen;
      const { name, query, params, meta, path } = newRoute;
      this.addTag({
        route: { name, query, params, meta, path },
        type: 'push',
      });
      this.setBreadCrumb(newRoute);
      this.setTagNavList(getNewTagList(this.tagNavList, newRoute));
    },
  },
  created() {
    this.headMenuNoShow = this.$route.meta.fullScreen;
    this.onLayoutResize();
    window.addEventListener('resize', this.onLayoutResize);
  },
  methods: {
    ...mapMutations(['setBreadCrumb', 'setTagNavList', 'addTag', 'setLocal', 'setHomeRoute', 'closeTag']),

    // Khi kích thước cửa sổ thay đổi (tương thích di động)
    onLayoutResize() {
      if (!Local.get('oldLayout')) Local.set('oldLayout', this.$store.state.themeConfig.themeConfig.layout);
      const clientWidth = document.body.clientWidth;
      if (clientWidth < 1000) {
        this.$store.state.themeConfig.themeConfig.isCollapse = false;
        this.bus.$emit('layoutMobileResize', {
          layout: 'defaults',
          clientWidth,
        });
      } else {
        this.bus.$emit('layoutMobileResize', {
          layout: Local.get('oldLayout') ? Local.get('oldLayout') : this.$store.state.themeConfig.themeConfig.layout,
          clientWidth,
        });
      }
    },
  },
  distroyed() {
    window.removeEventListener('resize', this.onLayoutResize);
  },
};
</script>
