<?php include 'header.php'; ?>
<div class="box pr-4">
    <div class="box-header mb-4">
        <h2 class="font-weight-light text-center text-muted float-left">Contracts </h2>
        <div class="clearfix"></div>
    </div>
    <div class="box-body">
        <table class="table table-striped table-bordered" id="tabla_contracts" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th> Date </th>
                    <th> Address </th>
                    <th class="text-center">Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $ot) : ?>
                    <tr data-regid="<?php echo $ot->id; ?>">
                        <td data-regid="<?php echo $ot->id; ?>"> <?php echo $ot->id; ?> </td>
                        <td data-titulo="<?php echo $ot->titulo; ?>"> <?php echo $ot->fecha; ?> </td>
                        <td data-vehiculo="<?php echo $ot->vehiculo_id; ?>"> <?php echo Mopar::getNombreVehiculo($ot->vehiculo_id) ?> </td>
                        <td class="text-center" style="white-space: nowrap;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(`#tabla_contracts`).DataTable({
            "scrollX": true,
            "ordering": false
        })
    })
</script>
<?php include 'footer.php'; ?>