# jquery.remember
*a fork of [jquery.cookie](https://github.com/carhartl/jquery-cookie)*

Remember stuff. A simple way to set/read/destroy cookies and localstorage.

## Differences between $.cookie...

+ $.remember instead of $.cookie...
+ call using settings object. $.remember({ name: 'name', value: 'value' }) instead of params $.cookie('name', 'value')
+ sets localstorage first and falls back to cookies (browser capabilities). unless specified $.remember({ name: 'test', use: 'cookie' })
+ new options like get and set (if not set already...) $.remember({ name: 'name', value: 'value', getSet: true })
+ gets rid of amd settings from $.cookie (didn't work great for me). now you can include in require and whatever name as namespaced argument
+ complete code restructure
+ tried to preserve all of the features of $.cookie

## Full Options

```javascript
settings = $.extend({
    name: null,         // name/key of the cookie/localstorage item
    value: undefined,   // value pair of cookie/localstorage
    getSet: false,      // if true, will get if available, set if not. default is to just get OR set
    remove: false,      // if true, will remove based on name/key
    use: 'default',     // whether to use localstorage or cookies. default localstorage with cookie fallback.
    expires: null,      // forces cookie (invalid localstorage attribue).
    path: null,         // forces cookie.
    domain: null,       // forces cookie.
    secure: null,       // forces cookie.
    json: false,        // will convert to json when set. parse with get.
    fallback: true,     // whether to fallback to cookies if localstorage not available.
    raw: false,         // if true, will skip uri encoding/decoding
    modernizr: false    // set true if youd rather handle localstorage detection through modernizr
}, options);
```

## Installation

Include script *after* the jQuery library (unless you are packaging scripts somehow else -- minify at your convenience):
    
    <script src="/path/to/jquery.remember.js"></script>

## Usage

Create localstorage item (localstorage default, can be overwritten/specified)

    $.remember({ name: 'name', value: 'value' })

Create session cookie:

    $.remember({ name: 'name', value: 'value', use: 'cookie' });

Create expiring cookie lasting 20 years (adding cookie specific options -- expires/path/secure/domain -- forces cookie:

    $.remember({ name: 'name', value: 'value', expires: 365 * 20 });

Create localstore item (disallow cookie fallback):

    $.remember({ name: 'name', value: 'value', fallback: false });

Read (will automatically fallback + check the other for named pair, unless specified):

    $.remember({ name: 'name' });
    $.remember({ name: 'name', use: 'cookie', fallback: false });
    $.remember({ name: 'not_found' }) => null

Get or Set item:

    $.remember({ name: 'name', value: 'value', getSet: true }) => will not set if already exists

Remove cookie/localstorage:

    $.remember({ name: 'name', remove: true });
    $.remember({ name: 'name', use: 'localstorage', fallback: false });

## Configuration

### TO USE WITH REQUIRE.JS

$.cookie required some editing for me. I did not want to use jquery namespace, like $.cookie(), I wanted cookie(). I recommend this setup...

```javascript
define(['jquery'], function($){
  $.remember = function(options){ // (or return function(options{ ... if you want to avoid jquery namespace entirely)
  // ALL CODE, DONT TOUCH (well, unless you want to)
  return $.remember; // remove if you went the return function route...
});

// then in whatever module

require(['jquery', 'jquery.remember'], function($, remember){ // jquery not required here...but figured you probably will have it
});

// now, both these would work
$.remember({ name: 'test' });
remember({ name: 'test' });
```
### modernizr

using modernizr is optional. a simple test is build in to detect browser support, but if you would rather use modernizr local storage, just make sure modernizr is loaded, and then call with

    $.remember({ name: 'test', value: 'value', modernizr: true });

### raw

By default the cookie/localstorage value is encoded/decoded when writing/reading, using `encodeURIComponent`/`decodeURIComponent`. Bypass this by raw to true:

    $.remember({ name: 'name', value: 'value', raw: true });

### json

Turn on automatic storage of JSON objects passed as the cookie value:

    $.remember({ name: 'name', value: 'value', json: true });


## Tests

Run over a server, example:

    $ python -m SimpleHTTPServer

Currently very basic test setup showing functionality, would be great to build this in actual testing framework (similar to $.cookie)

*If you prefer simple cookie management only, please check out the original repo at [jquery.cookie](https://github.com/carhartl/jquery-cookie)*

** Thanks
[carhartl](https://github.com/carhartl) -- original author of $.cookie
