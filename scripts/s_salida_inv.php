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
	
	//2.- De la cantidad existente en el almacén checar las salidas del almacen para tener actualizado el inventario
	//tomando en cuenta la fecha de desmontaje y que esten entregados "salio=1"
	$salidas=0;
	$res=$bd->query("SELECT 
		cantidad
	FROM almacen_salidas
	WHERE id_articulo=$id_art AND salio=1 AND fechamontaje <= '$ahorita'
	GROUP BY id_articulo;");
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$salidas=$res[0]["cantidad"];
	}
	$paraInv=$nuevaCant-$salidas;
	
	//3.- Como en el inventario debería ser lo mismo que en el almacén pero no con cantidades dinamicas
	//    por lo tanto se puede usar el id_item ya sacado de almacen y usarlo en almacen_inventario y dalre insert on update
	$bd->query("INSERT INTO almacen_inventario (id_item,id_empresa,id_articulo,cantidad) 
	VALUES ($id_item,$id_emp,$id_art,'$nuevaCant') 
	ON DUPLICATE KEY UPDATE cantidad='$nuevaCant';");
	$r["continuar"]=true;
	actInv($dsnw,$userw,$passw,$optPDO);
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error (".$err->getCode()."): ".$err-getMessage();
}

echo json_encode($r);

//print_r($datos);
?>