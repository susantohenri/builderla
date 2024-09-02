<?php

define('TALLER_COMPANY_SETTINGS_FIELDS', [
    ['name' => 'mopar_company_name', 'label' => "Direct Contractor's Name"],
    ['name' => 'mopar_license_number', 'label' => "Direct Contractor's License Number"],
    ['name' => 'mopar_contractor_initial', 'label' => "Direct Contractor's Initial"],
    ['name' => 'mopar_company_address', 'label' => "Direct Contractor's Address"],
    ['name' => 'mopar_city_state_zip', 'label' => "City, State & ZIP"],
    ['name' => 'mopar_company_phone_number', 'label' => "Direct Contractor's Telephone - FAX"],
    ['name' => 'mopar_company_email', 'label' => "Direct Contractor's Email"],
    ['name' => 'mopar_insurance_policy', 'label' => 'Insurance Company Name'],
    ['name' => 'mopar_insurance_phone', 'label' => 'Insurance Company Telephone'],
    ['name' => 'mopar_company_signature', 'label' => 'Company Signature'],
    ['name' => 'mopar_company_initials', 'label' => 'Company Initials'],
]);

function taller_company_settings()
{
    global $wpdb;
    $fields = TALLER_COMPANY_SETTINGS_FIELDS;

    $field_names = array_map(function ($field) {
        return $field['name'];
    }, $fields);

    if (isset($_POST['save_mopar_settings'])) {
        foreach ($field_names as $name) {
            if (isset($_POST[$name])) {
                $option_id = $wpdb->get_var("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = '{$name}'");
                if ($option_id) $wpdb->update("{$wpdb->prefix}options", ['option_value' => $_POST[$name]], ['option_id' => $option_id]);
                else $wpdb->insert("{$wpdb->prefix}options", ['option_name' => $name, 'option_value' => $_POST[$name]]);
            }
        }
    }

    $values = taller_company_settings_get_items();

    $inputs = '';
    foreach ($fields as $field) {
        if (in_array($field['name'], ['mopar_company_signature', 'mopar_company_initials'])) continue;
        $value = $values[$field['name']];
        $inputs .= "
            <div class=\"form-group col-sm-12 col-md-3\">
                <label>{$field['label']}</label>
            </div>
            <div class=\"form-group col-sm-12 col-md-3\">
                <input type=\"text\" name=\"{$field['name']}\" class=\"form-control\" value=\"{$value}\">
            </div>
        ";
    }

    wp_register_script('mopar-company-settings', plugin_dir_url(__FILE__) . 'company-settings.js', array('jquery'));
    wp_enqueue_script('mopar-company-settings');

    echo "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
        <script src=\"https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js\"></script>
        <div class=\"box pr-4\">
            <div class=\"box-header mb-4 ml-4 mt-4\">
                <div class=\"row\">
                    <div class=\"col-sm-12\">
                        <h2 class=\"font-weight-light text-center text-muted float-left\">Company Settings</h2>
                    </div>
                </div>
            </div>
            <div class=\"box-body\">
                <form method=\"POST\" name=\"mopar_company_settings\">
                    <div class=\"col-sm-12\">

                        <div class=\"form-row\">
                            {$inputs}
                        </div>

                        <div class=\"form-row\">
                            <div class=\"form-group col-sm-12 col-md-6\">
                                <label>Signature</label>
                                <canvas id=\"signature\" style=\"border: 1px solid #ccc; border-radius: 5px; width: 100%; height: 260px;\"></canvas>
                                <textarea class=\"d-none\" name=\"mopar_company_signature\">{$values['mopar_company_signature']}</textarea>
                                <a class=\"btn btn-warning text-white\" name=\"clear_signature\">Clear</a>
                            </div>
                            <div class=\"form-group col-sm-12 col-md-6\">
                                <label>Initials</label>
                                <canvas id=\"initials\" style=\"border: 1px solid #ccc; border-radius: 5px; width: 100%; height: 260px;\"></canvas>
                                <textarea class=\"d-none\" name=\"mopar_company_initials\">{$values['mopar_company_initials']}</textarea>
                                <a class=\"btn btn-warning text-white\" name=\"clear_initials\">Clear</a>
                            </div>
                        </div>

                        <div class=\"form-row\">
                            <div class=\"form-group col-sm-12 text-right\">
                                <input name=\"save_mopar_settings\" type=\"submit\" value=\"Save Settings\" class=\"btn btn-primary\">
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    ";
}

function taller_company_settings_get_items()
{
    global $wpdb;
    $field_names = array_map(function ($field) {
        return $field['name'];
    }, TALLER_COMPANY_SETTINGS_FIELDS);
    $option_names = implode("','", $field_names);
    $option_names = "'{$option_names}'";
    $values = [];
    foreach ($wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name IN ({$option_names})") as $record) {
        $values[$record->option_name] = $record->option_value;
    }
    foreach ($field_names as $field) if (!isset($values[$field])) $values[$field] = '';
    return $values;
}
