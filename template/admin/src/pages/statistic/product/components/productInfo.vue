<template>
  <el-card :bordered="false" shadow="never" class="ivu-mt" v-loading="spinShow">
    <div class="acea-row row-between-wrapper mb20">
      <div class="statics-header-title">
        <h4>Tổng quan sản phẩm</h4>
        <el-tooltip width="500" placement="right-start">
          <i class="el-icon-question ml10"></i>
          <div slot="content">
            <div>Lượt xem sản phẩm</div>
            <div>Trong điều kiện đã chọn, số lần các trang chi tiết sản phẩm được truy cập, một người truy cập nhiều lần trong thời gian thống kê sẽ được tính nhiều lần</div>
            <br />
            <div>Số khách truy cập sản phẩm</div>
            <div>Trong điều kiện đã chọn, số người truy cập bất kỳ trang chi tiết sản phẩm nào, một người truy cập nhiều lần trong khoảng thời gian thống kê chỉ được tính là một</div>
            <br />
            <div>Số lượng thanh toán</div>
            <div>
              Trong điều kiện đã chọn, tổng số lượng sản phẩm của các đơn hàng đã thanh toán thành công (sản phẩm mua chung được tính sau khi nhóm mua chung thành công, đơn hàng thanh toán ngoại tuyến được tính sau khi xác nhận thanh toán ở trang quản trị, không loại trừ đơn hoàn tiền)
            </div>
            <br />
            <div>Số tiền thanh toán</div>
            <div>
              Trong điều kiện đã chọn, tổng giá trị sản phẩm của các đơn hàng đã thanh toán thành công (sản phẩm mua chung được tính sau khi nhóm mua chung thành công, đơn hàng thanh toán ngoại tuyến được tính sau khi xác nhận thanh toán ở trang quản trị, không loại trừ đơn hoàn tiền)
            </div>
            <br />
            <div>Số lượng hoàn tiền</div>
            <div>Trong điều kiện đã chọn, tổng số lượng sản phẩm đã hoàn tiền thành công</div>
            <br />
            <div>Số tiền hoàn</div>
            <div>Trong điều kiện đã chọn, tổng giá trị sản phẩm đã hoàn tiền thành công</div>
          </div>
        </el-tooltip>
      </div>
      <div class="acea-row">
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
        <el-button type="primary" v-db-click @click="onSeach">Tra cứu</el-button>
        <el-button type="primary" v-db-click @click="excel">Xuất</el-button>
      </div>
    </div>
    <div class="mb20">
      <el-row>
        <el-col v-bind="grid" class="acea-row mb30" v-for="(item, index) in list" :key="index">
          <div
            class="iconCrl mr15"
            :class="{ one: index % 4 == 0, two: index % 4 == 1, three: index % 4 == 2, four: index % 4 == 3 }"
          >
            <i :class="item.icon" class="iconfont"></i>
          </div>
          <div class="info">
            <span class="sp1" v-text="item.name"></span>
            <span class="sp2" v-if="index === list.length - 1" v-text="item.list.num"></span>
            <span class="sp2" v-else v-text="item.list.num"></span>
            <span class="content-time spBlock"
              >Tăng trưởng so với kỳ trước:<i class="content-is" :class="Number(item.list.percent) >= 0 ? 'up' : 'down'"
                >{{ item.list.percent }}%</i
              >
              <i
                :style="{ color: Number(item.list.percent) >= 0 ? '#F5222D' : '#39C15B' }"
                :class="[Number(item.list.percent) >= 0 ? 'el-icon-caret-top' : 'el-icon-caret-bottom']"
              />
            </span>
          </div>
        </el-col>
      </el-row>
    </div>
    <echarts-new :option-data="optionData" :styles="style" height="100%" width="100%" v-if="optionData"></echarts-new>
  </el-card>
</template>

