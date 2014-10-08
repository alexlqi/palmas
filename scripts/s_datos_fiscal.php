<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];

include("datos.php");
include("pivotes.php");

$id=$_POST["id"];
$tabla=$_POST["tabla"]."_fiscal";
$pivote=$pivotes[$_POST["tabla"]];

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico
	$campos=array();
	$res=$bd->query("DESCRIBE $tabla;");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $a=>$c){
		$campos[$a]=$c["Field"];
	}
	
	$r=NULL;
	$res=$bd->query("SELECT * FROM $tabla WHERE id_empresa=$empresaid AND $pivote=$id;");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $i=>$v){
		$r["form"]="#f_".$tabla;
		foreach($campos as $campo){
			$r[$campo]=$v[$campo];
		}//*/
	}
	
}catch(PDOException $err){
	$r=$err->getMessage();
}

echo json_encode($r);
?>