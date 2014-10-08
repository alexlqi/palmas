<?php session_start();
include("datos.php");
$userid=$_SESSION["id_usuario"];
$empresaid=$_SESSION["id_empresa"];
$categoria=$_SESSION["categoria"];
setlocale(LC_ALL,"");
setlocale(LC_TIME,"es_MX");
date_default_timezone_set("America/Monterrey");
header("Content-type: Application/json");

/*Sección de los arrays*/
$s_cot=array(0=>"cancelado",1=>"pendiente"); //status cotización
$s_eve=array(0=>"cancelado",1=>"no_autorizado",2=>"evento"); //status evento

//---------------------//
$fechas_id="dia".date("j",strtotime("2014-06-13 15:09:09"));

if(isset($_POST["m"]) and isset($_POST["a"])){
	$m=$_POST["m"];
	$a=$_POST["a"];
	$inicio=date("Y-m-d H:i:s",mktime(0,0,0,$m,1,$a));
	$final=date("Y-m-d H:i:s",mktime(23,59,59,$m+1,0,$a));
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		//Para checar las cotizaciones con el filtro por admin o no
		if($categoria=="administrador"){
			$sqlCot="SELECT 
				*,
				cotizaciones.clave as clave,
				clientes.nombre as nombre_cliente,
				cotizaciones.nombre as nombre,
				tipo_evento.nombre as tipo_evento
			FROM cotizaciones
			INNER JOIN tipo_evento ON cotizaciones.id_tipo=tipo_evento.id_tipo
			INNER JOIN clientes ON cotizaciones.id_cliente=clientes.id_cliente
			WHERE cotizaciones.id_empresa=$empresaid AND fechaevento BETWEEN '$inicio' AND '$final' AND estatus BETWEEN 0 AND 1;";
		}else{
			$sqlCot="SELECT 
				*,
				cotizaciones.clave as clave,
				clientes.nombre as nombre_cliente,
				cotizaciones.nombre as nombre,
				tipo_evento.nombre as tipo_evento
			FROM cotizaciones
			INNER JOIN tipo_evento ON cotizaciones.id_tipo=tipo_evento.id_tipo
			INNER JOIN clientes ON cotizaciones.id_cliente=clientes.id_cliente
			WHERE cotizaciones.id_usuario=$userid AND cotizaciones.id_empresa=$empresaid AND fechaevento BETWEEN '$inicio' AND '$final' AND estatus BETWEEN 0 AND 1;";
		}
		$res=$bd->query($sqlCot);
		if($res->rowCount()>0){
			$r["continuar"]=true;
			$r["data"]["c"]=$res->fetchAll(PDO::FETCH_ASSOC);
			$ctrl=0;
			foreach($r["data"]["c"] as $i=>$v){
				unset($r["data"]["c"][$i]);
				$r["data"]["c"]["#dia".date("j",strtotime($v["fechaevento"]))][$ctrl]=$v;
				$r["data"]["c"]["#dia".date("j",strtotime($v["fechaevento"]))][$ctrl]["estatus"]=$s_cot[$v["estatus"]];
				$ctrl++;
			}
		}else{
			$r["data"]["c"]=NULL;
			$r["info"]="No hay elementos aquí";
		}
		
		//Para checar los eventos
		$sqlEve="SELECT 
			* 
		FROM eventos 
		WHERE id_usuario=$userid AND id_empresa=$empresaid AND fechaevento BETWEEN '$inicio' AND '$final';";
		if($categoria=="administrador"){
			$sqlEve="SELECT 
				* 
			FROM eventos 
			WHERE id_empresa=$empresaid AND fechaevento BETWEEN '$inicio' AND '$final';";
		}
		$res=$bd->query($sqlEve);
		if($res->rowCount()>0){
			$r["continuar"]=true;
			$r["data"]["e"]=$res->fetchAll(PDO::FETCH_ASSOC);
			$ctrl=0;
			foreach($r["data"]["e"] as $i=>$v){
				unset($r["data"]["e"][$i]);
				$r["data"]["e"]["#dia".date("j",strtotime($v["fechaevento"]))][$ctrl]=$v;
				$r["data"]["e"]["#dia".date("j",strtotime($v["fechaevento"]))][$ctrl]["estatus"]=$s_eve[$v["estatus"]];
				$ctrl++;
			}
		}else{
			$r["data"]["e"]=NULL;
			$r["info"]="No hay elementos aquí";
		}
	}catch(PDOException $err){
		$r["continuar"]=false;
		$r["info"]=$err->getMessage();
	}
}else{
	$r["continuar"]=false;
	$r["info"]="Datos de fecha incorrectos.";
}
echo json_encode($r);
?>