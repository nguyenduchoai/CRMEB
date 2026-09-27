<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.params.id ? 'Sửa sản phẩm đổi điểm' : 'Thêm sản phẩm đổi điểm'"
      :backUrl="$routeProStr + '/marketing/store_integral/index'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-row class="mt30 acea-row row-middle row-center" v-loading="spinShow">
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
            <el-col v-show="current === 1">
              <el-col :span="24">
                <el-form-item prop="image">
                  <div class="custom-label" slot="label">
                    <div>
                      <div>Ảnh chính sản phẩm</div>
                      <div>(750*750)</div>
                    </div>
                    <div>：</div>
                  </div>
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
                <el-form-item prop="images">
                  <div class="custom-label" slot="label">
                    <div>
                      <div>Ảnh trình chiếu sản phẩm</div>
                      <div>(750*750)</div>
                    </div>
                    <div>：</div>
                  </div>
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
                      <i class="el-icon-circle-close btndel" v-db-click @click="handleRemove(index)"></i>
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
                  <el-form-item label="Tiêu đề sản phẩm:" prop="title" label-for="title">
                    <el-input
                      placeholder="Vui lòng nhập tiêu đề sản phẩm"
                      v-model="formValidate.title"
                      class="content_width"
                      maxlength="80"
                      show-word-limit
                    />
                  </el-form-item>
                </el-col>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Giới hạn số lượng đổi mỗi người dùng:" prop="num">
                  <div class="acea-row row-middle">
                    <el-input-number
                      :controls="false"
                      :min="1"
                      :max="9999999999"
                      placeholder="Vui lòng nhập giới hạn số lượng"
                      element-id="num"
                      :precision="0"
                      v-model="formValidate.num"
                      class="content_width"
                    />
                    <div class="ml10 grey">
                      Giới hạn tổng số lượng sản phẩm này mà mỗi người dùng được mua. Ví dụ: đặt là 4 nghĩa là trong chương trình này, mỗi người dùng được đổi tối đa tổng cộng 4 sản phẩm
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
                    class="content_width"
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
                    class="content_width"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Trạng thái đăng bán:" props="is_show" label-for="status">
                  <el-radio-group element-id="is_show" v-model="formValidate.is_show">
                    <el-radio :label="1" class="radio">Bật</el-radio>
                    <el-radio :label="0">Tắt</el-radio>
                  </el-radio-group>
                </el-form-item>
              </el-col>
              <el-col v-bind="grid2">
                <el-form-item label="Đề xuất nổi bật:" props="is_host" label-for="is_host">
                  <el-radio-group element-id="is_host" v-model="formValidate.is_host">
                    <el-radio :label="1" class="radio">Bật</el-radio>
                    <el-radio :label="0">Tắt</el-radio>
                  </el-radio-group>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Chọn quy cách:">
                  <el-table
                    ref="multipleTable"
                    :data="specsData"
                    border
                    :row-key="getRowKeys"
                    class="mt14"
                    highlight-current-row
                    @selection-change="changeCheckbox"
                  >
                    <el-table-column type="selection" :reserve-selection="true" width="55"> </el-table-column>
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
                            @click="modalPicTap('dan', 'danTable', scope.$index)"
                          >
                            <div class="pictrue pictrueTab" v-if="scope.row.pic">
                              <img v-lazy="scope.row.pic" />
                            </div>
                            <div class="upLoad pictrueTab acea-row row-center-wrapper" v-else>
                              <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                            </div>
                          </div>
                        </template>
                        <template v-else-if="item.slot === 'price'">
                          <el-input-number
                            :controls="false"
                            v-model="scope.row.price"
                            :min="0"
                            :precision="0"
                            class="priceBox"
                            :active-change="false"
                          ></el-input-number>
                        </template>
                        <template v-else-if="item.slot === 'quota'">
                          <el-input-number
                            :controls="false"
                            v-model="scope.row.quota"
                            :min="1"
                            :max="scope.row.stock"
                            active-change
                            class="priceBox"
                          ></el-input-number>
                        </template>
                      </template>
                    </el-table-column>
                  </el-table>
                </el-form-item>
              </el-col>
            </el-col>
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
            <el-col :span="24">
              <el-form-item>
                <el-button
                  class="submission"
                  v-db-click
                  @click="step"
                  :disabled="($route.params.id && current === 1) || current === 0"
                  >Bước trước
                </el-button>
                <el-button
                  :disabled="submitOpen && current === 2"
                  type="primary"
                  class="submission"
                  v-db-click
                  @click="next('formValidate')"
                  >{{ current === 2 ? 'Gửi' : 'Bước tiếp theo' }}</el-button
                >
              </el-form-item>
            </el-col>
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
  </div>
