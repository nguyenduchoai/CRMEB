<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Chọn thời gian:">
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
          <el-form-item label="Tìm kiếm:" label-for="status">
            <el-input
              clearable
              placeholder="Vui lòng nhập họ tên, số điện thoại, UID"
              v-model="formValidate.nickname"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <cards-data :cardLists="cardLists" v-if="cardLists.length >= 0"></cards-data>
    <el-card :bordered="false" shadow="never">
      <el-button v-auth="['export-userAgent']" class="export" v-db-click @click="exports">Xuất</el-button>
      <el-table
        ref="selection"
        :data="tableList"
        class="mt14"
        v-loading="loading"
        empty-text="Chưa có dữ liệu"
        highlight-current-row
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hình ảnh sản phẩm" min-width="90">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.headimgurl ? scope.row.headimgurl : require('../../assets/images/moren.jpg')" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin người dùng" width="150">
          <template slot-scope="scope">
            <div class="name">
              <div class="item">Biệt danh: {{ scope.row.nickname }}</div>
              <div class="item">Họ tên: {{ scope.row.real_name }}</div>
              <div class="item">Điện thoại: {{ scope.row.phone }}</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Cấp độ CTV" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.agentLevel ? scope.row.agentLevel.name : '--' }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số người dùng được giới thiệu" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.spread_count }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số đơn hàng giới thiệu" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.spread_order.order_count }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Giá trị đơn hàng giới thiệu" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.spread_order.order_price || '0.00' }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Tổng tiền hoa hồng" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.brokerage_money }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền đã rút" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.extract_count_price }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số lần rút tiền" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.extract_count_num }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền chưa rút" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.new_money }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Người giới thiệu cấp trên" min-width="120">
          <template slot-scope="scope">
            <div>{{ scope.row.spread_name }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="120">
          <template slot-scope="scope">
            <a v-db-click @click="promoters(scope.row, 'man')">Người được giới thiệu</a>
            <el-divider direction="vertical"></el-divider>
            <template>
              <el-dropdown size="small" @command="changeMenu(scope.row, $event, scope.$index)" :transfer="true">
                <span class="el-dropdown-link">Xem thêm<i class="el-icon-arrow-down el-icon--right"></i> </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item command="1">Đơn hàng giới thiệu</el-dropdown-item>
                  <el-dropdown-item command="2">Mã QR giới thiệu</el-dropdown-item>
                  <el-dropdown-item command="3">Sửa người giới thiệu</el-dropdown-item>
                  <el-dropdown-item command="4" v-if="scope.row.spread_uid">Gỡ bỏ người giới thiệu</el-dropdown-item>
                  <el-dropdown-item command="5">Hủy tư cách cộng tác viên</el-dropdown-item>
                  <el-dropdown-item command="6">Sửa cấp độ CTV</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
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
    <!-- Danh sách người được giới thiệu-->
    <promoters-list ref="promotersLists"></promoters-list>
    <!-- Mã QR giới thiệu-->
    <el-dialog :visible.sync="modals" title="Mã QR giới thiệu" :close-on-click-modal="false" width="540px">
      <div class="acea-row row-around" v-loading="spinShow">
        <div class="acea-row row-column-around row-between-wrapper">
          <div class="QRpic" v-if="code_src"><img v-lazy="code_src" /></div>
          <span class="QRpic_sp1 mt10" v-db-click @click="getWeChat">Mã QR giới thiệu OA WeChat</span>
        </div>
        <div class="acea-row row-column-around row-between-wrapper">
          <div class="QRpic" v-if="code_xcx"><img v-lazy="code_xcx" /></div>
          <span class="QRpic_sp2 mt10" v-db-click @click="getXcx">Mã QR giới thiệu Mini Program</span>
        </div>
        <div class="acea-row row-column-around row-between-wrapper">
          <div class="QRpic" v-if="code_h5"><img v-lazy="code_h5" /></div>
          <span class="QRpic_sp2 mt10" v-db-click @click="getH5">Mã QR giới thiệu H5</span>
        </div>
      </div>
    </el-dialog>
    <!--Sửa người giới thiệu-->
    <el-dialog :visible.sync="promoterShow" title="Sửa người giới thiệu" width="540px" :show-close="true">
      <el-form ref="formInline" :model="formInline" label-width="100px" @submit.native.prevent>
        <el-form-item label="Ảnh đại diện người dùng:" prop="image">
          <div class="picBox" v-db-click @click="customer">
            <div class="pictrue" v-if="formInline.image">
              <img v-lazy="formInline.image" />
            </div>
            <div class="upLoad acea-row row-center-wrapper" v-else>
              <i class="el-icon-picture-outline" style="font-size: 24px"></i>
            </div>
          </div>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel('formInline')">Hủy</el-button>
        <el-button type="primary" v-db-click @click="putSend('formInline')">Gửi</el-button>
      </span>
    </el-dialog>
    <el-dialog :visible.sync="customerShow" title="Vui lòng chọn người dùng cửa hàng" :show-close="true" width="1000px">
      <customerInfo v-if="customerShow" @imageObject="imageObject"></customerInfo>
    </el-dialog>
  </div>
</template>

<script>
import cardsData from '@/components/cards/cards';
import searchFrom from '@/components/publicSearchFrom';
import { mapState } from 'vuex';
import {
  agentListApi,
  statisticsApi,
  lookCodeApi,
  lookxcxCodeApi,
  lookh5CodeApi,
  userAgentApi,
  agentSpreadApi,
} from '@/api/agent';
import promotersList from './handle/promotersList';
import customerInfo from '@/components/customerInfo';
import { membershipDataAddApi } from '@/api/membershipLevel';
export default {
  name: 'agentManage',
  components: { cardsData, searchFrom, promotersList, customerInfo },
  data() {
    return {
      customerShow: false,
      promoterShow: false,
      modals: false,
      spinShow: false,
      pickerOptions: this.$timeOptions,
      rows: {},
      formValidate: {
        nickname: '',
        data: '',
        page: 1,
        limit: 15,
      },
      date: 'all',
      total: 0,
      cardLists: [],
      loading: false,
      tableList: [],
      timeVal: [],
      code_src: '',
      code_xcx: '',
      code_h5: '',
      formInline: {
        uid: 0,
        spread_uid: 0,
        image: '',
      },
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
    this.getStatistics();
  },
  methods: {
    // Gửi
    putSend(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (!this.formInline.spread_uid) {
            return this.$message.error('Vui lòng chọn người dùng');
          }
          agentSpreadApi(this.formInline)
            .then((res) => {
              this.promoterShow = false;
              this.$message.success(res.msg);
              this.getList();
              this.$refs[name].resetFields();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        }
      });
    },
    // Xuất
    exports() {
      let formValidate = this.formValidate;
      let data = {
        data: formValidate.data,
        nickname: formValidate.nickname,
      };
      userAgentApi(data)
        .then((res) => {
          location.href = res.data[0];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thao tác
    changeMenu(row, name, index) {
      switch (name) {
        case '1':
          this.promoters(row, 'order'); //Đơn hàng người được giới thiệu
          break;
        case '2':
          this.spreadQR(row); //Mã QR hình thức giới thiệu
          break;
        case '3':
          this.editS(row); //Sửa người giới thiệu
          break;
        case '4': //Gỡ bỏ người giới thiệu
          this.del_parent(row, 'gỡ bỏ người giới thiệu của [ ' + row.nickname + ' ]', index);
          break;
        case '5': //Hủy tư cách cộng tác viên
          this.del_agent(row, 'hủy tư cách cộng tác viên của [ ' + row.nickname + ' ]', index);
          break;
        case '6': //Sửa hạng giới thiệu
          this.$modalForm(membershipDataAddApi({ uid: row.uid }, '/agent/get_level_form')).then(() => this.getList());
          break;
        default:
          break;
      }
    },
    editS(row) {
      this.promoterShow = true;
      this.formInline.uid = row.uid;
    },
    customer() {
      this.customerShow = true;
    },
    imageObject(e) {
      this.customerShow = false;
      this.formInline.spread_uid = e.uid;
      this.formInline.image = e.image;
    },
    // Xóa quan hệ người giới thiệu
    del_parent(rows, titile, num) {
      let delfromDatap = {
        title: titile,
        num: num,
        url: `agent/stair/delete_spread/${rows.uid}`,
        method: 'PUT',
        ids: '',
      };
      this.$modalSure(delfromDatap)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Hủy tư cách giới thiệu của bản thân
    del_agent(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `agent/stair/delete_system_spread/${row.uid}`,
        method: 'PUT',
        ids: '',
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
    edit(row) {
      this.promoterShow = true;
      this.formInline.uid = row.uid;
    },
    cancel(name) {
      this.promoterShow = false;
      this.$refs[name].resetFields();
    },
    // Danh sách người giới thiệu - đơn hàng
    promoters(row, tit) {
      this.$refs.promotersLists.modals = true;
      this.$refs.promotersLists.getList(row, tit);
    },
    // Thống kê
    getStatistics() {
      let data = {
        nickname: this.formValidate.nickname,
        data: this.formValidate.data,
      };
      statisticsApi(data)
        .then(async (res) => {
          let data = res.data;
          this.cardLists = data.res;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal ? this.timeVal.join('-') : '';
      this.formValidate.page = 1;
      if (!e[0]) {
        this.formValidate.data = '';
      }
      this.getList();
      this.getStatistics();
    },
    // Chọn giờ
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.data = tab;
      this.timeVal = [];
      this.getList();
      this.getStatistics();
    },
    // Danh sách
    getList() {
      this.loading = true;
      agentListApi(this.formValidate)
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
      this.formValidate.page = 1;
      this.getList();
      this.getStatistics();
    },
    // Mã QR
    spreadQR(row) {
      this.modals = true;
      this.rows = row;
      this.getWeChat();
      this.getXcx();
      this.getH5();
    },
    // Mã QR giới thiệu OA WeChat
    getWeChat() {
      this.spinShow = true;
      let data = {
        uid: this.rows.uid,
        action: 'wechant_code',
      };
      lookCodeApi(data)
        .then(async (res) => {
          let data = res.data;
          this.code_src = data.code_src;
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
    // Mã QR giới thiệu Mini Program
    getXcx() {
      this.spinShow = true;
      let data = {
        uid: this.rows.uid,
      };
      lookxcxCodeApi(data)
        .then(async (res) => {
          let data = res.data;
          this.code_xcx = data.code_src;
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
    getH5() {
      this.spinShow = true;
      let data = {
        uid: this.rows.uid,
      };
      lookh5CodeApi(data)
        .then(async (res) => {
          let data = res.data;
          this.code_h5 = data.code_src;
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
  },
};
</script>
<style lang="scss" scoped>
.picBox {
  display: inline-block;
  cursor: pointer;
  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
  }
  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
    }
  }
  .iconfont {
    color: #898989;
  }
}
.QRpic {
  width: 180px;
  height: 180px;

  img {
    width: 100%;
    height: 100%;
  }
}
.QRpic_sp1 {
  font-size: 13px;
  color: #19be6b;
  cursor: pointer;
}
.QRpic_sp2 {
  font-size: 13px;
  color: #2d8cf0;
  cursor: pointer;
}

img {
  height: 36px;
  display: block;
}
.ivu-mt .name .item {
  margin: 3px 0;
}
.tabform {
  margin-bottom: 10px;
}
.Refresh {
  font-size: 12px;
  color: var(--prev-color-primary);
  cursor: pointer;
}
.ivu-form-item {
  margin-bottom: 10px;
}

/* .ivu-mt ::v-deep .ivu-table-header */
/* border-top:1px dashed #ddd!important */
</style>
