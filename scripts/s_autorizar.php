<?php session_start();
header("content-type: application/json");
include("datos.php");
$emp=$_SESSION["id_empresa"];
$eve=$_POST["id_evento"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	foreach($_POST["items"] as $art){
		$id_salida=$art["art"];
		$bd->query("UPDATE almacen_salidas SET salio=1 WHERE id_salida=$id_salida;");
	}
	
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage()." $sql";
}

echo json_encode($r);
?>