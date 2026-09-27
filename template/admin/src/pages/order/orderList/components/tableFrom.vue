<template>
  <div class="table_box">
    <div class="padding-add">
      <el-form
        ref="orderData"
        :model="orderData"
        label-width="80px"
        label-position="right"
        inline
        @submit.native.prevent
      >
        <el-form-item label="Loại đơn hàng:">
          <el-select v-model="orderData.status" clearable @change="selectChange2" placeholder="Tất cả">
            <el-option label="Tất cả đơn hàng" value="" />
            <el-option label="Đơn hàng thường" value="1" />
            <el-option v-permission="'combination'" label="Đơn mua chung" value="2" />
            <el-option v-permission="'seckill'" label="Đơn flash sale" value="3" />
            <el-option v-permission="'bargain'" label="Đơn săn giảm giá" value="4" />
            <el-option label="Đơn đặt trước" value="5" />
          </el-select>
        </el-form-item>
        <el-form-item label="Phương thức thanh toán:">
          <el-select
            v-model="orderData.pay_type"
            clearable
            @change="userSearchs"
            placeholder="Tất cả"
            class="form_content_width"
          >
            <el-option v-for="item in payList" :value="item.val" :label="item.label" :key="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="Thời gian tạo:">
          <el-date-picker
            clearable
            v-model="timeVal"
            type="daterange"
            @change="onchangeTime"
            format="yyyy/MM/dd"
            value-format="yyyy/MM/dd"
            start-placeholder="Ngày bắt đầu"
            end-placeholder="Ngày kết thúc"
            :picker-options="pickerOptions"
            style="width: 250px"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="Tìm kiếm đơn hàng:" prop="real_name" label-for="real_name">
          <el-input clearable v-model="orderData.real_name" placeholder="Vui lòng nhập" class="form_content_width">
            <el-select v-model="orderData.field_key" slot="prepend" style="width: 100px">
              <el-option value="all" label="Tất cả"></el-option>
              <el-option value="order_id" label="Mã đơn hàng"></el-option>
              <el-option value="uid" label="UID"></el-option>
              <el-option value="real_name" label="Họ tên người dùng"></el-option>
              <el-option value="user_phone" label="Số điện thoại người dùng"></el-option>
              <el-option value="title" label="Tên sản phẩm"></el-option>
            </el-select>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-db-click @click="orderSearch">Tra cứu</el-button>
          <el-button v-db-click @click="handleReset">Đặt lại</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import { putWrite } from '@/api/order';
