<?php
$folder = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/builderla/uploads/';
$inserted = false;
$updated = false;

if ($_POST) {
	global $wpdb;

	$array_insert = [
		'titulo' => $_POST['titulo'],
		'detalle' => json_encode($_POST['detalle']),
		'valor' => $_POST['valor'],
		'estado' => 1,
	];
	if (isset($_POST['cliente'])) $array_insert['cliente_id'] = $_POST['cliente'];
	if (isset($_POST['vehiculo'])) $array_insert['vehiculo_id'] = $_POST['vehiculo'];

	if ($_POST['action'] == 'insertar_ot') {
		if ($wpdb->insert('ot', $array_insert)) {
			$inserted = true;
		}
	}

	if ($_POST['action'] == 'editar_ot') {
		$before_update = (array) Mopar::getOneOt($_POST['ot_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) $array_insert['upddate'] = date('Y-m-d H:i:s');

		if ($wpdb->update('ot', $array_insert, ['id' => $_POST['ot_id']])) {
			Mopar::solicitudCalculateSelling($_POST['ot_id']);
			$updated = true;
		}
	}
}
?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left">Estimates </h2>
		<!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewOT">Nueva Cotización</button> -->

		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_ots">
			<thead>
				<tr>
					<th>#</th>
					<th> Title </th>
					<th> Customer </th>
					<th> Address </th>
					<th> Total </th>
					<th> Status </th>
					<th class="text-center">Options</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ots as $ot) : ?>
					<tr data-regid="<?php echo $ot->id; ?>">
						<td data-regid="<?php echo $ot->id; ?>"> <?php echo $ot->id; ?> </td>
						<td data-titulo="<?php echo $ot->titulo; ?>"> <?php echo $ot->titulo; ?> </td>
						<td data-cliente="<?php echo $ot->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($ot->cliente_id) ?> </td>
						<td data-vehiculo="<?php echo $ot->vehiculo_id; ?>"> <?php echo Mopar::getNombreVehiculo($ot->vehiculo_id) ?> </td>
						<td data-valor="<?php echo $ot->valor; ?>"> $ <?php echo number_format($ot->valor, 0, ',', '.') ?> </td>
						<td data-estado="<?php echo $ot->estado; ?>" class="text-center align-middle">
							<?php if (3 == $ot->solicitud_estado) : ?>
								<a>
									<i class="fa fa-circle text-warning"></i>
								</a>
							<?php elseif (4 == $ot->solicitud_estado) : ?>
								<a>
									<i class="fa fa-circle text-danger"></i>
								</a>
							<?php elseif (1 == $ot->estado) : ?>
								<a>
									<i class="fa fa-circle text-danger"></i>
								</a>
							<?php elseif (2 == $ot->estado) : ?>
								<a>
									<i class="fa fa-circle text-success"></i>
								</a>
							<?php endif; ?>
						</td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $ot->id; ?>" data-toggle="tooltip" title="Editar"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/builderla/pdf.php?id=<?php echo $ot->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="Ver"><i class="fa fa-search"></i></a>
							<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar"><i class="fa fa-trash-o"></i></button>
							<button class="btn btn-warning btnComplete" data-toggle="tooltip" title="Finalizar Trabajo"><i class="fa fa-check"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<br>
		<ul>
			<li>
				<i class="fa fa-circle text-success"></i> This estimate was accepted and signed.
			</li>
			<li>
			<!--	<i class="fa fa-circle text-warning"></i> Esta cotización no tiene una orden de ingreso
			-->
			</li>
			<li>
				<i class="fa fa-circle text-danger"></i> There are no actions for this estimate
			</li>
		</ul>
	</div>
</div>




<!-- Nuevo OT -->
<div class="modal fade" id="modalNewOT" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoOT" enctype="multipart/form-data">
		<input type="hidden" name="action" value="insertar_ot">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Datos de la Cotización</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Cliente</span>
								</div>
								<select name="cliente" class="form-control">
									<option value="">Seleccione</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Vehiculo</span>
								</div>
								<select name="vehiculo" class="form-control" disabled required>
									<option value="">Seleccione Cliente primero</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Titulo Descriptivo</span>
								</div>
								<input type="text" name="titulo" class="form-control" required>
							</div>
						</div>
						<div class="form-group col-md-12">
							<table class="table">
								<thead>
									<tr>
										<th> Detalle </th>
										<th> Precio </th>
										<th></th>
									</tr>
								</thead>
								<tbody class="bg-light">
									<tr>
										<td class="">
											<input type="text" name="detalle[item][]" class="form-control" required>
										</td>
										<td class="">
											<input type="text" name="detalle[precio][]" class="form-control precio text-right" required>
										</td>
										<td></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="3"><button type="button" class="btn btn-success float-right btn-sm btnPlus" data-toggle="tooltip" title="Agregar linea de detalle"><i class="fa fa-plus"></i></button></th>
									</tr>
								</tfoot>
							</table>
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
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success btnGuardar">Guardar <i class="fa fa-save"></i> </button>
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
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Datos de la Cotización</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Cliente</span>
								</div>
								<select name="cliente" class="form-control" disabled>
									<option value="">Seleccione</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Vehiculo</span>
								</div>
								<select name="vehiculo" class="form-control" disabled required>
									<option value="">Seleccione Cliente primero</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Titulo Descriptivo</span>
								</div>
								<input type="text" name="titulo" class="form-control" required>
							</div>
						</div>
						<div class="form-group col-md-12">
							<table class="table">
								<thead>
									<tr>
										<th> Detalle </th>
										<th> Precio </th>
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
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success btnGuardar">Guardar <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>




<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$(`[name="cliente"]`).css(`display`, `none`).select2({
			theme: `bootstrap4`,
			minimumInputLength: 3,
			ajax: {
				url: `../wp-json/mopar-taller/v1/clientes`
			}
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

					$('#modalEditOT [name=cliente]').html(`<option value="${json.ot.cliente_id}" selected>${json.cliente.nombres} ${json.cliente.apellidoPaterno}</option>`)

					$('[name=vehiculo]').empty();
					$('[name=vehiculo]').append(new Option('Seleccione Vehiculo', ''));
					$.each(json.vehiculos, function(k, v) {
						$('[name=vehiculo]').append(new Option(v.street_address + " - " + v.address_line_2, v.id));
					})
					$("[name=vehiculo]").val(json.ot.vehiculo_id);

					$('#modalEditOT [name=titulo]').val(json.ot.titulo);

					$("#modalEditOT table tbody").empty();
					$.each(detalle.item, function(k, v) {
						h = '<tr data-row-num="'+k+'a">';
						h += '	<td>';
						h += '		<input type="text" value="' + detalle.item[k] + '" name="detalle[item][]" class="form-control" required>';
						h += '	</td>';
						h += '	<td>';
						h += '		<input type="text" value="' + detalle.precio[k] + '" name="detalle[precio][]" class="form-control precio text-right" value="0" required>';
						h += '	</td>';
						h += `	<td>
								<a href="#" data-toggle="tooltip" title="Borra Linea" class="btn btn-danger btn-sm btnLess"><i class="fa fa-minus"></i></a>
								<a href="#" class="btn btn-info btn-sm btnUp"><i class="fa fa-arrow-up"></i></a>
								<a href="#" class="btn btn-info btn-sm btnDown"><i class="fa fa-arrow-down"></i></a>
							</td>`;
						h += '</tr>';
						h += `
							<tr data-row-num="${k}b">
								<td colspan="2">
									<textarea name="detalle[observaciones][]" class="form-control">${detalle.observaciones[k]}</textarea>'
								</td>
								<td></td>
							</tr>
						`
						$("#modalEditOT table tbody").append(h);
						$("[data-toggle=tooltip]").tooltip();
						$('.tooltip').hide();
						recalcular();
					})

					$('#modalEditOT [name=valor]').val(json.ot.valor);

					$('#modalEditOT').modal('show');
				}
			})
		})

		if (location.hash == "#new") {
			$('#modalNewOT').modal('show');
		}

		$(document).on('keyup', '.precio', function(e) {
			recalcular();
		})

		$(document).on('click', '.btnLess', function(e) {
			e.preventDefault();
			tr = $(this).closest('tr');
			tr.fadeOut(300, function() {
				tr.next().remove()
				tr.remove();
				recalcular();
			})
			$("[data-toggle=tooltip]").tooltip();
			$('.tooltip').hide();
		})

		$(document).on('click', '.btnUp', function(e) {
			e.preventDefault();
			const tr = $(this).closest('tr');
			const prev = tr.prev()

			if (1 > prev.length) return false

			const target_row_num = prev.attr(`data-row-num`).replace(`b`, `a`)
			const target = tr.siblings(`tr[data-row-num="${target_row_num}"]`)

			const observaciones_num = tr.attr(`data-row-num`).replace(`a`, `b`)
			const observaciones = tr.siblings(`tr[data-row-num="${observaciones_num}"]`)

			tr.insertBefore(target)
			observaciones.insertBefore(target)
		})

		$(document).on('click', '.btnDown', function(e) {
			e.preventDefault();
			const tr = $(this).closest('tr');
			const next = tr.next().next()

			if (1 > next.length) return false

			const target_row_num = next.attr(`data-row-num`).replace(`a`, `b`)
			const target = tr.siblings(`tr[data-row-num="${target_row_num}"]`)

			const observaciones_num = tr.attr(`data-row-num`).replace(`a`, `b`)
			const observaciones = tr.siblings(`tr[data-row-num="${observaciones_num}"]`)

			observaciones.insertAfter(target)
			tr.insertAfter(target)
		})

		$(".btnPlus").click(function(e) {
			e.preventDefault();
			const last_row_num = jQuery(this).parent().parent().parent().parent().find(`tbody`).find(`tr`).last().attr(`data-row-num`).replace(`b`, ``)
			const new_row_num = parseInt(last_row_num) + 1

			h = '';
			h += '<tr data-row-num="'+new_row_num+'a">';
			h += '	<td>';
			h += '		<input type="text" name="detalle[item][]" class="form-control" required>';
			h += '	</td>';
			h += '	<td>';
			h += '		<input type="text" name="detalle[precio][]" class="form-control precio text-right" value="0" required>';
			h += '	</td>';
			h += `	<td>
					<a href="#" data-toggle="tooltip" title="Borra Linea" class="btn btn-danger btn-sm btnLess"><i class="fa fa-minus"></i></a>
					<a href="#" class="btn btn-info btn-sm btnUp"><i class="fa fa-arrow-up"></i></a>
					<a href="#" class="btn btn-info btn-sm btnDown"><i class="fa fa-arrow-down"></i></a>
				</td>`;
			h += '</tr>';
			h+= `
				<tr data-row-num="${new_row_num}b">
					<td colspan="2">
						<textarea name="detalle[observaciones][]" class="form-control"></textarea>
					</td>
					<td></td>
				</tr>
			`
			$(this).closest('.modal').find('table tbody').append(h)
			$("[data-toggle=tooltip]").tooltip();
			$('.tooltip').hide();
			recalcular();
		})

		$("[name=cliente]").change(function() {
			cliente_id = $(this).val();
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=get_vehiculos_by_cliente&cliente_id=' + cliente_id,
				beforeSend: function() {
					$("[name=vehiculo]").html('<option value="">Cargando Vehiculos...</option>');
				},
				success: function(json) {
					$('[name=vehiculo]').empty();
					$('[name=vehiculo]').append(new Option('Seleccione Vehiculo', ''));
					$.each(json.vehiculos, function(k, v) {
						$('[name=vehiculo]').append(new Option(v.street_address + " - " + v.address_line_2, v.id));
					})
					$("#modalNewOT [name=vehiculo]").removeAttr('disabled');
				}
			})
		})

		$(".btnDelete").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Eliminar Cotización!',
				content: '¿Desea eliminar la cotizacion seleccionada?',
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
								data: 'action=eliminar_ot&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									$.alert({
										title: false,
										type: 'green',
										content: 'Cotización borrada correctamente'
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
				title: 'Trabajo finalizado',
				content: '¿Quiere completar el trabajo para esta cotizacion?',
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
								data: 'action=completar_ot&regid=' + regid,
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
											content: 'Cotizacion borrada correctamente'
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
				content: 'OT ingresada correctamente'
			})
		<?php } ?>


		<?php if ($updated) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'OT actualizada correctamente'
			})
		<?php } ?>

		<?php if ($inserted || $updated) { ?>
			location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-cotizaciones';
		<?php } ?>


		$('#tabla_ots').DataTable({
			"ordering": false
		});
	});

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
</script>

<?php include 'footer.php'; ?>