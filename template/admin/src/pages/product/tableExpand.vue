<template>
  <div>
    <el-row class="expand-row">
      <el-col :span="6">
        <span class="expand-key">Truy cập lần đầu:</span>
        <span class="expand-value"> {{ row.add_time | formatDate }}</span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Truy cập gần nhất:</span>
        <span class="expand-value">{{ row.last_time | formatDate }}</span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Số CCCD/CMND:</span>
        <span class="expand-value">{{ row.card_id }}</span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Họ tên:</span>
        <span class="expand-value">{{ row.real_name }}</span>
      </el-col>
    </el-row>
    <el-row class="expand-row">
      <el-col :span="6">
        <span class="expand-key">Nhãn:</span>
        <span class="expand-value">{{ row.labels }}</span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Ngày sinh:</span>
        <span class="expand-value">{{ row.birthday }}</span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Người giới thiệu:</span>
        <span class="expand-value">{{ row.spread_uid_nickname }}</span>
      </el-col>
      <el-col :span="6">
        <span class="expand-key">Địa chỉ:</span>
        <span class="expand-value">{{ row.addres }}</span>
      </el-col>
    </el-row>
    <el-row class="expand-row">
      <el-col :span="6">
        <span class="expand-key">Ghi chú:</span>
        <span class="expand-value">{{ row.mark }}</span>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { formatDate } from '@/utils/validate';
export default {
  name: 'table-expand',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
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
