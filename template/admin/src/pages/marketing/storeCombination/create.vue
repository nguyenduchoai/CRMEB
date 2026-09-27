<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.params.id ? 'Sửa sản phẩm mua chung' : 'Thêm sản phẩm mua chung'"
      :backUrl="$routeProStr + '/marketing/store_combination/index'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-row class="mt30 acea-row row-middle row-center">
        <el-col :span="20">
          <steps :stepList="stepList" :isActive="current"></steps>
        </el-col>
        <el-col :span="23" v-loading="spinShow">
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
                  <el-form-item label="Tên chương trình mua chung:" prop="title" label-for="title">
                    <el-input
                      elearable
                      placeholder="Vui lòng nhập tên chương trình mua chung"
                      v-model="formValidate.title"
                      class="content_width"
                      maxlength="80"
                      show-word-limit
                    />
                  </el-form-item>
                </el-col>
              </el-col>
              <el-col :span="24">
                <el-col v-bind="grid">
                  <el-form-item label="Giới thiệu mua chung:" prop="info" label-for="info">
                    <el-input
                      placeholder="Vui lòng nhập giới thiệu mua chung"
                      type="textarea"
                      :rows="4"
                      v-model="formValidate.info"
                      class="content_width"
                      maxlength="100"
                      show-word-limit
                    />
                  </el-form-item>
                </el-col>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Thời gian mua chung:" prop="section_time">
                  <div>
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
                      class="content_width"
                      v-model="formValidate.section_time"
                    ></el-date-picker>
                    <div class="grey">Thiết lập thời gian bắt đầu và kết thúc chương trình, người dùng có thể khởi tạo và tham gia mua chung trong thời gian đã thiết lập</div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24" v-if="formValidate.virtual_type == 0">
                <el-form-item label="Phương thức vận chuyển:" prop="logistics">
                  <el-checkbox-group v-model="formValidate.logistics">
                    <el-checkbox label="1">Chuyển phát</el-checkbox>
                    <el-checkbox label="2">Nhận tại cửa hàng</el-checkbox>
                  </el-checkbox-group>
                </el-form-item>
              </el-col>
              <el-col :span="24" v-if="formValidate.virtual_type == 0">
                <el-form-item label="Cài đặt phí vận chuyển:" :prop="formValidate.freight != 1 ? 'freight' : ''">
                  <el-radio-group v-model="formValidate.freight">
                    <el-radio :label="2">Phí vận chuyển cố định</el-radio>
                    <el-radio :label="3">Mẫu phí vận chuyển</el-radio>
                  </el-radio-group>
                </el-form-item>
              </el-col>
              <el-col
                :span="24"
                v-if="formValidate.freight != 3 && formValidate.freight != 1 && formValidate.virtual_type == 0"
              >
                <el-form-item label="">
                  <div class="acea-row">
                    <el-input-number
                      :controls="false"
                      :min="0"
                      :max="9999999999"
                      v-model="formValidate.postage"
                      placeholder="Vui lòng nhập số tiền"
                      class="content_width input-number-unit-class"
                      class-unit="đ"
                    />
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24" v-if="formValidate.freight == 3 && formValidate.virtual_type == 0">
                <el-form-item label="" prop="temp_id">
                  <div class="acea-row">
                    <el-select
                      v-model="formValidate.temp_id"
                      clearable
                      placeholder="Vui lòng chọn mẫu phí vận chuyển"
                      class="content_width"
                    >
                      <el-option
                        v-for="(item, index) in templateList"
                        :value="item.id"
                        :key="index"
                        :label="item.name"
                      ></el-option>
                    </el-select>
                    <span class="addfont" v-db-click @click="freight">Thêm mẫu phí vận chuyển</span>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Thời hạn mua chung:" prop="effective_time">
                  <div>
                    <el-input-number
                      :controls="false"
                      placeholder="Vui lòng nhập thời hạn mua chung"
                      class="content_width input-number-unit-class"
                      class-unit="giờ"
                      v-model="formValidate.effective_time"
                    />
                    <div class="grey">
                      Thời gian được tính từ khi người dùng khởi tạo nhóm mua chung, cần mời đủ số bạn bè quy định tham gia nhóm trong thời gian đã thiết lập, nếu quá thời hạn, hệ thống sẽ xác định mua chung thất bại và tự động hoàn tiền
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Số người mua chung:" prop="people">
                  <div>
                    <el-input-number
                      :controls="false"
                      :min="2"
                      :max="10000"
                      placeholder="Vui lòng nhập số người mua chung"
                      :precision="0"
                      v-model="formValidate.people"
                      class="content_width input-number-unit-class"
                      class-unit="người"
                    />
                    <div class="grey">Số người dùng cần tham gia cho mỗi nhóm mua chung</div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Số người bổ sung ảo để thành nhóm:" prop="virtualPeople">
                  <div>
                    <el-input-number
                      :controls="false"
                      placeholder="Thiết lập số người bổ sung ảo để thành nhóm"
                      :precision="0"
                      :max="10000"
                      :min="0"
                      v-model="formValidate.virtualPeople"
                      class="content_width input-number-unit-class"
                      class-unit="người"
                    />
                    <div class="grey">
                      Thiết lập số người bổ sung ảo để thành nhóm, ví dụ: nhóm 5 người đặt bổ sung 2 người, khi nhóm có từ 3 thành viên trở lên, lúc kết thúc mua chung hệ thống sẽ tự động bổ sung tối đa 2 vị trí còn lại, nếu không bật thành nhóm ảo vui lòng đặt là 0
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Đơn vị:" prop="unit_name" label-for="unit_name">
                  <el-input clearable placeholder="Vui lòng nhập đơn vị" v-model="formValidate.unit_name" class="content_width" />
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Giới hạn tổng số lượng mua:" prop="num">
                  <div>
                    <el-input-number
                      :controls="false"
                      :min="1"
                      placeholder="Vui lòng nhập giới hạn tổng số lượng"
                      :precision="0"
                      :max="10000"
                      v-model="formValidate.num"
                      class="content_width input-number-unit-class"
                      :class-unit="formValidate.unit_name || 'cái'"
                    />
                    <div class="grey">
                      Số lượng tối đa người dùng có thể mua sản phẩm này trong thời gian diễn ra chương trình. Ví dụ đặt là 4, nghĩa là trong thời gian hiệu lực của chương trình, mỗi người dùng được mua tối đa 4 sản phẩm
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Giới hạn số lượng mỗi lần mua:" prop="once_num">
                  <div>
                    <el-input-number
                      :controls="false"
                      :min="1"
                      placeholder="Vui lòng nhập giới hạn số lượng mỗi lần mua"
                      :precision="0"
                      :max="10000"
                      v-model="formValidate.once_num"
                      class="content_width input-number-unit-class"
                      :class-unit="formValidate.unit_name || 'cái'"
                    />
                    <div class="grey">
                      Giới hạn số lượng tối đa mỗi lần mua khi người dùng tham gia mua chung. Ví dụ đặt là 2, nghĩa là mỗi lần tham gia mua chung, người dùng chỉ được chọn mua tối đa 2 sản phẩm
                    </div>
                  </div>
                </el-form-item>
              </el-col>

              <el-col :span="24">
                <el-form-item label="Tỷ lệ hoa hồng trưởng nhóm:" prop="head_commission">
                  <div>
                    <el-input-number
                      :controls="false"
                      :min="0"
                      :max="100"
                      placeholder="Tỷ lệ hoa hồng trưởng nhóm"
                      :precision="0"
                      v-model="formValidate.head_commission"
                      class="content_width input-number-unit-class"
                      class-unit="%"
                    />
                    <div class="grey">
                      Sau khi mua chung thành công, nếu trưởng nhóm là CTV, khi đơn hàng được xác nhận đã nhận hàng, hệ thống sẽ trả cho trưởng nhóm một khoản hoa hồng, tỷ lệ hoa hồng là 0-100% số tiền thực thanh toán
                    </div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Mua chung tham gia tiếp thị liên kết:" props="is_commission" label-for="is_commission">
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
                    <div class="grey">Sản phẩm mua chung có tham gia trả hoa hồng tiếp thị liên kết của cửa hàng hay không</div>
                  </div>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Thứ tự sắp xếp:">
                  <el-input-number
                    :controls="false"
                    placeholder="Vui lòng nhập thứ tự sắp xếp"
                    :precision="0"
                    :max="10000"
                    :min="0"
                    v-model="formValidate.sort"
                    class="content_width"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Đề xuất nổi bật:" props="is_hot" label-for="is_hot">
                  <el-switch
                    class="defineSwitch"
                    :active-value="1"
                    :inactive-value="0"
                    v-model="formValidate.is_host"
                    size="large"
                    active-text="Bật"
                    inactive-text="Tắt"
                  >
                  </el-switch>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Trạng thái chương trình:" props="is_show" label-for="is_show">
                  <el-switch
                    class="defineSwitch"
                    :active-value="1"
                    :inactive-value="0"
                    v-model="formValidate.is_show"
                    size="large"
                    active-text="Bật"
                    inactive-text="Tắt"
                  >
                  </el-switch>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item label="Chọn quy cách:">
                  <el-table
                    ref="multipleTable"
                    :data="specsData"
                    :row-key="getRowKeys"
                    border
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
                            :precision="2"
                            class="priceBox"
                            :active-change="false"
                          ></el-input-number>
                        </template>
                        <template v-if="item.slot === 'quota'">
                          <el-input-number
                            :controls="false"
                            v-model="scope.row.quota"
                            :min="1"
                            active-change
                            class="priceBox"
                          ></el-input-number>
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
                >Bước trước</el-button
              >
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
import { combinationInfoApi, combinationCreatApi, productAttrsApi } from '@/api/marketing';
import { productGetTemplateApi } from '@/api/product';
import freightTemplate from '@/components/freightTemplate/index';
import steps from '@/components/steps/index';

