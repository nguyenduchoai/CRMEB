import Vue from 'vue';
import loadingCss from '@/theme/loading.scss';

// Định nghĩa phương thức
export const PrevLoading = {
  // Nạp css
  setCss: () => {
    let link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = loadingCss;
    link.crossOrigin = 'anonymous';
    document.getElementsByTagName('head')[0].appendChild(link);
  },
  // Tạo loading
  start: () => {
    const bodys = document.body;
    const div = document.createElement('div');
    div.setAttribute('class', 'loading-prev');
    const htmls = `
			<div class="loading-prev-box">
			<div class="loading-prev-box-warp">
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
				<div class="loading-prev-box-item"></div>
			</div>
		</div>
		`;
    div.innerHTML = htmls;
    bodys.insertBefore(div, bodys.childNodes[0]);
  },
  // Xóa loading
  done: () => {
    Vue.nextTick(() => {
      setTimeout(() => {
        const el = document.querySelector('.loading-prev');
        el && el.parentNode?.removeChild(el);
      }, 1000);
    });
  },
};
