<template>
  <div v-loading="spinShow">
    <div class="i-layout-page-header">
      <div class="i-layout-page-header">
        <router-link :to="{ path: $routeProStr + '/marketing/presell/index' }">
          <el-button icon="ios-arrow-back" size="small" class="mr20">Quay lại </el-button>
        </router-link>
        <span
          class="ivu-page-header-title mr20"
          v-text="$route.params.id != 0 ? 'Sửa sản phẩm đặt trước' : 'Thêm sản phẩm đặt trước'"
        ></span>
      </div>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-row class="mt30 acea-row row-middle row-center">
        <el-col :span="20">
          <steps :stepList="stepList" :isActive="current"></steps>
        </el-col>
        <el-col :span="23">
          <el-form
            class="form mt30"
            ref="formValidate"
            :model="formValidate"
            :rules="ruleValidate"
            @on-validate="validate"
            :label-width="labelWidth"
            :label-position="labelPosition"
            @submit.native.prevent
          >
            <el-form-item label="Chọn sản phẩm:" prop="image_input" v-if="current === 0">
              <div class="picBox" v-db-click @click="changeGoods">
                <div class="pictrue" v-if="formValidate.image">
                  <img v-lazy="formValidate.image" />
                </div>
                <div class="upLoad acea-row row-center-wrapper" v-else>
                  <i class="el-icon-goods" style="font-size: 24px"></i>
                </div>
              </div>
            </el-form-item>
            <el-row v-show="current === 1">
              <el-col :span="24">
                <el-form-item label="Ảnh chính sản phẩm:" prop="image">
                  <div class="picBox" v-db-click @click="modalPicTap('dan', 'danFrom')">
                    <div class="pictrue" v-if="formValidate.image">
                      <img v-lazy="formValidate.image" />
                    </div>
                    <div class="upLoad acea-row row-center-wrapper" v-else>
                      <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Ảnh trình chiếu sản phẩm:" prop="images">
                  <div class="acea-row">
                    <div
                      class="pictrue"
                      v-for="(item, index) in formValidate.images"
                      :key="index"
                      draggable="true"
                      @dragstart="handleDragStart($event, item)"
                      @dragover.prevent="handleDragOver($event, item)"
                      @dragenter="handleDragEnter($event, item)"
                      @dragend="handleDragEnd($event, item)"
                    >
                      <img v-lazy="item" />
                      <el-button
                        shape="circle"
                        icon="md-close"
                        v-db-click
                        @click.native="handleRemove(index)"
                        class="btndel"
                      ></el-button>
                    </div>
                    <div
                      v-if="formValidate.images.length < 10"
                      class="upLoad acea-row row-center-wrapper"
                      v-db-click
                      @click="modalPicTap('duo')"
                    >
                      <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-col v-bind="grid">
                  <el-form-item label="Tên chương trình đặt trước:" prop="title" label-for="title">
                    <el-input placeholder="Vui lòng nhập tên chương trình đặt trước" element-id="title" v-model="formValidate.title" />
                  </el-form-item>
                </el-col>
              </el-col>
              <el-col :span="24">
                <el-col v-bind="grid">
                  <el-form-item label="Mô tả ngắn chương trình đặt trước:" prop="info" label-for="info">
                    <el-input
                      placeholder="Vui lòng nhập mô tả ngắn chương trình đặt trước"
                      type="textarea"
                      :rows="4"
                      element-id="info"
                      v-model="formValidate.info"
                    />
                  </el-form-item>
                </el-col>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Thời gian chương trình đặt trước:" prop="section_time">
                  <div class="acea-row row-middle">
                    <el-date-picker
                      clearable
                      :editable="false"
                      type="datetimerange"
                      format="yyyy-MM-dd HH:mm"
                      value-format="yyyy-MM-dd HH:mm"
                      range-separator="-"
                      start-placeholder="Ngày bắt đầu"
                      end-placeholder="Ngày kết thúc"
                      @change="onchangeTime"
                      class="perW20"
                      v-model="formValidate.section_time"
                    ></el-date-picker>
                    <div class="ml10 grey">Thiết lập thời gian bắt đầu và kết thúc chương trình, người dùng có thể tham gia đặt trước trong khoảng thời gian đã thiết lập</div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Thời gian giao hàng:" prop="deliver_time">
                  <div class="acea-row row-middle">
                    <span class="mr10">Sau khi chương trình đặt trước kết thúc, trong vòng</span>
                    <el-input-number
                      :controls="false"
                      placeholder="Vui lòng nhập thời gian giao hàng"
                      :precision="0"
                      :min="1"
                      v-model="formValidate.deliver_time"
                    />
                    <span class="ml10"> ngày </span>
                    <div class="ml10 grey"></div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Mẫu phí vận chuyển:" prop="temp_id">
                  <div class="acea-row row-middle">
                    <el-select v-model="formValidate.temp_id" class="perW20">
                      <el-option
                        v-for="item in templateList"
                        :value="item.id"
                        :key="item.id"
                        :label="item.name"
                      ></el-option>
                    </el-select>
                    <div class="ml10 col" v-db-click @click="freight">Thêm mẫu phí vận chuyển</div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Giới hạn tổng số lượng mua:" prop="num">
                  <div class="acea-row row-middle">
                    <el-input-number
                      :controls="false"
                      :min="1"
                      placeholder="Vui lòng nhập giới hạn tổng số lượng"
                      :precision="0"
                      element-id="num"
                      v-model="formValidate.num"
                      class="perW20"
                    />
                    <div class="ml10 grey">
                      Số lượng tối đa người dùng có thể mua sản phẩm này trong thời gian diễn ra chương trình. Ví dụ đặt là 4, nghĩa là trong thời gian hiệu lực của chương trình, mỗi người dùng được mua tối đa 4 sản phẩm
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Đơn vị:" prop="unit_name" label-for="unit_name">
                  <el-input
                    placeholder="Vui lòng nhập đơn vị"
                    element-id="unit_name"
                    v-model="formValidate.unit_name"
                    class="perW20"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Thứ tự sắp xếp:">
                  <el-input-number
                    :controls="false"
                    placeholder="Vui lòng nhập thứ tự sắp xếp"
                    element-id="sort"
                    :precision="0"
                    v-model="formValidate.sort"
                    class="perW10"
                  />
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
                    active-text="Đang bán"
                    inactive-text="Ngừng bán"
                  >
                  </el-switch>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Chọn quy cách:">
                  <el-table :data="specsData" @selection-change="changeCheckbox">
                    <el-table-column type="selection" width="55"> </el-table-column>
                    <el-table-column
                      :label="item.title"
                      :min-width="item.minWidth"
                      v-for="(item, index) in columns"
                      :key="index"
                    >
                      <template slot-scope="scope">
                        <template v-if="item.key">
                          <div>
                            <span>{{ scope.row[item.key] }}</span>
                          </div>
                        </template>
                        <template v-else-if="item.slot === 'pic'">
                          <div class="pictrue pictrueTab" v-if="scope.row.pic">
                            <img v-lazy="scope.row.pic" />
                          </div>
                          <div class="upLoad pictrueTab acea-row row-center-wrapper" v-else>
                            <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                          </div>
                        </template>
                      </template>
                    </el-table-column>
                  </el-table>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row v-show="current === 2">
              <el-col :span="24">
                <el-form-item label="Nội dung:">
                  <WangEditor
                    style="width: 90%"
                    :content="formValidate.description"
                    @editorContent="getEditorContent"
                  ></WangEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-form-item>
              <el-button
                class="submission"
                v-db-click
                @click="step"
                :disabled="($route.params.id && current === 1) || current === 0"
                >Bước trước
              </el-button>
              <el-button
                type="primary"
                :disabled="submitOpen && current === 2"
                class="submission"
                v-db-click
                @click="next('formValidate')"
                >{{ current === 2 ? 'Gửi' : 'Bước tiếp theo' }}</el-button
              >
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
    </el-card>
    <!-- Chọn sản phẩm-->
    <el-dialog :visible.sync="modals" title="Danh sách sản phẩm" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" @getProductId="getProductId"></goods-list>
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
  </div>
