<template>
  <div>
    <el-form ref="formValidate" :model="formValidate" :rules="ruleInline" inline>
      <el-form-item label="Chọn loại:" class="form-item" label-position="right" label-width="100px">
        <el-radio-group v-model="formValidate.gender">
          <el-radio :label="item.key" v-for="(item, index) in radioList" :key="index">{{ item.title }}</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item
        v-if="formValidate.gender == 1"
        label="Loại giao hàng:"
        class="form-item"
        label-position="right"
        label-width="100px"
        :key="'test0'"
      >
        <el-radio-group v-model="formValidate.shipStatus">
          <el-radio :label="item.key" v-for="(item, index) in shipType" :key="index">{{ item.title }}</el-radio>
        </el-radio-group>
      </el-form-item>
      <!--  Nhập tay khi giao hàng  -->
      <div v-if="formValidate.gender == 1 && formValidate.shipStatus == 1" :key="'test1'">
        <el-form-item
          label="Đơn vị vận chuyển:"
          prop="logisticsCode"
          class="form-item"
          label-position="right"
          label-width="100px"
        >
          <el-select
            v-model="formValidate.logisticsCode"
            filterable
            placeholder="Vui lòng chọn"
            @change="bindChange"
            :label-in-value="true"
            style="width: 100%"
          >
            <el-option
              :value="item.code"
              v-for="(item, index) in logisticsList"
              :key="index"
              :label="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Mã vận đơn:" prop="number" class="form-item" label-position="right" label-width="100px">
          <el-input v-model="formValidate.number" placeholder="Vui lòng nhập mã vận đơn" style="width: 100%"></el-input>
        </el-form-item>
        <el-form-item label="" class="form-item" label-position="right" label-width="100px">
          <div style="color: #c4c4c4">Với SF Express, vui lòng nhập mã vận đơn: 4 số cuối số điện thoại người nhận hoặc người gửi,</div>
          <div style="color: #c4c4c4">Ví dụ: SF000000000000:3941</div>
        </el-form-item>
      </div>
      <!--  In vận đơn điện tử  -->
      <div v-if="formValidate.gender == 1 && formValidate.shipStatus == 2" :key="'test2'">
        <el-form-item
          label="Đơn vị vận chuyển:"
          prop="logisticsCode"
          class="form-item"
          label-position="right"
          label-width="100px"
        >
          <el-select
            v-model="formValidate.logisticsCode"
            placeholder="Vui lòng chọn"
            style="width: 100%"
            @change="bindChange"
            filterable
            :label-in-value="true"
          >
            <el-option
              :value="item.code"
              v-for="(item, index) in logisticsList"
              :key="index"
              :label="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item
          label="Vận đơn điện tử:"
          class="form-item"
          label-position="right"
          label-width="100px"
          v-if="orderTempList.length > 0"
        >
          <el-select v-model="formValidate.electronic" placeholder="Vui lòng chọn vận đơn điện tử" style="width: 80%">
            <el-option
              :value="item.temp_id"
              v-for="(item, index) in orderTempList"
              :key="index"
              :label="item.title"
            ></el-option>
          </el-select>
          <el-button style="flex: 1; margin-left: 21px" v-db-click @click="lookImg">Xem trước</el-button>
          <viewer :images="orderTempList" class="viewer" ref="viewer" @inited="inited" style="display: none">
            <img v-for="src in orderTempList" :src="src.pic" :key="src.id" class="image" />
          </viewer>
        </el-form-item>
        <el-form-item label="Họ tên người gửi:" prop="sendName" class="form-item" label-position="right" label-width="100px">
          <el-input v-model="formValidate.sendName" placeholder="Vui lòng nhập họ tên người gửi" style="width: 100%"></el-input>
        </el-form-item>
        <el-form-item
          label="Số điện thoại người gửi:"
          prop="sendPhone"
          class="form-item"
          label-position="right"
          label-width="100px"
        >
          <el-input v-model="formValidate.sendPhone" placeholder="Vui lòng nhập số điện thoại người gửi" style="width: 100%"></el-input>
        </el-form-item>
        <el-form-item
          label="Địa chỉ người gửi:"
          prop="sendAddress"
          class="form-item"
          label-position="right"
          label-width="100px"
        >
          <el-input v-model="formValidate.sendAddress" placeholder="Vui lòng nhập địa chỉ người gửi" style="width: 100%"></el-input>
        </el-form-item>
      </div>
      <!--  Cửa hàng tự giao  -->
      <div v-if="formValidate.gender == 2" :key="'test3'">
        <el-form-item label="Chọn người giao hàng:" class="form-item" label-position="right" label-width="100px">
          <el-select v-model="formValidate.postPeople" placeholder="Chọn người giao hàng" style="width: 100%">
            <el-option
              :value="item.id"
              v-for="(item, index) in deliveryList"
              :key="index"
              :label="item.nickname"
            ></el-option>
          </el-select>
        </el-form-item>
      </div>
      <div v-if="formValidate.gender == 3">
        <el-form-item label="Ghi chú:" props="msg" class="form-item" label-position="right" label-width="100px">
          <el-input placeholder="Ghi chú" v-model="formValidate.msg" />
        </el-form-item>
      </div>
      <div class="mask-footer">
        <el-button type="primary" v-db-click @click="handleSubmit('formValidate')">Gửi</el-button>
        <el-button v-db-click @click="close">Hủy</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
