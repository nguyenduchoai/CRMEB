<template>
  <error-content code="403" desc="Xin lỗi, bạn không có quyền truy cập trang này" :src="src" />
</template>

<script>
import error403 from '@/assets/images/error-page/error-401.svg';
import errorContent from '../error-content.vue';
export default {
  name: 'error_403',
  components: {
    errorContent,
  },
  data() {
    return {
      src: error403,
    };
  },
};
</script>
