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
            if (isset($_POST[$name])) taller_set_personal_settings($name);
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

    $uncompiled_email_template = taller_get_personal_settings(false);

    // estimate email template
    $default_estimate_email_template = $uncompiled_email_template['estimate_email_template'];
    if (isset($_POST['estimate_email_template'])) taller_set_personal_settings('estimate_email_template');

    $estimate_email_template = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = 'estimate_email_template' AND user_id = {$user_id}");
    if (!$estimate_email_template) {
        $estimate_email_template = $default_estimate_email_template;
    }
    $estimate_email_template_rows = count(explode("\n", $estimate_email_template));
    $estimate_email_template_rows--;

    // unsigned contract email template
    $default_unsigned_contract_email_template = $uncompiled_email_template['unsigned_contract_email_template'];
    if (isset($_POST['unsigned_contract_email_template'])) taller_set_personal_settings('unsigned_contract_email_template');

    $unsigned_contract_email_template = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = 'unsigned_contract_email_template' AND user_id = {$user_id}");
    if (!$unsigned_contract_email_template) {
        $unsigned_contract_email_template = $default_unsigned_contract_email_template;
    }
    $unsigned_contract_email_template_rows = count(explode("\n", $unsigned_contract_email_template));
    $unsigned_contract_email_template_rows--;

    // signed contract email template
    $default_signed_contract_email_template = $uncompiled_email_template['signed_contract_email_template'];
    if (isset($_POST['signed_contract_email_template'])) taller_set_personal_settings('signed_contract_email_template');

    $signed_contract_email_template = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = 'signed_contract_email_template' AND user_id = {$user_id}");
    if (!$signed_contract_email_template) {
        $signed_contract_email_template = $default_signed_contract_email_template;
    }
    $signed_contract_email_template_rows = count(explode("\n", $signed_contract_email_template));
    $signed_contract_email_template_rows--;

    echo "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\">
        <div class=\"box pr-4\">
            <div class=\"box-header mb-4 ml-4 mt-4\">
                <div class=\"row\">
                    <div class=\"col-sm-12\">
                        <h2 class=\"font-weight-light text-center text-muted float-left\">Personal Settings</h2>
                    </div>
                </div>
            </div>
            <div class=\"box-body\">
                <form method=\"POST\">
                    <div class=\"col-sm-12 col-sm-12\">
                        {$inputs}

                        <br>
                        <br>
                        <h4> Email Template for Estimation </h4>
                        <hr>
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

                        <br>
                        <br>
                        <h4> Email Template for unsigned contract </h4>
                        <hr>
                        <div class=\"form-row\">
                            <div class=\"col-sm-12\">
                                When sending unsigned contracts via email, you can use specific tags in the subject and message fields to automatically fill in customer and user details. These tags are placeholders that will be replaced with the actual information when the email is generated. Here are the available tags:
                                <ol>
                                    <li>[customer]: Inserts the customer's name.</li>
                                    <li>[address]: Inserts the customer's primary address line.</li>
                                    <li>[address2]: Inserts the customer's secondary address line (e.g., apartment or suite number).</li>
                                    <li>[city]: Inserts the customer's city.</li>
                                    <li>[zip]: Inserts the customer's ZIP code.</li>
                                    <li>[sign_link]: Inserts link to be clicked by the client to sign the contract.</li>
                                    <li>[name]: Inserts your full name.</li>
                                    <li>[phone]: Inserts your phone number.</li>
                                </ol>
                            </div>
                        </div>
                        <div class=\"form-row\">
                            <div class=\"col-sm-12\">
                                <textarea rows=\"{$unsigned_contract_email_template_rows}\" style=\"width: 100%\" name=\"contract_email_template\">{$unsigned_contract_email_template}</textarea>
                            </div>
                        </div>

                        <br>
                        <br>
                        <h4> Email Template for signed contract </h4>
                        <hr>
                        <div class=\"form-row\">
                            <div class=\"col-sm-12\">
                                When sending signed contracts via email, you can use specific tags in the subject and message fields to automatically fill in customer and user details. These tags are placeholders that will be replaced with the actual information when the email is generated. Here are the available tags:
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
                                <textarea rows=\"{$signed_contract_email_template_rows}\" style=\"width: 100%\" name=\"contract_email_template\">{$signed_contract_email_template}</textarea>
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

function taller_set_personal_settings($name)
{
    global $wpdb;
    $user_id = get_current_user_id();
    $umeta_id = $wpdb->get_var("SELECT umeta_id FROM {$wpdb->prefix}usermeta WHERE meta_key = '{$name}' AND user_id = {$user_id}");
    if ($umeta_id) $wpdb->update("{$wpdb->prefix}usermeta", ['meta_value' => $_POST[$name]], ['umeta_id' => $umeta_id]);
    else $wpdb->insert("{$wpdb->prefix}usermeta", ['meta_key' => $name, 'meta_value' => $_POST[$name], 'user_id' => $user_id]);
}

function taller_get_personal_settings($compile_email_template = true, $user_id = null)
{
    global $wpdb;
    if (null == $user_id) $user_id = get_current_user_id();
    $result = [
        'mopar_phone_number' => '',
        'mopar_first_name' => '',
        'mopar_last_name' => '',
        'estimate_email_template' => "Dear [customer],
We have prepared an estimate for your project located at [address] [address2] - [city], [zip].
Please follow this link to view your estimate: [estimate_link]
If you have any questions or need further information, feel free to reach out to me.



Best regards,
[name]
[phone]
FHS Construction INC
        ",
        'unsigned_contract_email_template' => "Dear [customer],



We have prepared a contract for your project located at [address], [city], [zip].
To view the contract please follow this link [contract_link]
If all the information seems correct click here to sign it [sign_link]



Best regards,
[name]
[phone]
FHS Construction INC
        ",
        'signed_contract_email_template' => "Dear [customer],

We’re excited to inform you that the contract for your project at [address], [city], [zip] has been signed! We will be in touch shortly to discuss the next steps.
Please find the attached file for your review. If you have any questions, feel free to reach out.

Best regards,
[name]
[phone]
FHS Construction Inc.
        "
    ];
    $field_names = array_keys($result);
    $meta_keys = implode("','", $field_names);
    $meta_keys = "'{$meta_keys}'";
    foreach ($wpdb->get_results("SELECT meta_key, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key IN ({$meta_keys}) AND user_id = {$user_id}") as $record) {
        $result[$record->meta_key] = $record->meta_value;
    }

    if ($compile_email_template) foreach (
        [
            'estimate_email_template',
            'unsigned_contract_email_template',
            'signed_contract_email_template',
        ] as $template
    ) {
        $result[$template] = str_replace('[name]', "{$result['mopar_first_name']} {$result['mopar_last_name']}", $result[$template]);
        $result[$template] = str_replace('[phone]', $result['mopar_phone_number'], $result[$template]);
    }

    return $result;
}
