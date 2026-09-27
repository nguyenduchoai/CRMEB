<template>
  <div
    class="news-box"
    :style="{
      background: bottomBgColor,
      marginTop: slider + 'px',
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
    v-if="list.length"
  >
    <div
      class="item"
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: bgRadius,
      }"
      v-if="styleConfig == 0"
    >
      <div class="img-box" v-if="titleConfig == 0"><img :src="imgUrl" alt="" /></div>
      <div
        class="top"
        v-else
        :style="{
          color: toneConfig ? titleColor : '#fff',
          background: toneConfig
            ? `linear-gradient(90deg,${titleBgColorLeft} 0%,${titleBgColorRight} 100%)`
            : colorStyle.theme,
        }"
      >
        {{ titleTxtConfig || 'Tin nổi bật' }}
      </div>
      <div
        class="right-box"
        :style="{
          color: newsColor,
        }"
      >
        {{ list[0].chiild[0].val }}
      </div>
      <span
        class="iconfont iconjinru"
        :style="{
          color: bntColor,
        }"
        v-if="!buttonConfig"
      ></span>
    </div>
    <div
      class="list"
      v-else
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: bgRadius,
      }"
    >
      <div class="title acea-row row-between-wrapper">
        <div class="pictrue" v-if="titleConfig == 0">
          <img :src="imgUrl" alt="" />
        </div>
        <div
        class="top"
        v-else
        :style="{
          color: !toneConfig ? titleColor : colorStyle.theme,
        }"
        >
          {{ titleTxtConfig || 'Tin nổi bật' }}
        </div>
        <div
          v-if="!buttonConfig"
          :style="{
            color: bntColor,
          }"
        >
          {{ textConfig }}<span class="iconfont iconjinru"></span>
        </div>
      </div>
      <div
        class="text line1"
        v-for="(item, index) in list"
        :key="index"
        :style="{
          color: newsColor,
        }"
      >
        <span class="num" :class="index == 0 ? 'on' : index == 1 ? 'on2' : index == 2 ? 'on3' : ''">{{
          index + 1
        }}</span
        >{{ item.chiild[0].val }}
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'home_news_roll',
  cname: 'Thông báo tin tức',
  configName: 'c_news_roll',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'news', // Tên khớp bên ngoài
  icon: '#iconzujian-xinwenbobao',
  props: {
    index: {
      type: null,
    },
    num: {
      type: null,
    },
    colorStyle: {
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
  // mixins: [theme],
  data() {
    return {
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Thông báo tin tức',
        name: 'news',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleStyle: 'Phong cách thông báo',
        titleButton: 'Cài đặt nút',
        titleContent: 'Nội dung thông báo',
        titleRight: 'Kiểu tiêu đề',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          tabList: [
            {
              name: 'Kiểu 1',
            },
            {
              name: 'Kiểu 2',
            },
          ],
        },
        titleConfig: {
          title: 'Loại tiêu đề',
          tabVal: 0,
          tabList: [
            {
              name: 'Hình ảnh',
            },
            {
              name: 'Văn bản',
            },
          ],
        },
        imgConfig: {
          url: require('@/assets/images/news2.png'),
          type: 'code',
          delType: 0,
          name: 'Tải lên ảnh',
        },
        titleTxtConfig: {
          title: 'Chữ tiêu đề',
          value: 'Tin nổi bật',
          place: 'Vui lòng nhập chữ tiêu đề',
          max: 4,
        },
        rollConfig: {
          title: 'Kiểu cuộn',
          tabVal: 0,
          tabList: [
            {
              name: 'Cuộn dọc',
            },
            {
              name: 'Cuộn ngang',
            },
          ],
        },
        buttonConfig: {
          title: 'Nút bên phải',
          tabVal: 0,
          tabList: [
            {
              name: 'Hiện',
            },
            {
              name: 'Ẩn',
            },
          ],
        },
        textConfig: {
          title: 'Chữ bên phải',
          value: 'Xem thêm',
          place: 'Vui lòng nhập chữ bên phải',
          max: 4,
        },
        linkConfig: {
          title: 'Liên kết',
          value: '',
          place: 'Chọn liên kết chuyển hướng',
          max: 100,
          type: 'link',
        },
        listConfig: {
          max: 10,
          type: 1,
          list: [
            {
              chiild: [
                {
                  title: 'Tiêu đề',
                  val: 'Tiêu đề',
                  max: 20,
                  pla: 'Nhập tiêu đề',
                },
                {
                  title: 'Liên kết',
                  val: '',
                  max: 200,
                  pla: 'Nhập liên kết',
                },
              ],
              show: true,
            },
          ],
        },
        toneConfig: {
          title: 'Tông màu',
          tabVal: 0,
          tabList: [
            {
              name: 'Theo phong cách chủ đề',
            },
            {
              name: 'Tùy chỉnh',
            },
          ],
        },
        titleBgColor: {
          title: 'Nền tiêu đề',
          default: [
            {
              item: '#FCEAE9',
            },
            {
              item: '#FCEAE9',
            },
          ],
          color: [
            {
              item: '#FCEAE9',
            },
            {
              item: '#FCEAE9',
            },
          ],
        },
        titleColor: {
          title: 'Chữ tiêu đề',
          default: [
            {
              item: '#E93323',
            },
          ],
          color: [
            {
              item: '#E93323',
            },
          ],
        },
        newsColor: {
          title: 'Tiêu đề tin tức',
          default: [
            {
              item: '#333333',
            },
          ],
          color: [
            {
              item: '#333333',
            },
          ],
        },
        bntColor: {
          title: 'Màu nút',
          default: [
            {
              item: '#999999',
            },
          ],
          color: [
            {
              item: '#999999',
            },
          ],
        },
        moduleColor: {
          title: 'Nền thành phần',
          default: [
            {
              item: '#fff',
            },
            {
              item: '#fff',
            },
          ],
          color: [
            {
              item: '#fff',
            },
            {
              item: '#fff',
            },
          ],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          default: [
            {
              item: '#f5f5f5',
            },
          ],
          color: [
            {
              item: '#f5f5f5',
            },
          ],
        },
        topConfig: {
          title: 'Lề trên',
          val: 0,
          min: 0,
        },
        bottomConfig: {
          title: 'Lề dưới',
          val: 0,
          min: 0,
        },
        prConfig: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
        },
        mbConfig: {
          title: 'Lề trên trang',
          val: 0,
          min: 0,
        },
        fillet: {
          title: 'Bo góc nền',
          type: 0,
          list: [
            {
              val: 'Tất cả',
              icon: 'iconcaozuo-zhengti',
            },
            {
              val: 'Từng góc',
              icon: 'iconcaozuo-bianjiao',
            },
          ],
          valName: 'Giá trị bo góc',
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        // logoConfig: {
        //     header: 'Cài đặt biểu tượng',
        //     title: 'Tối đa thêm 1 ảnh, nên dùng chiều rộng 130 * 36px',
        //     url: require('@/assets/images/news.png')
        // }
      },
      tabVal: '',
      rollStyle: '',
      txtPosition: '',
      pageData: {},

      list: [],
      slider: 0,
      styleConfig: 0,
      imgUrl: '',
      buttonConfig: 0,
      textConfig: '',
      toneConfig: 0,
      newsColor: '',
      bntColor: '',
      bgColorLeft: '',
      bgColorRight: '',
      bottomBgColor: '',
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      bgRadius: 0,
      titleConfig: 0,
      titleBgColorLeft: '',
      titleBgColorRight: '',
      titleColor: '',
      titleTxtConfig: '',
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
        this.slider = data.mbConfig.val;
        this.styleConfig = data.styleConfig.tabVal;
        this.imgUrl = data.imgConfig.url;
        this.buttonConfig = data.buttonConfig.tabVal;
        this.textConfig = data.textConfig.value;
        let lists = data.listConfig.list;
        let list = [];
        lists.forEach((item) => {
          if (item.show) {
            list.push(item);
          }
        });
        console.log(data.titleColor,'data.titleColor')
        this.list = list;
        this.toneConfig = data.toneConfig.tabVal;
        this.newsColor = data.newsColor.color[0].item;
        this.bntColor = data.bntColor.color[0].item;
        this.bgColorLeft = data.moduleColor.color[0].item;
        this.bgColorRight = data.moduleColor.color[1].item;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.prConfig = data.prConfig.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.titleConfig = data.titleConfig.tabVal;
        this.titleBgColorLeft = data.titleBgColor.color[0].item;
        this.titleBgColorRight = data.titleBgColor.color[1].item;
        this.titleColor = data.titleColor.color[0].item;
        this.titleTxtConfig = data.titleTxtConfig.value;
        let fillet = data.fillet.type;
        let filletVal = data.fillet.val;
        let valList = data.fillet.valList;
        this.bgRadius = fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px';
      }
    },
  },
};
</script>

