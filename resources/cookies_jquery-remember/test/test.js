$(function(){

  $.remember({ name: 'test1', value: true});
  console.log('1 (expect true) : ' + $.remember({ name: 'test1' }));

  $.remember({ name: 'test2', value: true, use: 'localstorage' });
  console.log('2 (expect true) : ' + $.remember({ name: 'test2', use: 'localstorage' }));

  $.remember({ name: 'test3', value: true, use: 'cookie' });
  console.log('3 (expect true): ' + $.remember({ name: 'test3', use: 'cookie' }));

  $.remember({ name: 'test4', value: true, use: 'cookie' });
  console.log('4 (expect true) : ' + $.remember({ name: 'test4', use: 'localstorage' }));

  $.remember({ name: 'test5', value: true, use: 'localstorage' });
  console.log('5 (expect true) : ' + $.remember({ name: 'test5', use: 'cookie' }));

  $.remember({ name: 'test6', value: true, use: 'localstorage' });
  console.log('6 (expect null) : ' + $.remember({ name: 'test6', use: 'cookie', fallback: false }));

  $.remember({ name: 'test7', value: true, use: 'cookie' });
  console.log('7 (expect null) : ' + $.remember({ name: 'test7', use: 'localstorage', fallback: false }));

  $.remember({ name: 'test8', value: true, getSet: true });
  console.log('8 (expect true) : ' + $.remember({ name: 'test8', value: false, getSet: true }));

  $.remember({ name: 'test9', value: true });
  console.log('9 (expect undefined) : ' + $.remember({ name: 'test9', remove: true }));

  $.remember({ name: 'test10', value: true, use: 'cookie' });
  console.log('10 (expect undefined) : ' + $.remember({ name: 'test10', remove: true, use: 'cookie' }));

  $.remember({ name: 'test11', value: true, use: 'cookie' });
  console.log('11 (expect undefined) : ' + $.remember({ name: 'test11', remove: true, use: 'localstorage' }));

  $.remember({ name: 'test12', value: true, use: 'cookie' });
  $.remember({ name: 'test12', remove: true, use: 'localstorage', fallback: false });
  console.log('12 (expect true) : ' + $.remember({ name: 'test12' }));

  $.remember({ name: 'test13', value: true, expires: 10 * 365 });
  // 13 check expire date

  $.remember({ name: 'test14', value: true, path: '/404' });
  // go to 0.0.0.0:8000/404 and cookie will be set

  $.remember({ name: 'test15', value: '<div class="test></div>', use: 'cookie', json: true });
  console.log('15 (expect <div class="test"></div> + check json cookie) : ' + $.remember({ name: 'test15', use: 'cookie', json: true }));

  $.remember({ name: 'test16', value: 'https://github.com/ndreckshage/jquery-remember', use: 'cookie' });
  console.log('16 (expect https://github.com/ndreckshage/jquery-remember + check cookie) : ' + $.remember({ name: 'test16', use: 'cookie' }));

  $.remember({ name: 'test17', value: 'https://github.com/ndreckshage/jquery-remember', use: 'cookie', raw: true });
  console.log('17 (expect https://github.com/ndreckshage/jquery-remember + check cookie) : ' + $.remember({ name: 'test17', use: 'cookie', raw: true }));

  $.remember({ name: 'test18', value: 'https://github.com/ndreckshage/jquery-remember', use: 'cookie' });
  console.log('18 (expect https%3A%2F%2Fgithub.com%2Fndreckshage%2Fjquery-remember) : ' + $.remember({ name: 'test18', use: 'cookie', raw: true }));

});