<?php

add_action('init', function () {
	$url = explode('/', $_SERVER['REQUEST_URI']);
	$page = $url[1];
	if (!$page || 'estimates' != $page) return true;

    $titulo_pdf = 'Estimate';
	$ot_id = $url[2];
    include plugin_dir_path(__FILE__) . 'pdf/estimate.php';

    exit;
});