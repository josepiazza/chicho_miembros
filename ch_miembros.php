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
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."ch_miembros (
            id int NOT NULL AUTO_INCREMENT,
            user_id INT,
            tipo_documento INT NOT NULL, 
            numero_documento varchar(15),
            localidad varchar(50),
            nivel INT , 
            nivel_instructor INT ,
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
            vencimiento date,
            medio_pago varchar(20),
            monto DECIMAL(5,2),
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
        
}

register_activation_hook( __FILE__, 'crearEstructuraDeDatos' );
//register_activation_hook( __FILE__, 'cargarDatosIniciales' );


function the_url( $url ) {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'the_url' );