<template>
  <div>
    <el-card :bordered="false" shadow="never" :body-style="{ padding: '0 20px 20px' }">
      <el-tabs v-model="signFrom.type" @tab-click="onClickTab">
        <el-tab-pane :label="item.name" :name="item.type" v-for="(item, index) in tabList" :key="index" />
      </el-tabs>
      <el-button v-db-click @click="add" type="primary">{{
        signFrom.type == 0 ? 'Thêm phần thưởng điểm danh liên tục' : 'Thêm phần thưởng điểm danh tích lũy'
      }}</el-button>
      <el-table
        :data="tableData"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Loại" min-width="80">
          <template slot-scope="scope">
            <span>{{
              scope.row.type == 0 ? `Thưởng điểm danh liên tục ${scope.row.days} ngày` : `Thưởng điểm danh tích lũy ${scope.row.days} ngày`
            }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số ngày" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.days }} (ngày)</span>
          </template>
        </el-table-column>
        <el-table-column label="Điểm thưởng" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.point }} (điểm)</span>
          </template>
        </el-table-column>
        <el-table-column label="Điểm kinh nghiệm thưởng" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.exp }} (kinh nghiệm)</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="100">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="signFrom.page"
          :limit.sync="signFrom.limit"
          @pagination="pageChange"
        />
      </div>
    </el-card>
  </div>
</template>
<script>
import { addSignRewards, signRewards, editSignRewards } from '@/api/marketing.js';
export default {
  name: '',
  data() {
    return {
      signFrom: {
        type: 0,
        page: 1,
        limit: 20,
      },
      tabList: [
        { type: '0', name: 'Phần thưởng điểm danh liên tục' },
        { type: '1', name: 'Phần thưởng điểm danh tích lũy' },
      ],
      total: 0,
      tableData: [],
      loading: false,
    };
  },
  created() {
    this.getList();
  },
  mounted() {},
  methods: {
    onClickTab() {
      this.signFrom.page = 1;
      this.getList();
    },
    getList() {
      this.loading = true;
      signRewards(this.signFrom)
        .then((res) => {
          this.tableData = res.data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.$message.error(err.msg);
          this.loading = false;
        });
    },
    pageChange(val) {
      this.signFrom.page = val;
      this.getList();
    },
    add() {
      this.$modalForm(addSignRewards({ type: this.signFrom.type })).then((res) => {
        this.getList();
      });
    },
    edit(row) {
      this.$modalForm(editSignRewards(row.id)).then((res) => {
        this.getList();
      });
    },
    del(row) {
      let delfromData = {
        title: row.type == 0 ? `xóa phần thưởng điểm danh liên tục ${row.days} ngày` : `xóa phần thưởng điểm danh tích lũy ${row.days} ngày`,
        url: `/marketing/sign/del_rewards/${row.id}`,
        method: 'DELETE',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>
<style lang="scss" scoped>
::v-deep .el-tabs__item {
  height: 54px !important;
  line-height: 54px !important;
}
</style>
