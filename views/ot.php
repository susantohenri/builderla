<?php  
$folder = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/mopar_taller/uploads/';
$inserted = false;
$updated = false;

if( $_POST ){
	global $wpdb;

    $tmpFilePath = $_FILES['uploadsHistory']['tmp_name'];
    if ($tmpFilePath == ""){
    	if( $_POST['hdn_archivo'] == "" ){
    		$archivo = '';
    	} else {
    		$archivo = $_POST['hdn_archivo'];
    	}
    } else {
        $name = $_FILES['uploadsHistory']['name'];
        $pathinfo = pathinfo($name);
        $array_extension_allowed = array('pdf','xls', 'xlsx', 'doc', 'docx','jpeg','jpg','png');

        if( in_array($pathinfo['extension'],$array_extension_allowed) ){
            
            $filename_body = uniqid($input_name);
            $newName = $pathinfo['filename'].'___'.$filename_body.'.'.$pathinfo['extension'];
            $newFilePath = $folder . $newName;
            
            if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                $archivo = $newName;
            } else {
                $archivo = '';
            }
        }
    }


    $array_insert = [
		'cliente_id' => $_POST['cliente'],
		'vehiculo_id' => $_POST['vehiculo'],
		'titulo' => $_POST['titulo'],
		'detalle' => json_encode($_POST['detalle']),
		'valor' => $_POST['valor'],
		'km' => $_POST['km'],
		'estado' => $_POST['estado'],
		'observaciones' => $_POST['observaciones'],
		'archivo' => $archivo
	];

	if( $_POST['action'] == 'insertar_ot' ){
		if( $wpdb->insert('ot',$array_insert) ){
			$inserted = true;
		}
	}

	if( $_POST['action'] == 'editar_ot' ){
		$before_update = (array) Mopar::getOneOt($_POST['ot_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) $array_insert['upddate'] = date('Y-m-d H:i:s');

		if( $wpdb->update('ot',$array_insert,['id' => $_POST['ot_id']]) ){
			$updated = true;
		}
	}



}
?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Lista de OTs </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewOT">Nueva</button>

		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_ots">
			<thead>
				<tr>
					<th>#</th>
					<th> Titulo OT </th>
					<th> Cliente </th>
					<th> Vehiculo </th>
					<th> Valor Total </th>
					<th> Km. </th>
					<th> Estado </th>
					<th class="text-center">Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ots as $ot): ?>
				<tr data-regid="<?php echo $ot->id; ?>">
					<td data-regid="<?php echo $ot->id; ?>"> <?php echo $ot->id; ?> </td>
					<td data-titulo="<?php echo $ot->titulo; ?>"> <?php echo $ot->titulo; ?> </td>
					<td data-cliente="<?php echo $ot->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($ot->cliente_id) ?> </td>
					<td data-vehiculo="<?php echo $ot->vehiculo_id; ?>"> <?php echo Mopar::getNombreVehiculo($ot->vehiculo_id) ?> </td>
					<td data-valor="<?php echo $ot->valor; ?>"> $ <?php echo number_format($ot->valor,0,',','.') ?> </td>
					<td data-km="<?php echo $ot->km; ?>"> <?php echo $ot->km; ?> </td>
					<td data-estado="<?php echo $ot->estado; ?>"> <?php echo Mopar::getEstado($ot->estado); ?> </td>
					<td class="text-center" style="white-space: nowrap;">
						<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $ot->id; ?>" data-toggle="tooltip" title="Editar OT"><i class="fa fa-pencil"></i></button>
						<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/pdf.php?id=<?php echo $ot->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="Ver OT"><i class="fa fa-search"></i></a>
						<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar OT"><i class="fa fa-trash-o"></i></button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>




