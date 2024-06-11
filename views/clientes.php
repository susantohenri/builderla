<?php include 'header.php'; ?>

<?php 
if( $_GET['cid'] ):
	include 'ots_by_cliente.php';
else:
?>


<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Lista de Clientes </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewCliente">Nuevo Cliente</button>

		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_clientes">
			<thead>
				<tr>
					<th>#</th>
					<th> Nombre </th>
					<th> Email </th>
					<th> Telefono </th>
					<th class="text-center">Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($clientes as $cliente): ?>
				<tr data-regid="<?php echo $cliente->id; ?>">
					<td data-regid="<?php echo $cliente->id; ?>"> <?php echo $cliente->id; ?> </td>
					<td data-nombres="<?php echo $cliente->nombres; ?>" data-apellidopaterno="<?php echo $cliente->apellidoPaterno; ?>" data-apellidomaterno="<?php echo $cliente->apellidoMaterno; ?>"> <?php echo Mopar::getNombreCliente($cliente->id, false) ?> </td>
					<td data-email="<?php echo $cliente->email; ?>"> <?php echo $cliente->email; ?> </td>
					<td data-telefono="<?php echo $cliente->telefono; ?>"> <?php echo $cliente->telefono; ?> </td>
					<td class="text-center">
						<button class="btn btn-success btnEdit" data-toggle="tooltip" title="Editar Cliente"><i class="fa fa-pencil"></i></button>
						<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar Cliente"><i class="fa fa-trash-o"></i></button>
						<a href="admin.php?page=mopar-clientes&cid=<?php echo $cliente->id ?>" class="btn btn-info" data-toggle="tooltip" title="Ver OTs del Cliente"><i class="fa fa-search"></i></a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>




<!-- Nuevo Cliente -->
<div class="modal fade" id="modalNewCliente" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoCliente">
		<input type="hidden" name="action" value="insertar_cliente">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Datos del Cliente</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
        			<div class="form-row">
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Nombres</span>
						        </div>
						        <input type="text" name="nombres" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Apellido Paterno</span>
						        </div>
						        <input type="text" name="apellidoPaterno" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Apellido Materno</span>
						        </div>
						        <input type="text" name="apellidoMaterno" class="form-control">
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Email</span>
						        </div>
						        <input type="email" name="email" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Telefono</span>
						        </div>
						        <div class="input-group-prepend">
					          		<span class="input-group-text">(+56)</span>
						        </div>
						        <input type="text" name="telefono" class="form-control" required>
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





<!-- Editar Cliente -->
<div class="modal fade" id="modalEditCliente" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditCliente">
		<input type="hidden" name="action" value="actualizar_cliente">
		<input type="hidden" name="regid" value="">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Datos del Cliente</h5>
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	        		</button>
	      		</div>
	      		<div class="modal-body">
        			<div class="form-row">
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Nombres</span>
						        </div>
						        <input type="text" name="nombres" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Apellido Paterno</span>
						        </div>
						        <input type="text" name="apellidoPaterno" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Apellido Materno</span>
						        </div>
						        <input type="text" name="apellidoMaterno" class="form-control">
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Email</span>
						        </div>
						        <input type="email" name="email" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Telefono</span>
						        </div>
						        <div class="input-group-prepend">
					          		<span class="input-group-text">(+56)</span>
						        </div>
						        <input type="text" name="telefono" class="form-control" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-6">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Cambiar Password</span>
						        </div>
						        <input type="text" name="secret" value="*********" autocomplete="off" class="form-control" required>
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






<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script></head>

<script>

