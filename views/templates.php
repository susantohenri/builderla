<?php
$inserted = false;
$updated = false;
$deleted = false;
$error_message = false;

global $wpdb;
if (isset($_POST['action'])) switch ($_POST['action']) {
	case 'insertar_template':
		if ($wpdb->insert('template', [
			'titulo' => isset($_POST['titulo']) ? strtoupper($_POST['titulo']) : '',
			'detalle' => isset($_POST['detalle']) ? json_encode($_POST['detalle']) : '{"item":[""],"precio":[""], "observaciones":[""]}',
			'valor' => isset($_POST['valor']) ? $_POST['valor'] : '',
			'site_services' => isset($_POST['cb']['site_services']) ? $_POST['site_services'] : '',
			'customer_to_provide' => isset($_POST['cb']['customer_to_provide']) ? $_POST['customer_to_provide'] : '',
			'not_included' => isset($_POST['cb']['not_included']) ? $_POST['not_included'] : '',
			'price_breakdown' => isset($_POST['cb']['price_breakdown']) ? '1' : '0'
		])) $inserted = true;
		break;
	case 'editar_template':
		if ($wpdb->update('template', [
			'titulo' => isset($_POST['titulo']) ? strtoupper($_POST['titulo']) : '',
			'detalle' => isset($_POST['detalle']) ? json_encode($_POST['detalle']) : '{"item":[""],"precio":[""], "observaciones":[""]}',
			'valor' => isset($_POST['valor']) ? $_POST['valor'] : '',
			'site_services' => isset($_POST['cb']['site_services']) ? $_POST['site_services'] : '',
			'customer_to_provide' => isset($_POST['cb']['customer_to_provide']) ? $_POST['customer_to_provide'] : '',
			'not_included' => isset($_POST['cb']['not_included']) ? $_POST['not_included'] : '',
			'price_breakdown' => isset($_POST['cb']['price_breakdown']) ? '1' : '0'
		], ['id' => $_POST['template_id']])) $updated = true;
		break;
	case 'eliminar_template':
		if ($wpdb->delete('template', ['id' => $_POST['template_id']])) $deleted = true;
		break;
}
$templates = Mopar::getTemplates();
?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left">Templates </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewTemplate">New Template</button>
		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_templates" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th> Name </th>
					<th> Portable Toilet </th>
					<th> Customer to Provide </th>
					<th> Not Included </th>
					<th> Price Breakdown </th>
					<th> Total </th>
					<th class="text-center">Options</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($templates as $template) : ?>
					<tr data-regid="<?= $template->id; ?>">
						<td> <?= $template->id; ?> </td>
						<td><?= $template->titulo; ?></td>
						<td><?= '' != $template->site_services ? 'Yes' : 'No'; ?></td>
						<td><?= '' != $template->customer_to_provide ? 'Yes' : 'No'; ?></td>
						<td><?= '' != $template->not_included ? 'Yes' : 'No'; ?></td>
						<td><?= 1 == $template->price_breakdown ? 'Yes' : 'No'; ?></td>
						<td> $ <?= number_format($template->valor, 0, ',', '.') ?> </td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?= $template->id; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button>
							<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- Nuevo Templates -->
<div class="modal fade" id="modalNewTemplate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoTemplate" enctype="multipart/form-data">
		<input type="hidden" name="action" value="insertar_template">
		<input type="hidden" name="template_id" value="">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Template</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
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
						<div class="form-group col-md-12 hidden">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Site Services</span>
								</div>
								<input type="text" name="site_services" readonly class="form-control">
							</div>
						</div>
						<div class="form-group col-md-12">
							<table class="table table-template">
								<thead>
									<tr>
										<th> Details </th>
										<th> Price </th>
										<th></th>
									</tr>
								</thead>
								<tbody class="bg-light">
									<tr data-row-num="0a">
										<td> <input type="text" value="" name="detalle[item][]" class="form-control" required=""> </td>
										<td> <input type="text" value="" name="detalle[precio][]" class="form-control precio text-right" required="">
										</td>
									</tr>
									<tr data-row-num="0b">
										<td colspan="2">
											<input type="text" class="form-control observaciones"
												placeholder="write the details and press Enter to add it to the estimate">
											<textarea rows="1" name="detalle[observaciones][]" class="form-control observaciones"></textarea>'
										</td>
									</tr>

									<tr data-row-num="0c">
										<td colspan="2" class="text-right">
											<a href="#" data-toggle="tooltip" title="" class="btn btn-danger btn-sm btnLess"
												data-original-title="Borra Linea"><i class="fa fa-minus"></i></a>
											<a href="#" class="btn btn-info btn-sm btnUp"><i class="fa fa-arrow-up"></i></a>
											<a href="#" class="btn btn-info btn-sm btnDown"><i class="fa fa-arrow-down"></i></a>
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="3"><button type="button" class="btn btn-success float-right btn-sm btnPlus" data-toggle="tooltip" title="Agregar linea de detalle"><i class="fa fa-plus"></i></button></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="form-group col-md-12 hidden">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Customer to Provide</span>
								</div>
								<input type="text" name="feeder[customer_to_provide]" class="form-control">
							</div>
							<textarea name="customer_to_provide" class="form-control customer_to_provide"></textarea>
						</div>
						<div class="form-group col-md-12 hidden">
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

