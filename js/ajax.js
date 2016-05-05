function ajaxObj(method, url) {
   var x = new XMLHttpRequest();
   x.open(method, url, true);
   x.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   return x;
}

function ajaxReturn(x) {
   if (x.readyState == 4 && x.status == 200) {
      return true;
   }
}

// READY STATES:
// 0 = UNSENT (request that has not called open() yet)
// 1 = OPENED (opened but not sent)
// 2 = HEADERS_RECEIVED (headers and status received)
// 3 = LOADING (downloading responseText)
// 4 = DONE (data transmission completed)
