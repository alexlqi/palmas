<?php session_start(); 
include("datos.php");

if (isset($_POST["compra"])){
$id_compra=$_POST["compra"];
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		$compras=array();
		
		$sql="SELECT
			t2.id_evento,
			t2.nombre as evento,
			folio,
			t1.fecha,
			t1.estatus
		FROM compras t1
		INNER JOIN eventos t2 ON t1.id_evento=t2.id_evento
		WHERE t1.id_compra=$id_compra;";
		$res=$bd->query($sql);
		
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$compras[$id_compra]=$v;
		}
		
		$sql="SELECT
			t1.id_item,
			t1.cantidad,
			t2.nombre,
			t2.unidades,
			t3.monto
		FROM compras_articulos t1
		LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
		LEFT JOIN compras_pagos t3 ON t1.id_compra=t3.id_compra
		WHERE t1.id_compra=$id_compra
		GROUP BY t1.id_item;;";
		$res=$bd->query($sql);
		
		$total=0;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$item=$v["id_item"];
			unset($v["id_item"]);
			$compras[$id_compra]["items"][$item]=$v;
			$total+=$v["monto"];
		}
		
		$compras[$id_compra]["total"]=$total;
	}catch(PDOException $err){
		echo $err->getMessage();
	}
}

$col1="";
$col2="";
$col3="";
$col4="";
$col5="";
foreach($compras[$id_compra]["items"] as $i){
	$col1.='<div class="dato">'.$i["nombre"]."</div>";
	$col2.='<div class="dato">'.$i["unidades"]."</div>";
	$col3.='<div class="dato">'.$i["cantidad"]."</div>";
	$col4.='<div class="dato">'.$i["monto"]."</div>";
	$col5.='<div class="dato">'."</div>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo str_replace(" ","_","Compra ".$id_compra); ?></title>
<style media="all">
*{
	font-family:Arial, Helvetica, sans-serif;
	margin:0;
	padding:0;
}
table{
	width:100%;
	padding:0;
}
table *{
	margin:0.5px;
}
td{
	background-color:#FFF;
	padding:2px 4px;
}
.valign td{
	vertical-align:top;
}
.redondeado{
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
}
.borde{
	border:1px solid #000;
}
.dato{
	margin-bottom:5px;
}
.titulo{
	font-size:24px;
	text-align:center;
	font-weight:bold;
}
.m_top{
	margin-top:10px;
}
.fuente_grande{
	font-size:24px;
}
.fuente_mediana{
	font-size:20px;
}
.fuente_chica{
	font-size:14px;
}
.negrita{
	font-weight:bold;
}
.fondo_gris{
	background-color:rgb(225,225,225);
}
</style>
</head>

<body>
<table border="0">
	<tr>
    	<td style="width:25%;"><img src="../<?php echo $_SESSION["logo"]; ?>" width="100%" /></td>
        <td style="width:50%;" class="titulo">Orden de compra</td>
        <td style="width:25%;">
        	<table style="background-color:#000;">
            	<tr>
                	<td align="center" class="fondo_gris fuente_chica negrita">Folio</td>
                </tr>
                <tr>
                	<td align="center" class="fuente_chica"><?php echo $id_compra; ?></td>
                </tr>
                <tr>
                	<td align="center" class="fondo_gris fuente_chica negrita">Fecha</td>
                </tr>
                <tr>
                	<td align="center" class="fuente_chica"><?php echo $compras[$id_compra]["fecha"] ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="m_top">
    <div>
        <span>Nombre del evento: <u style="padding:3px;"><?php echo $compras[$id_compra]["evento"]; ?></u></span>
    </div>
    <div>
        <span>Nombre del evento: <u style="padding:3px;"><?php echo $compras[$id_compra]["evento"]; ?></u></span>
    </div>
</div>
<div class="m_top">
    <div>
        <span>Nombre del evento: <u style="padding:3px;"><?php echo $compras[$id_compra]["evento"]; ?></u></span>
    </div>
    <div>
        <span>Nombre del evento: <u style="padding:3px;"><?php echo $compras[$id_compra]["evento"]; ?></u></span>
    </div>
</div>
<table class="m_top valign" style="background-color:#000;" align="center">
	<tr>
    	<th class="fondo_gris">Concepto</th>
        <th class="fondo_gris">Unid.</th>
        <th class="fondo_gris">Cantidad</th>
        <th class="fondo_gris">Monto</th>
    </tr>
    <tr style="height:200px;">
    	<td style="width:50%"><?php echo $col1; ?></td>
        <td align="center" style="width:16.5%"><?php echo $col2; ?></td>
        <td align="center" style="width:16.5%"><?php echo $col3; ?></td>
        <td align="center" style="width:16.5%"><?php echo $col4; ?></td>
    </tr>
</table>
<script>
print();
close();
</script>
</body>
</html>