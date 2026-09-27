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

const pre = 'marketing_';

export default {
  path: routePre + '/marketing',
  name: 'marketing',
  header: 'marketing',
  redirect: {
    name: `${pre}storeCouponIssue`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'store_combination/index',
      name: `${pre}combinalist`,
      meta: {
        auth: ['marketing-store_combination'],
        title: 'Sản phẩm mua chung',
        keepAlive: true,
      },
      component: () => import('@/pages/marketing/storeCombination/index'),
    },
    {
      path: 'store_combination/combina_list',
      name: `${pre}combinaList`,
      meta: {
        auth: ['marketing-store_combination-combina_list'],
        title: 'Danh sách mua chung',
      },
      component: () => import('@/pages/marketing/storeCombination/combinaList'),
    },
    {
      path: 'store_combination/create/:id?/:copy?',
      name: `${pre}storeCombinationCreate`,
      meta: {
        auth: ['marketing-store_combination-create'],
        title: 'Thêm mua chung',
        activeMenu: routePre + '/marketing/store_combination/index',
      },
      component: () => import('@/pages/marketing/storeCombination/create'),
    },
    {
      path: 'store_combination/statistics/:id?',
      name: `${pre}storeCombinationStatistics`,
      meta: {
        title: 'Thống kê mua chung',
        activeMenu: routePre + '/marketing/store_combination/index',
      },
      component: () => import('@/pages/marketing/storeCombination/statistics'),
    },
    {
      path: 'store_coupon/index',
      name: `${pre}storeCoupon`,
      meta: {
        auth: ['marketing-store_coupon'],
        title: 'Mẫu phiếu giảm giá',
      },
      component: () => import('@/pages/marketing/storeCoupon/index'),
    },
    {
      path: 'store_coupon_issue/index',
      name: `${pre}storeCouponIssue`,
      meta: {
        auth: ['marketing-store_coupon_issue'],
        title: 'Danh sách phiếu giảm giá',
        keepAlive: true,
      },
      component: () => import('@/pages/marketing/storeCouponIssue/index'),
    },
    {
      path: 'store_coupon_issue/create/:id?/:edit?',
      name: `${pre}storeCouponCreate`,
      meta: {
        auth: ['marketing-store_coupon_issue-create'],
        title: 'Thêm phiếu giảm giá',
        activeMenu: routePre + '/marketing/store_coupon_issue/index',
      },
      component: () => import('@/pages/marketing/storeCouponIssue/create'),
    },
    {
      path: 'store_coupon_user/index',
      name: `${pre}storeCouponUser`,
      meta: {
        auth: ['marketing-store_coupon_user'],
        title: 'Lịch sử nhận của người dùng',
      },
      component: () => import('@/pages/marketing/storeCouponUser/index'),
    },
    {
      path: 'coupon/system_config/:type?/:tab_id?',
      name: `${pre}coupon`,
      meta: {
        auth: ['admin-order-storeOrder-index'],
        title: 'Cấu hình phiếu giảm giá',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'store_bargain/index',
      name: `${pre}storeBargain`,
      meta: {
        auth: ['marketing-store_bargain'],
        title: 'Sản phẩm săn giảm giá',
        keepAlive: true,
      },
      component: () => import('@/pages/marketing/storeBargain/index'),
    },
    {
      path: 'store_bargain/bargain_list',
      name: `${pre}bargainList`,
      meta: {
        auth: ['marketing-store_bargain-bargain_list'],
        title: 'Danh sách săn giảm giá',
      },
      component: () => import('@/pages/marketing/storeBargain/bargainList'),
    },
    {
      path: 'store_bargain/create/:id?/:copy?',
      name: `${pre}bargainCreate`,
      meta: {
        auth: ['marketing-store_bargain-create'],
        title: 'Thêm săn giảm giá',
        activeMenu: routePre + '/marketing/store_bargain/index',
      },
      component: () => import('@/pages/marketing/storeBargain/create'),
    },
    {
      path: 'store_bargain/statistics/:id?',
      name: `${pre}storeBargainStatistics`,
      meta: {
        title: 'Thống kê săn giảm giá',
        activeMenu: routePre + '/marketing/store_bargain/index',
      },
      component: () => import('@/pages/marketing/storeBargain/statistics'),
    },
    {
      path: 'store_seckill/index',
      name: `${pre}storeSeckill`,
      meta: {
        auth: ['marketing-store_seckill'],
        title: 'Sản phẩm flash sale',
        keepAlive: true,
      },
      component: () => import('@/pages/marketing/storeSeckill/index'),
    },
    {
      path: 'store_seckill_data/index/:id',
      name: `${pre}storeSeckillData`,
      meta: {
        auth: ['marketing-store_seckill-data'],
        title: 'Cấu hình flash sale',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'store_seckill/create/:id?/:copy?',
      name: `${pre}storeSeckillCreate`,
      meta: {
        auth: ['marketing-store_seckill-create'],
        title: 'Thêm flash sale',
        activeMenu: routePre + '/marketing/store_seckill/list',
      },
      component: () => import('@/pages/marketing/storeSeckill/create'),
    },
    {
      path: 'store_seckill/create_more/:id?/:copy?',
      name: `${pre}storeSeckillCreate`,
      meta: {
        auth: ['marketing-store_seckill-create-more'],
        title: 'Thêm flash sale',
        activeMenu: routePre + '/marketing/store_seckill/list',
      },
      component: () => import('@/pages/marketing/storeSeckill/createMore'),
    },
    {
      path: 'store_seckill/list',
      name: `${pre}marketing-store_seckill-list`,
      meta: {
        title: 'Danh sách flash sale',
      },
      component: () => import('@/pages/marketing/storeSeckill/list'),
    },
    {
      path: 'store_seckill/statistics/:id?',
      name: `${pre}storeSeckillStatistics`,
      meta: {
        title: 'Thống kê flash sale',
        activeMenu: routePre + '/marketing/store_seckill/index',
      },
      component: () => import('@/pages/marketing/storeSeckill/statistics'),
    },
    {
      path: `integral/system_config/:type?/:tab_id?`,
      name: `${pre}integral`,
      meta: {
        auth: ['marketing-integral-system_config'],
        title: 'Cấu hình điểm thưởng',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: `model/system_config/:type?/:tab_id?`,
      name: `${pre}model`,
      meta: {
        auth: ['system-model-system_config'],
        title: 'Cấu hình mô-đun',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'store_integral/index',
      name: `${pre}storeIntegral`,
      meta: {
        auth: ['marketing-store_integral'],
        title: 'Sản phẩm đổi điểm',
        keepAlive: true,
      },
      component: () => import('@/pages/marketing/storeIntegral/index'),
    },
    {
      path: 'store_integral/create/:id?/:copy?',
      name: `${pre}storeIntegralCreate`,
      meta: {
        auth: ['marketing-store_integral-create'],
        title: 'Thêm sản phẩm đổi điểm',
        activeMenu: routePre + '/marketing/store_integral/index',
      },
      component: () => import('@/pages/marketing/storeIntegral/create'),
    },
    {
      path: 'store_integral/order_list',
      name: `${pre}storeIntegralOrder`,
      meta: {
        auth: ['marketing-store_integral-order'],
        title: 'Đơn đổi điểm',
      },
      component: () => import('@/pages/marketing/storeIntegralOrder/index'),
    },
    {
      path: 'user_point/index',
      name: `${pre}userPoint`,
      meta: {
        auth: ['marketing-user_point'],
        title: 'Nhật ký điểm thưởng',
      },
      component: () => import('@/pages/marketing/userPoint/index'),
    },
    {
      path: 'live/live_room',
      name: `${pre}live_room`,
      meta: {
        auth: true,
        title: 'Quản lý phòng livestream',
      },
      component: () => import('@/pages/marketing/live/index'),
    },
    {
      path: 'live/add_live_room',
      name: `${pre}add_live_room`,
      meta: {
        auth: true,
        title: 'Quản lý phòng livestream',
        activeMenu: routePre + '/marketing/live/live_room',
      },
      component: () => import('@/pages/marketing/live/creat_live'),
    },
    {
      path: 'live/live_goods',
      name: `${pre}live_goods`,
      meta: {
        auth: true,
        title: 'Quản lý sản phẩm phòng livestream',
      },
      component: () => import('@/pages/marketing/live/live_goods'),
    },
    {
      path: 'live/add_live_goods',
      name: `${pre}add_live_goods`,
      meta: {
        auth: true,
        title: 'Quản lý sản phẩm phòng livestream',
        activeMenu: routePre + '/marketing/live/live_goods',
      },
      component: () => import('@/pages/marketing/live/add_goods'),
    },
    {
      path: 'live/anchor',
      name: `${pre}anchor`,
      meta: {
        auth: true,
        title: 'Quản lý streamer',
      },
      component: () => import('@/pages/marketing/live/anchor'),
    },
    {
      path: 'presell/index',
      name: `${pre}storePresell`,
      meta: {
        auth: ['marketing-presell'],
        title: 'Sản phẩm đặt trước',
      },
      component: () => import('@/pages/marketing/storePresell/index'),
    },
    {
      path: 'presell/presell_list',
      name: `${pre}presellList`,
      meta: {
        auth: ['marketing-presell-presell_list'],
        title: 'Danh sách đặt trước',
      },
      component: () => import('@/pages/marketing/storePresell/presellList'),
    },
    {
      path: 'presell/create/:id?/:copy?',
      name: `${pre}storePresellCreate`,
      meta: {
        auth: ['marketing-presell-create'],
        title: 'Thêm sản phẩm đặt trước',
      },
      component: () => import('@/pages/marketing/storePresell/create'),
    },
    {
      path: 'lottery/index',
      name: `${pre}lottery`,
      meta: {
        auth: true,
        title: 'Danh sách quay thưởng',
      },
      component: () => import('@/pages/marketing/lottery/index'),
    },
    {
      path: 'lottery/create',
      name: `${pre}create`,
      meta: {
        auth: true,
        title: 'Tạo chương trình quay thưởng',
        activeMenu: routePre + '/marketing/lottery/list',
      },
      component: () => import('@/pages/marketing/lottery/create'),
    },
    {
      path: 'lottery/recording_list',
      name: `${pre}recording_list`,
      meta: {
        auth: true,
        title: 'Lịch sử quay thưởng',
        activeMenu: routePre + '/marketing/lottery/list',
      },
      component: () => import('@/pages/marketing/lottery/recordingList'),
    },
    {
      path: 'lottery/config',
      name: `${pre}lottery_config`,
      meta: {
        auth: ['admin-marketing-lottery-config'],
        title: 'Cấu hình quay thưởng',
      },
      component: () => import('@/pages/marketing/lottery/config'),
    },
    {
      path: 'lottery/list',
      name: `${pre}list`,
      meta: {
        auth: true,
        title: 'Danh sách quay thưởng',
      },
      component: () => import('@/pages/marketing/lottery/lotteryList'),
    },
    {
      path: 'channel_code/channelCodeIndex',
      name: `${pre}channel_code`,
      meta: {
        auth: true,
        title: 'Mã kênh OA WeChat',
        keepAlive: true,
      },
      component: () => import('@/pages/marketing/channelCode/channelCodeIndex'),
    },
    {
      path: 'channel_code/create',
      name: `${pre}create_code`,
      meta: {
        auth: ['marketing-channel_code-create'],
        title: 'Mã kênh',
        activeMenu: routePre + '/marketing/channel_code/channelCodeIndex',
      },
      component: () => import('@/pages/marketing/channelCode/createCode'),
    },
    {
      path: 'channel_code/code_statistic',
      name: `${pre}code_statistic`,
      meta: {
        auth: ['marketing-channel_code-statistic'],
        title: 'Thống kê mã QR',
        activeMenu: routePre + '/marketing/channel_code/channelCodeIndex',
      },
      component: () => import('@/pages/marketing/channelCode/codeStatistic'),
    },
    {
      path: 'point_record',
      name: `${pre}point_record`,
      meta: {
        auth: ['marketing-point_record-index'],
        title: 'Lịch sử điểm thưởng',
      },
      component: () => import('@/pages/marketing/point_record/index'),
    },
    {
      path: 'point_statistic',
      name: `${pre}point_statistic`,
      meta: {
        auth: ['marketing-point_statistic-index'],
        title: 'Thống kê điểm thưởng',
      },
      component: () => import('@/pages/marketing/point_statistic/index'),
    },
    {
      path: 'recharge',
      name: `${pre}recharge`,
      meta: {
        title: 'Cấu hình nạp tiền',
      },
      component: () => import('@/pages/marketing/recharge/index'),
    },
    {
      path: 'sign',
      name: `${pre}sign`,
      meta: {
        title: 'Cấu hình điểm danh',
      },
      component: () => import('@/pages/marketing/sign/index'),
    },
    {
      path: 'sign_rewards',
      name: `${pre}sign_rewards`,
      meta: {
        title: 'Phần thưởng điểm danh',
      },
      component: () => import('@/pages/marketing/sign/rewards'),
    },
    {
      path: `member_config/:type?/:tab_id?`,
      name: `${pre}member_config`,
      meta: {
        title: 'Cấu hình thành viên',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: `newuser/gift`,
      name: `${pre}gift`,
      meta: {
        title: 'Quà tặng người mới',
        auth: ['admin-marketing-new-user-gift'],
      },
      component: () => import('@/pages/marketing/newuser/gift'),
    },
  ],
};
