<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap4-datetimepicker@5.2.3/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?= plugin_dir_url(__FILE__) . 'sign-contract.css' ?>">

<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap4-datetimepicker@5.2.3/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript" src="<?= plugin_dir_url(__FILE__) . 'sign-contract.js' ?>"></script>

<form name="sign_contract" method="POST">
    <div class="container-fluid pl-5 pr-5">
        <div class="row text-center mt-5">
            <h3 class="col-12">Sign Contract #<?= $ot_id ?></h3>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Sign Date</label>
                    <input type="text" name="signed_date" class="form-control" placeholder="MM/DD/YYYY" required>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Signature</label>
                    <canvas id="signature" style=""></canvas>
                    <textarea name="client_signature" class="d-none"></textarea>
                    <a name="clear_signature" class="btn btn-warning mt-1 text-white float-right">Clear</a>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Initial</label>
                    <canvas id="initial"></canvas>
                    <textarea name="client_initial" class="d-none"></textarea>
                    <a name="clear_initial" class="btn btn-warning mt-1 text-white float-right">Clear</a>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="text" name="client_dob" class="form-control" placeholder="MM/DD/YYYY" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-0 col-lg-7"></div>
            <button class="btn btn-lg btn-success col-6 offset-3 mt-2 col-lg-2 ml-lg-1" name="sign_contract">Send Contract</button>
            <button class="btn btn-lg text-white btn-warning col-6 offset-3 mt-2 col-lg-2 ml-lg-1" onclick="window.close()">Close</button>
        </div>
    </div>
</form>