<template>
  <div>
    <div class="i-layout-page-header header-title">
      <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="formValidate"
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <el-row :gutter="24">
          <el-col :xl="6" :lg="12" :md="13" :sm="12" :xs="24">
            <el-form-item label="Từ khóa:">
              <el-input enter-button placeholder="Vui lòng nhập" element-id="name" v-model="formValidate.nickname" />
            </el-form-item>
          </el-col>
          <el-col :xl="6" :lg="12" :md="13" :sm="12" :xs="24">
            <el-form-item label="Khoảng thời gian:" class="tab_data">
              <el-date-picker
                clearable
                :editable="false"
                @change="onchangeTime"
                format="yyyy/MM/dd"
                value-format="yyyy/MM/dd"
                type="daterange"
                range-separator="-"
                start-placeholder="Ngày bắt đầu"
                end-placeholder="Ngày kết thúc"
                style="width: 80%"
              ></el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :xl="6" :lg="12" :md="13" :sm="12" :xs="24">
            <el-form-item label="Lọc theo loại:" class="tab_data">
              <el-select v-model="formValidate.type" style="width: 200px; height: 32px" clearable>
                <el-option
                  v-for="(item, index) in billList"
                  :key="index"
                  :value="item.type"
                  :label="item.title"
                ></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item>
              <el-button type="primary" v-db-click @click="userSearchs">Tìm kiếm</el-button>
              <el-button v-auth="['export-userFinance']" class="export" v-db-click @click="exports">Xuất </el-button>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <el-table ref="table" highlight-current-row :data="tabList" v-loading="loading" empty-text="Chưa có dữ liệu">
        <el-table-column label="ID người dùng" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Biệt danh" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền" min-width="130">
          <template slot-scope="scope">
            <div :class="[scope.row.pm === 1 ? 'green' : 'red']">
              {{ scope.row.pm === 1 ? scope.row.number : '-' + scope.row.number }}
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Loại" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ghi chú" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.mark }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="formValidate.page"
          :limit.sync="formValidate.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { billTypeApi, billListApi, userFinanceApi } from '@/api/finance';

export default {
  name: 'bill',
  data() {
    return {
      billList: [],
      formValidate: {
        nickname: '',
        start_time: '',
        end_time: '',
        type: '',
        page: 1, // Trang hiện tại
        limit: 20, // Số mục hiển thị mỗi trang
      },
      loading: false,
      tabList: [],
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
    this.selList();
    this.getList();
  },
  methods: {
    // Thời gian
    onchangeTime(e) {
      this.formValidate.start_time = e[0];
      this.formValidate.end_time = e[1];
    },
    // Lấy loại lọc
    selList() {
      billTypeApi()
        .then(async (res) => {
          this.billList = res.data.list;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      billListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.data;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    // Xuất
    exports() {
      let formValidate = this.formValidate;
      let data = {
        start_time: formValidate.start_time,
        end_time: formValidate.end_time,
        nickname: formValidate.nickname,
        type: formValidate.type,
      };
      userFinanceApi(data)
        .then((res) => {
          location.href = res.data[0];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.ivu-form-label-left ::v-deep .ivu-form-item-label {
  text-align: right;
}
.tabform .export {
  margin-left: 10px;
}
.red {
  color: #ff5722;
}
.green {
  color: #009688;
}
.ivu-mt ::v-deep .ivu-select-placeholder {
  height: 32px;
}
</style>
