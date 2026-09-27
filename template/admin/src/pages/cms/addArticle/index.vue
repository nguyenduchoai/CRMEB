<template>
  <div class="article-manager">
    <pages-header
      ref="pageHeader"
      :title="$route.params.id ? 'Sửa bài viết' : 'Thêm bài viết'"
      :backUrl="$routeProStr + '/cms/article/index'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-form
        class="form"
        ref="formValidate"
        :model="formValidate"
        :rules="ruleValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <div class="goodsTitle acea-row">
          <div class="title">Thông tin bài viết</div>
        </div>
        <div class="grid_box">
          <el-form-item label="Tiêu đề:" prop="title" label-for="title">
            <el-input
              v-model="formValidate.title"
              placeholder="Vui lòng nhập"
              class="content_width"
              maxlength="80"
              show-word-limit
            />
          </el-form-item>
          <el-form-item label="Tác giả:" prop="author" label-for="author">
            <el-input
              v-model="formValidate.author"
              placeholder="Vui lòng nhập"
              class="content_width"
              maxlength="10"
              show-word-limit
            />
          </el-form-item>
          <el-form-item label="Danh mục bài viết:" label-for="cid" prop="cid">
            <el-cascader
              class="content_width"
              v-model="formValidate.cid"
              size="small"
              :options="treeData"
              :props="{ multiple: false, checkStrictly: true, emitPath: false }"
              clearable
            ></el-cascader>
          </el-form-item>
          <el-form-item label="Giới thiệu bài viết:" prop="synopsis" label-for="synopsis">
            <el-input
              v-model="formValidate.synopsis"
              type="textarea"
              placeholder="Vui lòng nhập"
              class="content_width"
              maxlength="300"
              show-word-limit
            />
          </el-form-item>
          <el-form-item label="Ảnh bìa bài viết:" prop="image_input">
            <div class="picBox" v-db-click @click="modalPicTap('Chọn một')">
              <div class="pictrue" v-if="formValidate.image_input">
                <img :src="formValidate.image_input" />
              </div>
              <div class="upLoad acea-row row-center-wrapper" v-else>
                <i class="el-icon-plus" style="font-size: 24px"></i>
              </div>
            </div>
            <div class="tip">Kích thước đề xuất: 500 x 312 px</div>
          </el-form-item>
        </div>
        <div class="goodsTitle acea-row">
          <div class="title">Nội dung bài viết</div>
        </div>
        <el-form-item label="Nội dung bài viết:" prop="content">
          <WangEditor style="width: 90%" :content="formValidate.content" @editorContent="getEditorContent"></WangEditor>
        </el-form-item>
        <div class="goodsTitle acea-row">
          <div class="title">Cài đặt khác</div>
        </div>
        <el-row :gutter="24">
          <!--                    <el-col :span="24">-->
          <!--                        <el-form-item label="Link bài gốc:">-->
          <!--                            <el-input v-model="formValidate.url" placeholder="Vui lòng nhập" element-id="url" style="width: 60%"/>-->
          <!--                        </el-form-item>-->
          <!--                    </el-col>-->
          <el-col :span="24">
            <el-form-item label="Hiển thị banner:" label-for="is_banner">
              <el-radio-group v-model="formValidate.is_banner" element-id="is_banner">
                <el-radio :label="1" class="radio">Hiện</el-radio>
                <el-radio :label="0">Không hiển thị</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Bài viết nổi bật:" label-for="is_hot">
              <el-radio-group v-model="formValidate.is_hot" element-id="is_hot">
                <el-radio :label="1" class="radio">Hiện</el-radio>
                <el-radio :label="0">Không hiển thị</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="">
              <el-button type="primary" class="submission" v-db-click @click="onsubmit('formValidate')">Gửi</el-button>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <el-dialog :visible.sync="modalPic" width="950px" title="Tải lên ảnh sản phẩm" :close-on-click-modal="false">
        <uploadPictures
          :isChoice="isChoice"
          @getPic="getPic"
          :gridBtn="gridBtn"
          :gridPic="gridPic"
          v-if="modalPic"
        ></uploadPictures>
      </el-dialog>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import uploadPictures from '@/components/uploadPictures';
