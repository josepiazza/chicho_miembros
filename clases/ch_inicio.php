<?php
/**
 * Description of ch_inicio
 *
 * @author chicho
 */

namespace chicho\miembros\clases;

use chicho\miembros\clases\ch_miembro_usuarios;
use QRcode;

class ch_inicio {

    function __construct() {
        add_action("admin_menu", array($this, "crearMenu"));
//        $this->crearMenu();
 
        
        

        add_action( 'admin_enqueue_scripts', [$this, "cargar_js_css"] );
        
        add_action('wp_login', array($this, 'redirect'));  
        add_action('wp_logout', array($this, 'redirect'));  
        add_action( 'register_form', [$this, 'modificar_form_registro']);
        add_action('user_register', [$this, 'registrar_usuario'], 10, 1 );
        add_filter( 'registration_errors', [$this, 'registrar_usuario_errors'], 10, 3 );
        add_action( "user_new_form", [$this, "formulario_miembro_admin"], 10, 3 );
        add_action( "edit_user_profile", [$this, "formulario_miembro_admin"], 10, 3 );
        add_action( "show_user_profile", [$this, "formulario_miembro_admin"], 10, 3 );
        add_action("edit_user_profile_update",  [$this, "registrar_usuario"]);
        add_action("personal_options_update",  [$this, "registrar_usuario"]);
        
        
        
        add_shortcode( 'ch_miembro_carnet', [$this, 'mostrar_carnet_miembro'] );
        add_shortcode('ch_miembro_listado_instructores', [$this, 'mostrar_listado_instructores']);
    } 
    
    public function cargar_js_css() {
        wp_enqueue_media();
        wp_enqueue_style('sua-css-style', plugins_url('css/style.css', __FILE__), array(), null);
//        wp_enqueue_script('sua-js-custom', plugins_url('js/scripts.js', __FILE__), array(), '1.3', true); // Ejemplo para cargar un JS
    }
    
    public function mostrar_listado_instructores(){
        $miembros = new ch_miembro_usuarios();
        print $miembros->get_tabla_html_frontend($_REQUEST);
    }
    
    public function crearMenu(){
        
        add_menu_page("CH_Miembros", "Miembros", "manage_options", "ch_menu_administrador", [$this, "main_menu" ]);
        add_submenu_page("ch_menu_administrador", "Todos los miembros", "Todos los miembros", "manage_options", "listado_miembros", [$this, "operar_miembros"]);
       
        add_submenu_page("ch_menu_administrador", "Importar", "Importar", "manage_options", "importar_listado", [$this, "importar_listado"]);
      
        
    }

    
    public function main_menu(){
        print "<h1>Menu Miembros de la organización</h1>";
    }
    
    public function operar_miembros(){
        print "<h2>OperarMiembros</h2>";
        $miembros = new ch_miembro_usuarios();
        print $miembros->salida_web($_REQUEST);
    }
    
    public function redirect() {
        wp_redirect(esc_url( site_url( ) ));
        exit();
    }
    

    public function nuevo_miembro_admin(){
        print "<h1>Hola formulario</h1>";
    }
    
    public function modificar_form_registro(){
        $documento = ( ! empty( $_POST['documento'] ) ) ? sanitize_text_field( $_POST['documento'] ) : '';
        $localidad = ( ! empty( $_POST['localidad'] ) ) ? sanitize_text_field( $_POST['localidad'] ) : '';
        $first_name = ( ! empty( $_POST['first_name'] ) ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name = ( ! empty( $_POST['last_name'] ) ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        ?>
        <p>
            <label for="first_name"><?php _e( 'Nombre' ) ?><br />
                <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(  $first_name ); ?>" size="25" /></label>
        </p>
        <p>
            <label for="last_name"><?php _e( 'Apellido' ) ?><br />
                <input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr(  $last_name ); ?>" size="25" /></label>
        </p>
        <p>
            <label for="tipo_documento"><?php _e( 'Tipo de documento' ) ?><br />
                <select name="tipo_documento">
                    <option value="1">DNI</option>
                    <option value="2">Pasaporte</option>
                </select>
            </label>
        </p>
        <p>
            <label for="dni"><?php _e( 'Documento' ) ?><br />
                <input type="text" name="documento" id="documento" class="input" value="<?php echo esc_attr(  $documento  ); ?>" size="25" /></label>
        </p>
        <p>
            <label for="localidad"><?php _e( 'Localidad' ) ?><br />
                <input type="text" name="localidad" id="localidad" class="input" value="<?php echo esc_attr(  $localidad  ); ?>" size="25" /></label>
        </p>
        <?php  
    }
    
    function redirect_register_form($q) {
        wp_redirect(esc_url( site_url( '/registro_exito' ) ));
        exit();
    }
    
    public function registrar_usuario( $user_id ){
        
        
        $user = new ch_miembro_usuarios();
        $user->set_user_id($user_id);
        $user->set_nombre($_POST["first_name"]);
        $user->set_apellido($_POST["last_name"]);
        $user->set_tipo_documento($_POST["tipo_documento"]);
        $user->set_documento($_POST["documento"]);
        $user->set_localidad($_POST["localidad"]);
        print "<h1>Files</h1>";
        print_r($_FILES);
        
        return $user->guardar();

    }
    
    
    
    function registrar_usuario_errors( $errors, $sanitized_user_login, $user_email ) {
        
        if ( empty(trim( $_POST['tipo_documento']  ) ) ) {
            $errors->add( 'first_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'Debe seleccionar un Tipo de Documento.', 'mydomain' ) ) );
        }       
        if ( empty(trim( $_POST['documento']  ) ) ) {
            $errors->add( 'first_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'Debe ingresar un documento.', 'mydomain' ) ) );
        }        
//        if ( empty(trim( $_POST['localidad']  ) ) ) {
//            $errors->add( 'first_name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( '.', 'mydomain' ) ) );
//        }
        return $errors;
    }
    
    public function formulario_miembro_admin( $profileuser ){
        
        
        
        if( isset($profileuser->ID) ){
        $usuario = ch_miembro_usuarios::get_miembro( $profileuser->ID );
            $documento = $usuario->get_documento();
            $localidad = $usuario->get_localidad();
        }else{
            $documento = ( ! empty( $_POST['documento'] ) ) ? sanitize_text_field( $_POST['documento'] ) : '';
            $localidad = ( ! empty( $_POST['localidad'] ) ) ? sanitize_text_field( $_POST['localidad'] ) : '';      
        }
              
        ?>
        
        <p>
            <label for="tipo_documento"><?php _e( 'Tipo de documento' ) ?>
                <br />
                <select name="tipo_documento">
                    <option value="1">DNI</option>
                    <option value="2">Pasaporte</option>
                </select>
            </label>
        </p>
        <p>
            <label for="dni"><?php _e( 'Documento' ) ?><br />
                <input type="text" name="documento" id="documento" class="input" value="<?php echo esc_attr(  $documento  ); ?>" size="25" /></label>
        </p>
        <p>
            <label for="localidad"><?php _e( 'Localidad' ) ?><br />
                <input type="text" name="localidad" id="localidad" class="input" value="<?php echo esc_attr(  $localidad  ); ?>" size="25" /></label>
        </p>

        
        <?php
        
        
    }
    
    public function mostrar_carnet_miembro(){
        QRcode::png('PHP QR Code :)', "imagen.png");
        print "<img src='".get_site_url()."/imagen.png'>";
    }
    
    public function importar_listado(){
        print "<h2>Importar Miembros</h2>";
        $miembros = new ch_miembro_usuarios();
        print $miembros->importar_listado($_REQUEST);  
    }

}
