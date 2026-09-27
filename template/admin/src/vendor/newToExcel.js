// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import { isEmpty } from 'element-ui/lib/utils/util';
import ExcelJS from 'exceljs';
import * as FileSaver from 'file-saver';

export default function createWorkBook(header, title, data, foot, filename, sheets) {
  const letter = [
    'A',
    'B',
    'C',
    'D',
    'E',
    'F',
    'G',
    'H',
    'I',
    'J',
    'K',
    'L',
    'M',
    'N',
    'O',
    'P',
    'Q',
    'R',
    'S',
    'T',
    'U',
    'V',
    'W',
    'X',
    'Y',
    'Z',
  ];
  let lcomun = 1;
  let worksheet;

  const workBook = new ExcelJS.Workbook();
  let long = header.length;

  /**
   *  Tạo workbook
   * @param {*} sheets
   */
  function createSheets(sheets) {
    let sheet = Array.isArray(sheets) ? sheets[0] : sheets;
    let style = Array.isArray(sheets) ? sheets[1] : {};
    worksheet = workBook.addWorksheet(sheet, style);
  }

  /**
   *  Thiết lập tên bảng, phần giới thiệu, v.v.
   * @param {*} title
   * @param {*} long
   */
  function setTitle(title, long) {
    if (isEmpty(title)) return;
    title = Array.isArray(title) ? title : title.split(',');
    for (let i = 0; i < title.length; i++) {
      let ti = worksheet.getRow(i + 1);
      ti.getCell(1).value = title[i];
      ti.height = 30;
      ti.font = { bold: true, size: 20, vertAlign: 'subscript' };
      ti.alignment = { vertical: 'bottom', horizontal: 'center' };
      ti.outlineLevel = 1;
      worksheet.mergeCells(i + 1, 1, i + 1, long);
      ti.commit();
      lcomun++;
    }
  }
  /**
   *  Thiết lập hàng tiêu đề bảng
   * @param {*} header
   */
  function setHeader(header) {
    if (isEmpty(header)) return;
    const headerRow = worksheet.getRow(lcomun);
    for (let index = 1; index <= header.length; index++) {
      headerRow.getCell(index).value = header[index - 1];
    }
    headerRow.height = 25;
    headerRow.width = 50;
    headerRow.font = { bold: true, size: 18, vertAlign: 'subscript' };
    headerRow.alignment = { vertical: 'bottom', horizontal: 'center' };
    headerRow.outlineLevel = 1;
    headerRow.commit();
    lcomun++;
  }

  /**
   * Nội dung xuất
   * @param {*} data
   */
  function setContent(data) {
    if (isEmpty(data)) return;
    for (let h = 0; h < data.length; h++) {
      let satarLcomun = lcomun;
      let lcomunNow = worksheet.getRow(lcomun);
      let hasMerge = false;
      let starKey = 0;
      let endKey = 0;
      /** Lặp qua các cột */
      //Cột cần thao tác
      let sk = 0;
      for (let l = 0; l < data[h].length; l++) {
        if (Array.isArray(data[h][l])) {
          //Độ dài mảng
          starKey = sk;
          hasMerge = true;
          setArrayContent(data[h][l], sk);
          sk = sk + data[h][l][0].length;
          endKey = sk;
        } else {
          //Không phải mảng
          lcomunNow.getCell(getLetter(sk)).value = data[h][l];
          lcomunNow.getCell(getLetter(sk)).border = {
            top: { style: 'thin' },
            left: { style: 'thin' },
            bottom: { style: 'thin' },
            right: { style: 'thin' },
          };
          lcomunNow.alignment = { vertical: 'middle', horizontal: 'center' };
          sk++;
        }
      }
      if (hasMerge) setMergeLcomun(satarLcomun, lcomun, starKey, endKey);
      lcomunNow.height = 25;
      lcomunNow.commit();
      lcomun++;
    }
  }
  /**
   * Mảng chiếm nhiều hàng
   * @param {*} arr
   * @param {*} sk
   */
  function setArrayContent(arr, sk) {
    /**
     *  Lặp mảng hai chiều, rồi lặp theo từng hàng
     */
    let al = arr.length;
    let sl = al - 1;
    for (let i = 0; i < arr.length; i++) {
      let lcomunNow = worksheet.getRow(lcomun);
      for (let v = 0; v < arr[i].length; v++) {
        lcomunNow.getCell(getLetter(sk + v)).value = arr[i][v];
        lcomunNow.getCell(getLetter(sk + v)).border = {
          top: { style: 'thin' },
          left: { style: 'thin' },
          bottom: { style: 'thin' },
          right: { style: 'thin' },
        };
        lcomunNow.alignment = { vertical: 'middle', horizontal: 'center' };
      }
      lcomunNow.height = 25;
      lcomunNow.commit();
      if (i < sl) lcomun++;
    }
  }
  /**
   *  Thao tác gộp ô
   * @param {*} satarLcomun
   * @param {*} endLcomun
   * @param {*} starKey
   * @param {*} endKey
   */
  function setMergeLcomun(satarLcomun, endLcomun, starKey, endKey) {
    for (let ml = 0; ml < long; ml++) {
      if (ml < starKey || ml >= endKey) {
        worksheet.mergeCells(getLetter(ml) + satarLcomun + ':' + getLetter(ml) + endLcomun);
      }
    }
  }

  /**
   * Thiết lập phần thống kê, ghi chú ở cuối bảng, v.v.
   * @param {*} footData
   */
  function setFoot(footData) {
    if (isEmpty(footData)) return;
    if (Array.isArray(footData)) {
      for (let f = 0; f < footData.length; f++) {
        let lcomunNow = worksheet.getRow(lcomun);
        lcomunNow.getCell(1).value = footData[f];
        lcomunNow.getCell(1).border = {
          top: { style: 'thin' },
          left: { style: 'thin' },
          bottom: { style: 'thin' },
          right: { style: 'thin' },
        };
        lcomunNow.alignment = { vertical: 'middle', horizontal: 'left' };
        worksheet.mergeCells('A' + lcomun + ':' + getLetter(long - 1) + lcomun);
        lcomun++;
      }
    } else {
      let lcomunNow = worksheet.getRow(lcomun);
      lcomunNow.getCell(1).value = footData[f];
      lcomunNow.getCell(1).border = {
        top: { style: 'thin' },
        left: { style: 'thin' },
        bottom: { style: 'thin' },
        right: { style: 'thin' },
      };
      lcomunNow.alignment = { vertical: 'middle', horizontal: 'left' };
      worksheet.mergeCells('A' + lcomun + ':' + getLetter(long - 1) + lcomun);
    }
  }

  /**
   * Xử lý cột vượt quá 26 chữ cái
   * @param {number} number - Chỉ số cột (số)
   * @returns {string} Ký hiệu cột Excel (A, B, C, ..., Z, AA, AB, ...)
   */
  function getLetter(number) {
    let char = new Array(
      'A',
      'B',
      'C',
      'D',
      'E',
      'F',
      'G',
      'H',
      'I',
      'J',
      'K',
      'L',
      'M',
      'N',
      'O',
      'P',
      'Q',
      'R',
      'S',
      'T',
      'U',
      'V',
      'W',
      'X',
      'Y',
      'Z',
    );
    let charS = char[parseInt(number / 26) - 1];

    for (let i = 0; i < 26; i++) {
      if (parseInt(number / 26) >= 1) {
        char[i] = charS + char[i];
      }
    }
    let charE = [];
    if (parseInt(number / 26) < 1) {
      charE = char[number - 25 * parseInt(number / 26)];
    } else {
      charE = char[number - 25 * parseInt(number / 26) - parseInt(number / 26)];
    }

    return charE;
  }

  /**
   *  Xuất và tải xuống
   * @param {*} filename
   */
  function saveAndDowloade(filename) {
    if (!filename) filename = new Date().getTime();
    workBook.xlsx.writeBuffer().then((data) => {
      const blob = new Blob([data], { type: 'application/octet-stream' });
      FileSaver.saveAs(blob, filename + '.xlsx');
    });
  }

  createSheets(sheets);
  setTitle(title, long);
  setHeader(header);
  setContent(data);
  setFoot(foot);
  saveAndDowloade(filename);
}
