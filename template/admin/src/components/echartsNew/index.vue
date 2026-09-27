<template>
  <div>
    <div :id="echarts" :style="styles" />
  </div>
</template>

<script>
import echarts from 'echarts';
export default {
  name: 'Index',
  props: {
    styles: {
      type: Object,
      default: null,
    },
    optionData: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      myChart: null,
    };
  },
  computed: {
    echarts() {
      return 'echarts' + Math.ceil(Math.random() * 100);
    },
  },
  watch: {
    optionData: {
      handler(newVal, oldVal) {
        this.handleSetVisitChart();
      },
      deep: true, // Theo dõi (watch) thuộc tính bên trong object, quan trọng.
    },
  },
  mounted: function () {
    const vm = this;
    vm.$nextTick(() => {
      vm.handleSetVisitChart();
      window.addEventListener('resize', this.wsFunc);
    });
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.wsFunc);
    if (!this.myChart) {
      return;
    }
    this.myChart.dispose();
    this.myChart = null;
  },
  methods: {
    wsFunc() {
      this.myChart.resize();
    },
    handleSetVisitChart() {
      this.myChart = echarts.init(document.getElementById(this.echarts));
      let option = null;
      option = this.optionData;
      // Dựa trên dom đã chuẩn bị, khởi tạo instance echarts
      this.myChart.setOption(option, true);
    },
  },
};
</script>

<style scoped></style>
