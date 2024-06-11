<?php include 'header.php'; ?>

<?php 
if( $_GET['vid'] ){
	include 'ots_by_vehiculo.php';
} else {
?>

<div class="box pr-4">

	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Lista de Vehiculos </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewVehiculo">Nuevo Vehiculo</button>

		<div class="clearfix"></div>
	</div>

	<table class="table table-striped table-bordered" id="tabla_vehiculos">
		<thead>
			<tr>
				<th> # </th>
				<th> Patente </th>
				<th> Marca </th>
				<th> Modelo </th>
				<th> Año </th>
				<th> Color </th>
				<th> VIN </th>
				<th> Cliente </th>
				<th class="text-center">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vehiculos as $key => $vehiculo): ?>
			<tr data-regid="<?php echo $vehiculo->id ?>" data-modelo_id="<?php echo $vehiculo->modelo_id ?>">
				<td data-regid="<?php echo $vehiculo->id ?>"> <?php echo $vehiculo->id ?> </td>
				<td data-patente="<?php echo $vehiculo->patente ?>"> <?php echo $vehiculo->patente ?> </td>
				<td data-marca="<?php echo $vehiculo->marca ?>"> <?php echo $vehiculo->marca ?> </td>
				<td data-modelo="<?php echo $vehiculo->modelo ?>"> <?php echo $vehiculo->modelo ?> </td>
				<td data-ano="<?php echo $vehiculo->ano ?>"> <?php echo $vehiculo->ano ?> </td>
				<td data-color="<?php echo $vehiculo->color ?>"> <?php echo $vehiculo->color ?> </td>
				<td data-nro_motor="<?php echo $vehiculo->nro_motor ?>"> <?php echo $vehiculo->nro_motor ?> </td>
				<td data-cliente_id="<?php echo $vehiculo->cliente_id ?>"> <?php echo Mopar::getNombreCliente($vehiculo->cliente_id) ?> </td>
				
				<td class="text-center">
					<button class="btn btn-success btnEdit" data-toggle="tooltip" title="Editar Vehiculo"><i class="fa fa-pencil"></i></button>
					<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar Vehiculo"><i class="fa fa-trash-o"></i></button>
					<a href="admin.php?page=mopar-vehiculos&vid=<?php echo $vehiculo->id ?>" class="btn btn-info" data-toggle="tooltip" title="Ver OTs del Vehiculo"><i class="fa fa-search"></i></a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

</div>

