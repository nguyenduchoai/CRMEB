<template>
  <div class="" id="shopp-manager" v-loading="spinShow">
    <pages-header
      ref="pageHeader"
      :title="$route.params.id ? 'Sửa sản phẩm' : 'Thêm sản phẩm'"
      :backUrl="$routeProStr + '/product/product_list'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16" :body-style="{ padding: '0px 20px' }">
      <el-tabs v-model="currentTab">
        <el-tab-pane v-for="(item, index) in headTab" :key="index" :label="item.tit" :name="item.name"></el-tab-pane>
      </el-tabs>
      <el-form
        class="formValidate mt20"
        ref="formValidate"
        :rules="ruleValidate"
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <!-- Thông tin cơ bản-->
        <basic-info
          v-show="currentTab === '1'"
          :isCai="type"
          :formValidate="formValidate"
          :goodsType="goodsType"
          :treeSelect="treeSelect"
          :tileLabelList="tileLabelList"
          :progress="progress"
          :upload="upload"
          :videoIng="videoIng"
          @virtualbtn="virtualbtn"
          @handleDragStart="handleDragStart"
          @handleDragOver="handleDragOver"
          @handleDragEnter="handleDragEnter"
          @handleDragEnd="handleDragEnd"
          @handleRemove="handleRemove"
          @modalPicTap="modalPicTap"
          @addVideo="addVideo"
          @delVideo="delVideo"
          @addCate="addCate"
          @addGoodsTag="addGoodsTag"
        ></basic-info>

        <!-- Phân loại và tồn kho-->
        <spec-stock
          ref="specStock"
          v-show="currentTab === '2'"
          :formValidate="formValidate"
          :ruleList="ruleList"
          :attrs="attrs"
          :manyFormValidate="manyFormValidate"
          :oneFormValidate="oneFormValidate"
          :tableKey="tableKey"
          :oneFormBatch="oneFormBatch"
          :formDynamic="formDynamic"
          :canSel="canSel"
          @changeSpec="changeSpec"
          @confirm="confirm"
          @onMoveSpec="onMoveSpec"
          @changeCurrentIndex="changeCurrentIndex"
          @handleRemoveRole="handleRemoveRole"
          @attrChangeValue="attrChangeValue"
          @handleFocus="handleFocus"
          @addPic="addPic"
          @handleRemove2="handleRemove2"
          @attrDetailChangeValue="attrDetailChangeValue"
          @handleBlur="handleBlur"
          @handleSelImg="handleSelImg"
          @handleRemoveImg="handleRemoveImg"
          @handleShowPop="handleShowPop"
          @createAttr="createAttr"
          @handleAddRole="handleAddRole"
          @handleSaveAsTemplate="handleSaveAsTemplate"
          @batchAdd="batchAdd"
          @batchDel="batchDel"
          @modalPicTap="modalPicTap"
          @changeDefaultSelect="changeDefaultSelect"
          @changeDefaultShow="changeDefaultShow"
          @addGoodsCoupon="addGoodsCoupon"
          @see="see"
          @addVirtual="addVirtual"
        ></spec-stock>

        <!-- Chi tiết sản phẩm-->
        <product-detail
          v-show="currentTab === '3'"
          :contents="contents"
          :content="content"
          @getEditorContent="getEditorContent"
        ></product-detail>

        <!-- Cài đặt vận chuyển-->
        <logistics-setting
          v-show="headTab.length === 7 ? currentTab === '4' : false"
          :formValidate="formValidate"
          :templateList="templateList"
          @logisticsBtn="logisticsBtn"
          @addTemp="addTemp"
        ></logistics-setting>

        <!-- Giá thành viên/Hoa hồng -->
        <price-commission
          v-show="headTab.length === 7 ? currentTab === '5' : currentTab === '4'"
          :formValidate="formValidate"
          :oneFormValidate="oneFormValidate"
          :manyFormValidate="manyFormValidate"
          :columnsInstall="columnsInstall"
          :columnsInstal2="columnsInstal2"
          :manyBrokerage.sync="manyBrokerage"
          :manyBrokerageTwo.sync="manyBrokerageTwo"
          :manyVipPrice.sync="manyVipPrice"
          :manyVipDiscount.sync="manyVipDiscount"
          @checkAllGroupChange="checkAllGroupChange"
          @changeVipPrice="changeVipPrice"
          @changeDiscount="changeDiscount"
          @brokerageSetUp="brokerageSetUp"
        ></price-commission>

        <!-- Cài đặt marketing-->
        <marketing-setting
          v-show="headTab.length === 7 ? currentTab === '6' : currentTab === '5'"
          :formValidate="formValidate"
          :couponName="couponName"
          :dataLabel="dataLabel"
          :activity="activity"
          @handleClose="handleClose"
          @addCoupon="addCoupon"
          @openLabel="openLabel"
          @closeLabel="closeLabel"
          @addLabel="addLabel"
          @onchangeTime="onchangeTime"
          @handleRemoveRecommend="handleRemoveRecommend"
          @changeGoods="changeGoods"
        ></marketing-setting>

        <!-- Cài đặt khác-->
        <other-setting
          v-show="headTab.length === 7 ? currentTab === '7' : currentTab === '6'"
          :formValidate="formValidate"
          :customBtn.sync="customBtn"
          :paramsType="paramsType"
          :paramsTypeList="paramsTypeList"
          :protectionList="protectionList"
          :CustomList="CustomList"
          @modalPicTap="modalPicTap"
          @changeParamsType="changeParamsType"
          @deleteRow="deleteRow"
          @handleAddParams="handleAddParams"
          @addProtection="addProtection"
          @customMessBtn="customMessBtn"
          @delcustom="delcustom"
          @addcustom="addcustom"
        ></other-setting>

        <el-form-item>
          <el-button v-if="currentTab !== '1'" v-db-click @click="upTab">Bước trước</el-button>
          <el-button
            class="submission"
            v-if="currentTab !== '7' && formValidate.virtual_type == 0"
            v-db-click
            @click="downTab"
            >Bước tiếp theo</el-button
          >
          <el-button
            class="submission"
            v-if="currentTab !== '6' && formValidate.virtual_type != 0"
            v-db-click
            @click="downTab"
            >Bước tiếp theo</el-button
          >
          <el-button
            type="primary"
            class="submission"
            v-db-click
            @click="handleSubmit('formValidate')"
            v-if="$route.params.id || currentTab !== '1'"
            >Lưu</el-button
          >
        </el-form-item>
      </el-form>
      <el-dialog :visible.sync="modalPic" width="950px" scrollable title="Tải lên ảnh sản phẩm" :close-on-click-modal="false">
        <uploadPictures
          :isChoice="isChoice"
          @getPic="getPic"
          @getPicD="getPicD"
          :gridBtn="gridBtn"
          :gridPic="gridPic"
          v-if="modalPic"
        ></uploadPictures>
      </el-dialog>
      <el-dialog
        :visible.sync="addVirtualModel"
        width="720px"
        title="Thêm mã thẻ"
        :show-close="true"
        :close-on-click-modal="false"
        @closed="initVirtualData"
      >
        <div class="trip"></div>
        <div class="type-radio">
          <el-form label-width="85px">
            <el-form-item label="Loại mã thẻ:">
              <el-radio-group v-model="disk_type" size="large">
                <el-radio :label="1">Mã thẻ cố định</el-radio>
                <el-radio :label="2">Mã thẻ dùng một lần</el-radio>
              </el-radio-group>
              <div v-if="disk_type == 1">
                <div class="stock-disk">
                  <el-input v-model="disk_info" size="large" type="textarea" :rows="4" placeholder="Nhập thông tin mã thẻ" />
                </div>
                <div class="stock-input">
                  <!-- <el-input type="number" v-model="stock" size="large" :min='0' placeholder="Nhập số lượng tồn kho">
                    <span slot="append">cái</span>
                  </el-input> -->
                  <el-input-number :controls="false" :max="100000" :min="1" :step="1" :precision="0" v-model="stock" />
                  <span class="pl10">cái</span>
                </div>
              </div>
              <div class="scroll-virtual" v-if="disk_type == 2">
                <div class="virtual-data mb10" v-for="(item, index) in virtualList" :key="index">
                  <span class="mr10 virtual-title">Số thẻ {{ index + 1 }}:</span>
                  <el-input
                    class="mr10"
                    type="text"
                    v-model.trim="item.key"
                    style="width: 150px"
                    placeholder="Vui lòng nhập số thẻ (không bắt buộc)"
                  ></el-input>
                  <span class="mr10 virtual-title">Mã thẻ {{ index + 1 }}:</span>
                  <el-input
                    class="mr10"
                    type="text"
                    v-model.trim="item.value"
                    style="width: 150px"
                    placeholder="Vui lòng nhập mã thẻ"
                  ></el-input>
                  <span class="deteal-btn" v-db-click @click="removeVirtual(index)">Xóa</span>
                </div>
              </div>
              <div class="add-more" v-if="disk_type == 2">
                <el-button class="h-33" type="primary" v-db-click @click="handleAdd">Thêm mới</el-button>
                <el-upload
                  class="ml10"
                  :action="cardUrl"
                  :data="uploadData"
                  :headers="header"
                  :on-success="upFile"
                  :before-upload="beforeUpload"
                >
                  <el-button>Nhập mã thẻ</el-button>
                </el-upload>
              </div>
            </el-form-item>
          </el-form>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button v-db-click @click="closeVirtual">Hủy</el-button>
          <el-button type="primary" v-db-click @click="upVirtual">Xác nhận</el-button>
        </span>
      </el-dialog>
    </el-card>
    <freightTemplate
      :template="template"
      v-on:changeTemplate="changeTemplate"
      @addSuccess="productGetTemplate"
      ref="templates"
    ></freightTemplate>
    <add-attr ref="addattr" @getList="userSearchs"></add-attr>
    <coupon-list
      ref="couponTemplates"
      @nameId="nameId"
      :couponids="formValidate.coupon_ids"
      :updateIds="updateIds"
      :updateName="updateName"
    ></coupon-list>
    <coupon-list ref="goodsCoupon" many="one" :luckDraw="true" @getCouponId="goodsCouponId"></coupon-list>
    <!-- Tạo biểu mẫu Taobao, JD-->
    <el-dialog
      :visible.sync="modals"
      @closed="cancel"
      class="Box"
      title="Sao chép từ Taobao, Tmall, JD, Suning, 1688"
      :close-on-click-modal="false"
      width="720px"
    >
      <tao-bao ref="taobaos" v-if="modals" @on-close="onClose"></tao-bao>
    </el-dialog>
    <el-dialog :visible.sync="goods_modals" title="Danh sách sản phẩm" footerHide class="paymentFooter" scrollable width="1000px">
      <goods-list v-if="goods_modals" ref="goodslist" :ischeckbox="true" @getProductId="getProductId"></goods-list>
    </el-dialog>
    <!-- Nhãn người dùng -->
    <el-dialog
      :visible.sync="labelShow"
      title="Vui lòng chọn nhãn người dùng"
      :show-close="true"
      width="540px"
      :close-on-click-modal="false"
    >
      <userLabel ref="userLabel" @activeData="activeData" @close="labelClose"></userLabel>
    </el-dialog>
    <!-- Nhãn sản phẩm -->
    <el-dialog
      :visible.sync="tagShow"
      title="Vui lòng chọn nhãn sản phẩm"
      :show-close="true"
      width="540px"
      :close-on-click-modal="false"
    >
      <goodsLabel
        ref="goodsLabel"
        :defaultLabelList="labelList"
        @activeLabel="activeLabel"
        @close="labelClose"
      ></goodsLabel>
    </el-dialog>
  </div>