<script>
import { statisticBasicApi, statisticTrendApi, statisticProductExcel } from '@/api/statistic';
import echartsNew from '@/components/echartsNew/index';
import { formatDate } from '@/utils/validate';
export default {
  name: 'productInfo',
  components: {
    echartsNew,
  },
  data() {
    return {
      spinShow: false,
      grid: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 24,
      },
      pickerOptions: this.$timeOptions,
      name: '30 ngày gần đây',
      timeVal: [],
      dataTime: '',
      list: [],
      optionData: {},
      style: { height: '400px' },
    };
  },
  created() {
    const end = new Date();
    const start = new Date();
    start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 29)));
    this.timeVal = [start, end];
    this.dataTime = formatDate(start, 'yyyy/MM/dd') + '-' + formatDate(end, 'yyyy/MM/dd');
  },
  mounted() {
    this.getStatistics();
    this.getTrend();
  },
  methods: {
    // Xuất
    excel() {
      statisticProductExcel({ data: this.dataTime }).then(async (res) => {
        res.data.url.map((item) => {
          window.location.href = item;
        });
      });
    },
    onSeach() {
      this.getStatistics();
      this.getTrend();
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.dataTime = this.timeVal ? this.timeVal.join('-') : '';
    },
    // Thống kê
    getStatistics() {
      statisticBasicApi({ data: this.dataTime })
        .then(async (res) => {
          const cardLists = res.data;
          this.list = [
            {
              name: 'Lượt xem sản phẩm',
              icon: 'iconshangpinliulanliang',
              list: cardLists.browse,
            },
            {
              name: 'Số khách truy cập sản phẩm',
              icon: 'iconshangpinfangkeshu',
              list: cardLists.user,
            },
            {
              name: 'Số lượng thanh toán',
              icon: 'iconzhifujianshu',
              list: cardLists.pay,
            },
            {
              name: 'Số tiền thanh toán',
              icon: 'iconzhifujine',
              list: cardLists.payPrice,
            },
            {
              name: 'Số lượng hoàn tiền',
              icon: 'icontuikuanjianshu',
              list: cardLists.refund,
            },
            {
              name: 'Số tiền hoàn',
              icon: 'icontuikuan',
              list: cardLists.refundPrice,
            },
          ];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Biểu đồ thống kê
    getTrend() {
      this.spinShow = true;
      statisticTrendApi({ data: this.dataTime })
        .then(async (res) => {
          let legend = res.data.series.map((item) => {
            return item.name;
          });
          let xAxis = res.data.xAxis;
          let col = ['#B37FEB', '#FFAB2B', '#1890FF', '#00C050'];
          res.data.series.map((item, index) => {
            item.itemStyle = {
              normal: {
                color: col[index],
              },
            };
          });
          this.optionData = {
            tooltip: {
              trigger: 'axis',
              axisPointer: {
                type: 'cross',
                label: {
                  backgroundColor: '#6a7985',
                },
              },
            },
            legend: {
              x: 'center',
              data: legend,
            },
            grid: {
              left: '3%',
              right: '4%',
              bottom: '3%',
              containLabel: true,
            },
            toolbox: {
              feature: {
                saveAsImage: {},
              },
            },
            xAxis: {
              type: 'category',
              boundaryGap: true,
              // axisTick:{
              //     show:false
              // },
              // axisLine:{
              //     show:false
              // },
              // splitLine: {
              //     show: false
              // },
              axisLabel: {
                interval: 0,
                rotate: 40,
                textStyle: {
                  color: '#000000',
                },
              },
              data: xAxis,
            },
            yAxis: [
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
            ],
            series: res.data.series,
          };
          this.spinShow = false;
        })
        .catch((res) => {
          this.$message.error(res.msg);
          this.spinShow = false;
        });
    },
  },
};
</script>

<style scoped lang="scss">
.one {
  background: var(--prev-color-primary);
}
.two {
  background: #00c050;
}
.three {
  background: #ffab2b;
}
.four {
  background: #b37feb;
}
.up,
.el-icon-caret-top {
  color: #f5222d;
  font-size: 12px;
  opacity: 1 !important;
}

.down,
.el-icon-caret-bottom {
  color: #39c15b;
  font-size: 12px;
}
.curP {
  cursor: pointer;
}
.header {
  &-title {
    font-size: 16px;
    color: rgba(0, 0, 0, 0.85);
  }
  &-time {
    font-size: 12px;
    color: #000000;
    opacity: 0.45;
  }
}

.iconfont {
  font-size: 16px;
  color: #fff;
}

.iconCrl {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  text-align: center;
  line-height: 32px;
  opacity: 0.7;
}

.lan {
  background: var(--prev-color-primary);
}

.iconshangpinliulanliang {
  color: #fff;
}

.infoBox {
  width: 20%;
  @media screen and (max-width: 1300px) {
    width: 25%;
  }
  @media screen and (max-width: 1200px) {
    width: 33%;
  }
  @media screen and (max-width: 900px) {
    width: 50%;
  }
}

.info {
  .sp1 {
    color: #666;
    font-size: 14px;
    display: block;
  }
  .sp2 {
    font-weight: 400;
    font-size: 30px;
    color: rgba(0, 0, 0, 0.85);
    display: block;
  }
  .sp3 {
    font-size: 12px;
    font-weight: 400;
    color: rgba(0, 0, 0, 0.45);
    display: block;
  }
}
</style>
