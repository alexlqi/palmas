<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
$term=$_GET["term"];
include("datos.php");

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico	
	$sqlArt="SELECT 
		CONCAT(articulos.nombre,' (',areas.nombre,')') as label,
		areas.nombre as area,
		familias.nombre as familia,
		subfamilias.nombre as subfamilia,
		articulos.id_articulo,
		precio1 as p1,
		precio2 as p2,
		precio3 as p3,
		precio4 as p4,
		areas.nombre,
		familias.nombre,
		subfamilias.nombre
	FROM articulos
	INNER JOIN listado_precios ON articulos.id_articulo=listado_precios.id_articulo
	LEFT JOIN areas ON articulos.area= areas.id_area
	LEFT JOIN familias ON articulos.familia= familias.id_familia
	LEFT JOIN subfamilias ON articulos.subfamilia= subfamilias.id_subfamilia
	WHERE articulos.id_empresa=$empresaid AND articulos.nombre LIKE '%$term%';";
	
	$sqlPaq="SELECT 
		paquetes.nombre as label,
		paquetes.id_paquete,
		precio1 as p1,
		precio2 as p2,
		precio3 as p3,
		precio4 as p4
	FROM paquetes
	INNER JOIN listado_precios ON paquetes.id_paquete=listado_precios.id_paquete
	WHERE paquetes.id_empresa=$empresaid AND paquetes.nombre LIKE '%$term%';";
	
	$i=0;
	$res=$bd->query($sqlArt);
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$precios='<select class="precios" onchange="darprecio(this);" style="margin-right:3px;">
			<option disabled="disabled" selected="selected">-----</option>
			<option value="'.$v["p1"].'">$'.$v["p1"].'</option>
			<option value="'.$v["p2"].'">$'.$v["p2"].'</option>
			<option value="'.$v["p3"].'">$'.$v["p3"].'</option>
			<option value="'.$v["p4"].'">$'.$v["p4"].'</option>
		</select><input type="text" class="precio numerico" />';
		$precios.='<script>(function(){$(".precio").numeric();}());';
		$precios.='$(".cantidad, .precio").keyup(function(e){
			//para el <th>
			row=$(this).parent().parent();
			cantidad=row.find(".cantidad").val()*1;
			precio=row.find(".precio").val()*1;
			total=cantidad*precio;
			//escribe el total
			row.find(".total").html(total);
		});</script>';
		unset($v["p1"],$v["p2"],$v["p3"],$v["p4"]);
		$v["precio"]=$precios;
		$r[$i]=$v;
		$i++;
	}
	
	$res=$bd->query($sqlPaq);
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$precios='<select class="precios" onchange="darprecio(this);" style="margin-right:3px;">
			<option disabled="disabled" selected="selected">-----</option>
			<option value="'.$v["p1"].'">$'.$v["p1"].'</option>
			<option value="'.$v["p2"].'">$'.$v["p2"].'</option>
			<option value="'.$v["p3"].'">$'.$v["p3"].'</option>
			<option value="'.$v["p4"].'">$'.$v["p4"].'</option>
		</select><input type="text" class="precio numerico" />';
		$precios.='<script>(function(){$(".precio").numeric();}());';
		$precios.='$(".cantidad, .precio").keyup(function(e){
			//para el <th>
			row=$(this).parent().parent();
			cantidad=row.find(".cantidad").val()*1;
			precio=row.find(".precio").val()*1;
			total=cantidad*precio;
			//escribe el total
			row.find(".total").html(total);
		});</script>';
		unset($v["p1"],$v["p2"],$v["p3"],$v["p4"]);
		$v["precio"]=$precios;
		$r[$i]=$v;
		$i++;
	}
	
}catch(PDOException $err){
	echo $err->getMessage();
}

echo json_encode($r);
?>