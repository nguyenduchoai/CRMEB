<template>
  <div class="mall-theme-page">
    <!-- Thanh tiêu đề trên cùng -->
    <div class="page-header">
      <div class="header-left">
        <span class="page-title">Chủ đề hiện tại: {{ title }}</span>
        <div class="theme-colors" v-if="themeColors.length">
          <span
            class="color-dot"
            v-for="(item, index) in themeColors.slice(0, 3)"
            :key="index"
            :style="{ backgroundColor: item }"
          ></span>
        </div>
        <span class="status-tag" v-if="confuse">
          <span class="text">Đang dùng kết hợp nhiều chủ đề</span>
        </span>
      </div>
      <div class="header-right">
        <i class="el-icon-refresh-left icon"></i>
        <span @click="handleRestore">Khôi phục chủ đề</span>
      </div>
    </div>

    <!-- Danh sách trang -->
    <div class="theme-list">
      <div class="list-wrapper">
        <div class="list-item" v-for="(item, index) in pageList" :key="index">
          <div class="theme-card">
            <div class="card-header">
              <span class="card-title">{{ item.name || 'Chủ đề chưa đặt tên' }}</span>
              <div class="card-actions">
                <span class="action-btn" @click="handleEdit(item)">Sửa</span>
                <el-divider direction="vertical"></el-divider>
                <span class="action-btn" @click="handleReplace(item, ['home', 'category', 'detail', 'user'][index])"
                  >Thay đổi</span
                >
              </div>
            </div>
            <div class="card-info">
              <div class="theme-name line1">{{ item.themeName }}</div>
              <div class="last-modified">Sửa gần nhất: {{ item.updateTime }}</div>
            </div>
            <div class="card-preview">
              <div class="phone-mockup" :class="{ 'has-image': item.image }">
                <template v-if="item.image">
                  <div class="phone-notch"></div>
                  <img :src="item.image" class="preview-image" alt="preview" />
                </template>
                <div class="empty-container" v-else>
                  <img src="@/assets/images/no-theme-poster.png" class="empty-poster" alt="no poster" />
                  <div class="empty-text">
                    Chưa có ảnh bìa chủ đề
                    <el-tooltip content="Vui lòng vào trang thiết kế để lưu ảnh bìa" placement="top">
                      <i class="el-icon-question"></i>
                    </el-tooltip>
                  </div>
                  <el-button type="primary" size="small" @click="handleEdit(item)">Chỉnh sửa ngay</el-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hộp thoại đổi chủ đề -->
    <theme-select-dialog
      :visible.sync="dialogVisible"
      activeTab="mall"
      type="mall"
      :theme-id="id"
      :current-type="currentType"
      @success="selectTheme"
      v-if="dialogVisible"
    ></theme-select-dialog>
  </div>
</template>

<script>
import { restoreTheme, getThemeUsing } from '@/api/diy';
import ThemeSelectDialog from '../components/themeSelect/index.vue';

export default {
  name: 'MallTheme',
  components: {
    ThemeSelectDialog,
  },
  data() {
    return {
      pageList: [],
      themeColors: [], // Mô phỏng giá trị màu chủ đề do backend trả về
      dialogVisible: false,
      title: '',
      is_diy: false,
      id: '',
      confuse: false,
      currentType: '',
    };
  },
  created() {
    this.getThemeUsing();
  },
  methods: {
    getThemeUsing() {
      getThemeUsing().then((res) => {
        const { title, data_info, theme_data, id, confuse } = res.data;
        this.title = title;
        this.id = id;
        this.confuse = confuse;
        this.themeColors = Object.values(theme_data || {});
        this.pageList = data_info.map((item, index) => {
          return {
            id: item.key,
            name: this.getPageName(item.key),
            themeName: item.title,
            updateTime: item.update_time,
            image: item.image,
            type: ['home', 'category', 'detail', 'user'][index],
          };
        });
      });
    },
    getPageName(key) {
      const names = {
        home: 'Trang chủ cửa hàng',
        category: 'Trang danh mục sản phẩm',
        detail: 'Trang chi tiết sản phẩm',
        user: 'Trang cá nhân',
      };
      return names[key] || key;
    },
    handleEdit(item) {
      // Chuyển đến trang chỉnh sửa, giả định cấu trúc route
      this.$router.push({
        path: this.$routeProStr + '/setting/edit_theme',
        query: { type: item.type, id: this.id },
      });
    },
    handleReplace(item, type) {
      this.currentType = type;
      this.dialogVisible = true;
    },
    handleRestore() {
      this.$confirm('Bạn có chắc muốn khôi phục chủ đề không? Mọi thay đổi tùy chỉnh sẽ bị mất.', 'Thông báo', {
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        type: 'warning',
      })
        .then(() => {
          // Gọi API khôi phục
          restoreTheme(this.id).then((res) => {
            this.$message.success('Đã khôi phục chủ đề');
            this.getThemeUsing();
          });
        })
        .catch(() => {});
    },
    selectTheme() {
      this.getThemeUsing();
    },
  },
};
</script>

