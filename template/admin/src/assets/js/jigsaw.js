// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import './jigsaw.css';

let w = 310; // Chiều rộng canvas
let h = 155; // Chiều cao canvas
const l = 42; // Cạnh của thanh trượt
const r = 9; // Bán kính thanh trượt
const PI = Math.PI;
const L = l + r * 2 + 3; // Cạnh thực tế của thanh trượt

function getRandomNumberByRange(start, end) {
  return Math.round(Math.random() * (end - start) + start);
}

function createCanvas(width, height) {
  const canvas = document.createElement('canvas');
  canvas.width = width;
  canvas.height = height;
  return canvas;
}

function createImg(onload) {
  const img = new Image();
  img.crossOrigin = 'Anonymous';
  img.onload = onload;
  img.onerror = () => {
    img.setSrc(getRandomImgSrc());
  };

  img.setSrc = function (src) {
    const isIE = window.navigator.userAgent.indexOf('Trident') > -1;
    if (isIE) {
      // Trình duyệt IE không thể cross-domain qua img.crossOrigin, dùng ajax lấy blob ảnh rồi chuyển thành dataURL để hiển thị
      const xhr = new XMLHttpRequest();
      xhr.onloadend = function (e) {
        const file = new FileReader(); // FileReader chỉ hỗ trợ IE10+
        file.readAsDataURL(e.target.response);
        file.onloadend = function (e) {
          img.src = e.target.result;
        };
      };
      xhr.open('GET', src);
      xhr.responseType = 'blob';
      xhr.send();
    } else img.src = src;
  };

  img.setSrc(getRandomImgSrc());
  return img;
}

function createElement(tagName, className) {
  const elment = document.createElement(tagName);
  elment.className = className;
  return elment;
}

function addClass(tag, className) {
  tag.classList.add(className);
}

function removeClass(tag, className) {
  tag.classList.remove(className);
}

function getRandomImgSrc() {
  return `https://picsum.photos/${w}/${h}/?image=${getRandomNumberByRange(0, 1084)}`;
}

function draw(ctx, x, y, operation) {
  ctx.beginPath();
  ctx.moveTo(x, y);
  ctx.arc(x + l / 2, y - r + 2, r, 0.72 * PI, 2.26 * PI);
  ctx.lineTo(x + l, y);
  ctx.arc(x + l + r - 2, y + l / 2, r, 1.21 * PI, 2.78 * PI);
  ctx.lineTo(x + l, y + l);
  ctx.lineTo(x, y + l);
  ctx.arc(x + r - 2, y + l / 2, r + 0.4, 2.76 * PI, 1.24 * PI, true);
  ctx.lineTo(x, y);
  ctx.lineWidth = 2;
  ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
  ctx.strokeStyle = 'rgba(255, 255, 255, 0.7)';
  ctx.stroke();
  ctx[operation]();
  ctx.globalCompositeOperation = 'destination-over';
}

function sum(x, y) {
  return x + y;
}

function square(x) {
  return x * x;
}

class jigsaw {
  constructor({ el, width = 310, height = 155, onSuccess, onFail, onRefresh }) {
    w = width;
    h = height;
    el.style.position = 'relative';
    el.style.width = w + 'px';
    Object.assign(el.style, {
      position: 'relative',
      width: w + 'px',
      margin: '0 auto',
    });
    this.el = el;
    this.onSuccess = onSuccess;
    this.onFail = onFail;
    this.onRefresh = onRefresh;
  }

  init() {
    this.initDOM();
    this.initImg();
    this.bindEvents();
  }

  initDOM() {
    const canvas = createCanvas(w, h); // Canvas (khung vẽ)
    const block = canvas.cloneNode(true); // Thanh trượt
    const sliderContainer = createElement('div', 'sliderContainer');
    sliderContainer.style.width = w + 'px';
    const refreshIcon = createElement('div', 'refreshIcon');
    const sliderMask = createElement('div', 'sliderMask');
    const slider = createElement('div', 'slider');
    const sliderIcon = createElement('span', 'sliderIcon');
    const text = createElement('span', 'sliderText');

    block.className = 'block';
    text.innerHTML = 'Kéo sang phải để hoàn thành ghép hình';

    const el = this.el;
    el.appendChild(canvas);
    el.appendChild(refreshIcon);
    el.appendChild(block);
    slider.appendChild(sliderIcon);
    sliderMask.appendChild(slider);
    sliderContainer.appendChild(sliderMask);
    sliderContainer.appendChild(text);
    el.appendChild(sliderContainer);

    Object.assign(this, {
      canvas,
      block,
      sliderContainer,
      refreshIcon,
      slider,
      sliderMask,
      sliderIcon,
      text,
      canvasCtx: canvas.getContext('2d'),
      blockCtx: block.getContext('2d'),
    });
  }

