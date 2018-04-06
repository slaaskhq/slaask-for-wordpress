<?php
header("Content-type: text/javascript");

if($api_key = isset($_GET['api_key']) ? $_GET['api_key'] : false){
    echo '(function() {
  var slk = document.createElement("script");
  slk.src = "https://cdn.slaask.com/chat.js";
  slk.type = "text/javascript";
  slk.async = "true";
  slk.onload = slk.onreadystatechange = function() {
    var rs = this.readyState;
    if (rs && rs != "complete" && rs != "loaded") return;
    try {
      _slaask.init("' . $api_key . '", { source: "wordpress" });
    } catch (e) {}
  };
  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(slk, s);
})();';
}
