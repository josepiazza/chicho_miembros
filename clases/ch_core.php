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
class ch_core {
    //put your code here
    
    public function get_lista( $filtro, $pagina = 1 ){print "Implementar";}
    
    public function get_tabla_html($filtro, $pagina = 1){
        $lista = $this->get_lista($filtro, $pagina = 1);
        $rta = "<table class='wp-list-table widefat fixed striped posts'><tbody id='the-list'>";
        foreach( $lista as $row ){ 
            $rta .= "<tr>";
            foreach( $row as $campo ){
                $rta .= "<td>".$campo."</td>";
            }
            $rta.="</tr>";            
        }
        $rta .= "</tbody></table>";
        return $rta;
    }
    
}
