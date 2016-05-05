function expand(element) {
   var target = document.getElementById(element);
   var h = target.offsetHeight;
   var sh = target.scrollHeight;
   var loopTimer = setTimeout('expand(\''+element+'\')',0);
   if (h < sh) {
      h += 5;
   } else {
      clearTimeout(loopTimer);
   }
   target.style.height = h+"px";
}

function retract(element) {
   var target = document.getElementById(element);
   var h = target.offsetHeight;
   var loopTimer = setTimeout('retract(\''+element+'\')',0);
   if (h > 0) {
      h -= 5;
   } else {
      target.style.height = "0px";
      clearTimeout(loopTimer);
   }
   target.style.height = h+"px";
}
