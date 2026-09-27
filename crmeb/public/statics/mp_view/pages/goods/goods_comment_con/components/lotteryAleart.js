require('../../common/vendor.js');(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/goods/goods_comment_con/components/lotteryAleart"],{3506:function(t,a,e){"use strict";e.r(a);var n=e("b2f4"),r=e("b0ac");for(var u in r)["default"].indexOf(u)<0&&function(t){e.d(a,t,(function(){return r[t]}))}(u);e("d22c");var o,c=e("f0c5"),f=Object(c["a"])(r["default"],n["b"],n["c"],!1,null,"7960acfe",null,!1,n["a"],o);a["default"]=f.exports},"8d59":function(t,a,e){},b0ac:function(t,a,e){"use strict";e.r(a);var n=e("fe2f"),r=e.n(n);for(var u in n)["default"].indexOf(u)<0&&function(t){e.d(a,t,(function(){return n[t]}))}(u);a["default"]=r.a},b2f4:function(t,a,e){"use strict";var n;e.d(a,"b",(function(){return r})),e.d(a,"c",(function(){return u})),e.d(a,"a",(function(){return n}));var r=function(){var t=this,a=t.$createElement;t._self._c},u=[]},d22c:function(t,a,e){"use strict";var n=e("8d59"),r=e.n(n);r.a},fe2f:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;a.default={data:function(){return{aleartData:{}}},props:{aleartType:{type:Number},alData:{type:Object},aleartStatus:{type:Boolean,default:!1}},watch:{aleartType:function(t){2===t&&(this.aleartData={title:"Kết quả quay thưởng",img:this.alData.image,msg:this.alData.prompt,btn:"OK",type:this.alData.type})},aleartStatus:function(t){t||(this.aleartData={})}},methods:{posterImageClose:function(t){this.$emit("close",!1)}}}}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'pages/goods/goods_comment_con/components/lotteryAleart-create-component',
    {
        'pages/goods/goods_comment_con/components/lotteryAleart-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("3506"))
        })
    },
    [['pages/goods/goods_comment_con/components/lotteryAleart-create-component']]
]);
