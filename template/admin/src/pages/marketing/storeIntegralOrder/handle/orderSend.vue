<template>
  <el-dialog :visible.sync="modals" title="Thực hiện giao đơn hàng" width="720px" class="order_box" :show-close="true">
    <el-form ref="formItem" :model="formItem" label-width="100px" @submit.native.prevent>
      <el-form-item label="Chọn loại:">
        <el-radio-group v-model="formItem.type" @input="changeRadio">
          <el-radio label="1">Giao hàng</el-radio>
          <el-radio label="2">Cửa hàng tự giao</el-radio>
          <el-radio label="3">Hàng ảo</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item v-show="formItem.type == '1' && export_open" label="Loại giao hàng:">
        <el-radio-group v-model="formItem.express_record_type" @input="changeExpress">
          <el-radio label="1">Điền thủ công</el-radio>
          <el-radio label="2">In vận đơn điện tử</el-radio>
        </el-radio-group>
      </el-form-item>
      <div v-show="formItem.type === '1'">
        <el-form-item label="Đơn vị vận chuyển:">
          <el-select
            v-model="formItem.delivery_name"
            filterable
            placeholder="Vui lòng chọn đơn vị vận chuyển"
            style="width: 80%"
            @change="expressChange"
          >
            <el-option
              v-for="(item, i) in express"
              :value="item.value"
              :key="item.value"
              :label="item.value"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item v-if="formItem.express_record_type === '1'" label="Mã vận đơn:">
          <el-input v-model="formItem.delivery_id" placeholder="Vui lòng nhập mã vận đơn" style="width: 80%"></el-input>
          <div class="trips" v-if="formItem.delivery_name == 'SF Express'">
            <p>Với SF Express, vui lòng nhập mã vận đơn: 4 số cuối số điện thoại người nhận hoặc người gửi</p>
            <p>Ví dụ: SF000000000000:3941</p>
          </div>
        </el-form-item>
        <template v-if="formItem.express_record_type === '2'">
          <el-form-item label="Vận đơn điện tử:" class="express_temp_id">
            <el-select
              v-model="formItem.express_temp_id"
              placeholder="Vui lòng chọn vận đơn điện tử"
              style="width: 80%"
              @change="expressTempChange"
            >
              <el-option
                v-for="(item, i) in expressTemp"
                :value="item.temp_id"
                :key="i"
                :label="item.title"
              ></el-option>
            </el-select>
            <el-button v-if="formItem.express_temp_id" type="text" v-db-click @click="preview">Xem trước</el-button>
          </el-form-item>
          <el-form-item label="Họ tên người gửi:">
            <el-input v-model="formItem.to_name" placeholder="Vui lòng nhập họ tên người gửi" style="width: 80%"></el-input>
          </el-form-item>
          <el-form-item label="Số điện thoại người gửi:">
            <el-input v-model="formItem.to_tel" placeholder="Vui lòng nhập số điện thoại người gửi" style="width: 80%"></el-input>
          </el-form-item>
          <el-form-item label="Địa chỉ người gửi:">
            <el-input v-model="formItem.to_addr" placeholder="Vui lòng nhập địa chỉ người gửi" style="width: 80%"></el-input>
          </el-form-item>
        </template>
      </div>
      <div v-show="formItem.type === '2'">
        <el-form-item label="Người giao hàng:">
          <el-select
            v-model="formItem.sh_delivery"
            placeholder="Vui lòng chọn người giao hàng"
            style="width: 80%"
            @change="shDeliveryChange"
          >
            <el-option
              v-for="(item, i) in deliveryList"
              :value="item.id"
              :key="i"
              :label="`${item.wx_name} (${item.phone})`"
            ></el-option>
          </el-select>
        </el-form-item>
      </div>
      <div v-show="formItem.type === '3'">
        <el-form-item label="Ghi chú:">
          <el-input
            v-model="formItem.fictitious_content"
            type="textarea"
            :autosize="{ minRows: 2, maxRows: 5 }"
            placeholder="Ghi chú"
            style="width: 80%"
          ></el-input>
        </el-form-item>
      </div>
    </el-form>
    <span slot="footer" class="dialog-footer">
      <el-button v-db-click @click="cancel">Hủy</el-button>
      <el-button type="primary" v-db-click @click="putSend">Gửi</el-button>
    </span>
    <div ref="viewer" v-viewer v-show="temp">
      <img :src="temp.pic" style="display: none" />
    </div>
  </el-dialog>