import { exportOrderList } from '@/api/export';
import timeOptions from '@/libs/timeOptions';
export default {
  name: 'table_from',
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
      currentTab: '',
      grid: {
        xl: 8,
        lg: 8,
        md: 8,
        sm: 24,
        xs: 24,
      },
      // Điều kiện tìm kiếm
      orderData: {
        status: '',
        data: '',
        real_name: '',
        field_key: 'all',
        pay_type: '',
        type: '',
      },
      modalTitleSs: '',
      statusType: '',
      time: '',
      value2: [],
      modals2: false,
      timeVal: [],
      payList: [
        { label: 'Tất cả', val: '' },
        { label: 'WeChat Pay', val: '1' },
        { label: 'Thanh toán Alipay', val: '4' },
        { label: 'Thanh toán bằng số dư', val: '2' },
        { label: 'Thanh toán ngoại tuyến', val: '3' },
      ],
      pickerOptions: timeOptions,
    };
  },
  computed: {
    ...mapState('order', ['orderChartType', 'isDels', 'delIdList', 'orderType']),

    today() {
      const end = new Date();
      const start = new Date();
      var datetimeStart = start.getFullYear() + '/' + (start.getMonth() + 1) + '/' + start.getDate();
      var datetimeEnd = end.getFullYear() + '/' + (end.getMonth() + 1) + '/' + end.getDate();
      return [datetimeStart, datetimeEnd];
    },
  },
  watch: {
    $route() {
      if (this.$route.fullPath === this.$routeProStr + '/order/list?status=1') {
        this.getPath();
      }
    },
    'orderData.field_key': function (val, oval) {
      this.getfieldKey(val);
    },
  },
  created() {
    this.setOrderKeyword('');
    if (this.$route.fullPath === this.$routeProStr + '/order/list?status=1') {
      this.getPath();
    }
  },
  methods: {
    ...mapMutations('order', [
      'getOrderStatus',
      'getOrderType',
      'getOrderTime',
      'onChangeTabs',
      'setOrderKeyword',
      'getfieldKey',
      'resetSearch',
    ]),
    getPath() {
      this.orderData.status = this.$route.query.status.toString();
      this.getOrderStatus(this.orderData.status);
      this.$emit('getList', 1);
    },
    // Xuất
    async exportList() {
      this.orderData.type = this.orderType === 0 ? '' : this.orderType;
      let [th, filekey, data, fileName] = [[], [], [], ''];
      let excelData = JSON.parse(JSON.stringify(this.orderData));
      excelData.page = 1;
      excelData.limit = 200;
      excelData.ids = this.delIdList;
      for (let i = 0; i < excelData.page + 1; i++) {
        let lebData = await this.getExcelData(excelData);
        if (!fileName) fileName = lebData.filename;
        if (!filekey.length) {
          filekey = lebData.fileKey;
        }
        if (!th.length) th = lebData.header;
        if (lebData.export.length) {
          data = data.concat(lebData.export);
          excelData.page++;
        } else {
          this.$exportExcel(th, filekey, fileName, data);
          return;
        }
      }
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        exportOrderList(excelData).then((res) => {
          resolve(res.data);
        });
      });
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e || [];
      this.orderData.data = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.$store.dispatch('order/getOrderTabs', {
        type: this.orderData.status,
        data: this.orderData.data,
        pay_type: this.orderData.pay_type,
        field_key: this.orderData.field_key,
        real_name: this.orderData.real_name,
      });
      this.getOrderTime(this.orderData.data);
      this.$emit('getList', 1);
    },
    // Chọn giờ
    selectChange(tab) {
      this.$store.dispatch('order/getOrderTabs', {
        type: this.orderData.status,
        data: this.orderData.data,
        pay_type: this.orderData.pay_type,
        field_key: this.orderData.field_key,
        real_name: this.orderData.real_name,
      });
      this.orderData.data = tab;
      this.getOrderTime(this.orderData.data);
      this.timeVal = [];
      this.$emit('getList');
    },
    // Trạng thái chọn đơn hàng
    selectChange2(tab) {
      this.onChangeTabs(Number(tab));
      this.$store.dispatch('order/getOrderTabs', {
        type: this.orderData.status,
        data: this.orderData.data,
        pay_type: this.orderData.pay_type,
        field_key: this.orderData.field_key,
        real_name: this.orderData.real_name,
      });
      // this.$emit('getList', 1);
    },
    userSearchs(type) {
      this.getOrderType(type);
      this.$store.dispatch('order/getOrderTabs', {
        type: this.orderData.status,
        data: this.orderData.data,
        pay_type: this.orderData.pay_type,
        field_key: this.orderData.field_key,
        real_name: this.orderData.real_name,
      });
      this.$emit('getList', 1);
    },
    // Trạng thái thời gian
    timeChange(time) {
      this.getOrderTime(time);
      this.$emit('getList');
    },
    // Tìm kiếm theo mã đơn hàng
    orderSearch() {
      this.setOrderKeyword(this.orderData.real_name);
      this.getfieldKey(this.orderData.field_key);
      this.$emit('getList', 1);
      this.$store.dispatch('order/getOrderTabs', {
        type: this.orderData.status,
        data: this.orderData.data,
        pay_type: this.orderData.pay_type,
        field_key: this.orderData.field_key,
        real_name: this.orderData.real_name,
      });
    },
    // Bấm chọn loại đơn hàng
    onClickTab() {
      this.$emit('onChangeType', this.currentTab);
    },
    // Xóa hàng loạt
    delAll() {
      if (this.delIdList.length === 0) {
        this.$message.error('Vui lòng chọn đơn hàng cần xóa trước!');
      } else {
        if (this.isDels) {
          let idss = {
            ids: this.delIdList,
          };
          let delfromData = {
            title: 'Xóa đơn hàng',
            url: `/order/dels`,
            method: 'post',
            ids: idss,
          };
          this.$modalSure(delfromData)
            .then((res) => {
              this.$message.success(res.msg);
              this.$emit('getList');
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          this.$message.error('Trong các đơn hàng bạn chọn có đơn người dùng chưa xóa, không thể xóa đơn hàng mà người dùng chưa xóa!');
        }
      }
    },
    // Làm mới
    Refresh() {
      this.$emit('getList');
    },
    //
    handleReset() {
      this.timeVal = [];
      this.time = '';
      this.resetSearch();
      this.$emit('getList');
    },
  },
};
</script>
