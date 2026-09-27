// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

/**
 * Cấu hình diy
 * */

import toolCom from '@/components/diyComponents/index.js';

export default {
  namespaced: true,
  state: {
    periphery: 1,
    activeName: {},
    defaultConfig: {
      headerSerch: {
        imgUrl: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước ảnh đề xuất 128 * 45px',
          url: '',
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
      swiperBg: {
        isShow: {
          val: true,
        },
        imgList: {
          title: 'Có thể thêm tối đa 10 ảnh, chiều rộng đề xuất 750px',
          max: 10,
          list: [
            {
              img: 'http://kaifa.crmeb.net/uploads/attach/2020/03/20200319/a32307fd1043c350932a462839288d38.jpg',
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
              img: 'http://kaifa.crmeb.net/uploads/attach/2020/03/20200319/906d46eb6f734eaf1fd820601893af0d.jpg',
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
      menus: {
        isShow: {
          val: true,
        },
        imgList: {
          title: 'Có thể thêm tối đa 20 mục, kích thước ảnh đề xuất 96*96px; kéo thả chấm tròn bên trái để điều chỉnh thứ tự biểu tượng',
          max: 20,
          list: [
            {
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/723bb4d18893a5aa6871c94d19f3bc4d.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/e908c8f088db07a0f4f6fddc2a7b96f9.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/1a9a1189bf4a1e9970517d31bcb00bbc.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/dded4f4779e705d54cf640826d1b5558.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/f95dd1f3f71fef869e80533df9ccb1a0.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/8bf36e0cd9f9490c1f06abcd7efe8c2d.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/5cbdc6eda8c4a2c92c88abffee50d1ff.png',
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
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/fdb67663ea188163b0ad863a05f77fbf.png',
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
      goodList: {
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
              val: 'Tổng hợp',
              icon: 'iconComm_whole',
            },
            {
              val: 'Lượt bán',
              icon: 'iconComm_number',
            },
            {
              val: 'Giá',
              icon: 'iconComm_Price',
            },
          ],
        },
        goodsList: {
          max: 20,
          list: [],
        },
      },
      tabBar: {
        isShow: {
          val: true,
        },
        tabBarList: {
          title: 'Kích thước ảnh đề xuất 81*81px',
          list: [
            {
              name: 'Trang chủ',
              imgList: [require('@/assets/images/foo1-01.png'), require('@/assets/images/foo1-02.png')],
              link: '/pages/index/index',
            },
            {
              name: 'Danh mục',
              imgList: [require('@/assets/images/foo2-01.png'), require('@/assets/images/foo2-02.png')],
              link: '/pages/goods_cate/goods_cate',
            },
            {
              name: 'Gần bạn',
              imgList: [require('@/assets/images/foo3-01.png'), require('@/assets/images/foo3-02.png')],
              pagePath: '/pages/storeList/index',
            },
            {
              name: 'Giỏ hàng',
              imgList: [require('@/assets/images/foo4-01.png'), require('@/assets/images/foo4-02.png')],
              link: '/pages/order_addcart/order_addcart',
            },
            {
              name: 'Tôi',
              imgList: [require('@/assets/images/foo5-01.png'), require('@/assets/images/foo5-02.png')],
              link: '/pages/user/index',
            },
          ],
        },
      },
    },
    component: {
      headerSerch: {
        list: [
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
      goodList: {
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
      tabBar: {
        list: [
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
    //Cập nhật dữ liệu
    upDataGoodList(state, type) {
      let list = [];
      if (type) {
        list = [
          {
            components: toolCom.c_is_show,
            configNme: 'isShow',
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
          {
            components: toolCom.c_txt_tab,
            configNme: 'goodsSort',
          },
        ];
      }
      state.component.goodList.list = list;
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
