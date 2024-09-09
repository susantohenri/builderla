<?php

include '../../../wp-load.php';
$ot_id = $_GET['id'];
$ot = Mopar::getOneOt($ot_id);

if (!$ot) mopar_contract_pdf_show_message('danger', 'Error: contract not found');
else if (isset($_GET['sign_contract'])) {
	if ('' != $ot->client_signature) mopar_contract_pdf_show_message('danger', 'Error: contract already signed');
	else if (isset($_POST['sign_contract'])) {
		global $wpdb;
		$wpdb->update('ot', [
			'client_signature' => $_POST['client_signature'],
			'client_initial' => $_POST['client_initial'],
			'signed_date' => date_format(date_create($_POST['signed_date']), 'Y-m-d'),
			'client_dob' => date_format(date_create($_POST['client_dob']), 'Y-m-d'),
		], [
			'id' => $ot_id
		]);
		$recipient = $wpdb->get_row("
			SELECT
				ot.id
				, clientes.email
				, clientes.nombres
				, vehiculos.street_address
				, vehiculos.address_line_2
				, vehiculos.city
				, vehiculos.zip_code
			FROM ot
			LEFT JOIN vehiculos ON ot.vehiculo_id = vehiculos.id
			LEFT JOIN clientes ON vehiculos.cliente_id = clientes.id
			WHERE ot.id = {$ot_id}
		");
		wp_schedule_single_event(time() + 1, 'async_send_signed_contract', [$recipient]);
		mopar_contract_pdf_show_message('success', 'Success: signed contract sent');
	} else include plugin_dir_path(__FILE__) . 'sign-contract/sign-contract.php';
} else if (user_can($current_user, 'administrator')) {
	$current_user = wp_get_current_user();
	include plugin_dir_path(__FILE__) . 'pdf/contract.php';

	$html2pdf = mopar_generate_contract_pdf($ot_id);
	$titulo_pdf = 'Contract';
	$html2pdf->output($titulo_pdf . '_000' . $ot_id . '.pdf');
} else mopar_contract_pdf_show_message('danger', 'error: unauthorized access');

function mopar_contract_pdf_show_message($type, $message)
{
	exit("
		<link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
		<div class=\"alert alert-{$type} text-center m-5\" role=\"alert\">{$message}</div>
	");
}

add_action('async_send_signed_contract', function ($recipient) {
	Mopar::sendMail($recipient, 'send_signed_contract');
});
