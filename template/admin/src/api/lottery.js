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
 * @description Vòng quay 9 ô -- Danh sách
 */
export function lotteryListApi(data) {
  return request({
    url: 'marketing/lottery/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Vòng quay 9 ô -- Chi tiết
 * @param id ID chương trình quay thưởng
 */
export function lotteryDetailApi(id) {
  return request({
    url: `marketing/lottery/detail/${id}`,
    method: 'get',
  });
}

/**
 * @description Vòng quay 9 ô -- Chi tiết bản mới
 * @param id ID chương trình quay thưởng
 */
export function lotteryNewDetailApi(type) {
  return request({
    url: `marketing/lottery/factor_info/${type}`,
    method: 'get',
  });
}

/**
 * @description Vòng quay 9 ô -- Tạo mới
 */
export function lotteryCreateApi(data) {
  return request({
    url: `marketing/lottery/add`,
    method: 'post',
    data,
  });
}
/**
 **
 * @description Vòng quay 9 ô -- Sửa/chỉnh sửa
 */
export function lotteryEditApi(id, data) {
  return request({
    url: `marketing/lottery/edit/${id}`,
    method: 'put',
    data,
  });
}

/**
 **
 * @description Vòng quay 9 ô -- Xóa
 */
export function lotteryDelApi(id) {
  return request({
    url: `marketing/lottery/del/${id}`,
    method: 'delete',
  });
}

/**
 **
 * @description Vòng quay 9 ô -- Trạng thái hiển thị
 */
export function lotteryStatusApi(data) {
  return request({
    url: `marketing/lottery/set_status/${data.id}/${data.status}`,
    method: 'post',
  });
}

/**
 **
 * @description Vòng quay 9 ô -- Lịch sử trúng thưởng
 */
export function lotteryRecordList(data) {
  return request({
    url: `marketing/lottery/record/list`,
    method: 'get',
    params: data,
  });
}

/**
 **
 * @description Vòng quay 9 ô -- Giao hàng trúng thưởng / xử lý ghi chú
 */
export function lotteryRecordDeliver(data) {
  return request({
    url: `marketing/lottery/record/deliver`,
    method: 'post',
    data,
  });
}

/**
 **
 * @description Danh sách quay thưởng
 */
export function lotteryList(data) {
  return request({
    url: `marketing/lottery/list`,
    method: 'get',
    params: data,
  });
}
/**
 **
 * @description Lấy loại quay thưởng
 */
export function factorListApi(data) {
  return request({
    url: `marketing/lottery/factor/list`,
    method: 'get',
  });
}

/**
 **
 * @description Lưu cấu hình quay thưởng
 */
export function factorUseApi(data) {
  return request({
    url: `marketing/lottery/factor/use`,
    method: 'post',
    data,
  });
}

/**
 * @description Chuyển trạng thái quay thưởng
 * @param data {Object} Truyền giá trị
 */
export function lotteryStatus(data) {
  return request({
    url: `marketing/lottery/set_status/${data.id}/${data.status}`,
    method: 'put',
  });
}
