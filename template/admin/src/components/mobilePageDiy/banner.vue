<template>
  <div class="mobile-page" :style="{ marginTop: `${mTOP}px` }">
    <div
      class="bg"
      :style="{
        background: `linear-gradient(90deg,${bgColor[0].item} 0%,${bgColor[1].item} 100%)`,
      }"
      v-if="bgColor.length > 0 && isShow"
    ></div>
    <div v-if="!isShow" class="bgset"></div>
    <div
      class="banner"
      :style="{
        paddingLeft: edge + 'px',
        paddingRight: edge + 'px',
      }"
    >
      <img :class="{ doc: imgStyle }" :src="imgSrc" alt="" v-if="imgSrc" />
      <div class="empty-box" :class="{ on: imgStyle }" v-else>
        <span class="iconfont-diy icontupian"></span>
      </div>
    </div>
    <div>
      <div
        class="dot"
        :style="{
          paddingLeft: edge + 10 + 'px',
          paddingRight: edge + 10 + 'px',
          justifyContent: dotPosition === 1 ? 'center' : dotPosition === 2 ? 'flex-end' : 'flex-start',
        }"
        v-if="docStyle == 0"
      >
        <div class="dot-item" :style="{ background: `${dotColor}` }"></div>
        <div class="dot-item"></div>
        <div class="dot-item"></div>
      </div>
      <div
        class="dot line-dot"
        :style="{
          paddingLeft: edge + 10 + 'px',
          paddingRight: edge + 10 + 'px',
          justifyContent: dotPosition === 1 ? 'center' : dotPosition === 2 ? 'flex-end' : 'flex-start',
        }"
        v-if="docStyle == 1"
      >
        <div class="line_dot-item" :style="{ background: `${dotColor}` }"></div>
        <div class="line_dot-item"></div>
        <div class="line_dot-item"></div>
      </div>
      <div
        class="dot number"
        :style="{
          paddingLeft: edge + 10 + 'px',
          paddingRight: edge + 10 + 'px',
          justifyContent: dotPosition === 1 ? 'center' : dotPosition === 2 ? 'flex-end' : 'flex-start',
        }"
        v-if="docStyle == 2"
      >
        <div class="num">1/3</div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'banner', // Tên thành phần
  cname: 'Ảnh trình chiếu', // Tên tiêu đề
  icon: 'icontupianguanggao1',
  defaultName: 'swiperBg', // Tên khớp bên ngoài
  configName: 'c_banner', // Tên cấu hình bên phải
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
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
        name: 'swiperBg',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        // Chọn mẫu
        // tabConfig: {
        //   tabVal: 0,
        //   type: 1,
        //   tabList: [
        //     {
        //       name: "Mẫu ảnh đơn",
        //       icon: "iconbanner_1",
        //     },
        //     {
        //       name: "Mẫu nhiều ảnh 1",
        //       icon: "iconbanner_2",
        //     },
        //     {
        //       name: "Mẫu nhiều ảnh 2",
        //       icon: "iconbanner_3",
        //     },
        //   ],
        // },
        // Danh sách ảnh
        swiperConfig: {
          title: 'Có thể thêm tối đa 10 ảnh, chiều rộng đề xuất 750px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự ảnh',
          maxList: 10,
          list: [
            {
              img: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Gợi ý hôm nay',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
          ],
        },
        isShow: {
          title: 'Hiển thị màu nền',
          val: true,
        },
        // Màu nền
        bgColor: {
          title: 'Màu nền (chuyển màu)',
          default: [
            {
              item: '#F62C2C',
            },
            {
              item: '#F96E29',
            },
          ],
          color: [
            {
              item: '#F62C2C',
            },
            {
              item: '#F96E29',
            },
          ],
        },
        dotColor: {
          title: 'Màu chỉ báo',
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
        // Khoảng cách trái phải
        lrConfig: {
          title: 'Lề trái phải',
          val: 10,
          min: 0,
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        // Kiểu điểm chấm của banner
        docConfig: {
          cname: 'swiper',
          title: 'Kiểu chỉ báo',
          type: 0,
          list: [
            {
              val: 'Hình tròn',
              icon: 'iconDot',
            },
            {
              val: 'Đường thẳng',
              icon: 'iconSquarepoint',
            },
            {
              val: 'Số',
              icon: 'iconshuzi',
            },
            {
              val: 'Không có chỉ báo',
              icon: 'iconjinyong',
            },
          ],
        },
        txtStyle: {
          title: 'Vị trí chỉ báo',
          type: 0,
          list: [
            {
              val: 'Căn trái',
              icon: 'icondoc_left',
            },
            {
              val: 'Căn giữa',
              icon: 'icondoc_center',
            },
            {
              val: 'Căn phải',
              icon: 'icondoc_right',
            },
          ],
        },
        // Kiểu ảnh
        imgConfig: {
          cname: 'docStyle',
          title: 'Kiểu ảnh trình chiếu',
          type: 0,
          list: [
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
          ],
        },
      },
      pageData: {},
      bgColor: [],
      mTOP: 0,
      edge: 0,
      imgStyle: 0,
      imgSrc: '',
      docStyle: 0,
      dotPosition: 0,
      dotColor: '',
      isShow: true,
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
        this.isShow = data.isShow.val;
        this.bgColor = data.bgColor.color;
        this.mTOP = data.mbConfig.val;
        this.edge = data.lrConfig.val;
        this.imgStyle = data.imgConfig.type;
        this.imgSrc = data.swiperConfig.list.length ? data.swiperConfig.list[0].img : '';
        this.docStyle = data.docConfig.type;
        this.dotPosition = data.txtStyle.type;
        this.dotColor = data.dotColor.color[0].item;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.empty-box {
  height: 170px;
}
.mobile-page {
  position: relative;
  width: auto;

  /* height: 140px; */
  .banner {
    /* position: absolute; */
    /* left: 0; */
    /* top: 0; */
    width: 100%;
    margin-top: -48px;

    img {
      width: 100%;
      height: 100%;
      border-radius: 6px;
      &.doc {
        border-radius: 0;
      }
    }
  }
  .bg {
    width: 100%;
    height: 50px;
    background: linear-gradient(90deg, #f62c2c 0%, #f96e29 100%);
  }
  .bgset {
    width: 100%;
    height: 50px;
  }
}
.dot {
  position: absolute;
  left: 0;
  bottom: 20px;
  width: 100%;
  display: flex;
  align-items: center;
  &.number {
    bottom: 4px;
  }
  .num {
    width: 25px;
    height: 18px;
    line-height: 18px;
    background-color: #000;
    color: #fff;
    opacity: 0.3;
    border-radius: 8px;
    font-size: 12px;
    text-align: center;
  }
  .dot-item {
    width: 5px;
    height: 5px;
    background: #aaaaaa;
    border-radius: 50%;
    margin: 0 3px;
  }
  &.line-dot {
    bottom: 20px;
    .line_dot-item {
      width: 8px;
      height: 2px;
      background: #aaaaaa;
      margin: 0 3px;
    }
  }
}
</style>
