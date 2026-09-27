<template>
  <div v-loading="spinShow">
    <!-- <div class="i-layout-page-header header-title">
      <div class="fl_header">
        <span>
          <el-button icon="ios-arrow-back" size="small" type="text" v-db-click @click="$router.go(-1)">Quay lại</el-button>
        </span>
        <el-divider direction="vertical"></el-divider>
        <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
      </div>
    </div> -->
    <pages-header
      ref="pageHeader"
      :title="$route.meta.title"
      :backUrl="$routeProStr + '/marketing/channel_code/channelCodeIndex'"
    ></pages-header>
    <cards-data class="mt16" :cardLists="cardLists" v-if="cardLists.length >= 0"></cards-data>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <div class="table-head">
        <h3>Xu hướng theo dõi</h3>
        <el-date-picker
          :editable="false"
          clearable
          @change="onchangeTime"
          v-model="timeVal"
          :picker-options="pickerOptions"
          format="yyyy/MM/dd"
          type="daterange"
          value-format="yyyy/MM/dd"
          range-separator="-"
          start-placeholder="Ngày bắt đầu"
          end-placeholder="Ngày kết thúc"
          class="mr20"
        ></el-date-picker>
      </div>
      <echarts-new :option-data="optionData" :styles="style" height="100%" width="100%" v-if="optionData"></echarts-new>
    </el-card>
  </div>
</template>

<script>
import cardsData from '@/components/cards/cards';
import echartsNew from '@/components/echartsNew/index';
import { getBasic, getTrend, getChannel, getType, wechatQrcodeStatistic } from '@/api/statistic';
import { formatDate } from '@/utils/validate';
import echartsFrom from '@/components/echarts/index';

export default {
  name: 'index',
  components: { cardsData, echartsNew, echartsFrom },
  data() {
    return {
      timeVal: [],
      pickerOptions: this.$timeOptions,
      style: { height: '400px' },
      infoList: {},
      infoList2: {},
      echartLeft: true,
      echartRight: false,
      loading: false,
      loading2: false,
      fromList: {
        title: 'Chọn giờ',
        custom: true,
        fromTxt: [
          { text: 'Tất cả', val: '' },
          { text: 'Hôm nay', val: 'today' },
          { text: 'Tuần này', val: 'week' },
          { text: 'Tháng này', val: 'month' },
          { text: 'Quý này', val: 'quarter' },
          { text: 'Năm nay', val: 'year' },
        ],
      },
      formValidate: {
        time: '',
      },
      cardLists: [
        {
          col: 6,
          count: 0,
          name: 'Lượt theo dõi mới hôm qua',
          className: 'md-rose',
        },
        {
          col: 6,
          count: 0,
          name: 'Tham gia hôm qua',
          className: 'ios-speedometer-outline',
        },
        {
          col: 6,
          count: 0,
          name: 'Tổng lượt theo dõi',
          className: 'ios-speedometer-outline',
        },
        {
          col: 6,
          count: 0,
          name: 'Tổng số người tham gia',
          className: 'md-rose',
        },
      ],
      optionData: {},
      spinShow: false,
    };
  },
  created() {
    this.id = this.$route.query.id;
    const end = new Date();
    const start = new Date();
    start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 29)));
    this.timeVal = [start, end];
    this.formValidate.time = formatDate(start, 'yyyy/MM/dd') + '-' + formatDate(end, 'yyyy/MM/dd');
    // this.getBasic();
    // this.getTrend();
    // this.getChannel();
    // this.getType();
    this.wechatQrcodeStatistic();
  },
  methods: {
    wechatQrcodeStatistic() {
      wechatQrcodeStatistic(this.id, this.formValidate).then((res) => {
        let arr = ['y_follow', 'y_scan', 'all_follow', 'all_scan'];
        this.cardLists.map((i, index) => {
          i.count = res.data[arr[index]];
        });
        this.getTrend(res.data.trend.series, res.data.trend.xAxis);
      });
    },
    timeG(dd) {
      var d = new Date(dd);
      var datetime =
        d.getFullYear() +
        '-' +
        (d.getMonth() + 1) +
        '-' +
        d.getDate() +
        ' ' +
        d.getHours() +
        ':' +
        d.getMinutes() +
        ':' +
        d.getSeconds();
      return datetime;
    },

    selectChange(e) {},
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.time = this.timeVal ? this.timeVal.join('-') : '';
      this.name = this.formValidate.time;
      this.wechatQrcodeStatistic();
    },
    // Biểu đồ thống kê
    getTrend(seriesData, xAxisData) {
      this.spinShow = true;
      let legend = seriesData.map((item) => {
        return item.name;
      });
      let xAxis = xAxisData;
      let col = ['#5B8FF9', '#5AD8A6', '#FFAB2B', '#5D7092'];
      let series = [];
      seriesData.map((item, index) => {
        series.push({
          name: item.name,
          type: 'line',
          data: item.data,
          itemStyle: {
            normal: {
              color: col[index],
            },
          },
          smooth: 0,
        });
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
        yAxis: {
          type: 'value',
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
        series: series,
      };
      this.spinShow = false;
    },
  },
};
</script>

<style scoped>
.cl {
  margin-right: 20px;
}
.code-row-bg {
  display: flex;
  flex-wrap: nowrap;
}
.code-row-bg .ivu-mt {
  width: 100%;
  margin: 0 5px;
}
.ech-box {
  margin-top: 10px;
}
.change-style {
  border: 1px solid #ccc;
  border-radius: 15px;
  padding: 0px 10px;
  cursor: pointer;
}
.table-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.return {
  margin-bottom: 6px;
}
.i-layout-page-header {
  padding-left: 13px;
}
</style>
