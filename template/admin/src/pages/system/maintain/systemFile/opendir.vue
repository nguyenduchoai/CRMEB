<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" v-loading="spinShow">
      <div v-if="isShowList" class="backs-box">
        <div class="backs">
          <span class="back" v-db-click @click="goBack(false)">
            <i class="el-icon-back icon" />
          </span>
          <span class="item" v-for="(item, index) in routeList" :key="index" v-db-click @click="jumpRoute(item)">
            <span class="key">{{ item.key }}</span>
            <i class="forward el-icon-arrow-right" v-if="index < routeList.length - 1" />
          </span>
        </div>
        <span class="refresh" v-db-click @click="refreshRoute">
          <i class="el-icon-refresh-right icon" />
        </span>
      </div>
      <el-table
        v-if="isShowList"
        ref="selection"
        :data="tabList"
        v-loading="loading"
        empty-text="Chưa có dữ liệu"
        class="mt14"
      >
        <el-table-column label="Tên tệp/thư mục" min-width="150">
          <template slot-scope="scope">
            <div class="file-name" v-db-click @click="currentChange(scope.row)">
              <i v-if="scope.row.isDir" class="el-icon-folder mr5" />
              <i v-else class="el-icon-document mr5" />
              <span>{{ scope.row.filename }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Kích thước tệp/thư mục" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.size }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian cập nhật" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.mtime }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ghi chú" min-width="120">
          <template slot-scope="scope">
            <div class="mark">
              <div v-if="scope.row.is_edit" class="table-mark" v-db-click @click="isEditMark(scope.row)">
                {{ scope.row.mark }}
              </div>
              <el-input ref="mark" v-else v-model="scope.row.mark" @blur="isEditBlur(scope.row)"></el-input>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="60">
          <template slot-scope="scope">
            <el-button type="text" v-db-click @click="open(scope.row)" v-if="scope.row.isDir">Mở</el-button>
            <el-button type="text" v-db-click @click="edit(scope.row)" v-else>Sửa</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <el-dialog
      :visible.sync="modals"
      :custom-class="className"
      :close-on-click-modal="false"
      width="80%"
      top="5vh"
      @close="editModalChange"
      append-to-body
      :title="editorIndex[indexEditor].title"
    >
      <p slot="header" class="diy-header" ref="diyHeader">
        <span>{{ title }}</span>
        <i
          v-db-click
          @click="winChanges"
          class="diy-header-icon"
          :class="className ? 'el-icon-cpu' : 'el-icon-full-screen'"
          style="font-size: 20px"
        />
      </p>
      <div style="height: 100%">
        <div class="top-button">
          <el-button type="primary" id="savefile" class="diy-button" v-db-click @click="savefile(indexEditor)"
            >Lưu</el-button
          >
          <el-button id="refresh" class="diy-button" v-db-click @click="refreshfile">Làm mới</el-button>
        </div>
        <div class="file-box">
          <div class="show-info">
            <div class="show-text" :title="navItem.pathname">Thư mục: {{ navItem.pathname }}</div>
            <div class="diy-button-list">
              <el-button class="diy-button" v-db-click @click="goBack(true)">Quay lại cấp trên</el-button>
              <el-button class="diy-button" v-db-click @click="getList(true, true)">Làm mới</el-button>
            </div>
          </div>
          <div class="file-left">
            <el-tree
              class="diy-tree-render"
              :data="navList"
              :render-content="renderContent"
              :load="loadData"
              @node-contextmenu="handleContextMenu"
              expand-node
              lazy
              :props="props"
            >
              <!-- <template transfer slot="contextMenu">
                <DropdownItem v-if="contextData && contextData.isDir" v-db-click @click.native="handleContextCreateFolder()"
                  >Tạo thư mục mới</DropdownItem
                >
                <DropdownItem v-if="contextData && contextData.isDir" v-db-click @click.native="handleContextCreateFile()"
                  >Tạo file mới</DropdownItem
                >
                <DropdownItem v-db-click @click.native="handleContextRename()">Đổi tên</DropdownItem>
                <DropdownItem v-db-click @click.native="handleContextDelFolder()" style="color: #ed4014">Xóa</DropdownItem>
              </template> -->
            </el-tree>
          </div>
          <div class="file-fix"></div>
          <div class="file-content">
            <el-tabs
              type="card"
              v-model="indexEditor"
              style="height: 100%"
              @tab-click="toggleEditor"
              :animated="false"
              closable
              @tab-remove="handleTabRemove"
            >
              <el-tab-pane
                v-for="value in editorIndex"
                :key="value.index"
                :name="value.index.toString()"
                :label="value.title"
                :icon="value.icon"
                v-if="value.tab"
              >
                <div
                  ref="container"
                  :id="'container_' + value.index"
                  style="height: 100%; min-height: calc(80vh - 100px)"
                ></div>
              </el-tab-pane>
            </el-tabs>
          </div>
        </div>
      </div>
    </el-dialog>

    <div v-show="formShow" class="diy-from">
      <div class="diy-from-header">
        {{ formTitle
        }}<span :title="contextData ? contextData.pathname : ''">{{ contextData ? contextData.pathname : '' }}</span>
      </div>
      <el-form ref="formInline" :model="formFile" :rules="ruleInline" inline>
        <el-form-item prop="filename" class="diy-file">
          <el-input type="text" class="diy-file" v-model="formFile.filename" placeholder="Vui lòng nhập tên">
            <i class="el-icon-folder-opened" slot="prepend"></i>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-button class="diy-button" v-db-click @click="handleSubmit('formInline')">Xác nhận</el-button>
        </el-form-item>
        <el-form-item>
          <el-button class="diy-button" v-db-click @click="formExit()">Hủy</el-button>
        </el-form-item>
        <div class="form-mask" v-show="formShow"></div>
      </el-form>
    </div>
  </div>
