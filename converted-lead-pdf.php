<?php  

require __DIR__.'/vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;
include '../../../wp-load.php';

$current_user = wp_get_current_user();
if (user_can( $current_user, 'administrator' )) {

	$solicitud = Mopar::getOneSolicitud($_GET['id']);
	$vehiculo = Mopar::getOneVehiculo($solicitud->vehiculo_id);
	$cliente = Mopar::getOneCliente($vehiculo->cliente_id);

	$html = '
	<!--
	<style>
	table{
		border-collapse: collapse;
	}
	table td{
		padding: 10px;
	}
	table.no_padding td{
		padding: 0px 3px;
	}
	</style>
	-->
	<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm"> 
	<table style="width: 590px;">
		<tr>
			<td style="width: 295px;">
			</td>
			<td style="width: 295px; text-align: center">
				<h3 style="margin-bottom: 10px">FHS CONSTRUCTION INC</h3>
			</td>
		</tr>
	</table>

	<table style="width: 590px;">
		<tr>
			<td style="width: 590px;">
				<h1 style="text-align: center">Converted Lead n&deg;000'.$solicitud->id.'</h1>
			</td>
		</tr>
	</table>

	<table style="width: 590px;">
		<tr>
			<td style="width: 295px; border: 1px solid #000;">
				<table class="no_padding">
					<tr><td><strong>Name: </strong></td><td>' . $cliente->nombres . ' ' . $cliente->apellidoPaterno . '</td></tr>
					<tr><td><strong>Email: </strong></td><td>' . $cliente->email . '</td></tr>
					<tr><td><strong>Phone: </strong></td><td>' . $cliente->telefono . '</td></tr>
				</table>
			</td>
			<td style="width: 295px; border: 1px solid #000;">
				<table class="no_padding">
					<tr>
						<td><strong>Address: </strong></td>
						<td>' . $vehiculo->street_address . '</td>
					</tr>
					<tr>
						<td></td>
						<td>' . $vehiculo->address_line_2 . '</td>
					</tr>
					<tr>
						<td><strong>City: </strong></td>
						<td>' . $vehiculo->city . '</td>
					</tr>
					<tr>
						<td><strong>State: </strong></td>
						<td>' . $vehiculo->state . '</td>
					</tr>
					<tr>
						<td><strong>ZIP Code: </strong></td>
						<td>' . $vehiculo->zip_code . '</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br><br><br>
	<table border="1">
		<tr>
			<td style="width: 635px; text-align: center;"> <strong>Request</strong> </td>
		</tr>';

		$lastupdated = is_null($solicitud->upddate) ? '-' : date_format(date_create($solicitud->upddate), 'm/d/Y - H:i');
		$motivo = '' === $solicitud->motivo ? '' : '<tr><td><strong>Reason:</strong> '.$solicitud->motivo.'</td></tr>';
		$fecha = is_null($solicitud->fecha) ? '' : '<tr><td><strong>Scheduled Date:</strong> '.date_format(date_create("{$solicitud->fecha} {$solicitud->hora}"), 'm/d/Y H:i') .'</td></tr>';

		$html .= '
		<tr>
			<td style="width: 635px; text-align: justify; white-space:pre-wrap"><strong>'. $solicitud->solicitud .'</strong></td>
		</tr>
	</table>
	<br><br><br>
	<table border="1">
		<tr>
			<td style="width: 635px; text-align: center;"> <strong>Details</strong> </td>
		</tr>';
		$html .= '
		<tr>
			<td style="width: 635px; text-align: justify; white-space:pre-wrap"><strong>'. $solicitud->details .'</strong></td>
		</tr>
	</table>';

	$html .= '<br><br><br>';
	foreach (json_decode($solicitud->photos) as $photo) {
	    $photo = str_replace(' ', '%20', $photo);
		$src = plugin_dir_url(__FILE__) . 'uploads/' . $photo;
		$html .= '<img style="width: 220px" src="' . $src . '"> &nbsp;';
	}

	$html .= '<br>
	<table border="0" style="width: 590px">
		<tr>
			<td>
				<strong>Created:</strong> '.date_format(date_create($solicitud->regdate), 'm/d/Y - H:i').'
				<br>
				<strong>Modified:</strong> '. $lastupdated .'
			</td>
		</tr>
		'.$motivo.'
		'.$fecha.'
	</table>
	</page>';

	$page_2 = '
	<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm">
	    
	    <div style="text-align:center">
	    </div>
	    <br><br>
	    
	    <br><br><br>
	    <br><br>
	    <table style="width: 590px;">
	        <tr>
	            <td style="width: 196px; text-align: center;">
	                <hr>
	                DOCTOR MOPAR
	            </td>
	            <td style="width: 196px; text-align: center;">&nbsp;</td>
	            <td style="width: 196px; text-align: center;">
	                <hr>
	                '. $cliente->nombres . ' ' . $cliente->apellidoPaterno .'<br>
	                (Cliente)
	            </td>
	        </tr>
	    </table>
	</page>
	';
    if (2 == $solicitud->estado) $html .= $page_2;

	$orientation = 'potrait';
	$titulo_pdf = 'solicitud';
	$html2pdf = new Html2Pdf($orientation,'LETTER','es');
	$html2pdf->writeHTML($html);
	$html2pdf->output( $titulo_pdf . '_000'. $solicitud->id .'.pdf');
}

?>