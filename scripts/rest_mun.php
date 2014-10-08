<?php
include("datos.php");
$m=$_GET["m"];
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="SELECT
		tags.folio,
		(SELECT nombre FROM restaurantes WHERE id=tags.folio) as nombre,
		(SELECT CONCAT('img/',tags.folio,path) FROM logos_rest WHERE folio=tags.folio) as logo,
		abierto
	FROM colonias
	INNER JOIN tags ON tags.coloniasId=colonias.id
	INNER JOIN config_rest ON tags.folio=config_rest.folio
	WHERE municipio='$m'
	GROUP BY tags.folio;";
	$res=$bd->query($sql);
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $row){
		
	}
}catch(PDOException $err){
}

?>