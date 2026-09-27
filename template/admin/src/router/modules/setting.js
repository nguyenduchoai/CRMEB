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

const meta = {
  auth: true,
};

const pre = 'setting_';

export default {
  path: routePre + '/setting',
  name: 'setting',
  header: 'setting',
  redirect: {
    name: `${pre}setSystem`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'system_role/index',
      name: `${pre}systemRole`,
      meta: {
        auth: ['setting-system-role'],
        title: 'Quản lý vai trò',
      },
      component: () => import('@/pages/setting/systemRole/index'),
    },
    {
      path: 'system_admin/index',
      name: `${pre}systemAdmin`,
      meta: {
        auth: ['setting-system-list'],
        title: 'Danh sách quản trị viên',
      },
      component: () => import('@/pages/setting/systemAdmin/index'),
    },
    {
      path: 'system_menus/index',
      name: `${pre}systemMenus`,
      meta: {
        auth: ['setting-system-menus'],
        title: 'Quy tắc quyền',
      },
      component: () => import('@/pages/setting/systemMenus/index'),
    },
    {
      path: 'system_config',
      name: `${pre}setSystem`,
      meta: {
        auth: ['setting-system-config'],
        title: 'Cài đặt hệ thống',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'system_config/:type?/:tab_id?',
      name: `${pre}setApp`,
      meta: {
        title: 'Cài đặt hệ thống',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'system_config_retail/:type?/:tab_id?',
      name: `${pre}distributionSet`,
      meta: {
        ...meta,
        title: 'Cấu hình tiếp thị liên kết',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'membership_level/index',
      name: `${pre}membershipLevel`,
      meta: {
        ...meta,
        title: 'Cấp độ CTV',
      },
      component: () => import('@/pages/setting/membershipLevel/index'),
    },
    {
      path: 'system_config_message/:type?/:tab_id?',
      name: `${pre}message`,
      meta: {
        auth: ['setting-system-config-message'],
        title: 'Bật/tắt SMS',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'notification/index',
      name: `${pre}notification`,
      meta: {
        auth: ['setting-notification'],
        title: 'Quản lý thông báo',
      },
      component: () => import('@/pages/setting/notification/index'),
    },
    {
      path: 'notification/notificationEdit',
      name: `${pre}notificationEdit`,
      meta: {
        auth: ['setting-notification'],
        title: 'Chỉnh sửa tin nhắn',
        activeMenu: routePre + '/setting/notification/index',
      },
      component: () => import('@/pages/setting/notification/notificationEdit'),
    },
    {
      path: 'system_config_logistics/:type?/:tab_id?',
      name: `${pre}logistics`,
      meta: {
        auth: ['setting-system-config-logistics'],
        title: 'Cấu hình vận chuyển',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'sms/sms_config/index',
      name: `${pre}config`,
      meta: {
        auth: ['setting-sms-sms-config'],
        title: 'Tài khoản Yihaotong',
      },
      component: () => import('@/pages/notify/smsConfig/index'),
    },
    {
      path: 'elec_invoice',
      name: `${pre}elec_invoice`,
      meta: {
        auth: ['setting-elec_invoice'],
        title: 'Cấu hình hóa đơn điện tử',
      },
      component: () => import('@/pages/notify/smsConfig/elecInvoice'),
    },
    {
      path: 'sms/sms_template_apply/index',
      name: `${pre}smsTemplateApply`,
      meta: {
        auth: ['setting-sms-config-template'],
        title: 'Mẫu SMS',
      },
      component: () => import('@/pages/notify/smsTemplateApply/index'),
    },
    {
      path: 'sms/sms_pay/index',
      name: `${pre}smsPay`,
      meta: {
        auth: ['setting-sms-sms-template'],
        title: 'Mua gói SMS',
      },
      component: () => import('@/pages/notify/smsPay/index'),
    },
    {
      path: 'sms/sms_template_apply/commons',
      name: `${pre}commons`,
      meta: {
        ...meta,
        title: 'Mẫu SMS chung',
      },
      component: () => import('@/pages/notify/smsTemplateApply/index'),
    },
    {
      path: 'system_group_data/index/:id',
      name: `${pre}groupDataIndex`,
      meta: {
        auth: ['setting-system-group_data-index'],
        title: 'Nút điều hướng trang chủ',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/slide/:id',
      name: `${pre}groupDataSlide`,
      meta: {
        auth: ['setting-system-group_data-slide'],
        title: 'Ảnh trình chiếu trang chủ',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/sign/:id',
      name: `${pre}groupDataSign`,
      meta: {
        auth: ['setting-system-group_data-sign'],
        title: 'Cấu hình số ngày điểm danh',
      },
      component: () => import('@/pages/system/group/list'),
    },
    // {
    //   path: 'system_group_data/order/:id',
    //   name: `${pre}groupDataOrder`,
    //   meta: {
    //     auth: ['setting-system-group_data-order'],
    //     title: 'Biểu đồ động chi tiết đơn hàng'
    //   },
    //   component: () => import('@/pages/system/group/list')
    // },
    // {
    //   path: 'system_group_data/user/:id',
    //   name: `${pre}groupDataUser`,
    //   meta: {
    //     auth: ['setting-system-group_data-user'],
    //     title: 'Menu trang cá nhân'
    //   },
    //   component: () => import('@/pages/system/group/list')
    // },
    {
      path: 'system_group_data/new/:id',
      name: `${pre}groupDataNew`,
      meta: {
        auth: ['setting-system-group_data-new'],
        title: 'Tin tức cuộn trang chủ',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/search/:id',
      name: `${pre}groupDataNew`,
      meta: {
        auth: ['setting-system-group_data-search'],
        title: 'Tìm kiếm phổ biến',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/hot/:id',
      name: `${pre}groupDataHot`,
      meta: {
        auth: ['setting-system-group_data-hot'],
        title: 'Gợi ý top bán chạy',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/new_product/:id',
      name: `${pre}groupDataNewProduct`,
      meta: {
        auth: ['setting-system-group_data-new_product'],
        title: 'Gợi ý hàng mới ra mắt',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/promotion/:id',
      name: `${pre}groupDataPromotion`,
      meta: {
        auth: ['setting-system-group_data-promotion'],
        title: 'Gợi ý sản phẩm khuyến mãi',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/poster/:id',
      name: `${pre}groupDataPoster`,
      meta: {
        auth: ['setting-system-group_data-poster'],
        title: 'Poster tiếp thị liên kết trang cá nhân',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/best/:id',
      name: `${pre}groupDataBest`,
      meta: {
        auth: ['setting-system-group_data-best'],
        title: 'Đề xuất nổi bật',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/activity/:id',
      name: `${pre}groupDataActivity`,
      meta: {
        auth: ['setting-system-group_data-activity'],
        title: 'Ảnh khu vực sự kiện trang chủ',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/system/:id',
      name: `${pre}groupDataSystem`,
      meta: {
        auth: ['setting-system-group_data-system'],
        title: 'Cấu hình trang chủ',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_group_data/hot_money/:id',
      name: `${pre}groupDataHotMoney`,
      meta: {
        auth: ['admin-setting-system_group_data-hot_money'],
        title: 'Hàng hot giá tốt trang chủ',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'merchant/system_store/index',
      name: `${pre}systemStore`,
      meta: {
        auth: ['setting-system-config-merchant'],
        title: 'Cài đặt cửa hàng',
      },
      component: () => import('@/pages/setting/systemStore/index'),
    },
    {
      path: 'freight/express/index',
      name: `${pre}freight`,
      meta: {
        auth: ['setting-freight-express'],
        title: 'Đơn vị vận chuyển',
      },
      component: () => import('@/pages/setting/freight/index'),
    },
    {
      path: 'store_service/index',
      name: `${pre}service`,
      meta: {
        auth: ['setting-store-service'],
        title: 'Quản lý CSKH',
      },
      component: () => import('@/pages/setting/storeService/index'),
    },
    {
      path: 'freight/city/list',
      name: `${pre}dada`,
      meta: {
        auth: ['setting-system-city'],
        title: 'Dữ liệu thành phố',
      },
      component: () => import('@/pages/setting/cityDada/index'),
    },
    {
      path: 'freight/shipping_templates/list',
      name: `${pre}templates`,
      meta: {
        auth: ['setting-shipping-templates'],
        title: 'Mẫu phí vận chuyển',
      },
      component: () => import('@/pages/setting/shippingTemplates/index'),
    },
    {
      path: 'merchant/system_store/list',
      name: `${pre}store`,
      meta: {
        auth: ['setting-merchant-system-store'],
        title: 'Điểm nhận hàng',
      },
      component: () => import('@/pages/setting/storeList/index'),
    },
    {
      path: 'merchant/system_store_staff/index',
      name: `${pre}staff`,
      meta: {
        auth: ['setting-merchant-system-store-staff'],
        title: 'Nhân viên xác nhận',
      },
      component: () => import('@/pages/setting/clerkList/index'),
    },
    {
      path: 'merchant/system_verify_order/index',
      name: `${pre}order`,
      meta: {
        auth: ['setting-merchant-system-verify-order'],
        title: 'Đơn hàng xác nhận sử dụng',
      },
      component: () => import('@/pages/setting/verifyOrder/index'),
    },
    {
      path: 'theme_style',
      name: `${pre}themeStyle`,
      meta: {
        auth: ['admin-setting-theme_style'],
        title: 'Chủ đề giao diện',
      },
      component: () => import('@/pages/setting/themeStyle/index'),
    },
    {
      path: 'theme/micro_page',
      name: `${pre}microPage`,
      meta: {
        auth: ['setting-theme-micro_page'],
        title: 'Trang tùy chỉnh',
      },
      component: () => import('@/pages/setting/theme/micro_page/index'),
    },
    {
      path: 'pages',
      name: `${pre}page`,
      header: 'setting',
      redirect: {
        name: `${pre}devise`,
      },
    },
    {
      path: 'pages/devise/:type',
      name: `${pre}devise`,
      meta: {
        auth: ['admin-setting-pages-devise'],
        title: 'Thiết kế giao diện cửa hàng',
      },
      component: () => import('@/pages/setting/devise/list'),
    },
    {
      path: 'pages/user_page/:type',
      name: `${pre}user`,
      meta: {
        auth: ['admin-setting-pages-user'],
        title: 'Trang cá nhân',
      },
      component: () => import('@/pages/setting/devise/list'),
    },
    {
      path: 'pages/link',
      name: `${pre}link`,
      meta: {
        auth: ['admin-setting-pages-link'],
        title: 'Quản lý liên kết',
      },
      component: () => import('@/pages/setting/link'),
    },
    {
      path: 'pages/cate_page/:type',
      name: `${pre}cate`,
      meta: {
        auth: ['admin-setting-pages-cate'],
        title: 'Danh mục sản phẩm',
      },
      component: () => import('@/pages/setting/devise/list'),
    },
    {
      path: 'pages/diy',
      name: `${pre}diy`,
      meta: {
        auth: ['admin-setting-pages-diy'],
        title: 'Thiết kế trang',
        activeMenu: routePre + '/setting/pages/devise',
      },
      component: () => import('@/pages/setting/devisePage/index'),
    },
    {
      path: 'pages/diy_index',
      name: `${pre}index_diy`,
      meta: {
        auth: ['admin-setting-pages-diy'],
        title: 'Thiết kế trang chủ',
        fullScreen: true, //Có hiển thị toàn màn hình khu vực main hay không
      },
      component: () => import('@/pages/setting/devise/diyIndex'),
    },
    {
      path: 'pages/links',
      name: `${pre}links`,
      meta: {
        auth: ['admin-setting-pages-links'],
        title: 'Liên kết trang',
      },
      component: () => import('@/pages/setting/devise/links'),
    },
    {
      path: 'store_service/speechcraft',
      name: `${pre}speechcraft`,
      meta: {
        auth: ['admin-setting-store_service-speechcraft'],
        title: 'Câu trả lời mẫu CSKH',
      },
      component: () => import('@/pages/setting/storeService/speechcraft'),
    },
    {
      path: 'store_service/feedback',
      name: `${pre}feedback`,
      meta: {
        auth: ['admin-setting-store_service-feedback'],
        title: 'Lời nhắn của người dùng',
      },
      component: () => import('@/pages/setting/storeService/feedback'),
    },
    {
      path: 'store_service/auto_reply',
      name: `${pre}auto_reply`,
      meta: {
        auth: ['admin-setting-store_service-auto_reply'],
        title: 'Trả lời tự động',
      },
      component: () => import('@/pages/setting/storeService/autoReply'),
    },
    {
      path: 'system_group_data/pc/:id',
      name: `${pre}groupDataPc`,
      meta: {
        auth: ['setting-system-group_data-pc'],
        title: 'Ảnh trình chiếu trang chủ PC',
      },
      component: () => import('@/pages/system/group/list'),
    },
    {
      path: 'system_config_member_right/:type?/:tab_id?',
      name: `${pre}right`,
      meta: {
        auth: ['setting-system-config-member-right'],
        title: 'Quyền lợi thành viên',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'delivery_service/index',
      name: `${pre}deliveryService`,
      meta: {
        auth: ['setting-delivery-service'],
        title: 'Danh sách nhân viên giao hàng',
      },
      component: () => import('@/pages/setting/deliveryService/index'),
    },
    {
      path: 'pc_group_data',
      name: `${pre}systemPcGroupData`,
      meta: {
        auth: ['setting-system-pc_data'],
        title: 'Cửa hàng PC',
      },
      component: () => import('@/pages/system/group/pc'),
    },
    {
      path: 'system_visualization_data',
      name: `${pre}systemGroupData`,
      meta: {
        auth: ['admin-setting-system_visualization_data'],
        title: 'Cấu hình dữ liệu',
      },
      component: () => import('@/pages/system/group/visualization'),
    },
    {
      path: 'storage',
      name: `${pre}storage`,
      meta: {
        auth: ['setting-storage'],
        title: 'Cấu hình lưu trữ',
      },
      component: () => import('@/pages/setting/storage'),
    },
    {
      path: 'wechat_config/:type?/:tab_id?',
      name: `${pre}wechat_config`,
      meta: {
        ...meta,
        title: 'Cấu hình OA WeChat',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'routine_config/:type?/:tab_id?',
      name: `${pre}routine_config`,
      meta: {
        ...meta,
        title: 'Cấu hình Mini Program',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'app_config/:type?/:tab_id?',
      name: `${pre}app_config`,
      meta: {
        ...meta,
        title: 'Cấu hình app',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'pc_config/:type?/:tab_id?',
      name: `${pre}pc_config`,
      meta: {
        ...meta,
        title: 'Cấu hình PC',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'other_config/print/:type?/:tab_id?',
      name: `${pre}other_print`,
      meta: {
        auth: ['setting-other-print'],
        title: 'Cấu hình in biên lai',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'other_config/copy/:type?/:tab_id?',
      name: `${pre}other_copy`,
      meta: {
        auth: ['setting-other-copy'],
        title: 'Cấu hình thu thập sản phẩm',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'other_config/logistics/:type?/:tab_id?',
      name: `${pre}other_logistics`,
      meta: {
        auth: ['setting-other-logistics'],
        title: 'Cấu hình tra cứu vận chuyển',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'other_config/electronic/:type?/:tab_id?',
      name: `${pre}other_electronic`,
      meta: {
        auth: ['setting-other-electronic'],
        title: 'Cấu hình vận đơn điện tử',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'other_config/sms/:type?/:tab_id?',
      name: `${pre}other_sms`,
      meta: {
        auth: ['setting-other-sms'],
        title: 'Cấu hình chức năng SMS',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'other_config/pay/:type?/:tab_id?',
      name: `${pre}other_pay`,
      meta: {
        auth: ['setting-other-sms'],
        title: 'Cấu hình thanh toán cửa hàng',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'agreement',
      name: `${pre}notification`,
      meta: {
        auth: ['setting-agreement'],
        title: 'Cài đặt thỏa thuận',
      },
      component: () => import('@/pages/setting/agreement/index'),
    },
    {
      path: 'other_config/out/:type?/:tab_id?',
      name: `${pre}other_print`,
      meta: {
        auth: ['setting-other-out'],
        title: 'Cấu hình API bên ngoài',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'system_out_account/index',
      name: `${pre}systemOutAccount`,
      meta: {
        auth: ['setting-system-out-account-index'],
        title: 'Danh sách tài khoản',
      },
      component: () => import('@/pages/setting/systemOutAccount/index'),
    },
    {
      path: 'system_out_interface/index',
      name: `${pre}systemOutAccount`,
      meta: {
        auth: ['setting-system-out-interface-index'],
        title: 'Tài liệu API',
      },
      component: () => import('@/pages/setting/systemOutInterface/index'),
    },
    {
      path: 'lang/list',
      name: `${pre}langList`,
      meta: {
        auth: ['admin-lang-list'],
        title: 'Danh sách ngôn ngữ',
      },
      component: () => import('@/pages/setting/multiLanguage/list'),
    },
    {
      path: 'lang/info',
      name: `${pre}langInfo`,
      meta: {
        auth: ['admin-lang-info'],
        title: 'Chi tiết ngôn ngữ',
      },
      component: () => import('@/pages/setting/multiLanguage/langList'),
    },
    {
      path: 'lang/country',
      name: `${pre}langCountry`,
      meta: {
        auth: ['admin-lang-country'],
        title: 'Ngôn ngữ liên kết theo khu vực',
      },
      component: () => import('@/pages/setting/multiLanguage/country'),
    },
    {
      path: 'yihaotong_config/:type?/:tab_id?',
      name: `${pre}yihaotong_config`,
      meta: {
        ...meta,
        title: 'Cấu hình Yihaotong',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'lang_config/:type?/:tab_id?',
      name: `${pre}lang_config`,
      meta: {
        ...meta,
        title: 'Cấu hình dịch thuật',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'kefu_config/:type?/:tab_id?',
      name: `${pre}kefu_config`,
      meta: {
        ...meta,
        title: 'Cấu hình CSKH',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'recharge_config/:type?/:tab_id?',
      name: `${pre}recharge_config`,
      meta: {
        ...meta,
        title: 'Cấu hình nạp tiền',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'member_config/:type?/:tab_id?',
      name: `${pre}member_config`,
      meta: {
        ...meta,
        title: 'Cấu hình thành viên trả phí',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'user_config/:type?/:tab_id?',
      name: `${pre}user_config`,
      meta: {
        ...meta,
        title: 'Cấu hình người dùng',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'order_config/:type?/:tab_id?',
      name: `${pre}order_config`,
      meta: {
        ...meta,
        title: 'Cấu hình đơn hàng',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'sign_config/:type?/:tab_id?',
      name: `${pre}sign_config`,
      meta: {
        ...meta,
        title: 'Cấu hình điểm danh',
      },
      component: () => import('@/pages/setting/setSystem/index'),
    },
    {
      path: 'ticket',
      name: `${pre}document`,
      meta: {
        ...meta,
        auth: ['admin-setting-ticket'],
        title: 'Cài đặt máy in',
      },
      component: () => import('@/pages/setting/ticket'),
    },
    {
      path: 'ticket/content',
      name: `${pre}content`,
      meta: {
        ...meta,
        auth: ['admin-setting-ticket-content'],
        title: 'Cấu hình biên lai',
        activeMenu: routePre + '/setting/ticket',
      },
      component: () => import('@/pages/setting/ticket/content'),
    },
    {
      path: 'my_theme',
      name: `${pre}myTheme`,
      meta: {
        title: 'Chủ đề của tôi',
      },
      component: () => import('@/pages/setting/theme/myTheme/index'),
    },
    {
      path: 'mall_theme',
      name: `${pre}mallTheme`,
      meta: {
        title: 'Chủ đề cửa hàng',
      },
      component: () => import('@/pages/setting/theme/mallTheme/index'),
    },
    {
      path: 'edit_theme',
      name: `${pre}editTheme`,
      meta: {
        title: 'Chủ đề giao diện',
        fullScreen: true, //Có hiển thị toàn màn hình khu vực main hay không
      },
      component: () => import('@/pages/setting/theme/editTheme/index'),
    },
  ],
};
