<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="levelFrom"
        :model="levelFrom"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <el-row :gutter="24">
          <el-col v-bind="grid">
            <el-form-item label="Trạng thái:" label-for="status1">
              <el-radio-group v-model="levelFrom.status" type="button" @input="userSearchs(levelFrom.status)">
                <el-radio-button label="">Tất cả</el-radio-button>
                <el-radio-button label="0">Chờ duyệt</el-radio-button>
                <el-radio-button label="1">Duyệt</el-radio-button>
                <el-radio-button label="2">Từ chối</el-radio-button>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid">
            <el-form-item label="Tìm người dùng:" label-for="title">
              <el-input
                search
                enter-button
                v-model="levelFrom.keywords"
                placeholder="Vui lòng nhập biệt danh/ID/số điện thoại người dùng"
                @on-search="userSearchs"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <el-table
        :data="levelLists"
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
        <el-table-column label="Biệt danh" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số điện thoại" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.phone }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.status }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian đăng ký" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian duyệt" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.up_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ghi chú" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.remark }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="agree(scope.row)">Đồng ý</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="refuse(scope.row)">Từ chối</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="remark(scope.row)">Ghi chú</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="levelFrom.page"
          :limit.sync="levelFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <!-- Nhiệm vụ lên hạng-->
    <remark ref="remark" @submitFail="submitFail"></remark>
  </div>
</template>
<script>
import { mapState, mapMutations } from 'vuex';
import { userCancelList, userCancelSetMark } from '@/api/user';
import taskList from './handle/task';
import editFrom from '@/components/from/from';
import remark from '@/components/remark/index';
export default {
  name: 'user_level',
  components: { remark },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      id: '',
      loading: false,
      levelFrom: {
        status: '',
        keywords: '',
        page: 1,
        limit: 15,
      },
      levelLists: [],
      total: 0,
    };
  },
  created() {
    this.getList();
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
  methods: {
    ...mapMutations('userLevel', ['getlevelId']),
    remark(row) {
      this.id = row.id;
      this.$refs.remark.formValidate.remark = row.remark;
      this.$refs.remark.modals = true;
    },
    agree(row) {
      this.delfromData = {
        title: 'Hủy tài khoản người dùng',
        url: `/user/cancel/agree/${row.id}`,
        method: 'get',
      };
      this.$modalSure(this.delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    refuse(row) {
      this.delfromData = {
        title: 'Từ chối hủy tài khoản người dùng',
        info: 'Bạn có chắc chắn từ chối hủy tài khoản người dùng này?',
        url: `/user/cancel/refuse/${row.id}`,
        method: 'get',
      };
      this.$modalSure(this.delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    submitFail(text) {
      let data = {
        id: this.id,
        mark: text,
      };
      userCancelSetMark(data)
        .then((res) => {
          this.$refs.remark.modals = false;
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((err) => {
          this.$refs.remark.modals = false;
          this.$message.error(err.msg);
        });
    },

    getList() {
      this.loading = true;
      this.levelFrom.status = this.levelFrom.status || '';
      userCancelList(this.levelFrom)
        .then(async (res) => {
          let data = res.data;
          this.levelLists = data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.levelFrom.page = 1;
      this.getList();
    },
  },
};
</script>

<style lang="scss" scoped>
.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
</style>
