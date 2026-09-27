<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="tableFrom"
          :model="tableFrom"
          :label-width="labelWidth"
          label-position="right"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Thời gian tạo:">
            <el-date-picker
              clearable
              v-model="timeVal"
              type="daterange"
              :editable="false"
              @change="onchangeTime"
              format="yyyy/MM/dd"
              value-format="yyyy/MM/dd"
              start-placeholder="Ngày bắt đầu"
              end-placeholder="Ngày kết thúc"
              :picker-options="pickerOptions"
              style="width: 250px"
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Trạng thái đăng bán:">
            <el-select
              placeholder="Vui lòng chọn"
              clearable
              v-model="tableFrom.is_show"
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Đang bán"></el-option>
              <el-option value="0" label="Ngừng bán"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm sản phẩm:" label-for="store_name">
            <el-input placeholder="Vui lòng nhập tên sản phẩm, ID" v-model="tableFrom.store_name" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button
        v-auth="['marketing-store_integral-create']"
        type="primary"
        icon="md-add"
        v-db-click
        @click="add"
        class="mr10"
        >Thêm sản phẩm đổi điểm</el-button
      >
      <el-table
        :data="tableList"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
        class="mt14"
      >
        <el-table-column label="ID" min-width="50">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hình ảnh sản phẩm" min-width="40">
          <template slot-scope="scope">
            <viewer>
              <div class="tabBox_img">
                <img v-lazy="scope.row.image" />
              </div>
            </viewer>
          </template>
        </el-table-column>
        <el-table-column label="Tiêu đề chương trình" min-width="130">
          <template slot-scope="scope">
            <el-tooltip placement="top" :open-delay="600">
              <div slot="content">{{ scope.row.title }}</div>
              <span class="line2">{{ scope.row.title }}</span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column label="Điểm quy đổi" min-width="60">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giới hạn số lượng" min-width="60">
          <template slot-scope="scope">
            <span>{{ scope.row.quota_show }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số lượng giới hạn còn lại" min-width="60">
          <template slot-scope="scope">
            <span>{{ scope.row.quota }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thứ tự sắp xếp" min-width="60">
          <template slot-scope="scope">
            <span>{{ scope.row.sort }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="60">
          <template slot-scope="scope">
            <el-switch
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.is_show"
              :value="scope.row.is_show"
              @change="onchangeIsShow(scope.row)"
              size="large"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="200">
          <template slot-scope="scope">
            <a v-db-click @click="orderList(scope.row)">Lịch sử đổi thưởng</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="copy(scope.row)">Sao chép</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa sản phẩm đổi điểm', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="tableFrom.page"
          :limit.sync="tableFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { integralProductListApi, integralIsShowApi, storeSeckillApi } from '@/api/marketing';
import { formatDate } from '@/utils/validate';
export default {
  name: 'marketing_storeIntegral',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd');
      }
    },
  },
  data() {
    return {
      loading: false,
      pickerOptions: this.$timeOptions,
      tableList: [],
      timeVal: [],
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        integral_time: '',
        is_show: '',
        store_name: '',
        page: 1,
        limit: 15,
      },
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
  activated() {
    this.getList();
  },
  methods: {
    // Thêm
    add() {
      this.$router.push({ path: this.$routeProStr + '/marketing/store_integral/create' });
    },
    orderList(row) {
      this.$router.push({
        path: this.$routeProStr + '/marketing/store_integral/order_list',
        query: {
          product_id: row.id,
        },
      });
    },
    // Xuất
    exports() {
      let formValidate = this.tableFrom;
      let data = {
        start_status: formValidate.start_status,
        status: formValidate.status,
        store_name: formValidate.store_name,
      };
      storeSeckillApi(data)
        .then((res) => {
          location.href = res.data[0];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {
      this.$router.push({
        path: this.$routeProStr + '/marketing/store_integral/create/' + row.id + '/0',
      });
    },
    // Sao chép một chạm
    copy(row) {
      this.$router.push({
        path: this.$routeProStr + '/marketing/store_integral/create/' + row.id + '/1',
      });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/integral/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tableFrom.page = 1;
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.tableFrom.start_status = this.tableFrom.start_status || '';
      this.tableFrom.is_show = this.tableFrom.is_show || '';
      integralProductListApi(this.tableFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableList = data.list;
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
      this.tableFrom.page = 1;
      this.getList();
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.tableFrom.integral_time = this.timeVal ? this.timeVal.join('-') : '';
      this.getList();
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        is_show: row.is_show,
      };
      integralIsShowApi(data)
        .then(async (res) => {
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
