<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
$term=$_GET["term"];
include("datos.php");

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico	
	$sqlArt="SELECT 
		*
	FROM paquetes
	INNER JOIN listado_precios ON paquetes.id_paquete=listado_precios.id_paquete
	WHERE paquetes.id_empresa=$empresaid AND paquetes.clave=$term;";
	
	$i=0;
	$res=$bd->query($sqlArt);
	$r=array("nombre"=>"No existe paquete con esta clave");
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$r=$res[0];
	}
}catch(PDOException $err){
	$r=$err->getMessage();
}

echo json_encode($r);
?>