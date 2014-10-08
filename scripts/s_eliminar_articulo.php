<?php session_start();
header("content-type: application/json");
include("datos.php");
$id=$_POST["art"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="UPDATE articulos SET clave=NULL, activo=0 WHERE id_articulo=$id;";
	
	$bd->query($sql);
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

$bd=NULL;
echo json_encode($r);
?>