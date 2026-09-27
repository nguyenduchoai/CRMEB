<template>
  <div class="article-manager">
    <div class="i-layout-page-header">
      <div class="i-layout-page-header">
        <span class="ivu-page-header-title">Cài đặt hệ thống</span>
        <div>
          <el-tabs v-model="currentTab">
            <el-tab-pane label="Cấu hình OA WeChat" name="onsale" />
            <el-tab-pane label="Cấu hình" name="forsale" />
          </el-tabs>
        </div>
      </div>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" v-if="currentTab === 'onsale'">
      <!--<form-create  :rule="Array.from(FromData.rules)" @submit="onSubmit" ></form-create>-->
    </el-card>
  </div>
</template>

<script>
import formCreate from '@form-create/element-ui';
export default {
  name: 'setApp',
  components: { formCreate },
  data() {
    return {
      FromData: null,
      currentTab: '',
    };
  },
  mounted() {},
  methods: {},
};
</script>

<style scoped></style>
