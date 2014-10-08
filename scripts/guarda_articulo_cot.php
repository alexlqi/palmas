<?php //script para eliminar articulos desde la tabla de articulos
include("datos.php");
header("Content-type: application/json");
$id_item=$_POST["id_item"];
$cant=$_POST["cantidad"]; //cantidad
$precio=$_POST["precio"]; //cantidad
$total=$cant*$precio;
$cot=$_POST["id_cotizacion"]; //cotizacion
$art=$_POST["id_articulo"]; //articulo id
$paq=$_POST["id_paquete"]; //paquete id
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	
	$sqlBuscar="";
	if($art!=""){//si es articulo
		if($id_item!=""){//si ya está guardado previamente
			$sql="UPDATE cotizaciones_articulos SET id_cotizacion=$cot, id_articulo=$art, cantidad=$cant, precio=$precio, total=$total WHERE id_item=$id_item;";
			$r["info"]="Modificacion al <strong>articulo</strong> realizada exitosamente";
		}else{//registro nuevo
			$sql="INSERT INTO 
				cotizaciones_articulos (id_cotizacion, id_articulo, cantidad, precio, total)
			VALUES ($cot, $art, $cant, $precio, $total);";
			$sqlBuscar="SELECT id_item FROM cotizaciones_articulos WHERE id_cotizacion=$cot AND id_articulo=$art AND total=$total LIMIT 1;";
			$r["info"]="<strong>Articulo</strong> guardado exitosamente";
		}
	}else if($paq!=""){//si es paquete
		if($id_item!=""){//si ya está guardado previamente
			$sql="UPDATE cotizaciones_articulos SET id_cotizacion=$cot, id_paquete=$paq, cantidad=$cant, precio=$precio, total=$total WHERE id_item=$id_item;";
			$r["info"]="Modificación al <strong>paquete</strong> realizada exitosamente";
		}else{//registro nuevo
			$sql="INSERT INTO 
				cotizaciones_articulos (id_cotizacion, id_paquete, cantidad, precio, total)
			VALUES ($cot, $paq, $cant, $precio, $total);";
			$sqlBuscar="SELECT id_item FROM cotizaciones_articulos WHERE id_cotizacion=$cot AND id_paquete=$paq AND total=$total LIMIT 1;";
			$r["info"]="<strong>Paquete</strong> guardado exitosamente";
		}
	}
	
	$bd->query($sql);
	
	if($sqlBuscar!=""){
		$res=$bd->query($sqlBuscar);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$id_item=$res[0]["id_item"];
	}
	
	$r["id_item"]=$id_item;
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}
//0084609

echo json_encode($r);
?>