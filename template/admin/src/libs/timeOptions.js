export default {
  shortcuts: [
    {
      text: 'Hôm nay',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()));
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: 'Hôm qua',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(
          start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)),
        );
        end.setTime(end.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)));
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: 'Tháng này',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), 1)));
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: 'Tháng trước',
      onClick(picker) {
        const start = new Date();
        const end = new Date(start);
        end.setMonth(start.getMonth());
        start.setMonth(start.getMonth() - 1);
        end.setDate(0);
        start.setDate(1);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: '7 ngày qua',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: '30 ngày qua',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: '90 ngày qua',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
        picker.$emit('pick', [start, end]);
      },
    },

    {
      text: '1 năm qua',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 365);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: 'Năm nay',
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.setTime(new Date(new Date().getFullYear(), 0, 1)));
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: 'Năm ngoái',
      onClick(picker) {
        //Lấy thời gian hiện tại
        let currentDate = new Date();
        //Lấy năm hiện tại dạng 4 chữ số
        let currentYear = currentDate.getFullYear() - 1;
        //Ngày đầu tiên của năm nay
        const start = new Date(currentYear, 0, 1);
        //Ngày cuối cùng của năm nay
        const end = new Date(currentYear, 11, 31);
        //end.setHours(23, 59, 59, 0)
        picker.$emit('pick', [start, end]);
      },
    },
  ],
};
