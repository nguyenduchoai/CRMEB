<template>
  <el-dialog :visible.sync="modals_son" :title="title" :close-on-click-modal="false" width="900px">
    <el-button type="primary" id="savefile" class="mr5 mb15" v-db-click @click="savefile">Lưu</el-button>
    <el-button id="undo" class="mr5 mb15" v-db-click @click="undofile">Hoàn tác</el-button>
    <el-button id="redo" class="mr5 mb15" v-db-click @click="redofile">Làm lại</el-button>
    <el-button id="refresh" class="mb15" v-db-click @click="refreshfile">Làm mới</el-button>
    <textarea ref="mycode" class="codesql public_text" v-model="code" style="height: 80vh"></textarea>
  </el-dialog>
</template>

<script>
import { opendirLoginApi } from '@/api/system';
import CodeMirror from 'codemirror/lib/codemirror';
import 'codemirror/theme/ambiance.css';
import { setCookies, getCookies, removeCookies } from '@/libs/util';

// Style cốt lõi
// import 'codemirror/lib/codemirror.css'
// Sau khi import theme còn cần chỉ định theme trong options mới có hiệu lực
import 'codemirror/theme/cobalt.css';

// Cần import thư viện syntax highlight cụ thể mới có hiệu ứng highlight cú pháp tương ứng
// codemirror chính thức thực ra hỗ trợ tải động thư viện syntax highlight tương ứng thông qua /addon/mode/loadmode.js và /mode/meta.js
// Nhưng vue có vẻ như không thể tải động JS tương ứng sau khi instance đã khởi tạo, nên ở đây mới import trước JS tương ứng
// import 'codemirror/mode/javascript/javascript.js'
// import 'codemirror/mode/css/css.js'
// import 'codemirror/mode/xml/xml.js'
// import 'codemirror/mode/clike/clike.js'
// import 'codemirror/mode/markdown/markdown.js'
// import 'codemirror/mode/python/python.js'
// import 'codemirror/mode/r/r.js'
// import 'codemirror/mode/shell/shell.js'
// import 'codemirror/mode/sql/sql.js'
// import 'codemirror/mode/swift/swift.js'
// import 'codemirror/mode/vue/vue.js'

require('codemirror/mode/javascript/javascript');
export default {
  name: 'opendir',
  props: {
    rows: {
      type: Object,
      default: {},
    },
    code: {
      type: String,
      default: ' ',
    },
    modals: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      editor: '',
      isShowLogn: false, // Đăng nhập
      isShowList: false, // Danh sách sau khi đăng nhập
      spinShow: false,
      loading: false,

      formItem: {
        dir: '',
        superior: 0,
        filedir: '',
      },
      pathname: '',
      modals_son: this.modals,
      fileToken: getCookies('file_token'),
    };
  },
  watch: {
    code: {
      handler(newValue, oldValue) {
        this.editor.setValue(newValue);
      },
      deep: true, // Giá trị mặc định là false, đại diện cho việc có theo dõi sâu hay không
    },
    modals: {
      handler(newValue, oldValue) {
        this.modals_son = newValue;
      },
      deep: true, // Giá trị mặc định là false, đại diện cho việc có theo dõi sâu hay không
    },
  },
  mounted() {
    this.editor = CodeMirror.fromTextArea(this.$refs.mycode, {
      value: 'http://www.crmeb.com', // Văn bản hiển thị mặc định của textarea
      mode: 'text/javascript',
      theme: 'ambiance', // Chọn style CSS
      indentUnit: 8, // Đơn vị thụt lề, mặc định 2
      smartIndent: true, // Có thụt lề thông minh hay không
      tabSize: 4, // Thụt lề Tab, mặc định 4
      readOnly: false, // Có chỉ đọc hay không, mặc định false
      showCursorWhenSelecting: true,
      lineNumbers: true, // Có hiển thị số dòng hay không

      indentWithTabs: true,
      matchBrackets: true,
      extraKeys: {
        Ctrl: 'autocomplete',
      }, //Phím tắt tùy chỉnh
    });
    //Chức năng gợi ý code tự động, hãy nhớ dùng event cursorActivity, không dùng event change, đây là một cái bẫy, nếu không trang sẽ bị đứng ngay
    editor.on('cursorActivity', function () {
      editor.showHint();
    });
  },
  created() {
    // this.getList();
    this.onIsLogin();
  },
  methods: {
    // Lưu
    savefile() {
      let data = {
        comment: this.editor.getValue(),
        filepath: this.pathname,
        fileToken: this.fileToken,
      };
      savefileApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.modals = false;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Hoàn tác
    undofile() {
      this.editor.undo();
    },
    redofile() {
      this.editor.redo();
    },
    // Làm mới
    refreshfile() {
      this.editor.refresh();
    },
  },
};
</script>
<style lang="scss" scoped>
::v-deep .CodeMirror {
  height: 70vh !important;
}
</style>
