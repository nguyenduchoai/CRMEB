<template>
  <div
    :style="{
      width: areaInit.areaWidth + 'px',
      height: areaInit.areaHeight + 'px',
      left: areaInit.starX + 'px',
      top: areaInit.starY + 'px',
    }"
    class="areaBox"
    @dblclick="editBoxShow = true"
    @mousedown.left.stop="mouseDownLint($event)"
    @mouseup.left.stop="mouseUp($event)"
  >
    <div class="prompt-text">
      <div class="prompt-item num">Vùng nóng {{ areaInit.number }}</div>
      <div class="prompt-item" :style="{ color: isSet ? '#2d8cf0' : '#f00' }">
        {{ isSet ? '(đã thiết lập)' : '(chưa thiết lập)' }}
      </div>
    </div>
    <!--Xóa-->
    <div class="del" @click.stop="del()">
      <i class="el-icon-close" size="16" />
    </div>
    <!--Điểm biến dạng-->
    <div class="shape" @mousedown.left.stop="shapeDown($event)" @mouseup.left.stop="mouseUp($event)" />
    <!--Khung sửa-->

    <div>
      <el-dialog :visible.sync="editBoxShow" title="Thiết lập vùng nóng" width="560px" append-to-body>
        <div class="area-set">
          <div class="area-label">Liên kết chuyển hướng của vùng nóng:</div>
          <div class="area-content">
            <el-input v-model="url" style="width: 100%" placeholder="Chọn liên kết chuyển hướng">
              <i class="el-icon-link" slot="suffix" @click="getLink()" />
            </el-input>
          </div>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click.stop="editBoxShow = false">Hủy</el-button>
          <el-button type="primary" @click.stop="addURL">Xác nhận</el-button>
        </span>
      </el-dialog>
      <linkaddress ref="linkaddres" @linkUrl="linkUrl"></linkaddress>
    </div>
  </div>
</template>

<script>
import linkaddress from '@/components/linkaddress';
export default {
  name: 'AreaBox',
  components: { linkaddress },
  props: {
    areaInit: {
      type: Object,
      default: () => {},
    },
    areaDataIndex: {
      type: Number,
      default: null,
    },
    link: {
      type: String,
      default: '',
    },
    title: {
      type: String,
      default: '',
    },
    type: {
      type: Number,
      default: -1,
    },
    parentWidth: {
      type: Number,
      default: 0,
    },
    parentHeight: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      areaTitle: '',
      url: '',
      editBoxShow: false,
      promptText: 'Nhấp đúp để thiết lập vùng nóng',
      // Điểm khởi đầu thao tác box
      move: {
        // Kéo (drag)
        startX: 0,
        starY: 0,
        // Biến dạng
        start1X: 0,
        start1Y: 0,
      },
    };
  },
  computed: {
    isSet() {
      return !!this.link;
    },
  },
  watch: {
    title(val) {
      this.areaTitle = val;
    },
    link(val) {
      this.url = val;
    },
  },
  mounted() {
    this.url = this.link;
  },
  methods: {
    // Xóa
    del() {
      this.$emit('delAreaBox', this.areaDataIndex);
    },
    // Thêm URL
    addURL() {
      if (!this.url) {
        this.$message.error('Vui lòng nhập liên kết');
      } else {
        this.$emit('addURL', this.areaDataIndex, this.url);
        this.editBoxShow = false;
      }
    },
    // Bắt đầu kéo có giới hạn phạm vi
    mouseDownLint(e) {
      e.preventDefault();
      this.starX = e.clientX;
      this.starY = e.clientY;
      const childrenDiv = e.target || e;
      //Lấy chiều rộng/cao của phần tử con
      let childrenWidth = childrenDiv.getBoundingClientRect().width;
      let childrenHight = childrenDiv.getBoundingClientRect().height;
      // console.log(childrenWidth, childrenHight)
      if (!document.onmousemove) {
        const initX = this.areaInit.starX;
        const initY = this.areaInit.starY;
        document.onmousemove = (ev) => {
          // Di chuyển vị trí
          let nLeft = initX + ev.clientX - this.starX;
          let nTop = initY + ev.clientY - this.starY;
          nLeft = nLeft <= 0 ? 0 : nLeft; //Kiểm tra bên trái có vượt giới hạn không
          nTop = nTop <= 0 ? 0 : nTop; //Kiểm tra bên trên có vượt giới hạn không
          let nRight = nLeft + childrenWidth;
          let nBottom = nTop + childrenHight;
          // Kiểm tra bên phải có vượt giới hạn không
          if (nRight >= this.parentWidth) {
            nLeft = this.parentWidth - childrenWidth;
          }
          // Kiểm tra bên dưới có vượt giới hạn không
          if (nBottom >= this.parentHeight) {
            nTop = this.parentHeight - childrenHight;
          }
          this.areaInit.starX = nLeft;
          this.areaInit.starY = nTop;
        };
      }
    },
    // Bắt đầu kéo không giới hạn phạm vi
    mouseDown(e) {
      e.preventDefault();
      this.starX = e.clientX;
      this.starY = e.clientY;
      if (!document.onmousemove) {
        const initX = this.areaInit.starX;
        const initY = this.areaInit.starY;
        document.onmousemove = (ev) => {
          this.areaInit.starX = initX + ev.clientX - this.starX;
          this.areaInit.starY = initY + ev.clientY - this.starY;
        };
      }
    },
    // Kết thúc kéo/biến dạng
    mouseUp() {
      document.onmousemove = null;
    },
    // Bắt đầu biến dạng
    shapeDown(e) {
      e.preventDefault();

      this.star1X = e.clientX;
      this.star1Y = e.clientY;
      // Lấy độ lệch bên trái và bên dưới

      if (!document.onmousemove) {
        const initX = this.areaInit.areaWidth;
        const initY = this.areaInit.areaHeight;
        document.onmousemove = (ev) => {
          this.areaInit.areaWidth = initX + ev.clientX - this.star1X;
          this.areaInit.areaHeight = initY + ev.clientY - this.star1Y;
        };
      }
    },
    getLink() {
      this.$refs.linkaddres.modals = true;
    },
    linkUrl(e) {
      this.url = e;
    },
  },
};
</script>

<style lang="scss" scoped>
.areaBox {
  position: absolute;
  background: rgba(24, 144, 255, 0.5);
  border: 1px dashed var(--prev-color-primary);
  display: flex;
  justify-content: center;
  align-items: center;
  color: var(--prev-color-primary);
  font-size: 12px;
  cursor: move;
  .prompt-text {
    overflow: hidden;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    max-width: 100%;
    max-height: 100%;
    text-align: center;
    align-items: center;
    color: #fff;
    .num {
      font-size: 12px;
    }
    .prompt-item {
      color: #fff;
      margin: 0 2px;
    }
  }
  .del {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 16px;
    height: 16px;
    line-height: 16px;
    font-size: 12px;
    background: var(--prev-color-primary);
    color: #fff;
    text-align: center;
    border-radius: 0 0 0 3px;
    position: absolute;
    right: 7px;
    top: 7px;
    transform: translate3d(50%, -50%, 0);
    cursor: default;
  }
  .del:hover {
    width: 16px;
    height: 16px;
    line-height: 16px;
  }
  .shape {
    position: absolute;
    width: 7px;
    height: 7px;
    background: transparent;
    right: 0;
    bottom: 0;
    transform: translate3d(50%, 50%, 0);
    cursor: nwse-resize;
  }
}
.area-set {
  display: flex;
  align-items: center;
  margin: 16px 0;
}
.area-label {
  width: 100px;
}
.area-content {
  flex: 1;
}
</style>
