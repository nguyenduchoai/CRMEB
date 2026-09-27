<template>
  <div class="mobile-page" :style="{ marginTop: mTOP + 'px', padding: '0 ' + prConfig + 'px' }">
    <div class="list-wrapper" :class="{ pageOn: bgStyle === 1 }" :style="{ background: bgColor }">
      <div
        class="item"
        :class="{ on: listStyle == 0, pageOn: conStyle === 1 }"
        v-for="(item, index) in list"
        :key="index"
        :style="{ marginBottom: itemEdge + 'px' }"
      >
        <div class="empty-box on" v-if="list[0].type === 'noList'"><span class="iconfont-diy icontupian"></span></div>
        <div class="pictrue" v-else>
          <img :src="item.image_input[0]" />
        </div>
        <div class="info">
          <div class="title line2">{{ item.title }}</div>
          <div class="time">{{ item.add_time | formatDate }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { categoryList } from '@/api/diy';
import { mapState } from 'vuex';
import { formatDate } from '@/utils/validate';
export default {
  name: 'home_new_list',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  cname: 'Danh sách tin tức',
  icon: 'iconwenzhangliebiao1',
  configName: 'c_new_list',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'articleList', // Tên khớp bên ngoài
  props: {
    index: {
      type: null,
    },
    num: {
      type: null,
    },
  },
  computed: {
    ...mapState('mobildConfig', ['defaultArray']),
  },
  watch: {
    pageData: {
      handler(nVal, oVal) {
        this.setConfig(nVal);
      },
      deep: true,
    },
    num: {
      handler(nVal, oVal) {
        let data = this.$store.state.mobildConfig.defaultArray[nVal];
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler(nVal, oVal) {
        let data = this.$store.state.mobildConfig.defaultArray[this.num];
        this.setConfig(data);
      },
      deep: true,
    },
  },
  data() {
    return {
      list: [],
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        name: 'articleList',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        numConfig: {
          val: 3,
          title: 'Số lượng bài viết',
        },
        selectConfig: {
          title: 'Danh mục bài viết',
          activeValue: '',
          list: [
            {
              activeValue: '',
              title: '',
            },
            {
              activeValue: '',
              title: '',
            },
          ],
        },
        selectList: {
          title: 'Danh sách bài viết',
          list: [],
        },
        listStyle: {
          cname: 'listStyle',
          title: 'Vị trí văn bản',
          type: 0,
          list: [
            {
              val: 'Căn trái',
              icon: 'icondoc_left',
            },
            {
              val: 'Căn phải',
              icon: 'icondoc_right',
            },
          ],
        },
        bgColor: {
          title: 'Màu nền',
          default: [
            {
              item: '#fff',
            },
          ],
          color: [
            {
              item: '#fff',
            },
          ],
        },
        bgStyle: {
          title: 'Kiểu nền',
          name: 'bgStyle',
          type: 0,
          list: [
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
          ],
        },
        conStyle: {
          title: 'Kiểu nội dung',
          name: 'conStyle',
          type: 0,
          list: [
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
          ],
        },
        prConfig: {
          title: 'Lề nền',
          val: 0,
          min: 0,
        },
        itemConfig: {
          title: 'Khoảng cách bài viết',
          val: 0,
          min: 0,
        },
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
      },
      mTOP: 0,
      bgColor: [],
      itemEdge: 0,
      listStyle: 0,
      itemStyle: 0,
      bgStyle: 0,
      conStyle: 0,
      prConfig: 0,
    };
  },
  created() {},
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
      //this.categoryList()
    });
  },
  methods: {
    categoryList() {
      categoryList().then((res) => {
        this.pageData.selectConfig.list = res.data;
        this.pageData.selectConfig.list.map((item) => {
          item.id.toString();
          // return item;
        });
        this.$store.commit('mobildConfig/UPDATEARR', { num: this.num, val: this.pageData });
      });
    },
    setConfig(data) {
      if (!data) return;
      if (data.mbConfig) {
        this.bgColor = data.bgColor.color[0].item;
        this.mTOP = data.mbConfig.val;
        this.itemEdge = data.itemConfig.val;
        this.listStyle = data.listStyle.type;
        this.bgStyle = data.bgStyle.type;
        this.prConfig = data.prConfig.val;
        this.conStyle = data.conStyle.type;
        let selectList = data.selectList.list || [];
        if (selectList.length) {
          this.list = selectList;
        } else {
          this.list = [
            {
              title: 'Tiêu đề bài viết tiêu đề bài viết tiêu đề bài viết tiêu đề bài viết',
              add_time: '1621474811',
              type: 'noList',
            },
          ];
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.pageOn {
  border-radius: 10px !important;
}
.list-wrapper {
  padding: 10px 0;
  .item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px;
    background-color: #fff;
    margin: 0 10px;
    &:nth-last-child(1) {
      margin-bottom: 0 !important;
    }
    &.on {
      flex-flow: row-reverse;
      .info {
        .time {
          text-align: left;
        }
      }
    }
    .img-box {
      width: 125px;
      height: 78px;
      background: #e8e8e8;
    }
    .info {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      width: 209px;
      height: 78px;
      .title {
        color: #282828;
        font-size: 15px;
      }
      .time {
        color: #999999;
        font-size: 12px;
        text-align: right;
      }
    }
    .empty-box {
      width: 125px;
      height: 78px;
    }
    .pictrue {
      width: 125px;
      height: 78px;
      img {
        width: 100%;
        height: 100%;
      }
    }
  }
}
</style>
