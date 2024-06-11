<?php 
global $wpdb;

$folder = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/mopar_taller/uploads/';
$inserted = false;

if( $_POST ){
    $total = count($_FILES['uploadsHistory']['name']);

    $uploads = [];
    for( $i=0 ; $i < $total ; $i++ ) {
        $tmpFilePath = $_FILES['uploadsHistory']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            
            $name = $_FILES['uploadsHistory']['name'][$i];
            $pathinfo = pathinfo($name);
            $array_extension_allowed = array('pdf','xls', 'xlsx', 'doc', 'docx','jpeg','jpg','png');

            if( in_array($pathinfo['extension'],$array_extension_allowed) ){
                
                $filename_body = uniqid($input_name);
                $newName = $pathinfo['filename'].'___'.$filename_body.'.'.$pathinfo['extension'];
                $newFilePath = $folder . $newName;
                
                if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $uploads[] = $newName;
                } else {
                    Mopar::dd($newFilePath." - ERROR",0);
                }
            }
        }
    }

    $array_insert = [
        'vehiculo_id' => $_GET['vid'],
        'titulo' => $_POST['titulo'],
        'contenido' => $_POST['contenido'],
        'uploads' => json_encode($uploads)
    ];
    if($wpdb->insert('historial',$array_insert)){
        $inserted = true;
    }

}

$historial = $wpdb->get_results('SELECT * FROM historial WHERE vehiculo_id = ' . $_GET['vid'] . ' ORDER BY fecha DESC ');
$nombreVehiculo = Mopar::getNombreVehiculo($_GET['vid']);
?>

<section id="history">
    <div class="box py-2 px-4">
        <h2 class="font-weight-light text-center text-muted float-left">
            Historial <?php echo $nombreVehiculo; ?>
        </h2>
        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewHistorial">Nuevo Historial</button>

        <div class="clearfix"></div>

        <?php 
        if($historial): 
        foreach ($historial as $hist) : 
        ?>
        <div class="row">
          <div class="col-auto text-center flex-column d-none d-sm-flex">
              
              <h5 class="m-2 mt-5">
                  <span class="badge badge-pill bg-success">
                    <?php  
                    $timestamp = strtotime($hist->fecha);
                    ?>
                    <em class="mes"><?php echo Mopar::getNombreMes(date('n',$timestamp),true); ?></em>
                    <em class="dia"><?php echo date('d',$timestamp) ?></em>
                    <em class="ano"><?php echo date('Y',$timestamp) ?></em>
                  </span>
              </h5>
              <div class="row h-75">
                  <div class="col border-right">&nbsp;</div>
                  <div class="col">&nbsp;</div>
              </div>
          </div>
          <div class="col py-2">
              <div class="card border-success shadow">
                    
                    <a href="" data-regid="<?php echo $hist->id ?>" class="rounded-circle d-block text-danger position-absolute btnDelete" style="width: 20px;height: 20px;top: 0;right: 0;" href=""><i class="fa fa-times"></i></a>
                    
                    <div class="card-body">
                      <div class="float-right text-success text-right"><small><?php echo $nombreVehiculo; ?></small></div>
                      <h4 class="card-title text-success"><?php echo $hist->titulo ?></h4>
                        <div class="card-text">
                            <?php echo $hist->contenido ?>
                        </div>
                        <?php 
                        $uploads = json_decode($hist->uploads); 
                        if(  $uploads) : 
                        ?>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-target="#t2_details" data-toggle="collapse">
                        Ver archivos adjuntos ▼
                        </button>
                        <div class="collapse border mt-2" id="t2_details">
                            <div class="p-2 text-monospace">
                                <?php  
                                foreach ($uploads as $upload) {
                                ?>
                                <div>
                                    <a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/uploads/<?php echo $upload; ?>" target="_blank"><?php echo $upload; ?></a>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                  </div>
              </div>
          </div>
        </div>
        <?php 
        endforeach; 
        else:
        ?>
        <p class="mt-5"><strong>Aun no hay historial para el vehiculo seleccionado</strong></p>
        <?php endif; ?>
    </div>
</section>



<!-- Nuevo Historial -->
<div class="modal fade" id="modalNewHistorial" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <form method="post" id="formNuevoHistorial" enctype="multipart/form-data">
        <input type="text" name="action" value="insertar_historial">
        <input type="text" name="vid" value="<?php echo $_GET['vid'] ?>">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historial para <?php echo $nombreVehiculo; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Titulo</label>
                        <input type="text" name="titulo" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Contenido</label>
                        <textarea name="contenido" class="form-control tinymce" rows="6"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="inputFile">Archivos</label>
                        <div class="fileGroup">
                            <input type="file" name="uploadsHistory[]" class="form-control my-2">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-sm float-right addFile">Otro Archivo <i class="fa fa-plus"></i></button>
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

    $('.btnDelete').click(function(e){
        e.preventDefault();
        regid = $(this).data('regid');
        me = $(this).closest('.row');
        $.confirm({
            title: false,
            content: '¿Esta seguro que desea eliminar el historial seleccionado?',
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
                            data: 'action=eliminar_historial&regid=' + regid,
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
                                me.fadeOut(500);
                            }
                        })
                    }
                }
            }
        });
    });

    $(".addFile").click(function(){
        h = '<input type="file" name="uploadsHistory[]" class="form-control my-2">';
        $(".fileGroup").append(h);
    })

    $("#formNuevoHistorial").submit(function(){
        $("#formNuevoHistorial button[type=submit]").replaceWith('<button type="button" class="btn">Guardando...</button>');
    })
    <?php if($inserted){ ?>
    $.alert({
        type: 'green',
        title: false,
        content: 'Historial ingresado correctamente'
    })
    <?php } ?>
})
</script>