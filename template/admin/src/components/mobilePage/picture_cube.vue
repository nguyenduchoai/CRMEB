<template>
  <div
    class="mobile-page"
    :style="{
      background: bottomBgColor,
      marginTop: mTop + 'px',
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
  >
    <div class="advert" :style="style === 10 ? 'height:100%' : 'height:' + (379 - prConfig * 2) + 'px'">
      <div class="advertItem07" v-if="style === 0">
        <div
          class="item"
          v-for="(item, index) in picList"
          :key="index"
          :style="{ border: imgConfig + 'px solid ' + bottomBgColor }"
        >
          <img
            class="img"
            :src="item.image"
            v-if="item.image"
            :style="{
              borderRadius: imgRadius,
            }"
          />
          <div
            class="empty-box"
            v-else
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </div>
      <div class="advertItem02 acea-row" v-if="style === 1">
        <div
          class="item"
          v-for="(item, index) in picList"
          :key="index"
          :style="{ border: imgConfig + 'px solid ' + bottomBgColor }"
        >
          <img
            class="img"
            :src="item.image"
            v-if="item.image"
            :style="{
              borderRadius: imgRadius,
            }"
          />
          <div
            class="empty-box"
            v-else
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </div>
      <div class="advertItem02 advertItem03 acea-row" v-if="style === 2">
        <div
          class="item"
          v-for="(item, index) in picList"
          :key="index"
          :style="{ border: imgConfig + 'px solid ' + bottomBgColor }"
        >
          <img
            class="img"
            :src="item.image"
            v-if="item.image"
            :style="{
              borderRadius: imgRadius,
            }"
          />
          <div
            class="empty-box"
            v-else
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </div>
      <div class="advertItem07 advertItem08" v-if="style === 3">
        <div class="item acea-row">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img
              class="img"
              :src="picList[0].image"
              v-if="picList[0].image"
              :style="{
                borderRadius: imgRadius,
              }"
            />
            <div
              class="empty-box"
              v-else
              :style="{
                borderRadius: imgRadius,
              }"
            >
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img
              class="img"
              :src="picList[1].image"
              v-if="picList[1].image"
              :style="{
                borderRadius: imgRadius,
              }"
            />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
        </div>
        <div class="item" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
          <img class="img" :src="picList[2].image" v-if="picList[2].image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </div>
      <div class="advertItem07 advertItem08" v-if="style === 4">
        <div class="item" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
          <img class="img" :src="picList[0].image" v-if="picList[0].image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="item acea-row">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[1].image" v-if="picList[1].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[2].image" v-if="picList[2].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
        </div>
      </div>
      <div class="advertItem04 acea-row" v-if="style === 5">
        <div class="item" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
          <img class="img" :src="picList[0].image" v-if="picList[0].image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="item">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[1].image" v-if="picList[1].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[2].image" v-if="picList[2].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
        </div>
      </div>
      <div class="advertItem04 acea-row" v-if="style === 6">
        <div class="item">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[0].image" v-if="picList[0].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[1].image" v-if="picList[1].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
        </div>
        <div class="item" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
          <img class="img" :src="picList[2].image" v-if="picList[2].image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </div>
      <div class="advertItem06 acea-row" v-if="style === 7">
        <div
          class="item"
          v-for="(item, index) in picList"
          :key="index"
          :style="{ border: imgConfig + 'px solid ' + bottomBgColor }"
        >
          <img class="img" :src="item.image" v-if="item.image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </div>
      <div class="advertItem07 advertItem08" v-if="style === 8">
        <div class="item acea-row">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[0].image" v-if="picList[0].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[1].image" v-if="picList[1].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
        </div>
        <div class="items acea-row">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[2].image" v-if="picList[2].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[3].image" v-if="picList[3].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[4].image" v-if="picList[4].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
        </div>
      </div>
      <div class="advertItem04 acea-row" v-if="style === 9">
        <div class="item" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
          <img class="img" :src="picList[0].image" v-if="picList[0].image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="item">
          <div class="pic" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
            <img class="img" :src="picList[1].image" v-if="picList[1].image" :style="{ borderRadius: imgRadius }" />
            <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="pic acea-row">
            <div class="picItem" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
              <img class="img" :src="picList[2].image" v-if="picList[2].image" :style="{ borderRadius: imgRadius }" />
              <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
                <img src="../../assets/images/shan.png" />
              </div>
            </div>
            <div class="picItem" :style="{ border: imgConfig + 'px solid ' + bottomBgColor }">
              <img class="img" :src="picList[3].image" v-if="picList[3].image" :style="{ borderRadius: imgRadius }" />
              <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
                <img src="../../assets/images/shan.png" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <template v-if="style === 10">
        <div
          class="advertItem01 acea-row"
          v-for="(item, index) in picList"
          :key="index"
          :style="{ border: imgConfig + 'px solid ' + bottomBgColor }"
        >
          <img class="img" :src="item.image" v-if="item.image" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </template>
      <template v-if="style === 11">
        <div
          class="advertItem11 acea-row"
          v-for="(item, index) in docPicList"
          :key="index"
          :style="{
            border: imgConfig + 'px solid ' + bottomBgColor,
            width: (item.doc.w / 375) * 100 + '%',
            height: (item.doc.h / 375) * 100 + '%',
            left: (item.doc.startX / 375) * 100 + '%',
            top: (item.doc.startY / 375) * 100 + '%',
          }"
        >
          <img class="img" :src="item.img" v-if="item.img" :style="{ borderRadius: imgRadius }" />
          <div class="empty-box" v-else :style="{ borderRadius: imgRadius }">
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'picture_cube',
  cname: 'Lưới ảnh',
  configName: 'c_picture_cube',
  icon: '#iconzujian-tupianmofang',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'pictureCube', // Tên khớp bên ngoài
  props: {
    index: {
      type: null,
    },
    num: {
      type: null,
    },
  },
  computed: {
    ...mapState('mobildConfig', ['defaultArray']),
  },
  watch: {
    pageData: {
      handler(nVal) {
        this.setConfig(nVal);
      },
      deep: true,
    },
    num: {
      handler(nVal) {
        let data = this.$store.state.mobildConfig.defaultArray[nVal];
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler() {
        let data = this.$store.state.mobildConfig.defaultArray[this.num];
        this.setConfig(data);
      },
      deep: true,
    },
  },
  data() {
    return {
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Lưới ảnh',
        name: 'pictureCube',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleShow: 'Cài đặt hiển thị',
        titleContent: 'Cài đặt nội dung',
        titleRight: 'Lưới ảnh',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          count: 2,
          type: 'pictureCube',
        },
        picStyle: {
          tabVal: 0,
          picList: [],
          docPicList: [],
        },

        menuConfig: {
          title: '',
          maxList: 1,
          isCube: 1,
          list: [
            {
              img: '',
              info: [
                {
                  title: 'Liên kết',
                  tips: 'Vui lòng nhập liên kết',
                  value: '',
                  max: 100,
                },
              ],
            },
          ],
        },
        imgConfig: {
          title: 'Khoảng cách ảnh',
          val: 0,
          min: 0,
        },
        filletImg: {
          title: 'Bo góc ảnh',
          type: 0,
          list: [
            {
              val: 'Tất cả',
              icon: 'iconcaozuo-zhengti',
            },
            {
              val: 'Từng góc',
              icon: 'iconcaozuo-bianjiao',
            },
          ],
          valName: 'Giá trị bo góc',
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          default: [
            {
              item: '#f5f5f5',
            },
          ],
          color: [
            {
              item: '#f5f5f5',
            },
          ],
        },
        topConfig: {
          title: 'Lề trên',
          val: 0,
          min: 0,
        },
        bottomConfig: {
          title: 'Lề dưới',
          val: 0,
          min: 0,
        },
        prConfig: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
        },
        mbConfig: {
          title: 'Lề trên trang',
          val: 0,
          min: 0,
        },
      },
      pageData: {},
      style: 0,
      picList: [],
      docPicList: [],
      bottomBgColor: [],
      mTop: 0,
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      imgRadius: 0,
      imgConfig: 0,
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
    });
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      if (data.mbConfig) {
        this.style = data.styleConfig.tabVal;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.prConfig = data.prConfig.val;
        this.mTop = data.mbConfig.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        let filletImg = data.filletImg.type;
        let filletValImg = data.filletImg.val;
        let valListImg = data.filletImg.valList;
        this.imgRadius = filletImg
          ? valListImg[0].val + 'px ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + valListImg[2].val + 'px'
          : filletValImg + 'px';
        this.imgConfig = data.imgConfig.val;
        if (data.styleConfig.tabVal !== 11) {
          if (!data.picStyle.picList.length) {
            this.picList = [
              {
                image: '',
                link: '',
              },
            ];
          } else {
            this.picList = data.picStyle.picList;
          }
        } else {
          data.picStyle.docPicList.map((e, i) => {
            data.picStyle.docPicList[i].img = data.picStyle.picList[i].image;
            data.picStyle.docPicList[i].link = data.picStyle.picList[i].link;
          });
          this.$set(this, 'docPicList', data.picStyle.docPicList);
        }
      }
    },
  },
};
</script>
<style scoped lang="scss">
.empty-box {
  background: #f3f9ff;
  img {
    width: 65px;
    height: 50px;
  }
}

