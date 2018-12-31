<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace chicho\miembros\clases;

/**
 * Description of ch_miembro_carnet
 *
 * @author chicho
 */
class ch_miembro_carnet {
    protected $miembro;

    public function setMiembro($miembro){
        $this->miembro = $miembro;
    }

    public function get_carnet(){
       $rta = <<<RTA
               
<table>

   
</table> 
RTA;
    }
}
