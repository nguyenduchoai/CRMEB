<template>
  <div class="hot_imgs">
    <div class="title">Có thể thêm tối đa 4 khối, kích thước ảnh đề xuất 140 * 140px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự khối</div>
    <div class="list-box">
      <draggable class="dragArea list-group" :list="defaults.menu" group="people" handle=".move-icon">
        <div class="item" v-for="(item, index) in defaults.menu" :key="index">
          <div class="move-icon">
            <Icon type="ios-keypad-outline" size="22" />
          </div>
          <div class="img-box" @click="modalPicTap('Chọn một', index)">
            <img :src="item.img" alt="" v-if="item.img" />
            <div class="upload-box" v-else><Icon type="ios-camera-outline" size="36" /></div>
            <div>
              <el-dialog
                :visible.sync="modalPic"
                width="960px"
                title="Tải lên ảnh"
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
          <div class="info">
            <div class="info-item" v-for="(infos, key) in item.info" :key="key">
              <span>{{ infos.title }}</span>
              <div class="input-box">
                <el-input v-model="infos.value" :placeholder="infos.tips" :maxlength="infos.max" />
              </div>
            </div>
          </div>
        </div>
      </draggable>
    </div>
    <div class="add-btn" v-if="defaults.menu.length < 4">
      <el-button style="width: 100%; height: 40px" @click="addBox">Thêm khối</el-button>
    </div>
  </div>
</template>

<script>
import vuedraggable from 'vuedraggable';
import { mapState, mapActions } from 'vuex';
import uploadPictures from '@/components/uploadPictures';
import { wechatNewsAddApi, wechatNewsInfotApi } from '@/api/app';
export default {
  name: 'c_hot_imgs',
  props: {
    configObj: {
      type: Object,
    },
  },
  components: {
    draggable: vuedraggable,
    uploadPictures,
  },
  data() {
    return {
      defaults: {},
      menus: [],
      list: [
        {
          title: 'aa',
          val: '',
        },
      ],
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
    };
  },
  created() {
    this.defaults = this.configObj;
  },
  watch: {
    configObj: {
      handler(nVal, oVal) {
        this.defaults = nVal;
      },
      immediate: true,
      deep: true,
    },
  },
  methods: {
    addBox() {
      let obj = {
        img: 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1594458238721&di=d9978a807dcbf5d8a01400875bc51162&imgtype=0&src=http%3A%2F%2Fattachments.gfan.com%2Fforum%2F201604%2F23%2F002205xqdkj84gnw4oi85v.jpg',
        info: [
          {
            title: 'Tiêu đề',
            value: '',
            tips: 'Không bắt buộc, tối đa 4 ký tự',
            max: 4,
          },
          {
            title: 'Mô tả ngắn',
            value: '',
            tips: 'Không bắt buộc, tối đa 20 ký tự',
            max: 20,
          },
        ],
        link: {
          title: 'Liên kết',
          optiops: [
            {
              type: 0,
              value: '',
              label: 'Danh mục cấp 1 > cấp 2',
            },
            {
              type: 1,
              value: '',
              label: 'Liên kết tùy chỉnh',
            },
          ],
        },
      };
      this.defaults.menu.push(obj);
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
            iframeUrl: '/admin/widget.images/index.html?fodder=dialog',
            editor: editor,
            name: uiName,
            title: 'Tải lên ảnh',
            cssRules: 'width:1200px;height:500px;padding:20px;',
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
      this.defaults.menu[this.activeIndex].img = pc.att_dir;
      this.modalPic = false;
    },
  },
};
</script>

<style scoped lang="scss">
::v-deep .ivu-input {
  font-size: 13px !important;
}

.hot_imgs {
  border-top: 1px solid rgba(0, 0, 0, 0.05);

  .title {
    padding: 13px 0;
    color: #999;
    font-size: 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  .list-box {
    .item {
      display: flex;
      margin-top: 20px;

      .move-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 80px;
        cursor: move;
      }

      .img-box {
        width: 80px;
        height: 80px;

        img {
          width: 100%;
          height: 100%;
        }
      }

      .info {
        flex: 1;
        margin-left: 22px;

        .info-item {
          display: flex;
          align-items: center;
          margin-bottom: 10px;

          span {
            width: 40px;
            font-size: 13px;
          }

          .input-box {
            flex: 1;
          }
        }
      }
    }
  }

  .add-btn {
    margin-top: 10px;
  }

  .upload-box {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: #ccc;
    border-radius: 5px;
  }
}
</style>
