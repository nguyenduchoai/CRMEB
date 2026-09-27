<template>
  <div class="cross-version-upgrade">
    <!-- Tabs chức năng -->
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-tabs v-model="currentTab" @tab-click="handleTabClick">
        <el-tab-pane label="Nâng cấp hệ thống" name="upgrade"></el-tab-pane>
        <el-tab-pane label="Lịch sử nâng cấp" name="logs"></el-tab-pane>
      </el-tabs>

      <!-- Tab nâng cấp hệ thống -->
      <div v-if="currentTab === 'upgrade'">
        <div v-if="showUpgradeComplete" class="new-version-summary">
          <div class="summary-header">
            <span class="summary-icon-wrap">
              <img src="@/assets/images/new.png" class="summary-icon-img" alt="upgrade" />
            </span>
            <div class="summary-title">Nâng cấp phiên bản hoàn tất</div>
            <div class="summary-sub">
              Vui lòng tải mã Mini Program lên máy chủ WeChat, sau khi hoàn tất cần phát hành trong trang quản trị Mini Program,
              <a href="https://mp.weixin.qq.com" target="_blank" style="color: #409eff">Nhấn để phát hành</a>
            </div>
            <div class="summary-actions">
              <el-button type="primary" @click="handleUploadMini" :loading="uploadingMini">Tải lên Mini Program</el-button>
              <el-button class="ml8" @click="handleRefreshStatus">Tạm thời chưa tải lên</el-button>
            </div>
          </div>
        </div>
        <!-- Tóm tắt phát hiện phiên bản mới (status = 1) -->
        <div
          v-if="remoteStatus.status === 1 && downloadStage === 'idle' && !showUpgradeComplete"
          class="new-version-summary"
        >
          <div class="summary-header">
            <span class="summary-icon-wrap">
              <img src="@/assets/images/upgrade.png" class="summary-icon-img" alt="upgrade" />
            </span>
            <div class="summary-title">
              Có phiên bản mới
              <span v-if="upgradeablePackage">
                v{{ upgradeablePackage.first_version }}.{{ upgradeablePackage.second_version }}.{{
                  upgradeablePackage.third_version
                }}
              </span>
            </div>
            <div class="summary-sub">Phát hiện phiên bản mới có thể nâng cấp, nhấn để nâng cấp ngay</div>
            <div class="summary-actions">
              <el-button type="primary" @click="startDownload" :disabled="startingDownload" :loading="startingDownload"
                >Nâng cấp ngay</el-button
              >
              <el-button class="ml8 check-update-btn" @click="checkRemoteUpdate" :loading="checkingRemote"
                >Kiểm tra cập nhật</el-button
              >
            </div>
          </div>
          <div v-if="upgradeablePackage" class="summary-content">
            <div class="content-title">
              Nội dung cập nhật V{{ upgradeablePackage.first_version }}.{{ upgradeablePackage.second_version }}.{{
                upgradeablePackage.third_version
              }}
              <span class="release-time" v-if="upgradeablePackage.release_time"
                >Ngày cập nhật: {{ upgradeablePackage.release_time }}</span
              >
            </div>
            <div class="content-desc" v-html="upgradeablePackage.content"></div>
          </div>
        </div>

        <div
          v-if="(remoteStatus.status === 0 || remoteStatus.status === -1) && downloadStage === 'idle'"
          class="new-version-summary"
        >
          <div class="summary-header">
            <span class="summary-icon-wrap">
              <img src="@/assets/images/new.png" class="summary-icon-img" alt="upgrade" />
            </span>
            <div class="summary-title">Phiên bản hiện tại {{ versionInfo.current_version_main || '-' }}</div>
            <div class="summary-sub">Số phiên bản hiện tại là {{ versionInfo.current_version || '-' }}</div>
            <div class="summary-actions">
              <el-button class="check-update-btn" @click="checkRemoteUpdate" :loading="checkingRemote"
                >Kiểm tra cập nhật</el-button
              >
            </div>
          </div>
        </div>

        <!-- Hiển thị tiến trình nâng cấp -->
        <div v-if="downloadStage !== 'idle' && !this.showUpgradeComplete" class="upgrade-section">
          <div class="section-title">
            Tiến trình nâng cấp
            <el-button type="text" class="ml8" @click="handleRefreshStatus">Làm mới trạng thái</el-button>
          </div>
          <!-- Thanh tiến trình trên cùng (màu xanh dương, nét dày) -->
          <el-progress :percentage="downloadProgress" :show-text="false" :stroke-width="10" class="progress-line" />
          <!-- Hàng trạng thái theo từng giai đoạn -->
          <div class="progress-status-row">
            <div class="status-item">
              <i
                :class="
                  downloadSteps[0].status === 'loading'
                    ? 'el-icon-loading status-loading'
                    : downloadSteps[0].status === 'success'
                    ? 'el-icon-circle-check status-ok'
                    : 'el-icon-time status-muted'
                "
              ></i>
              <span
                :class="
                  downloadSteps[0].status === 'loading'
                    ? 'status-loading'
                    : downloadSteps[0].status === 'success'
                    ? 'status-ok'
                    : 'status-muted'
                "
                >Kiểm tra</span
              >
            </div>
            <div class="status-item">
              <i
                :class="
                  downloadSteps[1].status === 'loading'
                    ? 'el-icon-loading status-loading'
                    : downloadSteps[1].status === 'success'
                    ? 'el-icon-circle-check status-ok'
                    : 'el-icon-time status-muted'
                "
              ></i>
              <span
                :class="
                  downloadSteps[1].status === 'loading'
                    ? 'status-loading'
                    : downloadSteps[1].status === 'success'
                    ? 'status-ok'
                    : 'status-muted'
                "
                >Sao lưu</span
              >
            </div>
            <div class="status-item">
              <i
                :class="
                  downloadSteps[2].status === 'loading'
                    ? 'el-icon-loading status-loading'
                    : downloadSteps[2].status === 'success'
                    ? 'el-icon-circle-check status-ok'
                    : 'el-icon-time status-muted'
                "
              ></i>
              <span
                :class="
                  downloadSteps[2].status === 'loading'
                    ? 'status-loading'
                    : downloadSteps[2].status === 'success'
                    ? 'status-ok'
                    : 'status-muted'
                "
                >Tải xuống</span
              >
            </div>
            <div class="status-item">
              <i
                :class="
                  downloadSteps[3].status === 'loading'
                    ? 'el-icon-loading status-loading'
                    : downloadSteps[3].status === 'success'
                    ? 'el-icon-circle-check status-ok'
                    : 'el-icon-time status-muted'
                "
              ></i>
              <span
                :class="
                  downloadSteps[3].status === 'loading'
                    ? 'status-loading'
                    : downloadSteps[3].status === 'success'
                    ? 'status-ok'
                    : 'status-muted'
                "
                >{{ downloadType === 4 ? 'Hoàn thành' : 'Đang cập nhật' }}</span
              >
            </div>
            <!-- <div class="status-item">
              <span
                :class="downloadType === 4 ? 'status-ok' : downloadStage === 'error' ? 'status-error' : 'status-muted'"
                >{{ downloadType === 4 ? 'Hoàn thành' : 'Đang cập nhật' }}</span
              >
            </div> -->
          </div>
          <div class="mt10 download-msg">{{ downloadMessage }}</div>
        </div>
        <!-- Thông báo kiểm tra tệp thất bại -->
        <div class="upgrade-section" v-if="downloadType >= 0 && downloadSteps[0].status !== 'loading' && !this.showUpgradeComplete">
          <div class="section-title">
            <span class="step-num">1</span>
            <span>Kiểm tra</span>
          </div>
          <template v-if="downloadSteps[0].status === 'error'">
            <div class="check-error-box mt16">
              <div class="check-error-desc">
                {{ downloadSteps[0].message || 'Chức năng hệ thống của bạn đã bị chỉnh sửa, dẫn đến nâng cấp thất bại' }}
              </div>
            </div>
            <!-- Danh sách tệp thay đổi -->
            <div v-if="normalizedCheckErrorFiles.length && downloadType === 0" class="mt16">
              <div class="error-files-table-wrap">
                <el-table :data="normalizedCheckErrorFiles" size="small" class="error-files-table" :max-height="325">
                  <el-table-column type="index" label="STT" width="120"></el-table-column>
                  <el-table-column prop="path" label="Đường dẫn tệp" min-width="360" show-overflow-tooltip></el-table-column>
                </el-table>
              </div>
            </div>
            <div class="check-error-actions mt10" v-if="downloadType === 0">
              <el-button size="small" type="primary" @click="cancelUpgrade">Hủy nâng cấp</el-button>
              <el-button size="small" class="ml8" @click="ignoreAndProceed(0)">Bỏ qua và tiếp tục</el-button>
            </div>
          </template>
          <div v-else class="check-success-box mt16">{{ downloadSteps[0].message || 'Kiểm tra hoàn tất' }}</div>
        </div>
        <!-- Bước 2: sao lưu -->
        <div class="upgrade-section" v-if="downloadType >= 1 && downloadSteps[1].status !== 'loading' && !this.showUpgradeComplete">
          <div class="section-title">
            <span class="step-num">2</span>
            <span>Sao lưu</span>
          </div>
          <div v-if="downloadSteps[1].status === 'success'" class="check-success-box mt16">
            {{ downloadSteps[1].message || 'Sao lưu cơ sở dữ liệu hoàn tất' }}
          </div>
          <template v-else>
            <div class="check-error-box mt16">
              <div class="check-error-desc">
                {{ downloadSteps[1].message || 'Sao lưu cơ sở dữ liệu thất bại' }}
              </div>
            </div>
            <div class="check-error-actions mt10">
              <el-button size="small" type="primary" @click="cancelUpgrade">Hủy nâng cấp</el-button>
              <el-button size="small" class="ml8" @click="ignoreAndProceed(1)">Bỏ qua và tiếp tục</el-button>
              <el-button size="small" class="ml8" @click="reExecuteUpgrade">Sao lưu lại</el-button>
            </div>
          </template>
        </div>
        <!-- Bước 3: thực thi nâng cấp cơ sở dữ liệu -->
        <div class="upgrade-section" v-if="downloadType >= 2 && downloadSteps[2].status !== 'loading' && !this.showUpgradeComplete">
          <div class="section-title">
            <span class="step-num">3</span>
            <span>Tải bản cập nhật</span>
          </div>
          <div class="check-success-box mt16">{{ downloadSteps[2].message || 'Đã tải xong tệp cập nhật' }}</div>
        </div>
        <div
          class="upgrade-section"
          v-if="sqlExecutionLogs.length > 0 && downloadType >= 3 && downloadSteps[3].status !== 'loading' && !this.showUpgradeComplete"
        >
          <!-- Chi tiết tiến trình nâng cấp -->
          <template>
            <div class="section-title">
              <span class="step-num">4</span>
              <span>Thực hiện nâng cấp cơ sở dữ liệu</span>
            </div>
            <div class="upgrade-progress-detail">
              <!-- Chi tiết thực thi SQL -->
              <div class="sql-execution-logs">
                <div class="logs-header">
                  <span>Chi tiết thực thi SQL</span>
                  <el-tag size="mini" type="success">Thành công: {{ sqlSuccessCount }}</el-tag>
                  <el-tag size="mini" type="danger" v-if="sqlFailedCount > 0">Thất bại: {{ sqlFailedCount }}</el-tag>
                  <el-tag size="mini" type="info" v-if="sqlSkippedCount > 0">Bỏ qua: {{ sqlSkippedCount }}</el-tag>
                </div>
                <div class="logs-content">
                  <div
                    v-for="(log, index) in sqlExecutionLogs"
                    :key="index"
                    class="log-item"
                    :class="{
                      'log-success': log.status === 'success',
                      'log-failed': log.status === 'failed',
                      'log-skipped': log.status === 'skipped',
                    }"
                  >
                    <span class="log-index">{{ index + 1 }}.</span>
                    <span class="log-version">[{{ log.version }}]</span>
                    <span class="log-type">[{{ getTypeName(log.type) }}]</span>
                    <span class="log-table">{{ log.table || '-' }}</span>
                    <span v-if="log.field" class="log-field">{{ log.field }}</span>
                    <el-tag
                      size="mini"
                      :type="log.status === 'success' ? 'success' : log.status === 'failed' ? 'danger' : 'info'"
                    >
                      {{ log.status === 'success' ? 'Thành công' : log.status === 'failed' ? 'Thất bại' : 'Bỏ qua' }}
                    </el-tag>
                    <span v-if="log.message" class="log-message">{{ log.message }}</span>
                  </div>
                </div>
              </div>

              <!-- Kết quả nâng cấp -->
              <div class="upgrade-result">
                <el-alert
                  :title="sqlFailedCount === 0 ? 'Nâng cấp thành công' : 'Nâng cấp hoàn tất (có mục thất bại)'"
                  :type="sqlFailedCount === 0 ? 'success' : 'warning'"
                  :closable="false"
                  show-icon
                >
                  <template slot="default">
                    <span>Thực thi thành công: {{ sqlSuccessCount }} câu lệnh;</span>
                    <span v-if="sqlFailedCount > 0"> Thực thi thất bại: {{ sqlFailedCount }} câu lệnh;</span>
                    <span v-if="sqlSkippedCount > 0"> Bỏ qua: {{ sqlSkippedCount }} câu lệnh;</span>
                  </template>
                </el-alert>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Tab lịch sử nâng cấp -->
      <div v-if="currentTab === 'logs'" class="upgrade-logs">
        <el-table :data="upgradeLogList" style="width: 100%" v-loading="loadingLogs">
          <el-table-column prop="title" label="Tiêu đề nâng cấp" min-width="120" show-overflow-tooltip />
          <el-table-column label="Phiên bản" width="100">
            <template slot-scope="scope">
              v{{ scope.row.first_version }}.{{ scope.row.second_version }}.{{ scope.row.third_version }}
            </template>
          </el-table-column>
          <el-table-column prop="upgrade_time" label="Thời gian nâng cấp" width="200" />
          <el-table-column label="Trạng thái sao lưu" min-width="150">
            <template slot-scope="scope">
              <el-tag size="mini" :type="scope.row.file_status ? 'success' : 'danger'">
                Dự án: {{ scope.row.file_status ? scope.row.package_link : 'Không có' }}
              </el-tag><br/>
              <el-tag size="mini" :type="scope.row.data_status ? 'success' : 'danger'">
                Cơ sở dữ liệu: {{ scope.row.data_status ? scope.row.data_link : 'Không có' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="content" label="Nội dung cập nhật" min-width="200" show-overflow-tooltip>
            <template slot-scope="scope">
              <span v-html="scope.row.content"></span>
            </template>
          </el-table-column>
        </el-table>

        <!-- Phân trang -->
        <div class="pagination-wrap" v-if="logsTotal > 0">
          <el-pagination
            @current-change="handleLogsPageChange"
            :current-page="logsPage"
            :page-size="logsLimit"
            layout="total, prev, pager, next"
            :total="logsTotal"
          >
          </el-pagination>
        </div>
      </div>
    </el-card>

    <!-- Hộp thoại thỏa thuận nâng cấp -->
    <el-dialog
      :visible.sync="agreementVisible"
      title="Thỏa thuận nâng cấp hệ thống"
      width="800px"
      destroy-on-close
      :close-on-click-modal="false"
      :modal="false"
      custom-class="agreement-dialog"
    >
      <div class="agreement-content" v-loading="agreementLoading">
        <div v-html="agreementContent"></div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="agreementVisible = false">Không đồng ý</el-button>
        <el-button type="primary" @click="doStartDownload" :loading="startingDownload">Đồng ý và nâng cấp</el-button>
      </span>
    </el-dialog>
    <!-- Lớp phủ cục bộ của trang, chỉ hiển thị trên trang này -->
    <div v-if="agreementVisible" class="local-mask"></div>

    <!-- Hộp thoại tải lên thành công -->
    <el-dialog
      :visible.sync="uploadSuccessVisible"
      width="400px"
      :show-close="false"
      :close-on-click-modal="false"
      custom-class="upload-success-dialog"
    >
      <div class="upload-success-content">
        <i class="el-icon-circle-check" style="font-size: 48px; color: #67c23a; margin-bottom: 16px"></i>
        <div class="success-title">Tải lên thành công</div>
        <div class="success-desc">
          Đã tải mã Mini Program lên máy chủ WeChat,<a href="https://mp.weixin.qq.com" target="_blank" style="color: #409eff"
            >Nhấn để phát hành</a
          >
        </div>
        <el-button type="primary" @click="handleUploadSuccessClose" style="margin-top: 24px; width: 120px"
          >Tôi đã hiểu</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  upgradeStatusApi,
  upgradeableListApi,
  downloadApi,
  downloadProgressApi,
  checkCrossVersionUpgradeApi,
  upgradeLogListApi,
  upgradeAgreementApi,
  reExecuteUpgradeApi,
} from '@/api/system';
import { routineCIUpload } from '@/api/app';
import setting from '@/setting';

export default {
  name: 'CrossVersionUpgrade',
  data() {
    return {
      currentTab: 'upgrade',
      versionInfo: {},
      // Kiểm tra từ xa
      checkingRemote: false,
      remoteStatus: {
        status: -1, // -1 chưa kiểm tra, 0 không có bản cập nhật, 1 có bản cập nhật
        title: '',
        force_reminder: 0,
      },
      upgradeablePackage: null, // Thông tin gói có thể nâng cấp
      // Trạng thái tải xuống
      startingDownload: false,
      downloadStage: 'idle', // idle，loading，error，success, complete
      downloadType: -1, // 0: kiểm tra, 1: sao lưu, 2: tải xuống, 3: cập nhật
      downloadSteps: [
        { status: 'loading', progress: 0, message: '', errorFiles: [] }, // Kiểm tra
        { status: 'loading', progress: 0, message: '', errorFiles: [] }, // Sao lưu
        { status: 'loading', progress: 0, message: '', errorFiles: [] }, // Tải xuống
        { status: 'loading', progress: 0, message: '', errorFiles: [] }, // Nâng cấp
      ],
      downloadProgress: 0,
      downloadMessage: '',
      downloadTimer: null,
      checkErrorFiles: [],
      mpVersionData: null, // Dữ liệu phiên bản Mini Program
      // Kiểm tra phiên bản cục bộ
      localCheckResult: null,
      pendingSqlList: [],
      // Nâng cấp cơ sở dữ liệu
      upgrading: false,
      upgradeProgress: 0,
      upgradeProgressStatus: '',
      sqlExecutionLogs: [],
      sqlSuccessCount: 0,
      sqlFailedCount: 0,
      sqlSkippedCount: 0,
      // Lịch sử nâng cấp
      upgradeLogList: [],
      loadingLogs: false,
      logsPage: 1,
      logsLimit: 15,
      logsTotal: 0,
      // Thỏa thuận nâng cấp
      agreementVisible: false,
      agreementLoading: false,
      agreementContent: '',
      uploadSuccessVisible: false,
      showUpgradeComplete: false,
      uploadingMini: false,
    };
  },
  mounted() {
    this.initPage();
  },
  beforeDestroy() {
    this.clearDownloadTimer();
  },
  computed: {
    normalizedCheckErrorFiles() {
      const list = Array.isArray(this.checkErrorFiles) ? this.checkErrorFiles : [];
      return list.map((item) => {
        if (typeof item === 'string') {
          return { path: item, note: '' };
        } else if (item && typeof item === 'object') {
          return {
            path: item.path || item.full_path || item.filename || item.file || item.name || JSON.stringify(item),
            note: item.note || item.desc || '',
          };
        } else {
          return { path: String(item), note: '' };
        }
      });
    },
  },
  methods: {
    // Chuyển Tab
    handleTabClick() {
      if (this.currentTab === 'logs') {
        this.loadUpgradeLogs();
      }
    },

    // Khởi tạo trang
    async initPage() {
      // Đồng thời kiểm tra bản cập nhật từ xa và phiên bản cục bộ
      await Promise.all([this.checkLocalVersion()]);
    },

    // Kiểm tra bản cập nhật từ xa
    async checkRemoteUpdate() {
      this.checkingRemote = true;
      try {
        const res = await upgradeStatusApi();
        this.remoteStatus = {
          status: res.data.status || 0,
          title: res.data.title || '',
          force_reminder: res.data.force_reminder || 0,
        };

        // Nếu có bản cập nhật thì lấy danh sách gói có thể nâng cấp
        if (res.data.status === 1) {
          await this.loadUpgradeableList();
        }
      } catch (err) {
        this.$message.error(err.msg || 'Kiểm tra cập nhật từ xa thất bại');
      } finally {
        this.checkingRemote = false;
      }
    },

    // Lấy danh sách gói có thể nâng cấp
    async loadUpgradeableList() {
      try {
        const res = await upgradeableListApi();
        const list = res.data?.list || res.data || [];
        if (list.length > 0) {
          this.upgradeablePackage = list[0]; // Lấy mục đầu tiên
        }
      } catch (err) {
        console.error('Lấy danh sách gói nâng cấp thất bại', err);
      }
    },

    // Kiểm tra phiên bản cục bộ
    async checkLocalVersion() {
      try {
        const res = await checkCrossVersionUpgradeApi();
        this.localCheckResult = res.data;
        this.versionInfo = {
          current_version: res.data.current_version,
          current_version_main: res.data.current_version_main,
          current_code: res.data.current_code,
        };
      } catch (err) {
        console.error('Kiểm tra phiên bản cục bộ thất bại', err);
      }
    },

    // Bắt đầu tải gói cập nhật
    async startDownload() {
      // Mở hộp thoại thỏa thuận
      await this.openAgreement();
    },

    async openAgreement() {
      try {
        this.agreementLoading = true;
        const res = await upgradeAgreementApi();
        this.agreementContent = res.data?.content || res.data || '';
        this.agreementVisible = true;
      } catch (err) {
        this.$message.error(err.msg || 'Lấy thỏa thuận nâng cấp thất bại');
      } finally {
        this.agreementLoading = false;
      }
    },

    async doStartDownload() {
      if (!this.upgradeablePackage?.package_key) {
        this.$message.error('Không lấy được thông tin gói nâng cấp, vui lòng kiểm tra cập nhật lại');
        return;
      }

      // Đóng hộp thoại thỏa thuận trước, hiển thị nội dung trang
      this.agreementVisible = false;
      this.downloadStage = 'loading';

      this.startingDownload = true;
      try {
        await downloadApi(this.upgradeablePackage.package_key);
        this.downloadType = 0;
        this.startDownloadProgressPolling();
      } catch (err) {
        this.$message.error(err.msg || 'Bắt đầu tải xuống thất bại');
        this.downloadStage = 'error';
        this.downloadMessage = err.msg || 'Bắt đầu tải xuống thất bại';
      } finally {
        this.startingDownload = false;
      }
    },

    // Polling tiến trình tải xuống
    startDownloadProgressPolling() {
      this.downloadTimer = setInterval(async () => {
        try {
          const res = await downloadProgressApi({ type: this.downloadType });
          if (res.data) {
            const currentStage = res.data.stage || 'idle';
            const currentProgress = res.data.progress || 0;
            const currentMessage = res.data.message || '';
            const currentErrorFiles = res.data.data || [];

            this.downloadStage = currentStage;
            this.downloadProgress = currentProgress;
            this.downloadMessage = currentMessage;
            this.checkErrorFiles = currentErrorFiles;

            // Cập nhật trạng thái bước hiện tại
            if (this.downloadType >= 0 && this.downloadType < this.downloadSteps.length) {
              this.$set(this.downloadSteps, this.downloadType, {
                status: currentStage,
                progress: currentProgress,
                message: currentMessage,
                errorFiles: currentErrorFiles,
              });
            }
            if (res.data.stage === 'success' && this.downloadType === 3) {
              this.sqlExecutionLogs = res.data.data.sql_logs;
              this.sqlSuccessCount = res.data.data.executed || 0;
              this.sqlSkippedCount = res.data.data.skipped || 0;
              this.sqlFailedCount = res.data.data.failed || 0;
            }
            // Khi hoàn tất thì dừng polling và tự động làm mới trang
            if (res.data.stage === 'complete' && this.downloadType === 4) {
              this.mpVersionData = res.data.routine_upload_data;
              this.clearDownloadTimer();
              this.checkLocalVersion();
              this.showUpgradeComplete = true;
            }

            // Dừng polling khi thất bại
            if (['error'].includes(res.data.stage)) {
              this.clearDownloadTimer();
            }
            if (['success'].includes(res.data.stage) && this.downloadType !== 4) {
              this.clearDownloadTimer();
              // Đánh dấu bước hiện tại đã hoàn thành
              if (this.downloadType >= 0 && this.downloadType < this.downloadSteps.length) {
                this.$set(this.downloadSteps[this.downloadType], 'status', 'success');
              }
              this.downloadType++;
              this.startDownloadProgressPolling();
            }
          }
        } catch (err) {
          console.error('Lấy tiến trình tải xuống thất bại', err);
        }
      }, 2000);
    },

    clearDownloadTimer() {
      if (this.downloadTimer) {
        clearInterval(this.downloadTimer);
        this.downloadTimer = null;
      }
    },

    // Thử tải xuống lại
    retryDownload() {
      this.downloadStage = 'idle';
      this.downloadProgress = 0;
      this.downloadMessage = '';
    },

    // Làm mới trang
    refreshPage() {
      window.location.reload();
    },
    // Hủy nâng cấp, quay về trạng thái ban đầu
    cancelUpgrade() {
      this.clearDownloadTimer();
      this.downloadStage = 'idle';
      this.downloadProgress = 0;
      this.downloadMessage = '';
      this.checkErrorFiles = [];
      this.remoteStatus.status = -1;
    },
    // Bỏ qua và thực thi (thử tiếp tục)
    async ignoreAndProceed(type) {
      if (type === 0) {
        this.normalizedCheckErrorFiles = [];
        this.downloadSteps[0].message = 'Bỏ qua và tiếp tục!';
        this.downloadSteps[0].status = 'success';
        this.downloadMessage = 'Bỏ qua và tiếp tục...';
      }
      this.downloadType += 1;
      this.startDownloadProgressPolling();
    },
    // Thực hiện lại nâng cấp
    async reExecuteUpgrade() {
      try {
        await reExecuteUpgradeApi();
        this.startDownloadProgressPolling();
      } catch (err) {
        this.$message.error(err.msg || 'Thực hiện lại nâng cấp thất bại');
      }
    },
    // Kiểm tra giai đoạn đã hoàn thành chưa
    isStageComplete(stage) {
      const stageOrder = ['idle', 'error', 'loading', 'complete', 'success'];
      const currentIndex = stageOrder.indexOf(this.downloadStage);
      const checkIndex = stageOrder.indexOf(stage);
      return currentIndex > checkIndex;
    },

    // Lấy biểu tượng bước
    getStepIcon(stage) {
      if (this.isStageComplete(stage)) return 'el-icon-success';
      if (this.downloadStage === stage) return 'el-icon-loading';
      return 'el-icon-time';
    },
    // Làm mới trạng thái
    handleRefreshStatus() {
      location.reload();
    },

    // Tải lịch sử nâng cấp
    async loadUpgradeLogs() {
      this.loadingLogs = true;
      try {
        const res = await upgradeLogListApi({ page: this.logsPage, limit: this.logsLimit });
        this.upgradeLogList = res.data.list || [];
        this.logsTotal = res.data.count || 0;
      } catch (err) {
        this.$message.error(err.msg || 'Tải lịch sử nâng cấp thất bại');
      } finally {
        this.loadingLogs = false;
      }
    },

    handleLogsPageChange(page) {
      this.logsPage = page;
      this.loadUpgradeLogs();
    },

    // Tên loại SQL
    getTypeName(type) {
      const typeMap = {
        1: 'Tạo bảng',
        2: 'Xóa bảng',
        3: 'Thêm trường',
        4: 'Trường được sửa',
        5: 'Xóa trường',
        6: 'Thêm dữ liệu',
        7: 'Dữ liệu cần chỉnh sửa',
        8: 'Xóa dữ liệu',
        '-1': 'Thực thi SQL',
      };
      return typeMap[type] || 'Không xác định';
    },

    getTypeTagType(type) {
      const typeColorMap = {
        1: 'success',
        2: 'danger',
        3: 'primary',
        4: 'warning',
        5: 'danger',
        6: 'success',
        7: 'warning',
        8: 'danger',
        '-1': 'info',
      };
      return typeColorMap[type] || 'info';
    },

    // Tải lên Mini Program
    async handleUploadMini() {
      this.uploadingMini = true;
      try {
        await routineCIUpload({
          version: this.mpVersionData.version,
          desc: this.mpVersionData.desc,
          is_live: this.mpVersionData.is_live,
        });
        this.uploadSuccessVisible = true;
      } catch (err) {
        this.$message.error(err.msg || 'Tải lên Mini Program thất bại');
        this.$router.push({ path: `${setting.routePre}/app/routine/ci_upload` });
      } finally {
        this.uploadingMini = false;
      }
    },

    handleUploadSuccessClose() {
      this.uploadSuccessVisible = false;
      this.handleRefreshStatus();
    },
  },
};
</script>

<style lang="scss" scoped>
.upload-success-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 0;

  .success-title {
    font-size: 18px;
    font-weight: 500;
    color: #303133;
    margin-bottom: 8px;
  }

  .success-desc {
    font-size: 14px;
    color: #606266;
  }
}
.cross-version-upgrade {
  .new-version-summary {
    padding: 24px 0;
    background: #fff;
    border-radius: 8px;
    margin-top: 16px;
    margin-bottom: 16px;

    .summary-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0;
      margin-top: 12px;
      margin-bottom: 12px;
    }

    .summary-icon-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .summary-icon-img {
      width: 36px;
      height: 36px;
      display: block;
    }

    .summary-title {
      font-size: 16px;
      font-weight: 600;
      text-align: center;
      margin-top: 16px;
    }

    .summary-sub {
      color: #666;
      text-align: center;
      font-size: 12px;
      margin-top: 6px;
    }

    .summary-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 20px;
    }

    .check-update-btn {
      color: #0256ff;
      border: 1px solid #0256ff;
      background-color: transparent;
    }

    .check-update-btn:hover,
    .check-update-btn:focus {
      color: #fff;
      background-color: #0256ff;
      border-color: #0256ff;
    }

    .summary-content {
      background: #fafafa;
      border-radius: 6px;
      padding: 16px;
      margin-top: 48px;
    }

    .content-title {
      font-weight: 500;
      font-size: 14px;
      color: #333;
      padding-bottom: 8px;
      border-bottom: 1px solid #eaeaea;
      margin-bottom: 12px;
    }

    .release-time {
      color: #666;
      font-size: 12px;
      margin-left: 8px;
    }

    .content-desc {
      white-space: pre-wrap;
      font-size: 12px;
      line-height: 18px;
      color: #333;
    }
  }

  .agreement-content {
    max-height: 60vh;
    overflow: auto;
    font-size: 12px;
    font-weight: 400;
    line-height: 20px;
    color: #333333;
  }

  :deep(.agreement-dialog) {
    .el-dialog__header {
      text-align: center;
    }

    .el-dialog__title {
      font-size: 16px !important;
      line-height: 16px !important;
      font-weight: 500;
      color: #333333;
    }
  }

  .local-mask {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 1998;
  }

  .progress-line :deep(.el-progress-bar__inner) {
    background-color: var(--prev-color-primary);
  }

  .progress-status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 12px;
  }

  .status-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .status-ok {
    color: var(--prev-color-primary);
  }

  .status-loading {
    color: #ff8a00;
  }

  .status-error {
    color: #ff4d4f;
  }

  .status-muted {
    color: #999;
  }

  .check-error-box {
    border: 1px solid #ffd591;
    background: #fff7e6;
    border-radius: 6px;
    padding: 12px;
    color: #ad6800;
  }

  .check-error-head {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
  }

  .check-error-desc {
    font-size: 12px;
    font-weight: 400;
  }

  .check-error-actions {
    display: flex;
    align-items: center;
  }
  .check-success-box {
    border: 1px solid rgba(2, 86, 255, 1);
    background: #e8fdf5;
    border-radius: 6px;
    padding: 12px;
    background: #f0f5ff;
    color: rgba(51, 51, 51, 1);
  }
  .mt16 {
    margin-top: 16px;
  }

  align-items: center;

  .version-info {
    display: flex;
    align-items: center;
    gap: 30px;

    .current-version {
      display: flex;
      align-items: center;
      font-size: 16px;

      .label {
        color: #666;
      }

      .version-num {
        font-weight: bold;
        color: var(--prev-color-primary);
      }
    }
  }
}

