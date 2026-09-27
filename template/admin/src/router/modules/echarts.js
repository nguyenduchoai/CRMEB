/*
 * @Author: From-wh from-wh@hotmail.com
 * @Date: 2023-02-21 09:14:27
 * @FilePath: /admin/src/router/modules/echarts.js
 * @Description:
 */
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

const pre = 'echarts_';

export default {
  path: routePre + '/echarts',
  name: 'echarts',
  header: 'echarts',
  redirect: {
    name: `${pre}/trade/order`,
  },
  component: LayoutMain,
  children: [
    // {
    //   path: 'trade/order',
    //   name: `${pre}/trade/order`,
    //   meta: {
    //     auth: ['admin-order-storeOrder-index'],
    //     title: 'Thống kê giao dịch',
    //   },
    //   component: () => import('@/pages/echarts/trade/order'),
    // },
    // {
    //   path: 'trade/product',
    //   name: `${pre}/trade/product`,
    //   meta: {
    //     auth: ['admin-order-storeOrder-index'],
    //     title: 'Thống kê sản phẩm',
    //   },
    //   component: () => import('@/pages/echarts/trade/product'),
    // },
  ],
};
