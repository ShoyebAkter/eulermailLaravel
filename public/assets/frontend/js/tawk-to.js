"use strict";
(function (global) {
  global.$_Tawk_AccountKey = '65617f23da19b36217909aae';
  global.$_Tawk_WidgetId = '1hg2dh96j';
  global.$_Tawk_Unstable = false;
  global.$_Tawk = global.$_Tawk || {};
  (function (w) {
    function l() {
      if (window.$_Tawk.init !== undefined) {
        return;
      }

      window.$_Tawk.init = true;

      var files = [
        'https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-main.js',
        'https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-vendor.js',
        'https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-chunk-vendors.js',
        'https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-chunk-common.js',
        'https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-runtime.js',
        'https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-app.js'
      ];

      if (typeof Promise === 'undefined') {
        files.unshift('https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-promise-polyfill.js');
      }

      if (typeof Symbol === 'undefined' || typeof Symbol.iterator === 'undefined') {
        files.unshift('https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-iterator-polyfill.js');
      }

      if (typeof Object.entries === 'undefined') {
        files.unshift('https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-entries-polyfill.js');
      }

      if (!window.crypto) {
        window.crypto = window.msCrypto;
      }

      if (typeof Event !== 'function') {
        files.unshift('https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-event-polyfill.js');
      }

      if (!Object.values) {
        files.unshift('https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-object-values-polyfill.js');
      }

      if (typeof Array.prototype.find === 'undefined') {
        files.unshift('https://embed.tawk.to/_s/v4/app/660147be42c/js/twk-arr-find-polyfill.js');
      }

      var s0 = document.getElementsByTagName('script')[0];

      for (var i = 0; i < files.length; i++) {
        var s1 = document.createElement('script');
        s1.src = files[i];
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
      }
    }
    if (document.readyState === 'complete') {
      l();
    } else if (w.attachEvent) {
      w.attachEvent('onload', l);
    } else {
      w.addEventListener('load', l, false);
    }
  })(window);

})(window);
