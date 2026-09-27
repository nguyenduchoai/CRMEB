/**
* @Chuyển đổi qua lại dương lịch, âm lịch trong khoảng 1900-2100
* @charset UTF-8
* @github  https://github.com/jjonline/calendar.js
* @Author  Jea Yang (JJonline@JJonline.Cn)
* @Time    2014-7-21
* @Time    2016-8-13 Fixed 2033hex、Attribution Annals
* @Time    2016-9-25 Fixed lunar LeapMonth Param Bug
* @Time    2017-7-24 Fixed use getTerm Func Param Error.use solar year,NOT lunar year
* @Version 1.0.3
* @Chuyển dương lịch sang âm lịch: calendar.solar2lunar(1987,11,01); //[you can ignore params of prefix 0]
* @Chuyển âm lịch sang dương lịch: calendar.lunar2solar(1987,09,10); //[you can ignore params of prefix 0]
*/
/* eslint-disable */
var calendar = {

  /**
      * Bảng thông tin tháng đủ/thiếu (nhuận) của âm lịch giai đoạn 1900-2100
      * @Array Of Property
      * @return Hex
      */
  lunarInfo: [0x04bd8, 0x04ae0, 0x0a570, 0x054d5, 0x0d260, 0x0d950, 0x16554, 0x056a0, 0x09ad0, 0x055d2, // 1900-1909
    0x04ae0, 0x0a5b6, 0x0a4d0, 0x0d250, 0x1d255, 0x0b540, 0x0d6a0, 0x0ada2, 0x095b0, 0x14977, // 1910-1919
    0x04970, 0x0a4b0, 0x0b4b5, 0x06a50, 0x06d40, 0x1ab54, 0x02b60, 0x09570, 0x052f2, 0x04970, // 1920-1929
    0x06566, 0x0d4a0, 0x0ea50, 0x06e95, 0x05ad0, 0x02b60, 0x186e3, 0x092e0, 0x1c8d7, 0x0c950, // 1930-1939
    0x0d4a0, 0x1d8a6, 0x0b550, 0x056a0, 0x1a5b4, 0x025d0, 0x092d0, 0x0d2b2, 0x0a950, 0x0b557, // 1940-1949
    0x06ca0, 0x0b550, 0x15355, 0x04da0, 0x0a5b0, 0x14573, 0x052b0, 0x0a9a8, 0x0e950, 0x06aa0, // 1950-1959
    0x0aea6, 0x0ab50, 0x04b60, 0x0aae4, 0x0a570, 0x05260, 0x0f263, 0x0d950, 0x05b57, 0x056a0, // 1960-1969
    0x096d0, 0x04dd5, 0x04ad0, 0x0a4d0, 0x0d4d4, 0x0d250, 0x0d558, 0x0b540, 0x0b6a0, 0x195a6, // 1970-1979
    0x095b0, 0x049b0, 0x0a974, 0x0a4b0, 0x0b27a, 0x06a50, 0x06d40, 0x0af46, 0x0ab60, 0x09570, // 1980-1989
    0x04af5, 0x04970, 0x064b0, 0x074a3, 0x0ea50, 0x06b58, 0x05ac0, 0x0ab60, 0x096d5, 0x092e0, // 1990-1999
    0x0c960, 0x0d954, 0x0d4a0, 0x0da50, 0x07552, 0x056a0, 0x0abb7, 0x025d0, 0x092d0, 0x0cab5, // 2000-2009
    0x0a950, 0x0b4a0, 0x0baa4, 0x0ad50, 0x055d9, 0x04ba0, 0x0a5b0, 0x15176, 0x052b0, 0x0a930, // 2010-2019
    0x07954, 0x06aa0, 0x0ad50, 0x05b52, 0x04b60, 0x0a6e6, 0x0a4e0, 0x0d260, 0x0ea65, 0x0d530, // 2020-2029
    0x05aa0, 0x076a3, 0x096d0, 0x04afb, 0x04ad0, 0x0a4d0, 0x1d0b6, 0x0d250, 0x0d520, 0x0dd45, // 2030-2039
    0x0b5a0, 0x056d0, 0x055b2, 0x049b0, 0x0a577, 0x0a4b0, 0x0aa50, 0x1b255, 0x06d20, 0x0ada0, // 2040-2049
    /** Add By JJonline@JJonline.Cn**/
    0x14b63, 0x09370, 0x049f8, 0x04970, 0x064b0, 0x168a6, 0x0ea50, 0x06b20, 0x1a6c4, 0x0aae0, // 2050-2059
    0x0a2e0, 0x0d2e3, 0x0c960, 0x0d557, 0x0d4a0, 0x0da50, 0x05d55, 0x056a0, 0x0a6d0, 0x055d4, // 2060-2069
    0x052d0, 0x0a9b8, 0x0a950, 0x0b4a0, 0x0b6a6, 0x0ad50, 0x055a0, 0x0aba4, 0x0a5b0, 0x052b0, // 2070-2079
    0x0b273, 0x06930, 0x07337, 0x06aa0, 0x0ad50, 0x14b55, 0x04b60, 0x0a570, 0x054e4, 0x0d160, // 2080-2089
    0x0e968, 0x0d520, 0x0daa0, 0x16aa6, 0x056d0, 0x04ae0, 0x0a9d4, 0x0a2d0, 0x0d150, 0x0f252, // 2090-2099
    0x0d520], // 2100

  /**
      * Bảng số ngày thông thường của từng tháng dương lịch
      * @Array Of Property
      * @return Number
      */
  solarMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],

  /**
      * Bảng tra nhanh Thiên Can trong Can Chi
      * @Array Of Property trans["Giáp","Ất","Bính","Đinh","Mậu","Kỷ","Canh","Tân","Nhâm","Quý"]
      * @return Cn string
      */
  Gan: ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'],

  /**
      * Bảng tra nhanh Địa Chi trong Can Chi
      * @Array Of Property
      * @trans["Tý","Sửu","Dần","Mão","Thìn","Tỵ","Ngọ","Mùi","Thân","Dậu","Tuất","Hợi"]
      * @return Cn string
      */
  Zhi: ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'],

  /**
      * Bảng tra nhanh Địa Chi <=> con giáp
      * @Array Of Property
      * @trans["Chuột","Trâu","Hổ","Thỏ","Rồng","Rắn","Ngựa","Dê","Khỉ","Gà","Chó","Lợn"]
      * @return Cn string
      */
  Animals: ['Chuột', 'Trâu', 'Hổ', 'Mèo', 'Rồng', 'Rắn', 'Ngựa', 'Dê', 'Khỉ', 'Gà', 'Chó', 'Lợn'],

  /**
      * Bảng tra nhanh 24 tiết khí
      * @Array Of Property
      * @trans["Tiểu hàn","Đại hàn","Lập xuân","Vũ thủy","Kinh trập","Xuân phân","Thanh minh","Cốc vũ","Lập hạ","Tiểu mãn","Mang chủng","Hạ chí","Tiểu thử","Đại thử","Lập thu","Xử thử","Bạch lộ","Thu phân","Hàn lộ","Sương giáng","Lập đông","Tiểu tuyết","Đại tuyết","Đông chí"]
      * @return Cn string
      */
  solarTerm: ['Tiểu hàn', 'Đại hàn', 'Lập xuân', 'Vũ thủy', 'Kinh trập', 'Xuân phân', 'Thanh minh', 'Cốc vũ', 'Lập hạ', 'Tiểu mãn', 'Mang chủng', 'Hạ chí', 'Tiểu thử', 'Đại thử', 'Lập thu', 'Xử thử', 'Bạch lộ', 'Thu phân', 'Hàn lộ', 'Sương giáng', 'Lập đông', 'Tiểu tuyết', 'Đại tuyết', 'Đông chí'],

  /**
      * Bảng tra nhanh ngày 24 tiết khí của các năm 1900-2100
      * @Array Of Property
      * @return 0x string For splice
      */
  sTermInfo: ['9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf97c3598082c95f8c965cc920f',
    '97bd0b06bdb0722c965ce1cfcc920f', 'b027097bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
    '97bcf97c359801ec95f8c965cc920f', '97bd0b06bdb0722c965ce1cfcc920f', 'b027097bd097c36b0b6fc9274c91aa',
    '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f', '97bd0b06bdb0722c965ce1cfcc920f',
    'b027097bd097c36b0b6fc9274c91aa', '9778397bd19801ec9210c965cc920e', '97b6b97bd19801ec95f8c965cc920f',
    '97bd09801d98082c95f8e1cfcc920f', '97bd097bd097c36b0b6fc9210c8dc2', '9778397bd197c36c9210c9274c91aa',
    '97b6b97bd19801ec95f8c965cc920e', '97bd09801d98082c95f8e1cfcc920f', '97bd097bd097c36b0b6fc9210c8dc2',
    '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec95f8c965cc920e', '97bcf97c3598082c95f8e1cfcc920f',
    '97bd097bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec9210c965cc920e',
    '97bcf97c3598082c95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
    '97b6b97bd19801ec9210c965cc920e', '97bcf97c3598082c95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722',
    '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f',
    '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
    '97bcf97c359801ec95f8c965cc920f', '97bd097bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
    '97b6b97bd19801ec9210c965cc920e', '97bcf97c359801ec95f8c965cc920f', '97bd097bd07f595b0b6fc920fb0722',
    '9778397bd097c36b0b6fc9210c8dc2', '9778397bd19801ec9210c9274c920e', '97b6b97bd19801ec95f8c965cc920f',
    '97bd07f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c920e',
    '97b6b97bd19801ec95f8c965cc920f', '97bd07f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2',
    '9778397bd097c36c9210c9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bd07f1487f595b0b0bc920fb0722',
    '7f0e397bd097c36b0b6fc9210c8dc2', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
    '97bcf7f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
    '97b6b97bd19801ec9210c965cc920e', '97bcf7f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
    '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e', '97bcf7f1487f531b0b0bb0b6fb0722',
    '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b97bd19801ec9210c965cc920e',
    '97bcf7f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
    '97b6b97bd19801ec9210c9274c920e', '97bcf7f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
    '9778397bd097c36b0b6fc9210c91aa', '97b6b97bd197c36c9210c9274c920e', '97bcf7f0e47f531b0b0bb0b6fb0722',
    '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9210c8dc2', '9778397bd097c36c9210c9274c920e',
    '97b6b7f0e47f531b0723b0b6fb0722', '7f0e37f5307f595b0b0bc920fb0722', '7f0e397bd097c36b0b6fc9210c8dc2',
    '9778397bd097c36b0b70c9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721', '7f0e37f1487f595b0b0bb0b6fb0722',
    '7f0e397bd097c35b0b6fc9210c8dc2', '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721',
    '7f0e27f1487f595b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
    '97b6b7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
    '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
    '7f0e397bd097c35b0b6fc920fb0722', '9778397bd097c36b0b6fc9274c91aa', '97b6b7f0e47f531b0723b0b6fb0721',
    '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9274c91aa',
    '97b6b7f0e47f531b0723b0787b0721', '7f0e27f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
    '9778397bd097c36b0b6fc9210c91aa', '97b6b7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
    '7f0e397bd07f595b0b0bc920fb0722', '9778397bd097c36b0b6fc9210c8dc2', '977837f0e37f149b0723b0787b0721',
    '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f5307f595b0b0bc920fb0722', '7f0e397bd097c35b0b6fc9210c8dc2',
    '977837f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e37f1487f595b0b0bb0b6fb0722',
    '7f0e397bd097c35b0b6fc9210c8dc2', '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
    '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722', '977837f0e37f14998082b0787b06bd',
    '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd097c35b0b6fc920fb0722',
    '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
    '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
    '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14998082b0787b06bd',
    '7f07e7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0b0bb0b6fb0722', '7f0e397bd07f595b0b0bc920fb0722',
    '977837f0e37f14998082b0723b06bd', '7f07e7f0e37f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
    '7f0e397bd07f595b0b0bc920fb0722', '977837f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b0721',
    '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f1487f595b0b0bb0b6fb0722', '7f0e37f0e37f14898082b0723b02d5',
    '7ec967f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0722', '7f0e37f1487f531b0b0bb0b6fb0722',
    '7f0e37f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
    '7f0e37f1487f531b0b0bb0b6fb0722', '7f0e37f0e37f14898082b072297c35', '7ec967f0e37f14998082b0787b06bd',
    '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e37f0e37f14898082b072297c35',
    '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722',
    '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f149b0723b0787b0721',
    '7f0e27f1487f531b0b0bb0b6fb0722', '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14998082b0723b06bd',
    '7f07e7f0e47f149b0723b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722', '7f0e37f0e366aa89801eb072297c35',
    '7ec967f0e37f14998082b0723b06bd', '7f07e7f0e37f14998083b0787b0721', '7f0e27f0e47f531b0723b0b6fb0722',
    '7f0e37f0e366aa89801eb072297c35', '7ec967f0e37f14898082b0723b02d5', '7f07e7f0e37f14998082b0787b0721',
    '7f07e7f0e47f531b0723b0b6fb0722', '7f0e36665b66aa89801e9808297c35', '665f67f0e37f14898082b0723b02d5',
    '7ec967f0e37f14998082b0787b0721', '7f07e7f0e47f531b0723b0b6fb0722', '7f0e36665b66a449801e9808297c35',
    '665f67f0e37f14898082b0723b02d5', '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721',
    '7f0e36665b66a449801e9808297c35', '665f67f0e37f14898082b072297c35', '7ec967f0e37f14998082b0787b06bd',
    '7f07e7f0e47f531b0723b0b6fb0721', '7f0e26665b66a449801e9808297c35', '665f67f0e37f1489801eb072297c35',
    '7ec967f0e37f14998082b0787b06bd', '7f07e7f0e47f531b0723b0b6fb0721', '7f0e27f1487f531b0b0bb0b6fb0722'],

  /**
      * Bảng tra nhanh chuyển số thành chữ Hán
      * @Array Of Property
      * @trans ['ngày','một','hai','ba','bốn','năm','sáu','bảy','tám','chín','mười']
      * @return Cn string
      */
  nStr1: ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'],

  /**
      * Bảng tra nhanh chuyển ngày thành cách gọi âm lịch
      * @Array Of Property
      * @trans ['đầu','mười','hai mươi','ba mươi']
      * @return Cn string
      */
  nStr2: ['Mùng'],

  /**
      * Bảng tra nhanh chuyển tháng thành cách gọi âm lịch
      * @Array Of Property
      * @trans ['Giêng','một','hai','ba','bốn','năm','sáu','bảy','tám','chín','mười','Đông','Chạp']
      * @return Cn string
      */
  nStr3: ['Giêng', 'Hai', 'Ba', 'Tư', 'Năm', 'Sáu', 'Bảy', 'Tám', 'Chín', 'Mười', 'Mười Một', 'Chạp'],

  /**
      * Trả về tổng số ngày của cả năm âm lịch y
      * @param lunar Year
      * @return Number
      * @eg:var count = calendar.lYearDays(1987) ;//count=387
      */
  lYearDays: function (y) {
    var i; var sum = 348
    for (i = 0x8000; i > 0x8; i >>= 1) { sum += (this.lunarInfo[y - 1900] & i) ? 1 : 0 }
    return (sum + this.leapDays(y))
  },

  /**
      * Trả về tháng nhuận của năm âm lịch y là tháng mấy; nếu năm y không có tháng nhuận thì trả về 0
      * @param lunar Year
      * @return Number (0-12)
      * @eg:var leapMonth = calendar.leapMonth(1987) ;//leapMonth=6
      */
  leapMonth: function (y) { // tháng nhuận
    return (this.lunarInfo[y - 1900] & 0xf)
  },

  /**
      * Trả về số ngày của tháng nhuận năm âm lịch y; nếu năm đó không có tháng nhuận thì trả về 0
      * @param lunar Year
      * @return Number (0、29、30)
      * @eg:var leapMonthDay = calendar.leapDays(1987) ;//leapMonthDay=29
      */
  leapDays: function (y) {
    if (this.leapMonth(y)) {
      return ((this.lunarInfo[y - 1900] & 0x10000) ? 30 : 29)
    }
    return (0)
  },

  /**
      * Trả về tổng số ngày của tháng m (không phải tháng nhuận) năm âm lịch y, muốn tính số ngày khi m là tháng nhuận thì dùng phương thức leapDays
      * @param lunar Year
      * @return Number (-1、29、30)
      * @eg:var MonthDay = calendar.monthDays(1987,9) ;//MonthDay=29
      */
  monthDays: function (y, m) {
    if (m > 12 || m < 1) { return -1 }// Tham số tháng từ 1 đến 12, tham số sai thì trả về -1
    return ((this.lunarInfo[y - 1900] & (0x10000 >> m)) ? 30 : 29)
  },

  /**
      * Trả về số ngày của tháng m năm dương lịch(!) y
      * @param solar Year
      * @return Number (-1、28、29、30、31)
      * @eg:var solarMonthDay = calendar.leapDays(1987) ;//solarMonthDay=30
      */
  solarDays: function (y, m) {
    if (m > 12 || m < 1) { return -1 } // Nếu tham số sai thì trả về -1
    var ms = m - 1
    if (ms == 1) { // Tháng 2 sau khi tính quy luật năm nhuận/thường thì xác nhận trả về 28 hoặc 29
      return (((y % 4 == 0) && (y % 100 != 0) || (y % 400 == 0)) ? 29 : 28)
    } else {
      return (this.solarMonth[ms])
    }
  },

  /**
     * Chuyển năm âm lịch thành năm Can Chi
     * @param  lYear Số năm của năm âm lịch
     * @return Cn string
     */
  toGanZhiYear: function (lYear) {
    var ganKey = (lYear - 3) % 10
    var zhiKey = (lYear - 3) % 12
    if (ganKey == 0) ganKey = 10// Nếu số dư là 0 thì là Thiên Can cuối cùng
    if (zhiKey == 0) zhiKey = 12// Nếu số dư là 0 thì là Địa Chi cuối cùng
    return this.Gan[ganKey - 1] + ' ' + this.Zhi[zhiKey - 1]
  },

  /**
     * Xác định cung hoàng đạo theo tháng, ngày dương lịch
     * @param  cMonth [description]
     * @param  cDay [description]
     * @return Cn string
     */
  toAstro: function (cMonth, cDay) {
    var s = ['Ma Kết', 'Bảo Bình', 'Song Ngư', 'Bạch Dương', 'Kim Ngưu', 'Song Tử', 'Cự Giải', 'Sư Tử', 'Xử Nữ', 'Thiên Bình', 'Thiên Yết', 'Nhân Mã', 'Ma Kết']
    var arr = [20, 19, 21, 21, 21, 22, 23, 23, 23, 23, 22, 22]
    return 'Cung ' + s[cMonth - (cDay < arr[cMonth - 1] ? 1 : 0)]
  },

  /**
      * Truyền vào offset (độ lệch), trả về Can Chi
      * @param offset Độ lệch tương đối so với Giáp Tý
      * @return Cn string
      */
  toGanZhi: function (offset) {
    return this.Gan[offset % 10] + ' ' + this.Zhi[offset % 12]
  },

  /**
      * Truyền vào năm dương lịch(!) y, lấy ngày dương lịch của tiết khí thứ n trong năm đó
      * @param y là năm dương lịch (1900-2100); n là tiết khí thứ mấy trong 24 tiết khí (1~24); tính từ n=1 (Tiểu hàn)
      * @return day Number
      * @eg:var _24 = calendar.getTerm(1987,3) ;//_24=4; nghĩa là ngày 4/2/1987 là Lập xuân
      */
  getTerm: function (y, n) {
    if (y < 1900 || y > 2100) { return -1 }
    if (n < 1 || n > 24) { return -1 }
    var _table = this.sTermInfo[y - 1900]
    var _info = [
      parseInt('0x' + _table.substr(0, 5)).toString(),
      parseInt('0x' + _table.substr(5, 5)).toString(),
      parseInt('0x' + _table.substr(10, 5)).toString(),
      parseInt('0x' + _table.substr(15, 5)).toString(),
      parseInt('0x' + _table.substr(20, 5)).toString(),
      parseInt('0x' + _table.substr(25, 5)).toString()
    ]
    var _calday = [
      _info[0].substr(0, 1),
      _info[0].substr(1, 2),
      _info[0].substr(3, 1),
      _info[0].substr(4, 2),

      _info[1].substr(0, 1),
      _info[1].substr(1, 2),
      _info[1].substr(3, 1),
      _info[1].substr(4, 2),

      _info[2].substr(0, 1),
      _info[2].substr(1, 2),
      _info[2].substr(3, 1),
      _info[2].substr(4, 2),

      _info[3].substr(0, 1),
      _info[3].substr(1, 2),
      _info[3].substr(3, 1),
      _info[3].substr(4, 2),

      _info[4].substr(0, 1),
      _info[4].substr(1, 2),
      _info[4].substr(3, 1),
      _info[4].substr(4, 2),

      _info[5].substr(0, 1),
      _info[5].substr(1, 2),
      _info[5].substr(3, 1),
      _info[5].substr(4, 2)
    ]
    return parseInt(_calday[n - 1])
  },

  /**
      * Truyền vào số tháng âm lịch, trả về cách gọi thông dụng bằng chữ Hán
      * @param lunar month
      * @return Cn string
      * @eg:var cnMonth = calendar.toChinaMonth(12) ;//cnMonth='Chạp'
      */
  toChinaMonth: function (m) { // tháng âm lịch => tên gọi
    if (m > 12 || m < 1) { return -1 } // Nếu tham số sai thì trả về -1
    return 'Tháng ' + this.nStr3[m - 1]
  },

  /**
      * Truyền vào số ngày âm lịch, trả về cách viết bằng chữ Hán
      * @param lunar day
      * @return Cn string
      * @eg:var cnDay = calendar.toChinaDay(21) ;//cnMonth='hai mươi mốt'
      */
  toChinaDay: function (d) { // ngày âm lịch => tên gọi (Mùng 1 ... Mùng 10, 11 ... 30)
    return d <= 10 ? this.nStr2[0] + ' ' + d : String(d)
  },

  /**
      * Chuyển năm sang con giáp [!chỉ có thể chuyển đổi gần đúng] => Ranh giới phân chia con giáp chính xác là “Lập xuân”
      * @param y year
      * @return Cn string
      * @eg:var animal = calendar.getAnimal(1987) ;//animal='Thỏ'
      */
  getAnimal: function (y) {
    return this.Animals[(y - 4) % 12]
  },

  /**
      * Truyền vào năm tháng ngày dương lịch để lấy object thông tin chi tiết dương lịch, âm lịch <=>JSON
      * @param y  solar year
      * @param m  solar month
      * @param d  solar day
      * @return JSON object
      * @eg:console.log(calendar.solar2lunar(1987,11,01));
      */
  solar2lunar: function (y, m, d) { // Khoảng tham số 1900.1.31~2100.12.31
    // Giới hạn năm, giới hạn trên
    if (y < 1900 || y > 2100) {
      return -1// undefined chuyển thành số sẽ ra NaN
    }
    // Giới hạn dưới của tham số dương lịch truyền vào
    if (y == 1900 && m == 1 && d < 31) {
      return -1
    }
    // Không truyền tham số thì lấy ngày hôm nay
    if (!y) {
      var objDate = new Date()
    } else {
      var objDate = new Date(y, parseInt(m) - 1, d)
    }
    var i; var leap = 0; var temp = 0
    // Chuẩn hóa tham số ymd
    var y = objDate.getFullYear()
    var m = objDate.getMonth() + 1
    var d = objDate.getDate()
    var offset = (Date.UTC(objDate.getFullYear(), objDate.getMonth(), objDate.getDate()) - Date.UTC(1900, 0, 31)) / 86400000
    for (i = 1900; i < 2101 && offset > 0; i++) {
      temp = this.lYearDays(i)
      offset -= temp
    }
    if (offset < 0) {
      offset += temp; i--
    }

    // Có phải hôm nay hay không
    var isTodayObj = new Date()
    var isToday = false
    if (isTodayObj.getFullYear() == y && isTodayObj.getMonth() + 1 == m && isTodayObj.getDate() == d) {
      isToday = true
    }
    // Thứ mấy trong tuần
    var nWeek = objDate.getDay()
    var cWeek = this.nStr1[nWeek]
    // Số biểu thị thứ mấy theo quy ước tuần bắt đầu từ thứ Hai
    if (nWeek == 0) {
      nWeek = 7
    }
    // Năm âm lịch
    var year = i
    var leap = this.leapMonth(i) // Nhuận tháng nào
    var isLeap = false

    // Kiểm tra tháng nhuận
    for (i = 1; i < 13 && offset > 0; i++) {
      // Tháng nhuận
      if (leap > 0 && i == (leap + 1) && isLeap == false) {
        --i
        isLeap = true; temp = this.leapDays(year) // Tính số ngày tháng nhuận âm lịch
      } else {
        temp = this.monthDays(year, i)// Tính số ngày tháng thường âm lịch
      }
      // Bỏ tháng nhuận
      if (isLeap == true && i == (leap + 1)) { isLeap = false }
      offset -= temp
    }
    // Tháng nhuận khiến chỉ số mảng bị trùng nên phải đảo ngược
    if (offset == 0 && leap > 0 && i == leap + 1) {
      if (isLeap) {
        isLeap = false
      } else {
        isLeap = true; --i
      }
    }
    if (offset < 0) {
      offset += temp; --i
    }
    // Tháng âm lịch
    var month = i
    // Ngày âm lịch
    var day = offset + 1
    // Xử lý Can Chi
    var sm = m - 1
    var gzY = this.toGanZhiYear(year)

    // Hai tiết khí của tháng đó
    // bugfix-2017-7-24 11:03:38 use lunar Year Param `y` Not `year`
    var firstNode = this.getTerm(y, (m * 2 - 1))// Trả về “tiết” của tháng đó bắt đầu từ ngày nào
    var secondNode = this.getTerm(y, (m * 2))// Trả về “tiết” của tháng đó bắt đầu từ ngày nào

    // Dựa theo 12 tiết khí để chuẩn hóa tháng Can Chi
    var gzM = this.toGanZhi((y - 1900) * 12 + m + 11)
    if (d >= firstNode) {
      gzM = this.toGanZhi((y - 1900) * 12 + m + 12)
    }

    // Ngày truyền vào có phải là tiết khí hay không
    var isTerm = false
    var Term = null
    if (firstNode == d) {
      isTerm = true
      Term = this.solarTerm[m * 2 - 2]
    }
    if (secondNode == d) {
      isTerm = true
      Term = this.solarTerm[m * 2 - 1]
    }
    // Trụ ngày: số ngày chênh lệch giữa ngày 1 của tháng đó với 1/1/1900
    var dayCyclical = Date.UTC(y, sm, 1, 0, 0, 0, 0) / 86400000 + 25567 + 10
    var gzD = this.toGanZhi(dayCyclical + d - 1)
    // Cung hoàng đạo của ngày đó
    var astro = this.toAstro(m, d)

    return { 'lYear': year, 'lMonth': month, 'lDay': day, 'Animal': this.getAnimal(year), 'IMonthCn': (isLeap ? 'Nhuận ' : '') + this.toChinaMonth(month), 'IDayCn': this.toChinaDay(day), 'cYear': y, 'cMonth': m, 'cDay': d, 'gzYear': gzY, 'gzMonth': gzM, 'gzDay': gzD, 'isToday': isToday, 'isLeap': isLeap, 'nWeek': nWeek, 'ncWeek': cWeek, 'isTerm': isTerm, 'Term': Term, 'astro': astro }
  },

  /**
      * Truyền vào năm tháng ngày âm lịch và tháng truyền vào có phải tháng nhuận hay không để lấy object thông tin chi tiết dương lịch, âm lịch <=>JSON
      * @param y  lunar year
      * @param m  lunar month
      * @param d  lunar day
      * @param isLeapMonth  lunar month is leap or not.[nếu là tháng nhuận âm lịch thì chỉ cần gán tham số thứ tư là true]
      * @return JSON object
      * @eg:console.log(calendar.lunar2solar(1987,9,10));
      */
  lunar2solar: function (y, m, d, isLeapMonth) { // Khoảng tham số 1900.1.31~2100.12.1
    var isLeapMonth = !!isLeapMonth
    var leapOffset = 0
    var leapMonth = this.leapMonth(y)
    var leapDay = this.leapDays(y)
    if (isLeapMonth && (leapMonth != m)) { return -1 }// Tham số yêu cầu tính dương lịch của tháng nhuận đó nhưng tháng nhuận tính ra của năm đó lại không giống tháng truyền vào
    if (y == 2100 && m == 12 && d > 1 || y == 1900 && m == 1 && d < 31) { return -1 }// Vượt quá giá trị giới hạn tối đa
    var day = this.monthDays(y, m)
    var _day = day
    // bugFix 2016-9-25
    // if month is leap, _day use leapDays method
    if (isLeapMonth) {
      _day = this.leapDays(y, m)
    }
    if (y < 1900 || y > 2100 || d > _day) { return -1 }// Kiểm tra tính hợp lệ của tham số

    // Tính khoảng chênh lệch thời gian âm lịch
    var offset = 0
    for (var i = 1900; i < y; i++) {
      offset += this.lYearDays(i)
    }
    var leap = 0; var isAdd = false
    for (var i = 1; i < m; i++) {
      leap = this.leapMonth(y)
      if (!isAdd) { // Xử lý tháng nhuận
        if (leap <= i && leap > 0) {
          offset += this.leapDays(y); isAdd = true
        }
      }
      offset += this.monthDays(y, i)
    }
    // Khi chuyển đổi âm lịch tháng nhuận cần bổ sung chênh lệch thời gian của tháng trước tháng nhuận của năm đó
    if (isLeapMonth) { offset += day }
    // Thời điểm dương lịch của ngày 1 tháng Giêng âm lịch năm 1900 là 0 giờ 0 phút 0 giây ngày 30/1/1900 (đây cũng là điểm bắt đầu của âm lịch này)
    var stmap = Date.UTC(1900, 1, 30, 0, 0, 0)
    var calObj = new Date((offset + d - 31) * 86400000 + stmap)
    var cY = calObj.getUTCFullYear()
    var cM = calObj.getUTCMonth() + 1
    var cD = calObj.getUTCDate()

    return this.solar2lunar(cY, cM, cD)
  }
}

export default calendar
