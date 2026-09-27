<template>
  <el-drawer :visible.sync="modals" title="Lịch sử đơn hàng" :wrapperClosable="false" :size="700">
    <el-card :bordered="false" shadow="never">
      <el-table :data="recordData" v-loading="loading" empty-text="Chưa có dữ liệu" highlight-current-row>
        <el-table-column label="ID đơn hàng" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.oid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Lịch sử thao tác" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.change_message }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thao tác" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.change_time }}</span>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </el-drawer>
</template>

<script>
import { getOrderRecord } from '@/api/order';
export default {
  name: 'orderRecord',
  data() {
    return {
      modals: false,
      loading: false,
      recordData: [],
      page: {
        page: 1, // Trang hiện tại
        limit: 15, // Số mục hiển thị mỗi trang
      },
    };
  },
  methods: {
    pageChange(index) {
      this.page.pageNum = index;
      this.getList();
    },
    getList(id) {
      let data = {
        id: id,
        datas: this.page,
      };
      this.loading = true;
      getOrderRecord(data)
        .then(async (res) => {
          this.recordData = res.data;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.ivu-table-wrapper {
  border-left: 1px solid #dcdee2;
  border-top: 1px solid #dcdee2;
}
.order_box ::v-deep .ivu-table th {
  background: #f8f8f9 !important;
}
</style>
