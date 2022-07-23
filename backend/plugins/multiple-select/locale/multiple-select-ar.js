(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(require('jquery')) :
    typeof define === 'function' && define.amd ? define(['jquery'], factory) :
      (global = global || self, factory(global.jQuery));
}(this, function ($) {
  'use strict';

  $ = $ && $.hasOwnProperty('default') ? $['default'] : $;

  /**
   * Multiple Select ar translation
   * Author: Zhixin Wen<wenzhixin2010@gmail.com>
   */

  $.fn.multipleSelect.locales['ar'] = {
    formatSelectAll: function formatSelectAll() {
      return '[تحديد الكل]';
    },
    formatAllSelected: function formatAllSelected() {
      return 'جميع الاختيارات';
    },
    formatCountSelected: function formatCountSelected(count, total) {
      return count + ' من ' + total + ' المحدد';
    },
    formatNoMatchesFound: function formatNoMatchesFound() {
      return 'لم يتم العثور علي اي تطابق';
    }
  };
  $.extend($.fn.multipleSelect.defaults, $.fn.multipleSelect.locales['ar']);

}));
