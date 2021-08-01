!function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=26)}({0:function(e,t){!function(){e.exports=this.wp.element}()},15:function(e,t){e.exports=function(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e},e.exports.default=e.exports,e.exports.__esModule=!0},26:function(e,t,n){"use strict";n.r(t);var r=n(0),o=n(8),i=n(7),l=n(5),c=n(15),a=n.n(c),s=function(){return Object(r.createElement)("svg",a()({width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},"fill","currentColor"),Object(r.createElement)("path",{d:"M3 3H8.02478V8.01382H3V3ZM9.48105 3H14.5058V8.01382H9.48105V3ZM15.9752 3H21V8.01382H15.9752V3ZM3 9.49309H8.02478V14.5069H3V9.49309ZM9.48105 9.49309H14.5058V14.5069H9.48105V9.49309ZM15.9752 9.49309H21V14.5069H15.9752V9.49309ZM3 15.9862H8.02478V21H3V15.9862ZM9.48105 15.9862H14.5058V21H9.48105V15.9862ZM15.9752 15.9862H21V21H15.9752V15.9862Z"}))};Object(o.registerBlockType)("wb4wp/block-generic-section",{title:"Website Builder Section",icon:Object(r.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",version:"1.0",width:"159.000000pt",height:"160.000000pt",viewBox:"0 0 159.000000 160.000000",preserveAspectRatio:"xMidYMid meet"},Object(r.createElement)("g",{transform:"translate(0.000000,160.000000) scale(0.100000,-0.100000)",stroke:"none"},Object(r.createElement)("path",{fill:"#3575d3",d:"M310 1150 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M660 1150 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M1000 1150 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M310 800 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M660 800 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M1000 800 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M310 450 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M660 450 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}),Object(r.createElement)("path",{fill:"#3575d3",d:"M1000 450 l0 -140 140 0 140 0 0 140 0 140 -140 0 -140 0 0 -140z"}))),description:"Section that's built with the Bluehost Website Builder",category:"layout",supports:{align:["wide","full"],inserter:!1},attributes:{id:{type:"number"},fonts:{type:"string"},align:{type:"string",default:"full"},layout:{type:"object"},category:{type:"string"},binding:{type:"object"}},edit:function(e){var t=Object(r.useCallback)((function(){var e=window.wp.data.select("core/editor").getCurrentPost().id;window.location.assign("".concat(window.websiteBuilder.root,"wp-admin/admin.php?page=wb4wp-editor&wb4wp-post-id=").concat(e))}),[]),n=null;return window.wp.data.select("core/block-editor").getBlock(e.clientId).innerBlocks[0]&&(n=(n=window.wp.data.select("core/block-editor").getBlock(e.clientId).innerBlocks[0].originalContent).replace(/kv-notify-inview/gi,"")),window.setTimeout((function(){var e=document.querySelectorAll(".kv-site *[data-src]");Array.from(e).forEach((function(e){e.style.backgroundImage="url("+e.dataset.srcNormal+")",e.removeAttribute("data-src")}));var t=document.querySelectorAll(".kv-site *[data-image]");Array.from(t).forEach((function(e){e.setAttribute("style",e.getAttribute("style")+";"+e.dataset.image),e.removeAttribute("data-image")}));["email","number"].forEach((function(e){document.querySelectorAll('.kv-site input[type="'.concat(e,'"]')).forEach((function(e){e.setAttribute("type","text")}))}))}),1),Object(r.createElement)("div",{className:"block-generic-section"},Object(r.createElement)("div",{className:"express-block-button-wrapper"},Object(r.createElement)(i.Button,{isPrimary:!0,isLarge:!0,onClick:t,icon:s,className:"express-block-button"},"Edit in Website Builder")),Object(r.createElement)("div",{className:"express-block-wrapper"},Object(r.createElement)("div",{className:"kv-main kv-site express-block",dangerouslySetInnerHTML:{__html:n}})),Object(r.createElement)("div",{className:"express-block-overlay"}))},save:function(){return Object(r.createElement)(l.InnerBlocks.Content,null)}})},5:function(e,t){!function(){e.exports=this.wp.blockEditor}()},7:function(e,t){!function(){e.exports=this.wp.components}()},8:function(e,t){!function(){e.exports=this.wp.blocks}()}});