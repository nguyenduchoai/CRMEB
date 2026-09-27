<template>
  <common_wrapper :config="configObj">
    <div class="member-card" :class="'style' + (styleConfig + 1)">
      <!-- Kiểu thông tin, tổng cộng 5 kiểu -->
      <template v-if="styleConfig == 0">
        <div
          class="card-header acea-row row-between-wrapper"
          :class="{
            'style-0': styleConfig == 0,
          }"
        >
          <div class="user-info acea-row row-middle">
            <div class="avatar">
              <img :src="logoConfig" v-if="logoConfig" />
              <div class="empty-box" v-else>
                <img src="@/assets/images/shan.png" />
              </div>
            </div>
            <div class="text">
              <div class="name" :style="{ color: nameColor, fontSize: nameSize + 'px' }">Đây là biệt danh</div>
              <!-- Có thể chuyển đổi hiển thị số điện thoại hoặc ID -->
              <div v-if="userInfoConfig == 0" class="level" :style="{ color: numColor, fontSize: numSize + 'px' }">
                14512349876
              </div>
              <div v-else class="level" :style="{ color: numColor, fontSize: numSize + 'px' }">ID: 9527</div>
            </div>
          </div>
          <!-- Lối vào menu -->
          <div
            class="menu-entry"
            v-if="menuList.length"
            style="position: absolute; right: 10px; top: 14px; z-index: 10"
          >
            <div class="item" v-for="(item, index) in menuList" :key="index" :style="{ marginLeft: '10px' }">
              <template v-if="menuStyle == 0">
                <img :src="item.img" v-if="item.img" class="menu-img" />
              </template>
              <template v-else>
                <span
                  class="mb-iconfont"
                  :class="item.icon"
                  :style="{
                    display: 'inline-block',
                    color: iconStyle.color.color[0].item,
                    fontSize: iconStyle.size.val + 'px',
                    padding: iconStyle.padding.val + 'px',
                    transform: 'rotate(' + iconStyle.rotate.val + 'deg)',
                  }"
                  v-if="item.icon"
                ></span>
              </template>
            </div>
          </div>
        </div>
      </template>
      <!-- Style 2: Simple Card (No Data) -->
      <template v-if="styleConfig == 1">
        <div class="card-header acea-row row-between-wrapper">
          <div class="user-info style-2 acea-row row-between-wrapper">
            <div class="text">
              <div class="name" :style="{ color: nameColor, fontSize: nameSize + 'px' }">Đây là biệt danh</div>
              <div class="level" v-if="userInfoConfig == 0" :style="{ color: numColor, fontSize: numSize + 'px' }">
                14512349876
              </div>
              <div class="level" v-else :style="{ color: numColor, fontSize: numSize + 'px' }">9527</div>
            </div>
            <div class="avatar">
              <img :src="logoConfig" v-if="logoConfig" />
              <div class="empty-box" v-else>
                <img src="@/assets/images/shan.png" />
              </div>
            </div>
          </div>
        </div>
      </template>
      <!-- Style 3: Centered Vertical Layout -->
      <template v-if="styleConfig == 2">
        <div class="card-header acea-row row-center-wrapper">
          <div class="user-info style-3">
            <div class="avatar">
              <img :src="logoConfig" v-if="logoConfig" />
              <div class="empty-box" v-else>
                <img src="@/assets/images/shan.png" />
              </div>
            </div>
            <div class="text">
              <div class="name" :style="{ color: nameColor, fontSize: nameSize + 'px' }">Đây là biệt danh</div>
              <div class="level" v-if="userInfoConfig == 0" :style="{ color: numColor, fontSize: numSize + 'px' }">
                14512349876
              </div>
              <div class="level" v-else :style="{ color: numColor, fontSize: numSize + 'px' }">ID: 9527</div>
            </div>
          </div>
          <!-- Menu Entry -->
          <div
            class="menu-entry"
            v-if="menuList.length"
            style="position: absolute; right: 10px; top: 14px; z-index: 10"
          >
            <div class="item" v-for="(item, index) in menuList" :key="index" :style="{ marginLeft: '10px' }">
              <template v-if="menuStyle == 0">
                <img :src="item.img" v-if="item.img" class="menu-img" />
                <div class="empty-icon" v-else>
                  <img src="@/assets/images/shan.png" />
                </div>
              </template>
              <template v-else>
                <span
                  class="mb-iconfont"
                  :class="item.icon"
                  :style="{
                    display: 'inline-block',
                    color: iconStyle.color.color[0].item,
                    fontSize: iconStyle.size.val + 'px',
                    padding: iconStyle.padding.val + 'px',
                    transform: 'rotate(' + iconStyle.rotate.val + 'deg)',
                  }"
                  v-if="item.icon"
                ></span>
              </template>
            </div>
          </div>
        </div>
      </template>
      <!-- Style 4: Split Layout -->
      <template v-if="styleConfig == 3">
        <div class="style-4-container acea-row row-between-wrapper">
          <div class="left-card" :style="moduleCardStyle">
            <div class="user-info acea-row row-middle">
              <div class="avatar">
                <img :src="logoConfig" v-if="logoConfig" />
                <div class="empty-box" v-else>
                  <img src="@/assets/images/shan.png" />
                </div>
              </div>
              <div class="text">
                <div class="name" :style="{ color: nameColor, fontSize: nameSize + 'px' }">Đây là biệt danh</div>
                <div class="level" v-if="userInfoConfig == 0" :style="{ color: numColor, fontSize: numSize + 'px' }">
                  14512349876
                </div>
                <div class="level" v-else :style="{ color: numColor, fontSize: numSize + 'px' }">ID: 9527</div>
              </div>
            </div>
            <div class="stats-row card-data acea-row row-around">
              <template v-if="assetMode == 0">
                <div
                  class="item"
                  v-for="(item, index) in dataList"
                  :key="index"
                  :class="{
                    'style-vert': dataStyle == 0,
                    'style-horiz': dataStyle == 1,
                    'style-vert-2': dataStyle == 2,
                  }"
                >
                  <template v-if="dataStyle == 0">
                    <div class="num" :style="{ color: dataNumColor }">{{ item.val }}</div>
                    <div class="label" :style="{ color: dataTitleColor }">{{ item.name }}</div>
                  </template>
                  <template v-if="dataStyle == 1 || dataStyle == 2">
                    <div class="label" :style="{ color: dataTitleColor }">{{ item.name }}</div>
                    <div class="num" :style="{ color: dataNumColor }">{{ item.val }}</div>
                  </template>
                </div>
              </template>
              <template v-if="assetMode == 1">
                <div class="item style-icon" v-for="(item, index) in assetList" :key="index">
                  <div class="icon-box">
                    <img :src="item.img" v-if="assetStyle == 0 && item.img" class="img-icon" />
                    <span
                      class="iconfont"
                      :class="item.icon"
                      :style="{ fontSize: assetIconSize + 'px', color: assetIconColor }"
                      v-else-if="assetStyle == 1 && item.icon"
                    ></span>
                    <div class="empty-icon" v-else><img src="@/assets/images/shan.png" /></div>
                  </div>
                  <div class="label" :style="{ fontSize: assetTextSize + 'px', color: assetTextColor }">
                    {{ item.info[0].value }}
                  </div>
                </div>
              </template>
            </div>
          </div>
          <div class="right-card" :style="moduleCardStyle">
            <div class="entry-content" v-if="rightEntryList.length">
              <div
                class="title"
                :style="{ fontSize: '14px', fontWeight: 'bold', marginBottom: '5px', color: moduleTitleColor }"
              >
                {{ rightEntryList[0].info[0].value }}
              </div>
              <div
                class="subtitle"
                style="font-size: 12px; color: #ff9900; margin-bottom: 10px; display: flex; align-items: center"
              >
                {{ rightEntryList[0].info[1].value }}
                <span class="iconfont iconyou" style="font-size: 12px"></span>
              </div>
              <div class="img-box">
                <img :src="rightEntryList[0].img" v-if="rightEntryList[0].img" />
                <div class="empty-icon" v-else><img src="@/assets/images/shan.png" /></div>
              </div>
            </div>
          </div>
        </div>
      </template>
      <!-- Style 5: White Card Layout -->
      <template v-if="styleConfig == 4">
        <div class="style-5-container">
          <div class="header acea-row row-between-wrapper">
            <div class="left">
              <div class="avatar">
                <img :src="logoConfig" v-if="logoConfig" />
                <div class="empty-box" v-else>
                  <img src="@/assets/images/shan.png" />
                </div>
              </div>
              <div class="text">
                <div class="name" :style="{ color: nameColor, fontSize: nameSize + 'px' }">Đây là biệt danh</div>
                <div class="level" v-if="userInfoConfig == 0" :style="{ color: numColor, fontSize: numSize + 'px' }">
                  14512349876
                </div>
                <div class="level" v-else :style="{ color: numColor, fontSize: numSize + 'px' }">ID: 9527</div>
              </div>
            </div>
            <!-- Menu Entry -->
            <div class="menu-entry" v-if="menuList.length">
              <div class="item" v-for="(item, index) in menuList" :key="index" :style="{ marginLeft: '10px' }">
                <template v-if="menuStyle == 0">
                  <img :src="item.img" v-if="item.img" class="menu-img" />
                </template>
                <template v-else>
                  <span
                    class="mb-iconfont"
                    :class="item.icon"
                    :style="{
                      display: 'inline-block',
                      color: iconStyle.color.color[0].item,
                      fontSize: iconStyle.size.val + 'px',
                      padding: iconStyle.padding.val + 'px',
                      transform: 'rotate(' + iconStyle.rotate.val + 'deg)',
                    }"
                    v-if="item.icon"
                  ></span>
                </template>
              </div>
            </div>
          </div>
          <!-- Asset Grid -->
          <div class="card-data acea-row row-around" v-if="assetList.length">
            <template v-if="assetMode == 0">
              <div
                class="item"
                v-for="(item, index) in dataList"
                :key="index"
                :class="{
                  'style-vert': dataStyle == 0,
                  'style-horiz': dataStyle == 1,
                  'style-vert-2': dataStyle == 2,
                }"
              >
                <template v-if="dataStyle == 0">
                  <div class="num" :style="{ color: dataNumColor }">{{ item.val }}</div>
                  <div class="label" :style="{ color: dataTitleColor }">{{ item.name }}</div>
                </template>
                <template v-if="dataStyle == 1 || dataStyle == 2">
                  <div class="label" :style="{ color: dataTitleColor }">{{ item.name }}</div>
                  <div class="num" :style="{ color: dataNumColor }">{{ item.val }}</div>
                </template>
              </div>
            </template>

            <!-- Graphic Mode -->
            <template v-if="assetMode == 1">
              <div class="item style-icon" v-for="(item, index) in assetList" :key="index">
                <div class="icon-box">
                  <img :src="item.img" v-if="assetStyle == 0 && item.img" class="img-icon" />
                  <span
                    class="iconfont"
                    :class="item.icon"
                    :style="{ fontSize: assetIconSize + 'px', color: assetIconColor }"
                    v-else-if="assetStyle == 1 && item.icon"
                  ></span>
                  <div class="empty-icon" v-else><img src="@/assets/images/shan.png" /></div>
                </div>
                <div class="label">{{ item.info[0].value }}</div>
              </div>
            </template>
          </div>
        </div>
      </template>
      <!-- Lối tắt -->
      <template
        v-if="
          ((assetMode == 0 && checkType.length) || (assetMode == 1 && assetList.length)) &&
          styleConfig != 3 &&
          styleConfig != 4
        "
      >
        <!-- Lối tắt -->
        <div class="card-data acea-row row-around">
          <!-- Data Mode -->
          <template v-if="assetMode == 0">
            <div
              class="item"
              v-for="(item, index) in dataList"
              :key="index"
              :class="{
                'style-vert': dataStyle == 0,
                'style-horiz': dataStyle == 1,
                'style-vert-2': dataStyle == 2,
              }"
            >
              <template v-if="dataStyle == 0">
                <div class="num" :style="{ color: dataNumColor }">{{ item.val }}</div>
                <div class="label" :style="{ color: dataTitleColor }">{{ item.name }}</div>
              </template>
              <template v-if="dataStyle == 1 || dataStyle == 2">
                <div class="label" :style="{ color: dataTitleColor }">{{ item.name }}</div>
                <div class="num" :style="{ color: dataNumColor }">{{ item.val }}</div>
              </template>
            </div>
          </template>

          <!-- Graphic Mode -->
          <template v-if="assetMode == 1">
            <div class="item style-icon" v-for="(item, index) in assetList" :key="index">
              <div class="icon-box">
                <img :src="item.img" v-if="assetStyle == 0 && item.img" class="img-icon" />
                <span
                  class="iconfont"
                  :class="item.icon"
                  :style="{ fontSize: assetIconSize + 'px', color: assetIconColor }"
                  v-else-if="assetStyle == 1 && item.icon"
                ></span>
                <div class="empty-icon" v-else><img src="@/assets/images/shan.png" /></div>
              </div>
              <div class="label" :style="{ color: assetTextColor, fontSize: assetTextSize + 'px' }">
                {{ item.info[0].value }}
              </div>
            </div>
          </template>
        </div>
      </template>
      <!-- Kiểu thành viên, tổng cộng 4 kiểu -->
      <template v-if="memberStyleConfig == 0">
        <div class="member-style-1" :style="[cardStyle, { marginTop: memberTopMargin }]">
          <div class="item" v-for="(item, index) in memberList" :key="index">
            <div class="text-box">
              <div class="title">{{ item.info[0].value }}</div>
              <div class="subtitle">{{ item.info[1].value }} <span class="iconfont iconyou"></span></div>
            </div>
            <div class="img-box">
              <img :src="item.img" v-if="item.img" />
              <div class="empty-icon" v-else><img src="@/assets/images/shan.png" /></div>
            </div>
            <div class="line" v-if="index < memberList.length - 1"></div>
          </div>
        </div>
      </template>

      <!-- Member Style 2 -->
      <template v-if="memberStyleConfig == 1">
        <div class="member-style-2" :style="[cardStyle, { marginTop: memberTopMargin }]">
          <div class="top-row">
            <div class="left-info">
              <div class="title-area">
                <img :src="ms2TitleImage" v-if="ms2TitleType == 1 && ms2TitleImage" class="title-img" />
                <span class="title-text" v-else :style="{ color: ms2TitleColor }">{{ ms2TitleText }}</span>
              </div>
              <div class="intro-text" :style="{ color: ms2IntroColor }">{{ ms2IntroText }}</div>
            </div>
            <div class="right-rights">
              <div class="right-item" v-for="(item, index) in ms2RightsList" :key="index">
                <div class="icon-wrap">
                  <img :src="item.img" v-if="item.img" />
                  <span
                    class="iconfont"
                    :class="item.icon"
                    v-else-if="item.icon"
                    :style="{ color: ms2RightsColor }"
                  ></span>
                  <div class="empty-icon" v-else><img src="@/assets/images/shan.png" /></div>
                </div>
                <span class="text" :style="{ color: ms2RightsColor }">{{ item.info[0].value }}</span>
              </div>
              <span class="iconfont iconyou" :style="{ color: ms2RightsColor }"></span>
            </div>
          </div>
          <div class="divider"></div>
          <div class="bottom-row">
            <div class="explain-list">
              <div class="icons" v-if="ms2ExplainIcons">
                <img :src="ms2ExplainIcons" />
              </div>
              <div class="explain-text" :style="{ color: ms2ExplainColor }">{{ ms2ExplainText }}</div>
            </div>
            <div class="action-btn" :style="{ color: ms2ButtonColor, background: ms2ButtonBgColor }">
              {{ ms2ButtonText }}
            </div>
          </div>
        </div>
      </template>

      <!-- Member Style 3 -->
      <template v-if="memberStyleConfig == 2">
        <div class="member-style-3" :style="[cardStyle]">
          <div class="content-wrapper" :style="ms3ContainerStyle">
            <div class="desc" :style="{ color: ms3TitleColor, fontSize: '12px' }">
              {{ ms3TitleText }}
            </div>
            <div
              class="btn"
              :style="{
                color: ms3ButtonColor,
                borderColor: ms3ButtonColor,
                border: '1px solid',
                padding: '4px 12px',
                borderRadius: '14px',
                fontSize: '12px',
              }"
            >
              {{ ms3ButtonText }}
            </div>
          </div>
        </div>
      </template>
      <!-- Member Style 4 -->
      <template v-if="memberStyleConfig == 3">
        <div class="member-style-4" :style="[cardStyle, { marginTop: memberTopMargin }]">
          <div class="left-box">
            <div class="label" style="color: #999; font-size: 12px; margin-bottom: 5px">Có thể rút (đ)</div>
            <div class="value" style="color: #f6d99d; font-size: 24px; font-weight: bold">200.00</div>
          </div>
          <div
            class="right-box"
            style="
              background: #f6d99d;
              color: #5a350c;
              padding: 5px 15px;
              border-radius: 20px;
              font-size: 12px;
              font-weight: bold;
            "
          >
            Rút tiền ngay
          </div>
        </div>
      </template>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';