</template>

<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import WangEditor from '@/components/wangEditor/index.vue';
import uploadPictures from '@/components/uploadPictures';
import freightTemplate from '@/components/freightTemplate/index';
import { presellInfoApi, presellCreatApi, productAttrsApi } from '@/api/marketing';
import { productGetTemplateApi } from '@/api/product';
import steps from '@/components/steps/index';

export default {
  name: 'storePersellCreate',
  components: {
    goodsList,
    uploadPictures,
    WangEditor,
    freightTemplate,
    steps,
  },
  data() {
    return {
      stepList: ['Chọn sản phẩm đặt trước', 'Điền thông tin cơ bản', 'Chỉnh sửa chi tiết sản phẩm'],
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
        lg: 8,
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
      templateList: [],
      columns: [],
      specsData: [],
      picTit: '',
      tableIndex: 0,
      formValidate: {
        images: [],
        info: '',
        title: '',
        image: '',
        unit_name: '',
        stock: 1,
        sales: 0,
        deliver_time: 3,
        sort: 0,
        status: 1,
        section_time: [],
        description: '',
        id: 0,
        product_id: 0,
        // pay_time: [],
        // type: 1,
        num: 1,
        deposit: 1,
        temp_id: '',
        attrs: [],
        items: [],
      },
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
        title: [{ required: true, message: 'Vui lòng nhập tên chương trình đặt trước', trigger: 'blur' }],
        info: [{ required: true, message: 'Vui lòng nhập mô tả ngắn chương trình đặt trước', trigger: 'blur' }],
        section_time: [
          {
            required: true,
            type: 'array',
            message: 'Vui lòng chọn thời gian chương trình',
            trigger: 'change',
          },
        ],
        // pay_time: [
        //   {
        //     required: true,
        //     type: "array",
        //     message: "Vui lòng chọn thời gian hoạt động",
        //     trigger: "change",
        //   },
        // ],
        unit_name: [{ required: true, message: 'Vui lòng nhập đơn vị', trigger: 'blur' }],
        price: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giá đặt trước',
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
        give_integral: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập điểm thưởng tặng',
            trigger: 'blur',
          },
        ],
        effective_time: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập thời hạn đặt trước (đơn vị: giờ)',
            trigger: 'blur',
          },
        ],
        people: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập số người đặt trước',
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
        deposit: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập số tiền đặt cọc',
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
        virtualPeople: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập số người bổ sung ảo để thành nhóm',
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
      },
      copy: 0,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '155px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  mounted() {
    if (this.$route.params.id != 0) {
      this.copy = this.$route.params.copy;
      this.current = 1;
      this.getInfo();
    }
    this.productGetTemplate();
  },
  methods: {
    getEditorContent(data) {
      this.formValidate.description = data;
    },
    // Thêm mẫu phí vận chuyển
    freight() {
      this.$refs.template.id = 0;
      this.$refs.template.isTemplate = true;
    },
    // Quy cách đặt trước;
    productAttrs(row) {
      let that = this;
      productAttrsApi(row.id, 6)
        .then((res) => {
          let data = res.data.info;
          that.specsData = data.attrs;
          that.specsData.forEach(function (item, index) {
            that.$set(that.specsData[index], 'id', index);
          });
          that.formValidate.items = data.items;
          that.columns = data.header;
        })
        .catch((res) => {
          that.$message.error(res.msg);
        });
    },

    // Chọn nhiều
    changeCheckbox(selection) {
      this.formValidate.attrs = selection;
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
    getProductId(row) {
      this.modal_loading = false;
      this.modals = false;
      setTimeout(() => {
        this.formValidate = {
          images: row.slider_image,
          info: row.store_info,
          title: row.store_name,
          image: row.image,
          unit_name: row.unit_name,
          stock: row.stock,
          sales: row.sales,
          sort: row.sort,
          section_time: [],
          deliver_time: 3,
          // pay_time: [],
          // type: 1,
          num: 1,
          deposit: 1,
          description: '', // Không lấy trong sản phẩm
          id: 0,
          num: 1,
          status: 1,
          product_id: row.id,
          temp_id: row.temp_id,
        };
        this.productAttrs(row);
      }, 500);
    },
    cancel() {
      this.modals = false;
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.formValidate.section_time = e;
    },
    // onchangePayTime(e) {
    //   this.formValidate.pay_time = e;
    // },
    // Chi tiết
    getInfo() {
      this.spinShow = true;
      presellInfoApi(this.$route.params.id)
        .then(async (res) => {
          let that = this;
          let info = res.data.info;
          let selection = {
            type: 'selection',
            width: 60,
            align: 'center',
          };
          this.formValidate = info;
          this.formValidate.virtualPeople = parseInt(
            this.formValidate.people - this.formValidate.people * (this.formValidate.virtual / 100),
          );
          this.$set(this.formValidate, 'items', info.attrs.items);
          this.columns = info.attrs.header;
          // this.columns.unshift(selection);
          this.specsData = info.attrs.value;
          that.specsData.forEach(function (item, index) {
            that.$set(that.specsData[index], 'id', index);
          });
          let data = info.attrs;
          let attr = [];
          for (let index in info.attrs.value) {
            if (info.attrs.value[index]._checked) {
              attr.push(info.attrs.value[index]);
            }
          }
          that.formValidate.attrs = attr;
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res);
        });
    },
    // Bước tiếp theo
    next(name) {
      let that = this;
      if (this.current === 2) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            if (this.copy == 1) this.formValidate.copy = 1;
            this.formValidate.id = Number(this.$route.params.id) || 0;
            this.submitOpen = true;
            this.formValidate.virtual = parseInt(
              ((this.formValidate.people - this.formValidate.virtualPeople) / this.formValidate.people) * 100,
            );
            presellCreatApi(this.formValidate)
              .then(async (res) => {
                this.submitOpen = false;
                this.$message.success(res.msg);
                setTimeout(() => {
                  this.$router.push({
                    path: this.$routeProStr + '/marketing/presell/index',
                  });
                }, 500);
              })
              .catch((res) => {
                this.submitOpen = false;
                this.$message.error(res.msg);
              });
          } else {
            return false;
          }
        });
      } else if (this.current === 1) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            if (that.formValidate.people < 2) {
              return that.$message.error('Số người đặt trước phải lớn hơn 2');
            }
            if (that.formValidate.num < 0) {
              return that.$message.error('Giới hạn số lượng mua phải lớn hơn 0');
            }
            if (!that.formValidate.attrs) {
              return that.$message.error('Vui lòng chọn thuộc tính quy cách');
            } else {
              for (let index in that.formValidate.attrs) {
                if (that.formValidate.attrs[index].quota <= 0) {
                  return that.$message.error('Số lượng giới hạn đặt trước phải lớn hơn 0');
                }
                if (this.formValidate.attrs[index].quota > this.formValidate.attrs[index]['stock']) {
                  return this.$message.error('Số lượng giới hạn đặt trước không được vượt quá tồn kho của phân loại');
                }
              }
            }
            this.current += 1;
          } else {
            return this.$message.warning('Vui lòng hoàn thiện thông tin của bạn');
          }
        });
      } else {
        if (this.formValidate.image) {
          this.current += 1;
        } else {
          this.$message.warning('Vui lòng chọn sản phẩm');
        }
      }
    },
    // Bước trước
    step() {
      this.current--;
    },
    // Nội dung
    getContent(val) {
      this.formValidate.description = val;
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
        this.$refs.goodslist.getList();
        this.$refs.goodslist.goodsCategory();
      });
    },
    // Di chuyển
    handleDragStart(e, item) {
      this.dragging = item;
    },
    handleDragEnd(e, item) {
      this.dragging = null;
    },
    // Đầu tiên biến div thành phần tử có thể thả vào, tức là ghi đè dragenter/dragover
    handleDragOver(e) {
      e.dataTransfer.dropEffect = 'move';
    },
    handleDragEnter(e, item) {
      e.dataTransfer.effectAllowed = 'move';
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
.grey {
  color: #999;
}
.maxW ::v-deep .ivu-select-dropdown {
  max-width: 600px;
}
.ivu-table-wrapper {
  border-left: 1px solid #dcdee2;
  border-top: 1px solid #dcdee2;
}
.tabBox_img {
  width: 50px;
  height: 50px;
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
</style>