.upgrade-section {
  margin-bottom: 16px;
  padding: 16px;
  background: #f9fafc;

  .section-title {
    display: flex;
    align-items: center;
    margin-bottom: 16px;
    font-size: 16px;
    font-weight: 500;

    .step-num {
      width: 24px;
      height: 24px;
      background: var(--prev-color-primary);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      margin-right: 10px;
    }
  }

  .section-content {
    .status-text {
      margin-left: 15px;
      color: #666;
    }
  }
}

.download-progress {
  .progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;

    .stage-text {
      color: #409eff;
      font-weight: 500;
    }

    .progress-text {
      color: #999;
    }
  }

  .progress-steps {
    display: flex;
    justify-content: space-between;
    margin-top: 16px;

    .step-item {
      display: flex;
      align-items: center;
      gap: 6px;
      color: #999;
      font-size: 13px;

      i {
        font-size: 16px;
      }

      &.active {
        color: #409eff;

        i {
          color: #409eff;
        }
      }

      &.done {
        color: #67c23a;

        i {
          color: #67c23a;
        }
      }
    }
  }
}

.download-complete,
.download-error {
  margin-top: 10px;
}

.upgrade-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  font-weight: 500;
}

.sql-list {
  margin-bottom: 20px;
}

.upgrade-progress-detail {
  

  .progress-header {
    margin-bottom: 20px;
  }

  .sql-execution-logs {
    margin-top: 20px;
    background: #f5f7fa;
    border-radius: 4px;
    padding: 12px;
    max-height: 300px;
    overflow-y: auto;

    .logs-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
      font-weight: 500;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .logs-content {
      font-size: 12px;
      font-family: monospace;

      .log-item {
        padding: 4px 8px;
        border-radius: 2px;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 8px;

        &.log-success {
          background: #f0f9eb;
        }

        &.log-failed {
          background: #fef0f0;
        }

        &.log-skipped {
          background: #f4f4f5;
        }

        .log-index {
          color: #999;
          min-width: 30px;
        }

        .log-version {
          color: #409eff;
        }

        .log-type {
          color: #e6a23c;
          margin-right: 4px;
        }

        .log-table {
          color: #606266;
          min-width: 100px;
        }

        .log-field {
          color: #67c23a;
          margin-right: 8px;
        }

        .log-message {
          color: #909399;
          font-size: 11px;
          margin-left: auto;
        }
      }
    }
  }

  .upgrade-result {
    margin-top: 20px;
  }
}

.upgrade-logs {
  .pagination-wrap {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
  }
}

/* Danh sách tệp lỗi: mỗi hàng cao 30px */
.error-files-table-wrap {
  max-height: none;
  overflow: visible;
}

.error-files-table :deep(.el-table__cell) {
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

.error-files-table :deep(.el-table__cell .cell) {
  line-height: 30px;
  padding-top: 0;
  padding-bottom: 0;
}

.download-msg {
  font-size: 12px;
  font-weight: 400;
  color: #999;
}
</style>
