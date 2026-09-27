<template>
  <div>
    <div class="message">
      <el-card :bordered="false" shadow="never" :body-style="{ padding: '0 20px 20px' }">
        <div class="">
          <el-tabs v-model="currentTab" @tab-click="changeTab">
            <el-tab-pane
              :label="item.label"
              :name="item.value.toString()"
              v-for="(item, index) in headerList"
              :key="index"
            />
          </el-tabs>
        </div>
        <el-alert closable v-if="currentTab == 1">
          <template slot="title">
            <p>Ảnh thu nhỏ sẽ được tạo khi tải ảnh lên</p>
            <p>Nếu không thiết lập sẽ tạo theo mặc định của hệ thống, mặc định: ảnh lớn 800*800, ảnh vừa 300*300, ảnh nhỏ 150*150</p>
            <p>Watermark chỉ được tạo khi tải ảnh lên, và hiển thị theo tỷ lệ trên ảnh gốc cũng như ảnh thu nhỏ cỡ lớn, vừa, nhỏ.</p>
            <p>Nếu khi tải ảnh lên chưa bật watermark, thì ảnh đó vẫn sẽ không có watermark kể cả sau khi bật watermark.</p>
          </template>
        </el-alert>
        <el-alert closable v-else>
          <template slot="title">
            <p v-if="currentTab == 2">
              Cách kích hoạt Qiniu Cloud:<a href="https://doc.crmeb.com/single/v5/7792" target="_blank">Nhấp để xem</a>
            </p>
            <p v-if="currentTab == 3">
              Cách kích hoạt Alibaba Cloud oss:<a href="https://doc.crmeb.com/single/v5/7790" target="_blank">Nhấp để xem</a>
            </p>
            <p v-if="currentTab == 4">
              Cách kích hoạt Tencent Cloud cos:<a href="https://doc.crmeb.com/single/v5/7791" target="_blank">Nhấp để xem</a>
            </p>
            <p v-if="currentTab == 5">
              Cách kích hoạt JD Cloud cos:<a href="https://doc.crmeb.com/single/v5/8522" target="_blank">Nhấp để xem</a>
            </p>
            <p v-if="currentTab == 6">
              Cách kích hoạt Huawei Cloud cos:<a href="https://doc.crmeb.com/single/v5/8523" target="_blank">Nhấp để xem</a>
            </p>
            <p v-if="currentTab == 7">
              Cách kích hoạt Tianyi Cloud cos:<a href="https://doc.crmeb.com/single/v5/8524" target="_blank">Nhấp để xem</a>
            </p>
            <p>Bước 1: Thêm [Không gian lưu trữ] (tên không gian không được trùng)</p>
            <p>Bước 2: Bật [Trạng thái sử dụng]</p>
            <template v-if="currentTab == 2">
              <p>Bước 3 (bắt buộc): Trong danh sách không gian lưu trữ đám mây, chọn thao tác [Sửa tên miền không gian]</p>
              <p>Bước 4 (bắt buộc): Trong danh sách không gian lưu trữ đám mây, chọn [Cấu hình CNAME], sau khi mở, sao chép giá trị bản ghi sang nền tảng tương ứng để phân giải tên miền</p>
            </template>
            <template v-else>
              <p>Bước 3 (tùy chọn): Trong danh sách không gian lưu trữ đám mây, chọn thao tác [Sửa tên miền không gian]</p>
              <p>Bước 4 (tùy chọn): Trong danh sách không gian lưu trữ đám mây, chọn [Cấu hình CNAME], sau khi mở, sao chép giá trị bản ghi sang nền tảng tương ứng để phân giải tên miền</p>
            </template>
          </template>
        </el-alert>
      </el-card>
    </div>
    <div class="pt16" v-if="currentTab == 1">
      <el-card :bordered="false" shadow="never" class="ivu-mt">
        <el-row>
          <el-col :span="24">
            <span class="save-type"> Phương thức lưu trữ: </span>
            <el-radio-group v-model="formValidate.upload_type" @input="changeSave">
              <el-radio label="1">Lưu trữ cục bộ</el-radio>
              <el-radio label="2">Lưu trữ Qiniu Cloud</el-radio>
              <el-radio label="3">Lưu trữ Alibaba Cloud</el-radio>
              <el-radio label="4">Lưu trữ Tencent Cloud</el-radio>
              <el-radio label="5">Lưu trữ JD Cloud</el-radio>
              <el-radio label="6">Lưu trữ Huawei Cloud</el-radio>
              <el-radio label="7">Lưu trữ Tianyi Cloud</el-radio>
            </el-radio-group>
            <!-- <el-switch :active-value="1"  :inactive-value="0"
              v-model="localStorage"
              size="large"
              @change="addSwitch"
            >
              <span slot="open">Bật</span>
              <span slot="close">Tắt</span>
             </el-switch> -->
          </el-col>
        </el-row>
      </el-card>
      <el-card :bordered="false" shadow="never" class="ivu-mt">
        <el-form ref="formValidate" :model="formValidate" :rules="ruleValidate">
          <div class="abbreviation">
            <el-form-item label="Bật ảnh thu nhỏ:" label-width="110px">
              <el-switch
                :active-value="1"
                :inactive-value="0"
                v-model="formValidate.image_thumb_status"
                size="large"
              >
                <span slot="open">Bật</span>
                <span slot="close">Tắt</span>
              </el-switch>
            </el-form-item>
            <div class="top" v-if="formValidate.image_thumb_status == 1">
              <div class="topBox">
                <div class="topLeft">
                  <div class="img">
                    <img class="imgs" src="../../../assets/images/abbreviationBig.png" alt="" />
                  </div>
                  <div>Ảnh thu nhỏ cỡ lớn</div>
                </div>
                <div class="topRight">
                  <el-form-item label="Rộng:">
                    <el-input
                      class="topIput"
                      type="number"
                      v-model="formValidate.thumb_big_width"
                      placeholder="Vui lòng nhập chiều rộng"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                  <el-form-item label="Cao:">
                    <el-input
                      class="topIput"
                      type="number"
                      v-model="formValidate.thumb_big_height"
                      placeholder="Vui lòng nhập chiều cao"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                </div>
              </div>
              <div class="topBox">
                <div class="topLeft">
                  <div class="img">
                    <img class="imgs" src="../../../assets/images/abbreviation.png" alt="" />
                  </div>
                  <div>Ảnh thu nhỏ cỡ vừa</div>
                </div>
                <div class="topRight">
                  <el-form-item label="Rộng:">
                    <el-input
                      class="topIput"
                      type="number"
                      v-model="formValidate.thumb_mid_width"
                      placeholder="Vui lòng nhập chiều rộng"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                  <el-form-item label="Cao:">
                    <el-input
                      type="number"
                      class="topIput"
                      v-model="formValidate.thumb_mid_height"
                      placeholder="Vui lòng nhập chiều cao"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                </div>
              </div>
              <div class="topBox">
                <div class="topLeft">
                  <div class="img">
                    <img class="imgs" src="../../../assets/images/abbreviationSmall.png" alt="" />
                  </div>
                  <div>Ảnh thu nhỏ cỡ nhỏ</div>
                </div>
                <div class="topRight">
                  <el-form-item label="Rộng:">
                    <el-input
                      class="topIput"
                      type="number"
                      v-model="formValidate.thumb_small_width"
                      placeholder="Vui lòng nhập chiều rộng"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                  <el-form-item label="Cao:">
                    <el-input
                      class="topIput"
                      type="number"
                      v-model="formValidate.thumb_small_height"
                      placeholder="Vui lòng nhập chiều cao"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                </div>
              </div>
            </div>
            <el-divider />
            <div class="content mt20">
              <el-form-item label="Bật watermark:" label-width="110px">
                <el-switch
                  :active-value="1"
                  :inactive-value="0"
                  v-model="formValidate.image_watermark_status"
                  size="large"
                >
                  <span slot="open">Bật</span>
                  <span slot="close">Tắt</span>
                </el-switch>
              </el-form-item>
              <div v-if="formValidate.image_watermark_status == 1">
                <el-form-item label="Loại:" label-width="110px">
                  <el-radio-group v-model="formValidate.watermark_type">
                    <el-radio :label="1">Hình ảnh</el-radio>
                    <el-radio :label="2">Văn bản</el-radio>
                  </el-radio-group>
                </el-form-item>
                <div v-if="formValidate.watermark_type == 1">
                  <div class="flex">
                    <el-form-item class="contentIput" label="Độ trong suốt:" prop="name" label-width="110px">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_opacity"
                        placeholder="Vui lòng nhập độ trong suốt của watermark"
                      >
                      </el-input>
                    </el-form-item>
                    <el-form-item class="contentIput" label="Độ nghiêng:" prop="mail" label-width="110px">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_rotate"
                        placeholder="Vui lòng nhập độ nghiêng của watermark"
                      >
                      </el-input>
                    </el-form-item>
                  </div>
                  <div class="flex">
                    <el-form-item class="contentIput" label="Hình ảnh:" prop="name" label-width="110px">
                      <div class="picBox" v-db-click @click="modalPicTap('Chọn một')">
                        <div class="pictrue" v-if="formValidate.watermark_image">
                          <img :src="formValidate.watermark_image" />
                        </div>
                        <div class="upLoad acea-row row-center-wrapper" v-else>
                          <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                        </div>
                      </div>
                    </el-form-item>
                    <el-form-item class="contentIput" label="Vị trí:" prop="mail" label-width="110px">
                      <div class="conents">
                        <div class="positionBox">
                          <div
                            class="topIput box"
                            :class="positionId == item.id ? 'on' : ''"
                            v-for="(item, index) in boxs"
                            :key="index"
                            v-db-click
                            @click="bindbox(item)"
                          ></div>
                        </div>
                        <div class="title">{{ positiontlt }}</div>
                      </div>
                    </el-form-item>
                  </div>
                  <div class="flex">
                    <el-form-item class="contentIput" label="Độ lệch trục X:" label-width="110px" prop="name">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_x"
                        placeholder="Vui lòng nhập độ lệch trục X của watermark"
                        style="width: 240px"
                      >
                        <span slot="append">px</span>
                      </el-input>
                    </el-form-item>
                    <el-form-item class="contentIput" label="Độ lệch trục Y:" label-width="110px" prop="mail">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_y"
                        placeholder="Vui lòng nhập độ lệch trục Y của watermark"
                        style="width: 240px"
                      >
                        <span slot="append">px</span>
                      </el-input>
                    </el-form-item>
                  </div>
                </div>
                <!-- Loại hình mờ là chữ -->
                <div v-else>
                  <div class="flex">
                    <el-form-item class="contentIput" label="Văn bản:" label-width="110px" prop="name">
                      <el-input class="topIput" v-model="formValidate.watermark_text" placeholder="Vui lòng nhập văn bản watermark">
                      </el-input>
                    </el-form-item>
                    <el-form-item class="contentIput" label="Cỡ chữ:" label-width="110px">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_text_size"
                        placeholder="Vui lòng nhập cỡ chữ watermark"
                      >
                      </el-input>
                    </el-form-item>
                  </div>
                  <div class="flex">
                    <el-form-item class="contentIput" label="Màu chữ:" prop="name" label-width="110px">
                      <el-color-picker v-model="formValidate.watermark_text_color"></el-color-picker>
                    </el-form-item>
                    <el-form-item class="contentIput" label="Vị trí:" prop="mail" label-width="110px">
                      <div class="conents">
                        <div class="positionBox">
                          <div
                            class="topIput box"
                            :class="positionId == item.id ? 'on' : ''"
                            v-for="(item, index) in boxs"
                            :key="index"
                            v-db-click
                            @click="bindbox(item)"
                          ></div>
                        </div>
                        <div class="title">{{ positiontlt }}</div>
                      </div>
                    </el-form-item>
                  </div>
                  <div class="flex">
                    <el-form-item class="contentIput" label="Góc xoay chữ:" label-width="110px">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_text_angle"
                        placeholder="Vui lòng nhập góc xoay chữ của watermark"
                      >
                      </el-input>
                    </el-form-item>
                    <el-form-item class="contentIput" label="Độ lệch trục X:" label-width="110px">
                      <el-input
                        class="topIput"
                        type="number"
                        v-model="formValidate.watermark_x"
                        placeholder="Vui lòng nhập độ lệch trục X của watermark"
                      >
                        <span slot="append">px</span>
                      </el-input>
                    </el-form-item>
                  </div>
                  <el-form-item class="contentIput" label="Độ lệch trục Y:" prop="mail" label-width="110px">
                    <el-input
                      class="topIput"
                      type="number"
                      v-model="formValidate.watermark_y"
                      placeholder="Vui lòng nhập độ lệch trục Y của watermark"
                    >
                      <span slot="append">px</span>
                    </el-input>
                  </el-form-item>
                </div>
              </div>
            </div>
            <el-form-item>
              <el-button type="primary" v-db-click @click="handleSubmit('formValidate')">Lưu</el-button>
            </el-form-item>
          </div>
        </el-form>
      </el-card>
    </div>
    <!-- Cấu hình ảnh thu nhỏ -->
    <div class="pt10" v-else-if="currentTab == 10"></div>
    <div class="pt10" v-else>
      <el-card :bordered="false" shadow="never" class="ivu-mt">
        <el-row class="mb20">
          <el-col :span="24">
            <el-button type="primary" v-db-click @click="addStorageBtn">Thêm không gian lưu trữ</el-button>
            <el-button type="success" v-db-click @click="synchro" style="margin-left: 20px">Đồng bộ không gian lưu trữ</el-button>
            <el-button v-db-click @click="addConfigBtn" style="float: right">Sửa thông tin cấu hình</el-button>
          </el-col>
        </el-row>
        <el-table
          :data="levelLists"
          ref="table"
          class="mt14"
          v-loading="loading"
          highlight-current-row
          no-userFrom-text="Chưa có dữ liệu"
          no-filtered-userFrom-text="Không có kết quả phù hợp"
        >
          <el-table-column label="Tên không gian lưu trữ" min-width="120">
            <template slot-scope="scope">
              <span>{{ scope.row.name }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Khu vực" min-width="90">
            <template slot-scope="scope">
              <span>{{ scope.row._region }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Tên miền không gian lưu trữ" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.domain }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Trạng thái sử dụng" min-width="90">
            <template slot-scope="scope">
              <el-switch
                class="defineSwitch"
                :active-value="1"
                :inactive-value="0"
                v-model="scope.row.status"
                :value="scope.row.status"
                @change="changeSwitch(scope.row, index)"
                size="large"
                active-text="Bật"
                inactive-text="Tắt"
              >
              </el-switch>
            </template>
          </el-table-column>
          <el-table-column label="Thời gian tạo" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row._add_time }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thời gian cập nhật" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row._update_time }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thao tác" fixed="right" width="220">
            <template slot-scope="scope">
              <template v-if="scope.row.domain && scope.row.domain != scope.row.cname">
                <span class="btn" v-db-click @click="config(scope.row)">Cấu hình CNAME</span>
                <el-divider direction="vertical"></el-divider>
              </template>
              <span class="btn" v-db-click @click="edit(scope.row)">Sửa tên miền không gian lưu trữ</span>
              <el-divider direction="vertical"></el-divider>
              <span class="btn" v-db-click @click="del(scope.row, 'Xóa dữ liệu này', scope.$index)">Xóa</span>
            </template>
          </el-table-column>
        </el-table>
        <div class="acea-row row-right page">
          <pagination
            v-if="total"
            :total="total"
            :page.sync="list.page"
            :limit.sync="list.limit"
            @pagination="getlist"
          />
        </div>
      </el-card>
    </div>
    <el-dialog :visible.sync="configuModal" title="Cấu hình CNAME" width="570px">
      <div>
        <div class="confignv"><span class="configtit">Bản ghi host:</span>{{ configData.domain }}</div>
        <div class="confignv"><span class="configtit">Loại bản ghi:</span>CNAME</div>
        <div class="confignv">
          <span class="configtit">Giá trị bản ghi:</span>{{ configData.cname }}
          <span class="copy copy-data" v-db-click @click="insertCopy(configData.cname)">Sao chép</span>
        </div>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="modalPic" width="950px" title="Tải lên ảnh sản phẩm" :close-on-click-modal="false">
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </el-dialog>
  </div>
</template>

<script>
import ClipboardJS from 'clipboard';
import uploadPictures from '@/components/uploadPictures';

import {
  storageConfigApi,
  addConfigApi,
  addStorageApi,
  storageListApi,
  storageSynchApi,
  storageSwitchApi,
  storageStatusApi,
  editStorageApi,
  positionInfoApi,
  positionPostApi,
  saveType,
} from '@/api/setting';
export default {
  components: { uploadPictures },
  data() {
    return {
      modalPic: false,
      saveType: 0,
      isChoice: 'Chọn một',
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      positionId: 1,
      positiontlt: '',
      formValidate: {
        image_thumb_status: 0,
        thumb_big_height: '',
        thumb_big_width: '',
        thumb_mid_width: '',
        thumb_mid_height: '',
        thumb_small_height: '',
        thumb_small_width: '',
        image_watermark_status: 0,
        watermark_type: 1,
        watermark_opacity: '',
        watermark_rotate: '',
        watermark_position: 1,
      },
      boxs: [
        { content: 'Trên trái', id: 1 },
        { content: 'Lên', id: 2 },
        { content: 'Trên phải', id: 3 },
        { content: 'Giữa trái', id: 4 },
        { content: 'Chính giữa', id: 5 },
        { content: 'Giữa phải', id: 6 },
        { content: 'Dưới trái', id: 7 },
        { content: 'Xuống', id: 8 },
        { content: 'Dưới phải', id: 9 },
      ],
      ruleValidate: {},
      configuModal: false,
      configData: '',
      headerList: [
        { label: 'Cấu hình lưu trữ', value: '1' },
        { label: 'Lưu trữ Qiniu Cloud', value: '2' },
        { label: 'Lưu trữ Alibaba Cloud', value: '3' },
        { label: 'Lưu trữ Tencent Cloud', value: '4' },
        { label: 'Lưu trữ JD Cloud', value: '5' },
        { label: 'Lưu trữ Huawei Cloud', value: '6' },
        { label: 'Lưu trữ Tianyi Cloud', value: '7' },
        // { label: "Cấu hình ảnh thu nhỏ", value: "10" },
      ],

      total: 0,
      list: {
        page: 1,
        limit: 15,
        type: '1',
      },
      levelLists: [],
      currentTab: '1',
      loading: false,
      addData: {
        input: '',
        select: '',
        jurisdiction: '1',
        type: '1',
      },
      confData: {
        AccessKeyId: '',
        AccessKeySecret: '',
      },
      localStorage: false,
    };
  },
  created() {
    storageConfigApi().then((res) => {
      if (res.data.type == 1) {
        this.localStorage = true;
      }
      this.formValidate.upload_type = res.data.type;
      this.currentTab = res.data.type.toString();
      this.changeTab();
    });
  },
  methods: {
    insertCopy(text) {
      this.$copyText(text)
        .then((message) => {
          this.$message.success('Sao chép thành công');
        })
        .catch((err) => {
          this.$message.error('Sao chép thất bại');
        });
    },
    changeSave(type) {
      saveType(type)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    bindbox(item) {
      this.positionId = item.id;
      this.positiontlt = item.content;
      this.formValidate.watermark_position = item.id;
    },
    handleSubmit(name) {
      if (this.formValidate.image_watermark_status) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.postMessage(this.formValidate);
          } else {
            this.$message.error('Fail!');
          }
        });
      } else {
        this.postMessage(this.formValidate);
      }
    },
    //API lưu
    postMessage(data) {
      positionPostApi(data)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Chọn ảnh
    modalPicTap() {
      this.modalPic = true;
    },
    // Ảnh đã chọn
    getPic(pc) {
      this.formValidate.watermark_image = pc.att_dir;
      this.modalPic = false;
    },
    config(row) {
      this.configuModal = true;
      this.configData = row;
    },
    //Đồng bộ dung lượng lưu trữ
    synchro() {
      storageSynchApi(this.currentTab)
        .then((res) => {
          this.$message.success(res.msg);
          this.getlist();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Thêm không gian lưu trữ
    addStorageBtn() {
      this.$modalForm(addStorageApi(this.currentTab)).then(() => {
        this.getlist();
      });
    },
    // Sửa thông tin cấu hình
    addConfigBtn() {
      this.$modalForm(addConfigApi(this.currentTab)).then(() => {
        this.getlist();
      });
    },
    //Sửa tên miền không gian lưu trữ
    edit(row) {
      this.$modalForm(editStorageApi(row.id)).then(() => {
        this.getlist();
      });
    },
    changeSwitch(row, item) {
      return new Promise((resolve) => {
        this.$msgbox({
          title: 'Chuyển trạng thái',
          message: 'Bạn có chắc chắn muốn chuyển trạng thái sử dụng không',
          showCancelButton: true,
          cancelButtonText: 'Hủy',
          confirmButtonText: 'Xác nhận',
          iconClass: 'el-icon-warning',
          confirmButtonClass: 'btn-custom-cancel',
        })
          .then(() => {
            storageStatusApi(row.id)
              .then((res) => {
                this.$message.success(res.msg);
                this.getlist();
              })
              .catch((err) => {
                this.$message.error(err.msg);
              });
          })
          .catch(() => {});
      });
    },
    getlist() {
      this.loading = true;
      storageListApi(this.list).then((res) => {
        this.total = res.data.count;
        this.levelLists = res.data.list;
        this.loading = false;
      });
    },
    changeTab() {
      this.list.type = this.currentTab;
      this.list.page = 1;
      if (this.currentTab == 1) {
        this.getposition();
      } else {
        this.getlist();
      }
    },
    getposition() {
      let that = this;
      positionInfoApi().then((res) => {
        this.formValidate = res.data;
        this.positionId = res.data.watermark_position;
        for (var i = 0; i < this.boxs.length; i++) {
          if (this.boxs[i].id == res.data.watermark_position) {
            that.bindbox(this.boxs[i]);
          }
        }
      });
    },
    addSwitch(e) {
      if (e) {
        this.localStorage = 1;
      }
      storageSwitchApi({ type: this.localStorage })
        .then((res) => {
          this.$message.success(res.msg);
          this.getlist();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `system/config/storage/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getlist();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>
<style scoped lang="scss">
::v-deep .el-tabs__item {
  height: 54px !important;
  line-height: 54px !important;
}
.ivu-input-group > .ivu-input:last-child,
::v-deep .ivu-input-group-append {
  background: none;
  color: #999999;
}
::v-deep .ivu-input-group .ivu-input {
  border-right: 0px !important;
}
.content ::v-deep .ivu-form .ivu-form-item-label {
  width: 133px;
}
.topIput {
  width: 186px;
  background: #ffffff;
  border-right: 0px !important;
}
.abbreviation {
  .top {
    display: flex;
    justify-content: flex-start;
    .topBox {
      display: flex;
      .topRight {
        width: 254px;
        margin-left: 36px;
      }
      .topLeft {
        width: 94px;
        height: 94px;

        text-align: center;
        font-size: 13px;
        font-weight: 400;
        color: #000000;
        .img {
          // width: 84px;
          height: 67px;
          background: #f7fbff;
          border-radius: 4px;
          margin-bottom: 9px;
          .imgs {
            width: 70px;
            height: 51px;
            display: inline-block;
            text-align: center;
            margin-top: 8px;
          }
        }
      }
    }
  }
  .content {
    ::v-deep .ivu-form-item-label {
      width: 96px;
    }
    .flex {
      display: flex;
      justify-content: flex-start;
      // width: 400px;

      .contentIput {
        width: 400px;
      }
      .conents {
        display: flex;
        .title {
          width: 30px;
          margin-top: 70px;
          margin-left: 6px;
        }
        .positionBox {
          display: flex;
          flex-wrap: wrap;
          width: 101px;
          height: 99px;
          border-right: 1px solid #dddddd;
          .box {
            width: 33px;
            height: 33px;
            // border-radius: 4px 0px 0px 0px;
            border: 1px solid #dddddd;
            cursor: pointer;
          }
          .on {
            background: rgba(24, 144, 255, 0.1);
          }
        }
      }
    }
  }
}
</style>
<style scoped>
.message ::v-deep .ivu-table-header thead tr th {
  padding: 8px 16px;
}
.ivu-radio-wrapper {
  margin-right: 15px;
  font-size: 12px !important;
}
.message ::v-deep .ivu-tabs-tab {
  border-radius: 0 !important;
}
.table-box {
  padding: 20px;
}
.is-table {
  display: flex;
  /* justify-content: space-around; */
  justify-content: center;
}
.btn {
  cursor: pointer;
  color: #2d8cf0;
  font-size: 12px;
}
.is-switch-close {
  background-color: #504444;
}
.is-switch {
  background-color: #eb5252;
}
.notice-list {
  background-color: #308cf5;
  margin: 0 15px;
}
.table {
  padding: 0 18px;
}
.confignv {
  margin: 10px 0px;
}
.configtit {
  display: inline-block;
  width: 90px;
  text-align: right;
}
.copy {
  padding: 3px 5px;
  border: 1px solid #cccccc;
  border-radius: 5px;
  color: #333;
  cursor: pointer;
  margin-left: 5px;
}
.copy:hover {
  border-color: #2d8cf0;
  color: #2d8cf0;
}
.picBox {
  display: inline-block;
  cursor: pointer;
}
.picBox .pictrue {
  width: 60px;
  height: 60px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 10px;
}

.picBox .pictrue img {
  width: 100%;
  height: 100%;
}
.picBox .upLoad {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
}
h3 {
  margin: 5px 0 15px 0;
}
.table-box p {
  margin-bottom: 10px;
}
.save-type {
  font-size: 12px;
}
</style>
