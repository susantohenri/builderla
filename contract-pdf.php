<?php

add_action('init', function () {
	$url = explode('/', $_SERVER['REQUEST_URI']);
	$page = $url[1];
	if (!$page || !in_array($page, ['contract', 'sign', 'agreement'])) return true;

	$ot_id = $url[2];
	$ot = Mopar::getOneOt($ot_id);
	if (!$ot) mopar_pdf_show_message('danger', 'Error: contract not found');

	if ('sign' == $page) {
		if ('SIGNED' == $ot->contract_status) mopar_pdf_show_message('danger', 'Error: contract already signed');
		else if (isset($_POST['sign_contract'])) {
			global $wpdb;

			$wpdb->update('ot', [
				'client_signature' => $_POST['client_signature'],
				'client_initial' => $_POST['client_initial'],
				'signed_date' => date_format(date_create($_POST['signed_date']), 'Y-m-d'),
				'client_dob' => date_format(date_create($_POST['client_dob']), 'Y-m-d'),
				'contract_status' => 'SIGNED'
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

			wp_schedule_single_event(time() + 1, 'mopar_async', [[
				'action' => 'send_signed_contract_email',
				'recipient' => $recipient
			]]);
			file_get_contents(site_url());// triggering scheduler
			mopar_pdf_show_message('success', 'Success: signed contract sent');
		} else {
			include plugin_dir_path(__FILE__) . 'sign-contract/sign-contract.php';
			exit();
		}
	}

	// if (!user_can(wp_get_current_user(), 'use_builderla')) mopar_pdf_show_message('danger', 'error: unauthorized access');

	include plugin_dir_path(__FILE__) . 'pdf/contract.php';

	if ('contract' == $page) {
		if ('SIGNED' == $ot->contract_status) mopar_pdf_show_message('danger', 'Error: contract already signed');
		$html2pdf = mopar_generate_contract_pdf($ot_id);
		$titulo_pdf = 'Contract';
	}
	if ('agreement' == $page) {
		if ('SIGNED' != $ot->contract_status) mopar_pdf_show_message('danger', 'Error: contract not signed');
		$html2pdf = mopar_generate_contract_pdf($ot_id, true);
		$titulo_pdf = 'Agreement';
	}

	$pdf_file_name = $titulo_pdf . '_000' . $ot_id . '.pdf';
	$html2pdf->output($pdf_file_name);
	exit;
});


function mopar_pdf_show_message($type, $message)
{
	exit("
		<link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
		<div class=\"alert alert-{$type} text-center m-5\" role=\"alert\">{$message}</div>
	");
}
