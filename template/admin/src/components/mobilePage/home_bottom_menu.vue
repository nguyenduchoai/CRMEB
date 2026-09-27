<template>
  <common_wrapper :config="configObj">
    <div class="bottom-menu acea-row row-middle">
      <!-- Icons Section -->
      <div class="icons-box acea-row row-middle">
        <div class="item" v-for="(item, index) in showIconsList" :key="index" :class="{ 'gift-item': item.id === 5 }">
          <div
            class="mb-iconfont"
            :class="item.icon"
            v-if="!isCustomImage && !item.url"
            :style="isCustomIcon ? customIconStyle : ''"
          ></div>
          <div v-else style="position: relative">
            <img class="menu-img" :src="item.img" :style="customImageStyle" alt="" />
            <span
              class="num"
              v-if="item.id === 2"
              style="
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #f5222d;
                color: #fff;
                font-size: 10px;
                padding: 0 4px;
                border-radius: 10px;
                line-height: 14px;
              "
              >0</span
            >
          </div>
          <div class="text">{{ item.name }}</div>
        </div>
      </div>

      <!-- Buttons Section -->
      <div class="buttons-box acea-row row-middle">
        <div
          class="btn cart-btn"
          v-if="showCartBtn"
          :style="{
            background: toneConfig ? cartBtnColor : themeColor2,
          }"
        >
          Thêm vào giỏ hàng
        </div>
        <div
          class="btn buy-btn"
          :style="{
            background: toneConfig ? buyBtnColor : themeColor,
          }"
        >
          Mua ngay
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'home_bottom_menu',
  cname: 'Menu dưới cùng',
  configName: 'c_bottom_menu',
  icon: '#iconzujian-dibucaidan', // Placeholder icon
  type: -1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ 3 thành phần sản phẩm 4 thành phần trang cá nhân
  defaultName: 'bottomMenu',
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
    ...mapState('mobildConfig', ['defaultArray', 'bottomMenu']),
    entryConfig() {
      return this.configObj?.entryConfig;
    },
    isCustomEntry() {
      return this.entryConfig?.tabVal === 1;
    },
    isCustomImage() {
      return this.isCustomEntry && this.configObj?.menuConfig?.listStyle === 0;
    },
    isCustomIcon() {
      return this.isCustomEntry && this.configObj?.menuConfig?.listStyle === 1;
    },
    customImageStyle() {
      const fillet = this.configObj?.menuPcFillet;
      if (!fillet) return { width: '20px', height: '20px' };
      let radius;
      if (fillet.type) {
        radius = `${fillet.valList[0].val}px ${fillet.valList[1].val}px ${fillet.valList[3].val}px ${fillet.valList[2].val}px`;
      } else {
        radius = `${fillet.val}px`;
      }
      return {
        borderRadius: radius,
        width: '20px',
        height: '20px',
        display: 'block',
      };
    },
    customIconStyle() {
      const config = this.configObj;
      if (!config) return {};
      const color = config.iconColor?.color?.[0]?.item || '#333';
      const size = config.iconSize?.val || 20;
      const rotate = config.iconRotate?.val || 0;
      const padding = config.padding?.val || 0;
      const shadow = config.shadow?.tabVal === 1 ? '0px 2px 4px rgba(0,0,0,0.2)' : 'none';
      return {
        width: '20px',
        height: '20px',
        color: color,
        fontSize: `${size}px`,
        transform: `rotate(${rotate}deg)`,
        padding: `${padding}px`,
        textShadow: shadow,
        display: 'inline-block',
      };
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
        let data;
        if (nVal) {
          data = this.$store.state.mobildConfig.defaultArray[nVal];
        } else {
          data = this.$store.state.mobildConfig.bottomMenu;
        }
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler(nVal, oVal) {
        if (this.num) {
          let data = this.$store.state.mobildConfig.defaultArray[this.num];
          this.setConfig(data);
        }
      },
      deep: true,
    },
    bottomMenu: {
      handler(nVal, oVal) {
        if (!this.num) {
          this.setConfig(nVal);
        }
      },
      deep: true,
    },
    colorStyle: {
      handler(nVal, oVal) {
        this.themeColor = `linear-gradient(90deg,${nVal.theme} 0%,${nVal.gradient} 100%)`;
      },
      deep: true,
    },
  },
  data() {
    return {
      defaultConfig: {
        cname: 'Menu dưới cùng',
        name: 'bottomMenu',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        entryConfig: {
          title: 'Nội dung lối vào',
          tabVal: 0,
          tabList: [{ name: 'Mặc định' }, { name: 'Tùy chỉnh' }],
        },
        styleTitle: 'Cài đặt kiểu',

        iconColor: {
          title: 'Màu biểu tượng',
          default: [{ item: '#333' }],
          color: [{ item: '#333' }],
        },
        iconSize: {
          title: 'Kích thước biểu tượng',
          val: 20,
          min: 10,
          max: 50,
        },
        iconRotate: {
          title: 'Góc xoay',
          val: 0,
          min: 0,
          max: 360,
        },
        padding: {
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 50,
        },
        contentConfigTitle: 'Cài đặt nội dung',
        showContent: {
          title: 'Nội dung hiển thị',
          name: 'showContent',
          type: [3, 1, 2], // Default: Service, Collect, Cart
          list: [
            { id: 3, name: 'Trang chủ', icon: 'icon-shouye6' },
            { id: 1, name: 'Yêu thích', icon: 'icon-shoucang4' },
            { id: 2, name: 'Giỏ hàng', icon: 'icon-gouwuche' },
            { id: 0, name: 'CSKH', icon: 'icon-kefu' },
            { id: 4, name: 'Chia sẻ', icon: 'icon-fenxiang4' },
          ],
        },
        cartButton: {
          title: 'Nút giỏ hàng',
          tabVal: 0,
          tabList: [{ name: 'Hiện' }, { name: 'Ẩn' }],
        },

        menuConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 90 * 90px',
          bnt: 'Thêm',
          type: 1,
          listStyle: 0,
          maxList: 100,
          list: [
            {
              img: '',
              type: 0,
              show: true,
              icon: '',
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
        buttonStyleTitle: 'Cài đặt nút',
        toneConfig: {
          title: 'Tông màu nút',
          tabVal: 0, // 0: Follow Theme, 1: Custom
          tabList: [{ name: 'Theo phong cách chủ đề' }, { name: 'Tùy chỉnh' }],
        },
        cartColor: {
          title: 'Nút giỏ hàng',
          default: [{ item: '#FAAD14' }, { item: '#FAAD14' }],
          color: [{ item: '#FAAD14' }, { item: '#FAAD14' }],
        },
        buyColor: {
          title: 'Nút mua',
          default: [{ item: '#E93323' }, { item: '#E93323' }],
          color: [{ item: '#E93323' }, { item: '#E93323' }],
        },
        generalStyleTitle: 'Kiểu chung',
        moduleColor: {
          title: 'Nền thành phần',
          default: [{ item: '#fff' }, { item: '#fff' }],
          color: [{ item: '#fff' }, { item: '#fff' }],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          default: [{ item: '#F5F5F5' }],
          color: [{ item: '#F5F5F5' }],
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
        paddingConfig: {
          title: 'Lề trong',
          isAll: false,
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          isAll: false,
          val: 0,
          min: 0,
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
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        menuPcFillet: {
          title: 'Cài đặt bo góc',
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
      },
      pageData: {},
      showIconsList: [],
      showCartBtn: true,
      toneConfig: 0,
      configObj: null,
      cartBtnColor: '',
      buyBtnColor: '',
      // bgColor: '',
      // bottomBgColor: '',
      mTop: 0,
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      // bgRadius: 0,
      themeColor: '',
      themeColor2: '',
    };
  },
  mounted() {
    this.$nextTick(() => {
      if (this.num) {
        this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      } else {
        this.pageData = this.$store.state.mobildConfig.bottomMenu;
      }
      this.setConfig(this.pageData);
    });
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      // Content
      if (data.entryConfig && data.entryConfig.tabVal === 1) {
        // Custom Mode: Use menuConfig list
        let list = data.menuConfig.list || [];
        this.showIconsList = list
          .filter((item) => item.show)
          .map((item) => ({
            name: item.info[0].value,
            icon: item.icon, // Using icon class if available
            img: item.img, // Or image
            id: 'custom', // Flag for custom
            url: item.url, // Custom URL
          }));
      } else {
        // Default Mode: Use showContent
        let showIds = data.showContent.type;
        let allIcons = data.showContent.list;
        this.showIconsList = showIds.map((id) => allIcons.find((item) => item.id === id)).filter((item) => item);
      }

      this.showCartBtn = data.cartButton.tabVal === 0;

      // Style
      this.toneConfig = data.toneConfig.tabVal;

      let cartC1 = data.cartColor.color[0].item;
      let cartC2 = data.cartColor.color[1].item;
      this.cartBtnColor = `linear-gradient(90deg,${cartC1} 0%,${cartC2} 100%)`;

      let buyC1 = data.buyColor.color[0].item;
      let buyC2 = data.buyColor.color[1].item;
      this.buyBtnColor = `linear-gradient(90deg,${buyC1} 0%,${buyC2} 100%)`;

      if (!data.componentBgConfig) {
        data.componentBgConfig = {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: data.moduleColor.default,
            color: data.moduleColor.color,
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
        };
      }
      if (!data.marginConfig) {
        data.marginConfig = {
          isAll: false,
          val: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
      }
      this.configObj = data;
      this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
      this.themeColor2 = 'linear-gradient(90deg, #FAAD14 0%, #FAAD14 100%)';
    },
  },
};
</script>

<style scoped lang="scss">
.bottom-menu {
  height: 50px;
  padding: 0 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  .menu-img {
    width: 20px;
    height: 20px;
  }
  .icons-box {
    display: flex;
    margin-right: 10px;
    .item {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: #333;

      .iconfont {
        font-size: 20px;
        margin-bottom: 2px;
      }
      .text {
        font-size: 10px;
      }

      &:last-child {
        margin-right: 0;
      }
    }
  }

  .buttons-box {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
    flex: 1;
    .btn {
      width: 100%;
      height: 36px;
      border-radius: 18px;
      color: #fff;
      text-align: center;
      line-height: 36px;
      font-size: 14px;
      // margin-left: 10px;

      &:first-child {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        margin-left: 0;
        margin-right: 0; // Stick together?
        border-radius: 18px;
      }

      &:last-child {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-radius: 18px;
      }

      &.cart-btn {
        // Orange handled by inline style
        background: #faad14;
        margin-right: 10px;
      }

      &.buy-btn {
        // Red handled by inline style
      }

      // &.full-width {
      //   width: 200px; // Or flex-grow
      //   border-radius: 18px;
      // }
    }

    // Adjust button style based on design.
    // The image shows buttons are separated or joined depending on style?
    // "Default Style": Joined. Yellow (Add) + Red (Buy).
    // "Hide Cart Button": One big Red button.
    // "2 icons + 2 buttons": Separated buttons in image 3? No, image 3 shows them separated with rounded corners.
    // Image 1 (Default): Joined.
    // Image 3 (2 icons + 2 buttons): Separated.

    // Wait, looking closely at the image:
    // Top Left (Default): Headset, Star, Cart | [Add to Cart][Buy Now] (Joined, pill shape)
    // Top Right (Hide Cart): Headset, Star, Cart | [Buy Now] (Full Pill)
    // Middle Left (2 icons + 2 buttons): Headset, Star | [Add to Cart] [Buy Now] (Separated pills? No, joined pill with gap? Or just two buttons?)
    // Actually, "2 icons + 2 buttons" looks like [Add to Cart] (Yellow Pill) [Buy Now] (Red Pill) -- Separated!
    // "1 icon + 2 buttons": Headset | [Add to Cart] (Yellow Pill) [Buy Now] (Red Pill) -- Separated.

    // BUT "Default Style" shows them JOINED.

    // Why the difference? Maybe it depends on space?
    // Or maybe the user didn't change style, but just removed icons, and the buttons automatically expanded/separated?
    // The image title "2 icons + 2 buttons" shows them separated.
    // The "Default" shows them joined.

    // I will implement "Joined" for default, and if there's enough space, maybe they separate?
    // Or I should just make them separate but margin 0 if joined?
    // The image shows "Add to Cart" is Left half pill, "Buy Now" is Right half pill in Default.
    // In "2 icons + 2 buttons", they are two full pills.

    // I don't see a config for "Button Style" (Joined vs Separated).
    // It might be responsive? Or based on count?
    // Let's assume they are joined by default for the standard e-commerce look.
    // But if I look at the "2 icons + 2 buttons" image, they are clearly separated.
    // Maybe I should just make them separated with small margin for now, or use border-radius manipulation.

    // Actually, looking at the "Default Style" image again:
    // [Add to Cart][Buy Now] -> Left one has left radius, right one has right radius.

    // In "2 icons + 2 buttons":
    // [Add to Cart] -> Full radius.
    // [Buy Now] -> Full radius.

    // This implies if there is space, they might separate?
    // Or maybe it's just a different style choice I missed?
    // I don't see a style choice for "Button Layout".

    // I'll stick to the "Default Style" (Joined) as the base implementation because it's the most complex to get right (radius manipulation).
    // If the user wants them separated, I might need more info or add a config.
    // Wait, looking at "2 icons + 1 button" (Red button), it's a full pill.
    // "1 icon + 1 button" (Red button), full pill.

    // I will implement the "Joined" style for 2 buttons, and "Full Pill" for 1 button.
    // If the user really wants the separated look in the screenshot, I might be missing something.
    // But joined is standard.
  }
}
</style>
