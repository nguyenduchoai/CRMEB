<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.meta.title"
      :backUrl="$routeProStr + '/app/wechat/reply/keyword'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="ivu-mt-16">
      <!-- Cài đặt OA WeChat -->
      <el-row :gutter="24">
        <el-col :span="24" class="ml40">
          <!-- Chức năng xem trước -->
          <el-col :span="24">
            <el-col :xl="7" :lg="7" :md="22" :sm="22" :xs="22" class="left mb15">
              <img class="top" src="../../../../assets/images/mobilehead.png" />
              <img class="bottom" src="../../../../assets/images/mobilefoot.png" />
              <div class="centent">
                <div class="time-wrapper"><span class="time">9:36</span></div>
                <div class="view-item text-box clearfix" v-if="formValidate.type !== 'news'">
                  <div class="avatar fl"><img src="../../../../assets/images/head.gif" /></div>
                  <div class="box-content fl">
                    <span v-text="formValidate.data.content" v-if="formValidate.type === 'text'"></span>
                    <div class="box-content_pic" v-if="formValidate.data.src">
                      <img :src="formValidate.data.src ? imgUrl + formValidate.data.src : ''" />
                    </div>
                  </div>
                </div>
                <div v-if="formValidate.type === 'news'">
                  <div v-for="(j, i) in formValidate.data.list" :key="i">
                    <div v-if="i === 0">
                      <div
                        class="news_pic"
                        :style="{ backgroundImage: 'url(' + j.image_input[0] + ')', backgroundSize: '100% 100%' }"
                      ></div>
                      <span class="news_sp">{{ j.title }}</span>
                    </div>
                    <div v-else class="news_cent">
                      <span class="news_sp1" v-if="j.synopsis">{{ j.title }}</span>
                      <div class="news_cent_img" v-if="j.image_input.length !== 0"><img :src="j.image_input[0]" /></div>
                    </div>
                  </div>
                </div>
              </div>
            </el-col>
            <el-col :xl="11" :lg="12" :md="22" :sm="22" :xs="22">
              <el-col :span="24" class="userAlert">
                <div class="box-card right">
                  <el-form
                    ref="formValidate"
                    :model="formValidate"
                    :rules="ruleValidate"
                    label-width="100px"
                    class="mt20"
                    @submit.native.prevent
                  >
                    <el-form-item label="Từ khóa:" prop="val" v-if="$route.params.id">
                      <div class="arrbox">
                        <!--:closable="$route.params.id==='0'? true : false"-->
                        <el-tag
                          @close="handleClose(item)"
                          :name="item"
                          :closable="true"
                          v-for="(item, index) in labelarr"
                          :key="index"
                          >{{ item }}
                        </el-tag>
                        <!--:readonly="$route.params.id!=='0'"-->
                        <input
                          class="arrbox_ip"
                          v-model="val"
                          placeholder="Nhập rồi nhấn Enter"
                          style="width: 90%"
                          @keyup.enter="addlabel"
                        />
                      </div>
                    </el-form-item>
                    <el-form-item label="Trạng thái tin nhắn:">
                      <el-radio-group v-model="formValidate.status">
                        <el-radio :label="1">Kích hoạt</el-radio>
                        <el-radio :label="0">Vô hiệu hóa</el-radio>
                      </el-radio-group>
                    </el-form-item>
                    <el-form-item label="Loại tin nhắn:" prop="type">
                      <el-select
                        v-model="formValidate.type"
                        placeholder="Vui lòng chọn trạng thái quy tắc"
                        style="width: 90%"
                        @change="RuleFactor(formValidate.type)"
                      >
                        <el-option value="text" label="Tin nhắn văn bản"></el-option>
                        <el-option value="image" label="Tin nhắn hình ảnh"></el-option>
                        <el-option value="news" label="Tin nhắn hình ảnh và văn bản"></el-option>
                        <el-option value="voice" label="Tin nhắn thoại"></el-option>
                      </el-select>
                    </el-form-item>
                    <el-form-item label="Nội dung tin nhắn:" prop="content" v-if="formValidate.type === 'text'">
                      <el-input
                        v-model="formValidate.data.content"
                        placeholder="Vui lòng điền nội dung tin nhắn"
                        style="width: 90%"
                        type="textarea"
                        :rows="4"
                      ></el-input>
                    </el-form-item>
                    <el-form-item label="Chọn tin bài:" v-if="formValidate.type === 'news'">
                      <el-button v-db-click @click="changePic">Chọn tin bài</el-button>
                    </el-form-item>

                    <el-form-item
                      :label="formValidate.type === 'image' ? 'Địa chỉ hình ảnh:' : 'Địa chỉ tệp âm thanh:'"
                      prop="src"
                      v-if="formValidate.type === 'image' || formValidate.type === 'voice'"
                    >
                      <div class="acea-row row-middle">
                        <el-input
                          readonly="readonly"
                          placeholder="default size"
                          style="width: 75%"
                          class="mr15"
                          v-model="formValidate.data.src"
                        />
                        <el-upload
                          :show-file-list="false"
                          :action="fileUrl"
                          :on-success="handleSuccess"
                          :accept="formValidate.type === 'image' ? formatImg : formatVoice"
                          :max-size="2048"
                          :headers="header"
                          :on-format-error="handleFormatError"
                          :on-exceeded-size="handleMaxSize"
                          :before-upload="beforeUpload"
                          class="mr20"
                          style="margin-top: 1px"
                        >
                          <el-button type="primary">Tải lên</el-button>
                        </el-upload>
                      </div>
                      <span v-show="formValidate.type === 'image'">Tệp tối đa 2Mb, hỗ trợ định dạng bmp/png/jpeg/jpg/gif</span>
                      <span v-show="formValidate.type === 'voice'">Tệp tối đa 2Mb, hỗ trợ định dạng mp3, thời lượng phát không quá 60s</span>
                    </el-form-item>
                    <el-form-item>
                      <el-button type="primary" class="mr20" v-db-click @click="submenus('formValidate')"
                        >Lưu và đăng
                      </el-button>
                    </el-form-item>
                  </el-form>
                </div>
              </el-col>
              <!-- <el-col :span="24">
                <div class="acea-row row-center">
                  <el-button type="primary" class="mr20" v-db-click @click="submenus('formValidate')">Lưu và đăng </el-button>
                </div>
              </el-col> -->
            </el-col>
          </el-col>
        </el-col>
      </el-row>
    </el-card>

    <!--Tin nhắn hình ảnh và văn bản -->
    <el-dialog :visible.sync="modals" title="Gửi tin nhắn" width="1200px" :lock-scroll="false" class="modelBox">
      <news-category
        v-if="modals"
        @getCentList="getCentList"
        :scrollerHeight="scrollerHeight"
        :contentTop="contentTop"
        :contentWidth="contentWidth"
        :maxCols="maxCols"
      ></news-category>
    </el-dialog>
  </div>