<!-- Nuevo Vehiculo -->
<div class="modal fade" id="modalNewVehiculo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoVehiculo">
		<input type="hidden" name="action" value="insertar_vehiculo">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Datos del Vehiculo</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
        			<div class="form-row">
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Patente</span>
						        </div>
						        <input type="text" name="patente" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Año</span>
						        </div>
						        <input type="text" name="ano" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Marca</span>
						        </div>
						        <input type="text" name="marca" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Modelo</span>
						        </div>
						        <input type="text" name="modelo" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Color</span>
						        </div>
						        <input type="text" name="color" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">VIN</span>
						        </div>
						        <input type="text" name="nro_motor" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Cliente</span>
						        </div>
						        <select name="cliente" class="form-control">
						        	<option value="">Seleccione</option>
						        	<?php foreach ($clientes as $cliente) { ?>
						        	<option value="<?php echo $cliente->id ?>"><?php echo $cliente->apellidoPaterno ?> <?php echo $cliente->apellidoMaterno ?> <?php echo $cliente->nombres ?></option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Modelo</span>
						        </div>
						        <select name="modelo_id" class="form-control">
									<!--<option value="0">Seleccione</option>-->
						        	<?php foreach ($modelos as $modelo) { ?>
						        	<option value="<?php echo $modelo->id ?>"><?php echo $modelo->marca ?> / <?php echo $modelo->modelo ?> / <?php echo $modelo->version ?> / <?php echo $modelo->ano ?> </option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				  	</div>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
	        		<button type="submit" class="btn btn-success">Guardar <i class="fa fa-save"></i> </button>
	      		</div>
			</div>
	  	</div>
	</form>
</div>





<!-- Editar Vehiculo -->
<div class="modal fade" id="modalEditVehiculo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditVehiculo">
		<input type="hidden" name="action" value="actualizar_vehiculo">
		<input type="hidden" name="regid" value="">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Datos del Vehiculo</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
        			<div class="form-row">
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Patente</span>
						        </div>
						        <input type="text" name="patente" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Año</span>
						        </div>
						        <input type="text" name="ano" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Marca</span>
						        </div>
						        <input type="text" name="marca" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Modelo</span>
						        </div>
						        <input type="text" name="modelo" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Color</span>
						        </div>
						        <input type="text" name="color" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">VIN</span>
						        </div>
						        <input type="text" name="nro_motor" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Cliente</span>
						        </div>
						        <select name="cliente" class="form-control">
						        	<option value="">Seleccione</option>
						        	<?php foreach ($clientes as $cliente) { ?>
						        	<option value="<?php echo $cliente->id ?>"><?php echo $cliente->apellidoPaterno ?> <?php echo $cliente->apellidoMaterno ?> <?php echo $cliente->nombres ?></option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Modelo</span>
						        </div>
						        <select name="modelo_id" class="form-control">
									<option value="0">Otro</option>
						        	<?php foreach ($modelos as $modelo) { ?>
						        	<option value="<?php echo $modelo->id ?>"><?php echo $modelo->marca ?> / <?php echo $modelo->modelo ?> / <?php echo $modelo->version ?> / <?php echo $modelo->ano ?> </option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				  	</div>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
	        		<button type="submit" class="btn btn-success">Guardar <i class="fa fa-save"></i> </button>
	      		</div>
			</div>
	  	</div>
	</form>
</div>




<script>
$(document).ready(function(){
	$(`.modal [type=text]`).keyup(function () {
		$(this).val($(this).val().toUpperCase())
	})

	$(".btnDelete").click(function(){
		tr = $(this).closest('tr');
		regid = tr.data('regid');
		$.confirm({
		    title: 'Eliminar Vehiculo!',
		    content: '¿Desea eliminar el vehiculo seleccionado?',
			type: 'red',
			theme: 'bootstrap',
			icon: 'fa fa-warning',
		    buttons: {
		        NO:{
		            text: 'No',
		            btnClass: 'btn-red',
		        },
		        SI:{
		            text: 'Si',
		            btnClass: 'btn-green',
		            action: function(){
		            	$.ajax({
		            		type: 'POST',
		            		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		            		dataType: 'json',
		            		data: {
								action: 'eliminar_vehiculo',
								regid: regid
							},
		            		beforeSend: function(){
		            		},
		            		success: function(json){
		            			$.alert({
		            				title: false,
		            				type: 'green',
		            				content: 'Vehiculo borrado correctamente'
		            			});
		            			tr.fadeOut(400);
		            		}
		            	})
		            }
		        }
		    }
		});
	});


	$("#formNuevoVehiculo").submit(function(e){
		e.preventDefault();
		regid = $(this).closest('tr').data('regid');
		$.ajax({
    		type: 'POST',
    		url: '<?php echo admin_url('admin-ajax.php'); ?>',
    		dataType: 'json',
    		data: $('#formNuevoVehiculo').serialize(),
    		beforeSend: function(){
    			$(".overlay").show();
    		},
    		success: function(json){
    			if( json.status == 'OK' ){
        			$('#modalNewVehiculo').modal('hide');
        			$.alert({
						title: false,
						type: 'green',
						content: 'Vehiculo ingresado correctamente',
						buttons: {
							volver: {
					            action: function () {
					                location.reload();
					            }
					        }
					    }
					});
        		} else {
        			$(".overlay").hide();
        			$.alert({
						title: false,
						type: 'red',
						content: json.msg
					});
        		}
    		}
    	})
	});	



	$(".btnEdit").click(function(){
		patente = $(this).closest('tr').find('[data-patente]').data('patente');
		ano = $(this).closest('tr').find('[data-ano]').data('ano');
		marca = $(this).closest('tr').find('[data-marca]').data('marca');
		modelo = $(this).closest('tr').find('[data-modelo]').data('modelo');
		color = $(this).closest('tr').find('[data-color]').data('color');
		nro_motor = $(this).closest('tr').find('[data-nro_motor]').data('nro_motor');
		cliente_id = $(this).closest('tr').find('[data-cliente_id]').data('cliente_id');
		modelo_id = $(this).closest('tr').data('modelo_id');

		tr = $(this).closest('tr');
		regid = tr.data('regid');

		$("#formEditVehiculo [name=regid]").val(regid);
		$("#formEditVehiculo [name=patente]").val(patente);
		$("#formEditVehiculo [name=ano]").val(ano);
		$("#formEditVehiculo [name=marca]").val(marca);
		$("#formEditVehiculo [name=modelo]").val(modelo);
		$("#formEditVehiculo [name=color]").val(color);
		$("#formEditVehiculo [name=nro_motor]").val(nro_motor);
		$("#formEditVehiculo [name=cliente]").val(cliente_id);
		$("#formEditVehiculo [name=modelo_id]").val(modelo_id);

		$("#modalEditVehiculo").modal('show');
	})

	$("#formEditVehiculo").submit(function(e){
		e.preventDefault();
		regid = $(this).closest('tr').data('regid');
		$.ajax({
    		type: 'POST',
    		url: '<?php echo admin_url('admin-ajax.php'); ?>',
    		dataType: 'json',
    		data: $('#formEditVehiculo').serialize(),
    		beforeSend: function(){
    			$(".overlay").show();
    		},
    		success: function(json){
    			if( json.status == 'OK' ){
        			$('#modalEditVehiculo').modal('hide');
        			$.alert({
						title: false,
						type: 'green',
						content: 'Vehiculo editado correctamente',
						buttons: {
							volver: {
					            action: function () {
					                location.reload();
					            }
					        }
					    }
					});
        		} else {
        			$(".overlay").hide();
        			$.alert({
						title: false,
						type: 'red',
						content: json.msg
					});
        		}
    		}
    	})
	});

	$('#tabla_vehiculos').DataTable({order: [[0, 'desc']]});

})

</script>

<?php } ?>

<?php include 'footer.php'; ?>