<template>
  <div class="edit-theme-layout">
    <!-- Điều hướng bên trái -->
    <div class="layout-sidebar" :class="{ collapsed }" v-if="!isMicroPage">
      <sidebar
        :active-menu="activeMenu"
        :collapsed="collapsed"
        :unsaved="isDirty"
        @change="handleMenuChange"
        @save="handleSidebarSave"
        @toggle="collapsed = !collapsed"
      ></sidebar>
    </div>

    <!-- Phần thân bên phải -->
    <div class="layout-main">
      <!-- Thanh trên cùng -->
      <page-header
        :theme-name="themeName"
        :theme-info="themeInfo"
        :isMicroPage="isMicroPage"
        @preview="onPreview"
        @save="onSave"
        @save-close="onSaveClose"
        @save-template="onSaveTemplate"
        @update-info="handleUpdateInfo"
      ></page-header>

      <!-- Vùng nội dung -->
      <div class="layout-content">
        <style-config ref="styleConfig" v-if="activeMenu === 'theme'"></style-config>
        <home-editor ref="homeEditor" v-else-if="activeMenu === 'home'"></home-editor>
        <category-editor ref="categoryEditor" v-else-if="activeMenu === 'category'"></category-editor>
        <detail-editor ref="detailEditor" v-else-if="activeMenu === 'detail'"></detail-editor>
        <user-editor ref="userEditor" v-else-if="activeMenu === 'user'"></user-editor>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue';
import PageHeader from './components/Header.vue';
import StyleConfig from './components/StyleConfig.vue';
import HomeEditor from './components/HomeEditor.vue';
import CategoryEditor from './components/CategoryEditor.vue';
import DetailEditor from './components/DetailEditor.vue';
import UserEditor from './components/UserEditor.vue';
import { saveThemeTitle, themeInfo } from '@/api/diy';

export default {
  name: 'EditTheme',
  components: {
    Sidebar,
    PageHeader,
    StyleConfig,
    HomeEditor,
    CategoryEditor,
    DetailEditor,
    UserEditor,
  },
  data() {
    return {
      themeName: 'Vui lòng đặt tên trang',
      themeInfo: '',
      activeMenu: 'home', // Mặc định chọn trang chủ cửa hàng
      collapsed: false,
      isDirty: false, // Có thay đổi chưa lưu không
    };
  },
  provide() {
    return {
      setDirty: (dirty) => {
        this.isDirty = dirty;
      },
    };
  },
  watch: {
    '$route.query.type': {
      handler(val) {
        if (val) {
          this.activeMenu = val;
        }
      },
      immediate: true,
    },
  },
  computed: {
    isMicroPage() {
      return this.$route.query.page_type === 'micro';
    },
  },
  mounted() {
    if (this.$route.query.id != 0) this.getThemeBaseInfo();
  },
  methods: {
    getThemeBaseInfo() {
      const id = this.$route.query.id || 0;
      if (!id) {
        this.themeName = 'Vui lòng đặt tên trang';
        return;
      }
      themeInfo(id, 'base')
        .then((res) => {
          this.themeName = res.data.title || 'Vui lòng đặt tên trang';
          this.themeInfo = res.data.info;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    handleMenuChange(menuKey) {
      if (this.isDirty) {
        this.isDirty = false;
      }
      this.activeMenu = menuKey;
      if (this.$route.query.type !== menuKey) {
        this.$router.replace({ query: { ...this.$route.query, type: menuKey } });
      }
    },
    handleSidebarSave(key) {
      this.onSave();
    },
    handleUpdateInfo(data) {
      this.themeName = data.title;
      this.themeInfo = data.info;
      let id = this.$route.query.id || 0;
      saveThemeTitle(id, data)
        .then((res) => {
          this.$message.success('Lưu thành công');
          if (id == 0) {
            let query = { ...this.$route.query, id: res.data.id };
            if (query.tid) {
              delete query.tid; // Xóa tid sau khi lưu
            }
            this.$router.replace({ query });
            // Update active component's pageId
            const refName = this.getRefName();
            if (refName && this.$refs[refName] && this.$refs[refName].$refs.diy) {
              this.$refs[refName].$refs.diy.pageId = res.data.id;
            }
          }
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    getRefName() {
      switch (this.activeMenu) {
        case 'theme':
          return 'styleConfig';
        case 'home':
          return 'homeEditor';
        case 'category':
          return 'categoryEditor';
        case 'detail':
          return 'detailEditor';
        case 'user':
          return 'userEditor';
        default:
          return '';
      }
    },
    onPreview() {
      const refName = this.getRefName();
      if (refName && this.$refs[refName] && this.$refs[refName].preview) {
        this.$refs[refName].preview();
      }
    },
    onSave() {
      const refName = this.getRefName();
      if (refName && this.$refs[refName] && this.$refs[refName].saveOnly) {
        this.$refs[refName].saveOnly();
      }
    },
    onSaveClose() {
      const refName = this.getRefName();
      if (refName && this.$refs[refName] && this.$refs[refName].saveAndClose) {
        this.$refs[refName].saveAndClose();
      }
    },
    onSaveTemplate() {
      const refName = this.getRefName();
      if (refName && this.$refs[refName] && this.$refs[refName].saveTemplate) {
        this.$refs[refName].saveTemplate();
      }
    },
  },
};
</script>
<style>
.el-main {
  padding: 0;
}
</style>
<style lang="scss" scoped>
.edit-theme-layout {
  display: flex;
  height: 100vh;
  background-color: #f5f7fa;
  overflow: hidden;

  .layout-sidebar {
    width: 120px;
    background: #fff;
    height: 100%;
    flex-shrink: 0;
    transition: width 0.2s ease;
    &.collapsed {
      width: 70px;
    }
  }

  .layout-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;

    .layout-content {
      flex: 1;
      overflow: hidden;
      position: relative;
      background: #fff;
    }
  }
}
</style>
