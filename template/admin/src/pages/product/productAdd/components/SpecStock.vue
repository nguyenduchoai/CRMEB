<template>
  <!-- Phân loại và tồn kho -->
  <el-row :gutter="24">
    <el-col :span="24">
      <el-form-item label="Kiểu phân loại:" props="spec_type">
        <el-radio-group v-model="formValidate.spec_type" @input="changeSpec">
          <el-radio :label="0" class="radio">Một quy cách</el-radio>
          <el-radio :label="1">Nhiều quy cách</el-radio>
        </el-radio-group>
        <el-dropdown v-if="formValidate.spec_type == 1" class="ml20" @command="confirm" trigger="hover">
          <span class="el-dropdown-link"> Chọn mẫu phân loại<i class="el-icon-arrow-down el-icon--right"></i> </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item v-for="(item, index) in ruleList" :key="index" :command="item.rule_name">{{
              item.rule_name
            }}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </el-form-item>
    </el-col>
    <!-- Thiết lập quy cách -->
    <el-col :span="24" v-if="formValidate.spec_type === 1" class="noForm">
      <el-form-item label="Phân loại sản phẩm:" prop="">
        <div class="specifications">
          <draggable
            group="specifications"
            :disabled="attrs.length < 2"
            :list="attrs"
            handle=".move-icon"
            @end="onMoveSpec"
            animation="300"
          >
            <div
              class="specifications-item active"
              v-for="(item, index) in attrs"
              :key="index"
              @click="changeCurrentIndex(index)"
            >
              <div class="move-icon">
                <span class="iconfont icondrag2"></span>
              </div>
              <i class="del el-icon-error" @click="handleRemoveRole(index, item.value)"></i>
              <div class="specifications-item-box">
                <div class="lineBox"></div>
                <div class="specifications-item-name mb18">
                  <el-input
                    v-model="item.value"
                    placeholder="Tên quy cách"
                    @change="attrChangeValue(index, item.value)"
                    @focus="handleFocus(item.value)"
                    class="specifications-item-name-input"
                    maxlength="30"
                    show-word-limit
                  ></el-input>
                  <el-checkbox
                    class="ml20"
                    v-model="item.add_pic"
                    :disabled="!item.add_pic && !canSel"
                    :true-label="1"
                    :false-label="0"
                    @change="(e) => addPic(e, index)"
                    >Thêm ảnh phân loại</el-checkbox
                  >
                  <el-tooltip
                    class="item"
                    effect="dark"
                    content="Thêm ảnh phân loại, chỉ hỗ trợ bật cho một nhóm phân loại (kích thước đề xuất: 800*800)"
                    placement="right"
                  >
                    <i class="el-icon-info"></i>
                  </el-tooltip>
                </div>
                <div class="rulesBox ml30">
                  <draggable
                    class="item"
                    :list="item.detail"
                    :disabled="item.detail.length < 2"
                    handle=".drag"
                    @end="onMoveSpec"
                  >
                    <div v-for="(det, indexn) in item.detail" :key="indexn" class="mr10 spec drag">
                      <i class="el-icon-error" @click="handleRemove2(item.detail, indexn, det.value)"></i>

                      <el-input
                        style="width: 120px"
                        v-model="det.value"
                        placeholder="Giá trị phân loại"
                        @change="attrDetailChangeValue(det.value, index)"
                        @focus="handleFocus(det.value)"
                        maxlength="30"
                        @blur="handleBlur()"
                      >
                        <template slot="prefix">
                          <span class="iconfont icondrag2"></span>
                        </template>
                      </el-input>
                      <div class="img-popover" v-if="item.add_pic">
                        <div class="popper-arrow"></div>
                        <div class="popper" @click="handleSelImg(det, indexn)">
                          <img class="img" v-if="det.pic" :src="det.pic" />
                          <i v-else class="el-icon-plus"></i>
                        </div>
                        <i v-if="det.pic" class="img-del el-icon-error" @click="handleRemoveImg(det)"></i>
                      </div>
                    </div>
                    <el-popover
                      :ref="'popoverRef_' + index"
                      placement=""
                      width="210"
                      trigger="click"
                      @after-enter="handleShowPop(index)"
                    >
                      <el-input
                        :ref="'inputRef_' + index"
                        placeholder="Vui lòng nhập giá trị phân loại"
                        v-model="formDynamic.attrsVal"
                        @keyup.enter.native="createAttr(formDynamic.attrsVal, index)"
                        @blur="createAttr(formDynamic.attrsVal, index)"
                        maxlength="30"
                        show-word-limit
                      >
                      </el-input>
                      <div class="addfont" slot="reference" type="text" v-db-click>Thêm giá trị phân loại</div>
                    </el-popover>
                  </draggable>
                </div>
              </div>
            </div>
          </draggable>
          <el-button v-if="attrs.length < 4" v-db-click @click="handleAddRole()">Thêm phân loại mới</el-button>
          <el-button v-if="attrs.length >= 1" type="text" v-db-click @click="handleSaveAsTemplate()"
            >Lưu thành mẫu</el-button
          >
        </div>
      </el-form-item>
    </el-col>
    <el-col :span="24" v-if="formValidate.spec_type === 1">
      <el-form-item label="Thuộc tính sản phẩm:" class="labeltop" v-if="manyFormValidate.length">
        <VirtualScroll
          :data="manyFormValidate"
          :buffer="50"
          :height="62"
          key-prop="index"
          @change="(renderData) => (virtualList = renderData)"
        >
          <!-- <el-table row-key="id" :data="virtualList" height="500px"> </el-table> -->
          <el-table
            row-key="index"
            height="700px"
            :data="manyFormValidate"
            style="width: 100%"
            :cell-class-name="tableCellClassName"
            :span-method="objectSpanMethod"
            border
            :key="tableKey"
          >
            <el-table-column
              v-for="(item, index) in formValidate.header"
              :key="index"
              :label="item.title"
              :min-width="item.minWidth || '100'"
              :fixed="item.fixed"
            >
              <template slot-scope="scope">
                <!-- Thiết lập hàng loạt -->
                <template v-if="scope.$index == 0">
                  <template v-if="item.key">
                    <div v-if="attrs.length && attrs[scope.column.index] && manyFormValidate.length">
                      <el-select v-model="oneFormBatch[0][item.title]" :placeholder="`Vui lòng chọn ${item.title}`" clearable>
                        <el-option
                          v-for="val in attrs[scope.column.index].detail"
                          :key="val.value"
                          :label="val.value"
                          :value="val.value"
                        >
                        </el-option>
                      </el-select>
                    </div>
                  </template>
                  <template v-else-if="item.slot === 'pic'">
                    <div class="acea-row row-middle" v-db-click @click="modalPicTap('dan', 'duopi', scope.$index)">
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
                      :max="9999999999"
                      class="priceBox"
                      clearable
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'cost'">
                    <el-input-number
                      :controls="false"
                      v-model="oneFormBatch[0].cost"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                      clearable
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'ot_price'">
                    <el-input-number
                      :controls="false"
                      v-model="oneFormBatch[0].ot_price"
                      :min="0"
                      class="priceBox"
                      clearable
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'stock'">
                    <el-input-number
                      :controls="false"
                      v-model="oneFormBatch[0].stock"
                      :disabled="formValidate.virtual_type == 1"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                      clearable
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'fictitious'"> -- </template>
                  <template v-else-if="item.slot === 'bar_code'">
                    <el-input v-model="oneFormBatch[0].bar_code"></el-input>
                  </template>
                  <template v-else-if="item.slot === 'bar_code_number'">
                    <el-input v-model="oneFormBatch[0].bar_code_number"></el-input>
                  </template>
                  <template v-else-if="item.slot === 'weight'">
                    <el-input-number
                      :controls="false"
                      v-model="oneFormBatch[0].weight"
                      :step="0.1"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                      clearable
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'volume'">
                    <el-input-number
                      :controls="false"
                      v-model="oneFormBatch[0].volume"
                      :step="0.1"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                      clearable
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'selected_spec'"> -- </template>
                  <template v-else-if="item.slot === 'action'">
                    <a v-db-click @click="batchAdd">Sửa hàng loạt</a>
                    <el-divider direction="vertical"></el-divider>
                    <a v-db-click @click="batchDel">Xóa hết</a>
                  </template>
                </template>
                <template v-else>
                  <template v-if="item.key">
                    <div>
                      <span>{{ scope.row.detail[item.key] }}</span>
                    </div>
                  </template>
                  <template v-if="item.slot === 'pic'">
                    <div class="acea-row row-middle" v-db-click @click="modalPicTap('dan', 'duoTable', scope.$index)">
                      <div class="pictrue pictrueTab" v-if="manyFormValidate[scope.$index].pic">
                        <img v-lazy="manyFormValidate[scope.$index].pic" />
                      </div>
                      <div class="upLoad pictrueTab acea-row row-center-wrapper" v-else>
                        <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                      </div>
                    </div>
                  </template>
                  <template v-if="item.slot === 'price'">
                    <el-input-number
                      :controls="false"
                      v-model="manyFormValidate[scope.$index].price"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'cost'">
                    <el-input-number
                      :controls="false"
                      v-model="manyFormValidate[scope.$index].cost"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'ot_price'">
                    <el-input-number
                      :controls="false"
                      v-model="manyFormValidate[scope.$index].ot_price"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'stock'">
                    <el-input-number
                      :controls="false"
                      v-model="manyFormValidate[scope.$index].stock"
                      :disabled="formValidate.virtual_type == 1"
                      :min="0"
                      :max="9999999999"
                      :precision="0"
                      class="priceBox"
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'bar_code'">
                    <el-input v-model="manyFormValidate[scope.$index].bar_code"></el-input>
                  </template>
                  <template v-else-if="item.slot === 'bar_code_number'">
                    <el-input v-model="manyFormValidate[scope.$index].bar_code_number"></el-input>
                  </template>
                  <template v-else-if="item.slot === 'weight'">
                    <el-input-number
                      :controls="false"
                      v-model="manyFormValidate[scope.$index].weight"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'volume'">
                    <el-input-number
                      :controls="false"
                      v-model="manyFormValidate[scope.$index].volume"
                      :min="0"
                      :max="9999999999"
                      class="priceBox"
                    ></el-input-number>
                  </template>
                  <template v-else-if="item.slot === 'fictitious'">
                    <el-button
                      v-if="!manyFormValidate[scope.$index].coupon_id && formValidate.virtual_type == 2"
                      v-db-click
                      @click="addGoodsCoupon(scope.$index, 'manyFormValidate')"
                      >Chọn phiếu giảm giá</el-button
                    >
                    <span
                      class="see"
                      v-else-if="manyFormValidate[scope.$index].coupon_id && formValidate.virtual_type == 2"
                      v-db-click
                      @click="see(manyFormValidate[scope.$index], 'manyFormValidate', scope.$index)"
                      >{{ manyFormValidate[scope.$index].coupon_name }}</span
                    >
                    <el-button
                      v-else-if="
                        !manyFormValidate[scope.$index].virtual_list.length &&
                        !manyFormValidate[scope.$index].stock &&
                        formValidate.virtual_type == 1
                      "
                      v-db-click
                      @click="addVirtual(scope.$index, 'manyFormValidate')"
                      >Thêm mã thẻ</el-button
                    >
                    <span
                      class="see"
                      v-else-if="
                        (manyFormValidate[scope.$index].virtual_list.length || manyFormValidate[scope.$index].stock) &&
                        formValidate.virtual_type == 1
                      "
                      v-db-click
                      @click="see(manyFormValidate[scope.$index], 'manyFormValidate', scope.$index)"
                      >Đã thiết lập</span
                    >
                  </template>

                  <template v-else-if="item.slot === 'selected_spec'">
                    <el-switch
                      v-model="manyFormValidate[scope.$index].is_default_select"
                      :active-value="1"
                      :inactive-value="0"
                      @change="(e) => changeDefaultSelect(e, scope.$index)"
                    />
                  </template>
                  <template v-else-if="item.slot === 'action'">
                    <el-switch
                      class="defineSwitch"
                      v-model="manyFormValidate[scope.$index].is_show"
                      active-text="Hiện"
                      inactive-text="Ẩn"
                      :active-value="1"
                      :inactive-value="0"
                      @change="changeDefaultShow(scope.$index)"
                    />
                  </template>
                </template>
              </template>
            </el-table-column>
          </el-table>
        </VirtualScroll>
      </el-form-item>
    </el-col>
    <!-- ------------------------------------------------- -->
    <!-- Bảng quy cách đơn-->
    <div v-if="formValidate.spec_type === 0">
      <el-col :span="24">
        <el-form-item label="Hình ảnh:">
          <div class="pictrueBox" v-db-click @click="modalPicTap('dan', 'danTable', 0)">
            <div class="pictrue" v-if="oneFormValidate[0].pic">
              <img v-lazy="oneFormValidate[0].pic" />
              <el-input v-model="oneFormValidate[0].pic" style="display: none"></el-input>
            </div>
            <div class="upLoad acea-row row-center-wrapper" v-else>
              <el-input v-model="oneFormValidate[0].pic" style="display: none"></el-input>
              <i class="el-icon-picture-outline" style="font-size: 24px"></i>
            </div>
          </div>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Giá bán:">
          <el-input-number
            :controls="false"
            v-model="oneFormValidate[0].price"
            :min="0"
            :precision="2"
            :max="9999999999"
            class="input_width input-number-unit-class"
            :active-change="false"
            class-unit="đ"
          ></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Giá vốn:">
          <el-input-number
            :controls="false"
            v-model="oneFormValidate[0].cost"
            :min="0"
            :max="9999999999"
            :precision="2"
            :active-change="false"
            class="input_width input-number-unit-class"
            class-unit="đ"
          ></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Giá gốc:">
          <el-input-number
            :controls="false"
            v-model="oneFormValidate[0].ot_price"
            :min="0"
            :max="9999999999"
            :precision="2"
            :active-change="false"
            class="input_width input-number-unit-class"
            class-unit="đ"
          ></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Tồn kho:">
          <el-input-number
            :controls="false"
            v-model="oneFormValidate[0].stock"
            :min="0"
            :max="9999999999"
            :disabled="formValidate.virtual_type == 1"
            :precision="0"
            class="input_width input-number-unit-class"
            :class-unit="formValidate.unit_name || 'cái'"
          ></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Mã sản phẩm:">
          <el-input v-model.trim="oneFormValidate[0].bar_code" class="input_width"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Mã vạch:">
          <el-input v-model.trim="oneFormValidate[0].bar_code_number" class="input_width"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="24" v-if="formValidate.virtual_type == 0">
        <el-form-item label="Trọng lượng:">
          <el-input-number
            :controls="false"
            v-model="oneFormValidate[0].weight"
            :min="0"
            :max="9999999999"
            class="input_width input-number-unit-class"
            class-unit="kg"
          ></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :span="24">
        <el-form-item label="Thể tích:" v-if="formValidate.virtual_type == 0">
          <el-input-number
            :controls="false"
            v-model="oneFormValidate[0].volume"
            :min="0"
            :max="9999999999"
            class="input_width input-number-unit-class"
            class-unit="m³"
          ></el-input-number>
        </el-form-item>
      </el-col>

      <el-col :span="24">
        <el-form-item
          :label="formValidate.virtual_type == 1 ? 'Thêm mã thẻ/lưu trữ đám mây:' : 'Chọn phiếu giảm giá:'"
          v-if="formValidate.virtual_type == 1 || formValidate.virtual_type == 2"
        >
          <el-button
            v-if="!oneFormValidate[0].coupon_id && formValidate.virtual_type == 2"
            v-db-click
            @click="addGoodsCoupon(0, 'oneFormValidate')"
            >Chọn phiếu giảm giá</el-button
          >
          <span
            class="see"
            v-else-if="oneFormValidate[0].coupon_id && formValidate.virtual_type == 2"
            v-db-click
            @click="see(oneFormValidate[0], 'oneFormValidate', 0)"
            >{{ oneFormValidate[0].coupon_name }}</span
          >
          <el-button
            v-if="
              !oneFormValidate[0].virtual_list.length && !oneFormValidate[0].stock && formValidate.virtual_type == 1
            "
            v-db-click
            @click="addVirtual(0, 'oneFormValidate')"
            >Thêm mã thẻ</el-button
          >
          <span
            class="see"
            v-else-if="
              (oneFormValidate[0].virtual_list.length || oneFormValidate[0].stock > 0) && formValidate.virtual_type == 1
            "
            v-db-click
            @click="see(oneFormValidate[0], 'oneFormValidate', 0)"
            >Đã thiết lập</span
          >
        </el-form-item>
      </el-col>
    </div>
  </el-row>
