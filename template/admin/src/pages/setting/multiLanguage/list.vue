<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-row>
        <el-col v-bind="grid">
          <el-button type="primary" v-db-click @click="add">Thêm ngôn ngữ</el-button>
        </el-col>
      </el-row>
      <el-table
        :data="list"
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
        <el-table-column label="Tên ngôn ngữ" min-width="200">
          <template slot-scope="scope">
            <div class="acea-scope.row scope.row-middle">
              <span>{{ scope.row.language_name }}</span>
              <el-tag class="ml10" color="default" v-if="scope.row.is_default">Mặc định</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Mã nhận dạng ngôn ngữ của trình duyệt" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.file_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="150">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="changeSwitch(scope.row)"
              size="large"
              active-text="Bật"
              inactive-text="Tắt"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row, 'Sửa ngôn ngữ', index)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa ngôn ngữ', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="langFrom.page"
          :limit.sync="langFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { langTypeList, langTypeForm, langTypeStatus } from '@/api/setting';
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

      langFrom: {
        page: 1,
        limit: 15,
      },
      list: [],
      total: 0,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getList();
  },
  methods: {
    // Thêm
    add() {
      this.$modalForm(langTypeForm(0)).then(() => this.getList());
    },
    // Danh sách nhóm
    getList() {
      this.loading = true;
      langTypeList(this.langFrom)
        .then(async (res) => {
          let data = res.data;
          this.list = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {
      this.$modalForm(langTypeForm(row.id)).then(() => this.getList());
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `setting/lang_type/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.list.splice(num, 1);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa trạng thái
    changeSwitch(row) {
      langTypeStatus(row.id, row.status)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          row.status = !row.status ? 1 : 0;
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped></style>
