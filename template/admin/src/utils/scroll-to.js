Math.easeInOutQuad = function (t, b, c, d) {
  t /= d / 2;
  if (t < 1) {
    return (c / 2) * t * t + b;
  }
  t--;
  return (-c / 2) * (t * (t - 2) - 1) + b;
};

//requestAnimationFrame dùng cho animation thông minh http://goo.gl/sx5sts
var requestAnimFrame = (function () {
  return (
    window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    function (callback) {
      window.setTimeout(callback, 1000 / 60);
    }
  );
})();

/**
 * Vì việc phát hiện phần tử cuộn quá khó, chỉ cần di chuyển tất cả chúng là được
 * @param {number} amount
 */
function move(amount) {
  document.documentElement.scrollTop = amount;
  document.body.parentNode.scrollTop = amount;
  document.body.scrollTop = amount;
}

function position() {
  return document.documentElement.scrollTop || document.body.parentNode.scrollTop || document.body.scrollTop;
}

/**
 * @param {number} to
 * @param {number} duration
 * @param {Function} callback
 */
export function scrollTo(to, duration, callback) {
  const start = position();
  const change = to - start;
  const increment = 20;
  let currentTime = 0;
  duration = typeof duration === 'undefined' ? 500 : duration;
  var animateScroll = function () {
    // Tăng số lần
    currentTime += increment;
    // Dùng hàm Math để tìm giá trị này
    var val = Math.easeInOutQuad(currentTime, start, change, duration);
    // Di chuyển phần tử này
    move(val);
    // Animation đã kết thúc hay chưa
    if (currentTime < duration) {
      requestAnimFrame(animateScroll);
    } else {
      if (callback && typeof callback === 'function') {
        //Animation đã hoàn thành, thực hiện callback
        callback();
      }
    }
  };
  animateScroll();
}
