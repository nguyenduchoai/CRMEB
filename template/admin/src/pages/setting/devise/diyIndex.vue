<template>
  <!-- Thêm chủ đề - Thiết kế giao diện trang chủ -->
  <div class="diy-page">
    <div class="i-layout-page-header header-title">
      <div class="fl_header">
        <!-- <span class="ivu-page-header-title mr20" style="padding: 0" v-text="$route.meta.title"></span> -->
        <div class="f-title acea-row row-middle">
          <div class="acea-row row-middle cup" @click="returnTap">
            <div class="iconfont iconfanhui"></div>
            <div class="return">Quay lại</div>
          </div>
          <div class="mr20">
            <span class="name mr5">Trang hiện tại: {{ nameTxt || 'Mẫu' }}</span>
            <el-popover v-model="visible" width="347">
              <span slot="reference" class="iconfont iconzidingyicaidan cup"></span>
              <template>
                <div class="flex">
                  <el-input
                    v-model="nameTxt"
                    placeholder="Bắt buộc, không quá 15 ký tự"
                    maxlength="15"
                    style="width: 200px"
                  ></el-input>
                  <el-button type="text" @click="cancel">Hủy</el-button>
                  <el-button type="primary" @click="determine">Xác nhận</el-button>
                </div>
              </template>
            </el-popover>
          </div>
        </div>
        <div class="rbtn">
          <!-- <el-button class="ml20 header-btn look" v-db-click @click="exportView" :loading="loading">Xuất</el-button>
          <el-button class="ml20 header-btn look" v-db-click @click="importView" :loading="loading">Nhập</el-button> -->
          <el-button class="ml20 header-btn look" v-db-click @click="preview" :loading="loading">Xem trước</el-button>
          <el-button class="ml20 header-btn close" v-db-click @click="saveConfig(1)" :loading="loading">Lưu</el-button>
          <el-button class="ml20 header-btn save" v-db-click @click="saveConfig(2)" :loading="loading"
            >Lưu và đóng</el-button
          >
        </div>
      </div>
    </div>
    <el-card :bordered="false" shadow="never">
      <div class="diy-wrapper" :style="'height:' + clientHeight + 'px;'">
        <!-- Bên trái -->
        <div class="left">
          <div class="wrapper" :style="'height:' + clientHeight + 'px;'">
            <div class="list" v-for="(item, index) in leftMenu" :key="index">
              <div class="tips" @click="item.isOpen = !item.isOpen">
                {{ item.title }}
                <div class="iconfont iconyou" v-if="!item.isOpen"></div>
                <div class="iconfont iconxia" v-else></div>
              </div>
              <!-- Kéo thả thành phần -->
              <draggable
                class="dragArea list-group"
                :list="item.list"
                :group="{ name: 'people', pull: 'clone', put: false }"
                :clone="cloneDog"
                dragClass="dragClass"
                filter=".search , .navbar , .homeComb , .service"
              >
                <div
                  class="list-group-item"
                  :class="{
                    search: element.cname == 'Ô tìm kiếm',
                    navbar: element.cname == 'Tab',
                    homeComb: element.cname == 'Tìm kiếm kèm ảnh trình chiếu',
                    service: element.cname == 'Nút nổi',
                  }"
                  v-for="(element, index) in item.list"
                  :key="element.id"
                  @click="addDom(element, 1)"
                  v-show="item.isOpen"
                >
                  <div>
                    <div class="position" style="display: none">Thả chuột để thêm thành phần vào đây</div>
                    <svg class="conter iconfont-diy icon svg-icon" aria-hidden="true">
                      <use :xlink:href="element.icon"></use>
                    </svg>
                    <p class="conter">{{ element.cname }}</p>
                  </div>
                </div>
              </draggable>
            </div>
          </div>
        </div>
        <!-- Phần giữa tùy chỉnh cấu hình trang di động -->
        <div class="wrapper-con">
          <div class="content">
            <div class="contxt">
              <div class="overflowy">
                <div class="picture"><img src="@/assets/images/electric.png" /></div>
                <div class="page-title" :class="{ on: activeIndex == -100 }" @click="showTitle">
                  {{ titleTxt }}
                  <div class="delete-box"></div>
                  <div class="handle"></div>
                </div>
              </div>
              <div class="scrollCon" :style="'height:' + rollHeight + 'px;'">
                <div style="width: 460px; margin: 0 auto">
                  <div
                    class="scroll-box"
                    :class="
                      picTxt && tabValTxt == 2
                        ? 'fullsize noRepeat'
                        : picTxt && tabValTxt == 1
                        ? 'repeat ysize'
                        : 'noRepeat ysize'
                    "
                    :style="
                      'background-color:' +
                      (colorTxt ? colorPickerTxt : '') +
                      ';background-image: url(' +
                      (picTxt ? picUrlTxt : '') +
                      ');min-height:' +
                      rollHeight +
                      'px;'
                    "
                    ref="imgContainer"
                  >
                    <draggable
                      class="dragArea list-group"
                      :list="mConfig"
                      group="people"
                      @change="log"
                      filter=".top"
                      :move="onMove"
                      animation="300"
                    >
                      <div
                        class="mConfig-item"
                        :class="{
                          on: activeIndex == key,
                          top: item.name == 'search_box' || item.name == 'nav_bar',
                          hide: defaultArrays[item.num].isHide,
                        }"
                        v-for="(item, key) in mConfig"
                        :key="key"
                        @click.stop="bindconfig(item, key)"
                        :style="colorTxt ? 'background-color:' + colorPickerTxt + ';' : 'background-color:#fff;'"
                      >
                        <component
                          :is="item.name"
                          ref="getComponentData"
                          :configData="propsObj"
                          :index="key"
                          :num="item.num"
                          :colorStyle="colorStyle"
                        ></component>
                        <div class="delete-box">
                          <div class="handleType">
                            <div
                              class="iconfont"
                              :class="defaultArrays[item.num].isHide ? 'iconyincang' : 'iconxianshi'"
                              @click.stop="bindHide(item)"
                            ></div>
                            <div class="iconfont iconshanchu3" @click.stop="bindDelete(item, key)"></div>
                            <div class="iconfont icona-fuzhi1" @click.stop="bindAddDom(item, 0, key)"></div>
                            <div
                              class="iconfont iconshang"
                              :class="key === 0 ? 'on' : ''"
                              @click.stop="movePage(item, key, 1)"
                            ></div>
                            <div
                              class="iconfont iconxia"
                              :class="key === mConfig.length - 1 ? 'on' : ''"
                              @click.stop="movePage(item, key, 0)"
                            ></div>
                          </div>
                        </div>
                        <div class="handle"></div>
                        <div class="delete-name" :class="{ on: activeIndex == key }">{{ item.cname }}</div>
                      </div>
                    </draggable>
                  </div>
                </div>
              </div>
              <div class="overflowy">
                <div
                  class="page-foot"
                  @click="showFoot"
                  :class="{ on: activeIndex == -101 }"
                  :style="pageFooterType == 1 ? 'bottom:' + (50 + pageFooterBottom) + 'px' : ''"
                >
                  <footPage></footPage>
                  <div class="delete-box"></div>
                  <div class="handle"></div>
                </div>
              </div>
              <div class="defaultData" v-if="pageId !== 0">
                <!-- <div class="data" @click="setmoren">Đặt làm mặc định</div>
                                  <div class="data" @click="getmoren">Khôi phục mặc định</div> -->
                <el-button class="data" @click="showTitle">Cài đặt trang</el-button>
                <el-button class="data" @click="nameModal = true">Lưu thành mẫu</el-button>
                <el-button class="data" @click="reast">Đặt lại</el-button>
              </div>
            </div>
          </div>
        </div>
        <!-- Thiết lập trang bên phải -->
        <div class="right-box">
          <div class="mConfig-item" style="background-color: #fff" v-for="(item, key) in rConfig" :key="key">
            <!-- <div class="title-bar">{{ item.cname }}</div> -->
            <component
              :is="item.configName"
              @config="config"
              :activeIndex="activeIndex"
              :num="item.num"
              :index="key"
            ></component>
          </div>
        </div>
      </div>
    </el-card>
    <el-dialog :visible.sync="modal" width="540px" title="Xem trước">
      <div>
        <div v-viewer class="acea-row row-around code">
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic" ref="qrCodeUrl"></div>
            <span class="mt10">Mã QR OA WeChat</span>
          </div>
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic">
              <img v-lazy="qrcodeImg" />
            </div>
            <span class="mt10">Mã QR Mini Program</span>
          </div>
        </div>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="nameModal" width="470px" title="Đặt tên mẫu" :show-close="true">
      <el-input v-model="saveName" placeholder="Vui lòng nhập tên mẫu"></el-input>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="nameModal = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="saveModal">Xác nhận</el-button>
      </span>
    </el-dialog>
    <!--<div class="foot-box">-->
    <!--<Button @click="reast">Đặt lại</Button>-->
    <!--<Button type="primary" @click="saveConfig" :loading="loading"-->
    <!--&gt;Lưu-->
    <!--</Button-->
    <!--&gt;-->
    <!--</div>-->
  </div>
