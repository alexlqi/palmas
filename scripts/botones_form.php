<?php
//ordenado por array categoría
$botones["ventas"]=array(
//metodo, tabla, nombre botón 
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"pivote",
		"tabla"=>"f_clientes.php",
		"nombre"=>"Clientes",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"pivote",
		"tabla"=>"f_proveedores.php",
		"nombre"=>"Proveedores",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_areas.php",
		"nombre"=>"Áreas",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_familias.php",
		"nombre"=>"Familias",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_subfamilias.php",
		"nombre"=>"Subfamilias",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"referenciado",
		"tabla"=>"f_articulos.php",
		"nombre"=>"Articulos",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"referenciado",
		"tabla"=>"f_paquetes.php",
		"nombre"=>"Paquetes",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_unidades.php",
		"nombre"=>"Unidades",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_tipo_eventos.php",
		"nombre"=>"Tipo<br />eventos",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_salones.php",
		"nombre"=>"Salones",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_bancos.php",
		"nombre"=>"Bancos",
	),
	array(
		"accion"=>"boton_abrir_form_dos",
		"metodo"=>"individual",
		"tabla"=>"f_usuarios.php",
		"nombre"=>"Usuarios",
	),
);

//botones para coordinador de ventas
$botones["coordinador"]=array(

);
	//agrega los botones de ventas a administrador
	foreach($botones["ventas"] as $ind => $val){
		array_push($botones["coordinador"],$val);
	}

//botones para administrador
$botones["administrador"]=array(

);
	//agrega los botones de ventas a administrador
	foreach($botones["ventas"] as $ind => $val){
		array_push($botones["administrador"],$val);
	}

//botones para almacenista
$botones["almacenista"]=array(

);
	//agrega los botones de ventas a administrador
	foreach($botones["ventas"] as $ind => $val){
		array_push($botones["almacenista"],$val);
	}
?>