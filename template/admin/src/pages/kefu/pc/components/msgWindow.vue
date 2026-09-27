<template>
  <div class="msg-box">
    <div class="head">
      <div class="tab-bar">
        <a
          href="javascript:;"
          class="tab-item"
          :class="{ on: item.key == tabCur }"
          v-for="(item, index) in tabList"
          :key="index"
          v-db-click
          @click="bindTab(item)"
          >{{ item.title }}</a
        >
      </div>
      <div class="search-box">
        <el-input placeholder="Tìm kiếm câu trả lời nhanh" style="width: 100%" v-model="searchTxt" />
      </div>
    </div>
    <div class="main">
      <div class="left-box">
        <vue-scroll :ops="ops">
          <div class="left-item" v-if="tabCur">
            <p>Nhóm</p>
            <span class="iconfont iconaddto" v-db-click @click="openAddSort"></span>
          </div>
          <div
            class="left-item"
            v-for="(item, index) in sortList"
            :key="index"
            :class="{ on: cateId == item.id }"
            v-db-click
            @click="selectSort(item)"
          >
            <p>{{ item.name }}</p>
            <template v-if="tabCur">
              <span class="iconfont iconDot" v-db-click @click.top="bindEdit(item, scope.$index)"></span>

              <div class="edit-wrapper" v-show="item.isEdit">
                <div class="edit-item" v-db-click @click="editSort(item)">Sửa</div>
                <div class="edit-item" v-db-click @click="delSort(item, 'Xóa danh mục', scope.$index)">Xóa</div>
              </div>
              <div class="edit-bg" v-show="item.isEdit" v-db-click @click.stop="item.isEdit = false"></div>
            </template>
          </div>
        </vue-scroll>
      </div>
      <div class="right-box">
        <div
          v-infinite-scroll="handleReachBottom"
          class="right-scroll"
          :infinite-scroll-immediate="false"
          :infinite-scroll-delay="500"
          style="overflow: auto"
        >
          <div class="msg-item add-box" v-if="tabCur" style="margin-top: 0">
            <div class="box2">
              <el-input
                class="input-box"
                v-model="addMsg.title"
                placeholder="Nhập tiêu đề (không bắt buộc)"
                style="width: 100%"
                @on-focus="bindFocus"
              />
              <div class="conBox" :class="{ active: addMsg.isEdit }">
                <div class="content">
                  <el-input v-model="addMsg.message" type="textarea" :rows="4" placeholder="Vui lòng nhập nội dung" />
                </div>
                <div class="bom">
                  <div class="select">
                    <el-select v-model="addMsg.cateId" style="width: 100px" size="small">
                      <el-option v-for="item in sortList" :value="item.id" :key="item.id">{{ item.name }} </el-option>
                    </el-select>
                  </div>
                  <div class="btns-box">
                    <el-button v-db-click @click.stop="addMsg.isEdit = false">Hủy</el-button>
                    <el-button type="primary" v-db-click @click.stop="bindAdd">Lưu</el-button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="msg-item" v-for="(item, index) in list" :key="index" v-if="item.id">
            <div class="box1" v-if="!item.isEdit">
              <div class="txt-box" v-db-click @click="bindRadio(item)">
                <span class="title" v-if="item.title">{{ item.title | filtersTitle }}</span>
                <span v-if="item.message">{{ item.message | filtersCon }}</span>
              </div>
              <div class="edit-box" v-if="tabCur">
                <span class="iconfont iconbianji" v-db-click @click.stop="editMsg(item)"></span>
                <span class="iconfont iconshanchu" v-db-click @click.stop="delMsg(item, 'Xóa câu trả lời mẫu', index)"></span>
              </div>
            </div>
            <div class="box2" v-else>
              <el-input class="input-box" v-model="item.title" placeholder="Nhập tiêu đề (không bắt buộc)" style="width: 100%" />
              <div class="content">
                <el-input v-model="item.message" type="textarea" :rows="4" placeholder="Vui lòng nhập nội dung" />
              </div>
              <div class="bom">
                <div class="select">
                  <el-select v-model="cateId" style="width: 100px" size="small">
                    <el-option v-for="item in sortList" :value="item.id" :key="item.id" :label="item.name"></el-option>
                  </el-select>
                </div>
                <div class="btns-box">
                  <el-button v-db-click @click.stop="item.isEdit = false">Hủy</el-button>
                  <el-button type="primary" v-db-click @click.stop="updataMsg(item)">Lưu</el-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <el-dialog :visible.sync="isAddSort" append-to-body :title="maskTitle" width="304px" class="class-box">
      <div class="item">
        <span>Tên nhóm:</span>
        <el-input v-model="classTitle" placeholder="Tên nhóm" />
      </div>
      <div class="item">
        <span>Thứ tự nhóm:</span>
        <el-input v-model="classSort" placeholder="Nhập thứ tự sắp xếp" />
      </div>
      <div class="btn"></div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" v-db-click @click="addServiceCate">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  speeChcraft,
  serviceCate,
  serviceCateUpdate,
  addSpeeChcraft,
  addServiceCate,
  editServiceCate,
} from '@/api/kefu';
export default {
  name: 'msgWindow',
  data() {
    return {
      ops: {
        vuescroll: {
          mode: 'native',
          enable: false,
          tips: {
            deactive: 'Push to Load',
            active: 'Release to Load',
            start: 'Loading...',
            beforeDeactive: 'Load Successfully!',
          },
          auto: false,
          autoLoadDistance: 0,
          pullRefresh: {
            enable: false,
          },
          pushLoad: {
            enable: false,
            auto: true,
            autoLoadDistance: 10,
          },
        },
        bar: {
          background: '#393232',
          opacity: '.5',
          size: '2px',
        },
      },
      isScroll: true,
      page: 1,
      limit: 15,
      tabCur: 1,
      tabList: [
        {
          title: 'Thư viện cá nhân',
          key: 1,
        },
        {
          title: 'Thư viện chung',
          key: 0,
        },
      ],
      searchTxt: '', // Tìm kiếm
      list: [
        {
          isEdit: false,
        },
      ], // Danh sách
      model1: '',
      msgTitle: '', // Tiêu đề đã nhập
      sortList: [], // Danh mục
      cateId: '', // ID đã chọn
      addMsg: {
        title: '',
        message: '',
        cateId: '',
        isEdit: false,
      },
      isAddSort: false, // Thêm danh mục
      classTitle: '', // Tên danh mục
      classSort: '', // Thứ tự danh mục
      maskTitle: '', // Tiêu đề popup
      editObj: {}, // Đối tượng danh mục đang sửa
    };
  },
  filters: {
    filtersTitle(val) {
      let len = 37;
      if (val.length > len) {
        let data = val.substring(0, len);
        return `${data}...`;
      } else {
        return val;
      }
    },
    filtersCon(val) {
      let len = 113;
      if (val.length > len) {
        let data = val.substring(0, len);
        return `${data}...`;
      } else {
        return val;
      }
    },
  },
  mounted() {
    let self = this;
    this.serviceCate();
    this.$nextTick(() => {
      // this.scroll = new BScroll(this.$refs.wrapper, {
      //   mouseWheel: {
      //     speed: 20,
      //     invert: false,
      //     easeTime: 300,
      //   },
      //   scrollbar: true,
      //   disableMouse: true,
      //   // and so on
      // });
    });
  },
  methods: {
    // Mở sửa
    editMsg(item) {
      item.isEdit = true;
      this.cateId = item.cate_id;
    },
    // Khung sửa
    bindEdit(item, index) {
      //   if (index == 0) {
      //     return;
      //   } else {
      item.isEdit = !item.isEdit;
      //   }
    },
    // Chọn ở phần đầu
    bindTab(item) {
      console.log('1122');
      debugger;
      this.tabCur = item.key;
      this.cateId = '';
      this.sortList = [];
      this.isScroll = true;
      this.page = 1;
      this.list = [];
      this.serviceCate();
    },
    // Tìm kiếm
    bindSearch() {
      this.isScroll = true;
      this.page = 1;
      this.list = [];
      this.getList();
    },
    // Chọn danh mục
    selectSort(item) {
      if (this.cateId == item.id) {
        return;
      }
      this.sortList.forEach((el, index) => {
        if (el.id != item.id) {
          el.isEdit = false;
        }
      });
      this.cateId = item.id;
      this.isScroll = true;
      this.page = 1;
      this.list = [];
      this.getList();
    },
    // Xóa danh mục
    delSort(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `/service/cate/${row.id}`,
        method: 'DELETE',
        ids: '',
        kefu: true,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.isScroll = true;
          this.page = 1;
          this.list = [];
          this.cateId = '';
          this.serviceCate();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Lấy danh mục
    serviceCate() {
      serviceCate({
        type: this.tabCur,
      }).then((res) => {
        let obj = {
          id: '',
          name: 'Tất cả',
        };
        res.data.data.forEach((el, index) => {
          el.isEdit = false;
        });
        // res.data.data.unshift(obj)
        this.sortList = res.data.data;
        if (this.cateId === '') {
          this.cateId = res.data.data[0].id;
        }
        this.getList();
      });
    },
    // Lấy danh sách
    getList() {
      if (!this.isScroll) return;
      speeChcraft({
        page: this.page,
        limit: this.limit,
        title: this.searchTxt,
        cate_id: this.cateId,
        type: this.tabCur,
      }).then((res) => {
        this.isScroll = res.data.length >= this.limit;
        res.data.forEach((el, index) => {
          el.isEdit = false;
        });
        this.page++;
        this.list = this.list.concat(res.data);
      });
    },
    // Sửa câu trả lời mẫu
    updataMsg(item) {
      serviceCateUpdate(item.id, {
        title: item.title,
        cate_id: this.cateId,
        message: item.message,
      })
        .then((res) => {
          this.$message.success('Sửa thành công');
          item.isEdit = false;
        })
        .catch((error) => {
          this.$message.error(error.msg);
          item.isEdit = true;
        });
    },
    // Hiển thị khung thêm
    bindFocus() {
      this.list.forEach((el, item) => {
        el.isEdit = false;
      });
      this.addMsg.isEdit = true;
    },
    // Mở cửa sổ thêm
    openAddSort() {
      this.isAddSort = true;
      this.maskTitle = 'Thêm nhóm';
      this.editObj.id = 0;
    },
    // Thêm câu trả lời mẫu
    bindAdd() {
      addSpeeChcraft({
        title: this.addMsg.title,
        cate_id: this.addMsg.cateId,
        message: this.addMsg.message,
      })
        .then((res) => {
          this.addMsg.title = '';
          this.addMsg.message = '';
          this.addMsg.cateId = '';
          this.addMsg.isEdit = false;
          this.$message.success(res.msg);
          res.data.isEdit = false;
          this.page = 1;
          this.list = [];
          this.isScroll = true;
          this.serviceCate();
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    // Xóa
    delMsg(row, tit, num, type) {
      let delfromData = {
        title: tit,
        num: num,
        url: `service/speechcraft/${row.id}`,
        method: 'DELETE',
        ids: '',
        kefu: true,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.list.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thêm danh mục
    addServiceCate() {
      if (this.editObj.id) {
        editServiceCate(this.editObj.id, {
          name: this.classTitle,
          sort: this.classSort,
        })
          .then((res) => {
            this.classTitle = '';
            this.classSort = '';
            this.$message.success(res.msg);
            this.isAddSort = false;
            this.page = 1;
            this.list = [];
            this.isScroll = true;
            this.serviceCate();
          })
          .catch((error) => {
            this.classTitle = '';
            this.classSort = '';
            this.$message.error(error.msg);
          });
      } else {
        addServiceCate({
          name: this.classTitle,
          sort: this.classSort,
        })
          .then((res) => {
            this.classTitle = '';
            this.classSort = '';
            this.$message.success(res.msg);
            this.isAddSort = false;
            this.page = 1;
            this.list = [];
            this.isScroll = true;
            this.serviceCate();
          })
          .catch((error) => {
            this.classTitle = '';
            this.classSort = '';
            this.$message.error(error.msg);
          });
      }
    },
    // Sửa danh mục
    editSort(item) {
      this.classSort = item.sort;
      this.classTitle = item.name;
      this.isAddSort = true;
      this.maskTitle = 'Sửa nhóm';
      this.editObj = item;
    },
    handleReachBottom() {
      this.getList();
    },
    bindRadio(data) {
      this.$emit('activeTxt', data.message);
    },
  },
};
</script>

<style lang="scss" scoped>
.head {
  .tab-bar {
    display: flex;
    .tab-item {
      margin-right: 24px;
      color: #999;
      font-size: 14px;
      font-weight: 500;
      &.on {
        color: #333333;
      }
    }
  }
  .search-box {
    margin-top: 15px;
  }
}
.main {
  display: flex;
  margin-top: 15px;
  height: 365px;
  .left-box {
    width: 106px;
    height: 100%;
    border-right: 1px solid #ececec;
    overflow: hidden;
    .left-item {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 36px;
      padding: 0 10px 0 14px;
      font-size: 13px;
      cursor: pointer;
      &.on {
        background: var(--prev-color-primary-light-9);
        color: var(--prev-color-primary);
        border-right: 2px solid var(--prev-color-primary);
        .iconDot {
          z-index: 1;
          opacity: 1;
        }
      }
      &:nth-child(1).on,
      &:nth-child(2).on {
        .iconDot {
          display: none;
        }
      }
      .iconaddto {
        font-size: 12px;
      }
      .iconDot {
        z-index: -1;
        opacity: 0;
      }
      .edit-wrapper {
        z-index: 50;
        position: absolute;
        right: -2px;
        top: -4px;
        background: #fff;
        width: 80px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        .edit-item {
          padding: 8px 16px;
          color: #666 !important;
          cursor: pointer;
        }
      }
      .edit-bg {
        z-index: 40;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: transparent;
      }
    }
  }
  .right-box {
    flex: 1;
    padding: 0 12px;
    overflow-x: hidden;
    .msg-item {
      margin-top: 12px;
      transition: all 0.3s ease;
      cursor: pointer;
      .box1 {
        position: relative;
        display: flex;
        .txt-box {
          flex: 1;
          font-size: 12px;
          color: #999999;
          .title {
            max-width: 370px;
            margin-right: 5px;
            color: #333;
            font-weight: 700;
          }
        }
        .edit-box {
          z-index: -1;
          opacity: 0;
          position: absolute;
          right: 7px;
          top: 0;
          width: 60px;
          height: 30px;
          background: #fff;
          .iconfont {
            margin: 0 8px;
            color: #000000;
            font-size: 16px;
            cursor: pointer;
          }
        }
      }
      .box2 {
        padding-bottom: 15px;
        border-radius: 5px;
        background: #f5f5f5;
        .input-box {
          border-bottom: 1px solid #eeeeee;
          ::v-deep .ivu-input {
            background: transparent;
            border: 0;
            border-radius: 0;
          }
        }
        .content {
          font-size: 12px;
          padding: 12px 11px 0;
          color: #333333;
        }
        .bom {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0 20px 0 11px;
          margin-top: 10px;

          button {
            margin-left: 8px;
            width: 70px;
          }
        }
      }
      &:hover {
        transition: all 0.3s ease;
        .box1 .edit-box {
          z-index: 1;
          opacity: 1;
          transition: all 0.3s ease;
        }
      }
    }
    .add-box {
      border-radius: 0;
      margin-bottom: 10px;
      .box2 {
        padding-bottom: 0;
        border-radius: 0;
        .conBox {
          height: 0;
          overflow: hidden;
          &.active {
            animation: mymove 0.4s ease;
            animation-iteration-count: 1;
            animation-fill-mode: forwards;
          }
        }
      }
    }
  }
}
.right-scroll {
  height: 345px;
}
.class-box {
  .item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }

    input {
      flex: 1;
    }

    span {
      width: 80px;
      font-size: 12px;
    }
  }
}
</style>
<style>
@keyframes mymove {
  0% {
    height: 0;
  }
  100% {
    height: 150px;
  }
}
</style>
