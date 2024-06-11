<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Preparación Contable</h2>
		<form style="float: right;" method="POST">
			<select name="filter_month">
				<?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] as $month) : ?>
					<option value="<?= $month ?>" <?= $month == $filter_month ? 'selected' : '' ?>><?= Mopar::getNombreMes($month) ?></option>
				<?php endforeach ?>
			</select>
			<select name="filter_year">
				<?php for ($year = $min_year; $year <= $max_year; $year++) : ?>
					<option value="<?= $year ?>" <?= $year == $filter_year ? 'selected' : '' ?>><?= $year ?></option>
				<?php endfor ?>
			</select>
			<button>Filter</button>
			<button name="filter_reset">Reset</button>
		</form>
		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_solicituds">
			<thead>
				<tr>
					<th>#</th>
					<th> FECHA </th>
					<th> CLIENTE </th>
					<th> VEHICULO </th>
					<th> TOTAL </th>
					<th> GASTOS </th>
					<th> TIPO DE DOCUMENTO </th>
					<th class="text-center">ACCIONES</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->id; ?> </td>
						<td><?= date('d-m-Y', strtotime($solicitud->regdate)) ?></td>
						<td data-cliente="<?php echo $solicitud->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($solicitud->cliente_id, false) ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php if (0 != $solicitud->vehiculo_id) echo Mopar::getNombreVehiculo($solicitud->vehiculo_id) ?> </td>
						<td style="text-align: right;">$<?= number_format($solicitud->total, 0) ?></td>
						<td style="text-align: right;">$<?= number_format($solicitud->gastos, 0) ?></td>
						<td><?= $solicitud->tipo_de_documento ?></td>
						<td class="text-center" style="white-space: nowrap;">
							<button class="btn btn-warning <?= !empty($solicitud->expense) ? 'btn-retrieve-expense' : '' ?>" data-toggle="tooltip"><i class="fa fa-list"></i></button>
							<button class="btn btn-success btn-add-expense" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="modalAddExpense" tabindex="-1" role="dialog" aria-labelledby="addExpenseLabel" aria-hidden="true">
	<form method="POST">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">AGREGAR GASTO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Proveedor</span>
								</div>
								<input type="text" name="proveedor" class="form-control" required>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Monto</span>
								</div>
								<input type="text" name="monto" class="form-control currency" required>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">detalle</span>
								</div>
								<textarea class="form-control" name="detalle" required></textarea>
							</div>
						</div>
						<div class="form-group col-md-1">
						</div>
						<div class="form-group col-md-11">
							<?php foreach (['FACTURA', 'BOLETA', 'SIN COMPROBANTE'] as $tipo_de_documento) : ?>
								<br><input type="radio" name="tipo_de_documento" value="<?= $tipo_de_documento ?>" required><?= $tipo_de_documento ?>
							<?php endforeach ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="solicitud_id">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success" name="add_expense">Guardar <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="modalRetrieveExpense" tabindex="-1" role="dialog" aria-labelledby="retrieveExpenseLabel" aria-hidden="true">
	<form method="POST">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">GASTOS</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-12">
							<table class="table">
								<thead>
									<tr>
										<th> DETALLE </th>
										<th> MONTO </th>
										<th></th>
									</tr>
								</thead>
								<tbody class="bg-light">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="solicitud_id">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success" name="rewrite_expense">Guardar <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="modalEditExpense" tabindex="-1" role="dialog" aria-labelledby="editExpenseLabel" aria-hidden="true">
	<form method="POST">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">AGREGAR GASTO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Proveedor</span>
								</div>
								<input type="text" name="proveedor" class="form-control" required>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Monto</span>
								</div>
								<input type="text" name="monto" class="form-control currency" required>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">detalle</span>
								</div>
								<textarea class="form-control" name="detalle" required></textarea>
							</div>
						</div>
						<div class="form-group col-md-1">
						</div>
						<div class="form-group col-md-11">
							<?php foreach (['FACTURA', 'BOLETA', 'SIN COMPROBANTE'] as $tipo_de_documento) : ?>
								<br><input type="radio" name="tipo_de_documento" value="<?= $tipo_de_documento ?>" required><?= $tipo_de_documento ?>
							<?php endforeach ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="solicitud_id">
					<input type="hidden" name="expense_index">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success" name="edit_expense">Guardar <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function() {
		<?php if (!empty($alert)) : ?>
			$.alert({
				title: false,
				type: '<?= $alert['type'] ?>',
				content: '<?= $alert['content'] ?>'
			});
		<?php endif ?>

		$('#tabla_solicituds').DataTable({
			"ordering": false,
			"columnDefs": [{
				"width": "10%",
				"targets": 1
			}, {
				"width": "20%",
				"targets": 3
			}, {
				"width": "15%",
				"targets": 6
			}]
		});

		const add_expense_modal = $(`#modalAddExpense`)
		const retrieve_expense_modal = $(`#modalRetrieveExpense`)
		const edit_expense_modal = $(`#modalEditExpense`)

		function activateCurrencyFormat() {
			$(`.currency`).each(function() {
				$(this)
					.off(`keyup.currency`)
					.on(`keyup.currency`, function() {
						var typed = $(this).val()
						typed = typed.replace(`$`, ``).replace(`,`, ``)
						typed = parseInt(typed)
						if (isNaN(typed)) typed = 0
						typed = typed.toString()
						typed = typed.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
						$(this).val(`$${typed}`)
					})
					.trigger(`keyup.currency`)

			})
		}
		activateCurrencyFormat()

		$(`.btn-add-expense`).click(function() {
			add_expense_modal.find(`[type=text], textarea`).val(``)
			add_expense_modal.find(`[type=radio]`).prop(`checked`, false)
			const solicitud_id = $(this).parents(`tr`).attr(`data-regid`)
			add_expense_modal.find(`[name="solicitud_id"]`).val(solicitud_id)
			add_expense_modal.modal(`show`)
		})

		$(`form`).submit(() => {
			$(`.currency`).each(function() {
				$(this).val($(this).val().replace(`$`, ``).replace(`,`, ``))
			})
			return true
		})

		$(`.btn-retrieve-expense`).click(function() {
			const solicitud_id = $(this).parents(`tr`).attr(`data-regid`)
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
					retrieve_expense_modal.find(`[name="solicitud_id"]`).val(solicitud_id)
					json.solicitud.expense = JSON.parse(json.solicitud.expense)
					retrieve_expense_modal.find(`tbody`).html(``)
					for (var index in json.solicitud.expense) {
						const row = json.solicitud.expense[index]
						retrieve_expense_modal.find(`tbody`).append(`
							<tr>
								<td>
									<input type="hidden" name="expense[proveedor][]" value="${row.proveedor}">
									<input type="hidden" name="expense[tipo_de_documento][]" value="${row.tipo_de_documento}">
									<input type="text" name="expense[detalle][]" value="${row.detalle}" class="form-control" required>
								</td>
								<td class="">
									<input type="text" name="expense[monto][]" value="${row.monto}" class="form-control currency text-right" required>
								</td>
								<td>
									<a class="btn" href="" data-expense-index="${index}"><i class="fa fa-pencil text-warning"></i></a>
									<a class="btn btn-danger btn-sm" href=""><i class="fa fa-minus"></i></a>
									<a class="btn btn-info btn-sm" href=""><i class="fa fa-arrow-up"></i></a>
									<a class="btn btn-info btn-sm" href=""><i class="fa fa-arrow-down"></i></a>
								</td>
							</tr>
						`)
					}
					activateCurrencyFormat()
					retrieve_expense_modal.find(`.fa-minus`).parent().click(function(event) {
						event.preventDefault()
						$(this).parents(`tr`).remove()
					})
					retrieve_expense_modal.find(`.fa-arrow-up`).parent().click(function(event) {
						event.preventDefault()
						tr = $(this).closest('tr')
						tr.insertBefore(tr.prev())
					})
					retrieve_expense_modal.find(`.fa-arrow-down`).parent().click(function(event) {
						event.preventDefault()
						tr = $(this).closest('tr')
						tr.insertAfter(tr.next())
					})
					retrieve_expense_modal.find(`.fa-pencil`).parent().click(function(event) {
						event.preventDefault()
						const index = $(this).attr(`data-expense-index`)
						edit_expense_modal.find(`[name="proveedor"]`).val(json.solicitud.expense[index].proveedor)
						edit_expense_modal.find(`[name="monto"]`).val(json.solicitud.expense[index].monto).trigger(`keyup`)
						edit_expense_modal.find(`[name="detalle"]`).val(json.solicitud.expense[index].detalle)
						edit_expense_modal.find(`[name="tipo_de_documento"][value="${json.solicitud.expense[index].tipo_de_documento}"]`).prop(`checked`, true)
						edit_expense_modal.find(`[name="solicitud_id"]`).val(solicitud_id)
						edit_expense_modal.find(`[name="expense_index"]`).val(index)
						edit_expense_modal.modal(`show`)
					})
					retrieve_expense_modal.modal(`show`)
				}
			})
		})
	});
</script>

<?php include 'footer.php'; ?>