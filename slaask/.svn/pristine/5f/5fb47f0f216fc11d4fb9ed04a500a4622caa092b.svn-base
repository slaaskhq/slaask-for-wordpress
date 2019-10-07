<?php
header("Content-type: text/javascript");

if($api_key = isset($_GET['api_key']) ? $_GET['api_key'] : false){
  echo 'window._slaaskSettings = {
    key: "' . $_GET['api_key'] . '",
    options: {
      source: "wordpress"
    }
  };';
}
