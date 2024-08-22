<?php

function taller_personal_settings()
{
    global $wpdb;
    $user_id = get_current_user_id();
    $fields = [
        ['name' => 'mopar_phone_number', 'label' => "Phone Number"],
        ['name' => 'mopar_first_name', 'label' => "First Name"],
        ['name' => 'mopar_last_name', 'label' => "Last Name"],
    ];

    $field_names = array_map(function ($field) {
        return $field['name'];
    }, $fields);

    if (isset($_POST['save_mopar_personal_settings'])) {
        foreach ($field_names as $name) {
            if (isset($_POST[$name])) {
                $umeta_id = $wpdb->get_var("SELECT umeta_id FROM {$wpdb->prefix}usermeta WHERE meta_key = '{$name}' AND user_id = {$user_id}");
                if ($umeta_id) $wpdb->update("{$wpdb->prefix}usermeta", ['meta_value' => $_POST[$name]], ['umeta_id' => $umeta_id]);
                else $wpdb->insert("{$wpdb->prefix}usermeta", ['meta_key' => $name, 'meta_value' => $_POST[$name], 'user_id' => $user_id]);
            }
        }
    }

    $meta_keys = implode("','", $field_names);
    $meta_keys = "'{$meta_keys}'";
    $values = [];
    foreach ($wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key IN ({$meta_keys}) AND user_id = {$user_id}") as $record) {
        $values[$record->meta_key] = $record->meta_value;
    }

    $inputs = '';
    foreach ($fields as $field) {
        $value = isset($values[$field['name']]) ? $values[$field['name']] : '';
        $inputs .= "
            <div class=\"form-row\">
                <div class=\"form-group col-sm-12 col-md-2\">
                    <label>{$field['label']}</label>
                </div>
                <div class=\"form-group col-sm-12 col-md-4\">
                    <input type=\"text\" name=\"{$field['name']}\" class=\"form-control\" value=\"{$value}\">
                </div>
            </div>
        ";
    }

    // estimate email template
    $default_estimate_email_template = "
Dear [customer],
We have prepared your project located at [address1] - [address2] - [city], [state] [zip].
Please find the attached estimate for your review. If you have any questions or need further information, feel free to reach out to me.

Best regards,
[name]
[phone]
FHS Construction INC
    ";
    if (isset($_POST['estimate_email_template'])) {
        $umeta_id = $wpdb->get_var("SELECT umeta_id FROM {$wpdb->prefix}usermeta WHERE meta_key = 'estimate_email_template' AND user_id = {$user_id}");
        if ($umeta_id) $wpdb->update("{$wpdb->prefix}usermeta", ['meta_value' => $_POST['estimate_email_template']], ['umeta_id' => $umeta_id]);
        else $wpdb->insert("{$wpdb->prefix}usermeta", ['meta_key' => 'estimate_email_template', 'meta_value' => $_POST['estimate_email_template'], 'user_id' => $user_id]);
    }
    $stored_estimate_email_template = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = 'estimate_email_template' AND user_id = {$user_id}");
    $estimate_email_template = $stored_estimate_email_template ? $stored_estimate_email_template : $default_estimate_email_template;
    $estimate_email_template_rows = count(explode("\n", $estimate_email_template));
    $estimate_email_template_rows --;

    echo "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
        <div class=\"box pr-4\">
            <div class=\"box-header mb-4 ml-4 mt-4\">
                <div class=\"row\">
                    <div class=\"col-sm-12\">
                        <h2 class=\"font-weight-light text-center text-muted float-left\">personal Settings</h2>
                    </div>
                </div>
            </div>
            <div class=\"box-body\">
                <form method=\"POST\">
                    <div class=\"col-sm-12 col-sm-12\">
                        {$inputs}
                        <div class=\"form-row\">
                            <div class=\"col-sm-12\">
                                When sending estimates via email, you can use specific tags in the subject and message fields to automatically fill in customer and user details. These tags are placeholders that will be replaced with the actual information when the email is generated. Here are the available tags:
                                <ol>
                                    <li>[customer]: Inserts the customer's name.</li>
                                    <li>[address]: Inserts the customer's primary address line.</li>
                                    <li>[address2]: Inserts the customer's secondary address line (e.g., apartment or suite number).</li>
                                    <li>[city]: Inserts the customer's city.</li>
                                    <li>[zip]: Inserts the customer's ZIP code.</li>
                                    <li>[name]: Inserts your full name.</li>
                                    <li>[phone]: Inserts your phone number.</li>
                                </ol>
                            </div>
                        </div>
                        <div class=\"form-row\">
                            <div class=\"col-sm-12\">
                                <textarea rows=\"{$estimate_email_template_rows}\" style=\"width: 100%\" name=\"estimate_email_template\">{$estimate_email_template}</textarea>
                            </div>
                        </div>
                        <div class=\"form-row\">
                            <div class=\"form-group col-sm-12 text-right\">
                                <input name=\"save_mopar_personal_settings\" type=\"submit\" value=\"Save Settings\" class=\"btn btn-primary\">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    ";
}
