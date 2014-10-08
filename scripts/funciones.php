<?php //modulo de funciones varias
date_default_timezone_set("America/Monterrey");
function esteMes(){
	echo date("n");
}
function esteAnio(){
	echo date("Y");
}
function empresa(){
	echo $_SESSION["id_empresa"];
}
function varEmpresa(){
	return $_SESSION["id_empresa"];
}

//función para actualizar el inventario después de un movimiento
function actInv($dsnw,$userw,$passw,$optPDO){
	$id_empresa=$_SESSION["id_empresa"];
	$inicioMes=date("Y-m-d H:i:s",mktime(0,0,0,date("n"),1,date("Y")));
	$today=date("Y-m-d H:i:s");
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		//1.- saber el total del inventario de cada cosa se ocupa id_item, id_articulo y cantidad
		//saber también si perece o no
		$sql="SELECT
			id_item,
			almacen.id_articulo,
			cantidad,
			articulos.perece
		FROM almacen
		LEFT JOIN articulos ON almacen.id_articulo=articulos.id_articulo
		WHERE almacen.id_empresa=$id_empresa;";
		
		$item=array();
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			$item[$d["id_articulo"]]["id_item"]=$d["id_item"];
			$item[$d["id_articulo"]]["almacen"]=$d["cantidad"];
			$item[$d["id_articulo"]]["perece"]=$d["perece"];
			$item[$d["id_articulo"]]["entradas"]=0;
			$item[$d["id_articulo"]]["salidas"]=0;
		}
		//2.- saber las entradas
		$sql="SELECT
			id_articulo,
			SUM(regresaron) as entradas
		FROM almacen_entradas
		WHERE id_empresa=$id_empresa AND entro=1 AND fechadesmont > '$inicioMes' AND fechadesmont <= '$today'
		GROUP BY id_articulo;";
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			$item[$d["id_articulo"]]["entradas"]=$d["entradas"];
		}
		//3.- saber las salidas no terminadas
		$sql="SELECT
			id_articulo,
			SUM(cantidad) as salidas
		FROM almacen_salidas
		WHERE id_empresa=$id_empresa AND salio=1 AND fechamontaje > '$inicioMes' AND fechamontaje <= '$today'
		GROUP BY id_articulo;";
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			$item[$d["id_articulo"]]["salidas"]=$d["salidas"];
		}
		//4.- sumar todo por articulo y actualizar el inventario
		foreach($item as $art => $data){
			//si no perece
			$totalInv=0;
			$totalInv=$data["almacen"]+$data["entradas"]-$data["salidas"];
			$id_item=$data["id_item"];
			$bd->query("UPDATE almacen_inventario SET cantidad=$totalInv WHERE id_item=$id_item;");
		}
	}catch(PDOException $err){
		echo $option=$err->getMessage();
	}
}
?>