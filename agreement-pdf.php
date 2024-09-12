<?php

include '../../../wp-load.php';
$ot_id = $_GET['id'];
$ot = Mopar::getOneOt($ot_id);

if (!$ot) mopar_contract_pdf_show_message('danger', 'Error: contract not found');
else if (user_can($current_user, 'administrator')) {
	$current_user = wp_get_current_user();
	include plugin_dir_path(__FILE__) . 'pdf/contract.php';

	$html2pdf = mopar_generate_contract_pdf($ot_id, true);
	$titulo_pdf = 'Agreement';
	$html2pdf->output($titulo_pdf . '_000' . $ot_id . '.pdf');
} else mopar_contract_pdf_show_message('danger', 'error: unauthorized access');

function mopar_contract_pdf_show_message($type, $message)
{
	exit("
		<link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
		<div class=\"alert alert-{$type} text-center m-5\" role=\"alert\">{$message}</div>
	");
}