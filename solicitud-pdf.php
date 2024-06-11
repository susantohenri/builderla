<?php  

include '../../../wp-load.php';

$current_user = wp_get_current_user();
if (user_can( $current_user, 'administrator' )) {

	$solicitud = Mopar::getOneSolicitud($_GET['id']);
	$cliente = Mopar::getOneCliente($solicitud->cliente_id);
	$vehiculo = 0 != $solicitud->vehiculo_id ? Mopar::getOneVehiculo($solicitud->vehiculo_id) : json_decode('{"marca":"","modelo":"","ano":"","color":"","patente":"","nro_motor":""}');
	$title = 1 == $solicitud->estado ? 'Solicitud de servicio' : 'Orden de Ingreso';

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
				<img style="width: 200px; height: auto;" src="https://www.doctormopar.com/wp-content/uploads/2019/02/mopar.png">
			</td>
			<td style="width: 295px; text-align: center">
				<h3 style="margin-bottom: 10px">Taller Doctor Mopar</h3>
				<h4 style="margin: 0; font-weight: lighter">
					Los Cerezos 375, Ñuñoa <br>
					Región Metropolitana <br>
					Fono: +569 8599 1053
				</h4>
			</td>
		</tr>
	</table>

	<table style="width: 590px;">
		<tr>
			<td style="width: 590px;">
				<h1 style="text-align: center">'.$title.' n&deg;000'.$solicitud->id.'</h1>
			</td>
		</tr>
	</table>

	<table style="width: 590px;">
		<tr>
			<td style="width: 295px; border: 1px solid #000;">
				<table class="no_padding">
					<tr><td><strong>Nombre: </strong></td><td>' . $cliente->nombres . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno . '</td></tr>
					<tr><td><strong>Email: </strong></td><td>' . $cliente->email . '</td></tr>
					<tr><td><strong>Teléfono: </strong></td><td>' . $cliente->telefono . '</td></tr>
				</table>
			</td>
			<td style="width: 295px; border: 1px solid #000;">
				<table class="no_padding">
					<tr>
						<td><strong>Marca/Modelo: </strong></td>
						<td>' . $vehiculo->marca . ' / ' . $vehiculo->modelo . '</td>
					</tr>
					<tr>
						<td><strong>A&ntilde;o: </strong></td>
						<td>' . $vehiculo->ano . '</td>
					</tr>
					<tr>
						<td><strong>Color: </strong></td>
						<td>' . $vehiculo->color . '</td>
					</tr>
					<tr>
						<td><strong>Patente: </strong></td>
						<td>' . $vehiculo->patente . '</td>
					</tr>
					<tr>
						<td><strong>VIN: </strong></td>
						<td>' . $vehiculo->nro_motor . '</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br><br><br>
	<table border="1">
		<tr>
			<td style="width: 635px; text-align: center;"> <strong>Solicitud</strong> </td>
		</tr>';

		$lastupdated = is_null($solicitud->upddate) ? '-' : date_format(date_create($solicitud->upddate), 'd/m/Y - H:i');
		$motivo = '' === $solicitud->motivo ? '' : '<tr><td><strong>Motivo:</strong> '.$solicitud->motivo.'</td></tr>';
		$fecha = is_null($solicitud->fecha) ? '' : '<tr><td><strong>Fecha Agendada:</strong> '.date_format(date_create("{$solicitud->fecha} {$solicitud->hora}"), 'd/m/Y H:i') .'</td></tr>';

		$html .= '
		<tr>
			<td style="width: 635px; text-align: justify; white-space:pre-wrap"><strong>'. $solicitud->solicitud .'</strong></td>
		</tr>
	</table>
	<br>
	<table border="0" style="width: 590px">
		<tr>
			<td>
				<strong>Creado:</strong> '.date_format(date_create($solicitud->regdate), 'd/m/Y - H:i').'
				<br>
				<strong>Modificado:</strong> '. $lastupdated .'
			</td>
		</tr>
		'.$motivo.'
		'.$fecha.'
	</table>
	</page>';

    $blueprint = Mopar::getBlueprintBySolicitudId($_GET['id']);
    if(is_null($blueprint)) $blueprint = 'https://www.doctormopar.com/wp-content/uploads/2023/10/jeep-compass-4.png';
    else $blueprint = site_url('wp-content/plugins/mopar_taller/uploads/' . rawurlencode($blueprint));

	$page_2 = '
	<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm">
	    
	    <div style="text-align:center">
	        <img src="'.$blueprint.'">
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


	require_once('html2pdf/html2pdf.class.php');
    $html2pdf = new HTML2PDF($orientation,'LETTER','es');
    $html2pdf->WriteHTML($html);
    $html2pdf->Output( $titulo_pdf . '_000'. $solicitud->id .'.pdf');
}

?>