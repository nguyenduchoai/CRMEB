<template>
  <!-- Chi tiết sản phẩm -->
  <el-row>
    <el-col :span="16">
      <el-form-item label="Chi tiết sản phẩm:">
        <WangEditor style="width: 100%" :content="contents" @editorContent="getEditorContent"></WangEditor>
      </el-form-item>
    </el-col>
    <el-col :span="6" style="width: 33%">
      <div class="ifam">
        <div class="content" v-html="content"></div>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import WangEditor from '@/components/wangEditor/index.vue';

export default {
  name: 'ProductDetail',
  components: {
    WangEditor,
  },
  props: {
    contents: {
      type: String,
      required: true,
    },
    content: {
      type: String,
      required: true,
    },
  },
  methods: {
    getEditorContent(val) {
      this.$emit('getEditorContent', val);
    },
  },
};
</script>
<style lang="scss" scoped>
@use '../productAdd.scss' as *;
</style>
