<?php
/*
Plugin Name: CH_Miembros
Description: Controla los miembros de una institución y administra carnets
Author: José Piazza
*/

require __DIR__.'/ch_miembro_include.php';


use chicho\miembros\clases\ch_inicio;

$r = new ch_inicio();

function crearEstructuraDeDatos(){

    global $wpdb;
    add_option( 'ch_miembros_version_db', '0.0.1' );
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."ch_miembro_nivel (
            id int not null AUTO_INCREMENT, 
            nivel varchar(50),
             UNIQUE KEY id (id)
            );";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."ch_miembro_nivel_instructor (
            id int NOT NULL AUTO_INCREMENT, 
            nivel varchar(50),
            UNIQUE KEY id (id)
            );";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."ch_miembro_tipo_documento (
            id int NOT NULL AUTO_INCREMENT, 
            tipo_documento varchar(50),
            UNIQUE KEY id (id)
            );";
    dbDelta( $sql );
    $sql = "create table IF NOT EXISTS ".$wpdb->prefix."ch_miembro_medio_pago(
            id int NOT NULL AUTO_INCREMENT, 
            tipo_medio_pago varchar(50),
            UNIQUE KEY id (id)
            )";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."ch_miembros (
            id int NOT NULL AUTO_INCREMENT,
            user_id INT,
            tipo_documento INT NOT NULL, 
            numero_documento varchar(15),
            domicilio varchar(100),
            localidad varchar(50),
            nivel INT , 
            nivel_instructor INT ,
            instructor INT,
            instructor_certificante INT,
            UNIQUE KEY id (id), 
            FOREIGN KEY (nivel) REFERENCES ".$wpdb->prefix."ch_miembro_nivel(id) ON DELETE RESTRICT,
            FOREIGN KEY (nivel_instructor) REFERENCES ".$wpdb->prefix."ch_miembro_nivel_instructor(id) ON DELETE RESTRICT,
            FOREIGN KEY (tipo_documento) REFERENCES ".$wpdb->prefix."ch_miembro_tipo_documento(id) ON DELETE RESTRICT
            );";
    dbDelta( $sql );
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."ch_miembro_pagos (
            id int NOT NULL AUTO_INCREMENT, 
            user_id INT,
            fecha_pago date,
            item int not null,
            vencimiento date,
            medio_pago int,
            referencia varchar(100),
            monto DECIMAL(5,2),
            UNIQUE KEY id (id),
            FOREIGN KEY (medio_pago) REFERENCES ".$wpdb->prefix."ch_miembro_medio_pago(id) ON DELETE RESTRICT
            );";
    dbDelta( $sql );
    $sql = "create table IF NOT EXISTS ".$wpdb->prefix."wp_ch_importar(
            id int NOT NULL AUTO_INCREMENT,
            nro_socio varchar(100),
            apellido varchar(100),
            nombre varchar(100),
            nivel varchar(100),
            carnet varchar(100),
            nivel_instructor varchar(100),
            campo_07 varchar(100),
            dni varchar(100),
            email varchar(100),
            estado varchar(10),
            error varchar(100),
            UNIQUE KEY id (id)
            );";
    dbDelta( $sql );
}

function cargarDatosIniciales(){
        global $wpdb;
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel", ["nivel"=> "Nivel 1"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel", ["nivel"=> "Nivel 2"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel", ["nivel"=> "Nivel 3"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel", ["nivel"=> "Nivel 4"]);
        
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel_instructor", ["nivel"=> "Nivel 1"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel_instructor", ["nivel"=> "Nivel 2"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel_instructor", ["nivel"=> "Nivel 3"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_nivel_instructor", ["nivel"=> "Nivel 4"]);
        
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_tipo_documento", ["tipo_documento"=> "DNI"]);
        
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_medio_pago", ["tipo_medio_pago"=> "Mercado Pago"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_medio_pago", ["tipo_medio_pago"=> "Tarjeta"]);
    	$wpdb->insert( "".$wpdb->prefix."ch_miembro_medio_pago", ["tipo_medio_pago"=> "Efectivo"]);
}

register_activation_hook( __FILE__, 'crearEstructuraDeDatos' );
//register_activation_hook( __FILE__, 'cargarDatosIniciales' );

    if(!session_id()) {
        session_start();
    }

function the_url( $url ) {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'the_url' );
