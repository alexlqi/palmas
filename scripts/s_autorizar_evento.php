<?php session_start();
header("content-type: application/json");
include("datos.php");
$id_eve=$_POST["id_evento"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="UPDATE eventos SET estatus=2 WHERE id_evento=$id_eve;";
	
	$bd->query($sql);
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

$bd=NULL;
echo json_encode($r);
?>