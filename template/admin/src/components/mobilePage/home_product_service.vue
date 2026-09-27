<template>
  <common_wrapper :config="configObj" v-if="!isHide">
    <div class="product-service">
      <!-- Chương trình -->
      <div class="item" v-if="checkList.includes(0)">
        <div class="label" :style="{ color: titleColor }">Chương trình</div>
        <div class="content">
          <div class="tags">
            <span class="tag" :style="tagStyle"
              ><span class="mb-iconfont icon-ic_user1"></span>Mua chung 2 người<span
                class="iconfont iconyou"
                :style="{ color: activityColor }"
              ></span
            ></span>
            <span class="tag" :style="tagStyle"
              ><span class="mb-iconfont icon-miaosha1"></span>Flash sale giờ vàng<span
                class="iconfont iconyou"
                :style="{ color: activityColor }"
              ></span
            ></span>
            <span class="tag" :style="tagStyle"
              ><span class="mb-iconfont icon-ic_sale"></span>Tham gia săn giảm giá<span
                class="iconfont iconyou"
                :style="{ color: activityColor }"
              ></span
            ></span>
          </div>
          <span class="iconfont iconyou" :style="{ color: contentColor }"></span>
        </div>
      </div>
      <!-- Chọn -->
      <div class="item" v-if="checkList.includes(1)">
        <div class="label" :style="{ color: titleColor }">Chọn</div>
        <div class="content">
          <span :style="{ color: contentColor }">Đen, 80ml</span>
          <span class="iconfont iconyou" :style="{ color: contentColor }"></span>
        </div>
      </div>
      <!-- Tham số -->
      <div class="item" v-if="checkList.includes(2)">
        <div class="label" :style="{ color: titleColor }">Tham số</div>
        <div class="content">
          <span :style="{ color: contentColor }">Hàm lượng lông vũ 85% · Vải polyester</span>
          <span class="iconfont iconyou" :style="{ color: contentColor }"></span>
        </div>
      </div>
      <!-- Dịch vụ -->
      <div class="item" v-if="checkList.includes(3)">
        <div class="label" :style="{ color: titleColor }">Dịch vụ</div>
        <div class="content">
          <span :style="{ color: contentColor }">Cam kết chính hãng · Đổi trả 7 ngày không cần lý do · Bảo hiểm phí vận chuyển trả hàng...</span>
          <span class="iconfont iconyou" :style="{ color: contentColor }"></span>
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_product_service',
  cname: 'Dịch vụ sản phẩm',
  configName: 'c_product_service',
  icon: '#iconzujian-shangpinfuwu', // Need a suitable icon, using placeholder
  type: 3, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ 3 thành phần sản phẩm 4 thành phần người dùng
  defaultName: 'productService',
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
    isHide() {
      return this.configObj ? this.configObj.isHide : true;
    },
    checkList() {
      return this.configObj && this.configObj.checkBoxConfig ? this.configObj.checkBoxConfig.type : [];
    },
    titleColor() {
      return this.configObj && this.configObj.titleColor ? this.configObj.titleColor.color[0].item : '#999999';
    },
    contentColor() {
      return this.configObj && this.configObj.contentColor ? this.configObj.contentColor.color[0].item : '#333333';
    },
    isCustomTone() {
      return this.configObj && this.configObj.toneConfig && this.configObj.toneConfig.tabVal === 1;
    },
    tagStyle() {
      if (this.isCustomTone) {
        return {
          color: this.configObj.activityColor ? this.configObj.activityColor.color[0].item : '#E93323',
          background: this.configObj.activityBgColor ? this.configObj.activityBgColor.color[0].item : '#FDEBE9',
        };
      }
      // Follow theme - assuming standard theme colors or hardcoded for now if theme var not available easily
      return {
        color: '#E93323',
        background: '#FDEBE9',
      };
    },
    activityColor() {
      return this.configObj && this.configObj.activityColor ? this.configObj.activityColor.color[0].item : '#E93323';
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
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
    });
  },
  data() {
    return {
      defaultConfig: {
        cname: 'Dịch vụ sản phẩm',
        name: 'productService',
        timestamp: this.num,
        openService: 'Bật dịch vụ',
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        checkBoxConfig: {
          title: 'Thông tin hiển thị',
          type: [0, 1, 2, 3],
          list: [
            { id: 0, name: 'Chương trình' },
            { id: 1, name: 'Chọn' },
            { id: 2, name: 'Tham số' },
            { id: 3, name: 'Dịch vụ' },
          ],
        },
        serviceStyleTitle: 'Kiểu dịch vụ',
        generalStyleTitle: 'Kiểu chung',
        titleColor: {
          title: 'Chữ tiêu đề',
          default: [{ item: '#999999' }],
          color: [{ item: '#999999' }],
        },
        contentColor: {
          title: 'Chữ nội dung',
          default: [{ item: '#333333' }],
          color: [{ item: '#333333' }],
        },
        toneConfig: {
          title: 'Tông màu',
          tabVal: 0,
          tabList: [{ name: 'Theo phong cách chủ đề' }, { name: 'Tùy chỉnh' }],
        },
        activityColor: {
          title: 'Nội dung chương trình',
          default: [{ item: '#E93323' }],
          color: [{ item: '#E93323' }],
        },
        activityBgColor: {
          title: 'Nền chương trình',
          default: [{ item: '#FDEBE9' }],
          color: [{ item: '#FDEBE9' }],
        },
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
        componentBgConfig: {
          title: 'Nền thành phần',
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
      },
      configObj: null,
      pageData: {},
    };
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

      // Tương thích dữ liệu cũ: nền thành phần
      if (!data.componentBgConfig && data.componentBgColor) {
        dataClone.componentBgConfig.colorConfig.color[0].item = data.componentBgColor.color[0].item;
        if (data.componentBgColor.color[1]) {
          dataClone.componentBgConfig.colorConfig.color[1].item = data.componentBgColor.color[1].item;
        }
      }
      this.configObj = dataClone;
    },
  },
};
</script>

<style scoped lang="scss">
.product-service {
  overflow: hidden;
  .item {
    display: flex;
    justify-content: space-between;
    align-items: center; // Align items vertically center
    padding: 12px 0;
    &:last-child {
      border-bottom: none;
    }
    .label {
      width: 40px;
      font-size: 14px;
      color: #999;
      margin-right: 10px;
      flex-shrink: 0; // Prevent label from shrinking
    }
    .content {
      flex: 1;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      color: #333;
      overflow: hidden; // Prevent overflow

      // Make sure text truncates if too long
      > span:first-child {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
      }

      .tags {
        display: flex;
        flex-wrap: wrap;
        .tag {
          font-size: 10px;
          padding: 2px 5px;
          border-radius: 10px;
          margin-right: 5px;
          margin-bottom: 0;
          display: flex;
          align-items: center;
          justify-content: center;
          .iconfont {
            font-size: 8px;
            margin-left: 2px;
            line-height: 12px;
          }
          .mb-iconfont {
            font-size: 12px;
            margin-right: 2px;
            line-height: 12px;
          }
        }
      }
      .iconfont {
        font-size: 12px;
        color: #333;
        margin-left: 5px;
      }
    }
  }
}
</style>
