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
      monograph: {
        isShow: {
          val: true,
        },
        imgList: {
          title: 'Chiều rộng đề xuất 750px',
          max: '',
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
      picTxt: {
        isShow: {
          val: true,
        },
        richText: {
          val: '',
        },
      },
      tabBar: {
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
            // {
            //     name:'Xung quanh',
            //     imgList:[require('@/assets/images/foo3-01.png'),require('@/assets/images/foo3-02.png')],
            //     pagePath: ''
            // },
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
      monograph: {
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
