<template>
  <div>
    <div class="i-layout-page-header header-title">
      <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
      <span class="clear_tit">
        <i class="el-icon-info" style="color: #ed4014" />
        <span>Hãy thận trọng khi xóa dữ liệu, dữ liệu đã xóa sẽ không thể khôi phục!</span>
      </span>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-row :gutter="24">
        <el-col v-bind="grid" class="mb20" v-for="(item, index) in tabList" :key="index">
          <div class="clear_box">
            <span class="clear_box_sp1" v-text="item.title"></span>
            <span class="clear_box_sp2" v-text="item.tlt"></span>
            <el-button
              :type="item.typeName"
              v-text="item.typeName === 'primary' ? 'Thay đổi ngay' : 'Dọn dẹp ngay'"
              v-db-click
              @click="onChange(item)"
            ></el-button>
          </div>
        </el-col>
      </el-row>
    </el-card>
    <!-- Thay đổi tên miền-->
    <el-dialog :visible.sync="modals" class="tableBox" title="Thay đổi tên miền" width="540px" :close-on-click-modal="false">
      <div class="acea-row row-column">
        <span>Vui lòng nhập tên miền cần thay thế, định dạng: http://tên miền.</span>
        <span>Quy tắc thay thế: [Tên miền website] trong [Cài đặt] hiện tại sẽ được thay thế bằng tên miền bạn vừa nhập.</span>
        <span class="mb15">Sau khi thay thế thành công, hãy thay đổi [Tên miền website].</span>
        <el-input v-model="value6" type="textarea" :rows="4" placeholder="Vui lòng nhập tên miền website..." />
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="modals = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="changeYU">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { replaceSiteUrlApi } from '@/api/system';
export default {
  name: 'systemCleardata',
  data() {
    return {
      value6: '',
      modals: false,
      grid: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tabList: [
        {
          title: 'Thay đổi tên miền',
          tlt: 'Thay thế tên miền của tất cả hình ảnh được tải lên cục bộ',
          typeName: 'primary',
          type: '11',
        },
        {
          title: 'Xóa tệp đính kèm tạm do người dùng tạo',
          tlt: 'Xóa tệp đính kèm tạm thời do người dùng tạo, không ảnh hưởng đến ảnh sản phẩm',
          typeName: 'error',
          type: 'temp',
        },
        {
          title: 'Xóa sản phẩm trong thùng rác',
          tlt: 'Xóa sản phẩm trong thùng rác, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'recycle',
        },
        {
          title: 'Xóa dữ liệu người dùng',
          tlt: 'Tất cả các bảng liên quan đến người dùng sẽ bị xóa, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'user',
        },
        {
          title: 'Xóa dữ liệu cửa hàng',
          tlt: 'Xóa toàn bộ dữ liệu cửa hàng, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'store',
        },
        {
          title: 'Xóa danh mục sản phẩm',
          tlt: 'Sẽ xóa toàn bộ danh mục sản phẩm, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'category',
        },
        {
          title: 'Xóa dữ liệu đơn hàng',
          tlt: 'Xóa toàn bộ dữ liệu đơn hàng của người dùng, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'order',
        },
        {
          title: 'Xóa dữ liệu CSKH',
          tlt: 'Xóa dữ liệu CSKH đã thêm, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'kefu',
        },
        {
          title: 'Xóa dữ liệu WeChat',
          tlt: 'Xóa dữ liệu menu WeChat đã lưu, câu trả lời cho từ khóa không hợp lệ của WeChat',
          typeName: 'error',
          type: 'wechat',
        },
        {
          title: 'Xóa danh mục nội dung',
          tlt: 'Xóa bài viết và danh mục bài viết đã thêm, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'article',
        },
        {
          title: 'Xóa tất cả tệp đính kèm',
          tlt: 'Xóa toàn bộ tệp đính kèm do người dùng tạo và tải lên từ trang quản trị, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'attachment',
        },
        {
          title: 'Xóa bản ghi hệ thống',
          tlt: 'Xóa nhật ký hệ thống, hãy thao tác thận trọng',
          typeName: 'error',
          type: 'system',
        },
      ],
    };
  },
  methods: {
    // Xóa dữ liệu
    onChange(item) {
      if (item.type === '11') {
        this.modals = true;
      } else {
        this.clearFroms(item);
      }
    },
    clearFroms(item) {
      let delfromData = {
        title: item.title,
        url: `system/clear/${item.type}`,
        method: 'get',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thay đổi tên miền
    changeYU() {
      replaceSiteUrlApi({ url: this.value6 })
        .then((res) => {
          this.modals = false;
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.clear_tit {
  align-items: center;
  margin: 15px;
  span {
    font-size: 14px;
    color: #ed4014;
  }
}
.clear_box {
  border: 1px solid #dadfe6;
  border-radius: 3px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px 10px;
  box-sizing: border-box;
  .clear_box_sp1 {
    font-size: 16px;
    color: #000000;
    display: block;
  }
  .clear_box_sp2 {
    font-size: 14px;
    color: #808695;
    display: block;
    margin: 12px 0;
  }
}
.clear_box ::v-deep .ivu-btn-error {
  color: #fff;
  background-color: #ed4014;
  border-color: #ed4014;
}
.product_tabs ::v-deep .ivu-page-header-title {
  margin-bottom: 0 !important;
}
</style>
