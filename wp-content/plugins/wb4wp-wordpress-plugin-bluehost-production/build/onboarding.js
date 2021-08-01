!function(e){var t={};function r(n){if(t[n])return t[n].exports;var o=t[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=e,r.c=t,r.d=function(e,t,n){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(n,o,function(t){return e[t]}.bind(null,o));return n},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=28)}([function(e,t){!function(){e.exports=this.wp.element}()},,function(e,t,r){var n=r(9),o=r(10),i=r(11),l=r(13);e.exports=function(e,t){return n(e)||o(e,t)||i(e,t)||l()},e.exports.default=e.exports,e.exports.__esModule=!0},,,,,,,function(e,t){e.exports=function(e){if(Array.isArray(e))return e},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t){e.exports=function(e,t){var r=e&&("undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"]);if(null!=r){var n,o,i=[],l=!0,a=!1;try{for(r=r.call(e);!(l=(n=r.next()).done)&&(i.push(n.value),!t||i.length!==t);l=!0);}catch(e){a=!0,o=e}finally{try{l||null==r.return||r.return()}finally{if(a)throw o}}return i}},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t,r){var n=r(12);e.exports=function(e,t){if(e){if("string"==typeof e)return n(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?n(e,t):void 0}},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t){e.exports=function(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t){e.exports=function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")},e.exports.default=e.exports,e.exports.__esModule=!0},,,function(e,t){function r(){return e.exports=r=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var n in r)Object.prototype.hasOwnProperty.call(r,n)&&(e[n]=r[n])}return e},e.exports.default=e.exports,e.exports.__esModule=!0,r.apply(this,arguments)}e.exports=r,e.exports.default=e.exports,e.exports.__esModule=!0},,,,,,,,,,,,function(e,t,r){"use strict";r.r(t);var n,o,i,l,a=r(2),u=r.n(a),c=r(16),s=r.n(c),d=r(0),b=(null===(n=window)||void 0===n||null===(o=n.websiteBuilder)||void 0===o?void 0:o.provider)||"Websitebuilder",f=(null===(i=window)||void 0===i||null===(l=i.websiteBuilder)||void 0===l?void 0:l.providerDomain)||"Websitebuilder.com",p=function(){return Object(d.createElement)("h1",{className:"website-builder-onboarding-title"},"Effortlessly create an amazing ",Object(d.createElement)("br",null),"WordPress site")},m=function(){return Object(d.createElement)("p",{className:"website-builder-onboarding-subtitle"},"Get started with ",b)},v=function(e){var t=e.children;return Object(d.createElement)("p",{className:"website-builder-onboarding-copy"},t)},g=function(e){var t=e.children;return Object(d.createElement)("ul",{className:"website-builder-onboarding-list"},t)};(g.Item=function(e){var t=e.children;return Object(d.createElement)("li",{className:"website-builder-onboarding-list-item"},t)}).displayName="List.Item";var y=function(e){var t,r,n=e.disabled,o=e.children,i=e.marginBottom,l=void 0!==i&&i,a=n?{}:{href:(null===(t=window)||void 0===t||null===(r=t.websiteBuilder)||void 0===r?void 0:r.launchUrl)||"/wp-admin/post-new.php"};return Object(d.createElement)("a",s()({},a,{className:"website-builder-onboarding-button"+(n?" website-builder-onboarding-button--disabled tooltip":"")+(l?" website-builder-onboarding-button--mb":""),target:"_self"}),n&&Object(d.createElement)("span",{className:"tooltip-text"},"The ",b," Editor only works for publicly available WordPress sites"),o)},w=function(){return Object(d.createElement)("div",{className:"website-builder-onboarding-error"},Object(d.createElement)("span",{className:"dashicons dashicons-warning website-builder-onboarding-error-icon"}),Object(d.createElement)("p",{className:"website-builder-onboarding-error-message"},"We can not reach your plugin from the Builder Stack - Are you sure your WordPress site is publicly available?"))},O=function(e){var t=e.disabled;return Object(d.createElement)(d.Fragment,null,Object(d.createElement)(v,null,"The fine print:"),Object(d.createElement)(g,null,Object(d.createElement)(g.Item,null,"By using ",b," you’ll share basic information about your site (including your site name and URL) with ",f," so that we can retrieve your blog posts, media files and store products for use on your website;"),Object(d.createElement)(g.Item,null,b," uses tools, including cookies, to improve the performance and experience of the product. For more information you can read our",Object(d.createElement)("a",{href:"#",className:"website-builder-onboarding-link",target:"_blank"}),"privacy notice.")),Object(d.createElement)(y,{disabled:t},"I agree, let's get started"))},j=function(e){var t=e.disabled;return Object(d.createElement)(d.Fragment,null,Object(d.createElement)(y,{disabled:t,marginBottom:!0},"Open the Editor"),Object(d.createElement)(g,null,Object(d.createElement)(g.Item,null,"Create an amazing website with our drag and drop editor."),Object(d.createElement)(g.Item,null,"Maintain a consistent style throughout your website."),Object(d.createElement)(g.Item,null,"No technical experience required.")))};Object(d.render)(Object(d.createElement)((function(){var e=Object(d.useState)(),t=u()(e,2),r=t[0],n=t[1];Object(d.useEffect)((function(){var e=new URL(window.websiteBuilder.pingUrl);e.searchParams.set("instance",window.location.origin),fetch(e).then((function(e){return e.ok})).catch((function(){return!1})).then(n)}),[]);var o=!1===r,i="provisioned"===window.websiteBuilder.pluginState;return Object(d.createElement)("div",{className:"website-builder-onboarding-card"},Object(d.createElement)("div",{className:"website-builder-onboarding-content"},o&&Object(d.createElement)(w,null),Object(d.createElement)(p,null),Object(d.createElement)(m,null),i?Object(d.createElement)(j,null):Object(d.createElement)(O,null)))}),null),document.getElementById("website-builder-onboarding"))}]);