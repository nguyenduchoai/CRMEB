<template>
  <div>
    <div class="i-layout-page-header header-title">
      <div class="fl_header">
        <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
      </div>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <div class="acea-row row-between-wrapper mb20">
        <div class="acea-row row-middle">
          <el-button type="primary" @click="add">Thêm trang tùy chỉnh</el-button>
        </div>
      </div>
      <el-table
        :data="tableList"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Mã số" min-width="80" prop="id"></el-table-column>
        <el-table-column label="Tên" min-width="150" prop="title"></el-table-column>
        <el-table-column label="Thời gian thêm" min-width="150" prop="add_time"></el-table-column>
        <el-table-column label="Thời gian cập nhật" min-width="150" prop="up_time"></el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="150">
          <template slot-scope="scope">
            <a @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a @click="del(scope.row, 'Xóa trang tùy chỉnh', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination v-if="total" :total="total" :page.sync="page" :limit.sync="limit" @pagination="getList" />
      </div>
    </el-card>
  </div>
</template>

<script>
import { getMicroPageList } from '@/api/diy';
import { mapState } from 'vuex';

export default {
  name: 'MicroPageList',
  data() {
    return {
      loading: false,
      tableList: [],
      total: 0,
      page: 1,
      limit: 20,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    getList() {
      this.loading = true;
      getMicroPageList({ page: this.page, limit: this.limit })
        .then((res) => {
          this.tableList = res.data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    add() {
      this.$router.push({
        path: '/admin/setting/edit_theme',
        query: { type: 'home', page_type: 'micro', id: 0 },
      });
    },
    edit(row) {
      this.$router.push({
        path: '/admin/setting/edit_theme',
        query: { type: 'home', page_type: 'micro', id: row.id },
      });
    },
    del(row, title, num) {
      let delfromData = {
        title: title,
        num: num,
        url: `theme/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tableList.splice(num, 1);
          if (!this.tableList.length && this.page > 1) {
            this.page = this.page - 1;
            this.getList();
          }
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    preview(row) {
      this.$message.info('Tính năng đang phát triển');
    },
  },
};
</script>

<style scoped></style>
