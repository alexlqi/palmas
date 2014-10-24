<?php
$dbnames=array(
	"local"=>"eventos",
	"eventos"=>"entropyd_eventos",
	"palmas"=>"entropyd_palmas",
	"gumpy"=>"entropyd_gumpy",
	"prorcesa"=>"entropyd_procesa",
	"admone"=>"entropyd_admone"
);

$a=explode(".",$_SERVER["HTTP_HOST"]);
$dbn=$dbnames[$a[0]];

$bbdd=array(
	"enthalpy"=>array(
		"dsnw"=>"mysql:host=localhost; dbname=$dbn; charset=utf8;",
		"userw"=>"entropyd_writer",
		"passw"=>"writer1"
	),
	"leadon"=>array(
		"dsnw"=>"mysql:host=localhost; dbname=leadonco_eventos; charset=utf8;"
	),
	"eventos"=>array(
		"dsnw"=>"mysql:host=localhost; dbname=eventos; charset=utf8;",
		"userw"=>"americanetw",
		"passw"=>"writer1"
	),
	"palmas"=>array(
		"dsnw"=>"mysql:host=localhost; dbname=entropyd_palmas; charset=utf8;",
		"userw"=>"americanetw",
		"passw"=>"writer1"
	),
);

$dsnw=$bbdd[$a[1]]["dsnw"];
$userw=$bbdd[$a[1]]["userw"];
$passw=$bbdd[$a[1]]["passw"];
$optPDO=array(PDO::ATTR_EMULATE_PREPARES=>false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION);

//datos de servidor
@define("HOST",$_SERVER['HTTP_HOST']);
@define("LIGA","http://".$_SERVER['HTTP_HOST']."/");
?>