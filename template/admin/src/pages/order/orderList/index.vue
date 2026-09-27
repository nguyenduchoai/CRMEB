<template>
  <div>
    <product-list-details ref="productlist"></product-list-details>
  </div>
</template>

<script>
import productListDetails from './orderListDetails';
import { mapMutations } from 'vuex';
export default {
  name: 'list',
  components: {
    productListDetails,
  },
  data() {
    return {
      tabs: [
        {
          type: '',
          label: 'Tất cả đơn hàng',
          value: Number(this.tablists?.all) || 0,
          max: 999999,
        },
        {
          type: '1',
          label: 'Đơn hàng thường',
          value: Number(this.tablists?.general) || 0,
          max: 999999,
        },
        {
          type: '2',
          label: 'Đơn mua chung',
          value: Number(this.tablists?.pink) || 0,
          max: 999999,
        },
        {
          type: '3',
          label: 'Đơn flash sale',
          value: Number(this.tablists?.seckill) || 0,
          max: 999999,
        },
        {
          type: '4',
          label: 'Đơn săn giảm giá',
          value: Number(this.tablists?.bargain) || 0,
          max: 999999,
        },
        {
          type: '5',
          label: 'Đơn đặt trước',
          value: Number(this.tablists?.advance) || 0,
          max: 999999,
        },
      ],
      spinShow: false,
      currentTab: '',
      data: [],
      tablists: null,
    };
  },
  created() {
    this.getOrderType('');
    this.getOrderStatus('');
    this.getOrderTime('');
    this.getOrderNum('');
    this.getfieldKey('');
    this.getisDelIdListl('');
    this.getIsDel(1);
  },
  beforeDestroy() {
    this.getOrderType('');
    this.getOrderStatus('');
    this.getOrderTime('');
    this.getOrderNum('');
    this.getfieldKey('');
    this.getisDelIdListl('');
    this.getIsDel(1);
  },
  mounted() {},
  methods: {
    ...mapMutations('order', [
      'getOrderStatus',
      'getOrderTime',
      'getOrderNum',
      'getfieldKey',
      'getOrderType',
      'getisDelIdListl',
      'getIsDel',
      // 'onChangeChart'
    ]),
  },
};
</script>
<style lang="scss" scoped>
.product_tabs ::v-deep .ivu-tabs-bar {
  margin-bottom: 0px !important;
}
.product_tabs ::v-deep .ivu-page-header-content {
  margin-bottom: 0px !important;
}
.product_tabs ::v-deep .ivu-page-header-breadcrumb {
  margin-bottom: 0px !important;
}
::v-deep .el-badge__content.is-fixed {
  top: 7px;
}
</style>
