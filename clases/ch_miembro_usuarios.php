<?php

namespace chicho\miembros\clases;
use chicho\miembros\clases\ch_core;
use chicho\miembros\clases\ch_miembro_pago;
use WP_User_Query;

/**
 * Description of ch_miembro_usuarios
 *
 * @author chicho
 */
class ch_miembro_usuarios extends ch_core{
    protected $id;
    protected $user_id;
    protected $tipo_documento;
    protected $documento;
    protected $localidad;
    protected $nombre;
    protected $apellido;
    protected $nivel;
    protected $nivel_instructor;
    protected $nombre_tabla = "ch_miembros";
    protected $pagos;
    
    
    
    public function get_formulario(){
        $rta = "<input type='text' name='dni'>";
        return $rta;   
    }
    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

        protected function get_campo_id() {
        return "user_id";
    }

    protected function get_tabla() {
        return $this->nombre_tabla;
    }

    
    public function guardar(){
        global $wpdb;
        
        if(!empty($this->user_id)){

            if( !empty( $this->nombre ) ) update_user_meta( $this->user_id, 'first_name', sanitize_text_field( $this->nombre ) );
            if( !empty( $this->apellido ) ) update_user_meta( $this->user_id, 'last_name', sanitize_text_field( $this->apellido ) );

            $insert = [
                "user_id"=>( $this->user_id ),
                "tipo_documento"=>( (!empty($this->tipo_documento))?$this->tipo_documento:null ),
                "numero_documento"=>( (!empty($this->documento))?$this->documento:'' ),
                "localidad"=>( (!empty($this->localidad))?$this->localidad:'' )
            ];
            $format= ["%d", "%d", "%s", "%s"];
            
            if(!empty($this->nivel)){
                $insert["nivel"] = $this->nivel;
                $format[] = "%d";
            }
            
            if(!empty($this->nivel_instructor)){
                $insert["nivel_instructor"] = $this->nivel_instructor;
                $format[] = "%d";
            }
            
//            $format = ["%d", "%d", "%s", "%s", "%d", "%d"];
            if( $this->existo($this->user_id, "user_id", $wpdb->prefix.$this->nombre_tabla) ){
                $where = ["user_id"=>$this->user_id];
                return $wpdb->update($wpdb->prefix.$this->nombre_tabla, $insert, $where, $format);
            }else{
                $isert["user_id"]= $this->user_id ;
                return $wpdb->insert($wpdb->prefix.$this->nombre_tabla, $insert, $format);
            }
        }
    }
    
    public function set_user_id($valor){ $this->user_id = $valor; }
    public function set_nombre($valor){ $this->nombre = $valor; }
    public function set_apellido($valor){ $this->apellido = $valor; }
    public function set_tipo_documento($valor){ $this->tipo_documento = $valor; }
    public function set_documento($valor){ $this->documento = $valor; }
    public function set_localidad($valor){ $this->localidad = $valor; }
    public function set_nivel($valor){ $this->nivel = $valor; }
    public function set_nivel_instructor($valor){ $this->nivel_instructor = $valor; }
    
    
  
    private function getUsuarios($search_term){
        
        foreach ($search_term as $key => $value) {
            if( $key == "parametrosBusqueda" ){
                $a = explode(" ", $value);
                foreach( $a as $v ){
                    
                   $meta_query = array(
                       array(
                           'key'     => 'first_name',
                           'value'   => $v,
                           'compare' => 'LIKE'
                       ),
                       array(
                           'key'     => 'last_name',
                           'value'   => $v,
                           'compare' => 'LIKE'
                       )
                  
                   ); 
                }
                $meta_query["relation"] = "OR";
            }
        }
        $args = ["meta_query" => $meta_query];  
        

    // Create the WP_User_Query object
    $wp_user_query = new WP_User_Query( $args );

    // Get the results
    $rta = $wp_user_query->get_results();   
    return $rta;
    }
    
