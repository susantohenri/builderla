<?php  

include '../../../wp-load.php';

$current_user = wp_get_current_user();
if (user_can( $current_user, 'administrator' )) {

	$ot = Mopar::getOneOt($_GET['id']);
	$cliente = Mopar::getOneCliente($ot->cliente_id);
	$vehiculo = Mopar::getOneVehiculo($ot->vehiculo_id);
	$solicitud = Mopar::getOneSolicitudByOtId($_GET['id']);
	
	if( $ot->estado == 1 ){
		$titulo_ot = 'Cotización';
		$titulo_pdf = 'cotizacion';
	} else {
		$titulo_ot = 'Trabajo Realizado';
		$titulo_pdf = 'ot';
	}


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
				<h3 style="margin-bottom: 10px">Doctor Mopar Taller</h3>
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
				<h1 style="text-align: center">'. $titulo_ot .' n&deg;000'.$ot->id.'</h1>
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
						<td>' . $vehiculo->marca . '/' . $vehiculo->modelo . '</td>
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
	<table style="width: 590px;" border="1">
		<tr>
			<td style="width: 480px; text-align: center;"> <strong>Descripción</strong> </td>
			<td style="width: 110px; text-align: center;"> <strong>Valor</strong> </td>
		</tr>';

		$detalles = json_decode($ot->detalle);
		foreach ($detalles->item as $key => $value) {

			$html .= '
			<tr>
				<td style="text-align: left">' . $detalles->item[$key] . '</td>
				<td style="text-align: right;"> $ ' . number_format($detalles->precio[$key],0,',','.') . '</td>
			</tr>';
		}

		$observaciones = '' === $ot->observaciones ? '' : '<tr><td style="width: 590px"><strong>Observaciones adicionales: </strong><br>' . nl2br($ot->observaciones) . '</td></tr>';
		$solicitud_inicial = !isset($solicitud->solicitud) && '' !== $solicitud->solicitud ? '' : '<tr><td style="width: 590px"><strong>Solicitud inicial: </strong><br>' . nl2br($solicitud->solicitud) . '</td></tr>';
		$lastupdated = is_null($ot->upddate) ? '-' : date_format(date_create($ot->upddate), 'd/m/Y - H:i');

		$html .= '
		<tr>
			<td style="text-align: right;"><strong>TOTAL</strong></td>
			<td style="text-align: right;"> $ ' . number_format($ot->valor,0,',','.') . '</td>
		</tr>
	</table>
	<br>
	<table border="0" style="width: 590px">
		<tr>
			<td style="width: 590px">
			    <strong>Kilometraje: ' . $ot->km . '</strong>
			</td>
		</tr>
		'.$observaciones.'
		'.$solicitud_inicial.'
		<tr>
			<td>
				<strong>Creado:</strong> '.date_format(date_create($ot->regdate), 'd/m/Y - H:i').'
				<br>
				<strong>Modificado:</strong> '. $lastupdated .'
			</td>
		</tr>
	</table>
	</page>
	';


	require_once('html2pdf/html2pdf.class.php');
    $html2pdf = new HTML2PDF($orientation,'LETTER','es');
    $html2pdf->WriteHTML($html);
    $html2pdf->Output( $titulo_pdf . '_000'. $ot->id .'.pdf');

}

?>