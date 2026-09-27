// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

/**
 * Cấu hình diy
 * */

import toolCom from '@/components/diyComponents/index.js';

export default {
  namespaced: true,
  state: {
    activeName: {},
    defaultConfig: {
      headerSerch: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgUrl: {
            title: 'Có thể thêm tối đa 1 ảnh, kích thước ảnh đề xuất 128 * 45px',
            url: '',
          },
          titleInfo: {
            title: '',
            type: 8,
            list: [
              {
                title: 'Giới thiệu cửa hàng',
                val: 'Hàng tốt thỏa thích, tha hồ lựa chọn',
                max: 20,
                pla: 'Không bắt buộc, tối đa 10 ký tự',
              },
            ],
          },
          hotList: {
            title: 'Từ khóa phổ biến tối đa 20 ký tự, kéo thả chấm tròn bên trái để điều chỉnh thứ tự từ khóa',
            max: 99,
            list: [
              {
                val: '',
                maxlength: 20,
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          imgUrl: {
            title: 'Có thể thêm tối đa 1 ảnh, kích thước ảnh đề xuất 128 * 45px',
            url: '',
          },
          titleInfo: {
            title: '',
            type: 8,
            list: [
              {
                title: 'Giới thiệu cửa hàng',
                val: 'Hàng tốt thỏa thích, tha hồ lựa chọn',
                max: 20,
                pla: 'Không bắt buộc, tối đa 10 ký tự',
              },
            ],
          },
          hotList: {
            title: 'Từ khóa phổ biến tối đa 20 ký tự, kéo thả chấm tròn bên trái để điều chỉnh thứ tự từ khóa',
            max: 99,
            list: [
              {
                val: '',
                maxlength: 20,
              },
            ],
          },
        },
      },
      swiperBg: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgList: {
            title: 'Có thể thêm tối đa 10 ảnh, chiều rộng đề xuất 750px',
            max: 10,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: '',
                    maxlength: 10,
                    tips: 'Không bắt buộc, tối đa 10 ký tự',
                  },
                  {
                    title: 'Liên kết',
                    value: '',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: '',
                    maxlength: 10,
                    tips: 'Không bắt buộc, tối đa 10 ký tự',
                  },
                  {
                    title: 'Liên kết',
                    value: '',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          imgList: {
            title: 'Có thể thêm tối đa 10 ảnh, chiều rộng đề xuất 750px',
            max: 10,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: '',
                    maxlength: 10,
                    tips: 'Không bắt buộc, tối đa 10 ký tự',
                  },
                  {
                    title: 'Liên kết',
                    value: '',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: '',
                    maxlength: 10,
                    tips: 'Không bắt buộc, tối đa 10 ký tự',
                  },
                  {
                    title: 'Liên kết',
                    value: '',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
            ],
          },
        },
      },
      menus: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgList: {
            title: 'Có thể thêm tối đa 20 mục, kích thước ảnh đề xuất 96*96px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự biểu tượng',
            max: 20,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Danh mục sản phẩm',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/goods_cate/goods_cate',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Nhận phiếu giảm giá',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/users/user_get_coupon/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Tin tức ngành',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/extension/news_list/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Yêu thích của tôi',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/users/user_goods_collection/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động mua chung',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_combination/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động flash sale',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_seckill/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động săn giảm giá',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_bargain/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Quản lý địa chỉ',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_bargain/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          imgList: {
            title: 'Có thể thêm tối đa 20 mục, kích thước ảnh đề xuất 96*96px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự biểu tượng',
            max: 20,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Danh mục sản phẩm',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/goods_cate/goods_cate',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Nhận phiếu giảm giá',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/users/user_get_coupon/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Tin tức ngành',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/extension/news_list/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Yêu thích của tôi',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/users/user_goods_collection/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động mua chung',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_combination/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động flash sale',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_seckill/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động săn giảm giá',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_bargain/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Quản lý địa chỉ',
                    maxlength: 5,
                    tips: 'Vui lòng điền tiêu đề',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_bargain/index',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
            ],
          },
        },
      },

      tabNav: {
        defaultVal: {
          isShow: {
            val: true,
          },
        },
        default: {
          isShow: {
            val: true,
          },
        },
      },
      news: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgUrl: {
            title: 'Có thể thêm tối đa 10 mẫu, kích thước ảnh đề xuất 124 * 28px',
            url: '',
          },
          newList: {
            max: 10,
            list: [
              {
                chiild: [
                  {
                    title: 'Tiêu đề',
                    val: 'CRMEB_PRO 1.1 đã chính thức mở beta công khai',
                    max: 20,
                    pla: 'Không bắt buộc, tối đa 4 ký tự',
                  },
                  {
                    title: 'Liên kết',
                    val: 'Liên kết',
                    max: 99,
                    pla: 'Không bắt buộc',
                  },
                ],
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          imgUrl: {
            title: 'Có thể thêm tối đa 10 mẫu, kích thước ảnh đề xuất 124 * 28px',
            url: '',
          },
          newList: {
            max: 10,
            list: [
              {
                chiild: [
                  {
                    title: 'Tiêu đề',
                    val: 'CRMEB_PRO 1.1 đã chính thức mở beta công khai',
                    max: 20,
                    pla: 'Không bắt buộc, tối đa 4 ký tự',
                  },
                  {
                    title: 'Liên kết',
                    val: 'Liên kết',
                    max: 99,
                    pla: 'Không bắt buộc',
                  },
                ],
              },
            ],
          },
        },
      },
      activity: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgList: {
            isDelete: true,
            title: 'Có thể thêm tối đa 3 nhóm khối, ảnh đầu tiên 336*298px, hai ảnh sau 416*124px',
            max: 3,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Cùng nhau mua chung',
                    maxlength: 20,
                    tips: 'Tiêu đề',
                  },
                  {
                    title: 'Mô tả',
                    value: 'Ưu đãi ngập tràn',
                    maxlength: 20,
                    tips: 'Mô tả',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_combination/index',
                    maxlength: 999,
                    tips: 'Liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Khu flash sale',
                    maxlength: 20,
                    tips: 'Tiêu đề',
                  },
                  {
                    title: 'Mô tả',
                    value: 'Ô tô năng lượng mới ưu đãi ngập tràn',
                    maxlength: 20,
                    tips: 'Mô tả',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_seckill/index',
                    maxlength: 999,
                    tips: 'Liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động săn giảm giá',
                    maxlength: 20,
                    tips: 'Tiêu đề',
                  },
                  {
                    title: 'Mô tả',
                    value: 'Rủ bạn bè cùng săn giảm giá~~',
                    maxlength: 20,
                    tips: 'Mô tả',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_bargain/index',
                    maxlength: 999,
                    tips: 'Liên kết',
                  },
                ],
              },
            ],
          },
          max: 3,
        },
        default: {
          isShow: {
            val: true,
          },
          imgList: {
            isDelete: true,
            title: 'Có thể thêm tối đa 3 nhóm khối, ảnh đầu tiên 336*298px, hai ảnh sau 416*124px',
            max: 3,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Cùng nhau mua chung',
                    maxlength: 20,
                    tips: 'Tiêu đề',
                  },
                  {
                    title: 'Mô tả',
                    value: 'Ưu đãi ngập tràn',
                    maxlength: 20,
                    tips: 'Mô tả',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_combination/index',
                    maxlength: 999,
                    tips: 'Liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Khu flash sale',
                    maxlength: 20,
                    tips: 'Tiêu đề',
                  },
                  {
                    title: 'Mô tả',
                    value: 'Ô tô năng lượng mới ưu đãi ngập tràn',
                    maxlength: 20,
                    tips: 'Mô tả',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_seckill/index',
                    maxlength: 999,
                    tips: 'Liên kết',
                  },
                ],
              },
              {
                img: '',
                info: [
                  {
                    title: 'Tiêu đề',
                    value: 'Hoạt động săn giảm giá',
                    maxlength: 20,
                    tips: 'Tiêu đề',
                  },
                  {
                    title: 'Mô tả',
                    value: 'Rủ bạn bè cùng săn giảm giá~~',
                    maxlength: 20,
                    tips: 'Mô tả',
                  },
                  {
                    title: 'Liên kết',
                    value: '/pages/activity/goods_bargain/index',
                    maxlength: 999,
                    tips: 'Liên kết',
                  },
                ],
              },
            ],
          },
          max: 3,
        },
      },
      alive: {
        defaultVal: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Phòng livestream',
                max: 20,
                pla: 'Không bắt buộc, tối đa 6 ký tự',
              },
              {
                title: 'Giới thiệu',
                val: 'Livestream hấp dẫn',
                max: 8,
                pla: 'Không bắt buộc, tối đa 8 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/columnGoods/live_list/index',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
          numConfig: {
            title: 'Số lượng hiển thị',
            val: 3,
          },
        },
        default: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Phòng livestream',
                max: 20,
                pla: 'Không bắt buộc, tối đa 6 ký tự',
              },
              {
                title: 'Giới thiệu',
                val: 'Livestream hấp dẫn',
                max: 8,
                pla: 'Không bắt buộc, tối đa 8 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/columnGoods/live_list/index',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
          numConfig: {
            title: 'Số lượng hiển thị',
            val: 3,
          },
        },
      },
      scrollBox: {
        defaultVal: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Chọn nhanh',
                max: 4,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Giới thiệu',
                val: 'Tận tâm gợi ý sản phẩm chất lượng',
                max: 8,
                pla: 'Không bắt buộc, tối đa 8 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/goods_cate/goods_cate',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
          // tabConfig: {
          //     tabVal: 0,
          //     type: 1,
          //     tabList: [
          //         {
          //             name: 'Tự động chọn',
          //             icon: 'iconzidongxuanze'
          //         },
          //         {
          //             name: 'Chọn thủ công',
          //             icon: 'iconshoudongxuanze'
          //         }
          //     ]
          // },
          // selectConfig: {
          //     title: 'Danh mục sản phẩm',
          //     type: 1,//type=1 thì chỉ truyền danh mục cấp 2
          //     activeValue: '',
          //     list: [
          //         {
          //             activeValue: '',
          //             title: ''
          //         },
          //         {
          //             activeValue: '',
          //             title: ''
          //         }
          //     ]
          // },
          // numConfig: {
          //     title:'Số lượng hiển thị',
          //     val: 6
          // },
          // goodsList: {
          //     max: 20,
          //     list: []
          // }
        },
        default: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Chọn nhanh',
                max: 4,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Giới thiệu',
                val: 'Tận tâm gợi ý sản phẩm chất lượng',
                max: 8,
                pla: 'Không bắt buộc, tối đa 8 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/goods_cate/goods_cate',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
          // tabConfig: {
          //     tabVal: 0,
          //     type: 1,
          //     tabList: [
          //         {
          //             name: 'Tự động chọn',
          //             icon: 'iconzidongxuanze'
          //         },
          //         {
          //             name: 'Chọn thủ công',
          //             icon: 'iconshoudongxuanze'
          //         }
          //     ]
          // },
          // selectConfig: {
          //     title: 'Danh mục sản phẩm',
          //     type: 1,//type=1 thì chỉ truyền danh mục cấp 2
          //     activeValue: '',
          //     list: [
          //         {
          //             activeValue: '',
          //             title: ''
          //         },
          //         {
          //             activeValue: '',
          //             title: ''
          //         }
          //     ]
          // },
          // numConfig: {
          //     title:'Số lượng hiển thị',
          //     val: 6
          // },
          // goodsList: {
          //     max: 20,
          //     list: []
          // }
        },
      },
      adsRecommend: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgList: {
            title: 'Kích thước ảnh đề xuất 338 * 206px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự khối',
            max: 10,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Liên kết',
                    value: '',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          imgList: {
            title: 'Kích thước ảnh đề xuất 338 * 206px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự khối',
            max: 10,
            list: [
              {
                img: '',
                info: [
                  {
                    title: 'Liên kết',
                    value: '',
                    maxlength: 999,
                    tips: 'Vui lòng nhập liên kết',
                  },
                ],
              },
            ],
          },
        },
      },
      coupon: {
        defaultVal: {
          isShow: {
            val: true,
          },
          numConfig: {
            val: 10,
          },
        },
        default: {
          isShow: {
            val: true,
          },
          numConfig: {
            val: 10,
          },
        },
      },
      seckill: {
        defaultVal: {
          isShow: {
            val: true,
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          titleInfo: {
            title: '',
            type: 2,
            list: [
              {
                title: 'Loại sản phẩm',
                val: 'Flash sale giờ vàng',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          titleInfo: {
            title: '',
            type: 2,
            list: [
              {
                title: 'Loại sản phẩm',
                val: 'Flash sale giờ vàng',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
      },
      combination: {
        defaultVal: {
          isShow: {
            val: true,
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          titleInfo: {
            title: '',
            type: 3,
            list: [
              {
                title: 'Loại sản phẩm',
                val: 'Danh sách mua chung',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          titleInfo: {
            title: '',
            type: 3,
            list: [
              {
                title: 'Loại sản phẩm',
                val: 'Danh sách mua chung',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
      },
      bargain: {
        defaultVal: {
          isShow: {
            val: true,
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          titleInfo: {
            title: '',
            type: 8,
            list: [
              {
                title: 'Loại sản phẩm',
                val: 'Danh sách săn giảm giá',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          titleInfo: {
            title: '',
            type: 8,
            list: [
              {
                title: 'Loại sản phẩm',
                val: 'Danh sách săn giảm giá',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
      },
      goodList: {
        defaultVal: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Chọn nhanh',
                max: 4,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Giới thiệu',
                val: 'Tận tâm gợi ý sản phẩm chất lượng',
                max: 8,
                pla: 'Không bắt buộc, tối đa 8 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/columnGoods/HotNewGoods/index',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          selectSortConfig: {
            title: 'Loại sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '0',
                title: 'Danh sách sản phẩm',
              },
              // {
              //   activeValue: '4',
              //   title: 'Bảng xếp hạng phổ biến',
              // },
              // {
              //   activeValue: '5',
              //   title: 'Sản phẩm mới ra mắt',
              // },
              // {
              //   activeValue: '6',
              //   title: 'Sản phẩm khuyến mãi',
              // },
              {
                activeValue: '7',
                title: 'Đề xuất sản phẩm tốt',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Chọn nhanh',
                max: 4,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Giới thiệu',
                val: 'Tận tâm gợi ý sản phẩm chất lượng',
                max: 8,
                pla: 'Không bắt buộc, tối đa 8 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/columnGoods/HotNewGoods/index?type=1',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
          tabConfig: {
            tabVal: 0,
            type: 1,
            tabList: [
              {
                name: 'Chọn tự động',
                icon: 'iconzidongxuanze',
              },
              {
                name: 'Chọn thủ công',
                icon: 'iconshoudongxuanze',
              },
            ],
          },
          selectSortConfig: {
            title: 'Loại sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '0',
                title: 'Danh sách sản phẩm',
              },
              // {
              //   activeValue: '4',
              //   title: 'Bảng xếp hạng phổ biến',
              // },
              // {
              //   activeValue: '5',
              //   title: 'Sản phẩm mới ra mắt',
              // },
              // {
              //   activeValue: '6',
              //   title: 'Sản phẩm khuyến mãi',
              // },
              {
                activeValue: '7',
                title: 'Đề xuất nổi bật',
              },
            ],
          },
          selectConfig: {
            title: 'Danh mục sản phẩm',
            activeValue: '',
            list: [
              {
                activeValue: '',
                title: '',
              },
              {
                activeValue: '',
                title: '',
              },
            ],
          },
          numConfig: {
            val: 6,
          },
          goodsSort: {
            title: 'Sắp xếp sản phẩm',
            name: 'goodsSort',
            type: 0,
            list: [
              {
                val: 'Sắp xếp theo hệ thống',
                icon: 'iconComm_whole',
              },
              {
                val: 'Bán chạy nhất',
                icon: 'iconComm_number',
              },
              {
                val: 'Mới lên kệ',
                icon: 'iconzuixin',
              },
            ],
          },
          goodsList: {
            max: 20,
            list: [],
          },
        },
      },
      picTxt: {
        defaultVal: {
          isShow: {
            val: true,
          },
          richText: {
            val: '',
          },
        },
        default: {
          isShow: {
            val: true,
          },
          richText: {
            val: '',
          },
        },
      },
      titles: {
        defaultVal: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Đề xuất nổi bật',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Tiêu đề',
                val: 'Đề xuất nổi bật',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/columnGoods/HotNewGoods/index?type=1',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          titleInfo: {
            title: '',
            list: [
              {
                title: 'Tiêu đề',
                val: 'Đề xuất nổi bật',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Tiêu đề',
                val: 'Đề xuất nổi bật',
                max: 20,
                pla: 'Không bắt buộc, tối đa 4 ký tự',
              },
              {
                title: 'Liên kết',
                val: '/pages/columnGoods/HotNewGoods/index?type=1',
                max: 999,
                pla: 'Không bắt buộc',
              },
            ],
          },
        },
      },
      customerService: {
        defaultVal: {
          isShow: {
            val: true,
          },
          imgUrl: {
            title: 'Có thể thêm tối đa 1 ảnh, kích thước ảnh đề xuất 128 * 45px',
            url: '',
          },
        },
        default: {
          isShow: {
            val: true,
          },
          imgUrl: {
            title: 'Có thể thêm tối đa 1 ảnh, kích thước ảnh đề xuất 128 * 45px',
            url: '',
          },
        },
      },
      tabBar: {
        defaultVal: {
          isShow: {
            val: true,
          },
          tabBarList: {
            title: 'Kích thước ảnh đề xuất 81*81px',
            list: [
              {
                name: 'Trang chủ',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/9ebdf202104251644215768.png',
                  'https://qiniu.crmeb.net/attach/2021/04/44bc420210425164421586.png',
                ],
                link: '/pages/index/index',
              },
              {
                name: 'Danh mục',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/b62c8202104251644218412.png',
                  'https://qiniu.crmeb.net/attach/2021/04/9509c202104251644214836.png',
                ],
                link: '/pages/goods_cate/goods_cate',
              },
              // {
              //     name:'Xung quanh',
              //     imgList:[require('@/assets/images/foo3-01.png'),require('@/assets/images/foo3-02.png')],
              //     pagePath: ''
              // },
              {
                name: 'Giỏ hàng',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/2e682202104251644216849.png',
                  'https://qiniu.crmeb.net/attach/2021/04/6b3cb202104251644218211.png',
                ],
                link: '/pages/order_addcart/order_addcart',
              },
              {
                name: 'Tôi',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/3329c20210425164421428.png',
                  'https://qiniu.crmeb.net/attach/2021/04/031ce202104251644215432.png',
                ],
                link: '/pages/user/index',
              },
            ],
          },
        },
        default: {
          isShow: {
            val: true,
          },
          tabBarList: {
            title: 'Kích thước ảnh đề xuất 81*81px',
            list: [
              {
                name: 'Trang chủ',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/9ebdf202104251644215768.png',
                  'https://qiniu.crmeb.net/attach/2021/04/44bc420210425164421586.png',
                ],
                link: '/pages/index/index',
              },
              {
                name: 'Danh mục',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/b62c8202104251644218412.png',
                  'https://qiniu.crmeb.net/attach/2021/04/9509c202104251644214836.png',
                ],
                link: '/pages/goods_cate/goods_cate',
              },
              // {
              //     name:'Xung quanh',
              //     imgList:[require('@/assets/images/foo3-01.png'),require('@/assets/images/foo3-02.png')],
              //     pagePath: ''
              // },
              {
                name: 'Giỏ hàng',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/2e682202104251644216849.png',
                  'https://qiniu.crmeb.net/attach/2021/04/6b3cb202104251644218211.png',
                ],
                link: '/pages/order_addcart/order_addcart',
              },
              {
                name: 'Tôi',
                imgList: [
                  'https://qiniu.crmeb.net/attach/2021/04/3329c20210425164421428.png',
                  'https://qiniu.crmeb.net/attach/2021/04/031ce202104251644215432.png',
                ],
                link: '/pages/user/index',
              },
            ],
          },
        },
      },
    },

    component: {
      headerSerch: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_upload_img,
            configNme: 'imgUrl',
          },
          {
            components: toolCom.c_hot_word,
            configNme: 'hotList',
          },
        ],
      },
      swiperBg: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_upload_list,
            configNme: 'imgList',
          },
        ],
      },
      menus: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_upload_list,
            configNme: 'imgList',
          },
        ],
      },
      news: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_upload_img,
            configNme: 'imgUrl',
          },
          {
            components: toolCom.c_txt_list,
            configNme: 'newList',
          },
        ],
      },
      tabNav: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
        ],
      },
      activity: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_upload_list,
            configNme: 'imgList',
          },
        ],
      },
      alive: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
        ],
      },
      scrollBox: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          // {
          //     components: toolCom.c_tab,
          //     configNme: 'tabConfig'
          // },
          // {
          //     components: toolCom.c_select,
          //     configNme: 'selectConfig'
          // },
          // {
          //     components: toolCom.c_input_number,
          //     configNme: 'numConfig'
          // }
        ],
      },
      adsRecommend: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_upload_list,
            configNme: 'imgList',
          },
        ],
      },
      coupon: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
        ],
      },
      seckill: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_tab,
            configNme: 'tabConfig',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectConfig',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
          {
            components: toolCom.c_txt_tab,
            configNme: 'goodsSort',
          },
        ],
      },
      combination: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_tab,
            configNme: 'tabConfig',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectConfig',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
          {
            components: toolCom.c_txt_tab,
            configNme: 'goodsSort',
          },
        ],
      },
      bargain: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_tab,
            configNme: 'tabConfig',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectConfig',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
          {
            components: toolCom.c_txt_tab,
            configNme: 'goodsSort',
          },
        ],
      },
      goodList: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_tab,
            configNme: 'tabConfig',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectSortConfig',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectConfig',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
          {
            components: toolCom.c_txt_tab,
            configNme: 'goodsSort',
          },
        ],
      },
      picTxt: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_page_ueditor,
            configNme: 'richText',
          },
        ],
      },
      titles: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
        ],
      },
      customerService: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_upload_img,
            configNme: 'imgUrl',
          },
        ],
      },
      tabBar: {
        list: [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_tab_bar,
            configNme: 'tabBarList',
          },
        ],
      },
    },
  },
  mutations: {
    /**
     * @description Đặt name được chọn
     * @param {Object} state vuex state
     * @param {String} name
     */
    setConfig(state, name) {
      state.activeName = name;
    },

    upDataName(state, data) {
      state.defaultConfig[state.activeName] = data;
    },

    upDataGoodList(state, data) {
      let list = [];
      if (data.type) {
        list = [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_tab,
            configNme: 'tabConfig',
          },
          {
            components: toolCom.c_goods,
            configNme: 'goodsList',
          },
        ];
      } else {
        list = [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
          },
          {
            components: toolCom.c_input_list,
            configNme: 'titleInfo',
          },
          {
            components: toolCom.c_tab,
            configNme: 'tabConfig',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectConfig',
          },
          {
            components: toolCom.c_input_number,
            configNme: 'numConfig',
          },
        ];
        let sort = {
          components: toolCom.c_txt_tab,
          configNme: 'goodsSort',
        };
        let type = {
          components: toolCom.c_select,
          configNme: 'selectSortConfig',
        };
        let fixed = {
          components: toolCom.c_input_list,
          configNme: 'titleInfo',
        };
        if (data.name === 'seckill' || data.name === 'combination' || data.name === 'bargain') {
          list.splice(2, 0, fixed);
          list.push(sort);
        }
        if (data.name === 'goodList') {
          list.splice(3, 0, type);
          list.push(sort);
        }
      }
      let recommend = {
        components: toolCom.c_upload_list,
        configNme: 'imgList',
      };
      if (data.name === 'recommend') {
        list.splice(1, 0, recommend);
      }
      switch (data.name) {
        case 'scrollBox':
          state.component.scrollBox.list = list;
          break;
        case 'popular':
          state.component.popular.list = list;
          break;
        case 'recommend':
          state.component.recommend.list = list;
          break;
        case 'seckill':
          state.component.seckill.list = list;
          break;
        case 'combination':
          state.component.combination.list = list;
          break;
        case 'bargain':
          state.component.bargain.list = list;
          break;
        case 'newGoods':
          state.component.newGoods.list = list;
          break;
        case 'promotion':
          state.component.promotion.list = list;
          break;
        case 'goodList':
          state.component.goodList.list = list;
          break;
        default:
      }
    },
    /**
     * @description Cập nhật dữ liệu mặc định
     * @param {Object} state vuex state
     * @param {Object} data
     */
    updataConfig(state, data) {
      let value = state.defaultConfig;
      for (let i in data) {
        for (let j in value) {
          if (i === j) {
            value[j] = data[i];
          }
        }
      }
      state.defaultConfig = value;
    },
  },
  actions: {},
};
