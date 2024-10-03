<?php

define('TALLER_GRECAPTCHA_PUBLIC', '');
define('TALLER_GRECAPTCHA_PRIVATE', '');

add_shortcode('builderla-lead-form', function () {

    wp_register_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . TALLER_GRECAPTCHA_PUBLIC);
    wp_enqueue_script('google-recaptcha');

    wp_register_script('builderla-lead-form', plugin_dir_url(__FILE__) . 'lead-form.js', ['jquery'], '1.0.0');
    wp_localize_script('builderla-lead-form', 'builderla_lead_form_obj', [
        'url' => site_url() . '/wp-admin/admin-ajax.php',
        'action' => 'builderla_lead_form',
        'grecaptcha_public' => TALLER_GRECAPTCHA_PUBLIC
    ]);
    wp_enqueue_script('builderla-lead-form');

    return "
		<style type=\"text/css\">
			.grecaptcha-badge { display: none !important; }
		</style>
        <div class=\"contact-form\" id=\"builderla_lead_form\">
            <div class=\"contact-form-inner\">

                <div class=\"contact-form-row\" builderla-lead-form-step=\"1\">
                    <div class=\"contact-col full\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-text wpcf7-validates-as-required\" aria-required=\"true\" aria-invalid=\"false\" placeholder=\"Name\" value=\"\" type=\"text\" name=\"nombres\">
                    </div>
                    <div class=\"contact-col\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-email wpcf7-validates-as-required wpcf7-text wpcf7-validates-as-email\" aria-required=\"true\" aria-invalid=\"false\" placeholder=\"Email\" value=\"\" type=\"email\" name=\"email\">
                    </div> 
                    <div class=\"contact-col\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-text wpcf7-validates-as-required\" aria-required=\"true\" aria-invalid=\"false\" placeholder=\"Phone\" value=\"\" type=\"text\" name=\"telefono\">
                    </div>
                </div>

                <div class=\"contact-form-row\" builderla-lead-form-step=\"2\">
                    <input type=\"hidden\" name=\"cliente_id\">
                    <div class=\"contact-col full\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-text wpcf7-validates-as-required\" aria-required=\"true\" aria-invalid=\"false\" placeholder=\"Address\" value=\"\" type=\"text\" name=\"street_address\">
                    </div>
                    <div class=\"contact-col full\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-text wpcf7-validates-as-required\" aria-required=\"false\" aria-invalid=\"false\" placeholder=\"Address 2\" value=\"\" type=\"text\" name=\"address_line_2\">
                    </div>
                    <div class=\"contact-col\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-text wpcf7-validates-as-required\" aria-required=\"true\" aria-invalid=\"false\" placeholder=\"City\" value=\"\" type=\"text\" name=\"city\">
                    </div>
                    <div class=\"contact-col\">
                        <input size=\"40\" maxlength=\"400\" class=\"wpcf7-form-control wpcf7-text wpcf7-validates-as-required\" aria-required=\"true\" aria-invalid=\"false\" placeholder=\"ZIP Code\" value=\"\" type=\"text\" name=\"zip_code\">
                    </div>
                    <div class=\"contact-col full\">
                        <p>
                            <span class=\"wpcf7-form-control-wrap\" data-name=\"state\">
                                <select class=\"wpcf7-form-control wpcf7-select\" aria-invalid=\"false\" name=\"state\">
                                    <option value=\"CA\">California (CA)</option>
                                </select>
                            </span>
				        </p>
                    </div>
                </div>

                <div class=\"contact-form-row\" builderla-lead-form-step=\"3\">
                    <input type=\"hidden\" name=\"vehiculo_id\">
                    <div class=\"contact-col full\">
                        <p>
                            <span class=\"wpcf7-form-control-wrap\" data-name=\"additional-comment\">
                                <textarea placeholder=\"Additional Comments\" cols=\"40\" rows=\"10\" maxlength=\"2000\" class=\"wpcf7-form-control wpcf7-textarea\" aria-invalid=\"false\" name=\"solicitud\"></textarea>
                            </span>
                        </p>
                    </div>
                </div>

                <div>
                    <br>
                    <p id=\"message\"></p>
                </div>

                <div class=\"contact-form-row\">
                    <div class=\"contact-col full\" style=\"text-align: right; padding-right: 10px;\">
                        <input style=\"background-color:#333333;\" class=\"wpcf7-form-control wpcf7-submit has-spinner\" id=\"submit_btn\" type=\"submit\" value=\"Next >>\">
                    </div>
                </div>
                
            </div>
        </div>
    ";
});

add_action('wp_ajax_nopriv_builderla_lead_form', function () {
    if (!taller_validate_grecaptcha($_POST['token'])) exit(0);
    else {
        global $wpdb;
        switch ($_POST['step']) {
            case 1:
                $wpdb->insert('clientes', [
                    'nombres' => strtoupper($_POST['nombres']),
                    'email' => strtolower($_POST['email']),
                    'telefono' => $_POST['telefono']
                ]);
                exit('' . $wpdb->insert_id);
                break;
            case 2:
                $wpdb->insert('vehiculos', [
                    'street_address' => $_POST['street_address'],
                    'address_line_2' => $_POST['address_line_2'],
                    'city' => $_POST['city'],
                    'state' => $_POST['state'],
                    'zip_code' => $_POST['zip_code'],
                    'cliente_id' => $_POST['cliente_id']
                ]);
                exit('' . $wpdb->insert_id);
                break;
            case 3:
                $wpdb->insert('solicitud', [
                    'vehiculo_id' => $_POST['vehiculo_id'],
                    'solicitud' => $_POST['solicitud'],
                    'estado' => 1,
                    'photos' => '[]',
                ]);
                exit('' . $wpdb->insert_id);
                break;
        }
    }
});

function taller_validate_grecaptcha($token)
{
    try {
        $context  = stream_context_create([
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query([
                    'secret'   => TALLER_GRECAPTCHA_PRIVATE,
                    'response' => $token,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ])
            ]
        ]);
        $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($result);
        return $result->success;
    } catch (Exception $e) {
        return false;
    }
}
