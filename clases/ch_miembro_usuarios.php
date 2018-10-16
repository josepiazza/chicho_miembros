<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace chicho\miembros\clases;
use chicho\miembros\clases\ch_core;
/**
 * Description of ch_miembro_usuarios
 *
 * @author chicho
 */
class ch_miembro_usuarios extends ch_core{
    protected $user_id;
    protected $tipo_documento;
    protected $documento;
    protected $localidad;
    protected $nombre;
    protected $apellido;
    protected $nombre_tabla = "ch_miembros";
    
    public function get_formulario(){
        
        $rta = "<input type='text' name='dni'>";
        return $rta;
        
    }
    
    public function guardar(){
        global $wpdb;
        
        if(!empty($this->user_id)){
            if( !empty( $this->nombre ) ) update_user_meta( $this->user_id, 'first_name', sanitize_text_field( $this->nombre ) );
            if( !empty( $this->apellido ) ) update_user_meta( $this->user_id, 'last_name', sanitize_text_field( $this->apellido ) );

            $insert = [
                "user_id"=>( $this->user_id ),
                "tipo_documento"=>( (!empty($this->tipo_documento))?$this->tipo_documento:'null' ),
                "numero_documento"=>( (!empty($this->documento))?$this->documento:'' ),
                "localidad"=>( (!empty($this->localidad))?$this->localidad:'' ),
            ];
            $format = ["%d", "%d", "%s", "%s"];
            return $wpdb->insert($wpdb->prefix.$this->nombre_tabla, $insert, $format);
        }
    }
    
    public function set_user_id($valor){ $this->user_id = $valor; }
    
    public function set_nombre($valor){ $this->nombre = $valor; }
    
    public function set_apellido($valor){ $this->apellido = $valor; }
    
    public function set_tipo_documento($valor){ $this->tipo_documento = $valor; }
    
    public function set_documento($valor){ $this->documento = $valor; }
    
    public function set_localidad($valor){ $this->localidad = $valor; }
    
    public function get_lista($filtro, $page=1){
        
        global $wpdb;
        $sql = "SELECT * FROM ".$wpdb->prefix."ch_miembros"; //$wpdb->prepare();
        $rta = $wpdb->get_results( $sql );
        return $rta;
    }
}