</template>

<script>
import { resolveComponent } from 'vue';
import {
  opendirListApi,
  openfileApi,
  savefileApi,
  opendirLoginApi,
  createFolder,
  createFile,
  delFolder,
  rename,
  fileMark,
  markSave,
} from '@/api/system';
import CodeMirror from 'codemirror/lib/codemirror';
import loginFrom from './components/loginFrom';
import { setCookies, getCookies, removeCookies } from '@/libs/util';
// import Fullscreen from '@/layout/components/fullscreen';
import * as monaco from 'monaco-editor';
export default {
  name: 'opendir',
  data() {
    return {
      modals: false, //Công tắc trình soạn thảo
      editor: '', //Đối tượng trình soạn thảo hiện tại
      editorIndex: [
        //Mảng tab
        {
          tab: true,
          index: '0',
          title: '',
          icon: '',
        },
      ],
      editorList: [], //Mảng trình soạn thảo
      indexEditor: 0, //Chỉ số trình soạn thảo hiện tại
      code: '', //Nội dung khi file hiện tại được mở
      navList: [], //Dữ liệu điều hướng bên trái
      navItem: {}, //Dữ liệu được chọn khi click vào điều hướng bên trái
      contextData: null, //Đối tượng dữ liệu được tạo ra khi nhấp chuột phải vào điều hướng bên trái

      fileType: '', // Loại thao tác file createFolder|tạo thư mục createFile|tạo file delFolder|xóa thư mục hoặc file
      className: '', //Tên class toàn màn hình
      // fullscreen:false,  // Có toàn màn hình hay không
      isSave: true, //Tệp hiện tại đã được lưu hay chưa

      isShowLogn: false, // Đăng nhập
      isShowList: false, // Danh sách sau khi đăng nhập

      spinShow: false,
      loading: false,
      tabList: [],

      formItem: {
        //Ghi lại thông tin đường dẫn hiện tại, dùng khi lấy danh sách file
        dir: '',
        superior: 0,
        filedir: '',
        fileToken: getCookies('file_token'),
      },
      dir: '', //Đường dẫn file đầy đủ hiện tại
      // rows: {},  //
      pathname: '', // Đường dẫn file hiện tại
      title: '', //Tiêu đề file hiện tại

      formFile: {
        //Form đổi tên
        filename: '',
      },
      ruleInline: {
        filename: [{ required: true, message: 'Vui lòng nhập tên tệp hoặc thư mục', trigger: 'blur' }],
      },
      formShow: false, //Bật/tắt form
      formTitle: '', //Tiêu đề biểu mẫu
      fileToken: getCookies('file_token'),
      routeList: [], //  Đường dẫn file đang mở
      props: {
        label: 'title',
        children: 'children',
        isLeaf: 'isLeaf',
      },
    };
  },

  components: {
    loginFrom,
  },
  mounted() {
    // this.initEditor();
  },
  created() {
    this.getList();
  },
  beforeDestroy() {
    removeCookies('file_token');
  },
  computed: {},
  methods: {
    // Click vào dòng
    currentChange(currentRow) {
      if (currentRow.isDir) {
        this.open(currentRow);
      } else {
        this.edit(currentRow);
      }
    },
    /**
     * Danh sách tệp
     * @param {Object} refresh   // Có tải lại hay không (bool)
     * @param {Object} is_edit   // Có phải làm mới trong trình soạn thảo hay không (bool)
     */
    getList(refresh, is_edit) {
      let params;
      if (refresh) {
        params = {
          dir: '',
          superior: 0,
          filedir: '',
          fileToken: this.fileToken,
        };
      } else {
        params = this.formItem;
        params.fileToken = this.fileToken;
      }
      if (!is_edit) this.loading = true;
      opendirListApi(params)
        .then(async (res) => {
          let data = res.data;
          this.routeList = data.routeList;

          if (is_edit) {
            this.navList = data.navList;
          } else {
            this.navListForTab = data.navList;
            this.tabList = data.list;
            // this.navList = data.navList;
            this.isShowList = true;
          }
          this.dir = data.dir;
          this.isShowLogn = false;
          this.loading = false;
        })
        .catch((res) => {
          this.catchFun(res);
        });
    },
    //Sau khi tạo file mới thì tải lại điều hướng bên trái
    getListItem(data) {
      opendirListApi(data)
        .then(async (res) => {
          this.$set(this.contextData, 'children', res.data.navList);
        })
        .catch((res) => {
          this.catchFun(res);
        });
    },

    // Quay lại cấp trên
    goBack(is_edit) {
      this.formItem = {
        dir: this.dir,
        superior: 1,
        filedir: '',
      };
      this.getList(false, is_edit);
    },
    // Mở
    open(row) {
      // this.rows = row;
      this.formItem = {
        dir: row.path,
        superior: 0,
        filedir: row.filename,
        fileToken: this.fileToken,
      };
      this.getList(false, false);
    },
    jumpRoute(item) {
      let data = {
        path: item.route,
        filename: '',
      };
      this.open(data);
    },
    refreshRoute() {
      let data = {
        path: this.routeList[this.routeList.length - 1].route,
        filename: '',
      };
      this.open(data);
    },
    // Sửaß
    edit(row) {
      this.navItem = row;
      this.spinShow = true;
      this.pathname = row.pathname;
      this.title = row.filename;
      this.editorIndex[0].title = row.filename;
      this.editorIndex[0].pathname = row.pathname;
      this.navList = this.navListForTab;
      this.dir = row.path;
      // Tạo container code
      if (this.editorList.length <= 0) {
        // this.initEditor();
      }
      this.openfile(row.pathname, false);
    },
    /**
     * Ghi chú
     */
    mark(row) {
      this.$modalForm(
        fileMark({
          path: row.pathname,
          fileToken: this.fileToken,
        }),
      ).then(() => this.getList(true, false));
    },
    /**
     * Lưu
     * @param {Object} index   // Chỉ số hiện tại
     * @param {Object} type    // true Không cập nhật dữ liệu cục bộ hiện tại, false hoặc rỗng thì cập nhật dữ liệu hiện tại
     */
    savefile(index, type) {
      let code = this.editorList[index].editor.getValue();
      let data = {
        comment: code,
        filepath: this.editorList[index].path,
        fileToken: this.fileToken,
      };
      let that = this;
      savefileApi(data)
        .then(async (res) => {
          if (!type) {
            that.code = code;
            that.isSave = true;
            that.editorIndex[index].icon = '';
            that.editorList[index].isSave = true;
          }
          that.$message.success(res.msg);
          that.$Modal.remove();
        })
        .catch((res) => {
          that.catchFun(res);
        });
    },
    // Làm mới
    refreshfile() {
      // Làm mới trình soạn thảo
      if (this.editorList[this.indexEditor]) this.openfile(this.editorList[this.indexEditor].path, true);
    },
    //Tính thời gian hết hạn token
    getExpiresTime(expiresTime) {
      let nowTimeNum = Math.round(new Date() / 1000);
      let expiresTimeNum = expiresTime - nowTimeNum;
      return parseFloat(parseFloat(parseFloat(expiresTimeNum / 60) / 60) / 24);
    },
    // Tải bất đồng bộ thanh bên
    loadData(item, callback) {
      if (!item.data.isLeaf) {
        this.formItem = {
          dir: item.data.path,
          superior: 0,
          filedir: item.data.title,
          fileToken: this.fileToken,
        };
        opendirListApi(this.formItem)
          .then(async (res) => {
            callback(res.data.navList);
          })
          .catch((res) => {
            if (res.status == 110008) {
              this.$message.error(res.msg);
              this.isShowLogn = true;
              this.isShowList = false;
              this.loading = false;
            } else {
              this.catchFun(res);
            }
          });
      }
    },
    // Hiển thị tùy chỉnh
    renderContent(h, { node, data, root }) {
      let that = this;
      return h(
        'span',
        {
          style: {
            display: 'inline-block',
            cursor: 'pointer',
            userSelect: 'null',
            color: '#cccccc',
            display: 'inline-block',
            width: '100%',
            borderRadis: '5px',
          },
          on: {
            click: () => {
              that.clickDir(data, root, node);
            },
            contextmenu: () => {
              // that.handleContextDelFolder(data,root,node);
            },
          },
        },
        [
          h('span', [
            h('Icon', {
              props: {
                type: !data.isLeaf ? 'md-folder' : 'ios-document-outline',
              },
              style: {
                marginRight: '8px',
              },
            }),
            h(
              'span',
              {
                attrs: {
                  title: data.title,
                },
              },
              data.title,
            ),
          ]),
        ],
      );
    },
    /**
     * Sự kiện nhấp sidebar
     * @param {Object} data
     */
    clickDir(data, root, node) {
      let that = this;
      that.navItem = data;
      that.pathname = data.pathname;

      if (!data.isDir) {
        let i = that.editorIndex.findIndex((e) => {
          return e.pathname === data.pathname;
        });
        if (i > -1) {
          that.indexEditor = i.toString();
          that.toggleEditor();
        } else {
          let index = that.editorIndex.length;
          // Tạo tabs
          that.editorIndex.push({
            tab: true,
            index: index.toString(),
            title: data.title,
            icon: '',
            pathname: data.pathname,
          });
          that.indexEditor = index.toString();
          // Tạo container code
          that.initEditor();
          that.openfile(data.pathname, true);
        }
      }
    },
    //Sự kiện nhấp chuột phải sidebar
    handleContextMenu(data, event, position) {
      position.left = Number(position.left.slice(0, -2)) + 75 + 'px';
      this.contextData = data;
    },
    // Loại thao tác file createFolder|tạo thư mục createFile|tạo file delFolder|xóa thư mục hoặc file renameFile|đổi tên file
    //Tạo thư mục
    handleContextCreateFolder() {
      this.formFile.filename = '';
      this.formTitle = 'Tạo thư mục';
      this.formShow = true;
      this.fileType = 'createFolder';
    },
    //Tạo tệp
    handleContextCreateFile() {
      this.formFile.filename = '';
      this.formTitle = 'Tạo tệp';
      this.formShow = true;
      this.fileType = 'createFile';
    },
    //Xóa file
    handleContextDelFolder() {
      let that = this;
      that.$Modal.confirm({
        title: 'Xóa thư mục và tệp',
        content: 'Bạn có chắc chắn muốn xóa tệp này?',
        loading: true,
        onOk: () => {
          let data = {
            path: that.contextData.pathname,
            fileToken: this.fileToken,
          };
          delFolder(data)
            .then(async (res) => {
              that.loopDel(that.navList, that.contextData.nodeKey);
              that.$Modal.remove();
              that.$message.success('Xóa thành công');
            })
            .catch((res) => {
              that.catchFun(res);
            });
        },
        onCancel: () => {
          that.$message.info('Đã hủy xóa');
        },
      });
    },
    //Đổi tên
    handleContextRename() {
      this.formFile.filename = this.contextData.title;
      this.formTitle = 'Đổi tên tệp';
      this.formShow = true;
      this.fileType = 'renameFile';
    },
    //Mở file
    openfile(path, is_edit) {
      let that = this;
      let params = {
        filepath: path,
        fileToken: this.fileToken,
      };

      openfileApi(params)
        .then(async (res) => {
          if (!is_edit) {
            that.modals = true;
            that.spinShow = false;
            this.initEditor();
          }
          let data = res.data;
          that.code = data.content;
          // Lưu thông tin liên quan

          that.editorList[that.indexEditor].oldCode = that.code;
          this.$nextTick((e) => {
            that.editorList[that.indexEditor || 0].path = path;
            that.editorList[that.indexEditor || 0].pathname = path;
          });
          //Thay đổi thuộc tính
          that.changeModel(data.mode, that.code);
        })
        .catch((res) => {
          that.catchFun(res);
        });
    },
    /**
     * Khởi tạo trình soạn thảo
     */
    initEditor() {
      let that = this;
      that.$nextTick(() => {
        // Khởi tạo trình soạn thảo, đảm bảo dom đã được render
        that.editor = monaco.editor.create(document.getElementById('container_' + that.indexEditor), {
          value: that.code, //Văn bản hiển thị ban đầu của trình soạn thảo
          language: 'sql', //Hỗ trợ ngôn ngữ, tự tham khảo demo
          automaticLayout: true, //Tự động bố cục
          theme: 'vs', //Chính thức có sẵn 3 chủ đề vs, hc-black, or vs-dark
          foldingStrategy: 'indentation', // Code có thể gập theo từng đoạn nhỏ
          overviewRulerBorder: false, // Không cần viền thanh cuộn
          scrollbar: {
            // Thiết lập thanh cuộn
            verticalScrollbarSize: 4, // Thanh cuộn dọc
            horizontalScrollbarSize: 10, // Thanh cuộn ngang
          },
          autoIndent: true, // Tự động bố cục
          tabSize: 4, // Độ dài thụt lề tab
          autoClosingOvertype: 'always',
        });
        //Thêm lắng nghe phím bấm
        that.editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S, function () {
          that.savefile(that.indexEditor);
        });
        that.editor.onKeyUp(() => {
          // Khi nhấn phím, kiểm tra văn bản hiện tại trong trình soạn thảo có khớp với văn bản đã lưu hay không
          if (that.editor.getValue() != that.code) {
            that.isSave = false;
            that.editorIndex[that.indexEditor].icon = 'md-warning';
            that.editorList[that.indexEditor].isSave = false;
          }
        });
        that.editorList.push({
          editor: that.editor,
          oldCode: that.code,
          path: this.pathname,
          isSave: true,
          index: that.indexEditor,
        });
      });
    },
    /**
     * Chuyển đổi ngôn ngữ
     * @param {Object} mode
     */
    changeModel(mode, value) {
      var oldModel = this.editorList[this.indexEditor].editor.getModel(); //Lấy model cũ
      // var value = this.editor.getValue();//Lấy văn bản cũ
      //Tạo model mới, value là văn bản cũ, id là modeId, tức ngôn ngữ (language.id)
      //modesIds chính là ngôn ngữ được hỗ trợ
      // var modesIds = monaco.languages.getLanguages().map(function(lang) { return lang.id; });
      if (!mode) mode = oldModel.getLanguageId();
      // if(!value) value = this.editor.getValue();

      var newModel = monaco.editor.createModel(value, mode);
      //Hủy model cũ
      if (oldModel) {
        oldModel.dispose();
      }
      //Thiết lập model mới
      this.editorList[this.indexEditor].editor.setModel(newModel);
    },
    // Loại thao tác file createFolder|tạo thư mục createFile|tạo file delFolder|xóa thư mục hoặc file
    handleSubmit(name) {
      let that = this;
      let data = '';
      let dataItem = '';
      this.$refs[name].validate((valid) => {
        if (valid) {
          switch (that.fileType) {
            case 'createFolder':
              data = {
                path: that.contextData.pathname,
                name: that.formFile.filename,
                fileToken: this.fileToken,
              };
              createFolder(data)
                .then(async (res) => {
                  dataItem = {
                    dir: that.contextData.path,
                    superior: 0,
                    filedir: that.contextData.title,
                    fileToken: this.fileToken,
                  };
                  that.getListItem(dataItem);
                  if (that.formShow) that.formShow = false;
                  that.$message.success('Tạo thành công');
                })
                .catch((res) => {
                  that.catchFun(res);
                });
              break;
            case 'createFile':
              data = {
                path: that.contextData.pathname,
                name: that.formFile.filename,
                fileToken: this.fileToken,
              };
              createFile(data)
                .then(async (res) => {
                  dataItem = {
                    dir: that.contextData.path,
                    superior: 0,
                    filedir: that.contextData.title,
                    fileToken: this.fileToken,
                  };
                  that.getListItem(dataItem);
                  if (that.formShow) that.formShow = false;
                  that.$message.success('Tạo thành công');
                })
                .catch((res) => {
                  that.catchFun(res);
                });
              break;
            case 'renameFile':
              data = {
                newname: that.contextData.path + '\\' + that.formFile.filename,
                oldname: that.contextData.pathname,
                fileToken: this.fileToken,
              };
              rename(data)
                .then(async (res) => {
                  that.$set(that.contextData, 'title', that.formFile.filename);
                  that.$message.success('Sửa thành công');
                  if (that.formShow) that.formShow = false;
                })
                .catch((res) => {
                  that.catchFun(res);
                });
              break;
          }
        } else {
          this.$message.error('Fail!');
        }
      });
    },
    /**
     * Đóng form
     */
    formExit() {
      this.formShow = false;
    },

    /**
     * Xử lý callback API
     * @param {Object} res
     */
    catchFun(res) {
      if (res.status) {
        if (res.status == 400) this.$message.error(res.msg);
        if (res.status == 110008) {
          // this.$message.error(res.msg);
          this.isShowLogn = true;
          this.isShowList = false;
          this.loading = false;
        }
      } else {
        // this.$message.error('Bảng mã file không tương thích, không thể đọc file đúng cách!');
      }
      //Đóng lớp phủ
      if (this.spinShow) this.spinShow = false;
      // Đóng hiển thị danh sách file
      if (this.loading) this.loading = false;
    },
    loopDel(data, nodeKey) {
      data.forEach((item, index) => {
        if (item.nodeKey === nodeKey) {
          return data.splice(index, 1);
        }
        if (item.children.length > 0) {
          return this.loopDel(item.children, nodeKey);
        }
      });
    },
    /**
     * Tối đa hóa cửa sổ
     */
    winChanges() {
      if (this.className) {
        this.className = '';
      } else {
        this.className = 'diy-fullscreen';
      }
    },
    /**
     * Chuyển đổi tab
     * @param {Object} index
     */
    toggleEditor() {
      let index = Number(this.indexEditor);
      this.code = this.editorList[index].oldCode; //Thiết lập code khi file được mở
      this.editor = this.editorList[index].editor; //Thiết lập instance trình soạn thảo
    },
    isEditMark(row) {
      try {
        row.is_edit = true;
        this.$nextTick((e) => {
          this.$refs.mark.focus();
        });
      } catch (error) {
        console.log(error);
      }
    },
    isEditBlur(row) {
      row.is_edit = false;
      let data = {
        full_path: row.real_path,
        mark: row.mark,
      };
      markSave(this.fileToken, data)
        .then((res) => {
          // this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    handleTabRemove(index) {
      let that = this;

      // Đóng tab
      that.editorIndex[index].tab = false; // Đóng tab
      // Kiểm tra file hiện tại đã lưu hay chưa
      if (!that.editorList[index].isSave) {
        that.$Modal.confirm({
          title: 'Tệp chưa được lưu',
          content: 'Bạn có muốn lưu tệp hiện tại không',
          loading: true,
          onOk: () => {
            // Lưu tệp
            that.savefile(index);
          },
          onCancel: () => {
            that.$message.info('Đã hủy lưu');
          },
        });
      }
    },
    //Trạng thái trình soạn thảo thay đổi
    editModalChange() {
      let that = this;
      that.editorList.forEach(function (value, index) {
        if (value.isSave === false) {
          if (confirm(`Tệp ${that.editorIndex[index].title} chưa được lưu, bạn có muốn lưu tệp này không`)) {
            // Lưu file hiện tại
            that.savefile(index, true);
          } else {
            that.$message.info(`Đã hủy lưu tệp ${that.editorIndex[index].title}`);
          }
        }
        // Hủy trình soạn thảo hiện tại
        that.editorList[index].editor.dispose();
        that.editorList[index].editor = null;
      });
      // Khởi tạo dữ liệu
      that.modals = false; //Công tắc trình soạn thảo
      that.editor = ''; //Đối tượng trình soạn thảo hiện tại
      that.editorIndex = [
        //Mảng tab
        {
          tab: true,
          index: '0',
          title: '',
          icon: '',
        },
      ];
      that.editorList = []; //Mảng trình soạn thảo
      that.indexEditor = '0'; //Chỉ số trình soạn thảo hiện tại
      that.code = ''; //Nội dung khi file hiện tại được mở
      that.navList = []; //Dữ liệu điều hướng bên trái
      that.navItem = {}; //Dữ liệu được chọn khi click vào điều hướng bên trái
      that.contextData = null; //Đối tượng dữ liệu được tạo ra khi nhấp chuột phải vào điều hướng bên trái
    },
  },
};
</script>
<style scoped>
.file-left ::v-deep .ivu-tree-title {
  font-weight: 500;
  font-family: SourceHanSansSC-regular, 'Microsoft YaHei', Arial, Helvetica, sans-serif;
}
.file-content ::v-deep .ivu-tabs.ivu-tabs-card > .ivu-tabs-bar .ivu-tabs-tab-active {
  border-bottom: 1px solid orange;
}
</style>
<style lang="scss" scoped>
.file-left {
  padding-left: 10px;
  color: #cccccc;
}
.mr5 {
  margin-right: 5px;
}
.backs-box {
  display: flex;
  justify-content: space-between;
  min-width: 800px;
  max-width: max-content;
  border: 1px solid #cfcfcf;
  background: #f6f6f6;
  .refresh {
    background: #fff;
    border-left: 1px solid #cfcfcf;
    padding: 0 8px 0 10px;
    font-size: 16px;
    font-weight: bold;
  }
  .refresh {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .refresh:hover,
  .back:hover {
    background: #2d8cf0;
    border-color: #38983b;
    color: #fff;
  }
}
.file-name {
  cursor: pointer;
}
.backs {
  cursor: pointer;
  display: inline-block;
  display: flex;
  align-items: center;
  width: 100%;
  .back {
    height: 100%;
    background: #fff;
    border-right: 1px solid #cfcfcf;
    padding: 6px 8px 0 10px;
    font-size: 16px;
    font-weight: bold;
  }
  .item:last-child {
    padding-right: 5px !important;
  }
  .item {
    padding: 0 0 0 8px;
    font-size: 12px;
    line-height: 33px;
    color: #555;
    display: flex;
    align-items: center;
    .key {
      margin-right: 3px;
    }
  }
  .item:hover {
    background: #fff;
  }
}
::v-deep .CodeMirror {
  height: 70vh !important;
}
.file-box {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  position: relative;
  min-height: calc(100% - 35px);
  overflow: hidden;
}
.file-box {
  .file-left {
    position: absolute;
    top: 58px;
    left: 0;
    height: calc(100% - 58px);

    width: 25%;
    max-width: 250px;
    overflow: auto;
    background-color: #292929;
  }
  .file-fix {
    flex: 1;
    max-width: 250px;
    min-height: calc(100% - 35px);

    min-height: calc(100% - 35px);
    background-color: #292929;
  }
}
.file-box {
  .file-content {
    flex: 3;
    overflow: hidden;
    min-height: calc(100% - 35px);
    height: 100%;
  }
}
::v-deep .el-dialog__body {
  padding: 0 !important;
  height: 80vh;
  max-height: 80vh;
}
.diy-button {
  height: 35px;
  padding: 0 15px;
  font-size: 13px;
  text-align: center;
  color: #fff;
  border: 0;
  border-right: 1px solid #4c4c4c;
  cursor: pointer;
  border-radius: 0;
  background-color: #565656;
}
.form-mask {
  z-index: -1;
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  background: rgba(0, 0, 0, 0.3);
}
.table-mark {
  cursor: text;
}
.table-mark:hover {
  border: 1px solid #c2c2c2;
  padding: 3px 5px;
}
.mark ::v-deep .el-input__inner {
  background: #fff;
  border-radius: 0.39rem;
}
.mark ::v-deep .el-input__inner,
.el-input__inner:hover,
.el-input__inner:focus {
  border: transparent;
  box-shadow: none;
}
.diy-from-header {
  height: 30px;
  line-height: 30px;
  background-color: #fff;
  text-align: left;
  padding-left: 20px;
  font-size: 16px;
  margin-bottom: 15px;

  span {
    display: inline-block;
    float: right;
    color: #999;
    text-align: right;
    font-size: 12px;
    width: 280px;
    word-break: keep-all; /* Không xuống dòng */
    white-space: nowrap; /* Không xuống dòng */
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
.diy-from {
  z-index: 9999;
  width: 400px;
  height: 100px;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  text-align: center;
  background-color: #2f2f2f;
}
.top-button {
  background-color: #292929;
}
.show-info {
  background-color: #292929;
  color: #fff;
  width: 25%;
  max-width: 250px;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1122;
  .diy-button {
    width: 50%;
    height: 25px;
    line-height: 8px;
  }
  .diy-button-list {
    display: flex;
    align-items: center;
  }
  .show-text {
    padding-left: 10px;
    word-break: keep-all; /* Không xuống dòng */
    white-space: nowrap; /* Không xuống dòng */
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 7px 5px;
  }
}

body ::v-deep .ivu-select-dropdown {
  background: #fff;
}
::v-deep .el-tabs__item {
  background-color: #fff;
}
::v-deep .el-tree {
  background-color: #292929 !important;
}
.file-box {
  .file-left::-webkit-scrollbar {
    width: 4px;
  }
}
.file-box {
  .file-left::-webkit-scrollbar-thumb {
    border-radius: 10px;
    -webkit-box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
    background: rgba(255, 255, 255, 0.2);
  }
}
.file-box {
  .file-left::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
    border-radius: 0;
    background: rgba(0, 0, 0, 0.1);
  }
}
.diy-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  .diy-header-icon {
    margin-right: 30px;
    cursor: pointer;
  }
  .diy-header-icon:hover {
    opacity: 0.8;
  }
}
::v-deep .diy-fullscreen {
  overflow: hidden;
  .ivu-modal {
    top: 0px;
    left: 0px;
    right: 0px;
    bottom: 0px;
    height: 100%;
    width: 100% !important;
    .ivu-modal-content {
      height: 100%;
      .ivu-modal-body {
        height: 100%;
      }
    }
    .ivu-tabs {
      .ivu-tabs-content-animated {
        height: 92%;
        background-color: #2f2f2f !important;
      }
    }
    .ivu-tabs-content {
      height: 100%;
    }
    .ivu-tabs {
      .ivu-tabs-tabpane {
        height: 92%;
      }
    }
  }
}
::v-deep .ivu-modal {
  top: 70px;
}
.ivu-modal-content {
  .ivu-modal-body {
    min-height: 632px;
    height: 80vh;
    overflow: hidden;
  }
}
.ivu-tabs {
  .ivu-tabs-content-animated {
    min-height: 580px;
    height: 73vh;
    margin-top: -1px;
  }
  .ivu-tabs-tabpane {
    min-height: 580px;
    height: 73vh;
    margin-top: -1px;
  }
}
.ivu-tabs-nav .ivu-tabs-tab .ivu-icon {
  color: #f00;
}
::v-deepbody .ivu-select-dropdown .ivu-dropdown-transfer {
  background: red !important;
}
.file-left ::v-deep .ivu-select-dropdown.ivu-dropdown-transfer .ivu-dropdown-menu .ivu-dropdown-item:hover {
  background-color: #e5e5e5 !important;
}
::v-deep .ivu-tabs.ivu-tabs-card > .ivu-tabs-bar .ivu-tabs-nav-container {
  background-color: #333;
}
</style>
