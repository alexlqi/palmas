<?php //script para eliminar articulos desde la tabla de articulos
include("datos.php");
header("Content-type: application/json");
$id_item=$_POST["id_item"];
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="DELETE FROM cotizaciones_articulos WHERE id_item=$id_item;";
	
	$bd->query($sql);
	
	$r["continuar"]=true;
	$r["info"]="Articulo eliminado satisfactoriamente";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}

echo json_encode($r);
?>