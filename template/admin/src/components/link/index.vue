<template>
  <a
    :href="linkUrl"
    :target="target"
    class="i-link"
    :class="{ 'i-link-color': !linkColor }"
    v-db-click
    @click.exact="handleClickItem($event, false)"
    @click.ctrl="handleClickItem($event, true)"
    @click.meta="handleClickItem($event, true)"
    ><slot></slot
  ></a>
</template>
<script>
import mixinsLink from 'view-design/src/mixins/link';
export default {
  name: 'i-link',
  mixins: [mixinsLink],
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
    // Sau khi bật, màu liên kết là màu xanh mặc định, mặc định tắt thì kế thừa hiệu ứng (inherit)
    linkColor: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    handleClickItem(event, new_window = false) {
      if (this.disabled) return;

      this.handleCheckClick(event, new_window);
    },
  },
};
</script>
<style lang="scss">
.i-link {
  cursor: pointer;
  &-color {
    &,
    &:hover,
    &:active {
      color: inherit;
    }
  }
}
</style>
