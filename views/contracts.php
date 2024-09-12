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
                    <th> Date </th>
                    <th> Customer </th>
                    <th> Project Description </th>
                    <th class="text-center"> Total </th>
                    <th class="text-center">Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $ot) : ?>
                    <tr data-regid="<?php echo $ot->id; ?>">
                        <td> <?= date_format(date_create($ot->regdate),'Y-m-d') ?> </td>
                        <td data-vehiculo="<?php echo $ot->vehiculo_id; ?>"> <?php echo Mopar::getTitleVehiculo($ot->vehiculo_id) ?> </td>
                        <td> <?php echo $ot->titulo; ?> </td>
                        <td class="text-right"><?php echo '$ ' . number_format($ot->valor, 0); ?></td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/builderla/contract-pdf.php?id=<?php echo $ot->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
                            <button class="btn btn-warning btnSendUnsignedContract" data-toggle="tooltip" title="Send Unsigned Contract"><i class="fa fa-envelope"></i></button>
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

        jQuery(".btnSendUnsignedContract").click(function() {
            tr = jQuery(this).closest('tr')
            regid = tr.data('regid')

            jQuery.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                dataType: 'json',
                data: 'action=send_unsigned_contract&regid=' + regid,
                beforeSend: function() {
                    jQuery(".overlay").show()
                },
                success: function(json) {
                    jQuery(".overlay").hide()
                    if (`ERROR` === json.status) {
                        jQuery.alert({
                            title: false,
                            type: 'red',
                            content: json.message
                        })
                    } else {
                        jQuery.alert({
                            title: false,
                            type: 'green',
                            content: 'Email sent successfully',
                            buttons: {
                                ok: () => {
                                }
                            }
                        })
                    }
                }
            })
        })
    })
</script>
<?php include 'footer.php'; ?>