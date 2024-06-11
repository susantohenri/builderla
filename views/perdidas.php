<?php
$inserted = false;
$updated = false;
?>
<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Solicitudes Perdidas </h2>
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
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/solicitud-pdf.php?id=<?php echo $solicitud->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="Ver"><i class="fa fa-search"></i></a>
							<button class="btn btn-danger btnRestaurar" data-toggle="tooltip" title="Restaurar"><i class="fa fa-reply"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function() {

		$(".btnRestaurar").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Restaurar Solicitud?',
				content: '¿Desea restaurar la solicitud de servicio de esta solicitud perdida?',
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
								data: 'action=restaurar_solicitud&regid=' + regid,
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

		$('#tabla_solicituds').DataTable({
			"ordering": false
		});
	});
</script>

<?php include 'footer.php'; ?>