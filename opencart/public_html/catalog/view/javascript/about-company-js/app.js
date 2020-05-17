"use strict";
$(document).ready(function () {

  (function e(t, n, r) {
    function s(o, u) {
      if (!n[o]) {
        if (!t[o]) {
          var a = typeof require == "function" && require;
          if (!u && a) return a(o, !0);
          if (i) return i(o, !0);
          throw new Error("Cannot find module '" + o + "'");
        }

        var f = n[o] = {
          exports: {}
        };
        t[o][0].call(f.exports, function (e) {
          var n = t[o][1][e];
          return s(n ? n : e);
        }, f, f.exports, e, t, n, r);
      }

      return n[o].exports;
    }

    var i = typeof require == "function" && require;

    for (var o = 0; o < r.length; o++) {
      s(r[o]);
    }

    return s;
  })({
    1: [function (require, module, exports) {
      /**
       * Различные утилиты, геттеры и т.д.
       */
      var ww = $(window).width(),
          wh = window.innerHeight ? window.innerHeight : $(window).height(),
          $body = $('body'),
          $pageWrap = $body.find('.page-wrap');
      module.exports = {
        isTouch: function isTouch() {
          var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');

          var mq = function mq(query) {
            return window.matchMedia(query).matches;
          };

          if ('ontouchstart' in window || window.DocumentTouch && document instanceof DocumentTouch) {
            return true;
          } // include the 'heartz' as a way to have a non matching MQ to help terminate the join
          // https://git.io/vznFH


          var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
          return mq(query);
        },
        isIe: function isIe() {
          return navigator.userAgent.match(/Trident\/7.0/i);
        },
        isSafari: function isSafari() {
          return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        },
        refreshCustomVH: function refreshCustomVH() {
          var vh = window.innerHeight * 0.01;
          document.documentElement.style.setProperty('--vh', "".concat(vh, "px"));
        },
        getScrollbarWidth: function getScrollbarWidth() {
          var documentWidth = parseInt(document.documentElement.clientWidth),
              windowsWidth = parseInt(window.innerWidth);
          return windowsWidth - documentWidth;
        },
        getWindowWidth: function getWindowWidth() {
          return ww;
        },
        getWindowHeight: function getWindowHeight() {
          return wh;
        },
        getBody: function getBody() {
          return $body;
        },
        getPageWrap: function getPageWrap() {
          return $pageWrap;
        }
      };
    }, {}],
    2: [function (require, module, exports) {
      // Константы
      var BIG_WIDTH = 1199;
      var MEDIUM_WIDTH = 767;
      var SMALL_WIDTH = 375; // Убери лишние 2 слеша чтобы подключить модуль
      // $.fn.accordion = require('./components/accordion.js');
      // $.fn.floatHeader = require('./components/float_header.js');
      // $.fn.modal = require('./components/modal.js');
      // $.fn.inputFile = require('./components/inputFile.js');
      // $.fn.tabs = require('./components/tabs.js');

      var util = require('./base/util.js');

      var ww = util.getWindowWidth(),
          wh = util.getWindowHeight(),
          $body = util.getBody(),
          $pageWrap = util.getPageWrap(),
          app = {
            executeModules: function executeModules(modulesList) {
              for (var i = 0; i < modulesList.length; i++) {
                if (typeof modulesList[i] === 'function') {
                  try {
                    modulesList[i]();
                  } catch (e) {
                    if (util.isTouch()) {
                      $body.append($('<span class="debug-message">Error occured in module ' + modulesList[i].name + ' with text: ' + e.message + '</span>'));
                    }

                    console.log('Error occured in module ' + modulesList[i].name + ' with text: ' + e.message);
                  }
                }
              }
            },
            initSlick: function initSlick() {
              // Slick section
              if ($.fn.slick != undefined) {
                var sliders = {
                  /*mainTeamSlider: {
                    infinite: false,
                    arrows: false,
                    dots: false,
                    fade: true,
                    onInit: function onInit(e, slick) {
                      var $me = $(this),
                          $nav = $('<div class="main-team__nav"></div>');
                      slick.$slides.each(function (i, v) {
                        $nav.append($('<img src="' + $(v).data('miniature') + '" data-target="' + i + '" class="' + (i == 0 ? 'active' : '') + '" />'));
                      });
                      var $allNavs = $nav.find('img');
                      $allNavs.click(function (e) {
                        $allNavs.removeClass('active');
                        $(this).addClass('active');
                        slick.goTo($(this).data('target'));
                      });
                      $me.after($nav);
                    }
                  },*/
                  mainHistoryNav: {
                    infinite: false,
                    arrows: true,
                    dots: false,
                    asNavFor: '#mainHistorySlider',
                    slidesToScroll: 1,
                    focusOnSelect: true,
                    nextArrow: '<button class="btn btn-outline-info slick-next" type="button"><i class="fas fa-chevron-right"></i></button>',
                    prevArrow: '<button class="btn btn-outline-info slick-prev" type="button"><i class="fas fa-chevron-left"></i></button>',
                    draggable: false,
                    swipe: false
                  },
                  mainHistorySlider: {
                    infinite: false,
                    arrows: false,
                    dots: false,
                    asNavFor: '#mainHistoryNav',
                    slidesToScroll: 1,
                    variableWidth: true
                  },
                  trophySlider: {
                    infinite: false,
                    dots: true,
                    arrows: true,
                    nextArrow: '<button class="btn btn-outline-info slick-next" type="button"><i class="fas fa-chevron-right"></i></button>',
                    prevArrow: '<button class="btn btn-outline-info slick-prev" type="button"><i class="fas fa-chevron-left"></i></button>',
                    mobileFirst: true,
                    variableWidth: true,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    swipeToSlide: true,
                    responsive: [{
                      breakpoint: 540,
                      settings: {
                        slidesToShow: 4
                      }
                    }, {
                      breakpoint: 1200,
                      settings: {
                        slidesToShow: 5
                      }
                    }]
                  }
                };
                $('[data-settslick]').each(function (i, v) {
                  var $sld = $(v),
                      sett = sliders[$sld.data('settslick')]; // Для добавления в слайдер счётчика слайдов.

                  if ($sld.hasClass('slick-countered')) {
                    $sld.on('init', function (e, slick) {
                      $(this).append('<span class="slick-counter">' + (slick.currentSlide + 1 + ' / ' + slick.$slides.length) + '</span>');
                    }).on('beforeChange', function (e, slick, curr, next) {
                      $(this).find('.slick-counter').html(next + 1 + ' / ' + slick.$slides.length);
                    });
                  }

                  if (sett !== undefined) {
                    if (sett.onInit !== undefined) {
                      $sld.on('init', sett.onInit);
                    }

                    if (sett.forwardOnClick) {
                      $sld.on('init', function (e, slick) {
                        $sld.find('.slick-list').click(function (e) {
                          slick.slickNext();
                        });
                      });
                    }

                    $sld.slick(sett);
                  }
                });
              }
            },
            initRellax: function initRellax() {
              if (Rellax) {
                var rellax = new Rellax('.rellax');
              }
            },
            initOthers: function initOthers() {
              // Кастомный vh
              util.refreshCustomVH();
              window.addEventListener('resize', function () {
                // We execute the same script as before
                util.refreshCustomVH();
              });
              document.documentElement.style.setProperty('--scrollbarWidth', util.getScrollbarWidth() + 'px');
            },
            // initHeader: function () {
            //     $('#menuBurger').click(function (e) {
            //         var has = $('#pageHeader').toggleClass('expanded').hasClass('expanded');
            //         if (has) {
            //             $('body').addClass('cancel-scroll');
            //         } else {
            //             $('body').removeClass('cancel-scroll');
            //         }
            //         e.preventDefault();
            //     });
            //     if ($.fn.floatHeader) $('.page-header').floatHeader({ offset: 0 });
            // },
            removeBodyPreload: function removeBodyPreload() {
              $body.removeClass('preload');
            },
            addBodyPrefixes: function addBodyPrefixes() {
              var prefixes = [];
              if (util.isTouch()) prefixes.push('touch-device');
              if (util.isIe()) prefixes.push('is-ie');
              if (util.isSafari()) prefixes.push('is-safari');
              $body.addClass(prefixes.join(' '));
            },
            initMaskedInput: function initMaskedInput() {
              if ($.fn.mask != undefined) $('input[type=tel]').mask('+7 (999) 999-99-99');
            },
            // initPopups: function () {
            //     if ($.fn.modal != undefined) $('[data-modal]').modal('bindData');
            // },
            // initAccordions: function () {
            //     if ($.fn.accordion != undefined) $('.accordion').accordion();
            // },
            // initTabs: function () {
            //     if ($.fn.tabs != undefined) $('.tabs').tabs();
            // },
            // initTippy: function () {
            //     if (tippy != undefined) {
            //         tippy('[data-tippy-content]', { theme: "light", placement: 'bottom' });
            //         $('[data-tippy-target]').each(function (i, v) {
            //             tippy(v, { theme: "light", placement: 'bottom', content: $($(v).data('tippy-target')).get(0) });
            //         });
            //     }
            // },
            initFancyBox: function initFancyBox() {
              if ($.fn.fancybox) $('[data-fancybox]').fancybox({
                aspectRatio: true,
                protect: true,
                autoSize: false,
                autoScale: false,
                autoDimensions: false
              });
            },
            // initFieldsCustomFocus: function(){
            //     var $inputs = $('input, textarea');
            //     $inputs.focus(function (e) {
            //         $(this).addClass('focused');
            //     }).blur(function (e) {
            //         if ($(this).val().length == 0) {
            //             $(this).removeClass('focused');
            //         }
            //     });
            //
            //     $inputs.each(function (i, v) {
            //         var $me = $(v);
            //         if ($me.val().length > 0) {
            //             $me.addClass('focused');
            //         }
            //     });
            // },
            initSmoothScroll: function initSmoothScroll() {
              $('.smooth').click(function (e) {
                var $me = $(this),
                    $targ = $($me.attr('href')),
                    offset = ww > MEDIUM_WIDTH ? 80 : 30;

                if ($targ.length) {
                  $('html, body').animate({
                    scrollTop: $targ.offset().top - offset
                  }, 1000);
                }

                e.preventDefault();
              });
            }
          };
      $(function () {
        try {
          app.executeModules([app.initSlick, app.initOthers, //app.initHeader,
            // app.initMaskedInput,
            // app.initPopups,
            // app.initTabs,
            // app.initSmoothScroll,
            // app.initTippy,
            // app.initAccordions,
            // app.initFancyBox,
            app.addBodyPrefixes]);
        } catch (e) {
          console.log('Произошла ошибка в файле app.js: ' + e.message);
        } finally {
          app.removeBodyPreload();
        }
      });

      function CommonForm($scope, $http, url, callback) {
        return function () {
          $scope.submitProcess = true;
          $http.post(url, $scope.model).then(function (resp) {
            $scope.resp = resp.data;
            $scope.submitProcess = false;

            if (callback) {
              callback();
            }
          });
        };
      }
    }, {
      "./base/util.js": 1
    }]
  }, {}, [2]);
})