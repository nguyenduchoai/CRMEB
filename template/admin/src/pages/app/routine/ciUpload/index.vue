<template>
  <div class="routine-ci-upload">
    <!-- Phần đầu trang -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-icon">
          <i class="el-icon-upload"></i>
        </div>
        <div class="header-text">
          <h1>Tải lên Mini Program nhanh</h1>
          <p>Dùng miniprogram-ci để tự động triển khai mã nguồn Mini Program lên máy chủ WeChat</p>
        </div>
      </div>
    </div>

    <!-- Chỉ báo tiến trình -->
    <div class="progress-steps">
      <div class="step" :class="{ active: true, completed: envStatus.ready }">
        <div class="step-number">1</div>
        <div class="step-label">Cấu hình môi trường</div>
      </div>
      <div class="step-line" :class="{ completed: envStatus.ready }"></div>
      <div class="step"
        :class="{ active: envStatus.ready, completed: uploadConfig.app_id_configured && uploadConfig.private_key_exists }">
        <div class="step-number">2</div>
        <div class="step-label">Cấu hình tải lên</div>
      </div>
      <div class="step-line" :class="{ completed: uploadConfig.app_id_configured && uploadConfig.private_key_exists }">
      </div>
      <div class="step"
        :class="{ active: envStatus.ready && uploadConfig.app_id_configured && uploadConfig.private_key_exists }">
        <div class="step-number">3</div>
        <div class="step-label">Tải lên code</div>
      </div>
    </div>

    <!-- Lưu ý sử dụng - có thể thu gọn -->
    <div class="notice-banner" :class="{ expanded: showNotice }">
      <div class="notice-header" @click="showNotice = !showNotice">
        <div class="notice-title">
          <i class="el-icon-info"></i>
          <span>Chuẩn bị trước khi sử dụng</span>
        </div>
        <i :class="showNotice ? 'el-icon-arrow-up' : 'el-icon-arrow-down'"></i>
      </div>
      <transition name="slide-fade">
        <div v-show="showNotice" class="notice-content">
          <div class="notice-item">
            <div class="notice-step">1</div>
            <div class="notice-text">
              <strong>Tạo khóa tải lên mã nguồn</strong>
              <p>Truy cập <a href="https://mp.weixin.qq.com/" target="_blank" rel="noopener">nền tảng WeChat Official Accounts</a> → Quản lý phát triển → Cài đặt phát triển → Tải lên mã nguồn Mini Program → Tạo khóa</p>
            </div>
          </div>
          <div class="notice-item">
            <div class="notice-step">2</div>
            <div class="notice-text">
              <strong>Cấu hình danh sách trắng IP</strong>
              <p>Trên cùng trang đó, thêm IP công khai của máy chủ vào danh sách trắng</p>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Vùng nội dung chính -->
    <div class="main-content">
      <!-- Thẻ trạng thái môi trường -->
      <div class="card env-card" :class="{ 'card-success': envStatus.ready, 'card-warning': !envStatus.ready }">
        <div class="card-header">
          <div class="card-title">
            <i class="el-icon-cpu"></i>
            <span>Môi trường chạy</span>
          </div>
          <button class="refresh-btn" @click="checkEnvironment" :disabled="loading.environment">
            <i :class="loading.environment ? 'el-icon-loading' : 'el-icon-refresh'"></i>
          </button>
        </div>

        <div class="card-body" v-loading="loading.environment">
          <!-- Tổng quan trạng thái môi trường -->
          <div class="status-overview">
            <div class="status-badge" :class="envStatus.ready ? 'success' : 'warning'">
              <i :class="envStatus.ready ? 'el-icon-check' : 'el-icon-warning'"></i>
              {{ envStatus.ready ? 'Môi trường đã sẵn sàng' : 'Môi trường chưa sẵn sàng' }}
            </div>
          </div>

          <!-- Chi tiết môi trường -->
          <div class="env-grid">
            <div class="env-item">
              <div class="env-icon os">
                <i class="el-icon-monitor"></i>
              </div>
              <div class="env-info">
                <span class="env-label">Hệ điều hành</span>
                <span class="env-value">{{ envStatus.os?.type || '-' }} {{ envStatus.os?.version || '' }}</span>
              </div>
            </div>
            <div class="env-item">
              <div class="env-icon" :class="envStatus.node?.installed ? 'success' : 'error'">
                <i class="el-icon-connection"></i>
              </div>
              <div class="env-info">
                <span class="env-label">Node.js</span>
                <span class="env-value" :class="envStatus.node?.installed ? 'text-success' : 'text-error'">
                  {{ envStatus.node?.installed ? 'v' + envStatus.node.version : 'Chưa cài đặt' }}
                </span>
              </div>
            </div>
            <div class="env-item">
              <div class="env-icon" :class="envStatus.miniprogram_ci?.installed ? 'success' : 'error'">
                <i class="el-icon-box"></i>
              </div>
              <div class="env-info">
                <span class="env-label">miniprogram-ci</span>
                <span class="env-value" :class="envStatus.miniprogram_ci?.installed ? 'text-success' : 'text-error'">
                  {{ envStatus.miniprogram_ci?.installed ? 'v' + envStatus.miniprogram_ci.version : 'Chưa cài đặt' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Cảnh báo exec bị vô hiệu hóa -->
          <div v-if="envStatus.exec_enabled === false" class="alert alert-error">
            <i class="el-icon-warning"></i>
            <div class="alert-content">
              <strong>Không thể dùng tính năng tải lên Mini Program</strong>
              <p>Máy chủ đã vô hiệu hóa hàm <code>exec</code> — vui lòng bật hàm này trong cấu hình PHP.</p>
              <p class="alert-hint">BT Panel: App Store → PHP → Cài đặt → Hàm bị vô hiệu hóa → Xóa exec</p>
            </div>
          </div>

          <!-- Nút cài đặt -->
          <div v-if="!envStatus.ready && envStatus.exec_enabled !== false" class="action-buttons">

            <button class="btn btn-secondary" @click="showGuide = true">
              <i class="el-icon-document"></i>
              <span>Hướng dẫn cài đặt thủ công</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Thẻ cấu hình tải lên -->
      <div v-if="envStatus.ready" class="card config-card">
        <div class="card-header">
          <div class="card-title">
            <i class="el-icon-setting"></i>
            <span>Cấu hình tải lên</span>
          </div>
        </div>

        <div class="card-body">
          <div class="config-grid">
            <!-- Cấu hình AppId -->
            <div class="config-item">
              <div class="config-icon">
                <i class="el-icon-key"></i>
              </div>
              <div class="config-info">
                <span class="config-label">AppId Mini Program</span>
                <div class="config-value">
                  <template v-if="uploadConfig.app_id_configured">
                    <span class="value-text">{{ uploadConfig.app_id }}</span>
                    <span class="status-dot success"></span>
                  </template>
                  <template v-else>
                    <span class="value-text text-muted">Chưa cấu hình</span>
                    <router-link :to="{ path: $routeProStr + '/setting/routine_config/2/7' }" class="config-link">
                      Cấu hình ngay <i class="el-icon-arrow-right"></i>
                    </router-link>
                  </template>
                </div>
              </div>
            </div>

            <!-- Cấu hình khóa tải lên -->
            <div class="config-item">
              <div class="config-icon">
                <i class="el-icon-lock"></i>
              </div>
              <div class="config-info">
                <span class="config-label">Khóa tải lên</span>
                <div class="config-value">
                  <template v-if="uploadConfig.private_key_exists">
                    <span class="value-text">Đã cấu hình</span>
                    <span class="status-dot success"></span>
                    <button class="link-btn" @click="showKeyUpload = true">Tải lên lại</button>
                  </template>
                  <template v-else>
                    <span class="value-text text-muted">Chưa cấu hình</span>
                    <button class="btn btn-sm btn-primary" @click="showKeyUpload = true">
                      <i class="el-icon-upload2"></i> Khóa tải lên
                    </button>
                  </template>
                </div>
              </div>
              <div class="config-hint">
                <a href="https://mp.weixin.qq.com/" target="_blank" rel="noopener">Truy cập nền tảng WeChat Official Accounts để lấy</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Thẻ tải lên một cú nhấp -->
      <div v-if="envStatus.ready && uploadConfig.app_id_configured && uploadConfig.private_key_exists" class="card upload-card">
        <div class="card-header">
          <div class="card-title">
            <i class="el-icon-upload"></i>
            <span>Tải lên nhanh</span>
          </div>
          <span class="badge badge-success">Không cần công cụ nhà phát triển</span>
        </div>

        <div class="card-body">
          <!-- Thông báo -->
          <div class="info-banner">
            <i class="el-icon-info"></i>
            <span>Hệ thống sẽ tự động tạo gói mã nguồn Mini Program và tải lên máy chủ WeChat</span>
          </div>

          <!-- Biểu mẫu tải lên -->
          <el-form :model="uploadForm" :rules="uploadRules" ref="uploadForm" class="upload-form" label-position="top">
            <div class="form-row">
              <el-form-item label="Số phiên bản" prop="version" class="form-item-half">
                <el-input v-model="uploadForm.version" placeholder="Ví dụ: 1.0.0" prefix-icon="el-icon-price-tag">
                </el-input>
              </el-form-item>

              <el-form-item label="Tính năng livestream" class="form-item-half">
                <!-- <el-radio-group v-model="uploadForm.is_live" class="radio-group-custom">
                  <el-radio-button :label="0">Chưa kích hoạt</el-radio-button>
                  <el-radio-button :label="1">Đã kích hoạt</el-radio-button>
                </el-radio-group> -->
                <el-switch v-model="uploadForm.is_live" :active-value="1" :inactive-value="0" class="defineSwitch"
                  size="large" width=200 active-text="Đã kích hoạt" inactive-text="Chưa kích hoạt" />
              </el-form-item>
            </div>

            <el-form-item label="Mô tả phiên bản">
              <el-input v-model="uploadForm.desc" type="textarea" :rows="3" placeholder="Mô tả ngắn nội dung cập nhật lần này (không bắt buộc)">
              </el-input>
            </el-form-item>

            <div class="form-actions">
              <button type="button" class="btn btn-primary btn-lg" :class="{ loading: loading.upload }"
                :disabled="loading.upload" @click="handleUpload">
                <i :class="loading.upload ? 'el-icon-loading' : 'el-icon-upload2'"></i>
                <span>Tải lên WeChat</span>
              </button>
              <button type="button" class="btn btn-outline btn-lg" :class="{ loading: loading.preview }"
                :disabled="loading.preview" @click="handlePreview">
                <i :class="loading.preview ? 'el-icon-loading' : 'el-icon-mobile-phone'"></i>
                <span>Lấy mã xem trước</span>
              </button>
            </div>
          </el-form>

          <!-- Mã QR xem trước -->
          <transition name="fade-slide">
            <div v-if="previewQrcode" class="preview-section">
              <div class="preview-card">
                <img :src="previewQrcode" alt="Mã QR xem trước" />
                <p>Dùng WeChat quét mã để xem trước</p>
              </div>
            </div>
          </transition>

          <!-- Kết quả tải lên -->
          <transition name="fade-slide">
            <div v-if="uploadResult" class="result-section">
              <div class="result-card" :class="uploadResult.success ? 'success' : 'error'">
                <div class="result-icon">
                  <i :class="uploadResult.success ? 'el-icon-check' : 'el-icon-close'"></i>
                </div>
                <div class="result-content">
                  <h4>{{ uploadResult.success ? 'Tải lên thành công' : 'Tải lên thất bại' }}</h4>
                  <p v-if="uploadResult.success">Phiên bản {{ uploadResult.version }} đã được tải lên máy chủ WeChat</p>
                  <p v-if="uploadResult.success">
                    Vui lòng truy cập <a href="https://mp.weixin.qq.com/" target="_blank" rel="noopener">nền tảng WeChat Official Accounts</a> Gửi duyệt
                  </p>
                  <p v-else class="error-msg">{{ uploadResult.message }}</p>
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <!-- Hộp thoại hướng dẫn cài đặt thủ công -->
    <el-dialog title="Hướng dẫn cài đặt thủ công" :visible.sync="showGuide" width="650px" custom-class="custom-dialog">
      <div v-if="installGuide" class="install-guide">
        <!-- Script cài đặt một cú nhấp -->
        <div v-if="installGuide.script_url" class="guide-section recommended">
          <div class="section-badge">
            <i class="el-icon-star-on"></i>
            <span>Cách được đề xuất</span>
          </div>
          <div class="section-header">
            <div class="header-icon">
              <i class="el-icon-magic-stick"></i>
            </div>
            <div class="header-text">
              <h3>Cài đặt tự động nhanh</h3>
              <p>Cách đơn giản nhất, tự động hoàn tất mọi cấu hình</p>
            </div>
          </div>
          <div class="install-instruction">
            <span class="instruction-label">Chạy lệnh sau trên terminal của máy chủ:</span>
          </div>
          <div class="code-block">
            <div class="code-content">
              <span class="code-prompt">$</span>
              <code>curl -fsSL {{ installGuide.script_url }} | bash</code>
            </div>
            <button class="copy-btn" @click="copyToClipboard(`curl -fsSL ${installGuide.script_url} | bash`)"
              title="Sao chép lệnh">
              <i class="el-icon-document-copy"></i>
              <span class="copy-text">Sao chép</span>
            </button>
          </div>
          <div class="section-footer">
            <i class="el-icon-download"></i>
            <span>Hoặc <a :href="installGuide.script_url" target="_blank" rel="noopener">tải xuống tệp script</a> rồi chạy thủ công</span>
          </div>
        </div>

        <div class="divider-section">
          <div class="divider-line"></div>
          <span class="divider-text">Hoặc cài đặt thủ công</span>
          <div class="divider-line"></div>
        </div>

        <div class="guide-section manual">
          <div class="section-header">
            <div class="header-icon manual-icon">
              <i class="el-icon-document"></i>
            </div>
            <div class="header-text">
              <h3>{{ installGuide.title || 'Các bước cài đặt thủ công' }}</h3>
              <p>Thực hiện lần lượt theo các bước sau</p>
            </div>
          </div>
          <div class="guide-steps">
            <div v-for="(step, index) in installGuide.steps" :key="index" class="step-item">
              <div class="step-number">{{ index + 1 }}</div>
              <div class="step-content">
                <code>{{ step }}</code>
              </div>
              <button class="step-copy-btn" @click="copyToClipboard(step)" title="Sao chép">
                <i class="el-icon-document-copy"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <span slot="footer">
        <button class="btn btn-secondary" @click="showGuide = false">Tắt</button>
      </span>
    </el-dialog>

    <!-- Hộp thoại tải lên khóa -->
    <el-dialog title="Tải lên khóa tải lên mã nguồn" :visible.sync="showKeyUpload" width="650px" custom-class="custom-dialog">
      <div class="key-upload-content">
        <div class="info-card">
          <div class="info-card-header">
            <i class="el-icon-info"></i>
            <span>Cách lấy khóa</span>
          </div>
          <ol class="info-steps">
            <li>Đăng nhập <a href="https://mp.weixin.qq.com/" target="_blank" rel="noopener">nền tảng WeChat Official Accounts</a></li>
            <li>Vào Quản lý phát triển → Cài đặt phát triển → Tải lên mã nguồn Mini Program</li>
            <li>Nhấn nút “Tạo”, tải xuống tệp private.key</li>
            <li>Mở bằng trình soạn thảo văn bản, sao chép toàn bộ nội dung và dán vào bên dưới</li>
          </ol>
        </div>

        <div class="info-card warning">
          <div class="info-card-header">
            <i class="el-icon-warning"></i>
            <span>Cấu hình danh sách trắng IP</span>
          </div>
          <p>Trên cùng trang đó, thêm IP công khai của máy chủ vào danh sách trắng</p>
        </div>

        <div class="key-input-section">
          <label>Nội dung khóa</label>
          <textarea v-model="keyContent" rows="10" placeholder="Vui lòng dán toàn bộ nội dung tệp private.key -----BEGIN RSA PRIVATE KEY----- ... -----END RSA PRIVATE KEY-----" class="key-textarea">
      </textarea>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <button class="btn btn-secondary" @click="showKeyUpload = false">Hủy</button>
        <button class="btn btn-primary" :class="{ loading: loading.saveKey }" :disabled="loading.saveKey"
          @click="savePrivateKey">
          <i :class="loading.saveKey ? 'el-icon-loading' : 'el-icon-check'"></i>
          <span>Lưu khóa</span>
        </button>
      </span>
    </el-dialog>

  </div>
</template>

<script>
import {
  routineCIEnvironment,
  routineCIGuide,
  routineCIConfig,
  routineCISaveKey,
  routineCIUpload,
  routineCIPreview,
} from '@/api/app';

export default {
  name: 'RoutineCIUpload',
  data() {
    return {
      // Trạng thái trang
      showNotice: false,
      envStatus: {},
      uploadConfig: {},
      installGuide: null,

      // Trạng thái tải
      loading: {
        environment: false,
        saveKey: false,
        upload: false,
        preview: false,
      },

      // Trạng thái hộp thoại
      showGuide: false,
      showKeyUpload: false,

      // Dữ liệu biểu mẫu
      keyContent: '',
      uploadForm: {
        version: '',
        desc: '',
        is_live: 0,
      },
      uploadRules: {
        version: [
          { required: true, message: 'Vui lòng nhập số phiên bản', trigger: 'blur' },
          { pattern: /^\d+\.\d+\.\d+$/, message: 'Số phiên bản sai định dạng, vui lòng dùng định dạng x.x.x', trigger: 'blur' },
        ],
      },

      // Dữ liệu kết quả
      previewQrcode: '',
      uploadResult: null,
    };
  },

  mounted() {
    this.checkEnvironment();
  },

  methods: {
    // Sao chép vào bộ nhớ tạm
    async copyToClipboard(text) {
      try {
        await navigator.clipboard.writeText(text);
        this.$message.success('Đã sao chép vào bộ nhớ tạm');
      } catch (err) {
        this.$message.error('Sao chép thất bại');
      }
    },

    // Kiểm tra môi trường
    async checkEnvironment() {
      this.loading.environment = true;
      try {
        const res = await routineCIEnvironment();
        this.envStatus = res.data;

        if (res.data.ready) {
          this.getUploadConfig();
        } else {
          this.getInstallGuide();
        }
      } catch (err) {
        this.$message.error(err.msg || 'Kiểm tra môi trường thất bại');
      } finally {
        this.loading.environment = false;
      }
    },

    // Lấy cấu hình tải lên
    async getUploadConfig() {
      try {
        const res = await routineCIConfig();
        this.uploadConfig = res.data;
      } catch (err) {
        console.error('Lấy cấu hình tải lên thất bại', err);
      }
    },

    // Lấy hướng dẫn cài đặt
    async getInstallGuide() {
      try {
        const res = await routineCIGuide();
        this.installGuide = res.data;
      } catch (err) {
        console.error('Lấy hướng dẫn cài đặt thất bại', err);
      }
    },

    // Lưu khóa
    async savePrivateKey() {
      if (!this.keyContent.trim()) {
        this.$message.warning('Vui lòng nhập nội dung khóa');
        return;
      }

      this.loading.saveKey = true;
      try {
        await routineCISaveKey({ key_content: this.keyContent });
        this.$message.success('Lưu khóa thành công');
        this.showKeyUpload = false;
        this.keyContent = '';
        this.getUploadConfig();
      } catch (err) {
        this.$message.error(err.msg || 'Lưu thất bại');
      } finally {
        this.loading.saveKey = false;
      }
    },

    // Tải lên code
    async handleUpload() {
      this.$refs.uploadForm.validate(async (valid) => {
        if (!valid) return;

        try {
          await this.$confirm('Bạn có chắc muốn tải lên mã nguồn Mini Program không?', 'Xác nhận tải lên', {
            confirmButtonText: 'Xác nhận tải lên',
            cancelButtonText: 'Hủy',
            type: 'info',
          });

          this.loading.upload = true;
          this.uploadResult = null;

          const res = await routineCIUpload(this.uploadForm);

          this.uploadResult = {
            success: true,
            version: this.uploadForm.version,
            message: res.data?.message || 'Tải lên thành công',
          };

          this.$message.success('Tải lên thành công');
        } catch (err) {
          if (err !== 'cancel') {
            this.uploadResult = {
              success: false,
              message: err.msg || 'Tải lên thất bại',
            };
            this.$message.error(err.msg || 'Tải lên thất bại');
          }
        } finally {
          this.loading.upload = false;
        }
      });
    },

    // Lấy mã xem trước
    async handlePreview() {
      this.loading.preview = true;
      try {
        const res = await routineCIPreview({});
        this.previewQrcode = res.data.qrcode_url;
        this.$message.success('Tạo mã QR xem trước thành công');
      } catch (err) {
        this.$message.error(err.msg || 'Lấy mã xem trước thất bại');
      } finally {
        this.loading.preview = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@use 'sass:color';

// ========================================
// Biến design system (bảng màu SaaS)
// ========================================
$primary: #2563EB;
$primary-light: #3B82F6;
$primary-lighter: #DBEAFE;
$secondary: #64748B;
$success: #10B981;
$success-light: #D1FAE5;
$warning: #F59E0B;
$warning-light: #FEF3C7;
$error: #EF4444;
$error-light: #FEE2E2;
$cta: #F97316;

$bg-primary: #F8FAFC;
$bg-card: #FFFFFF;
$text-primary: #1E293B;
$text-secondary: #475569;
$text-muted: #64748B;
$border-color: #E2E8F0;

$radius-sm: 6px;
$radius-md: 10px;
$radius-lg: 16px;
$shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
$shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
$shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);

$transition-fast: 150ms ease;
$transition-normal: 200ms ease;
$transition-slow: 300ms ease;

// ========================================
// Bố cục cơ bản
// ========================================
.routine-ci-upload {
  min-height: 100vh;
  background: $bg-primary;
  padding: 24px;

  // Phần đầu trang
  .page-header {
    margin-bottom: 24px;

    .header-content {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .header-icon {
      width: 56px;
      height: 56px;
      background: linear-gradient(135deg, $primary 0%, $primary-light 100%);
      border-radius: $radius-lg;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba($primary, 0.3);

      i {
        font-size: 28px;
        color: white;
      }
    }

    .header-text {
      h1 {
        font-size: 24px;
        font-weight: 700;
        color: $text-primary;
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
      }

      p {
        font-size: 14px;
        color: $text-muted;
        margin: 0;
      }
    }
  }

  // Các bước tiến trình
  .progress-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 32px;
    padding: 20px;
    background: $bg-card;
    border-radius: $radius-lg;
    box-shadow: $shadow-sm;

    .step {
      display: flex;
      align-items: center;
      gap: 10px;
      opacity: 0.4;
      transition: all $transition-normal;

      &.active {
        opacity: 1;
      }

      &.completed .step-number {
        background: $success;
        border-color: $success;
      }

      .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: $bg-primary;
        border: 2px solid $border-color;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        color: $text-secondary;
        transition: all $transition-normal;
      }

      &.active .step-number {
        background: $primary;
        border-color: $primary;
        color: white;
      }

      .step-label {
        font-size: 14px;
        font-weight: 500;
        color: $text-secondary;
      }
    }

    .step-line {
      width: 60px;
      height: 2px;
      background: $border-color;
      margin: 0 16px;
      transition: background $transition-normal;

      &.completed {
        background: $success;
      }
    }
  }

  // Banner lưu ý sử dụng
  .notice-banner {
    background: $bg-card;
    border: 1px solid $border-color;
    border-radius: $radius-md;
    margin-bottom: 24px;
    overflow: hidden;

    .notice-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 20px;
      cursor: pointer;
      transition: background $transition-fast;

      &:hover {
        background: $bg-primary;
      }

      .notice-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: $text-primary;

        i {
          color: $warning;
          font-size: 18px;
        }
      }

      >i {
        color: $text-muted;
        transition: transform $transition-normal;
      }
    }

    .notice-content {
      padding: 0 20px 20px;
      border-top: 1px solid $border-color;

      .notice-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 0;
        border-bottom: 1px solid color.adjust($border-color, $lightness: 3%);

        &:last-child {
          border-bottom: none;
          padding-bottom: 0;
        }

        .notice-step {
          width: 24px;
          height: 24px;
          min-width: 24px;
          background: $primary-lighter;
          color: $primary;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
          font-size: 12px;
        }

        .notice-text {
          strong {
            display: block;
            font-size: 14px;
            color: $text-primary;
            margin-bottom: 4px;
          }

          p {
            font-size: 13px;
            color: $text-muted;
            margin: 0;
            line-height: 1.5;

            a {
              color: $primary;
              text-decoration: none;
              font-weight: 500;

              &:hover {
                text-decoration: underline;
              }
            }
          }
        }
      }
    }
  }

  // Vùng nội dung chính
  .main-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  // Kiểu cơ bản của thẻ
  .card {
    background: $bg-card;
    border-radius: $radius-lg;
    box-shadow: $shadow-sm;
    border: 1px solid $border-color;
    overflow: hidden;
    transition: box-shadow $transition-normal, border-color $transition-normal;

    &:hover {
      box-shadow: $shadow-md;
    }

    &.card-success {
      border-color: color.adjust($success, $lightness: 30%);
    }

    &.card-warning {
      border-color: color.adjust($warning, $lightness: 30%);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 24px;
      border-bottom: 1px solid $border-color;
      background: linear-gradient(to bottom, $bg-card, $bg-primary);

      .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 600;
        color: $text-primary;

        i {
          font-size: 20px;
          color: $primary;
        }
      }

      .refresh-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: $bg-primary;
        border-radius: $radius-sm;
        color: $text-muted;
        cursor: pointer;
        transition: all $transition-fast;
        display: flex;
        align-items: center;
        justify-content: center;

        &:hover:not(:disabled) {
          background: $primary-lighter;
          color: $primary;
        }

        &:disabled {
          cursor: not-allowed;
          opacity: 0.6;
        }
      }
    }

    .card-body {
      padding: 24px;
    }
  }

  // Thẻ trạng thái môi trường
  .env-card {
    .status-overview {
      display: flex;
      justify-content: center;
      margin-bottom: 24px;

      .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;

        i {
          font-size: 18px;
        }

        &.success {
          background: $success-light;
          color: color.adjust($success, $lightness: -10%);
        }

        &.warning {
          background: $warning-light;
          color: color.adjust($warning, $lightness: -10%);
        }
      }
    }

    .env-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;

      @media (max-width: 768px) {
        grid-template-columns: 1fr;
      }

      .env-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        background: $bg-primary;
        border-radius: $radius-md;
        border: 1px solid transparent;
        transition: all $transition-fast;

        &:hover {
          border-color: $border-color;
        }

        .env-icon {
          width: 44px;
          height: 44px;
          border-radius: $radius-md;
          display: flex;
          align-items: center;
          justify-content: center;
          background: white;
          border: 1px solid $border-color;

          i {
            font-size: 20px;
            color: $text-muted;
          }

          &.os i {
            color: $secondary;
          }

          &.success {
            background: $success-light;
            border-color: color.adjust($success, $lightness: 30%);

            i {
              color: $success;
            }
          }

          &.error {
            background: $error-light;
            border-color: color.adjust($error, $lightness: 25%);

            i {
              color: $error;
            }
          }
        }

        .env-info {
          display: flex;
          flex-direction: column;
          gap: 4px;

          .env-label {
            font-size: 12px;
            color: $text-muted;
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }

          .env-value {
            font-size: 14px;
            font-weight: 600;
            color: $text-primary;

            &.text-success {
              color: $success;
            }

            &.text-error {
              color: $error;
            }
          }
        }
      }
    }
  }

  // Kiểu cảnh báo
  .alert {
    display: flex;
    gap: 14px;
    padding: 16px 20px;
    border-radius: $radius-md;
    margin-top: 20px;

    >i {
      font-size: 22px;
      flex-shrink: 0;
    }

    &.alert-error {
      background: $error-light;
      border: 1px solid color.adjust($error, $lightness: 25%);

      >i {
        color: $error;
      }
    }

    .alert-content {
      strong {
        display: block;
        font-size: 14px;
        color: $text-primary;
        margin-bottom: 6px;
      }

      p {
        font-size: 13px;
        color: $text-secondary;
        margin: 0 0 4px;
        line-height: 1.5;

        code {
          background: rgba(0, 0, 0, 0.06);
          padding: 2px 6px;
          border-radius: 4px;
          font-family: 'SF Mono', Monaco, monospace;
          font-size: 12px;
        }
      }

      .alert-hint {
        color: $text-muted;
        font-size: 12px;
      }
    }
  }

  // Vùng nút thao tác
  .action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid $border-color;
  }

  // Thẻ cấu hình
  .config-card {
    .config-grid {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .config-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      padding: 20px 0;
      border-bottom: 1px solid $border-color;

      &:first-child {
        padding-top: 0;
      }

      &:last-child {
        border-bottom: none;
        padding-bottom: 0;
      }

      .config-icon {
        width: 40px;
        height: 40px;
        background: $primary-lighter;
        border-radius: $radius-md;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        i {
          font-size: 18px;
          color: $primary;
        }
      }

      .config-info {
        flex: 1;
        min-width: 0;

        .config-label {
          display: block;
          font-size: 12px;
          color: $text-muted;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          margin-bottom: 6px;
        }

        .config-value {
          display: flex;
          align-items: center;
          gap: 10px;
          flex-wrap: wrap;

          .value-text {
            font-size: 15px;
            font-weight: 500;
            color: $text-primary;

            &.text-muted {
              color: $text-muted;
            }
          }

          .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;

            &.success {
              background: $success;
              box-shadow: 0 0 0 3px rgba($success, 0.2);
            }
          }

          .config-link {
            font-size: 13px;
            color: $primary;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;

            &:hover {
              text-decoration: underline;
            }
          }
        }
      }

      .config-hint {
        font-size: 12px;
        color: $text-muted;
        margin-top: 6px;

        a {
          color: $primary;
          text-decoration: none;

          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }

  // Thẻ tải lên
  .upload-card {
    .badge {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 50px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;

      &.badge-success {
        background: $success-light;
        color: color.adjust($success, $lightness: -10%);
      }
    }

    .info-banner {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 18px;
      background: $primary-lighter;
      border-radius: $radius-md;
      margin-bottom: 24px;

      i {
        font-size: 18px;
        color: $primary;
      }

      span {
        font-size: 13px;
        color: color.adjust($primary, $lightness: -10%);
      }
    }

    .upload-form {
      .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;

        @media (max-width: 768px) {
          grid-template-columns: 1fr;
        }
      }

      .form-item-half {
        margin-bottom: 0;
      }

      ::v-deep .el-form-item__label {
        font-weight: 500;
        color: $text-primary;
        padding-bottom: 8px;
      }

      ::v-deep .el-input__inner,
      ::v-deep .el-textarea__inner {
        border-radius: $radius-sm;
        border-color: $border-color;
        transition: all $transition-fast;

        &:focus {
          border-color: $primary;
          box-shadow: 0 0 0 3px rgba($primary, 0.1);
        }
      }

      .radio-group-custom {
        ::v-deep .el-radio-button__inner {
          border-radius: $radius-sm;
          border-color: $border-color;
          padding: 10px 20px;
        }

        ::v-deep .el-radio-button__orig-radio:checked+.el-radio-button__inner {
          background: $primary;
          border-color: $primary;
          box-shadow: -1px 0 0 0 $primary;
        }
      }

      .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 8px;
      }
    }
  }

  // Vùng xem trước
  .preview-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid $border-color;

    .preview-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 24px;
      background: $bg-primary;
      border-radius: $radius-md;
      text-align: center;

      img {
        width: 180px;
        height: 180px;
        border-radius: $radius-md;
        box-shadow: $shadow-md;
        margin-bottom: 12px;
      }

      p {
        font-size: 13px;
        color: $text-muted;
        margin: 0;
      }
    }
  }

  // Vùng kết quả
  .result-section {
    margin-top: 24px;

    .result-card {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      padding: 20px;
      border-radius: $radius-md;

      &.success {
        background: $success-light;
        border: 1px solid color.adjust($success, $lightness: 30%);

        .result-icon {
          background: $success;
        }
      }

      &.error {
        background: $error-light;
        border: 1px solid color.adjust($error, $lightness: 25%);

        .result-icon {
          background: $error;
        }
      }

      .result-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        i {
          font-size: 20px;
          color: white;
        }
      }

      .result-content {
        h4 {
          font-size: 15px;
          font-weight: 600;
          color: $text-primary;
          margin: 0 0 6px;
        }

        p {
          font-size: 13px;
          color: $text-secondary;
          margin: 0 0 4px;
          line-height: 1.5;

          a {
            color: $primary;
            text-decoration: none;
            font-weight: 500;

            &:hover {
              text-decoration: underline;
            }
          }

          &.error-msg {
            color: $error;
          }
        }
      }
    }
  }
}

