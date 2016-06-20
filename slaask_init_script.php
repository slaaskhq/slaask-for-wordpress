<?php

/**
 * Created by PhpStorm.
 * User: Madjack
 * Date: 23.05.2016
 * Time: 14:17
 */
header("Content-type: text/javascript");
include_once '../../../wp-config.php';

$slaask_options = get_option('slaask_options');

// If no api key, we can't access slaask
if (!(isset($slaask_options['api_key']) && $slaask_options['api_key'] != '')) {
    exit();
}

// get the current user
$current_user = wp_get_current_user();

// if identification is activated and all fields filled 
if (isset($slaask_options['enable_identification']) && $slaask_options['enable_identification'] == 1 && isset($slaask_options['identification_fields']) && count($slaask_options['identification_fields']) > 0 && $current_user) {

    // get user's data
    $data = [];
    foreach ($slaask_options['identification_fields'] as $field) {
	$data[] = ( "'$field' : '" . $current_user->data->$field . "'");
    }

    // add the identify part 
    echo "_slaask.identify('" . $current_user->data->display_name . "', {" . join(',', $data) . "});";
}

// init slaask 
echo '_slaask.init("' . $slaask_options['api_key'] . '");';

