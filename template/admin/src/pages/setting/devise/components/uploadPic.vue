<template>
  <div class="hot_imgs">
    <div class="list-box">
      <draggable class="dragArea list-group" :list="listData" group="peoples" handle=".move-icon">
        <div class="item" v-for="(item, index) in listData" :key="index">
          <div class="move-icon">
            <span class="iconfont icondrag"></span>
          </div>
          <div class="img-box" v-db-click @click="modalPicTap('Chọn một', index)">
            <img :src="item.pic" alt="" v-if="item.pic && item.pic != ''" />
            <div class="upload-box" v-else>
              <i class="el-icon-picture-outline" style="font-size: 24px"></i>
            </div>
          </div>
          <div class="info">
            <div class="info-item" v-if="item.hasOwnProperty('name')">
              <span>{{ type == 1 ? 'Tên mục quản lý:' : type == 5 ? 'Tên quảng cáo' : 'Tên dịch vụ:' }}</span>
              <div class="input-box">
                <el-input v-model="item.name" :placeholder="type == 5 ? 'Vui lòng nhập tên' : 'Trung tâm dịch vụ'" :maxlength="4" />
              </div>
            </div>
            <div class="info-item">
              <span>Địa chỉ liên kết:</span>
              <div class="input-box" v-db-click>
                <el-input v-model="item.url" placeholder="Chọn liên kết">
                  <i class="el-icon-link" slot="suffix" @click="getLink(index)" />
                </el-input>
              </div>
            </div>
            <!-- Công tắc có hiển thị hay không -->
            <div class="info-item">
              <span>Hiển thị:</span>
              <div class="input-box">
                <el-switch class="defineSwitch" v-model="item.is_show" active-value="1" inactive-value="0" />
              </div>
            </div>
          </div>
          <div v-if="type != 1" class="delect-btn" v-db-click @click.stop="bindDelete(item, index)">
            <span class="iconfont icondel_1 cup"></span>
          </div>
        </div>
      </draggable>
      <div>
        <el-dialog :visible.sync="modalPic" width="950px" title="Tải lên ảnh sản phẩm" :close-on-click-modal="false">
          <uploadPictures
            :isChoice="isChoice"
            @getPic="getPic"
            :gridBtn="gridBtn"
            :gridPic="gridPic"
            v-if="modalPic"
          ></uploadPictures>
        </el-dialog>
      </div>
    </div>
    <template v-if="listData">
      <div class="add-btn" v-if="type == 2 || (type == 5 && listData.length < 5)">
        <el-button type="primary" ghost style="width: 100px; color: #fff; font-size: 13px" v-db-click @click="addBox"
          >Thêm {{ type == 5 ? 'Quảng cáo' : 'Dịch vụ' }}</el-button
        >
      </div>
    </template>
    <linkaddress ref="linkaddres" @linkUrl="linkUrl"></linkaddress>
  </div>
</template>

<script>
import vuedraggable from 'vuedraggable';
import uploadPictures from '@/components/uploadPictures';
import linkaddress from '@/components/linkaddress';
export default {
  name: 'uploadPic',
  props: {
    listData: {
      type: Array,
    },
    type: {
      type: Number,
    },
  },
  components: {
    draggable: vuedraggable,
    uploadPictures,
    linkaddress,
  },
  data() {
    return {
      modalPic: false,
      isChoice: 'Chọn một',
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      activeIndex: 0,
      lastObj: {
        name: '',
        pic: '',
        url: '',
      },
    };
  },
  mounted() {},
  watch: {
    configObj: {
      handler(nVal, oVal) {},
      deep: true,
    },
  },
  methods: {
    linkUrl(e) {
      this.listData[this.activeIndex].url = e;
    },
    getLink(index) {
      this.activeIndex = index;
      this.$refs.linkaddres.modals = true;
    },
    addBox() {
      if (this.listData.length == 0) {
        this.listData.push(this.lastObj);
      } else {
        let obj = JSON.parse(JSON.stringify(this.listData[this.listData.length - 1]));
        obj.name = '';
        obj.pic = '';
        obj.url = '';
        this.listData.push(obj);
      }
      // this.$emit('parentFun',this.listData)
    },
    // Click vào ảnh bìa bài viết ảnh-văn
    modalPicTap(title, index) {
      this.activeIndex = index;
      this.modalPic = true;
    },
    // Thêm popup tùy chỉnh
    addCustomDialog(editorId) {
      window.UE.registerUI(
        'test-dialog',
        function (editor, uiName) {
          let dialog = new window.UE.ui.Dialog({
            iframeUrl: this.$routeProStr + '/widget.images/index.html?fodder=dialog',
            editor: editor,
            name: uiName,
            title: 'Tải lên ảnh',
            cssRules: 'width:960px;height:550px;padding:20px;',
          });
          this.dialog = dialog;
          // Tham khảo nút tùy chỉnh ở trên
          var btn = new window.UE.ui.Button({
            name: 'dialog-button',
            title: 'Tải lên ảnh',
            cssRules: `background-image: url(../../../assets/images/icons.png);background-position: -726px -77px;`,
            onclick: function () {
              // Render dialog
              dialog.render();
              dialog.open();
            },
          });

          return btn;
        },
        37,
      );
    },
    // Lấy thông tin ảnh
    getPic(pc) {
      this.$nextTick(() => {
        this.listData[this.activeIndex].pic = pc.att_dir;
        this.modalPic = false;
      });
    },
    // Xóa
    bindDelete(item, index) {
      if (this.listData.length == 1) {
        this.lastObj = this.listData[0];
      }
      this.listData.splice(index, 1);
      // this.$emit('parentFun',this.listData)
    },
  },
};
</script>

<style lang="scss" scoped>
.hot_imgs {
  margin-bottom: 20px;
  .title {
    padding: 0 0 13px 0;
    color: #999;
    font-size: 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }
  .list-box {
    .item {
      position: relative;
      display: flex;
      align-items: center;
      margin-top: 10px;
      border: 1px dashed rgba(0, 0, 0, 0.15);
      padding-right: 10px;
      border-radius: 6px;
      .move-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        cursor: move;
      }
      .img-box {
        position: relative;
        width: 50px;
        height: 50px;
        cursor: pointer;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .info {
        flex: 1;
        margin-left: 16px;
        padding-top: 10px;
        .info-item {
          display: flex;
          align-items: center;
          margin-bottom: 10px;

          span {
            width: 70px;
            font-size: 13px;
          }
          .input-box {
            flex: 1;
            ::v-deep input {
              cursor: pointer;
            }
          }
          ::v-deep .ivu-input {
            font-size: 13px !important;
          }
        }
      }
      .delect-btn {
        position: absolute;
        right: -13px;
        top: -13px;
        .iconfont {
          font-size: 25px;
          color: #ff1818;
        }
      }
    }
  }
  .add-btn {
    margin-top: 24px;
  }
}
.upload-box {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background: #ccc;
}
.iconfont {
  color: #dddddd;
  font-size: 28px;
}
</style>
