<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form label-width="85px" @submit.native.prevent>
        <el-form-item label="Tên thỏa thuận:">
          <el-input v-model="agreement.title"></el-input>
        </el-form-item>
        <el-form-item label="Nội dung thỏa thuận:">
          <WangEditor :content="agreement.content" @editorContent="getEditorContent"></WangEditor>
        </el-form-item>
        <el-form-item label="Trạng thái bật:">
          <el-switch :active-value="1" :inactive-value="0" v-model="agreement.status" size="large"> </el-switch>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-db-click @click="memberAgreementSave">Lưu</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import WangEditor from '@/components/wangEditor/index.vue';
import { memberAgreement, memberAgreementSave } from '@/api/user';

export default {
  components: { WangEditor },
  data() {
    return {
      ueConfig: {
        autoHeightEnabled: false,
        initialFrameHeight: 500,
        initialFrameWidth: '100%',
        UEDITOR_HOME_URL: '/UEditor/',
        serverUrl: '',
      },
      id: 0,
      agreement: {
        title: '',
        content: '',
        status: 1,
      },
      spinShow: false,
    };
  },
  created() {
    this.memberAgreement();
  },
  methods: {
    getEditorContent(data) {
      this.agreement.content = data;
    },
    memberAgreement() {
      this.spinShow = true;
      memberAgreement()
        .then((res) => {
          this.spinShow = false;
          const { title, content, status, id } = res.data;
          this.agreement.title = title;
          this.agreement.content = content;
          this.agreement.status = status;
          this.id = id;
        })
        .catch((err) => {
          this.$message.error(err);
          this.spinShow = false;
        });
    },
    // Lưu
    memberAgreementSave() {
      memberAgreementSave(this.id, this.agreement)
        .then((res) => {
          this.$message.success('Lưu thành công');
          this.memberAgreement();
        })
        .catch((err) => {
          this.$message.error(err);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .ivu-form-item-content {
  line-height: unset !important;
}
</style>