    public function get_lista($filtro, $page=1){
   
        global $wpdb;
        $where = [];
        if(!empty($filtro["tipo"])){
            switch( $filtro["tipo"] ){
                case 1:
                    $where[] = "(nivel is not null AND nivel_instructor is null) ";
                    break;
                case 2:
                    $where[] = "nivel_instructor is not null ";
                    break;
            }
        }
        
        if( !empty( $filtro) ){
            $rt =  $this->getUsuarios( $filtro );
            if( !empty($rt) ){
                foreach($rt as $f){
                    $in[]  = $f->data->ID;
                }
                $where[] = " user_id IN (". implode(" , ", $in) .")";
            }else{
                $where[] = " 1=2 ";
            }
        }
        
        if(!empty($where)){
            $where = " WHERE ".implode(" and ", $where);
        }else{
            $where = null;
        }
        
        $sql = "SELECT * FROM ".$wpdb->prefix."ch_miembros $where"; //$wpdb->prepare();
//        print $sql;
        $rta = $wpdb->get_results( $sql );
        return $rta;
    }
    
    public static function get_miembro($id){
        $m = new ch_miembro_usuarios();
        $m->buscar_usuario($id);
        return $m;
    }
    
    public function buscar_usuario($id){
        global $wpdb;
        $this->user_id = $id;
        $sql = "SELECT * FROM ".$wpdb->prefix."ch_miembros WHERE user_id = $id";
        $rs = $wpdb->get_results( $sql );
        $this->tipo_documento = $rs[0]->tipo_documento;
        $this->documento = $rs[0]->numero_documento;
        $this->localidad = $rs[0]->localidad;
        $this->nivel = $rs[0]->nivel;
        $this->nivel_desc = $this->buscar_nivel($this->nivel);
        $this->nivel_instructor = $rs[0]->nivel_instructor;
        $this->nivel_instructor_desc = $this->buscar_nivel_instructor($this->nivel_instructor);
        $this->nombre = get_user_meta($this->user_id,"first_name", true);
        $this->apellido = get_user_meta($this->user_id,"last_name",true);
        $this->nickname = get_user_meta($this->user_id, "nickname", true);
        $this->email = get_user_option("user_email", $this->user_id);
    }
    
    public function get_tipo_documento(){return $this->tipo_documento ;}
    public function get_documento(){return $this->documento ;}
    public function get_localidad(){return $this->localidad ;}
    public function get_nivel(){return $this->nivel ;}
    public function get_nivel_instructor(){return $this->nivel_instructor ;}
    
    public function add_pago(ch_miembro_pago $pago ){
        $pago->setUser_id($this->user_id);
        $pago->guardar();
    }
    
    public function get_tabla_html($filtroLista, $pagina = 1){
        

        if( isset($filtroLista["btn_limpiar"]) ){
            $_SESSION["filtro_ch_miembro"] = null;
        }
        if( isset($filtroLista["btn_filtrar"]) ){
            $_SESSION["filtro_ch_miembro"] = $filtroLista;
        }
        
        if( !empty( $_SESSION["filtro_ch_miembro"] ) ){
            $filtro= $_SESSION["filtro_ch_miembro"];       
            $lista = $this->get_lista($filtro, $pagina = 1);
        }else{
            $lista = [];
        }
        
        $parametrosBusqueda = (!empty($filtro["parametrosBusqueda"]))?$filtro["parametrosBusqueda"]:"";
        $tipo = (!empty($filtro["tipo"]))?$filtro["tipo"]:"";       
        $rta=<<<FIL
                <form name="filtro" method="post" action="?page=listado_miembros&filtro=true">lslsls
   Clave Búsqueda: <input name="parametrosBusqueda" value="{$parametrosBusqueda}"> 
   Tipo: <select name="tipo" id="idTipoNavegante"><option value=0>Todos</option><option value=1>Navegante</option><option value=2>Instructor</option></select>
   <input type="submit" name="btn_filtrar" value="Filtrar"><input name="btn_limpiar" type="submit" value="limpiar">
   <script>
   document.getElementById("idTipoNavegante").value= "{$tipo}"
   </script>
   </form><hr/>
FIL;
        
        $rta .= "<table class='wp-list-table widefat fixed striped posts'><tbody id='the-list'>";
        foreach( $lista as $row ){ 
            $nombre = get_user_meta($row->user_id,"first_name", true);
            $apellido = get_user_meta($row->user_id,"last_name",true);
            $nickname = get_user_meta($row->user_id, "nickname", true);
            $email = get_user_option("user_email", $row->user_id);
            $rta .= "<tr>";
                $rta .= "<td>".$nickname."</td>";
                $rta .= "<td>".$nombre."</td>";
                $rta .= "<td>".$apellido."</td>";
                $rta .= "<td>".$email."</td>";
            
            $rta.="<td><a href='?page=listado_miembros&opt=detalles&id=".$row->user_id."'>Detalles</a></td>";
            $rta.="<td><a href='?page=listado_miembros&opt=editar&id=".$row->user_id."'>Editar</a></td>";
            $rta.="</tr>";            
        }
        $rta .= "</tbody></table>";
        return $rta;
    }
    
