<?php session_start();
header("content-type: application/json");
include("datos.php");
$cve=$_POST["cve"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT estatus FROM cotizaciones WHERE id_cotizacion=$cve;";
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$estatus=$res[0]["estatus"];
	if($estatus!=2){
		$sql="UPDATE cotizaciones SET estatus=0 WHERE id_cotizacion=$cve;";
		$bd->query($sql);
		$r["continuar"]=true;
	}else{
		$r["continuar"]=false;
		$r["info"]="Esta cotización ya se convirtió en evento, no se puede cancelar";
	}
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

$bd=NULL;
echo json_encode($r);
?>