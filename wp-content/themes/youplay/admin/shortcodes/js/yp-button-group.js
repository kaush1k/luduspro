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
/******/ 	return __webpack_require__(__webpack_require__.s = 22);
/******/ })
/************************************************************************/
/******/ ({

/***/ 22:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(23);


/***/ }),

/***/ 23:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function () {
    tinymce.create('tinymce.plugins.YPButtonGroup', {
        init: function init(editor, url) {
            editor.addButton('yp_button_group', {
                title: 'YP Button Group',
                icon: 'fa-square-o',
                onclick: function onclick() {
                    editor.execCommand('mceInsertContent', false, '[yp_button_group]<br>   [yp_button href="https://nkdev.info" size="" full_width="false" active="false" color="success" icon_before="fa fa-html5" icon_after=""]Youplay 1[/yp_button]<br>   [yp_button href="https://nkdev.info" size="" full_width="false" active="false" color="success" icon_before="fa fa-css3" icon_after=""]Youplay 2[/yp_button]<br>[/yp_button_group]');
                }
            });
        },
        createControl: function createControl(n, cm) {
            return null;
        },
        getInfo: function getInfo() {
            return {
                longname: "YP Button Group Shortcode",
                author: 'nK',
                authorurl: 'https://nkdev.info/',
                infourl: 'https://nkdev.info/',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('yp_button_group', tinymce.plugins.YPButtonGroup);
})();

/***/ })

/******/ });