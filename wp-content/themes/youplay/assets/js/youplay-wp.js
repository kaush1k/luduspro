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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(7);


/***/ }),

/***/ 7:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


!function ($) {
  var $window = $(window);

  // fix navbar position if admin bar showed
  $(function () {
    var $adminBar = $('#wpadminbar');
    var $navbar = $('.navbar-youplay');
    var scrollTimeout;
    if ($adminBar.length && $navbar.length) {
      $window.on('scroll resize load', function () {
        var rect = $adminBar[0].getBoundingClientRect();
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function () {
          $navbar[0].style.setProperty('top', Math.max(0, rect.top + rect.height) + 'px', 'important');
        }, 200);
      });
    }
  });

  // fix widget area rss
  $('.widget_rss cite, .widget_rss .rss-date').addClass('date');

  // fix form selects and inputs
  // for sidebar widgets and rtmedia
  $('.side-block, .rtmedia-container').find('select').each(function () {
    var $this = $(this);
    if (!$this.parent('.youplay-select').length) {
      $this.wrap('<div class="youplay-select">');
    }
  });
  $('.side-block, .rtmedia-container').find('input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=range])').each(function () {
    var $this = $(this);
    if (!$this.parent('.youplay-input').length) {
      $this.wrap('<div class="youplay-input">');
    }
  });
  $('.side-block, .rtmedia-container').find('textarea').each(function () {
    var $this = $(this);
    if (!$this.parent('.youplay-textarea').length) {
      $this.wrap('<div class="youplay-textarea">');
    }
  });
  $('.side-block, .rtmedia-container').find('input[type=button], input[type=submit]:not([name="rtmedia-upload"])').each(function () {
    var $this = $(this);
    if (!$this.parent('.btn').length) {
      $this.wrap('<span class="btn">' + $this.val() + '</span>');
    }
  });
  $('.side-block, .rtmedia-container').find('button:not(.btn)').each(function () {
    $(this).addClass('btn');
  });

  // update navbar cart products count and subtotal
  var $cart = $('.navbar-youplay .dropdown-cart');
  $(document.body).on('added_to_cart wc_fragments_loaded wc_fragments_refreshed', function (e, a) {
    var $count = $cart.find('.nav_products_count');
    var $subtotal = $cart.find('.nav_products_subtotal');

    var count = $cart.find('[data-cart-count]').attr('data-cart-count') || '';
    var subtotal = $cart.find('[data-cart-subtotal]').attr('data-cart-subtotal') || '';

    $count[count ? 'show' : 'hide']();
    $count.html(count);

    $subtotal[subtotal ? 'show' : 'hide']();
    $subtotal.html(subtotal);
  });

  /* fix BuddyPress alerts */
  $('.buddypress .bp-template-notice, .buddypress #message').each(function () {
    var $alert = $(this);
    $alert.addClass('alert');
    $alert.attr('id', '');

    if ($alert.hasClass('updated')) {
      $alert.addClass('alert-success');
      $alert.removeClass('updated');
    } else if ($alert.hasClass('warning')) {
      $alert.addClass('alert-warning');
      $alert.removeClass('warning');
    } else if ($alert.hasClass('error')) {
      $alert.addClass('alert-danger');
      $alert.removeClass('error');
    } else if ($alert.hasClass('info')) {
      $alert.addClass('alert-info');
      $alert.removeClass('info');
    }

    $alert.children('p').addClass('m-0');
  });

  /* Change Woocommerce product image on banner */
  $(".variations_form").on("show_variation", function (event, variation) {
    var $banner = $(this).parents('.youplay-banner:eq(0)').children('.image');

    // save default styles
    if (!$banner.attr('data-style')) {
      $banner.attr('data-style', $banner.attr('style'));
    }

    // change image
    if (variation && variation.image && variation.image.full_src) {
      $banner.css({
        'background-image': 'url("' + variation.image.full_src + '")'
      });
    } else {
      reset_woo_image(this);
    }
  });
  function reset_woo_image(form) {
    var $banner = $(form).parents('.youplay-banner:eq(0)').children('.image');

    // reset default styles
    if ($banner.attr('data-style')) {
      $banner.attr('style', $banner.attr('data-style'));
    }
  }
  $(".variations_form").on("reset_image", reset_woo_image);

  /* Change input html in WooCommerce checkout */
  $(document.body).on('country_to_state_changed', function (event, country, wrapper) {
    var statebox = wrapper.find('#billing_state, #shipping_state, #calc_shipping_state');

    statebox.each(function () {
      var $this = $(this);
      if ($this.is('input')) {
        if (!$this.parent().hasClass('youplay-input')) {
          statebox.wrap('<div class="youplay-input">');
        }
      } else {
        if ($this.parent().hasClass('youplay-input')) {
          statebox.unwrap();
        }
      }
    });
  });

  /* WooCommerce prevent review without rating */
  $('body').on('click', '#respond #submit', function () {
    if (typeof wc_single_product_params === 'undefined') {
      return;
    }

    var $rating = $(this).closest('#respond').find('.youplay-rating');
    var $form = $(this).closest('form');
    var formData = $form.serializeArray();
    var rating = false;

    for (var k = 0; k < formData.length; k++) {
      if (formData[k].name === 'rating') {
        rating = formData[k].value;
        break;
      }
    }

    if ($rating.length > 0 && !rating && wc_single_product_params.review_rating_required === 'yes') {
      window.alert(wc_single_product_params.i18n_required_rating_text);

      return false;
    }
  });
}(jQuery);

/***/ })

/******/ });