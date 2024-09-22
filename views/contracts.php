<?php

include 'header.php';
$contract_status_map = [
    'NEWLY_CREATED' => 'danger',
    'SIGNED' => 'success',
];

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'send_unsigned_contract_email_body':
            $ot_id = $_POST['ot_id'];

            wp_schedule_single_event(time(), 'mopar_async', [
                [
                    'action' => 'send_unsigned_contract_body',
                    'recipient' => [
                        'ot_id' => $ot_id,
                        'email' => $_POST['recipient'],
                        'email_body' => $_POST['email_body']
                    ]
                ]
            ]);

            global $wpdb;
            $wpdb->update('ot', ['estado' => 2], ['id' => $ot_id]);
            $wpdb->update('solicitud', ['estado' => 5], ['ot_id' => $ot_id]);
            $unsigned_contract_email_body_sent = true;
            break;
    }
}
?>

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
                    <th> Status </th>
                    <th class="text-center">Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $ot) : ?>
                    <tr data-regid="<?php echo $ot->id; ?>">
                        <td> <?= date_format(date_create($ot->regdate), 'Y-m-d') ?> </td>
                        <td data-vehiculo="<?php echo $ot->vehiculo_id; ?>"> <?php echo Mopar::getTitleVehiculo($ot->vehiculo_id) ?> </td>
                        <td> <?php echo $ot->titulo; ?> </td>
                        <td class="text-right"><?php echo '$ ' . number_format($ot->valor, 0); ?></td>
                        <td class="text-center">
                            <a>
                                <i class="fa fa-circle text-<?= $contract_status_map[$ot->contract_status] ?>"></i>
                            </a>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/builderla/contract-pdf.php?id=<?php echo $ot->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="View"><i class="fa fa-search"></i></a>
                            <button class="btn btn-warning btnSendUnsignedContract" data-toggle="tooltip" title="Send Contract"><i class="fa fa-envelope"></i></button>
                            <?php if ('SIGNED' != $ot->contract_status): ?>
                                <button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <ul>
            <li>
                <i class="fa fa-circle text-success"></i> This contract was signed
            </li>
            <li>
                <i class="fa fa-circle text-danger"></i> There are no actions for this contract
            </li>
        </ul>
    </div>
</div>

<!-- SEND UNSIGNED CONTRACT EMAIL -->
<div class="modal fade" id="modalUnsignedContractEmail" tabindex="-1" role="dialog"
    aria-labelledby="modalUnsignedContractEmail" aria-hidden="true">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="send_unsigned_contract_email_body">
        <input type="hidden" name="ot_id">
        <input type="hidden" name="recipient">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Contract Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-12">
                            <textarea name="email_body" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btnGuardar">
                        <i class="fa fa-envelope"></i>
                        SEND
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        CANCEL
                    </button>
                </div>
            </div>
        </div>
    </form>
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

            jQuery(`div#modalUnsignedContractEmail [name="ot_id"]`).val(regid)
            jQuery.ajax({
                type: `GET`,
                dataType: 'json',
                url: `<?= admin_url('admin-ajax.php') ?>`,
                data: `action=get_unsigned_contract_body&ot_id=${regid}`,
                beforeSend: function() {
                    jQuery(`.overlay`).show()
                },
                success: function(json) {
                    jQuery(`.overlay`).hide()
                    if (`ERROR` === json.status) {
                        $.alert({
                            title: false,
                            type: 'red',
                            content: json.message
                        });
                    } else {
                        jQuery(`[name="recipient"]`).val(json.recipient)
                        jQuery(`[name="email_body"]`)
                            .val(json.message)
                            .attr(`rows`, json.message.split(`\n`).length)
                        $(`#modalUnsignedContractEmail`).modal(`show`)
                    }
                }
            })
        })

        <?php if (isset($unsigned_contract_email_body_sent)): ?>
            $.alert({
                title: false,
                type: 'green',
                content: 'Usigned contract email sent successfully',
                buttons: {
                    OK: () => {
                        location.reload()
                    }
                }
            });
        <?php endif ?>

        jQuery(`.btnDelete`).click(function() {
            tr = jQuery(this).closest(`tr`)
            regid = tr.data(`regid`)

            jQuery.confirm({
                title: `Delete Contract!`,
                content: `Do you want to delete the selected contract?`,
                type: `red`,
                icon: `fa fa-warning`,
                buttons: {
                    NO: {
                        text: `No`,
                        btnClass: `btn-red`,
                    },
                    SI: {
                        text: `Yes`,
                        btnClass: `btn-green`,
                        action: function() {
                            jQuery.ajax({
                                type: `POST`,
                                url: `<?= admin_url('admin-ajax.php') ?>`,
                                dataType: `json`,
                                data: `action=delete_contract&regid=${regid}`,
                                beforeSend: function() {
                                    jQuery(`.overlay`).show()
                                },
                                success: function(json) {
                                    jQuery(`.overlay`).hide()
                                    jQuery.alert({
                                        title: false,
                                        type: `green`,
                                        content: `Contract deleted`
                                    })
                                    tr.fadeOut(400)
                                }
                            })
                        }
                    }
                }
            })
        })
    })
</script>
<?php include 'footer.php'; ?>