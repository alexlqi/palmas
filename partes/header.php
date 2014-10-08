<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");

include_once("scripts/datos.php");
if(isset($_SESSION["id_usuario"])){
	if($_SESSION["id_usuario"]==""){
		session_destroy();
		header('Location: '.LIGA);
	}
}else{
	header('Location: '.LIGA);
}

$empresaid=$_SESSION["id_empresa"];
$userid=$_SESSION["id_usuario"];
$categoria=$_SESSION["categoria"];

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sqlEmpresa="SELECT * FROM empresas WHERE id_empresa=$empresaid;";
	
	$res=$bd->query($sqlEmpresa);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	
	$empresa=$res[0]["nombre"];
	$logo=$res[0]["logo"];
	$_SESSION["logo"]=$logo;
	$logo2=$res[0]["logo2"];
	$hoy=date("Y-m-d H:i:s");
	
	//para lo de vuelta al almacén
	$sql="SELECT * FROM almacen_entradas WHERE id_empresa=$empresaid AND entro=0 AND fechadesmont < '$hoy' GROUP BY id_evento;";
	$res=$bd->query($sql);
	$vuelta=$res->rowCount();
	$vueltas=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$id=$v["id_entrada"];
		unset($v["id_entrada"]);
		$vueltas[$id]=$v;
	}
	
	//para lo de las ordenes de compra
	$sql="SELECT * FROM compras WHERE id_empresa=$empresaid AND estatus=1";
	$res=$bd->query($sql);
	$orden=$res->rowCount();
	$ordenes=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$id=$v["id_compra"];
		unset($v["id_compra"]);
		$ordenes[$id]=$v;
	}
	
}catch(PDOException $err){
	echo 'Error encontrado: '.$err->getMessage();
}
$bd=NULL;
?>
<!DOCTYPE html PUBspanC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lt IE 7]><html class="ie6"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if gt IE 8]><!--><html><!--<![endif]-->
<!--[if !IE]><!--><html xmlns="http://www.w3.org/1999/xhtml"><!--<![endif]-->

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" media="all" href="css/eventos.css" />
<link rel="stylesheet" media="all" href="css/custom-theme/jquery-ui-1.10.4.custom.min.css" />
<link rel="stylesheet" media="all" href="css/jquery-ui-timepicker-addon.min.css" />
<!--[if IE 7]>
<script type="text/javascript" src="../js/json3.min.js"></script>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE7.js"></script>
<![endif]-->
<script src="js/jquery-1.10.2.min.js"></script>
<script>$(function(){
	console.log(<?php echo json_encode($_SESSION); ?>);
});</script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.min.js"></script>
<script src="js/jquery.numeric.js"></script>
<script src="js/general.js"></script>
<title>Administración de eventos</title>
</head>

<body>
<div id="alerta_error" class="tabla" align="center" style="display:none;">
    <div class="celda" style="vertical-align:middle;">
        <div class="ui-widget" style="width:300px;">
            <div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
                <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                <strong>Error encontrado:</strong><br />
                <p style="max-width:300px; word-wrap:break-word;" align="left"></p>
            </div>
        </div>
    </div>
</div>
<div id="alerta_info" class="tabla" align="center" style="display:none;">
    <div class="celda" style="vertical-align:middle;">
        <div class="ui-widget" style="width:300px;">
            <div class="ui-state-highlight ui-corner-all" style="padding: 0.7em;">
                <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                <strong>Información del servidor</strong><br />
                <p style="max-width:300px; word-wrap:break-word;" align="left"></p>
            </div>
        </div>
    </div>
</div>
<div class="wrapper tabla">
 	<div id="topbar">
    	<div align="center">
          <div class="fondo_azul linea_abajo_azul">
            <div id="nav_bar">
              <span class="celda link" data-url="home.php">INICIO</span>
              <span class="celda link" data-url="cotizaciones.php">COTIZACIONES</span>
              <span class="celda link" data-url="eventos.php">EVENTOS</span>
              <span class="celda link" data-url="almacen.php">ALMACEN</span>
              <span class="celda link" data-url="compras.php">COMPRAS</span>
              <span class="celda link" data-url="bancos.php">BANCOS</span>
              <span class="celda link" data-url="modulos.php">MÓDULOS</span>
            </div>
          </div>
          <div class="fondo_gris linea_abajo_gris">
          	<div id="atajos" class="tabla">
              <div class="celda mitad" align="left">
                <div class="tabla" style="height:100%;">
                  <img id="logo" class="celda" src="<?php echo $logo; ?>" />
                <?php if($logo2!=""){ ?>
                  <img id="logo2" class="celda" src="<?php echo $logo2; ?>" width="50" />
                <?php } ?>
                  <div id="credencial" class="celda">
                    <img id="foto_user" class="flotado_izq" src="img/foto.png" />
                    <div class="dato_credencial">Bienvenido: <strong><?php echo $_SESSION["usuario"]; ?></strong></div>
                    <div class="dato_credencial">Ventas del mes: <span>0</span></div>
                    <div class="dato_credencial">Comisiones del mes: $<span>0</span></div>
                  </div>
                </div>
              </div>
              <div class="celda mitad" align="right">
                <div class="tabla">
                  <div id="a_vuelta_almacen" class="atajo celda">Vuelta al almacén<span class="fondo_rojo"><?php echo $vuelta; ?></span></div>
                  <div id="a_ordenes_compra" class="atajo celda">Órdenes de compra<span class="fondo_rojo"><?php echo $orden; ?></span></div>
                  <div id="a_pagos" class="atajo celda">Pagos<span class="fondo_verde">0</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="fila" style=""><!-- //div tag con clase fila para centrar el contenido -->