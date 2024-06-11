<?php 
global $wpdb;

$folder = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/mopar_taller/uploads/';
$inserted = false;
$updated = false;

if( $_POST ){

    $tmpFilePath = $_FILES['uploadsHistory']['tmp_name'];
    if ($tmpFilePath == ""){
        if( $_POST['hdn_imagen'] == "" ){
            $archivo = '';
        } else {
            $archivo = $_POST['hdn_imagen'];
        }
    } else {
        $name = $_FILES['uploadsHistory']['name'];
        $pathinfo = pathinfo($name);
        $array_extension_allowed = array('jpeg','jpg','png');

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

    $tmp_blueprint = $_FILES['blueprint']['tmp_name'];
    if ($tmp_blueprint == ""){
        if( $_POST['existing_blueprint'] == "" ){
            $blueprint = '';
        } else {
            $blueprint = $_POST['existing_blueprint'];
        }
    } else {
        $name = $_FILES['blueprint']['name'];
        $pathinfo = pathinfo($name);
        $array_extension_allowed = array('jpeg','jpg','png');

        if( in_array($pathinfo['extension'],$array_extension_allowed) ){

            $filename_body = uniqid($input_name);
            $newName = $pathinfo['filename'].'___'.$filename_body.'.'.$pathinfo['extension'];
            $newFilePath = $folder . $newName;

            if(move_uploaded_file($tmp_blueprint, $newFilePath)) {
                $blueprint = $newName;
            } else {
                $blueprint = '';
            }
        }
    }

    $array_insert = [
        'marca' => $_POST['marca'],
        'modelo' => $_POST['modelo'],
        'version' => $_POST['version'],
        'imagen' => $archivo,
        'blueprint' => $blueprint
    ];

    if( $_POST['action'] == 'insertar_modelo' ){
        if( $wpdb->insert('modelos',$array_insert) ){
            $inserted = true;
        }
    }

    if( $_POST['action'] == 'editar_modelo' ){
        if( $wpdb->update('modelos',$array_insert,['id' => $_POST['modelo_id']]) ){
            $updated = true;
        }
    }

}

$modelos = Mopar::getModelos();
?>

<?php include 'header.php'; ?>

<section id="modelos">

	<div class="box pr-4">
		<div class="box-header mb-4">
			<h2 class="font-weight-light text-center text-muted float-left"> Lista de Modelos </h2>
			<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewModelo">Nuevo Modelo</button>

			<div class="clearfix"></div>
		</div>
		<div class="box-body">
			<table class="table table-striped table-bordered" id="tabla_ots">
				<thead>
					<tr>
						<th>#</th>
						<th> Marca </th>
						<th> Modelo </th>
						<th> Version </th>
						<th> Miniatura </th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($modelos as $modelo): ?>
					<tr data-regid="<?php echo $modelo->id; ?>">
						<td data-regid="<?php echo $modelo->id; ?>"> <?php echo $modelo->id; ?> </td>
						<td data-marca="<?php echo $modelo->marca; ?>"> <?php echo $modelo->marca; ?> </td>
						<td data-modelo="<?php echo $modelo->modelo; ?>"> <?php echo $modelo->modelo; ?> </td>
						<td data-version="<?php echo $modelo->version; ?>"> <?php echo $modelo->version; ?> </td>
						<td data-imagen="<?php echo $modelo->imagen; ?>"> <img src="../wp-content/plugins/mopar_taller/uploads/<?php echo $modelo->imagen; ?>"> </td>
						<td class="text-center">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $modelo->id; ?>" data-toggle="tooltip" title="Editar Modelo"><i class="fa fa-pencil"></i></button>
							<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar Modelo"><i class="fa fa-trash-o"></i></button>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

</section>





<!-- Nuevo Modelo -->
<div class="modal fade" id="modalNewModelo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <form method="post" id="formNewModelo" enctype="multipart/form-data">
        <input type="hidden" name="action" value="insertar_modelo">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos del Modelo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
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
                                    <span class="input-group-text">Version</span>
                                </div>
                                <input type="text" name="version" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Miniatura</span>
                                </div>
                                <input type="file" name="uploadsHistory" class="form-control pl-4" required>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Blueprint</span>
                                </div>
                                <input type="file" name="blueprint" class="form-control pl-4" required>
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






<!-- Editar Modelo -->
<div class="modal fade" id="modalEditModelo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <form method="post" id="formEditModelo" enctype="multipart/form-data">
        <input type="hidden" name="action" value="editar_modelo">
        <input type="hidden" name="modelo_id" value="">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos del Modelo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
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
                                    <span class="input-group-text">Version</span>
                                </div>
                                <input type="text" name="version" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Foto</span>
                                </div>
                                <input type="file" name="uploadsHistory" class="form-control pl-4">
                                <input type="hidden" name="hdn_imagen">
                                <a class="btn-link btn border archivo_link" target="_blank" href=""></a>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Blueprint</span>
                                </div>
                                <input type="file" name="blueprint" class="form-control pl-4">
                                <input type="hidden" name="existing_blueprint">
                                <a class="btn-link btn border blueprint_link" target="_blank" href=""></a>
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






<script>
$(document).ready(function(){

    $("#formNewModelo").submit(function(e){
        $(".overlay").show();
        recalcular();
        e.preventDefault();
        $("#formNuevoOT")[0].submit();
    });


    $("#formEditModelo").submit(function(e){
        $(".overlay").show();
        recalcular();
        e.preventDefault();
        $("#formEditOT")[0].submit();
    });


    $(".btnEdit").click(function(){
        modelo_id = $(this).data('regid');
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            dataType: 'json',
            data: 'action=get_modelo&modelo_id=' + modelo_id,
            beforeSend: function(){
                $(".overlay").show();
            },
            success: function(json){

                $('#modalEditModelo [name=modelo_id]').val(json.modelo.id);
                $('#modalEditModelo [name=marca]').val(json.modelo.marca);
                $('#modalEditModelo [name=modelo]').val(json.modelo.modelo);
                $('#modalEditModelo [name=version]').val(json.modelo.version);
                $('#modalEditModelo [name=hdn_imagen]').val(json.modelo.imagen);

                $('#modalEditModelo a.archivo_link').attr('href','<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/uploads/' + json.modelo.imagen);
                $('#modalEditModelo a.archivo_link').html(json.modelo.imagen + ' &nbsp; <i class="fa fa-external-link"></i>');

                $('#modalEditModelo a.blueprint_link').attr('href','<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/uploads/' + json.modelo.blueprint);
                $('#modalEditModelo a.blueprint_link').html(json.modelo.blueprint + ' &nbsp; <i class="fa fa-external-link"></i>');

                $(".overlay").hide();
                $('#modalEditModelo').modal('show');        
            }
        })
    })


    $('.btnDelete').click(function(e){
        e.preventDefault();
        tr = $(this).closest('tr');
        regid = tr.data('regid');
        $.confirm({
            title: false,
            content: '¿Esta seguro que desea eliminar el modelo seleccionado?',
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
                            data: 'action=eliminar_modelo&regid=' + regid,
                            beforeSend: function(){
                                $(".overlay").show();
                            },
                            success: function(json){
                                $(".overlay").hide();
                                $.alert({
                                    type: 'green',
                                    title: false,
                                    content: 'Registro eliminado correctamente'
                                })
                                tr.fadeOut(500);
                            }
                        })
                    }
                }
            }
        });
    });

    
    <?php if($inserted){ ?>
    $.alert({
        type: 'green',
        title: false,
        content: 'Modelo ingresado correctamente'
    })
    <?php } ?>


    <?php if($updated){ ?>
    $.alert({
        type: 'green',
        title: false,
        content: 'Modelo actualizado correctamente'
    })
    <?php } ?>

})
</script>

<?php include 'footer.php'; ?>