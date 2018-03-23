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
/******/ 	return __webpack_require__(__webpack_require__.s = 38);
/******/ })
/************************************************************************/
/******/ ({

/***/ 38:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(39);


/***/ }),

/***/ 39:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function () {
    tinymce.create('tinymce.plugins.YPRecentPosts', {
        init: function init(editor, url) {
            editor.addButton('yp_recent_posts', {
                title: 'YP Recent Posts',
                icon: 'fa-thumb-tack',
                onclick: function onclick() {
                    editor.execCommand('mceInsertContent', false, '[yp_recent_posts style="1" post_type="post" ids="" exclude_ids="" custom_query="" count="5" pagination="false" boxed="false"]');
                }
            });
        },
        createControl: function createControl(n, cm) {
            return null;
        },
        getInfo: function getInfo() {
            return {
                longname: "YP Recent Posts Shortcode",
                author: 'nK',
                authorurl: 'https://nkdev.info/',
                infourl: 'https://nkdev.info/',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('yp_recent_posts', tinymce.plugins.YPRecentPosts);
})();

/***/ })

/******/ });