</template>

<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import WangEditor from '@/components/wangEditor/index.vue';
import uploadPictures from '@/components/uploadPictures';
import { integralAddApi, productAttrsApi, integralInfoApi } from '@/api/marketing';
import steps from '@/components/steps/index';

export default {
  name: 'storeIntegralCreate',
  components: { goodsList, uploadPictures, WangEditor, steps },
  data() {
    return {
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
      stepList: ['Chọn sản phẩm đổi điểm', 'Điền thông tin cơ bản', 'Chỉnh sửa chi tiết sản phẩm'],
      modals: false,
      modal_loading: false,
      images: [],
      formValidate: {
        images: [],
        info: '',
        title: '',
        image: '',
        unit_name: '',
        price: 0,
        ot_price: 0,
        cost: 0,
        sales: 0,
        stock: 0,
        sort: 0,
        num: 1,
        once_num: 1,
        give_integral: 0,
        postage: 0,
        section_time: [],
        is_postage: 0,
        is_hot: 0,
        status: 0,
        description: '',
        id: 0,
        product_id: 0,
        temp_id: '',
        time_id: '',
        attrs: [],
        items: [],
      },
      description: '',
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
        info: [{ required: true, message: 'Vui lòng nhập mô tả ngắn chương trình đổi điểm', trigger: 'blur' }],
        unit_name: [{ required: true, message: 'Vui lòng nhập đơn vị', trigger: 'blur' }],
        price: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập số điểm cần đổi',
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
            message: 'Vui lòng nhập giới hạn số lượng đổi',
            trigger: 'blur',
          },
        ],
        once_num: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập giới hạn số lượng đổi mỗi lần',
            trigger: 'blur',
          },
        ],
      },
      copy: 0,
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
      this.copy = this.$route.params.copy;
      this.current = 1;
      this.getInfo();
    }
  },
  methods: {
    getEditorContent(data) {
      this.description = data;
    },
    // Quy cách;
    productAttrs(rows) {
      let that = this;
      productAttrsApi(rows.id, 4)
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
          price: 0, // Không lấy giá gốc trong sản phẩm
          ot_price: row.ot_price,
          cost: row.cost,
          sales: row.sales,
          stock: row.stock,
          sort: row.sort,
          num: 1,
          once_num: 1,
          give_integral: row.give_integral,
          postage: row.postage,
          section_time: [],
          is_postage: row.is_postage,
          is_host: 0,
          is_show: 1,
          description: row.description,
          id: 0,
          product_id: row.id,
          temp_id: row.temp_id,
        };
        this.productAttrs(row);
        this.$refs.goodslist.productRow = null;
      }, 500);
    },

    cancel() {
      this.modals = false;
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.formValidate.section_time = e;
    },
    // Chi tiết
    getInfo() {
      this.spinShow = true;
      integralInfoApi(this.$route.params.id)
        .then(async (res) => {
          let that = this;
          let info = res.data.info;
          this.formValidate = info;
          this.$set(this.formValidate, 'items', info.attrs.items);
          this.columns = info.attrs.header;
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
          attr.forEach((row) => {
            that.$refs.multipleTable.toggleRowSelection(row, true);
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
    // Bước tiếp theo
    next(name) {
      let that = this;
      if (this.current === 2) {
        this.formValidate.description = this.description;
        this.$refs[name].validate((valid) => {
          if (valid) {
            if (this.copy == 1) this.formValidate.copy = 1;
            this.formValidate.id = Number(this.$route.params.id) || 0;
            this.submitOpen = true;
            integralAddApi(this.formValidate)
              .then(async (res) => {
                this.submitOpen = false;
                this.$message.success(res.msg);
                setTimeout(() => {
                  this.$router.push({
                    path: this.$routeProStr + '/marketing/store_integral/index',
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
            if (!that.formValidate.attrs) {
              return that.$message.error('Vui lòng chọn thuộc tính quy cách');
            } else {
              for (let index in that.formValidate.attrs) {
                if (that.formValidate.attrs[index].quota <= 0) {
                  return that.$message.error('Số lần đổi phải lớn hơn 0');
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
        this.$refs.goodslist.formValidate.type = 7;
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
.custom-label {
  display: inline-flex;
  line-height: 1.5;
}
.grey {
  color: #999;
}
.maxW ::v-deep .ivu-select-dropdown {
  max-width: 600px;
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
}
</style>
