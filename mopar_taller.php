<?php
/**
 *  Plugin Name: Taller Doctor Mopar
 * 	Plugin URI: http://www.doctormopar.com
 * 	Description: Sencillo plugin para administrar el Taller
 * 	Version: 0.1
 * 	Author: Javier Basso
 * 	Author URI: http://www.doctormopar.com
 */

function theme_options_panel(){
	add_menu_page('Doctor Mopar Taller', 'Doctor Mopar Taller', 'manage_options', 'mopar-taller', 'taller_home_func','dashicons-admin-tools',2);
	add_submenu_page( 'mopar-taller', 'Modelos', 'Modelos', 'manage_options', 'mopar-modelos', 'taller_modelos_func');
	add_submenu_page( 'mopar-taller', 'Clientes', 'Clientes', 'manage_options', 'mopar-clientes', 'taller_clientes_func');
	add_submenu_page( 'mopar-taller', 'Vehiculos', 'Vehiculos', 'manage_options', 'mopar-vehiculos', 'taller_vehiculos_func');
	// add_submenu_page( 'mopar-taller', 'OT', 'OT', 'manage_options', 'mopar-ot', 'taller_ot_func');
	add_submenu_page( 'mopar-taller', 'Solicitudes de Servicio', 'Solicitudes de Servicio', 'manage_options', 'mopar-solicitudes-de-servicio', 'taller_solicitudes_de_servicio_func');
	add_submenu_page( 'mopar-taller', 'Solicitudes Perdidas', 'Solicitudes Perdidas', 'manage_options', 'mopar-perdidas', 'taller_perdidas_func');
	add_submenu_page( 'mopar-taller', 'Solicitudes Agendadas', 'Solicitudes Agendadas', 'manage_options', 'mopar-agendadas', 'taller_agendadas_func');
	add_submenu_page( 'mopar-taller', 'Ordenes de Ingreso', 'Ordenes de Ingreso', 'manage_options', 'mopar-orden-de-ingreso', 'taller_orden_de_ingreso_func');
	add_submenu_page( 'mopar-taller', 'Cotizaciones', 'Cotizaciones', 'manage_options', 'mopar-cotizaciones', 'taller_cotizaciones_func');
	add_submenu_page( 'mopar-taller', 'Trabajos Realizados', 'Trabajos Realizados', 'manage_options', 'mopar-trabajos-realizado', 'taller_trabajos_realizado_func');
	add_submenu_page( 'mopar-taller', 'Preparación Contable', 'Preparación Contable', 'manage_options', 'preparacion-contable', 'taller_preparacion_contable_func');
	add_submenu_page( 'mopar-taller', 'Conciliación Contable', 'Conciliación Contable', 'manage_options', 'conciliacion-contable', 'taller_conciliacion_contable_func');
}
add_action('admin_menu', 'theme_options_panel');
 
function taller_home_func(){
	$events = Mopar::getCalendarEvents();
	include('views/home.php');	
}

function taller_modelos_func(){
    $vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
	
	include('views/modelos.php');	
}


function taller_vehiculos_func(){
    $vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes(['field' => 'id', 'type' => 'DESC']);
	$modelos = Mopar::getModelos();
	
	include('views/vehiculos.php');	
}

function taller_clientes_func(){
	$clientes = Mopar::getClientes();
	include('views/clientes.php');	
}

function taller_ot_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
    $ots = Mopar::getOts();
	include('views/ot.php');	
}

function taller_cotizaciones_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
    $ots = Mopar::getCotizaciones();
	include('views/cotizaciones.php');	
}

function taller_trabajos_realizado_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
    $ots = Mopar::getTrabajosRealizado();
	include('views/trabajos-realizados.php');
}

function taller_preparacion_contable_func(){
	global $wpdb;
	$alert = [];

	if (isset($_POST['add_expense'])) $alert = Mopar::solicitudAddExpense($_POST);
	if (isset($_POST['rewrite_expense'])) $alert = Mopar::solicitudRewriteExpense($_POST);
	if (isset($_POST['edit_expense'])) $alert = Mopar::solicitudEditExpense($_POST);

	$filter_month = isset($_POST['filter_month']) && !isset($_POST['filter_reset']) ? $_POST['filter_month'] : date('m', time());
    $min_year = $wpdb->get_var("SELECT MIN(YEAR(regdate)) FROM solicitud");
	$max_year = $wpdb->get_var("SELECT MAX(YEAR(regdate)) FROM solicitud");
	$filter_year = isset($_POST['filter_year']) && !isset($_POST['filter_reset']) ? $_POST['filter_year'] : date('Y', time());

	$solicituds = Mopar::getPreparacionContable($filter_month, $filter_year);
	include('views/preparacion-contable.php');
}

function taller_conciliacion_contable_func(){
	global $wpdb;
	$alert = [];

	if (isset($_POST['add_expense'])) $alert = Mopar::solicitudAddExpense($_POST);
	if (isset($_POST['rewrite_expense'])) $alert = Mopar::solicitudRewriteExpense($_POST);
	if (isset($_POST['edit_expense'])) $alert = Mopar::solicitudEditExpense($_POST);

	$filter_month = isset($_POST['filter_month']) && !isset($_POST['filter_reset']) ? $_POST['filter_month'] : date('m', time());
    $min_year = $wpdb->get_var("SELECT MIN(YEAR(entregardate)) FROM ot");
	$max_year = $wpdb->get_var("SELECT MAX(YEAR(entregardate)) FROM ot");
	$filter_year = isset($_POST['filter_year']) && !isset($_POST['filter_reset']) ? $_POST['filter_year'] : date('Y', time());

    $solicituds = Mopar::ConciliacionContable($filter_month, $filter_year);
	include('views/conciliacion-contable.php');
}

