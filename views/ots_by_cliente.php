<?php 
global $wpdb;

$nombreVehiculo = Mopar::getNombreCliente($_GET['cid']);
$ots = Mopar::getOtByCliente($_GET['cid']);

?>

<section id="history">
    <div class="box py-2 px-4">
        <h2 class="font-weight-light text-center text-muted float-left">
            Cliente: <?php echo $nombreVehiculo; ?>
        </h2>
       

        <div class="clearfix"></div>

        <?php if($ots): ?>

        <div class="box-body">
            <div class="row">
              <div class="col-12 text-center flex-column d-none d-sm-flex">
                <table class="table table-striped table-bordered" id="tabla_ots">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th> Titulo </th>
                            <th> Cliente </th>
                            <th> Vehiculo </th>
                            <th> Valor Total </th>
                            <th> Km. </th>
                            <th> Tipo de Documento </th>
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
                            <td class="text-center">
                                <a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/pdf.php?id=<?php echo $ot->id; ?>" target="_blank" class="btn btn-info btnEdit" data-toggle="tooltip" title="Ver"><i class="fa fa-search"></i></a>
                                <button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar"><i class="fa fa-trash-o"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
              </div>
            </div>
        </div>

        <?php else: ?>

        <p class="mt-5"><strong>Aun no hay OTs para el vehiculo seleccionado</strong></p>
        
        <?php endif; ?>
    </div>
</section>


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
                            data: 'action=eliminar_ot&regid=' + regid,
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

})
</script>