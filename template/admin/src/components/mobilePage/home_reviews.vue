<template>
  <common_wrapper :config="configObj" v-if="!isHide">
    <div class="reviews-box">
      <!-- Header -->
      <div class="header">
        <div class="left">
          <span class="title" :style="{ color: titleColor }">Đánh giá</span>
          <span class="count" :style="{ color: countColor }" v-if="checkList.includes(0)">(2.3k)</span>
        </div>
        <div class="right" v-if="checkList.includes(1)">
          <span class="rate"><span :style="{ color: rateColor }">99.0% </span>Tỷ lệ đánh giá tốt</span>
          <span class="iconfont iconyou" :style="{ color: rateColor }"></span>
        </div>
      </div>

      <!-- Review List -->
      <div class="list" :class="{ 'is-slide': isSlide }">
        <div class="item" v-for="(item, index) in showList" :key="index">
          <div class="user-info">
            <div class="avatar">
              <img :src="item.avatar" alt="" />
            </div>
            <div class="info">
              <div class="name">{{ item.name }}</div>
              <div class="stars">
                <span
                  class="mb-iconfont icon-pingjia"
                  v-for="i in 5"
                  :key="i"
                  :style="{ color: i <= item.star ? starColor : '#eee' }"
                ></span>
              </div>
            </div>
          </div>
          <div class="content">{{ item.content }}</div>
          <div class="images" v-if="item.images && item.images.length">
            <div class="img-box" v-for="(img, imgIndex) in item.images.slice(0, 4)" :key="imgIndex">
              <img :src="img" alt="" />
              <div class="more" v-if="imgIndex === 3 && item.images.length > 4">+{{ item.images.length - 4 }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'home_reviews',
  cname: 'Đánh giá sản phẩm',
  configName: 'c_reviews',
  icon: '#iconzujian-shangpinpingjia',
  type: 3, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'reviews',
  props: {
    index: {
      type: null,
      default: -1,
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
  data() {
    return {
      configObj: null,
      isHide: false,
      checkList: [],
      isSlide: false,
      showList: [],
      titleColor: '',
      countColor: '',
      isCustomTone: false,
      rateColor: '',
      starColor: '',
      defaultConfig: {
        cname: 'Đánh giá sản phẩm',
        name: 'reviews',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        headTitle: 'Cài đặt phần đầu',
        checkBoxConfig: {
          title: 'Thông tin hiển thị',
          type: [0, 1],
          list: [
            { id: 0, name: 'Số đánh giá' },
            { id: 1, name: 'Tỷ lệ đánh giá tốt' },
          ],
        },
        listTitle: 'Danh sách đánh giá',
        layoutConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          tabList: [{ name: 'Hiển thị một cột' }, { name: 'Vuốt ngang' }],
        },
        numConfig: {
          title: 'Số lượng đánh giá',
          val: 2,
          min: 1,
          max: 10,
        },
        // Style Config
        reviewStyleTitle: 'Kiểu đánh giá',
        generalStyleTitle: 'Kiểu chung',
        titleColor: {
          title: 'Chữ tiêu đề',
          default: [{ item: '#333333' }],
          color: [{ item: '#333333' }],
        },
        countColor: {
          title: 'Số lượng đánh giá',
          default: [{ item: '#999999' }],
          color: [{ item: '#999999' }],
        },
        toneConfig: {
          title: 'Tông màu',
          tabVal: 0, // 0: Follow Theme, 1: Custom
          tabList: [{ name: 'Theo phong cách chủ đề' }, { name: 'Tùy chỉnh' }],
        },
        rateColor: {
          title: 'Phần trăm đánh giá tốt',
          default: [{ item: '#E93323' }],
          color: [{ item: '#E93323' }],
        },
        starColor: {
          title: 'Số sao',
          default: [{ item: '#E93323' }],
          color: [{ item: '#E93323' }],
        },
        componentBgConfig: {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#fff' }, { item: '#fff' }],
            color: [{ item: '#fff' }, { item: '#fff' }],
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
          default: [{ item: '#F5F5F5' }],
          color: [{ item: '#F5F5F5' }],
        },
        paddingConfig: {
          title: 'Lề trong',
          val: 10,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
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
          val: 0,
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
        mbConfig: {
          title: 'Lề trang',
          val: 10,
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
          val: 8,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
      },
      mockList: [
        {
          avatar: require('@/assets/images/yonghu.png'), // Use placeholder if available
          name: 'Biệt danh người dùng',
          star: 5,
          content: 'Nhỏ nhắn, rất nhẹ và tiện, thích vô cùng! Mềm mại, mịn màng, cực kỳ tinh tế...',
          images: [
            require('@/assets/images/videoBg.png'),
            require('@/assets/images/videoBg.png'),
            require('@/assets/images/videoBg.png'),
            require('@/assets/images/videoBg.png'),
            require('@/assets/images/videoBg.png'),
          ],
        },
        {
          avatar: require('@/assets/images/yonghu.png'),
          name: 'Biệt danh người dùng',
          star: 4,
          content: 'Rất tốt, đáng đồng tiền, lần sau sẽ quay lại mua tiếp.',
          images: [require('@/assets/images/videoBg.png'), require('@/assets/images/videoBg.png')],
        },
        {
          avatar: require('@/assets/images/yonghu.png'),
          name: 'Biệt danh người dùng',
          star: 5,
          content: 'Rất tốt, đáng đồng tiền, lần sau sẽ quay lại mua tiếp.',
          images: [],
        },
      ],
    };
  },
  created() {
    const data = this.$store.state.mobildConfig.defaultArray[this.num];
    this.setConfig(data);
  },
  methods: {
    setConfig(data) {
      this.configObj = data ? data : this.defaultConfig;
      if (!this.configObj) return;
      this.isHide = this.configObj.isHide;
      this.checkList = this.configObj.checkBoxConfig ? this.configObj.checkBoxConfig.type : [];
      this.isSlide = this.configObj.layoutConfig ? this.configObj.layoutConfig.tabVal === 1 : false;
      const num = this.configObj.numConfig ? this.configObj.numConfig.val : 2;
      this.showList = this.mockList.slice(0, num);
      this.titleColor = this.configObj.titleColor ? this.configObj.titleColor.color[0].item : '#333333';
      this.countColor = this.configObj.countColor ? this.configObj.countColor.color[0].item : '#999999';
      this.isCustomTone = this.configObj.toneConfig && this.configObj.toneConfig.tabVal === 1;
      if (this.isCustomTone) {
        this.rateColor = this.configObj.rateColor ? this.configObj.rateColor.color[0].item : '#E93323';
        this.starColor = this.configObj.starColor ? this.configObj.starColor.color[0].item : '#E93323';
      } else {
        this.rateColor = '#E93323';
        this.starColor = '#E93323';
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
.reviews-box {
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    .left {
      .title {
        font-size: 15px;
        font-weight: bold;
        margin-right: 5px;
      }
      .count {
        font-size: 12px;
      }
    }
    .right {
      display: flex;
      align-items: center;
      .rate {
        font-size: 12px;
      }
      .iconyou {
        font-size: 12px;
        margin-left: 2px;
      }
    }
  }

  .list {
    &.is-slide {
      display: flex;
      overflow-x: auto;
      // Hide scrollbar
      &::-webkit-scrollbar {
        display: none;
      }
      .item {
        flex-shrink: 0;
        width: 85%;
        margin-bottom: 0;
        margin-right: 10px;
        // marginRight handled by inline style
      }
    }

    .item {
      background: #f9f9f9;
      border-radius: 6px;
      padding: 10px;
      margin-bottom: 10px; // Handled by inline style

      .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        .avatar {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          overflow: hidden;
          margin-right: 8px;
          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
        }
        .info {
          .name {
            font-size: 13px;
            color: #333;
            margin-bottom: 2px;
          }
          .stars {
            display: flex;
            .icon-pingjia {
              font-size: 10px;
              margin-right: 2px;
            }
          }
        }
      }

      .content {
        font-size: 13px;
        color: #333;
        line-height: 1.4;
        margin-bottom: 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
      }

      .images {
        display: flex;
        .img-box {
          width: 60px;
          height: 60px;
          border-radius: 4px;
          overflow: hidden;
          margin-right: 6px;
          position: relative;
          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
          .more {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
          }
        }
      }
    }
  }
}
</style>
