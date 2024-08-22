<?php

function taller_company_settings()
{
    global $wpdb;
    $fields = [
        ['name' => 'mopar_company_name', 'label' => "Direct Contractor's Name"],
        ['name' => 'mopar_license_number', 'label' => "Direct Contractor's License Number"],
        ['name' => 'mopar_company_address', 'label' => "Direct Contractor's Address"],
        ['name' => 'mopar_city_state_zip', 'label' => "City, State & ZIP"],
        ['name' => 'mopar_company_phone_number', 'label' => "Direct Contractor's Telephone - FAX"],
        ['name' => 'mopar_company_email', 'label' => "Direct Contractor's Email"],
        ['name' => 'mopar_insurance_policy', 'label' => 'Insurance Company Name'],
        ['name' => 'mopar_insurance_phone', 'label' => 'Insurance Company Telephone'],
    ];

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

    $option_names = implode("','", $field_names);
    $option_names = "'{$option_names}'";
    $values = [];
    foreach ($wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name IN ({$option_names})") as $record) {
        $values[$record->option_name] = $record->option_value;
    }

    $inputs = '';
    foreach ($fields as $field) {
        $value = isset($values[$field['name']]) ? $values[$field['name']] : '';
        $inputs .= "
            <div class=\"form-group col-xs-12 col-md-3\">
                <label>{$field['label']}</label>
            </div>
            <div class=\"form-group col-xs-12 col-md-3\">
                <input type=\"text\" name=\"{$field['name']}\" class=\"form-control\" value=\"{$value}\">
            </div>
        ";
    }

    echo "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
        <div class=\"box pr-4\">
            <div class=\"box-header mb-4 ml-4 mt-4\">
                <div class=\"row\">
                    <div class=\"col-xs-12\">
                        <h2 class=\"font-weight-light text-center text-muted float-left\">Company Settings</h2>
                    </div>
                </div>
            </div>
            <div class=\"box-body\">
                <form method=\"POST\">
                    <div class=\"col-xs-12\">
                        <div class=\"form-row\">

                            {$inputs}
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
