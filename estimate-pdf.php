<?php

require __DIR__ . '/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

include '../../../wp-load.php';

$current_user = wp_get_current_user();
if (user_can($current_user, 'administrator')) {
	$titulo_pdf = 'Estimate';
    $ot_id = $_GET['id'];
	include plugin_dir_path(__FILE__) . 'pdf/estimate.php';

	$orientation = 'potrait';
	$html2pdf = new Html2Pdf($orientation,'LETTER','es');
	$html2pdf->writeHTML($html);
	$html2pdf->output( $titulo_pdf . '_000'. $ot_id .'.pdf');
}
