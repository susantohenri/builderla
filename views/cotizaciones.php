<?php
$folder = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/builderla/uploads/';
$inserted = false;
$updated = false;
$error_message = false;

$estimate_status_map = [
	'NEWLY_CREATED' => 'danger',
	'UPDATED' => 'warning',
	'ESTIMATE_EMAIL_SENT' => 'success',
	'CONTRACT_INITIATED' => 'primary'
];

if ($_POST) {
	global $wpdb;

	$array_insert = [
		'titulo' => isset($_POST['titulo']) ? strtoupper($_POST['titulo']) : '',
		'detalle' => isset($_POST['detalle']) ? json_encode($_POST['detalle']) : [],
		'valor' => isset($_POST['valor']) ? $_POST['valor'] : '',
		'site_services' => isset($_POST['cb']['site_services']) ? $_POST['site_services'] : '',
		'customer_to_provide' => isset($_POST['cb']['customer_to_provide']) ? $_POST['customer_to_provide'] : '',
		'not_included' => isset($_POST['cb']['not_included']) ? $_POST['not_included'] : '',
		'price_breakdown' => isset($_POST['cb']['price_breakdown']) ? '1' : '0'
	];
	if (isset($_POST['cliente'])) $array_insert['cliente_id'] = $_POST['cliente'];
	if (isset($_POST['vehiculo'])) $array_insert['vehiculo_id'] = $_POST['vehiculo'];

	if ($_POST['action'] == 'insertar_cotizaciones') {
		$client_createdBy = $wpdb->get_var("
			SELECT clientes.createdBy
			FROM vehiculos
			LEFT JOIN clientes ON vehiculos.cliente_id = clientes.id
			WHERE vehiculos.id = {$_POST['vehiculo']}
		");
		if (0 == $client_createdBy) $error_message = 'Error: customer must be claimed first';
		else {
			$create_ot = $wpdb->insert('ot', [
				'vehiculo_id' => $_POST['vehiculo'],
				'estado' => 1,
				'estimate_status' => 'NEWLY_CREATED',
				'detalle' => '{"item":[""],"precio":[""], "observaciones":[""]}'
			]);
			$create_solicitud = $wpdb->insert('solicitud', [
				'ot_id' => $wpdb->insert_id,
				'vehiculo_id' => $_POST['vehiculo']
			]);
			if ($create_ot) {
				$inserted = true;
			}
		}
	}

	if ($_POST['action'] == 'editar_ot') {
		$before_update = (array) Mopar::getOneOt($_POST['ot_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) {
			$array_insert['upddate'] = date('Y-m-d H:i:s');
			$array_insert['estimate_status'] = 'UPDATED';
		}

		if ($wpdb->update('ot', $array_insert, ['id' => $_POST['ot_id']])) {
			Mopar::solicitudCalculateSelling($_POST['ot_id']);
			$updated = true;
		}
	}

	if ($_POST['action'] == 'send_estimation_email_body') {
		$ot_id = $_POST['ot_id'];

		wp_schedule_single_event(time(), 'mopar_async', [
			[
				'action' => 'send_estimation_email',
				'recipient' => [
					'ot_id' => $ot_id,
					'email' => $_POST['recipient'],
					'email_body' => $_POST['email_body']
				]
			]
		]);

		$wpdb->update('ot', ['estado' => 2, 'estimate_status' => 'ESTIMATE_EMAIL_SENT'], ['id' => $ot_id]);
		$wpdb->update('solicitud', ['estado' => 5], ['ot_id' => $ot_id]);
		$estimation_email_body_sent = true;
	}

	if ($_POST['action'] == 'initiate_contract') {
		$wpdb->update('solicitud',
			[
				'estado' => 6,
				// 'owner_over_65' => $_POST['owner_over_65'],
				'construction_lender_name' => $_POST['construction_lender_name'],
				'construction_lender_address' => $_POST['construction_lender_address'],
				'approximate_start_date' => date_format(date_create($_POST['approximate_start_date']), 'Y-m-d'),
				'approximate_completion_date' => date_format(date_create($_POST['approximate_completion_date']), 'Y-m-d'),
			],
			['ot_id' => $_POST['ot_id']]
		);
		$wpdb->update('ot', ['estimate_status' => 'CONTRACT_INITIATED', 'contract_status' => 'NEWLY_CREATED'], ['id' => $_POST['ot_id']]);
		$contract_initiated = true;
	}
}
?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left">Estimates </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewCotizaciones">New Estimate</button>

		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_ots" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th> Date </th>
					<th> Customer </th>
                    <th> Project Description </th>
					<th> Total </th>
					<th> Status </th>
					<th class="text-center">Options</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ots as $ot) : ?>
					<tr data-regid="<?php echo $ot->id; ?>">
						<td data-regid="<?php echo $ot->id; ?>"> <?php echo $ot->id; ?> </td>
						<td data-titulo="<?php echo $ot->titulo; ?>"> <?php echo date_format(date_create($ot->regdate),"Y-m-d"); ?> </td>
						<td data-vehiculo="<?php echo $ot->vehiculo_id; ?>"> <?php echo Mopar::getTitleVehiculo($ot->vehiculo_id) ?> </td>
                        <td> <?php echo $ot->titulo; ?> </td>
						<td data-valor="<?php echo $ot->valor; ?>"> $ <?php echo number_format($ot->valor, 0, ',', '.') ?> </td>
						<td data-estado="<?php echo $ot->estado; ?>" class="text-center align-middle">
							<a>
								<i class="fa fa-circle text-<?= $estimate_status_map[$ot->estimate_status] ?>"></i>
							</a>
						</td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $ot->id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/builderla/estimate-pdf.php?id=<?php echo $ot->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
							<?php if(!in_array($ot->estimate_status, ['ESTIMATE_EMAIL_SENT', 'CONTRACT_INITIATED'])): ?>
								<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
							<?php endif; ?>
							<button class="btn btn-warning btnSendEstimationEmail" data-toggle="tooltip" title="Send Estimate"><i class="fa fa-envelope"></i></button>
							<button class="btn btn-primary btnContract" data-toggle="tooltip" title="Initiate Contract"><i class="fa fa-check"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<br>
		<ul>
			<li>
				<i class="fa fa-circle text-primary"></i> Contract was initiated for this estimate.
			</li>
			<li>
				<i class="fa fa-circle text-success"></i> This estimate was sent to the customer.
			</li>
			<li>
				<i class="fa fa-circle text-warning"></i> This estimate is currently being prepared.
			</li>
			<li>
				<i class="fa fa-circle text-danger"></i> There are no actions for this estimate
			</li>
		</ul>
	</div>
</div>

<!-- Nuevo Cotizaciones -->
<div class="modal fade" id="modalNewCotizaciones" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="modalNewCotizaciones" enctype="multipart/form-data">
		<input type="hidden" name="action" value="insertar_cotizaciones">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">New Estimate</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Address</span>
								</div>
								<select name="vehiculo" class="form-control">
									<option value="">Select property first</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn btn-success btnGuardar">Save <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- EDITAR OT -->
<div class="modal fade" id="modalEditOT" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditOT" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_ot">
		<input type="hidden" name="ot_id" value="">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Estimate</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Address</span>
								</div>
								<select name="vehiculo" class="form-control" disabled required>
									<option value="">Seleccione Cliente primero</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Project Description</span>
								</div>
								<!-- <input type="text" name="titulo" class="form-control" required> -->
								 <textarea name="titulo" class="form-control" required></textarea>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="row">
								<label class="col-md-3">
									<input type="checkbox" name="cb[site_services]"> Portable toilet
								</label>
								<label class="col-md-3">
									<input type="checkbox" name="cb[customer_to_provide]"> customer to provide
								</label>
								<label class="col-md-3">
									<input type="checkbox" name="cb[not_included]"> not included
								</label>
								<label class="col-md-3">
									<input type="checkbox" name="cb[price_breakdown]"> price breakdown
								</label>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Site Services</span>
								</div>
								<input type="text" name="site_services" readonly class="form-control">
							</div>
						</div>
						<div class="form-group col-md-12">
							<table class="table table-estimate">
								<thead>
									<tr>
										<th> Details </th>
										<th> Price </th>
										<th></th>
									</tr>
								</thead>
								<tbody class="bg-light">
								</tbody>
								<tfoot>
									<tr>
										<th colspan="3"><button type="button" class="btn btn-success float-right btn-sm btnPlus" data-toggle="tooltip" title="Agregar linea de detalle"><i class="fa fa-plus"></i></button></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Customer to Provide</span>
								</div>
								<input type="text" name="feeder[customer_to_provide]" class="form-control">
							</div>
							<textarea name="customer_to_provide" class="form-control customer_to_provide"></textarea>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Not Included</span>
								</div>
								<input type="text" name="feeder[not_included]" class="form-control">
							</div>
							<textarea name="not_included" class="form-control not_included"></textarea>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Total</span>
								</div>
								<input type="text" class="form-control text-right" name="valor" required readonly>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn btn-success btnGuardar">Save <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- SEND ESTIMATION EMAIL -->
<div class="modal fade" id="modalSendEstimationEmail" tabindex="-1" role="dialog"
	aria-labelledby="modalSendEstimationEmail" aria-hidden="true">
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="send_estimation_email_body">
		<input type="hidden" name="ot_id">
		<input type="hidden" name="recipient">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Send Estimation Email</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<div class="col-12">
							<textarea name="email_body" class="form-control"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btnGuardar">
						<i class="fa fa-envelope"></i>
						SEND
					</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i>
						CANCEL
					</button>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- INITIATE CONTRACT -->
<div class="modal fade" id="modalInitiateContract" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="initiate_contract">
		<input type="hidden" name="ot_id">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Initiate Contract</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<!-- <div class="form-group row">
						<label class="col-sm-12 col-md-4 col-form-label">Is the owner over 65?</label>
						<div class="col-sm-12 col-md-8">
							<select name="owner_over_65" class="form-control">
								<option value="1">YES</option>
								<option value="0">NO</option>
							</select>
						</div>
					</div> -->
					<div class="form-group row">
						<label class="col-sm-12 col-md-4 col-form-label">is there a construction lender?</label>
						<div class="col-sm-12 col-md-8">
							<select name="construction_lender" class="form-control">
								<option value="1">YES</option>
								<option value="0">NO</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-12 col-md-4 col-form-label">NAME:</label>
						<div class="col-sm-12 col-md-8">
							<input type="text" name="construction_lender_name" class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-12 col-md-4 col-form-label">ADDRESS:</label>
						<div class="col-sm-12 col-md-8">
							<input type="text" name="construction_lender_address" class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-12 col-md-4 col-form-label">Approximate start date</label>
						<div class="col-sm-12 col-md-8">
							<input type="text" name="approximate_start_date" class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-12 col-md-4 col-form-label">Approximate completion date</label>
						<div class="col-sm-12 col-md-8">
							<input type="text" name="approximate_completion_date" class="form-control">
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success btnGuardar"><i class="fa fa-save"></i> SAVE</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> CANCEL</button>
				</div>
			</div>
		</div>
	</form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap4-datetimepicker@5.2.3/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-datetimepicker@5.2.3/build/js/bootstrap-datetimepicker.min.js"></script>
<script>
	$(document).ready(function() {
		$(`[name="vehiculo"]`).css(`display`, `none`).select2({
			theme: `bootstrap4`,
			minimumInputLength: 3,
			ajax: {
				url: `../wp-json/mopar-taller/v1/select2-property-for-leads`
			}
		})

		$('[name="approximate_start_date"],[name="approximate_completion_date"]').datetimepicker({
			format: `MM/DD/YYYY`
		})

		jQuery(`[name="construction_lender"]`).change(function () {
			const lender_detail = jQuery(`[name^="construction_lender_"]`).parent().parent()
			if (1 == jQuery(this).val()) lender_detail.show()
			else lender_detail.hide()
		})

		$(".btnEdit").click(function() {
			ot_id = $(this).data('regid');
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=get_ot&ot_id=' + ot_id,
				beforeSend: function() {
					$(".overlay").show();
				},
				success: function(json) {
					detalle = JSON.parse(json.ot.detalle);

					$(".overlay").hide();
					$('#modalEditOT [name=ot_id]').val(json.ot.id);

					const selected_property = json.vehiculos[0]
					jQuery(`[name="vehiculo"]`).html(`<option selected value="${selected_property.id}">${selected_property.street_address} ${selected_property.address_line_2} - ${json.cliente.nombres}</option>`)

					$(`[name="site_services"]`).val(json.ot.site_services)
					$(`[name="cb[site_services]"]`).attr(`checked`, `` != json.ot.site_services).trigger(`change`)

					$(`[name="customer_to_provide"]`).val(json.ot.customer_to_provide).attr(`rows`, json.ot.customer_to_provide.split(`\n`).length + 1)
					$(`[name="cb[customer_to_provide]"]`).attr(`checked`, `` != json.ot.customer_to_provide).trigger(`change`)

					$(`[name="not_included"]`).val(json.ot.not_included).attr(`rows`, json.ot.not_included.split(`\n`).length + 1)
					$(`[name="cb[not_included]"]`).attr(`checked`, `` != json.ot.not_included).trigger(`change`)

					$(`[name="cb[price_breakdown]"]`).attr(`checked`, 1 == json.ot.price_breakdown)

					$('#modalEditOT [name=titulo]').val(json.ot.titulo);

					$("#modalEditOT table tbody").empty();
					$.each(detalle.item, function(k, v) {
						const observaciones_row_count = detalle.observaciones[k].split(`\n`).length
						h = '<tr>';
						h += '	<td>';
						h += '		<input type="text" value="' + detalle.item[k] + '" name="detalle[item][]" class="form-control" required>';
						h += '	</td>';
						h += '	<td>';
						h += '		<input type="text" value="' + detalle.precio[k] + '" name="detalle[precio][]" class="form-control precio text-right" required>';
						h += '	</td>';
						h += '</tr>';
						h += `
							<tr">
								<td colspan="2">
									<input type="text" class="form-control observaciones" placeholder="write the details and press Enter to add it to the estimate">
									<textarea rows="${observaciones_row_count}" name="detalle[observaciones][]" class="form-control observaciones">${detalle.observaciones[k]}</textarea>'
								</td>
							</tr>
						`
						h += `
							<tr>
								<td colspan="2" class="text-right">
									<a href="#" data-toggle="tooltip" title="Borra Linea" class="btn btn-danger btn-sm btnLess"><i class="fa fa-minus"></i></a>
									<a href="#" class="btn btn-info btn-sm btnUp"><i class="fa fa-arrow-up"></i></a>
									<a href="#" class="btn btn-info btn-sm btnDown"><i class="fa fa-arrow-down"></i></a>
								</td>
							</tr>
						`
						$("#modalEditOT table tbody").append(h);
						$("[data-toggle=tooltip]").tooltip();
						$('.tooltip').hide();
					})
					recalcular()
					rewrite_row_num()

					$('#modalEditOT [name=valor]').val(json.ot.valor);

					$('#modalEditOT').modal('show');
				}
			})
		})

		$(document).on('keyup', '.precio', function(e) {
			recalcular();
		})

		$(document).on('keydown', '[type="text"].observaciones', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault()

				const input = jQuery(this)
				const textArea = input.siblings(`textarea`)
				const curVal = textArea.val()
				const curRow = curVal.split(`\n`).length
                let text = input.val().trim()

				if (!text) return false
				else text = text.charAt(0).toUpperCase() + text.slice(1)

				text = `- ${text}`
				text = `` == curVal ? text : `\n${text}`
				textArea.val(curVal + text)
				textArea.attr(`rows`, curRow + 1)
				input.val(``)
            }
		})

		$(document).on('click', '.btnLess', function(e) {
			e.preventDefault();
			tr = $(this).closest('tr');
			const row_num = tr.attr(`data-row-num`).replace(`c`, ``)

			tr.fadeOut(300, function() {
				jQuery(`tr[data-row-num^="${row_num}"]`).remove()
				recalcular();
			})
			$("[data-toggle=tooltip]").tooltip();
			$('.tooltip').hide();
		})

		$(document).on('click', '.btnUp', function(e) {
			e.preventDefault();
			const tr = $(this).closest('tr');
			const row_num = parseInt(tr.attr(`data-row-num`).replace(`c`, ``))
			if (1 > row_num) return false;

			jQuery(`[data-row-num^=${row_num}]`).insertBefore(jQuery(`[data-row-num="${row_num - 1}a"]`))
			rewrite_row_num()
		})

		$(document).on('click', '.btnDown', function(e) {
			e.preventDefault();
			const tr = $(this).closest('tr');
			const row_num = parseInt(tr.attr(`data-row-num`).replace(`c`, ``))
			const next_row= jQuery(`[data-row-num="${row_num + 1}c"]`)

			if (1 > next_row.length) return false
			jQuery(`[data-row-num^="${row_num}"]`).insertAfter(next_row)
			rewrite_row_num()
		})

		$(".btnPlus").click(function(e) {
			e.preventDefault();

			h = '';
			h += '<tr>';
			h += '	<td>';
			h += '		<input type="text" name="detalle[item][]" class="form-control" required>';
			h += '	</td>';
			h += '	<td>';
			h += '		<input type="text" name="detalle[precio][]" class="form-control precio text-right" value="" required>';
			h += '	</td>';
			h += '</tr>';
			h += `
				<tr>
					<td colspan="2">
						<input type="text" class="form-control observaciones" placeholder="write the details and press Enter to add it to the estimate">
						<textarea name="detalle[observaciones][]" class="form-control observaciones"></textarea>
					</td>
				</tr>
			`
			h += `
				<tr>
					<td colspan="2" class="text-right">
						<a href="#" data-toggle="tooltip" title="Borra Linea" class="btn btn-danger btn-sm btnLess"><i class="fa fa-minus"></i></a>
						<a href="#" class="btn btn-info btn-sm btnUp"><i class="fa fa-arrow-up"></i></a>
						<a href="#" class="btn btn-info btn-sm btnDown"><i class="fa fa-arrow-down"></i></a>
					</td>
				</tr>
			`
			$(this).closest('.modal').find('table tbody').append(h)
			$("[data-toggle=tooltip]").tooltip();
			$('.tooltip').hide();
			recalcular();
		})

		$(".btnDelete").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Delete Estimate!',
				content: 'Do you want to delete the selected estimate?',
				type: 'red',
				icon: 'fa fa-warning',
				buttons: {
					NO: {
						text: 'No',
						btnClass: 'btn-red',
					},
					SI: {
						text: 'Yes',
						btnClass: 'btn-green',
						action: function() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType: 'json',
								data: 'action=eliminar_ot&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									$.alert({
										title: false,
										type: 'green',
										content: 'Estimate deleted'
									});
									tr.fadeOut(400);
								}
							})
						}
					}
				}
			});
		});

		jQuery(`.btnSendEstimationEmail`).click(function() {
			tr = $(this).closest('tr')
			regid = tr.data('regid')
			jQuery(`div#modalSendEstimationEmail [name="ot_id"]`).val(regid)
			jQuery.ajax({
				type: `GET`,
				dataType: 'json',
				url: `<?= admin_url('admin-ajax.php') ?>`,
				data: `action=get_estimation_email_body&ot_id=${regid}`,
				beforeSend: function() {
					jQuery(`.overlay`).show()
				},
				success: function(json) {
					jQuery(`.overlay`).hide()
					if (`ERROR` === json.status) {
						$.alert({
							title: false,
							type: 'red',
							content: json.message
						});
					} else {
						jQuery(`[name="recipient"]`).val(json.recipient)
						jQuery(`[name="email_body"]`)
							.val(json.message)
							.attr(`rows`, json.message.split(`\n`).length)
						$(`#modalSendEstimationEmail`).modal(`show`)
					}
				}
			})
		})

		<?php if (isset($estimation_email_body_sent)): ?>
			$.alert({
				title: false,
				type: 'green',
				content: 'Estimation email sent successfully',
				buttons: {
					OK: () => {
						location.reload()
					}
				}
			});
		<?php endif ?>

		$(".btnContract").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=validate_initiate_contract&regid=' + regid,
				beforeSend: function() {
					$(".overlay").show();
				},
				success: function(json) {
					$(".overlay").hide();
					if (`ERROR` === json.status) {
						$.alert({
							title: false,
							type: 'red',
							content: json.message
						});
					} else {
						$('#modalInitiateContract').find(`select`).val(0).change()
						$('#modalInitiateContract').find(`[type="text"]`).val(``)
						$('#modalInitiateContract').find(`[name="ot_id"]`).val(regid)
						$('#modalInitiateContract').modal('show');
					}
				}
			})
		});

		<?php if (isset($contract_initiated)): ?>
			$.alert({
				title: false,
				type: 'green',
				content: 'Contract Initiated Successfully',
				buttons: {
					OK: () => {
						location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-contracts';
					}
				}
			});
		<?php endif ?>

		$("#formNuevoOT").submit(function(e) {
			$(".overlay").show();
			recalcular();
			e.preventDefault();
			$("#formNuevoOT")[0].submit();
		});


		$("#formEditOT").submit(function(e) {
			$(".overlay").show();
			recalcular();
			e.preventDefault();
			$("#formEditOT")[0].submit();
		});


		<?php if ($inserted) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Estimate created'
			})
		<?php } ?>


		<?php if ($updated) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Estimate updated'
			})
		<?php } ?>

		<?php if ($inserted || $updated) { ?>
			location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-cotizaciones';
		<?php } ?>

		<?php if ('' != $error_message) { ?>
			$.alert({
				type: 'red',
				title: false,
				content: '<?= $error_message ?>'
			})
		<?php } ?>

		$('#tabla_ots').DataTable({
			"scrollX": true,
			"ordering": false
		});
	});

	function rewrite_row_num () {
		let number = 0
		const letter = [`a`,`b`,`c`]
		let current_letter_index = 0
		jQuery(`.table-estimate tbody tr`).each((index, tr) => {
			jQuery(tr).attr(`data-row-num`, `${number}${letter[current_letter_index]}`)

			current_letter_index++
			if (!letter[current_letter_index]) {
				current_letter_index = 0
				number++
			}
		})
	}

	function recalcular() {
		tot = 0;
		$(".precio").each(function() {
			if ($(this).val() == "") {
				this_val = 0;
			} else {
				this_val = $(this).val();
			}
			tot += parseInt(this_val);
		})
		$("[name=valor]").val(tot);
	}

	$(`[name="cb[site_services]"]`).change(function() {
		const checked = jQuery(this).is(`:checked`)
		const input = $(`[name="site_services"]`)
		const container = input.parent().parent()
		if (checked) {
			input.val(`Contractor will provide a portable toilet`)
			container.show()
		} else container.hide()
	})

	$(`[name="cb[customer_to_provide]"]`).change(function() {
		const checked = jQuery(this).is(`:checked`)
		const input = $(`[name="feeder[customer_to_provide]"]`).parent().parent()
		if (checked) input.show()
		else input.hide()
	})

	$(`[name="cb[not_included]"]`).change(function() {
		const checked = jQuery(this).is(`:checked`)
		const input = $(`[name="feeder[not_included]"]`).parent().parent()
		if (checked) input.show()
		else input.hide()
	})

	$(document).on(`keydown`, `[name^="feeder"]`, function(event) {
		if (event.key === 'Enter') {
			event.preventDefault()

			const input = jQuery(this)
			const textArea = input.parent().siblings(`textarea`)
			const curVal = textArea.val()
			const curRow = curVal.split(`\n`).length
			let text = input.val().trim()

			if (!text) return false
			else text = text.charAt(0).toUpperCase() + text.slice(1)

			text = `- ${text}`
			text = `` == curVal ? text : `\n${text}`
			textArea.val(curVal + text)
			textArea.attr(`rows`, curRow + 2)
			input.val(``)
		}
	})
</script>

<?php include 'footer.php'; ?>