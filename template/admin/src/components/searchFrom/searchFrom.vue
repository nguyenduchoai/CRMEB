<template>
  <div class="table_box">
    <el-form
      ref="DataList"
      :model="DataList"
      :rules="rules"
      label-width="85px"
      :label-position="labelPosition"
      class="tabform"
    >
      <el-row :gutter="24" justify="end">
        <el-col :span="24" class="ivu-text-left">
          <el-form-item label="Trạng thái đơn hàng:">
            <el-radio-group v-model="DataList.status" type="button" @input="selectChange(DataList.status)">
              <el-radio-button :label="item.label" v-for="(item, i) in typeName" :key="i">{{
                item.name + '(' + item.num + ')'
              }}</el-radio-button>
            </el-radio-group>
          </el-form-item>
        </el-col>
        <el-col :span="24" class="ivu-text-left">
          <el-col v-bind="grid">
            <el-form-item label="Thời gian tạo:">
              <el-radio-group v-model="DataList.data" type="button" @input="timeChange(DataList.data)">
                <el-radio-button label="today">Hôm nay</el-radio-button>
                <el-radio-button label="yesterday">Hôm qua</el-radio-button>
                <el-radio-button label="lately7">7 ngày qua</el-radio-button>
                <el-radio-button label="lately30">30 ngày qua</el-radio-button>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid">
            <el-form-item class="tab_data">
              <el-date-picker
                :editable="false"
                v-model="value2"
                value-format="yyyy/MM/dd"
                type="daterange"
                range-separator="-"
                start-placeholder="Ngày bắt đầu"
                end-placeholder="Ngày kết thúc"
                style="width: 200px"
              ></el-date-picker>
            </el-form-item>
          </el-col>
        </el-col>
        <el-col :span="24" class="ivu-text-left" v-if="$route.path === routePro + '/echarts/trade/order'">
          <el-form-item label="Loại đơn hàng:">
            <el-radio-group v-model="currentTab" type="button" @input="onClickTab(currentTab)">
              <el-radio-button label="">Tất cả</el-radio-button>
              <el-radio-button label="1">Thường</el-radio-button>
              <el-radio-button v-permission="'combination'" label="2">Mua chung</el-radio-button>
              <el-radio-button v-permission="'bargain'" label="3">Săn giảm giá</el-radio-button>
              <el-radio-button v-permission="'seckill'" label="4">Flash sale</el-radio-button>
            </el-radio-group>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'searchFrom',
  props: {
    typeName: Array,
  },
  data() {
    return {
      routePro: this.$routeProStr,
      currentTab: '',
      grid: {
        xl: 8,
        lg: 8,
        md: 8,
        sm: 24,
        xs: 24,
      },
      // collapse: false,
      // Điều kiện tìm kiếm
      DataList: {
        status: '',
        data: '',
        real_name: '',
      },
      rules: {},
      statusType: '',
      time: '',
      value2: [],
    };
  },
  computed: {
    ...mapState('order', ['orderChartType']),
  },
  methods: {
    // Trạng thái chọn đơn hàng
    selectChange(status) {
      this.$emit('getTypeNum', status);
    },
    // Trạng thái thời gian
    timeChange(time) {
      this.$emit('getSeachTime', time);
    },
    // Tìm kiếm theo mã đơn hàng
    orderSearch(num) {
      this.getOrderNum(num);
      this.$emit('getList');
    },
    // Bấm chọn loại đơn hàng
    onClickTab() {
      this.$emit('onChangeType', this.currentTab);
    },
    handleSubmit() {
      this.$emit('on-submit', this.data);
    },
    // Làm mới
    Refresh() {
      this.$emit('getList');
    },
    handleReset() {
      this.$refs.form.resetFields();
      this.$emit('on-reset');
    },
  },
};
</script>

<style lang="scss" scoped>
.tab_data ::v-deep .ivu-form-item-content {
  margin-left: 0 !important;
}
.table_box ::v-deep .ivu-divider-horizontal {
  margin-top: 0px !important;
}
.table_box ::v-deep .ivu-form-item {
  margin-bottom: 15px !important;
}
.tabform {
  margin-bottom: 10px;
}
.Refresh {
  font-size: 12px;
  color: var(--prev-color-primary);
  cursor: pointer;
}
</style>