</template>

<script crossorigin="anonymous">
import { categoryList, diyProInfo, diyProSave, setDefault, recovery, diyUpdateName, getRoutineCode } from '@/api/diy';
import vuedraggable from 'vuedraggable';
import mPage from '@/components/mobilePage/index.js';
import mConfig from '@/components/mobileConfig/index.js';
import footPage from '@/components/pagesFoot';
import { mapState } from 'vuex';
import html2canvas from 'html2canvas';
import theme from '@/mixins/theme';
import Setting from '@/setting';
import QRCode from 'qrcodejs2';

export default {
  inject: ['reload'],
  name: 'index.vue',
  components: {
    footPage,
    html2canvas,
    draggable: vuedraggable,
    ...mPage,
    ...mConfig,
  },
  filters: {
    filterTxt(val) {
      if (val) {
        return (val = val.substr(0, val.length - 1));
      }
    },
  },
  computed: {
    ...mapState({
      titleTxt: (state) => state.mobildConfig.pageTitle || 'Trang chủ',
      showTxt: (state) => state.mobildConfig.pageShow,
      colorTxt: (state) => state.mobildConfig.pageColor,
      picTxt: (state) => state.mobildConfig.pagePic,
      colorPickerTxt: (state) => state.mobildConfig.pageColorPicker,
      tabValTxt: (state) => state.mobildConfig.pageTabVal,
      picUrlTxt: (state) => state.mobildConfig.pagePicUrl,
      pageFooterType: (state) => state.mobildConfig.pageFooter.navConfig.tabVal || 0,
      pageFooterBottom: (state) => state.mobildConfig.pageFooter.mbConfig.val,
      defaultArrays: (state) => state.mobildConfig.defaultArray,
    }),
    nameTxt: {
      get() {
        return this.$store.state.mobildConfig.pageName;
      },
      set(value) {
        this.$store.commit('mobildConfig/UPNAME', value);
      },
    },
  },
  mixins: [theme],
  data() {
    return {
      BaseURL: Setting.apiBaseURL.replace(/adminapi/, ''),
      qrcodeImg: '',
      modal: false,
      clientHeight: '', //Chiều cao động của trang
      rollHeight: '',
      leftMenu: [], // Menu bên trái
      lConfig: [], // Thành phần bên trái
      mConfig: [], // Render thành phần ở giữa
      rConfig: [], // Cấu hình thành phần bên phải
      activeConfigName: '',
      propsObj: {}, // Dữ liệu do thành phần truyền,
      activeIndex: -100, // Chỉ số được chọn
      number: 0,
      pageId: '',
      pageName: '',
      pageType: '',
      category: [],
      tabList: [
        {
          title: 'Thư viện thành phần',
          key: 0,
        },
        {
          title: 'Liên kết trang',
          key: 1,
        },
      ],
      footActive: false,
      loading: false,
      relLoading: false,
      isSearch: false,
      isTab: false,
      isFllow: false,
      isComb: false,
      isService: false,
      visible: true,
      diyStatus: 0,
      nameModal: false,
      saveName: '',
    };
  },
  created() {
    this.categoryList();
    this.pageId = this.$route.query.id;
    this.pageName = this.$route.query.name;
    this.pageType = this.$route.query.type;
    this.lConfig = this.objToArr(mPage);
    let imgList = {
      imgList: [require('@/assets/images/foot-005.png'), require('@/assets/images/foot-006.png')],
      name: 'Giỏ hàng',
      link: '/pages/order_addcart/order_addcart',
    };
    this.$nextTick(() => {
      this.$store.commit('mobildConfig/FOOTER', { title: 'Tùy chỉnh', name: imgList });
      this.arraySort();
      if (this.pageId != 0) {
        this.getDefaultConfig();
      } else {
        this.showTitle();
      }
      this.clientHeight = `${document.documentElement.clientHeight}` - 65.81; //Lấy chiều cao vùng hiển thị của trình duyệt
      let H = `${document.documentElement.clientHeight}` - 180;
      this.rollHeight = H > 650 ? 650 : H;
      let that = this;
      window.onresize = function () {
        that.clientHeight = `${document.documentElement.clientHeight}` - 65.81;
        let H = `${document.documentElement.clientHeight}` - 180;
        that.rollHeight = H > 650 ? 650 : H;
      };
    });
  },
  methods: {
    exportView() {
      let that = this;
      this.loading = true;
      this.$nextTick(() => {
        console.log(this.mConfig);
      });
    },
    importView() {},
    preview() {
      this.modal = true;
      this.creatQrCode(this.pageId, this.diyStatus);
      this.routineCode(this.pageId);
    },
    //Mã QR Mini Program
    routineCode(id) {
      getRoutineCode(id)
        .then((res) => {
          this.qrcodeImg = res.data.image;
        })
        .catch((err) => {
          this.$message.error(err);
        });
    },
    //Tạo mã QR
    creatQrCode(id, status) {
      this.$refs.qrCodeUrl.innerHTML = '';
      let url = '';
      if (status) {
        url = `${this.BaseURL}pages/index/index`;
      } else {
        url = `${this.BaseURL}pages/annex/special/index?id=${id}`;
      }
      var qrcode = new QRCode(this.$refs.qrCodeUrl, {
        text: url, // Nội dung cần chuyển đổi thành mã QR
        width: 160,
        height: 160,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
      });
    },
    changName(val) {
      this.$store.commit('mobildConfig/UPNAME', val);
    },
    cancel() {
      this.visible = false;
    },
    determine() {
      if (this.nameTxt.trim() == '') {
        return this.$message.error('Vui lòng nhập tên mẫu');
      }
      if (this.pageId == 0) {
        this.$message.success('Sửa thành công');
        return false;
      }
      diyUpdateName(this.pageId, { name: this.nameTxt })
        .then((res) => {
          this.visible = false;
          this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
      this.visible = false;
    },
    returnTap() {
      this.$msgbox({
        title: 'Lưu ý',
        message: 'Bạn có chắc muốn rời trang này? Hệ thống có thể sẽ không lưu các thay đổi bạn đã thực hiện.',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xác nhận',
        iconClass: 'el-icon-warning',
        confirmButtonClass: 'btn-custom-cancel',
      })
        .then(() => {
          this.$router.push('/admin/setting/pages/devise/0');
        })
        .catch(() => {});
    },
    leftRemove({ to, from, item, clone, oldIndex, newIndex }) {
      if (this.isSearch && newIndex == 0) {
        if (item._underlying_vm_.name == 'z_wechat_attention') {
          this.isFllow = true;
        } else {
          this.$store.commit('mobildConfig/ARRAYREAST', this.mConfig[0].num);
          this.mConfig.splice(0, 1);
        }
      }
      if ((this.isFllow = true && newIndex >= 1)) {
        this.$store.commit('mobildConfig/ARRAYREAST', this.mConfig[0].num);
      }
    },
    onMove(e) {
      if (e.relatedContext.element.name == 'search_box') return false;
      if (e.relatedContext.element.name == 'nav_bar') return false;
      if (e.relatedContext.element.name == 'home_comb') return false;
      return true;
    },
    onCopy() {
      this.$message.success('Sao chép thành công');
    },
    onError() {
      this.$message.error('Sao chép thất bại');
    },
    //Đặt dữ liệu mặc định
    setmoren() {
      setDefault(this.pageId)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    //Khôi phục mặc định
    getmoren() {
      recovery(this.pageId)
        .then((res) => {
          this.$message.success(res.msg);
          this.reload();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Nhấp vào tiêu đề trang
    showTitle() {
      this.activeIndex = -100;
      let obj = {};
      for (var i in mConfig) {
        if (i == 'pageTitle') {
          // this.rConfig = obj
          obj = mConfig[i];
          obj.configName = mConfig[i].name;
          obj.cname = 'Cài đặt trang';
        }
      }
      let abc = obj;
      this.rConfig = [];
      this.rConfig[0] = JSON.parse(JSON.stringify(obj));
    },
    // Nhấp vào chân trang
    showFoot() {
      this.activeIndex = -101;
      let obj = {};
      for (var i in mConfig) {
        if (i == 'pageFoot') {
          // this.rConfig = obj
          obj = mConfig[i];
          obj.configName = mConfig[i].name;
          obj.cname = 'Menu dưới cùng';
        }
      }
      let abc = obj;
      this.rConfig = [];
      this.rConfig[0] = JSON.parse(JSON.stringify(obj));
    },
    // Chuyển object thành mảng
    objToArr(data) {
      let obj = Object.keys(data);
      let m = obj.map((key) => data[key]);
      return m;
    },
    log(evt) {
      // Kéo thả sắp xếp ở giữa
      if (evt.moved) {
        if (evt.moved.element.name == 'search_box') {
          return this.$message.warning('Thành phần này không cho phép kéo thả');
        }
        // if (evt.moved.element.name == "nav_bar") {
        //     return this.$message.warning("Thành phần này không được phép kéo thả");
        // }
        evt.moved.oldNum = this.mConfig[evt.moved.oldIndex].num;
        evt.moved.newNum = this.mConfig[evt.moved.newIndex].num;
        evt.moved.status = evt.moved.oldIndex > evt.moved.newIndex;
        this.mConfig.forEach((el, index) => {
          el.num = new Date().getTime() * 1000 + index;
        });
        evt.moved.list = this.mConfig;
        this.rConfig = [];
        let item = evt.moved.element;
        let tempItem = JSON.parse(JSON.stringify(item));
        this.rConfig.push(tempItem);
        this.activeIndex = evt.moved.newIndex;
        this.$store.commit('mobildConfig/SETCONFIGNAME', item.name);
        this.$store.commit('mobildConfig/defaultArraySort', evt.moved);
      }
      // Kéo thả sắp xếp từ trái sang phải
      if (evt.added) {
        let data = evt.added.element;
        let obj = {};
        let timestamp = new Date().getTime() * 1000;
        data.num = timestamp;
        this.activeConfigName = data.name;
        let tempItem = JSON.parse(JSON.stringify(data));
        tempItem.id = 'id' + tempItem.num;
        this.mConfig[evt.added.newIndex] = tempItem;
        this.rConfig = [];
        this.rConfig.push(tempItem);
        this.mConfig.forEach((el, index) => {
          el.num = new Date().getTime() * 1000 + index;
        });
        evt.added.list = this.mConfig;
        this.activeIndex = evt.added.newIndex;
        // Lưu tên thành phần
        this.$store.commit('mobildConfig/SETCONFIGNAME', data.name);
        this.$store.commit('mobildConfig/defaultArraySort', evt.added);
      }
    },
    cloneDog(data) {
      // this.mConfig.push(tempItem)
      return {
        ...data,
      };
    },
    //Đổi vị trí phần tử trong mảng
    swapArray(arr, index1, index2) {
      arr[index1] = arr.splice(index2, 1, arr[index1])[0];
      return arr;
    },
    //Nhấp để di chuyển lên xuống;
    movePage(item, index, type) {
      if (type) {
        if (index == 0) {
          return;
        }
      } else {
        if (index == this.mConfig.length - 1) {
          return;
        }
      }
      if (item.name == 'search_box' || item.name == 'nav_bar' || item.name == 'home_comb') {
        return this.$message.warning('Thành phần này không cho phép di chuyển');
      }
      if (type) {
        if (
          this.mConfig[index - 1].name == 'search_box' ||
          this.mConfig[index - 1].name == 'nav_bar' ||
          this.mConfig[index - 1].name == 'home_comb'
        ) {
          return this.$message.warning('Ô tìm kiếm, tab hoặc tìm kiếm kèm banner phải nằm trên cùng');
        }
        this.swapArray(this.mConfig, index - 1, index);
      } else {
        this.swapArray(this.mConfig, index, index + 1);
      }
      let obj = {};
      this.rConfig = [];
      obj.oldIndex = index;
      if (type) {
        obj.newIndex = index - 1;
      } else {
        obj.newIndex = index + 1;
      }
      this.mConfig.forEach((el, index) => {
        el.num = new Date().getTime() * 1000 + index;
      });
      let tempItem = JSON.parse(JSON.stringify(item));
      this.rConfig.push(tempItem);
      obj.element = item;
      obj.list = this.mConfig;
      if (type) {
        this.activeIndex = index - 1;
      } else {
        this.activeIndex = index + 1;
      }

      this.$store.commit('mobildConfig/SETCONFIGNAME', item.name);
      this.$store.commit('mobildConfig/defaultArraySort', obj);
    },
    // Thêm thành phần
    addDomCon(item, type, index) {
      if (item.name == 'search_box') {
        if (this.isSearch) return this.$message.error('Thành phần này chỉ được thêm một lần');
        if (this.isComb) return this.$message.error('Tìm kiếm kèm banner không thể dùng đồng thời với thành phần tìm kiếm và thành phần tab');
        this.isSearch = true;
      }
      if (item.name == 'nav_bar') {
        if (this.isTab) return this.$message.error('Thành phần này chỉ được thêm một lần');
        if (this.isComb) return this.$message.error('Tìm kiếm kèm banner không thể dùng đồng thời với thành phần tìm kiếm và thành phần tab');
        this.isTab = true;
      }
      if (item.name == 'home_comb') {
        if (this.isComb) return this.$message.error('Thành phần này chỉ được thêm một lần');
        if (this.isSearch || this.isTab) return this.$message.error('Tìm kiếm kèm banner không thể dùng đồng thời với thành phần tìm kiếm và thành phần tab');
        this.isComb = true;
      }
      if (item.name == 'home_service') {
        if (this.isService) return this.$message.error('Thành phần này chỉ được thêm một lần');
        this.isService = true;
      }
      let obj = {};
      let timestamp = new Date().getTime() * 1000;
      item.num = `${timestamp}`;
      item.id = `id${timestamp}`;
      this.activeConfigName = item.name;
      let tempItem = JSON.parse(JSON.stringify(item));
      if (item.name == 'home_comb') {
        this.rConfig = [];
        this.mConfig.unshift(tempItem);
        this.activeIndex = 0;
        this.rConfig.push(tempItem);
      } else if (item.name == 'search_box') {
        this.rConfig = [];
        this.mConfig.unshift(tempItem);
        this.activeIndex = 0;
        this.rConfig.push(tempItem);
      } else if (item.name == 'nav_bar') {
        this.rConfig = [];
        if (this.mConfig[0] && this.mConfig[0].name === 'search_box') {
          this.mConfig.splice(1, 0, tempItem);
          this.activeIndex = 1;
        } else {
          this.mConfig.splice(0, 0, tempItem);
          this.activeIndex = 0;
        }
        this.rConfig.push(tempItem);
      } else {
        if (type) {
          this.rConfig = [];
          if (this.activeIndex == 0 && this.mConfig[1] && this.mConfig[1].name == 'nav_bar') {
            this.activeIndex = 2;
          } else {
            this.activeIndex = this.activeIndex >= 0 ? this.activeIndex + 1 : this.mConfig.length;
          }
          this.mConfig.splice(this.activeIndex, 0, tempItem);
          this.rConfig.push(tempItem);
        } else {
          this.mConfig.splice(index + 1, 0, tempItem);
          this.activeIndex = index;
        }
      }
      this.mConfig.forEach((el, index) => {
        el.num = new Date().getTime() * 1000 + index;
      });
      // Lưu tên thành phần
      obj.element = item;
      obj.list = this.mConfig;
      this.$store.commit('mobildConfig/SETCONFIGNAME', item.name);
      this.$store.commit('mobildConfig/defaultArraySort', obj);
    },
    //Nhấp ở trang giữa để thêm mô-đun;
    bindAddDom(item, type, index) {
      let i = item;
      this.lConfig.forEach((j) => {
        if (item.name == j.name) {
          i = j;
        }
      });
      this.addDomCon(i, type, index);
    },
    //Nhấp thêm ở mô-đun cấu hình bên trái;
    addDom(item, type) {
      this.addDomCon(item, type);
    },
    // Nhấp để hiển thị cấu hình tương ứng
    bindconfig(item, index) {
      this.rConfig = [];
      let tempItem = JSON.parse(JSON.stringify(item));
      this.rConfig.push(tempItem);
      this.activeIndex = index;
      this.$store.commit('mobildConfig/SETCONFIGNAME', item.name);
    },
    bindHide(item) {
      let obj = this.$store.state.mobildConfig.defaultArray;
      let num = this.rConfig[0].num;
      obj[num].isHide = !obj[num].isHide;
      this.$store.commit('mobildConfig/UPDATEARR', { num: num, val: obj[num] });
    },
    // Xóa thành phần
    bindDelete(item, key) {
      if (item.name == 'search_box') {
        this.isSearch = false;
      }
      if (item.name == 'nav_bar') {
        this.isTab = false;
      }
      if (item.name == 'home_comb') {
        this.isComb = false;
      }
      if (item.name == 'home_service') {
        this.isService = false;
      }
      this.mConfig.splice(key, 1);
      this.rConfig.splice(0, 1);
      if (this.mConfig.length != key) {
        this.rConfig.push(this.mConfig[key]);
      } else {
        if (this.mConfig.length) {
          this.activeIndex = key - 1;
          this.rConfig.push(this.mConfig[key - 1]);
        } else {
          this.showTitle();
        }
      }
      // Xóa cấu hình thứ mấy
      this.$store.commit('mobildConfig/DELETEARRAY', item);
    },
    // Trả về thành phần
    config(data) {
      let propsObj = this.propsObj;
      propsObj.data = data;
      propsObj.name = this.activeConfigName;
    },
    addSort(arr, index1, index2) {
      arr[index1] = arr.splice(index2, 1, arr[index1])[0];
      return arr;
    },
    // Sắp xếp mảng
    arraySort() {
      let tempArr = [];
      let basis = {
        title: 'Thành phần cơ bản',
        list: [],
        isOpen: true,
      };
      let marketing = {
        title: 'Thành phần marketing',
        list: [],
        isOpen: true,
      };
      let tool = {
        title: 'Thành phần công cụ',
        list: [],
        isOpen: true,
      };
      this.lConfig.map((el, index) => {
        if (el.type == 0) {
          basis.list.push(el);
        }
        if (el.type == 1) {
          marketing.list.push(el);
        }
        if (el.type == 2) {
          tool.list.push(el);
        }
      });
      tempArr.push(basis, marketing, tool);
      this.leftMenu = tempArr;
    },
    // toImage(val){
    //     html2canvas(this.$refs.imgContainer,{
    //         useCORS:true,
    //         logging:true,
    //         taintTest: false,
    //         backgroundColor: null
    //     }).then((canvas) => {
    //         let imgUrl = canvas.toDataURL('image/jpeg');
    //         this.diySaveDate(val,imgUrl)
    //     });
    // },
    diySaveDate(val, num, type, save) {
      diyProSave(type ? 0 : this.pageId, {
        type: this.pageType || save,
        value: val,
        title: this.titleTxt,
        name: this.nameTxt || 'Mẫu',
        is_show: this.showTxt ? 1 : 0,
        is_bg_color: this.colorTxt ? 1 : 0,
        color_picker: this.colorPickerTxt,
        bg_pic: this.picUrlTxt,
        bg_tab_val: this.tabValTxt,
        is_bg_pic: this.picTxt ? 1 : 0,
      })
        .then((res) => {
          this.pageId = res.data.id;
          this.$message.success(res.msg);
          let that = this;
          this.nameModal = false;
          if (num == 2) {
            this.relLoading = false;
            setTimeout(() => {
              window.location.replace('/admin/setting/pages/devise/0');
            }, 2000);
          } else {
            this.loading = false;
          }
        })
        .catch((res) => {
          this.relLoading = false;
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    saveModal() {
      if (!this.saveName) return this.$message.warning('Vui lòng nhập tên mẫu trước');
      this.saveConfig(1, this.saveName);
    },
    closeWindow() {
      this.$msgbox({
        title: 'Thông báo',
        message: 'Vui lòng lưu dữ liệu trước khi đóng trang, nếu chưa lưu, dữ liệu sẽ bị mất',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xác nhận',
        iconClass: 'el-icon-warning',
        confirmButtonClass: 'btn-custom-cancel',
      })
        .then(() => {
          setTimeout(() => {
            // this.saveConfig();
            window.close();
          }, 1000);
        })
        .catch(() => {});
    },
    // Lưu cấu hình
    saveConfig(num, type, save) {
      if (this.mConfig.length == 0) {
        return this.$message.error('Chưa thêm thành phần nào, lưu thất bại!');
      }
      if (num == 1) {
        this.loading = true;
      } else {
        this.relLoading = true;
      }
      let val = this.$store.state.mobildConfig.defaultArray;
      if (!this.footActive) {
        let timestamp = new Date().getTime() * 1000;
        val[timestamp] = this.$store.state.mobildConfig.pageFooter;
        this.footActive = true;
      }
      this.$nextTick(() => {
        this.diySaveDate(val, num, type, save);
      });
    },
    // Lấy cấu hình mặc định
    getDefaultConfig() {
      diyProInfo(this.pageId, {
        type: 1,
      }).then(({ data }) => {
        let obj = {};
        let tempARR = [];
        this.$store.commit('mobildConfig/titleUpdata', data.info.title);
        this.$store.commit('mobildConfig/nameUpdata', data.info.name);
        this.$store.commit('mobildConfig/showUpdata', data.info.is_show);
        this.$store.commit('mobildConfig/colorUpdata', data.info.is_bg_color || 0);
        this.$store.commit('mobildConfig/picUpdata', data.info.is_bg_pic || 0);
        this.$store.commit('mobildConfig/pickerUpdata', data.info.color_picker || '#f5f5f5');
        this.$store.commit('mobildConfig/radioUpdata', data.info.bg_tab_val || 0);
        this.$store.commit('mobildConfig/picurlUpdata', data.info.bg_pic || '');
        this.diyStatus = data.info.status;
        let newArr = this.objToArr(data.info.value);

        function sortNumber(a, b) {
          return a.timestamp - b.timestamp;
        }
        newArr.sort(sortNumber);
        newArr.map((el, index) => {
          if (el.name == 'headerSerch') {
            this.isSearch = true;
          }
          if (el.name == 'tabNav') {
            this.isTab = true;
          }
          if (el.name == 'homeComb') {
            this.isComb = true;
          }
          if (el.name == 'customerService') {
            this.isService = true;
          }
          if (el.name == 'goodList') {
            // let storage = window.localStorage;
            // storage.setItem(el.timestamp, el.selectConfig.activeValue);
          }
          el.id = 'id' + el.timestamp;
          this.lConfig.map((item, j) => {
            if (el.name == item.defaultName) {
              item.num = el.timestamp;
              item.id = 'id' + el.timestamp;
              let tempItem = JSON.parse(JSON.stringify(item));
              tempARR.push(tempItem);
              obj[el.timestamp] = el;
              this.mConfig.push(tempItem);
              // Lưu cấu hình thành phần mặc định
              this.$store.commit('mobildConfig/ADDARRAY', {
                num: el.timestamp,
                val: el,
              });
            }
          });
        });

        let objs = newArr[newArr.length - 1];

        if (objs.name == 'pageFoot') {
          this.$store.commit('mobildConfig/footPageUpdata', objs);
        }
        this.showTitle();
      });
    },
    categoryList() {
      categoryList((res) => {
        this.category = res.data;
      });
    },
    // Đặt lại
    reast() {
      if (this.pageId == 0) {
        this.$message.error('Trang mới thêm, không thể đặt lại');
      } else {
        this.$confirm('Thao tác này sẽ xóa toàn bộ nội dung mẫu, bạn có muốn tiếp tục?', 'Thông báo', {
          confirmButtonText: 'Xác nhận',
          cancelButtonText: 'Hủy',
          type: 'warning',
        }).then((res) => {
          this.mConfig = [];
          this.rConfig = [];
          this.activeIndex = -99;
          this.getDefaultConfig();
        });
      }
    },
  },
  beforeDestroy() {
    this.$store.commit('mobildConfig/titleUpdata', '');
    this.$store.commit('mobildConfig/nameUpdata', '');
    this.$store.commit('mobildConfig/showUpdata', 1);
    this.$store.commit('mobildConfig/colorUpdata', 0);
    this.$store.commit('mobildConfig/picUpdata', 0);
    this.$store.commit('mobildConfig/pickerUpdata', '#f5f5f5');
    this.$store.commit('mobildConfig/radioUpdata', 0);
    this.$store.commit('mobildConfig/picurlUpdata', '');
    this.$store.commit('mobildConfig/SETEMPTY');
  },
  destroyed() {
    this.$store.commit('mobildConfig/titleUpdata', '');
    this.$store.commit('mobildConfig/nameUpdata', '');
    this.$store.commit('mobildConfig/showUpdata', 1);
    this.$store.commit('mobildConfig/colorUpdata', 0);
    this.$store.commit('mobildConfig/picUpdata', 0);
    this.$store.commit('mobildConfig/pickerUpdata', '#f5f5f5');
    this.$store.commit('mobildConfig/radioUpdata', 0);
    this.$store.commit('mobildConfig/picurlUpdata', '');
    this.$store.commit('mobildConfig/SETEMPTY');
  },
};
</script>
<style>
.el-main {
  padding: 0px !important;
}
</style>
<style scoped>
.header-title {
  background: var(--prev-color-primary);
  border-radius: 0;
  margin-bottom: 0;
  padding: 16px;
}
.ivu-page-header-title {
  color: #fff;
  font-size: 16px;
}
</style>
<style scoped lang="scss">
::v-deep .el-card__body {
  padding: 0;
}
::v-deep {
  .icondel_1,
  .upload-box {
    cursor: pointer;
  }
  .el-checkbox,
  .el-radio {
    margin-bottom: 15px;
    margin-right: 15px;
  }
}
.c_label {
  margin-top: 0;
}
::v-deep .el-button--small {
  // border-radius: 0;
  border-radius: 4px;
}
.look,
.look:hover,
.look:focus,
.look:active,
.close,
.close:hover,
.close:focus,
.close:active {
  background: var(--prev-color-primary);
  color: #fff;
  border-color: #fff;
}

.save,
.save:hover,
.save:active,
.save:focus {
  background: #fff;
  color: var(--prev-color-primary);
  border-color: var(--prev-color-primary);
}
::v-deep .c_row-item {
  margin-bottom: 15px;
}
.ysize {
  background-size: 100%;
}

.fullsize {
  background-size: 100% 100%;
}

.repeat {
  background-repeat: repeat;
}

.noRepeat {
  background-repeat: no-repeat;
}
.fl_header {
  color: #fff;
  .f-title {
    position: relative;
  }
  .return {
    color: #fff;
    margin-right: 34px;
    margin-left: 5px;
    &::after {
      content: ' ';
      position: absolute;
      width: 1px;
      height: 16px;
      background-color: rgba(238, 238, 238, 0.5);
      left: 65px;
      top: 50%;
      margin-top: -8px;
    }
  }
  .iconfont {
    color: #fff;
  }
  .f_title {
    &:hover {
      .return {
        color: rgba(255, 255, 255, 0.8);
      }
      .iconfanhui {
        color: rgba(255, 255, 255, 0.8);
      }
    }
    .name {
      font-size: 16px;
    }
    .iconfont {
      margin-left: 10px;
      color: #fff;
    }
  }
}
.wrapper-con {
  position: relative;
  flex: 1;
  background: #f0f2f5;
  display: flex;
  justify-content: center;
  padding-top: 20px;
  height: 100%;
  .acticons {
    position: absolute;
    right: 20px;
    top: 20px;
    display: flex;
    flex-direction: column;
    z-index: 1;
    .el-button + .el-button {
      margin-left: 0;
    }
  }
  /* min-width 700px; */
}
.main .content-wrapper {
  padding: 0 !important;
}
.defaultData {
  /* margin-left 20px; */
  cursor: pointer;
  position: absolute;
  left: 50%;
  margin-left: 245px;

  .data {
    display: block;
    margin-top: 20px;
    color: #282828;
    background-color: #fff;
    width: 94px;
    text-align: center;
    height: 32px;
    border-radius: 3px;
    font-size: 12px;
    margin-left: 0 !important;
  }

  .data:hover {
    background-color: #2d8cf0;
    color: #fff;
    border: 0;
  }
}

.overflowy {
  margin-right: 4px;

  .picture {
    width: 379px;
    height: 20px;
    margin: 0 auto;
    background-color: #fff;
  }
}

.bnt {
  width: 80px !important;
}

/* Định nghĩa thanh trượt: đổ bóng trong + góc tròn */
::-webkit-scrollbar-thumb {
  -webkit-box-shadow: inset 0 0 6px #fff;
  display: none;
}

.left:hover::-webkit-scrollbar-thumb,
.right-box:hover::-webkit-scrollbar-thumb {
  display: block;
}

.contxt:hover ::-webkit-scrollbar-thumb {
  display: block;
}

::-webkit-scrollbar {
  width: 4px !important; /* Áp dụng cho thanh cuộn dọc */
}

.scrollCon {
  overflow-y: scroll;
  overflow-x: hidden;
}

.scroll-box .position {
  display: block !important;
  height: 40px;
  text-align: center;
  line-height: 40px;
  border: 1px dashed var(--prev-color-primary);
  color: var(--prev-color-primary);
  background-color: #edf4fb;
}

.scroll-box .conter {
  display: none !important;
}
.conter {
  margin-top: 3px;
}
.dragClass {
  background-color: #fff;
}

.ivu-mt {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.iconfont-diy {
  font-size: 24px;
  color: var(--prev-color-primary);
}

.diy-wrapper {
  max-width: 100%;
  min-width: 1100px;
  display: flex;
  justify-content: space-between;
  height: calc(100vh - 62px);
  .left {
    min-width: 300px;
    max-width: 300px;
    /* border 1px solid #DDDDDD */
    border-radius: 4px;
    height: 100%;

    .title-bar {
      display: flex;
      color: #333;
      border-bottom: 1px solid #eee;
      border-radius: 4px;
      cursor: pointer;

      .title-item {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1;
        height: 45px;

        &.on {
          color: var(--prev-color-primary);
          font-size: 14px;
          border-bottom: 1px solid var(--prev-color-primary);
        }
      }
    }

    .wrapper {
      padding: 15px;
      overflow-y: scroll;
      -webkit-overflow-scrolling: touch;

      .tips {
        display: flex;
        justify-content: space-between;
        padding-bottom: 15px;
        font-size: 13px;
        color: #000;
        cursor: pointer;

        .ivu-icon {
          color: #000;
        }
      }
    }

    .link-item {
      padding: 10px;
      border-bottom: 1px solid #f5f5f5;
      font-size: 12px;
      color: #323232;

      .name {
        font-size: 14px;
        color: var(--prev-color-primary);
      }
      .copy_btn {
        cursor: pointer;
      }

      .link-txt {
        margin-top: 2px;
        word-break: break-all;
      }

      .params {
        margin-top: 5px;
        color: #1cbe6b;
        word-break: break-all;

        .txt {
          color: #323232;
        }

        span {
          &:last-child i {
            display: none;
            color: red;
          }
        }
      }

      .lable {
        display: flex;
        margin-top: 5px;
        color: #999;

        p {
          flex: 1;
          word-break: break-all;
        }

        button {
          margin-left: 30px;
          width: 38px;
        }
      }
    }

    .dragArea.list-group {
      display: flex;
      flex-wrap: wrap;

      .list-group-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 74px;
        height: 66px;
        margin-right: 17px;
        margin-bottom: 10px;
        font-size: 12px;
        color: #666;
        cursor: pointer;
        border-radius: 5px;
        text-align: center;

        &:hover {
          box-shadow: 0 0 5px 0 rgba(24, 144, 255, 0.3);
          border-right: 5px;
          transform: scale(1.1);
          transition: all 0.2s;
        }

        &:nth-child(3n) {
          margin-right: 0;
        }
      }
    }
  }

  .content {
    position: relative;
    height: 100%;
    width: 100%;

    .page-foot {
      position: relative;
      width: 379px;
      margin: 0 auto 20px auto;

      .delete-box {
        display: none;
        position: absolute;
        left: -2px;
        top: 0;
        width: 383px;
        height: 100%;
        border: 2px dashed var(--prev-color-primary);
        padding: 10px 0;
      }

      &:hover,
      &.on {
        /* cursor: move; */
        .delete-box {
          /* display: block; */
        }
      }

      &.on {
        cursor: move;

        .delete-box {
          display: block;
          border: 2px solid var(--prev-color-primary);
          box-shadow: 0 0 10px 0 rgba(24, 144, 255, 0.3);
        }
      }
    }

    .page-title {
      position: relative;
      height: 35px;
      line-height: 35px;
      background: #fff;
      font-size: 15px;
      color: #333333;
      text-align: center;
      width: 379px;
      margin: 0 auto;

      .delete-box {
        display: none;
        position: absolute;
        left: -2px;
        top: 0;
        width: 383px;
        height: 100%;
        border: 2px dashed var(--prev-color-primary);
        padding: 10px 0;

        span {
          position: absolute;
          right: 0;
          bottom: 0;
          width: 32px;
          height: 16px;
          line-height: 16px;
          display: inline-block;
          text-align: center;
          font-size: 10px;
          color: #fff;
          background: rgba(0, 0, 0, 0.4);
          margin-left: 2px;
          cursor: pointer;
          z-index: 11;
        }
      }

      &:hover,
      &.on {
        /* cursor: move; */
        .delete-box {
          /* display: block; */
        }
      }

      &.on {
        cursor: move;

        .delete-box {
          display: block;
          border: 2px solid var(--prev-color-primary);
          box-shadow: 0 0 10px 0 rgba(24, 144, 255, 0.3);
        }
      }
    }

    .scroll-box {
      flex: 1;
      background-color: #fff;
      width: 379px;
      margin: 0 auto;
      padding-top: 1px;
    }

    .dragArea.list-group {
      width: 100%;
      height: 100%;

      .mConfig-item {
        position: relative;
        cursor: move;
        &.hide {
          &::before {
            position: absolute;
            content: 'Đã ẩn';
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            z-index: 99;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
          }
        }
        .delete-name.on {
          background: var(--prev-color-primary-light-3);
          color: #fff;
          &::before {
            background: var(--prev-color-primary-light-3);
          }
        }
        .delete-name {
          position: absolute;
          top: 0;
          background: #fff;
          left: -100px;
          width: 86px;
          height: 32px;
          text-align: center;
          line-height: 32px;
          font-size: 13px;
          color: #666;
          border-radius: 3px;

          &::before {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: #fff;
            transform: rotate(45deg);
            top: 50%;
            right: -5px;
            margin-top: -5px;
          }
        }
        .delete-box {
          display: none;
          position: absolute;
          left: -2px;
          top: 0;
          width: 383px;
          height: 100%;
          border: 2px dashed var(--prev-color-primary);

          /* padding: 10px 0; */
          .handleType {
            position: absolute;
            right: -43px;
            top: 0;
            width: 36px;
            border-radius: 4px;
            background-color: var(--prev-color-primary);
            cursor: pointer;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 4px 0;
            .el-tooltip {
              background-color: inherit;
              color: inherit;
            }
            .iconfont {
              padding: 5px 0;

              &.on {
                opacity: 0.4;
              }
            }
          }
        }

        &.on {
          cursor: move;

          .delete-box {
            display: block;
            border: 2px solid var(--prev-color-primary);
            box-shadow: 0 0 10px 0 rgba(24, 144, 255, 0.3);
          }
        }
      }

      .mConfig-item:hover {
        transform: scale(1.01);
        box-shadow: 0 0 10px 0 rgba(24, 144, 255, 0.3);
        transition: all 0.2s;
      }
    }
  }

  .right-box {
    max-width: 400px;
    min-width: 400px;
    height: 100%;
    border-radius: 4px;
    overflow: scroll;
    -webkit-overflow-scrolling: touch;

    ::v-deep .ivu-tabs-bar {
      margin-bottom: 16px;
    }

    .title-bar {
      width: 100%;
      height: 45px;
      line-height: 45px;
      padding-left: 24px;
      color: #000;
      border-radius: 4px;
      border-bottom: 1px solid #eee;
      font-size: 14px;
    }
  }

  ::-webkit-scrollbar {
    width: 6px;
    background-color: transparent;
  }

  ::-webkit-scrollbar-track {
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb {
    background-color: #bfc1c4;
  }
}

.foot-box {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 80px;
  background: #fff;
  box-shadow: 0px -2px 4px 0px rgba(0, 0, 0, 0.03);

  button {
    width: 100px;
    height: 32px;
    font-size: 13px;

    &:first-child {
      margin-right: 20px;
    }
  }
}

::v-deep .ivu-scroll-loader {
  display: none;
}

::v-deep .ivu-card-body {
  width: 100%;
  padding: 0;
  height: calc(100vh - 73px);
}

.rbtn {
  position: absolute;
  right: 20px;
}
.code {
  position: relative;
}

.QRpic {
  width: 160px;
  height: 160px;

  img {
    width: 100%;
    height: 100%;
  }
}
.contxt {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  height: 100%;
}

.contxt:hover ::-webkit-scrollbar-thumb {
  display: block;
}
.iconfont-diy {
  font-size: 24px;
  color: var(--prev-color-primary);
}
.icon {
  width: 28px;
  height: 28px;
  // vertical-align: -0.15em;
  fill: currentColor;
  overflow: hidden;
}
</style>