<!-- Nuevo OT -->
<div class="modal fade" id="modalNewOT" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoOT" enctype="multipart/form-data">
		<input type="hidden" name="action" value="insertar_ot">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title">Datos de la OT</h5>
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
						      			<td class="w-75">
						      				<input type="text" name="detalle[item][]" class="form-control" required>
						      			</td>
						      			<td class="w-25">
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
					          		<span class="input-group-text">Estado</span>
						        </div>
						        <select name="estado" class="form-control" required>
						        	<option value="">Seleccione</option>
						        	<option value="1">Cotización</option>
						        	<option value="2">Trabajo Realizado</option>
						        	<option value="3">Trabajo NO Realizado</option>
						        </select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-3">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Km.</span>
						        </div>
						        <input type="text" class="form-control" name="km" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-3">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Total</span>
						        </div>
						        <input type="text" class="form-control text-right" name="valor" required readonly>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Observaciones</span>
						        </div>
						        <textarea class="form-control" name="observaciones"></textarea>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Archivo Adicional</span>
						        </div>
						        <input type="file" name="uploadsHistory" class="form-control">
					      	</div>
					      	<small class="text-muted w-100">(Dependiendo del peso del archivo, el proceso de guardar puede tomar mas tiempo de lo estimado. Evite que pese mas de 1MB)</small>
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
	        		<h5 class="modal-title">Datos de la OT</h5>
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
					          		<span class="input-group-text">Estado</span>
						        </div>
						        <select name="estado" class="form-control" required>
						        	<option value="">Seleccione</option>
						        	<option value="1">Cotización</option>
						        	<option value="2">Trabajo Realizado</option>
						        	<option value="3">Trabajo NO Realizado</option>
						        </select>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-3">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Km.</span>
						        </div>
						        <input type="text" class="form-control" name="km" required>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-3">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Total</span>
						        </div>
						        <input type="text" class="form-control text-right" name="valor" required readonly>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Observaciones</span>
						        </div>
						        <textarea class="form-control" name="observaciones"></textarea>
					      	</div>
				    	</div>
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Archivo Adicional</span>
						        </div>
						        <input type="file" name="uploadsHistory" class="form-control">
					      	</div>
					      	<small class="text-muted w-100">(Dependiendo del peso del archivo, el proceso de guardar puede tomar mas tiempo de lo estimado. Evite que pese mas de 1MB)</small>
				    	</div>
				    	<div class="form-group col-md-12">
					      	<div class="input-group">
						        <div class="input-group-prepend">
					          		<span class="input-group-text">Archivo:</span>
						        </div>
						        <a class="btn-link btn border archivo_link" target="_blank" href=""></a>
						        <input type="hidden" name="hdn_archivo">
						        <button type="button" class="btn btn-danger btnQuitarArchivo" data-toggle="tooltip" data-placement="right" title="Quitar archivo"><i class="fa fa-times"></i></button>
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