// ========================================
// Thành phần nút
// ========================================
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 500;
  border-radius: $radius-sm;
  border: none;
  cursor: pointer;
  transition: all $transition-fast;
  text-decoration: none;

  i {
    font-size: 16px;
  }

  &:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba($primary, 0.2);
  }

  &.btn-sm {
    padding: 6px 14px;
    font-size: 13px;

    i {
      font-size: 14px;
    }
  }

  &.btn-lg {
    padding: 14px 28px;
    font-size: 15px;

    i {
      font-size: 18px;
    }
  }

  &.btn-primary {
    background: linear-gradient(135deg, $primary 0%, $primary-light 100%);
    color: white;
    box-shadow: 0 2px 8px rgba($primary, 0.3);

    &:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba($primary, 0.4);
    }

    &:active:not(:disabled) {
      transform: translateY(0);
    }
  }

  &.btn-secondary {
    background: $bg-primary;
    color: $text-secondary;
    border: 1px solid $border-color;

    &:hover:not(:disabled) {
      background: white;
      border-color: color.adjust($border-color, $lightness: -5%);
    }
  }

  &.btn-outline {
    background: transparent;
    color: $text-primary;
    border: 1px solid $border-color;

    &:hover:not(:disabled) {
      background: $bg-primary;
      border-color: color.adjust($border-color, $lightness: -5%);
    }
  }

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  &.loading {
    pointer-events: none;

    i {
      animation: spin 1s linear infinite;
    }
  }
}

