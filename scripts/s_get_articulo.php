<?php session_start();
include("datos.php");
include("pivotes.php");
$tabla=$_POST["zona"];
$id=$_POST["id"];
$pivote=$pivotes[$tabla];
try{
	$sql="SELECT
		*,
		articulos.nombre as articulo
	FROM $tabla 
	INNER JOIN articulos ON $tabla.$pivote = articulos.".str_replace("id_","",$pivote)."
	WHERE $pivote=$id;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$res=$bd->query($sql);
	$option="<option value=''>Elige artículo</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$option.='<option value="'.$v["id_articulo"].'">'.$v["articulo"].'</option>';
	}
	echo $option;
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>