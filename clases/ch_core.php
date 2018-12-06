<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace chicho\miembros\clases;

/**
 * Description of ch_core
 *
 * @author chicho
 */
abstract class ch_core  {
    //put your code here
    abstract protected function get_tabla();
    abstract protected function get_campo_id();


    abstract public function get_lista( $filtro, $pagina = 1 );
    
    protected function existo($id, $campo, $tabla){
        global $wpdb;
        $sql = "SELECT count(0) as existe FROM $tabla WHERE $campo = $id";
        $rta = $wpdb->get_results( $sql );
        if($rta[0]->existe > 0){
            return true;
        }else{
            return false;
        }
    }
    
    public function get_tabla_html($filtro, $pagina = 1){
        $lista = $this->get_lista($filtro, $pagina = 1);
        $rta = "<table class='wp-list-table widefat fixed striped posts'><tbody id='the-list'>";
        foreach( $lista as $row ){ 
            $rta .= "<tr>";
            foreach( $row as $campo ){
                $rta .= "<td>".$campo."</td>";
            }      
        }
        $rta .= "</tbody></table>";
        return $rta;
    }
    
    public function buscar_nivel($id_nivel){
        global $wpdb;
        $sql = "SELECT * FROM wp_ch_miembro_nivel WHERE id = $id_nivel";
        $rta = $wpdb->get_results( $sql );
        return $rta[0]->nivel;
    }
    public function buscar_nivel_instructor($id_nivel){
        global $wpdb;
        $sql = "SELECT * FROM wp_ch_miembro_nivel_instructor WHERE id = $id_nivel";
        $rta = $wpdb->get_results( $sql );
        return $rta[0]->nivel;
    }
    
    public function get_lista_nivel(){
        global $wpdb;
        $sql = "SELECT * FROM wp_ch_miembro_nivel";
        $rta = $wpdb->get_results( $sql );
        return $rta;
    }
    
    public function get_lista_nivel_instructor(){
        global $wpdb;
        $sql = "SELECT * FROM wp_ch_miembro_nivel_instructor";
        $rta = $wpdb->get_results( $sql );
        return $rta;
    }
}
