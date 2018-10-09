<?php
/**
 * Description of ch_inicio
 *
 * @author chicho
 */

namespace chicho\miembros\clases;

class ch_inicio {

    function __construct() {
        add_action("admin_menu", array($this, "crearMenu"));
//        $this->crearMenu();
    } 
    
    public function crearMenu(){
       add_menu_page("CH_Miembros", "Administracion", "manage_options", "ch_menu_administrador", [$this, "testeo" ]);
       add_submenu_page("ch_menu_administrador", "Sub Menu", "Sub my menu", "manage_options", "sub_menu", [$this, "testSubMenu"]);
    }
    
    public function testeo(){
        print "<h1>hola</h1>";
    }
    
    public function testSubMenu(){
        print "<h1>Sub menu</h1>";
    }
    
    
}