import { orderExport, orderTemp, orderDeliveryAll, orderDelivery, getSender } from '@/api/kefu';
export default {
  name: 'delivery',
  props: {
    isShow: {
      type: Boolean,
      default: false,
    },
    orderId: {
      type: String | Number,
      default: '',
    },
    virtualType: {
      type: Number,
      default: 0,
    },
  },
  watch: {
    'formValidate.shipStatus': {
      handler(nVal, oVal) {
        if (nVal == 2 && !this.formValidate.sendName) {
          getSender().then((res) => {
            this.formValidate.sendName = res.data.to_name;
            this.formValidate.sendPhone = res.data.to_tel;
            this.formValidate.sendAddress = res.data.to_add;
          });
        }
        this.$refs['formValidate'].resetFields();
      },
      deep: true,
    },
    'formValidate.gender': {
      handler(nVal, oVal) {
        this.$refs['formValidate'].resetFields();
      },
      deep: true,
    },
    virtualType: {
      handler(nVal, oVal) {
        if (nVal == 3) this.formValidate.gender = 3;
      },
      immediate: true,
    },
  },
  data() {
    return {
      shipType: [
        {
          key: 1,
          title: 'Điền thủ công',
        },
        {
          key: 2,
          title: 'In vận đơn điện tử',
        },
      ],
      radioList: [
        {
          key: 1,
          title: 'Giao hàng',
        },
        {
          key: 2,
          title: 'Cửa hàng tự giao',
        },
        {
          key: 3,
          title: 'Hàng ảo',
        },
      ],
      ruleInline: {
        logisticsCode: [{ required: true, message: 'Vui lòng chọn đơn vị vận chuyển', trigger: 'change' }],
        number: [{ required: true, message: 'Vui lòng nhập mã vận đơn', trigger: 'change' }],
        sendName: [{ required: true, message: 'Vui lòng điền họ tên người gửi', trigger: 'change' }],
        sendPhone: [
          { required: true, message: 'Vui lòng điền số điện thoại người gửi', trigger: 'change' },
          { pattern: /^1[3456789]\d{9}$/, message: 'Số điện thoại không đúng định dạng', trigger: 'blur' },
        ],
        sendAddress: [{ required: true, message: 'Vui lòng điền địa chỉ người gửi', trigger: 'change' }],
        msg: [{ required: true, message: 'Vui lòng điền thông tin ghi chú', trigger: 'change' }],
      },
      formValidate: {
        gender: 1,
        shipStatus: 1,
        logisticsCode: '', // Mã đơn vị vận chuyển
        logisticsName: '', // Tên đơn vị vận chuyển
        number: '', // Mã vận đơn
        electronic: '', //Vận đơn điện tử
        sendName: '', //Họ tên người gửi
        sendPhone: '', // Số điện thoại người gửi
        sendAddress: '', //Địa chỉ người gửi
        postPeople: '', // Nhân viên giao hàng
        msg: '', // Ghi chú
      },
      logisticsList: [],
      orderTempList: [],
      deliveryList: [],
    };
  },
  mounted() {
    this.getOrderExport();
    this.getDelivery();
  },
  methods: {
    // Lấy người giao hàng
    getDelivery() {
      orderDeliveryAll().then((res) => {
        this.deliveryList = res.data;
      });
    },
    //Xem ảnh lớn
    inited(viewer) {
      this.$viewer = viewer;
    },
    //Đơn vị vận chuyển
    getOrderExport() {
      orderExport().then((res) => {
        this.logisticsList = res.data;
      });
    },
    handleSubmit(name) {
      if (this.formValidate.gender == 1) {
        this.$refs[name].validate((valid) => {
          let paramsData = {};
          paramsData.type = this.formValidate.gender;
          paramsData.express_record_type = parseFloat(this.formValidate.shipStatus);
          paramsData.delivery_name = this.formValidate.logisticsName;
          paramsData.delivery_code = this.formValidate.logisticsCode;
          if (valid) {
            // Thủ công
            if (this.formValidate.gender == 1 && this.formValidate.shipStatus == 1) {
              paramsData.delivery_id = this.formValidate.number;
            }
            // Điện tử
            if (this.formValidate.gender == 1 && this.formValidate.shipStatus == 2) {
              paramsData.to_name = this.formValidate.sendName;
              paramsData.to_tel = this.formValidate.sendPhone;
              paramsData.to_addr = this.formValidate.sendAddress;
              paramsData.express_temp_id = this.formValidate.electronic;
            }
            orderDelivery(this.orderId, paramsData)
              .then((res) => {
                this.$message.success(res.msg);
                this.$emit('ok');
              })
              .catch((error) => {
                this.$message.error(error.msg);
              });
          } else {
          }
        });
      }
      if (this.formValidate.gender == 2) {
        let people = {};
        this.deliveryList.forEach((el, index) => {
          if (el.id == this.formValidate.postPeople) {
            people = el;
          }
        });
        orderDelivery(this.orderId, {
          type: this.formValidate.gender,
          sh_delivery_name: people.wx_name,
          sh_delivery_id: people.phone,
          sh_delivery_uid: people.id,
        })
          .then((res) => {
            this.$message.success(res.msg);
            this.$emit('ok');
          })
          .catch((error) => {
            this.$message.error(error.msg);
          });
      }
      if (this.formValidate.gender == 3) {
        orderDelivery(this.orderId, {
          type: this.formValidate.gender,
          remark: this.formValidate.msg,
        })
          .then((res) => {
            this.$message.success(res.msg);
            this.$emit('ok');
          })
          .catch((error) => {
            this.$message.error(error.msg);
          });
      }
    },
    close() {
      this.$emit('close');
    },
    // Vận chuyển đã chọn
    bindChange(val) {
      let deliveryItem = this.logisticsList.find((item) => {
        return item.code == val;
      });
      this.formValidate.logisticsName = deliveryItem.value;
      if (this.formValidate.shipStatus == 2) {
        orderTemp({
          com: val.value,
        }).then((res) => {
          this.orderTempList = res.data.data;
        });
      }
    },
    lookImg() {
      if (this.formValidate.electronic) {
        this.orderTempList.forEach((el, index) => {
          if (el.temp_id == this.formValidate.electronic) {
            this.$viewer.view(index);
          }
        });
      } else {
        this.$message.error('Vui lòng chọn vận đơn điện tử');
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.form-item {
  width: 100%;
}
</style>
