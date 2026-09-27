
function LotteryDraw(obj, callback) {
	this.timer = null; //Bộ đếm thời gian (timer)
	this.startIndex = obj.startIndex-1 || 0; //Bắt đầu quay thưởng từ vị trí thứ mấy [mặc định là 0]
	this.count = 0; //Đếm số vòng đã quay
	this.winingIndex = obj.winingIndex || 0;//Vị trí trúng thưởng
	this.totalCount = obj.totalCount || 6;//Số vòng quay thưởng
	this.speed = obj.speed || 100;
	this.domData=obj.domData;
	this.rollFn();
	this.callback = callback;
}

LotteryDraw.prototype = {
	rollFn: function() {
		var that = this;
		// Tăng giá trị index của hoạt động, tức di chuyển sang ô tiếp theo
		this.startIndex++;
		
		//Khi startIndex là ô cuối cùng thì đi hết một vòng, bắt đầu lại
		if (this.startIndex >= this.domData.length - 1) {
			this.startIndex = 0;
			this.count++;
		}
		
		// Dừng khi số vòng đã quay bằng số vòng đã đặt và giá trị index hoạt động là vị trí giải thưởng
		if (this.count >= this.totalCount && this.startIndex === this.winingIndex) {
			if (typeof this.callback === 'function') {
				setTimeout(function() {
					that.callback(that.startIndex,that.count); //Thực thi hàm callback, các thao tác liên quan khi quay thưởng hoàn tất
				}, 400);
			}
			clearInterval(this.timer);
		}else { //Bắt đầu lại một vòng
			if (this.count >= this.totalCount - 1) {
				this.speed += 30;
			}
			this.timer = setTimeout(function() {
				that.callback(that.startIndex,that.count);
				that.rollFn();
			}, this.speed);
		}
	}
}

module.exports = LotteryDraw;