<template>
  <div class="Box" v-loading="spinShow">
    <div>
      <div>
        Sản phẩm được tạo mặc định chưa đăng bán, vui lòng đăng bán sản phẩm thủ công!
        <a href="https://doc.crmeb.com/single/v5/7785" v-if="copyConfig.copy_type == 2" target="_blank">Cách cấu hình khóa bí mật</a>
        <span v-else
          >Bạn hiện còn {{ copyConfig.copy_num }} lượt thu thập,<a class="add" v-db-click @click="mealPay('copy')"
            >Thêm lượt thu thập</a
          ></span
        >
      </div>
      <div>Cài đặt thu thập sản phẩm: Cài đặt > Cài đặt hệ thống > Cài đặt API bên thứ ba > Cấu hình thu thập sản phẩm</div>
    </div>
    <el-form
      class="formValidate mt20"
      ref="formValidate"
      :model="formValidate"
      :rules="ruleInline"
      label-width="120px"
      label-position="right"
      @submit.native.prevent
    >
      <el-row :gutter="24">
        <!--<el-col :span="24">-->
        <!--<el-form-item label=""  label-for="">-->
        <!--<el-radio-group v-model="artFrom.type">-->
        <!--<el-radio label="taobao">Taobao</el-radio>-->
        <!--<el-radio label="tmall">Tmall</el-radio>-->
        <!--<el-radio label="jd">JD</el-radio>-->
        <!--<el-radio label="pdd">Pinduoduo</el-radio>-->
        <!--<el-radio label="suning">Suning</el-radio>-->
        <!--<el-radio label="1688">1688</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
        <!--</el-col>-->
        <el-col span="15">
          <el-form-item label="Địa chỉ liên kết:">
            <el-input
              search
              enter-button="Xác nhận"
              v-model="soure_link"
              placeholder="Vui lòng nhập địa chỉ liên kết"
              class="numPut"
              @on-search="add"
            />
          </el-form-item>
        </el-col>
        <div>
          <div v-if="isData">
            <el-col :span="24" class="">
              <el-form-item label="Tên sản phẩm:" prop="store_name">
                <el-input v-model="formValidate.store_name" placeholder="Vui lòng nhập tên sản phẩm" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Mô tả ngắn sản phẩm:" prop="store_info" label-for="store_info">
                <el-input v-model="formValidate.store_info" type="textarea" :rows="3" placeholder="Vui lòng nhập mô tả ngắn sản phẩm" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Danh mục sản phẩm:" prop="cate_id">
                <!-- <el-select v-model="formValidate.cate_id" multiple>
                  <el-option v-for="item in treeSelect" :disabled="item.pid === 0" :value="item.id" :key="item.id">{{
                    item.html + item.cate_name
                  }}</el-option>
                </el-select> -->
                <el-cascader
                  v-model="formValidate.cate_id"
                  size="small"
                  :options="treeSelect"
                  :props="{ multiple: true, emitPath: false, checkStrictly: true }"
                  filterable
                  clearable
                ></el-cascader>
              </el-form-item>
            </el-col>
            <el-col v-bind="grid">
              <el-form-item label="Từ khóa sản phẩm:" prop="keyword" label-for="keyword">
                <el-input v-model="formValidate.keyword" placeholder="Vui lòng nhập từ khóa sản phẩm" />
              </el-form-item>
            </el-col>
            <el-col v-bind="grid">
              <el-form-item label="Đơn vị:" prop="unit_name" label-for="unit_name">
                <el-input v-model="formValidate.unit_name" placeholder="Vui lòng nhập đơn vị" />
              </el-form-item>
            </el-col>
            <el-col v-bind="grid">
              <el-form-item label="Lượt bán ảo:" label-for="ficti">
                <el-input-number
                  :controls="false"
                  class="perW100"
                  v-model="formValidate.ficti"
                  placeholder="Vui lòng nhập lượt bán ảo"
                />
              </el-form-item>
            </el-col>
            <el-col v-bind="grid">
              <el-form-item label="Điểm thưởng:" label-for="give_integral">
                <el-input-number
                  :controls="false"
                  class="perW100"
                  v-model="formValidate.give_integral"
                  placeholder="Vui lòng nhập điểm thưởng"
                />
              </el-form-item>
            </el-col>
            <el-col v-bind="grid">
              <el-form-item label="Mẫu phí vận chuyển:" prop="temp_id">
                <el-select v-model="formValidate.temp_id" clearable>
                  <el-option
                    v-for="(item, index) in templateList"
                    :value="item.id"
                    :key="index"
                    :label="item.name"
                  ></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <!--<el-col v-bind="grid">-->
            <!--<el-form-item label="Phí vận chuyển:"  label-for="postage">-->
            <!--<el-input-number controls-position="right"  v-model="formValidate.postage" placeholder="Vui lòng nhập phí vận chuyển"  />-->
            <!--</el-form-item>-->
            <!--</el-col>-->
            <el-col :span="24">
              <el-form-item label="Ảnh sản phẩm:">
                <div class="pictrueBox">
                  <div class="pictrue" v-if="formValidate.image" v-viewer>
                    <img v-lazy="formValidate.image" />
                  </div>
                </div>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Ảnh trình chiếu sản phẩm:">
                <div class="acea-row" v-viewer>
                  <div
                    class="lunBox mr15"
                    v-for="(item, index) in formValidate.slider_image"
                    :key="index"
                    draggable="true"
                    @dragstart="handleDragStart($event, item)"
                    @dragover.prevent="handleDragOver($event, item)"
                    @dragenter="handleDragEnter($event, item)"
                    @dragend="handleDragEnd($event, item)"
                  >
                    <div class="pictrue"><img v-lazy="item" /></div>
                    <ButtonGroup size="small">
                      <el-button v-db-click @click.native="checked(item, index)">Ảnh chính</el-button>
                      <el-button v-db-click @click.native="handleRemove(index)">Gỡ bỏ</el-button>
                    </ButtonGroup>
                  </div>
                </div>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Thiết lập hàng loạt:" class="labeltop" v-if="formValidate.attrs">
                <el-col :xl="23" :lg="24" :md="24" :sm="24" :xs="24">
                  <el-form-item>
                    <el-table :data="oneFormBatch" border>
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
                            <div
                              class="acea-row row-middle row-center-wrapper"
                              v-db-click
                              @click="modalPicTap('dan', 'duopi', scope.$index)"
                            >
                              <div class="pictrue pictrueTab" v-if="oneFormBatch[0].pic">
                                <img v-lazy="oneFormBatch[0].pic" />
                              </div>
                              <div class="upLoad pictrueTab acea-row row-center-wrapper" v-else>
                                <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                              </div>
                            </div>
                          </template>
                          <template v-else-if="item.slot === 'price'">
                            <el-input-number
                              :controls="false"
                              v-model="oneFormBatch[0].price"
                              :min="0"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'cost'">
                            <el-input-number
                              :controls="false"
                              v-model="oneFormBatch[0].cost"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'ot_price'">
                            <el-input-number
                              :controls="false"
                              v-model="oneFormBatch[0].ot_price"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'stock'">
                            <el-input-number
                              :controls="false"
                              v-model="oneFormBatch[0].stock"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'bar_code'">
                            <el-input v-model="oneFormBatch[0].bar_code"></el-input>
                          </template>
                          <template v-else-if="item.slot === 'weight'">
                            <el-input-number
                              :controls="false"
                              v-model="oneFormBatch[0].weight"
                              :min="0"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'volume'">
                            <el-input-number
                              :controls="false"
                              v-model="oneFormBatch[0].volume"
                              :min="0"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                        </template>
                      </el-table-column>
                      <el-table-column label="Thao tác" fixed="right" width="170">
                        <template slot-scope="">
                          <a v-db-click @click="batchAdd">Thêm</a>
                          <el-divider direction="vertical"></el-divider>
                          <a v-db-click @click="batchDel">Xóa hết</a>
                        </template>
                      </el-table-column>
                    </el-table>
                  </el-form-item>
                </el-col>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Phân loại sản phẩm:" props="spec_type" label-for="spec_type">
                <!-- Bảng quy cách đơn-->
                <el-col :xl="23" :lg="24" :md="24" :sm="24" :xs="24">
                  <el-form-item>
                    <el-table :data="items" border>
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
                            <div
                              class="acea-row row-middle row-center-wrapper"
                              v-db-click
                              @click="modalPicTap('dan', scope.$index)"
                            >
                              <div class="pictrue pictrueTab" v-if="formValidate.attrs[scope.$index].pic">
                                <img v-lazy="formValidate.attrs[scope.$index].pic" />
                              </div>
                              <div class="upLoad upLoadTab acea-row row-center-wrapper" v-else>
                                <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                              </div>
                            </div>
                          </template>
                          <template v-else-if="item.slot === 'price'">
                            <el-input-number
                              :controls="false"
                              v-model="formValidate.attrs[scope.$index].price"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'cost'">
                            <el-input-number
                              :controls="false"
                              v-model="formValidate.attrs[scope.$index].cost"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'ot_price'">
                            <el-input-number
                              :controls="false"
                              v-model="formValidate.attrs[scope.$index].ot_price"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'stock'">
                            <el-input-number
                              :controls="false"
                              v-model="formValidate.attrs[scope.$index].stock"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'bar_code'">
                            <el-input v-model="formValidate.attrs[scope.$index].bar_code"></el-input>
                          </template>
                          <template v-else-if="item.slot === 'weight'">
                            <el-input-number
                              :controls="false"
                              v-model="formValidate.attrs[scope.$index].weight"
                              :min="0"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                          <template v-else-if="item.slot === 'volume'">
                            <el-input-number
                              :controls="false"
                              v-model="formValidate.attrs[scope.$index].volume"
                              :min="0"
                              class="priceBox"
                            ></el-input-number>
                          </template>
                        </template>
                      </el-table-column>
                      <el-table-column label="Thao tác" fixed="right" width="170">
                        <template slot-scope="scope">
                          <a v-db-click @click="delAttrTable(scope.$index)">Xóa</a>
                        </template>
                      </el-table-column>
                    </el-table>
                  </el-form-item>
                </el-col>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Chi tiết sản phẩm:">
                <WangEditor
                  style="width: 100%"
                  :content="formValidate.description"
                  @editorContent="getEditorContent"
                ></WangEditor>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item>
                <el-button
                  type="primary"
                  :loading="modal_loading"
                  class="submission"
                  v-db-click
                  @click="handleSubmit('formValidate')"
                  >Gửi</el-button
                >
              </el-form-item>
            </el-col>
          </div>
        </div>
      </el-row>
    </el-form>
    <el-dialog
      :visible.sync="modalPic"
      width="950px"
      title="Tải lên ảnh sản phẩm"
      :mask-closable="false"
      :close-on-click-modal="false"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </el-dialog>
  </div>
