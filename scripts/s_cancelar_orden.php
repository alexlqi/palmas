<?php session_start();
header("content-type: application/json");
include("datos.php");
$id=$_POST["id"];

try{
	//cambiar a 0 cuando sea para entregar
	$sql="UPDATE compras SET estatus=1 WHERE id_compra=$id;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$bd->query($sql);
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>