import template from '../../pages/setting/devise/template.vue';
export default {
  components: { template },
  name: 'home_member',
  cname: 'Trung tâm thành viên',
  configName: 'c_member',
  icon: '#iconzujian-huiyuanxinxi',
  type: 4, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ 3 thành phần sản phẩm 4 thành phần người dùng
  defaultName: 'member',
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
    dataList() {
      let list = [
        { id: 1, name: 'Số dư', val: '200' },
        { id: 3, name: 'Phiếu giảm giá', val: '2888' },
        { id: 2, name: 'Điểm thưởng', val: '3000' },
        { id: 5, name: 'Sản phẩm yêu thích', val: '1660' },
        { id: 6, name: 'Lịch sử xem', val: '1660' },
        { id: 8, name: 'Hoa hồng giới thiệu', val: '666' },
        { id: 9, name: 'Người được giới thiệu', val: '1660' },
        { id: 10, name: 'Đơn hàng giới thiệu', val: '1660' },
      ];
      return list.filter((item) => this.checkType.indexOf(item.id) != -1);
    },
    moduleCardStyle() {
      if (this.styleConfig == 3) {
        let color1 = this.moduleBgColor && this.moduleBgColor.color[0] ? this.moduleBgColor.color[0].item : '#fff';
        let color2 = this.moduleBgColor && this.moduleBgColor.color[1] ? this.moduleBgColor.color[1].item : '#fff';
        return {
          background: `linear-gradient(90deg, ${color1} 0%, ${color2} 100%)`,
          borderRadius: this.moduleRadius ? this.moduleRadius : '0px',
        };
      }
      return {};
    },
    moduleTitleColor() {
      return this.moduleTextColor && this.moduleTextColor.color[0] ? this.moduleTextColor.color[0].item : '#333';
    },
    memberTopMargin() {
      return this.styleConfig == 3 || this.styleConfig == 4 ? '10px' : '';
    },
    cardStyle() {
      if (this.memberStyleConfig == 0) {
        let color1 = this.cardBgColor && this.cardBgColor.color[0] ? this.cardBgColor.color[0].item : '#fff';
        let color2 = this.cardBgColor && this.cardBgColor.color[1] ? this.cardBgColor.color[1].item : '#fff';
        return {
          background: `linear-gradient(90deg, ${color1} 0%, ${color2} 100%)`,
          borderRadius: this.cardBgRadius ? this.cardBgRadius : '0px',
        };
      } else if (this.memberStyleConfig == 1) {
        let color1 = this.cardBgColor && this.cardBgColor.color[0] ? this.cardBgColor.color[0].item : '#fff';
        let color2 = this.cardBgColor && this.cardBgColor.color[1] ? this.cardBgColor.color[1].item : '#fff';
        return {
          background: `linear-gradient(90deg, ${color1} 0%, ${color2} 100%)`,
          borderRadius: this.cardBgRadius ? this.cardBgRadius : '0px',
        };
      } else if (this.memberStyleConfig == 2) {
        let style = { borderRadius: this.cardBgRadius ? this.cardBgRadius : '0px' };
        if (this.ms3BgMode === 1 && this.ms3BackgroundImage) {
          style.backgroundImage = `url(${this.ms3BackgroundImage})`;
          style.backgroundRepeat = 'no-repeat';
          style.backgroundSize = '100% 100%';
        } else {
          const c1 = this.cardBgColor && this.cardBgColor.color[0] ? this.cardBgColor.color[0].item : '#fff';
          const c2 = this.cardBgColor && this.cardBgColor.color[1] ? this.cardBgColor.color[1].item : c1;
          style.background = `linear-gradient(90deg, ${c1} 0%, ${c2} 100%)`;
        }
        return style;
      } else if (this.memberStyleConfig == 3) {
        let style = { borderRadius: this.cardBgRadius ? this.cardBgRadius : '0px' };
        if (this.ms4BgMode === 1 && this.ms4BackgroundImage) {
          style.backgroundImage = `url(${this.ms4BackgroundImage})`;
          style.backgroundRepeat = 'no-repeat';
          style.backgroundSize = '100% 100%';
        } else {
          const c1 = this.cardBgColor && this.cardBgColor.color[0] ? this.cardBgColor.color[0].item : '#fff';
          const c2 = this.cardBgColor && this.cardBgColor.color[1] ? this.cardBgColor.color[1].item : c1;
          style.background = `linear-gradient(90deg, ${c1} 0%, ${c2} 100%)`;
        }
        return style;
      }
      return {};
    },
    ms4BgColorStyle() {
      return this.ms4BgColor && this.ms4BgColor.color ? this.ms4BgColor.color : [];
    },
    ms3ContainerStyle() {
      let pad = this.ms3PaddingConfig
        ? this.ms3PaddingConfig.valList
        : [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }];
      return {
        padding: `${pad[0].val}px ${pad[1].val}px ${pad[2].val}px ${pad[3].val}px`,
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
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
    configObj: {
      handler(nVal, oVal) {
        if (!nVal) return;
        this.ms3BgColor = nVal.ms3BgColor ? nVal.ms3BgColor.color : [];
        this.ms3BgMode = nVal.ms3BgMode ? nVal.ms3BgMode.tabVal : 0;
        this.ms3BackgroundImage = nVal.ms3BackgroundImage ? nVal.ms3BackgroundImage.url : '';
        this.ms4BgColor = nVal.ms4BgColor ? nVal.ms4BgColor.color : [];
        this.ms4BgMode = nVal.ms4BgMode ? nVal.ms4BgMode.tabVal : 0;
        this.ms4BackgroundImage = nVal.ms4BackgroundImage ? nVal.ms4BackgroundImage.url : '';
        this.assetIconColor = nVal.assetIconColor ? nVal.assetIconColor.color[0].item : '#ff9900';
        this.assetIconSize = nVal.assetIconSize ? nVal.assetIconSize.val : 20;
        this.assetTextColor = nVal.assetTextColor ? nVal.assetTextColor.color[0].item : '#333';
        this.assetTextSize = nVal.assetTextSize ? nVal.assetTextSize.val : 12;
      },
      deep: true,
    },
  },
  data() {
    return {
      configObj: null,
      assetIconColor: '',
      assetIconSize: 20,
      assetTextColor: '',
      assetTextSize: 12,
      defaultConfig: {
        cname: 'Trung tâm thành viên',
        name: 'member',
        desc: 'Mô-đun trung tâm thành viên, có thể dùng để hiển thị thông tin thành viên, phiếu giảm giá, điểm thưởng và nhiều nội dung khác',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleImg: 'Ảnh đại diện mặc định',
        titleRight: 'Cài đặt kiểu',
        titleCurrency: 'Kiểu chung',
        infoStyleText: 'Thông tin thành viên',
        memberStyleText: 'Kiểu thành viên',
        iconStyleText: 'Kiểu biểu tượng',
        assetConfigText: 'Lối vào ảnh kèm chữ',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          tabList: [{ name: 'Kiểu 1' }, { name: 'Kiểu 2' }, { name: 'Kiểu 3' }, { name: 'Kiểu 4' }, { name: 'Kiểu 5' }],
        },
        memberStyleConfig: {
          title: 'Kiểu thành viên',
          tabVal: 0,
          tabList: [{ name: 'Kiểu 1' }, { name: 'Kiểu 2' }, { name: 'Kiểu 3' }, { name: 'Kiểu 4' }],
        },
        menuConfig: {
          title: 'Có thể thêm tối đa 2 ảnh, kích thước đề xuất 40 * 40px',
          bnt: 'Thêm',
          listStyleName: 'Nội dung thao tác',
          type: 1,
          listStyle: 0,
          maxList: 2,
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
        checkboxInfo: {
          title: 'Nội dung dữ liệu',
          name: 'checkboxInfo',
          maxList: 5,
          type: [1, 2, 3],
          list: [
            { id: 1, name: 'Số dư' },
            { id: 3, name: 'Phiếu giảm giá' },
            { id: 2, name: 'Điểm thưởng' },
            { id: 5, name: 'Sản phẩm yêu thích' },
            { id: 6, name: 'Lịch sử xem' },
            { id: 8, name: 'Hoa hồng giới thiệu' },
            { id: 9, name: 'Người được giới thiệu' },
            { id: 10, name: 'Đơn hàng giới thiệu' },
          ],
        },
        logoConfig: {
          info: 'Đề xuất: kích thước ảnh 90px * 90px',
          url: '',
          type: 'code',
          delType: 1,
          name: 'Tải lên ảnh',
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
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
        fillet: {
          title: 'Bo góc nền',
          type: 0,
          list: [
            { val: 'Tất cả', icon: 'iconcaozuo-zhengti' },
            { val: 'Từng góc', icon: 'iconcaozuo-bianjiao' },
          ],
          valName: 'Giá trị bo góc',
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        paddingConfig: {
          title: 'Lề trong',
          val: 15,
          min: 0,
          isAll: false,
          valList: [{ val: 15 }, { val: 15 }, { val: 0 }, { val: 15 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        assetIconColor: {
          title: 'Màu biểu tượng',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        },
        assetIconSize: {
          title: 'Kích thước biểu tượng',
          val: 20,
          min: 10,
          max: 32,
        },
        assetTextColor: {
          title: 'Màu chữ',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        },
        assetTextSize: {
          title: 'Cỡ chữ',
          val: 12,
          min: 10,
          max: 32,
        },
      },
      pageData: {},
      styleConfig: 0,
      userInfoConfig: 0,
      memberStyleConfig: 0,
      iconStyle: {},
      checkType: [],
      logoConfig: '',
      menuStyle: 0,
      menuList: [],
      shortcutStyle: 0,
      shortcutList: [],
      assetMode: 0,
      dataStyle: 0,
      assetStyle: 2,
      assetList: [],
      bgColorLeft: '',
      bgColorRight: '',
      bottomBgColor: '',
      fillet: null,
      paddingConfig: {
        title: 'Lề trong',
        isAll: false,
        val: 15,
        min: 0,
        valList: [{ val: 15 }, { val: 15 }, { val: 15 }, { val: 15 }],
      },
      marginConfig: {
        title: 'Lề ngoài',
        isAll: false,
        val: 0,
        min: 0,
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
      nameColor: '#fff',
      nameSize: 16,
      numColor: '#fff',
      numSize: 14,
      dataTitleColor: '#fff',
      dataNumColor: '#fff',
      zIndexConfig: null,
      componentBgConfig: null,
      borderConfig: null,
      shadowConfig: null,
      memberConfig: null,
      memberList: [],
      rightEntryList: [],
      leftMenuList: [],
      cardBgColor: null,
      cardBgRadius: 0,
      moduleBgColor: null,
      moduleTextColor: null,
      moduleRadius: null,
      ms2TitleType: 0,
      ms2TitleText: '',
      ms2TitleColor: '',
      ms2TitleImage: '',
      ms2IntroText: '',
      ms2IntroColor: '',
      ms2RightsList: [],
      ms2RightsColor: '',
      ms2ExplainIcons: '',
      ms2ExplainText: '',
      ms2ExplainColor: '',
      ms2ButtonText: '',
      ms2ButtonLink: '',
      ms2ButtonColor: '',
      ms2ButtonBgColor: '',
      ms3TitleText: '',
      ms3TitleColor: '',
      ms3ButtonText: '',
      ms3ButtonColor: '',
      ms3PaddingConfig: null,
      ms3BackgroundImage: '',
      ms3BgColor: null,
      ms3BgMode: 0,
      ms4BgColor: null,
      ms4BgMode: 0,
      ms4BackgroundImage: '',
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
    });
  },
  methods: {
    getNum(item) {
      if (!item.info || !item.info[1]) return 0;
      let val = item.info[1].value;
      if (val.indexOf('money') != -1) return 200;
      if (val.indexOf('integral') != -1) return 3000;
      if (val.indexOf('coupon') != -1) return 2888;
      if (val.indexOf('collection') != -1) return 666;
      if (val.indexOf('visit') != -1) return 1660;
      return 0;
    },
    setConfig(data) {
      if (!data) return;

      let dataClone = JSON.parse(JSON.stringify(data));
      for (let key in this.defaultConfig) {
        if (dataClone[key] === undefined) {
          this.$set(dataClone, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }
      this.configObj = dataClone;
      this.paddingConfig = dataClone.paddingConfig;
      this.marginConfig = dataClone.marginConfig;
      this.zIndexConfig = dataClone.zIndexConfig;
      this.borderConfig = dataClone.borderConfig;
      this.shadowConfig = dataClone.shadowConfig;
      this.componentBgConfig = dataClone.componentBgConfig;

      this.menuStyle = dataClone.menuConfig ? dataClone.menuConfig.listStyle : 0;
      this.styleConfig = dataClone.styleConfig.tabVal;
      this.userInfoConfig = dataClone.userInfoConfig ? dataClone.userInfoConfig.tabVal : 0;
      this.memberStyleConfig = dataClone.memberStyleConfig ? dataClone.memberStyleConfig.tabVal : 0;
      this.checkType = dataClone.checkboxInfo.type;
      this.logoConfig = dataClone.logoConfig.url;

      this.menuList = dataClone.menuConfig ? dataClone.menuConfig.list : [];
      this.shortcutStyle = dataClone.shortcutConfig ? dataClone.shortcutConfig.listStyle : 0;
      this.shortcutList = dataClone.shortcutConfig ? dataClone.shortcutConfig.list : [];
      this.assetMode = dataClone.assetMode ? dataClone.assetMode.tabVal : 0;
      this.dataStyle = dataClone.dataStyle ? dataClone.dataStyle.tabVal : 0;
      this.memberList = dataClone.memberConfig ? dataClone.memberConfig.list : [];
      this.rightEntryList = dataClone.rightEntryConfig ? dataClone.rightEntryConfig.list : [];
      this.cardBgColor = dataClone.cardBgColor ? dataClone.cardBgColor : [];
      let cardFillet = dataClone.cardBgRadius ? dataClone.cardBgRadius.type : 0;
      let cardFilletVal = dataClone.cardBgRadius ? dataClone.cardBgRadius.val : 0;
      let cardValList = dataClone.cardBgRadius && dataClone.cardBgRadius.valList ? dataClone.cardBgRadius.valList : [];
      this.cardBgRadius = cardFillet
        ? cardValList[0].val +
          'px ' +
          cardValList[1].val +
          'px ' +
          cardValList[3].val +
          'px ' +
          cardValList[2].val +
          'px'
        : cardFilletVal + 'px';
      this.moduleBgColor = dataClone.moduleBgColor;
      this.moduleTextColor = dataClone.moduleTextColor;
      let moduleFillet = dataClone.moduleRadius ? dataClone.moduleRadius.type : 0;
      let moduleFilletVal = dataClone.moduleRadius ? dataClone.moduleRadius.val : 0;
      let moduleValList =
        dataClone.moduleRadius && dataClone.moduleRadius.valList ? dataClone.moduleRadius.valList : [];
      this.moduleRadius = moduleFillet
        ? moduleValList[0].val +
          'px ' +
          moduleValList[1].val +
          'px ' +
          moduleValList[3].val +
          'px ' +
          moduleValList[2].val +
          'px'
        : moduleFilletVal + 'px';
      this.ms2TitleType = dataClone.ms2TitleType ? dataClone.ms2TitleType.tabVal : 0;
      this.ms2TitleText = dataClone.ms2TitleText ? dataClone.ms2TitleText.value : '';
      this.ms2TitleColor = dataClone.ms2TitleColor ? dataClone.ms2TitleColor.color[0].item : '';
      this.ms2TitleImage = dataClone.ms2TitleImage ? dataClone.ms2TitleImage.url : '';
      this.ms2IntroText = dataClone.ms2IntroText ? dataClone.ms2IntroText.value : '';
      this.ms2IntroColor = dataClone.ms2IntroColor ? dataClone.ms2IntroColor.color[0].item : '';
      this.ms2RightsList = dataClone.ms2RightsList ? dataClone.ms2RightsList.list : [];
      this.ms2RightsColor = dataClone.ms2RightsColor ? dataClone.ms2RightsColor.color[0].item : '';
      this.ms2ExplainIcons = dataClone.ms2ExplainIcons ? dataClone.ms2ExplainIcons.url : '';
      this.ms2ExplainText = dataClone.ms2ExplainText ? dataClone.ms2ExplainText.value : '';
      this.ms2ExplainColor = dataClone.ms2ExplainColor ? dataClone.ms2ExplainColor.color[0].item : '';
      this.ms2ButtonText = dataClone.ms2ButtonText ? dataClone.ms2ButtonText.value : '';
      this.ms2ButtonLink = dataClone.ms2ButtonLink ? dataClone.ms2ButtonLink.value : '';
      this.ms2ButtonColor = dataClone.ms2ButtonColor ? dataClone.ms2ButtonColor.color[0].item : '';
      this.ms2ButtonBgColor = dataClone.ms2ButtonBgColor ? dataClone.ms2ButtonBgColor.color[0].item : '';
      this.ms3TitleText = dataClone.ms3TitleText ? dataClone.ms3TitleText.value : '';
      this.ms3TitleColor = dataClone.ms3TitleColor ? dataClone.ms3TitleColor.color[0].item : '';
      this.ms3ButtonText = dataClone.ms3ButtonText ? dataClone.ms3ButtonText.value : '';
      this.ms3ButtonColor = dataClone.ms3ButtonColor ? dataClone.ms3ButtonColor.color[0].item : '';
      this.ms3PaddingConfig = dataClone.ms3PaddingConfig;
      this.ms3BgMode = dataClone.ms3BgMode ? dataClone.ms3BgMode.tabVal : 0;
      this.ms3BgColor = dataClone.ms3BgColor ? dataClone.ms3BgColor.color : [];
      this.ms3BackgroundImage = dataClone.ms3BackgroundImage ? dataClone.ms3BackgroundImage.url : '';
      this.ms4BgMode = dataClone.ms4BgMode ? dataClone.ms4BgMode.tabVal : 0;
      this.ms4BgColor = dataClone.ms4BgColor ? dataClone.ms4BgColor.color : [];
      this.ms4BackgroundImage = dataClone.ms4BackgroundImage ? dataClone.ms4BackgroundImage.url : '';
      this.assetStyle = dataClone.assetConfig ? dataClone.assetConfig.listStyle : 2;
      this.assetList = dataClone.assetConfig ? dataClone.assetConfig.list : [];
      this.iconStyle = dataClone.iconStyleConfig || dataClone.iconStyle || {};
      this.bottomBgColor = dataClone.bottomBgColor.color[0].item;
      this.fillet = dataClone.fillet;
      this.nameColor = dataClone.nameColor ? dataClone.nameColor.color[0].item : '#333333';
      this.nameSize = dataClone.nameSize ? dataClone.nameSize.val : 16;
      this.numColor = dataClone.numColor ? dataClone.numColor.color[0].item : '#333333';
      this.numSize = dataClone.numSize ? dataClone.numSize.val : 14;
      this.dataTitleColor = dataClone.dataTitleColor ? dataClone.dataTitleColor.color[0].item : '#333333';
      this.dataNumColor = dataClone.dataNumColor ? dataClone.dataNumColor.color[0].item : '#333333';
      this.assetIconColor = dataClone.assetIconColor ? dataClone.assetIconColor.color[0].item : '#ff9900';
      this.assetIconSize = dataClone.assetIconSize ? dataClone.assetIconSize.val : 20;
      this.assetTextColor = dataClone.assetTextColor ? dataClone.assetTextColor.color[0].item : '#333';
      this.assetTextSize = dataClone.assetTextSize ? dataClone.assetTextSize.val : 12;
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-page {
  display: inline-block;
  width: -webkit-fill-available;
}
.member-card {
  position: relative;
  min-height: 100px;
  overflow: hidden;
  .shortcut-entry {
    .shortcut-img {
      width: 20px;
      height: 20px;
      display: block;
    }
    .empty-icon {
      width: 20px;
      height: 20px;
      background: #f5f5f5;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
      font-size: 12px;
    }
  }
  .menu-entry {
    display: flex;
    align-items: center;
    .iconfont {
      font-size: 20px;
      color: #333;
    }
    .menu-img {
      width: 20px;
      height: 20px;
      display: block;
    }
    .empty-icon {
      width: 20px;
      height: 20px;
      background: #f5f5f5;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
      font-size: 12px;
    }
  }
  .card-header {
    position: relative;
    z-index: 2;
    .user-info {
      &.style-2 {
        width: 100%;
        padding: 0px 21px;
        .avatar {
          margin-right: 0;
        }
      }
      &.style-3 {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        .avatar {
          margin-right: 0;
          margin-bottom: 10px;
        }
        .text {
          text-align: center;
          .name {
            justify-content: center;
          }
        }
      }
      .avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        margin-right: 0;
        margin-right: 16px;
        img {
          width: 100%;
          height: 100%;
          border-radius: 50%;
          object-fit: cover;
        }
        .empty-box {
          background-color: #f3f9ff;
          border: 1px solid #eeeeee;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 100%;
          height: 100%;
          border-radius: 50%;
          img {
            width: 26px;
            height: 20px;
            border-radius: 0;
          }
        }
      }
      .text {
        .name {
          font-size: 16px;
          font-weight: 500;
          color: #fff;
          display: flex;
          align-items: center;
          margin-bottom: 5px;
        }
        .level {
          font-size: 10px;
          color: #ffffff;
        }
      }
    }
  }
  .card-data {
    margin-top: 20px;
    position: relative;
    z-index: 2;
    .item {
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;

      &.style-vert {
        flex-direction: column;
      }
      &.style-horiz {
        flex-direction: row;
        .num {
          margin-left: 5px;
        }
      }
      &.style-vert-2 {
        flex-direction: column;
      }
      &.style-icon {
        flex-direction: column;
        .icon-box {
          margin-bottom: 5px;
          display: flex;
          justify-content: center;
          align-items: center;
          .iconfont {
            font-size: 24px;
            color: #fff;
          }
          .img-icon {
            width: 24px;
            height: 24px;
          }
          .empty-icon {
            width: 24px;
            height: 24px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
            border-radius: 4px;
          }
        }
      }

      .num {
        font-size: 16px;
        font-weight: bold;
        color: #fff;
      }
      .label {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0;
      }
    }
  }

  &.style4 {
    .text-center {
      text-align: center;
      margin-top: 10px;
    }
    .avatar {
      margin-right: 0;
    }
  }
}
.member-style-1 {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 0px;
  margin-top: 18px;
  .item {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    padding: 0 20px 0 20px;

    .text-box {
      .title {
        font-size: 14px;
        color: #333;
        font-weight: bold;
        margin-bottom: 5px;
      }
      .subtitle {
        font-size: 11px;
        color: #ff7d00;
        display: flex;
        align-items: center;
        .iconfont {
          font-size: 10px;
          margin-top: 1px;
        }
      }
    }

    .img-box {
      width: 44px;
      height: 44px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
      }
      .empty-icon {
        width: 100%;
        height: 100%;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
        border-radius: 50%;
      }
    }

    .line {
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 1px;
      height: 20px;
      background: #eee;
    }
  }
}

.style-4-container {
  align-items: stretch;
  .left-card {
    flex: 2;
    margin-right: 10px;
    padding: 15px;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    .user-info {
      margin-bottom: 10px;
      .avatar {
        width: 40px;
        height: 40px;
        margin-right: 10px;
        img {
          width: 100%;
          height: 100%;
          border-radius: 50%;
          object-fit: cover;
        }
        .empty-box {
          width: 100%;
          height: 100%;
          border-radius: 50%;
          background: #eee;
          display: flex;
          align-items: center;
          justify-content: center;
          img {
            width: 20px;
            height: 20px;
            border-radius: 0;
          }
        }
      }
      .text {
        .name {
          margin-bottom: 3px;
        }
      }
    }
    .stats-row {
      .item {
        display: flex;
        flex-direction: column;
        align-items: center;
      }
    }
  }
  .right-card {
    flex: 1;
    padding: 15px 10px;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;

    .entry-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;

      .img-box {
        width: 60px;
        height: 60px;
        margin-top: 5px;
        img {
          width: 100%;
          height: 100%;
          object-fit: contain;
        }
        .empty-icon {
          width: 100%;
          height: 100%;
          background: #f5f5f5;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #ccc;
          border-radius: 4px;
        }
      }
    }
  }
}

.style-5-container {
  padding: 20px;
  background: #fff;
  border-radius: 12px;
  margin-top: 45px;

  .header {
    margin-bottom: 45px;
    position: relative;
    .left {
      position: absolute;
      top: -46px;
      left: 0px;
      display: flex;
      align-items: flex-end;
      .avatar {
        width: 74px;
        height: 74px;
        margin-right: 12px;
        border-radius: 50%;
        border: 3px solid #fff;
        position: relative;
        img {
          width: 100%;
          height: 100%;
          border-radius: 50%;
          object-fit: cover;
        }
        .empty-box {
          width: 100%;
          height: 100%;
          border-radius: 50%;
          background: #f5f5f5;
          display: flex;
          align-items: center;
          justify-content: center;
          img {
            width: 25px;
            height: 25px;
            border-radius: 0;
          }
        }
      }

      .text {
        .name {
          font-weight: bold;
          margin-bottom: 5px;
        }
      }
    }

    .menu-entry {
      display: flex;
      align-items: center;
      position: absolute;
      top: 0px;
      right: 0px;
      .item {
        .menu-img {
          width: 24px;
          height: 24px;
        }
        .mb-iconfont {
          font-size: 24px;
          color: #333;
        }
      }
    }
  }

  .asset-grid {
    padding-top: 35px;
    .item {
      display: flex;
      flex-direction: column;
      align-items: center;

      .icon-box {
        width: 30px;
        height: 30px;
        margin-bottom: 8px;
        display: flex;
        justify-content: center;
        align-items: center;

        .img-icon {
          width: 100%;
          height: 100%;
          object-fit: contain;
        }
        .iconfont {
          font-size: 30px;
          color: #333;
        }
        .empty-icon {
          width: 30px;
          height: 30px;
          background: #f5f5f5;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #ccc;
          border-radius: 4px;
        }
      }
    }
  }
}

.member-style-2 {
  margin-top: 18px;
  padding: 15px;
  .top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    .left-info {
      .title-area {
        display: flex;
        align-items: center;
        .title-img {
          width: 162px;
          height: 30px;
          object-fit: cover;
        }
        .title-text {
          font-size: 18px;
          font-weight: bold;
        }
      }
      .intro-text {
        font-size: 12px;
        opacity: 0.8;
      }
    }
    .right-rights {
      display: flex;
      align-items: center;
      .right-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-left: 10px;
        .icon-wrap {
          width: 28px;
          height: 28px;
          margin-right: 4px;
          img {
            width: 100%;
            height: 100%;
            object-fit: contain;
          }
          .iconfont {
            font-size: 16px;
          }
          .empty-icon {
            width: 100%;
            height: 100%;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            border-radius: 50%;
            img {
              font-size: 12px;
            }
          }
        }
        .text {
          font-size: 12px;
        }
      }
      .iconyou {
        margin-left: 5px;
        font-size: 12px;
      }
    }
  }
  .divider {
    height: 1px;
    background: rgba(0, 0, 0, 0.05);
    margin: 0;
  }
  .bottom-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    .explain-list {
      display: flex;
      align-items: center;
      .icons {
        display: flex;
        margin-right: 10px;
        img {
          height: 32px;
          object-fit: contain;
        }
      }
      .explain-text {
        font-size: 12px;
      }
    }
    .action-btn {
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
    }
  }
}

.member-style-3 {
  margin-top: 18px;
}

.member-style-4 {
  margin-top: 18px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
