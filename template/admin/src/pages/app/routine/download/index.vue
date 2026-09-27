<template>
  <div>
    <!-- <div class="i-layout-page-header header-title">
      <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
    </div> -->
    <pages-header ref="pageHeader" :title="$route.meta.title"></pages-header>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <div class="flex-wrapper">
        <!-- :src="iframeUrl" -->
        <div>
          <iframe class="iframe-box" :src="iframeUrl" frameborder="0" ref="iframe"></iframe>
          <div class="mask"></div>
        </div>

        <div class="right">
          <div class="content">
            <div class="content-box title">
              <div class="line"></div>
              <div class="right title">Cài đặt Mini Program</div>
            </div>
            <el-alert v-if="!pageData.appId && !pageData.code">
              <template slot="title">
                Bạn chưa cấu hình thông tin Mini Program, vui lòng<router-link :to="{ path: $routeProStr + '/setting/routine_config/2/7' }"
                  >cài đặt ngay</router-link
                ></template
              >
            </el-alert>
            <div class="content-box">
              <div class="left">Tên Mini Program:</div>
              <div class="right">{{ pageData.routine_name || 'Chưa đặt tên' }}</div>
            </div>
            <div class="content-box">
              <div class="left">Mã Mini Program:</div>
              <div class="right">
                <el-button type="primary" v-db-click @click="downLoadCode(pageData.code)">Tải xuống mã Mini Program</el-button>
              </div>
            </div>
            <div class="content-box">
              <div class="left">Gói Mini Program:</div>
              <div class="right">
                <span>Đã kích hoạt livestream Mini Program hay chưa</span>
                <el-radio-group class="rad" size="large" v-model="is_live">
                  <el-radio :label="0">Chưa kích hoạt</el-radio>
                  <el-radio :label="1">Đã kích hoạt</el-radio>
                </el-radio-group>
              </div>
            </div>
            <div class="content-box last">
              <div class="left"></div>
              <div class="right">
                <div>
                  Vui lòng chọn cẩn thận việc đã kích hoạt tính năng livestream Mini Program hay chưa, nếu không sẽ ảnh hưởng đến việc phát hành Mini Program. Có thể xem
                  <a :href="pageData.help" target="_blank">tài liệu hướng dẫn</a>
                  để biết cách kích hoạt tính năng livestream
                </div>

                <el-button class="mt10" type="primary" v-db-click @click="downLoad()">Tải xuống gói Mini Program</el-button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
import { routineDownload, routineInfo } from '@/api/app';
import { mapState } from 'vuex';
import { formatDate } from '@/utils/validate';
export default {
  name: 'routineTemplate',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      iframeUrl: `${location.origin}/pages/index/index?mdType=iframeWindow`,
      is_live: 1,
      pageData: {
        code: '',
        routine_name: '',
        help: '',
        appId: '1',
      },
    };
  },
  created() {
    routineInfo().then((res) => {
      this.pageData = res.data;
    });
  },
  watch: {
    $route(to, from) {},
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  methods: {
    downLoad() {
      routineDownload({
        is_live: this.is_live,
      })
        .then((res) => {
          window.open(res.data.url);
        })
        .catch((err) => {
          this.$message.warning(err.msg);
        });
    },
    downLoadCode(url) {
      if (!url) return this.$message.warning('Chưa có mã Mini Program');
      var image = new Image();
      image.src = url;
      // Giải quyết vấn đề Canvas bị nhiễm (tainted) do cross-origin
      image.setAttribute('crossOrigin', 'anonymous');
      image.onload = function () {
        var canvas = document.createElement('canvas');
        canvas.width = image.width;
        canvas.height = image.height;
        var context = canvas.getContext('2d');
        context.drawImage(image, 0, 0, image.width, image.height);
        var url = canvas.toDataURL(); //Lấy dữ liệu mã hóa base64 của ảnh
        var a = document.createElement('a'); // Tạo một phần tử a
        var event = new MouseEvent('click'); // Tạo một sự kiện click
        a.download = name || 'photo'; // Đặt tên ảnh
        a.href = url; // Đặt URL đã tạo vào thuộc tính a.href
        a.dispatchEvent(event); // Kích hoạt sự kiện click của a
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.template_sp_box {
  padding: 5px 0;
  box-sizing: border-box;
}
.template_sp {
  display: block;
  padding: 2px 0;
  box-sizing: border-box;
}
.flex-wrapper {
  display: flex;
  border-radius: 10px;
}
.iframe-box {
  width: 312px;
  height: 550px;
  border-radius: 10px;
}
.ivu-mt {
  height: 600px;
}
.content {
  padding: 0 20px;
}
.content > .title {
  padding-bottom: 26px;
}
.content-box {
  display: flex;
  align-items: center;
  margin: 20px 20px 0 20px;
  color: #333;
}
.content-box.last {
  margin-top: 0;
  color: #999999;
}
.line {
  width: 3px;
  height: 16px;
  background-color: var(--prev-color-primary);
  margin-right: 11px;
}
.content-box .title {
  font-size: 16px;
  font-weight: bold;
}
.content-box > span {
  color: #f5222d;
  font-size: 20px;
}
.content-box .left {
  width: 100px;
  text-align: right;
}
.content-box .right {
  width: 400px;
}
.rad {
  margin-left: 20px;
}
.mask {
  position: absolute;
  left: 0;
  top: 0;
  width: 312px;
  height: 550px;
  background-color: rgba(0, 0, 0, 0);
}
</style>
