<template>
  <div class="custom-box" v-if="visibleComponents && visibleComponents.length">
    <common_wrapper :config="configObj">
      <!-- Unified List Mode -->
      <div v-if="['article', 'coupon', 'goods'].includes(selectTypeValue) && listData.length > 0">
        <div class="custom-box-list" :style="getContainerStyle(currentDisplayMode, currentColumnStyle)">
          <div
            v-for="(dataItem, dataIndex) in listData"
            :key="dataIndex"
            :style="getWrapperStyle(currentDisplayMode, currentColumnStyle)"
          >
            <div
              class="home_custom_component"
              :style="{
                background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
                ...getDataWrapperStyle(),
              }"
            >
              <div :style="getItemStyle(currentDisplayMode, currentColumnStyle)">
                <div v-for="item in visibleComponents" :key="item.id" :style="getComponentStyle(item.style)">
                  <!-- Picture -->
                  <img
                    v-if="item.component === 'Picture'"
                    :src="getPictureUrl(item, dataItem)"
                    :style="getPictureStyle(item.propValue)"
                  />

                  <!-- Text -->
                  <div v-else-if="item.component === 'Text'" :style="getTextBg(item.propValue)">
                    <div :style="getTextStyle(item.propValue)">
                      {{ getDisplayText(item, dataItem) }}
                    </div>
                  </div>

                  <!-- Icon -->
                  <div v-else-if="item.component === 'Icon'" :style="getIconStyle(item.propValue)">
                    <span
                      class="mb-iconfont"
                      :class="item.propValue.class"
                      :style="{ fontSize: item.propValue.size * contentScale + 'px', color: item.propValue.color }"
                    ></span>
                  </div>

                  <!-- Line -->
                  <div
                    v-else-if="item.component === 'Line'"
                    :style="{
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      width: item.propValue.width ? item.propValue.width * contentScale + 'px' : '100%',
                      height: item.propValue.height ? item.propValue.height * contentScale + 'px' : '100%',
                    }"
                  >
                    <div :style="getLineStyle(item.propValue)"></div>
                  </div>

                  <!-- Panel -->
                  <div v-else-if="item.component === 'Panel'" :style="getPanelStyle(item.propValue)"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Default Mode (Single/Design Preview) -->
      <div v-else style="width: 100%">
        <div
          class="home_custom_component"
          :style="{
            background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
            ...getDataWrapperStyle(),
          }"
        >
          <div :style="getItemStyle()">
            <div v-for="item in visibleComponents" :key="item.id" :style="getComponentStyle(item.style)">
              <!-- Picture -->
              <img
                v-if="item.component === 'Picture'"
                :src="item.propValue.url || require('@/assets/images/shan.png')"
                :style="getPictureStyle(item.propValue)"
              />

              <!-- Text -->
              <!-- Text -->
              <div v-if="item.component === 'Text'" :style="getTextBg(item.propValue)">
                <div :style="getTextStyle(item.propValue)">
                  {{ getDisplayText(item) }}
                </div>
              </div>

              <!-- Icon -->
              <div v-else-if="item.component === 'Icon'" :style="getIconStyle(item.propValue)">
                <span
                  class="mb-iconfont"
                  :class="item.propValue.class"
                  :style="{ fontSize: item.propValue.size * contentScale + 'px', color: item.propValue.color }"
                ></span>
              </div>

              <!-- Line -->
              <div
                v-else-if="item.component === 'Line'"
                style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%"
              >
                <div :style="getLineStyle(item.propValue)"></div>
              </div>

              <!-- Panel -->
              <div v-else-if="item.component === 'Panel'" :style="getPanelStyle(item.propValue)"></div>
            </div>
          </div>
        </div>
      </div>
    </common_wrapper>
  </div>
  <div class="custom-box custom-component-box" v-else-if="configObj">
    <img class="shan" src="@/assets/images/shan.png" />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { getArticleList, getCouponList, getThemeProduct } from '@/api/diy';

