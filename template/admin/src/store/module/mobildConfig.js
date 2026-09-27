// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
/**
 * Component tùy chỉnh diy
 * */

const bottomMenu = {
  cname: 'Menu dưới cùng',
  name: 'bottomMenu',
  isHide: false,
  setUp: {
    tabVal: 0,
  },
  entryConfig: {
    title: 'Nội dung lối vào',
    tabVal: 0,
    tabList: [{ name: 'Mặc định' }, { name: 'Tùy chỉnh' }],
  },
  styleTitle: 'Cài đặt kiểu',

  iconColor: {
    title: 'Màu biểu tượng',
    default: [{ item: '#333' }],
    color: [{ item: '#333' }],
  },
  iconSize: {
    title: 'Kích thước biểu tượng',
    val: 20,
    min: 10,
    max: 50,
  },
  iconRotate: {
    title: 'Góc xoay',
    val: 0,
    min: 0,
    max: 360,
  },
  padding: {
    title: 'Lề trong',
    val: 0,
    min: 0,
    max: 50,
  },
  contentConfigTitle: 'Cài đặt nội dung',
  showContent: {
    title: 'Nội dung hiển thị',
    name: 'showContent',
    type: [3, 1, 2],
    list: [
      { id: 3, name: 'Trang chủ', icon: 'icon-shouye6' },
      { id: 1, name: 'Yêu thích', icon: 'icon-shoucang4' },
      { id: 2, name: 'Giỏ hàng', icon: 'icon-gouwuche' },
      { id: 0, name: 'CSKH', icon: 'icon-kefu' },
      { id: 4, name: 'Chia sẻ', icon: 'icon-fenxiang4' },
    ],
  },
  cartButton: {
    title: 'Nút giỏ hàng',
    tabVal: 0,
    tabList: [{ name: 'Hiện' }, { name: 'Ẩn' }],
  },
  menuConfig: {
    title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 90 * 90px',
    bnt: 'Thêm',
    type: 1,
    listStyle: 0,
    maxList: 100,
    list: [
      {
        img: '',
        type: 0,
        show: true,
        icon: '',
        info: [
          {
            title: 'Tiêu đề',
            value: 'Tiêu đề',
            tips: 'Không bắt buộc, tối đa 4 ký tự',
            max: 4,
          },
          {
            title: 'Liên kết',
            value: '',
            tips: 'Vui lòng nhập liên kết',
            max: 100,
          },
        ],
      },
    ],
  },
  buttonStyleTitle: 'Cài đặt nút',
  toneConfig: {
    title: 'Tông màu nút',
    tabVal: 0, // 0: Follow Theme, 1: Custom
    tabList: [{ name: 'Theo phong cách chủ đề' }, { name: 'Tùy chỉnh' }],
  },
  cartColor: {
    title: 'Nút giỏ hàng',
    default: [{ item: '#FAAD14' }, { item: '#FAAD14' }],
    color: [{ item: '#FAAD14' }, { item: '#FAAD14' }],
  },
  buyColor: {
    title: 'Nút mua',
    default: [{ item: '#E93323' }, { item: '#E93323' }],
    color: [{ item: '#E93323' }, { item: '#E93323' }],
  },
  generalStyleTitle: 'Kiểu chung',
  moduleColor: {
    title: 'Nền thành phần',
    default: [{ item: '#fff' }, { item: '#fff' }],
    color: [{ item: '#fff' }, { item: '#fff' }],
  },
  bottomBgColor: {
    title: 'Nền phía dưới',
    default: [{ item: '#F5F5F5' }],
    color: [{ item: '#F5F5F5' }],
  },
  componentBgConfig: {
    title: 'Cài đặt nền',
    tabVal: 0,
    tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
    colorConfig: {
      title: 'Màu nền',
      default: [{ item: '#fff' }, { item: '#fff' }],
      color: [{ item: '#fff' }, { item: '#fff' }],
    },
    colorDirection: {
      title: 'Hướng chuyển màu',
      tabVal: 0,
      tabList: [{ name: 'Ngang' }, { name: 'Dọc' }, { name: 'Chéo trái' }, { name: 'Chéo phải' }],
    },
    imageConfig: {
      header: 'Ảnh nền',
      title: '',
      name: 'Tải lên ảnh',
      type: 'code',
      url: '',
      info: 'Kích thước đề xuất: 750px * 400px',
    },
  },
  zIndexConfig: {
    title: 'Thứ tự lớp thành phần',
    val: 0,
    min: 0,
  },
  borderConfig: {
    title: 'Cài đặt viền',
    tabVal: 0,
    tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
    val: 0, // 0: Hide, 1: Show
    styleConfig: {
      title: 'Kiểu viền',
      tabVal: 0,
      tabList: [
        { name: 'Nét liền', style: 'solid' },
        { name: 'Nét đứt', style: 'dashed' },
        { name: 'Nét chấm', style: 'dotted' },
      ],
    },
    widthConfig: {
      title: 'Độ dày viền',
      val: 1,
      min: 1,
    },
    colorConfig: {
      title: 'Màu viền',
      default: [{ item: '#e5e5e5' }],
      color: [{ item: '#e5e5e5' }],
    },
  },
  shadowConfig: {
    title: 'Cài đặt đổ bóng',
    tabVal: 0,
    tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
    val: 0, // 0: Off, 1: On
    colorConfig: {
      title: 'Màu đổ bóng',
      default: [{ item: 'rgba(0,0,0,0.1)' }],
      color: [{ item: 'rgba(0,0,0,0.1)' }],
    },
    xConfig: {
      title: 'Độ lệch trục X',
      val: 0,
      min: -50,
    },
    yConfig: {
      title: 'Độ lệch trục Y',
      val: 0,
      min: -50,
    },
    blurConfig: {
      title: 'Bán kính làm mờ',
      val: 10,
      min: 0,
    },
    spreadConfig: {
      title: 'Bán kính lan tỏa',
      val: 0,
      min: -50,
    },
  },
  paddingConfig: {
    title: 'Lề trong',
    isAll: false,
    val: 0,
    min: 0,
    valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
  },
  marginConfig: {
    title: 'Lề ngoài',
    isAll: false,
    val: 0,
    min: 0,
    valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
  },
  fillet: {
    title: 'Bo góc nền',
    type: 0,
    list: [
      {
        val: 'Tất cả',
        icon: 'iconcaozuo-zhengti',
      },
      {
        val: 'Từng góc',
        icon: 'iconcaozuo-bianjiao',
      },
    ],
    valName: 'Giá trị bo góc',
    val: 0,
    min: 0,
    valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
  },
  menuPcFillet: {
    title: 'Cài đặt bo góc',
    type: 0,
    list: [
      {
        val: 'Tất cả',
        icon: 'iconcaozuo-zhengti',
      },
      {
        val: 'Từng góc',
        icon: 'iconcaozuo-bianjiao',
      },
    ],
    valName: 'Giá trị bo góc',
    val: 0,
    min: 0,
    valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
  },
};

