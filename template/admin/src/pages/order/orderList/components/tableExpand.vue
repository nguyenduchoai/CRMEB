<template>
  <div class="expand">
    <el-row class="expand-row">
      <el-col :span="6">
        <span class="expand-key">Tổng tiền hàng:</span>
        <span class="expand-value" v-text="row.total_price"></span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Thời gian đặt hàng:</span>
        <span class="expand-value" v-text="row.add_time"></span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Người giới thiệu:</span>
        <span class="expand-value" v-text="row.spread_nickname ? row.spread_nickname : 'Không có'"></span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Đại lý khu vực:</span>
        <span class="expand-value" v-text="row.division_name ? row.division_name : 'Không có'"></span>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="6">
        <span class="expand-key">Ghi chú người dùng:</span>
        <span class="expand-value" v-text="row.mark ? row.mark : 'Không có'"></span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Ghi chú của người bán:</span>
        <span class="expand-value" v-text="row.remark ? row.remark : 'Không có'"></span>
      </el-col>
      <el-col :span="6" v-if="row.shipping_type == 2">
        <span class="expand-key">Cửa hàng xác nhận sử dụng:</span>
        <span class="expand-value" v-text="row.verify_code ? row.store_name : 'Không có'"></span>
      </el-col>
      <el-col :span="6" v-if="row.shipping_type == 2">
        <span class="expand-key">Mã xác nhận:</span>
        <span class="expand-value" v-text="row.verify_code ? row.verify_code : 'Không có'"></span>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  name: 'table-expand',
  props: {
    row: Object,
  },
};
</script>

<style scoped>
.expand {
  /* padding-left: 50px; */
}
.expand-row {
  margin-bottom: 16px;
}
</style>
