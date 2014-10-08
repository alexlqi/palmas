<?php //requiere de haber iniciado sesión previamente

function ordenCompra($eve){
	include("datos.php");
	$emp=$_SESSION["id_empresa"];
	
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		//saber fechas de montaje y desmontaje
		$sql="SELECT
			fechamontaje,
			fechadesmont
		FROM eventos
		WHERE id_empresa=$emp AND id_evento=$eve;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		
		$montajei=$res[0]["fechamontaje"];
			$nuevafecha = new DateTime($montajei);
			$nuevafecha->add(new DateInterval('P1D'));
		$montajef=$nuevafecha->format('Y-m-d H:i:s');
			$nuevafecha=NULL;
		
		$desmonti=$res[0]["fechadesmont"];
			$nuevafecha = new DateTime($montajei);
			$nuevafecha->add(new DateInterval('P1D'));
		$desmontf=$nuevafecha->format('Y-m-d H:i:s');
			$nuevafecha=NULL;
		
		//saber las salidas del montaje
		$sql="SELECT
			id_articulo,
			SUM(cantidad) as uso,
			fechamontaje
		FROM almacen_salidas
		WHERE id_empresa=$emp AND fechamontaje BETWEEN '$montajei' AND '$montajef'
		GROUP BY id_articulo;";
		$res=$bd->query($sql);
		
		$salidas=array();
		foreach($res->fetchAll() as $d){
			$salidas[$d["id_articulo"]]=$d["uso"];
		}
		
		//saber las entradas del desmontaje
		$sql="SELECT
			id_articulo,
			SUM(cantidad) as uso,
			fechadesmont
		FROM almacen_entradas
		WHERE id_empresa=$emp AND fechadesmont BETWEEN '$desmonti' AND '$desmontf'
		GROUP BY id_articulo;";
		$res=$bd->query($sql);
		
		$entradas=array();
		foreach($res->fetchAll() as $d){
			$entradas[$d["id_articulo"]]=$d["uso"];
		}
		
		//saber los totales del almacen
		$sql="SELECT
			id_articulo,
			SUM(cantidad) as existencia
		FROM almacen
		WHERE id_empresa=$emp
		GROUP BY id_articulo;";
		$res=$bd->query($sql);
		
		$almacen=array();
		foreach($res->fetchAll() as $d){
			$almacen[$d["id_articulo"]]=$d["existencia"];
		}
		
		//se concentra todo lo buscado en resumen
		$resumen=array();
		foreach($almacen as $art=>$d){
			$resumen[$art]=$d;
			//Se le resta si hay salidas en el período de búqueda
			if(isset($salidas[$art])){
				$resumen[$art]-=$salidas[$art];
			}
			//Se le suma si hay regresos en el período de búsqueda
			if(isset($entradas[$art])){
				$resumen[$art]+=$entradas[$art];
			}
		}
		
		//se crean ordenes de compra por cada elemento negativo en $resumen
		//ya se sabe el id del evento, crear un control boolean para saber si se va a hacer una orden o no
		$crear=false;
		$articulos=array();
		foreach($resumen as $art=>$cant){
			if($cant<0){ //si la cantidad es menor a 0 entonces hay que pedir ese artículo
				$crear=true;
				$articulos[$art]=abs($cant);
			}
		}
		
		if($crear){
			//extrae el nuevo folio
			$sql="SELECT (MAX(folio) + 1) as folio FROM compras WHERE id_empresa=$emp;";
			$res=$bd->query($sql);
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
			$folio=$res[0]["folio"];
			
			//inserta la orden de compra en la base de datos
			$sql="INSERT INTO compras (id_empresa,id_evento,folio,fecha) VALUES ($emp,$eve,$folio,'$montajei');";
			$bd->query($sql);
			$id_compra=$bd->lastInsertId();
			
			//añade los articulos a la orden de compra
			foreach($articulos as $art=>$cant){
				$sql="INSERT INTO compras_articulos (id_compra,id_empresa,id_articulo,cantidad) VALUES ($id_compra,$emp,$art,$cant);";
				$bd->query($sql);
			}
		}
		
		return "listo";
	}catch(PDOException $err){
		return $err->getMessage();
	}
}
?>