export default {
  namespaced: true,
  state: {
    configName: '',
    pageTitle: '',
    pageName: 'Tên mẫu 1',
    pageShow: 1,
    pageColor: 0,
    pagePic: 0,
    pageColorPicker: '#f5f5f5',
    pageTabVal: 0,
    pagePicUrl: '',
    // Mảng dữ liệu mặc định của danh sách component đã biết
    defaultArray: {},
    bottomMenu: JSON.parse(JSON.stringify(bottomMenu)),
    pageFooter: {
      cname: 'Điều hướng dưới cùng',
      name: 'pageFoot',
      setUp: {
        tabVal: 0,
      },
      titleLeft: 'Cài đặt hiển thị',
      titleNav: 'Nội dung điều hướng',
      titleRight: 'Cài đặt màu sắc',
      titleCurrency: 'Kiểu chung',
      effectConfig: {
        title: 'Hiệu ứng hiển thị',
        tabVal: 1,
        tabList: [
          {
            name: 'Mặc định hệ thống',
          },
          {
            name: 'Tùy chỉnh',
          },
        ],
      },
      navConfig: {
        title: 'Loại điều hướng',
        tabVal: 0,
        tabList: [
          {
            name: 'Cố định dưới cùng',
          },
          {
            name: 'Nổi dưới cùng',
          },
        ],
      },
      navStyleConfig: {
        title: 'Kiểu điều hướng',
        tabVal: 0,
        tabList: [
          {
            name: 'Hình ảnh + văn bản',
          },
          {
            name: 'Văn bản',
          },
          {
            name: 'Hình ảnh',
          },
        ],
      },
      toneConfig: {
        title: 'Tông màu',
        tabVal: 1,
        tabList: [
          {
            name: 'Theo phong cách chủ đề',
          },
          {
            name: 'Tùy chỉnh',
          },
        ],
      },
      topConfig: {
        title: 'Lề trên',
        val: 0,
        min: 0,
      },
      bottomConfig: {
        title: 'Lề dưới',
        val: 0,
        min: 0,
      },
      prConfig: {
        title: 'Lề trái phải',
        val: 10,
        min: 0,
      },
      mbConfig: {
        title: 'Khoảng cách dưới trang',
        val: 25,
        min: 0,
      },
      fillet: {
        title: 'Bo góc nền',
        type: 0,
        list: [
          {
            val: 'Tất cả',
            icon: 'iconcaozuo-zhengti',
          },
          {
            val: 'Từng góc',
            icon: 'iconcaozuo-bianjiao',
          },
        ],
        valName: 'Giá trị bo góc',
        val: 30,
        min: 0,
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
      txtColor: {
        title: 'Màu chữ',
        name: 'txtColor',
        default: [{ item: '#282828' }],
        color: [{ item: '#282828' }],
      },
      activeTxtColor: {
        title: 'Màu chữ khi được chọn',
        name: 'txtColor',
        default: [{ item: '#F62C2C' }],
        color: [{ item: '#F62C2C' }],
      },
      bgColor: {
        title: 'Màu nền',
        name: 'bgColor',
        default: [{ item: '#fff' }],
        color: [{ item: '#fff' }],
      },
      bgColor2: {
        title: 'Màu nền',
        name: 'bgColor2',
        default: [{ item: 'rgba(255,255,255,0.8)' }],
        color: [{ item: 'rgba(255,255,255,0.8)' }],
      },

      status: {
        title: 'Tùy chỉnh',
        name: 'status',
        status: false,
      },

      menuList: [
        {
          imgList: [require('@/assets/images/foot-001.png'), require('@/assets/images/foot-002.png')],
          name: 'Trang chủ',
          link: '/pages/index/index',
        },
        {
          imgList: [require('@/assets/images/foot-003.png'), require('@/assets/images/foot-004.png')],
          name: 'Danh mục',
          link: '/pages/goods_cate/goods_cate',
        },
        {
          imgList: [require('@/assets/images/foot-005.png'), require('@/assets/images/foot-006.png')],
          name: 'Giỏ hàng',
          link: '/pages/order_addcart/order_addcart',
        },
        {
          imgList: [require('@/assets/images/foot-007.png'), require('@/assets/images/foot-008.png')],
          name: 'Tôi',
          link: '/pages/user/index',
        },
      ],
    },
  },
  mutations: {
    FOOTER(state, data) {
      // state.pageFooter.status.title = data.title;
      state.pageFooter.menuList[2] = data.name;
    },
    UPBOTTOMMENU(state, data) {
      state.bottomMenu = data;
    },
    /**
     * @description Push cấu hình mặc định vào mảng
     * @param {Object} state vuex state
     * @param {Object} data
     * Thêm dữ liệu mặc định vào mảng mặc định, giải quyết vấn đề các component trùng lặp dùng chung một cấu hình
     */
    ADDARRAY(state, data) {
      data.val.id = 'id' + data.val.timestamp;
      state.defaultArray[data.num] = data.val;
    },
    /**
     * @description Xóa dữ liệu mặc định thứ mấy trong danh sách
     * @param {Object} state vuex state
     * @param {Object} data Dữ liệu
     */
    DELETEARRAY(state, data) {
      let tempObj = delete state.defaultArray[data.num];
    },
    /**
     * @description Xóa dữ liệu mặc định thứ mấy trong danh sách
     * @param {Object} state vuex state
     * @param {Object} data Dữ liệu
     */
    ARRAYREAST(state, data) {
      let tempObj = delete state.defaultArray[data];
    },
    /**
     * @description Sắp xếp mảng
     * @param {Object} state vuex state
     * @param {Object} data Ghi lại index vị trí
     */
    defaultArraySort(state, data) {
      let newArr = objToArr(state.defaultArray);
      let sortArr = [];
      let newObj = {};
      function objToArr(data) {
        let obj = Object.keys(data);
        let m = obj.map((key) => data[key]);
        return m;
      }
      function swapArray(arr, index1, index2) {
        let oldObj = {};
        let newObj = {};
        let active = 0;
        arr.forEach((el, index) => {
          if (!el.id) {
            el.id = 'id' + el.timestamp;
          }
          data.list.forEach((item, j) => {
            if (el.id == item.id) {
              el.timestamp = item.num;
            }
          });
        });
        return arr;
      }
      if (data.oldIndex != undefined) {
        sortArr = JSON.parse(JSON.stringify(swapArray(newArr, data.newIndex, data.oldIndex)));
      } else {
        newArr.splice(data.newIndex, 0, data.element.data().defaultConfig);
        sortArr = JSON.parse(JSON.stringify(swapArray(newArr, 0, 0)));
      }
      for (let i = 0; i < sortArr.length; i++) {
        newObj[sortArr[i].timestamp] = sortArr[i];
      }
      state.defaultArray = Object.assign({}, newObj);
    },
    /**
     * @description Cập nhật một nhóm dữ liệu trong mảng
     * @param {Object} state vuex state
     * @param {Object} data
     */
    UPDATEARR(state, data) {
      for (var k in state.defaultArray) {
        if (state.defaultArray[k].id == data.val.id) {
          state.defaultArray[k] = data.val;
        }
      }
      let value = Object.assign({}, state.defaultArray);
      state.defaultArray = value;
    },
    /**
     * @description Lưu tên thành phần
     * @param {Object} state vuex state
     * @param {string} data
     */
    SETCONFIGNAME(state, name) {
      state.configName = name;
    },
    /**
     * @description Xóa trắng component mặc định
     * @param {Object} state vuex state
     * @param {string} data
     */
    SETEMPTY(state, name) {
      state.defaultArray = {};
    },
    UPTITLE(state, val) {
      state.pageTitle = val;
    },
    UPNAME(state, val) {
      state.pageName = val;
    },
    UPSHOW(state, val) {
      state.pageShow = val;
    },
    UPCOLOR(state, val) {
      state.pageColor = val;
    },
    UPPIC(state, val) {
      state.pagePic = val;
    },
    UPPICKER(state, val) {
      state.pageColorPicker = val;
    },
    UPRADIO(state, val) {
      state.pageTabVal = val;
    },
    UPPICURL(state, val) {
      state.pagePicUrl = val;
    },
    /**
     * @description Cập nhật cấu hình menu foot
     * @param {Object} state vuex state
     * @param {string} data
     */
    footUpdata(state, data) {
      state.pageFooter.menuList = [];
      state.pageFooter.menuList = data;
    },
    /**
     * @description Cập nhật công tắc tùy chỉnh foot
     * @param {Object} state vuex state
     * @param {string} data
     */
    footStatus(state, data) {
      // state.pageFooter.status.status = data
    },
    // Cập nhật loại điều hướng
    footType(state, data) {
      state.pageFooter.navConfig.tabVal = data;
    },
    //Lề dưới của điều hướng dưới cùng;
    footBottom(state, data) {
      state.pageFooter.mbConfig.val = data;
    },
    /**
     * @description Cập nhật cấu hình foot
     * @param {Object} state vuex state
     * @param {string} data
     */
    footPageUpdata(state, data) {
      state.pageFooter = data;
    },
    /**
     * @description Cập nhật cấu hình bottomMenu
     * @param {Object} state vuex state
     * @param {string} data
     */
    bottomMenuUpdata(state, data) {
      state.bottomMenu = data;
    },
    RESET_BOTTOM_MENU(state) {
      state.bottomMenu = JSON.parse(JSON.stringify(bottomMenu));
    },
    /**
     * @description Cập nhật cấu hình title
     * @param {Object} state vuex state
     * @param {string} data
     */
    titleUpdata(state, data) {
      state.pageTitle = data;
    },
    /**
     * @description Cập nhật cấu hình name
     * @param {Object} state vuex state
     * @param {string} data
     */
    nameUpdata(state, data) {
      state.pageName = data;
    },
    //
    showUpdata(state, data) {
      state.pageShow = data;
    },
    colorUpdata(state, data) {
      state.pageColor = data;
    },
    picUpdata(state, data) {
      state.pagePic = data;
    },
    pickerUpdata(state, data) {
      state.pageColorPicker = data;
    },
    radioUpdata(state, data) {
      state.pageTabVal = data;
    },
    picurlUpdata(state, data) {
      state.pagePicUrl = data;
    },
  },
  actions: {
    getData({ commit }, data) {},
  },
};
