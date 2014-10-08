<?php session_start();
include("datos.php");

$idCot=$_GET["id_cotizacion"];
$id_empresa=$_SESSION["id_empresa"];
$id_usuario=$_SESSION["id_usuario"];

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$elementos="";
	
	//para saber los artículos---------------------->
	$sqlArt="SELECT
		cotizaciones_articulos.id_item,
		listado_precios.id_articulo,
		articulos.nombre,
		cantidad,
		precio,
		precio1 as p1,
		precio2 as p2,
		precio3 as p3,
		precio4 as p4,
		cantidad*precio as total
	FROM cotizaciones_articulos
	INNER JOIN articulos ON cotizaciones_articulos.id_articulo=articulos.id_articulo
	INNER JOIN listado_precios ON cotizaciones_articulos.id_articulo=listado_precios.id_articulo
	WHERE articulos.id_empresa=$id_empresa AND id_cotizacion=$idCot AND cotizaciones_articulos.id_articulo IS NOT NULL;";	
	$res=$bd->query($sqlArt);
	
	//es el id para llenar los elementos en el log de los items de la cotización
	$id=1;
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		//hacer el select para los precios
		$precios='<select class="precios" onchange="darprecio(this);" style="margin-right:3px;">
			<option selected="selected" value="'.$v["precio"].'">$'.$v["precio"].'</option>
			<option disabled="disabled">------</option>
			<option value="'.$v["p1"].'">$'.$v["p1"].'</option>
			<option value="'.$v["p2"].'">$'.$v["p2"].'</option>
			<option value="'.$v["p3"].'">$'.$v["p3"].'</option>
			<option value="'.$v["p4"].'">$'.$v["p4"].'</option>
		</select>';
		
		//$id es el id de cotizacion proveniente de la busqueda
		$elementos.='
		<tr id="'.$id.'" class="lista_articulos verde_ok">
			<td style="background-color:#FFF;"><input type="hidden" class="id_item" value="'.$v["id_item"].'" /><input type="hidden" class="id_cotizacion" value="'.$idCot.'" /><input type="hidden" class="id_articulo" value="'.$v["id_articulo"].'" /><input type="hidden" class="id_paquete" value="" /></td>
			<td><input class="cantidad" type="text" size="7" onkeyup="cambiar_cant('.$id.');" value="'.$v["cantidad"].'" /></td>
			<td><input class="articulo_nombre text_full_width" onkeyup="art_autocompletar('.$id.');" value="'.$v["nombre"].'" /></td>
			<td>'.$precios.'<span class="precio" >'.$v["precio"].'</span></td>
			<td>$<span class="total">'.$v["total"].'</span></td>
			<td><span class="guardar_articulo" onclick="guardar_art('.$id.')"></span><span class="eliminar_articulo" onclick="eliminar_art('.$id.')"></span></td>
		</tr>';
		$id++;
	}
	
	//para saber los paquetes-------------------->
	$sqlPaq="SELECT
		cotizaciones_articulos.id_item,
		listado_precios.id_paquete,
		cantidad,
		nombre,
		precio,
		precio1 as p1,
		precio2 as p2,
		precio3 as p3,
		precio4 as p4,
		cantidad*precio as total
	FROM cotizaciones_articulos
	INNER JOIN paquetes ON cotizaciones_articulos.id_paquete=paquetes.id_paquete
	INNER JOIN listado_precios ON cotizaciones_articulos.id_paquete=listado_precios.id_paquete
	WHERE paquetes.id_empresa=$id_empresa AND id_cotizacion=$idCot AND cotizaciones_articulos.id_paquete IS NOT NULL;";	
	$res=$bd->query($sqlPaq);
	
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$precios='<select class="precios" onchange="darprecio(this);" style="margin-right:3px;">
			<option selected="selected" value="'.$v["precio"].'">$'.$v["precio"].'</option>
			<option disabled="disabled">------</option>
			<option value="'.$v["p1"].'">$'.$v["p1"].'</option>
			<option value="'.$v["p2"].'">$'.$v["p2"].'</option>
			<option value="'.$v["p3"].'">$'.$v["p3"].'</option>
			<option value="'.$v["p4"].'">$'.$v["p4"].'</option>
		</select>';
		//$id es el id de cotizacion proveniente de la busqueda
		$elementos.='
		<tr id="'.$id.'" class="lista_articulos verde_ok">
			<td style="background-color:#FFF;"><input type="hidden" class="id_item" value="'.$v["id_item"].'" /><input type="hidden" class="id_cotizacion" value="'.$idCot.'" /><input type="hidden" class="id_articulo" value="" /><input type="hidden" class="id_paquete" value="'.$v["id_paquete"].'" /></td>
			<td><input class="cantidad" type="text" size="7" onkeyup="cambiar_cant('.$id.');" value="'.$v["cantidad"].'" /></td>
			<td><input class="articulo_nombre text_full_width" onkeyup="art_autocompletar('.$id.');" value="'.$v["nombre"].'" /></td>
			<td>'.$precios.'<span class="precio" >'.$v["precio"].'</span></td>
			<td>$<span class="total">'.$v["total"].'</span></td>
			<td><span class="guardar_articulo" onclick="guardar_art('.$id.')"></span><span class="eliminar_articulo" onclick="eliminar_art('.$id.')"></span></td>
		</tr>';
		$id++;
	}
	
	//escribe los elementos de tabla correspondientes
	echo $elementos;
}catch(PDOException $err){
	echo $err->getMessage();
}
?>