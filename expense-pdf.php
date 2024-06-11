<?php

include '../../../wp-load.php';
$solicitud = Mopar::getOneSolicitud($_GET['id']);
foreach (['total', 'iva_debito', 'iva_credito', 'gastos', 'utilidad'] as $currency) $solicitud->$currency = number_format($solicitud->$currency, 0);

$expenses = '';
foreach (json_decode($solicitud->expense) as $expense) {
    $expense->monto = number_format($expense->monto, 0);
    $expenses .= "
        <tr>
            <td style='width: 144.5px; border: 1px solid black; text-align: center;'>
                {$expense->proveedor}
            </td>
            <td style='width: 144.5px; border: 1px solid black; text-align: center;'>
                $ {$expense->monto}
            </td>
            <td style='width: 144.5px; border: 1px solid black; text-align: center;'>
                {$expense->tipo_de_documento}
            </td>
            <td style='width: 144.5px; border: 1px solid black; text-align: center;'>
                {$expense->detalle}
            </td>
        </tr>
    ";
}

$html = "
	<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'>

		<table style='width: 590px;'>
			<tbody>
                <tr>
                    <td style='width: 590px;font-size: 32px; text-align: center;'>
                        <u>Conciliaci¨®n Contable</u>
                    </td>
                </tr>
				<tr>
					<td style='width: 590px;text-align: center;font-size: 20px;'>
						<b>TRABAJO N¡ã 000{$solicitud->id}</b>
					</td>
				</tr>
			</tbody>
            <tfoot>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
			<tbody>
                <tr>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>VALOR TOTAL</b>
                    </td>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>$ {$solicitud->total}</b>
                    </td>
                </tr>
                <tr>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>IVA DEBITO</b>
                    </td>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>$ {$solicitud->iva_debito}</b>
                    </td>
                </tr>
			</tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
            <thead>
                <tr>
                    <th style='width: 144.5px; border: 1px solid black; text-align: center;'>
                        <b>PROVEEDOR</b>
                    </th>
                    <th style='width: 144.5px; border: 1px solid black; text-align: center;'>
                        <b>MONTO</b>
                    </th>
                    <th style='width: 144.5px; border: 1px solid black; text-align: center;'>
                        <b>TIPO DE DOCUMENTO</b>
                    </th>
                    <th style='width: 144.5px; border: 1px solid black; text-align: center;'>
                        <b>DETALLE</b>
                    </th>
                </tr>
            </thead>
			<tbody>
                {$expenses}
			</tbody>
            <tfoot>
                <tr>
                    <td colspan='4'>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
			<tbody>
                <tr>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>TOTAL GASTOS</b>
                    </td>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>$ {$solicitud->gastos}</b>
                    </td>
                </tr>
                <tr>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>IVA CREDITO</b>
                    </td>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>$ {$solicitud->iva_credito}</b>
                    </td>
                </tr>
			</tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
			<tbody>
                <tr>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>RESULTADO</b>
                    </td>
                    <td style='width: 295px; border: 1px solid black; text-align: center;'>
                        <b>$ {$solicitud->utilidad}</b>
                    </td>
                </tr>
			</tbody>
		</table>

    </page>
";

require_once('html2pdf/html2pdf.class.php');
$html2pdf = new HTML2PDF($orientation, 'LETTER', 'es');
$html2pdf->WriteHTML($html);
$html2pdf->Output('Conciliaci¨®n_000' . $solicitud->id . '.pdf');
