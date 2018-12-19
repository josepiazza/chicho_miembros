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
    protected $referencia;
    protected $nombre_tabla = "ch_miembro_pagos";
    
    function getReferencia() {
        return $this->referencia;
    }

    function setReferencia($referencia) {
        $this->referencia = $referencia;
    }

    
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
        global $wpdb;
        
        $where = [];
        if( isset($filtro["user_id"]) ) $where[] = " user_id = {$filtro["user_id"]} ";
        
        if(!empty($where)){
            $where = " WHERE ".implode(" and ", $where);
        }else{
            $where = null;
        }
        
        $sql = "SELECT * FROM ".$wpdb->prefix.$this->nombre_tabla." ".$where." ORDER BY vencimiento DESC";
        $rta = $wpdb->get_results( $sql );
        return $rta;
    }

    public function guardar(){
        global $wpdb;
        
        if(!empty($this->user_id)){

            
            $vencimientoFecha = new \DateTime( $this->fecha_pago );
            
            $vencimientoFecha->add(new \DateInterval('P1Y'));

            $this->vencimiento = $vencimientoFecha->format("Y-m-d");
            
            $insert = [
                "user_id"=>$this->user_id ,
                "fecha_pago"=>$this->fecha_pago ,
                "vencimiento"=>$this->vencimiento ,
                "medio_pago"=>$this->medio_pago ,
                "monto"=>$this->monto ,
                "item"=>$this->item,
                "referencia"=>$this->referencia
            ];
            $format = ["%d", "%s", "%s", "%s", "%f", "%d", "%s"];
            if( $this->existo($this->user_id, "id", $wpdb->prefix.$this->nombre_tabla) ){
                $where = ["id"=>$this->id];
                return $wpdb->update($wpdb->prefix.$this->nombre_tabla, $insert, $where, $format);
            }else{
                return $wpdb->insert($wpdb->prefix.$this->nombre_tabla, $insert, $format);
            }
        }
    }
    
    public function getMediosPago(){
        global $wpdb;
        $sql = "SELECT * FROM ".$wpdb->prefix."ch_miembro_medio_pago";
        $rta = $wpdb->get_results( $sql );
        return $rta;
    }
    
    public function getFormulario(){
        $medios = $this->getMediosPago();
        $option = "";
        foreach( $medios as $m ){
            $option .= "<option value='{$m->id}'>{$m->tipo_medio_pago}</option>";
        }
        $user_id=$this->getUser_id();
        $fechaHoy = date("Y/m/d", time());
        $rtaPagos = <<<PAG
    <style>
                .label_ch_form {
//                  border: 1px solid red;
                    width: 100px;
                    display: block;
                    clear: none;
                    float: left;
                    padding-top: 8px;
                }
                
                .lineFormulario{
                    margin: 10px;
                }
                #formulario_Nuevo_Pago{
                    display: none;
                }
    </style>
    <form ction="?page=listado_miembros&opt=editar&id={$user_id}" method="post">
    <input type="hidden" name="opt2" value="guardarPago" />
    <div id="formulario_Nuevo_Pago">
    <div class="lineFormulario"><span class="label_ch_form">Fecha Pago:</span><input name="fecha" value="{$fechaHoy}"/></div>
    <div class="lineFormulario"><div class="label_ch_form">Medio de Pago:</div>
        <select name="tipo_medio_pago">
            $option
        </select>
    </div>
    <div class="lineFormulario"><div class="label_ch_form">Item a pagar:</div>
        <select name="item">
            <option value="1">Navegante</option>
            <option value="2">Instructor</option>
        </select>
    </div>     
    
    <div class="lineFormulario"><div  class="label_ch_form">Importe:</div> <input name="monto"></div>
    <div class="lineFormulario"><div class="label_ch_form">Referencia del pago:</div> <textarea name="referencia"></textarea> </div>
    <div class="lineFormulario">
                <input type="submit" value="Guardar"/>
                <button onClick="cancelrFormularioMedioPago()">Cancelar</button>
    </div>
    </div>    
    </form>
    <script>
          var cancelrFormularioMedioPago = function(){
            document.getElementById("formulario_Nuevo_Pago").style="display: none;";
            }   
                
        var mostrarFormularioMedioPago = function(){
            document.getElementById("formulario_Nuevo_Pago").style="display: block;";
        }
    </script>
PAG;
        
        return $rtaPagos;
    }
    
}
