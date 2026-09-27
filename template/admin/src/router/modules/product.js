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

const pre = 'product_';

export default {
  path: routePre + '/product',
  name: 'product',
  header: 'product',
  meta: {
    title: 'Sản phẩm',
    // Mã định danh ủy quyền
    auth: ['admin-store-index'],
  },
  redirect: {
    name: `${pre}productList`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'product_list',
      name: `${pre}productList`,
      meta: {
        title: 'Quản lý sản phẩm',
        auth: ['admin-store-storeProuduct-index'],
        keepAlive: true,
      },
      component: () => import('@/pages/product/productList'),
    },
    {
      path: 'product_classify',
      name: `${pre}productClassify`,
      meta: {
        title: 'Danh mục sản phẩm',
        auth: ['admin-store-storeCategory-index'],
      },
      component: () => import('@/pages/product/productClassify'),
    },
    {
      path: 'add_product/:id?',
      name: `${pre}productAdd`,
      meta: {
        auth: ['admin-store-storeProuduct-index'],
        title: 'Thêm sản phẩm',
        activeMenu: routePre + '/product/product_list',
      },
      component: () => import('@/pages/product/productAdd'),
    },
    {
      path: 'product_reply/:id?',
      name: `${pre}productEvaluate`,
      meta: {
        auth: ['admin-store-storeProuduct-index'],
        title: 'Đánh giá sản phẩm',
      },
      component: () => import('@/pages/product/productReply'),
    },
    {
      path: 'product_attr',
      name: `${pre}productAttr`,
      meta: {
        auth: ['admin-store-storeProuduct-index'],
        title: 'Quy cách sản phẩm',
      },
      component: () => import('@/pages/product/productAttr'),
    },
    {
      path: 'param/list',
      name: `${pre}paramList`,
      meta: {
        auth: ['admin-product-param-list'],
        title: 'Thông số sản phẩm',
      },
      component: () => import('@/pages/product/paramList'),
    },
    {
      path: 'label/list',
      name: `${pre}labelList`,
      meta: {
        auth: ['admin-product-label-list'],
        title: 'Nhãn sản phẩm',
      },
      component: () => import('@/pages/product/labelList'),
    },
    {
      path: 'protection/list',
      name: `${pre}labelList`,
      meta: {
        auth: ['admin-product-protection-list'],
        title: 'Đảm bảo sản phẩm',
      },
      component: () => import('@/pages/product/protectionList'),
    },
  ],
};