<!-- EDITAR Template -->
<div class="modal fade" id="modalEditTemplate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditTemplate" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_template">
		<input type="hidden" name="template_id" value="">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Template</h5>
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
							<table class="table table-template">
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

<script>
	$(document).ready(function() {
		jQuery(`#modalNewTemplate`).on(`hidden.bs.modal`, e => {
			const formCreate = jQuery(`#modalNewTemplate`)
			formCreate.find(`textarea, [type="text"]`).val(``)
			formCreate.find(`:checkbox:checked`).click()
			formCreate.find(`tbody tr`).not(`[data-row-num]`).remove()
		})

		$(".btnEdit").click(function() {
			template_id = $(this).data('regid');
			$.ajax({
				type: 'POST',
				url: '<?= admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=get_template&template_id=' + template_id,
				beforeSend: function() {
					$(".overlay").show();
				},
				success: function(json) {
					const formEdit = jQuery(`#formEditTemplate`)
					detalle = JSON.parse(json.detalle);

					$(".overlay").hide();
					formEdit.find('[name=template_id]').val(json.id);

					formEdit.find(`[name="site_services"]`).val(json.site_services)
					formEdit.find(`[name="cb[site_services]"]`).attr(`checked`, `` != json.site_services).trigger(`change`)

					formEdit.find(`[name="customer_to_provide"]`).val(json.customer_to_provide).attr(`rows`, json.customer_to_provide.split(`\n`).length + 1)
					formEdit.find(`[name="cb[customer_to_provide]"]`).attr(`checked`, `` != json.customer_to_provide).trigger(`change`)

					formEdit.find(`[name="not_included"]`).val(json.not_included).attr(`rows`, json.not_included.split(`\n`).length + 1)
					formEdit.find(`[name="cb[not_included]"]`).attr(`checked`, `` != json.not_included).trigger(`change`)

					formEdit.find(`[name="cb[price_breakdown]"]`).attr(`checked`, 1 == json.price_breakdown)

					formEdit.find('[name=titulo]').val(json.titulo);

					formEdit.find('table tbody').empty();
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
						$("#modalEditTemplate table tbody").append(h);
						formEdit.find("[data-toggle=tooltip]").tooltip();
						formEdit.find('.tooltip').hide();
					})
					recalcular()
					rewrite_row_num()

					formEdit.find('[name=valor]').val(json.valor);

					$('#modalEditTemplate').modal('show');
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

			tr.parent().find(`[data-row-num^=${row_num}]`).insertBefore(jQuery(`[data-row-num="${row_num - 1}a"]`))
			rewrite_row_num()
		})

		$(document).on('click', '.btnDown', function(e) {
			e.preventDefault();
			const tr = $(this).closest('tr');
			const row_num = parseInt(tr.attr(`data-row-num`).replace(`c`, ``))
			const next_row = tr.parent().find(`[data-row-num="${row_num + 1}c"]`)

			if (1 > next_row.length) return false
			tr.parent().find(`[data-row-num^="${row_num}"]`).insertAfter(next_row)
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
				title: 'Delete Template!',
				content: 'Do you want to delete the selected template?',
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
							jQuery(`body`).append(`
								<form method="POST" name="form-delete-template">
									<input type="hidden" name="action" value="eliminar_template">
									<input type="hidden" name="template_id" value="${regid}">
								</form>
							`)
							jQuery(`[name="form-delete-template"]`).submit()
						}
					}
				}
			});
		});

		$("#formNuevoTemplate").submit(function(e) {
			$(".overlay").show();
			recalcular();
			e.preventDefault();
			$("#formNuevoTemplate")[0].submit();
		});

		$("#formEditTemplate").submit(function(e) {
			$(".overlay").show();
			recalcular();
			e.preventDefault();
			$("#formEditTemplate")[0].submit();
		});

		<?php if ($inserted) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Template created'
			})
		<?php } ?>


		<?php if ($updated) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Template updated'
			})
		<?php } ?>

		<?php if ($deleted) { ?>
			$.alert({
				title: false,
				type: 'green',
				content: 'Template deleted',
				buttons: {
					OK: () => {
						location.reload()
					}
				}
			});
		<?php } ?>

		<?php if ('' != $error_message) { ?>
			$.alert({
				type: 'red',
				title: false,
				content: '<?= $error_message ?>'
			})
		<?php } ?>

		$('#tabla_templates').DataTable({
			"scrollX": true,
			"ordering": false
		});
	});

	function rewrite_row_num() {
		let number = 0
		const letter = [`a`, `b`, `c`]
		let current_letter_index = 0
		jQuery(`.table-template tbody tr`).each((index, tr) => {
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