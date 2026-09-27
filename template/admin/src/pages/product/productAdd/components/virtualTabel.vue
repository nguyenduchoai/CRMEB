<template>
  <div>
    <slot></slot>
  </div>
</template>

<script>
import throttle from 'lodash/throttle';

export default {
  name: 'el-table-virtual-scroll',
  props: {
    data: {
      type: Array,
      required: true,
    },
    height: {
      type: Number,
      default: 60,
    },
    buffer: {
      type: Number,
      default: 500,
    },
    keyProp: {
      type: String,
      default: 'id',
    },
    throttleTime: {
      type: Number,
      default: 100,
    },
  },
  data() {
    return {
      sizes: {}, // Ánh xạ kích thước (phụ thuộc responsive)
    };
  },
  computed: {
    // Tính khoảng cách từ mỗi item (theo giá trị key) đến đỉnh container cuộn
    offsetMap({ keyProp, height, sizes, data }) {
      const res = {};
      let total = 0;
      for (let i = 0; i < data.length; i++) {
        const key = data[i][keyProp];
        res[key] = total;

        const curSize = sizes[key];
        const size = typeof curSize === 'number' ? curSize : height;
        total += size;
      }
      return res;
    },
  },
  methods: {
    // Khởi tạo dữ liệu
    initData() {
      // Hiển thị dữ liệu trong phạm vi hiển thị
      this.renderData = [];
      // Đỉnh, đáy phạm vi hiển thị của trang
      this.top = undefined;
      this.bottom = undefined;
      // Cắt lấy chỉ số bắt đầu và kết thúc của dữ liệu hiển thị trong phạm vi hiển thị trang
      this.start = 0;
      this.end = undefined;

      this.scroller = this.$el.querySelector('.el-table__body-wrapper');

      // Thực thi lần đầu
      setTimeout(() => {
        this.handleScroll();
      }, 100);

      // Lắng nghe sự kiện
      this.onScroll = throttle(this.handleScroll, this.throttleTime);
      this.scroller.addEventListener('scroll', this.handleScroll);
      window.addEventListener('resize', this.onScroll);
    },

    // Cập nhật kích thước (chiều cao)
    updateSizes() {
      const rows = this.$el.querySelectorAll('.el-table__body > tbody > .el-table__row');

      Array.from(rows).forEach((row, index) => {
        const item = this.renderData[index];
        if (!item) return;

        const key = item[this.keyProp];
        const offsetHeight = row.offsetHeight;

        if (this.sizes[key] !== offsetHeight) {
          this.$set(this.sizes, key, offsetHeight);
        }
      });
    },

    // Xử lý sự kiện cuộn
    handleScroll(shouldUpdate = true) {
      // Cập nhật kích thước hiện tại (chiều cao)
      this.updateSizes();
      // Tính toán renderData
      this.calcRenderData();
      // Tính toán vị trí
      this.calcPosition();
      shouldUpdate && this.updatePosition();
      // Kích hoạt sự kiện
      this.$emit('change', this.renderData, this.start, this.end);
    },

    // Lấy offsetTop của một dòng dữ liệu
    getOffsetTop(index) {
      const item = this.data[index];
      if (item) {
        return this.offsetMap[item[this.keyProp]] || 0;
      }
      return 0;
    },

    // Lấy kích thước của một dòng dữ liệu
    getSize(index) {
      const item = this.data[index];
      if (item) {
        const key = item[this.keyProp];
        return this.sizes[key] || this.height;
      }
      return this.height;
    },

    // Tính dữ liệu chỉ render trên khung nhìn
    calcRenderData() {
      const { scroller, data, buffer } = this;
      // Tính đỉnh, đáy của phạm vi hiển thị
      const top = scroller.scrollTop - buffer;
      const bottom = scroller.scrollTop + scroller.offsetHeight + buffer;

      // Dùng phương pháp chia đôi để tính nội dung đầu tiên trong phạm vi hiển thị
      let l = 0;
      let r = data.length - 1;
      let mid = 0;
      while (l <= r) {
        mid = Math.floor((l + r) / 2);
        const midVal = this.getOffsetTop(mid);
        if (midVal < top) {
          const midNextVal = this.getOffsetTop(mid + 1);
          if (midNextVal > top) break;
          l = mid + 1;
        } else {
          r = mid - 1;
        }
      }

      // Tính chỉ số bắt đầu, kết thúc của nội dung render
      let start = mid;
      let end = data.length - 1;
      for (let i = start + 1; i < data.length; i++) {
        const offsetTop = this.getOffsetTop(i);
        if (offsetTop >= bottom) {
          end = i;
          break;
        }
      }

      // Chỉ số bắt đầu luôn giữ số chẵn, nếu là số lẻ thì cộng thêm 1 để thành số chẵn [đảm bảo số chẵn của các hàng bảng luôn nhất quán, tránh hiển thị sọc ngựa vằn bị lộn thứ tự]
      if (start % 2) {
        start = start - 1;
      }
      // console.log(start, end, 'start end')

      this.top = top;
      this.bottom = bottom;
      this.start = start;
      this.end = end;
      this.renderData = data.slice(start, end + 1);
    },

    // Tính toán vị trí
    calcPosition() {
      const last = this.data.length - 1;
      // Tính tổng chiều cao nội dung
      const wrapHeight = this.getOffsetTop(last) + this.getSize(last);
      // Tính chiều cao cần chống đỡ tại vị trí cuộn hiện tại
      const offsetTop = this.getOffsetTop(this.start);

      // Thiết lập vị trí dom
      const classNames = [
        '.el-table__body-wrapper',
        '.el-table__fixed-right .el-table__fixed-body-wrapper',
        '.el-table__fixed .el-table__fixed-body-wrapper',
      ];
      classNames.forEach((className) => {
        const el = this.$el.querySelector(className);
        if (!el) return;

        // Tạo wrapEl, innerEl
        if (!el.wrapEl) {
          const wrapEl = document.createElement('div');
          const innerEl = document.createElement('div');
          wrapEl.appendChild(innerEl);
          innerEl.appendChild(el.children[0]);
          el.insertBefore(wrapEl, el.firstChild);
          el.wrapEl = wrapEl;
          el.innerEl = innerEl;
        }

        if (el.wrapEl) {
          // Thiết lập chiều cao
          el.wrapEl.style.height = wrapHeight + 'px';
          // Thiết lập chiều cao bằng transform
          el.innerEl.style.transform = `translateY(${offsetTop}px)`;
          // Thiết lập chiều cao bằng paddingTop
          // el.innerEl.style.paddingTop = `${offsetTop}px`
        }
      });
    },

    // Cập nhật vị trí khi rảnh
    updatePosition() {
      this.timer && clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.timer && clearTimeout(this.timer);
        // Truyền vào false, tránh gọi lặp liên tục
        this.handleScroll(false);
      }, this.throttleTime + 10);
    },

    // [Gọi từ bên ngoài] Cập nhật
    update() {
      this.handleScroll();
    },

    // [Gọi từ bên ngoài] Cuộn đến hàng thứ mấy
    scrollTo(index, stop = false) {
      const item = this.data[index];
      if (item && this.scroller) {
        this.updateSizes();
        this.calcRenderData();

        this.$nextTick(() => {
          const offsetTop = this.getOffsetTop(index);
          this.scroller.scrollTop = offsetTop;

          // Gọi scrollTo hai lần, khi cuộn lần đầu nếu chiều cao render lần đầu của hàng bảng thay đổi sẽ gây lệch vị trí cuộn, lúc này cần thực hiện cuộn lần hai để đảm bảo vị trí cuộn chính xác
          if (!stop) {
            setTimeout(() => {
              this.scrollTo(index, true);
            }, 50);
          }
        });
      }
    },

    // [Gọi từ bên ngoài] Đặt lại
    reset() {
      this.sizes = {};
      this.scrollTo(0, false);
    },
  },
  watch: {
    data() {
      this.update();
    },
  },
  created() {
    this.$nextTick(() => {
      this.initData();
    });
  },
  beforeDestroy() {
    if (this.scroller) {
      this.scroller.removeEventListener('scroll', this.onScroll);
      window.removeEventListener('resize', this.onScroll);
    }
  },
};
</script>

<style lang="less" scoped></style>
