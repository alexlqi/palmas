<?php session_start();
header("content-type: application/json");
include("datos.php");
include("s_check_inv_compra.php");

$id_emp=$id_empresa=$_SESSION["id_empresa"];
$id_cot=$_POST["id_cotizacion"];
$total=$_POST["total"];
$anticipo=$_POST["anticipo"];
$id_usuario=$_SESSION["id_usuario"];

if($id_cot!=""){
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		//checar si ya se pasó a eventos
		$sql="SELECT * FROM eventos WHERE id_cotizacion=$id_cot;";
		$res=$bd->query($sql);
		if($res->rowCount()>0){
			$r["continuar"]=false;
			$r["info"]="Esta cotización ya se convirtió en evento";
			echo json_encode($r);
			exit;
		}
		
		$sql="INSERT INTO eventos
		SELECT null,cotizaciones.*
		FROM cotizaciones
		WHERE cotizaciones.id_cotizacion = $id_cot;";
		$res=$bd->query($sql);
		
		//para obtener el id de evento
		$sql="SELECT 
			id_evento,
			id_cliente
		FROM eventos
		WHERE id_cotizacion = $id_cot;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		
		//se conoce el id de cliente y del evento
		$id_eve=$res[0]["id_evento"];
		$id_cliente=$res[0]["id_cliente"];
		
		//para añadir los articulos de la cotiacion a los articulos del evento
		$sql="INSERT INTO eventos_articulos
		SELECT 
			null,
			$id_eve,
			$id_cot,
			cotizaciones_articulos.id_articulo,
			cotizaciones_articulos.id_paquete,
			cotizaciones_articulos.cantidad,
			cotizaciones_articulos.precio,
			cotizaciones_articulos.total
		FROM cotizaciones_articulos
		WHERE cotizaciones_articulos.id_cotizacion = $id_cot;";
		$res=$bd->query($sql);
		
		$sql="UPDATE cotizaciones SET estatus=2 WHERE id_cotizacion = $id_cot;";
		$res=$bd->query($sql);
		
		/* ################################
		 # mover los articulos del almacen
		 # 1.- Se deben de leer los articulos y las cantidades de la cotización
		 # 2.- Se deben de leer los paquetes a usar y extraer los articulos de cada paquete con sus cantidad
		 #     de la tabla paquetes_articulos
		 # 3.- Se deben de escribir los movimientos en la tabla almacen salidas
		 #################################*/
		$sql="SELECT
			id_cliente,
			eventos_articulos.id_articulo,
			eventos_articulos.id_paquete,
			cantidad,
			estatus,
			fechamontaje,
			fechadesmont
		FROM eventos
		INNER JOIN eventos_articulos ON eventos.id_evento=eventos_articulos.id_evento
		WHERE eventos.id_evento=$id_eve;";
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			//$id_empresa
			//$id_evento
			$id_art=$v["id_articulo"];
			$cantidad=$v["cantidad"];
			$montaje=$v["fechamontaje"];
			$desmontaje=$v["fechadesmont"];
			$estatus=$v["estatus"];
			$id_cliente=$v["id_cliente"];
			
		//para las salidas
			//checa si es articulo o paquete
			if($v["id_articulo"]!=""){ //articulo
				$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje) VALUES ($id_empresa,$id_eve,$id_art,$cantidad,'$montaje');";
				$bd->query($sql);
			}else{ //paquete
				$sql="INSERT INTO 
					almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje) 
				SELECT $id_empresa,$id_eve,articulos.id_articulo,cantidad*$cantidad as cantidad,'$montaje' 
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=".$v["id_paquete"].";";
				$bd->query($sql);
			}
			
		//para las entradas
			if($v["id_articulo"]!=""){
				//paso previo, checar si es perecedero, si no lo es entonces se debe omitir el paso 1
				$id_art=$v["id_articulo"];
				$resDos=$bd->query("SELECT perece FROM articulos WHERE id_empresa=$id_emp AND id_articulo=$id_art;");
				$resDos=$resDos->fetchAll(PDO::FETCH_ASSOC);
				$perece=$resDos[0]["perece"];
				if($perece==0){
					//no es perecedero
					$sql="INSERT INTO almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont) VALUES ($id_empresa,$id_eve,$id_art,$cantidad,'$desmontaje');";
					$bd->query($sql);
				}else{
					//si es perecedero no vuelve a entrar
				}
				
			}else{
				//no es perecedero
				$sql="INSERT INTO 
					almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont) 
				SELECT $id_empresa,$id_eve,articulos.id_articulo,cantidad,'$desmontaje' 
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=".$v["id_paquete"]." AND articulos.perece=0;";
				$bd->query($sql);
			}
		}//*/
		
		/* ################################
		 # Poner el total del evento
		 # 1.- Crear el identificador de empresa_evento para que sea unico
		 # 2.- añadir el total
		 ##################################*/
		 $id_emp_eve=$id_empresa."_".$id_eve;
		 $sqlEveTotal="INSERT INTO eventos_total (id_evento,total) VALUES ('$id_emp_eve','$total');";
		 $bd->query($sqlEveTotal);
		 
		 /* ################################
		 # Registrar anticipo
		 # 1.- Crear el identificador de empresa_evento para que sea unico
		 # 2.- añadir el total
		 ##################################*/
		 $sqlPago="INSERT INTO eventos_pagos (id_evento,id_cliente,plazo,cantidad) VALUES ('$id_emp_eve',$id_cliente,'anticipo','$anticipo');";
		 $bd->query($sqlPago);
		 
		 /* ################################
		 # Registrar comisión del vendedor por hacer un evento
		 # 1.- Crear el identificador de empresa_evento para que sea unico
		 # 2.- añadir el total
		 ##################################*/
		 $comision=$total*$_SESSION["comision"];
		 $sqlComision="INSERT INTO usuarios_comisiones (
		 	id_empresa,id_usuario,id_evento,comision) 
		 VALUES
		 	($id_empresa,$id_usuario,'$id_emp_eve','$anticipo');";
		 $bd->query($sqlComision);
		 
		 $r["info"]=ordenCompra($id_eve);
		
		$r["continuar"]=true;
	}catch(PDOException $err){
		$r["continuar"]=false;
		$r["info"]="Error: ".$err->getMessage()." <br />$sql";
	}
}else{
	$r["continuar"]=false;
	$r["info"]="No ha seleccionado ninguna cotización";
}

echo json_encode($r);
?>