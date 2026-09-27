<template>
  <el-dialog :visible.sync="modal" title="Danh sách tác vụ" width="1000px">
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="formValidate"
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        class="tabform"
        @submit.native.prevent
      >
        <el-row :gutter="24">
          <el-col span="10">
            <el-form-item label="Thời gian thao tác:">
              <el-date-picker
                clearable
                :editable="false"
                @change="onchangeTime"
                v-model="timeVal"
                format="yyyy/MM/dd"
                type="datetimerange"
                value-format="yyyy/MM/dd"
                range-separator="-"
                start-placeholder="Ngày bắt đầu"
                end-placeholder="Ngày kết thúc"
                style="width: 90%"
                :options="options"
              ></el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :span="7">
            <el-form-item label="Loại:">
              <el-select v-model="formValidate.type" clearable @change="typeSearchs">
                <el-option
                  v-for="item in typeList"
                  :value="item.value"
                  :key="item.value"
                  :label="item.label"
                ></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="7">
            <el-form-item label="Trạng thái:">
              <el-select v-model="formValidate.status" clearable @change="statusSearchs">
                <el-option
                  v-for="item in statusList"
                  :value="item.value"
                  :key="item.value"
                  :label="item.label"
                ></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <el-table class="mt14" height="530" :data="data1" v-loading="loading">
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thao tác" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số đơn giao hàng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.total_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số đơn giao hàng thành công" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.success_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hình thức giao hàng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.status_cn }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <template v-if="scope.row.is_show_log">
              <a v-db-click @click="deliveryLook(scope.row)">Xem</a>
              <el-divider direction="vertical"></el-divider>
            </template>
            <template>
              <el-dropdown size="small" @command="changeMenu(scope.row, $event)">
                <span class="el-dropdown-link">Xem thêm<i class="el-icon-arrow-down el-icon--right"></i> </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item command="1">Tải xuống</el-dropdown-item>
                  <el-dropdown-item command="2">Thực thi lại</el-dropdown-item>
                  <el-dropdown-item v-if="scope.row.is_stop_button" command="3">Dừng tác vụ</el-dropdown-item>
                  <el-dropdown-item v-if="scope.row.is_error_button" command="4">Xóa tác vụ lỗi</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="page1.total"
          :total="page1.total"
          :page.sync="page1.pageNum"
          :limit.sync="page1.pageSize"
          @pagination="getQueue"
        />
      </div>
    </el-card>
    <el-dialog :visible.sync="modal1" width="1000px">
      <el-table height="500" class="mt14" :data="data2" v-loading="loading2">
        <el-table-column
          :label="item.title"
          :min-width="item.minWidth || 100"
          v-for="(item, index) in columns4"
          :key="index"
        >
          <template slot-scope="scope">
            <template v-if="item.key">
              <div>
                <span>{{ scope.row[item.key] }}</span>
              </div>
            </template>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="page2.total"
          :total="page2.total"
          :page.sync="page2.pageNum"
          :limit.sync="page2.pageSize"
          @pagination="getDeliveryLog"
        />
      </div>
    </el-dialog>
    <!-- </div> -->
  </el-dialog>
</template>

<script>
import { queueIndex, deliveryLog, queueAgain, queueDel, batchOrderDelivery, stopWrongQueue } from '@/api/order';
import { mapState } from 'vuex';

