<template>
  <div v-loading="spinShow">
    <pages-header
      ref="pageHeader"
      :title="$route.params.id ? 'Sửa sản phẩm flash sale' : 'Thêm sản phẩm flash sale'"
      :backUrl="$routeProStr + '/marketing/store_seckill/list'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-row class="mt30 acea-row row-middle row-center">
        <el-col :span="20">
          <steps :stepList="stepList" :isActive="current" @stepActive="stepActive"></steps>
        </el-col>
        <el-col :span="23">
          <el-form
            class="form mt30"
            ref="formValidate"
            :model="formValidate"
            :label-width="labelWidth"
            :label-position="labelPosition"
            @submit.native.prevent
          >
            <el-col v-show="current === 0">
              <el-col :span="24">
                <el-col v-bind="grid">
                  <el-form-item label="Tiêu đề chương trình:" label-for="title">
                    <el-input
                      clearable
                      placeholder="Vui lòng nhập tiêu đề chương trình"
                      v-model="formValidate.title"
                      class="content_width"
                      maxlength="80"
                      show-word-limit
                    />
                  </el-form-item>
                </el-col>
              </el-col>

              <el-col :span="24">
                <el-form-item label="Thời gian chương trình:">
                  <div>
                    <el-date-picker
                      clearable
                      :editable="false"
                      type="daterange"
                      format="yyyy-MM-dd"
                      value-format="yyyy-MM-dd"
                      range-separator="-"
                      start-placeholder="Ngày bắt đầu"
                      end-placeholder="Ngày kết thúc"
                      @change="onchangeTime"
                      class="content_width"
                      v-model="formValidate.section_time"
                    ></el-date-picker>
                    <div class="grey">Thiết lập thời gian bắt đầu và kết thúc chương trình, người dùng có thể tham gia flash sale trong thời gian hiệu lực</div>
                  </div>
                </el-form-item>
              </el-col>

              <el-col :span="24">
                <el-form-item label="Thời gian bắt đầu:">
                  <div>
                    <el-select v-model="formValidate.time_ids" multiple class="content_width">
                      <el-option
                        v-for="item in timeList"
                        :value="item.id"
                        :key="item.id"
                        :label="item.time_name"
                      ></el-option>
                    </el-select>
                    <div class="grey">
                      Chọn khung giờ bắt đầu cho sản phẩm, trong khung giờ này người dùng có thể tham gia mua; ở các khung giờ khác sẽ hiển thị chương trình chưa bắt đầu hoặc đã kết thúc. Nếu chương trình kéo dài hơn một ngày thì trong thời gian diễn ra, chương trình sẽ tự động mở vào khung giờ này mỗi ngày
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Giới hạn tổng số lượng mua:">
                  <div>
                    <el-input-number
                      :controls="false"
                      :min="1"
                      placeholder="Vui lòng nhập giới hạn số lượng"
                      element-id="num"
                      :precision="0"
                      :max="10000"
                      v-model="formValidate.num"
                      class="content_width"
                    />
                    <div class="grey">
                      Giới hạn tổng số lượng sản phẩm này mà mỗi người dùng được mua trong thời gian hiệu lực của chương trình. Ví dụ: đặt là 4 nghĩa là trong thời gian hiệu lực của chương trình này, mỗi người dùng được mua tối đa tổng cộng 4 sản phẩm
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Giới hạn số lượng mỗi lần mua:">
                  <div>
                    <el-input-number
                      :controls="false"
                      :min="1"
                      placeholder="Vui lòng nhập giới hạn số lượng mỗi lần mua"
                      element-id="once_num"
                      :precision="0"
                      :max="10000"
                      v-model="formValidate.once_num"
                      class="content_width"
                    />
                    <div class="grey">
                      Giới hạn số lượng tối đa cho mỗi lần mua khi người dùng tham gia flash sale. Ví dụ: đặt là 2 nghĩa là khi tham gia flash sale, mỗi lần người dùng được chọn mua tối đa 2 sản phẩm
                    </div>
                  </div>
                </el-form-item>
              </el-col>

              <el-col :span="24">
                <el-form-item label="Flash sale tham gia tiếp thị liên kết:" props="is_commission" label-for="is_commission">
                  <div>
                    <el-switch
                      class="defineSwitch"
                      :active-value="1"
                      :inactive-value="0"
                      v-model="formValidate.is_commission"
                      size="large"
                      active-text="Bật"
                      inactive-text="Tắt"
                    >
                    </el-switch>
                    <div class="grey">Sản phẩm có tham gia trả hoa hồng tiếp thị liên kết của cửa hàng hay không</div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Trạng thái chương trình:" props="status" label-for="status">
                  <el-switch
                    class="defineSwitch"
                    :active-value="1"
                    :inactive-value="0"
                    v-model="formValidate.status"
                    size="large"
                    active-text="Bật"
                    inactive-text="Tắt"
                  >
                  </el-switch>
                </el-form-item>
              </el-col>
            </el-col>
            <el-row v-show="current === 1">
              <el-col :span="24">
                <div class="acea-row row-between-wrapper">
                  <div>
                    <el-button type="primary" @click="addGoods">Thêm sản phẩm</el-button>
                    <el-button @click="batchSet" class="ml20" :disabled="!isAllChecked && !checkPidList.length"
                      >Thiết lập hàng loạt</el-button
                    >
                    <el-button @click="delAll" class="ml20" :disabled="!isAllChecked && !checkPidList.length"
                      >Xóa hàng loạt</el-button
                    >
                  </div>
                  <div class="goodsWord">
                    <el-form-item label="Tìm kiếm sản phẩm:">
                      <el-input
                        class="w_input240"
                        v-model="keyword"
                        placeholder="Vui lòng nhập từ khóa sản phẩm"
                        @input="searchWord"
                      ></el-input>
                    </el-form-item>
                  </div>
                </div>
              </el-col>
              <el-col :span="24">
                <div class="vxeTable">
                  <vxe-table
                    border="inner"
                    ref="xTree"
                    :column-config="{ resizable: true }"
                    row-id="id"
                    :tree-config="{ children: 'attrs', reserve: true }"
                    @checkbox-all="checkboxAll"
                    @checkbox-change="checkboxItem"
                    :data="searchTableData.length || keyword ? searchTableData : tableData"
                  >
                    <vxe-column type="checkbox" title="Chọn nhiều" width="100" tree-node></vxe-column>
                    <vxe-column field="info" title="Thông tin sản phẩm" min-width="300">
                      <template v-slot="{ row }">
                        <div class="flex imgPic row-middle">
                          <viewer>
                            <div class="pictrue"><img v-lazy="row.parent == 1 ? row.image : row.pic" /></div>
                          </viewer>
                          <div class="info">
                            <el-tooltip max-width="200" placement="bottom" transfer>
                              <span class="line2">{{ row.store_name }}{{ row.suk }}</span>
                              <p slot="content">{{ row.store_name }}{{ row.suk }}</p>
                            </el-tooltip>
                          </div>
                        </div>
                      </template>
                    </vxe-column>
                    <vxe-column field="cost" title="Giá vốn" min-width="80"></vxe-column>
                    <vxe-column field="product_price" title="Giá bán" min-width="80"></vxe-column>
                    <vxe-column field="price" title="Giá flash sale" min-width="150">
                      <template v-slot="{ row }">
                        <div v-if="row.parent == 1">——</div>
                        <vxe-input
                          v-else
                          v-model="row.price"
                          min="0"
                          placeholder="Vui lòng nhập giá flash sale"
                          type="float"
                          digits="2"
                          step="1"
                        ></vxe-input>
                      </template>
                    </vxe-column>
                    <vxe-column field="quota" title="Giới hạn số lượng" min-width="150">
                      <template v-slot="{ row }">
                        <div v-if="row.parent == 1">——</div>
                        <vxe-input
                          v-else
                          v-model="row.quota"
                          min="0"
                          placeholder="Vui lòng nhập số lượng giới hạn"
                          type="integer"
                        ></vxe-input>
                      </template>
                    </vxe-column>
                    <vxe-column field="stock" title="Tồn kho" min-width="90"></vxe-column>
                    <vxe-column field="status" title="Bật" min-width="100">
                      <template v-slot="{ row }">
                        <el-switch v-model="row.status" :active-value="1" :inactive-value="0" size="large">
                          <span slot="open">Đang bán</span>
                          <span slot="close">Ngừng bán</span>
                        </el-switch>
                      </template>
                    </vxe-column>
                    <vxe-column field="date" title="Thao tác" min-width="100" fixed="right" align="center">
                      <template v-slot="{ row }">
                        <a @click="del(row, $event)" v-if="row.parent == 1">Xóa</a>
                      </template>
                    </vxe-column>
                  </vxe-table>
                </div>
              </el-col>
            </el-row>
            <el-col class="mt20" :span="24">
              <el-form-item>
                <el-button class="submission" v-db-click @click="step" :disabled="current === 0">Bước trước </el-button>
                <el-button
                  :disabled="submitOpen && current === 1"
                  type="primary"
                  class="submission"
                  v-db-click
                  @click="next('formValidate')"
                  >{{ current === 1 ? 'Gửi' : 'Bước tiếp theo' }}</el-button
                >
              </el-form-item>
            </el-col>
          </el-form>
        </el-col>
      </el-row>
    </el-card>
    <!-- Chọn sản phẩm-->
    <el-dialog :visible.sync="modals" title="Danh sách sản phẩm" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" :ischeckbox="true" isdiy :goodsType="1" @getProductId="getProductId"></goods-list>
    </el-dialog>
    <!-- Tải lên ảnh-->
    <el-dialog :visible.sync="modalPic" width="950px" title="Tải lên ảnh sản phẩm" :close-on-click-modal="false">
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        @getPicD="getPicD"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </el-dialog>
    <!-- Mẫu phí vận chuyển-->
    <freight-template ref="template" @addSuccess="productGetTemplate"></freight-template>
    <el-dialog :visible.sync="modalsSet" title="Thiết lập hàng loạt" @close="batchVisibleChange">
      <el-form
        ref="formBatch"
        :model="formBatch"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <el-form-item label="Giá flash sale:" prop="price">
          <el-input
            class="w_input315"
            v-model="formBatch.price"
            min="0"
            placeholder="Vui lòng nhập giá flash sale"
            type="float"
            digits="2"
            step="1"
          ></el-input>
        </el-form-item>
        <el-form-item label="Số lượng giới hạn:" prop="quota">
          <el-input
            class="w_input315"
            v-model="formBatch.quota"
            min="0"
            placeholder="Vui lòng nhập số lượng giới hạn"
            type="integer"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="modalsSet = false">Hủy</el-button>
        <el-button type="primary" @click="okBatch">Lưu</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import WangEditor from '@/components/wangEditor/index.vue';
