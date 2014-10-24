<?php session_start();
include ("datos.php");
header('content-type: application/json');
if(isset($_POST)){

	$data=$_POST["compra"];
	
	//variables importantes
	$id_compra=$data["id_compra"];
	$eve=$data["id_evento"];
	$total=$data["totalcompra"];
	$emp=$_SESSION["id_empresa"];
	$metodo=$data["metodo"];
	$mont=$data["mont"];
	$desmont=$data["desmont"];
	/* para el debug
	$r["continuar"]=true;
	$r["info"]=$_POST["compra"];//*/

	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		//el manejo de la orden de compra se divide en 3 o 4 partes
		
		//1.- Afectar Bancos (si aplica)
		if($data["banco"]!=""){
			$id_banco=$data["banco"];
			$sql="INSERT INTO bancos_movimientos (id_empresa,id_banco,movimiento,monto) VALUES ($emp,$id_banco,'retiro','$total');";
			$bd->query($sql);
		}else{
			$id_banco=0;
		}
		//2.- Afectar Inventarios y Proveedores
		foreach($data["articulos"] as $art=>$d){
			//articulo $art
			$prov=$d["proveedor"]; //id del proveedor
			$cant=$d["cantidad"]; //cantidad del articulo
			$perece=$d["perece"]; //saber si perece o no
			$monto=$d["monto"]; //monto pagado del articulo
			
			if($metodo=="renta"){
				$sql="INSERT INTO almacen_temporal (id_empresa,id_proveedor,id_articulo,cantidad) VALUES ($emp,$prov,$art,'$cant');";
				$bd->query($sql);
				if($perece==0){ //si no es perecedero
					$sql="INSERT INTO almacen_entradas (id_empresa,id_articulo,fechadesmont,cantidad) VALUES ($emp,$art,'$desmont','$cant');";
				}else{//si es perecedero
					$sql="INSERT INTO almacen_entradas (id_empresa,id_articulo,fechadesmont,cantidad,entro,termino) VALUES ($emp,$art,'$desmont','$cant',1,1);";
				}
				$bd->query($sql);
				$mov="renta";
			
			}else{
			//si lo va a comprar
				$mov="compra";
				//1.- checar si ya existe en el almacén
				if($perece==0){ //articulos no perecederos
					$res=$bd->query("SELECT id_item, cantidad FROM almacen WHERE id_empresa=$emp AND id_articulo=$art;");
					if($res->rowCount()>0){
						//si existe entonces sumar la cantidad de los inventarios y sumar la nueva cantidad
						$res=$res->fetchAll(PDO::FETCH_ASSOC);
						$id_item=$res[0]["id_item"];
						$cantidadPrevia=$res[0]["cantidad"];
						$nuevaCant=$cantidadPrevia+$cant;
						$sql="UPDATE almacen SET cantidad='$nuevaCant' WHERE id_item=$id_item;";
						$bd->query($sql);
					}else{
						//si no existe entonces agregar al almacen la cantidad
						$id_item="NULL";
						$sql="INSERT INTO almacen (id_empresa,id_articulo,cantidad) VALUES ($emp,$art,$cant);";
						$bd->query($sql);
					}
				}else{//articulos perecedores
					if($perece==0){ //si no es perecedero
						$sql="INSERT INTO almacen_entradas (id_empresa,id_articulo,fechadesmont,cantidad,regresaron) VALUES ($emp,$art,'$desmont','$cant','$cant');";
					}else{//si es perecedero
						$sql="INSERT INTO almacen_entradas (id_empresa,id_articulo,fechadesmont,cantidad,regresaron,entro,termino) VALUES ($emp,$art,'$desmont','$cant','$cant',1,1);";
					}
					$bd->query($sql);
				}
			}
			
			//modificación a los proveedores
			$sql="INSERT INTO proveedores_movimientos 
				(id_empresa,id_proveedor,movimiento,id_ref,cantidad) 
			VALUES 
				($emp,$prov,'$mov',$id_compra,'$monto');";
			$bd->query($sql);
			
			//modificación a compras_pagos
			$sql="INSERT INTO compras_pagos (id_empresa,id_compra,id_banco,id_articulo,monto) VALUES ($emp,$id_compra,$id_banco,$art,'$monto');";
			$bd->query($sql);
		}
		//3.- Afectar la compra
		$sql="UPDATE compras SET estatus=2 WHERE id_compra=$id_compra;";
		$bd->query($sql);
		
		//4.- Afectar los gastos del evento
		$sql="INSERT INTO eventos_gastos (id_empresa,id_evento,concepto,id_ref,gasto) VALUES ($emp,$eve,'compra',$id_compra,'$total');";
		$bd->query($sql);
		
		$r["continuar"]=true;
		$r["info"]="Compra realizada con exito";
	}catch(PDOException $err){
		$r["continuar"]=false;
		$r["info"]="Error: ".$err->getMessage()." en $sql";
	}//*/
}else{
	$r["continuar"]=false;
	$r["info"]= 'no hay datos';
}

echo json_encode($r);
?>