import WangEditor from '@/components/wangEditor/index.vue';
import { cmsAddApi, createApi, categoryTreeListApi } from '@/api/cms';
export default {
  name: 'addArticle',
  components: { uploadPictures, WangEditor },
  data() {
    const validateUpload = (rule, value, callback) => {
      if (this.formValidate.image_input) {
        callback();
      } else {
        callback(new Error('Vui lòng tải lên ảnh bìa bài viết'));
      }
    };
    const validateUpload2 = (rule, value, callback) => {
      if (!this.formValidate.cid) {
        callback(new Error('Vui lòng chọn danh mục bài viết'));
      } else {
        callback();
      }
    };
    return {
      dialog: {},
      isChoice: 'Chọn một',
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      loading: false,
      formValidate: {
        id: 0,
        title: '',
        author: '',
        image_input: '',
        content: '',
        synopsis: '',
        url: '',
        is_hot: 0,
        is_banner: 0,
        cid: '',
        visit: 0,
      },
      content: '',
      ruleValidate: {
        title: [{ required: true, message: 'Vui lòng nhập tiêu đề', trigger: 'blur' }],
        cid: [
          {
            required: true,
            validator: validateUpload2,
            trigger: 'change',
            type: 'number',
          },
        ],
        image_input: [{ required: true, validator: validateUpload, trigger: 'change' }],
        content: [{ required: true, message: 'Vui lòng nhập nội dung bài viết', trigger: 'change' }],
      },
      value: '',
      modalPic: false,
      template: false,
      treeData: [],
      formValidate2: {
        type: 1,
      },
      myConfig: {
        autoHeightEnabled: false, // Trình soạn thảo không tự động giãn cao theo nội dung
        initialFrameHeight: 500, // Chiều cao container ban đầu
        initialFrameWidth: '100%', // Chiều rộng container ban đầu
        UEDITOR_HOME_URL: '/UEditor/',
        serverUrl: '',
      },
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '100px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  watch: {
    $route(to, from) {
      if (this.$route.params.id) {
        this.getDetails();
      } else {
        this.formValidate = {
          id: 0,
          title: '',
          author: '',
          image_input: '',
          content: '',
          synopsis: '',
          url: '',
          is_hot: 0,
          is_banner: 0,
        };
      }
    },
  },
  methods: {
    getEditorContent(data) {
      this.content = data;
    },
    // Chọn ảnh
    modalPicTap() {
      this.modalPic = true;
    },
    // Ảnh đã chọn
    getPic(pc) {
      this.formValidate.image_input = pc.att_dir;
      this.modalPic = false;
    },
    // Danh mục
    getClass() {
      categoryTreeListApi()
        .then(async (res) => {
          this.treeData = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Lọc nội dung chi tiết
    formatRichText(html) {
      let newContent = html.replace(/<img[^>]*>/gi, function (match, capture) {
        match = match.replace(/style="[^"]+"/gi, '').replace(/style='[^']+'/gi, '');
        match = match.replace(/width="[^"]+"/gi, '').replace(/width='[^']+'/gi, '');
        match = match.replace(/height="[^"]+"/gi, '').replace(/height='[^']+'/gi, '');
        return match;
      });
      newContent = newContent.replace(/style="[^"]+"/gi, function (match, capture) {
        match = match.replace(/width:[^;]+;/gi, 'max-width:100%;').replace(/max-max-width:[^;]+;/gi, 'max-width:100%;');
        return match;
      });
      // newContent = newContent.replace(/<br[^>]*\/>/gi, '');
      newContent = newContent.replace(
        /\<img/gi,
        '<img style="max-width:100%;height:auto;display:block;margin-top:0;margin-bottom:0;"',
      );
      return newContent;
    },
    // Gửi dữ liệu
    onsubmit(name) {
      this.formValidate.content = this.formatRichText(this.content);
      this.$refs[name].validate((valid) => {
        if (valid) {
          cmsAddApi(this.formValidate)
            .then(async (res) => {
              this.$message.success(res.msg);
              setTimeout(() => {
                this.$router.push({ path: this.$routeProStr + '/cms/article/index' });
              }, 500);
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    // Chi tiết bài viết
    getDetails() {
      createApi(this.$route.params.id ? this.$route.params.id : 0)
        .then(async (res) => {
          let data = res.data;
          let news = data.info;
          this.formValidate = {
            id: news.id,
            title: news.title,
            author: news.author,
            image_input: news.image_input,
            content: news.content,
            synopsis: news.synopsis,
            url: news.url,
            is_hot: news.is_hot,
            is_banner: news.is_banner,
            cid: news.cid,
            visit: news.visit,
          };
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
  },
  mounted() {
    if (this.$route.params.id) {
      this.getDetails();
    }
  },
  created() {
    this.getClass();
  },
};
</script>
<style scoped lang="scss">
.grid_box {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: auto;
  grid-gap: 0;
}
.content_width {
  width: 414px;
}
::v-deep .ivu-form-item-content {
  line-height: unset !important;
}
.picBox {
  display: inline-block;
  cursor: pointer;
}

.form .goodsTitle {
  border-bottom: 1px solid rgba(0, 0, 0, 0.09);
  margin-bottom: 25px;
}

.form .goodsTitle ~ .goodsTitle {
  margin-top: 20px;
}

.form .goodsTitle .title {
  border-bottom: 2px solid var(--prev-color-primary);
  padding: 0 8px 12px 5px;
  color: #000;
  font-size: 14px;
}

.form .goodsTitle .icons {
  font-size: 15px;
  margin-right: 8px;
  color: #999;
}

.form .add {
  font-size: 12px;
  color: var(--prev-color-primary);
  padding: 0 12px;
  cursor: pointer;
}

.form .radio {
  margin-right: 20px;
}

.form .submission {
  width: 10%;
}

.form .upLoad {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
}

.form .iconfont {
  color: #898989;
}

.form .pictrue {
  width: 60px;
  height: 60px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 10px;
}

.form .pictrue img {
  width: 100%;
  height: 100%;
}

.Modals .address {
  width: 90%;
}

.Modals .address .iconfont {
  font-size: 20px;
}
.tip {
  margin-top: 10px;
  color: #bbb;
  font-size: 12px;
}
</style>
