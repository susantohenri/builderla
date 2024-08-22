<?php
$inserted = false;
$updated = false;

if ($_POST) {
	global $wpdb;

	if ($_POST['action'] == 'editar_fecha') {

		$hora = explode(' ', $_POST['hora']);
		$am_pm = $hora[1];
		$time = explode(':', $hora[0]);
		$hour = $time[0];
		$minute = $time[1];
		if ('PM' == $am_pm) $hour += 12;
		$mysql_time = "{$hour}:{$minute}";

		$array_insert = [
			'fecha' => $_POST['fecha'],
			'hora' => $mysql_time,
		];

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

	if ($_POST['action'] == 'editar_solicitud') {
		$upload_dir = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/builderla/uploads/';
		$before_update = (array) Mopar::getOneSolicitud($_POST['solicitud_id']);

		$photos = json_decode($before_update['photos']);
		$remove_photos = explode('|', $_POST['delete_photos']);
		$photos = array_values(array_filter($photos, function ($photo) use ($remove_photos, $upload_dir) {
			if (in_array($photo, $remove_photos)) {
				if (file_exists(($upload_dir . $photo))) unlink($upload_dir . $photo);
				return false;
			} else return true;
		}));
		for ($index = 0; $index < count($_FILES['photos']['name']); $index++) {
			$tmp =  $_FILES['photos']['tmp_name'][$index];
			if ('' === $tmp) continue;

			// compress
			$img = imagecreatefromjpeg($tmp);
			$exif = exif_read_data($tmp);
			if ($img && $exif && isset($exif['Orientation']))
			{
				$ort = $exif['Orientation'];
				if ($ort == 6 || $ort == 5)
					$img = imagerotate($img, 270, 0);
				if ($ort == 3 || $ort == 4)
					$img = imagerotate($img, 180, 0);
				if ($ort == 8 || $ort == 7)
					$img = imagerotate($img, 90, 0);
				if ($ort == 5 || $ort == 4 || $ort == 7)
					imageflip($img, IMG_FLIP_HORIZONTAL);
			}
			imagejpeg($img, $tmp, 25);

			$name =  $_FILES['photos']['name'][$index];
			$name = rand() . $name;
			$location = $upload_dir . $name;
			if (move_uploaded_file($tmp, $location)) {
				$photos[] = $name;
			}
		}

		$array_update['photos'] = json_encode($photos);
		$array_update['details'] = $_POST['details'];

		$posted_attr = array_keys($array_update);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_update) $array_update['upddate'] = date('Y-m-d H:i:s');

		$wpdb->update('solicitud', $array_update, ['id' => $_POST['solicitud_id']]);
		$inserted = true;
	}
}
?>
<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Converted Leads </h2>
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
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->regdate_format; ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php if (0 != $solicitud->vehiculo_id) echo Mopar::getTitleVehiculo($solicitud->vehiculo_id) ?> </td>
						<td class="text-center">
							<?php $allow_init_estimate = false ?>
							<?php if (0 != $solicitud->ot_id) : ?>
								<a>
									<i class="fa fa-circle text-success"></i>
								</a>
							<?php elseif ('' == $solicitud->details) : ?>
								<a>
									<i class="fa fa-circle text-danger"></i>
								</a>
							<?php else : $allow_init_estimate = true; ?>
								<a>
									<i class="fa fa-circle text-warning"></i>
								</a>
							<?php endif; ?>
						</td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnFecha" data-regid="<?php echo $solicitud->id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/builderla/converted-lead-pdf.php?id=<?php echo $solicitud->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
							
							
							<button class="btn btn-warning btnAddDetails" data-regid="<?php echo $solicitud->id; ?>" data-toggle="tooltip" title="Add Details"><i class="fa fa-home"></i></button>
							
							<?php if ($allow_init_estimate): ?>
							<button class="btn btn-warning btnProceedWithoutIngreso" data-toggle="tooltip" title="Start Estimate"><i class="fa fa-list"></i></button>
							<?php endif ?>
							
							<button class="btn btn-danger btnCancelarCita" data-toggle="tooltip" title="Cancel Appointment"><i class="fa fa-reply"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<br>
		<ul>
			<li>
				<i class="fa fa-circle text-danger"></i> No details provided for this lead.
			</li>
			<li>
				<i class="fa fa-circle text-warning"></i> Lead details provided.
			</li>
			<li>
				<i class="fa fa-circle text-success"></i> Estimate ready for this lead.
			</li>
		</ul>
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
					<h5 class="modal-title">Edit Appointment</h5>
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