function taller_solicitudes_de_servicio_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
	$solicituds = Mopar::getSolicitudsDeServicioso();
	include('views/solicitudes_de_servicio.php');	
}

function taller_orden_de_ingreso_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
	$solicituds = Mopar::getOrdenDeIngreso();
	include('views/orden_de_ingreso.php');
}

function taller_perdidas_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
	$solicituds = Mopar::getPerdidas();
	include('views/perdidas.php');
}

function taller_agendadas_func(){
	$vehiculos = Mopar::getVehiculos();
	$clientes = Mopar::getClientes();
	$solicituds = Mopar::getAgendadas();
	include('views/agendadas.php');
}




/* ==============================
ACCIONES CRUD
================================= */



/********** CLIENTES *********/

function eliminar_cliente_callback(){
	global $wpdb;
	$wpdb->delete( 'clientes', ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}

function validate_cliente_callback(){
	$json = Mopar::clienteValidateEmail($_POST['email'], $_POST['id']) ?
		[
			'status' => 'OK'
		]:[
			'status' => 'ERROR',
			'message' => 'El correo del cliente ya está registrado'
		];

	echo json_encode($json);
	exit();
}

function insertar_cliente_callback(){
	global $wpdb;
	$pass = Mopar::randomPassword();
	$array_insert = [
		'nombres' => $_POST['nombres'],
		'apellidoPaterno' => $_POST['apellidoPaterno'],
		'apellidoMaterno' => $_POST['apellidoMaterno'],
		'email' => $_POST['email'],
		'telefono' => $_POST['telefono'],
		'secret' => md5($pass),
		'nuevo' => 1
	];
	$wpdb->insert('clientes',$array_insert);

	//Enviar por correo la pass
	$body = "Hola " . $_POST['nombres'] . "\n\nBienvenido! Has sido creado como cliente en el taller Doctor Mopar, se ha creado un password para acceder a nuestro portal de clientes:\n\n\nTu password es: " . $pass . "\n\n";
	$body .= "https://www.doctormopar.com/clientes/";
	mail($_POST['email'].",j.basso@me.com",'Password para entrar a DoctorMopar',$body);

	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}

function actualizar_cliente_callback(){
	global $wpdb;
	

	$array_edit = [
		'nombres' => $_POST['nombres'],
		'apellidoPaterno' => $_POST['apellidoPaterno'],
		'apellidoMaterno' => $_POST['apellidoMaterno'],
		'email' => $_POST['email'],
		'telefono' => $_POST['telefono']
	];

	if( $_POST['secret'] != "*********" ){
		$array_edit['secret'] = md5($_POST['secret']);

		$body = "Hola " . $_POST['nombres'] . "\n\nSe te ha creado una nueva contraseña para poder acceder y ver el historial de tu vehiculo en el taller Doctor Mopar.\n\nTu nueva contraseña es: " . $_POST['secret'] . "\n\n";
		$body .= "https://www.doctormopar.com/clientes/";
		mail($_POST['email'].",j.basso@me.com",'Nueva contraseña para entrar a DoctorMopar',$body);
	}

	$wpdb->update('clientes',$array_edit,['id' => $_POST['regid']]);

	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit(); 
}



/*********** VEHICULOS ************/


function eliminar_vehiculo_callback(){
	global $wpdb;
	$wpdb->delete( 'vehiculos', ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}



function insertar_vehiculo_callback(){
	global $wpdb;
	//Check Vehiculo Exist
	$sql = "SELECT * FROM vehiculos WHERE patente = '" . $_POST['patente'] . "'";
	$vehiculo = $wpdb->get_row($sql);

	if( $vehiculo ){
		$json = [
			'status' => 'ERROR',
			'msg' => 'La Patente del Vehiculo ya existe en la base de datos'
		];
	} else {

		$array_insert = [
			'patente' => $_POST['patente'],
			'marca' => $_POST['marca'],
			'modelo' => $_POST['modelo'],
			'color' => $_POST['color'],
			'ano' => $_POST['ano'],
			'nro_motor' => $_POST['nro_motor'],
			'cliente_id' => $_POST['cliente'],
			'modelo_id' => $_POST['modelo_id']
		];
		$wpdb->insert('vehiculos',$array_insert);
		$last_query = $wpdb->last_query;
		$json = [
			'status' => 'OK',
			'sql' => $last_query,
			'error' => $wpdb->last_error
		];
	}

	echo json_encode($json);
	exit();  
}


function actualizar_vehiculo_callback(){
	global $wpdb;
	//Check Vehiculo Exist
	$sql = "SELECT * FROM vehiculos WHERE patente = '" . $_POST['patente'] . "' AND id != " . $_POST['regid'];
	$vehiculo = $wpdb->get_row($sql);

	if( $vehiculo ){
		$json = [
			'status' => 'ERROR',
			'msg' => 'La Patente del Vehiculo ya existe en la base de datos'
		];
	} else {

		$array_edit = [
			'patente' => $_POST['patente'],
			'marca' => $_POST['marca'],
			'modelo' => $_POST['modelo'],
			'color' => $_POST['color'],
			'ano' => $_POST['ano'],
			'nro_motor' => $_POST['nro_motor'],
			'cliente_id' => $_POST['cliente'],
			'modelo_id' => $_POST['modelo_id']
		];
		$wpdb->update('vehiculos',$array_edit,['id' => $_POST['regid']]);

		$json = [
			'status' => 'OK',
			'sql' => $wpdb->last_query
		];
	}

	echo json_encode($json);
	exit(); 
}




/********** HISTORIAL *********/

function eliminar_historial_callback(){
	global $wpdb;
	$wpdb->delete( 'historial', ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}





/********** OT *********/

function eliminar_ot_callback(){
	global $wpdb;
	$wpdb->delete( 'ot', ['id' => $_POST['regid']]);
	$wpdb->update('solicitud', ['estado' => 1], ['ot_id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}

function eliminar_solicitud_callback(){
	global $wpdb;
	$wpdb->delete( 'solicitud', ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}

function completar_realizados_callback(){
	global $wpdb;
	$wpdb->update('ot', ['entregar' => 1, 'entregardate' => date('Y-m-d')], ['id' => $_POST['regid']]);
	Mopar::sendMail($_POST['regid'], 'entregar_created');
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();
}

function completar_ot_callback(){
	$solicitud = Mopar::getOneSolicitudByOtId($_POST['regid']);
	if (3 == $solicitud->estado) {
		$json = [
			'status' => 'ERROR',
			'message' => 'Esta cotizacion no tiene una orden de ingreso creada'
		];
	} else {
		global $wpdb;
		$wpdb->update('ot', ['estado' => 2], ['id' => $_POST['regid']]);
		$wpdb->update('solicitud', ['estado' => 5], ['ot_id' => $_POST['regid']]);
		Mopar::sendMail($_POST['regid'], 'realizados_created');
		$json = [
			'status' => 'OK'
		];
	}

	echo json_encode($json);
	exit();  
}

function restaurar_solicitud_callback(){
	global $wpdb;
	$wpdb->update('solicitud', ['motivo' => ''], ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();
}

function cancelar_cita_solicitud_callback(){
	global $wpdb;
	$wpdb->update('solicitud', ['fecha' => null, 'hora' => '00:00:00'], ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();
}

function uncompletar_ot_callback(){
	global $wpdb;
	$wpdb->update('ot', ['estado' => 1], ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();
}

function completar_solicitud_callback(){
	global $wpdb;
	$id = $_POST['regid'];
	$solicitud = Mopar::getOneSolicitud($id);
	if (0 == $solicitud->vehiculo_id) {
		$json = [
			'status' => 'ERROR',
			'message' => 'Antes de continuar debe completar la informacion de esta Solicitud de Servicio'
		];
	} else {
		$wpdb->update('solicitud', ['estado' => 2, 'fecha' => null, 'hora' => '00:00:00'], ['id' => $id]);
		Mopar::sendMail($id, 'ingreso_created');
		$json = [
			'status' => 'OK'
		];
	}

	echo json_encode($json);
	exit();  
}

function uncompletar_solicitud_callback(){
	global $wpdb;
	$solicitud = Mopar::getOneSolicitud($_POST['regid']);
	if (2 == $solicitud->estado) $solicitud->estado = 1;// orden de ingreso, green to red
	else if (4 == $solicitud->estado) $solicitud->estado = 3;// cotization with orden de ingreso, green to yellow
	$wpdb->update('solicitud', ['estado' => $solicitud->estado], ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();
}

function proceed_solicitud_callback(){
	global $wpdb;
	$solicitud = Mopar::getOneSolicitud($_POST['regid']);

	if ($solicitud->ot_id) $wpdb->update('solicitud', ['estado' => 4], ['id' => $_POST['regid']]);
	else {
		$wpdb->insert('ot', [
			'cliente_id' => $solicitud->cliente_id,
			'vehiculo_id' => $solicitud->vehiculo_id,
			'titulo' => '',
			'detalle' => '{"item":[""],"precio":["0"]}',
			'valor' => '',
			'km' => '',
			'estado' => 1,
			'observaciones' => ''
		]);
		$wpdb->update('solicitud', ['estado' => 4, 'ot_id' => $wpdb->insert_id], ['id' => $_POST['regid']]);

	}

	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();
}

function proceed_solicitud_without_ingreso_callback(){
	global $wpdb;
	$solicitud = Mopar::getOneSolicitud($_POST['regid']);
	if (1 != $solicitud->estado) {
		$json = [
			'status' => 'ERROR',
			'message' => 'La creación de esta cotización debe hacerse a través del menu Orden de Ingreso'
		];
	} else if (0 == $solicitud->vehiculo_id) {
		$json = [
			'status' => 'ERROR',
			'message' => 'Antes de continuar debe completar la informacion de esta Solicitud de Servicio'
		];
	} else {
		$wpdb->insert('ot', [
			'cliente_id' => $solicitud->cliente_id,
			'vehiculo_id' => $solicitud->vehiculo_id,
			'titulo' => '',
			'detalle' => '{"item":[""],"precio":["0"]}',
			'valor' => '',
			'km' => '',
			'estado' => 1,
			'observaciones' => ''
		]);

		$wpdb->update('solicitud', ['estado' => 3, 'ot_id' => $wpdb->insert_id], ['id' => $_POST['regid']]);

		$json = [
			'status' => 'OK'
		];
	}

	echo json_encode($json);
	exit();
}

function insertar_ot_callback(){
	global $wpdb;

	if (!function_exists('wp_handle_upload')) {
       require_once(ABSPATH . 'wp-admin/includes/file.php');
   	}
	// echo $_FILES["upload"]["name"];
	$uploadedfile = $_FILES['aditional_file'];
	$upload_overrides = array('test_form' => false);
	$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

    // echo $movefile['url'];
  	if ($movefile && !isset($movefile['error'])) {
     	echo "File is valid, and was successfully uploaded.\n";
    	var_dump( $movefile );
    } else {
		/**
		* Error generated by _wp_handle_upload()
		* @see _wp_handle_upload() in wp-admin/includes/file.php
		*/
		echo $movefile['error'];
    }

	$array_insert = [
		'cliente_id' => $_POST['cliente'],
		'vehiculo_id' => $_POST['vehiculo'],
		'titulo' => $_POST['titulo'],
		'detalle' => json_encode($_POST['detalle']),
		'valor' => $_POST['valor'],
		'km' => $_POST['km'],
		'estado' => $_POST['estado'],
		'observaciones' => $_POST['observaciones']
	];
	$wpdb->insert('ot',$array_insert);

	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}


function editar_ot(){
	global $wpdb;

	Mopar::dd($_POST);

    if (!function_exists('wp_handle_upload')) {
   		require_once(ABSPATH . 'wp-admin/includes/file.php');
   	}
  	
  	$uploadedfile = $_FILES['file'];
	$upload_overrides = array('test_form' => false);
	$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

    // echo $movefile['url'];
  	if ($movefile && !isset($movefile['error'])) {
    	$file_url = $movefile['url'];
    } else {
		$file_url = "";
    }


    $array_update = [
		'cliente_id' => $_POST['cliente'],
		'vehiculo_id' => $_POST['vehiculo'],
		'titulo' => $_POST['titulo'],
		'detalle' => json_encode($_POST['detalle']),
		'valor' => $_POST['valor'],
		'km' => $_POST['km'],
		'estado' => $_POST['estado'],
		'observaciones' => $_POST['observaciones']
	];

	$wpdb->update('ot',$array_update,['id' => $_POST['regid']]);

	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();

}



function editar_ot_callback(){
	global $wpdb;

	echo "hola mundo";
	exit();


	if (!function_exists('wp_handle_upload')) {
       require_once(ABSPATH . 'wp-admin/includes/file.php');
   	}
	// echo $_FILES["upload"]["name"];
	$uploadedfile = $_FILES['aditional_file'];
	Mopar::dd($_FILES);
	$upload_overrides = array('test_form' => false);
	$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

    // echo $movefile['url'];
  	if ($movefile && !isset($movefile['error'])) {
     	echo "File is valid, and was successfully uploaded.\n";
    	var_dump( $movefile );
    } else {
		/**
		* Error generated by _wp_handle_upload()
		* @see _wp_handle_upload() in wp-admin/includes/file.php
		*/
		var_dump( $movefile );
    }

	$array_update = [
		'cliente_id' => $_POST['cliente'],
		'vehiculo_id' => $_POST['vehiculo'],
		'titulo' => $_POST['titulo'],
		'detalle' => json_encode($_POST['detalle']),
		'valor' => $_POST['valor'],
		'km' => $_POST['km'],
		'estado' => $_POST['estado'],
		'observaciones' => $_POST['observaciones']
	];

	$wpdb->update('ot',$array_update,['id' => $_POST['regid']]);

	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}

function get_vehiculos_by_cliente_callback(){
	$cliente_id = $_POST['cliente_id'];
	$vehiculos = Mopar::getVehiculosByCliente($cliente_id);

	$json = [
		'vehiculos' => $vehiculos
	];

	echo json_encode($json);
	exit(); 
}



function get_ot_callback(){
	$ot_id = $_POST['ot_id'];
	$ot = Mopar::getOneOt($ot_id);

	$vehiculos = Mopar::getVehiculosByCliente($ot->cliente_id);
	$cliente = Mopar::getOneCliente($ot->cliente_id);

	$json = [
		'ot' => $ot,
		'vehiculos' => $vehiculos,
		'detalle' => json_decode($ot->detalle),
		'cliente' => $cliente
	];

	echo json_encode($json);
	exit();  
}

function get_solicitud_callback(){
	$solicitud_id = $_POST['solicitud_id'];
	$solicitud = Mopar::getOneSolicitud($solicitud_id);

	$vehiculos = Mopar::getVehiculosByCliente($solicitud->cliente_id);
	$cliente = Mopar::getOneCliente($solicitud->cliente_id);

	$json = [
		'solicitud' => $solicitud,
		'vehiculos' => $vehiculos,
		'cliente' => $cliente
	];

	echo json_encode($json);
	exit();  
}


/************** MODELOS *****************/
function eliminar_modelo_callback(){
	global $wpdb;
	$wpdb->delete( 'modelos', ['id' => $_POST['regid']]);
	$json = [
		'status' => 'OK'
	];

	echo json_encode($json);
	exit();  
}



function get_modelo_callback(){
	global $wpdb;

	$modelo_id = $_POST['modelo_id'];
	$modelo = $wpdb->get_row('SELECT * FROM modelos WHERE id = ' . $modelo_id);

	$json = [
		'modelo' => $modelo
	];

	echo json_encode($json);
	exit();  
}

function mopar_taller_select2_clientes () {
	register_rest_route('mopar-taller/v1', '/clientes', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function () {
			return Mopar::getSelect2Clientes();
		}
	]);
}

//Clientes
add_action('wp_ajax_validate_cliente','validate_cliente_callback');
add_action('wp_ajax_insertar_cliente','insertar_cliente_callback');
add_action('wp_ajax_actualizar_cliente','actualizar_cliente_callback');
add_action('wp_ajax_eliminar_cliente','eliminar_cliente_callback');

//Vehiculos
add_action('wp_ajax_insertar_vehiculo','insertar_vehiculo_callback');
add_action('wp_ajax_actualizar_vehiculo','actualizar_vehiculo_callback');
add_action('wp_ajax_eliminar_vehiculo','eliminar_vehiculo_callback');

//Historial
add_action('wp_ajax_eliminar_historial','eliminar_historial_callback');

//Modelos
add_action('wp_ajax_eliminar_modelo','eliminar_modelo_callback');
add_action('wp_ajax_get_modelo','get_modelo_callback');

//OT
add_action('wp_ajax_insertar_ot','insertar_ot_callback');
add_action( 'wp_ajax_md_support_save','editar_ot' );
add_action( 'wp_ajax_nopriv_md_support_save','editar_ot' );
add_action('wp_ajax_eliminar_ot','eliminar_ot_callback');
add_action('wp_ajax_eliminar_solicitud','eliminar_solicitud_callback');
add_action('wp_ajax_completar_ot','completar_ot_callback');
add_action('wp_ajax_uncompletar_ot','uncompletar_ot_callback');
add_action('wp_ajax_completar_realizados','completar_realizados_callback');
add_action('wp_ajax_restaurar_solicitud','restaurar_solicitud_callback');
add_action('wp_ajax_cancelar_cita_solicitud','cancelar_cita_solicitud_callback');
add_action('wp_ajax_completar_solicitud','completar_solicitud_callback');
add_action('wp_ajax_uncompletar_solicitud','uncompletar_solicitud_callback');
add_action('wp_ajax_proceed_solicitud','proceed_solicitud_callback');
add_action('wp_ajax_proceed_solicitud_without_ingreso','proceed_solicitud_without_ingreso_callback');
add_action('wp_ajax_get_vehiculos_by_cliente','get_vehiculos_by_cliente_callback');
add_action('wp_ajax_get_ot','get_ot_callback');
add_action('wp_ajax_get_solicitud','get_solicitud_callback');
add_action('rest_api_init', 'mopar_taller_select2_clientes');

class Mopar{

	public static function getModelos(){
		global $wpdb;
    	$modelos = $wpdb->get_results('SELECT * FROM modelos');

    	return $modelos;
	}

	public static function getClientes($sorting = ['field' => 'apellidoPaterno', 'type' => 'ASC']){
		global $wpdb;
		$clientes = $wpdb->get_results("SELECT * FROM clientes ORDER BY {$sorting['field']} {$sorting['type']}");
    	return $clientes;
	}

	public static function getSelect2Clientes(){
		global $wpdb;
		$clientes = $wpdb->get_results("
			SELECT id, CONCAT(nombres, ' ', apellidoPaterno, ' ', apellidoMaterno) text
			FROM clientes
			WHERE CONCAT(apellidoPaterno, ' ', apellidoMaterno, ' ', nombres) LIKE '%{$_GET['q']}%'
			ORDER BY id DESC
			LIMIT 10
		");
		return ['results' => $clientes];
	}

	public static function getOneCliente($cliente_id){
		global $wpdb;
    	$cliente = $wpdb->get_row('SELECT * FROM clientes where id = ' . $cliente_id);

    	return $cliente;
	}

	public static function clienteValidateEmail($email, $client_id = 0){
		global $wpdb;
		$clientes = $wpdb->get_results($wpdb->prepare('SELECT id FROM clientes where email = %s', $email));

		return 0 === $client_id ? 0 === count($clientes) : 0 === count($clientes) || $clientes[0]->id == $client_id;
	}

	public static function getVehiculos(){
		global $wpdb;
    	$vehiculos = $wpdb->get_results('SELECT * FROM vehiculos');

    	return $vehiculos;
	}

	public static function getOneVehiculo($vehiculo_id){
		global $wpdb;
    	$cliente = $wpdb->get_row('SELECT * FROM vehiculos where id = ' . $vehiculo_id);

    	return $cliente;
	}

	public static function getVehiculosByCliente($cliente_id){
		global $wpdb;
		$cliente = $wpdb->get_results($wpdb->prepare("
			SELECT vehiculos.*, modelos.imagen
			FROM vehiculos
			LEFT JOIN modelos ON vehiculos.modelo_id = modelos.id
			WHERE cliente_id = %d
		", $cliente_id));

    	return $cliente;
	}

	public static function getSolicitudsDeServicioso(){
		global $wpdb;
		$solicituds = $wpdb->get_results('SELECT * FROM solicitud WHERE estado IN (1,2,3,4,5) ORDER BY id DESC');

    	return $solicituds;
	}

	public static function getPreparacionContable($month, $year){
		/*
			1) Cotizaciones, without a trabajo realizado document (4)
			2) Ordenes de ingreso without a cotización (2)
			3) trabajos realizados that have not been delivered to their owners yet (5 && !entragar)
		*/
		//Mopar::solicitudUpdateTotalOldRecord();
		global $wpdb;
		$solicituds = $wpdb->get_results($wpdb->prepare("
			SELECT
				solicitud.*
				, CASE
					WHEN 2 = solicitud.estado THEN 'ORDEN DE INGRESO'
					WHEN 4 = solicitud.estado THEN 'COTIZACIÓN'
					ELSE 'TRABAJO REALIZADO'
				END tipo_de_documento
			FROM solicitud
			LEFT JOIN ot ON solicitud.ot_id = ot.id
			WHERE (solicitud.estado IN (2,4) OR (solicitud.estado = 5 AND 0 = ot.entregar))
			AND MONTH(solicitud.regdate) = %d AND YEAR(solicitud.regdate) = %d
			ORDER BY id DESC
		", $month, $year
		));

		return $solicituds;
	}

	public static function ConciliacionContable($month, $year){
		// 1) trabajos realizados that have been delivered to their owners
		//Mopar::solicitudUpdateTotalOldRecord();
		global $wpdb;
		$solicituds = $wpdb->get_results($wpdb->prepare("
			SELECT
				solicitud.*
				, ot.entregardate
			FROM solicitud
			LEFT JOIN ot ON solicitud.ot_id = ot.id
			WHERE (solicitud.estado = 5 AND 1 = ot.entregar)
			AND MONTH(ot.entregardate) = %d AND YEAR(ot.entregardate) = %d
			ORDER BY id DESC
		", $month, $year));

		return $solicituds;
	}

	public static function getOrdenDeIngreso(){
		global $wpdb;
		$solicituds = $wpdb->get_results("
			SELECT
				solicitud.*
				, ot.estado ot_estado
			FROM solicitud
			LEFT JOIN ot ON solicitud.ot_id = ot.id
			WHERE
				solicitud.estado = 2
				OR solicitud.estado = 4
				OR (solicitud.estado = 5 AND ot.entregar <> 1)
			ORDER BY solicitud.id DESC
		");

    	return $solicituds;
	}

	public static function getPerdidas(){
		global $wpdb;
		$solicituds = $wpdb->get_results('SELECT * FROM solicitud WHERE "" <> motivo ORDER BY id DESC');

		return $solicituds;
	}

	public static function getAgendadas(){
		global $wpdb;
		$solicituds = $wpdb->get_results('SELECT * FROM solicitud WHERE fecha IS NOT NULL ORDER BY id DESC');

		return $solicituds;
	}

	public static function getOneSolicitud($id){
		global $wpdb;
    	$solicitud = $wpdb->get_row('SELECT * FROM solicitud WHERE id = ' . $id);

    	return $solicitud;
	}

	public static function getOneSolicitudByOtId($ot_id){
		global $wpdb;
		$solicitud = $wpdb->get_row('SELECT * FROM solicitud WHERE ot_id = ' . $ot_id);

		return $solicitud;
	}

	public static function getCalendarEvents() {
		global $wpdb;
		return array_map(function ($record) {
			$record->url = site_url("wp-admin/admin.php?page=mopar-solicitudes-de-servicio&id=$record->id");
			return $record;
		}, $wpdb->get_results("
			SELECT
				solicitud.id
				, CONCAT(clientes.nombres, ' ', clientes.apellidoPaterno) 'title'
				, CONCAT(fecha, ' ', hora) 'start'
			FROM solicitud
				LEFT JOIN clientes ON solicitud.cliente_id = clientes.id
				LEFT JOIN vehiculos ON solicitud.vehiculo_id = vehiculos.id
			WHERE solicitud.fecha IS NOT NULL
		"));
	}

	public static function getBlueprintBySolicitudId ($id) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("
			SELECT
				modelos.blueprint
			FROM solicitud
			LEFT JOIN vehiculos ON solicitud.vehiculo_id = vehiculos.id
			LEFT JOIN modelos ON vehiculos.modelo_id = modelos.id
			WHERE solicitud.id = %d
		", $id));
	}

	public static function solicitudAddExpense ($params) {
		$alert = [];
		$solicitud = Mopar::getOneSolicitud($params['solicitud_id']);
		if (!$solicitud) $alert = ['type' => 'red', 'content' => 'Error: document not found!'];
		else {
			$solicitud->expense = empty($solicitud->expense) ? [] : json_decode($solicitud->expense);
			$solicitud->expense[] = [
				'proveedor' => $params['proveedor'],
				'monto' => $params['monto'],
				'detalle' => $params['detalle'],
				'tipo_de_documento' => $params['tipo_de_documento'],
			];
			$solicitud->expense = json_encode($solicitud->expense);
			Mopar::solicitudCalculateBuying($solicitud);
			$alert = ['type' => 'green', 'content' => 'Success: expense sucessfully added!'];
		}
		return $alert;
	}

	public static function solicitudRewriteExpense ($params) {
		$alert = [];
		$solicitud = Mopar::getOneSolicitud($params['solicitud_id']);
		if (!$solicitud) $alert = ['type' => 'red', 'content' => 'Error: document not found!'];
		else {
			$solicitud->expense = [];
			foreach ($params['expense']['proveedor'] as $index => $value)
			$solicitud->expense[$index] = [
				'proveedor' => $params['expense']['proveedor'][$index],
				'monto' => $params['expense']['monto'][$index],
				'detalle' => $params['expense']['detalle'][$index],
				'tipo_de_documento' => $params['expense']['tipo_de_documento'][$index],
			];
			$solicitud->expense = json_encode($solicitud->expense);
			Mopar::solicitudCalculateBuying($solicitud);
			$alert = ['type' => 'green', 'content' => 'Success: expense sucessfully added!'];
		}
		return $alert;
	}

	public static function solicitudEditExpense ($params) {
		$alert = [];
		$solicitud = Mopar::getOneSolicitud($params['solicitud_id']);
		if (!$solicitud) $alert = ['type' => 'red', 'content' => 'Error: document not found!'];
		else {
			$solicitud->expense = json_decode($solicitud->expense);
			$solicitud->expense[$params['expense_index']] = [
				'proveedor' => $params['proveedor'],
				'monto' => $params['monto'],
				'detalle' => $params['detalle'],
				'tipo_de_documento' => $params['tipo_de_documento'],
			];
			$solicitud->expense = json_encode($solicitud->expense);
			Mopar::solicitudCalculateBuying($solicitud);
			$alert = ['type' => 'green', 'content' => 'Success: expense sucessfully added!'];
		}
		return $alert;
	}

	protected static function solicitudCalculateBuying($solicitud) {
		global $wpdb;
		$solicitud->iva_credito = 0;
		$solicitud->gastos = 0;
		$solicitud->utilidad = 0;
		foreach (json_decode($solicitud->expense) as $expense) {
			switch ($expense->tipo_de_documento) {
				case 'FACTURA':
					$solicitud->iva_credito = (int) $solicitud->iva_credito + ((int)$expense->monto * 0.19);
				case 'BOLETA':
				case 'SIN COMPROBANTE':
					$solicitud->gastos = (int) $solicitud->gastos + (int)$expense->monto;
					break;
			}
		}
		$wpdb->update('solicitud', [
			'expense' => $solicitud->expense
			, 'iva_credito' => $solicitud->iva_credito
			, 'gastos' => $solicitud->gastos
			, 'utilidad' => (int) $solicitud->total - (int) $solicitud->iva_debito - (int) $solicitud->gastos + (int) $solicitud->iva_credito
		], ['id' => $solicitud->id]);
	}

	protected static function solicitudUpdateTotalOldRecord() {
		global $wpdb;
		$empty_detalle = '{"item":[""],"precio":["0"]}';
		$uncalculateds = $wpdb->get_results("
			SELECT
				solicitud.ot_id
			FROM solicitud
			LEFT JOIN ot ON solicitud.ot_id = ot.id
			WHERE solicitud.total < 1 AND ot.detalle <> '{$empty_detalle}';
		");
		foreach ($uncalculateds as $solicitud) Mopar::solicitudCalculateSelling($solicitud->ot_id);
	}

	public static function solicitudCalculateSelling($ot_id) {
		$solicitud = Mopar::getOneSolicitudByOtId($ot_id);
		if (!$solicitud) return false;
		$ot = Mopar::getOneOt($ot_id);
		if (!$ot) return false;
		$detalle = json_decode($ot->detalle);
		$total = (int) 0;
		foreach ($detalle->precio as $precio) $total += (int) $precio;
		global $wpdb;
		$wpdb->update('solicitud', ['total' => $total, 'iva_debito' => 0.19 * $total], ['id' => $solicitud->id]);
	}

	public static function getOts(){
		global $wpdb;
    	$ots = $wpdb->get_results('SELECT * FROM ot ORDER BY id DESC');

    	return $ots;
	}

	public static function getCotizaciones(){
		global $wpdb;
		$ots = $wpdb->get_results("
			SELECT
				ot.*
				, solicitud.estado solicitud_estado
			FROM ot
			LEFT JOIN solicitud ON ot.id = solicitud.ot_id
			WHERE ot.estado IN (1, 2)
			ORDER BY id DESC
		");

    	return $ots;
	}

	public static function getTrabajosRealizado(){
		global $wpdb;
    	$ots = $wpdb->get_results('SELECT * FROM ot WHERE estado = 2 ORDER BY id DESC');

    	return $ots;
	}

	public static function getOneOt($ot_id){
		global $wpdb;
    	$ot = $wpdb->get_row('SELECT * FROM ot WHERE id = ' . $ot_id);

    	return $ot;
	}


	public static function getOtByVehiculo($vehiculo_id){
		global $wpdb;
    	$ot = $wpdb->get_results('SELECT * FROM ot WHERE vehiculo_id = ' . $vehiculo_id);

    	return $ot;
	}

	public static function getOtByCliente($cliente_id){
		global $wpdb;
    	$ot = $wpdb->get_results('SELECT * FROM ot WHERE cliente_id = ' . $cliente_id);

    	return $ot;
	}

	public static function getNombreCliente($cliente_id, $apellido_primero=true){
		global $wpdb;
		if( $cliente_id ):
			$cliente = Mopar::getOneCliente($cliente_id);
			if (!$cliente) return '';

			if( $apellido_primero )
				$nombre_cliente = $cliente->apellidoPaterno . " " . $cliente->apellidoMaterno . " " . $cliente->nombres;
			else
				$nombre_cliente = $cliente->nombres . " " . $cliente->apellidoPaterno . " " . $cliente->apellidoMaterno;

			return $nombre_cliente;
		else:
			return "";
		endif;
	}

	public static function getNombreVehiculo($vehiculo_id){
		global $wpdb;
		$sql = 'SELECT marca,modelo,patente FROM vehiculos where id = ' . $vehiculo_id;
		$vehiculo = $wpdb->get_row($sql);
		$nombre_vehiculo = $vehiculo->marca . " - " . $vehiculo->modelo . " - " . $vehiculo->patente;

		return $nombre_vehiculo;
	}

	public static function getEstado($estado_id){
		switch ($estado_id) {
			case 1: $estado = 'Cotización'; break;
        	case 2: $estado = 'Trabajo Realizado'; break;
        	case 3: $estado = 'Trabajo NO Realizado'; break;
			default: $estado = ''; break;
		}

		return $estado;
	}

	public static function getSolicitudEstado($estado_id){
		switch ($estado_id) {
			case 1: $estado = 'Solicitudes de Servicio'; break;
        	case 2: $estado = 'Orden de Ingreso'; break;
        	case 3: $estado = 'Cotización without Ingreso'; break;
        	case 4: $estado = 'Cotización with Ingreso'; break;
			case 5: $estado = 'Trabajo Realizado'; break;
			default: $estado = ''; break;
		}

		return $estado;
	}

	public static function dd($array, $stop=true){
	    echo "<pre>";
	    print_r($array);
	    echo "</pre>";
	    if($stop){
	        exit();
	    }
	}

	public static function getNombreMes($num, $acortar = false){
	    $strMes = '';
	    switch( $num ){
	        case 1: $strMes = 'Enero'; break;
	        case 2: $strMes = 'Febrero'; break;
	        case 3: $strMes = 'Marzo'; break;
	        case 4: $strMes = 'Abril'; break;
	        case 5: $strMes = 'Mayo'; break;
	        case 6: $strMes = 'Junio'; break;
	        case 7: $strMes = 'Julio'; break;
	        case 8: $strMes = 'Agosto'; break;
	        case 9: $strMes = 'Septiembre'; break;
	        case 10: $strMes = 'Octubre'; break;
	        case 11: $strMes = 'Noviembre'; break;
	        case 12: $strMes = 'Diciembre'; break;
	        default: $strMes = ''; break;
	    }
	    
	    if( $acortar ){
	        return substr($strMes,0,3);   
	    } else {
	        return $strMes;
	    }
	}


	public static function randomPassword() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	public static function sendMail ($entity_id, $event) {
		$recipient = '';
		$subject = '';
		$message = '';
		switch ($event) {
			case 'fecha_updated':
				$solicitud = Mopar::getOneSolicitud($entity_id);
				$cliente = Mopar::getOneCliente($solicitud->cliente_id);
				$recipient = $cliente->email;
				$subject = 'Su hora al taller ha sido agendada!';
				$fecha = date_create("{$solicitud->fecha} {$solicitud->hora}");
				$day = date_format($fecha, 'd');
				$month = Mopar::getNombreMes(date_format($fecha, 'm'));
				$year = date_format($fecha, 'Y');
				$hour = date_format($fecha, 'H');
				$minute = date_format($fecha, 'i');
				$message = "{$cliente->nombres}:

Gracias por agendar una hora con Doctor Mopar. Tu cita está programada para el día {$day} de {$month} de {$year} a las {$hour}:{$minute}! Si necesitas cambiar tu hora, no dudes en contactarnos.
Te esperamos!

Atentamente,
Catalina Heckmann
Servicio al cliente
+56985991053";
				break;
			case 'ingreso_created':
				$solicitud = Mopar::getOneSolicitud($entity_id);
				$cliente = Mopar::getOneCliente($solicitud->cliente_id);
				$recipient = $cliente->email;
				$vehicle = Mopar::getOneVehiculo($solicitud->vehiculo_id);

				$subject = 'Estamos reparando su vehículo!';
				$message = "{$cliente->nombres}:
	
Nos complace informarte que tu {$vehicle->marca} {$vehicle->modelo} está siendo atendido por nuestro equipo de profesionales.

Durante el proceso de servicio, si tienes alguna pregunta o necesitas alguna información adicional, no dudes en ponerte en contacto con nosotros. Estamos aquí para ayudarte en todo momento y asegurarnos de que tengas la mejor experiencia.

Te mantendremos informado sobre el progreso de tu vehículo y te notificaremos cuando esté listo para ser retirado. Gracias nuevamente por elegirnos y confiar en nuestro taller.

Marco Alvarado
Jefe de Taller
+56985991053";
				break;
			case 'realizados_created':
				$ot = Mopar::getOneOt($entity_id);
				$cliente = Mopar::getOneCliente($ot->cliente_id);
				$recipient = $cliente->email;

				$subject = 'Su vehículo está listo!';
				$message = "{$cliente->nombres}:

Nos complace informarte que tu vehículo ha sido completamente atendido y se encuentra listo para ser retirado en nuestro taller. Estamos seguros de que notarás la diferencia en el rendimiento y el estado de tu vehículo!

Para acceder a una descripción detallada y los valores de los servicios realizados en tu vehículo, ingresa al portal del cliente usando tu usuario y password, siguiendo este enlace: https://www.doctormopar.com/clientes/

Datos de transferencia:
Banco Santander
Cuenta Corriente N° 84154814
Javier Basso
17.266.522-5
taller@doctormopar.com

Por favor, acércate a nuestro taller en Los Cerezos #375, Ñuñoa para recoger tu vehículo. Nuestro horario de atención es de 8:30 a 6:30 hrs de lunes a viernes. Si necesitas programar un horario de retiro especial, no dudes en contactarnos con anticipación.

Mariela Diaz
Gerente de Local
+56985991053";
				break;
			case 'entregar_created':
				$ot = Mopar::getOneOt($entity_id);
				$cliente = Mopar::getOneCliente($ot->cliente_id);
				$recipient = $cliente->email;

				$subject = 'Gracias por elegir Doctor Mopar!';
				$message = "{$cliente->nombres}: 

Soy el Doctor Mopar, y deseo expresar mi más sincero agradecimiento por elegir mi taller para el servicio de su vehículo. He dedicado años de esfuerzo y dedicación para garantizar que su experiencia supere toda expectativa.

Si en algún momento tiene alguna pregunta, comentario o sugerencia sobre el servicio que ha recibido, no dude en ponerse en contacto conmigo directamente a través de mi correo personal: j.basso@me.com. Estoy aquí para brindarle la mejor atención y asistencia posible.

Además, lo invito a compartir su experiencia con el taller dejando una reseña en Google, simplemente siguiendo este enlace: 
https://g.page/r/Cf9nCYvkpvGhEBM/review
Su opinión es muy valiosa para mi, y para otros conductores.

Nuevamente, gracias por la confianza.
Saludos,

Javier Basso
Doctor Mopar
+1(213)522-6721";
				break;
			case 'forgot_password':
					$recipient = $entity_id['recipient'];
					$subject = 'Your new password on Doctormopar';
					$message = "Here is your new password for Doctormopar client area: {$entity_id['new_password']}";
				break;
		}
		add_filter( 'wp_mail_from', function () {
			return 'taller@doctormopar.com';
		});
		add_filter( 'wp_mail_from_name', function () {
			return 'Doctor Mopar';
		});
		wp_mail($recipient, $subject, $message);
	}
}
