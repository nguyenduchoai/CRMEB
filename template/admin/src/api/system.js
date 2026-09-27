// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from '@/libs/request';

/**
 * @description Danh mục cấu hình -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function classListApi(data) {
  return request({
    url: 'setting/config_class',
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh mục cấu hình -- Form thêm mới
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function classAddApi(data) {
  return request({
    url: 'setting/config_class/create',
    method: 'get',
  });
}

/**
 * @description Danh mục cấu hình -- Form sửa
 * @param {Number} param id {Number} ID danh mục cấu hình
 */
export function classEditApi(id) {
  return request({
    url: `setting/config_class/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Danh mục cấu hình -- Đổi trạng thái
 * @param {Number} param id {Number} ID bài viết
 */
export function setStatusApi(data) {
  return request({
    url: `setting/config_class/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Cấu hình -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function configTabListApi(data) {
  return request({
    url: 'setting/config',
    method: 'get',
    params: data,
  });
}

/**
 * @description Cấu hình -- Form thêm mới
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function configTabAddApi(data) {
  return request({
    url: 'setting/config/create',
    method: 'get',
    params: data,
  });
}

/**
 * @description Cấu hình -- Form sửa
 * @param {Number} param id {Number} ID cấu hình
 */
export function configTabEditApi(id) {
  return request({
    url: `/setting/config/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cấu hình -- Đổi trạng thái
 * @param {Number} param id {Number} ID bài viết
 */
export function configSetStatusApi(id, status) {
  return request({
    url: `setting/config/set_status/${id}/${status}`,
    method: 'PUT',
  });
}

/**
 * @description Dữ liệu tổ hợp -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function groupListApi(data) {
  return request({
    url: 'setting/group',
    method: 'get',
    params: data,
  });
}

/**
 * @description Dữ liệu tổ hợp -- Thêm mới
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function groupAddApi(data) {
  return request({
    url: data.url,
    method: data.method,
    data: data.datas,
  });
}

/**
 * @description Dữ liệu tổ hợp -- Chi tiết
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function groupInfoApi(id) {
  return request({
    url: `setting/group/${id}`,
    method: 'get',
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function groupDataListApi(id, url) {
  return request({
    url: url,
    method: 'get',
    params: id,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form thêm mới
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function groupDataAddApi(id, url) {
  return request({
    url: url,
    method: 'get',
    params: id,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form sửa
 * @param {Number} param id {Number} ID danh sách dữ liệu tổ hợp
 * @param {Object} param data {Object} Đối tượng ID dữ liệu tổ hợp
 */
export function groupDataEditApi(data, url) {
  return request({
    url: url,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form sửa
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function groupDataHeaderApi(data, url) {
  return request({
    url: url,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Đổi trạng thái
 * @param {Object} param data {Object} Truyền giá trị danh sách dữ liệu tổ hợp
 */
export function groupDataSetApi(url) {
  return request({
    url: url,
    method: 'PUT',
  });
}

/**
 * @description Log hệ thống -- Điều kiện tìm kiếm
 */
export function searchAdminApi(data) {
  return request({
    url: `system/log/search_admin`,
    method: 'GET',
  });
}

/**
 * @description Log hệ thống -- Điều kiện tìm kiếm
 */
export function systemListApi(params) {
  return request({
    url: `system/log`,
    method: 'GET',
    params,
  });
}

/**
 * @description Kiểm tra file -- Danh sách
 */
export function fileListApi() {
  return request({
    url: `system/file`,
    method: 'GET',
  });
}

/**
 * @description Sao lưu dữ liệu -- Danh sách cơ sở dữ liệu
 */
export function backupListApi() {
  return request({
    url: `system/backup`,
    method: 'GET',
  });
}

/**
 * @description Sao lưu dữ liệu -- Xem chi tiết cấu trúc bảng
 */
export function backupReadListApi(params) {
  return request({
    url: `system/backup/read`,
    method: 'GET',
    params,
  });
}

/**
 * @description Sao lưu dữ liệu -- Sao lưu bảng
 */
export function backupBackupApi(data) {
  return request({
    url: `system/backup/backup`,
    method: 'put',
    data,
  });
}

/**
 * @description Sao lưu dữ liệu -- Tối ưu bảng
 */
export function backupOptimizeApi(data) {
  return request({
    url: `system/backup/optimize`,
    method: 'put',
    data,
  });
}

/**
 * @description Sao lưu dữ liệu -- Sửa bảng
 */
export function backupRepairApi(data) {
  return request({
    url: `system/backup/repair`,
    method: 'put',
    data,
  });
}

/**
 * @description Sao lưu dữ liệu -- Bảng lịch sử sao lưu
 */
export function filesListApi(data) {
  return request({
    url: `system/backup/file_list`,
    method: 'GET',
  });
}

/**
 * @description Sao lưu dữ liệu -- Tải bảng lịch sử sao lưu
 */
export function filesDownloadApi(params) {
  return request({
    url: `backup/download`,
    method: 'get',
    params,
  });
}

/**
 * @description Sao lưu dữ liệu -- Nhập
 */
export function filesImportApi(data) {
  return request({
    url: `system/backup/import`,
    method: 'POST',
    data,
  });
}

/**
 * @description Quản lý file -- Đăng nhập
 */
export function opendirLoginApi(data) {
  return request({
    url: `system/file/login`,
    method: 'POST',
    data,
  });
}

/**
 * @description Quản lý file -- Danh sách
 */
export function opendirListApi(params) {
  return request({
    url: `system/file/opendir`,
    method: 'GET',
    params,
    file_edit: true,
  });
}

/**
 * @description Quản lý file -- Đọc file
 */
export function openfileApi(params) {
  return request({
    url: `system/file/openfile`,
    method: 'GET',
    params,
    file_edit: true,
  });
}

/**
 * @description Quản lý file -- Lưu
 */
export function savefileApi(data) {
  return request({
    url: `system/file/savefile?fileToken=${data.fileToken}`,
    method: 'post',
    data,
    file_edit: true,
  });
}
/**
 * @description Quản lý file -- Tạo thư mục mới
 */
export function createFolder(params) {
  return request({
    url: `system/file/createFolder`,
    method: 'GET',
    params,
    file_edit: true,
  });
}
/**
 * @description Quản lý file -- Tạo file mới
 */
export function createFile(params) {
  return request({
    url: `system/file/createFile`,
    method: 'GET',
    params,
    file_edit: true,
  });
}
/**
 * @description Quản lý file -- Xóa file hoặc thư mục
 */
export function rename(params) {
  return request({
    url: `system/file/rename`,
    method: 'GET',
    params,
    file_edit: true,
  });
}
/**
 * @description Quản lý file -- Xóa file hoặc thư mục
 */
export function delFolder(params) {
  return request({
    url: `system/file/delFolder`,
    method: 'GET',
    params,
    file_edit: true,
  });
}

/**
 * Ghi chú tệp
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function fileMark(params) {
  return request({
    url: `system/file/mark`,
    method: 'get',
    params,
    file_edit: true,
  });
}

/**
 * @description Bảo trì an toàn -- Đổi domain
 */
export function replaceSiteUrlApi(data) {
  return request({
    url: `system/replace_site_url`,
    method: 'post',
    data,
  });
}

/**
 *
 */
export function auth() {
  return request({
    url: 'auth',
    method: 'get',
  });
}

/**
 * @description Yêu cầu cấp phép
 * @param data
 */
export function authApply(data) {
  return request({
    url: 'auth_apply',
    method: 'post',
    data,
  });
}

/**
 * @description Lấy quảng cáo trang CSKH
 * @param data
 */
export function getKfAdv() {
  return request({
    url: 'setting/get_kf_adv',
    method: 'get',
  });
}

/**
 * @description Thiết lập quảng cáo trang CSKH
 * @param data
 */
export function setKfAdv(data) {
  return request({
    url: 'setting/set_kf_adv',
    method: 'post',
    data,
  });
}

/**
 * @description Cấu hình dữ liệu
 * @param data
 */
export function groupAllApi() {
  return request({
    url: 'setting/group_all',
    method: 'get',
  });
}
/**
 * Danh sách phiên bản APP
 */
export function versionList(params) {
  return request({
    url: `system/version_list`,
    method: 'get',
    params,
  });
}
/**
 * Danh sách phiên bản APP
 */
export function versionCrate(id) {
  return request({
    url: `system/version_crate/${id}`,
    method: 'get',
  });
}

/**
 * @description Lưu cấu hình dữ liệu
 */
export function groupSaveApi(data) {
  return request({
    url: `setting/group_data/save_all`,
    method: 'POST',
    data,
  });
}

/**
 * @description Lưu cấu hình dữ liệu trang hướng dẫn
 */
export function openAdvSave(data) {
  return request({
    url: `diy/open_adv/add`,
    method: 'POST',
    data,
  });
}

/**
 * @description Lưu cấu hình dữ liệu trang hướng dẫn
 */
export function getOpenAdv() {
  return request({
    url: `diy/open_adv/info`,
    method: 'get',
  });
}

/**
 * @description Lấy logo shop trên PC
 */
export function pcLogoApi(id) {
  return request({
    url: `setting/config/get_system/${id}`,
    method: 'get',
  });
}

/**
 * @description Logo shop trên PC
 */
export function pcLogoSave(data) {
  return request({
    url: `setting/config/save_basics`,
    method: 'POST',
    data,
  });
}
/**
 * @description Lấy chính sách bảo mật
 * @param data
 */
export function getAgreement() {
  return request({
    url: 'setting/get_user_agreement',
    method: 'get',
  });
}

/**
 * @description Đặt chính sách bảo mật
 * @param data
 */
export function setAgreement(data) {
  return request({
    url: 'setting/set_user_agreement',
    method: 'post',
    data,
  });
}

/**
 * @description Lấy thỏa thuận
 * @param data
 */
export function getAgreements(type) {
  return request({
    url: `setting/get_agreement/${type}`,
    method: 'get',
  });
}
/**
 * @description Đặt chính sách bảo mật
 * @param data
 */
export function setAgreements(data, type) {
  return request({
    url: `setting/save_agreement`,
    method: 'post',
    data,
  });
}

/**
 * @description Lấy sản phẩm được cấp quyền
 */
export function crmebProduct(params) {
  return request({
    url: 'crmeb_product',
    method: 'get',
    params,
  });
}

/**
 * @description Lấy đơn hàng được cấp quyền
 */
export function getVersion() {
  return request({
    url: `setting/get_version`,
    method: 'get',
  });
}

/**
 * @description Lấy bản quyền
 */
export function getCrmebCopyRight() {
  return request({
    url: `copyright`,
    method: 'get',
  });
}

/**
 * @description Lưu bản quyền
 */
export function saveCrmebCopyRight(data) {
  return request({
    url: `copyright`,
    method: 'post',
    data,
  });
}

/**
 * @description Gói nâng cấp -- Danh sách
 * @param data
 */
export function upgradeListApi(params) {
  return request({
    url: '/system/upgrade/list',
    method: 'get',
    params,
  });
}

/**
 * @description Tiến trình nâng cấp
 */
export function upgradeProgressApi() {
  return request({
    url: `/system/upgrade_progress`,
    method: 'get',
  });
}

/**
 * @description Thỏa thuận nâng cấp
 */
export function upgradeAgreementApi() {
  return request({
    url: `/system/upgrade/agreement`,
    method: 'get',
  });
}

/**
 * @description Trạng thái nâng cấp
 */
export function upgradeStatusApi() {
  return request({
    url: `/system/upgrade_status`,
    method: 'get',
  });
}

/**
 * @description Tiến độ tải xuống
 */
export function downloadProgressApi(data) {
  return request({
    url: `/system/upgrade_download/progress`,
    method: 'get',
    params: data,
  });
}

export function upgradeIgnoreFileApi() {
  return request({
    url: `/system/upgrade/ignore_file`,
    method: 'get',
  });
}

/**
 * @description Gói nâng cấp -- Lịch sử nâng cấp
 * @param data
 */
export function upgradeLogListApi(params) {
  return request({
    url: '/system/upgrade_log/list',
    method: 'get',
    params,
  });
}

/**
 * Xuất file sao lưu
 */
export function upgradeExportApi(id) {
  return request({
    url: `system/upgrade_export/${id}`,
    method: 'get',
    responseType: 'blob',
  });
}

/**
 * @description Tải gói nâng cấp
 */
export function downloadApi(params) {
  return request({
    url: '/system/package_download/' + params,
    method: 'POST',
  });
}

/**
 * @description Gói nâng cấp -- Danh sách có thể nâng cấp
 * @param data
 */
export function upgradeableListApi(params) {
  return request({
    url: '/system/upgradeable/list',
    method: 'get',
    params,
  });
}

/**
 * Danh sách tác vụ định kỳ
 * @param {*} params
 * @returns
 */
export function timerIndex(params) {
  return request({
    url: `system/crontab/list`,
    params,
  });
}

/**
 * Sửa trạng thái tác vụ định kỳ
 * @param {*} params
 * @returns
 */
export function showTimer(id, is_open) {
  return request({
    url: `system/crontab/set_open/${id}/${is_open}`,
  });
}

/**
 * Lấy thông tin tác vụ định kỳ
 * @param {*} params
 * @returns
 */
export function timerInfo(id) {
  return request({
    url: `system/crontab/info/${id}`,
  });
}

/**
 * Lưu tác vụ định kỳ
 * @param {*} data
 * @returns
 */
export function saveTimer(data) {
  return request({
    url: `system/crontab/save`,
    method: 'post',
    data,
  });
}

/**
 * Cập nhật tác vụ định kỳ
 * @param {*} id
 * @param {*} data
 * @returns
 */
export function updateTimer(id, data) {
  return request({
    url: `system/crontab/update/${id}`,
    method: 'post',
    data,
  });
}
/**
 * Cập nhật ghi chú
 * @param {*} data
 * @returns
 */
export function updateMark(data) {
  return request({
    url: `system/database/update_mark`,
    method: 'post',
    data,
  });
}
/**
 * Quản lý file, Cập nhật ghi chú
 * @param {*} data
 * @returns
 */
export function markSave(fileToken, data) {
  return request({
    url: `system/file/mark/save?fileToken=${fileToken}`,
    method: 'post',
    data,
  });
}

/**
 * Tên và mã định danh tác vụ định kỳ
 * @returns
 */
export function timerTask() {
  return request({
    url: `system/crontab/mark`,
  });
}

// ----Sự kiện tùy chỉnh

/**
 * Danh sách sự kiện tùy chỉnh
 * @param {*} params
 * @returns
 */
export function eventIndex(params) {
  return request({
    url: `system/event/list`,
    params,
  });
}

/**
 * Sửa trạng thái sự kiện tùy chỉnh
 * @param {*} params
 * @returns
 */
export function eventShowTimer(id, is_open) {
  return request({
    url: `system/event/set_open/${id}/${is_open}`,
  });
}

/**
 * Thông tin sự kiện tùy chỉnh
 * @param {*} params
 * @returns
 */
export function eventInfo(id) {
  return request({
    url: `system/event/info/${id}`,
  });
}

/**
 * Lưu sự kiện tùy chỉnh
 * @param {*} data
 * @returns
 */
export function eventSave(data) {
  return request({
    url: `system/event/save`,
    method: 'post',
    data,
  });
}
/**
 * Cập nhật sự kiện tùy chỉnh
 * @returns
 */
export function eventTask() {
  return request({
    url: `system/event/mark`,
  });
}

/**
 * Thông tin danh sách module bản quyền
 * @returns
 */
export function copyrightList() {
  return request({
    url: `system/info`,
  });
}

// ==================== API nâng cấp vượt phiên bản ====================

/**
 * Kiểm tra nâng cấp vượt phiên bản
 * @returns
 */
export function checkCrossVersionUpgradeApi() {
  return request({
    url: 'system/cross_version/check',
    method: 'get',
  });
}

/**
 * Lấy danh sách SQL nâng cấp đang chờ thực thi
 * @returns
 */
export function pendingSqlListApi() {
  return request({
    url: 'system/cross_version/pending_sql',
    method: 'get',
  });
}

/**
 * Thực hiện nâng cấp vượt phiên bản (từng bước)
 * @param {Number} step Chỉ số bước
 * @returns
 */
export function executeCrossVersionApi(step) {
  return request({
    url: 'system/cross_version/execute',
    method: 'post',
    data: { step },
  });
}

/**
 * Thực thi toàn bộ nâng cấp vượt phiên bản bằng một cú nhấp
 * @returns
 */
export function executeAllCrossVersionApi() {
  return request({
    url: 'system/cross_version/execute_all',
    method: 'post',
  });
}

/**
 * Lấy tiến trình nâng cấp vượt phiên bản
 * @returns
 */
export function crossVersionUpgradeProgressApi() {
  return request({
    url: 'system/cross_version/progress',
    method: 'get',
  });
}

/**
 * Lấy trạng thái sao lưu
 * @returns
 */
export function backupStatusApi() {
  return request({
    url: 'system/cross_version/backup_status',
    method: 'get',
  });
}

/**
 * Lấy danh sách phiên bản có thể quay lại (rollback)
 * @returns
 */
export function rollbackVersionsApi() {
  return request({
    url: 'system/rollback/versions',
    method: 'get',
  });
}
/**
 * Thực hiện lại nâng cấp
 * @returns
 */
export function reExecuteUpgradeApi(data) {
  return request({
    url: 'system/upgrade/reExecute',
    method: 'get',
    params: data,
  });
}

/**
 * Thực hiện quay lại phiên bản
 * @param {Number} logId ID nhật ký nâng cấp
 * @returns
 */
export function executeRollbackApi(logId) {
  return request({
    url: 'system/rollback/execute',
    method: 'post',
    data: { log_id: logId },
  });
}
