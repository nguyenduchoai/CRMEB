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

const pre = 'division_';
const meta = {
  auth: true,
};
export default {
  path: routePre + '/division',
  name: 'division',
  header: 'division',
  redirect: {
    name: `${pre}division`,
  },
  meta,
  component: LayoutMain,
  children: [
    {
      path: 'index',
      name: `${pre}division`,
      meta: {
        auth: ['agent-division-index'],
        title: 'Danh sách đại lý khu vực',
      },
      component: () => import('@/pages/division/list/index'),
    },
    
    {
      path: 'agent/index',
      name: `${pre}agent`,
      meta: {
        auth: ['agent-division-agent-index'],
        title: 'Danh sách đại lý',
      },
      component: () => import('@/pages/division/agent/index'),
    },
    {
      path: 'agent/statistics',
      name: `${pre}agent`,
      meta: {
        auth: ['agent-division-statistics'],
        title: 'Thống kê đại lý khu vực',
      },
      component: () => import('@/pages/division/agent/statistics'),
    },
    {
      path: 'agent/applyList',
      name: `${pre}agent`,
      meta: {
        auth: ['agent-division-agent-applyList'],
        title: 'Đăng ký đại lý',
      },
      component: () => import('@/pages/division/agent/applyList'),
    },
    {
      path: 'agent/agreement',
      name: `${pre}agent`,
      meta: {
        auth: ['agent-division-agent-agreement'],
        title: 'Quy định đại lý',
      },
      component: () => import('@/pages/division/agent/agreement'),
    },
  ],
};
