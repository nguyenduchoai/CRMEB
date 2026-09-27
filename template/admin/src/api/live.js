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
 * @description Danh sách livestream
 */
export function liveList(params) {
  return request({
    url: 'live/room/list',
    method: 'get',
    params,
  });
}

/**
 * @description Danh sách livestream
 */
export function liveAdd(data) {
  return request({
    url: 'live/room/add',
    method: 'post',
    data,
  });
}

/**
 * @description Chi tiết danh sách livestream
 */
export function liveDetail(id) {
  return request({
    url: 'live/room/detail/' + id,
    method: 'get',
  });
}

/**
 * @description Thiết lập hiển thị phòng livestream
 */
export function liveShow(id, type) {
  return request({
    url: `live/room/set_show/${id}/${type}`,
    method: 'get',
  });
}

/**
 * @description Danh sách sản phẩm livestream
 */
export function liveGoods(params) {
  return request({
    url: 'live/goods/list',
    method: 'get',
    params,
  });
}

/**
 * @description Danh sách sản phẩm livestream, tạo sản phẩm livestream
 */
export function liveGoodsCreat(data) {
  return request({
    url: 'live/goods/create',
    method: 'post',
    data,
  });
}

/**
 * @description Thêm vào danh sách sản phẩm livestream
 */
export function liveGoodsAdd(data) {
  return request({
    url: 'live/goods/add',
    method: 'post',
    data,
  });
}

/**
 * @description Thêm sản phẩm vào phòng livestream
 */
export function liveRoomGoodsAdd(data) {
  return request({
    url: 'live/room/add_goods',
    method: 'post',
    data,
  });
}

/**
 * @description Đồng bộ phòng livestream
 */
export function liveSyncRoom() {
  return request({
    url: 'live/room/syncRoom',
    method: 'get',
  });
}

/**
 * @description Đồng bộ sản phẩm
 */
export function liveSyncGoods() {
  return request({
    url: 'live/goods/syncGoods',
    method: 'get',
  });
}

/**
 * @description Danh sách streamer
 */
export function liveAuchorList(params) {
  return request({
    url: 'live/anchor/list',
    method: 'get',
    params,
  });
}

/**
 * @description Lấy form thêm/sửa streamer (người livestream)
 */
export function liveAuchorAdd(id) {
  return request({
    url: 'live/anchor/add/' + id,
    method: 'get',
  });
}

/**
 * @description Chi tiết sản phẩm livestream
 */
export function liveGoodsDetail(id) {
  return request({
    url: 'live/goods/detail/' + id,
    method: 'get',
  });
}

/**
 * @description Hiển thị sản phẩm livestream
 */
export function liveGoodsShow(id, type) {
  return request({
    url: `live/goods/set_show/${id}/${type}`,
    method: 'get',
  });
}