<style scoped lang="scss">
.pageOn {
  border-radius: 6px !important;
}
.news-box {
  .list {
    padding: 0 12px 16px 12px;
    .title {
      color: #999999;
      font-size: 12px;
      padding: 16px 0;

      .top {
        font-size: 16px;
      }

      .pictrue {
        height: 18px;
        img {
          height: 100%;
          width: 100%;
        }
      }
      .iconfont {
        font-size: 12px;
      }
    }
    .text {
      color: #282828;
      font-size: 14px;

      .num {
        font-size: 15px;
        margin-right: 7px;
        color: #999999;
        &.on {
          color: #e93323;
        }
        &.on2 {
          color: #ff7300;
        }
        &.on3 {
          color: #ffc300;
        }
      }

      & ~ .text {
        margin-top: 12px;
      }
    }
  }
  .item {
    display: flex;
    align-items: center;
    height: 44px;
    padding: 0 10px;
    font-size: 13px;
    color: #333;
    position: relative;
    .top {
      max-width: 64px;
      height: 20px;
      font-size: 13px;
      text-align: center;
      line-height: 20px;
      padding: 0 6px;
      border-radius: 4px;
      margin-right: 8px;
    }
    .iconfont {
      color: #999;
      font-size: 14px;
    }
    .img-box {
      height: 24px;
      img {
        height: 100%;
        margin-right: 8px;
      }
    }
    .right-box {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  }
}
</style>