import uploadPictures from '@/components/uploadPictures';
import { seckillActivityInfoApi, seckillActivityAddApi, seckillTimeListApi } from '@/api/marketing';
import { productGetTemplateApi } from '@/api/product';
import freightTemplate from '@/components/freightTemplate/index';
import steps from '@/components/steps/index';

export default {
  name: 'storeSeckillCreate',
  components: {
    goodsList,
    uploadPictures,
    WangEditor,
    freightTemplate,
    steps,
  },
  data() {
    return {
      stepList: ['Điền thông tin cơ bản', 'Chọn sản phẩm flash sale'],
      submitOpen: false,
      spinShow: false,
      isChoice: '',
      current: 0,
      modalPic: false,
      grid: {
        xl: 12,
        lg: 20,
        md: 24,
        sm: 24,
        xs: 24,
      },
      grid2: {
        xl: 8,
        lg: 12,
        md: 12,
        sm: 24,
        xs: 24,
      },
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
      myConfig: {
        autoHeightEnabled: false, // Trình soạn thảo không tự động giãn cao theo nội dung
        initialFrameHeight: 500, // Chiều cao container ban đầu
        initialFrameWidth: '100%', // Chiều rộng container ban đầu
        UEDITOR_HOME_URL: '/UEditor/',
        serverUrl: '',
      },
      modals: false,
      modal_loading: false,
      images: [],
      formValidate: {
        title: '',
        section_time: [],
        time_ids: [],
        num: 0,
        once_num: 0,
        status: 1,
        product_infos: [],
      },
      formBatch: {
        price: '',
        quota: '',
      },
      templateList: [],
      timeList: [],
      columns: [],
      specsData: [],
      picTit: '',
      tableIndex: 0,
      ruleValidate: {
        image: [{ required: true, message: 'Vui lòng chọn ảnh chính', trigger: 'change' }],
        images: [
          {
            required: true,
            type: 'array',
            message: 'Vui lòng chọn ảnh chính',
            trigger: 'change',
          },
          {
            type: 'array',
            min: 1,
            message: 'Choose two hobbies at best',
            trigger: 'change',
          },
        ],
        title: [{ required: true, message: 'Vui lòng nhập tiêu đề sản phẩm', trigger: 'blur' }],
        info: [{ required: true, message: 'Vui lòng nhập mô tả ngắn chương trình flash sale', trigger: 'blur' }],
        section_time: [
          {
            required: true,
            type: 'array',
            message: 'Vui lòng chọn thời gian chương trình',
            trigger: 'change',
          },
        ],
        unit_name: [{ required: true, message: 'Vui lòng nhập đơn vị', trigger: 'blur' }],
        price: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giá flash sale',
            trigger: 'blur',
          },
        ],
        ot_price: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giá gốc',
            trigger: 'blur',
          },
        ],
        cost: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giá vốn',
            trigger: 'blur',
          },
        ],
        stock: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập tồn kho',
            trigger: 'blur',
          },
        ],
        num: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giới hạn số lượng mua',
            trigger: 'blur',
          },
        ],
        once_num: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giới hạn số lượng mỗi lần mua',
            trigger: 'blur',
          },
        ],
        temp_id: [
          {
            required: true,
            message: 'Vui lòng chọn mẫu phí vận chuyển',
            trigger: 'change',
            type: 'number',
          },
        ],
        time_ids: [
          {
            required: true,
            message: 'Vui lòng chọn thời gian bắt đầu',
            trigger: 'change',
            type: 'Array',
          },
        ],
      },
      copy: 0,
      modalsSet: false,
      isAllChecked: false,
      checkPidList: [], //Tập id liên quan đến cấp cha (yêu cầu cấm xóa cấp con, dùng để xóa toàn bộ sản phẩm)
      searchTableData: [],
      tableData: [],
      keyword: '',
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '135px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  mounted() {
    if (this.$route.params.id) {
      this.current = 0;
      this.getInfo();
    }
    this.productGetTemplate();
    this.seckillTimeList();
  },
  methods: {
    stepActive(index){
      this.current = index;
    },
    addGoods() {
      this.modals = true;
    },
    //Thiết lập hàng loạt
    batchSet() {
      this.modalsSet = true;
    },
    //Xóa
    del(row) {
      // this.tableData = this.tableData.filter((item) => item.id !== row.id);
      if (this.searchTableData.length) {
        this.searchTableData.forEach((i, index) => {
          if (row.id == i.id) {
            this.searchTableData.splice(index, 1);
          }
        });
        this.tableData.forEach((i, index) => {
          if (row.id == i.id) {
            return this.tableData.splice(index, 1);
          }
        });
      } else {
        this.tableData.forEach((i, index) => {
          if (row.id == i.id) {
            return this.tableData.splice(index, 1);
          }
        });
      }
      if (this.isAllChecked && !this.tableData.length) {
        this.isAllChecked = false;
        this.checkPidList = [];
      } else {
        let index = this.checkPidList.indexOf(row.id);
        this.checkPidList.splice(index, 1);
      }
    },
    //Xóa hàng loạt
    delAll() {
      if (this.isAllChecked && (this.tableData.length == this.searchTableData.length || !this.searchTableData.length)) {
        this.tableData = [];
      } else {
        this.tableData = this.tableData.filter((item) => !this.checkPidList.some((ele) => ele === item.id));
      }
      this.checkPidList = [];
      this.isAllChecked = false;
    },
    cancel() {
      this.modals = false;
    },
    batchVisibleChange() {
      this.formBatch.price = '';
      this.formBatch.quota = '';
    },
    searchWord() {
      let list = [];
      console.log(this.tableData, this.keyword);
      this.tableData.forEach((item) => {
        let obj = item.store_name.indexOf(this.keyword);
        if (obj != -1) {
          list.push(item);
        }
      });
      console.log(list);
      if (this.keyword) {
        this.searchTableData = list;
      } else {
        this.searchTableData = [];
      }
    },
    checkboxAll() {
      this.isAllChecked = this.$refs.xTree.isAllCheckboxChecked();
      if (!this.isAllChecked) {
        this.checkPidList = [];
      }
    },
    checkboxItem(e) {
      let id = parseInt(e.rowid);
      if (e.row.product_id) {
        let pIndex = this.checkPidList.indexOf(e.row.product_id);
        if (pIndex !== -1 && !e.checked) {
          this.checkPidList = this.checkPidList.filter((item) => item !== e.row.product_id);
        }
        if (pIndex === -1 && e.checked) {
          this.checkPidList.push(e.row.product_id);
        }
      } else {
        let pIndex = this.checkPidList.indexOf(id);
        if (pIndex !== -1 && !e.checked) {
          this.checkPidList = this.checkPidList.filter((item) => item !== id);
        }
        if (pIndex === -1 && e.checked) {
          this.checkPidList.push(id);
        }
      }
      this.isAllChecked = this.$refs.xTree.isAllCheckboxChecked();
    },
    // Thêm mẫu phí vận chuyển
    freight() {
      this.$refs.template.id = 0;
      this.$refs.template.isTemplate = true;
    },

    // Chọn nhiều
    changeCheckbox(selection) {
      this.formValidate.attrs = selection;
    },
    seckillTimeList() {
      let that = this;
      seckillTimeListApi()
        .then((res) => {
          that.timeList = res.data.list.data;
        })
        .catch((res) => {
          that.$message.error(res.msg);
        });
    },
    // Lấy mẫu phí vận chuyển;
    productGetTemplate() {
      productGetTemplateApi().then((res) => {
        this.templateList = res.data;
      });
    },
    // Kiểm tra form
    validate(prop, status, error) {
      if (status === false) {
        this.$message.error(error);
      }
    },
    // id sản phẩm
    getProductId(data) {
      console.log(data, 'data');
      this.modals = false;
      let listChecked = JSON.parse(JSON.stringify(data));
      listChecked.forEach((item) => {
        item.parent = 1;
        item.status = 1;
        item.isAllChecked = true;
        item.attrs.forEach((value) => {
          value.cate_name = item.cate_name;
          value.store_label = item.store_label;
          value.product_price = item.price;
          value.status = 1;
        });
      });
      let list = this.tableData.concat(listChecked);
      let uni = this.unique(list);
      this.tableData = uni;
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1));
    },
    cancel() {
      this.modals = false;
    },
    okBatch() {
      if (this.formBatch.price == '' && this.formBatch.quota == '') {
        return this.$Message.error('Vui lòng nhập giá flash sale hoặc số lượng giới hạn');
      }
      if (this.isAllChecked && (this.tableData.length == this.searchTableData.length || !this.searchTableData.length)) {
        this.tableData.forEach((item) => {
          item.attrs.forEach((j) => {
            if (this.formBatch.price != '') {
              j.price = this.formBatch.price;
            }
            if (this.formBatch.quota != '') {
              j.quota = this.formBatch.quota;
            }
          });
        });
      } else {
        for (let i = 0; i < this.tableData.length; i++) {
          for (let j = 0; j < this.checkPidList.length; j++) {
            if (this.tableData[i].id == this.checkPidList[j]) {
              this.tableData[i].attrs.forEach((x) => {
                if (this.formBatch.price != '') {
                  x.price = this.formBatch.price;
                }
                // Nếu thiết lập số lượng giới hạn theo lô không để trống, thì sửa số lượng giới hạn của quy cách đang lên kệ
                if (this.formBatch.quota != '' && x.status) {
                  x.quota = this.formBatch.quota;
                }
              });
            }
          }
        }
      }
      this.modalsSet = false;
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.formValidate.section_time = e;
    },
    // Chi tiết
    getInfo() {
      this.spinShow = true;
      seckillActivityInfoApi(this.$route.params.id)
        .then(async (res) => {
          this.formValidate = res.data;
          this.tableData = res.data.product_infos;
          this.tableData.forEach((item) => {
            item.parent = 1;
            item.isAllChecked = true;
            item.attrs.forEach((value) => {
              value.cate_name = item.cate_name;
              value.store_label = item.store_label;
            });
          });
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
    getRowKeys(row) {
      return row.id;
    },
    changePrice(e, index) {
      this.$set(this.specsData[index], 'price', e);
    },
    // Bước tiếp theo
    next(name) {
      let that = this;
      if (this.current === 1) {
        this.formValidate.id = Number(this.$route.params.id) || 0;
        this.submitOpen = true;
        let product_infos = [];
        this.tableData.forEach((item) => {
          product_infos.push({
            id: item.id,
            status: item.status,
            attrs: item.attrs,
          });
          this.formValidate.product_infos = product_infos;
        });
        seckillActivityAddApi(this.formValidate)
          .then(async (res) => {
            this.submitOpen = false;
            this.$message.success(res.msg);
            setTimeout(() => {
              this.$router.push({
                path: this.$routeProStr + '/marketing/store_seckill/index',
              });
            }, 500);
          })
          .catch((res) => {
            this.submitOpen = false;
            this.$message.error(res.msg);
          });
      } else {
        this.current += 1;
      }
    },
    // Bước trước
    step() {
      this.current--;
    },
    // Bấm ảnh sản phẩm
    modalPicTap(tit, picTit, index) {
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
          break;
        // case 'danTable':
        //     this.specsData[this.tableIndex].pic = pc.att_dir;
        //     break;
        default:
          if (!!this.formValidate.attrs && this.formValidate.attrs.length) {
            this.$set(this.specsData[this.tableIndex], '_checked', true);
          }
          this.specsData[this.tableIndex].pic = pc.att_dir;
      }
      this.modalPic = false;
    },
    // Lấy thông tin nhiều ảnh
    getPicD(pc) {
      this.images = pc;
      this.images.map((item) => {
        this.formValidate.images.push(item.att_dir);
        this.formValidate.images = this.formValidate.images.splice(0, 10);
      });
      this.modalPic = false;
    },
    handleRemove(i) {
      this.images.splice(i, 1);
      this.formValidate.images.splice(i, 1);
    },
    // Chọn sản phẩm
    changeGoods() {
      this.modals = true;
      this.$nextTick((e) => {
        this.$refs.goodslist.formValidate.is_show = -1;
        this.$refs.goodslist.formValidate.type = 3;
        this.$refs.goodslist.getList();
        this.$refs.goodslist.goodsCategory();
      });
    }, // Di chuyển
    handleDragStart(e, item) {
      this.dragging = item;
    },
    handleDragEnd(e, item) {
      this.dragging = null;
    },
    // Đầu tiên biến div thành phần tử có thể thả vào, tức là ghi đè dragenter/dragover
    handleDragOver(e) {
      e.dataTransfer.dropEffect = 'move'; // e.dataTransfer.dropEffect="move";//Thiết lập trong dragenter nhằm vào mục tiêu thả!
    },
    handleDragEnter(e, item) {
      e.dataTransfer.effectAllowed = 'move'; // Thiết lập sự kiện dragstart cho phần tử cần di chuyển
      if (item === this.dragging) {
        return;
      }
      const newItems = [...this.formValidate.images];
      const src = newItems.indexOf(this.dragging);
      const dst = newItems.indexOf(item);
      newItems.splice(dst, 0, ...newItems.splice(src, 1));
      this.formValidate.images = newItems;
    },
  },
};
</script>

<style lang="scss" scoped>
.content_width {
  width: 460px;
}
.maxW ::v-deep .ivu-select-dropdown {
  max-width: 600px;
}
.grey {
  color: #999;
  font-size: 12px;
}
.tabBox_img {
  width: 50px;
  height: 50px;
  margin: 0 auto;
}
.tabBox_img img {
  width: 100%;
  height: 100%;
}
.priceBox {
  width: 100%;
}
.form {
  .picBox {
    display: inline-block;
    cursor: pointer;
  }
  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 15px;
    display: inline-block;
    position: relative;
    cursor: pointer;

    img {
      width: 100%;
      height: 100%;
    }
    .btndel {
      position: absolute;
      z-index: 9;
      width: 20px !important;
      height: 20px !important;
      left: 46px;
      top: -4px;
    }
  }
  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
    cursor: pointer;
  }
  .col {
    color: #2d8cf0;
    cursor: pointer;
  }
}
.addfont {
  font-size: 12px;
  color: var(--prev-color-primary);
  margin-left: 14px;
  cursor: pointer;
  margin-left: 10px;
  cursor: pointer;
}
</style>
