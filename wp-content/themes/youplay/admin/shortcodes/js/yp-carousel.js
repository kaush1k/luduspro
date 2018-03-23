/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 26);
/******/ })
/************************************************************************/
/******/ ({

/***/ 26:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(27);


/***/ }),

/***/ 27:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function () {
    tinymce.create('tinymce.plugins.YPCarousel', {
        init: function init(editor, url) {
            editor.addButton('yp_carousel', {
                title: 'YP Carousel',
                icon: 'fa-refresh',
                onclick: function onclick() {
                    editor.execCommand('mceInsertContent', false, '[yp_carousel style="1" width="100%" badges_always_show="false" center="false" boxed="false"]<br>   [yp_carousel_img img_src="88" title="Image 1" href="https://nkdev.info" rating="3" badge_text="Badge 1" badge_color="default" price="$10"]<br>   [yp_carousel_img img_src="85" title="Image 2" href="https://nkdev.info" rating="5" badge_text="Badge 2" badge_color="primary" price="$14"]<br>[/yp_carousel]');
                }
            });
        },
        createControl: function createControl(n, cm) {
            return null;
        },
        getInfo: function getInfo() {
            return {
                longname: "YP Carousel Shortcode",
                author: 'nK',
                authorurl: 'https://nkdev.info/',
                infourl: 'https://nkdev.info/',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('yp_carousel', tinymce.plugins.YPCarousel);
})();

/***/ })

/******/ });