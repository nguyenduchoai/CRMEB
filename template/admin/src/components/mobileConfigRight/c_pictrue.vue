<template>
  <div class="mobile-page">
    <div v-if="isUpdate">
      <!-- <div class="title">Bố cục</div>
            <div class="tip">Chọn vùng bố cục, thêm ảnh ở phía dưới, nên thêm ảnh có tỷ lệ giống nhau</div> -->
      <div class="advert">
        <div
          class="advertItem07"
          :class="currentIndex === index ? 'on' : ''"
          @click="currentTab(index, configData)"
          v-if="style === 0"
          v-for="(item, index) in configData.picList"
          :key="index"
        >
          <img :src="item.image" v-if="item.image" />
          <div class="empty-box" v-else>750*375</div>
        </div>
        <div class="advertItem02 acea-row" v-if="style === 1">
          <div
            class="item"
            :class="currentIndex === index ? 'on' : ''"
            @click="currentTab(index, configData)"
            v-for="(item, index) in configData.picList"
            :key="index"
          >
            <img :src="item.image" v-if="item.image" />
            <div class="empty-box" v-else>
              <div>
                <div>375*750</div>
              </div>
            </div>
          </div>
        </div>
        <div class="advertItem03 acea-row" v-if="style === 2">
          <div
            class="item"
            :class="currentIndex === index ? 'on' : ''"
            @click="currentTab(index, configData)"
            v-for="(item, index) in configData.picList"
            :key="index"
          >
            <img :src="item.image" v-if="item.image" />
            <div class="empty-box" v-else>
              <div>
                <div>250*750</div>
              </div>
            </div>
          </div>
        </div>
        <div class="advertItem08" v-if="style === 3">
          <div class="item acea-row">
            <div class="pic" :class="currentIndex === 0 ? 'on' : ''" @click="currentTab(0, configData)">
              <img :src="configData.picList[0].image" v-if="configData.picList[0].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
            <div class="pic" :class="currentIndex === 1 ? 'on' : ''" @click="currentTab(1, configData)">
              <img :src="configData.picList[1].image" v-if="configData.picList[1].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
          </div>
          <div class="item" :class="currentIndex === 2 ? 'on' : ''" @click="currentTab(2, configData)">
            <img :src="configData.picList[2].image" v-if="configData.picList[2].image" />
            <div class="empty-box" v-else>750*375</div>
          </div>
        </div>
        <div class="advertItem08" v-if="style === 4">
          <div class="item" :class="currentIndex === 0 ? 'on' : ''" @click="currentTab(0, configData)">
            <img :src="configData.picList[0].image" v-if="configData.picList[0].image" />
            <div class="empty-box" v-else>750*375</div>
          </div>
          <div class="item acea-row">
            <div class="pic" :class="currentIndex === 1 ? 'on' : ''" @click="currentTab(1, configData)">
              <img :src="configData.picList[1].image" v-if="configData.picList[1].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
            <div class="pic" :class="currentIndex === 2 ? 'on' : ''" @click="currentTab(2, configData)">
              <img :src="configData.picList[2].image" v-if="configData.picList[2].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
          </div>
        </div>
        <div class="advertItem04 acea-row" v-if="style === 5">
          <div class="item" :class="currentIndex === 0 ? 'on' : ''" @click="currentTab(0, configData)">
            <img :src="configData.picList[0].image" v-if="configData.picList[0].image" />
            <div class="empty-box" v-else>375*750</div>
          </div>
          <div class="item">
            <div class="pic" :class="currentIndex === 1 ? 'on' : ''" @click="currentTab(1, configData)">
              <img :src="configData.picList[1].image" v-if="configData.picList[1].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
            <div class="pic" :class="currentIndex === 2 ? 'on' : ''" @click="currentTab(2, configData)">
              <img :src="configData.picList[2].image" v-if="configData.picList[2].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
          </div>
        </div>
        <div class="advertItem04 acea-row" v-if="style === 6">
          <div class="item">
            <div class="pic" :class="currentIndex === 0 ? 'on' : ''" @click="currentTab(0, configData)">
              <img :src="configData.picList[0].image" v-if="configData.picList[0].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
            <div class="pic" :class="currentIndex === 1 ? 'on' : ''" @click="currentTab(1, configData)">
              <img :src="configData.picList[1].image" v-if="configData.picList[1].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
          </div>
          <div class="item" :class="currentIndex === 2 ? 'on' : ''" @click="currentTab(2, configData)">
            <img :src="configData.picList[2].image" v-if="configData.picList[2].image" />
            <div class="empty-box" v-else>375*750</div>
          </div>
        </div>
        <div class="advertItem06 acea-row" v-if="style === 7">
          <div
            class="item"
            :class="currentIndex === index ? 'on' : ''"
            @click="currentTab(index, configData)"
            v-for="(item, index) in configData.picList"
            :key="index"
          >
            <img :src="item.image" v-if="item.image" />
            <div class="empty-box" v-else>375*375</div>
          </div>
        </div>
        <div class="advertItem08" v-if="style === 8">
          <div class="item acea-row">
            <div class="pic" :class="currentIndex === 0 ? 'on' : ''" @click="currentTab(0, configData)">
              <img :src="configData.picList[0].image" v-if="configData.picList[0].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
            <div class="pic" :class="currentIndex === 1 ? 'on' : ''" @click="currentTab(1, configData)">
              <img :src="configData.picList[1].image" v-if="configData.picList[1].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
          </div>
          <div class="items acea-row">
            <div class="pic" :class="currentIndex === 2 ? 'on' : ''" @click="currentTab(2, configData)">
              <img :src="configData.picList[2].image" v-if="configData.picList[2].image" />
              <div class="empty-box" v-else>250*375</div>
            </div>
            <div class="pic" :class="currentIndex === 3 ? 'on' : ''" @click="currentTab(3, configData)">
              <img :src="configData.picList[3].image" v-if="configData.picList[3].image" />
              <div class="empty-box" v-else>250*375</div>
            </div>
            <div class="pic" :class="currentIndex === 4 ? 'on' : ''" @click="currentTab(4, configData)">
              <img :src="configData.picList[4].image" v-if="configData.picList[4].image" />
              <div class="empty-box" v-else>250*375</div>
            </div>
          </div>
        </div>
        <div class="advertItem04 acea-row" v-if="style === 9">
          <div class="item" :class="currentIndex === 0 ? 'on' : ''" @click="currentTab(0, configData)">
            <img :src="configData.picList[0].image" v-if="configData.picList[0].image" />
            <div class="empty-box" v-else>375*750</div>
          </div>
          <div class="item">
            <div class="pic" :class="currentIndex === 1 ? 'on' : ''" @click="currentTab(1, configData)">
              <img :src="configData.picList[1].image" v-if="configData.picList[1].image" />
              <div class="empty-box" v-else>375*375</div>
            </div>
            <div class="pic acea-row">
              <div class="picItem" :class="currentIndex === 2 ? 'on' : ''" @click="currentTab(2, configData)">
                <img :src="configData.picList[2].image" v-if="configData.picList[2].image" />
                <div class="empty-box" v-else>375*250</div>
              </div>
              <div class="picItem" :class="currentIndex === 3 ? 'on' : ''" @click="currentTab(3, configData)">
                <img :src="configData.picList[3].image" v-if="configData.picList[3].image" />
                <div class="empty-box" v-else>375*250</div>
              </div>
            </div>
          </div>
        </div>
        <div class="advertItem01 acea-row" v-if="style === 10" v-for="(item, index) in configData.picList" :key="index">
          <img :src="item.image" v-if="item.image" />
          <div class="empty-box" v-else>Không giới hạn kích thước</div>
        </div>
        <template v-if="style === 11">
          <div class="pic-box" @mousemove.stop="move">
            <div class="advertItem11 acea-row" id="lay1" @click="clickBox">
              <div
                class="lay-item"
                :class="currentIndex === index ? 'on' : ''"
                v-for="(item, index) in configData.picList"
                :key="index + 'aaa'"
              >
                <img :src="item.img" v-if="item.img" />
                <div class="empty-box" v-else>+</div>
              </div>
            </div>
            <div
              v-for="(item, index) in selBoxList"
              :key="index"
              :style="{
                width: item.doc.w + 'px',
                height: item.doc.h + 'px',
                left: item.doc.startX + 'px',
                top: item.doc.startY + 'px',
              }"
              class="areaBox"
              :class="{ active: selPicBox == index }"
              @mouseover="initRect"
              @click="currentTab(index, configData)"
            >
              <img :src="item.img" v-if="item.img" />
              <div class="prompt-text" v-else>{{ item.doc.w }}x{{ item.doc.h }}</div>
              <div v-if="selPicBox == index" class="del" @click.stop="delAreaBox(index)">
                <i class="el-icon-close" />
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'c_pictrue',
  props: {
    configObj: {
      type: Object,
    },
    configNme: {
      type: String,
    },
  },
  data() {
    return {
      defaults: {},
      configData: {},
      style: 0,
      isUpdate: false, // Render lại
      currentIndex: 0,
      arrayObj: {
        image: '',
        link: '',
      },
      list: undefined,
      select: false,
      lis: undefined,
      rect: null, // Định nghĩa div phần tử di chuyển
      // Ghi lại tọa độ khi nhấn chuột
      downX: 0,
      downY: 0,
      // Ghi lại tọa độ khi thả chuột
      mouseX2: 0,
      mouseY2: 0,
      imgNum: 0,
      selPicBox: 0, // Khung ảnh đang được chọn
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.defaults = this.configObj;
      if (this.configObj.hasOwnProperty('timestamp')) {
        this.isUpdate = true;
      } else {
        this.isUpdate = false;
      }
      this.$set(this, 'configData', this.configObj[this.configNme]);
      this.style = this.configObj.styleConfig.tabVal;
      this.count = this.defaults.styleConfig.count;
      this.picArrayConcat(this.count);

      if (this.style == 11) {
        this.lis = document.getElementsByClassName('lay-item');
      }
      this.currentTab(0, this.configData);
    });
  },
  computed: {
    selBoxList() {
      return this.configObj.picStyle.docPicList;
    },
  },
  watch: {
    configObj: {
      handler(nVal) {
        this.defaults = nVal;
        this.$set(this, 'configData', nVal[this.configNme]);
        this.style = nVal.styleConfig.tabVal;
        this.isUpdate = true;
        this.$set(this, 'isUpdate', true);
      },
      deep: true,
    },
    'configObj.styleConfig.tabVal': {
      handler() {
        this.count = this.defaults.styleConfig.count;
        this.picArrayConcat(this.count);
        this.configData.picList.splice(this.count);
        this.currentIndex = 0;
        let list = this.defaults.menuConfig.list[0];
        if (this.configData.picList[0]) {
          list.img = this.configData.picList[0].image;
          list.info[0].value = this.configData.picList[0].link;
        }
        this.lis = document.getElementsByClassName('lay-item');
      },
      deep: true,
      immediate: true,
    },
    'configObj.picStyle.docPicList': {
      handler() {
        if (this.configObj.styleConfig.tabVal == 11) {
          this.configObj.picStyle.docPicList.map((e, i) => {
            this.configObj.picStyle.docPicList[i].img = this.configObj.picStyle.picList[i].image;
            this.configObj.picStyle.docPicList[i].link = this.configObj.picStyle.picList[i].link;
          });
        }
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    currentTab(e, data) {
      this.selPicBox = e;
      this.currentIndex = e;
      this.configData.tabVal = e;
      if (this.defaults.menuConfig.isCube) {
        if (this.configData.tabVal !== 11) {
          let list = this.defaults.menuConfig.list[0];
          if (data.picList[e] && data.picList[e].image) {
            list.img = data.picList[e].image;
            list.info[0].value = data.picList[e].link;
          } else {
            list.img = '';
            list.info[0].value = '';
          }
        } else {
          this.selPicBox = e;
          let list = this.defaults.docPicList;
          if (data.menuConfig.picStyle.picList[e].image) {
            list[e].img = data.menuConfig.picStyle.picList[e].image;
            list[e].info[0].value = data.menuConfig.picStyle.docPicList[e].value;
          } else {
            list[0].img = '';
            list[0].info[0].value = '';
          }
        }
      }
    },
    picArrayConcat(count) {
      for (let i = this.configData.picList.length; i < count; i++) {
        this.configData.picList.push(this.arrayObj);
      }
    },
    // Xóa vùng nóng chỉ định
    delAreaBox(index) {
      /* Xóa một vùng nóng */
      this.selBoxList.splice(index, 1);
      this.configObj.picStyle.picList.splice(index, 1);
      this.configObj.picStyle.picList.push({ image: '', link: '' });
      if (this.selBoxList.length) this.currentTab(this.selBoxList.length - 1, this.configData);
    },
    initRect() {
      if (this.rect) {
        document.getElementById('lay1').removeChild(this.rect);
      }
    },
    //Xử lý sự kiện nhấn chuột
    clickBox(event) {
      if (this.select) {
        let boxData = this.up();
        try {
          if (this.selBoxList.length && this.selBoxList.length == 1 && this.selBoxList[0].doc.w === 0) {
            this.selBoxList[0].doc = boxData;
          } else {
            this.selBoxList.push({
              img: '',
              link: '',
              doc: boxData,
            });
          }
          this.currentTab(this.selBoxList.length - 1, this.configData);
        } catch (error) {
          console.log(error);
        }

        this.selPicBox = this.selBoxList.length ? this.selBoxList.length - 1 : 0;
        return;
      }
      // Chỉ cho phép xử lý sự kiện di chuyển chuột khi đã nhấn chuột
      this.select = true;
      this.rect = document.createElement('div');
      // Kiểu div khung chọn
      this.rect.style.cssText =
        'position:absolute;width:0px;height:0px;font-size:0px;margin:0px;padding:0px;border:1px dashed #0099FF;background-color:#C3D5ED;z-index:1000;filter:alpha(opacity:60);opacity:0.6;display:none;';
      this.rect.id = 'selectDiv';
      // Thêm vào dưới lay1
      document.getElementById('lay1').appendChild(this.rect);
      // Lấy vị trí tọa độ khi nhấn chuột
      this.downX = event.layerX;
      this.downY = event.layerY;
      this.rect.style.left = this.downX + 'px';
      this.rect.style.top = this.downY + 'px';
      //Đặt vị trí điểm bắt đầu của khung chữ nhật cần vẽ
      this.rect.style.left = this.downX + 'px';
      this.rect.style.top = this.downY + 'px';
    },

    //Sự kiện thả chuột
    up() {
      let topList = [];
      let leftList = [];
      for (let i = 0; i < this.lis.length; i++) {
        //So sánh bốn điểm của div di chuyển với bốn điểm của phần tử div
        if (
          //Kiểm tra vị trí viền phải của phần tử div lớn hơn điểm bắt đầu bên trái của div di chuyển
          this.rect.offsetLeft < this.lis[i].offsetLeft + this.lis[i].offsetWidth &&
          //Kiểm tra vị trí viền dưới của phần tử div lớn hơn điểm bắt đầu bên trên của div di chuyển
          this.lis[i].offsetTop + this.lis[i].offsetHeight > this.rect.offsetTop &&
          // Kiểm tra vị trí viền trái của phần tử div nhỏ hơn điểm bắt đầu bên phải của div di chuyển
          this.rect.offsetLeft + this.rect.offsetWidth > this.lis[i].offsetLeft &&
          // Kiểm tra vị trí viền trên của phần tử div nhỏ hơn điểm bắt đầu bên dưới của div di chuyển
          this.rect.offsetTop + this.rect.offsetHeight > this.lis[i].offsetTop
        ) {
          //Thay đổi kiểu của phần đã chọn
          if (this.lis[i].className.indexOf('seled') == -1) {
            topList.push(this.lis[i].offsetTop);
            leftList.push(this.lis[i].offsetLeft);
          }
        } else {
          //Nếu không có gì được chọn thì xóa kiểu
          if (this.lis[i].className.indexOf('seled') != -1) {
            this.lis[i].className = 'lay-item';
          }
        }
        //Khi thả chuột thì không cho phép xử lý sự kiện di chuyển chuột nữa
        this.select = false;
      }

      //Ẩn layer
      if (this.rect) {
        document.getElementById('lay1').removeChild(this.rect);
      }

      return {
        startX: this.getMin(leftList),
        startY: this.getMin(topList),
        w: this.getMax(leftList) - this.getMin(leftList) + 93.75,
        h: this.getMax(topList) - this.getMin(topList) + 93.75,
      };
    },
    // Xóa
    del() {
      this.$emit('delAreaBox', this.areaDataIndex);
    },
    getMin(arr) {
      let min = arr[0];
      for (let i = 1; i < arr.length; i++) {
        if (arr[i] < min) {
          min = arr[i];
        }
      }
      return min;
    },
    getMax(arr) {
      let max = arr[0];
      for (let i = 1; i < arr.length; i++) {
        if (arr[i] > max) {
          max = arr[i];
        }
      }
      return max;
    },
    out() {
      if (this.rect) {
        this.select = false;
        document.getElementById('lay1').removeChild(this.rect);
      }
    },
    //Sự kiện di chuyển chuột, sự kiện quan trọng nhất
    move(event) {
      event.preventDefault();
      if (!this.select) return;
      /*
            Phần này, dựa vào mối quan hệ giữa vị trí nhấn chuột và vị trí thả chuột khi kéo khung, có thể chia vùng thành bốn phần, dựa theo sự khác nhau của bốn phần đó,
            chúng ta có thể vẽ khung tương ứng, nếu không thì chỉ có thể vẽ khung theo một hướng, nghĩa là vẽ khung về phía dưới bên phải của điểm nhấn.
            */
      if (this.select) {
        console.log(event.layerX, event.layerY, event);
        window.requestAnimationFrame(() => {
          // Lấy vị trí tọa độ khi di chuyển chuột
          this.mouseX2 = event.layerX - 5;
          this.mouseY2 = event.layerY - 5;
          // Hiển thị phần tử khung chọn
          if (this.rect.style.display == 'none') {
            this.rect.style.display = '';
          }
          this.rect.style.left = Math.min(this.mouseX2, this.downX) + 'px';
          this.rect.style.top = Math.min(this.mouseY2, this.downY) + 'px';
          this.rect.style.width = this.mouseX2 - this.downX + 'px';
          this.rect.style.height = this.mouseY2 - this.downY + 'px';
          // // A part
          // if (this.mouseX2 < this.downX && this.mouseY2 < this.downY) {
          //   this.rect.style.left = this.mouseX2;
          //   this.rect.style.top = this.mouseY2;
          // }

          // // B part
          // if (this.mouseX2 > this.downX && this.mouseY2 < this.downY) {
          //   this.rect.style.left = this.downX;
          //   this.rect.style.top = this.mouseY2;
          // }

          // // C part
          // if (this.mouseX2 < this.downX && this.mouseY2 > this.downY) {
          //   this.rect.style.left = this.mouseX2;
          //   this.rect.style.top = this.downY;
          // }

          // // D part
          // if (this.mouseX2 > this.downX && this.mouseY2 > this.downY) {
          //   this.rect.style.left = this.downX;
          //   this.rect.style.top = this.downY;
          // }
          //   this.rect.style.left = this.downX;
          //   this.rect.style.top = this.downY;
        });
      }

      // Ngăn sự kiện lan truyền lên
      window.event.cancelBubble = true;
      // Ngăn hành vi mặc định (preventDefault)
      window.event.returnValue = false;
    },
  },
};
</script>
<style scoped lang="scss">
::v-deep .ivu-divider-horizontal {
  margin: 12px 0;
}

img {
  object-fit: cover;
  width: 100%;
  height: 100%;
}

.empty-box {
  color: #8c8c8c;
  font-size: 12px;
  border-radius: 0;
  background-color: #eee;
  width: 100%;
  border: 1px solid #ddd;
  text-align: center;
}

.mobile-page {
  .tip {
    font-size: 12px;
    color: rgba(0, 0, 0, 0.45);
    margin-bottom: 2px;
  }

  .advert {
    cursor: pointer;
    padding: 0 15px 20px 15px;

    .advertItem07 {
      height: 185px;

      &.on {
        img {
          border: 1px solid var(--prev-color-primary) !important;
        }

        .empty-box {
          border: 1px solid var(--prev-color-primary) !important;
          color: var(--prev-color-primary);
        }
      }
    }

    .advertItem08 {
      .item {
        height: 185px;

        .pic {
          width: 50%;
          height: 100%;

          &.on {
            img {
              border: 1px solid var(--prev-color-primary) !important;
            }

            .empty-box {
              border: 1px solid var(--prev-color-primary) !important;
              color: var(--prev-color-primary);
            }
          }
        }

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }
      }

      .items {
        .pic {
          width: 33.33333%;
          height: 185px;

          &.on {
            img {
              border: 1px solid var(--prev-color-primary) !important;
            }

            .empty-box {
              border: 1px solid var(--prev-color-primary) !important;
              color: var(--prev-color-primary);
            }
          }
        }
      }
    }

    .advertItem01 {
      width: 100%;
      height: 100%;

      .empty-box {
        height: 366px;
        border: 1px solid #ddd;
      }

      img {
        width: 100%;
        height: 100%;
        border: 1px solid var(--prev-color-primary);
      }
    }

    .advertItem02 {
      width: 370px;
      height: 370px;

      .item {
        width: 50%;
        height: 370px;

        img {
          width: 100%;
          height: 100%;
        }

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }

        .empty-box {
          border-right: 1px solid #eee;
        }

        &:nth-child(2) {
          .empty-box {
            border-right: 1px solid #ddd;
          }
        }
      }
    }

    .advertItem03 {
      .item {
        width: 33.3333%;
        height: 370px;

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }

        .empty-box {
          border-right: 1px solid #eee;
        }

        &:nth-child(2) {
          .empty-box {
            border-right: 1px solid #eee;
          }
        }

        &:nth-child(3) {
          .empty-box {
            border-right: 1px solid #ddd;
          }
        }
      }
    }

    .advertItem04 {
      .item {
        width: 50%;
        height: 370px;

        .empty-box {
          height: 100%;
        }

        img {
          width: 100%;
          height: 100%;
        }

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }

        .pic {
          width: 100%;
          height: 185px;

          .picItem {
            width: 50%;
            height: 100%;

            &.on {
              img {
                border: 1px solid var(--prev-color-primary) !important;
              }

              .empty-box {
                border: 1px solid var(--prev-color-primary) !important;
                color: var(--prev-color-primary);
              }
            }
          }

          &.on {
            img {
              border: 1px solid var(--prev-color-primary) !important;
            }

            .empty-box {
              border: 1px solid var(--prev-color-primary) !important;
              color: var(--prev-color-primary);
            }
          }
        }

        &:nth-child(1) {
          .empty-box {
            border-right: 1px solid #eee;
          }
        }

        &:nth-child(2) {
          .pic {
            &:nth-child(2) {
              .empty-box {
                border-top: 1px solid #eee;
              }
            }
          }
        }
      }
    }

    .advertItem05 {
      .item {
        width: 25%;

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }

        .empty-box {
          height: 94.75px;
        }

        &:nth-child(4) {
          .empty-box {
            border-right: 1px solid #ddd;
          }
        }

        &:nth-child(2) {
          .empty-box {
            border-right: 1px solid #eee;
          }
        }
      }
    }

    .advertItem06 {
      .item {
        width: 50%;
        height: 185px;

        img {
          width: 100%;
          height: 100%;
        }

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }

        .empty-box {
          height: 100%;
          border-right: 1px solid #eee;
          border-bottom: 1px solid #eee;
        }

        &:nth-child(2) {
          .empty-box {
            border-right: 1px solid #ddd;
          }
        }

        &:nth-child(3) {
          .empty-box {
            border-bottom: 1px solid #ddd;
          }
        }

        &:nth-child(4) {
          .empty-box {
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
          }
        }
      }
    }

    .advertItem11 {
      width: 375px;
      height: 375px;
      visibility: visible;
      position: relative;

      .seled {
        border: 1px solid red;
        background-color: #d6dff7;
      }

      .lay-item {
        width: 93.75px;
        height: 93.75px;

        .empty-box {
          height: 100%;
        }

        img {
          width: 100%;
          height: 100%;
        }

        &.on {
          img {
            border: 1px solid var(--prev-color-primary) !important;
          }

          .empty-box {
            border: 1px solid var(--prev-color-primary) !important;
            color: var(--prev-color-primary);
          }
        }

        .pic {
          width: 100%;
          height: 100%;

          .picItem {
            width: 50%;

            &.on {
              img {
                border: 1px solid var(--prev-color-primary) !important;
              }

              .empty-box {
                border: 1px solid var(--prev-color-primary) !important;
                color: var(--prev-color-primary);
              }
            }
          }

          &.on {
            img {
              border: 1px solid var(--prev-color-primary) !important;
            }

            .empty-box {
              border: 1px solid var(--prev-color-primary) !important;
              color: var(--prev-color-primary);
            }
          }
        }

        .empty-box {
          border-right: 1px solid #ddd;
        }

        .empty-box {
          border-top: 0px solid #ddd;
        }
      }
    }

    .pic-box {
      position: relative;
    }

    .areaBox.active {
      border: 1px solid var(--prev-color-primary);
    }

    .areaBox {
      position: absolute;
      background: #eee;
      border: 1px solid #dddddd;
      display: flex;
      justify-content: center;
      align-items: center;
      color: var(--prev-color-primary);
      font-size: 12px;
      cursor: pointer;
      z-index: 11;

      .prompt-text {
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 100%;
        max-height: 100%;
        text-align: center;
        align-items: center;
        color: var(--prev-color-primary);
      }

      .del {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 16px;
        height: 16px;
        line-height: 16px;
        font-size: 12px;
        background: var(--prev-color-primary);
        color: #fff;
        text-align: center;
        border-radius: 0 0 0 3px;
        position: absolute;
        right: 7px;
        top: 7px;
        transform: translate3d(50%, -50%, 0);
        cursor: default;
      }

      .del:hover {
        width: 16px;
        height: 16px;
        line-height: 16px;
      }
    }
  }
}
</style>
