<template>
  <!-- Cài đặt khác -->
  <el-row>
    <el-col :span="24">
      <el-form-item label="Từ khóa sản phẩm:">
        <el-input
          class="content_width"
          v-model.trim="formValidate.keyword"
          placeholder="Vui lòng nhập từ khóa sản phẩm"
          maxlength="100"
          show-word-limit
        />
        <div class="tips-info">Dùng để tối ưu SEO trên PC và tìm kiếm sản phẩm theo từ khóa</div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Mô tả ngắn sản phẩm:">
        <el-input
          class="content_width"
          v-model.trim="formValidate.store_info"
          type="textarea"
          :rows="3"
          placeholder="Vui lòng nhập mô tả ngắn sản phẩm"
          maxlength="100"
          show-word-limit
        />
        <div class="tips-info">Dùng khi chia sẻ sản phẩm qua OA WeChat và để tối ưu SEO trên PC</div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Mã chia sẻ sản phẩm:">
        <el-input
          v-model.trim="formValidate.command_word"
          placeholder="Vui lòng nhập mã chia sẻ sản phẩm"
          type="textarea"
          :rows="3"
          class="content_width"
        />
        <div class="tips-info">Điền và lưu mã chia sẻ sản phẩm từ nền tảng khác, khi vào trang chi tiết sản phẩm trên di động sẽ tự động sao chép</div>
      </el-form-item>
    </el-col>

    <el-col :span="24">
      <el-form-item label="Ảnh đề xuất sản phẩm:">
        <div class="pictrueBox" v-db-click @click="modalPicTap('dan', 'recommend_image')">
          <div class="pictrue" v-if="formValidate.recommend_image">
            <img v-lazy="formValidate.recommend_image" />
            <el-input v-model.trim="formValidate.recommend_image" style="display: none"></el-input>
          </div>
          <div class="upLoad acea-row row-center-wrapper" v-else>
            <el-input v-model.trim="formValidate.recommend_image" style="display: none"></el-input>
            <i class="el-icon-picture-outline" style="font-size: 24px"></i>
          </div>
          <div class="tips-info">Ảnh chữ nhật hiển thị ở kiểu danh mục 2 trên di động, tỷ lệ đề xuất: 5:2</div>
        </div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Đảm bảo dịch vụ:">
        <el-checkbox-group v-model="formValidate.protection_list" v-if="protectionList.length">
          <el-checkbox v-for="(item, index) in protectionList" :key="index" :label="item.id">{{
            item.title
          }}</el-checkbox>
        </el-checkbox-group>
        <el-button v-else type="primary" v-db-click @click="addProtection">Thêm đảm bảo</el-button>
        <div class="tips-info">Thông tin đảm bảo dịch vụ hiển thị trong chi tiết sản phẩm, có thể chọn nhiều</div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Thông số sản phẩm:">
        <el-select
          v-model="paramsType"
          clearable
          style="width: 200px; margin-right: 10px"
          @change="changeParamsType"
        >
          <el-option v-for="items in paramsTypeList" :value="items.id" :key="items.id" :label="items.name"></el-option>
        </el-select>
        <div class="specifications">
          <el-table
            class="mt15"
            ref="selection"
            :data="formValidate.params_list"
          >
            <el-table-column label="Tên thông số" min-width="80">
              <template slot-scope="scope">
                <el-input v-model="scope.row.name"></el-input>
              </template>
            </el-table-column>
            <el-table-column label="Giá trị thông số" min-width="80">
              <template slot-scope="scope">
                <el-input v-model="scope.row.value"></el-input>
              </template>
            </el-table-column>
            <el-table-column label="Thao tác" fixed="right" width="80">
              <template slot-scope="scope">
                <a class="submission mr15" v-db-click @click="deleteRow(scope.$index)">Xóa</a>
              </template>
            </el-table-column>
          </el-table>
          <el-button
            v-if="formValidate.params_list.length < 8"
            type="primary"
            class="submission mr15 mt20"
            v-db-click
            @click="handleAddParams"
            >Thêm thông số</el-button
          >
        </div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Biểu mẫu tùy chỉnh:">
        <el-switch :active-value="1" :inactive-value="0" v-model="innerCustomBtn" size="large">
          <span slot="open">Bật</span>
          <span slot="close">Tắt</span>
        </el-switch>
        <div class="addCustom_content" v-if="customBtn">
          <div v-for="(item, index) in formValidate.custom_form" :key="index" class="custom_box">
            <el-input
              v-model.trim="item.title"
              :placeholder="'Tiêu đề biểu mẫu' + (index + 1)"
              style="width: 150px; margin-right: 10px"
              maxlength="10"
              show-word-limit
            />
            <el-select v-model="item.label" style="width: 200px; margin-left: 6px; margin-right: 10px">
              <el-option
                v-for="items in CustomList"
                :value="items.value"
                :key="items.value"
                :label="items.label"
              ></el-option>
            </el-select>
            <el-checkbox v-model="item.status">Bắt buộc</el-checkbox>
            <div class="addfont" v-db-click @click="delcustom(index)">Xóa</div>
          </div>
        </div>
        <div class="addCustomBox" v-show="customBtn">
          <div class="btn" v-db-click @click="addcustom">+ Thêm biểu mẫu</div>
          <div class="tips-info">Thông tin người dùng cần điền khi đặt hàng, tối đa 10 mục, sản phẩm đã thiết lập biểu mẫu tùy chỉnh không thể thêm vào giỏ hàng</div>
        </div>
      </el-form-item>
    </el-col>
  </el-row>
</template>

<script>
export default {
  name: 'OtherSetting',
  props: {
    formValidate: {
      type: Object,
      required: true,
    },
    customBtn: {
      type: Number,
      default: 0,
    },
    paramsType: {
      type: Number,
      default: 0,
    },
    paramsTypeList: {
      type: Array,
      default: () => [],
    },
    protectionList: {
      type: Array,
      default: () => [],
    },
    CustomList: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    innerCustomBtn: {
      get() {
        return this.customBtn;
      },
      set(val) {
        this.$emit('customMessBtn', val);
      },
    },
  },
  methods: {
    modalPicTap(tit, type) {
      this.$emit('modalPicTap', tit, type);
    },
    changeParamsType(val) {
      this.$emit('changeParamsType', val);
    },
    deleteRow(index) {
      this.$emit('deleteRow', index);
    },
    handleAddParams() {
      this.$emit('handleAddParams');
    },
    addProtection() {
      this.$emit('addProtection');
    },
    // customMessBtn(e) {
    //   console.log(e);
    //   this.$emit('customMessBtn', e);
    // },
    delcustom(index) {
      this.$emit('delcustom', index);
    },
    addcustom() {
      this.$emit('addcustom');
    },
  },
};
</script>
<style lang="scss" scoped>
@use '../productAdd.scss' as *;
</style>
