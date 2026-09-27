<template>
  <div class="goodClass">
    <!-- <div class="title">Thiết lập trang</div> -->
    <div class="list acea-row row-top">
      <div
        class="item"
        :class="activeStyle == index ? 'on' : ''"
        v-for="(item, index) in classList"
        :key="index"
        v-db-click
        @click="selectTap(index)"
      >
        <div class="pictrue" :style="{ backgroundColor: themeColor }"><img :src="item.image" /></div>
        <div class="name">{{ item.name }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { themeInfo, themeSave } from '@/api/diy';
import setting from '@/setting';

export default {
  name: 'goodClass',
  props: {},
  data() {
    return {
      classList: [
        { image: require('@/assets/images/cate1.png'), name: 'Kiểu 1' },
        { image: require('@/assets/images/cate2.png'), name: 'Kiểu 2' },
        { image: require('@/assets/images/cate3.png'), name: 'Kiểu 3' },
      ],
      activeStyle: '-1',
      themeColor: '',
    };
  },
  created() {
    this.getInfo();
    this.getTheme();
  },
  methods: {
    getTheme() {
      themeInfo(this.$route.query.id, 'theme').then((res) => {
        this.themeColor = res.data ? res.data.theme_color : '#E93323';
      });
    },
    getInfo() {
      themeInfo(this.$route.query.id, 'category').then((res) => {
        this.activeStyle = res.data.status ? res.data.status - 1 : 0;
      });
    },
    selectTap(index) {
      this.activeStyle = index;
    },
    saveOnly(num) {
      this.$emit('parentFun', true);
      this.activeStyle = num == 1 ? 0 : this.activeStyle;
      themeSave(this.$route.query.id, {
        type: 'category',
        value: num == 1 ? 1 : this.activeStyle + 1,
      }).then((res) => {
        if (this.$route.query.id == 0) {
          this.$router.replace({ query: { ...this.$route.query, id: res.data.id } });
        }
        this.$message.success(res.msg);
      });
    },
    saveAndClose() {
      // Kích hoạt sự kiện của thành phần cha trước
      this.$emit('parentFun', true);

      // Lưu dữ liệu
      themeSave(this.$route.query.id, {
        type: 'category',
        value: this.activeStyle + 1,
      })
        .then((res) => {
          // Nếu là tạo mới (id bằng 0) thì cập nhật tham số route
          if (this.$route.query.id == 0) {
            this.$router.replace({ query: { ...this.$route.query, id: res.data.id } });
          }

          // Hiển thị thông báo thành công
          this.$message.success(res.msg);

          // Sau khi lưu thành công thì quay về trang danh sách chủ đề
          this.$router.push(`${setting.routePre}/setting/my_theme`);
        })
        .catch((err) => {
          // Xử lý khi lưu thất bại
          this.$message.error(err.msg || 'Lưu thất bại');
        });
    },
  },
};
</script>
<style lang="scss" scoped>
.goodClass {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fff;
  .title {
    font-size: 14px;
    color: rgba(0, 0, 0, 0.85);
    position: relative;
    padding-left: 11px;
    font-weight: bold;
    &:after {
      position: absolute;
      content: ' ';
      width: 2px;
      height: 14px;
      background-color: var(--prev-color-primary);
      left: 0;
      top: 3px;
    }
  }
  .list {
    .item {
      width: 264px;
      margin: 0px 30px 0 0;
      cursor: pointer;
      .pictrue {
        width: 100%;
        height: 496px;
        border: 1px solid #eeeeee;
        border-radius: 10px;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .name {
        font-size: 13px;
        color: rgba(0, 0, 0, 0.85);
        margin-top: 16px;
        text-align: center;
      }
      &.on {
        .pictrue {
          border: 2px solid var(--prev-color-primary);
        }
        .name {
          color: var(--prev-color-primary);
        }
      }
    }
  }
}
</style>
