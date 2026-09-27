<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-row>
        <el-col v-bind="grid">
          <el-button v-auth="['admin-user-group']" type="primary" v-db-click @click="add">Thêm nhóm</el-button>
        </el-col>
      </el-row>
      <el-table
        :data="groupLists"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Nhóm" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.group_name }}</span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="Thao tác" width="100">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row.id)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa nhóm', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="groupFrom.page"
          :limit.sync="groupFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { userGroupApi, groupAddApi } from '@/api/user';
export default {
  name: 'user_group',
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,

      groupFrom: {
        page: 1,
        limit: 15,
      },
      groupLists: [],
      total: 0,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'left';
    },
  },
  created() {
    this.getList();
  },
  methods: {
    // Thêm
    add() {
      this.$modalForm(groupAddApi(0))
        .then(() => this.getList())
        .catch(() => {
          console.log('error');
        });
    },
    // Danh sách nhóm
    getList() {
      this.loading = true;
      userGroupApi(this.groupFrom)
        .then(async (res) => {
          let data = res.data;
          this.groupLists = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(id) {
      this.$modalForm(groupAddApi(id)).then(() => this.getList());
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `user/user_group/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.groupLists.splice(num, 1);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped></style>
