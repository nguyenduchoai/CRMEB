require('../../common/vendor.js');(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/goods/lottery/components/noticeBar"],{"0dd0":function(t,n,e){"use strict";e.r(n);var a=e("955e"),i=e("1222");for(var r in i)["default"].indexOf(r)<0&&function(t){e.d(n,t,(function(){return i[t]}))}(r);e("8b4c");var o,s=e("f0c5"),c=Object(s["a"])(i["default"],a["b"],a["c"],!1,null,"28703674",null,!1,a["a"],o);n["default"]=c.exports},1222:function(t,n,e){"use strict";e.r(n);var a=e("935e"),i=e.n(a);for(var r in a)["default"].indexOf(r)<0&&function(t){e.d(n,t,(function(){return a[t]}))}(r);n["default"]=i.a},"798c":function(t,n,e){},"8b4c":function(t,n,e){"use strict";var a=e("798c"),i=e.n(a);i.a},"935e":function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;n.default={name:"noticeBar",data:function(){return{animateUp:!1,listData:JSON.parse(JSON.stringify(this.showMsg)),timer:null}},props:{showMsg:{type:Array}},mounted:function(){this.timer=setInterval(this.scrollAnimate,2500)},methods:{scrollAnimate:function(){var t=this;this.animateUp=!0,setTimeout((function(){t.listData.push(t.listData[0]),t.listData.shift(),t.animateUp=!1}),500)}},destroyed:function(){clearInterval(this.timer)}}},"955e":function(t,n,e){"use strict";var a;e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return r})),e.d(n,"a",(function(){return a}));var i=function(){var t=this,n=t.$createElement,e=(t._self._c,t.$t("Chúc mừng bạn")),a=t.$t("nhận được");t.$mp.data=Object.assign({},{$root:{m0:e,m1:a}})},r=[]}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'pages/goods/lottery/components/noticeBar-create-component',
    {
        'pages/goods/lottery/components/noticeBar-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("0dd0"))
        })
    },
    [['pages/goods/lottery/components/noticeBar-create-component']]
]);