  initImg() {
    const img = createImg(() => {
      this.draw();
      this.canvasCtx.drawImage(img, 0, 0, w, h);
      this.blockCtx.drawImage(img, 0, 0, w, h);
      const y = this.y - r * 2 - 1;
      const ImageData = this.blockCtx.getImageData(this.x - 3, y, L, L);
      this.block.width = L;
      this.blockCtx.putImageData(ImageData, 0, y);
    });
    this.img = img;
  }

  draw() {
    // Tạo ngẫu nhiên vị trí thanh trượt
    this.x = getRandomNumberByRange(L + 10, w - (L + 10));
    this.y = getRandomNumberByRange(10 + r * 2, h - (L + 10));
    draw(this.canvasCtx, this.x, this.y, 'fill');
    draw(this.blockCtx, this.x, this.y, 'clip');
  }

  clean() {
    this.canvasCtx.clearRect(0, 0, w, h);
    this.blockCtx.clearRect(0, 0, w, h);
    this.block.width = w;
  }

  bindEvents() {
    this.el.onselectstart = () => false;
    this.refreshIcon.onclick = () => {
      this.reset();
      typeof this.onRefresh === 'function' && this.onRefresh();
    };

    let originX,
      originY,
      trail = [],
      isMouseDown = false;

    const handleDragStart = function (e) {
      originX = e.clientX || e.touches[0].clientX;
      originY = e.clientY || e.touches[0].clientY;
      isMouseDown = true;
    };

    const handleDragMove = (e) => {
      if (!isMouseDown) return false;
      const eventX = e.clientX || e.touches[0].clientX;
      const eventY = e.clientY || e.touches[0].clientY;
      const moveX = eventX - originX;
      const moveY = eventY - originY;
      if (moveX < 0 || moveX + 38 >= w) return false;
      this.slider.style.left = moveX + 'px';
      const blockLeft = ((w - 40 - 20) / (w - 40)) * moveX;
      this.block.style.left = blockLeft + 'px';

      addClass(this.sliderContainer, 'sliderContainer_active');
      this.sliderMask.style.width = moveX + 'px';
      trail.push(moveY);
    };

    const handleDragEnd = (e) => {
      if (!isMouseDown) return false;
      isMouseDown = false;
      const eventX = e.clientX || e.changedTouches[0].clientX;
      if (eventX === originX) return false;
      removeClass(this.sliderContainer, 'sliderContainer_active');
      this.trail = trail;
      const { spliced, verified } = this.verify();
      if (spliced) {
        if (verified) {
          addClass(this.sliderContainer, 'sliderContainer_success');
          typeof this.onSuccess === 'function' && this.onSuccess();
        } else {
          addClass(this.sliderContainer, 'sliderContainer_fail');
          this.text.innerHTML = 'Vui lòng thử lại';
          this.reset();
        }
      } else {
        addClass(this.sliderContainer, 'sliderContainer_fail');
        typeof this.onFail === 'function' && this.onFail();
        setTimeout(() => {
          this.reset();
        }, 1000);
      }
    };
    this.slider.addEventListener('mousedown', handleDragStart);
    this.slider.addEventListener('touchstart', handleDragStart);
    this.block.addEventListener('mousedown', handleDragStart);
    this.block.addEventListener('touchstart', handleDragStart);
    document.addEventListener('mousemove', handleDragMove);
    document.addEventListener('touchmove', handleDragMove);
    document.addEventListener('mouseup', handleDragEnd);
    document.addEventListener('touchend', handleDragEnd);
  }

  verify() {
    const arr = this.trail; // Khoảng di chuyển theo trục y khi kéo
    const average = arr.reduce(sum) / arr.length;
    const deviations = arr.map((x) => x - average);
    const stddev = Math.sqrt(deviations.map(square).reduce(sum) / arr.length);
    const left = parseInt(this.block.style.left);
    return {
      spliced: Math.abs(left - this.x) < 10,
      verified: stddev !== 0, // Kiểm tra đơn giản quỹ đạo kéo, nếu bằng 0 nghĩa là trục Y không có dao động lên xuống, có thể không phải do người thao tác
    };
  }

  reset() {
    this.sliderContainer.className = 'sliderContainer';
    this.slider.style.left = 0;
    this.block.style.left = 0;
    this.sliderMask.style.width = 0;
    this.clean();
    this.img.setSrc(getRandomImgSrc());
  }
}

window.jigsaw = {
  init: function (opts) {
    let $jigsaw = new jigsaw(opts);
    $jigsaw.init();
    return $jigsaw;
  },
};
