/*! 
 * jQuery Hash Plugin v0.1
 * https://github.com/srasch/jquery.hash
 *
 * Copyright 2012, Steve Rasch
 */
/*jshint bitwise:false */ 

(function ($, window, document, undefined) {
  "use strict";
  $.hash = $.hash || {};

  var HEX_CHARS = "0123456789abcdef";

  /*
   * Convert a 32-bit number to a hex string with ls-byte first
   */
  var numberToHex = function (num) {
    var result = "";
    for (var j = 0; j <= 3; j++) {
      result += HEX_CHARS.charAt((num >> (j * 8 + 4)) & 0x0F) + HEX_CHARS.charAt((num >> (j * 8)) & 0x0F);
    }
    return result;
  };

  /*
   * Convert a string to a sequence of 16-word blocks, stored as an array.
   * Append padding bits and the length, as described in the MD5 standard.
   */
  function stringToBlocks(value) {
    var numberOfBlocks = ((value.length + 8) >> 6) + 1,
      blocks = new Array(numberOfBlocks * 16),
      idx;

    for (idx = 0; idx < numberOfBlocks * 16; idx++) {
      blocks[idx] = 0;
    }
    for (idx = 0; idx < value.length; idx++) {
      blocks[idx >> 2] |= value.charCodeAt(idx) << ((idx % 4) * 8);
    }

    blocks[idx >> 2] |= 0x80 << ((idx % 4) * 8);
    blocks[numberOfBlocks * 16 - 2] = value.length * 8;

    return blocks;
  }

  /*
   * Bitwise rotate a 32-bit number to the left
   */
  function rol(num, cnt) {
    return (num << cnt) | (num >>> (32 - cnt));
  }

  /*
   * Add integers, wrapping at 2^32. This uses 16-bit operations internally 
   * to work around bugs in some JS interpreters.
   */
  function add(x, y) {
    var lsw = (x & 0xFFFF) + (y & 0xFFFF);
    var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
    return (msw << 16) | (lsw & 0xFFFF);
  }

  $.hash.utils = (function () {
    return {
      numberToHex: numberToHex,
      stringToBlocks: stringToBlocks,
      add: add,
      rol: rol
    };
  })();

})(jQuery, window, document);