    protected function get_detalle($user_id){
//        $this->buscar_usuario($user_id);
        $rta = <<<RTA
 <table >
    <tr>
        <td>Nombre:</td><td>{$this->nombre}</td>
    </tr>
    <tr>
        <td>Apellido:</td><td>{$this->apellido}</td>
    </tr>
    <tr>
        <td>Email:</td><td>{$this->email}</td>
    </tr>
    <tr>
        <td>Nivel:</td><td>{$this->nivel } - {$this->nivel_desc}</td>
    </tr>
    <tr>
        <td>Nivel Instructor:</td><td>{$this->nivel_instructor}{$this->nivel_instructor_desc}</td>
    </tr>
</table>       
     <div>
   <a href="?page=listado_miembros">Volver</a>      
   </div>$valor

RTA;
        
//        var_dump( get_userdata($user_id) );
//        print_r(get_user_meta($user_id));
        return $rta;
    }

    public function get_editar($user_id, $request){

        $this->buscar_usuario($user_id);
//        print_r($request);
       
// ( [page] => listado_miembros [opt] => editar [id] => 2638 [opt2] => [fecha] => dddd [tipo_medio_pago] => 1 [item] => 1 [monto] => dd [referencia] => dd )         
        
        
        if( $request["opt2"] == "guardarPago" ){
//            print_r($request);
            
            $pago = new ch_miembro_pago();
            $pago->setUser_id($this->user_id);
            $pago->setMedio_pago($request["tipo_medio_pago"]);
            $pago->setFecha_pago($request["fecha"]);
            $pago->setItem($request["item"]);
            $pago->setReferencia($request["referencia"]);
            $pago->setMonto($request["monto"]);
            $pago->guardar();
        }
        if( $request["opt2"] == "guardarUsuario"){
            $this->set_nivel($request["nivel"]);
            $this->set_nivel_instructor($request["nivel_instructor"]);
            $this->guardar();
        }
        
        $nivel = $this->get_lista_nivel();
        $nivel_instructor = $this->get_lista_nivel_instructor();
        
        $combo_nivel = "<select name='nivel' id='nivel' value='{$this->nivel}'>";
        $combo_nivel .= "<option value='0'>Seleccionar...</option>";
        foreach($nivel as $row){
            $combo_nivel .= "<option value='{$row->id}'>{$row->nivel}</option>";
        }
        $combo_nivel .= "</select>";
        
        $combo_nivel_instructor = "<select name='nivel_instructor' id='nivel_instructor' value='{$this->nivel}'>";
        $combo_nivel_instructor .= "<option value='0'>Seleccionar...</option>";
        foreach($nivel_instructor as $row){
            $combo_nivel_instructor .= "<option value='{$row->id}'>{$row->nivel}</option>";
        }
         $combo_nivel_instructor .= "</select>";
        
         
        $pago = new ch_miembro_pago();
        $pagos = $pago->get_lista( ["user_id" => $this->user_id]);
        $rtaPagos ="<button onClick='mostrarFormularioMedioPago()'>Nuevo Pago</button>";
        $pago->setUser_id( $this->user_id );
        $rtaPagos .= $pago->getFormulario();
        $rtaPagos .= "<table style='width:80%' class='wp-list-table widefat fixed striped posts'><tr><td>Fcha Pago</td><td>Medio Pago</td>"
                . "<td>Vencimiento</td><td>Importe</td><td>Item</td></tr>";
        
        foreach( $pagos as $p ){
            $rtaPagos .= "<tr>";
            $rtaPagos .= "<td>".$p->fecha_pago."</td>";
            $rtaPagos .= "<td>".$p->medio_pago."</td>";
            $rtaPagos .= "<td>".$p->vencimiento."</td>";
            $rtaPagos .= "<td>".$p->monto."</td>";
            $rtaPagos .= "<td>".$p->item."</td>";
            $rtaPagos .= "</tr>";
            
        }
        $rtaPagos .= "</table>";
        $rta = <<<RTA
<form ction="?page=listado_miembros&opt=editar&id={$user_id}" method="post">
<input type="hidden" name="opt2" value="guardarUsuario" />    
  <table>
    <tr>
        <td>Nombre:</td><td>{$this->nombre}</td>
    </tr>
    <tr>
        <td>Apellido:</td><td>{$this->apellido}</td>
    </tr>
    <tr>
        <td>Email:</td><td>---  {$this->email}</td>
    </tr>
    <tr>
        <td>Nivel:</td><td>{$combo_nivel}</td>
    </tr>
    <tr>
        <td>Nivel Instructor:</td><td>{$combo_nivel_instructor}</td>
    </tr>
</table>   
 <input type="submit" value="guardar">
</form>
<div>
$rtaPagos    
</div>
<script>

        document.getElementById("nivel").value= "{$this->nivel}";
        document.getElementById("nivel_instructor").value= "{$this->nivel_instructor}";
        
</script>
        
RTA;
        return $rta;
    }
    
