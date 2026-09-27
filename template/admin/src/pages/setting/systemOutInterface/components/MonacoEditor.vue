<template>
  <div class="monaco-container">
    <div ref="container" class="monaco-editor"></div>
  </div>
</template>

<script>
import * as monaco from 'monaco-editor';
export default {
  name: '',
  props: {
    // Nội dung hiển thị trong trình soạn thảo
    codes: {
      type: String,
      default: function () {
        return '';
      },
    },
    readOnly: {
      type: Boolean,
      default: function () {
        return false;
      },
    },
    // Cấu hình chính
    editorOptions: {
      type: Object,
      default: function () {
        return {
          selectOnLineNumbers: true,
          roundedSelection: false,
          readOnly: this.readOnly, // Chỉ đọc
          cursorStyle: 'line', // Kiểu con trỏ
          automaticLayout: false, // Tự động bố cục
          glyphMargin: true, // Viền chữ
          useTabStops: false,
          fontSize: 28, // Cỡ chữ
          autoIndent: true, // Tự động bố cục
        };
      },
    },
  },

  data() {
    return {};
  },
  created() {},
  mounted() {
    this.monacoEditor = monaco.editor.create(this.$refs.container, {
      value: this.codes, // Xem props
      language: 'json',
      theme: 'vs', // Chủ đề trình soạn thảo: vs, hc-black, or vs-dark, xem thêm các lựa chọn khác tại trang chủ
      automaticLayout: true, //Tự động bố cục
      //   foldingStrategy: 'indentation', // Code có thể gập theo từng đoạn nhỏ
      scrollbar: {
        // Thiết lập thanh cuộn
        verticalScrollbarSize: 4, // Thanh cuộn dọc
        horizontalScrollbarSize: 10, // Thanh cuộn ngang
      },
      lineNumbersMinChars: 5,
      editorOptions: this.editorOptions, // Tương tự codes
    });
    setTimeout(() => {
      this.monacoEditor.trigger('anyString', 'editor.action.formatDocument');
      this.monacoEditor.setValue(this.monacoEditor.getValue());
    }, 100);
  },
  methods: {},
};
</script>
<style lang="scss" scoped>
.monaco-editor {
  min-height: 300px;
}
</style>
