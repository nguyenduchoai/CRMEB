// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import { export_json_to_excel } from '../vendor/Export2Excel';

/**
 * @method exportExcel
 * @param {Array} header   Đầu bảng
 * @param {Array} filterVal Trường thuộc tính đầu bảng
 * @param {String} filename Tên tệp
 * @param {Array} tableData Dữ liệu danh sách
 **/
export default function exportExcel(header, filterVal, filename, tableData) {
  var data = formatJson(filterVal, tableData);
  export_json_to_excel(header, data, filename);
}

function formatJson(filterVal, tableData) {
  return tableData.map((v) => {
    return filterVal.map((j) => {
      return v[j];
    });
  });
}