$(document).ready(function(){

	$(`[name="nombres"],[name="apellidoPaterno"],[name="apellidoMaterno"]`).keyup(function () {
		var str = $(this).val()
		str = str.toLowerCase()
		str = str.charAt(0).toUpperCase() + str.slice(1)
		$(this).val(str)
	})

	$(".btnDelete").click(function(){
		tr = $(this).closest('tr');
		regid = tr.data('regid');

		$.confirm({
		    title: 'Eliminar Cliente!',
		    content: '¿Desea eliminar el cliente seleccionado?',
			type: 'red',
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
		            		data: 'action=eliminar_cliente&regid=' + regid,
		            		beforeSend: function(){
		            		},
		            		success: function(json){
		            			$.alert({
		            				title: false,
		            				type: 'green',
		            				content: 'Cliente borrado correctamente'
		            			});
		            			tr.fadeOut(400);
		            		}
		            	})
		            }
		        }
		    }
		});
	});



	$("#formNuevoCliente").submit(function(e){
		e.preventDefault();
		validateEmail($(this), is_valid => {
			if (!is_valid) return false
			else $.confirm({
				title: 'Nueva Password para nuevo cliente!',
				content: 'Al crear un nuevo cliente, se generará una password aleatoria y será enviada al email del cliente ingresado.<br>¿Proceder?',
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
								data: $('#formNuevoCliente').serialize(),
								beforeSend: function(){
									$(".overlay").show();
								},
								success: function(json){
									$(".overlay").hide();
									if( json.status == 'OK' ){
										$('#modalNewCliente').modal('hide');
										$.alert({
											title: false,
											type: 'green',
											content: 'Cliente ingresado correctamente',
											buttons: {
												volver: {
													action: function () {
														location.reload();
													}
												}
											}
										});
									} else {
										$.alert({
											title: false,
											type: 'red',
											content: json.msg
										});
									}
								}
							})
						}
					}
				}
			});
		})
	});



	$(".btnEdit").click(function(){
		nombres = $(this).closest('tr').find('[data-nombres]').data('nombres');
		apellidopaterno = $(this).closest('tr').find('[data-apellidopaterno]').data('apellidopaterno');
		apellidomaterno = $(this).closest('tr').find('[data-apellidomaterno]').data('apellidomaterno');
		email = $(this).closest('tr').find('[data-email]').data('email');
		telefono = $(this).closest('tr').find('[data-telefono]').data('telefono');
		tr = $(this).closest('tr');
		regid = tr.data('regid');

		$("#formEditCliente [name=regid]").val(regid);
		$("#formEditCliente [name=nombres]").val(nombres);
		$("#formEditCliente [name=apellidoPaterno]").val(apellidopaterno);
		$("#formEditCliente [name=apellidoMaterno]").val(apellidomaterno);
		$("#formEditCliente [name=email]").val(email);
		$("#formEditCliente [name=telefono]").val(telefono);

		$("#modalEditCliente").modal('show');
	})

	$("#formEditCliente").submit(function(e){
		e.preventDefault();
		validateEmail($(this), is_valid => {
			if (!is_valid) return false
			else $.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: $('#formEditCliente').serialize() + '&regid=' + regid,
				beforeSend: function(){
					$(".overlay").show();
				},
				success: function(json){
					$(".overlay").hide();
					if( json.status == 'OK' ){
						$('#modalEditCliente').modal('hide');
						$.alert({
							title: false,
							type: 'green',
							content: 'Cliente editado correctamente',
							buttons: {
								volver: {
									action: function () {
										location.reload();
									}
								}
							}
						});
					} else {
						$.alert({
							title: false,
							type: 'red',
							content: json.msg
						});
					}
				}
			})
		})
	});

	$("[name=rut]").blur(function(){
		$(this).val( formateaRut($(this).val() ) );
	});

    $('#tabla_clientes').DataTable({order: [[0, 'desc']]});

	function validateEmail(form, cb) {
		const email = form.find(`[name=email]`).val()
		const id = 0 < form.find(`[name=regid]`).length ? form.find(`[name=regid]`).val() : 0
		$.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			dataType: 'json',
			data: `action=validate_cliente&email=${email}&id=${id}`,
			beforeSend: function () { },
			success: function (json) {
				if (`ERROR` === json.status) {
					$.alert({
						title: false,
						type: 'red',
						content: json.message
					});
					cb(false)
				} else cb(true)
			}
		})
	}
});



</script>
<?php endif; ?>
<?php include 'footer.php'; ?>