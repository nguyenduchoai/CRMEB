<template>
  <common_wrapper :config="configObj">
    <div class="mobile-page">
      <div
        class="advert"
        :style="
          style === 10
            ? 'height:100%'
            : 'height:' + (379 - paddingConfig.valList[1].val - paddingConfig.valList[3].val) + 'px'
        "
      >
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
  </common_wrapper>
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
          listStyle: -1,
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
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
        borderConfig: {
          title: 'Cài đặt viền',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0, // 0: Hide, 1: Show
          styleConfig: {
            title: 'Kiểu viền',
            tabVal: 0,
            tabList: [
              { name: 'Nét liền', style: 'solid' },
              { name: 'Nét đứt', style: 'dashed' },
              { name: 'Nét chấm', style: 'dotted' },
            ],
          },
          widthConfig: {
            title: 'Độ dày viền',
            val: 1,
            min: 1,
          },
          colorConfig: {
            title: 'Màu viền',
            default: [{ item: '#e5e5e5' }],
            color: [{ item: '#e5e5e5' }],
          },
        },
        shadowConfig: {
          title: 'Cài đặt đổ bóng',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0, // 0: Hide, 1: Show
          colorConfig: {
            title: 'Màu đổ bóng',
            default: [{ item: 'rgba(0,0,0,0.1)' }],
            color: [{ item: 'rgba(0,0,0,0.1)' }],
          },
          xConfig: {
            title: 'Độ lệch trục X',
            val: 0,
            min: -50,
          },
          yConfig: {
            title: 'Độ lệch trục Y',
            val: 0,
            min: -50,
          },
          blurConfig: {
            title: 'Bán kính làm mờ',
            val: 10,
            min: 0,
          },
          spreadConfig: {
            title: 'Bán kính lan tỏa',
            val: 0,
            min: -50,
          },
        },
        componentBgConfig: {
          title: 'Nền thành phần',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0, // 0: Hide, 1: Show
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#fff' }],
            color: [{ item: '#fff' }],
          },
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
        fillet: {
          title: 'Bo góc nền',
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
      },
      pageData: {},
      style: 0,
      imgConfig: 0,
      picList: [],
      configObj: null,
      bottomBgColor: '',
      paddingConfig: {
        title: 'Lề trong',
        val: 0,
        min: 0,
        max: 100,
        isAll: false,
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
      imgRadius: 0,
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
      let dataClone = JSON.parse(JSON.stringify(data));
      for (let key in this.defaultConfig) {
        if (dataClone[key] == undefined) {
          this.$set(dataClone, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }
      this.configObj = dataClone;

      let isLegacyPadding = !dataClone.paddingConfig;
      let isLegacyMargin = !dataClone.marginConfig;

      if (isLegacyPadding) {
        dataClone.paddingConfig = {
          title: 'Cài đặt lề',
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        if (dataClone.topConfig) dataClone.paddingConfig.valList[0].val = dataClone.topConfig.val;
        if (dataClone.bottomConfig) dataClone.paddingConfig.valList[2].val = dataClone.bottomConfig.val;
        if (dataClone.prConfig) {
          dataClone.paddingConfig.valList[1].val = dataClone.prConfig.val;
          dataClone.paddingConfig.valList[3].val = dataClone.prConfig.val;
        }
      }
      if (isLegacyMargin) {
        dataClone.marginConfig = {
          title: 'Cài đặt khoảng cách',
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        if (dataClone.mbConfig) dataClone.marginConfig.valList[0].val = dataClone.mbConfig.val;
      }
      this.paddingConfig = dataClone.paddingConfig;

      this.style = dataClone.styleConfig.tabVal;
      this.bottomBgColor = dataClone.bottomBgColor.color[0].item;
      let filletImg = dataClone.filletImg.type;
      let filletValImg = dataClone.filletImg.val;
      let valListImg = dataClone.filletImg.valList;
      this.imgRadius = filletImg
        ? valListImg[0].val + 'px ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + valListImg[2].val + 'px'
        : filletValImg + 'px';
      this.imgConfig = dataClone.imgConfig.val;
      if (dataClone.styleConfig.tabVal !== 11) {
        if (!dataClone.picStyle.picList.length) {
          this.picList = [
            {
              image: '',
              link: '',
            },
          ];
        } else {
          this.picList = dataClone.picStyle.picList;
        }
      } else {
        dataClone.picStyle.docPicList.map((e, i) => {
          dataClone.picStyle.docPicList[i].img = dataClone.picStyle.picList[i].image;
          dataClone.picStyle.docPicList[i].link = dataClone.picStyle.picList[i].link;
        });
        this.$set(this, 'docPicList', dataClone.picStyle.docPicList);
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
  display: inline-block;
  width: -webkit-fill-available;
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
        height: 375px;

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
