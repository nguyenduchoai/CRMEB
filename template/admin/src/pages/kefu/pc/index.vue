<template>
  <div class="kefu-layouts">
    <div class="content-wrapper">
      <baseHeader :kefuInfo="kefuInfo" :online="online" @setOnline="setOnline" @search="bindSearch"></baseHeader>
      <div class="container">
        <chatList
          @setDataId="setDataId"
          @changeType="changeType"
          :userOnline="userOnline"
          :newRecored="newRecored"
          :searchData="searchData"
        ></chatList>
        <div class="chat-content">
          <div class="chat-body">
            <happy-scroll size="5" resize hide-horizontal :scroll-top="scrollTop" @vertical-start="scrollHandler">
              <div style="width: 600px; padding: 20px" id="chat_scroll" ref="scrollBox" v-loading="isLoad">
                <div
                  class="chat-item"
                  v-for="(item, index) in records"
                  :key="index"
                  :class="[{ 'right-box': item.uid == kefuInfo.uid }, { gary: item.msn_type == 5 }]"
                  :id="`chat_${item.id}`"
                >
                  <div class="time" v-show="item.show">{{ item.time }}</div>
                  <div class="flex-box">
                    <div class="avatar">
                      <img v-lazy="item.avatar" alt="" />
                    </div>
                    <div class="msg-wrapper">
                      <!-- Tài liệu -->
                      <template v-if="item.msn_type <= 2">
                        <div class="txt-wrapper pad16" v-html="item.msn"></div>
                      </template>
                      <!-- Hình ảnh -->
                      <template v-if="item.msn_type == 3">
                        <div class="img-wraper" v-viewer>
                          <img v-lazy="item.msn" alt="" />
                        </div>
                      </template>
                      <!-- Sản phẩm -->
                      <template v-if="item.msn_type == 5">
                        <div class="order-wrapper pad16">
                          <div class="img-box">
                            <img :src="item.productInfo.image" alt="" />
                          </div>
                          <div class="order-info">
                            <div class="name line1">
                              {{ item.productInfo.store_name }}
                            </div>
                            <div class="sku">Tồn kho: {{ item.productInfo.stock }} Lượt bán: {{ item.productInfo.sales }}</div>
                            <div class="price-box">
                              <div class="num">¥ {{ item.productInfo.price }}</div>
                              <a herf="javascript:;" class="more" v-db-click @click.stop="lookGoods(item)"
                                >Xem sản phẩm ></a
                              >
                            </div>
                          </div>
                        </div>
                      </template>
                      <!-- Đơn hàng -->
                      <template
                        v-if="item.msn_type == 6 && item.orderInfo && (item.orderInfo.length > 0 || item.orderInfo.id)"
                      >
                        <div class="order-wrapper pad16">
                          <div class="img-box">
                            <img :src="item.orderInfo.cartInfo[0].productInfo.image" alt="" />
                          </div>
                          <div class="order-info">
                            <div class="name line1">
                              {{ item.orderInfo.order_id }}
                            </div>
                            <div class="sku">Số lượng sản phẩm: {{ item.orderInfo.total_num }}</div>
                            <div class="price-box">
                              <div class="num">¥ {{ item.orderInfo.pay_price }}</div>
                              <a href="javascript:;" class="more" v-db-click @click.stop="lookOrder(item)"
                                >Xem đơn hàng ></a
                              >
                            </div>
                          </div>
                        </div>
                      </template>
                      <template v-if="item.msn_type == 6 && !item.orderInfo">
                        <div class="txt-wrapper pad16" v-html="item.msn"></div>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </happy-scroll>
          </div>
          <div class="chat-textarea">
            <div class="chat-btn-wrapper">
              <div class="left-wrappers">
                <div class="icon-item" v-db-click @click.stop="isEmoji = !isEmoji">
                  <span class="iconfont iconbiaoqing1"></span>
                </div>
                <div class="icon-item">
                  <el-upload
                    :show-file-list="false"
                    :headers="header"
                    :data="uploadData"
                    :on-success="handleSuccess"
                    accept="image/*"
                    :on-format-error="handleFormatError"
                    :action="upload"
                    :before-upload="beforeUpload"
                  >
                    <span class="iconfont icontupian1"></span>
                  </el-upload>
                </div>
                <div class="icon-item" v-db-click @click.stop.stop="isMsg = true">
                  <span class="iconfont iconliaotian"></span>
                </div>
              </div>
              <div class="right-wrapper">
                <div class="icon-item" v-db-click @click.stop="isTransfer = !isTransfer">
                  <span class="iconfont iconzhuanjie"></span>
                  <span>Chuyển tiếp</span>
                </div>
                <div class="transfer-box" v-if="isTransfer">
                  <transfer @close="msgClose" @transferPeople="transferPeople" :userUid="userActive.to_uid"></transfer>
                </div>
                <div class="transfer-bg" v-if="isTransfer" v-db-click @click.stop="isTransfer = false"></div>
              </div>
              <!-- Biểu tượng cảm xúc -->
              <div class="emoji-box" v-show="isEmoji">
                <div class="emoji-item" v-for="(emoji, index) in emojiList" :key="index">
                  <i class="em" :class="emoji" v-db-click @click.stop="select(emoji)"></i>
                </div>
              </div>
            </div>
            <div class="textarea-box" style="position: relative">
              <el-input
                ref="chatInput"
                v-paste="handleParse"
                v-model="chatCon"
                type="textarea"
                :rows="7"
                @keydown.enter.native="listen($event)"
                placeholder="Vui lòng nhập nội dung văn bản"
                style="font-size: 14px; height: 150px"
              />
              <div class="send-btn">
                <el-button class="btns" type="primary" :disabled="disabled" v-db-click @click.stop="sendText"
                  >Gửi</el-button
                >
              </div>
            </div>
          </div>
        </div>
        <div>
          <rightMenu
            :isTourist="tourist"
            :uid="userActive.to_uid"
            :webType="userActive.type"
            @bindPush="bindPush"
          ></rightMenu>
        </div>
      </div>
      <!-- Nhãn người dùng -->
      <el-dialog :visible.sync="isMsg" title="Câu trả lời mẫu CSKH" class="none-radius isMsgbox" width="720px">
        <msgWindow v-if="isMsg" @close="msgClose" @activeTxt="activeTxt"></msgWindow>
      </el-dialog>
      <!-- Popup sản phẩm -->
      <div v-if="isProductBox">
        <div class="bg" v-db-click @click.stop="isProductBox = false"></div>
        <goodsDetail :goodsId="goodsId"></goodsDetail>
      </div>
      <!-- Chi tiết đơn hàng -->
      <div v-if="isOrder">
        <el-dialog :visible.sync="isOrder" title="Thông tin đơn hàng" width="720px" class="none-radius">
          <orderDetail :orderId="orderId"></orderDetail>
        </el-dialog>
      </div>
    </div>
  </div>
