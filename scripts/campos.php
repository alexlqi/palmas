<?php //Arrays para formularios

/* name/class => [0] boolean para poner o no, [1] label, [2] id/clase para accion jquery  */

$campos=array(
/*Sección de campos para metodo pivote*/
"pivote"=>array(
	//sección de clientes
	"clientes"=>array(
		"id_cliente"=>array( false, "ID cliente",'text_corto' ,"text"),
		"clave"=>array( true, "CLAVE",'text_corto requerido mayuscula' ,"text"),
		"nombre"=>array( true, "Nombre",'text_largo' ,"text"),
		"limitecredito"=>array( true, "Límite de crédito",'text_mediano' ,"text"),
		"fecha"=>array( false, "Fecha",'text_mediano fecha_cliente' ),
	),
	"clientes_contacto"=>array(
		"id_cliente"=>array( false, "ID cliente","clases","text"),
		"clave"=>array( true, "CLAVE","requerido mayuscula","text"),
		"direccion"=>array( true, "Dirección","clases","text"),
		"colonia"=>array( true, "Colonia","clases","text"),
		"ciudad"=>array( true, "Ciudad","clases","text"),
		"estado"=>array( true, "Estado","clases","text"),
		"cp"=>array( true, "Código Postal","clases","text"),
		"telefono"=>array( true, "Telefono","clases","text"),
		"celular"=>array( true, "Celular","clases","text"),
		"email"=>array( true, "E-mail","clases","text"),
	),
	"clientes_fiscal"=>array(
		"id_cliente"=>array( false, "ID cliente","clases","text"),
		"rfc"=>array( true, "RFC","requerido mayuscula","text"),
		"razon"=>array( true, "Razón social","requerido","text"),
		"nombrecomercial"=>array( true, "Nombre Comercial","requerido","text"),
		"direccion"=>array( true, "Direccion Fiscal","requerido","text"),
		"colonia"=>array( true, "Colonia","requerido","text"),
		"ciudad"=>array( true, "Ciudad","requerido","text"),
		"estado"=>array( true, "Estado","requerido","text"),
		"cp"=>array( true,"Código Postal","requerido","text"),
	),
	//sección de proveedores
	"proveedores"=>array(
		"id_proveedor"=>array( false, "label","clases","text"),
		"clave"=>array( true, "CLAVE","requerido mayuscula","text"),
		"nombre"=>array( true, "Nombre","clases","text"),
		"fecha"=>array( false, "label","clases","text"),
		"limitecredito"=>array( true, "Límite de crédito","clases","text"),
		"tipocambio"=>array( true, "Tipo de cambio","clases","text"),
	),
	"proveedores_contacto"=>array(
		"id_proveedor"=>array( false, "Tipo de cambio","clases","text"),
		"clave"=>array( true, "CLAVE","requerido mayuscula","text"),
		"encargado"=>array( true, "Nombre del encargado","clases","text"),
		"puesto"=>array( true, "Puesto","clases","text"),
		"direccion"=>array( true, "Dirección","clases","text"),
		"colonia"=>array( true, "Colonia","clases","text"),
		"ciudad"=>array( true, "Ciudad","clases","text"),
		"estado"=>array( true, "Estado","clases","text"),
		"cp"=>array( true, "Código Postal","clases","text"),
		"telefono1"=>array( true, "Telefono 1","clases","text"),
		"telefono2"=>array( true, "Telefono 2","clases","text"),
		"telefono3"=>array( true, "Telefono 3","clases","text"),
		"celular"=>array( true, "Celular","clases","text"),
		"email"=>array( true, "E-mail","clases","text"),
	),
	"proveedores_fiscal"=>array(
		"id_proveedor"=>array( false, "label","clases","text"),
		"rfc"=>array( true, "RFC","requerido mayuscula","text"),
		"razon"=>array( true, "Razón social","requerido","text"),
		"nombrecomercial"=>array( true, "Nombre Comercial","requerido","text"),
		"direccion"=>array( true, "Direccion Fiscal","requerido","text"),
		"colonia"=>array( true, "Colonia","requerido","text"),
		"ciudad"=>array( true, "Ciudad","requerido","text"),
		"estado"=>array( true, "Estado","requerido","text"),
		"cp"=>array( true, "Código Postal","requerido","text"),
	),
),

/*Sección de campos para metodo referenciado*/
"referenciado"=>array(
	//array de proveedores_productos
	"proveedores"=>array(
		"id_proveedor"=>array( true, "Proveedor","id_proveedor remueve_name","select"),
		"clave"=>array( true, "CLAVE","disabled clave remueve_name","text"),
		"nombre"=>array( true, "Nombre","disabled nombre remueve_name","text"),
	),
	"proveedores_productos"=>array(
		"id_proveedor"=>array( true,"","id_proveedor","hidden"),
		"clave"=>array( true,"CLAVE","clases","text"),
		"nombre"=>array( true,"NOMBRE","clases","text"),
		"descripcion"=>array( true,"Descripción","clases","text"),
		"unidades"=>array( true,"Unidades","clases","text"),
		"precio"=>array( true,"Precio","clases","text"),
	),
),
	
/*Sección de campos para metodo individual*/
"individual"=>array(
	//array de áreas
	"areas"=>array(
		"clave"=>array( true, "CLAVE","text_mediano requerido mayuscula","text"),
		"nombre"=>array( true, "Nombre de área","text_mediano","text"),
	),
	"familias"=>array(
		"clave"=>array( true, "CLAVE","text_mediano requerido mayuscula","text"),
		"nombre"=>array( true, "Nombre de familia","text_mediano","text"),
	),
	"subfamilias"=>array(
		"clave"=>array( true, "CLAVE","text_mediano requerido mayuscula","text"),
		"nombre"=>array( true, "Nombre de sub familia","text_mediano","text"),
	),
),
);

?>