.img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.advertItem07 {
  width: 100%;
  height: 100%;

  .item {
    width: 100%;
    height: 50%;
  }

  .items {
    width: 100%;
    height: 50%;

    .pic {
      width: 33.3333%;
      height: 100%;
    }
  }
}

.advertItem08 {
  .item {
    .pic {
      width: 50%;
      height: 100%;
    }
  }
}

.mobile-page {
  .advert {
    position: relative;

    .advertItem11 {
      position: absolute;
    }

    .advertItem01 {
      width: 100%;
      height: 100%;

      .empty-box {
        width: 100%;
        height: 379px;

        .icontupian {
          font-size: 50px;
          color: #999;
        }
      }
    }

    .advertItem02 {
      width: 100%;
      height: 100%;

      .item {
        width: 50%;
        height: 100%;

        .empty-box {
          width: 100%;
          height: 100%;
        }
      }
    }

    .advertItem03 {
      .item {
        width: 33.3333%;
        height: 100%;

        .empty-box {
          width: 100%;
          height: 100%;
        }
      }
    }

    .advertItem04 {
      width: 100%;
      height: 100%;

      .item {
        width: 50%;
        height: 100%;

        .empty-box {
          width: 100%;
          height: 100%;
        }

        .pic {
          width: 100%;
          height: 50%;

          .picItem {
            width: 50%;
            height: 100%;
          }
        }
      }
    }

    .advertItem05 {
      .item {
        width: 25%;

        .empty-box {
          width: 100%;
          height: 94.75px;
        }
      }
    }

    .advertItem06 {
      width: 100%;
      height: 100%;

      .item {
        width: 50%;
        height: 50%;

        .empty-box {
          width: 100%;
          height: 100%;
        }
      }
    }
  }
}
</style>