export default {
  data() {
    return {
      modal: false,
      data1: [],
      page1: {
        total: 0, // Tổng số bản ghi
        pageNum: 1, // Trang hiện tại
        pageSize: 10, // Số mục hiển thị mỗi trang
      },
      formValidate: {
        type: '',
        status: '',
        data: '',
      },
      options: {
        shortcuts: [
          {
            text: 'Hôm nay',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()));
              return [start, end];
            },
          },
          {
            text: 'Hôm qua',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)),
              );
              end.setTime(
                end.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)),
              );
              return [start, end];
            },
          },
          {
            text: '7 ngày qua',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 6)),
              );
              return [start, end];
            },
          },
          {
            text: '30 ngày qua',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 29)),
              );
              return [start, end];
            },
          },
          {
            text: 'Tháng này',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), 1)));
              return [start, end];
            },
          },
          {
            text: 'Năm nay',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), 0, 1)));
              return [start, end];
            },
          },
        ],
      },
      timeVal: [],
      typeList: [
        // {
        //     label: 'Phát phiếu giảm giá theo lô cho người dùng',
        //     value: '1'
        // },
        // {
        //     label: 'Thiết lập nhóm người dùng theo lô',
        //     value: '2'
        // },
        // {
        //     label: 'Thiết lập nhãn người dùng theo lô',
        //     value: '3'
        // },
        // {
        //     label: 'Hạ kệ sản phẩm theo lô',
        //     value: '4'
        // },
        // {
        //     label: 'Xóa quy cách sản phẩm theo lô',
        //     value: '5'
        // },
        {
          label: 'Xóa đơn hàng hàng loạt',
          value: '6',
        },
        {
          label: 'Giao hàng thủ công hàng loạt',
          value: '7',
        },
        {
          label: 'In vận đơn điện tử hàng loạt',
          value: '8',
        },
        {
          label: 'Tự giao hàng hàng loạt',
          value: '9',
        },
        {
          label: 'Giao hàng ảo hàng loạt',
          value: '10',
        },
      ],
      statusList: [
        {
          label: 'Chưa xử lý',
          value: '0',
        },
        {
          label: 'Đang xử lý',
          value: '1',
        },
        {
          label: 'Đã hoàn thành',
          value: '2',
        },
        {
          label: 'Xử lý thất bại',
          value: '3',
        },
      ],
      columns2: [
        {
          title: 'ID đơn hàng',
          key: 'order_id',
        },
        {
          title: 'Đơn vị vận chuyển',
          key: 'delivery_name',
        },
        {
          title: 'Mã vận đơn',
          key: 'delivery_id',
        },
        {
          title: 'Trạng thái xử lý',
          key: 'status_cn',
        },
        {
          title: 'Lý do lỗi',
          key: 'error',
        },
      ],
      columns3: [
        {
          title: 'ID đơn hàng',
          key: 'order_id',
        },
        {
          title: 'Ghi chú',
          key: 'fictitious_content',
        },
        {
          title: 'Trạng thái xử lý',
          key: 'status_cn',
        },
        {
          title: 'Lý do lỗi',
          key: 'error',
        },
      ],
      columns5: [
        {
          title: 'ID đơn hàng',
          key: 'order_id',
        },
        {
          title: 'Nhân viên giao hàng',
          key: 'delivery_name',
        },
        {
          title: 'Số điện thoại nhân viên giao hàng',
          key: 'delivery_id',
        },
        {
          title: 'Trạng thái xử lý',
          key: 'status_cn',
        },
        {
          title: 'Lý do lỗi',
          key: 'error',
        },
      ],
      columns4: [],
      data2: [],
      page2: {
        total: 0, // Tổng số bản ghi
        pageNum: 1, // Trang hiện tại
        pageSize: 12, // Số mục hiển thị mỗi trang
      },
      modal1: false,
      deliveryLog: null,
      deliveryLogId: 0,
      deliveryLogType: '',
      loading: false,
      loading2: false,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '75px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getQueue();
  },
  methods: {
    getQueue() {
      let data = {
        page: this.page1.pageNum,
        limit: this.page1.pageSize,
      };
      if (this.formValidate.status) {
        data.status = this.formValidate.status;
      }
      if (this.formValidate.type) {
        data.type = this.formValidate.type;
      }
      if (this.formValidate.data) {
        data.data = this.formValidate.data;
      }
      this.loading = true;
      queueIndex(data)
        .then((res) => {
          this.loading = false;
          this.data1 = res.data.list;
          this.page1.total = res.data.count;
        })
        .catch((err) => {
          this.loading = false;
        });
    },
    // Tìm kiếm - Thời gian thao tác
    onchangeTime(time) {
      this.timeVal = time || [];
      this.formValidate.data = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.page1.pageNum = 1;
      this.getQueue();
    },
    // Tìm kiếm - Loại
    typeSearchs() {
      this.page1.pageNum = 1;
      this.getQueue();
    },
    // Tìm kiếm - Trạng thái
    statusSearchs() {
      this.page1.pageNum = 1;
      this.getQueue();
    },
    // Xem - Lấy dữ liệu
    getDeliveryLog() {
      this.loading2 = true;
      deliveryLog(this.deliveryLogId, this.deliveryLogType, {
        page: this.page2.pageNum,
        limit: this.page2.pageSize,
      })
        .then((res) => {
          this.loading2 = false;
          this.data2 = res.data.list;
          this.page2.total = res.data.count;
        })
        .catch((err) => {
          this.loading2 = false;
        });
    },
    // Xem
    deliveryLook(row) {
      this.modal1 = true;
      this.deliveryLogId = row.id;
      this.deliveryLogType = row.cache_type;
      this.deliveryLog = row;
      switch (row.type) {
        case 7:
        case 8:
          this.columns4 = this.columns2;
          break;
        case 9:
          this.columns4 = this.columns5;
          break;
        case 10:
          this.columns4 = this.columns3;
          break;
      }
      this.getDeliveryLog();
    },
    // Xem thêm
    changeMenu(row, $event) {
      switch ($event) {
        // Tải xuống
        case '1':
          batchOrderDelivery(row.id, row.type, row.cache_type)
            .then((res) => {
              window.open(res.data[0]);
            })
            .catch((err) => {
              this.$message.error(err.msg);
            });
          break;
        // Thực thi lại
        case '2':
          this.queueAgain(row.id, row.type);
          break;
        // Dừng tác vụ
        case '3':
          this.$msgbox({
            title: 'Thao tác cẩn thận',
            message: 'Xác nhận dừng tác vụ này?',
            showCancelButton: true,
            cancelButtonText: 'Hủy',
            confirmButtonText: 'Xác nhận',
            iconClass: 'el-icon-warning',
            confirmButtonClass: 'btn-custom-cancel',
          })
            .then(() => {
              this.stopQueue(row.id);
            })
            .catch(() => {});
          break;
        // Xóa tác vụ lỗi
        case '4':
          this.queueDel(row.id, row.type);
          break;
      }
    },
    // Thực thi lại
    queueAgain(id, type) {
      queueAgain(id, type)
        .then((res) => {
          this.$message.success(res.msg);
          this.getQueue();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Xóa tác vụ lỗi
    queueDel(id, type) {
      queueDel(id, type)
        .then((res) => {
          this.$message.success(res.msg);
          this.getQueue();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Dừng tác vụ
    stopQueue(id) {
      stopWrongQueue(id)
        .then((res) => {
          this.$message.success(res.msg);
          this.getQueue();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
  },
};
</script>

<style></style>
