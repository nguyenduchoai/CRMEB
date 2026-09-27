<template>
  <el-row :gutter="16">
    <el-col :xs="24" :sm="24" :md="24" :lg="18">
      <el-card :bordered="false" shadow="never" class="ivu-mt-16">
        <div class="acea-row row-between-wrapper">
          <h4 class="statics-header-title mb20">Phân bố khu vực người dùng</h4>
        </div>
        <el-row>
          <el-col :xs="24" :sm="24" :md="24" :lg="10">
            <div class="echarts">
              <div :style="{ height: '400px', width: '100%' }" ref="myEchart"></div>
            </div>
          </el-col>
          <el-col :xs="24" :sm="24" :md="24" :lg="14">
            <div class="tables">
              <el-table height="400" :columns="columns1" :data="resdataList">
                <el-table-column :label="item.title" :min-width="100" v-for="(item, index) in columns1" :key="index">
                  <template slot-scope="scope">
                    <template v-if="item.key">
                      <div>
                        <span>{{ scope.row[item.key] }}</span>
                      </div>
                    </template>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </el-col>
        </el-row>
      </el-card>
    </el-col>
    <el-col :xs="24" :sm="24" :md="24" :lg="6">
      <el-card :bordered="false" shadow="never" class="ivu-mt-16">
        <div class="acea-row row-between-wrapper">
          <h4 class="statics-header-title mb20">Tỷ lệ giới tính người dùng</h4>
        </div>
        <echarts-new
          :option-data="optionData"
          :styles="style"
          height="100%"
          width="100%"
          v-if="optionData"
        ></echarts-new>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
import echarts from 'echarts';
import { statisticWechatRegionApi, statisticWechatSexApi } from '@/api/statistic';
import echartsNew from '@/components/echartsNew/index';
export default {
  name: 'userRegion',
  components: {
    echartsNew,
  },
  props: {
    formInline: {
      type: Object,
      default: function () {
        return {
          channel_type: '',
          data: '',
        };
      },
    },
  },
  data() {
    return {
      chart: null,
      resdata: [],
      resdataList: [],
      columns1: [
        {
          title: 'TOP tỉnh/thành',
          key: 'province',
        },
        {
          title: 'Số người dùng tích lũy',
          key: 'allNum',
          sortable: true,
        },
        {
          title: 'Số người dùng mới',
          key: 'newNum',
          sortable: true,
        },
        {
          title: 'Số khách truy cập',
          key: 'visitNum',
          sortable: true,
        },
        {
          title: 'Số tiền thanh toán',
          key: 'payPrice',
          sortable: true,
        },
      ],
      style: { height: '400px' },
      optionData: {},
    };
  },
  mounted() {
    this.getTrend();
    this.getSex();
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    this.chart.dispose();
    this.chart = null;
  },
  methods: {
    regionConfigure() {
      // Dùng biểu đồ cột Top tỉnh/thành thay cho bản đồ: bản đồ Trung Quốc của ECharts thể hiện yêu sách "đường 9 đoạn"
      if (this.chart) {
        this.chart.dispose();
      }
      this.$nextTick(() => {
        let myChart = echarts.init(this.$refs.myEchart);
        this.chart = myChart;
        window.onresize = myChart.resize;
        const top = this.resdata
          .slice()
          .sort((a, b) => b.value - a.value)
          .slice(0, 10)
          .reverse();
        myChart.setOption({
          backgroundColor: '#fff',
          tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' },
            formatter: function (params) {
              const d = params[0].data;
              return `Khu vực: ${params[0].name}</br>Người dùng tích lũy: ${d.value}</br>Người dùng mới: ${d.newNum}</br>Khách truy cập: ${d.visitNum}</br>Số tiền thanh toán: ${d.payPrice}`;
            },
          },
          grid: { left: 10, right: 30, top: 10, bottom: 10, containLabel: true },
          xAxis: { type: 'value', minInterval: 1 },
          yAxis: { type: 'category', data: top.map((item) => item.name) },
          series: [{ type: 'bar', data: top, barMaxWidth: 20, itemStyle: { color: '#1890ff' } }],
        });
      });
    },
    // Biểu đồ thống kê
    getTrend() {
      statisticWechatRegionApi(this.formInline)
        .then(async (res) => {
          this.resdataList = res.data;
          this.resdata = res.data.map((item) => {
            let jsonData = {};
            jsonData.name = item.province.replace('Tỉnh', '');
            jsonData.value = item.allNum;
            jsonData.newNum = item.newNum;
            jsonData.payPrice = item.payPrice;
            jsonData.visitNum = item.visitNum;
            return jsonData;
          });
          this.regionConfigure();
        })
        .catch((res) => {
          this.$message.error(res);
        });
    },
    //Giới tính
    getSex() {
      statisticWechatSexApi(this.formInline)
        .then(async (res) => {
          let totalSumAll = 0;
          res.data.forEach((item) => {
            totalSumAll += item.value;
          });
          this.optionData = {
            title: {
              show: true,
              text: 'Tổng số người dùng', // Hiện tại đang hardcode
              subtext: totalSumAll, // Hiện tại đang hardcode
              x: 'center',
              y: 'center',
              textStyle: {
                fontSize: '14',
                color: '#666666',
              },
              subtextStyle: {
                fontSize: '30',
                fontWeight: 'bold',
                color: '#333333',
              },
            },
            tooltip: {
              trigger: 'item',
              formatter: '{a} <br/>{b}: {c} ({d}%)',
            },
            legend: {
              orient: 'vertical',
              left: 10,
              data: ['Không xác định', 'Nam', 'Nữ'],
            },
            series: [
              {
                name: 'Nguồn truy cập',
                type: 'pie',
                radius: ['50%', '70%'],
                avoidLabelOverlap: false,
                label: {
                  show: false,
                  position: 'center',
                },
                labelLine: {
                  show: false,
                },
                data: res.data,
                itemStyle: {
                  emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)',
                  },
                  normal: {
                    color: function (params) {
                      //Màu tùy chỉnh
                      var colorList = ['#999999', '#1890FF', '#FFAB2B'];
                      return colorList[params.dataIndex];
                    },
                  },
                },
              },
            ],
          };
        })
        .catch((res) => {
          this.$message.error(res);
        });
    },
  },
};
</script>

<style scoped lang="scss">
.echarts {
  width: 100%;
}
.tables {
  width: 100%;
  ::v-deep .ivu-table-overflowY {
    &::-webkit-scrollbar {
      width: 0;
    }
    &::-webkit-scrollbar-track {
      background-color: transparent;
    }
    &::-webkit-scrollbar-thumb {
      background: #e8eaec;
    }
  }
}
</style>
