<?php session_start();
header("content-type: application/json");
include("datos.php");
include("pivotes.php");

$id=$_POST["id"];
$id_empresa=$_SESSION["id_empresa"];
$id_emp_eve=$id_empresa."_".$id;

try{
	$sql="";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$sql="SELECT 
		total
	FROM eventos_total
	WHERE id_evento='$id_emp_eve';";
	
	$res=$bd->query($sql);
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$total=$res[0]["total"];
		
		//leer los pagos hechos
		$res=$bd->query("SELECT SUM(cantidad) as pagado FROM eventos_pagos WHERE id_evento='$id_emp_eve' GROUP BY id_evento;");
		if($res->rowCount()>0){
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
			$pagado=$res[0]["pagado"];
		}
		
		$r["continuar"]=true;
		$r["total"]=$total;
		if(($total-$pagado)==0){
			$bd->query("UPDATE eventos SET estatus=2 WHERE id_evento=$id;");
		}
		$r["restante"]=$total-$pagado;
		
	}else{
		$r["continuar"]=false;
		$r["info"]="Error: no existe total";
	}
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>