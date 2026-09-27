<template>
  <div>
    <el-card :bordered="false" shadow="never">
      <el-tabs v-model="isChecked" @tab-click="onChangeType">
        <el-tab-pane label="SMS" name="1"></el-tab-pane>
        <el-tab-pane label="Thu thập sản phẩm" name="4"></el-tab-pane>
        <el-tab-pane label="Tra cứu vận chuyển" name="3"></el-tab-pane>
        <el-tab-pane label="In vận đơn điện tử" name="2"></el-tab-pane>
      </el-tabs>
      <!--Danh sách SMS-->
      <div class="note" v-if="isChecked === '1' && sms.open === 1">
        <div class="acea-row row-between-wrapper">
          <div>
            <span>Trạng thái SMS:</span>
            <el-radio-group type="button" v-model="tableFrom.type" @input="selectChange(tableFrom.type)">
              <el-radio-button label="">Tất cả</el-radio-button>
              <el-radio-button label="1">Thành công</el-radio-button>
              <el-radio-button label="2">Thất bại</el-radio-button>
              <el-radio-button label="0">Đang gửi</el-radio-button>
            </el-radio-group>
          </div>
          <div>
            <el-button type="primary" v-db-click @click="shortMes">Mẫu SMS</el-button>
            <el-button style="margin-left: 20px" v-db-click @click="editSign">Sửa chữ ký</el-button>
          </div>
        </div>
        <el-table
          :data="tableList"
          v-loading="loading"
          highlight-current-row
          no-userFrom-text="Chưa có dữ liệu"
          no-filtered-userFrom-text="Không có kết quả phù hợp"
          class="mt14"
        >
          <el-table-column label="Số điện thoại" width="100">
            <template slot-scope="scope">
              <span>{{ scope.row.phone }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Nội dung mẫu" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.content }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Số tin (mỗi 67 ký tự/+1)" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.num }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thời gian gửi" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.add_time }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Mã trạng thái" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row._resultcode }}</span>
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
      </div>
      <!--Danh sách thu thập sản phẩm, vận chuyển, vận đơn điện tử-->
      <div
        v-else-if="
          (isChecked === '3' && query.open === 1) ||
          (isChecked === '4' && copy.open === 1) ||
          (isChecked === '2' && dump.open === 1)
        "
      >
        <el-table
          :data="tableList"
          v-loading="loading"
          highlight-current-row
          no-userFrom-text="Chưa có dữ liệu"
          no-filtered-userFrom-text="Không có kết quả phù hợp"
          class="mt14"
        >
          <el-table-column
            :label="item.title"
            :min-width="item.minWidth"
            v-for="(item, index) in columns2"
            :key="index"
          >
            <template slot-scope="scope">
              <template v-if="item.key">
                <div>
                  <span>{{ scope.row[item.key] }}</span>
                </div>
              </template>
              <template v-else-if="item.slot === 'num' && isChecked === '3' && query.open === 1">
                <div>{{ scope.row.content.num }}</div>
              </template>
            </template>
          </el-table-column>
        </el-table>
        <div class="acea-row row-right page">
          <pagination
            v-if="total"
            :total="total"
            :page.sync="tableFrom.page"
            :limit.sync="tableFrom.limit"
            @pagination="getRecordList"
          />
        </div>
      </div>
      <!--Chưa kích hoạt-->
      <div v-else>
        <!--Nút kích hoạt-->
        <div
          v-if="
            (isChecked === '1' && !isSms) ||
            (isChecked === '2' && !isDump) ||
            (isChecked === '3' && !isLogistics) ||
            (isChecked === '4' && !isCopy)
          "
          class="wuBox acea-row row-column-around row-middle"
        >
          <div class="wuTu"><img src="../../../assets/images/wutu.png" /></div>
          <span v-if="isChecked === '1'">
            <span class="wuSp1">Dịch vụ SMS chưa được kích hoạt</span>
            <span class="wuSp2">Nhấn nút “Kích hoạt ngay” để sử dụng dịch vụ SMS nhé~~~</span>
          </span>
          <span v-if="isChecked === '4'">
            <span class="wuSp1">Dịch vụ thu thập sản phẩm chưa được kích hoạt</span>
            <span class="wuSp2">Nhấn nút “Kích hoạt ngay” để sử dụng dịch vụ thu thập sản phẩm nhé~~~</span>
          </span>
          <span v-if="isChecked === '3'">
            <span class="wuSp1">Tra cứu vận chuyển chưa được kích hoạt</span>
            <span class="wuSp2">Nhấn nút “Kích hoạt ngay” để sử dụng dịch vụ tra cứu vận chuyển nhé~~~</span>
          </span>
          <span v-if="isChecked === '2'">
            <span class="wuSp1">In vận đơn điện tử chưa được kích hoạt</span>
            <span class="wuSp2">Nhấn nút “Kích hoạt ngay” để sử dụng dịch vụ in vận đơn điện tử nhé~~~</span>
          </span>
          <el-button size="default" type="primary" v-db-click @click="onOpen">Kích hoạt ngay</el-button>
        </div>
        <!--Kích hoạt SMS ngay-->
        <div class="smsBox" v-if="isSms && isChecked === '1'">
          <div class="index_from page-account-container">
            <div class="page-account-top">
              <span class="page-account-top-tit">Kích hoạt dịch vụ SMS</span>
            </div>
            <el-form
              ref="formInline"
              :model="formInline"
              :rules="ruleInline"
              @submit.native.prevent
              @keyup.enter="handleSubmit('formInline')"
            >
              <el-form-item prop="sign" class="maxInpt">
                <el-input
                  type="text"
                  v-model="formInline.sign"
                  prefix="ios-contact-outline"
                  placeholder="Vui lòng nhập chữ ký SMS"
                />
              </el-form-item>
              <el-form-item class="maxInpt">
                <el-button type="primary" long size="default" v-db-click @click="handleSubmit('formInline')" class="btn"
                  >Đăng nhập</el-button
                >
              </el-form-item>
            </el-form>
          </div>
        </div>
        <!--Kích hoạt vận đơn điện tử ngay-->
        <div class="smsBox" v-if="isDump && isChecked === '2'">
          <div class="index_from page-account-container">
            <div class="page-account-top">
              <span class="page-account-top-tit" v-if="isChecked === '2'">Kích hoạt dịch vụ vận đơn điện tử</span>
              <span class="page-account-top-tit" v-if="isChecked === '3'">Kích hoạt dịch vụ tra cứu vận chuyển</span>
            </div>
            <el-form
              ref="formInlineDump"
              :model="formInlineDump"
              :rules="ruleInlineDump"
              @submit.native.prevent
              @keyup.enter="handleSubmitDump('formInlineDump')"
            >
              <el-form-item prop="com" class="maxInpt">
                <el-select
                  v-model="formInlineDump.com"
                  placeholder="Vui lòng chọn đơn vị vận chuyển"
                  @change="onChangeExport"
                  style="text-align: left"
                >
                  <el-option
                    v-for="(item, index) in exportList"
                    :value="item.code"
                    :key="index"
                    :label="item.name"
                  ></el-option>
                </el-select>
              </el-form-item>
              <el-form-item prop="temp_id" class="tempId maxInpt">
                <div class="acea-row">
                  <el-select
                    v-model="formInlineDump.temp_id"
                    placeholder="Vui lòng chọn mẫu vận đơn điện tử"
                    style="text-align: left"
                    :class="[formInlineDump.temp_id ? 'width9' : 'width10']"
                    @change="onChangeImg"
                  >
                    <el-option
                      v-for="(item, index) in exportTempList"
                      :value="item.temp_id"
                      :key="index"
                      :label="item.title"
                    ></el-option>
                  </el-select>
                  <div v-if="formInlineDump.temp_id">
                    <span class="tempImg">Xem trước</span>
                    <div class="tabBox_img" v-viewer>
                      <img v-lazy="tempImg" />
                    </div>
                  </div>
                </div>
              </el-form-item>
              <el-form-item prop="to_name" class="maxInpt">
                <el-input
                  type="text"
                  v-model="formInlineDump.to_name"
                  prefix="ios-contact-outline"
                  placeholder="Vui lòng điền họ tên người gửi"
                />
              </el-form-item>
              <el-form-item prop="to_tel" class="maxInpt">
                <el-input
                  type="text"
                  v-model="formInlineDump.to_tel"
                  prefix="ios-contact-outline"
                  placeholder="Vui lòng điền số điện thoại người gửi"
                />
              </el-form-item>
              <el-form-item prop="to_address" class="maxInpt">
                <el-input
                  type="text"
                  v-model="formInlineDump.to_address"
                  prefix="ios-contact-outline"
                  placeholder="Vui lòng điền địa chỉ chi tiết của người gửi"
                />
              </el-form-item>
              <el-form-item prop="siid" class="maxInpt">
                <el-input
                  type="text"
                  v-model="formInlineDump.siid"
                  prefix="ios-contact-outline"
                  placeholder="Vui lòng nhập mã số máy in đám mây"
                />
              </el-form-item>
              <el-form-item class="maxInpt">
                <el-button
                  type="primary"
                  long
                  size="default"
                  v-db-click
                  @click="handleSubmitDump('formInlineDump')"
                  class="btn"
                  >Kích hoạt ngay</el-button
                >
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
    </el-card>
    <el-dialog
      :visible.sync="modals"
      title="Sửa chữ ký tài khoản SMS"
      width="540px"
      class="order_box"
      @closed="cancel('formInline')"
    >
      <el-form ref="formInline" :model="formInline" :rules="ruleInline" label-width="100px" @submit.native.prevent>
        <el-form-item>
          <el-input
            v-model="accountInfo.account"
            disabled
            prefix="ios-person-outline"
            size="large"
            style="width: 87%"
          ></el-input>
        </el-form-item>
        <el-form-item prop="sign">
          <el-input
            v-model="formInline.sign"
            prefix="ios-document-outline"
            placeholder="Vui lòng nhập chữ ký SMS, ví dụ: CRMEB"
            size="large"
            style="width: 87%"
          ></el-input>
        </el-form-item>
        <el-form-item prop="phone">
          <el-input
            v-model="formInline.phone"
            prefix="ios-call-outline"
            placeholder="Vui lòng nhập số điện thoại của bạn"
            size="large"
            style="width: 87%"
          ></el-input>
        </el-form-item>
        <el-form-item prop="code">
          <div class="code acea-row row-middle" style="width: 87%">
            <el-input
              type="text"
              v-model="formInline.code"
              prefix="ios-keypad-outline"
              placeholder="Mã xác thực"
              size="large"
              style="width: 75%"
            />
            <el-button :disabled="!this.canClick" v-db-click @click="cutDown" size="large">{{ cutNUm }}</el-button>
          </div>
        </el-form-item>
        <el-form-item>
          <el-button
            type="primary"
            long
            size="large"
            v-db-click
            @click="editSubmit('formInline')"
            class="btn"
            style="width: 87%"
            >Xác nhận sửa</el-button
          >
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import {
  smsRecordApi,
  serveInfoApi,
  serveSmsOpenApi,
  serveOpnExpressApi,
  serveOpnOtherApi,
  serveRecordListApi,
  exportTempApi,
  exportAllApi,
  serveSign,
  captchaApi,
  serveOpen,
} from '@/api/setting';
export default {
  name: 'tableList',
  props: {
    copy: {
      type: Object,
      default: null,
    },
    dump: {
      type: Object,
      default: null,
    },
    query: {
      type: Object,
      default: null,
    },
    sms: {
      type: Object,
      default: null,
    },
    accountInfo: {
      type: Object,
      default: null,
    },
  },
  data() {
    const validatePhone = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Vui lòng điền số điện thoại'));
      } else if (!/^1[3456789]\d{9}$/.test(value)) {
        callback(new Error('Số điện thoại không đúng định dạng!'));
      } else {
        callback();
      }
    };
    return {
      cutNUm: 'Lấy mã xác thực',
      canClick: true,
      spinShow: true,
      formInline: {
        sign: '',
        phone: '',
        code: '',
      },
      ruleInline: {
        sign: [{ required: true, message: 'Vui lòng nhập chữ ký SMS', trigger: 'blur' }],
        phone: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        code: [{ required: true, message: 'Vui lòng nhập mã xác thực', trigger: 'blur' }],
      },
      isChecked: '1',
      columns2: [],
      tableFrom: {
        page: 1,
        limit: 20,
        type: '',
      },
      total: 0,
      loading: false,
      tableList: [],
      formInlineDump: {
        temp_id: '',
        com: '',
        to_name: '',
        to_tel: '',
        siid: '',
        to_address: '',
      },
      ruleInlineDump: {
        com: [{ required: true, message: 'Vui lòng chọn đơn vị vận chuyển', trigger: 'change' }],
        temp_id: [{ required: true, message: 'Vui lòng chọn mẫu in', trigger: 'change' }],
        to_name: [{ required: true, message: 'Vui lòng nhập họ tên người gửi', trigger: 'blur' }],
        to_tel: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        siid: [{ required: true, message: 'Vui lòng nhập mã số máy in đám mây', trigger: 'blur' }],
        to_address: [{ required: true, message: 'Vui lòng nhập địa chỉ người gửi', trigger: 'blur' }],
      },
      tempImg: '', // Hình ảnh
      exportTempList: [], // Mẫu vận đơn điện tử
      exportList: [], // Danh sách đơn vị vận chuyển
      isSms: false, // Có kích hoạt SMS hay không
      isDump: false, // Có mở vận đơn điện tử không
      isCopy: false, // Có kích hoạt thu thập sản phẩm hay không
      modals: false,
      isLogistics: false, //Có kích hoạt tra cứu vận chuyển hay không
    };
  },
  watch: {
    sms(n) {
      if (n.open === 1) this.getList();
    },
  },
  created() {
    if (this.isChecked === '1' && this.sms.open === 1) this.getList();
  },
  // mounted() {
  //     serveDumpOpen().then(res=>{
  //         this.isLogistics = res.data.isOpen
  //     })
  // },
  methods: {
    //Trang mẫu SMS
    shortMes() {
      this.$router.push({
        path: this.$routeProStr + '/setting/sms/sms_template_apply/index',
      });
    },
    // Mã xác thực SMS
    cutDown() {
      if (this.formInline.phone) {
        if (!this.canClick) return;
        this.canClick = false;
        this.cutNUm = 60;
        let data = {
          phone: this.formInline.phone,
        };
        captchaApi(data)
          .then(async (res) => {
            this.$message.success(res.msg);
          })
          .catch((res) => {
            this.$message.error(res.msg);
          });
        let time = setInterval(() => {
          this.cutNUm--;
          if (this.cutNUm === 0) {
            this.cutNUm = 'Lấy mã xác thực';
            this.canClick = true;
            clearInterval(time);
          }
        }, 1000);
      } else {
        this.$message.warning('Vui lòng nhập số điện thoại!');
      }
    },
    editSign() {
      this.formInline.sign = this.accountInfo.sms.sign;
      this.modals = true;
    },
    cancel(name) {
      this.modals = false;
      this.$refs[name].resetFields();
    },
    // Gửi
    editSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          serveSign(this.formInline)
            .then((res) => {
              this.modals = false;
              this.$message.success(res.msg);
              this.$refs[name].resetFields();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        }
      });
    },
    onChangeImg(item) {
      this.exportTempList.map((i) => {
        if (i.temp_id === item) this.tempImg = i.pic;
      });
    },
    // Đơn vị vận chuyển
    exportTempAllList() {
      exportAllApi()
        .then(async (res) => {
          this.exportList = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Chọn đơn vị vận chuyển
    onChangeExport(val) {
      this.formInlineDump.temp_id = '';
      this.exportTemp(val);
    },
    // Mẫu vận đơn điện tử
    exportTemp(val) {
      exportTempApi({ com: val })
        .then(async (res) => {
          this.exportTempList = res.data.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    onChangeType() {
      if (this.isChecked === '1' && this.sms.open === 1) {
        this.tableFrom.type = '';
        this.getList();
      } else {
        // if ((this.isChecked === '2' && this.query.open === 0) || (this.dump.open === 0 && this.isChecked === '3')) this.isDump = false
        if (this.isChecked === '2' && this.query.open === 0) this.isDump = false;
        if (this.isChecked === '3' && this.query.open === 0) this.isLogistics = false;
        if (this.dump.open === 1 || this.query.open === 1 || this.copy.open === 1) this.getRecordList();
      }
    },
    // Danh sách khác
    getRecordList() {
      this.loading = true;
      this.tableFrom.type = this.isChecked;
      serveRecordListApi(this.tableFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableList = data.data;
          this.total = res.data.count;
          switch (this.isChecked) {
            case '2':
              this.columns2 = [
                {
                  title: 'Mã đơn hàng',
                  key: 'order_id',
                  minWidth: 150,
                },
                {
                  title: 'Người gửi hàng',
                  key: 'from_name',
                  minWidth: 120,
                },
                {
                  title: 'Người nhận hàng',
                  key: 'to_name',
                  minWidth: 120,
                },
                {
                  title: 'Mã vận đơn',
                  key: 'num',
                  minWidth: 120,
                },
                {
                  title: 'Mã đơn vị vận chuyển',
                  key: 'code',
                  minWidth: 120,
                },
                {
                  title: 'Trạng thái',
                  key: '_resultcode',
                  minWidth: 100,
                },
                {
                  title: 'Thời gian in',
                  key: 'add_time',
                  minWidth: 150,
                },
              ];
              break;
            case '3':
              this.columns2 = [
                {
                  title: 'Mã vận đơn',
                  slot: 'num',
                  minWidth: 120,
                },
                {
                  title: 'Mã đơn vị vận chuyển',
                  key: 'code',
                  minWidth: 120,
                },
                {
                  title: 'Trạng thái',
                  key: '_resultcode',
                  minWidth: 120,
                },
                {
                  title: 'Thời gian thêm',
                  key: 'add_time',
                  minWidth: 150,
                },
              ];
              break;
            default:
              this.columns2 = [
                {
                  title: 'Sao chép URL',
                  key: 'url',
                  minWidth: 400,
                },
                {
                  title: 'Trạng thái yêu cầu',
                  key: '_resultcode',
                  minWidth: 120,
                },
                {
                  title: 'Thời gian thêm',
                  key: 'add_time',
                  minWidth: 150,
                },
              ];
              break;
          }
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Gửi kích hoạt SMS
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          serveSmsOpenApi(this.formInline)
            .then(async (res) => {
              this.$message.success('Kích hoạt thành công!');
              this.getList();
              this.$emit('openService', 'sms');
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    // Đi kích hoạt từ trang chủ
    onOpenIndex(val) {
      switch (val) {
        case 'sms':
          this.isChecked = '1';
          this.isSms = true;
          break;
        case 'copy':
          this.isChecked = '4';
          this.openOther();
          break;
        case 'query':
          this.isChecked = '3';
          this.onDumpOpen();
          break;
        default:
          this.isChecked = '2';
          this.openDump();
          break;
      }
    },
    // Nút kích hoạt
    onOpen() {
      if (this.isChecked === '1') this.isSms = true;
      if (this.isChecked === '2') this.openDump();
      if (this.isChecked === '3') this.onDumpOpen();
      if (this.isChecked === '4') this.openOther();
    },
    // Kích hoạt vận chuyển
    onDumpOpen() {
      this.$msgbox({
        title: 'Kích hoạt tra cứu vận chuyển',
        message: 'Bạn có chắc muốn kích hoạt tra cứu vận chuyển?',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xác nhận',
        iconClass: 'el-icon-warning',
        confirmButtonClass: 'btn-custom-cancel',
      })
        .then(() => {
          serveOpen().then((res) => {
            this.getRecordList();
            this.isLogistics = true;
            this.$message.info(res.msg);
            this.$emit('openService', 'query');
          });
        })
        .catch(() => {});
    },
    // Kích hoạt khác
    openOther() {
      this.$msgbox({
        title: 'Kích hoạt thu thập sản phẩm',
        message: 'Bạn có chắc muốn kích hoạt thu thập sản phẩm?',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xác nhận',
        iconClass: 'el-icon-warning',
        confirmButtonClass: 'btn-custom-cancel',
      })
        .then(() => {
          setTimeout(() => {
            serveOpnOtherApi({ type: 1 })
              .then(async (res) => {
                this.getRecordList();
                this.$emit('openService', 'copy');
              })
              .catch((res) => {
                this.$message.error(res.msg);
              });
          }, 300);
        })
        .catch(() => {});
    },
    // Mở dịch vụ vận đơn điện tử
    openDump() {
      this.exportTempAllList();
      this.isDump = true;
    },
    // Chọn
    selectChange(tab) {
      this.tableFrom.type = tab;
      this.tableFrom.page = 1;
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      smsRecordApi(this.tableFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableList = data.data;
          this.total = res.data.count;
          this.spinShow = false;
          this.loading = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.getList();
    },
    handleSubmitDump(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          serveOpnExpressApi(this.formInlineDump)
            .then(async (res) => {
              this.$message.success('Kích hoạt thành công!');
              this.getRecordList();
              this.$emit('openService', 'dump');
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
  },
};
</script>
<style lang="scss" scoped>
.order_box ::v-deep .ivu-form-item-content {
  margin-left: 50px !important;
}
.maxInpt {
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}
.smsBox .page-account-top {
  text-align: center;
  margin: 70px 0 30px 0;
}
.note {
  margin-top: 15px;
}
.tempImg {
  cursor: pointer;
  margin-left: 11px;
  color: var(--prev-color-primary);
}
.tabBox_img {
  opacity: 0;
  width: 38px;
  height: 30px;
  margin-top: -30px;
  cursor: pointer;
  img {
    width: 100%;
    height: 100%;
  }
}
.width9 {
  width: 90%;
}
.width10 {
  width: 100%;
}
.wuBox {
  width: 100%;
}
.wuSp1 {
  display: block;
  text-align: center;
  color: #000000;
  font-size: 21px;
  font-weight: 500;
  line-height: 32px;
  margin-top: 23px;
  margin-bottom: 5px;
}
.wuSp2 {
  opacity: 45%;
  font-weight: 400;
  color: #000000;
  line-height: 22px;
  margin-bottom: 30px;
}
.page-account-top-tit {
  font-size: 21px;
  color: var(--prev-color-primary);
}
.wuTu {
  width: 295px;
  height: 164px;
  margin-top: 54px;
  img {
    width: 100%;
    height: 100%;
  }

  + span {
    margin-bottom: 20px;
  }
}
.tempId {
  cursor: pointer;
  margin-left: 11px;
  color: var(--prev-color-primary);
  ::v-deep .ivu-form-item-content {
    text-align: left !important;
  }
}
.tabBox_img {
  opacity: 0;
  width: 38px;
  height: 30px;
  margin-top: -30px;
  cursor: pointer;
  img {
    width: 100%;
    height: 100%;
  }
}
.width9 {
  width: 90%;
}
.width10 {
  width: 100%;
}
.wuBox {
  width: 100%;
}
.wuSp1 {
  display: block;
  text-align: center;
  color: #000000;
  font-size: 21px;
  font-weight: 500;
  line-height: 32px;
  margin-top: 23px;
  margin-bottom: 5px;
}
.wuSp2 {
  opacity: 45%;
  font-weight: 400;
  color: #000000;
  line-height: 22px;
  margin-bottom: 30px;
}
.page-account-top-tit {
  font-size: 21px;
  color: var(--prev-color-primary);
}
.wuTu {
  width: 295px;
  height: 164px;
  margin-top: 54px;
  img {
    width: 100%;
    height: 100%;
  }

  + span {
    margin-bottom: 20px;
  }
}
.tempId {
  cursor: pointer;
  margin-left: 11px;
  color: var(--prev-color-primary);
  ::v-deep .ivu-form-item-content {
    text-align: left !important;
  }
}
.tabBox_img {
  opacity: 0;
  width: 38px;
  height: 30px;
  margin-top: -30px;
  cursor: pointer;
  img {
    width: 100%;
    height: 100%;
  }
}
.width9 {
  width: 90%;
}
.width10 {
  width: 100%;
}
.wuBox {
  width: 100%;
}
.wuSp1 {
  display: block;
  text-align: center;
  color: #000000;
  font-size: 21px;
  font-weight: 500;
  line-height: 32px;
  margin-top: 23px;
  margin-bottom: 5px;
}
.wuSp2 {
  opacity: 45%;
  font-weight: 400;
  color: #000000;
  line-height: 22px;
  margin-bottom: 30px;
}
.page-account-top-tit {
  font-size: 21px;
  color: var(--prev-color-primary);
}
.wuTu {
  width: 295px;
  height: 164px;
  margin-top: 54px;
  img {
    width: 100%;
    height: 100%;
  }

  + span {
    margin-bottom: 20px;
  }
}
.tempId {
  ::v-deep .ivu-form-item-content {
    text-align: left !important;
  }
}
</style>
