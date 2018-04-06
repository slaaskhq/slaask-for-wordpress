<?php
/**
 * Created by PhpStorm.
 * User: Madjack
 * Date: 23.05.2016
 * Time: 14:17
 */

header("Content-type: text/javascript");

if($api_key = isset($_GET['api_key']) ? $_GET['api_key'] : false){
    echo '_slaask.init("' . $api_key . '");';
}
