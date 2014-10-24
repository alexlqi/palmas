<?php session_start();
//script para guardar articulos desde la tabla de articulos en eventos_articulos
include("datos.php");
include("funciones.php");
include("s_check_inv_compra.php");
header("Content-type: application/json");

$emp=$_SESSION["id_empresa"];
$id_item=$_POST["id_item"];
$cant=$_POST["cantidad"]; //cantidad
$precio=$_POST["precio"]; //precio
$total=$cant*$precio; //total
$eve=$_POST["id_evento"]; //evento
$art=$_POST["id_articulo"]; //articulo id
$paq=$_POST["id_paquete"]; //paquete id

//boolean para modificar el total
$boolTotal=false;
if($_POST["boolTotal"]=="true"){
	$boolTotal=true;
}

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$bd->beginTransaction();
	
	$bd->query("SET SQL_SAFE_UPDATES=0;");
	
	$sql="SELECT fechamontaje, fechadesmont FROM eventos WHERE id_evento=$eve;";
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$montaje=$res[0]["fechamontaje"];
	$desmontaje=$res[0]["fechadesmont"];
	
	$sqlBuscar="";
	if($art!=""){//si es articulo
		//buscar el evento y el perecedero del articulo
		$sql="SELECT perece FROM articulos WHERE id_articulo=$art;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$perece=$res[0]["perece"];
	
		if($id_item!=""){//si ya está guardado previamente hay que restar de salidas y entradas para volverlos a escribir
			//saber la cantidad original del item y luego restarlo de las entradas y salidas
			$sql="SELECT cantidad FROM eventos_articulos WHERE id_item=$id_item;";
			$res=$bd->query($sql);
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
			$cantPrevia=$res[0]["cantidad"];
			
			//termina los que estaban antes
			$sql="UPDATE almacen_entradas SET termino=1, entro=1 WHERE id_evento=$eve AND id_articulo=$art;";
			$bd->exec($sql);
			$sql="UPDATE almacen_salidas SET termino=1, salio=1 WHERE id_evento=$eve AND id_articulo=$art;";
			$bd->exec($sql);
			
			//modificar las entradas y salidas con el negativo de cantPrevia
			$sql="INSERT INTO almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont,entro,termino) VALUES ($emp,$eve,$art,'-$cantPrevia','$desmontaje',1,1);";
			$bd->exec($sql);
			$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,salio,termino) VALUES ($emp,$eve,$art,'-$cantPrevia','$montaje',1,1);";
			$bd->exec($sql);
			
			//modificar el articulo del evento
			$sql="UPDATE eventos_articulos SET id_evento=$eve, id_articulo=$art, cantidad=$cant, precio=$precio, total=$total WHERE id_item=$id_item;";
			$bd->exec($sql);
			
			$r["info"]="Modificacion al <strong>articulo</strong> realizada exitosamente";
		
		}else{//registro nuevo con modificación al inventario
			$sql="INSERT INTO 
				eventos_articulos (id_evento, id_articulo, cantidad, precio, total)
			VALUES ($eve, $art, $cant, $precio, $total);";
			$bd->exec($sql);
			$id_item=$bd->lastInsertId();
			
			$r["info"]="<strong>Articulo</strong> guardado exitosamente";
		}
		//se debe añadir los elementos recién ingresados a la lista de salidas y entradas
		//si perece entonces no deben tener entrada de vuelta solamente salida
		if($perece==0){//no perece, da la entrada y salida
			//salida
			$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,id_item) VALUES ($emp,$eve,$art,$cant,'$montaje',$id_item);";
			$bd->exec($sql);
			
			//entrada
			$sql="INSERT INTO almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont,id_item) VALUES ($emp,$eve,$art,$cant,'$desmontaje',$id_item);";
			$bd->exec($sql);
		}else{
			//sí perece, da la salida solamente
			$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,id_item) VALUES ($emp,$eve,$art,$cant,'$montaje',$id_item);";
			$bd->exec($sql);
		}
		
	}else if($paq!=""){//si es paquete
		if($id_item!=""){//si ya está guardado previamente
				//se restan las salidas del paq
				$sql="INSERT INTO 
					almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,salio,termino) 
				SELECT 1,1,articulos.id_articulo,(SELECT cantidad FROM eventos.almacen_salidas WHERE id_articulo=articulos.id_articulo ORDER BY id_salida DESC LIMIT 1)*-1 as cantidad,'$montaje',1,1
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq;";
				$bd->exec($sql);
				
				//actualiza los estatus del item
				$sql="UPDATE almacen_salidas SET termino=1, salio=1 WHERE id_evento=$eve AND id_articulo IN (SELECT id_articulo FROM paquetes_articulos WHERE id_paquete=$paq);";
				$bd->exec($sql);
				$sql="UPDATE almacen_entradas SET termino=1, entro=1 WHERE id_evento=$eve AND id_articulo IN (SELECT id_articulo FROM paquetes_articulos WHERE id_paquete=$paq);";
				$bd->exec($sql);
				
				//se restan las entradas del paq cuyo articulo no sea perecedero
				$sql="INSERT INTO 
					almacen_entradas (id_empresa,id_evento,id_articulo,regresaron,fechadesmont, entro, termino) 
				SELECT 1,1,articulos.id_articulo,(SELECT cantidad FROM eventos.almacen_salidas WHERE id_articulo=articulos.id_articulo ORDER BY id_salida DESC LIMIT 1)*-1 as cantidad,'$desmontaje',1,1
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq AND articulos.perece=0;";
				$bd->exec($sql);
			
			//se actualizan las cantidades del paquete en eventos_articulos
			$sql="UPDATE eventos_articulos SET id_evento=$eve, id_paquete=$paq, cantidad=$cant, precio=$precio, total=$total WHERE id_item=$id_item;";
			$bd->exec($sql);
			
				//se escriben las salidas del paq
				$sql="INSERT INTO 
					almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje) 
				SELECT $emp,$eve,articulos.id_articulo,paquetes_articulos.cantidad*$cant as cantidad,'$montaje' 
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq;";
				$bd->exec($sql);
				
				//se escriben las entradas del paq cuyo articulo no sea perecedero
				$sql="INSERT INTO 
					almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont) 
				SELECT $emp,$eve,articulos.id_articulo,paquetes_articulos.cantidad*$cant as cantidad,'$desmontaje' 
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq AND articulos.perece=0;";
				$bd->exec($sql);
			
			$r["info"]="Modificación al <strong>paquete</strong> realizada exitosamente";
		}else{//registro nuevo
			$sql="INSERT INTO
				eventos_articulos (id_evento, id_paquete, cantidad, precio, total)
			VALUES ($eve, $paq, $cant, $precio, $total);";
			$bd->exec($sql);
			$id_item=$bd->lastInsertId();
			
			//se escriben las salidas de los ariculos del paquete
			$sql="INSERT INTO 
				almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,id_item) 
			SELECT $emp,$eve,articulos.id_articulo,cantidad*$cant as cantidad,'$montaje',$id_item 
			FROM paquetes_articulos
			INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
			WHERE id_paquete=$paq;";
			$bd->exec($sql);
			
			//se restan las entradas del item cuyo articulo no sea perecedero
			$sql="INSERT INTO 
				almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont,id_item) 
			SELECT $emp,$eve,articulos.id_articulo,paquetes_articulos.cantidad*$cant as cantidad,'$desmontaje',$id_item
			FROM paquetes_articulos
			INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
			WHERE id_paquete=$paq AND articulos.perece=0;";
			$bd->exec($sql);
			
			//
			$r["info"]="<strong>Paquete</strong> guardado exitosamente";
		}
	}
	
	//se actualiza el inventario y se genera l orden de compra
	actInv($dsnw, $userw, $passw, $optPDO);
	ordenCompra($eve);
	
	//modificar el total +=$total
	if($boolTotal){
		$total;
		$id_emp_eve=$emp."_".$eve;
		$sql="UPDATE eventos_total SET total=total+$total WHERE id_evento='$id_emp_eve';";
		$bd->exec($sql);
	}
	
	$bd->commit();
	$r["id_item"]=$id_item;
	$r["continuar"]=true;
}catch(PDOException $err){
	$bd->rollBack();
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage()." $sql";
}
//0084609

echo json_encode($r);
?>