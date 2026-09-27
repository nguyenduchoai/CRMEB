const ruleShip = {
  deliver_name: [
    {
      required: true,
      type: 'string',
      message: 'Vui lòng chọn đơn vị vận chuyển',
      trigger: 'select',
    },
  ],
  deliver_number: [
    {
      required: true,
      message: 'Vui lòng nhập mã vận đơn',
      trigger: 'blur',
    },
  ],
};
const ruleMark = {
  mark: [
    {
      required: true,
      message: 'Vui lòng nhập thông tin ghi chú',
      trigger: 'blur',
    },
  ],
};
export { ruleShip, ruleMark };
