<?php
//Funciones para mostrear configuraciones y menus de restaurantes
function nombre($data){
	echo $data["nombre"];
}
function minima($data){
	echo $data["minima"];
}
function abierto($data){
	$data=$data["abierto"];
	switch($data){
		case 0:
			echo "CERRADO";
		break;
		case 1:
			echo "SIN SERVICIO";
		break;
		case 2:
			echo "ABIERTO";
		break;
		default:
			echo "CERRADO";
		break;
	}
}
function formapago($data){
	$fp=explode("/",$data["formapago"]);
	$r=array("tdcrep"=>"","tdcel"=>"","tdd"=>"","paso"=>"","domicilio"=>"","programar"=>"");
	foreach($fp as $i=>$v){
		switch($v){
			case 0:
				$fp[$i]='src="img/cross.png" data-uso="false"';
			break;
			case 1:
				$fp[$i]='src="img/check.png" data-uso="true"';
			break;
		}
	}
	$r["tdcrep"]=$fp[0]; $r["tdcel"]=$fp[1]; $r["tdd"]=$fp[2]; $r["paso"]=$fp[3]; $r["domicilio"]=$fp[4]; $r["programar"]=$fp[5];
	return $r;
}
function logo($data){
	echo "img/".$data["logo"] ;
}
function direccion($data){
	echo $data["direccion"];
}
function comida($data){
	echo $data["comida"];
}
function aprox($data){
	echo $data["aprox"];
}
function costo_envio($data){
	echo $data["costo_envio"];
}
?>