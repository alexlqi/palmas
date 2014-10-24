<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
$term=$_GET["term"];
include("datos.php");

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico
	
	/* para busqueda numerica
	$sqlArt="SELECT 
		*
	FROM articulos
	INNER JOIN listado_precios ON articulos.id_articulo=listado_precios.id_articulo
	WHERE articulos.id_empresa=$empresaid AND articulos.clave=$term;";*/
	
	$sqlArt="SELECT 
		articulos.id_articulo,
		articulos.id_empresa,
		area,
		familia,
		subfamilia,
		articulos.clave,
		articulos.nombre,
		descripcion,
		unidades,
		areas.id_area,
		familias.id_familia,
		subfamilias.id_subfamilia,
		articulos.perece,
		listado_precios.*
	FROM articulos
	LEFT JOIN listado_precios ON articulos.id_articulo=listado_precios.id_articulo
	LEFT JOIN areas ON articulos.area=areas.id_area
	LEFT JOIN familias ON articulos.familia=familias.id_familia
	LEFT JOIN subfamilias ON articulos.subfamilia=subfamilias.id_subfamilia
	WHERE articulos.id_empresa=$empresaid AND articulos.clave='$term';";
	
	$i=0;
	$res=$bd->query($sqlArt);
	$r=array("nombre"=>"No existe articulo con esta clave");
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$r=$res[0];
	}
}catch(PDOException $err){
	$r=$err->getMessage();
}

echo json_encode($r);
?>