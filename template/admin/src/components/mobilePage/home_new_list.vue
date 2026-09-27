<template>
  <common_wrapper :config="configObj">
    <div class="wrapper" v-if="styleConfig == 0">
      <div
        class="item"
        v-for="(item, index) in list"
        :key="index"
        :style="{
          background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
          borderRadius: fillet
            ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
            : filletVal + 'px',
        }"
      >
        <div class="name line1" :style="{ color: nameColor, fontWeight: nameConfig ? '' : 'bold' }">
          {{ item.title || 'Tiêu đề bài viết tiêu đề bài viết tiêu đề bài viết' }}
        </div>
        <div class="pictrue">
          <img
            :src="item.image_input[0]"
            v-if="item.image_input"
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
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="bottom acea-row" :class="checkboxList.indexOf(0) != -1 ? 'row-between-wrapper' : 'row-right'">
          <div v-if="checkboxList.indexOf(0) != -1" :style="{ color: timeColor }">
            {{ (item.add_time || '1621474811') | formatDate }}
          </div>
          <div class="right">
            <div class="acea-row row-center-wrapper mr5" v-if="checkboxList.indexOf(1) != -1">
              <span class="iconfont iconfangwenliang" :style="{ color: `${browseColor}` }"></span>
              <span class="num" :style="{ color: `${statisticColor}` }">{{ item.visit || 0 }}</span>
            </div>
            <!-- <div class="like acea-row row-center-wrapper" v-if="checkboxList.indexOf(2) != -1">
              <span
                v-if="item.isLike"
                class="iconfont iconic_Like"
                :style="{ color: toneConfig ? `${likeSuccessColor}` : `${colorStyle.theme}` }"
              ></span>
              <span v-else class="iconfont iconic_Like" :style="{ color: `${likeColor}` }"></span>
              <span class="num" :style="{ color: `${statisticColor}` }">{{ item.like || 0 }}</span>
            </div> -->
          </div>
        </div>
      </div>
    </div>
    <div class="list-wrapper" v-else-if="styleConfig == 1">
      <div
        class="item"
        v-for="(item, index) in list"
        :key="index"
        :style="{
          background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
          borderRadius: fillet
            ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
            : filletVal + 'px',
        }"
      >
        <div class="pictrue" v-if="item.image_input">
          <img
            :src="item.image_input[0]"
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
        </div>
        <div
          class="empty-box on"
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
          <img src="../../assets/images/shan.png" />
        </div>
        <div class="info">
          <div class="titleCon">
            <div class="title line2" :style="{ color: nameColor, fontWeight: nameConfig ? '' : 'bold' }">
              {{ item.title || 'Tiêu đề bài viết tiêu đề bài viết tiêu đề bài viết' }}
            </div>
          </div>
          <div class="bottom acea-row" :class="checkboxList.indexOf(0) != -1 ? 'row-between-wrapper' : 'row-right'">
            <div class="time" v-if="checkboxList.indexOf(0) != -1" :style="{ color: timeColor }">
              {{ (item.add_time || '1621474811') | formatDate }}
            </div>
            <div class="right">
              <div class="acea-row row-center-wrapper mr5" v-if="checkboxList.indexOf(1) != -1">
                <span class="iconfont iconfangwenliang" :style="{ color: `${browseColor}` }"></span>
                <span :style="{ color: `${statisticColor}` }">{{ item.visit || 0 }}</span>
              </div>
              <!-- <div class="like acea-row row-center-wrapper" v-if="checkboxList.indexOf(2) != -1">
                <span
                  v-if="item.isLike"
                  class="iconfont iconic_Like"
                  :style="{ color: toneConfig ? `${likeSuccessColor}` : `${colorStyle.theme}` }"
                ></span>
                <span v-else class="iconfont iconic_Like" :style="{ color: `${likeColor}` }"></span>
                <span class="likeNum" :style="{ color: `${statisticColor}` }">{{ item.like || 0 }}</span>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wrapper2" v-else>
      <div
        class="item"
        :class="list.length % 2 == 0 ? 'on2' : 'on1'"
        v-for="(item, index) in list"
        :key="index"
        :style="{
          background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
          borderRadius: fillet
            ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
            : filletVal + 'px',
        }"
      >
        <div class="pictrue">
          <img
            :src="item.image_input[0]"
            v-if="item.image_input"
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
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="text">
          <div class="name line1" :style="{ color: `${nameColor}`, fontWeight: nameConfig ? '' : 'bold' }">
            {{ item.title || 'Tiêu đề bài viết tiêu đề bài viết tiêu đề bài viết' }}
          </div>
          <div class="bottom acea-row" :class="checkboxList.indexOf(0) != -1 ? 'row-between-wrapper' : 'row-right'">
            <div class="time" v-if="checkboxList.indexOf(0) != -1" :style="{ color: `${timeColor}` }">
              {{ (item.add_time || '1621474811') | formatDate }}
            </div>
            <div class="acea-row row-center-wrapper mr5" v-if="checkboxList.indexOf(1) != -1">
              <span class="iconfont iconfangwenliang" :style="{ color: `${browseColor}` }"></span>
              <span :style="{ color: `${statisticColor}` }">{{ item.visit || 0 }}</span>
            </div>
            <!-- <div
              class="like acea-row row-center-wrapper"
              v-if="checkboxList.indexOf(1) == -1 && checkboxList.indexOf(2) != -1"
            >
              <span
                v-if="item.isLike"
                class="iconfont iconic_Like"
                :style="{ color: toneConfig ? `${likeSuccessColor}` : `${colorStyle.theme}` }"
              ></span>
              <span v-else class="iconfont iconic_Like" :style="{ color: `${likeColor}` }"></span>
              <span class="likeNum" :style="{ color: `${statisticColor}` }">{{ item.like || 0 }}</span>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { categoryList } from '@/api/diy';
