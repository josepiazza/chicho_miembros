<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace chicho\miembros\clases;

/**
 * Description of ch_miembro_pago
 *
 * @author chicho
 */
class ch_miembro_pago extends ch_core{

    protected $id;
    protected $user_id;
    protected $fecha_pago;
    protected $vencimiento;
    protected $medio_pago;
    protected $monto;
    protected $item;
    protected $nombre_tabla = "ch_miembro_pagos";
    
    protected function get_campo_id() {
        return "id";
    }
    function getItem() {
        return $this->item;
    }

    function setItem($item) {
        
        /*
1 - Socio
2 - instructor
         
         * 
         *          */
        
        $this->item = $item;
    }

        protected function get_tabla() {
        return $this->nombre_tabla;
    }

    function getId() {
        return $this->id;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function getFecha_pago() {
        return $this->fecha_pago;
    }

    function getVencimiento() {
        return $this->vencimiento;
    }

    function getMedio_pago() {
        return $this->medio_pago;
    }

    function getMonto() {
        return $this->monto;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setFecha_pago($fecha_pago) {
        $this->fecha_pago = $fecha_pago;
    }

    function setVencimiento($vencimiento) {
        $this->vencimiento = $vencimiento;
    }

    function setMedio_pago($medio_pago) {
        $this->medio_pago = $medio_pago;
    }

    function setMonto($monto) {
        $this->monto = $monto;
    }

        
    public function get_lista($filtro, $pagina = 1) {
        
    }

    public function guardar(){
        global $wpdb;
        
        if(!empty($this->user_id)){


            $insert = [
                "user_id"=>$this->user_id ,
                "fecha_pago"=>$this->fecha_pago ,
                "vencimiento"=>$this->vencimiento ,
                "medio_pago"=>$this->medio_pago ,
                "monto"=>$this->monto 
            ];
            $format = ["%d", "%s", "%s", "%s", "%f"];
            if( $this->existo($this->user_id, "id", $wpdb->prefix.$this->nombre_tabla) ){
                $where = ["id"=>$this->id];
                return $wpdb->update($wpdb->prefix.$this->nombre_tabla, $insert, $where, $format);
            }else{
                return $wpdb->insert($wpdb->prefix.$this->nombre_tabla, $insert, $format);
            }
        }
    }
    
}
