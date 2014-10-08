<?php session_start();
header("content-type: application/json");
include("datos.php");
include("pivotes.php");

$tabla=$_POST["tabla"]."_articulos";
$id=$_POST["id"];
$pivote=$pivotes[$_POST["tabla"]];
$id_empresa=$_SESSION["id_empresa"];

try{
	$sql="";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$sql="SELECT 
		SUM(total) as total
	FROM $tabla
	WHERE $pivote=$id;";
	
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	
	$r["continuar"]=true;
	$r["total"]=$res[0]["total"];
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>