</template>

<script>
import userLabel from '@/components/labelList';
import useLabel from '@/components/goodsLabel/useLabel';
import goodsLabel from '@/components/goodsLabel';
import { mapState } from 'vuex';
import uploadPictures from '@/components/uploadPictures';
import freightTemplate from '@/components/freightTemplate';
import couponList from '@/components/couponList';
import addAttr from '../productAttr/addAttr';
import goodsList from '@/components/goodsList/index';
import taoBao from './taoBao';
import { userLabelAddApi } from '@/api/user';
import {
  productInfoApi,
  cascaderListApi,
  productAddApi,
  generateAttrApi,
  productGetRuleApi,
  productGetTemplateApi,
  productGetTempKeysApi,
  checkActivityApi,
  productCache,
  cacheDelete,
  uploadType,
  importCard,
  productCreateApi,
  getProductTypeConfig,
  ruleAddApi,
  paramListApi,
  paramInfoApi,
  productProtectionListApi,
  productLabelUseListApi,
} from '@/api/product';
import Setting from '@/setting';
import { getCookies } from '@/libs/util';
import { uploadByPieces } from '@/utils/upload'; //Import phương thức uploadByPieces
import { isFileUpload, isVideoUpload, arraysEqual } from '@/utils';
import checkArray from '@/libs/permission';
import {
  GoodsTableHead,
  VirtualTableHead,
  VirtualTableHead2,
  columns2,
  columns3,
  CustomList,
  RuleValidate,
} from './defaultData.js';
import BasicInfo from './components/BasicInfo.vue';
import SpecStock from './components/SpecStock.vue';
import ProductDetail from './components/ProductDetail.vue';
import LogisticsSetting from './components/LogisticsSetting.vue';
import PriceCommission from './components/PriceCommission.vue';
import MarketingSetting from './components/MarketingSetting.vue';
import OtherSetting from './components/OtherSetting.vue';
import { formatRichText } from '@/utils/editorImg';

