import md5 from 'js-md5'; //Import mã hóa MD5
import { upload } from '@/api/upload.js'; // Đây chỉ phương thức api mà frontend gọi tới interface
export const uploadByPieces = ({ file, pieceSize = 2, success, error, uploading }) => {
  // Nếu file truyền vào là rỗng thì return luôn
  if (!file) return;
  let fileMD5 = ''; // Danh sách tổng các file
  const chunkSize = pieceSize * 1024 * 1024; // Mỗi phần 5MB
  const chunkCount = Math.ceil(file.size / chunkSize); // Tổng số phần (chunk)
  // Lấy md5
  const readFileMD5 = () => {
    // Đọc md5 của file video
    let fileRederInstance = new FileReader();
    fileRederInstance.readAsBinaryString(file);
    fileRederInstance.addEventListener('load', (e) => {
      let fileBolb = e.target.result;
      fileMD5 = md5(fileBolb);
      readChunkMD5();
    });
  };
  const getChunkInfo = (file, currentChunk, chunkSize) => {
    let start = currentChunk * chunkSize;
    let end = Math.min(file.size, start + chunkSize);
    let chunk = file.slice(start, end);
    return { start, end, chunk };
  };
  // Xử lý chunk cho từng file
  const readChunkMD5 = async () => {
    // Upload chunk cho từng file riêng lẻ
    for (var i = 0; i < chunkCount; i++) {
      const { chunk } = getChunkInfo(file, i, chunkSize);
      await uploadChunk({ chunk, currentChunk: i, chunkCount });
    }
  };
  const uploadChunk = (chunkInfo) => {
    // progressFun()
    return new Promise((resolver, reject) => {
      let config = {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      };
      // Tạo đối tượng formData, dưới đây là đối tượng truyền cho backend kết hợp với từng project khác nhau.
      let fetchForm = new FormData();
      fetchForm.append('chunkNumber', chunkInfo.currentChunk + 1); // Phần thứ mấy
      fetchForm.append('chunkSize', chunkSize); // Giới hạn kích thước phân đoạn (chunk)  ví dụ giới hạn 5M
      fetchForm.append('currentChunkSize', chunkInfo.chunk.size); // Kích thước mỗi phần
      fetchForm.append('file', chunkInfo.chunk); //File của mỗi phần
      fetchForm.append('filename', file.name); // Tên tệp
      fetchForm.append('totalChunks', chunkInfo.chunkCount); //Tổng số phần (chunk)
      fetchForm.append('md5', fileMD5);
      upload(fetchForm, config)
        .then((res) => {
          if (res.data.code == 1) {
            // // Kết hợp với từng project để trả về thông tin thành công
            // Nếu bên dưới không dùng trong project thì không cần mở comment
            uploading(chunkInfo.currentChunk + 1, chunkInfo.chunkCount);
            resolver(true);
          } else if (res.data.code == 2) {
            if (chunkInfo.currentChunk < chunkInfo.chunkCount - 1) {
            } else {
              // Khi tổng số lớn hơn hoặc bằng số phần chia
              if (chunkInfo.currentChunk + 1 == chunkInfo.chunkCount) {
                success(res.data);
              }
            }
          }
        })
        .catch((e) => {
          error && error(e);
        });
    });
  };
  readFileMD5(); // Bắt đầu thực thi code
};