</template>

<script>
var mp3 = require('../../../assets/video/notice.wav');
var mp3 = new Audio(mp3);
import Setting from '@/setting';
import { HappyScroll } from 'vue-happy-scroll';
import baseHeader from './components/baseHeader';
import chatList from './components/chatList';
import rightMenu from './components/rightMenu';
import emojiList from '@/utils/emoji';
import { Socket } from '@/libs/socket';
import util from '@/libs/util';
import msgWindow from './components/msgWindow';
import transfer from './components/transfer';
import { serviceList, uploadImg } from '@/api/kefu';
import goodsDetail from './components/goods_detail';
import orderDetail from './components/order_detail';
import { mapState } from 'vuex';
import { getCookies, removeCookies, setCookies } from '@/libs/util';
import { serviceInfo } from '@/api/kefu_mobile';
import { isPicUpload } from '@/utils';

const chunk = function (arr, num) {
  num = num * 1 || 1;
  var ret = [];
  arr.forEach(function (item, i) {
    if (i % num === 0) {
      ret.push([]);
    }
    ret[ret.length - 1].push(item);
  });
  return ret;
};
export default {
  name: 'index',
  components: {
    baseHeader,
    chatList,
    rightMenu,
    msgWindow,
    transfer,
    HappyScroll,
    goodsDetail,
    orderDetail,
  },
  data() {
    return {
      isEmoji: false,
      chatCon: '',
      emojiGroup: chunk(emojiList, 20), // Danh sách biểu tượng cảm xúc
      emojiList: emojiList,
      html: '',
      userActive: {}, //Thông tin đã chọn trong danh sách người dùng bên trái
      kefuInfo: {}, //Thông tin chăm sóc khách hàng
      isMsg: false,
      isTransfer: false,
      activeMsg: '', // Mẫu câu đã chọn
      chatList: [],
      text: '',
      limit: 20,
      upperId: 0,
      online: true, //Trạng thái online hiện tại của CSKH
      scrollTop: 0,
      isScroll: true,
      oldHeight: 0,
      isLoad: false,
      isProductBox: false,
      goodsId: '',
      isOrder: false,
      orderId: '',
      upload: '',
      header: {},
      uploadData: {
        filename: 'file',
      },
      userOnline: {},
      newRecored: {}, //Thông tin cuộc trò chuyện mới
      searchData: '', // Chữ tìm kiếm
      scrollNum: 0, //Số lần cuộn
      transferId: '', //ID chuyển tiếp
      bodyClose: false,
      tourist: 0,
    };
  },
  computed: {
    ...mapState({
      socketStatus: (state) => state.admin.kefu.socketStatus,
    }),
    disabled() {
      if (this.chatCon.length == 0) {
        return true;
      } else {
        return false;
      }
    },
    records() {
      return this.chatList.map((item, index) => {
        item.time = this.$moment(item.add_time * 1000).format('MMMDo H:mm');
        if (index) {
          if (item.add_time - this.chatList[index - 1].add_time >= 300) {
            item.show = true;
          } else {
            item.show = false;
          }
        } else {
          item.show = true;
        }
        return item;
      });
    },
  },
  // Định nghĩa directive paste
  directives: {
    paste: {
      bind(el, binding, vnode) {
        el.addEventListener('paste', function (event) {
          //Ở đây theo dõi trực tiếp sự kiện paste của phần tử
          binding.value(event);
        });
      },
    },
  },
  watch: {
    // socketStatus:{
    //     handler(nVal,Val){
    //         if(nVal){
    //             Socket.send({
    //                 data: util.cookies.kefuGet('token'),
    //                 type: "kefu_login"
    //             });
    //         }
    //     },
    //     deep:true
    // }
  },
  beforeDestroy() {
    if (this.ws) {
      this.ws.$off('socket_open', this.onSocketOpen);
      this.ws.$off('close', this.onSocketClose);
    }
  },
  created() {
    this.upload = Setting.apiBaseURL.replace('adminapi', 'kefuapi') + '/upload';
    serviceInfo().then((res) => {
      this.kefuInfo = res.data;
      if (this.kefuInfo.site_name) {
        document.title = this.kefuInfo.site_name;
      } else {
        this.kefuInfo.site_name = '';
      }
    });
  },
  mounted() {
    let self = this;
    window.addEventListener('click', function () {
      self.isEmoji = false;
    });
    setTimeout((e) => {
      Socket.then((ws) => {
        if (this._isDestroyed) return;
        this.ws = ws;
        ws.send({
          type: 'kefu_login',
          data: getCookies('kefu_token'),
        });
        ws.$on('socket_open', this.onSocketOpen);
        ws.$on('close', this.onSocketClose);
        ws.$on(['reply', 'chat'], (data) => {
          if (data.msn_type == 1) {
            data.msn = this.replace_em(data.msn);
          }
          if (data.msn_type == 2) {
            if (data.msn.indexOf('[') == -1) {
              data.msn = this.replace_em(`[${data.msn}]`);
            }
          }
          if (data.to_uid == this.userActive.to_uid || data.uid == this.userActive.to_uid) {
            this.chatList.push(data);
            this.$nextTick(function () {
              setTimeout(() => {
                var container = document.querySelector('#chat_scroll');
                if (container) {
                  this.scrollTop = container.offsetHeight;
                }
              }, 800);
            });
          }
        });
        ws.$on('reply', (data) => {
          // mp3.play();
        });
        ws.$on('socket_error', () => {
          this.$message.error('Kết nối thất bại');
        });
        ws.$on('err_tip', (data) => {
          this.$message.error(data.msg);
        });
        //Broadcast nhắc nhở người dùng online
        ws.$on('user_online', (data) => {
          this.userOnline = data;
        });
        //Thay đổi số tin nhắn chưa đọc của người dùng
        ws.$on('mssage_num', (data) => {
          if (data.num > 0) {
            mp3.play();
          }
          this.chatList.forEach((item) => {
            if (item.to_uid == data.uid) {
              item.mssage_num = data.num;
            }
          });
          if (data.recored.id) {
            this.newRecored = data.recored;
          }
        });
      });
    }, 2000);
    this.header['Authori-zation'] = 'Bearer ' + getCookies('kefu_token');
    this.text = this.replace_em('[em-smiling_imp]');
    // Socket.init(this,'kefu');
  },
  methods: {
    onSocketOpen(key) {
      if (key == 2) {
        this.ws.send({
          type: 'kefu_login',
          data: getCookies('kefu_token'),
        });
      }
    },
    onSocketClose(data) {
      if (data.key == 2) {
        this.$message.error('Mất kết nối, đang thử kết nối lại...');
        setTimeout(() => {
          this.ws.init(2);
        }, 2000);
      }
    },
    beforeUpload(file) {
      return isPicUpload(file);
    },
    handleFormatError(file) {
      this.$message.error('Ảnh tải lên chỉ được có định dạng jpg, jpg, jpeg, gif!');
    },
    bindEnter(e) {},
    //Kích hoạt khi tải lên ảnh chụp màn hình WeChat
    handleParse(e) {
      let file = null;
      if (
        e.clipboardData &&
        e.clipboardData.items[0] &&
        e.clipboardData.items[0].type &&
        e.clipboardData.items[0].type.indexOf('image') > -1
      ) {
        //Ở đây kiểm tra có file được paste vào không và file đó có phải định dạng ảnh không
        file = e.clipboardData.items[0].getAsFile();
      } else {
        this.$message({
          type: 'warning',
          message: 'Tệp tải lên phải là hình ảnh, không thể sao chép ảnh cục bộ và không thể sao chép nhiều ảnh cùng lúc',
        });
        return;
      }
      this.update(file);
    },
    update(e) {
      // Tải ảnh lên
      let file = e;
      let param = new FormData(); // Tạo đối tượng form
      param.append('filename', 'file'); // Dùng append để thêm dữ liệu vào đối tượng form
      param.append('file', file); // Dùng append để thêm dữ liệu vào đối tượng form
      // Thêm header request
      uploadImg(param).then((res) => {
        this.sendMsg(res.data.url, 3);
      });
    },
    // Tải lên thành công
    handleSuccess(res, file, fileList) {
      if (res.status === 200) {
        this.$message.success(res.msg);
        this.sendMsg(res.data.url, 3);
      } else {
        this.$message.error(res.msg);
      }
    },
    //Chi tiết đơn hàng
    lookOrder(item) {
      this.orderId = item.orderInfo.id;
      this.isOrder = true;
    },
    setOnline(data) {
      Socket.then((ws) => {
        ws.send({
          data: {
            online: data,
          },
          type: 'online',
        });
      });
      this.online = data;
    },
    // Ngăn hành vi xuống dòng mặc định của trình duyệt
    listen(event) {
      if (!event.shiftKey && event.keyCode == 13) {
        if (event.target.value == '') {
          return this.$message.error('Vui lòng nhập tin nhắn');
        }
        this.sendMsg(event.target.value, 1);
        this.chatCon = '';
        this.$nextTick(() => this.$refs.chatInput.focus());
      }
    },
    // Chọn biểu tượng cảm xúc trong ô nhập
    select(data) {
      let val = `[${data}]`;
      this.chatCon += val;
      this.isEmoji = false;
    },
    // Chuyển đổi biểu tượng cảm xúc trong chat
    replace_em(str) {
      str = str.replace(/\[([^\[\]]+)\]/g, "<span class='em $1'/></span>");
      return str;
    },
    // Lấy trạng thái có phải khách không
    changeType(data) {
      this.tourist = data;
    },
    // Lấy thông tin người dùng trong danh sách
    setDataId(data) {
      this.userActive = data;
      this.chatList = [];
      this.upperId = 0;
      this.oldHeight = 0;
      this.isScroll = true;
      if (data) {
        window.document.title = data.nickname
          ? `Đang trò chuyện với ${data.nickname} - ${this.kefuInfo.site_name}`
          : 'Đang trò chuyện với khách vãng lai - ' + this.kefuInfo.site_name;

        Socket.then((ws) => {
          ws.send({
            data: {
              id: this.userActive.to_uid,
            },
            type: 'to_chat',
          });
        });
        this.getChatList();
      } else {
        window.document.title = this.kefuInfo.site_name;
      }
    },
    msgClose() {
      this.isTransfer = false;
    },
    // Chọn mẫu câu
    activeTxt(data) {
      this.chatCon = data;
      this.isMsg = false;
    },
    // Gửi văn bản
    sendText() {
      this.sendMsg(this.chatCon, 1);
      this.chatCon = '';
      this.$nextTick(() => this.$refs.chatInput.focus());
    },

    // Xử lý gửi thống nhất
    sendMsg(msn, type) {
      let obj = {
        type: 'chat',
        data: {
          msn,
          type,
          to_uid: this.userActive.to_uid,
          is_tourist: this.tourist,
        },
      };
      Socket.then((ws) => {
        ws.send(obj);
      });
    },
    send(type, data) {
      Socket.send({
        data,
        type,
      });
    },
    // Lấy danh sách chat
    getChatList() {
      serviceList({
        limit: this.limit,
        uid: this.userActive.to_uid,
        upperId: this.upperId,
        is_tourist: this.tourist,
      }).then((res) => {
        res.data.forEach((el) => {
          if (el.msn_type == 1) {
            el.msn = this.replace_em(el.msn);
          } else if (el.msn_type == 2) {
            el.msn = this.replace_em(`[${el.msn}]`);
          }
        });
        let selector = '';
        if (this.upperId == 0) {
          selector = '';
        } else {
          selector = `chat_${this.chatList[0].id}`;
        }

        // this.chatList = res.data.concat(this.chatList)
        this.chatList = [...res.data, ...this.chatList];
        this.upperId = res.data.length > 0 ? res.data[0].id : 0;
        this.isLoad = false;
        this.$nextTick(() => {
          // this.scrollToTop()
          this.isScroll = res.data.length >= this.limit;
          this.setPageScrollTo(selector);
        });
      });
    },
    // Đặt vị trí cuộn trang
    setPageScrollTo(selector) {
      this.$nextTick(() => {
        if (selector) {
          setTimeout(() => {
            let num = parseFloat(document.getElementById(selector).offsetTop) - 60;
            this.scrollTop = num;
          }, 0);
        } else {
          var container = document.querySelector('#chat_scroll');
          this.scrollTop = container.offsetHeight;
          setTimeout((res) => {
            if (this.scrollTop != this.$refs.scrollBox.offsetHeight) {
              this.scrollTop = document.querySelector('#chat_scroll').offsetHeight;
            }
          }, 300);
        }
      });
    },
    //Cuộn lên đầu
    scrollHandler() {
      let self = this;
      if (this.isScroll && this.upperId) {
        this.isLoad = true;
        this.getChatList();
      }
    },
    // Animation thanh cuộn
    scrollToTop(duration) {
      var container = document.querySelector('#chat_scroll');
      this.scrollTop = container.offsetHeight - this.oldHeight;
      setTimeout((res) => {
        this.scrollTop = this.$refs.scrollBox.offsetHeight - this.oldHeight;
      }, 300);
    },
    // Đẩy sản phẩm
    bindPush(data) {
      this.sendMsg(data, 5);
    },
    // Chi tiết sản phẩm
    lookGoods(item) {
      this.goodsId = item.msn;
      this.isProductBox = true;
    },
    // Tìm kiếm người dùng
    bindSearch(data) {
      this.searchData = data;
      this.oldHeight = 0;
      this.upperId = 0;
      this.isScroll = false;
    },
    // Chuyển tiếp CSKH
    transferPeople(data) {
      this.transferId = data.id;
      this.isTransfer = false;
      this.$message.success('Chuyển tiếp thành công');
      Socket.then((ws) => {
        ws.send({
          type: 'to_chat',
          data: { id: data.uid },
        });
      });
    },
    // Xác nhận chuyển tiếp CSKH
    transferOk() {},
  },
};
</script>

