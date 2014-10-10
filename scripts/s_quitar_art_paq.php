<?php session_start();
header("content-type: application/json");
include("datos.php");

$id=$_POST["id"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="DELETE FROM paquetes_articulos WHERE id_articulos=$id;";
	
	$bd->query($sql);
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

$bd=NULL;
echo json_encode($r);
?>