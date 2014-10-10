<?php session_start();
header("content-type: application/json");
include("datos.php");
$id=$_POST["id"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$bd->beginTransaction();
	
	//eliminar el paquete
	$sql="DELETE FROM paquetes WHERE id_paquete=$id;";
	$bd->exec($sql);
	
	//eliminar los articulos en el paquete
	$sql="DELETE FROM paquetes_articulos WHERE id_paquete=$id;";
	$bd->exec($sql);
	
	//si todo sale bien
	$bd->commit();
	$r["continuar"]=true;
}catch(PDOException $err){
	//si hubo un error
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
	$bd->rollBack();
}

$bd=NULL;
echo json_encode($r);
?>