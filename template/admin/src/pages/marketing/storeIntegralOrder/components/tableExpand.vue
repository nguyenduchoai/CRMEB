<template>
  <div>
    <el-row class="expand-row">
      <el-col :span="8">
        <span class="expand-key">Thời gian đặt hàng:</span>
        <span class="expand-value" v-text="row.add_time"></span>
      </el-col>
      <el-col :span="8">
        <span class="expand-key">Ghi chú người dùng:</span>
        <span class="expand-value" v-text="row.mark ? row.mark : 'Không có'"></span>
      </el-col>
      <el-col :span="8">
        <span class="expand-key">Ghi chú của người bán:</span>
        <span class="expand-value" v-text="row.remark ? row.remark : 'Không có'"></span>
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
.expand-row {
  margin-bottom: 16px;
}
</style>