<!-- EDITAR Solicitud -->
<div class="modal fade" id="modalEditSolicitud" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditSolicitud" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_solicitud">
		<input type="hidden" name="solicitud_id" value="">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Details</h5>
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
								<select name="vehiculo" class="form-control" disabled>
									<option value="">Seleccione Cliente primero</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Details</span>
								</div>
								<textarea class="form-control" name="details"></textarea>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="preview-stored"></div>
							<div class="preview-uploaded"></div>
							<a href="javascript:;" class="btn addPhotos">
								<i class="fa fa-lg fa-camera"></i> &nbsp; Add Photos
							</a>
							<input type="file" name="photos[]" class="d-none" multiple accept="image/*">
							<input type="hidden" name="delete_photos">
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
				title: 'Cancel Appointment?',
				content: 'Would you like to cancel the appointment for this request and return it to the leads menu?',
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
										content: 'Appointment cancelled'
									});
									tr.fadeOut(400);
								}
							})
						}
					}
				}
			});
		});

		$(".btnAddDetails").click(function() {
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
					$('#modalEditSolicitud [name=details]').val(json.solicitud.details);

					$(`#modalEditSolicitud .preview-stored`).html(``)
					for (let stored_photo of JSON.parse(json.solicitud.photos)) {
						$(`#modalEditSolicitud .preview-stored`).append(`
							<div class="m-2 d-inline-block text-center">
								<img src="${json.upload_url}${stored_photo}" class="img-thumbnail">
								<br>
								<a href="javascript:;" class="delete-photo" data-name="${stored_photo}">
									<i class="fa fa-trash text-danger"></i>
								</a>
							</div>
						`)
					}

					$(`.delete-photo`).click(function () {
						const btn = $(this)
						const input_delete = $('#modalEditSolicitud [name="delete_photos"]')
						let images_to_delete = input_delete.val().split(`,`)
						images_to_delete.push(btn.data('name'))
						input_delete.val(images_to_delete.join(`|`))
						btn.parent().remove()
					})

					$('#modalEditSolicitud').modal('show');
				}
			})
		})

		$(`.addPhotos`).click(function () {
			const btn = $(this)
			const input_file = btn.siblings(`[type="file"]`)
			input_file.click()
		})

		$(`[name="photos[]"]`).change(function (e) {
			const input = $(this)
			const preview_placeholder = input.siblings(`.preview-uploaded`)
			preview_placeholder.html(``)
			for (let img of event.target.files) {
				const src = URL.createObjectURL(img)
				preview_placeholder.append(`<img src="${src}" class="m-2 img-thumbnail">`)
			}
		})

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
			"scrollX": true,
			"ordering": false,
			"columnDefs": [{
				"width": "20%",
				"targets": 3
			}]
		});

		$(".btnProceedWithoutIngreso").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Start Estimate',
				content: 'Do you want to start this estimate?',
				type: 'red',
				icon: 'fa fa-warning',
				buttons: {
					NO: {
						text: 'Cancel',
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
								data: 'action=proceed_solicitud_without_ingreso&regid=' + regid,
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
											content: 'Solicitud borrado correctamente'
										});
										window.location = `<?= site_url('wp-admin/admin.php?page=mopar-cotizaciones') ?>`
									}
								}
							})
						}
					}
				}
			});
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