(function(e){function t(t){for(var a,o,s=t[0],u=t[1],l=t[2],c=0,d=[];c<s.length;c++)o=s[c],Object.prototype.hasOwnProperty.call(r,o)&&r[o]&&d.push(r[o][0]),r[o]=0;for(a in u)Object.prototype.hasOwnProperty.call(u,a)&&(e[a]=u[a]);p&&p(t);while(d.length)d.shift()();return i.push.apply(i,l||[]),n()}function n(){for(var e,t=0;t<i.length;t++){for(var n=i[t],a=!0,s=1;s<n.length;s++){var u=n[s];0!==r[u]&&(a=!1)}a&&(i.splice(t--,1),e=o(o.s=n[0]))}return e}var a={},r={app:0},i=[];function o(t){if(a[t])return a[t].exports;var n=a[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.m=e,o.c=a,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)o.d(n,a,function(t){return e[t]}.bind(null,a));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/vue-apps/bezahlen/dist/";var s=window["webpackJsonp"]=window["webpackJsonp"]||[],u=s.push.bind(s);s.push=t,s=s.slice();for(var l=0;l<s.length;l++)t(s[l]);var p=u;i.push([0,"chunk-vendors"]),n()})({0:function(e,t,n){e.exports=n("56d7")},1634:function(e,t,n){},"56d7":function(e,t,n){"use strict";n.r(t);n("a79d");var a=n("2b0e"),r=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"content",attrs:{id:"app"}},[n("div",{staticClass:"info"},[e._v(" Du nutzt Paypal? Jetzt einfach bezahlen! Wähle einfach denjenigen aus, der heute für das Essen zuständig ist! ")]),n("form",{attrs:{"accept-charset":"utf-8",action:"/jetzt-zahlen",method:"post"}},[n("input",{attrs:{autocomplete:"off",name:"_csrfToken",type:"hidden"},domProps:{value:e.csrfToken}}),n("PayList")],1),n("p",[e._v("Du willst auch in der Liste stehen? "),n("span",{domProps:{innerHTML:e._s(e.payAddLink)}},[e._v("Hier")]),e._v(" kannst du dich eintragen")])])},i=[],o=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"list content"},[n("h3",[e._v("Paypalierer")]),n("div",{staticClass:"range-slider"},[n("h4",[e._v("Trinkgeld:")]),n("input",{directives:[{name:"model",rawName:"v-model",value:e.tipValue,expression:"tipValue"}],staticClass:"range-slider__range",attrs:{max:"5",min:"0",name:"tip",step:"0.1",type:"range"},domProps:{value:e.tipValue},on:{__r:function(t){e.tipValue=t.target.value}}}),n("span",{staticClass:"range-slider__value"},[e._v(e._s(e.tipValueFixed)+"€")])]),n("hr"),e._l(e.paypalmes,(function(t){return n("div",{key:t.id,class:"paypalmeslistitem"+("number"===typeof e.activeId&&e.activeId===t.id?" active":"")},[n("button",{attrs:{value:t.id,name:"id",type:"submit"}},[e._v(e._s(t.name))])])}))],2)},s=[],u=(n("5319"),{name:"PayList",data:()=>({paypalmes:window.paypalmes,activeId:window.activeId,tipValue:.5}),computed:{tipValueFixed:function(){return Number.parseFloat(this.tipValue).toFixed(2).replace(".",",")}}}),l=u,p=(n("b677"),n("2877")),c=Object(p["a"])(l,o,s,!1,null,"57e7a6f2",null),d=c.exports,f={name:"App",components:{PayList:d},data:()=>({payAddLink:window.payAddLink,csrfToken:window.csrfToken})},v=f,m=Object(p["a"])(v,r,i,!1,null,null,null),h=m.exports;a["a"].config.productionTip=!1,new a["a"]({render:e=>e(h)}).$mount("#app")},b677:function(e,t,n){"use strict";n("1634")}});
//# sourceMappingURL=app.js.map