</template>

<script>
import vuedraggable from 'vuedraggable';
import VirtualScroll from './virtualTabel.vue';
export default {
  name: 'SpecStock',
  components: {
    draggable: vuedraggable,
    VirtualScroll,
  },
  props: {
    formValidate: {
      type: Object,
      required: true,
    },
    ruleList: {
      type: Array,
      required: true,
    },
    attrs: {
      type: Array,
      required: true,
    },
    manyFormValidate: {
      type: Array,
      required: true,
    },
    oneFormValidate: {
      type: Array,
      required: true,
    },
    tableKey: {
      type: Number,
      required: true,
    },
    canSel: {
      type: Boolean,
      required: true,
    },
    formDynamic: {
      type: Object,
      required: true,
    },
    oneFormBatch: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      virtualList: [],
    };
  },
  methods: {
    changeSpec(val) {
      this.$emit('changeSpec', val);
    },
    confirm(name) {
      this.$emit('confirm', name);
    },
    onMoveSpec(val) {
      this.$emit('onMoveSpec', val);
    },
    changeDefaultSelect(e, index) {
      this.$emit('changeDefaultSelect', e, index);
    },
    changeDefaultShow(index) {
      this.$emit('changeDefaultShow', index);
    },
    modalPicTap(type, tableName, index) {
      this.$emit('modalPicTap', type, tableName, index);
    },
    handleRemove2(index, type, tableName) {
      this.$emit('handleRemove2', index, type, tableName);
    },
    batchAdd() {
      this.$emit('batchAdd');
    },
    handleAddRole() {
      this.$emit('handleAddRole');
    },
    batchDel() {
      this.$emit('batchDel');
    },
    handleFocus(e) {
      this.$emit('handleFocus', e);
    },
    attrDetailChangeValue(val, index) {
      this.$emit('attrDetailChangeValue', val, index);
    },
    handleBlur(e) {
      this.$emit('handleBlur', e);
    },
    attrChangeValue(val, index) {
      this.$emit('attrChangeValue', val, index);
    },
    handleShowPop(e) {
      this.$emit('handleShowPop', e);
    },
    handleSelImg(e) {
      this.$emit('handleSelImg', e);
    },
    handleSaveAsTemplate() {
      this.$emit('handleSaveAsTemplate');
    },
    addGoodsCoupon(index, type) {
      this.$emit('addGoodsCoupon', index, type);
    },
    changeCurrentIndex(index) {
      this.$emit('changeCurrentIndex', index);
    },
    addVirtual(index, type) {
      this.$emit('addVirtual', index, type);
    },
    handleRemoveImg(index) {
      this.$emit('handleRemoveImg', index);
    },
    handleRemoveRole(index) {
      this.$emit('handleRemoveRole', index);
    },
    see(val, type, index) {
      this.$emit('see', val, type, index);
    },
    createAttr(data, index) {
      this.$emit('createAttr', data, index);
    },
    // Tạo dữ liệu danh sách theo hàng cột
    tableCellClassName({ row, column, rowIndex, columnIndex }) {
      //Chú ý ở đây là phép destructuring
      //Dùng phương thức callback className của ô để gán giá trị chỉ số hàng cột
      row.index = rowIndex || '';
      column.index = columnIndex;
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
    // Hợp nhất ô
    objectSpanMethod({ row, column, rowIndex, columnIndex }) {
      if (columnIndex === 0 && rowIndex > 0) {
        let lable = column.label;
        //Ở đây kiểm tra cột thứ mấy cần hợp nhất
        const tagFamily = this.manyFormValidate[rowIndex].detail[lable];
        const index = this.manyFormValidate.findIndex((item, index) => {
          if (index > 0) return item.detail[lable] == tagFamily;
        });
        if (rowIndex == index) {
          let len = 1;
          for (let i = index + 1; i < this.manyFormValidate.length; i++) {
            if (this.manyFormValidate[i].detail[lable] !== tagFamily) {
              break;
            }
            len++;
          }
          return {
            rowspan: len,
            colspan: 1,
          };
        } else {
          return {
            rowspan: 0,
            colspan: 0,
          };
        }
      }
    },
  },
};
</script>
<style lang="scss" scoped>
@use '../productAdd.scss' as *;
</style>