import { mapState } from 'vuex';
import { formatDate } from '@/utils/validate';
// import theme from "@/mixins/theme";
export default {
  name: 'home_new_list',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        const date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  cname: 'Danh sách bài viết',
  icon: '#iconzujian-wenzhangliebiao',
  configName: 'c_new_list',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'articleList', // Tên khớp bên ngoài
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
        cname: 'Danh sách bài viết',
        desc: 'Giới thiệu danh sách bài viết',
        name: 'articleList',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleRight: 'Kiểu danh sách',
        titleArticle: 'Cài đặt bài viết',
        titleList: 'Cài đặt danh sách',
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
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 1,
          tabList: [
            {
              name: 'Hiển thị ảnh lớn',
            },
            {
              name: 'Một cột dọc',
            },
            {
              name: 'Hai cột ngang',
            },
          ],
        },
        numConfig: {
          val: 3,
          title: 'Số lượng bài viết',
        },
        selectConfig: {
          title: 'Danh mục bài viết',
          activeValue: '',
          list: [
            {
              activeValue: '',
              title: '',
            },
            {
              activeValue: '',
              title: '',
            },
          ],
        },
        goodsList: {
          max: 20,
          list: [],
        },
        selectList: {
          title: 'Danh sách bài viết',
          list: [],
        },
        checkboxList: {
          title: 'Hiển thị',
          type: [0, 1, 2],
          list: [
            {
              id: 0,
              name: 'Ngày giờ',
            },
            {
              id: 1,
              name: 'Lượt xem',
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
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        nameConfig: {
          title: 'Tiêu đề bài viết',
          tabVal: 1,
          tabList: [
            {
              name: 'In đậm',
            },
            {
              name: 'Thường',
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
        likeSuccessColor: {
          title: 'Đã thích',
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
        nameColor: {
          title: 'Tiêu đề bài viết',
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
        timeColor: {
          title: 'Ngày giờ',
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
        browseColor: {
          title: 'Biểu tượng lượt xem',
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
        statisticColor: {
          title: 'Số liệu thống kê',
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
        componentBgConfig: {
          title: 'Cài đặt nền',
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
        bgColor: {
          title: 'Nền thành phần',
          isAlpha: false,
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
              item: '#F5F5F5',
            },
          ],
          color: [
            {
              item: '#F5F5F5',
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
          val: 10,
          min: 0,
        },
        mbConfig: {
          title: 'Lề trang',
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
          val: 6,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
      },
      bgColorLeft: '',
      bgColorRight: '',
      itemStyle: 0,
      prConfig: 0,
      styleConfig: 0,
      checkboxList: [],
      filletImg: 0,
      filletValImg: 0,
      valListImg: [],
      fillet: 0,
      filletVal: 0,
      valList: [],
      nameConfig: 0,
      nameColor: '',
      timeColor: '',
      browseColor: '',
      // likeColor: '',
      statisticColor: '',
      bottomBgColor: '',
      configObj: {},
      pageData: {},
      topConfig: 0,
      bottomConfig: 0,
      toneConfig: 0,
      likeSuccessColor: '',
      list: [],
    };
  },
  created() {},
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
      // this.categoryList()
    });
  },
  methods: {
    categoryList() {
      categoryList().then((res) => {
        this.pageData.selectConfig.list = res.data;
        this.pageData.selectConfig.list.map((item) => {
          item.id.toString();
          // return item;
        });
        this.$store.commit('mobildConfig/UPDATEARR', { num: this.num, val: this.pageData });
      });
    },
    setConfig(data) {
      if (!data) return;
      for (let key in this.defaultConfig) {
        if (data[key] == undefined) {
          this.$set(data, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }
      this.styleConfig = data.styleConfig.tabVal;
      this.checkboxList = data.checkboxList.type;
      this.filletImg = data.filletImg.type;
      this.filletValImg = data.filletImg.val;
      this.valListImg = data.filletImg.valList;
      this.fillet = data.fillet.type;
      this.filletVal = data.fillet.val;
      this.valList = data.fillet.valList;
      this.bgColorLeft = data.bgColor.color[0].item;
      this.bgColorRight = data.bgColor.color[1].item;
      this.nameConfig = data.nameConfig.tabVal;
      this.nameColor = data.nameColor.color[0].item;
      this.timeColor = data.timeColor.color[0].item;
      this.browseColor = data.browseColor.color[0].item;
      // this.likeColor = data.likeColor.color[0].item;
      this.likeSuccessColor = data.likeSuccessColor.color[0].item;
      this.statisticColor = data.statisticColor.color[0].item;
      this.bottomBgColor = data.bottomBgColor.color[0].item;
      this.topConfig = data.topConfig.val;
      this.bottomConfig = data.bottomConfig.val;
      this.toneConfig = data.toneConfig.tabVal;
      this.mTOP = data.mbConfig ? data.mbConfig.val : 0;
      this.prConfig = data.prConfig.val;

      this.configObj = data;

      if (!data.paddingConfig) {
        let paddingConfig = {
          title: 'Lề trong',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        paddingConfig.valList[0].val = data.topConfig.val;
        paddingConfig.valList[2].val = data.bottomConfig.val;
        paddingConfig.valList[1].val = data.prConfig.val;
        paddingConfig.valList[3].val = data.prConfig.val;
        this.$set(this.configObj, 'paddingConfig', paddingConfig);
      }

      if (!data.marginConfig) {
        let marginConfig = {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        marginConfig.valList[0].val = data.mbConfig ? data.mbConfig.val : 0;
        this.$set(this.configObj, 'marginConfig', marginConfig);
      }

      const selectList = data.selectList.list || [];
      if (selectList.length) {
        this.list = selectList;
      } else {
        this.list = data.numConfig.val;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-page {
  display: inline-block;
  width: -webkit-fill-available;
}
.empty-box {
  background: #f3f9ff;
  img {
    width: 65px !important;
    height: 50px !important;
  }
}
.wrapper2 {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  width: 100%;
  
  .item {
    width: calc(50% - 5px);
    margin-bottom: 10px;
    box-sizing: border-box;
    
    &.on1 {
      &:nth-last-child(1) {
        margin-bottom: 0;
      }
    }
    &.on2 {
      &:nth-last-child(1) {
        margin-bottom: 0;
      }
      &:nth-last-child(2) {
        margin-bottom: 0;
      }
    }
    .pictrue {
      width: 100%;
      height: 108px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    .text {
      padding: 8px 10px 4px 10px;
      box-sizing: border-box;
      .name {
        color: #333333;
        font-size: 14px;
      }
      .time {
        font-size: 12px;
        color: #999999;
      }
      .bottom {
        font-size: 12px;
        color: #999999;
        margin-top: 2px;
        .likeNum {
          padding-top: 2px;
        }
        .iconfont {
          font-size: 15px;
          margin-right: 4px;
          padding-top: 2px;
        }
      }
    }
  }
}
.wrapper {
  .item {
    padding: 16px 12px 12px 12px;
    margin-bottom: 10px;
    &:nth-last-child(1) {
      margin-bottom: 0;
    }
    .name {
      font-size: 14px;
      color: #333333;
    }
    .pictrue {
      width: 100%;
      height: 160px;
      margin-top: 12px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    .bottom {
      color: #999999;
      font-size: 12px;
      margin-top: 12px;
      .right {
        display: flex;
        align-items: center;
        .iconfont {
          font-size: 16px;
          margin-right: 4px;
        }
        .iconic_Like {
          font-size: 14px;
        }
        .like {
          margin-left: 16px;
        }
        .num {
          margin-top: 2px;
        }
      }
    }
  }
}
.list-wrapper {
  .item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #fff;
    padding: 8px;
    margin-bottom: 10px;
    &:nth-last-child(1) {
      margin-bottom: 0 !important;
    }
    .info {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      margin-left: 10px;
      flex: 1;
      .titleCon {
        height: 52px;
      }
      .title {
        color: #333;
        font-size: 14px;
      }
      .bottom {
        color: #999999;
        font-size: 12px;
        .right {
          display: flex;
          align-items: center;
          .iconfont {
            font-size: 16px;
            margin-right: 4px;
          }
          .like {
            margin-left: 16px;
            .likeNum {
              padding-top: 2px;
            }
          }
        }
      }
    }
    .empty-box {
      width: 120px;
      height: 76px;
      background: #f3f9ff img {
        width: 50px !important;
        height: 38px !important;
      }
    }
    .pictrue {
      width: 120px;
      height: 76px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
  }
}
</style>
