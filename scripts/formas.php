<?php //Arrays para formularios

/*	
	Cada array con el nombre de la sección tiene los nombres de las tablas
  	de mysql para que las tome el script y haga los arreglos correspondientes
	para mostrar los formularios.
	Esto ahorra tiempo de escribir codigo o reemplazarlo entre muchas líneas
*/

//[0] es para el nombre de la tabla, [1] es para nombre del formulario
/*Sección de metodo pivote*/
$formas["pivote"]["clientes"]=array(
	array(0=>"clientes",1=>"cliente"),
	array("clientes_contacto","datos de contacto"),
	array("clientes_fiscal","información fiscal"),
);

$formas["pivote"]["proveedores"]=array(
	array(0=>"proveedores",1=>"proveedor"),
	array("proveedores_contacto","datos de contacto"),
	array("proveedores_fiscal","información fiscal"),
);

/*Sección de metodo referenciado*/
$formas["referenciado"]["proveedores_productos"]=array(
	array(0=>"proveedores",1=>"proveedor"),
	array("proveedores_productos","Nuevo Producto"),
);

/*Sección de metodo individual*/
$formas["individual"]["areas"]=array(
	array("areas", "áreas"),
);
$formas["individual"]["familias"]=array(
	array("familias", "familias"),
);
$formas["individual"]["subfamilias"]=array(
	array("subfamilias", "subfamilias"),
);
?>