</template>

<script>
import { crawlFromApi, cascaderListApi, crawlSaveApi, productGetTemplateApi, copyConfigApi } from '@/api/product';
import uploadPictures from '@/components/uploadPictures';
import WangEditor from '@/components/wangEditor/index.vue';

export default {
  name: 'taoBao',
  data() {
    return {
      // Thiết lập data bảng theo lô
      oneFormBatch: [
        {
          pic: '',
          price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: '',
          weight: 0,
          volume: 0,
        },
      ],
      columnsBatch: [
        {
          title: 'Hình ảnh',
          slot: 'pic',
          align: 'center',
          minWidth: 80,
        },
        {
          title: 'Giá bán',
          slot: 'price',
          align: 'center',
          minWidth: 95,
        },
        {
          title: 'Giá vốn',
          slot: 'cost',
          align: 'center',
          minWidth: 95,
        },
        {
          title: 'Giá gốc',
          slot: 'ot_price',
          align: 'center',
          minWidth: 95,
        },
        {
          title: 'Tồn kho',
          slot: 'stock',
          align: 'center',
          minWidth: 95,
        },
        {
          title: 'Mã sản phẩm',
          slot: 'bar_code',
          align: 'center',
          minWidth: 120,
        },
        {
          title: 'Trọng lượng (KG)',
          slot: 'weight',
          align: 'center',
          minWidth: 95,
        },
        {
          title: 'Thể tích (m³)',
          slot: 'volume',
          align: 'center',
          minWidth: 95,
        },
        {
          title: 'Thao tác',
          slot: 'action',
          align: 'center',
          minWidth: 140,
        },
      ],
      modal_loading: false,
      images: '',
      soure_link: '',
      modalPic: false,
      isChoice: '',
      spinShow: false,
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
      columns: [],
      treeSelect: [],
      ruleInline: {
        cate_id: [
          {
            required: true,
            message: 'Vui lòng chọn danh mục sản phẩm',
            trigger: 'change',
            type: 'array',
            min: '1',
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
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      grid2: {
        xl: 12,
        lg: 12,
        md: 12,
        sm: 24,
        xs: 24,
      },
      formValidate: {
        store_name: '',
        cate_id: [],
        temp_id: '',
        keyword: '',
        unit_name: '',
        store_info: '',
        image: '',
        slider_image: [],
        description: '',
        ficti: 0,
        give_integral: 0,
        is_show: 0,
        price: 0,
        cost: 0,
        ot_price: 0,
        stock: 0,
        soure_link: '',
        description_images: '',
        postage: 0,
        attrs: [],
        items: [],
      },
      items: [
        {
          pic: '',
          price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: '',
          weight: 0,
          volume: 0,
        },
      ],
      templateList: [],
      copyConfig: {
        copy_type: 2,
        copy_num: 0,
      },
      isData: false,
      artFrom: {
        type: 'taobao',
        url: '',
      },
      tableIndex: 0,
      content: '',
    };
  },
  components: { WangEditor, uploadPictures },
  computed: {},

  created() {
    this.goodsCategory();
  },
  mounted() {
    this.productGetTemplate();
    this.getCopyConfig();
  },
  methods: {
    mealPay(val) {
      this.$router.push({
        path: this.$routeProStr + '/setting/sms/sms_pay/index',
        query: { type: val },
      });
    },
    batchDel() {
      this.oneFormBatch = [
        {
          pic: '',
          price: 0,
          cost: 0,
          ot_price: 0,
          stock: 0,
          bar_code: '',
          weight: 0,
          volume: 0,
        },
      ];
    },
    batchAdd() {
      let formBatch = this.oneFormBatch[0];
      this.$set(
        this.formValidate,
        'attrs',
        this.formValidate.attrs.map((item) => {
          if (formBatch.pic) {
            item.pic = formBatch.pic;
          }
          if (formBatch.price > 0) {
            item.price = formBatch.price;
          }
          if (formBatch.cost > 0) {
            item.cost = formBatch.cost;
          }
          if (formBatch.ot_price > 0) {
            item.ot_price = formBatch.ot_price;
          }
          if (formBatch.stock > 0) {
            item.stock = formBatch.stock;
          }
          if (formBatch.bar_code) {
            item.bar_code = formBatch.bar_code;
          }
          if (formBatch.weight) {
            item.weight = formBatch.weight;
          }
          if (formBatch.volume) {
            item.weight = formBatch.volume;
          }
          return item;
        }),
      );
    },
    getEditorContent(data) {
      this.content = data;
    },
    // Xóa thuộc tính trong bảng
    delAttrTable(index) {
      this.items.splice(index, 1);
    },
    // Lấy mẫu phí vận chuyển;
    productGetTemplate() {
      productGetTemplateApi().then((res) => {
        this.templateList = res.data;
      });
    },
    getCopyConfig() {
      copyConfigApi().then((res) => {
        this.copyConfig.copy_type = res.data.copy_type;
        this.copyConfig.copy_num = res.data.copy_num;
      });
    },
    // Xóa ảnh
    handleRemove(i) {
      this.formValidate.slider_image.splice(i, 1);
    },
    // Chọn ảnh chính
    checked(item, index) {
      this.formValidate.image = item;
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
    // Tạo biểu mẫu
    add() {
      if (this.soure_link) {
        var reg = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/;
        if (!reg.test(this.soure_link)) {
          return this.$message.warning('Vui lòng nhập địa chỉ bắt đầu bằng http!');
        }
        this.spinShow = true;
        this.artFrom.url = this.soure_link;
        crawlFromApi(this.artFrom)
          .then((res) => {
            let info = res.data.info;
            this.columns = info.info.header;
            this.formValidate = info;
            this.formValidate.soure_link = this.soure_link;
            this.formValidate.attrs = info.info.value;
            if (this.formValidate.image) {
              this.oneFormBatch[0].pic = this.formValidate.image;
            }
            this.items = this.formValidate.attrs;
            this.isData = true;
            this.spinShow = false;
          })
          .catch((res) => {
            this.spinShow = false;
            this.$message.error(res.msg);
          });
      } else {
        this.$message.warning('Vui lòng nhập địa chỉ liên kết!');
      }
    },
    // Gửi
    handleSubmit(name) {
      this.formValidate.description = this.content;
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.modal_loading = true;
          // this.formValidate.attrs = [
          //     {
          //         pic: this.images,
          //         price: this.formValidate.price,
          //         cost: this.formValidate.cost,
          //         ot_price: this.formValidate.ot_price,
          //         stock: this.formValidate.stock,
          //         bar_code: this.formValidate.bar_code,
          //         weight: this.formValidate.weight,
          //         volume: this.formValidate.volume
          //     }
          // ];
          // this.formValidate.items = [];
          crawlSaveApi(this.formValidate)
            .then((res) => {
              this.$message.success('Sản phẩm mặc định ở trạng thái chưa đăng bán, vui lòng đăng bán sản phẩm thủ công!');
              setTimeout(() => {
                this.modal_loading = false;
              }, 500);
              setTimeout(() => {
                this.$emit('on-close');
              }, 600);
            })
            .catch((res) => {
              this.modal_loading = false;
              this.$message.error(res.msg);
            });
        } else {
          if (!this.formValidate.cate_id) {
            this.$message.warning('Vui lòng điền danh mục sản phẩm!');
          }
        }
      });
    },
    // Bấm ảnh sản phẩm
    modalPicTap(tit, index) {
      this.modalPic = true;
      this.isChoice = tit === 'dan' ? 'Chọn một' : 'Chọn nhiều';
      this.tableIndex = index;
    },
    // Lấy thông tin một ảnh
    getPic(pc) {
      if (this.tableIndex === 'duopi') {
        this.oneFormBatch[0].pic = pc.att_dir;
      } else {
        this.formValidate.attrs[this.tableIndex].pic = pc.att_dir;
      }
      this.modalPic = false;
    },
    handleDragStart(e, item) {
      this.dragging = item;
    },
    handleDragEnd(e, item) {
      this.dragging = null;
    },
    // Đầu tiên biến div thành phần tử có thể thả vào, tức là ghi đè dragenter/dragover
    handleDragOver(e) {
      // e.dataTransfer.dropEffect="move";//Thiết lập trong dragenter nhằm vào mục tiêu thả!
      e.dataTransfer.dropEffect = 'move';
    },
    handleDragEnter(e, item) {
      // Thiết lập sự kiện dragstart cho phần tử cần di chuyển
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
    // Thêm popup tùy chỉnh
    addCustomDialog(editorId) {
      window.UE.registerUI(
        'test-dialog',
        function (editor, uiName) {
          // Tạo dialog
          let dialog = new window.UE.ui.Dialog({
            iframeUrl: this.$routeProStr + '/widget.images/index.html?fodder=dialog',
            editor: editor,
            name: uiName,
            title: 'Tải lên ảnh',
            cssRules: 'width:960px;height:550px;padding:20px;',
          });
          this.dialog = dialog;
          let btn = new window.UE.ui.Button({
            name: 'dialog-button',
            title: 'Tải lên ảnh',
            cssRules: `background-image: url(../../../assets/images/icons.png);background-position: -726px -77px;`,
            onclick: function () {
              // Render dialog
              dialog.render();
              dialog.open();
            },
          });
          return btn;
        },
        37,
      );
      // window.UE.registerUI('test-dialog', function (editor, uiName) {
      //     let dialog = new window.UE.ui.Dialog({
      //         iframeUrl: '/admin/widget.images/index.html?fodder=dialog',
      //         editor: editor,
      //         name: uiName,
      //         title: 'Tải ảnh lên',
      //         cssRules: 'width:960px;height:550px;padding:20px;'
      //     })
      //     this.dialog = dialog
      //     var btn = new window.UE.ui.Button({
      //         name: 'dialog-button',
      //         title: 'Tải ảnh lên',
      //         cssRules: `background-image: url(../../../assets/images/icons.png);background-position: -726px -77px;`,
      //         onclick: function () {
      //             dialog.render()
      //             dialog.open()
      //         }
      //     })
      //     return btn
      // }, 37)
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .ivu-form-item-content {
  line-height: unset !important;
}
.Box .ivu-radio-wrapper {
  margin-right: 25px;
}
.Box .numPut {
  width: 100% !important;
}
.add {
  color: #2d8cf0;
  cursor: pointer;
}
.lunBox {
  /* width 80px */
  display: flex;
  flex-direction: column;
  border: 1px solid #0bb20c;
}
.pictrueBox {
  display: inline-block;
}
.pictrue {
  width: 85px;
  height: 85px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  display: inline-block;
  position: relative;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
.pictrueTab {
  width: 40px !important;
  height: 40px !important;
}
.upLoad {
  width: 40px;
  height: 40px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
  cursor: pointer;
}
.ivu-table-wrapper {
  border-left: 1px solid #dcdee2;
  border-top: 1px solid #dcdee2;
}
.ft {
  color: red;
}
</style>
