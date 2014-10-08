<?php session_start();
header("content-type: application/json");
include("datos.php");
$paq=$_POST["paq"];
$art=$_POST["art"];
$cant=$_POST["cant"];

try{
	$sql="";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$bd->query("INSERT INTO paquetes_articulos (id_paquete,id_articulo,cantidad) VALUES ($paq,$art,$cant);");
	
	$r["continuar"]=true;
	$r["info"]="Articulo añadido al paquete";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>