export default {
  name: 'storeCombinationCreate',
  components: {
    goodsList,
    uploadPictures,
    WangEditor,
    freightTemplate,
    steps,
  },
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
      stepList: ['Chọn sản phẩm mua chung', 'Điền thông tin cơ bản', 'Chỉnh sửa chi tiết sản phẩm'],
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
        price: 0,
        effective_time: 24,
        stock: 1,
        sales: 0,
        sort: 0,
        is_postage: 0,
        is_commission: 0,
        is_host: 0,
        is_show: 0,
        section_time: [],
        description: '',
        id: 0,
        product_id: 0,
        people: 2,
        once_num: 1,
        num: 1,
        temp_id: '',
        attrs: [],
        items: [],
        virtual: 100,
        virtualPeople: 0,
        head_commission: 0,
        logistics: ['1'], //Chọn phương thức vận chuyển
        freight: 2, //Cài đặt phí vận chuyển
        postage: 1, //Thiết lập số tiền phí vận chuyển
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
        title: [{ required: true, message: 'Vui lòng nhập tên chương trình mua chung', trigger: 'blur' }],
        info: [{ required: true, message: 'Vui lòng nhập giới thiệu mua chung', trigger: 'blur' }],
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
            message: 'Vui lòng nhập giá mua chung',
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
            message: 'Vui lòng nhập thời hạn mua chung (đơn vị: giờ)',
            trigger: 'blur',
          },
        ],
        people: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập số người mua chung',
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
      description: '',
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
    if (this.$route.params.id) {
      this.copy = this.$route.params.copy;
      this.current = 1;
      this.getInfo();
    }
    this.productGetTemplate();
  },
  methods: {
    changePrice(e, index) {
      this.$set(this.specsData[index], 'price', e);
    },
    getEditorContent(data) {
      this.description = data;
    },
    // Thêm mẫu phí vận chuyển
    freight() {
      this.$refs.template.id = 0;
      this.$refs.template.isTemplate = true;
    },
    // Quy cách mua chung;
    productAttrs(row) {
      let that = this;
      productAttrsApi(row.id, 3)
        .then((res) => {
          let data = res.data.info;
          let selection = {
            type: 'selection',
            width: 60,
            align: 'center',
          };
          that.specsData = data.attrs;
          that.specsData.forEach(function (item, index) {
            that.$set(that.specsData[index], 'id', index);
          });
          that.formValidate.items = data.items;
          that.columns = data.header;
          // that.columns.unshift(selection);
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
          price: 0, // Không lấy giá gốc trong sản phẩm
          effective_time: 24,
          stock: row.stock,
          sales: row.sales,
          sort: row.sort,
          is_postage: row.is_postage,
          is_commission: 0,
          is_host: row.is_hot,
          is_show: 0,
          section_time: [],
          description: row.description, // Không lấy trong sản phẩm
          id: 0,
          people: 2,
          num: 1,
          once_num: 1,
          product_id: row.id,
          temp_id: row.temp_id,
          virtual: 100,
          virtualPeople: 0,
          logistics: row.logistics, //Chọn phương thức vận chuyển
          freight: row.freight, //Cài đặt phí vận chuyển
          postage: row.postage, //Thiết lập số tiền phí vận chuyển
          custom_form: row.custom_form, //Dữ liệu biểu mẫu tùy chỉnh
          virtual_type: row.virtual_type, //Loại sản phẩm ảo
          head_commission: 0,
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
    // Chi tiết
    getInfo() {
      this.spinShow = true;
      combinationInfoApi(this.$route.params.id)
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
            this.formValidate.virtual = parseInt(
              ((this.formValidate.people - this.formValidate.virtualPeople) / this.formValidate.people) * 100,
            );
            combinationCreatApi(this.formValidate)
              .then(async (res) => {
                this.submitOpen = false;
                this.$message.success(res.msg);
                setTimeout(() => {
                  this.$router.push({
                    path: this.$routeProStr + '/marketing/store_combination/index',
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
              return that.$message.error('Số người mua chung phải lớn hơn 2');
            }
            if (that.formValidate.num < 0) {
              return that.$message.error('Giới hạn số lượng mua phải lớn hơn 0');
            }
            if (that.formValidate.once_num < 0) {
              return that.$message.error('Giới hạn số lượng mỗi lần mua phải lớn hơn 0');
            }
            if (!that.formValidate.attrs) {
              return that.$message.error('Vui lòng chọn thuộc tính quy cách');
            } else {
              for (let index in that.formValidate.attrs) {
                if (that.formValidate.attrs[index].quota <= 0) {
                  return that.$message.error('Số lượng giới hạn mua chung phải lớn hơn 0');
                }
                if (this.formValidate.attrs[index].quota > this.formValidate.attrs[index]['stock']) {
                  return this.$message.error('Số lượng giới hạn mua chung không được vượt quá tồn kho của quy cách');
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
        this.$refs.goodslist.formValidate.is_show = -1;
        this.$refs.goodslist.formValidate.type = 3;
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
.content_width {
  width: 460px;
}
.grey {
  font-size: 12px;
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
.addfont {
  font-size: 12px;
  color: var(--prev-color-primary);
  margin-left: 14px;
  cursor: pointer;
  margin-left: 10px;
  cursor: pointer;
}
</style>
