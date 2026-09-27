<template>
  <div v-show="visible" class="contextmenu" :style="{ top: top + 'px', left: left + 'px' }" @mousedown.stop>
    <ul>
      <template v-if="curComponent">
        <template v-if="!curComponent.isLock">
          <li @click="handleAction('copy')">Sao chép</li>
          <li @click="handleAction('delete')">Xóa</li>
          <li @click="handleAction('lock')">Khóa</li>
          <li class="divider"></li>
          <li @click="handleAction('top')">Lên trên cùng</li>
          <li @click="handleAction('bottom')">Xuống dưới cùng</li>
          <li @click="handleAction('up')">Lên một lớp</li>
          <li @click="handleAction('down')">Xuống một lớp</li>
        </template>
        <li v-else @click="handleAction('unlock')">Mở khóa</li>
      </template>
      <template v-else>
        <li class="disabled">Không có thao tác</li>
      </template>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'ContextMenu',
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
    top: {
      type: Number,
      default: 0,
    },
    left: {
      type: Number,
      default: 0,
    },
    curComponent: {
      type: Object,
      default: null,
    },
  },
  methods: {
    handleAction(action) {
      this.$emit('action', action);
      this.$emit('close');
    },
  },
};
</script>

<style scoped lang="scss">
.contextmenu {
  position: absolute;
  z-index: 1000;

  ul {
    border: 1px solid #e4e7ed;
    border-radius: 4px;
    background-color: #fff;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    margin: 5px 0;
    padding: 6px 0;
    list-style: none;

    li {
      font-size: 14px;
      padding: 0 20px;
      position: relative;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: #606266;
      height: 34px;
      line-height: 34px;
      box-sizing: border-box;
      cursor: pointer;

      &:hover {
        background-color: #f5f7fa;
        color: #409eff;
      }

      &.disabled {
        color: #c0c4cc;
        cursor: not-allowed;
        &:hover {
          background-color: #fff;
        }
      }

      &.divider {
        height: 1px;
        background-color: #ebeef5;
        margin: 5px 0;
        padding: 0;
      }
    }
  }
}
</style>
