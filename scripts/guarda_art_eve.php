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

//pendiente.- mover del almacén nuevos elementos

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
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
			$bd->query($sql);
			$sql="UPDATE almacen_salidas SET termino=1, salio=1 WHERE id_evento=$eve AND id_articulo=$art;";
			$bd->query($sql);
			
			//modificar las entradas y salidas con el negativo de cantPrevia
			$sql="INSERT INTO almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont,entro,termino) VALUES ($emp,$eve,$art,'-$cantPrevia','$desmontaje',1,1);";
			$bd->query($sql);
			$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,salio,termino) VALUES ($emp,$eve,$art,'-$cantPrevia','$montaje',1,1);";
			$bd->query($sql);
			
			//modificar el articulo del evento
			$sql="UPDATE eventos_articulos SET id_evento=$eve, id_articulo=$art, cantidad=$cant, precio=$precio, total=$total WHERE id_item=$id_item;";
			$bd->query($sql);
			
			$r["info"]="Modificacion al <strong>articulo</strong> realizada exitosamente";
		
		}else{//registro nuevo con modificación al inventario
			$sql="INSERT INTO 
				eventos_articulos (id_evento, id_articulo, cantidad, precio, total)
			VALUES ($eve, $art, $cant, $precio, $total);";
			$bd->query($sql);
			$id_item=$bd->lastInsertId();
			
			$r["info"]="<strong>Articulo</strong> guardado exitosamente";
		}
		//se debe añadir los elementos recién ingresados a la lista de salidas y entradas
		//si perece entonces no deben tener entrada de vuelta solamente salida
		if($perece==0){//no perece, da la entrada y salida
			//salida
			$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,id_item) VALUES ($emp,$eve,$art,$cant,'$montaje',$id_item);";
			$bd->query($sql);
			
			//entrada
			$sql="INSERT INTO almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont,id_item) VALUES ($emp,$eve,$art,$cant,'$desmontaje',$id_item);";
			$bd->query($sql);
		}else{
			//sí perece, da la salida solamente
			$sql="INSERT INTO almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,id_item) VALUES ($emp,$eve,$art,$cant,'$montaje',$id_item);";
			$bd->query($sql);
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
				$bd->query($sql);
				
				//actualiza los estatus del item
				$sql="UPDATE almacen_salidas SET termino=1, salio=1 WHERE id_evento=$eve AND id_articulo IN (SELECT id_articulo FROM paquetes_articulos WHERE id_paquete=$paq);";
				$bd->query($sql);
				$sql="UPDATE almacen_entradas SET termino=1, entro=1 WHERE id_evento=$eve AND id_articulo IN (SELECT id_articulo FROM paquetes_articulos WHERE id_paquete=$paq);";
				$bd->query($sql);
				
				//se restan las entradas del paq cuyo articulo no sea perecedero
				$sql="INSERT INTO 
					almacen_entradas (id_empresa,id_evento,id_articulo,regresaron,fechadesmont, entro, termino) 
				SELECT 1,1,articulos.id_articulo,(SELECT cantidad FROM eventos.almacen_salidas WHERE id_articulo=articulos.id_articulo ORDER BY id_salida DESC LIMIT 1)*-1 as cantidad,'$desmontaje',1,1
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq AND articulos.perece=0;";
				$bd->query($sql);
			
			//se actualizan las cantidades del paquete en eventos_articulos
			$sql="UPDATE eventos_articulos SET id_evento=$eve, id_paquete=$paq, cantidad=$cant, precio=$precio, total=$total WHERE id_item=$id_item;";
			$bd->query($sql);
			
				//se escriben las salidas del paq
				$sql="INSERT INTO 
					almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje) 
				SELECT $emp,$eve,articulos.id_articulo,paquetes_articulos.cantidad*$cant as cantidad,'$montaje' 
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq;";
				$bd->query($sql);
				
				//se escriben las entradas del paq cuyo articulo no sea perecedero
				$sql="INSERT INTO 
					almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont) 
				SELECT $emp,$eve,articulos.id_articulo,paquetes_articulos.cantidad*$cant as cantidad,'$desmontaje' 
				FROM paquetes_articulos
				INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
				WHERE id_paquete=$paq AND articulos.perece=0;";
				$bd->query($sql);
			
			$r["info"]="Modificación al <strong>paquete</strong> realizada exitosamente";
		}else{//registro nuevo
			$sql="INSERT INTO
				eventos_articulos (id_evento, id_paquete, cantidad, precio, total)
			VALUES ($eve, $paq, $cant, $precio, $total);";
			$bd->query($sql);
			$id_item=$bd->lastInsertId();
			
			//se escriben las salidas de los ariculos del paquete
			$sql="INSERT INTO 
				almacen_salidas (id_empresa,id_evento,id_articulo,cantidad,fechamontaje,id_item) 
			SELECT $emp,$eve,articulos.id_articulo,cantidad*$cant as cantidad,'$montaje',$id_item 
			FROM paquetes_articulos
			INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
			WHERE id_paquete=$paq;";
			$bd->query($sql);
			
			//se restan las entradas del item cuyo articulo no sea perecedero
			$sql="INSERT INTO 
				almacen_entradas (id_empresa,id_evento,id_articulo,cantidad,fechadesmont,id_item) 
			SELECT $emp,$eve,articulos.id_articulo,paquetes_articulos.cantidad*$cant as cantidad,'$desmontaje',$id_item
			FROM paquetes_articulos
			INNER JOIN articulos ON paquetes_articulos.id_articulo=articulos.id_articulo
			WHERE id_paquete=$paq AND articulos.perece=0;";
			$bd->query($sql);
			
			//
			$r["info"]="<strong>Paquete</strong> guardado exitosamente";
		}
	}
	
	//se actualiza el inventario ys e genera l orden de compra
	actInv($dsnw, $userw, $passw, $optPDO);
	ordenCompra($eve);
	
	//también se debe actualizar el total del evento en las tablas
	//1.- primero busca el reg del evento
	$eve;
	
	//2.- modificar el total +=$total
	$total;
	
	$r["id_item"]=$id_item;
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage()." $sql";
}
//0084609

echo json_encode($r);
?>