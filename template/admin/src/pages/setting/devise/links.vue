<template>
  <div class="right-box">
    <div class="link-item" v-for="(item, index) in list" :key="index">
      <div class="title">{{ item.name }}</div>
      <div class="txt"><span>Địa chỉ:</span>{{ item.url }}</div>
      <div class="txt" v-if="item.parameter">
        <p><span>Tham số:</span></p>
        <span v-for="(val, key, index) in item.parameter" :key="index"
          >{{ key + '=' + val }}<i style="font-style: normal">&</i></span
        >
      </div>
      <div class="tips">
        Ví dụ: {{ item.example }}
        <!--<el-button size="small" style="margin-left: 10px" v-clipboard:copy="item.example"-->
        <!--v-clipboard:success="onCopy"-->
        <!--v-clipboard:error="onError">Sao chép</el-button>-->
        <span class="copy copy-data" v-db-click @click="onCopy(item.example)">Sao chép</span>
      </div>
    </div>
  </div>
</template>

<script>
import ClipboardJS from 'clipboard';
import { getUrl } from '@/api/diy';
export default {
  name: 'links',
  data() {
    return {
      list: [
        {
          name: 'Trang chủ cửa hàng',
          url: '/pages/goods/order_list/index',
          parameter: [{}],
          example: '/pages/activity/bargain/index',
        },
        {
          name: 'Trang chủ cửa hàng',
          url: '/pages/goods/order_list/index',
          parameter: [{}],
          example: '/pages/activity/bargain/index',
        },
      ],
    };
  },
  created() {
    getUrl().then((res) => {
      this.list = res.data.url;
    });
  },
  mounted: function () {
    this.$nextTick(function () {
      const clipboard = new ClipboardJS('.copy-data');
      clipboard.on('success', () => {
        this.$message.success('Sao chép thành công');
      });
    });
  },
  methods: {
    onCopy(copyData) {
      this.$copyText(copyData)
        .then((message) => {
          this.$message.success('Sao chép thành công');
        })
        .catch((err) => {
          this.$message.error('Sao chép thất bại');
        });
    },
    // onError () {
    //     this.$message.error('Sao chép thành công');
    // }
  },
};
</script>

<style lang="scss" scoped>
.right-box {
  width: 400px;
  margin-left: 50px;
  border: 1px solid #ddd;
  border-radius: 4px;
  height: 700px;
  overflow-y: scroll;
  padding: 0 10px;
  &::-webkit-scrollbar {
    /*Kiểu tổng thể của thanh cuộn*/
    width: 4px; /*Chiều cao/rộng tương ứng với kích thước thanh cuộn ngang/dọc*/
    height: 1px;
  }
  &::-webkit-scrollbar-thumb {
    /*Ô vuông nhỏ bên trong thanh cuộn*/
    border-radius: 4px;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
    background: #535353;
  }
  &::-webkit-scrollbar-track {
    /*Đường trượt bên trong thanh cuộn*/
    box-shadow: inset 0 0 5px #fff;
    border-radius: 4px;
    background: #fff;
  }
  .link-item {
    padding: 10px 0;
    border-bottom: 1px solid #f5f5f5;
    .title {
      font-size: 14px;
      color: #2d8cf0;
    }
    .txt {
      margin: 5px 0;
      font-size: 12px;
      span {
        color: #333;
      }
      p {
        display: inline-block;
        color: #19be6b;
        margin-right: 10px;
        span {
          color: #333;
        }
        &.red {
          color: #f00;
        }
      }
    }
    .tips {
      font-size: 12px;
      color: #999;
      .copy {
        padding: 3px 5px;
        border: 1px solid #cccccc;
        border-radius: 5px;
        color: #333;
        cursor: pointer;
        margin-left: 5px;
        &:hover {
          border-color: #2d8cf0;
          color: #2d8cf0;
        }
      }
    }
  }
}
</style>
