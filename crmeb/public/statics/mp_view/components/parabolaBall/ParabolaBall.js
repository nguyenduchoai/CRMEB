(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/parabolaBall/ParabolaBall"],{"144f":function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;e.default={props:{size:{type:Number,default:20},color:{type:String,default:"#f5222d"},zIndex:{type:Number,default:999},duration:{type:Number,default:500}},data:function(){return{dots:[]}},methods:{showBall:function(t){var e=this,n=t.start,r=(t.end,t.src);return new Promise((function(t){var u=e.dots.find((function(t){return!t.show}));u||(u={src:"",left:0,top:0,show:!1},e.dots.push(u));var a=e.duration,o=n.x-e.size/2,f=n.y-e.size/2,c=50-e.size/2,i=640-e.size/2,s=Date.now(),l=c-o,d=i-f,p=-2*l/(a*a)/5,b=Math.abs(p),v=l/a-p*a/2,h=d/a-b*a/2,w=function(){var e=Date.now()-s,n=o+(v*e+p*e*e/2),r=f+(h*e+b*e*e/2);u.left=n,u.top=r,e<a?setTimeout(w):(u.show=!1,t())};u.src=r,u.show=!0,w()}))}}}},"2f14":function(t,e,n){"use strict";n.r(e);var r=n("c1cf"),u=n("3d8c");for(var a in u)["default"].indexOf(a)<0&&function(t){n.d(e,t,(function(){return u[t]}))}(a);var o,f=n("f0c5"),c=Object(f["a"])(u["default"],r["b"],r["c"],!1,null,null,null,!1,r["a"],o);e["default"]=c.exports},"3d8c":function(t,e,n){"use strict";n.r(e);var r=n("144f"),u=n.n(r);for(var a in r)["default"].indexOf(a)<0&&function(t){n.d(e,t,(function(){return r[t]}))}(a);e["default"]=u.a},c1cf:function(t,e,n){"use strict";var r;n.d(e,"b",(function(){return u})),n.d(e,"c",(function(){return a})),n.d(e,"a",(function(){return r}));var u=function(){var t=this,e=t.$createElement;t._self._c},a=[]}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/parabolaBall/ParabolaBall-create-component',
    {
        'components/parabolaBall/ParabolaBall-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('543d')['createComponent'](__webpack_require__("2f14"))
        })
    },
    [['components/parabolaBall/ParabolaBall-create-component']]
]);