</template>

<script>
import {
  getExpressData,
  orderExpressTemp,
  orderDeliveryList,
  orderSheetInfo,
  integralOrderPutDelivery,
} from '@/api/marketing';
// import {integralOrderPutDelivery} from "@/api/marketing";
export default {
  name: 'orderSend',
  props: {
    orderId: Number,
  },
  data() {
    return {
      formItem: {
        type: '1',
        express_record_type: '1',
        delivery_name: '',
        delivery_id: '',
        express_temp_id: '',
        to_name: '',
        to_tel: '',
        to_addr: '',
        sh_delivery: '',
        fictitious_content: '',
      },
      modals: false,
      express: [],
      expressTemp: [],
      deliveryList: [],
      // ruleValidate: {
      //     delivery_name: [
      //         { required: true, message: 'Vui lòng chọn đơn vị vận chuyển', trigger: 'change' }
      //     ],
      //     delivery_id: [
      //         { required: true, message: 'Vui lòng nhập mã vận đơn', trigger: 'blur' }
      //     ],
      //     express_temp_id: [
      //         { required: true, message: 'Vui lòng chọn vận đơn điện tử', trigger: 'change' }
      //     ],
      //     sh_delivery: [
      //         { required: true, message: 'Vui lòng chọn người giao hàng', trigger: 'change', type: 'number' }
      //     ]
      // },
      temp: {},
      export_open: false,
    };
  },
  methods: {
    changeRadio(o) {
      this.$refs.formItem.resetFields();
      switch (o) {
        case '1':
          this.formItem.delivery_name = '';
          this.formItem.delivery_id = '';
          this.formItem.express_temp_id = '';
          this.formItem.express_record_type = '1';
          this.expressTemp = [];
          this.getList(1);
          break;
        case '2':
          this.formItem.sh_delivery = '';
          break;
        case '3':
          this.formItem.fictitious_content = '';
          break;
        default:
          // this.formItem = {
          //     type: '3',
          //     express_record_type: '1',
          //     delivery_name: '',
          //     delivery_id: '',
          //     express_temp_id: '',
          //     to_name: '',
          //     to_tel: '',
          //     to_addr: '',
          //     sh_delivery: ''
          // };
          break;
      }
    },
    changeExpress(j) {
      switch (j) {
        case '2':
          this.formItem.delivery_name = '';
          this.formItem.express_temp_id = '';
          this.expressTemp = [];
          this.getList(2);
          break;
        case '1':
          this.formItem.delivery_name = '';
          this.formItem.delivery_id = '';
          this.getList(1);
          break;
        default:
          break;
      }
    },
    reset() {
      this.formItem = {
        type: '1',
        express_record_type: '1',
        delivery_name: '',
        delivery_id: '',
        express_temp_id: '',
        expressTemp: [],
        to_name: '',
        to_tel: '',
        to_addr: '',
        sh_delivery: '',
        fictitious_content: '',
      };
    },
    // Danh sách đơn vị vận chuyển
    getList(type) {
      let status = type === 2 ? 1 : '';
      getExpressData(status)
        .then(async (res) => {
          this.express = res.data;
          this.getSheetInfo();
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Gửi
    putSend(name) {
      let data = {
        id: this.orderId,
        datas: this.formItem,
      };
      if (this.formItem.type === '1' && this.formItem.express_record_type === '2') {
        if (this.formItem.delivery_name === '') {
          return this.$message.error('Đơn vị vận chuyển không được để trống');
        } else if (this.formItem.express_temp_id === '') {
          return this.$message.error('Vận đơn điện tử không được để trống');
        } else if (this.formItem.to_name === '') {
          return this.$message.error('Họ tên người gửi không được để trống');
        } else if (this.formItem.to_tel === '') {
          return this.$message.error('Số điện thoại người gửi không được để trống');
        } else if (!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(this.formItem.to_tel)) {
          return this.$message.error('Vui lòng nhập đúng số điện thoại');
        } else if (this.formItem.to_addr === '') {
          return this.$message.error('Địa chỉ người gửi không được để trống');
        }
      }
      if (this.formItem.type === '1' && this.formItem.express_record_type === '1') {
        if (this.formItem.delivery_name === '') {
          return this.$message.error('Đơn vị vận chuyển không được để trống');
        } else if (this.formItem.delivery_id === '') {
          return this.$message.error('Mã vận đơn không được để trống');
        }
      }
      if (this.formItem.type === '2') {
        if (this.formItem.sh_delivery === '') {
          return this.$message.error('Người giao hàng không được để trống');
        }
      }
      integralOrderPutDelivery(data)
        .then(async (res) => {
          this.$emit('submitFail');
          this.modals = false;
          this.$message.success(res.msg);
          this.reset();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
      // if (this.formItem.type == 3) {
      //     putDelivery(data).then(async res => {
      //         this.modals = false;
      //         this.$message.success(res.msg);
      //         this.$refs[name].resetFields();
      //         this.$emit('submitFail')
      //     }).catch(res => {
      //         this.$message.error(res.msg);
      //     })
      // } else {
      //     this.$refs[name].validate((valid) => {
      //         if (valid) {
      //             putDelivery(data).then(async res => {
      //                 this.modals = false;
      //                 this.$message.success(res.msg);
      //                 this.$refs[name].resetFields();
      //                 this.$emit('submitFail')
      //             }).catch(res => {
      //                 this.$message.error(res.msg);
      //             })
      //         } else {
      //             this.$message.error('Vui lòng điền thông tin');
      //         }
      //     })
      // }
    },
    cancel(name) {
      this.modals = false;
      this.reset();
      // this.$refs[name].resetFields();
      // this.formItem.type = '1';
    },
    // Danh sách vận đơn điện tử
    expressChange(value) {
      let expressItem = this.express.find((item) => {
        return item.value === value;
      });
      if (expressItem === undefined) {
        return;
      }
      this.formItem.delivery_code = expressItem.code;
      if (this.formItem.express_record_type === '2') {
        this.expressTemp = [];
        this.formItem.express_temp_id = '';
        orderExpressTemp({
          com: this.formItem.delivery_code,
        })
          .then((res) => {
            this.expressTemp = res.data;
            if (!res.data.length) {
              this.$message.error('Vui lòng cấu hình vận đơn điện tử cho đơn vị vận chuyển bạn đã chọn');
            }
          })
          .catch((err) => {
            this.$message.error(err.msg);
          });
      }
    },
    getDeliveryList() {
      orderDeliveryList()
        .then((res) => {
          this.deliveryList = res.data.list;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    getSheetInfo() {
      orderSheetInfo()
        .then((res) => {
          const data = res.data;
          for (const key in data) {
            if (data.hasOwnProperty(key)) {
              this.formItem[key] = data[key];
            }
          }
          this.export_open = data.export_open === undefined ? true : data.export_open;
          if (!this.export_open) {
            this.formItem.express_record_type = '1';
          }
          this.formItem.to_addr = data.to_add;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    shDeliveryChange(value) {
      let deliveryItem = this.deliveryList.find((item) => {
        return item.id === value;
      });
      this.formItem.sh_delivery_name = deliveryItem.wx_name;
      this.formItem.sh_delivery_id = deliveryItem.phone;
      this.formItem.sh_delivery_uid = deliveryItem.uid;
    },
    expressTempChange(tempId) {
      this.temp = this.expressTemp.find((item) => {
        return tempId === item.temp_id;
      });
      if (this.temp === undefined) {
        this.temp = {};
      }
    },
    // inited (viewer) {
    //     this.$viewer = viewer;
    // },
    preview() {
      this.$refs.viewer.$viewer.show();
      // this.$viewer.show();
    },
  },
};
</script>

<style scoped>
.express_temp_id {
  position: relative;
}

.express_temp_id button {
  position: absolute;
  top: 50%;
  right: 110px;
  padding: 0;
  border: none;
  background: none;
  transform: translateY(-50%);
  color: #57a3f3;
}

.ivu-btn-text:focus {
  box-shadow: none;
}
.trips {
  color: #ccc;
}
</style>
