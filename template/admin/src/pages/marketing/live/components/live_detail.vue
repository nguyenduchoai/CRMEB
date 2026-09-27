<template>
  <div class="divBox">
    <div>
      <div class="box-container">
        <div class="list sp">
          <label class="name">Tên phòng livestream:</label>
          <span class="info">{{ FormData.name }}</span>
        </div>
        <div class="list sp">
          <label class="name">ID WeChat của streamer:</label>
          <span class="info">{{ FormData.anchor_wechat }}</span>
        </div>
        <div class="list sp">
          <label class="name">ID phòng livestream:</label>
          <span class="info">{{ FormData.id }}</span>
          <span class="info">(ID phòng livestream WeChat: {{ FormData.room_id }})</span>
        </div>
        <div class="list sp">
          <label class="name">Biệt danh streamer:</label>
          <span class="info">{{ FormData.anchor_name }}</span>
        </div>
        <div class="list sp">
          <label class="name">Số điện thoại:</label>
          <span class="info">{{ FormData.phone }}</span>
        </div>
        <div class="list sp">
          <label class="name">Trạng thái livestream:</label>
          <span class="info">{{ FormData.live_status | liveReviewStatusFilter }}</span>
        </div>
        <div class="list sp">
          <label class="name">Thời gian bắt đầu livestream:</label>
          <span class="info">{{ FormData.start_time }}</span>
        </div>
        <div class="list sp">
          <label class="name">Thời gian kết thúc livestream:</label>
          <span class="info">{{ FormData.end_time }}</span>
        </div>
        <div class="list sp">
          <label class="name">Loại phòng livestream:</label>
          <span class="info">{{ FormData.type | broadcastType }}</span>
        </div>
        <div class="list sp">
          <label class="name">Kiểu hiển thị:</label>
          <span class="info">{{ FormData.screen_type | broadcastDisplayType }}</span>
        </div>
        <div class="list sp image">
          <label class="name">Ảnh nền:</label>
          <img style="max-width: 150px; height: 80px" :src="FormData.cover_img" />
        </div>
        <div class="list sp image">
          <label class="name">Ảnh chia sẻ:</label>
          <img style="max-width: 150px; height: 80px" :src="FormData.share_img" />
        </div>
        <div class="list sp">
          <label class="name">Bật lượt thích:</label>
          <span class="info blue">{{ FormData.close_like | filterClose }}</span>
        </div>
        <div class="list sp">
          <label class="name">Bật kệ hàng:</label>
          <span class="info blue">{{ FormData.close_goods | filterClose }}</span>
        </div>
        <div class="list sp">
          <label class="name">Bật bình luận:</label>
          <span class="info blue">{{ FormData.close_comment | filterClose }}</span>
        </div>

        <div class="list">
          <label class="name">Hiển thị phát lại livestream:</label>
          <span class="info blue">{{ FormData.replay_status | filterClose }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { liveDetail } from '@/api/live';
export default {
  name: 'live_detail',
  data() {
    return {
      option: {
        form: {
          labelWidth: '150px',
        },
      },
      FormData: {},
      loading: false,
    };
  },
  methods: {
    getData(id) {
      this.loading = true;
      liveDetail(id)
        .then((res) => {
          this.FormData = res.data;
          this.loading = false;
        })
        .catch((error) => {
          this.$message.error(error.msg);
          this.loading = false;
        });
    },
  },
};
</script>

<style scoped>
.box-container {
  overflow: hidden;
}
.box-container .list {
  float: left;
  line-height: 40px;
}
.box-container .sp {
  width: 50%;
}
.box-container .sp3 {
  width: 33.3333%;
}
.box-container .sp100 {
  width: 100%;
}
.box-container .list .name {
  display: inline-block;
  width: 150px;
  text-align: right;
  color: #606266;
}
.box-container .list .blue {
  color: var(--prev-color-primary);
}
.box-container .list.image {
  margin-bottom: 40px;
}
.box-container .list.image img {
  position: relative;
  top: 40px;
}
.el-textarea {
  width: 400px;
}
</style>
