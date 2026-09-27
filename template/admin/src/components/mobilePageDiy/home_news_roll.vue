<template>
  <div
    class="news-box"
    :class="{ pageOn: bgStyle === 1 }"
    :style="{ margin: '0 ' + prConfig + 'px', marginTop: slider + 'px', background: bgColor }"
    v-if="list.length"
  >
    <div class="item" :style="{ color: txtColor }">
      <div class="img-box"><img :src="imgUrl" alt="" /></div>
      <div class="right-box" :style="{ textAlign: textStyle }">{{ list[0].chiild[0].val }}</div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'home_news_roll',
  cname: 'Bản tin',
  configName: 'c_news_roll',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'news', // Tên khớp bên ngoài
  icon: 'iconxinwenbobao1',
  props: {
    index: {
      type: null,
    },
    num: {
      type: null,
    },
  },
  computed: {
    ...mapState('mobildConfig', ['defaultArray']),
  },
  watch: {
    pageData: {
      handler(nVal, oVal) {
        this.setConfig(nVal);
      },
      deep: true,
    },
    num: {
      handler(nVal, oVal) {
        let data = this.$store.state.mobildConfig.defaultArray[nVal];
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler(nVal, oVal) {
        let data = this.$store.state.mobildConfig.defaultArray[this.num];
        this.setConfig(data);
      },
      deep: true,
    },
  },
  data() {
    return {
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        name: 'news',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        txtStyle: {
          title: 'Vị trí văn bản',
          name: 'txtStyle',
          type: 0,
          list: [
            {
              val: 'Căn trái',
              icon: 'icondoc_left',
              style: 'left',
            },
            {
              val: 'Căn giữa',
              icon: 'icondoc_center',
              style: 'center',
            },
            {
              val: 'Căn phải',
              icon: 'icondoc_right',
              style: 'right',
            },
          ],
        },
        bgColor: {
          title: 'Màu nền',
          default: [
            {
              item: '#fff',
            },
          ],
          color: [
            {
              item: '#fff',
            },
          ],
        },
        txtColor: {
          title: 'Màu chữ',
          default: [
            {
              item: '#333',
            },
          ],
          color: [
            {
              item: '#333',
            },
          ],
        },
        listConfig: {
          title: 'Có thể thêm tối đa 10 khối; kéo thả chấm tròn bên trái để điều chỉnh thứ tự khối',
          max: 10,
          list: [
            {
              chiild: [
                {
                  title: 'Tiêu đề',
                  val: 'Tiêu đề',
                  max: 30,
                  pla: 'Không bắt buộc, tối đa 30 ký tự',
                  empty: true,
                },
                {
                  title: 'Liên kết',
                  val: 'Liên kết',
                  max: 200,
                  pla: 'Vui lòng nhập liên kết',
                },
              ],
            },
          ],
        },
        bgStyle: {
          title: 'Kiểu nền',
          name: 'bgStyle',
          type: 0,
          list: [
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
          ],
        },
        prConfig: {
          title: 'Lề nền',
          val: 0,
          min: 0,
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        logoConfig: {
          header: 'Cài đặt biểu tượng',
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 130 * 36px',
          url: require('@/assets/images/news.png'),
        },
      },
      tabVal: '',
      bgColor: [],
      txtColor: [],
      rollStyle: '',
      txtPosition: '',
      pageData: {},
      list: [],
      imgUrl: '',
      textStyle: '',
      slider: 0,
      bgStyle: 0,
      prConfig: 0,
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
    });
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      if (data.mbConfig) {
        this.list = data.listConfig.list;
        this.imgUrl = data.logoConfig.url;
        this.textStyle = data.txtStyle.list[data.txtStyle.type].style;
        this.slider = data.mbConfig.val;
        this.bgColor = data.bgColor.color[0].item;
        this.txtColor = data.txtColor.color[0].item;
        this.bgStyle = data.bgStyle.type;
        this.prConfig = data.prConfig.val;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.pageOn {
  border-radius: 6px !important;
}
.news-box {
  .item {
    display: flex;
    align-items: center;
    height: 30px;
    margin: 0 7px;
    .img-box {
      width: 75px;
      height: 18px;
      border-right: 1px solid #ddd;
      padding-right: 10px;
      img {
        width: 100%;
        height: 100%;
      }
    }
    .right-box {
      flex: 1;
      padding: 0 20px 0 10px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      background-image: url('~@/assets/images/right.png');
      background-size: 20px 20px;
      background-position: right center;
      background-repeat: no-repeat;
    }
  }
}
</style>
