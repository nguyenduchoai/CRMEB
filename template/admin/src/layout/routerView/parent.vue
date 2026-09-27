<template>
  <div :class="isTagHistory ? 'h100' : 'h101'">
    <transition :name="setTransitionName" mode="out-in">
      <keep-alive :include="keepAliveNameList">
        <router-view :key="refreshRouterViewKey" />
      </keep-alive>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'parent',
  data() {
    return {
      refreshRouterViewKey: null,
      keepAliveNameList: [],
      keepAliveNameNewList: [],
    };
  },
  computed: {
    // Đặt animation chuyển đổi màn hình chính
    setTransitionName() {
      return this.$store.state.themeConfig.themeConfig.animation;
    },
    isTagHistory() {
      return this.$store.state.themeConfig.themeConfig.isTagsview;
    },
  },
  created() {
    /**
     * Lấy danh sách tên thành phần cần giữ trạng thái hoạt động (keep-alive)
     */
    this.keepAliveNameList = this.getKeepAliveNames();
    // Theo dõi sự kiện làm mới route view từ tagsView
    this.bus.$on('onTagsViewRefreshRouterView', (path) => {
      // Nếu đường dẫn route hiện tại không bằng đường dẫn truyền vào thì trả về false ngay
      if (this.$route.path !== path) return false;
      // Lọc bỏ tên thành phần tương ứng với route hiện tại, và đặt lại keepAliveNameList
      this.keepAliveNameList = this.getKeepAliveNames().filter((name) => this.$route.name !== name);
      // Làm mới key của route view
      this.refreshRouterViewKey = this.$route.path;
      // Đặt lại keepAliveNameList ở tick tiếp theo
      this.$nextTick(() => {
        this.refreshRouterViewKey = null;
        /**
         * Lấy danh sách tên thành phần cần giữ trạng thái hoạt động (keep-alive)
         */
        this.keepAliveNameList = this.getKeepAliveNames();
      });
    });
  },

  methods: {
    // Lấy danh sách route được cache (name), mặc định tất cả route đều được cache
    getKeepAliveNames() {
      return this.$store.state.keepAliveNames.keepAliveNames;
    },
  },
};
</script>
