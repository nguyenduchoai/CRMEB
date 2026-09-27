<template>
  <div class="Modal">
    <div class="colLeft">
      <div class="Nav">
        <div class="trees-coadd">
          <div v-if="isPage" class="tree_tit" v-db-click @click="addSort">
            <i class="el-icon-circle-plus"></i>
            Thêm danh mục
          </div>
          <div class="scollhide">
            <div :class="isPage ? 'tree' : 'isTree'">
              <el-tree
                :data="treeData"
                node-key="id"
                default-expand-all
                highlight-current
                :expand-on-click-node="false"
                @node-click="appendBtn"
                :current-node-key="treeId"
              >
                <span class="custom-tree-node" slot-scope="{ data }">
                  <!-- <span class="file-name">
                    <i class="icon el-icon-folder-remove"></i>
                    {{ data.title }}</span
                  > -->
                  <!-- <span class="file-name">
                    <img v-if="!data.pid" class="icon" src="@/assets/images/file.jpg" />
                    <span class="name line1">{{ data.title }}</span>
                  </span> -->
                  <div class="file-name">
                    <img v-if="!data.pid" class="icon" src="@/assets/images/file.jpg" />
                    <el-tooltip class="item" effect="dark" :content="data.title" placement="top">
                      <div class="text line1">
                        {{ data.title }}
                      </div>
                    </el-tooltip>
                  </div>
                  <span>
                    <el-dropdown @command="(command) => clickMenu(data, command)">
                      <i class="el-icon-more el-icon--right"></i>
                      <template slot="dropdown">
                        <el-dropdown-menu>
                          <el-dropdown-item command="1">Thêm danh mục</el-dropdown-item>
                          <el-dropdown-item v-if="data.id" command="2">Sửa danh mục</el-dropdown-item>
                          <el-dropdown-item v-if="data.id" command="3">Xóa</el-dropdown-item>
                        </el-dropdown-menu>
                      </template>
                    </el-dropdown>
                  </span>
                </span>
              </el-tree>
            </div>
          </div>
        </div>
      </div>
      <div class="conter">
        <div class="bnt acea-row row-middle df-jcsb">
          <div class="">
            <el-button
              class="mr8"
              type="primary"
              :disabled="checkPicList.length === 0"
              v-db-click
              @click="checkPics"
              size="small"
              v-if="isShow !== 0"
              >Dùng video đã chọn</el-button
            >
            <!-- <el-button size="small" type="primary" v-db-click @click="uploadModal">Tải video lên</el-button> -->
            <el-button
              class="mr8"
              v-if="upload_type !== '1'"
              type="primary"
              size="small"
              v-db-click
              @click="zh_uploadFile"
              >Tải lên video</el-button
            >
            <el-upload
              v-if="upload_type === '1'"
              :show-file-list="false"
              :action="fileUrl"
              :before-upload="videoSaveToUrl"
              :data="uploadData"
              :headers="header"
              :multiple="true"
              style="display: inline-block"
              accept=".mp4"
            >
              <el-button class="mr8" size="small" type="primary">Tải lên video</el-button>
            </el-upload>
            <!-- Nhập liên kết -->
            <el-button class="mr8" size="small" type="primary" icon="el-icon-link" @click="openInputModal"></el-button>
            <el-button
              class="mr8"
              size="small"
              :disabled="!checkPicList.length && !ids.length"
              v-db-click
              @click.stop="editPicList()"
              >Xóa video</el-button
            >
            <el-cascader
              v-model="pids"
              placeholder="Chuyển video đến"
              style="width: 150px"
              class="treeSel"
              :options="treeData2"
              :props="{ checkStrictly: true, emitPath: false, label: 'title', value: 'id' }"
              clearable
              size="small"
              @visible-change="moveImg"
            ></el-cascader>
          </div>
          <div>
            <el-input
              class="mr8"
              v-model="fileData.real_name"
              placeholder="Vui lòng nhập tên video"
              size="small"
              style="width: 150px"
              @change="searchFile"
            >
              <i slot="suffix" class="el-icon-search el-input__icon" v-db-click @click="getFileList"></i>
            </el-input>
            <el-radio-group class="mr10" v-if="isPage" v-model="lietStyle" size="small" @input="radioChange">
              <el-radio-button label="list">
                <i class="el-icon-menu"></i>
              </el-radio-button>
              <el-radio-button label="table">
                <!-- <i class="el-icon-files"></i> -->
                <span class="iconfont iconliebiao"></span>
              </el-radio-button>
            </el-radio-group>
          </div>
        </div>
        <div class="pictrueList acea-row" :class="{ 'is-modal': !isPage }">
          <div v-if="lietStyle == 'list'" style="width: 100%">
            <div v-show="isShowPic" class="imagesNo">
              <i class="el-icon-picture" style="color: #dbdbdb; font-size: 60px"></i>
              <span class="imagesNo_sp">Thư viện video trống</span>
            </div>
            <div ref="imgListBox" class="acea-row mb10">
              <div
                class="pictrueList_pic mb10 mt10"
                v-for="(item, index) in pictrueList"
                :key="index"
                :style="{ margin: picmargin }"
                @mouseenter="enterMouse(item)"
                @mouseleave="enterMouse(item)"
              >
                <p class="number" v-if="item.num > 0">
                  <el-badge :value="item.num" type="primary">
                    <a href="#" class="demo-badge"></a>
                  </el-badge>
                </p>
                <div
                  class="img"
                  :class="item.isSelect ? 'on' : ''"
                  v-db-click
                  @click.stop="changImage(item, index, pictrueList)"
                >
                  <video :src="item.satt_dir" />
                </div>

                <div class="operate-item" @mouseenter="enterLeave(item)" @mouseleave="enterLeave(item)">
                  <p v-if="!item.isEdit">
                    {{ item.editName }}
                  </p>
                  <el-input size="small" type="text" v-model="item.real_name" v-else @blur="bindTxt(item)" />
                  <div class="operate-height">
                    <span class="operate mr10" v-db-click @click="editPicList(item.att_id)" v-if="item.isShowEdit"
                      >Xóa</span
                    >
                    <span class="operate mr10" v-db-click @click="item.isEdit = !item.isEdit" v-if="item.isShowEdit"
                      >Đổi tên</span
                    >
                    <span class="operate" v-db-click @click="lookImg(item)" v-if="item.isShowEdit">Xem</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <el-table
            v-if="lietStyle == 'table'"
            ref="table"
            :data="pictrueList"
            v-loading="loading"
            highlight-row
            :row-key="getRowKey"
            @selection-change="handleSelectRow"
            no-data-text="Chưa có dữ liệu"
            no-filtered-data-text="Không có kết quả phù hợp"
          >
            <el-table-column type="selection" width="60" :reserve-selection="true"> </el-table-column>
            <el-table-column label="Tên video" min-width="190">
              <template slot-scope="scope">
                <div class="df-aic">
                  <div class="tabBox_img mr10">
                    <video :src="scope.row.att_dir" @click="lookImg(scope.row)" />
                  </div>
                  <span v-if="!scope.row.isEdit" class="line2 real-name">{{ scope.row.real_name }}</span>
                  <el-input
                    size="small"
                    type="text"
                    style="width: 90%"
                    v-model="scope.row.real_name"
                    v-else
                    @blur="bindTxt(scope.row)"
                  />
                </div>
              </template>
            </el-table-column>
            <el-table-column label="Thời gian tải lên" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.time }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thao tác" fixed="right" width="170">
              <template slot-scope="scope">
                <a v-db-click @click="editPicList(scope.row)">Xóa</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="scope.row.isEdit = !scope.row.isEdit">{{
                  scope.row.isEdit ? 'Xác nhận' : 'Đổi tên'
                }}</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="lookImg(scope.row)">Xem</a>
              </template>
            </el-table-column>
          </el-table>
        </div>
        <div class="footer acea-row row-right">
          <pagination
            v-if="total"
            :total="total"
            :pageCount="9"
            layout="total, prev, pager, next"
            :page.sync="fileData.page"
            @pagination="pageChange"
            :limit.sync="fileData.limit"
          ></pagination>
        </div>
      </div>
    </div>
    <uploadImg
      ref="upload"
      :isPage="isPage"
      :isIframe="isIframe"
      :categoryId="treeId"
      :categoryList="treeData"
      @uploadSuccess="uploadSuccess"
    ></uploadImg>
    <el-dialog title="Xem video" append-to-body :visible.sync="videoModal" width="1024px">
      <video :src="imageUrl" controls />
    </el-dialog>
    <input type="file" ref="refid" style="display: none" @change="zh_uploadFile_change" />
    <!-- Hộp thoại nhập liên kết -->
    <el-dialog title="Nhập liên kết video" append-to-body :visible.sync="inputModal" width="400px">
      <div class="flex">
        <el-input class="mr-20" v-model="inputUrl" placeholder="Vui lòng nhập liên kết video" />
        <el-button type="primary" @click="uploadByUrl">Đã sử dụng</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  getCategoryListApi,
  createApi,
  fileListApi,
  categoryEditApi,
  moveApi,
  fileUpdateApi,
  videoCloudUpload,
} from '@/api/uploadPictures';
import { productGetTempKeysApi, uploadType } from '@/api/product';
import { uploadByPieces } from '@/utils/upload'; //Import phương thức uploadByPieces