    public function salida_web($request){
        $request["opt"] = (!empty($request["opt"]))?$request["opt"]:"";
        switch ($request["opt"]){
           case "detalles";
               $rta = $this->get_detalle($request["id"]);
               break;
           case "editar";
               $rta = $this->get_editar($request["id"], $request);
               break;
           default:
               $rta = $this->get_tabla_html($request);
               break;
        }
            
        return $rta;
    }
    
    public function importar_listado($request){
        global $wpdb;
        switch ($request["opt"]){
            case "paso1":
                $archivo = explode("\n", $request["archivo"]);
                $importados = 0;
                foreach($archivo as $fila){
                    $item = explode(",", $fila);
                    for($i=0; $i<count($item); $i++){
                        $item[$i] = str_replace('\"', '', $item[$i]);
                    }
                    $estado = "N";
                    $error = "";
                    if(is_email($item[8]) ){
                        $mail = $item[8];
                        $direccion = '-';
                    }else{
                        $mail = "";
                        $direccion=$item[8];
                        $estado = "E";
                        $error = "Sin email";
                    }
                    
                    $nivel_instructor = 0;
                    $nivel_instructor = (strpos($item[5], "4"))?4:$nivel_instructor;
                    $nivel_instructor = (strpos($item[5], "3"))?3:$nivel_instructor;
                    $nivel_instructor = (strpos($item[5], "2"))?2:$nivel_instructor;
                    $nivel_instructor = (strpos($item[5], "1"))?1:$nivel_instructor;
                    
                    $dni = preg_split('/[^0-9]+/i', $item[7]);
                    $insert = [
                        "nro_socio" => $item[0],
                        "apellido" => $item[1] ,
                        "nombre" => $item[2] ,
                        "nivel" => intval($item[3]),
                        "carnet" => $item[4],
                        "nivel_instructor" =>$nivel_instructor,
                        "campo_07" => $direccion,
                        "email" => $mail,
                        "dni" => $item[7],
                        "estado" => $estado,
                        "error" => $error
                    ];
                    $wpdb->insert($wpdb->prefix."ch_importar", $insert);
                    $importados++;
                }
                print "importados: $importados";
                break;
            case "procesar";
//                remove_action($tag, $function_to_remove);
                remove_action('user_register', [$this, 'registrar_usuario'] );
                $sql = "SELECT * FROM ".$wpdb->prefix."ch_importar WHERE estado in ('N', 'E') " ;
                $rs = $wpdb->get_results($sql);
                $ok = 0;
                $fail = 0;
                foreach($rs as $fila){

                    if( !is_email($fila->email) ){
                        $error = ["estado"=>"E", "error"=>"Email no valido" ];
                        $wpdb->update($wpdb->prefix."ch_importar", $error, ["nro_socio" => $fila->nro_socio]);
                        continue;
                    }
                        $userdata = ['user_login'=>$fila->dni,
                                    'user_email'=>$fila->email, 
                                    'user_pass'=>$fila->apellido.$fila->nro_socio,
                            ];
                            print_r($userdata);
                            print "********************<br/>";
//                    $userdata = ['user_login', 'user_email', 'user_pass'];
                    $user_id = wp_insert_user($userdata);
                    if(!is_numeric($user_id) ){
                        $error = ["estado"=>"E",
                                  "error"=>$user_id->get_error_message()
                            ];
                            print_r($user_id->get_error_message());
                        $wpdb->update($wpdb->prefix."ch_importar", $error, ["nro_socio" => $fila->nro_socio]);
                        continue;
                    }
                    
                    $nu = new ch_miembro_usuarios();
                    $nu->set_user_id($user_id);
                    $nu->setId( $fila->nro_socio );
                    $nu->set_apellido( $fila->apellido );
                    $nu->set_nombre( $fila->nombre );
                    $nu->set_tipo_documento(1);
                    $nu->set_documento( $fila->dni );
                    $nu->set_nivel( $fila->nivel );
                    $nu->set_nivel_instructor( $fila->nivel_instructor );
                    
                    $rtaNU = $nu->guardar();
                    if( !$rtaNU ){
                        $error = ["estado"=>"E",
                                  "error"=>"Falla inesperada"
                            ];
                        $wpdb->update($wpdb->prefix."ch_importar", $error, ["nro_socio" => $fila->nro_socio]);
                        $fail++;
                        continue;
                        /* update tabla con el error y dejar de procesar */
                    }else{
                        /* Si guardó bien seguir cuardando el pago del carnet */
                        $fecha = explode("-", $fila->carnet );
                        $mes = $this->getMesNumero($fecha[0]);

                            $anio = "20".$fecha[1];
                            $fecha_socio = "{$anio}/{$mes}/1";
                            $fecha_vencimiento = ($anio+1)."/{$mes}/1";
                            $carnet_socio = new ch_miembro_pago();
                            $carnet_socio->setUser_id($user_id);
                            $carnet_socio->setFecha_pago($fecha_socio);
                            $carnet_socio->setVencimiento($fecha_vencimiento);

                        if( empty( $fila->nivel_instructor ) ){
                            $carnet_socio->setItem(1);
                        }else{
                            $carnet_socio->setItem(2);
                        }
                        $rta_carnet = $carnet_socio->guardar();
                        if( !is_numeric($rta_carnet) ){
                            $error = ["estado"=>"P",
                                      "error"=>"No Cargo el pago"
                                ];
                            $wpdb->update($wpdb->prefix."ch_importar", $error, ["nro_socio" => $fila->nro_socio]);
                            continue;
                        }
                        
                        $succes = ["estado"=>"P"];
                        $wpdb->update($wpdb->prefix."ch_importar", $succes, ["nro_socio" => $fila->nro_socio]);
                        $ok++;
                    }

//                    $carnet_socio = new ch_miembro_pago1();
//                    $carnet_socio->
//                    print $nu->get_detalle(1);
                    print "<hr>";     
                    print "OK: $ok <br/>Fail: $fail";
                }
                
                $rta = "Procesando";
//                $user_login = wp_slash( "user1" );
//                $user_email = wp_slash( "user@mail.com"    );
//                $user_pass = time();

//                $rta = "ss";
                break;
            case "borrar":
                print '  <form action="?page=importar_listado&opt=borrar" method="post">
      <input type="submit" value="Borrar"/>
   </form> ';
                $sql="SELECT user_id FROM ".$wpdb->prefix."ch_miembros";
//                $sql = "SELECT ID as user_id FROM ".$wpdb->prefix."users WHERE id > 10";
                $rs = $wpdb->get_results($sql);
                foreach($rs as $fila){
                    print "borrando: ".$fila->user_id;
                    print wp_delete_user($fila->user_id);
                    print "<hr>";
                }
                $wpdb->query("truncate table ".$wpdb->prefix."ch_miembro_pagos");
                $wpdb->query("truncate table ".$wpdb->prefix."ch_miembros");
                $wpdb->query("truncate table ".$wpdb->prefix."ch_importar");
                break;
            default:
                print strpos("N4 - EXHAMINER", "4");
                 $rta = <<<RTA
   <form action="?page=importar_listado&opt=paso1" method="post">
<div> <textarea name="archivo" cols=75 rows=15></textarea>  </div>
      <input type="submit" value="enviar"/>
   </form>  
       <hr/>
  <form action="?page=importar_listado&opt=procesar" method="post">
      <input type="submit" value="Procesar"/>
   </form>  
       <hr/>
  <form action="?page=importar_listado&opt=borrar" method="post">
      <input type="submit" value="Borrar"/>
   </form>  
RTA;
                break;
        }
            
        return $rta;     
    }
    
