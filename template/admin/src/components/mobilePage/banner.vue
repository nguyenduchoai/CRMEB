<template>
  <common_wrapper :config="configObj">
    <div style="position: relative">
      <div v-if="styleConfig == 0 || styleConfig == 1">
        <div class="banner" v-if="styleConfig == 0">
          <img
            :src="imgSrc[0].img"
            alt=""
            v-if="imgSrc.length && imgSrc[0].img"
            :style="{
              borderRadius: filletImg
                ? valListImg[0].val +
                  'px ' +
                  valListImg[1].val +
                  'px ' +
                  valListImg[3].val +
                  'px ' +
                  valListImg[2].val +
                  'px'
                : filletValImg + 'px',
            }"
          />
          <div
            class="empty-box"
            v-else
            :style="{
              borderRadius: filletImg
                ? valListImg[0].val +
                  'px ' +
                  valListImg[1].val +
                  'px ' +
                  valListImg[3].val +
                  'px ' +
                  valListImg[2].val +
                  'px'
                : filletValImg + 'px',
            }"
          >
            <img class="shan" src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="banner" v-else>
          <div class="acea-row row-middle">
            <div
              class="empty-box style3"
              :style="{
                borderRadius: filletImg
                  ? '0 ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + '0'
                  : '0 ' + filletValImg + 'px ' + filletValImg + 'px ' + '0',
              }"
            >
              <img
                :src="imgSrc[1].img"
                alt=""
                v-if="imgSrc.length > 1 && imgSrc[1].img"
                :style="{
                  borderRadius: filletImg
                    ? '0 ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + '0'
                    : '0 ' + filletValImg + 'px ' + filletValImg + 'px ' + '0',
                }"
              />
            </div>
            <div
              class="empty-box style3 on"
              :style="{
                marginLeft: imgConfig + 'px',
                marginRight: imgConfig + 'px',
                borderRadius: filletImg
                  ? valListImg[0].val +
                    'px ' +
                    valListImg[1].val +
                    'px ' +
                    valListImg[3].val +
                    'px ' +
                    valListImg[2].val +
                    'px'
                  : filletValImg + 'px',
              }"
            >
              <img
                :src="imgSrc[0].img"
                alt=""
                v-if="imgSrc.length && imgSrc[0].img"
                :style="{
                  borderRadius: filletImg
                    ? valListImg[0].val +
                      'px ' +
                      valListImg[1].val +
                      'px ' +
                      valListImg[3].val +
                      'px ' +
                      valListImg[2].val +
                      'px'
                    : filletValImg + 'px',
                }"
              />
              <img class="shan" src="../../assets/images/shan.png" v-else />
            </div>
            <div
              class="empty-box style3"
              :style="{
                borderRadius: filletImg
                  ? valListImg[1].val + 'px 0 0 ' + valListImg[3].val + 'px'
                  : filletValImg + 'px 0 0 ' + filletValImg + 'px',
              }"
            >
              <img
                :src="imgSrc[2].img"
                alt=""
                v-if="imgSrc.length > 2 && imgSrc[2].img"
                :style="{
                  borderRadius: filletImg
                    ? valListImg[1].val + 'px 0 0 ' + valListImg[3].val + 'px'
                    : filletValImg + 'px 0 0 ' + filletValImg + 'px',
                }"
              />
            </div>
          </div>
        </div>
      </div>

      <div>
        <!-- <div class="dot number acea-row " v-if="docStyle == 0">
		  <div class="num">2</div>
		  <div class="numCon">8</div>
	  </div> -->
        <div
          class="dot"
          v-if="docStyle == 2"
          :style="{
            paddingLeft:
              styleConfig == 1
                ? paddingConfig.valList[3].val + imgConfig * 2 + 26 + 'px'
                : paddingConfig.valList[3].val + 13 + 'px',
            paddingRight:
              styleConfig == 1
                ? paddingConfig.valList[1].val + imgConfig * 2 + 26 + 'px'
                : paddingConfig.valList[1].val + 13 + 'px',
            paddingBottom: paddingConfig.valList[2].val + 10 + 'px',
            justifyContent: docPosition === 1 ? 'center' : docPosition === 2 ? 'flex-end' : 'flex-start',
          }"
        >
          <div
            class="line-dot"
            :style="{
              background: toneConfig ? dotBgColor : '#ddd',
            }"
          >
            <div
              class="item"
              :style="{
                background: toneConfig ? `${dotColor}` : `${colorStyle.theme}`,
              }"
            ></div>
          </div>
        </div>
        <div
          class="dot"
          :class="docStyle == 1 ? 'on' : docStyle == 3 ? 'on2' : ''"
          v-else
          :style="{
            paddingLeft:
              styleConfig == 1
                ? paddingConfig.valList[3].val + imgConfig * 2 + 26 + 'px'
                : paddingConfig.valList[3].val + 10 + 'px',
            paddingRight:
              styleConfig == 1
                ? paddingConfig.valList[1].val + imgConfig * 2 + 26 + 'px'
                : paddingConfig.valList[1].val + 10 + 'px',
            paddingBottom: paddingConfig.valList[2].val + 10 + 'px',
            justifyContent: docPosition === 1 ? 'center' : docPosition === 2 ? 'flex-end' : 'flex-start',
          }"
        >
          <div
            class="dot-item"
            :class="docStyle == 1 ? 'ons' : ''"
            :style="{ background: toneConfig ? `${dotColor}` : `${colorStyle.theme}` }"
          ></div>
          <div
            class="dot-item"
            :style="{ background: toneConfig ? dotBgColor : '#ddd' }"
            v-for="(item, index) in 2"
            :key="index"
          ></div>
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'banner', // Tên thành phần
  cname: 'Ảnh trình chiếu', // Tên tiêu đề
  icon: '#iconzujian-lunbotu',
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
        cname: 'Ảnh trình chiếu',
        name: 'swiperBg',
        // Mô tả ngắn
        desc: 'Ảnh trình chiếu (banner) giới thiệu nhiều tư liệu hình ảnh đến người dùng dưới dạng trình chiếu động, thành phần ảnh trình chiếu giúp mỗi tư liệu hình ảnh đều được hiển thị tốt hơn, nâng cao tỷ lệ chuyển đổi của sản phẩm và nội dung.',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleContent: 'Cài đặt nội dung',
        titleRight: 'Cài đặt chỉ báo',
        titleImg: 'Cài đặt ảnh',
        titleCurrency: 'Kiểu chung',
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
        borderConfig: {
          title: 'Cài đặt viền',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0, // 0: Hide, 1: Show
          styleConfig: {
            title: 'Kiểu viền',
            tabVal: 0,
            tabList: [
              { name: 'Nét liền', style: 'solid' },
              { name: 'Nét đứt', style: 'dashed' },
              { name: 'Nét chấm', style: 'dotted' },
            ],
          },
          widthConfig: {
            title: 'Độ dày viền',
            val: 1,
            min: 1,
          },
          colorConfig: {
            title: 'Màu viền',
            default: [{ item: '#e5e5e5' }],
            color: [{ item: '#e5e5e5' }],
          },
        },
        shadowConfig: {
          title: 'Cài đặt đổ bóng',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0,
          colorConfig: {
            title: 'Màu đổ bóng',
            default: [{ item: '#e5e5e5' }],
            color: [{ item: '#e5e5e5' }],
          },
          xConfig: {
            title: 'Độ lệch trục X',
            val: 0,
            min: -50,
          },
          yConfig: {
            title: 'Độ lệch trục Y',
            val: 0,
            min: -50,
          },
          blurConfig: {
            title: 'Bán kính làm mờ',
            val: 10,
            min: 0,
          },
          spreadConfig: {
            title: 'Bán kính lan tỏa',
            val: 0,
            min: -50,
          },
        },
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 1,
          tabList: [
            {
              name: 'Kiểu 1',
            },
            {
              name: 'Kiểu 2',
            },
          ],
        },
        // Danh sách ảnh
        swiperConfig: {
          maxList: 10,
          list: [
            {
              img: '',
              imgTitle: 'Hình ảnh',
              info: [
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
        docConfig: {
          title: 'Kiểu chỉ báo',
          tabVal: 0,
          tabList: [
            {
              name: 'Kiểu 1',
            },
            {
              name: 'Kiểu 2',
            },
            {
              name: 'Kiểu 3',
            },
            {
              name: 'Kiểu 4',
            },
          ],
        },
        docPosition: {
          title: 'Vị trí chỉ báo',
          tabVal: 1,
          tabList: [
            {
              name: 'Căn trái',
            },
            {
              name: 'Căn giữa',
            },
            {
              name: 'Căn phải',
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
        dotColor: {
          title: 'Kiểu khi được chọn',
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
        dotBgColor: {
          title: 'Kiểu thường',
          default: [
            {
              item: '#DDDDDD',
            },
          ],
          color: [
            {
              item: '#DDDDDD',
            },
          ],
        },
        filletImg: {
          title: 'Bo góc ảnh',
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
          val: 10,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        imgConfig: {
          title: 'Khoảng cách ảnh',
          val: 1,
          min: 0,
        },
        componentBgConfig: {
          title: 'Nền thành phần',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#F5F5F5' }, { item: '#F5F5F5' }],
            color: [{ item: '#F5F5F5' }, { item: '#F5F5F5' }],
          },
          colorDirection: {
            title: 'Hướng chuyển màu',
            tabVal: 0,
            tabList: [{ name: 'Ngang' }, { name: 'Dọc' }, { name: 'Chéo trái' }, { name: 'Chéo phải' }],
          },
          imageConfig: {
            header: 'Ảnh nền',
            title: '',
            name: 'Tải lên ảnh',
            type: 'code',
            url: '',
            info: 'Kích thước đề xuất: 750px * 400px',
          },
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
        paddingConfig: {
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 10 }, { val: 10 }, { val: 10 }, { val: 10 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        fillet: {
          title: 'Bo góc nền',
          type: 0,
          list: [
            { val: 'Tất cả', icon: 'iconcaozuo-zhengti' },
            { val: 'Từng góc', icon: 'iconcaozuo-bianjiao' },
          ],
          valName: 'Giá trị bo góc',
          val: 8,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
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
      },
      configObj: null,
      vuexMenu: [],
      styleConfig: 0,
      bottomBgColor: '',
      filletImg: 0,
      filletValImg: 0,
      valListImg: [],
      docPosition: 0,
      toneConfig: 0,
      dotBgColor: '',
      dotColor: '',
      imgConfig: 0,
      pageData: {},
      edge: 0,
      imgSrc: [],
      docStyle: 0,
      paddingConfig: {
        val: 0,
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
      marginConfig: {
        val: 0,
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
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
      let dataClone = JSON.parse(JSON.stringify(data));
      for (let key in this.defaultConfig) {
        if (dataClone[key] == undefined) {
          this.$set(dataClone, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }

      // Tương thích dữ liệu cũ: nền phía dưới
      if (data.bgColor && !dataClone.bottomBgColor) {
        dataClone.bottomBgColor.color[0].item = data.bgColor.color[0].item;
      }

      // Tương thích dữ liệu cũ: lề trong (padding)
      if (!data.paddingConfig) {
        if (dataClone.topConfig) dataClone.paddingConfig.valList[0].val = dataClone.topConfig.val;
        if (dataClone.bottomConfig) dataClone.paddingConfig.valList[2].val = dataClone.bottomConfig.val;
        if (dataClone.prConfig) {
          dataClone.paddingConfig.valList[1].val = dataClone.prConfig.val;
          dataClone.paddingConfig.valList[3].val = dataClone.prConfig.val;
        }
      }
      // Tương thích dữ liệu cũ: lề ngoài (margin)
      if (!data.marginConfig) {
        if (dataClone.mbConfig) dataClone.marginConfig.valList[0].val = dataClone.mbConfig.val;
      }

      this.configObj = dataClone;

      this.styleConfig = dataClone.styleConfig.tabVal || 0;
      this.bottomBgColor = dataClone.bottomBgColor ? dataClone.bottomBgColor.color[0].item : '';
      this.filletImg = dataClone.filletImg.type;
      this.filletValImg = dataClone.filletImg.val;
      this.valListImg = dataClone.filletImg.valList;
      this.docPosition = dataClone.docPosition.tabVal;
      this.toneConfig = dataClone.toneConfig.tabVal;
      this.dotColor = dataClone.dotColor.color[0].item;
      this.dotBgColor = dataClone.dotBgColor.color[0].item;
      this.imgConfig = dataClone.imgConfig.val;
      this.imgSrc = dataClone.swiperConfig.list;
      this.docStyle = dataClone.docConfig.tabVal;
      this.paddingConfig = dataClone.paddingConfig;
      this.marginConfig = dataClone.marginConfig;
    },
  },
};
</script>

<style scoped lang="scss">
.empty-box {
  height: 170px;
  background-color: #f3f9ff;
  .shan {
    width: 65px !important;
    height: 50px !important;
  }
  &.style3 {
    width: 16px;
    height: 134px;
    border-radius: 0;
    img {
      height: 100%;
    }
  }
  &.on {
    flex: 1;
    height: 170px;
  }
}

.banner {
  width: 100%;
  margin-top: 0;
  &.on {
    margin-top: -96px;
  }

  img {
    width: 100%;
    height: 100%;
    border-radius: 6px;
  }
}

.bg {
  width: 100%;
  height: 96px;
}

.dot {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  display: flex;
  align-items: center;

  &.on {
    .dot-item {
      width: 5px;
      height: 5px;
      &.ons {
        width: 9px;
        height: 5px;
        border-radius: 4px;
      }
    }
  }

  &.on2 {
    .dot-item {
      width: 10px;
      height: 3px;
      border-radius: 4px;
    }
  }

  .dot-item {
    width: 6px;
    height: 6px;
    background: #dddddd;
    border-radius: 50%;
    margin: 0 3px;
  }

  .line-dot {
    width: 30px;
    height: 3px;
    border-radius: 4px;
    background-color: #dddddd;
    .item {
      width: 10px;
      height: 100%;
      border-radius: 4px;
      background-color: #e93323;
    }
  }

  &.number {
    width: 40px;
    height: 18px;
    border-radius: 100px;
    background: rgba(0, 0, 0, 0.3);
    color: #fff;
    font-size: 8px;
    .num {
      width: 22px;
      height: 100%;
      border-radius: 20px 0 20px 20px;
      background: rgba(0, 0, 0, 0.1);
      font-size: 10px;
      text-align: center;
      line-height: 18px;
    }
    .numCon {
      width: 18px;
      text-align: center;
      line-height: 18px;
    }
  }
}
</style>
