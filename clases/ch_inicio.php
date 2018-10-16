<?php
/**
 * Description of ch_inicio
 *
 * @author chicho
 */

namespace chicho\miembros\clases;

use chicho\miembros\clases\ch_miembro_usuarios;

class ch_inicio {

    function __construct() {
        add_action("admin_menu", array($this, "crearMenu"));
//        $this->crearMenu();
        
        add_action('wp_login', array($this, 'redirect'));  
        add_action('wp_logout', array($this, 'redirect'));  
//        add_shortcode( 'ch_testeo', [$this, 'custom_shortcode'] );
        add_action( 'register_form', [$this, 'modificar_form_registro']);
        add_action('user_register', [$this, 'registrar_usuario'], 10, 1 );
        add_filter( 'registration_errors', [$this, 'registrar_usuario_errors'], 10, 3 );
    } 
    
    public function crearMenu(){
       add_menu_page("CH_Miembros", "Miembros", "manage_options", "ch_menu_administrador", [$this, "main_menu" ]);
       add_submenu_page("ch_menu_administrador", "Todos los miembros", "Todos los miembros", "manage_options", "sub_menu", [$this, "operar_miembros"]);
    }

    
    public function main_menu(){
        print "<h1>Menu Miembros de la organización</h1>";
    }
    
    public function operar_miembros(){
        print "<h2>OperarMiembros</h2>";
        $miembros = new ch_miembro_usuarios();
        $rta = $miembros->get_tabla_html(1);
        print_r($rta);
    }
    
    public function redirect() {
        wp_redirect(esc_url( site_url( ) ));
        exit();
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
        
        
        print "tratando: $user_id";
        $user = new ch_miembro_usuarios();
        $user->set_user_id($user_id);
        $user->set_nombre($_POST["first_name"]);
        $user->set_apellido($_POST["last_name"]);
        $user->set_tipo_documento($_POST["tipo_documento"]);
        $user->set_documento($_POST["documento"]);
        $user->set_localidad($_POST["localidad"]);
        print $user->guardar();
        exit();
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
    

}
