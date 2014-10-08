<?php session_start();
header("content-type: application/json");
include("datos.php");
$emp=$_SESSION["id_empresa"];
$eve=$_POST["id_evento"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	foreach($_POST["items"] as $art){
		$sql="SELECT id_salida FROM almacen_salidas WHERE id_evento=$eve AND id_empresa=$emp AND id_articulo=".$art["art"].";";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$id_salida=$res[0]["id_salida"];
		$bd->query("UPDATE almacen_salidas SET salio=1 WHERE id_salida=$id_salida;");
	}
	
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage()." $sql";
}

echo json_encode($r);
?>