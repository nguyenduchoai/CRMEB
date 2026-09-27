<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="pagination"
          :model="pagination"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Trạng thái hoàn tiền:">
            <el-select
              v-model="pagination.refund_type"
              clearable
              class="form_content_width"
              @change="selectChange2"
              placeholder="Tất cả"
            >
              <el-option v-for="(item, index) in num" :value="index" :key="index" :label="item.name">{{
                item.name
              }}</el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Thời gian hoàn tiền:">
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
              class="mr20"
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Tìm kiếm đơn hàng:" label-for="title">
            <el-input clearable v-model="pagination.order_id" placeholder="Vui lòng nhập mã đơn hàng" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="orderSearch">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-table
        :data="tbody"
        ref="table"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Mã đơn hoàn tiền" min-width="150">
          <template slot-scope="scope">
            <span
              class="cup hover-pimary"
              v-text="scope.row.order_id"
              style="display: block"
              @click="changeMenu(scope.row, '2')"
            ></span>
            <span v-if="scope.row.is_del === 1" style="color: #ed4014; display: block">Người dùng đã xóa</span>
          </template>
        </el-table-column>
        <el-table-column label="Mã đơn hàng gốc" min-width="150">
          <template slot-scope="scope">
            <span
              class="cup hover-pimary"
              v-text="scope.row.store_order_order_id"
              style="display: block"
              @click="changeMenu(scope.row, '3')"
            ></span>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin sản phẩm" min-width="250">
          <template slot-scope="scope">
            <div class="tab" v-for="(item, i) in scope.row._info" :key="i">
              <img
                v-lazy="
                  item.cart_info.productInfo.attrInfo
                    ? item.cart_info.productInfo.attrInfo.image
                    : item.cart_info.productInfo.image
                "
              />
              <el-tooltip placement="top" :open-delay="300">
                <div slot="content">
                  <div>
                    <span>Tên sản phẩm:</span>
                    <span>{{ item.cart_info.productInfo.store_name || '--' }}</span>
                  </div>
                  <div>
                    <span>Tên phân loại:</span>
                    <span>{{
                      item.cart_info.productInfo.attrInfo ? item.cart_info.productInfo.attrInfo.suk : '---'
                    }}</span>
                  </div>
                  <div>
                    <span>Giá:</span>
                    <span>¥{{ item.cart_info.truePrice || '--' }}</span>
                  </div>
                  <div>
                    <span>Số lượng:</span>
                    <span>{{ item.cart_info.cart_num || '--' }}</span>
                  </div>
                </div>
                <span class="line2 w-250">{{ item.cart_info.productInfo.store_name }}</span>
              </el-tooltip>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin người dùng" min-width="100">
          <template slot-scope="scope">
            <span class="cup hover-pimary" @click="userDetail(scope.row, '2')">{{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thanh toán thực tế" min-width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.pay_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian yêu cầu hoàn tiền" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái hoàn tiền" min-width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.refund_type == 1">Chỉ hoàn tiền</div>
            <div v-else-if="scope.row.refund_type == 2">Trả hàng hoàn tiền</div>
            <div v-else-if="scope.row.refund_type == 3">
              <div>Từ chối hoàn tiền</div>
              <div class="c-red">Lý do: {{ scope.row.refuse_reason }}</div>
            </div>
            <div v-else-if="scope.row.refund_type == 4">Chờ trả hàng</div>
            <div v-else-if="scope.row.refund_type == 5">
              <div>Chờ nhận hàng trả</div>
              <div>Mã vận đơn: {{ scope.row.refund_express }}</div>
            </div>
            <div v-else-if="scope.row.refund_type == 6">Đã hoàn tiền</div>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái đơn hàng" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.store_order_status }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin hoàn tiền" min-width="120">
          <template slot-scope="scope">
            <div v-html="scope.row.refund_reason" class="pt5"></div>
            <div class="pictrue-box" v-if="scope.row.refund_img.length">
              <div v-viewer v-for="(item, index) in scope.row.refund_img || []" :key="index">
                <img class="pictrue mr10" v-lazy="item" :src="item" />
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="Thao tác" width="80">
          <template slot-scope="scope">
            <el-dropdown size="small" @command="changeMenu(scope.row, $event)">
              <span class="el-dropdown-link">Xem thêm<i class="el-icon-arrow-down el-icon--right"></i> </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item
                  command="1"
                  ref="ones"
                  v-show="scope.row._status === 1 && scope.row.paid === 0 && scope.row.pay_type === 'offline'"
                  >Thanh toán ngay</el-dropdown-item
                >
                <el-dropdown-item command="2">Chi tiết đơn hàng</el-dropdown-item>
                <el-dropdown-item
                  command="4"
                  v-show="
                    scope.row._status !== 1 ||
                    (scope.row._status === 3 &&
                      scope.row.use_integral > 0 &&
                      scope.row.use_integral >= scope.row.back_integral)
                  "
                  >Ghi chú đổi trả</el-dropdown-item
                >
                <el-dropdown-item
                  command="5"
                  v-show="
                    [1, 2, 5].includes(scope.row.refund_type) &&
                    (parseFloat(scope.row.pay_price) > parseFloat(scope.row.refunded_price) || scope.row.pay_price == 0)
                  "
                  >{{ scope.row.refund_type == 2 ? 'Đồng ý trả hàng' : 'Hoàn tiền ngay' }}</el-dropdown-item
                >
                <el-dropdown-item
                  command="7"
                  v-show="[1, 2].includes(scope.row.refund_type) && scope.row.is_pink_cancel === 0"
                  >Từ chối hoàn tiền</el-dropdown-item
                >
                <el-dropdown-item command="8" v-show="scope.row.is_del == 1">Xóa đơn hàng</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="pagination.page"
          :limit.sync="pagination.limit"
          @pagination="getOrderList"
        />
      </div>
    </el-card>
    <!-- Sửa, hoàn tiền, hoàn điểm, không hoàn tiền-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="submitFail"></edit-from>
    <!-- Chi tiết thành viên-->
    <user-details ref="userDetails"></user-details>
    <!-- Chi tiết -->
    <details-from ref="detailss" :orderDatalist="orderDatalist" :orderId="orderId" :is_refund="1"></details-from>
    <!-- Ghi chú -->
    <order-remark ref="remarks" remarkType="refund" :orderId="orderId" @submitFail="submitFail"></order-remark>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import {
  orderRefundList,
  getDataInfo,
  getDataInfoNew,
  getNewRefundFrom,
  getNewnoRefundFrom,
  refundIntegral,
  getDistribution,
} from '@/api/order';
import userDetails from '@/pages/user/list/handle/userDetails';

import editFrom from '@/components/from/from';
import detailsFrom from '../orderList/handle/orderDetails';
import orderRemark from '../orderList/handle/orderRemark';
import timeOptions from '@/libs/timeOptions';
export default {
  components: { editFrom, detailsFrom, orderRemark, userDetails },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tbody: [],
      num: [],
      orderDatalist: null,
      loading: false,
      FromData: null,
      total: 0,
      orderId: 0,
      animal: 1,
      pagination: {
        page: 1,
        limit: 15,
        order_id: '',
        time: '',
        refund_type: 0,
      },
      timeVal: [],
      modal: false,
      qrcode: null,
      name: '',
      spin: false,
      pickerOptions: timeOptions,
    };
  },
  computed: {
    ...mapState('order', ['orderChartType']),
    // ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getOrderList();
  },
  methods: {
    onchangeCode(e) {
      this.animal = e;
      this.qrcodeShow();
    },
    // Tìm kiếm theo ngày cụ thể();
    onchangeTime(e) {
      this.pagination.page = 1;
      this.timeVal = e || [];
      this.pagination.time = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.getOrderList();
    },
    userDetail(row) {
      this.$refs.userDetails.modals = true;
      this.$refs.userDetails.getDetails(row.uid);
    },
    // Thao tác
    changeMenu(row, name) {
      this.orderId = row.id;
      switch (name) {
        case '1':
          this.delfromData = {
            title: 'Xác nhận đã thanh toán',
            url: `/order/pay_offline/${row.id}`,
            method: 'post',
            ids: '',
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$message.success(res.msg);
              this.getOrderList();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
          // this.modalTitleSs = 'Sửa thanh toán ngay';
          break;
        case '2':
          this.getData(row.order_id, 2);
          break;
        case '3':
          this.getData(row.store_order_id, 3);
          break;
        case '4':
          this.$refs.remarks.modals = true;
          this.$refs.remarks.formValidate.remark = row.remark;
          break;
        case '5':
          this.getRefundData(row.id, row.refund_type);
          break;
        case '6':
          this.getRefundIntegral(row.id);
          break;
        case '7':
          this.getNoRefundData(row.id);
          break;
        case '8':
          this.delfromData = {
            title: 'Xóa đơn hàng',
            url: `/order/del/${row.store_order_id}`,
            method: 'DELETE',
            ids: '',
          };
          this.delOrder(row, this.delfromData);
          break;
        case '10':
          this.delfromData = {
            title: 'In đơn hàng ngay',
            info: 'Bạn có chắc muốn in đơn hàng này?',
            url: `/order/print/${row.id}`,
            method: 'get',
            ids: '',
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$message.success(res.msg);
              this.$emit('changeGetTabs');
              this.getOrderList();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
          break;
        case '11':
          this.delfromData = {
            title: 'In vận đơn điện tử ngay',
            info: 'Bạn có chắc muốn in vận đơn điện tử này?',
            url: `/order/order_dump/${row.id}`,
            method: 'get',
            ids: '',
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$message.success(res.msg);
              this.getOrderList();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
          break;
        default:
          this.delfromData = {
            title: 'Xóa đơn hàng',
            url: `/order/del/${row.id}`,
            method: 'DELETE',
            ids: '',
          };
          // this.modalTitleSs = 'Xóa đơn hàng';
          this.delOrder(row, this.delfromData);
      }
    },
    // Lấy dữ liệu form hoàn tiền
    getRefundData(id, refund_type) {
      if (refund_type == 2) {
        this.delfromData = {
          title: 'Đồng ý trả hàng hoàn tiền',
          url: `/refund/agree/${id}`,
          method: 'get',
        };
        this.$modalSure(this.delfromData)
          .then((res) => {
            this.$message.success(res.msg);
            this.getOrderList();
          })
          .catch((res) => {
            this.$message.error(res.msg);
          });
      } else {
        this.$modalForm(getNewRefundFrom(id)).then(() => {
          this.getOrderList();
          this.$emit('changeGetTabs');
        });
      }
    },
    // Lấy dữ liệu biểu mẫu hoàn điểm
    getRefundIntegral(id) {
      refundIntegral(id)
        .then(async (res) => {
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Lấy dữ liệu biểu mẫu chi tiết
    getData(id, type) {
      let fun;
      if (type == 2) {
        fun = getDataInfoNew;
      } else {
        fun = getDataInfo;
      }
      fun(id)
        .then(async (res) => {
          this.orderDatalist = res.data;
          // if (this.orderDatalist.orderInfo.refund_img.length) {
          //   try {
          //     this.orderDatalist.orderInfo.refund_img = this.orderDatalist.orderInfo.refund_img;
          //   } catch (e) {
          //     this.orderDatalist.orderInfo.refund_img = [];
          //   }
          // }
          this.$nextTick((e) => {
            this.$refs.detailss.modals = true;
          });
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Xóa một đơn hàng
    delOrder(row, data) {
      if (row.is_del === 1) {
        this.$modalSure(data)
          .then((res) => {
            this.$message.success(res.msg);
            this.getOrderList();
          })
          .catch((res) => {
            this.$message.error(res.msg);
          });
      } else {
        this.$message.error('Trong các đơn hàng bạn chọn có đơn người dùng chưa xóa, không thể xóa đơn hàng mà người dùng chưa xóa!');
      }
    },
    // Sửa thành công
    submitFail() {
      this.getOrderList();
    },
    // Trạng thái chọn đơn hàng
    selectChange2(tab) {
      this.pagination.page = 1;
      this.getOrderList(tab);
    },
    // Dữ liệu biểu mẫu không hoàn tiền
    getNoRefundData(id) {
      this.$modalForm(getNewnoRefundFrom(id)).then(() => {
        this.getOrderList();
        this.$emit('changeGetTabs');
      });
    },
    // Danh sách đơn hàng
    getOrderList() {
      this.loading = true;
      orderRefundList(this.pagination)
        .then((res) => {
          this.loading = false;
          const { count, list, num } = res.data;
          this.total = count;
          this.tbody = list;
          this.num = num;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    nameSearch() {
      this.pagination.page = 1;
      this.getOrderList();
    },
    // Tìm kiếm đơn hàng
    orderSearch() {
      this.pagination.page = 1;
      this.getOrderList();
    },
    // Dữ liệu biểu mẫu thông tin giao hàng
    delivery(row) {
      getDistribution(row.id)
        .then(async (res) => {
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.code {
  position: relative;
}
.ivu-form-item {
}
.QRpic {
  width: 180px;
  height: 259px;

  img {
    width: 100%;
    height: 100%;
  }
}
.tabBox {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  .tabBox_img {
    width: 36px;
    height: 36px;

    img {
      width: 100%;
      height: 100%;
    }
  }
  .tabBox_tit {
    width: 60%;
    font-size: 12px !important;
    margin: 0 2px 0 10px;
    letter-spacing: 1px;
    padding: 5px 0;
    box-sizing: border-box;
  }
}
.pictrue-box {
  display: flex;
  align-item: center;
}
.pictrue {
  width: 25px;
  height: 25px;
}
.tab {
  display: flex;
  align-items: center;

  img {
    width: 36px;
    height: 36px;
    margin-right: 10px;
  }
}
.w-250 {
  max-width: 250px;
}
.w-120 {
  width: 120px;
}
</style>