export default {
  name: 'home_custom_component',
  cname: 'Siêu thành phần',
  configName: 'c_custom_component',
  icon: '#iconzujian-zidingyi',
  type: 0,
  defaultName: 'customComponent',
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
    selectTypeValue() {
      return this.configObj.selectType ? this.configObj.selectType.activeValue : 'user';
    },
    currentDisplayMode() {
      if (this.selectTypeValue === 'article') return this.configObj.articleDisplayMode;
      if (this.selectTypeValue === 'coupon') return this.configObj.couponDisplayMode;
      if (this.selectTypeValue === 'goods') return this.configObj.goodsDisplayMode;
      return null;
    },
    currentColumnStyle() {
      if (this.selectTypeValue === 'article') return this.configObj.articleColumnStyle;
      if (this.selectTypeValue === 'coupon') return this.configObj.couponColumnStyle;
      if (this.selectTypeValue === 'goods') return this.configObj.goodsColumnStyle;
      return null;
    },
    // List of components (Picture, Text, etc.)
    customComponents() {
      return this.configObj.customComponents;
    },
    visibleComponents() {
      if (!this.customComponents || !this.customComponents.list) return [];
      return this.customComponents.list.filter((item) => !item.isHidden);
    },
    // Background Config (Left Color)
    bgColorLeft() {
      return this.configObj.componentBgDataConfig &&
        this.configObj.componentBgDataConfig.colorConfig &&
        this.configObj.componentBgDataConfig.colorConfig.color &&
        this.configObj.componentBgDataConfig.colorConfig.color[0]
        ? this.configObj.componentBgDataConfig.colorConfig.color[0].item
        : 'transparent';
    },
    articleDisplayMode() {
      return this.configObj.articleDisplayMode;
    },
    articleColumnStyle() {
      return this.configObj.articleColumnStyle;
    },
    articleDataSource() {
      return this.configObj.articleDataSource;
    },
    articleList() {
      return this.configObj.articleList;
    },
    articleClass() {
      return this.configObj.articleClass;
    },
    articleSort() {
      return this.configObj.articleSort;
    },
    articleSortRule() {
      return this.configObj.articleSortRule;
    },
    couponDisplayMode() {
      return this.configObj.couponDisplayMode;
    },
    couponColumnStyle() {
      return this.configObj.couponColumnStyle;
    },
    couponDataSource() {
      return this.configObj.couponDataSource;
    },
    couponList() {
      return this.configObj.couponList;
    },
    couponType() {
      return this.configObj.couponType;
    },
    couponSendType() {
      return this.configObj.couponSendType;
    },
    couponUserType() {
      return this.configObj.couponUserType;
    },
    couponThreshold() {
      return this.configObj.couponThreshold;
    },
    couponThresholdValue() {
      return this.configObj.couponThresholdValue;
    },
    couponTime() {
      return this.configObj.couponTime;
    },
    couponSort() {
      return this.configObj.couponSort;
    },
    couponSortRule() {
      return this.configObj.couponSortRule;
    },
    goodsDisplayMode() {
      return this.configObj.goodsDisplayMode;
    },
    goodsColumnStyle() {
      return this.configObj.goodsColumnStyle;
    },
    goodsSortRule() {
      return this.configObj.goodsSortRule;
    },
    goodsDataSource() {
      return this.configObj.goodsDataSource;
    },
    goodsList() {
      return this.configObj.goodsList;
    },
    goodsClass() {
      return this.configObj.goodsClass;
    },
    contentScale() {
      const { marginConfig, paddingConfig, borderConfig, paddingDataConfig, borderDataConfig, marginDataConfig } =
        this.configObj;
      let width = 375;

      // Wrapper Margin
      if (marginConfig) {
        if (!marginConfig.isAll) {
          width -= (marginConfig.val || 0) * 2;
        } else {
          width -= (marginConfig.valList[1].val || 0) + (marginConfig.valList[3].val || 0);
        }
      }

      // Wrapper Padding
      if (paddingConfig) {
        if (!paddingConfig.isAll) {
          width -= (paddingConfig.val || 0) * 2;
        } else {
          width -= (paddingConfig.valList[1].val || 0) + (paddingConfig.valList[3].val || 0);
        }
      }

      // Wrapper Border
      if (borderConfig && borderConfig.tabVal) {
        const borderWidth = (borderConfig.widthConfig.val || 0) * 2;
        width -= borderWidth;
      }

      // Data Margin
      if (marginDataConfig) {
        if (!marginDataConfig.isAll) {
          width -= (marginDataConfig.val || 0) * 2;
        } else {
          width -= (marginDataConfig.valList[1].val || 0) + (marginDataConfig.valList[3].val || 0);
        }
      }

      // Data Padding
      if (paddingDataConfig) {
        if (!paddingDataConfig.isAll) {
          width -= (paddingDataConfig.val || 0) * 2;
        } else {
          width -= (paddingDataConfig.valList[1].val || 0) + (paddingDataConfig.valList[3].val || 0);
        }
      }

      // Data Border
      if (borderDataConfig && borderDataConfig.tabVal) {
        const borderWidth = (borderDataConfig.widthConfig.val || 0) * 2;
        width -= borderWidth;
      }

      return width / 375;
    },
  },
  watch: {
    num: {
      handler(nVal, oVal) {
        this.lastArticleParams = null;
        this.lastCouponParams = null;
        this.lastGoodsParams = null;
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
      defaultConfig: {
        cname: 'Siêu thành phần',
        name: 'customComponent',
        timestamp: this.num,
        messageTitle: 'Cài đặt thông tin',
        dataTitle: 'Cài đặt dữ liệu',
        designTitle: 'Thiết kế thành phần',
        commonTitle: 'Kiểu chung',
        dataStyleTitle: 'Kiểu trình bày dữ liệu',
        setUp: {
          tabVal: 0,
        },
        selectType: {
          title: 'Chọn thông tin',
          activeValue: 'user',
          list: [
            { activeValue: 'user', title: 'Người dùng' },
            { activeValue: 'article', title: 'Bài viết' },
            { activeValue: 'coupon', title: 'Phiếu giảm giá' },
            { activeValue: 'goods', title: 'Sản phẩm' },
          ],
        },

        // Article Config
        articleDisplayMode: {
          title: 'Cách hiển thị',
          tabVal: 0,
          tabList: [{ name: 'Xếp dọc' }, { name: 'Vuốt ngang' }],
        },
        articleColumnStyle: {
          title: 'Cách sắp xếp',
          tabVal: 0,
          tabList: [{ name: '1 cột' }, { name: '2 cột' }, { name: '3 cột' }, { name: '4 cột' }],
        },
        articleDataSource: {
          title: 'Chọn dữ liệu',
          tabVal: 0,
          tabList: [{ name: 'Dữ liệu chỉ định' }, { name: 'Dữ liệu theo bộ lọc' }],
        },
        articleList: {
          list: [],
        },
        articleClass: {
          title: 'Danh mục bài viết',
          activeValue: '',
          list: [],
        },
        articleNum: {
          title: 'Số lượng hiển thị',
          val: 1,
          min: 1,
        },
        articleSort: {
          title: 'Loại sắp xếp',
          tabVal: 0,
          tabList: [{ name: 'Lượt xem' }, { name: 'Thời gian đăng' }],
        },
        articleSortRule: {
          title: 'Quy tắc sắp xếp',
          tabVal: 0,
          tabList: [{ name: 'Tăng dần' }, { name: 'Giảm dần' }],
        },

        // Coupon Config
        couponDisplayMode: {
          title: 'Cách hiển thị',
          tabVal: 0,
          tabList: [{ name: 'Xếp dọc' }, { name: 'Vuốt ngang' }],
        },
        couponColumnStyle: {
          title: 'Cách sắp xếp',
          tabVal: 0,
          tabList: [{ name: '1 cột' }, { name: '2 cột' }, { name: '3 cột' }, { name: '4 cột' }],
        },
        couponDataSource: {
          title: 'Chọn dữ liệu',
          tabVal: 0,
          tabList: [{ name: 'Dữ liệu chỉ định' }, { name: 'Dữ liệu theo bộ lọc' }],
        },
        couponList: {
          list: [],
        },
        couponType: {
          title: 'Loại phiếu giảm giá',
          activeValue: '',
          list: [
            { activeValue: '', title: 'Tất cả' },
            { activeValue: '0', title: 'Phiếu toàn cửa hàng' },
            { activeValue: '1', title: 'Phiếu theo danh mục' },
            { activeValue: '2', title: 'Phiếu theo sản phẩm' },
          ],
        },
        couponUserType: {
          title: 'Loại người dùng',
          activeValue: '',
          list: [
            { activeValue: '', title: 'Tất cả' },
            { activeValue: '1', title: 'Người dùng thường' },
            { activeValue: '2', title: 'Người dùng thành viên' },
          ],
        },
        couponSendType: {
          title: 'Hình thức phát hành',
          activeValue: '',
          list: [
            { activeValue: '', title: 'Tất cả' },
            { activeValue: '1', title: 'Tự nhận' },
            { activeValue: '3', title: 'Phiếu tặng kèm' },
          ],
        },

        couponThreshold: {
          title: 'Điều kiện sử dụng',
          tabVal: 0,
          tabList: [{ name: 'Không điều kiện' }, { name: 'Có điều kiện' }],
        },
        couponThresholdValue: {
          title: 'Giá trị đơn tối thiểu',
          val: 0,
          min: 0,
          max: 10000,
        },
        couponTime: {
          title: 'Thời gian nhận',
          val: [],
        },
        couponSort: {
          title: 'Loại sắp xếp',
          tabVal: 0,
          tabList: [{ name: 'Mệnh giá' }, { name: 'Thời gian đăng' }],
        },
        couponSortRule: {
          title: 'Quy tắc sắp xếp',
          tabVal: 0,
          tabList: [{ name: 'Tăng dần' }, { name: 'Giảm dần' }],
        },
        couponNum: {
          title: 'Số lượng hiển thị',
          val: 1,
          min: 1,
        },

        // Goods Config
        goodsDisplayMode: {
          title: 'Cách hiển thị',
          tabVal: 0,
          tabList: [{ name: 'Xếp dọc' }, { name: 'Vuốt ngang' }],
        },
        goodsColumnStyle: {
          title: 'Cách sắp xếp',
          tabVal: 0,
          tabList: [{ name: '1 cột' }, { name: '2 cột' }, { name: '3 cột' }, { name: '4 cột' }],
        },
        goodsDataSource: {
          title: 'Chọn dữ liệu',
          tabVal: 0,
          tabList: [{ name: 'Dữ liệu chỉ định' }, { name: 'Danh mục chỉ định' }],
        },
        goodsList: {
          title: 'Danh sách sản phẩm',
          max: 20,
          list: [],
        },
        goodsClass: {
          title: 'Danh mục sản phẩm',
          activeValue: '',
          list: [],
        },
        goodsNum: {
          title: 'Số lượng hiển thị',
          val: 6,
          min: 1,
        },
        goodsSort: {
          title: 'Sắp xếp sản phẩm',
          tabVal: 0,
          tabList: [{ name: 'Lượt bán' }, { name: 'Giá' }],
        },
        goodsSortRule: {
          title: 'Quy tắc sắp xếp',
          tabVal: 0,
          tabList: [{ name: 'Giảm dần' }, { name: 'Tăng dần' }],
        },

        // Common Styles
        paddingConfig: {
          isAll: false,
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 500,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          isAll: false,
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
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
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
      },
      bottomBgColor: '',
      configObj: {},
      bgColorLeft: '',
      bgColorRight: '',
      bgRadius: 0,
      listData: [],
      lastArticleParams: null,
      lastCouponParams: null,
      lastGoodsParams: null,
      lastSelectType: '',
    };
  },
  mounted() {
    this.$nextTick(() => {
      let data = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(data);
    });
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      this.configObj = data;
      // for (let key in this.defaultConfig) {
      //   if (this.configObj[key] === undefined) {
      //     this.$set(this.configObj, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
      //   }
      // }
      this.bottomBgColor = this.configObj.bottomBgColor.color[0].item;
      this.bgColorLeft = data.moduleColor.color[0].item;
      this.bgColorRight = data.moduleColor.color[1].item;
      if (this.selectTypeValue === 'article') {
        this.fetchArticleList();
      } else if (this.selectTypeValue === 'coupon') {
        this.fetchCouponList();
      } else if (this.selectTypeValue === 'goods') {
        this.fetchGoodsList();
      }
    },
    fetchCouponList() {
      if (!this.configObj.couponDataSource) return;
      let params = {
        limit: this.configObj.couponNum.val,
        order: this.configObj.couponSort.tabVal,
        sort: this.configObj.couponSortRule.tabVal,
      };
      if (this.configObj.couponDataSource.tabVal === 0) {
        // Specific Data
        if (!this.configObj.couponList || !this.configObj.couponList.list) return;
        params.ids = this.configObj.couponList.list.map((item) => item.id).join(',');
        if (params.ids.length === 0) {
          this.listData = [];
          return;
        }
      } else {
        // Filter Data
        params.type = this.configObj.couponType.activeValue;
        params.user_type = this.configObj.couponUserType.activeValue;
        if (this.configObj.couponUserType.activeValue != 2) {
          params.send_type = this.configObj.couponSendType.activeValue;
        }
        params.is_min_price = this.configObj.couponThreshold.tabVal;
        if (params.is_min_price == 1) {
          params.min_price = this.configObj.couponThresholdValue.val;
        }
        if (this.configObj.couponTime.val && this.configObj.couponTime.val.length) {
          params.start_time = this.configObj.couponTime.val[0];
          params.end_time = this.configObj.couponTime.val[1];
        }
      }
      const paramsStr = JSON.stringify(params);
      if (this.lastCouponParams === paramsStr) return;
      this.lastCouponParams = paramsStr;
      getCouponList(params)
        .then((res) => {
          this.listData = res.data;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },

    fetchArticleList() {
      if (!this.configObj.articleDataSource) return;
      let params = {
        limit: this.configObj.articleNum.val,
        order: this.configObj.articleSort.tabVal,
        sort: this.configObj.articleSortRule.tabVal,
      };
      if (this.configObj.articleDataSource.tabVal === 0) {
        // Specific Data
        if (!this.configObj.articleList || !this.configObj.articleList.list) return;
        params.ids = this.configObj.articleList.list.map((item) => item.id).join(',');
        if (params.ids.length === 0) {
          this.listData = [];
          return;
        }
      } else {
        // Filter Data
        params.cid = Array.isArray(this.configObj.articleClass.activeValue)
          ? this.configObj.articleClass.activeValue.join(',')
          : this.configObj.articleClass.activeValue;
      }
      const paramsStr = JSON.stringify(params);
      if (this.lastArticleParams === paramsStr) return;
      this.lastArticleParams = paramsStr;
      getArticleList(params)
        .then((res) => {
          this.listData = res.data;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    fetchGoodsList() {
      if (!this.configObj.goodsDataSource) return;
      let params = {
        limit: this.configObj.goodsNum.val,
        order: this.configObj.goodsSort.tabVal,
        sort: this.configObj.goodsSortRule.tabVal,
      };
      if (this.configObj.goodsDataSource.tabVal === 0) {
        // Specific Data
        if (!this.configObj.goodsList || !this.configObj.goodsList.list) return;
        params.ids = this.configObj.goodsList.list.map((item) => item.id).join(',');
        if (params.ids.length === 0) {
          this.listData = [];
          return;
        }
      } else {
        // Filter Data
        params.cate_ids = Array.isArray(this.configObj.goodsClass.activeValue)
          ? this.configObj.goodsClass.activeValue.join(',')
          : this.configObj.goodsClass.activeValue;
      }
      const paramsStr = JSON.stringify(params);
      if (this.lastGoodsParams === paramsStr) return;
      this.lastGoodsParams = paramsStr;
      getThemeProduct(params)
        .then((res) => {
          this.listData = res.data;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    getTabName(config) {
      if (!config || !config.tabList) return '';
      return config.tabList[config.tabVal] ? config.tabList[config.tabVal].name : '';
    },
    getSelectTitle(config) {
      if (!config || !config.list) return 'Tất cả';
      const item = config.list.find((item) => item.activeValue == config.activeValue);
      return item ? item.title : 'Tất cả';
    },
    getContainerStyle(displayMode, columnStyle) {
      const style = {
        position: 'relative',
        width: '100%',
      };
      if (displayMode && displayMode.tabVal === 1) {
        // Horizontal Scroll
        style.display = 'flex';
        style.overflowX = 'auto';
        style.overflowY = 'hidden';
        style.flexWrap = 'nowrap';
      } else {
        style.display = 'flex';
        style.flexWrap = 'wrap';
      }
      return style;
    },
    getWrapperStyle(displayMode, columnStyle) {
      const style = {
        width: '100%',
      };
      if (displayMode && displayMode.tabVal === 1) {
        style.flexShrink = 0;
        if (columnStyle) {
          const colCount = columnStyle.tabVal + 1;
          style.width = `${100 / colCount}%`;
        } else {
          style.width = '200px';
        }
      } else {
        if (columnStyle) {
          const colCount = columnStyle.tabVal + 1;
          style.width = `${100 / colCount}%`;
        }
      }
      return style;
    },
    getItemStyle(displayMode, columnStyle) {
      const { marginConfig, paddingConfig, borderConfig, marginDataConfig, paddingDataConfig, borderDataConfig } =
        this.configObj;

      let marginHeight = 0;
      let paddingHeight = 0;
      let borderWidth = 0;

      // Wrapper Margin
      if (marginConfig) {
        marginHeight += marginConfig.isAll
          ? (marginConfig.valList[0].val || 0) + (marginConfig.valList[2].val || 0)
          : (marginConfig.val || 0) * 2;
      }

      // Wrapper Padding
      if (paddingConfig) {
        paddingHeight += paddingConfig.isAll
          ? (paddingConfig.valList[0].val || 0) + (paddingConfig.valList[2].val || 0)
          : (paddingConfig.val || 0) * 2;
      }

      // Wrapper Border
      if (borderConfig && borderConfig.tabVal) {
        borderWidth += borderConfig.widthConfig.val || 0;
      }

      // Data Margin
      if (marginDataConfig) {
        marginHeight += marginDataConfig.isAll
          ? (marginDataConfig.valList[0].val || 0) + (marginDataConfig.valList[2].val || 0)
          : (marginDataConfig.val || 0) * 2;
      }

      // Data Padding
      if (paddingDataConfig) {
        paddingHeight += paddingDataConfig.isAll
          ? (paddingDataConfig.valList[0].val || 0) + (paddingDataConfig.valList[2].val || 0)
          : (paddingDataConfig.val || 0) * 2;
      }

      // Data Border
      if (borderDataConfig && borderDataConfig.tabVal) {
        borderWidth += borderDataConfig.widthConfig.val || 0;
      }

      const canvasHeight =
        (this.customComponents.canvasHeight * this.contentScale || 0) +
        borderWidth * this.contentScale +
        marginHeight + // Margin/Padding should not be scaled if they are in px, but here we scale wrapper, so... wait.
        paddingHeight +
        'px';

      // Correct logic:
      // contentScale scales the inner content (canvasHeight).
      // External margins/paddings are usually fixed px in mobile config.
      // However, if we want the total height to reflect the scaled content + spacing:

      const totalHeight =
        this.customComponents.canvasHeight * this.contentScale + borderWidth + marginHeight + paddingHeight;

      const style = {
        height: this.customComponents.canvasHeight * this.contentScale + 'px', // Set height to content height
        position: 'relative',
        boxSizing: 'border-box',
        width: '100%',
      };
      return style;
    },
    // Add new method for data wrapper styling
    getDataWrapperStyle() {
      const {
        filletDataConfig,
        marginDataConfig,
        paddingDataConfig,
        componentBgDataConfig,
        borderDataConfig,
        shadowDataConfig,
      } = this.configObj;
      const style = {};

      // Border Radius
      if (filletDataConfig) {
        if (filletDataConfig.type) {
          style.borderRadius = `${filletDataConfig.valList[0].val}px ${filletDataConfig.valList[1].val}px ${filletDataConfig.valList[3].val}px ${filletDataConfig.valList[2].val}px`;
        } else {
          style.borderRadius = `${filletDataConfig.val}px`;
        }
      }

      // Margin
      if (marginDataConfig) {
        if (marginDataConfig.isAll) {
          style.marginTop = `${marginDataConfig.valList[0].val}px`;
          style.marginRight = `${marginDataConfig.valList[1].val}px`;
          style.marginBottom = `${marginDataConfig.valList[2].val}px`;
          style.marginLeft = `${marginDataConfig.valList[3].val}px`;
        } else {
          style.margin = `${marginDataConfig.val}px`;
        }
      }

      // Padding
      if (paddingDataConfig) {
        if (paddingDataConfig.isAll) {
          style.paddingTop = `${paddingDataConfig.valList[0].val}px`;
          style.paddingRight = `${paddingDataConfig.valList[1].val}px`;
          style.paddingBottom = `${paddingDataConfig.valList[2].val}px`;
          style.paddingLeft = `${paddingDataConfig.valList[3].val}px`;
        } else {
          style.padding = `${paddingDataConfig.val}px`;
        }
      }

      // Background
      if (componentBgDataConfig) {
        if (componentBgDataConfig.tabVal === 0) {
          // Color
          const colorList = componentBgDataConfig.colorConfig.color;
          if (colorList && colorList.length > 0) {
            style.background = `linear-gradient(${
              componentBgDataConfig.colorDirection.tabVal === 0
                ? '90deg'
                : componentBgDataConfig.colorDirection.tabVal === 1
                ? '180deg'
                : componentBgDataConfig.colorDirection.tabVal === 2
                ? '135deg'
                : '45deg'
            }, ${colorList[0].item} 0%, ${colorList[1] ? colorList[1].item : colorList[0].item} 100%)`;
          }
        } else {
          // Image
          if (componentBgDataConfig.imageConfig.url) {
            style.backgroundImage = `url(${componentBgDataConfig.imageConfig.url})`;
            style.backgroundSize = '100% 100%';
            style.backgroundRepeat = 'no-repeat';
          }
        }
      }

      // Border
      if (borderDataConfig && borderDataConfig.tabVal) {
        style.borderStyle =
          borderDataConfig.styleConfig.tabVal === 0
            ? 'solid'
            : borderDataConfig.styleConfig.tabVal === 1
            ? 'dashed'
            : 'dotted';
        style.borderWidth = `${borderDataConfig.widthConfig.val}px`;
        style.borderColor = borderDataConfig.colorConfig.color[0].item;
      }

      // Shadow
      if (shadowDataConfig && shadowDataConfig.tabVal) {
        style.boxShadow = `${shadowDataConfig.xConfig.val}px ${shadowDataConfig.yConfig.val}px ${shadowDataConfig.blurConfig.val}px ${shadowDataConfig.spreadConfig.val}px ${shadowDataConfig.colorConfig.color[0].item}`;
      }

      return style;
    },
    getComponentStyle(style) {
      if (!style) return {};
      const scale = this.contentScale;
      return {
        position: 'absolute',
        top: style.top * scale + 'px',
        left: style.left * scale + 'px',
        width: style.width * scale + 'px',
        maxWidth: '100%',
        height: style.height * scale + 'px',
        zIndex: style.zIndex,
        transform: `rotate(${style.rotate || 0}deg)`,
      };
    },
    getPictureStyle(propValue) {
      const scale = this.contentScale;
      const style = {
        width: '100%',
        height: '100%',
        objectFit: 'cover',
        pointerEvents: 'none',
        boxSizing: 'border-box',
        borderWidth: (propValue.showBorder ? propValue.borderWidth * scale : 0) + 'px',
        borderColor: propValue.borderColor || 'transparent',
        borderStyle: propValue.borderStyle || 'solid',
      };

      // Border Radius
      if (propValue.isRadiusAll) {
        style.borderRadius = (propValue.borderRadius || 0) * scale + 'px';
      } else {
        style.borderRadius = `${(propValue.borderRadiusTopLeft || 0) * scale}px ${
          (propValue.borderRadiusTopRight || 0) * scale
        }px ${(propValue.borderRadiusBottomRight || 0) * scale}px ${(propValue.borderRadiusBottomLeft || 0) * scale}px`;
      }

      // Box Shadow
      if (propValue.showShadow) {
        style.boxShadow = `${(propValue.shadowX || 0) * scale}px ${(propValue.shadowY || 0) * scale}px ${
          (propValue.shadowBlur || 0) * scale
        }px ${(propValue.shadowSpread || 0) * scale}px ${propValue.shadowColor || 'rgba(0,0,0,0.5)'}`;
      }
      return style;
    },
    getIconStyle(propValue) {
      const scale = this.contentScale;
      const style = {
        display: 'flex',
        justifyContent: propValue.iconAlign || 'center',
        alignItems: 'center',
        width: '100%',
        height: '100%',
        boxSizing: 'border-box',
        borderWidth: (propValue.showBorder ? propValue.borderWidth * scale : 0) + 'px',
        borderColor: propValue.borderColor || 'transparent',
        borderStyle: propValue.borderStyle || 'solid',
        padding: `${(propValue.paddingTop || 0) * scale}px ${(propValue.paddingRight || 0) * scale}px ${
          (propValue.paddingBottom || 0) * scale
        }px ${(propValue.paddingLeft || 0) * scale}px`,
      };

      // Background
      if (propValue.bgColor2) {
        const directionMap = {
          horizontal: '90deg',
          vertical: '180deg',
          'left-diagonal': '135deg',
          'right-diagonal': '45deg',
        };
        const deg = directionMap[propValue.bgDirection] || '180deg';
        style.background = `linear-gradient(${deg}, ${propValue.backgroundColor || 'transparent'}, ${
          propValue.bgColor2
        })`;
      } else {
        style.backgroundColor = propValue.backgroundColor || 'transparent';
      }
      if (propValue.showBorder && propValue.borderWidth) {
        style.backgroundSize = `100% calc(100% + ${propValue.borderWidth * scale * 2 || 0}px`;
        style.backgroundPosition = `0px -${propValue.borderWidth * scale || 0}px`;
      }
      // Border Radius
      if (propValue.isRadiusAll) {
        style.borderRadius = (propValue.borderRadius || 0) * scale + 'px';
      } else {
        style.borderRadius = `${(propValue.borderRadiusTopLeft || 0) * scale}px ${
          (propValue.borderRadiusTopRight || 0) * scale
        }px ${(propValue.borderRadiusBottomRight || 0) * scale}px ${(propValue.borderRadiusBottomLeft || 0) * scale}px`;
      }

      return style;
    },
    getTextStyle(propValue) {
      const scale = this.contentScale;
      const style = {
        width: '100%',
        fontSize: propValue.fontSize * scale + 'px',
        color: propValue.color,
        lineHeight: propValue.lineHeight,
        letterSpacing: (propValue.letterSpacing || 0) * scale + 'px',
        fontWeight: propValue.fontWeight || 'normal',
        fontStyle: propValue.fontStyle || 'normal',
        textDecoration: propValue.textDecoration || 'none',
        textAlign: propValue.textAlign || 'left',

        boxSizing: 'border-box',
        wordBreak: 'break-all',
      };

      // Ellipsis
      if (propValue.ellipsis > 0) {
        style.display = '-webkit-box';
        style.WebkitBoxOrient = 'vertical';
        style.WebkitLineClamp = propValue.ellipsis;
        style.overflow = 'hidden';
        style.whiteSpace = 'normal';
      } else {
        style.whiteSpace = 'normal';
      }

      return style;
    },
    getTextBg(propValue) {
      const scale = this.contentScale;
      const style = {
        height: '100%',
        padding: `${(propValue.paddingTop || 0) * scale}px ${(propValue.paddingRight || 0) * scale}px ${
          (propValue.paddingBottom || 0) * scale
        }px ${(propValue.paddingLeft || 0) * scale}px`,
        borderWidth: (propValue.showBorder ? propValue.borderWidth * scale : 0) + 'px',
        borderColor: propValue.borderColor || 'transparent',
        borderStyle: propValue.borderStyle || 'solid',
      };
      // Background
      if (propValue.bgColor2) {
        const directionMap = {
          horizontal: '90deg',
          vertical: '180deg',
          'left-diagonal': '135deg',
          'right-diagonal': '45deg',
        };
        const deg = directionMap[propValue.bgDirection] || '180deg';
        style.backgroundImage = `linear-gradient(${deg}, ${propValue.backgroundColor || 'transparent'}, ${
          propValue.bgColor2
        })`;
      } else {
        style.backgroundColor = propValue.backgroundColor || 'transparent';
      }
      if (propValue.showBorder && propValue.borderWidth) {
        style.backgroundSize = `100% calc(100% + ${propValue.borderWidth * scale * 2 || 0}px`;
        style.backgroundPosition = `0px -${propValue.borderWidth * scale || 0}px`;
      }
      // Border Radius
      if (propValue.isRadiusAll) {
        style.borderRadius = (propValue.borderRadius || 0) + 'px';
      } else {
        style.borderRadius = `${propValue.borderRadiusTopLeft || 0}px ${propValue.borderRadiusTopRight || 0}px ${
          propValue.borderRadiusBottomRight || 0
        }px ${propValue.borderRadiusBottomLeft || 0}px`;
      }
      return style;
    },
    getPanelStyle(propValue) {
      const scale = this.contentScale;
      const style = {
        width: '100%',
        height: '100%',
        boxSizing: 'border-box',
        borderWidth: (propValue.showBorder ? propValue.borderWidth * scale : 0) + 'px',
        borderColor: propValue.borderColor || 'transparent',
        borderStyle: propValue.borderStyle || 'solid',
      };

      // Background
      if (propValue.bgColor2) {
        const directionMap = {
          horizontal: '90deg',
          vertical: '180deg',
          'left-diagonal': '135deg',
          'right-diagonal': '45deg',
        };
        const deg = directionMap[propValue.bgDirection] || '180deg';
        style.backgroundImage = `linear-gradient(${deg}, ${propValue.backgroundColor || 'transparent'}, ${
          propValue.bgColor2
        })`;
      } else {
        style.backgroundColor = propValue.backgroundColor || 'transparent';
      }
      if (propValue.showBorder && propValue.borderWidth) {
        style.backgroundSize = `100% calc(100% + ${propValue.borderWidth * scale * 2 || 0}px`;
        style.backgroundPosition = `0px -${propValue.borderWidth * scale || 0}px`;
      }
      // Border Radius
      if (propValue.isRadiusAll) {
        style.borderRadius = (propValue.borderRadius || 0) * scale + 'px';
      } else {
        style.borderRadius = `${(propValue.borderRadiusTopLeft || 0) * scale}px ${
          (propValue.borderRadiusTopRight || 0) * scale
        }px ${(propValue.borderRadiusBottomRight || 0) * scale}px ${(propValue.borderRadiusBottomLeft || 0) * scale}px`;
      }
      // Box Shadow
      if (propValue.showShadow) {
        style.boxShadow = `${(propValue.shadowX || 0) * scale}px ${(propValue.shadowY || 0) * scale}px ${
          (propValue.shadowBlur || 0) * scale
        }px ${(propValue.shadowSpread || 0) * scale}px ${propValue.shadowColor || '#000000'}`;
      }

      return style;
    },
    getLineStyle(propValue) {
      const scale = this.contentScale;
      const height = (propValue.height || 0) * scale;
      return {
        width: propValue.direction === 'vertical' ? '0px' : '100%',
        height: propValue.direction === 'vertical' ? '100%' : '0px',
        borderTop:
          propValue.direction === 'vertical' ? 'none' : height + 'px ' + propValue.style + ' ' + propValue.color,
        borderLeft:
          propValue.direction === 'vertical' ? height + 'px ' + propValue.style + ' ' + propValue.color : 'none',
      };
    },
    getDisplayText(item, dataItem) {
      if (dataItem) {
        const field = item.propValue.fieldType;
        if (this.selectTypeValue === 'article') {
          // Article mapping
          if (field === 'title') return dataItem.title;
          if (field === 'visit') return dataItem.visit;
          if (field === 'add_time') return dataItem.add_time;
          if (field === 'synopsis') return dataItem.synopsis;
        } else if (this.selectTypeValue === 'coupon') {
          // Coupon mapping
          const field = item.propValue.fieldType;
          if (field === 'coupon_title') return dataItem.coupon_title || dataItem.title;
          if (field === 'coupon_price') return dataItem.coupon_price;
          if (field === 'use_min_price') return dataItem.use_min_price;
          if (field === 'coupon_time') return dataItem.coupon_time;
          if (field === 'type') return dataItem.type === 1 ? 'Phiếu theo danh mục' : dataItem.type === 2 ? 'Phiếu theo sản phẩm' : 'Phiếu toàn cửa hàng';
          if (field === 'status') return dataItem.status === 1 ? 'Bật' : 'Tắt';
          if (field === 'receive_time') return dataItem.receive_time;
          if (field === 'use_time') return dataItem.use_time;
          if (field === 'receive_count') return dataItem.receive_count;
          if (field === 'add_time') return dataItem.add_time;
        } else if (this.selectTypeValue === 'goods') {
          // Goods mapping
          if (field === 'store_name') return dataItem.store_name;
          if (field === 'id') return dataItem.id;
          if (field === 'image') return dataItem.image;
          if (field === 'store_info') return dataItem.store_info;
          if (field === 'unit_name') return dataItem.unit_name;
          if (field === 'cate_name') return dataItem.cate_name;
          if (field === 'label_name') return dataItem.label_name;
          if (field === 'stock') return dataItem.stock;
          if (field === 'price') return dataItem.price;
          if (field === 'max_price') return dataItem.max_price;
          if (field === 'min_price') return dataItem.min_price;
          if (field === 'ot_price') return dataItem.ot_price;
          if (field === 'max_ot_price') return dataItem.max_ot_price;
          if (field === 'min_ot_price') return dataItem.min_ot_price;
          if (field === 'min_qty') return dataItem.min_qty;
          if (field === 'sales') return dataItem.sales;
          if (field === 'browse') return dataItem.browse;
          if (field === 'add_time') return dataItem.add_time;
        }
      }
      if (this.selectTypeValue === 'user' && item.propValue.typeLabel) {
        return item.propValue.typeLabel;
      }
      return item.propValue.text || item.propValue.typeLabel;
    },
    getPictureUrl(item, dataItem) {
      const field = item.propValue.fieldType;
      if (field && !item.propValue.url && dataItem.image) {
        return dataItem.image;
      }
      return item.propValue.url || require('@/assets/images/shan.png');
    },
  },
};
</script>

<style scoped lang="scss">
.home_custom_component {
  .custom-box {
    text-align: center;

    .title {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .desc {
      font-size: 12px;
      color: #666;
    }

    .list-wrapper {
      padding: 10px;

      .list-item {
        display: flex;
        margin-bottom: 10px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 5px;

        .img-box {
          width: 70px;
          height: 70px;
          margin-right: 10px;
          flex-shrink: 0;

          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
          }
        }

        .info {
          flex: 1;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          text-align: left;
          overflow: hidden;

          .name {
            font-size: 14px;
            color: #333;
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
          }

          .time,
          .view {
            font-size: 12px;
            color: #999;
          }
        }
      }
    }
  }
}
.custom-component-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 380px;
  background: #f3f9ff;
  .title {
    font-weight: bold;
  }
  img {
    width: 65px;
    height: 50px;
  }
}
// Ẩn thanh cuộn
.custom-box-list {
  overflow: auto;
  &::-webkit-scrollbar {
    display: none;
  }
}
</style>
