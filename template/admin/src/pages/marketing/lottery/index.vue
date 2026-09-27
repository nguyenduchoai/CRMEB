<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="tableFrom"
        :model="tableFrom"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <el-scope.row :gutter="24">
          <el-col>
            <el-form-item label="Loại chương trình:" clearable>
              <el-select
                style="width: 200px"
                v-model="tableFrom.factor"
                placeholder="Vui lòng chọn loại chương trình"
                clearable
                @change="userSearchs"
              >
                <el-option value="1" label="Quay bằng điểm thưởng"></el-option>
                <el-option value="3" label="Thanh toán đơn hàng"></el-option>
                <el-option value="4" label="Đánh giá đơn hàng"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col>
            <el-form-item label="Trạng thái chương trình:" clearable>
              <el-select
                style="width: 200px"
                v-model="tableFrom.start_status"
                placeholder="Vui lòng chọn"
                clearable
                @change="userSearchs"
              >
                <el-option value="0" label="Chưa bắt đầu"></el-option>
                <el-option value="1" label="Đang diễn ra"></el-option>
                <el-option value="-1" label="Đã kết thúc"></el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <el-col>
            <el-form-item label="Trạng thái đăng bán:">
              <el-select
                style="width: 200px"
                placeholder="Vui lòng chọn"
                v-model="tableFrom.status"
                clearable
                @change="userSearchs"
              >
                <el-option value="1" label="Đang bán"></el-option>
                <el-option value="0" label="Ngừng bán"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col>
            <el-form-item label="Tìm kiếm quay thưởng:" label-for="store_name">
              <el-input
                search
                enter-button
                style="width: 200px"
                placeholder="Vui lòng nhập tên chương trình quay thưởng, ID"
                v-model="tableFrom.store_name"
                @on-search="userSearchs"
              />
            </el-form-item>
          </el-col>
        </el-scope.row>
        <el-scope.row class="mb20">
          <el-button v-auth="['marketing-store_bargain-create']" type="primary" v-db-click @click="add" class="mr10"
            >Thêm quay thưởng</el-button
          >
        </el-scope.row>
      </el-form>
      <el-table
        :data="tableList"
        v-loading="loading"
        highlight-scope.row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên chương trình" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại chương trình" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.lottery_type }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số lượt tham gia" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.lottery_all }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số người quay thưởng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.lottery_people }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số người trúng thưởng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.lottery_win }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái chương trình" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.status_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái đăng bán" min-width="130">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              :disabled="scope.row.lottery_status == 2 ? true : false"
              @change="onchangeIsShow(scope.row)"
              size="large"
              active-text="Đang bán"
              inactive-text="Ngừng bán"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian chương trình" min-width="130">
          <template slot-scope="scope">
            <div>Từ: {{ scope.row.start_time || '--' }}</div>
            <div>Đến: {{ scope.row.end_time || '--' }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái chương trình" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.status_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa quay thưởng', scope.$index)">Xóa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="copy(scope.row)">Sao chép</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="getRecording(scope.row)">Lịch sử quay thưởng</a>
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
import { lotteryListApi, lotteryStatusApi } from '@/api/lottery';
import { formatDate } from '@/utils/validate';
export default {
  name: 'storeBargain',
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
      loading: false,
      tableList: [],
      tableFrom: {
        start_status: '',
        status: '',
        store_name: '',
        export: 0,
        page: 1,
        factor: '',
        limit: 15,
      },
      total: 0,
    };
  },
  computed: {
    ...mapState('admin/layout', ['isMobile']),
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
      this.$router.push({ path: this.$routeProStr + '/marketing/lottery/create' });
    },
    // Sửa
    edit(row) {
      this.$router.push({
        name: 'marketing_create',
        query: {
          id: row.id,
        },
      });
    },
    // Sao chép một chạm
    copy(row) {
      this.$router.push({
        name: 'marketing_create',
        query: {
          id: row.id,
          copy: 1,
        },
      });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/lottery/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tableList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    //Xem lịch sử quay thưởng
    getRecording(row) {
      this.$router.push({
        path: this.$routeProStr + `/marketing/lottery/recording_list`,
        query: {
          id: row.id,
        },
      });
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.tableFrom.start_status = this.tableFrom.start_status || '';
      this.tableFrom.status = this.tableFrom.status || '';
      lotteryListApi(this.tableFrom)
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
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      lotteryStatusApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
          this.getList();
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
