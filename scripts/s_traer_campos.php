<?php session_start();
$empresaid=$_SESSION["id_empresa"];

include("datos.php");
$id=$_POST["id"];
$tabla=$_POST["tabla"];

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico
	$campos=array();
	$res=$bd->query("DESCRIBE $tabla;");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $a=>$c){
		$campos[$a]=$c["Field"];
	}
	
	$res=$bd->query("SELECT * FROM $tabla WHERE id_empresa=$empresaid AND $pivote=$datoPivote;");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $i=>$v){
		foreach($campos as $campo){
			$r[$i]["$tabla"."_".$campo]=$v[$campo];
		}
	}
	
}catch(PDOException $err){
	echo $err->getMessage();
}

echo json_encode($r);
?>