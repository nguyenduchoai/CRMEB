<template>
  <el-card :bordered="false" shadow="never" class="ivu-mt">
    <div class="Modal">
      <div class="colLeft">
        <div class="Nav">
          <div class="trees-coadd">
            <div class="tree_tit" v-db-click @click="append">
              <i class="el-icon-circle-plus"></i>
              Thêm danh mục
            </div>
            <div class="scollhide">
              <div class="tree" v-if="treeData.length">
                <el-tree
                  ref="tree"
                  :data="treeData"
                  node-key="id"
                  default-expand-all
                  :highlight-current="highlightCurrent"
                  :expand-on-click-node="true"
                  :check-on-click-node="false"
                  @node-click="appendBtn"
                  @node-expand="nodeExpand"
                  :current-node-key="treeId"
                >
                  <span class="custom-tree-node" slot-scope="{ data }">
                    <div class="file-name">
                      <img v-if="data.pid == 1" class="icon" src="@/assets/images/file.jpg" />
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
                            <el-dropdown-item v-if="data.pid == 1" command="1">Thêm danh mục</el-dropdown-item>
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
          <el-button type="primary" @click="addLink">Thêm liên kết</el-button>
          <el-table
            :data="linkList"
            ref="couponTable"
            class="mt20"
            v-loading="loading"
            highlight-current-row
            no-userFrom-text="Chưa có dữ liệu"
            no-filtered-userFrom-text="Không có kết quả phù hợp"
          >
            <el-table-column label="ID" width="70">
              <template slot-scope="scope">
                <span>{{ scope.row.id }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Tên" width="150">
              <template slot-scope="scope">
                <span>{{ scope.row.name }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Liên kết H5" minWidth="170">
              <template slot-scope="scope">
                <el-tooltip class="item pointer" content="Nhấn để sao chép">
                  <span v-db-click @click="onCopy(scope.row.h5_url)">{{ scope.row.h5_url }}</span>
                </el-tooltip>
              </template>
            </el-table-column>
            <el-table-column label="Liên kết Mini Program" minWidth="140">
              <template slot-scope="scope">
                <el-tooltip class="item pointer" content="Nhấn để sao chép">
                  <span v-db-click @click="onCopy(scope.row.url)">{{ scope.row.url }}</span>
                </el-tooltip>
              </template>
            </el-table-column>
            <el-table-column label="Thời gian thêm" minWidth="90">
              <template slot-scope="scope">
                <span>{{ scope.row.add_time }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thao tác" fixed="right" width="90">
              <template slot-scope="scope">
                <a v-db-click @click="edit(scope.row)">Sửa</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="del(scope.row, 'Xóa liên kết', scope.$index)">Xóa</a>
              </template>
            </el-table-column>
          </el-table>
          <div class="acea-row row-right page">
            <pagination
              v-if="total"
              :total="total"
              :page.sync="fileData.page"
              :limit.sync="fileData.limit"
              @pagination="getLinkList"
            />
          </div>
        </div>
      </div>
    </div>
    <el-dialog
      title="Liên kết"
      :visible.sync="dialogVisible"
      width="40%"
      :before-close="handleClose"
      :close-on-click-modal="false"
    >
      <el-form :model="linkForm" ref="linkForm" label-width="80px">
        <el-form-item label="Tên:" prop="name">
          <el-input v-model="linkForm.name" placeholder="Vui lòng nhập tên"></el-input>
        </el-form-item>
        <el-form-item label="Liên kết chuyển hướng:" prop="url">
          <el-input v-model="linkForm.url" placeholder="Vui lòng nhập liên kết chuyển hướng"></el-input>
        </el-form-item>
        <el-form-item label="Thứ tự sắp xếp:" prop="url">
          <el-input v-model="linkForm.sort" placeholder="Vui lòng nhập thứ tự sắp xếp"></el-input>
        </el-form-item>
        <el-form-item label="Bật:" prop="url">
          <el-switch v-model="linkForm.status" :active-value="1" :inactive-value="0"></el-switch>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">Hủy</el-button>
        <el-button type="primary" @click="addLinkSubmit">Xác nhận</el-button>
      </div>
    </el-dialog>
  </el-card>
</template>

<script>
import { diyLinkCategoryListApi, linkCategoryFormApi, linkListApi, linkCreateApi } from '@/api/setting';
export default {
  name: 'linkLisk',
  props: {},
  data() {
    return {
      dialogVisible: false,
      treeData: [],
      linkList: [],
      uploadName: {
        name: '',
        all: 1,
      },
      treeId: 0,
      fileData: {
        page: 1,
        limit: 15,
      },
      total: 0,
      pids: 0,

      loading: false,
      multipleSelection: [],
      linkForm: {
        id: 0,
        name: '',
        url: '',
        sort: 0,
        status: 1,
      },
      highlightCurrent: true,
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    handleClose() {
      this.dialogVisible = false;
      this.initData();
    },
    onCopy(copyData) {
      this.$copyText(copyData)
        .then((message) => {
          this.$message.success('Sao chép thành công');
        })
        .catch((err) => {
          this.$message.error('Sao chép thất bại');
        });
    },
    edit(row) {
      this.linkForm.name = row.name;
      this.linkForm.url = row.url;
      this.linkForm.sort = row.sort;
      this.linkForm.status = row.status;
      this.linkForm.id = row.id;
      this.dialogVisible = true;
    },
    addLinkSubmit() {
      this.$refs.linkForm.validate((valid) => {
        if (valid) {
          let data = {
            cate_id: this.treeId,
            id: this.linkForm.id,
            name: this.linkForm.name,
            url: this.linkForm.url,
            sort: this.linkForm.sort,
            status: this.linkForm.status,
          };
          linkCreateApi(data)
            .then((res) => {
              this.$message.success(res.msg);
              this.dialogVisible = false;
              this.getLinkList();
            })
            .catch((err) => {
              this.$message.error(err);
            });
        } else {
          return false;
        }
      });
    },
    // Bấm vào menu
    clickMenu(data, name) {
      if (name == 1) {
        this.editLinkCategory(data, 0);
      } else if (name == 2) {
        this.editLinkCategory(data, 1);
      } else if (name == 3) {
        this.remove(data, 'Danh mục');
      }
    },
    delImg(id) {
      let ids = {
        ids: id,
      };
      let delfromData = {
        title: 'Xóa ảnh đã chọn',
        url: `file/file/delete`,
        method: 'POST',
        ids: ids,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getLinkList();
          this.checkPicList = [];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Xóa ảnh
    editPicList(id) {
      let ids = {
        ids: id || this.ids.toString(),
      };
      let delfromData = {
        title: 'Xóa ảnh đã chọn',
        url: `file/file/delete`,
        method: 'POST',
        ids: ids,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getLinkList();
          this.initData();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    initData() {
      this.fileData.page = 1;
      this.fileData.id = '';
      this.linkForm.name = '';
      this.linkForm.h5_url = '';
      this.linkForm.url = '';
    },
    // Chuột di vào / di ra
    onMouseOver(root, node, data) {
      event.preventDefault();
      data.flag = !data.flag;
      if (data.flag2) {
        data.flag2 = false;
      }
    },
    addLink() {
      this.initData();
      this.dialogVisible = true;
    },
    // Bấm vào cây (tree)
    appendBtn(data, n, d) {
      if (data.pid == 1 && !n.expanded) {
        this.highlightCurrent = false;
        return;
      } else if (data.pid != 1) {
        this.treeId = data.id;
        this.fileData.page = 1;
        this.getLinkList();
      }
    },
    nodeExpand(data, n, d) {
      if (n.expanded) {
        if (data.children.length) {
          this.highlightCurrent = true;
          this.$nextTick(() => {
            this.fileData.page = 1;
            this.treeId = data.children[0].id || '';
            this.$refs['tree'].setCurrentKey(this.treeId);
            this.getLinkList();
          });
        }
      }
    },
    // nhấn để thêm
    append() {
      this.getFrom();
    },
    // Xóa danh mục
    remove(data, tit) {
      this.tits = tit;
      let delfromData = {
        title: 'xóa [ ' + data.title + ' ] ' + 'Danh mục',
        url: `diy/link/category/del/${data.id}`,
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
    editLinkCategory(data, type) {
      this.$modalForm(linkCategoryFormApi(type ? data.id : 0, data.id ? data.id : 1)).then(() => this.getList());
    },
    // Cây danh sách danh mục
    getList(type) {
      diyLinkCategoryListApi(this.uploadName)
        .then((res) => {
          this.treeId = res.data.length > 0 ? res.data[0]?.children[0]?.id : '';
          this.treeData = res.data;
          this.getLinkList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách tệp
    getLinkList() {
      this.fileData.id = this.treeId;
      this.loading = true;
      linkListApi(this.fileData)
        .then(async (res) => {
          this.linkList = res.data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `diy/link/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.linkList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    addFlag(treedata) {
      treedata.map((item) => {
        this.$set(item, 'flag', false);
        this.$set(item, 'flag2', false);
        item.children && this.addFlag(item.children);
      });
    },
    pageChange(index) {
      this.fileData.page = index;
      this.getLinkList();
      this.checkPicList = [];
    },
    // Form tạo danh mục mới
    getFrom() {
      this.$modalForm(linkCategoryFormApi(0, 1)).then((res) => {
        this.getList();
      });
    },
  },
};
</script>

<style scoped lang="scss">
.box {
  width: 100%;
  height: 100%;
  background: #fff;
}
::v-deep .el-card__body {
  min-height: 700px;
  padding: 16px 16px 16px 0;
}
::v-deep .conter .linkList {
  max-width: 100%;
}
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

.tree_tit {
  padding-top: 7px;
  padding-bottom: 22px;
}
.treeBox {
  width: 100%;
  height: 100%;
  max-width: 180px;
}
.Nav {
  width: 100%;
  border-right: 1px solid #eee;
  min-width: 220px;
  max-width: max-content;
}
::v-deep .tree .is-current {
  background-color: #fff !important;
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

.conter .linkList {
  // width: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  min-height: 463px;
}
.conter .linkList.is-modal {
  max-height: 480px;
}
.conter .linkList img {
  max-width: 100%;
}
.conter .linkList .img.on {
  border: 2px solid var(--prev-color-primary);
}

.conter .footer {
  padding: 0 20px 10px 20px;
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
</style>