</template>

<script>
import Setting from '@/setting';
import { replyApi, keywordsinfoApi } from '@/api/app';
// import { mapActions } from 'vuex'
import newsCategory from '@/components/newsCategory/index';
import { getCookies } from '@/libs/util';
import { isPicUpload } from '@/utils';

export default {
  name: 'follow',
  components: { newsCategory },
  data() {
    const validateContent = (rule, value, callback) => {
      if (this.formValidate.type === 'text') {
        if (this.formValidate.data.content === '') {
          callback(new Error('Vui lòng điền nội dung quy tắc'));
        } else {
          callback();
        }
      }
    };
    const validateSrc = (rule, value, callback) => {
      if (this.formValidate.type === 'image' && this.formValidate.data.src === '') {
        callback(new Error('Vui lòng tải lên'));
      } else {
        callback();
      }
    };
    const validateVal = (rule, value, callback) => {
      if (this.labelarr.length === 0) {
        callback(new Error('Vui lòng nhập rồi nhấn Enter'));
      } else {
        callback();
      }
    };
    return {
      delfromData: {},
      isShow: false,
      maxCols: 4,
      scrollerHeight: '600',
      contentTop: '130',
      contentWidth: '98%',
      modals: false,
      val: '',
      formatImg: ['jpg', 'jpeg', 'png', 'bmp', 'gif'],
      formatVoice: ['mp3', 'wma', 'wav', 'amr'],
      header: {},
      formValidate: {
        status: -1,
        type: '',
        key: this.$route.params.key || '',
        data: {
          content: '',
          src: '',
          list: [],
        },
        id: 0,
      },
      fileUrl: Setting.apiBaseURL + '/file/upload/1',
      ruleValidate: {
        val: [{ required: true, validator: validateVal, trigger: 'change' }],
        type: [{ required: true, message: 'Vui lòng chọn loại tin nhắn', trigger: 'change' }],
        content: [{ required: true, validator: validateContent, trigger: 'blur' }],
        src: [{ required: true, validator: validateSrc, trigger: 'change' }],
      },
      labelarr: [],
    };
  },
  watch: {
    $route(to, from) {
      if (this.$route.params.key || this.$route.params.id !== '0') {
        this.formValidate.key = this.$route.params.key;
        this.details();
      } else {
        this.labelarr = [];
        this.$refs['formValidate'].resetFields();
      }
    },
  },
  computed: {
    imgUrl() {
      const search = '/adminapi/';
      const start = Setting.apiBaseURL.indexOf(search);
      return Setting.apiBaseURL.substring(0, start); // Cắt chuỗi
    },
  },
  mounted() {
    this.getToken();
    if (this.$route.params.key || (this.$route.params.id && this.$route.params.id !== '0')) {
      this.details();
    }
  },
  methods: {
    beforeUpload(file) {},
    getCentList(val) {
      this.formValidate.data.list = val.new;
      this.modals = false;
    },
    addlabel() {
      let count = this.labelarr.indexOf(this.val);
      if (count === -1) {
        this.labelarr.push(this.val);
      }
      this.val = '';
    },
    handleClose(name) {
      const index = this.labelarr.indexOf(name);
      this.labelarr.splice(index, 1);
    },
    // Chi tiết
    details() {
      let url = '';
      let data = {};
      if (this.$route.params.id) {
        url = 'app/wechat/keyword/' + this.$route.params.id;
        data = {};
      } else {
        url = 'app/wechat/reply';
        data = {
          key: {
            key: this.formValidate.key,
          },
        };
      }
      keywordsinfoApi(url, data)
        .then(async (res) => {
          if (res.data.info.data instanceof Array) {
            this.formValidate.status = 0;
            return;
          }
          let info = res.data.info || {};
          let data = info.data || {};
          this.formValidate = {
            status: info.status,
            type: info.type,
            key: info.key,
            data: {
              content: data.content,
              src: data.src,
              list: data.list,
            },
            id: info.id,
          };
          if (this.$route.params.id) {
            this.labelarr = this.formValidate.key.split(',') || [];
          }
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Chọn bài viết
    changePic() {
      this.modals = true;
    },
    // Danh sách thả xuống
    RuleFactor(type) {
      switch (type) {
        case 'text':
          this.formValidate.data.src = '';
          this.formValidate.data.list = [];
          break;
        case 'news':
          this.formValidate.data.src = '';
          this.formValidate.data.content = '';
          break;
        default:
          this.formValidate.data.list = [];
          this.formValidate.data.content = '';
          this.formValidate.data.src = '';
      }
      // this.$refs['formValidate'].resetFields();
    },
    // Token trong header khi tải lên
    getToken() {
      this.header['Authori-zation'] = 'Bearer ' + getCookies('token');
    },
    // Tải lên thành công
    handleSuccess(res, file) {
      if (res.status === 200) {
        this.formValidate.data.src = res.data.src;
        this.$message.success(res.msg);
      } else {
        this.$message.error(res.msg);
      }
    },
    handleFormatError(file) {
      if (this.formValidate.type === 'image') {
        this.$message.warning('Vui lòng tải lên ảnh định dạng bmp/png/jpeg/jpg/gif');
      } else {
        this.$message.warning('Vui lòng tải lên âm thanh định dạng mp3/wma/wav/amr');
      }
    },
    handleMaxSize(file) {
      this.$message.warning('Vui lòng tải lên tệp có dung lượng không quá 2M');
    },
    // Lưu
    submenus(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          let data = {};
          if (this.$route.params.id) {
            this.formValidate.key = this.labelarr.join(',');
            data = {
              url: 'app/wechat/keyword/' + this.$route.params.id,
              key: this.formValidate,
            };
          } else {
            data = {
              url: 'app/wechat/keyword/' + this.formValidate.id,
              key: this.formValidate,
            };
          }
          replyApi(data)
            .then(async (res) => {
              this.operation();
              this.$message.success(res.msg);
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    // Xử lý khi lưu thành công
    operation() {
      if (this.$route.params.id && this.$route.params.id === '0') {
        this.$msgbox({
          title: 'Thông báo',
          message: 'Bạn có muốn tiếp tục thêm không',
          showCancelButton: true,
          cancelButtonText: 'Không',
          confirmButtonText: 'Có',
          iconClass: 'el-icon-warning',
          confirmButtonClass: 'btn-custom-cancel',
        })
          .then(() => {
            setTimeout(() => {
              this.labelarr = [];
              this.val = '';
              this.$refs['formValidate'].resetFields();
            }, 1000);
          })
          .catch(() => {
            setTimeout(() => {
              this.$router.push({ path: this.$routeProStr + '/app/wechat/reply/keyword' });
            }, 500);
          });
      } else if (this.$route.params.id && this.$route.params.id !== '0') {
        this.$router.push({ path: this.$routeProStr + '/app/wechat/reply/keyword' });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
* {
  -moz-user-select: none; /* Firefox */
  -webkit-user-select: none; /* Trình duyệt webkit */
  -ms-user-select: none; /* IE10 */
  -khtml-user-select: none; /* Trình duyệt đời cũ */
  user-select: none;
}
.arrbox {
  background-color: white;
  font-size: 12px;
  border: 1px solid #dcdee2;
  border-radius: 6px;
  margin-bottom: 0px;
  padding: 0 5px;
  text-align: left;
  box-sizing: border-box;
  width: 90%;
  .el-tag {
    margin-right: 3px;
  }
}
.arrbox_ip {
  font-size: 12px;
  border: none;
  box-shadow: none;
  outline: none;
  background-color: transparent;
  padding: 0;
  margin: 0;
  width: auto !important;
  max-width: inherit;
  min-width: 80px;
  vertical-align: top;
  height: 30px;
  color: #34495e;
  margin: 2px;
  line-height: 30px;
}
.left {
  min-width: 390px;
  min-height: 550px;
  position: relative;
  padding-left: 40px;
}
.top {
  position: absolute;
  top: 0px;
}
.bottom {
  position: absolute;
  bottom: 0px;
}
.centent {
  background: #f4f5f9;
  min-height: 438px;
  position: absolute;
  top: 63px;
  width: 320px;
  height: 60%;
  overflow-y: auto;
  padding: 15px;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.right {
  background: #fff;
  min-height: 300px;
}
.box-content {
  position: relative;
  max-width: 60%;
  min-height: 40px;
  margin-left: 15px;
  padding: 10px;
  box-sizing: border-box;
  border: 1px solid #ccc;
  word-break: break-all;
  word-wrap: break-word;
  line-height: 1.5;
  border-radius: 5px;
}
.box-content_pic {
  width: 100%;
}
.box-content_pic img {
  width: 100%;
  height: auto;
}
.box-content:before {
  content: '';
  position: absolute;
  left: -13px;
  top: 11px;
  display: block;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 10px solid #ccc;
  -webkit-transform: rotate(90deg);
  transform: rotate(90deg);
}
.box-content:after {
  content: '';
  content: '';
  position: absolute;
  left: -12px;
  top: 11px;
  display: block;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 10px solid #f5f5f5;
  -webkit-transform: rotate(90deg);
  transform: rotate(90deg);
}
.time-wrapper {
  margin-bottom: 10px;
  text-align: center;
}
.time {
  display: inline-block;
  color: #f5f5f5;
  background: rgba(0, 0, 0, 0.3);
  padding: 3px 8px;
  border-radius: 3px;
  font-size: 12px;
}
.text-box {
  display: flex;
}
.avatar {
  width: 40px;
  height: 40px;
}
.avatar img {
  width: 100%;
  height: 100%;
}
.modelBox {
  ::v-deep .ivu-modal-body {
    padding: 0 16px 16px 16px !important;
  }
}
.news_pic {
  width: 100%;
  height: 150px;
  overflow: hidden;
  position: relative;
  background-size: 100%;
  background-position: center center;
  border-radius: 5px 5px 0 0;
  padding: 10px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}
.news_sp {
  font-size: 12px;
  color: #000000;
  background: #fff;
  width: 100%;
  height: 38px;
  line-height: 38px;
  padding: 0 12px;
  box-sizing: border-box;
  display: block;
}
.news_cent {
  width: 100%;
  height: auto;
  background: #fff;
  border-top: 1px dashed #eee;
  display: flex;
  padding: 10px;
  box-sizing: border-box;
  justify-content: space-between;
  .news_sp1 {
    font-size: 12px;
    color: #000000;
    width: 71%;
  }
  .news_cent_img {
    width: 81px;
    height: 46px;
    border-radius: 6px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
    }
  }
}
</style>
