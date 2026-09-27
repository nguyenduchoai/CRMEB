<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
          class="tabform"
        >
          <el-form-item :label="fromList.title + '：'">
            <el-date-picker
              clearable
              :editable="false"
              @change="onchangeTime"
              v-model="timeVal"
              format="yyyy/MM/dd"
              type="daterange"
              value-format="yyyy/MM/dd"
              start-placeholder="Ngày bắt đầu"
              end-placeholder="Ngày kết thúc"
              style="width: 250px"
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Tên:">
            <el-select v-model="formValidate.admin_id" clearable @change="userSearchs" class="form_content_width">
              <el-option
                :value="item.id"
                v-for="(item, index) in dataList"
                :key="index"
                :label="item.real_name"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Liên kết:">
            <el-input
              placeholder="Vui lòng nhập liên kết"
              v-model="formValidate.path"
              class="form_content_width"
              clearable
            ></el-input>
          </el-form-item>
          <el-form-item label="IP：">
            <el-input placeholder="Vui lòng nhập IP" v-model="formValidate.ip" clearable class="form_content_width"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" class="userSearch" v-db-click @click="userSearchs">Tìm kiếm</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-table ref="selection" :data="tabList" v-loading="loading" empty-text="Chưa có dữ liệu" highlight-current-row>
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="ID/Tên" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.admin_id + ' / ' + scope.row.admin_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.path_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Liên kết" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.path }}</span>
          </template>
        </el-table-column>
        <el-table-column label="IP thao tác" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.ip }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.type }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thao tác" min-width="100">
          <template slot-scope="scope">
            <span> {{ scope.row.add_time | formatDate }}</span>
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
import { searchAdminApi, systemListApi } from '@/api/system';
import { formatDate } from '@/utils/validate';
export default {
  name: 'systemLog',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  data() {
    return {
      fromList: {
        title: 'Chọn giờ',
        custom: true,
        fromTxt: [
          { text: 'Tất cả', val: '' },
          { text: 'Hôm nay', val: 'today' },
          { text: 'Hôm qua', val: 'yesterday' },
          { text: '7 ngày qua', val: 'lately7' },
          { text: '30 ngày qua', val: 'lately30' },
          { text: 'Tháng này', val: 'month' },
          { text: 'Năm nay', val: 'year' },
        ],
      },
      timeVal: [],
      formValidate: {
        limit: 20,
        page: 1,
        pages: '',
        data: '',
        path: '',
        ip: '',
        admin_id: '',
      },
      loading: false,
      tabList: [],
      total: 0,

      dataList: [],
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
    this.getSearchAdmin();
    this.getList();
  },
  methods: {
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal ? this.timeVal.join('-') : '';
      this.formValidate.page = 1;
      this.getList();
    },
    // Chọn giờ
    selectChange(tab) {
      this.formValidate.data = tab;
      this.timeVal = [];
      this.formValidate.page = 1;
      this.getList();
    },
    // Điều kiện tìm kiếm
    getSearchAdmin() {
      searchAdminApi()
        .then(async (res) => {
          this.dataList = res.data.info;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      systemListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
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
  },
};
</script>

<style scoped></style>