$(document).ready(function(){
	$(`[name="cliente"]`).css(`display`, `none`).select2({
		theme: `bootstrap4`,
		minimumInputLength: 3,
		ajax: {
			url: `../wp-json/mopar-taller/v1/clientes`
		}
	})

	$(document).on('click','.btnQuitarArchivo', function(e){
		e.preventDefault();
		$('#modalEditOT [name=hdn_archivo]').val("");
		$('#modalEditOT a.archivo_link').attr('href','');
		$('#modalEditOT a.archivo_link').html('');
	})

	$(".btnEdit").click(function(){
		ot_id = $(this).data('regid');
		$.ajax({
    		type: 'POST',
    		url: '<?php echo admin_url('admin-ajax.php'); ?>',
    		dataType: 'json',
    		data: 'action=get_ot&ot_id=' + ot_id,
    		beforeSend: function(){
    			$(".overlay").show();
    		},
    		success: function(json){
    			detalle = JSON.parse(json.ot.detalle);

    			$(".overlay").hide();
    			$('#modalEditOT [name=ot_id]').val(json.ot.id);

				$('#modalEditOT [name=cliente]').html(`<option value="${json.ot.cliente_id}" selected>${json.cliente.nombres} ${json.cliente.apellidoPaterno} ${json.cliente.apellidoMaterno}</option>`)

    			$('[name=vehiculo]').empty();
				$('[name=vehiculo]').append(new Option('Seleccione Vehiculo', ''));
				$.each(json.vehiculos, function(k,v){
					$('[name=vehiculo]').append(new Option(v.marca+" - "+v.modelo+" - "+v.ano, v.id));
				})
				$("[name=vehiculo]").removeAttr('disabled');
				$("[name=vehiculo]").val(json.ot.vehiculo_id);

				$('#modalEditOT [name=titulo]').val(json.ot.titulo);

				icon = '';
				if( json.ot.archivo ){
					icon = ' &nbsp; <i class="fa fa-external-link"></i>';
				}

				$('#modalEditOT [name=hdn_archivo]').val(json.ot.archivo);
				$('#modalEditOT a.archivo_link').attr('href','<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/uploads/' + json.ot.archivo);
				$('#modalEditOT a.archivo_link').html(json.ot.archivo + icon);

				$("#modalEditOT table tbody").empty();
				$.each(detalle.item,function(k,v){
					h = '<tr>';
					h += '	<td>';
					h += '		<input type="text" value="'+detalle.item[k]+'" name="detalle[item][]" class="form-control" required>';
					h += '	</td>';
					h += '	<td>';
					h += '		<input type="text" value="'+detalle.precio[k]+'" name="detalle[precio][]" class="form-control precio text-right" value="0" required>';
					h += '	</td>';
					h += `	<td>
								<a href="#" data-toggle="tooltip" title="Borra Linea" class="btn btn-danger btn-sm btnLess"><i class="fa fa-minus"></i></a>
								<a href="#" class="btn btn-info btn-sm btnUp"><i class="fa fa-arrow-up"></i></a>
								<a href="#" class="btn btn-info btn-sm btnDown"><i class="fa fa-arrow-down"></i></a>
							</td>`;
					h += '</tr>';
					$("#modalEditOT table tbody").append(h);
					$("[data-toggle=tooltip]").tooltip();
					$('.tooltip').hide();
					recalcular();
				})

				$('#modalEditOT [name=estado]').val(json.ot.estado);
				$('#modalEditOT [name=km]').val(json.ot.km);
				$('#modalEditOT [name=valor]').val(json.ot.valor);
				$('#modalEditOT [name=observaciones]').val(json.ot.observaciones);

    			$('#modalEditOT').modal('show');		
    		}
    	})
	})

	if( location.hash == "#new" ){
		$('#modalNewOT').modal('show');
	}

	$(document).on('keyup','.precio',function(e){
		recalcular();
	})

	$(document).on('click','.btnLess',function(e){
		e.preventDefault();
		tr = $(this).closest('tr');
		tr.fadeOut(300, function(){
			tr.remove();
			recalcular();
		})
		$("[data-toggle=tooltip]").tooltip();
		$('.tooltip').hide();
	})

	$(document).on('click','.btnUp',function(e){
		e.preventDefault();
		tr = $(this).closest('tr');
		tr.insertBefore(tr.prev())
	})

	$(document).on('click','.btnDown',function(e){
		e.preventDefault();
		tr = $(this).closest('tr');
		tr.insertAfter(tr.next())
	})

	$(".btnPlus").click(function(e){
		e.preventDefault();
		h = '';
		h += '<tr>';
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
		$(this).closest('.modal').find('table tbody').append(h)
		$("[data-toggle=tooltip]").tooltip();
		$('.tooltip').hide();
		recalcular();
	})

	$("[name=cliente]").change(function(){
		cliente_id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			dataType: 'json',
			data: 'action=get_vehiculos_by_cliente&cliente_id=' + cliente_id,
			beforeSend: function(){
				$("[name=vehiculo]").html('<option value="">Cargando Vehiculos...</option>');
			},
			success: function(json){
				$('[name=vehiculo]').empty();
				$('[name=vehiculo]').append(new Option('Seleccione Vehiculo', ''));
				$.each(json.vehiculos, function(k,v){
					$('[name=vehiculo]').append(new Option(v.marca+" - "+v.modelo+" - "+v.ano, v.id));
				})
				$("[name=vehiculo]").removeAttr('disabled');
			}
		})
	})

	$(".btnDelete").click(function(){
		tr = $(this).closest('tr');
		regid = tr.data('regid');

		$.confirm({
		    title: 'Eliminar OT!',
		    content: '¿Desea eliminar la ot seleccionada?',
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
		            		data: 'action=eliminar_ot&regid=' + regid,
		            		beforeSend: function(){
		            		},
		            		success: function(json){
		            			$.alert({
		            				title: false,
		            				type: 'green',
		            				content: 'OT borrado correctamente'
		            			});
		            			tr.fadeOut(400);
		            		}
		            	})
		            }
		        }
		    }
		});
	});



	$("#formNuevoOT").submit(function(e){
		$(".overlay").show();
		recalcular();
		e.preventDefault();
		$("#formNuevoOT")[0].submit();
	});


	$("#formEditOT").submit(function(e){
		$(".overlay").show();
		recalcular();
		e.preventDefault();
		$("#formEditOT")[0].submit();
	});


	<?php if($inserted){ ?>
    $.alert({
        type: 'green',
        title: false,
        content: 'OT ingresada correctamente'
    })
    <?php } ?>


    <?php if($updated){ ?>
    $.alert({
        type: 'green',
        title: false,
        content: 'OT actualizada correctamente'
    })
    <?php } ?>

    <?php if( $inserted || $updated ){ ?>
	location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-ot';
    <?php } ?>


    $('#tabla_ots').DataTable({
    	"ordering": false 
    });
});

function recalcular(){
	tot = 0;
	$(".precio").each(function(){
		if( $(this).val() == "" ){
			this_val = 0;
		} else {
			this_val = $(this).val();
		}
		tot += parseInt( this_val );
	})
	$("[name=valor]").val( tot );
}


</script>

<?php include 'footer.php'; ?>