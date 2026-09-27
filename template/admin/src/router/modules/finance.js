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

const pre = 'finance_';
export default {
  path: routePre + '/finance',
  name: 'finance',
  header: 'finance',
  meta: {
    // Mã định danh ủy quyền
    auth: ['admin-finance'],
  },
  redirect: {
    name: `${pre}cashApply`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'billing_records/index',
      name: `${pre}billingRecords`,
      meta: {
        auth: ['finance-billing_records-index'],
        title: 'Lịch sử sao kê',
      },
      component: () => import('@/pages/finance/billingRecords/index'),
    },
    {
      path: 'capital_flow/index',
      name: `${pre}capitalFlow`,
      meta: {
        auth: ['finance-capital_flow-index'],
        title: 'Dòng tiền',
      },
      component: () => import('@/pages/finance/capitalFlow/index'),
    },
    {
      path: 'user_extract/index',
      name: `${pre}cashApply`,
      meta: {
        auth: ['finance-user_extract'],
        title: 'Yêu cầu rút tiền',
      },
      component: () => import('@/pages/finance/userExtract/index'),
    },
    {
      path: 'user_recharge/index',
      name: `${pre}recharge`,
      meta: {
        auth: ['finance-user-recharge'],
        title: 'Lịch sử nạp tiền',
      },
      component: () => import('@/pages/finance/financialRecords/recharge'),
    },
    {
      path: 'finance/bill',
      name: `${pre}bill`,
      meta: {
        auth: ['finance-finance-bill'],
        title: 'Lịch sử dòng tiền',
      },
      component: () => import('@/pages/finance/financialRecords/bill'),
    },
    {
      path: 'finance/commission',
      name: `${pre}commissionRecord`,
      meta: {
        auth: ['finance-finance-commission'],
        title: 'Lịch sử hoa hồng',
      },
      component: () => import('@/pages/finance/commission/index'),
    },
    {
      path: 'balance/balance',
      name: `${pre}balance`,
      meta: {
        auth: ['finance-user-balance'],
        title: 'Lịch sử số dư',
      },
      component: () => import('@/pages/finance/balance/index'),
    },
  ],
};
