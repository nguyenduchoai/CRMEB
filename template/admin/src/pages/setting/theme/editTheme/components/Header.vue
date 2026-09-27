<template>
  <div class="page-header">
    <div class="header-left">
      <span v-if="isMicroPage" class="iconfont iconfanhui" @click="backToMicroPage"></span>
      <span class="label">{{ isMicroPage ? 'Chuyên đề' : 'Chủ đề' }} hiện tại:</span>
      <span class="theme-name">{{ themeName }}</span>
      <span class="iconfont iconic_edit1 edit-icon" @click="handleEdit"></span>
    </div>

    <div class="header-right">
      <el-button size="small" icon="el-icon-view" @click="$emit('preview')">Xem trước</el-button>
      <el-button v-if="!isMicroPage" size="small" @click="$emit('save-template')">Lưu thành chủ đề mới</el-button>
      <el-button size="small" @click="$emit('save')">Lưu</el-button>
      <el-button type="primary" size="small" @click="$emit('save-close')">Lưu và đóng</el-button>
    </div>

    <!-- Hộp thoại sửa thông tin chủ đề -->
    <el-dialog :title="`Sửa thông tin ${isMicroPage ? 'Chuyên đề' : 'Chủ đề'}`" :visible.sync="dialogVisible" width="500px">
      <el-form :model="form" ref="form" label-width="80px">
        <el-form-item :label="`Tên ${isMicroPage ? 'Chuyên đề' : 'Chủ đề'}:`">
          <el-input
            v-model="form.title"
            :maxlength="20"
            show-word-limit
            :placeholder="`Vui lòng nhập tên ${isMicroPage ? 'Chuyên đề' : 'Chủ đề'}`"
          ></el-input>
        </el-form-item>
        <el-form-item :label="`Giới thiệu ${isMicroPage ? 'Chuyên đề' : 'Chủ đề'}:`">
          <el-input
            type="textarea"
            v-model="form.info"
            :maxlength="200"
            show-word-limit
            :placeholder="`Vui lòng nhập giới thiệu ${isMicroPage ? 'Chuyên đề' : 'Chủ đề'}`"
            :rows="4"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">Hủy</el-button>
        <el-button type="primary" @click="handleConfirm">Xác nhận</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'PageHeader',
  props: {
    themeName: {
      type: String,
      default: '',
    },
    themeInfo: {
      type: String,
      default: '',
    },
    isMicroPage: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      dialogVisible: false,
      form: {
        title: '',
        info: '',
      },
    };
  },
  methods: {
    handleEdit() {
      this.form.title = this.themeName;
      this.form.info = this.themeInfo;
      this.dialogVisible = true;
    },
    handleConfirm() {
      let data = {
        title: this.form.title,
        info: this.form.info,
      };
      if (this.$route.query.page_type === 'micro') data.page_type = 'micro';
      if (this.$route.query.tid) data.tid = this.$route.query.tid;
      this.$emit('update-info', data);
      this.dialogVisible = false;
    },
    backToMicroPage() {
      this.$router.push({
        path: `/admin/setting/theme/micro_page`,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.page-header {
  height: 60px;
  background: #fff;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;

  .header-left {
    display: flex;
    align-items: center;
    font-size: 16px;
    color: #333;

    .label {
      font-weight: 500;
    }
    .iconfanhui {
      margin-right: 10px;
    }
    .theme-name {
      font-weight: 500;
      margin-right: 10px;
    }

    .edit-icon {
      cursor: pointer;
      color: #999;

      &:hover {
        color: #1890ff;
      }
    }
  }
}
</style>
