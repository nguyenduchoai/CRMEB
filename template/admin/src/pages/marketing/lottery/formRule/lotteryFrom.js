const lotteryFrom = {
  name: [{ required: true, message: 'Vui lòng nhập tên chương trình', trigger: 'blur' }],
  factor: [{ required: true, type: 'number', message: 'Vui lòng chọn loại chương trình', trigger: 'change' }],
  attends_user: [{ required: true, type: 'number', message: 'Vui lòng chọn người dùng tham gia', trigger: 'change' }],
  factor_num: [{ required: true, type: 'number', message: 'Vui lòng nhập số lượt quay', trigger: 'blur' }],
  prize: [
    {
      required: true,
      type: 'array',
      message: 'Vui lòng thêm giải thưởng (8 giải)',
      trigger: 'change',
    },
    {
      type: 'array',
      min: 8,
      message: 'Vui lòng thêm giải thưởng (8 giải)',
      trigger: 'change',
    },
  ],
  lottery_num: [
    {
      required: true,
      type: 'number',
      message: 'Vui lòng nhập số lượt quay tối đa nhận được khi mời người dùng mới',
      trigger: 'blur',
    },
  ],
  spread_num: [
    {
      required: true,
      type: 'number',
      message: 'Vui lòng nhập số lượt quay thêm khi theo dõi',
      trigger: 'blur',
    },
  ],
  image: [
    {
      required: true,
      message: 'Vui lòng tải lên ảnh nền chương trình',
      trigger: 'change',
    },
  ],
  content: [
    {
      required: true,
      message: 'Vui lòng điền thể lệ chương trình',
      trigger: 'blur',
    },
  ],
};
function validate(rule, value, callback) {
  if (Array.isArray(value)) {
    //Kiểm tra định dạng: daterange, datetimerange
    value.map(function (item) {
      if (item === '') {
        return callback('Ngày không được để trống');
      }
    });
  } else {
    //Định dạng là: date, datetime, year, month kiểm tra
    if (value === '') {
      return callback('Ngày không được để trống');
    }
  }
  return callback();
}

export { lotteryFrom };