.link-btn {
  background: none;
  border: none;
  color: $primary;
  font-size: 13px;
  cursor: pointer;
  padding: 0;
  margin-left: 8px;

  &:hover {
    text-decoration: underline;
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

// ========================================
// Kiểu hộp thoại
// ========================================
::v-deep .custom-dialog {
  border-radius: $radius-lg;
  overflow: hidden;

  .el-dialog__header {
    padding: 20px 24px;
    border-bottom: 1px solid $border-color;
    background: $bg-primary;

    .el-dialog__title {
      font-size: 16px;
      font-weight: 600;
      color: $text-primary;
    }

    .el-dialog__headerbtn {
      top: 20px;
      right: 20px;
    }
  }

  .el-dialog__body {
    padding: 24px;
  }

  .el-dialog__footer {
    padding: 16px 24px;
    border-top: 1px solid $border-color;
    background: $bg-primary;
  }
}

// Hộp thoại hướng dẫn cài đặt
.install-guide {
  .guide-section {
    padding: 24px;
    background: $bg-primary;
    border-radius: $radius-lg;
    margin-bottom: 0;
    border: 1px solid $border-color;
    transition: all $transition-normal;

    &.recommended {
      background: linear-gradient(135deg, rgba($success, 0.08) 0%, rgba($success, 0.03) 100%);
      border: 2px solid rgba($success, 0.3);
      position: relative;
      overflow: visible;

      &:hover {
        border-color: rgba($success, 0.5);
        box-shadow: 0 8px 24px rgba($success, 0.15);
      }

      .section-badge {
        position: absolute;
        top: -12px;
        left: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, $success 0%, color.adjust($success, $lightness: -8%) 100%);
        color: white;
        font-size: 12px;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba($success, 0.4);

        i {
          font-size: 14px;
        }
      }

      .header-icon {
        background: linear-gradient(135deg, $success 0%, color.adjust($success, $lightness: -10%) 100%);
        box-shadow: 0 4px 12px rgba($success, 0.3);

        i {
          color: white;
        }
      }
    }

    &.manual {
      background: $bg-card;
      border: 1px solid $border-color;

      &:hover {
        border-color: color.adjust($border-color, $lightness: -8%);
        box-shadow: $shadow-md;
      }

      .manual-icon {
        background: linear-gradient(135deg, $secondary 0%, color.adjust($secondary, $lightness: -10%) 100%);

        i {
          color: white;
        }
      }
    }

    .section-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
      padding-top: 8px;

      .header-icon {
        width: 48px;
        height: 48px;
        border-radius: $radius-md;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        i {
          font-size: 24px;
        }
      }

      .header-text {
        flex: 1;

        h3 {
          font-size: 16px;
          font-weight: 700;
          color: $text-primary;
          margin: 0 0 4px;
          letter-spacing: -0.3px;
        }

        p {
          font-size: 13px;
          color: $text-muted;
          margin: 0;
        }
      }
    }

    .install-instruction {
      margin-bottom: 12px;

      .instruction-label {
        font-size: 13px;
        font-weight: 500;
        color: $text-secondary;
      }
    }

    .section-footer {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px dashed rgba($success, 0.3);
      font-size: 13px;
      color: $text-muted;

      i {
        font-size: 14px;
        color: $text-muted;
      }

      a {
        color: $success;
        font-weight: 500;
        text-decoration: none;
        transition: color $transition-fast;

        &:hover {
          color: color.adjust($success, $lightness: -10%);
          text-decoration: underline;
        }
      }
    }
  }

  .code-block {
    display: flex;
    align-items: center;
    gap: 0;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: $radius-md;
    padding: 0;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2), 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.05);

    .code-content {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 20px;
      overflow-x: auto;

      .code-prompt {
        font-family: 'SF Mono', Monaco, 'Consolas', monospace;
        font-size: 14px;
        color: #64748b;
        user-select: none;
      }

      code {
        font-family: 'SF Mono', Monaco, 'Consolas', monospace;
        font-size: 14px;
        color: #4ade80;
        word-break: break-all;
        line-height: 1.5;
      }
    }

    .copy-btn {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 16px 18px;
      background: rgba(255, 255, 255, 0.05);
      border: none;
      border-left: 1px solid rgba(255, 255, 255, 0.1);
      color: #94a3b8;
      cursor: pointer;
      transition: all $transition-fast;
      height: 100%;

      i {
        font-size: 16px;
      }

      .copy-text {
        font-size: 12px;
        font-weight: 500;
      }

      &:hover {
        background: rgba($primary, 0.2);
        color: white;

        i {
          color: #60a5fa;
        }
      }

      &:active {
        background: rgba($primary, 0.3);
      }
    }
  }

  .divider-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 28px 0;
    padding: 0 10px;

    .divider-line {
      flex: 1;
      height: 1px;
      background: linear-gradient(90deg, transparent 0%, $border-color 50%, transparent 100%);
    }

    .divider-text {
      font-size: 13px;
      font-weight: 500;
      color: $text-muted;
      white-space: nowrap;
      padding: 6px 16px;
      background: $bg-card;
      border: 1px solid $border-color;
      border-radius: 50px;
    }
  }

  .guide-steps {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 8px;

    .step-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 16px;
      background: $bg-primary;
      border: 1px solid $border-color;
      border-radius: $radius-md;
      transition: all $transition-fast;

      &:hover {
        background: white;
        border-color: color.adjust($border-color, $lightness: -5%);
        box-shadow: $shadow-sm;

        .step-copy-btn {
          opacity: 1;
        }
      }

      .step-number {
        width: 28px;
        height: 28px;
        min-width: 28px;
        background: $primary-lighter;
        color: $primary;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
      }

      .step-content {
        flex: 1;
        overflow-x: auto;

        code {
          font-family: 'SF Mono', Monaco, 'Consolas', monospace;
          font-size: 13px;
          color: $text-primary;
          line-height: 1.5;
          white-space: pre-wrap;
          word-break: break-all;
        }
      }

      .step-copy-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        background: transparent;
        border: 1px solid $border-color;
        border-radius: $radius-sm;
        color: $text-muted;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all $transition-fast;

        i {
          font-size: 14px;
        }

        &:hover {
          background: $primary-lighter;
          border-color: $primary;
          color: $primary;
        }

        &:active {
          transform: scale(0.95);
        }
      }
    }
  }
}

