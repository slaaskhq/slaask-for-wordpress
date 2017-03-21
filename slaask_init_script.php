<?php
/**
 *  Plugin Name:        Slaask
 *  Plugin URI:         https://slaask.com/wordpress
 *  Description:        Your customer service app for Slack. Bring all your team -and client!- communication together in one place.
 *  Version:            1.3
 *  Author:             Slaask Team
 *  Author URI:         https://slaask.com/team
 *  License:            GPL2
 *  License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *  GitHub Plugin URI:  https://github.com/slaaskhq/slaask-for-wordpress
 *  GitHub Branch:      master
 **/

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
      _slaask.init("' . $api_key . '");
    } catch (e) {}
  };
  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(slk, s);
})();';
}
