<?php
$inserted = false;
$updated = false;

if ($_POST) {
	global $wpdb;
	$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/builderla/uploads/';

	if (in_array($_POST['action'], ['insertar_solicitud', 'editar_solicitud'])) $array_insert = [
		'vehiculo_id' => $_POST['vehiculo'],
		'solicitud' => $_POST['solicitud'],
		'photos' => '[]'
	];

	if ($_POST['action'] == 'insertar_solicitud') {
		$array_insert['estado'] = 1;
		$array_insert['createdBy'] = get_current_user_id();

		if ($wpdb->insert('solicitud', $array_insert)) {
			$inserted = true;
		}
	}

	if ($_POST['action'] == 'editar_solicitud') {
		$before_update = (array) Mopar::getOneSolicitud($_POST['solicitud_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) $array_insert['upddate'] = date('Y-m-d H:i:s');

		if ($wpdb->update('solicitud', $array_insert, ['id' => $_POST['solicitud_id']])) {
			$updated = true;
		}
	}

	if ($_POST['action'] == 'editar_motivo') {
		$array_insert = ['motivo' => $_POST['motivo']];
		$before_update = (array) Mopar::getOneSolicitud($_POST['solicitud_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) $array_insert['upddate'] = date('Y-m-d H:i:s');

		if ($wpdb->update('solicitud', $array_insert, ['id' => $_POST['solicitud_id']])) {
			$updated = true;
		}
	}

	if ($_POST['action'] == 'editar_fecha') {
		$hora = explode(' ', $_POST['hora']);
		$am_pm = $hora[1];
		$time = explode(':', $hora[0]);
		$hour = $time[0];
		$minute = $time[1];
		if ('PM' == $am_pm) $hour += 12;
		$mysql_time = "{$hour}:{$minute}";
		$array_insert = ['fecha' => $_POST['fecha'], 'hora' => $mysql_time];

		$before_update = (array) Mopar::getOneSolicitud($_POST['solicitud_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) {
			$array_insert['upddate'] = date('Y-m-d H:i:s');
			$wpdb->update('solicitud', $array_insert, ['id' => $_POST['solicitud_id']]);
			Mopar::sendMail($_POST['solicitud_id'], 'fecha_updated');
		}
		$updated = true;
	}
}
?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Leads </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewSolicitud">New Lead</button>

		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_solicituds" width="100%">
			<thead>
				<tr>
					<th> Date </th>
					<th> Customer </th>
					<th> Status </th>
					<th class="text-center">Options</th>
					<th> Source </th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->regdate_format; ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php if (0 != $solicitud->vehiculo_id) echo Mopar::getTitleVehiculo($solicitud->vehiculo_id) ?> </td>
						<td data-estado="<?php echo $solicitud->estado; ?>" class="text-center align-middle">
							<?php if (!is_null($solicitud->fecha)) : ?>
								<a>
									<i class="fa fa-check text-success"></i>
								</a>
							<?php elseif ('' !== $solicitud->motivo) : ?>
								<a>
									<i class="fa fa-times text-danger"></i>
								</a>
							<?php elseif (1 == $solicitud->estado) : ?>
								<a>
									<i class="fa fa-circle text-danger"></i>
								</a>
							<?php elseif (in_array($solicitud->estado, [3])) : ?>
								<a>
									<i class="fa fa-circle text-warning"></i>
								</a>
							<?php elseif (in_array($solicitud->estado, [2, 4, 5])) : ?>
								<a>
									<i class="fa fa-circle text-success"></i>
								</a>
							<?php endif; ?>
						</td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $solicitud->id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/builderla/solicitud-pdf.php?id=<?php echo $solicitud->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
							<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
							<!--
							<button class="btn btn-warning btnProceedWithoutIngreso" data-toggle="tooltip" title="Iniciar Cotización"><i class="fa fa-list"></i></button>
							-->
							<button class="btn btn-success btnFecha" data-toggle="tooltip" title="Schedule"><i class="fa fa-check"></i></button>
							<button class="btn btn-danger btnMotivo" data-toggle="tooltip" title="Discard"><i class="fa fa-times"></i></button>
						</td>
						<td class="text-center" data-createdBy="<?php echo $solicitud->createdBy; ?>"> <?php echo builderla_get_creator_user_login($solicitud->createdBy); ?> </td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<ul>
			<li>
				<i class="fa fa-check text-success"></i> This lead has been scheduled for inspection.
			</li>
			<li>
				<i class="fa fa-times text-danger"></i> This lead has been marked as lost or rejected.
			</li>
			<li>
				<i class="fa fa-circle text-success"></i> This lead is an active project or has been completed.
			</li>
			<li>
				<i class="fa fa-circle text-danger"></i> There are no actions for this request.
			</li>
		</ul>
	</div>
</div>




<!-- Nuevo Solicitud -->
<div class="modal fade" id="modalNewSolicitud" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoSolicitud" enctype="multipart/form-data">
		<input type="hidden" name="action" value="insertar_solicitud">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Request</h5>
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
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Request</span>
								</div>
								<textarea class="form-control" name="solicitud"></textarea>
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






<!-- EDITAR Solicitud -->
<div class="modal fade" id="modalEditSolicitud" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditSolicitud" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_solicitud">
		<input type="hidden" name="solicitud_id" value="">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Lead Details</h5>
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
								<select name="vehiculo" class="form-control">
									<option value="">Seleccione Cliente primero</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Request</span>
								</div>
								<textarea class="form-control" name="solicitud"></textarea>
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

<!-- EDITAR Motivo -->
<div class="modal fade" id="modalEditMotivo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditMotivo" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_motivo">
		<input type="hidden" name="solicitud_id" value="">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Provide a reason why the lead was lost:</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Reason</span>
								</div>
								<textarea class="form-control" name="motivo" required></textarea>
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

<!-- EDITAR Fecha -->
<div class="modal fade" id="modalEditFecha" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditFecha" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_fecha">
		<input type="hidden" name="solicitud_id" value="">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Set Appointment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Date</span>
								</div>
								<input type="text" class="form-control" name="fecha" required>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Time</span>
								</div>
								<input type="text" class="form-control" name="hora" required>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap4-datetimepicker@5.2.3/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-datetimepicker@5.2.3/build/js/bootstrap-datetimepicker.min.js"></script>
<script>
	$(document).ready(function() {
		const url_retrieve_id = (new URLSearchParams(window.location.search)).get(`id`)
		$(`#modalEditSolicitud`).on(`hidden.bs.modal`, () => {
			if (null !== url_retrieve_id) location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-solicitudes-de-servicio';
		});
		$(`[name="vehiculo"]`).css(`display`, `none`).select2({
			theme: `bootstrap4`,
			minimumInputLength: 3,
			ajax: {
				url: `../wp-json/mopar-taller/v1/select2-property-for-leads`
			}
		})
		$('[name="fecha"]').datetimepicker({
			format: `YYYY-MM-DD`
		})
		$('[name="hora"]').datetimepicker({
			format: `LT`
		})

		$(".btnEdit").click(function() {
			solicitud_id = $(this).data('regid');
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=get_solicitud&solicitud_id=' + solicitud_id,
				beforeSend: function() {
					$(".overlay").show();
				},
				success: function(json) {

					$(".overlay").hide();
					$('#modalEditSolicitud [name=solicitud_id]').val(json.solicitud.id);

					$('[name=vehiculo]').empty();
					$.each(json.vehiculos, function(k, v) {
						$('[name=vehiculo]').append(new Option(v.street_address + " - " + v.address_line_2, v.id));
					})
					$("[name=vehiculo]").val(json.solicitud.vehiculo_id);
					$('#modalEditSolicitud [name=solicitud]').val(json.solicitud.solicitud);

					$('#modalEditSolicitud').modal('show');
				}
			})
		})

		if (null !== url_retrieve_id) {
			$(`#tabla_solicituds tbody tr[data-regid=${url_retrieve_id}] .btnEdit`).click()
		}

		if (location.hash == "#new") {
			$('#modalNewOT').modal('show');
		}

		$(".btnDelete").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Delete Lead!',
				content: '¿Are you sure you want to delete this lead?',
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
								data: 'action=eliminar_solicitud&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									$.alert({
										title: false,
										type: 'green',
										content: 'Lead Deleted'
									});
									tr.fadeOut(400);
								}
							})
						}
					}
				}
			});
		});

		$(`.btnMotivo`).click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');
			const modal = $(`#modalEditMotivo`)
			modal.find(`textarea`).val(``)
			modal.find(`[name="solicitud_id"]`).val(regid)
			modal.modal(`show`)
		})

		$(`.btnFecha`).click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');
			const modal = $(`#modalEditFecha`)
			modal.find(`input[type="text"]`).val(``)
			modal.find(`[name="solicitud_id"]`).val(regid)
			modal.modal(`show`)
		})

		$("#formNuevoSolicitud").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formNuevoSolicitud")[0].submit();
		});


		$("#formEditSolicitud").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formEditSolicitud")[0].submit();
		});

		$("#formEditMotivo").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formEditMotivo")[0].submit();
		});

		$("#formEditFecha").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formEditFecha")[0].submit();
		});

		<?php if ($inserted) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Processing Lead...'
			})
		<?php } ?>


		<?php if ($updated) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Processing lead...'
			})
		<?php } ?>

		<?php if ($inserted || $updated) { ?>
			location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-solicitudes-de-servicio';
		<?php } ?>


		$('#tabla_solicituds').DataTable({
			"scrollX": true,
			"ordering": false,
			"columnDefs": [{
				"width": "20%",
				"targets": 3
			}]
		});

	});
</script>

<?php include 'footer.php'; ?>