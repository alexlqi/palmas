<?php session_start();
include("datos.php");
include("funciones.php");
header('Content-type: application/json');
$id_emp=$_SESSION["id_empresa"];
$id_art=$_POST["id_articulo"];
$cant=$_POST["cantidad"];
$ahorita=date("Y-m-d H:i:s");

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	//1.- checar si ya existe en el almacén
	$res=$bd->query("SELECT id_item, cantidad FROM almacen WHERE id_empresa=$id_emp AND id_articulo=$id_art;");
	if($res->rowCount()>0){
		//si existe entonces sumar la cantidad de los inventarios y sumar la nueva cantidad
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$id_item=$res[0]["id_item"];
		$cantidadPrevia=$res[0]["cantidad"];
		$nuevaCant=$cantidadPrevia+$cant;
		$bd->query("UPDATE almacen SET cantidad='$nuevaCant' WHERE id_item=$id_item;");
	}else{
		//si no existe entonces agregar al almacen la cantidad
		$id_item="NULL";
		$bd->query("INSERT INTO almacen (id_empresa,id_articulo,cantidad) VALUES ($id_emp,$id_art,$cant);");
		$nuevaCant=$cantidadPrevia;
	}
	
	//actualizar el inventario
	actInv($dsnw,$userw,$passw,$optPDO);
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error (".$err->getCode()."): ".$err-getMessage();
}//*/

echo json_encode($r);

//print_r($datos);
?>