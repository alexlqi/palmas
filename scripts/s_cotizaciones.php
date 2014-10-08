<?php session_start();
header("Content-type: application-json");
include("datos.php");

try{
	//hacer el sql
	foreach($_POST["datos"] as $form=>$datos){
		foreach($datos as $item=>$valor){
			$datos[$form][$valor["name"]]=$valor["value"];
		}
	}
	foreach($_POST["datos"] as $campo => $valor){
		$campos.= $campo.",";
		$values.= "'".$valor."',"; //numerico no lleva ''
	}
	$campos=trim($campos,",");
	$values=substr($values, 0, -1);
	$sql="INSERT INTO $tabla ($campos) VALUES ($values);";
	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$bd->query($sql);
	$r["continuar"]=true;
	$r["info"]="Cotización añadida satisfactoriamente.";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}

?>