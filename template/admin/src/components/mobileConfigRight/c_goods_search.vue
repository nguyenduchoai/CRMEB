<template>
  <!-- Thành phần này hiện chưa dùng, giữ lại để sau này phát triển tiếp có thể dùng -->
  <div class="acea-row row-top" style="margin-bottom: 20px" v-if="configData">
    <el-checkbox-group v-model="configData.type" @change="checkboxChange">
      <div>
        <el-checkbox :label="1">
          <span>Danh mục sản phẩm</span>
        </el-checkbox>
        <el-cascader
          :data="configData.list"
          placeholder="Vui lòng chọn danh mục sản phẩm"
          :props="{ multiple: true, checkStrictly: true, emitPath: false }"
          v-model="configData.activeValue"
          filterable
          @change="sliderChange"
        ></el-cascader>
      </div>
    </el-checkbox-group>
  </div>
</template>

<script>
export default {
  name: 'c_goods_search',
  props: {
    configObj: {
      type: Object,
    },
    configNme: {
      type: String,
    },
  },
  data() {
    return {
      formData: {
        type: 0,
      },
      defaults: {},
      configData: {},
    };
  },
  watch: {
    configObj: {
      handler(nVal, oVal) {
        this.defaults = nVal;
        this.configData = nVal[this.configNme];
      },
      deep: true,
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.defaults = this.configObj;
      this.configData = this.configObj[this.configNme];
    });
  },
  methods: {
    checkboxChange(e) {
      this.$emit('getConfig', e);
    },
    sliderChange(e) {
      let storage = window.localStorage;
      this.configData.activeValue = e ? e : storage.getItem(this.timeStamp);
      this.$emit('getConfig', { name: 'cascader', values: e });
    },
  },
};
</script>

<style></style>