    private function getMesNumero($mes){
        
        if(is_numeric($mes) ){
            return $mes;
        }
        
        $lista = ["ene.", "feb.", "mar.", "abr.", "may.", "jun.", "jul.", "ago.", "sept.", "oct.", "nov.", "dic."];
        $rta = array_search($mes, $lista);
        if( $rta === false ){
            return null;
        }else{
            return $rta + 1;
        }
        
    }
    
    private function getPagos(){
        
    }

public function get_tabla_html_frontend($filtroLista, $pagina = 1){
        
        if( isset($filtroLista["btn_limpiar"]) ){
            $_SESSION["filtro_ch_miembro"] = null;
        }
        if( isset($filtroLista["btn_filtrar"]) ){
            $_SESSION["filtro_ch_miembro"] = $filtroLista;
        }
        
        if( !empty( $_SESSION["filtro_ch_miembro"] ) ){
            $filtro= $_SESSION["filtro_ch_miembro"];    
            $filtro["tipo"] = 2; //siempre instructores
            $lista = $this->get_lista($filtroLista, $pagina = 1);
        }else{
            $lista = [];
        }
        
        $parametrosBusqueda = (!empty($filtroLista["parametrosBusqueda"]))?$filtroLista["parametrosBusqueda"]:"";
        $tipo = (!empty($filtroLista["tipo"]))?$filtroLista["tipo"]:"";
        
        $rta=<<<FIL
                <form name="filtro" method="post" action="?filtro=true">
   Clave Búsqueda: <input name="parametrosBusqueda" value="{$parametrosBusqueda}"> 
   <input type="submit" name="btn_filtrar" value="Filtrar"><input name="btn_limpiar" type="submit" value="limpiar">
   <script>
   document.getElementById("idTipoNavegante").value= "{$tipo}"
   </script>
   </form><hr/>
FIL;
        
        $rta .= "<table class='wp-list-table widefat fixed striped posts'><tbody id='the-list'>";
        foreach( $lista as $row ){ 
            $nombre = get_user_meta($row->user_id,"first_name", true);
            $apellido = get_user_meta($row->user_id,"last_name",true);
            $nickname = get_user_meta($row->user_id, "nickname", true);
            $email = get_user_option("user_email", $row->user_id);
            $rta .= "<tr>";
                $rta .= "<td>".$nombre."</td>";
                $rta .= "<td>".$apellido."</td>";
                $rta .= "<td>".$email."</td>";
                $rta .= "<td>Instructor nivel ".$row->nivel_instructor."</td>";
            
//            $rta.="<td><a href='?page=listado_miembros&opt=detalles&id=".$row->user_id."'>Detalles</a></td>";
//            $rta.="<td><a href='?page=listado_miembros&opt=editar&id=".$row->user_id."'>Editar</a></td>";
            $rta.="</tr>";            
        }
        $rta .= "</tbody></table>";
        return $rta;
    }
    
    
}