export default {
  name: 'ProductAdd',
  components: {
    uploadPictures,
    freightTemplate,
    addAttr,
    couponList,
    taoBao,
    goodsList,
    userLabel,
    goodsLabel,
    useLabel,
    BasicInfo,
    SpecStock,
    ProductDetail,
    LogisticsSetting,
    PriceCommission,
    MarketingSetting,
    OtherSetting,
  },
  data() {
    return {
      labelShow: false,
      tagShow: false,
      dataLabel: [],
      headTab: [
        { tit: 'Thông tin cơ bản', name: '1' },
        { tit: 'Phân loại và tồn kho', name: '2' },
        { tit: 'Chi tiết sản phẩm', name: '3' },
        { tit: 'Cài đặt vận chuyển', name: '4' },
        { tit: 'Giá thành viên/Hoa hồng', name: '5' },
        { tit: 'Cài đặt marketing', name: '6' },
        { tit: 'Cài đặt khác', name: '7' },
      ],
      virtual: [
        { tit: 'Sản phẩm thường', id: 0, tit2: 'Giao qua đơn vị vận chuyển' },
        { tit: 'Mã thẻ/Lưu trữ đám mây', id: 1, tit2: 'Giao hàng tự động' },
        { tit: 'Phiếu giảm giá', id: 2, tit2: 'Giao hàng tự động' },
        { tit: 'Sản phẩm ảo', id: 3, tit2: 'Giao hàng ảo' },
      ],
      seletVideo: 0, //Chọn loại video
      customBtn: 0, //Công tắc lời nhắn tùy chỉnh
      content: '',
      contents: '',
      fileUrl: Setting.apiBaseURL + '/file/upload',
      fileUrl2: Setting.apiBaseURL + '/file/video_upload',
      cardUrl: Setting.apiBaseURL + '/file/upload/1',
      upload_type: '', //Loại tải video lên 1 tải lên local 2 3 4 tải lên OSS
      uploadData: {}, // Tham số tải lên
      header: {},
      type: 0,
      modals: false,
      goods_modals: false,
      spinShow: false,
      openSubimit: false,
      virtualList: [
        {
          key: '',
          value: '',
        },
      ],
      // Thiết lập data bảng theo lô
      oneFormBatch: [
        {
          pic: '',
          price: void 0,
          cost: void 0,
          ot_price: void 0,
          stock: void 0,
          bar_code: '',
          bar_code_number: '',
          weight: void 0,
          volume: void 0,
          virtual_list: [],
        },
      ],

      // Dữ liệu phân loại
      formDynamic: {
        attrsName: '',
        attrsVal: '',
      },
      disk_type: 1, //Loại mã thẻ
      tabIndex: 0,
      tabName: '',
      formDynamicNameData: [],
      isBtn: false,
      columns2: columns2,
      columns3: columns3,
      columns: [],
      columnsInstall: [],
      columnsInstal2: [],
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      //Lựa chọn thả xuống lời nhắn tùy chỉnh
      CustomList: CustomList,
      //Nội dung lời nhắn tùy chỉnh
      currentIndex: 0,

      formValidate: {
        disk_info: '', //Loại mã thẻ
        logistics: ['1'], //Chọn phương thức vận chuyển
        freight: 2, //Cài đặt phí vận chuyển
        postage: 0, //Thiết lập số tiền phí vận chuyển
        recommend: [], //Đề xuất sản phẩm
        presale_day: 1, //Thời gian giao hàng đặt trước - Kết thúc
        presale: false, //Bật/tắt sản phẩm đặt trước
        is_limit: false,
        limit_type: 0,
        limit_num: 0,
        vip_product: false, //Công tắc dành riêng cho thành viên trả phí
        custom_form: [], //Lời nhắn tùy chỉnh
        store_name: '',
        cate_id: [],
        label_id: [],
        keyword: '',
        unit_name: '',
        store_info: '',
        image: '',
        recommend_image: '',
        slider_image: [],
        description: '',
        ficti: 0,
        give_integral: 0,
        sort: 0,
        is_show: 1,
        is_gift: 0, // Bật tặng quà
        gift_price: 0,
        is_hot: 0,
        is_benefit: 0,
        is_best: 0,
        is_new: 0,
        is_good: 0,
        is_postage: 0,
        is_sub: [],
        recommend_list: [],
        params_list: [], //Thông số sản phẩm
        virtual_type: 0,
        // is_sub: 0,
        id: 0,
        spec_type: 0,
        is_virtual: 0,
        video_link: '',
        // postage: 0,
        temp_id: '',
        attrs: [],
        items: [
          {
            pic: '',
            price: 0,
            cost: 0,
            ot_price: 0,
            stock: 0,
            bar_code: '',
            bar_code_number: '',
          },
        ],
        activity: ['Mặc định', 'Flash sale', 'Săn giảm giá', 'Mua chung'],
        couponName: [],
        header: [],
        selectRule: '',
        coupon_ids: [],
        command_word: '',
        min_qty: 1,
        label_list: [],
        protection_list: [],
      },
      ruleList: [],
      templateList: [],
      createBnt: true,
      showIput: false,
      manyFormValidate: [],
      // Data bảng quy cách đơn
      oneFormValidate: [
        {
          pic: '',
          price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: '',
          bar_code_number: '',
          weight: 0,
          volume: 0,
          brokerage: 0,
          brokerage_two: 0,
          vip_price: 0,
          virtual_list: [],
          coupon_id: 0,
        },
      ],
      images: [],
      imagesTable: '',
      currentTab: '1',
      isChoice: '',
      loading: false,
      modalPic: false,
      addVirtualModel: false,
      template: false,
      uploadList: [],
      treeSelect: [],
      picTit: '',
      tableIndex: 0,
      ruleValidate: RuleValidate,
      manyBrokerage: undefined,
      manyBrokerageTwo: undefined,
      manyVipPrice: undefined,
      manyVipDiscount: undefined,
      upload: {
        videoIng: false, // Có hiển thị thanh tiến trình không;
      },
      videoIng: false, // Có hiển thị thanh tiến trình không;
      progress: 0, // Thanh tiến trình mặc định 0
      stock: 0,
      disk_info: '',
      videoLink: '',
      attrs: [],
      activity: { 'Mặc định': 'red', 'Flash sale': 'blue', 'Săn giảm giá': 'green', 'Mua chung': 'yellow' },
      couponName: [],
      updateIds: [],
      updateName: [],
      couponIds: '',
      couponNames: [],
      rakeBack: [
        {
          title: 'Hoa hồng cấp 1 (đ)',
          slot: 'brokerage',
          align: 'center',
          width: 95,
        },
        {
          title: 'Hoa hồng cấp 2 (đ)',
          slot: 'brokerage_two',
          align: 'center',
          width: 95,
        },
      ],
      member: [
        {
          title: 'Giá thành viên',
          slot: 'vip_price',
          align: 'center',
          width: 95,
        },
        {
          title: 'Chiết khấu thành viên',
          slot: 'vip_proportion',
          align: 'center',
          width: 95,
        },
      ],
      columnsInstalM: [],
      moveIndex: '',
      addValue: '',
      visible: false,
      typeConfig: [],
      goodsType: [],
      paramsTypeList: [],
      paramsType: null,
      canSel: true, // Kiểm tra thêm ảnh quy cách
      changeAttrValue: '', //Giá trị quy cách đã sửa
      tableKey: 0,
      protectionList: [], // Đảm bảo dịch vụ
      labelList: [],
      tileLabelList: [],
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '120px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
    labelBottom() {
      return this.isMobile ? undefined : '15px';
    },
  },
  watch: {
    typeConfig(val) {
      if (val.length) {
        // Đối với id trong virtual bằng id trong val
        this.goodsType = this.virtual.filter((item) => {
          return val.includes(item.id + '');
        });
      } else {
        this.goodsType = this.virtual;
      }
    },
  },
  beforeRouteUpdate(to, from, next) {
    this.bus.$emit('onTagsViewRefreshRouterView', this.$route.path);
    next();
  },
  created() {
    this.columns = this.columns2.slice(0, 8);
    this.getToken();
  },
  async mounted() {
    if (this.$route.params.id !== '0' && this.$route.params.id) {
      await this.getInfo();
    } else if (this.$route.params.id === '0') {
      this.getProductCache();
    } else {
      this.getproductLabelUseListApi();
    }
    if (this.$route.query.type) {
      this.modals = true;
      this.type = this.$route.query.type;
    } else {
      this.type = 0;
    }
    this.goodsCategory();
    this.productGetRule();
    this.productGetTemplate();
    this.paramsGetTemplate();
    this.uploadType();
    this.productConfig();
    this.watchActivity();
    this.getProtectionList();
  },
  methods: {
    getProductCache() {
      productCache()
        .then((res) => {
          let data = res.data.info;
          this.getproductLabelUseListApi();

          if (!Array.isArray(data)) {
            let cate_id = data.cate_id.map(Number);
            let label_id = data.label_id.map(Number);
            this.attrs = data.items || [];
            let ids = [];
            if (data.coupons) {
              data.coupons.map((item) => {
                ids.push(item.id);
              });
              this.couponName = data.coupons;
            }

            this.formValidate = data;
            this.dataLabel = data.label_id;
            this.formValidate.coupon_ids = ids;
            this.updateIds = ids;
            this.updateName = data.coupons;
            this.formValidate.cate_id = cate_id;
            this.oneFormValidate = data.attrs;
            this.generateHeader(this.attrs);
            this.formValidate.logistics = data.logistics || ['1'];
            this.formValidate.header = [];
            this.manyFormValidate = data.attrs;
            this.spec_type = data.spec_type;
            this.formValidate.is_virtual = data.is_virtual;
            this.formValidate.custom_form = data.custom_form || [];
            if (this.formValidate.custom_form.length != 0) {
              this.customBtn = 1;
            }
            this.attrs.map((item) => {
              if (item.add_pic) this.canSel = false;
            });
            this.virtualbtn(data.virtual_type, 1);
            if (data.spec_type === 0) {
              this.manyFormValidate = [];
            } else {
              this.createBnt = true;
              this.oneFormValidate = [
                {
                  pic: data.image,
                  price: 0,
                  cost: 0,
                  ot_price: 0,
                  stock: 0,
                  bar_code: '',
                  bar_code_number: '',
                  weight: 0,
                  volume: 0,
                  brokerage: 0,
                  brokerage_two: 0,
                  vip_price: 0,
                  virtual_list: [],
                  coupon_id: 0,
                },
              ];
            }
            this.watchActivity();
            this.spinShow = false;
          }
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    getProtectionList() {
      productProtectionListApi({ page: 0, limit: 0, status: 1 }).then((res) => {
        this.protectionList = res.data.list;
      });
    },
    getproductLabelUseListApi() {
      productLabelUseListApi().then((res) => {
        // Hợp nhất tất cả list trong mảng
        this.tileLabelList = res.data.flatMap((item) => item.list);
        let labelList = res.data;
        if (this.formValidate.label_list.length) {
          this.formValidate.label_list.map((el) => {
            labelList.map((re) => {
              re.list.map((label) => {
                if (label.id === el) {
                  label.active = true;
                } else {
                  label.active = false;
                }
              });
            });
          });
        } else {
          labelList.map((el) => {
            el.list.map((label) => {
              label.active = false;
            });
          });
        }
        this.labelList = labelList;
      });
    },
    addProtection() {
      this.$router.push({ path: this.$routeProStr + '/product/protection/list' });
    },
    productConfig() {
      getProductTypeConfig().then((res) => {
        this.typeConfig = res.data;
      });
    },
    beforeUpload(file) {
      return isFileUpload(file);
    },
    // Tải lên theo phần (multipart)
    videoSaveToUrl(file) {
      if (isVideoUpload(file)) {
        uploadByPieces({
          file: file, // Đối tượng video
          pieceSize: 3, // Kích thước từng phần
          success: (data) => {
            this.formValidate.video_link = data.file_path;
            this.progress = 100;
          },
          error: (e) => {
            this.$message.error(e.msg);
          },
          uploading: (chunk, allChunk) => {
            this.videoIng = true;
            let st = Math.floor((chunk / allChunk) * 100);
            this.progress = st;
          },
        });
      }
      return false;
    },
    // Chọn loại/kiểm tra nội dung nhập vào
    virtualbtn(index, type) {
      if (type != 1) {
        if (this.$route.params.id) return this.$message.error('Không hỗ trợ chuyển loại sản phẩm khi sửa sản phẩm');
        this.formValidate.is_sub = [];
        let id = this.$route.params.id;
        if (id) {
          checkActivityApi(id)
            .then((res) => {})
            .catch((res) => {
              this.formValidate.spec_type = this.spec_type;
              this.$message.error(res.msg);
            });
        } else {
          if (this.formValidate.spec_type == 1) {
            this.generate(1);
          }
        }
      }
      // Định nghĩa cấu hình tab cho sản phẩm cơ bản và sản phẩm ảo
      const baseHeadTabs = [
        { tit: 'Thông tin cơ bản', name: '1' },
        { tit: 'Phân loại và tồn kho', name: '2' },
        { tit: 'Chi tiết sản phẩm', name: '3' },
        { tit: 'Cài đặt vận chuyển', name: '4' },
        { tit: 'Giá thành viên/Hoa hồng', name: '5' },
        { tit: 'Cài đặt marketing', name: '6' },
        { tit: 'Cài đặt khác', name: '7' },
      ];
      const virtualHeadTabs = [
        { tit: 'Thông tin cơ bản', name: '1' },
        { tit: 'Phân loại và tồn kho', name: '2' },
        { tit: 'Chi tiết sản phẩm', name: '3' },
        { tit: 'Giá thành viên/Hoa hồng', name: '4' },
        { tit: 'Cài đặt marketing', name: '5' },
        { tit: 'Cài đặt khác', name: '6' },
      ];

      switch (index) {
        case 0: // Sản phẩm thường
          this.formValidate.virtual_type = 0;
          this.formValidate.is_virtual = 0;
          this.headTab = baseHeadTabs;
          break;

        case 1: // Sản phẩm mã thẻ/lưu trữ mạng
          this.formValidate.virtual_type = 1;
          this.formValidate.postage = 0;
          this.formValidate.is_virtual = 1;
          this.headTab = virtualHeadTabs;
          break;

        case 2: // Sản phẩm phiếu giảm giá
          this.formValidate.virtual_type = 2;
          this.formValidate.is_virtual = 1;
          this.headTab = virtualHeadTabs;
          break;

        case 3: // Sản phẩm ảo
          this.formValidate.virtual_type = 3;
          this.formValidate.is_virtual = 1;
          this.headTab = virtualHeadTabs;
          break;
      }
    },
    // Thêm danh mục
    addCate() {
      this.$modalForm(productCreateApi()).then(() => this.goodsCategory());
    },
    // Chọn phương thức vận chuyển
    logisticsBtn(e) {
      this.formValidate.logistics = e;
    },
    // Thêm nhãn
    addLabel() {
      this.$modalForm(userLabelAddApi(0)).then(() => this.userLabel());
    },
    // Chọn nhãn
    addGoodsTag() {
      this.tagShow = true;
    },
    // Lời nhắn tùy chỉnh Bật tắt
    customMessBtn(e) {
      if (!e) {
        this.formValidate.custom_form = [];
      }
      this.customBtn = e;
    },
    // Lời nhắn tùy chỉnh Thêm biểu mẫu mới
    addcustom() {
      if (this.formValidate.custom_form.length > 9) {
        this.$message.warning('Chỉ được thêm tối đa 10 mục');
      } else {
        this.formValidate.custom_form.push({
          title: '',
          label: 'text',
          value: '',
          status: false,
        });
      }
    },
    // Xóa
    delcustom(index) {
      this.formValidate.custom_form.splice(index, 1);
    },
    // Ngày cụ thể đặt trước
    onchangeTime(e) {
      this.formValidate.presale_time = e;
    },
    // Chi tiết sản phẩm
    getEditorContent(data) {
      this.content = data;
    },
    cancel() {
      this.modals = false;
    },
    // Token trong header khi tải lên
    getToken() {
      this.header['Authori-zation'] = 'Bearer ' + getCookies('token');
    },
    // Nhập mã thẻ
    upFile(res) {
      importCard({ file: res.data.src }).then((res) => {
        this.virtualList = this.virtualList.concat(res.data);
      });
    },
    //Lấy loại tải lên video
    uploadType() {
      uploadType().then((res) => {
        this.upload_type = res.data.upload_type;
      });
    },
    // Khởi tạo hiển thị dữ liệu
    infoData(data, isCopy) {
      let cate_id = data.cate_id.map(Number);
      let label_id = data.label_id.map(Number);
      this.attrs = data.items || [];
      let ids = [];
      data.coupons.map((item) => {
        ids.push(item.id);
      });
      this.formValidate = data;
      this.seletVideo = data.seletVideo;
      this.contents = data.description;
      this.couponName = data.coupons;
      this.formValidate.coupon_ids = ids;
      this.updateIds = ids;
      this.dataLabel = data.label_id;
      this.updateName = data.coupons;
      this.virtualbtn(data.virtual_type, 1);
      this.formValidate.logistics = data.logistics || ['1'];
      this.formValidate.custom_form = data.custom_form || [];
      if (this.formValidate.custom_form.length != 0) {
        this.customBtn = 1;
      }
      this.formValidate.cate_id = cate_id;
      if (data.attr) {
        this.oneFormValidate = [data.attr];
        this.oneFormValidate[0].vip_proportion = (
          (this.oneFormValidate[0].vip_price / this.oneFormValidate[0].price) *
          100
        ).toFixed(2);
      }
      this.getproductLabelUseListApi();

      this.formValidate.header = [];
      this.spec_type = data.spec_type;
      this.formValidate.spec_type = this.spec_type;
      this.formValidate.is_virtual = data.is_virtual;
      this.attrs.map((item) => {
        if (item.add_pic) this.canSel = false;
      });
      if (data.spec_type === 0) {
        this.manyFormValidate = [];
      } else {
        this.createBnt = true;
        this.oneFormValidate = [
          {
            pic: '',
            price: 0,
            cost: 0,
            ot_price: 0,
            stock: 0,
            bar_code: '',
            bar_code_number: '',
            weight: 0,
            volume: 0,
            brokerage: 0,
            brokerage_two: 0,
            vip_price: 0,
            virtual_list: [],
            coupon_id: 0,
          },
        ];

        this.generateHeader(this.attrs);
        this.manyFormValidate = [...this.oneFormBatch, ...data.attrs];
      }

      setTimeout((e) => {
        this.checkAllGroup(data.is_sub);
      }, 1000);
      this.watchActivity();
    },
    //Đóng popup Taobao và tạo dữ liệu;
    onClose(data) {
      this.modals = false;
      this.infoData(data, 1);
    },

    checkMove(evt) {
      this.moveIndex = evt.draggedContext.index;
    },
    end() {
      this.moveIndex = '';
      this.generate(1);
    },
    // Thiết lập riêng phần thành viên
    checkAllGroupChange(data) {
      this.checkAllGroup(data);
    },
    checkAllGroup(data) {
      let endLength = this.attrs.length + 3;
      if (this.formValidate.spec_type === 0) {
        if (data.length === 2) {
          this.columnsInstall = this.columns2.slice(0, endLength).concat(this.rakeBack).concat(this.member);
        } else if (data.indexOf(0) > -1) {
          this.columnsInstall = this.columns2.slice(0, endLength).concat(this.member);
        } else if (data.indexOf(1) > -1) {
          this.columnsInstall = this.columns2.slice(0, endLength).concat(this.rakeBack);
        } else {
          this.columnsInstall = this.columns2.slice(0, endLength);
        }
      } else {
        if (data.length === 2) {
          this.columnsInstal2 = this.columnsInstalM
            .slice(0, endLength + 1)
            .concat(this.rakeBack)
            .concat(this.member);
        } else if (data.indexOf(0) > -1) {
          this.columnsInstal2 = this.columnsInstalM.slice(0, endLength).concat(this.member);
        } else if (data.indexOf(1) > -1) {
          this.columnsInstal2 = this.columnsInstalM.slice(0, endLength).concat(this.rakeBack);
        } else {
          this.columnsInstal2 = this.columnsInstalM.slice(0, endLength);
        }
      }
    },
    // Thêm phiếu giảm giá
    addCoupon() {
      this.$refs.couponTemplates.isTemplate = true;
      this.$refs.couponTemplates.tableList();
    },
    // Xem phiếu giảm giá trong quy cách
    see(data, name, index) {
      this.tabName = name;
      this.tabIndex = index;

      if (this.formValidate.virtual_type === 1) {
        if (data.disk_info != '') {
          this.disk_type = 1;
          this.disk_info = data.disk_info;
          this.stock = data.stock;
        } else if (data.virtual_list.length) {
          this.disk_type = 2;
          this.virtualList = data.virtual_list;
        }
        this.addVirtualModel = true;
      } else {
        this.$refs.goodsCoupon.isTemplate = true;
        this.$refs.goodsCoupon.tableList(3);
      }
    },
    // Sửa tỷ lệ chia hoa hồng
    changeDiscount(index, type = 'manyFormValidate') {
      // Dựa theo tỷ lệ chia hoa hồng vip_proportion để sửa giá thành viên, giữ 2 chữ số thập phân
      this[type][index].vip_price = (this[type][index].price * (this[type][index].vip_proportion / 100)).toFixed(2);
    },
    // Sửa giá thành viên
    changeVipPrice(index, type = 'manyFormValidate') {
      // Tính tỷ lệ chia hoa hồng dựa theo giá thành viên
      this[type][index].vip_proportion = ((this[type][index].vip_price / this[type][index].price) * 100).toFixed(2);
    },
    // Thêm phiếu giảm giá
    addGoodsCoupon(index, name) {
      this.tabIndex = index;
      this.tabName = name;
      this.$refs.goodsCoupon.isTemplate = true;
      this.$refs.goodsCoupon.tableList(3);
    },
    addVirtual(index, name) {
      this.tabIndex = index;
      this.tabName = name;
      this.addVirtualModel = true;
    },
    // Gửi thông tin mã thẻ
    upVirtual() {
      if (this.disk_type == 2) {
        for (let i = 0; i < this.virtualList.length; i++) {
          const element = this.virtualList[i];
          if (!element.value) {
            this.$message.error('Vui lòng nhập tất cả mã thẻ');
            return;
          }
        }
        this.$set(this[this.tabName][this.tabIndex], 'virtual_list', this.virtualList);
        this.$set(this[this.tabName][this.tabIndex], 'stock', this.virtualList.length);
        this.virtualList = [
          {
            key: '',
            value: '',
          },
        ];
        this.$set(this[this.tabName][this.tabIndex], 'disk_info', '');
      } else {
        if (!this.disk_info.length) {
          return this.$message.error('Vui lòng nhập thông tin mã thẻ');
        }
        if (!this.stock) {
          return this.$message.error('Vui lòng nhập số lượng tồn kho');
        }
        this.$set(this[this.tabName][this.tabIndex], 'stock', Number(this.stock));
        this.$set(this[this.tabName][this.tabIndex], 'stock', Number(this.stock));
        this.$set(this[this.tabName][this.tabIndex], 'disk_info', this.disk_info);
        this.$set(this[this.tabName][this.tabIndex], 'virtual_list', []);
      }
      this.addVirtualModel = false;
      this.closeVirtual();
    },
    //  Khởi tạo thông tin dữ liệu mã thẻ
    closeVirtual() {
      this.addVirtualModel = false;
      this.virtualList = [
        {
          key: '',
          value: '',
        },
      ];
      this.disk_info = '';
      this.stock = 0;
    },
    //Loại bỏ trùng lặp trong mảng object;
    uniqueArray(arr) {
      const seen = {};
      return arr.filter((item) => {
        const key = JSON.stringify(item); // Dùng JSON.stringify để tạo khóa duy nhất
        if (seen[key]) {
          return false;
        } else {
          seen[key] = true;
          return true;
        }
      });
    },
    // Lấy dữ liệu id phiếu giảm giá
    nameId(id, names) {
      this.formValidate.coupon_ids = id;
      this.couponName = this.uniqueArray(names);
    },
    // Lấy thông tin phiếu giảm giá
    goodsCouponId(data) {
      this.$set(this[this.tabName][this.tabIndex], 'coupon_id', data.id);
      this.$set(this[this.tabName][this.tabIndex], 'coupon_name', data.title);
      this.$refs.goodsCoupon.isTemplate = false;
    },
    handleClose(name) {
      let index = this.couponName.indexOf(name);
      this.couponName.splice(index, 1);
      let couponIds = this.formValidate.coupon_ids;
      couponIds.splice(index, 1);
      this.updateIds = couponIds;
      this.updateName = this.couponName;
    },
    // Thêm mẫu phí vận chuyển
    addTemp() {
      this.$refs.templates.isTemplate = true;
    },
    addVideo() {
      this.$videoModal((e) => {
        this.formValidate.video_link = e;
      });
    },
    // Xóa video;
    delVideo() {
      this.$set(this.formValidate, 'video_link', '');
      this.$set(this, 'progress', 0);
      this.videoIng = false;
      this.upload.videoIng = false;
    },
    zh_uploadFile() {
      if (this.seletVideo == 1) {
        this.formValidate.video_link = this.videoLink;
      } else {
        this.$refs.refid.click();
      }
    },
    // Tải lên video
    zh_uploadFile_change(evfile) {
      let suffix = evfile.target.files[0].name.substr(evfile.target.files[0].name.indexOf('.'));
      if (suffix.indexOf('.mp4') === -1) {
        return this.$message.error('Chỉ có thể tải lên tệp MP4');
      }
      let types = {
        key: evfile.target.files[0].name,
        contentType: evfile.target.files[0].type,
      };
      productGetTempKeysApi(types)
        .then((res) => {
          this.$videoCloud
            .videoUpload({
              type: res.data.type,
              evfile: evfile,
              res: res,
              uploading(status, progress) {
                this.upload.videoIng = status;
                if (res.status == 200) {
                  this.progress = 100;
                }
              },
            })
            .then((res) => {
              this.formValidate.video_link = res.url;
              this.$message.success('Tải lên video thành công');
              this.upload.videoIng = false;
            })
            .catch((res) => {
              this.$message.error(res);
            });
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Trang trước;
    upTab() {
      this.currentTab = (Number(this.currentTab) - 1).toString();
    },
    // Trang sau;
    downTab() {
      this.currentTab = (Number(this.currentTab) + 1).toString();
    },
    // Hàm callback popup thuộc tính;
    userSearchs() {
      this.productGetRule();
    },
    // Thêm quy tắc;
    addRule() {
      this.$refs.addattr.modal = true;
    },
    // Thiết lập chia hoa hồng theo lô;
    brokerageSetUp() {
      if (this.formValidate.is_sub.indexOf(1) > -1) {
        if (this.manyBrokerage <= 0 || this.manyBrokerageTwo <= 0) {
          return this.$message.error('Vui lòng nhập số tiền hoa hồng rồi mới thêm hàng loạt');
        }
      } else if (this.formValidate.is_sub.indexOf(0) > -1) {
        if (this.manyVipPrice <= 0) {
          return this.$message.error('Vui lòng nhập giá thành viên rồi mới thêm hàng loạt');
        }
      }
      if (this.formValidate.is_sub.length === 2) {
        if (this.manyBrokerage <= 0 || this.manyBrokerageTwo <= 0) {
          return this.$message.error('Vui lòng nhập đầy đủ số tiền rồi mới thêm hàng loạt');
        }
        if (this.manyVipPrice > 0 && this.manyVipDiscount > 0) {
          return this.$message.error('Chỉ được chọn thêm một trong hai: giá thành viên hoặc chiết khấu thành viên');
        }
      }
      for (let val of this.manyFormValidate) {
        this.manyBrokerage != undefined && this.$set(val, 'brokerage', this.manyBrokerage);
        this.manyBrokerageTwo != undefined && this.$set(val, 'brokerage_two', this.manyBrokerageTwo);
        if (this.manyVipPrice != undefined) {
          this.$set(val, 'vip_price', this.manyVipPrice);
          this.$set(val, 'vip_proportion', ((val.vip_price / val.price) * 100).toFixed(2));
        } else {
          this.$set(val, 'vip_proportion', this.manyVipDiscount);
          this.$set(val, 'vip_price', (val.price * (this.manyVipDiscount / 100)).toFixed(2));
        }
      }
    },
    // Thiết lập giá thành viên theo lô
    vipPriceSetUp() {
      if (this.manyVipPrice <= 0) {
        return this.$message.error('Vui lòng nhập giá thành viên rồi mới thêm hàng loạt');
      } else {
        for (let val of this.manyFormValidate) {
          this.$set(val, 'vip_price', this.manyVipPrice);
        }
      }
    },
    // Thêm mã thẻ
    handleAdd() {
      this.virtualList.push({
        key: '',
        value: '',
      });
    },
    // Khởi tạo thông tin mã thẻ
    initVirtualData(status) {
      this.virtualList = [
        {
          key: '',
          value: '',
        },
      ];
    },
    removeVirtual(index) {
      this.virtualList.splice(index, 1);
    },
    // Xóa trắng thông tin quy cách theo lô
    batchDel() {
      this.oneFormBatch = [
        {
          pic: '',
          price: void 0,
          cost: void 0,
          ot_price: void 0,
          stock: void 0,
          bar_code: '',
          bar_code_number: '',
          weight: void 0,
          volume: void 0,
          virtual_list: [],
        },
      ];
    },
    confirm(name) {
      this.createBnt = true;
      this.formValidate.selectRule = name;
      this.attrs = [];
      if (this.formValidate.selectRule.trim().length <= 0) {
        return this.$message.error('Vui lòng chọn thuộc tính');
      }
      this.ruleList.forEach((item, index) => {
        if (item.rule_name === this.formValidate.selectRule) {
          this.attrs = [...item.rule_value];
        }
      });
      this.canSel = true;
      this.generateAttr(this.attrs);
    },
    // Chọn mẫu quy cách
    handleCommand(e) {},
    // Lấy mẫu thuộc tính sản phẩm;
    productGetRule() {
      productGetRuleApi().then((res) => {
        this.ruleList = res.data;
      });
    },
    // Lấy mẫu phí vận chuyển;
    productGetTemplate() {
      productGetTemplateApi().then((res) => {
        this.templateList = res.data;
      });
    },
    paramsGetTemplate() {
      paramListApi().then((res) => {
        this.paramsTypeList = res.data.list;
      });
    },
    changeParamsType(e) {
      e ? this.getParams(e) : (this.formValidate.params_list = []);
    },
    getParams(id) {
      paramInfoApi(id).then((res) => {
        this.formValidate.params_list = res.data.value;
      });
    },
    isSubset(arr1, arr2) {
      // Chuyển mảng thành Set để kiểm tra chứa hiệu quả hơn
      const set1 = new Set(arr1);
      const set2 = new Set(arr2);

      // Kiểm tra mỗi phần tử trong set2 có nằm trong set1 hay không
      for (let elem of set2) {
        if (!set1.has(elem)) {
          return false;
        }
      }
      return true;
    },
    // Thêm theo lô
    batchAdd() {
      let arr = [];
      for (let val of this.attrs) {
        if (this.oneFormBatch[0][val.value]) {
          arr.push(this.oneFormBatch[0][val.value]);
        }
      }

      // Thiết lập thuộc tính quy cách sản phẩm theo lô
      const batchFields = [
        'pic',
        'price',
        'cost',
        'ot_price',
        'stock',
        'weight',
        'volume',
        'bar_code',
        'bar_code_number',
      ];
      // const defaultFields = ['bar_code', 'bar_code_number'];

      for (let val of this.manyFormValidate) {
        const batch = this.oneFormBatch[0];
        // Nếu có điều kiện lọc và thỏa điều kiện, hoặc khi không có điều kiện lọc
        if (!arr.length || this.isSubset(val.attr_arr, arr)) {
          // Thiết lập các trường có giá trị theo lô
          batchFields.forEach((field) => {
            if (batch[field] && batch[field] !== undefined) {
              if (field === 'pic' && batch[field]) {
                this.$set(val, field, batch[field]);
              } else if (field != 'pic') {
                this.$set(val, field, batch[field]);
              }
            }
          });

          // Thiết lập trường mặc định
          // defaultFields.forEach((field) => {
          //   this.$set(val, field, batch[field]);
          // });
        }
      }
    },
    changeSpecImg(arr, img) {
      // Kiểm tra có tồn tại ảnh quy cách hay không
      let isHas = false;
      for (let i = 1; i < this.manyFormValidate.length; i++) {
        let item = this.manyFormValidate[i];
        if (item.pic && this.isSubset(item.attr_arr, arr)) {
          isHas = true;
          break;
        }
      }
      if (isHas) {
        this.$confirm('Có thể đồng bộ sửa ảnh của phân loại này ở bên dưới, bạn có chắc muốn thay thế?', 'Thông báo', {
          confirmButtonText: 'Thay thế',
          cancelButtonText: 'Để sau',
          type: 'warning',
        })
          .then(() => {
            for (let val of this.manyFormValidate) {
              if (this.isSubset(val.attr_arr, arr)) {
                this.$set(val, 'pic', img);
              }
            }
          })
          .catch(() => {});
      } else {
        for (let val of this.manyFormValidate) {
          if (this.isSubset(val.attr_arr, arr)) {
            this.$set(val, 'pic', img);
          }
        }
      }
    },
    // Tạo ngay
    generate(type, isCopy, arr) {
      this.manyFormValidate = [];
      this.formValidate.header = [];
    },
    clearAttr() {
      this.formDynamic.attrsName = '';
      this.formDynamic.attrsVal = '';
    },

    // Xóa quy cách
    handleRemoveRole(index) {
      this.attrs.splice(index, 1);
      this.manyFormValidate.splice(index, 1);
      if (!this.attrs.length) {
        this.formValidate.header = [];
        this.manyFormValidate = [];
      } else {
        this.generateAttr(this.attrs);
      }
    },
    // Xóa thuộc tính tương ứng trong bảng
    delAttrTable(val) {
      for (let i = 0; i < this.manyFormValidate.length; i++) {
        let item = this.manyFormValidate[i];
        if (item.attr_arr && item.attr_arr.includes(val)) {
          this.manyFormValidate.splice(i, 1);
          i--;
        }
      }
    },
    // Xóa thuộc tính
    handleRemove2(item, index, val) {
      // Xóa giá trị thuộc tính có title = item.value trong manyFormValidate
      item.splice(index, 1);
      // this.generateAttr(this.attrs);
      this.delAttrTable(val);
    },
    // Thêm quy cách mới
    handleAddRole() {
      let data = {
        value: this.formDynamic.attrsName,
        add_pic: 0,
        detail: [],
      };
      this.attrs.push(data);
    },
    handleAddParams() {
      let data = {
        name: '',
        value: '',
      };
      this.formValidate.params_list.push(data);
    },
    handleSaveAsTemplate() {
      this.$prompt('', 'Vui lòng nhập tên mẫu', {
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
      })
        .then(({ value }) => {
          let spec = this.attrs.map((item) => {
            return {
              value: item.value,
              detail: item.detail.map((e) => e.value),
            };
          });
          let formDynamic = {
            rule_name: value,
            spec: spec,
          };
          ruleAddApi(formDynamic, 0)
            .then((res) => {
              this.$message.success(res.msg);
              this.productGetRule();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        })
        .catch(() => {});
    },
    // Thêm một thuộc tính mới
    addOneAttr(val, val2) {
      this.generateAttr(this.attrs, val2);
    },
    handleFocus(val) {
      this.changeAttrValue = val;
    },
    handleBlur() {
      this.changeAttrValue = '';
    },
    handleSelImg(item) {
      this.$imgModal((e) => {
        item.pic = e.att_dir;
        this.changeSpecImg([item.value], e.att_dir);
      });
    },
    handleRemoveImg(item) {
      item.pic = '';
    },
    // Tên quy cách thay đổi
    attrChangeValue(i, val) {
      if (val.trim().length && this.attrs[i].detail.length) {
        this.generateHeader(this.attrs);
        if (this.manyFormValidate.length) {
          this.manyFormValidate.map((item, i) => {
            if (i > 0) {
              if (Object.keys(item.detail).includes(this.changeAttrValue)) {
                item.detail[val] = item.detail[this.changeAttrValue];
                item[val] = item[this.changeAttrValue];
                delete item.detail[this.changeAttrValue];
                delete item[this.changeAttrValue];
              }
            }
          });
          this.changeAttrValue = val;
        }
      } else {
        this.generateAttr(this.attrs);
      }
    },
    // Giá trị quy cách thay đổi
    attrDetailChangeValue(val, i) {
      if (this.manyFormValidate.length) {
        let key = this.attrs[i].value;
        this.manyFormValidate.map((item, i) => {
          if (i > 0) {
            if (Object.keys(item.detail).includes(key) && item.detail[key] === this.changeAttrValue) {
              item.detail[key] = val;
              let index = item.attr_arr.findIndex((item) => item === this.changeAttrValue);
              item.attr_arr[index] = val;
            }
          }
        });
        this.changeAttrValue = val;
      } else {
        this.generateAttr(this.attrs, 1);
      }
    },
    // Công tắc thêm ảnh quy cách
    addPic(e, i) {
      if (e) {
        this.attrs.map((item, ii) => {
          if (ii !== i) {
            this.$set(item, 'add_pic', 0);
          }
        });
        this.canSel = false;
      } else {
        this.canSel = true;
      }
    },
    // Sau khi kéo thả sắp xếp quy cách
    onMoveSpec() {
      this.generateAttr(this.attrs);
    },
    changeCurrentIndex(i) {
      this.currentIndex = i;
    },
    // Tạo tiêu đề bảng quy cách sản phẩm
    generateHeader(data) {
      let specificationsColumns = data.map((item) => ({
        title: item.value,
        key: item.value,
        minWidth: 140,
        fixed: 'left',
      }));
      let arr;
      if ([1, 2].includes(Number(this.formValidate.virtual_type))) {
        arr = [...specificationsColumns, ...VirtualTableHead];
        // Tìm slot bằng fictitious, đổi title thành tên quy cách
        this.formValidate.header.map((item) => {
          if (item.slot === 'fictitious') {
            item.title = this.formValidate.virtual_type == 1 ? 'Thêm mã thẻ/lưu trữ đám mây' : 'Chọn phiếu giảm giá';
          }
        });
      } else if (this.formValidate.virtual_type == 3) {
        arr = [...specificationsColumns, ...VirtualTableHead2];
      } else {
        arr = [...specificationsColumns, ...GoodsTableHead];
      }
      this.$set(this.formValidate, 'header', arr);
      this.tableKey += 1;
      this.columnsInstalM = arr;
    },
    /*
     * Tạo thuộc tính
     * @param {Array} data Dữ liệu phân loại
     * */
    generateAttr(data, val) {
      this.generateHeader(data);
      const combinations = this.generateCombinations(data);
      console.log('Tổng số tổ hợp phân loại:' + combinations.length);
      const virtualType = this.formValidate.virtual_type;
      // Nếu số lượng combinations vượt quá 500 thì tạo thuộc tính theo từng lô
      let rows = [];
      if (combinations.length > 500) {
        const batchSize = Math.ceil(combinations.length / 500);
        for (let i = 0; i < combinations.length; i += batchSize) {
          setTimeout((e) => {
            let d = this.generateAttrBatch(data, combinations.slice(i, i + batchSize), val);
            rows = [...rows, ...d];
            this.manyFormValidate = [...this.oneFormBatch, ...rows];
          }, 0);
        }
      } else {
        rows = this.generateAttrBatch(data, combinations, val);
        this.manyFormValidate = [...this.oneFormBatch, ...rows];
      }
    },
    // Tạo thuộc tính theo lô
    generateAttrBatch(data, combinations, val) {
      const existingItems = this.manyFormValidate.slice(1); // Loại trừ dữ liệu mặc định của mục đầu tiên

      const rows = combinations.map((combination) => {
        const row = {
          attr_arr: combination,
          detail: {},
          title: '',
          key: '',
          price: 0,
          pic: '',
          ot_price: 0,
          cost: 0,
          stock: 0,
          is_show: 1,
          is_default_select: 0,
          unique: '',
          weight: '',
          volume: '',
          brokerage: 0,
          brokerage_two: 0,
          vip_price: 0,
          vip_proportion: 0,
        };

        // Thiết lập thuộc tính liên quan đến loại ảo
        if (this.formValidate.virtual_type === 1) {
          row.virtual_list = [];
          row.disk_info = '';
        } else if (this.formValidate.virtual_type === 2) {
          row.coupon_id = 0;
          row.coupon_name = '';
        }

        // Xử lý thuộc tính quy cách
        data.forEach((item, i) => {
          const value = combination[i];
          row[item.value] = value;
          row.title = item.value;
          row.key = item.value;
          row.detail[item.value] = value;

          // Tìm mục quy cách hiện có khớp
          const matchedItem = existingItems.find((item) => item.attr_arr && arraysEqual(item.attr_arr, combination));

          if (matchedItem) {
            Object.assign(row, {
              price: matchedItem.price,
              cost: matchedItem.cost,
              ot_price: matchedItem.ot_price,
              stock: matchedItem.stock,
              pic: matchedItem.pic,
              unique: matchedItem.unique || '',
              weight: matchedItem.weight || '',
              volume: matchedItem.volume || '',
              is_show: matchedItem.is_show || 1,
              is_default_select: matchedItem.is_default_select || 0,
              volume: matchedItem.volume || 0,
              bar_code_number: matchedItem.bar_code_number || 0,
              is_virtual: matchedItem.is_virtual,
              brokerage: matchedItem.brokerage,
              brokerage_two: matchedItem.brokerage_two,
              vip_price: matchedItem.vip_price,
              vip_proportion: matchedItem.vip_proportion,
            });

            if (this.formValidate.virtual_type === 1) {
              row.virtual_list = matchedItem.virtual_list;
              row.disk_info = matchedItem.disk_info;
            } else if (this.formValidate.virtual_type === 2 && matchedItem.coupon_id) {
              row.coupon_id = matchedItem.coupon_id;
              row.coupon_name = matchedItem.coupon_name;
            }
          } else if (item.add_pic && combination.includes(val)) {
            const picItem = item.detail.find((e) => combination.includes(e.value));
            if (picItem) row.pic = picItem.pic;
          }
        });
        return row;
      });
      return rows;
    },
    // Chuyển đổi quy cách chọn mặc định
    changeDefaultSelect(e, index) {
      // Một cái bật, cái khác tắt
      this.manyFormValidate.map((item, i) => {
        if (i !== index) {
          item.is_default_select = 0;
        }
      });
      if (e) this.manyFormValidate[index].is_show = 1;
    },
    // Thay đổi có hiển thị hay không
    changeDefaultShow(index) {
      // Nếu bật chọn mặc định thì không thể ẩn
      if (this.manyFormValidate[index].is_default_select === 1) {
        this.manyFormValidate[index].is_show = 1;
        this.$message.error('Không thể ẩn phân loại mặc định');
      }
    },
    // Tạo tổ hợp quy cách
    generateCombinations(arr, prefix = []) {
      if (arr.length === 0) {
        return [prefix];
      }
      const [first, ...rest] = arr;
      return first.detail.flatMap((detail) => this.generateCombinations(rest, [...prefix, detail.value]));
    },
    // Thêm thuộc tính
    createAttr(num, idx) {
      if (num) {
        // Kiểm tra có tồn tại thuộc tính giống nhau hay không
        var isExist = this.attrs[idx].detail.some((item) => item.value === num);
        if (isExist) {
          this.$message.error('Giá trị phân loại đã tồn tại');
          return;
        }
        this.attrs[idx].detail.push({ value: num, pic: '' });
        if (this.manyFormValidate.length) {
          this.addOneAttr(this.attrs[idx].value, num);
        } else {
          this.generateAttr(this.attrs);
        }

        this.$refs.specStock.$refs['popoverRef_' + idx][0].doClose(); //Đóng lại
        this.clearAttr();
        setTimeout(() => {
          if (this.$refs.specStock.$refs['popoverRef_' + idx]) {
            //Trọng tâm là hai câu sau
            this.$refs.specStock.$refs['popoverRef_' + idx][0].doShow(); //Mở ra
            //Trọng tâm là hai câu trên
          }
        }, 20);
      } else {
        this.$refs.specStock.$refs['popoverRef_' + idx][0].doClose(); //Đóng lại
      }
    },
    handleShowPop(index) {
      this.$refs.specStock.$refs['inputRef_' + index][0].focus();
    },
    // Danh mục sản phẩm;
    goodsCategory() {
      cascaderListApi(1)
        .then((res) => {
          this.treeSelect = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thay đổi quy cách
    changeSpec() {
      this.formValidate.is_sub = [];
      let id = this.$route.params.id;
      if (id) {
        checkActivityApi(id)
          .then((res) => {})
          .catch((res) => {
            this.formValidate.spec_type = this.spec_type;
            this.$message.error(res.msg);
          });
      }
    },
    // Chi tiết
    getInfo() {
      this.spinShow = true;
      productInfoApi(this.$route.params.id)
        .then(async (res) => {
          let data = res.data.productInfo;
          this.infoData(data);
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
    handleRemove(i) {
      this.images.splice(i, 1);
      this.formValidate.slider_image.splice(i, 1);
      this.oneFormValidate[0].pic = this.formValidate.slider_image[0];
    },
    // Đóng modal tải ảnh lên
    changeCancel(msg) {
      this.modalPic = false;
    },
    // Bấm ảnh sản phẩm
    modalPicTap(tit, picTit = '', index = 0) {
      this.modalPic = true;
      this.isChoice = tit === 'dan' ? 'Chọn một' : 'Chọn nhiều';
      this.picTit = picTit;
      this.tableIndex = index;
    },
    // Lấy thông tin một ảnh
    getPic(pc) {
      switch (this.picTit) {
        case 'danFrom':
          this.formValidate.image = pc.att_dir;
          if (!this.$route.params.id) {
            if (this.formValidate.spec_type === 0) {
              this.oneFormValidate[0].pic = pc.att_dir;
            } else {
              this.manyFormValidate.map((item) => {
                item.pic = pc.att_dir;
              });
              this.oneFormBatch[0].pic = pc.att_dir;
            }
          }
          break;
        case 'danTable':
          this.oneFormValidate[this.tableIndex].pic = pc.att_dir;
          break;
        case 'duopi':
          this.oneFormBatch[this.tableIndex].pic = pc.att_dir;
          break;
        case 'recommend_image':
          this.formValidate.recommend_image = pc.att_dir;
          break;
        default:
          if (this.manyFormValidate.length) this.manyFormValidate[this.tableIndex].pic = pc.att_dir;
      }
      this.modalPic = false;
    },
    deleteRow(index) {
      this.formValidate.params_list.splice(index, 1);
    },
    // Lấy thông tin nhiều ảnh
    getPicD(pc) {
      this.images = pc;
      this.images.map((item) => {
        this.formValidate.slider_image.push(item.att_dir);
        this.formValidate.slider_image = this.formValidate.slider_image.splice(0, 10);
      });
      this.oneFormValidate[0].pic = this.formValidate.slider_image[0];
      this.modalPic = false;
    },
    // Gửi
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.formValidate.type = this.type;
          let arr = this.formValidate.spec_type === 0 ? this.oneFormValidate : this.manyFormValidate;
          let item = JSON.parse(JSON.stringify(arr));
          if (this.formValidate.spec_type === 1) {
            if (item.length < 2) return this.$message.warning('Phân loại sản phẩm - số lượng phân loại tối thiểu là 1');
            // Xóa mục đầu tiên
            item.shift();
          }
          for (let i = 0; i < item.length; i++) {
            if (item[i].stock > 1000000) {
              return this.$message.error('Phân loại và tồn kho - tồn kho vượt quá giới hạn hệ thống (1000000)');
            }
          }
          if (this.formValidate.is_sub[0] === 1) {
            for (let i = 0; i < item.length; i++) {
              if (item[i].brokerage === null || item[i].brokerage_two === null) {
                return this.$message.error('Cài đặt marketing - hoa hồng cấp 1 và cấp 2 không được để trống');
              }
            }
          } else {
            for (let i = 0; i < item.length; i++) {
              if (item[i].vip_price === null) {
                return this.$message.error('Cài đặt marketing - giá thành viên không được để trống');
              }
            }
          }
          if (this.formValidate.is_sub.length === 2) {
            for (let i = 0; i < item.length; i++) {
              if (item[i].brokerage === null || item[i].brokerage_two === null || item[i].vip_price === null) {
                return this.$message.error('Cài đặt marketing - hoa hồng cấp 1, cấp 2 và giá thành viên không được để trống');
              }
            }
          }
          if (this.formValidate.freight == 3 && !this.formValidate.temp_id) {
            return this.$message.warning('Thông tin sản phẩm - mẫu phí vận chuyển không được để trống');
          }
          let activeIds = [];
          this.dataLabel.forEach((item) => {
            activeIds.push(item.id);
          });
          this.formValidate.label_id = activeIds;
          if (this.openSubimit) return;
          this.openSubimit = true;
          this.formValidate.description = formatRichText(this.content);
          if (this.formValidate.spec_type === 0) {
            this.formValidate.attrs = item;
            this.formValidate.header = [];
            this.formValidate.items = [];
            this.formValidate.is_copy = 0;
          } else {
            this.formValidate.items = this.attrs;
            this.formValidate.attrs = item;
            this.formValidate.is_copy = 1;
          }
          productAddApi(this.formValidate)
            .then(async (res) => {
              this.openSubimit = false;
              this.$message.success(res.msg);
              if (this.$route.params.id === '0') {
                cacheDelete().catch((err) => {
                  this.$message.error(err.msg);
                });
              }
              setTimeout(() => {
                this.openSubimit = false;
                this.$router.push({ path: this.$routeProStr + '/product/product_list' });
              }, 500);
            })
            .catch((res) => {
              setTimeout((e) => {
                this.openSubimit = false;
              }, 1000);
              this.$message.error(res.msg);
            });
        } else {
          if (!this.formValidate.store_name) {
            return this.$message.warning('Thông tin sản phẩm - tên sản phẩm không được để trống');
          } else if (!this.formValidate.cate_id.length) {
            return this.$message.warning('Thông tin sản phẩm - danh mục sản phẩm không được để trống');
          } else if (!this.formValidate.unit_name) {
            return this.$message.warning('Thông tin sản phẩm - đơn vị sản phẩm không được để trống');
          } else if (!this.formValidate.slider_image.length) {
            return this.$message.warning('Thông tin sản phẩm - ảnh trình chiếu sản phẩm không được để trống');
          } else if (!this.formValidate.logistics.length && !this.formValidate.virtual_type) {
            return this.$message.warning('Cài đặt vận chuyển - chọn ít nhất một phương thức vận chuyển');
          } else if (!this.formValidate.temp_id && this.formValidate.freight == 3) {
            return this.$message.warning('Thông tin sản phẩm - mẫu phí vận chuyển không được để trống');
          }
        }
      });
    },
    changeTemplate(msg) {
      this.template = msg;
    },
    // Kiểm tra form
    validate(prop, status, error) {
      if (status === false) {
        this.$message.warning(error);
      }
    },
    // Di chuyển
    handleDragStart(e, item) {
      this.dragging = item;
    },
    handleDragEnd(e, item) {
      this.dragging = null;
    },
    handleDragOver(e) {
      e.dataTransfer.dropEffect = 'move';
    },
    handleDragEnter(e, item) {
      e.dataTransfer.effectAllowed = 'move';
      if (item === this.dragging) {
        return;
      }
      const newItems = [...this.formValidate.slider_image];
      const src = newItems.indexOf(this.dragging);
      const dst = newItems.indexOf(item);
      newItems.splice(dst, 0, ...newItems.splice(src, 1));
      this.formValidate.slider_image = newItems;
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.product_id) && res.set(arr.product_id, 1));
    },
    // id sản phẩm
    getProductId(data) {
      this.goods_modals = false;
      this.formValidate.recommend_list = this.unique(this.formValidate.recommend_list.concat(data));
    },
    // Chọn sản phẩm đề xuất
    changeGoods() {
      this.goods_modals = true;
      this.$refs.goodslist.getList();
      this.$refs.goodslist.goodsCategory();
    },
    // Chọn nhãn người dùng
    activeData(dataLabel) {
      this.labelShow = false;
      this.dataLabel = dataLabel;
    },
    // Chọn nhãn sản phẩm
    activeLabel(data) {
      this.tagShow = false;
      this.formValidate.label_list = Array.from(new Set(data));
    },
    // Đóng popup nhãn
    labelClose() {
      this.labelShow = false;
      this.tagShow = false;
    },
    // Xóa nhãn người dùng
    closeLabel(label) {
      let index = this.dataLabel.indexOf(this.dataLabel.filter((d) => d.id == label.id)[0]);
      this.dataLabel.splice(index, 1);
    },
    // Mở chọn nhãn người dùng
    openLabel(row) {
      this.labelShow = true;
    },
    handleRemoveRecommend(i) {
      this.formValidate.recommend_list.splice(i, 1);
    },
    // Nhãn hoạt động marketing đã mở
    watchActivity() {
      let marketing = [];
      // Dùng object mapping để tối ưu logic kiểm tra quyền
      const permissionMap = {
        'Mặc định': true,
        'Flash sale': 'seckill',
        'Săn giảm giá': 'bargain',
        'Mua chung': 'combination',
      };
      this.formValidate.activity.forEach((el) => {
        if (permissionMap[el] === true || (permissionMap[el] && checkArray(permissionMap[el]))) {
          marketing.push(el);
        }
      });
      this.formValidate.activity = marketing;
    },
  },
};
</script>
<style lang="scss" scoped>
@use './productAdd.scss' as *;
</style>
