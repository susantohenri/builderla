<?php include 'header.php'; ?>

<?php 
if(isset($_GET['vid'])){
	include 'ots_by_vehiculo.php';
} else {
?>

<div class="box pr-4">

	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Properties</h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewVehiculo">New Property</button>

		<div class="clearfix"></div>
	</div>

	<table class="table table-striped table-bordered" id="tabla_vehiculos" width="100%">
		<thead>
			<tr>
				<th> Source </th>
				<th> Address 1 </th>
				<th> Address 2 </th>
				<th> City </th>
				<th> Zip Code </th>
				<th> Customer </th>
				<th> Customer 2</th>
				<th class="text-center">Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vehiculos as $key => $vehiculo): ?>
			<tr data-regid="<?php echo $vehiculo->id ?>" <?php foreach (['street_address', 'address_line_2', 'city', 'state', 'zip_code', 'cliente_id', 'cliente_id_2'] as $field) { echo "data-{$field}=\"{$vehiculo->$field}\"";} ?>>
				<td class="text-center"> <?php echo builderla_get_creator_display_name($vehiculo->createdBy); ?> </td>
				<td> <?php echo $vehiculo->street_address ?> </td>
				<td> <?php echo $vehiculo->address_line_2 ?> </td>
				<td> <?php echo $vehiculo->city ?> </td>
				<td> <?php echo $vehiculo->zip_code ?> </td>
				<td> <?php echo Mopar::getNombreCliente($vehiculo->cliente_id) ?> </td>
				<td> <?php echo Mopar::getNombreCliente($vehiculo->cliente_id_2) ?> </td>
				<td class="text-center">
					<button class="btn btn-success btnEdit" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button>
					<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
					<!--
					<a href="admin.php?page=mopar-vehiculos&vid=<?php echo $vehiculo->id ?>" class="btn btn-info" data-toggle="tooltip" title="Ver OTs del Vehiculo"><i class="fa fa-search"></i></a>
					-->
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
	        		<h5 class="modal-title">Property Information</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
        			<div class="form-row">
						<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Street Address</span>
						        </div>
						        <input type="text" name="street_address" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Address 2</span>
						        </div>
						        <input type="text" name="address_line_2" class="form-control">
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">City</span>
						        </div>
						        <input type="text" name="city" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">State</span>
						        </div>
								<select name="state" class="form-control" required>
									<option value="California (CA)">California (CA)</option>
								</select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Zip code</span>
						        </div>
						        <input type="text" name="zip_code" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Customer</span>
						        </div>
						        <select name="cliente" class="form-control">
						        	<option value="">Seleccione</option>
						        	<?php foreach ($clientes as $cliente) { ?>
						        	<option value="<?php echo $cliente->id ?>"><?php echo $cliente->apellidoPaterno ?> <?php echo $cliente->nombres ?></option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Customer 2</span>
						        </div>
						        <select name="cliente_2" class="form-control">
						        	<option value="">Seleccione</option>
						        	<?php foreach ($clientes as $cliente) { ?>
						        	<option value="<?php echo $cliente->id ?>"><?php echo $cliente->apellidoPaterno ?> <?php echo $cliente->nombres ?></option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				  	</div>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Close</button>
	        		<button type="submit" class="btn btn-success">Save <i class="fa fa-save"></i> </button>
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
	        		<h5 class="modal-title">Customer Information</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
        			<div class="form-row">
						<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Street Address</span>
						        </div>
						        <input type="text" name="street_address" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Address 2</span>
						        </div>
						        <input type="text" name="address_line_2" class="form-control">
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">City</span>
						        </div>
						        <input type="text" name="city" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">State</span>
						        </div>
								<select name="state" class="form-control" required>
									<option value="California (CA)">California (CA)</option>
								</select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Zip code</span>
						        </div>
						        <input type="text" name="zip_code" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Customer</span>
						        </div>
						        <select name="cliente" class="form-control">
						        	<option value="">Seleccione</option>
						        	<?php foreach ($clientes as $cliente) { ?>
						        	<option value="<?php echo $cliente->id ?>"><?php echo $cliente->apellidoPaterno ?> <?php echo $cliente->nombres ?></option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Customer 2</span>
						        </div>
						        <select name="cliente_2" class="form-control">
						        	<option value="">Seleccione</option>
						        	<?php foreach ($clientes as $cliente) { ?>
						        	<option value="<?php echo $cliente->id ?>"><?php echo $cliente->apellidoPaterno ?> <?php echo $cliente->nombres ?></option>
						        	<?php } ?>
						        </select>
					      	</div>
				    	</div>
				  	</div>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Close</button>
	        		<button type="submit" class="btn btn-success">Save <i class="fa fa-save"></i> </button>
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
		    title: 'Delete Property!',
		    content: 'Do you want to delete the selected property?',
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
									content: 'Property deleted successfully'
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
						content: 'Property Successfully Added',
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
		tr = $(this).closest('tr');

		street_address = tr.data('street_address');
		address_line_2 = tr.data('address_line_2');
		city = tr.data('city');
		state = tr.data('state');
		zip_code = tr.data('zip_code');
		cliente_id =tr.data('cliente_id');
		cliente_id_2 = tr.data('cliente_id_2');

		regid = tr.data('regid');

		$("#formEditVehiculo [name=regid]").val(regid);
		$("#formEditVehiculo [name=street_address]").val(street_address);
		$("#formEditVehiculo [name=address_line_2]").val(address_line_2);
		$("#formEditVehiculo [name=city]").val(city);
		$("#formEditVehiculo [name=state]").val(state);
		$("#formEditVehiculo [name=zip_code]").val(zip_code);
		$("#formEditVehiculo [name=cliente]").val(cliente_id);
		$("#formEditVehiculo [name=cliente_2]").val(cliente_id_2);

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

	$('#tabla_vehiculos').DataTable({scrollX: true, order: [[0, 'desc']]});

})

</script>

<?php } ?>

<?php include 'footer.php'; ?>