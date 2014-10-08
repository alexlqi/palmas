<?php session_start();
header("content-type: application/json");
include("datos.php");
$eve=$_POST["id_evento"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	foreach($_POST["items"] as $d){
		$sql="SELECT id_entrada, cantidad FROM almacen_entradas WHERE id_evento=$eve AND id_articulo=".$d["art"].";";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$id_entrada=$res[0]["id_entrada"];
		$cantidad=$res[0]["cantidad"];
		if($cantidad==$d["cant"]){
			$entro=1;
		}else{
			$entro=0;
		}
		$bd->query("UPDATE almacen_entradas SET entro=$entro, regresaron='".$d["cant"]."' WHERE id_entrada=$id_entrada;");
	}
	
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>