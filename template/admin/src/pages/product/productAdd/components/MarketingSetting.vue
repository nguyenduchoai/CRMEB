<template>
  <!-- Cài đặt marketing -->
  <el-row>
    <el-col :span="24">
      <el-form-item label="Tặng điểm thưởng khi mua:" prop="give_integral">
        <el-input-number
          :controls="false"
          v-model="formValidate.give_integral"
          :min="0"
          :max="9999999999"
          placeholder="Vui lòng nhập điểm thưởng"
          class="input_width input-number-unit-class"
          class-unit="Điểm thưởng"
        />
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Tặng phiếu giảm giá khi mua:">
        <div v-if="couponName.length" class="mb10">
          <el-tag class="mr10" closable v-for="(item, index) in couponName" :key="index" @close="handleClose(item)">{{
            item.title
          }}</el-tag>
        </div>
        <el-button type="primary" v-db-click @click="addCoupon">Chọn phiếu giảm giá</el-button>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Liên kết nhãn người dùng:" prop="label_id">
        <div style="display: flex">
          <div class="labelInput acea-row row-between-wrapper" v-db-click @click="openLabel">
            <div style="width: 90%">
              <div v-if="dataLabel.length">
                <el-tag closable v-for="(item, index) in dataLabel" @close="closeLabel(item)" :key="index">{{
                  item.label_name
                }}</el-tag>
              </div>
              <span class="span" v-else>Chọn nhãn gắn cho người dùng</span>
            </div>
            <div class="iconfont iconxiayi"></div>
          </div>
          <span class="addfont" v-db-click @click="addLabel">Thêm nhãn</span>
        </div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <div class="line"></div>
    </el-col>
    <el-col v-if="formValidate.virtual_type == 0" :span="24">
      <el-form-item label="Số lượng mua tối thiểu:">
        <el-input-number
          :controls="false"
          :min="1"
          :max="9999999999"
          :precision="0"
          v-model="formValidate.min_qty"
          placeholder="Vui lòng nhập số lượng mua tối thiểu"
          class="input_width input-number-unit-class"
          :class-unit="formValidate.unit_name || 'cái'"
        />
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Giới hạn mua:">
        <el-switch
          v-model="formValidate.is_limit"
          class="defineSwitch"
          active-text="Bật"
          inactive-text="Tắt"
          :active-value="1"
          :inactive-value="0"
          size="large"
        >
        </el-switch>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Loại giới hạn mua:" v-if="formValidate.is_limit">
        <el-radio-group v-model="formValidate.limit_type">
          <el-radio :label="1">Giới hạn mỗi lần mua</el-radio>
          <el-radio :label="2">Giới hạn mỗi người</el-radio>
        </el-radio-group>
        <div class="tips-info">Giới hạn mỗi lần mua là giới hạn số lượng tối đa cho mỗi lần đặt hàng, giới hạn mỗi người là giới hạn tổng số lượng mà một người dùng có thể mua</div>
      </el-form-item>
    </el-col>
    <el-col :span="24" v-if="formValidate.is_limit">
      <el-form-item label="Số lượng giới hạn mua:" prop="limit_num">
        <div class="acea-row row-middle">
          <el-input-number
            :controls="false"
            placeholder="Vui lòng nhập số lượng giới hạn mua"
            :precision="0"
            :min="1"
            v-model="formValidate.limit_num"
            class="input_width input-number-unit-class"
            :class-unit="formValidate.unit_name || 'cái'"
          />
        </div>
      </el-form-item>
    </el-col>
    <el-col  v-if="formValidate.is_limit" :span="24">
      <div class="line"></div>
    </el-col>
    <el-col :span="24" v-if="formValidate.virtual_type == 0 || formValidate.virtual_type == 3">
      <el-form-item label="Sản phẩm đặt trước:">
        <el-switch
          v-model="formValidate.presale"
          class="defineSwitch"
          active-text="Bật"
          inactive-text="Tắt"
          :active-value="1"
          :inactive-value="0"
          size="large"
        >
        </el-switch>
      </el-form-item>
    </el-col>
    <el-col :span="24" v-if="formValidate.presale">
      <el-form-item label="Thời gian chương trình đặt trước:" prop="presale_time">
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
            v-model="formValidate.presale_time"
          ></el-date-picker>
        </div>
        <div class="tips-info">Thiết lập thời gian bắt đầu và kết thúc chương trình, người dùng có thể tham gia đặt trước trong khoảng thời gian đã thiết lập</div>
      </el-form-item>
    </el-col>
    <el-col :span="24" v-if="formValidate.presale">
      <el-form-item label="Thời gian giao hàng:" prop="presale_day">
        <div class="acea-row row-middle">
          <span class="mr10">Sau khi chương trình đặt trước kết thúc, trong vòng</span>
          <el-input-number
            class="w-80 input-number-unit-class"
            :controls="false"
            placeholder="Vui lòng nhập thời gian giao hàng"
            :precision="0"
            :min="1"
            class-unit="ngày"
            v-model="formValidate.presale_day"
          />
          <span class="ml10"> sẽ giao hàng </span>
        </div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <div class="line"></div>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Đề xuất sản phẩm:">
        <el-checkbox-group v-model="formValidate.recommend">
          <el-checkbox label="is_hot">Sản phẩm bán chạy</el-checkbox>
          <el-checkbox label="is_best">Đề xuất nổi bật</el-checkbox>
          <el-checkbox label="is_new">Hàng mới ra mắt</el-checkbox>
          <el-checkbox label="is_good">Đề xuất sản phẩm tốt</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Mức ưu tiên chương trình:">
        <div class="color-list acea-row row-middle">
          <div
            class="color-item"
            :class="activity[color]"
            v-for="color in formValidate.activity"
            v-dragging="{
              item: color,
              list: formValidate.activity,
              group: 'color',
            }"
            :key="color"
          >
            {{ color }}
          </div>
        </div>
        <div class="tips-info">Có thể kéo nút để điều chỉnh thứ tự ưu tiên hiển thị của các chương trình</div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Sản phẩm đề xuất chất lượng:">
        <div class="picBox">
          <div class="pictrue" v-for="(item, index) in formValidate.recommend_list" :key="index">
            <img v-lazy="item.image" />
            <i class="el-icon-error btndel" v-db-click @click="handleRemoveRecommend(index)"></i>
          </div>
          <div class="upLoad acea-row row-center-wrapper" v-db-click @click="changeGoods">
            <i class="el-icon-picture-outline" style="font-size: 24px"></i>
          </div>
        </div>
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <div class="line"></div>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Số lượng đã bán:">
        <el-input-number
          :controls="false"
          :min="0"
          :max="9999999999"
          v-model="formValidate.ficti"
          placeholder="Vui lòng nhập lượt bán ảo"
          class="input_width input-number-unit-class"
          :class-unit="formValidate.unit_name || 'cái'"
        />
      </el-form-item>
    </el-col>
    <el-col :span="24">
      <el-form-item label="Thứ tự sắp xếp:">
        <el-input-number
          :controls="false"
          :min="0"
          :max="9999999999"
          v-model="formValidate.sort"
          placeholder="Vui lòng nhập số, số càng lớn càng xếp trước"
          class="input_width"
        />
      </el-form-item>
    </el-col>
  </el-row>
</template>

<script>
export default {
  name: 'MarketingSetting',
  props: {
    formValidate: {
      type: Object,
      required: true,
    },
    couponName: {
      type: Array,
      default: () => [],
    },
    dataLabel: {
      type: Array,
      default: () => [],
    },
    activity: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
    handleClose(tag) {
      this.$emit('handleClose', tag);
    },
    addCoupon() {
      this.$emit('addCoupon');
    },
    openLabel() {
      this.$emit('openLabel');
    },
    closeLabel() {
      this.$emit('closeLabel');
    },
    addLabel() {
      this.$emit('addLabel');
    },
    onchangeTime(val) {
      this.$emit('onchangeTime', val);
    },
    handleRemoveRecommend(index) {
      this.$emit('handleRemoveRecommend', index);
    },
    changeGoods(val) {
      this.$emit('changeGoods', val);
    },
  },
};
</script>
<style lang="scss" scoped>
@use '../productAdd.scss' as *;
</style>
