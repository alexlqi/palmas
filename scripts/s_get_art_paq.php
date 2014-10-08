<?php session_start();
include("datos.php");
if(isset($_POST["paq"])){
	$paq=$_POST["paq"];
	
	try{
		$sql="SELECT 
			articulos.id_articulo,
			articulos.nombre,
			articulos.unidades,
			paquetes_articulos.cantidad,
			areas.nombre as area,
			familias.nombre as familia,
			subfamilias.nombre as subfamilia
		FROM paquetes_articulos
		INNER JOIN articulos ON articulos.id_articulo=paquetes_articulos.id_articulo
		INNER JOIN areas ON articulos.area=areas.id_area
		INNER JOIN familias ON articulos.familia=familias.id_familia
		INNER JOIN subfamilias ON articulos.subfamilia=subfamilias.id_subfamilia
		WHERE paquetes_articulos.id_paquete=$paq;";
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		$arts="";
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$arts.="<tr>";
			$arts.='<td>'.$v["area"].'</td>';
			$arts.='<td>'.$v["familia"].'</td>';
			$arts.='<td>'.$v["subfamilia"].'</td>';
			$arts.='<td>'.$v["nombre"].'</td>';
			$arts.='<td>'.$v["unidades"].'</td>';
			$arts.='<td>'.$v["cantidad"].'</td>';
			$arts.='<td><input type="button" value="Quitar" onclick="eliminar(this);" data-paq="'.$paq.'" data-art="'.$v["id_articulo"].'" /></td>';
			$arts.="</tr>";
		}
		
		echo $arts;
	}catch(PDOException $err){
		echo "Error: ".$err->getMessage();
	}
}
?>