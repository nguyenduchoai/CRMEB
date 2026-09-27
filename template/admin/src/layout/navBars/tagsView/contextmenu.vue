<template>
  <div>
    <transition name="el-zoom-in-center">
      <ul
        class="el-dropdown-menu el-popper el-dropdown-menu--medium custom-contextmenu"
        :style="`top: ${dropdowns.y}px;left: ${dropdowns.x}px;`"
        x-placement="bottom-end"
        id="contextmenu"
        v-show="isShow"
      >
        <li
          class="el-dropdown-menu__item"
          v-for="(v, k) in dropdownList"
          :key="k"
          v-db-click
          @click="onCurrentContextmenuClick(v.id)"
        >
          <template v-if="!v.affix">
            <i :class="v.icon"></i>
            <span>{{ $t(v.txt) }}</span>
          </template>
        </li>
        <div x-arrow class="popper__arrow" :style="{ left: `${arrowLeft}px` }"></div>
      </ul>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'layoutTagsViewContextmenu',
  props: {
    dropdown: {
      type: Object,
    },
  },
  data() {
    return {
      isShow: false,
      dropdownList: [
        { id: 0, txt: 'message.tagsView.refresh', affix: false, icon: 'el-icon-refresh-right' },
        { id: 1, txt: 'message.tagsView.close', affix: false, icon: 'el-icon-close' },
        { id: 2, txt: 'message.tagsView.closeOther', affix: false, icon: 'el-icon-circle-close' },
        { id: 3, txt: 'message.tagsView.closeAll', affix: false, icon: 'el-icon-folder-delete' },
      ],
      path: {},
      arrowLeft: 5,
    };
  },
  computed: {
    dropdowns() {
      // 99 là chiều rộng của `Dropdown menu thả xuống`
      if (this.dropdown.x + 99 > document.documentElement.clientWidth) {
        return {
          x: document.documentElement.clientWidth - 99 - 5,
          y: this.dropdown.y,
        };
      } else {
        return this.dropdown;
      }
    },
  },
  mounted() {
    // Theo dõi trang để đóng menu chuột phải
    document.body.addEventListener('click', this.closeContextmenu);
  },
  methods: {
    // Bấm mục menu hiện tại
    onCurrentContextmenuClick(id) {
      this.$emit('currentContextmenuClick', { id, path: this.path });
    },
    // Mở menu chuột phải: kiểm tra có cố định (pin) hay không, nếu cố định thì không hiển thị nút đóng
    openContextmenu(item) {
      this.path = item.path;
      item.meta.isAffix ? (this.dropdownList[1].affix = true) : (this.dropdownList[1].affix = false);
      this.closeContextmenu();
      setTimeout(() => {
        this.isShow = true;
      }, 80);
    },
    // Đóng menu chuột phải
    closeContextmenu() {
      this.isShow = false;
    },
  },
  destroyed() {
    // Khi trang unload, gỡ bỏ sự kiện theo dõi menu chuột phải
    document.body.removeEventListener('click', this.closeContextmenu);
  },
  // Theo dõi vị trí menu thả xuống
  watch: {
    dropdown: {
      handler({ x }) {
        if (x + 99 > document.documentElement.clientWidth)
          this.arrowLeft = 99 - (document.documentElement.clientWidth - x);
        else this.arrowLeft = 10;
      },
      deep: true,
    },
  },
};
</script>

<style scoped lang="scss">
.custom-contextmenu {
  transform-origin: center top;
  z-index: 2190;
  position: fixed;
  .el-dropdown-menu__item {
    font-size: 12px !important;
    white-space: nowrap;
    i {
      font-size: 12px !important;
    }
  }
}
</style>
