/**
 * @Phương thức chung nén
 * @params file
 * @return File sau khi nén, hỗ trợ 2 loại: file và blob
 */
export default function compressImg(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    // Phương thức readAsDataURL sẽ đọc đối tượng Blob hoặc File được chỉ định. Khi đọc xong, readyState sẽ chuyển thành DONE (đã hoàn thành), và kích hoạt event loadend (en-US),
    // Đồng thời thuộc tính result sẽ chứa một chuỗi dạng data:URL (mã hóa base64) để biểu diễn nội dung file đã đọc.
    reader.readAsDataURL(file);
    reader.onload = () => {
      const img = new Image();
      img.src = reader.result;
      img.onload = () => {
        // Chiều rộng cao của ảnh
        const w = img.width;
        const h = img.height;
        const canvas = document.createElement('canvas');
        // canvas cắt ảnh, ở đây đặt bằng kích thước gốc của ảnh
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');
        // Trong canvas, chuyển png sang jpg sẽ bị nền đen, nên trải một nền trắng cho canvas trước
        ctx.fillStyle = '#fff';
        // Phương thức fillRect() vẽ một hình chữ nhật được lấp đầy nội dung, điểm bắt đầu (điểm trên trái) của hình chữ nhật này ở
        // (x, y), chiều rộng và chiều cao của nó được xác định lần lượt bởi width và height, style lấp đầy do fillStyle hiện tại quyết định.
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        // Vẽ ảnh
        ctx.drawImage(img, 0, 0, w, h);

        // canvas chuyển thành ảnh để đạt hiệu ứng nén ảnh
        // Trả về một data URI base64 chứa ảnh hiển thị, trong trường hợp định dạng ảnh được chỉ định là image/jpeg hoặc image/webp,
        // có thể chọn chất lượng ảnh trong khoảng từ 0 đến 1. Nếu vượt quá phạm vi giá trị, sẽ dùng giá trị mặc định 0.92. Các tham số khác sẽ bị bỏ qua.
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        let newFile = dataURLtoFile(dataUrl, file.name);
        resolve(newFile);
      };
    };
  });
}
//  base64->file
function dataURLtoFile(dataurl, fileName) {
  let arr = dataurl.split(','),
    mime = arr[0].match(/:(.*?);/)[1],
    bstr = atob(arr[1]),
    n = bstr.length,
    u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new File([u8arr], fileName, { type: mime });
}
// base64->blob
function dataURLtoBlob(dataurl) {
  const arr = dataurl.split(','),
    mime = arr[0].match(/:(.*?);/)[1],
    bstr = atob(arr[1]);
  let n = bstr.length;
  const u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new Blob([u8arr], { type: mime });
}
