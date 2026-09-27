<template>
  <div @resize="handleResize">
    <el-row :gutter="24">
      <el-col san="24" class="ivu-mb">
        <el-card :bordered="false" shadow="never" class="dashboard-console-visit">
          <div>
            <el-row justify="center" align="middle">
              <el-col :span="8" class="card-title">
                <el-avatar
                  icon="el-icon-s-marketing"
                  size="small"
                  style="color: var(--prev-color-primary); background-color: #e6f7ff"
                ></el-avatar>
                <h4 class="ivu-pl-8">Đơn hàng</h4>
              </el-col>
              <el-col :span="16" class="ivu-text-right">
                <el-radio-group v-model="visitDate" type="button" class="ivu-mr-8" @input="handleChangeVisitType">
                  <el-radio-button label="thirtyday">30 ngày</el-radio-button>
                  <el-radio-button label="week">Tuần</el-radio-button>
                  <el-radio-button label="month">Tháng</el-radio-button>
                  <el-radio-button label="year">năm</el-radio-button>
                </el-radio-group>
              </el-col>
            </el-row>
          </div>
          <echarts-from
            ref="visitChart"
            :series="series"
            :infoList="infoList"
            v-if="infoList"
            :yAxisData="yAxisData"
          ></echarts-from>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>
<script>
import { orderApi } from '@/api/index';
import echartsFrom from '@/components/echarts/index';
export default {
  components: { echartsFrom },
  data() {
    return {
      infoList: null,
      visitDate: 'thirtyday',
      series: [],
      yAxisData: [],
    };
  },
  methods: {
    // Thống kê
    getStatistics() {
      let data = {
        cycle: this.visitDate,
      };
      orderApi(data)
        .then(async (res) => {
          this.infoList = res.data || {};
          (this.series = this.infoList.series || []),
            (this.yAxisData = [
              {
                type: 'value',
                name: 'Số tiền',
                axisLine: {
                  show: false,
                },
                axisTick: {
                  show: false,
                },
                axisLabel: {
                  textStyle: {
                    color: '#7F8B9C',
                  },
                },
                splitLine: {
                  show: true,
                  lineStyle: {
                    color: '#F5F7F9',
                  },
                },
              },
              {
                type: 'value',
                name: 'Số lượng',
                axisLine: {
                  show: false,
                },
                axisTick: {
                  show: false,
                },
                axisLabel: {
                  textStyle: {
                    color: '#7F8B9C',
                  },
                },
                splitLine: {
                  show: true,
                  lineStyle: {
                    color: '#F5F7F9',
                  },
                },
                // axisLabel: {
                //     formatter: '{value} °C'
                // }
              },
            ]);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thời gian thay đổi
    handleChangeVisitType() {
      this.getStatistics();
    },
    // Theo dõi thay đổi chiều rộng trang, làm mới bảng
    handleResize() {
      if (this.infoList) this.$refs.visitChart.handleResize();
    },
  },
  created() {
    this.getStatistics();
  },
};
</script>
<style lang="scss" scoped>
.dashboard-console-visit {
  ul {
    li {
      list-style-type: none;
      margin-top: 12px;
    }
  }
}
.ivu-text-right {
  text-align: right;
}
.ivu-pl-8 {
  padding-left: 8px !important;
}
.card-title {
  display: flex;
  align-items: center;
  ::v-deep .el-avatar--small {
    background-color: var(--prev-color-primary-light-9) !important;
  }
}
</style>
