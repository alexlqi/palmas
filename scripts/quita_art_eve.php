<?php session_start();
//script para eliminar articulos desde la tabla de articulos
include("datos.php");
header("Content-type: application/json");
$id_item=$_POST["id_item"];
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$bd->beginTransaction();
	
	$sql="SELECT id_evento,total FROM eventos_articulos WHERE id_item=$id_item;";
	$res=$bd->query($sql);
	$d=$res->fetchAll(PDO::FETCH_ASSOC);
	$aRestar=$d[0]["total"];
	$id_emp_eve=$emp."_".$d[0]["id_evento"];
	
	$sql="UPDATE eventos_total SET total=total-$aRestar WHERE id_evento='$id_emp_eve';";
	$bd->exec($sql);
	
	$sql="DELETE FROM eventos_articulos WHERE id_item=$id_item;";
	$bd->exec($sql);
	
	//quitar el articulo de las entradas y salidas usando el id_item
	$sql="DELETE FROM almacen_entradas WHERE id_item=$id_item;";
	$bd->exec($sql);
	$sql="DELETE FROM almacen_salidas WHERE id_item=$id_item;";
	$bd->exec($sql);
	
	//modificar el total del evento
	
	$bd->commit();
	$r["continuar"]=true;
	$r["info"]="Articulo eliminado satisfactoriamente";
}catch(PDOException $err){
	$bd->rollBack();
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}

echo json_encode($r);
?>