<template>
  <common_wrapper v-if="configObj" :config="configObj">
    <div class="homeComb" :class="bannerImg ? '' : 'on'">
      <div class="bgImg">
        <img :src="bannerImg" v-if="bannerImg" />
      </div>
      <div class="bag-gradient" :style="[bgGradientStyle]"></div>
      <div class="searchBox acea-row row-between-wrapper">
        <div class="title" v-if="searchBox == 0">{{ titleConfig }}</div>
        <img :src="imgSrc" alt="" v-if="imgSrc && searchBox == 1" />
        <div class="box acea-row row-between-wrapper" :class="imgSrc ? '' : 'on'">
          <span v-if="hotWords" class="hot">{{ hotWords }}</span>
          <span v-else>{{ placeholders }}</span>
          <span class="iconfont iconsousuo1"></span>
        </div>
      </div>
      <div class="nav acea-row row-between-wrapper flex-no-wrap" v-if="classConfig == 0">
        <div class="list">
          <div class="listCon acea-row row-middle">
            <div
              class="item"
              :class="index == 0 ? 'on' : ''"
              :style="{
                marginLeft: contentConfig + 'px',
              }"
              v-for="(item, index) in navList.slice(0, 20)"
              :key="index"
            >
              {{ item.text.val }}
            </div>
          </div>
        </div>
        <div class="acea-row row-middle flex-no-wrap">
          <div class="bar"></div>
          <div class="iconfont iconerweima"></div>
        </div>
      </div>
      <div class="banner" :class="classConfig == 0 ? '' : 'on'" v-if="styleConfig == 0">
        <img
          :src="bannerImg"
          v-if="bannerImg"
          :style="{
            borderRadius: imgRadius,
          }"
        />
        <div
          class="empty-box"
          v-else
          :style="{
            borderRadius: imgRadius,
          }"
        >
          <img class="shan" src="../../assets/images/shan.png" />
        </div>
      </div>
      <div class="banner ons" :class="classConfig == 0 ? '' : 'on'" v-else>
        <div class="acea-row row-middle">
          <div
            class="empty-box style3"
            :style="{
              borderRadius: imgRadiusLeft,
            }"
          >
            <img
              :src="imgSrcList[1].img"
              alt=""
              v-if="imgSrcList.length > 1 && imgSrcList[1].img"
              :style="{
                borderRadius: imgRadiusLeft,
              }"
            />
          </div>
          <div
            class="empty-box style3 on"
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img
              :src="imgSrcList[0].img"
              alt=""
              v-if="imgSrcList.length && imgSrcList[0].img"
              :style="{
                borderRadius: imgRadius,
              }"
            />
            <img class="shan" src="../../assets/images/shan.png" v-else />
          </div>
          <div
            class="empty-box style3"
            :style="{
              borderRadius: imgRadiusRight,
            }"
          >
            <img
              :src="imgSrcList[2].img"
              alt=""
              v-if="imgSrcList.length > 2 && imgSrcList[2].img"
              :style="{
                borderRadius: imgRadiusRight,
              }"
            />
          </div>
        </div>
      </div>
      <div :class="styleConfig ? 'banDot' : ''">
        <div
          class="dot"
          v-if="docStyle == 2"
          :style="{
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
  name: 'home_comb', // Tên thành phần
  cname: 'Tìm kiếm kèm ảnh trình chiếu', // Tên tiêu đề
  icon: '#iconzujian-zuhezujian',
  defaultName: 'homeComb', // Tên khớp bên ngoài
  configName: 'c_home_comb', // Tên cấu hình bên phải
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
    bgGradientStyle() {
      return {
        'background-image': `linear-gradient(to bottom, rgba(245,245,245,0) 0%, rgba(245,245,245,0) 50%, ${this.gradientColor} 100%)`,
      };
      return {};
    },
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
        const data = this.$store.state.mobildConfig.defaultArray[nVal];
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler(nVal, oVal) {
        const data = this.$store.state.mobildConfig.defaultArray[this.num];
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
        cname: 'Tìm kiếm kèm ảnh trình chiếu',
        // Mô tả thành phần
        desc: 'Thành phần tìm kiếm kèm ảnh trình chiếu',
        name: 'homeComb',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleSearch: 'Cài đặt tìm kiếm',
        titleHotWords: 'Từ khóa tìm kiếm phổ biến',
        titleTab: 'Cài đặt tab',
        titleImg: 'Cài đặt ảnh',
        titleRight: 'Cài đặt nhãn',
        titlePointer: 'Cài đặt chỉ báo',
        titleGradient: 'Cài đặt chuyển màu',
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
        classConfig: {
          title: 'Cài đặt danh mục',
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
        searchConfig: {
          title: 'Cài đặt tìm kiếm',
          tabVal: 0,
          tabList: [
            {
              name: 'Hiển thị bình thường',
            },
            {
              name: 'Cố định ở đầu trang khi cuộn',
            },
          ],
        },
        searchBox: {
          title: 'Ô tìm kiếm',
          tabVal: 1,
          tabList: [
            {
              name: 'Văn bản',
            },
            {
              name: 'logo',
            },
          ],
        },
        searchFix: {
          title: 'Loại định vị',
          tabVal: 0,
          tabList: [
            {
              name: 'Cửa hàng',
            },
            {
              name: 'Vị trí người dùng',
            },
          ],
        },
        titleConfig: {
          title: 'Tiêu đề',
          value: 'Tiêu đề',
          place: 'Vui lòng nhập tiêu đề',
          max: 6,
        },
        logoConfig: {
          info: 'Đề xuất: 200px * 100px',
          url: '',
          type: 'code',
          name: 'Logo mặc định',
        },
        logoUpConfig: {
          info: 'Đề xuất: 200px * 100px',
          url: '',
          type: 'code',
          name: 'Logo khi cố định đầu trang',
        },
        inputConfig: {
          title: 'Chữ gợi ý',
          value: 'Vui lòng nhập từ khóa tìm kiếm',
          place: 'Điền nội dung',
          max: 10,
        },
        hotWords: {
          list: [
            {
              val: '',
            },
          ],
        },
        gradientColor: {
          title: 'Nền thành phần',
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
        numConfig: {
          placeholder: 'Đặt thời gian hiển thị từ khóa phổ biến',
          title: 'Thời gian cuộn',
          val: 3,
          type: 'words',
        },
        tabListConfig: {
          title: 'Kéo thả khối để điều chỉnh thứ tự tab',
          max: 100,
          list: [
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Trang chủ',
                max: 4,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Tiêu đề mẫu',
                max: 4,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Tiêu đề mẫu',
                max: 4,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Tiêu đề mẫu',
                max: 4,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
          ],
        },
        contentConfig: {
          title: 'Khoảng cách nội dung',
          val: 20,
          min: 0,
        },
        classColor: {
          title: 'Danh mục thả xuống',
          default: [
            {
              item: '#E93323',
            },
            {
              item: '#E93323',
            },
          ],
          color: [
            {
              item: '#E93323',
            },
            {
              item: '#E93323',
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
        swiperConfig: {
          title: 'Đề xuất: kích thước ảnh 702*320px; kéo thả khối để điều chỉnh thứ tự ảnh',
          bnt: 'Thêm',
          maxList: 10,
          list: [
            {
              img: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Tiêu đề',
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
        paddingConfig: {
          title: 'Lề trong',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          default: [{ item: '#f5f5f5' }],
          color: [{ item: '#f5f5f5' }],
        },
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
          val: 0, // 0: Off, 1: On
          colorConfig: {
            title: 'Màu đổ bóng',
            default: [{ item: 'rgba(0,0,0,0.1)' }],
            color: [{ item: 'rgba(0,0,0,0.1)' }],
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
        componentBgConfig: {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#E93323' }, { item: '#E93323' }],
            color: [{ item: '#E93323' }, { item: '#E93323' }],
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
      },
      pageData: {},
      imgSrc: '',
      bannerImg: [],
      navList: [],
      styleConfig: 0,
      classConfig: 0,
      searchConfig: 0,
      placeholders: '',
      hotWords: '',
      contentConfig: 0,
      docPosition: 0,
      toneConfig: 0,
      dotBgColor: '',
      dotColor: '',
      docStyle: 0,
      imgRadius: 0,
      imgRadiusLeft: 0,
      imgRadiusRight: 0,
      imgSrcList: [],
      searchBox: 0,
      searchFix: 0,
      titleConfig: '',
      gradientColor: '#f5f5f5',
      configObj: null,
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
        if (dataClone[key] === undefined) {
          this.$set(dataClone, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }
      this.configObj = dataClone;
      this.navList = dataClone.tabListConfig.list;
      this.styleConfig = dataClone.styleConfig.tabVal;
      this.classConfig = dataClone.classConfig.tabVal;
      this.searchConfig = dataClone.searchConfig.tabVal;
      this.searchBox = dataClone.searchBox.tabVal;
      this.searchFix = dataClone.searchFix.tabVal;
      this.logoConfig = dataClone.logoConfig.url;
      this.imgSrc = dataClone.logoConfig.url;
      this.titleConfig = dataClone.titleConfig.value;
      this.placeholders = dataClone.inputConfig.value;
      this.hotWords = dataClone.hotWords.list.length ? dataClone.hotWords.list[0].val : '';
      this.contentConfig = dataClone.contentConfig.val;
      this.imgSrcList = dataClone.swiperConfig.list;
      this.bannerImg = dataClone.swiperConfig.list.length ? dataClone.swiperConfig.list[0].img : '';
      this.docPosition = dataClone.docPosition.tabVal;
      this.toneConfig = dataClone.toneConfig.tabVal;
      this.dotBgColor = dataClone.dotBgColor.color[0].item;
      this.dotColor = dataClone.dotColor.color[0].item;
      this.docStyle = dataClone.docConfig.tabVal;
      this.gradientColor = dataClone.gradientColor.color[0].item;
      let filletImg = dataClone.filletImg.type;
      let filletValImg = dataClone.filletImg.val;
      let valListImg = dataClone.filletImg.valList;
      this.imgRadius = filletImg
        ? valListImg[0].val + 'px ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + valListImg[2].val + 'px'
        : filletValImg + 'px';
      this.imgRadiusLeft = filletImg
        ? '0 ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + '0'
        : '0 ' + filletValImg + 'px ' + filletValImg + 'px ' + '0';
      this.imgRadiusRight = filletImg
        ? valListImg[1].val + 'px 0 0 ' + valListImg[3].val + 'px'
        : filletValImg + 'px 0 0 ' + filletValImg + 'px';
    },
  },
};
</script>

<style scoped lang="scss">
.empty-box {
  height: 160px;
  background-color: #f3f9ff;
  .shan {
    width: 65px !important;
    height: 50px !important;
  }
  &.style3 {
    width: 16px;
    border-radius: 0;
    height: 144px;
    img {
      width: 16px;
      height: 100%;
    }
  }
  &.on {
    flex: 1;
    margin: 0 12px;
    height: 160px;
  }
}
.banDot {
  .dot {
    padding: 0 40px;
  }
}
.dot {
  position: absolute;
  left: 0;
  bottom: 25px;
  width: 100%;
  display: flex;
  align-items: center;
  z-index: 9;
  padding: 0 30px;

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
.homeComb {
  width: 100%;
  position: relative;
  overflow: hidden;
  padding-bottom: 13px;
  .bag-gradient {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
  }
  &.on {
    background: rgba(0, 0, 0, 0.2);
  }
  .bgImg {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 1;
    filter: blur(0);
    overflow: hidden;
    img {
      width: 100%;
      height: 100%;
      filter: blur(15px);
      transform: scale(1.5);
    }
  }
  .searchBox {
    position: relative;
    padding: 9px 12px 0 12px;
    z-index: 1;
    img {
      width: 69px;
      height: 30px;
      display: inline-block;
      margin-right: 10px;
    }
    .title {
      font-size: 14px;
      font-weight: 400;
      color: #fff;
      margin-right: 12px;
    }
    .map {
      font-size: 14px;
      font-weight: 400;
      color: #fff;
      margin-right: 12px;
      .iconfont {
        font-size: 12px;
      }
      .icondingwei {
        font-size: 14px;
        margin-right: 6px;
      }
    }
    .box {
      flex: 1;
      height: 29px;
      border-radius: 16px 16px 16px 16px;
      background-color: rgba(228, 228, 228, 0.4);
      padding: 0 12px;
      font-size: 12px;
      font-weight: 400;
      color: rgba(255, 255, 255, 0.5);
      &.on {
        width: 100%;
      }
      .hot {
        color: #fff;
      }
    }
  }
  .nav {
    position: relative;
    z-index: 1;
    padding: 0 12px;
    width: 100%;
    box-sizing: border-box;
    height: 42px;
    .list {
      width: 325px;
      overflow: hidden;
      .listCon {
        width: 10000%;
      }
    }
    .iconfont {
      font-size: 14px;
      color: #fff;
    }
    .bar {
      width: 1px;
      height: 15px;
      background: linear-gradient(135deg, rgba(215, 215, 215, 0) 0%, #fff 50%, rgba(215, 215, 215, 0) 100%);
      margin: 0 5px;
    }
    .item {
      font-weight: 400;
      color: #ffffff;
      font-size: 15px;
      position: relative;
      &.on {
        font-size: 16px;
        margin-left: 0 !important;
        .lines {
          position: absolute;
          width: 10px;
          height: 2px;
          background: #ffffff;
          transform: translateX(-50%);
          left: 50%;
          bottom: 0;
        }
      }
    }
  }
  .banner {
    width: 355px;
    height: 180px;
    position: relative;
    z-index: 1;
    border-radius: 6px;
    margin: 0 auto;
    &.on {
      margin-top: 15px;
    }
    &.ons {
      width: 100%;
    }
    img {
      width: 100%;
      height: 100%;
      border-radius: 6px;
    }
  }
}
</style>