<style lang="scss" scoped>
@import '../../../styles/emoji-awesome/css/google.min.css';
::v-deeptextarea.ivu-input {
  border: none;
  resize: none;
}
.kefu-layouts {
  padding-top: 30px;
  height: 100%;
  display: flex;
  background: #ccc;
  overflow: scroll;
}
.content-wrapper {
  display: flex;
  flex-direction: column;
  width: 1200px;
  height: 810px;
  margin: 0 auto;
  background: #fff;
  .container {
    flex: 1;
    display: flex;
    .chat-content {
      width: 600px;
      height: 100%;
      border-right: 1px solid #ececec;
      .chat-body {
        height: 530px;
        .chat-item {
          margin-bottom: 10px;
          .time {
            text-align: center;
            color: #999999;
            font-size: 14px;
            margin: 18px 0;
          }
          .flex-box {
            display: flex;
          }
          .avatar {
            width: 40px;
            height: 40px;
            margin-right: 16px;

            img {
              display: block;
              width: 100%;
              height: 100%;
              border-radius: 50%;
            }
          }
          .msg-wrapper {
            max-width: 320px;
            background: #f5f5f5;
            border-radius: 10px;
            color: #000000;
            font-size: 14px;
            overflow: hidden;
            .txt-wrapper {
              word-break: break-all;
              white-space: pre-wrap;
            }
            .pad16 {
              padding: 9px;
            }
            .img-wraper img {
              max-width: 100%;
              height: auto;
              display: block;
            }
            .order-wrapper {
              display: flex;
              width: 320px;
              .img-box {
                width: 60px;
                height: 60px;

                img {
                  width: 100%;
                  height: 100%;
                  border-radius: 5px;
                }
              }
              .order-info {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                width: 224px;
                margin-left: 10px;
                font-size: 12px;
                .price-box {
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  font-size: 14px;
                  color: #ff0000;
                  .more {
                    font-size: 12px;
                    color: var(--prev-color-primary);
                  }
                }
                .name {
                  font-size: 14px;
                }
                .sku {
                  margin: 1px 0;
                  color: #999999;
                }
              }
            }
          }
          &.right-box {
            .flex-box {
              flex-direction: row-reverse;
              .avatar {
                margin-right: 0;
                margin-left: 16px;
              }
              .msg-wrapper {
                background: #cde0ff;
              }
            }
            &.gary .msg-wrapper {
              background: #f5f5f5;
            }
          }
        }
      }
      .chat-textarea {
        height: 214px;
        border-top: 1px solid #ececec;
        .chat-btn-wrapper {
          position: relative;
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 15px 0;
          .left-wrappers {
            display: flex;
            align-items: center;
            .icon-item {
              display: flex;
              align-items: center;
              margin-left: 20px;
              cursor: pointer;
              .iconfont {
                font-size: 22px;
                color: #333333;
              }
            }
          }
          .right-wrapper {
            position: relative;
            padding-right: 20px;
            .icon-item {
              display: flex;
              align-items: center;
              font-size: 15px;
              color: #333;
              cursor: pointer;

              span {
                margin-left: 10px;
              }
            }
            .transfer-box {
              z-index: 60;
              position: absolute;
              right: 1px;
              bottom: 43px;
              width: 140px;
              background: #fff;
              box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
              padding: 16px;
            }
            .transfer-bg {
              z-index: 50;
              position: fixed;
              left: 0;
              top: 0;
              width: 100%;
              height: 100%;
              background: transparent;
            }
          }
          .emoji-box {
            position: absolute;
            left: 0;
            top: 0;
            transform: translateY(-100%);
            display: flex;
            flex-wrap: wrap;
            width: 60%;
            padding: 15px 9px;
            box-shadow: 0px 0px 13px 1px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: auto;
            height: 240px;
            .emoji-item {
              margin-right: 13px;
              margin-bottom: 8px;
              cursor: pointer;
              &:nth-child(10n) {
                margin-right: 0;
              }
            }
          }
        }
      }
    }
  }
}
.send-btn {
  position: absolute;
  right: 0;
  bottom: 10px;
  display: flex;
  justify-content: flex-end;
  margin-top: 10px;
  margin-right: 10px;
  width: 80px;
  .btns {
    width: 100%;
    background: #3875ea;
    &[disabled] {
      background: #cccccc;
      color: #fff;
    }
  }
}
.bg {
  z-index: 100;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
}
::v-deep .happy-scroll-content {
  width: 100%;
  .demo-spin-icon-load {
    animation: ani-demo-spin 1s linear infinite;
  }

  @-webkit-keyframes ani-demo-spin {
    from {
      transform: rotate(0deg);
    }
    50% {
      transform: rotate(180deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  @-moz-keyframes ani-demo-spin {
    from {
      transform: rotate(0deg);
    }
    50% {
      transform: rotate(180deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  @-ms-keyframes ani-demo-spin {
    from {
      transform: rotate(0deg);
    }
    50% {
      transform: rotate(180deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  @-o-keyframes ani-demo-spin {
    from {
      transform: rotate(0deg);
    }
    50% {
      transform: rotate(180deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  @keyframes ani-demo-spin {
    from {
      transform: rotate(0deg);
    }
    50% {
      transform: rotate(180deg);
    }

    to {
      transform: rotate(360deg);
    }
  }
  .demo-spin-col {
    height: 100px;
    position: relative;
    border: 1px solid #eee;
  }
}
.isMsgbox {
  ::v-deep .ivu-modal-body {
    padding: 0;
  }
}
.emoji-box::-webkit-scrollbar {
  width: 0;
}
.textarea-box ::v-deep .ivu-input:focus {
  box-shadow: none;
}
.textarea-box ::v-deep .el-textarea__inner {
  border: none;
  resize: none;
}
</style>
