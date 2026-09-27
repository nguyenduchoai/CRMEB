// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import LayoutMain from '@/layout';
import setting from '@/setting';
let routePre = setting.routePre;

const pre = 'order_';

export default {
  path: routePre + '/order',
  name: 'order',
  header: 'order',
  redirect: {
    name: `${pre}list`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'list',
      name: `${pre}list`,
      meta: {
        auth: ['admin-order-storeOrder-index'],
        title: 'Quản lý đơn hàng',
      },
      component: () => import('@/pages/order/orderList/index'),
    },
    {
      path: 'offline',
      name: `${pre}offline`,
      meta: {
        auth: ['admin-order-offline'],
        title: 'Đơn thu ngân',
      },
      component: () => import('@/pages/order/offline/index'),
    },
    {
      path: 'refund',
      name: `${pre}refund`,
      meta: {
        auth: ['admin-order-refund'],
        title: 'Đơn đổi trả',
      },
      component: () => import('@/pages/order/refund/index'),
    },
    {
      path: 'invoice/list',
      name: `${pre}invoice`,
      meta: {
        auth: ['admin-order-startOrderInvoice-index'],
        title: 'Quản lý hóa đơn',
      },
      component: () => import('@/pages/order/invoice/index'),
    },
  ],
};
