<template>
  <div class="tabBars">
    <div class="title">{{ datas[name].title }}</div>
    <draggable class="dragArea list-group" :list="datas[name].list" group="peoples" handle=".iconfont">
      <div class="box-item" v-for="(item, index) in datas[name].list" :key="index">
        <div class="left-tool">
          <span class="iconfont icondrag2"></span>
        </div>
        <div class="right-wrapper">
          <div class="img-wrapper">
            <div class="img-item" v-for="(img, j) in item.imgList" v-db-click @click="modalPicTap('Chọn một', index, j)">
              <img :src="img" alt="" v-if="img" />
              <p class="txt" v-if="img">{{ j == 0 ? 'Đã chọn' : 'Chưa chọn' }}</p>
              <div class="empty-img" v-else>
                <span class="iconfont iconjiahao"></span>
                <p>{{ j == 0 ? 'Đã chọn' : 'Chưa chọn' }}</p>
              </div>
            </div>
          </div>
          <div class="c_row-item">
            <el-col class="label" :span="4"> Tên </el-col>
            <el-col :span="19" class="slider-box">
              <el-input v-model="item.name" placeholder="Không bắt buộc, tối đa 10 ký tự" />
            </el-col>
          </div>
          <div class="c_row-item">
            <el-col class="label" :span="4"> Liên kết </el-col>
            <el-col :span="19" class="slider-box">
              <el-input v-model="item.link" placeholder="Không bắt buộc, tối đa 10 ký tự" />
            </el-col>
          </div>
        </div>
        <div class="del-box" v-db-click @click="deleteMenu(index)">
          <span class="iconfont iconcha"></span>
        </div>
      </div>
    </draggable>
    <div class="add-btn" v-if="datas[name].list.length < 5">
      <el-button
        type="primary"
        ghost
        style="width: 100%; height: 40px; border-color: var(--prev-color-primary); color: var(--prev-color-primary)"
        v-db-click
        @click="addMenu"
        >Thêm điều hướng hình ảnh
      </el-button>
    </div>
    <div>
      <el-dialog
        :visible.sync="modalPic"
        width="950px"
        scrollable
        footer-hide
        :show-close="true"
        title="Tải lên ảnh sản phẩm"
        :mask-closable="false"
        :z-index="888"
      >
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
</template>

<script>
import vuedraggable from 'vuedraggable';
import uploadPictures from '@/components/uploadPictures';
export default {
  name: 'c_tab_bar',
  props: {
    name: {
      type: String,
    },
    configData: {
      type: null,
    },
    configNum: {
      type: Number | String,
      default: 'default',
    },
  },
  components: {
    uploadPictures,
    draggable: vuedraggable,
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
      isSelect: 0,
      datas: this.configData[this.configNum],
      lastObj: {},
    };
  },
  mounted() {},
  watch: {
    configData: {
      handler(nVal, oVal) {
        this.datas = nVal[this.configNum];
      },
      deep: true,
    },
  },
  methods: {
    // Thêm mô-đun
    addMenu() {
      if (this.configData[this.configNum][this.name].list.length == 0) {
        this.configData[this.configNum][this.name].list.push(this.lastObj);
      } else {
        let obj = JSON.parse(
          JSON.stringify(
            this.configData[this.configNum][this.name].list[this.configData[this.configNum][this.name].list.length - 1],
          ),
        );
        this.configData[this.configNum][this.name].list.push(obj);
      }
    },
    deleteMenu(index) {
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
          if (this.configData[this.configNum][this.name].list.length == 1) {
            this.lastObj = this.configData[this.configNum][this.name].list[0];
          }
          this.configData[this.configNum][this.name].list.splice(index, 1);
        })
        .catch(() => {});
    },
    // Click vào ảnh bìa bài viết ảnh-văn
    modalPicTap(title, index, select) {
      this.activeIndex = index;
      this.modalPic = true;
      this.isSelect = select;
    },
    // Lấy thông tin ảnh
    getPic(pc) {
      this.$nextTick(() => {
        this.configData[this.configNum][this.name].list[this.activeIndex].imgList[this.isSelect] = pc.att_dir;
        this.modalPic = false;
      });
    },
  },
};
</script>
<style lang="scss" scoped>
.tabBars .box-item:last-child {
  margin-bottom: 20px;
}
.tabBars {
  .title {
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    font-size: 12px;
    color: #999;
  }
  .box-item {
    position: relative;
    display: flex;
    margin-top: 15px;
    padding: 20px 30px 20px 0;
    border: 1px solid #dddddd;
    border-radius: 3px;
    .del-box {
      position: absolute;
      right: -13px;
      top: -18px;
      cursor: pointer;
      .iconfont {
        color: #999;
        font-size: 30px;
      }
    }
    .left-tool {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 72px;
      .iconfont {
        color: #999;
        font-size: 36px;
        cursor: move;
      }
    }
    .right-wrapper {
      flex: 1;
      .img-wrapper {
        display: flex;
        .img-item {
          position: relative;
          width: 80px;
          height: 80px;
          margin-right: 20px;
          cursor: pointer;
          img {
            display: block;
            width: 100%;
            height: 100%;
          }
          .empty-img {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            height: 100%;
            background: #f7f7f7;
            font-size: 12px;
            color: #bfbfbf;
            .iconfont {
              font-size: 16px;
            }
          }
          .txt {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 22px;
            line-height: 22px;
            text-align: center;
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            font-size: 12px;
          }
        }
      }
      .c_row-item {
        margin-top: 10px;
      }
    }
  }
  .add-btn {
    margin-bottom: 20px;
    width: 100%;
    height: 40px;
  }
}
</style>
