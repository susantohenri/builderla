<?php
$inserted = false;
$updated = false;

if ($_POST) {
	global $wpdb;

	$array_insert = [
		'fecha' => $_POST['fecha'],
		'hora' => $_POST['hora'],
	];

	if ($_POST['action'] == 'editar_fecha') {
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
}
?>
<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Solicitudes Agendadas </h2>
		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_solicituds">
			<thead>
				<tr>
					<th>#</th>
					<th> Cliente </th>
					<th> Vehiculo </th>
					<th class="text-center">Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->id; ?> </td>
						<td data-cliente="<?php echo $solicitud->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($solicitud->cliente_id, false) ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php if (0 != $solicitud->vehiculo_id) echo Mopar::getNombreVehiculo($solicitud->vehiculo_id) ?> </td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnFecha" data-regid="<?php echo $solicitud->id; ?>" data-toggle="tooltip" title="Editar"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/solicitud-pdf.php?id=<?php echo $solicitud->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="Ver"><i class="fa fa-search"></i></a>
							<button class="btn btn-warning btnComplete" data-toggle="tooltip" title="Ingresar a Taller"><i class="fa fa-wrench"></i></button>
							<button class="btn btn-danger btnCancelarCita" data-toggle="tooltip" title="Cancelar Cita"><i class="fa fa-reply"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- EDITAR Fecha -->
<div class="modal fade" id="modalEditFecha" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditFecha" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_fecha">
		<input type="hidden" name="solicitud_id" value="">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Editar la fecha agendada</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Fecha</span>
								</div>
								<input type="text" class="form-control" name="fecha" required>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Hora</span>
								</div>
								<input type="text" class="form-control" name="hora" required>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success btnGuardar">Guardar <i class="fa fa-save"></i> </button>
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
		$('[name="fecha"]').datetimepicker({
			format: `YYYY-MM-DD`
		})
		$('[name="hora"]').datetimepicker({
			format: `LT`
		})

		$(`.btnFecha`).click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');
			const modal = $(`#modalEditFecha`)
			modal.find(`input[type="text"]`).val(``)
			modal.find(`[name="solicitud_id"]`).val(regid)
			modal.modal(`show`)
		})

		$("#formEditFecha").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formEditFecha")[0].submit();
		});

		$(".btnCancelarCita").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Cancelar Cita?',
				content: '¿Desea Cancelar la cita para esta solicitud y regresarla al menú solicitudes de servicio?',
				type: 'red',
				icon: 'fa fa-warning',
				buttons: {
					NO: {
						text: 'No',
						btnClass: 'btn-red',
					},
					SI: {
						text: 'Si',
						btnClass: 'btn-green',
						action: function() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType: 'json',
								data: 'action=cancelar_cita_solicitud&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									$.alert({
										title: false,
										type: 'green',
										content: 'Solicitud restaurada'
									});
									tr.fadeOut(400);
								}
							})
						}
					}
				}
			});
		});

		$(".btnComplete").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Completar Solicitud',
				content: '¿Quiere ingresar a taller esta solicitud?',
				type: 'green',
				icon: 'fa fa-success',
				buttons: {
					NO: {
						text: 'Cancelar',
						btnClass: 'btn-red',
					},
					SI: {
						text: 'Si',
						btnClass: 'btn-green',
						action: function() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType: 'json',
								data: 'action=completar_solicitud&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									if (`ERROR` === json.status) {
										$.alert({
											title: false,
											type: 'red',
											content: json.message
										});
									} else {
										$.alert({
											title: false,
											type: 'green',
											content: 'Procesando...'
										});
										window.location.reload()
									}
								}
							})
						}
					}
				}
			});
		});

		$('#tabla_solicituds').DataTable({
			"ordering": false,
			"columnDefs": [{
				"width": "20%",
				"targets": 3
			}]
		});

		<?php if ($updated) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Solicitud actualizada correctamente'
			})
		<?php } ?>

		<?php if ($inserted || $updated) { ?>
			location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-agendadas';
		<?php } ?>
	});
</script>

<?php include 'footer.php'; ?>