<style>
body .v-modal {
  background: rgba(0, 0, 0, 0.5) !important;
  opacity: 1 !important;
}
</style>

<style lang="scss" scoped>
.mall-theme-page {
  min-height: 100%;

  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 16px;
    background: #fff;
    margin-bottom: 20px;
    border-radius: 4px;

    .header-left {
      display: flex;
      align-items: center;

      .page-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-right: 15px;
      }

      .theme-colors {
        display: flex;
        align-items: center;
        margin-right: 15px;

        .color-dot {
          width: 16px;
          height: 16px;
          border-radius: 50%;
          margin-right: -6px;
          position: relative;
          z-index: 5;

          &:first-child {
            z-index: 3;
          }

          &:nth-child(2) {
            z-index: 4;
          }
        }
      }

      .status-tag {
        display: flex;
        align-items: center;
        background: rgba(51, 51, 51, 0.04);
        padding: 2px 10px;
        border-radius: 4px;
        .text {
          font-size: 12px;
          color: #333;
        }
      }
    }
    .header-right {
      display: flex;
      align-items: center;
      font-size: 14px;
      color: #333;
      cursor: pointer;
      .icon {
        margin-right: 5px;
      }
      &:hover {
        color: var(--prev-color-primary-light-1);
      }
    }
  }
  .theme-list {
    padding: 16px;
    border-radius: 5px;
    background-color: #fff;

    .list-wrapper {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -10px;
    }

    .list-item {
      min-width: 270px;
      width: 25%;
      padding: 0 10px;
      box-sizing: border-box;
    }
  }
  .theme-card {
    width: 100%;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 17px;
    transition: all 0.3s;
    margin-bottom: 20px;

    &:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transform: translateY(-2px);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;

      .card-title {
        font-size: 12px;
        color: #333;
        border-radius: 0px 20px 20px 0px;
        padding: 7px 12px;
        margin-left: -17px;
        background: rgba(51, 51, 51, 0.05);
      }

      .card-actions {
        .action-btn {
          font-size: 14px;
          color: var(--prev-color-primary);
          cursor: pointer;

          &:hover {
            color: var(--prev-color-primary-light-1);
          }
        }
      }
    }

    .card-info {
      margin-bottom: 15px;

      .theme-name {
        font-size: 14px;
        font-weight: 500;
        color: rgba(48, 49, 51, 1);
        margin-bottom: 5px;
      }

      .last-modified {
        font-size: 12px;
        color: rgba(144, 147, 153, 1);
      }
    }

    .card-preview {
      display: flex;
      justify-content: center;
      background: #f5f7fa;
      padding: 0;
      border-radius: 12px;

      .phone-mockup {
        width: 100%;
        background: #fff;
        border-radius: 12px;
        overflow-y: scroll;
        position: relative;
        border: 1px solid #f9f9f9;
        max-height: calc(100vh - 360px);
        &.has-image {
          aspect-ratio: 1/2;
        }

        .preview-image {
          width: 100%;
          object-fit: cover;
        }

        .empty-container {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 75px 0 330px;
          height: 100%;
          min-height: 100%;
          background: #f9f9f9;
          .empty-poster {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
          }

          .empty-text {
            font-size: 14px;
            color: #999999;
            margin-bottom: 20px;

            i {
              color: #999;
              cursor: help;
            }
          }
        }
      }
      // Ẩn thanh cuộn
      ::-webkit-scrollbar {
        display: none;
      }
    }
  }
}
</style>
