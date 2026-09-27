// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import setting from '@/setting';
let routePre = setting.routePre;
const pre = 'kefu_';

export default [
  // Đăng nhập
  {
    path: routePre + '/login',
    name: 'login',
    meta: {
      title: 'Đăng nhập',
      hideInMenu: true,
    },
    component: () => import('@/pages/account/login'),
  },
  {
    path: '/kefu',
    name: `${pre}index`,
    meta: {
      auth: true,
      title: 'Quản lý CSKH',
      kefu: true,
    },
    component: () => import('@/pages/kefu/index'),
  },
  // CSKH
  {
    path: routePre + '/kefu',
    name: `${pre}index`,
    meta: {
      auth: true,
      title: 'Quản lý CSKH',
      kefu: true,
    },
    redirect: {
      name: `setting_service`,
    },
    component: () => import('@/pages/kefu/index'),
  },
  {
    path: '/kefu/mobile_list',
    name: `${pre}mobile_list`,
    meta: {
      auth: true,
      title: 'Danh sách tin nhắn',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/chat_list'),
  },
  {
    path: '/kefu/mobile_chat',
    name: `${pre}mobile_chat`,
    meta: {
      auth: true,
      title: 'Chi tiết cuộc trò chuyện',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/index'),
  },
  {
    path: '/kefu/pc_list',
    name: `${pre}pc_list`,
    meta: {
      auth: true,
      title: 'CSKH',
      kefu: true,
    },
    component: () => import('@/pages/kefu/pc/index'),
  },
  {
    path: '/kefu/orderList/:type?/:toUid?',
    name: `${pre}order-list`,
    meta: {
      auth: true,
      title: 'Danh sách đơn hàng',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/orderList/index'),
  },
  {
    path: '/kefu/orderDetail/:id?/:goname?',
    name: `${pre}order-detail`,
    meta: {
      auth: true,
      title: 'Chi tiết đơn hàng',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/orderList/orderDetail.vue'),
  },
  {
    path: '/kefu/orderDelivery/:id?/:orderId?',
    name: `${pre}order-delivery`,
    meta: {
      auth: true,
      title: 'Giao hàng',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/orderList/orderDelivery.vue'),
  },
  {
    path: '/kefu/user/index/:uid?/:type?',
    name: `${pre}user-index`,
    meta: {
      auth: true,
      title: 'Thông tin khách hàng',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/user/index'),
  },
  {
    path: '/kefu/goods/list',
    name: `${pre}goods-list`,
    meta: {
      auth: true,
      title: 'Danh sách sản phẩm',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/goods/list.vue'),
  },
  {
    path: '/kefu/goods/detail',
    name: `${pre}goods-detail`,
    meta: {
      auth: true,
      title: 'Danh sách sản phẩm',
      kefu: true,
    },
    component: () => import('@/pages/kefu/mobile/goods/detail.vue'),
  },
  {
    path: '/kefu/appChat',
    name: `${pre}app-chat`,
    meta: {
      auth: true,
      title: 'CSKH',
      kefu: true,
    },
    component: () => import('@/pages/kefu/appChat/index'),
  },
  {
    path: '/kefu/mobile_user_chat',
    name: `${pre}app-mobile_user_chat`,
    meta: {
      auth: true,
      title: 'CSKH người dùng',
      kefu: true,
    },
    component: () => import('@/pages/kefu/appChat/mobile/index'),
  },
  {
    path: '/kefu/mobile_feedback',
    name: `${pre}app-mobile_feedback`,
    meta: {
      auth: true,
      title: 'Phản hồi người dùng',
      kefu: true,
    },
    component: () => import('@/pages/kefu/appChat/mobile/feedback'),
  },
  {
    path: '/app/upload',
    name: `mobile_upload`,
    meta: {
      auth: true,
      title: 'Quét mã bằng điện thoại để tải lên',
      kefu: true,
    },
    component: () => import('@/pages/app/upload'),
  },
  {
    path: routePre + '/order/print',
    name: `order-print-print`,
    meta: {
      title: 'In phiếu soạn hàng',
    },
    component: () => import('@/pages/order/print/index'),
  },
];