// Hộp thoại tải lên khóa
.key-upload-content {
  .info-card {
    padding: 16px 20px;
    background: $primary-lighter;
    border-radius: $radius-md;
    margin-bottom: 16px;

    &.warning {
      background: $warning-light;

      .info-card-header i {
        color: $warning;
      }
    }

    .info-card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      color: $text-primary;
      margin-bottom: 10px;

      i {
        font-size: 18px;
        color: $primary;
      }
    }

    ol.info-steps {
      margin: 0;
      padding-left: 20px;

      li {
        font-size: 13px;
        color: $text-secondary;
        line-height: 1.8;

        a {
          color: $primary;
          text-decoration: none;

          &:hover {
            text-decoration: underline;
          }
        }
      }
    }

    p {
      font-size: 13px;
      color: $text-secondary;
      margin: 0;
    }
  }

  .key-input-section {
    margin-top: 20px;

    label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: $text-primary;
      margin-bottom: 8px;
    }

    .key-textarea {
      width: 100%;
      padding: 14px;
      font-family: 'SF Mono', Monaco, monospace;
      font-size: 13px;
      border: 1px solid $border-color;
      border-radius: $radius-sm;
      resize: vertical;
      transition: all $transition-fast;

      &:focus {
        outline: none;
        border-color: $primary;
        box-shadow: 0 0 0 3px rgba($primary, 0.1);
      }

      &::placeholder {
        color: $text-muted;
      }
    }
  }
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

// ========================================
// Hiệu ứng động
// ========================================
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all $transition-normal;
}

.slide-fade-enter,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all $transition-slow;
}

.fade-slide-enter,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

::v-deep .defineSwitch.el-switch .el-switch__core,
::v-deep .defineSwitch.el-switch .el-switch__label {
  width: 65px !important;

}

::v-deep .el-switch__label {
  height: 21px !important;
}
</style>