import Setting from '@/setting';
import { getCookies } from '@/libs/util';
import uploadImg from '@/components/uploadImg';
import { VueTreeList, Tree, TreeNode } from 'vue-tree-list';
import { isVideoUpload } from '@/utils';
export default {
  name: 'uploadPictures',
  components: { uploadImg, VueTreeList },
  props: {
    isChoice: {
      type: String,
      default: 'one',
    },
    isPage: {
      type: Boolean,
      default: false,
    },
    isIframe: {
      type: Boolean,
      default: false,
    },
    gridBtn: {
      type: Object,
      default: null,
    },
    gridPic: {
      type: Object,
      default: null,
    },
    isShow: {
      type: Number,
      default: 1,
    },
    pageLimit: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      spinShow: false,
      fileUrl: Setting.apiBaseURL + '/file/upload',
      modalPic: false,
      treeData: [],
      treeData2: [],
      pictrueList: [],
      uploadData: {}, // Tham số tải lên
      checkPicList: [],
      uploadName: {
        name: '',
        all: 1,
        type: 1,
      },
      formValidate: { id: 0 },
      FromData: null,
      treeId: '',
      isJudge: false,
      buttonProps: {
        type: 'default',
        size: 'small',
      },
      fileData: {
        pid: 0,
        real_name: '',
        page: 1,
        limit: this.pageLimit || 18,
        type: 1,
      },
      total: 0,
      pids: 0,
      list: [],
      modalTitleSs: '',
      isShowPic: false,
      header: {},
      ids: [], // Tập hợp id của các tệp đính kèm đã chọn
      lietStyle: 'list',
      imageUrl: '',
      loading: false,
      multipleSelection: [],
      picmargin: '5px', //Khoảng cách mặc định tới bên phải
      videoModal: false,
      upload_type: '',
      upload: {
        videoIng: false, // Có hiển thị thanh tiến trình không;
      },
      inputModal: false, // Hộp thoại nhập liên kết
      inputUrl: '', // Liên kết video đã nhập
    };
  },
  mounted() {
    if (this.isPage) {
      let hang = parseInt((document.body.clientHeight - this.$refs.imgListBox.clientHeight - 325) / 180); //Tính số dòng
      let col = parseInt(this.$refs.imgListBox.clientWidth / 156); //Tính số cột
      this.fileData.limit = col * hang; //Tính số lượng phân trang
      this.picmargin = parseInt(this.$refs.imgListBox.clientWidth - col * 146) / (2 * col) + 'px'; //Tính khoảng cách margin phân bố đều
    }
    this.getToken();
    this.uploadType();
    this.getList();
    this.getFileList();
  },
  methods: {
    // Mở hộp thoại nhập liên kết
    openInputModal() {
      this.inputModal = true;
    },
    //Lấy loại tải lên video
    uploadType() {
      uploadType().then((res) => {
        this.upload_type = res.data.upload_type;
      });
    },
    beforeUpload() {
      this.uploadData = {};
      let promise = new Promise((resolve) => {
        this.$nextTick(function () {
          resolve(true);
        });
      });
      return promise;
    },
    zh_uploadFile() {
      this.$refs.refid.click();
    },
    zh_uploadFile_change(evfile) {
      let that = this;
      if (evfile.target.files[0].type !== 'video/mp4') {
        return that.$message.error('Chỉ được tải lên tệp mp4');
      }
      debugger;
      let types = {
        key: evfile.target.files[0].name,
        contentType: evfile.target.files[0].type,
      };
      productGetTempKeysApi(types).then((res) => {
        that.$videoCloud
          .videoUpload({
            type: res.data.type,
            evfile: evfile,
            res: res,
            uploading(status, progress) {
              that.upload.videoIng = status;
            },
          })
          .then((res) => {
            videoCloudUpload({
              pid: this.treeId || 0,
              video_path: res.url,
              video_name: evfile.target.files[0].name,
            }).then((res) => {
              this.getFileList();
            });
          })
          .catch((res) => {
            that.$message.error(res);
          });
      });
    },
    videoSaveToUrl(file) {
      if (isVideoUpload(file))
        uploadByPieces({
          file: file, // Đối tượng video
          pieceSize: 3, // Kích thước từng phần
          success: (data) => {
            this.progress = 100;
            videoCloudUpload({
              pid: this.treeId || 0,
              video_path: data.file_path,
              video_name: file.name,
            }).then((res) => {
              this.getFileList();
              this.$message.success('Tải lên video thành công');
            });
          },
          error: (e) => {
            this.$message.error(e.msg);
          },
          uploading: (chunk, allChunk) => {
            this.videoIng = true;
            let st = Math.floor((chunk / allChunk) * 100);
            this.progress = st;
          },
        });
      return false;
    },
    radioChange() {
      this.initData();
    },
    lookImg(item) {
      this.imageUrl = item.att_dir;
      this.videoModal = true;
    },
    onDel(node) {
      let method = node.cate_id ? routeDel : routeCateDel;
      this.$msgbox({
        title: 'Thông báo',
        message: 'Bạn có chắc chắn muốn xóa menu này không',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xóa',
        iconClass: 'el-icon-warning',
        confirmButtonClass: 'btn-custom-cancel',
      })
        .then(() => {
          method(node.id)
            .then((res) => {
              this.$message.success(res.msg);
              node.remove();
            })
            .catch((err) => {
              this.$message.error(err);
            });
        })
        .catch(() => {});
    },

    onChangeName(params) {
      if (params.eventType == 'blur') {
        let data = {
          name: params.newName,
          id: params.id,
        };
        interfaceEditName(data)
          .then((res) => {
            this.$message.success(res.msg);
          })
          .catch((err) => {
            this.$message.error(err);
          });
      }
    },
    // Thêm danh mục
    addSort() {
      this.append({ id: this.treeId || 0 });
    },
    // Bấm vào menu
    clickMenu(data, name) {
      if (name == 1) {
        this.append(data);
      } else if (name == 2) {
        this.editPic(data);
      } else if (name == 3) {
        this.remove(data, 'Danh mục');
      }
    },
    uploadSuccess() {
      this.fileData.page = 1;
      this.initData();
      this.getFileList();
    },
    uploadModal() {
      this.$refs.upload.uploadModal = true;
    },
    enterMouse(item) {
      item.realName = !item.realName;
    },
    enterLeave(item) {
      item.isShowEdit = !item.isShowEdit;
    },
    // Token trong header khi tải lên
    getToken() {
      this.header['Authori-zation'] = 'Bearer ' + getCookies('token');
    },
    moveImg(status) {
      if (!status) {
        this.getMove();
      } else {
        if (!this.ids.toString()) {
          this.$message.warning('Vui lòng chọn video trước');
          return;
        }
      }
    },
    searchImg() {},
    // Di chuyển danh mục
    getMove() {
      let data = {
        pid: this.pids,
        images: this.ids.toString(),
      };
      if (!data.images) return;
      moveApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getFileList();
          this.pids = 0;
          this.checkPicList = [];
          this.ids = [];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    delImg(id) {
      let ids = {
        ids: id,
      };
      let delfromData = {
        title: 'Xóa video đã chọn',
        url: `file/file/delete`,
        method: 'POST',
        ids: ids,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getFileList();
          this.checkPicList = [];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Xóa video
    editPicList(id) {
      let ids = {
        ids: id || this.ids.toString(),
      };
      let delfromData = {
        title: 'Xóa video đã chọn',
        url: `file/file/delete`,
        method: 'POST',
        ids: ids,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getFileList();
          this.initData();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    initData() {
      this.checkPicList = [];
      this.ids = [];
      this.multipleSelection = [];
    },
    // Chuột di vào / di ra
    onMouseOver(root, node, data) {
      event.preventDefault();
      data.flag = !data.flag;
      if (data.flag2) {
        data.flag2 = false;
      }
    },
    // Bấm vào cây (tree)
    appendBtn(data) {
      this.treeId = data.id;
      this.fileData.page = 1;
      this.getFileList();
    },
    // nhấn để thêm
    append(data) {
      this.treeId = data.id;
      this.getFrom();
    },
    // Xóa danh mục
    remove(data, tit) {
      this.tits = tit;
      let delfromData = {
        title: 'xóa [ ' + data.title + ' ] ' + 'Danh mục',
        url: `file/category/${data.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
          this.checkPicList = [];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Form sửa cây (tree)
    editPic(data) {
      this.$modalForm(categoryEditApi(data.id)).then(() => this.getList());
    },
    // Tìm kiếm danh mục
    changePage() {
      this.getList('search');
    },
    // Cây danh sách danh mục
    getList(type) {
      let data = {
        title: 'Tất cả video',
        id: '',
        pid: 0,
      };
      getCategoryListApi(this.uploadName)
        .then(async (res) => {
          if (type !== 'search') {
            this.treeData2 = JSON.parse(JSON.stringify([...res.data.list]));
          }
          res.data.list.unshift(data);
          this.treeData = res.data.list;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    loadData(item, callback) {
      getCategoryListApi({
        pid: item.id,
      })
        .then(async (res) => {
          const data = res.data.list;
          callback(data);
        })
        .catch((res) => {});
    },
    addFlag(treedata) {
      treedata.map((item) => {
        this.$set(item, 'flag', false);
        this.$set(item, 'flag2', false);
        item.children && this.addFlag(item.children);
      });
    },
    // Tạo danh mục mới
    add() {
      this.treeId = 0;
      this.getFrom();
    },
    searchFile() {
      this.fileData.page = 1;
      this.getFileList();
    },
    // Danh sách tệp
    getFileList() {
      this.fileData.pid = this.treeId;
      fileListApi(this.fileData)
        .then(async (res) => {
          res.data.list.forEach((el) => {
            el.isSelect = false;
            el.isEdit = false;
            el.isShowEdit = false;
            el.realName = false;
            el.num = 0;
            this.editName(el);
          });
          this.pictrueList = res.data.list;

          if (this.pictrueList.length) {
            this.isShowPic = false;
          } else {
            this.isShowPic = true;
          }
          this.total = res.data.count;
          this.$nextTick(() => {
            //Đảm bảo dom đã tải xong
            // this.showSelectData();
          });
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    showSelectData() {
      if (this.multipleSelection.length > 0) {
        // Kiểm tra có dữ liệu đã tick chọn không
        this.pictrueList.forEach((row) => {
          // Dữ liệu lấy được từ API danh sách dữ liệu
          this.multipleSelection.forEach((item) => {
            // Dữ liệu đã tick chọn
            if (row.att_id === item.att_id) {
              // this.$refs.table.toggleRowSelection(item, true); // Nếu có trùng khớp thì hiển thị lại dòng dữ liệu này
            }
          });
        });
      }
    },
    getRowKey(row) {
      return row.att_id;
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      let result = arr.reduce((acc, curr) => {
        const x = acc.find((item) => item.att_id === curr.att_id);
        if (!x) {
          return acc.concat([curr]);
        } else {
          return acc;
        }
      }, []);
      return result;
    },
    //  Chọn một dòng
    handleSelectRow(selection) {
      let arr = this.unique(selection);
      const uniqueArr = [];
      const ids = [];
      for (let i = 0; i < arr.length; i++) {
        const item = arr[i];
        if (!ids.includes(item.att_id)) {
          uniqueArr.push(item);
          ids.push(item.att_id);
        }
      }
      this.ids = ids;
      this.multipleSelection = uniqueArr;
    },
    pageChange(index) {
      this.fileData.page = index;
      this.getFileList();
      this.checkPicList = [];
    },
    // Form tạo danh mục mới
    getFrom() {
      this.$modalForm(createApi({ id: this.treeId, type: 1 })).then((res) => {
        this.getList();
      });
    },
    // Trước khi tải lên
    beforeUpload(file) {
      // if (file.size > 2097152) {
      //   this.$message.error(file.name + "Kích thước vượt quá 2M!");
      // } else
      if (!/image\/\w+/.test(file.type)) {
        this.$message.error('Vui lòng tải lên tệp video có đuôi như jpg, jpeg, png'); //FileExt.toLowerCase()
        return false;
      }
      this.uploadData = {
        pid: this.treeId,
      };
      let promise = new Promise((resolve) => {
        this.$nextTick(function () {
          resolve(true);
        });
      });
      return promise;
    },
    // Tải lên thành công
    handleSuccess(res, file, fileList) {
      if (res.status === 200) {
        this.$message.success(res.msg);
        this.fileData.page = 1;
        this.getFileList();
      } else {
        this.$message.error(res.msg);
      }
    },
    // Tắt
    cancel() {
      this.$emit('changeCancel');
    },
    // Video đã chọn
    changImage(item, index, row) {
      let activeIndex = 0;
      if (!item.isSelect) {
        item.isSelect = true;
        this.checkPicList.push(item);
      } else {
        item.isSelect = false;
        this.checkPicList.map((el, index) => {
          if (el.att_id == item.att_id) {
            activeIndex = index;
          }
        });
        this.checkPicList.splice(activeIndex, 1);
      }

      this.ids = [];
      this.checkPicList.map((item, i) => {
        this.ids.push(item.att_id);
      });
      this.pictrueList.map((el, i) => {
        if (el.isSelect) {
          this.checkPicList.filter((el2, j) => {
            if (el.att_id == el2.att_id) {
              el.num = j + 1;
            }
          });
        } else {
          el.num = 0;
        }
      });
    },
    // Bấm dùng video đã chọn
    checkPics() {
      if (this.isChoice === 'one') {
        if (this.checkPicList.length > 1) return this.$message.warning('Chỉ được chọn tối đa một video');
        this.$emit('getVideo', this.checkPicList[0].att_dir);
      } else {
        let maxLength = this.$route.query.maxLength;
        if (maxLength != undefined && this.checkPicList.length > Number(maxLength))
          return this.$message.warning('Chỉ được chọn tối đa ' + maxLength + ' video');
        this.$emit('getPicD', this.checkPicList);
        this.$emit('getVideo', this.checkPicList);
      }
    },
    // Tải lên video qua liên kết
    uploadByUrl() {
      if (!this.inputUrl) {
        this.$message.error('Vui lòng nhập liên kết video');
        return;
      }
      this.$emit('getVideo', this.inputUrl);
      this.$emit('getPicD', this.inputUrl);
      this.inputUrl = '';
    },
    editName(item) {
      let it = item.real_name.split('.');
      let it1 = it[1] == undefined ? [] : it[1];
      let len = it[0].length + it1.length;
      item.editName = len < 10 ? item.real_name : item.real_name.substr(0, 4) + '...' + item.real_name.substr(-5, 5);
    },
    // Sửa tải lên video/chữ
    bindTxt(item) {
      if (item.real_name == '') {
        this.$message.error('Vui lòng điền nội dung');
      }
      fileUpdateApi(item.att_id, {
        real_name: item.real_name,
      })
        .then((res) => {
          this.editName(item);
          item.isEdit = false;
          this.$message.success(res.msg);
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
  },
};
</script>

<style scoped lang="scss">
.nameStyle {
  position: absolute;
  white-space: nowrap;
  z-index: 9;
  background: #eee;
  height: 20px;
  line-height: 20px;
  color: #555;
  border: 1px solid #ebebeb;
  padding: 0 5px;
  left: 56px;
  bottom: -18px;
}

.iconbianji1 {
  font-size: 13px;
}

.selectTreeClass {
  background: #d5e8fc;
}
.tree_tit {
  padding-top: 7px;
}
.treeBox {
  width: 100%;
  height: 100%;
  max-width: 180px;
}
.is-modal .pictrueList_pic {
  width: 100px;
  margin: 10px 5px !important;
  .img {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
    background-color: rgb(248, 248, 248);
    padding: 2px;
    img {
      max-width: 96px;
      max-height: 96px;
      // object-fit: cover;
    }
    .operate-height {
      bottom: -8px;
    }
  }
}
.pictrueList_pic {
  position: relative;
  width: 146px;
  cursor: pointer;
  // margin-right: 20px !important;
  .img {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 146px;
    height: 146px;
    background-color: rgb(248, 248, 248);
    padding: 3px;
    video {
      max-width: 100px;
      max-height: 100px;
      // object-fit: cover;
    }
  }

  p {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    height: 20px;
    font-size: 12px;
    color: #515a6d;
    text-align: center;
  }

  .number {
    height: 33px;
  }

  .number {
    position: absolute;
    right: 0;
    top: 0;
  }
  ::v-deep .el-badge__content.is-fixed {
    top: 13px;
    right: 25px;
  }
}
.Nav {
  width: 100%;
  border-right: 1px solid #eee;
  min-width: 220px;
  max-width: max-content;
}
.trees-coadd {
  width: 100%;
  border-radius: 4px;
  overflow: hidden;
  position: relative;

  .scollhide {
    overflow-x: hidden;
    overflow-y: scroll;
    padding: 0px 0 10px 0;
    box-sizing: border-box;

    .isTree {
      min-height: 374px;
      max-height: 550px;
      ::v-deep .file-name {
        display: flex;
        align-items: center;
        .name {
          max-width: 7em;
        }
        .icon {
          width: 12px;
          height: 12px;
          margin-right: 8px;
        }
      }
      ::v-deep .el-tree-node {
        margin-right: 16px;
      }
      ::v-deep .el-tree-node__children .el-tree-node {
        margin-right: 0;
      }
      ::v-deep .el-tree-node__content {
        width: 100%;
        height: 36px;
      }
      ::v-deep .custom-tree-node {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-right: 20px;
        font-size: 13px;
        font-weight: 400;
        color: rgba(0, 0, 0, 0.6);
        line-height: 13px;
      }
      ::v-deep .is-current {
        background: #f1f9ff !important;
        color: var(--prev-color-primary) !important;
      }
      ::v-deep .is-current .custom-tree-node {
        color: var(--prev-color-primary) !important;
      }
    }
  }

  .scollhide::-webkit-scrollbar {
    display: none;
  }
}

.treeSel ::v-deep .ivu-select-dropdown-list {
  padding: 0 5px !important;
  box-sizing: border-box;
  width: 200px;
}
.imagesNo {
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  margin: 65px 0;

  .imagesNo_sp {
    font-size: 13px;
    color: #dbdbdb;
    line-height: 3;
  }
}

.Modal {
  width: 100%;
  height: 100%;
  background: #fff !important;
}
.fill-window {
  height: 100vh;
}
.colLeft {
  padding-right: 0 !important;
  height: 100%;
  display: flex;
  flex-wrap: nowrap;
}

.conter {
  width: 100%;
  height: 100%;
  margin-left: 20px !important;
  .iconliebiao {
    font-size: 12px;
  }
}

.conter .bnt {
  width: 100%;
  padding: 0 0px 20px 0px;
  box-sizing: border-box;
}

.conter .pictrueList {
  // width: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  min-height: 463px;
}
.conter .pictrueList.is-modal {
  max-height: 480px;
}
.right-col {
  // flex: 1;
}
.conter .pictrueList img {
  max-width: 100%;
}
.conter .pictrueList .img.on {
  border: 2px solid var(--prev-color-primary);
}

.conter .footer {
  padding: 0 20px 10px 20px;
}
.tabBox_img {
  display: flex;
  align-items: center;
  video {
    max-width: 90px;
    max-height: 30px;
  }
}
.real-name {
  flex: 1;
}
.df-aic {
  display: flex;
  align-items: center;
}
.demo-badge {
  width: 42px;
  height: 42px;
  background: transparent;
  border-radius: 6px;
  display: inline-block;
}

.bnt ::v-deep .ivu-tree-children {
  padding: 5px 0;
}

.card-tree {
  background: #fff;
  height: 72px;
  box-sizing: border-box;
  overflow-x: scroll; /* Đặt overflow scroll */
  white-space: nowrap;
  overflow-y: hidden;
  /* Ẩn thanh cuộn */
  border-radius: 4px;
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
}
.card-tree::-webkit-scrollbar {
  display: none; /* Chrome Safari */
}
.tabs {
  background: #fff;
  padding-top: 10px;
  border-radius: 5px 5px 0 0;
}
.operate-item {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  margin: 5px 0;
}
.operate-height {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 16px;
  position: absolute;
  bottom: -10px;
}
.operate {
  color: var(--prev-color-primary);
  font-size: 12px;
  white-space: nowrap;
}
</style>
