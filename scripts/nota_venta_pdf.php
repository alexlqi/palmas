<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

//funciones para usarse dentro de los pdfs
function mmtopx($d){
	$fc=96/25.4;
	$n=$d*$fc;
	return $n."px";
}
function pxtomm($d){
	$fc=96/25.4;
	$n=$d/$fc;
	return $n."mm";
}
function checkmark(){
	$url="http://".$_SERVER["HTTP_HOST"]."/img/checkmark.png";
	$s='<img src="'.$url.'" style="height:10px;" />';
	return $s;
}
function folio($digitos,$folio){
	$usado=strlen($folio);
	$salida="";
	for($i=0;$i<($digitos-$usado);$i++){
		$salida.="0";
	}
	$salida.=$folio;
	return $salida;
}

//tamaño carta alto:279.4 ancho:215.9
$heightCarta=550;
$widthCarta=400;
$celdas=12;
$widthCell=$widthCarta/$celdas;

$mmCartaH=pxtomm($heightCarta);
$mmCartaW=pxtomm($widthCarta);

//sacar los datos del cliente
$error="";
if(isset($_GET["id_evento"])){
	$eve=$_GET["id_evento"];
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		// para saber los datos del cliente
		$sql="SELECT 
			t1.id_evento,
			t1.fechaevento,
			t1.id_cliente,
			t2.nombre,
			t3.direccion,
			t3.colonia,
			t3.ciudad,
			t3.estado,
			t3.cp,
			t3.telefono
		FROM eventos t1
		LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
		LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
		WHERE id_evento=$eve;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$evento=$res[0];
		$cliente=$evento["nombre"];
		$telCliente=$evento["telefono"];
		$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
		$fechaEve=$evento["fechaevento"];
		
		//para saber los articulos y paquetes
		$sql="SELECT
			t1.*,
			t2.nombre
		FROM eventos_articulos t1
		LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
		WHERE t1.id_evento=$eve;";
		$res=$bd->query($sql);
		$articulos=array();
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			if($d["id_articulo"]!=""){
				$art=$d["id_item"];
				unset($d["id_item"]);
				$articulos[$art]=$d;
			}else{
				$art=$d["id_item"];
				unset($d["id_item"]);
				$articulos[$art]=$d;
				$paq=$d["id_paquete"];
				
				//nombre del paquete
				$sql="SELECT nombre FROM paquetes WHERE id_paquete=$paq;";
				$res3=$bd->query($sql);
				$res3=$res3->fetchAll(PDO::FETCH_ASSOC);
				$articulos[$art]["nombre"]="PAQ. ".$res3[0]["nombre"];
				
				$sql="SELECT 
					t1.cantidad,
					t2.nombre
				FROM paquetes_articulos t1
				INNER JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
				WHERE id_paquete=$paq AND t2.perece=0;";
				$res2=$bd->query($sql);
				
				foreach($res2->fetchAll(PDO::FETCH_ASSOC) as $dd){
					$dd["precio"]="";
					$dd["total"]="";
					$dd["nombre"]=$dd["cantidad"]." ".$dd["nombre"];
					$dd["cantidad"]="";
					$articulos[]=$dd;
				}
			}
		}
		//para saber el anticipo
		$emp_eve=$emp."_".$eve;
		$sql="SELECT cantidad FROM eventos_pagos WHERE id_evento='$emp_eve' AND plazo='anticipo';";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$anticipo=$res[0]["cantidad"];
	}catch(PDOException $err){
		$error= $err->getMessage();
	}
}
ob_start();
?>
<?php if($error==""){ ?>
<style>
span{
	display:inline-block;
	padding:10px;
}
h1{
	font-size:20px;
}
.spacer{
	display:inline-block;
	height:1px;
}
td{
	background-color:#FFF;
}
th{
	color:#FFF;
	text-align:center;
}
</style>
<table style="width:100%;border-bottom:<?php echo pxtomm(2); ?> solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
		 <td valign="top" style="width:25%; text-align:left;"><img src="../img/laspalmas/logo.jpg" style="width:100%;" /></td>
         <td valign="top" style="width:50%; text-align:left;">
         	<p style="width:100%; padding:4px; font-size:8px; margin:0; font-size:10px; text-align:justify;">RENTA DE SILLAS, MESAS, TABLONES, BANQUETES Y TODO PARA SUS FIESTAS</p>
            <p style="width:100%; padding:4px; font-size:8px; margin:0; font-size:10px; text-align:center;">Araceli Crisanto Arteaga<br />R.F.C. : CIIA 651204 J37</p>
         </td>
         <td valign="top" style="width:25%; text-align:left;">
         	<div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px;">PEDIDO No</div>
            <div style="width:100%; color:#C00; text-align:center;"><?php echo folio(5,$eve); ?></div>
         </td>
    </tr>
</table>

<p style="width:100%; text-align:center; margin:5px auto; font-size:8px;">CARRANZA No 702 COL. CENTRO TEL. 212-52-31 CEL. 921 123 0765 COATZACOALCOS, VER</p>
<table style="width:100%; margin-top:5px;">
<tr><td valign="top" style="width:75%;">
    <table cellpadding="0" cellspacing="0" style=" font-size:10px;width:100%; padding:10px; padding-top:5px; padding-bottom:5px; border::1px solid #000; border-radius:6px;">
        <tr>
            <td style="width:20%;">Nombre:</td>
            <td style="width:80%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><?php echo $cliente; ?></div></td>
        </tr><tr>
            <td style="width:20%;">Dirección:</td>
            <td style="width:80%;"><div style="margin-left:5px; border-bottom:1px solid #000; font-size:8px;"><?php echo $domicilio; ?></div></td>
        </tr><tr>
            <td style="width:20%;">Teléfono:</td>
            <td style="width:80%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><?php echo $telCliente; ?></div></td>
        </tr>
    </table>
</td>
<td valign="top" style="width:25%;">
	<table cellpadding="0" cellspacing="0.8" style="background-color:#000;text-align:center;font-size:10px;width:100%;">
        <tr>
            <td style="width:30%;padding:5px;background-color:#E1E1E1;">Día</td>
            <td style="width:30%;padding:5px;background-color:#E1E1E1;">Mes</td>
            <td style="width:40%;padding:5px;background-color:#E1E1E1;">Año</td>
        </tr>
        <tr>
            <td style="padding:5px;"><?php echo date("d",strtotime($fechaEve)); ?></td>
            <td style="padding:5px;"><?php echo date("m",strtotime($fechaEve)); ?></td>
            <td style="padding:5px;"><?php echo date("Y",strtotime($fechaEve)); ?></td>
        </tr>
    </table>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" style=" font-size:11px;width:100%; margin-top:5px;">
	<tr>
    	<td style="width:27%; font-size:10px;">Fecha de entrega:</td>
        <td style="width:73%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><input style="width:100%; border:0;" type="text" value="<?php echo varFechaExtensa(date("Y-m-d H:i:s"))." a ".date("h:i a",strtotime(date("Y-m-d H:i:s"))+7200); ?>" /></div></td>
    </tr><tr>
        <td style="width:27%; font-size:10px;">Fecha para recoger:</td>
        <td style="width:73%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><input style="width:100%; border:0;" type="text" value="<?php echo varFechaExtensa(date("Y-m-d H:i:s"))." a ".date("h:i a",strtotime(date("Y-m-d H:i:s"))+7200); ?>" /></div></td>
    </tr>
</table>
<table border="0" cellspacing="0.8" style="width:100%;background-color:#000;font-size:10px;margin-top:5px;">
	<tr>
    	<th style="width:15%;">CANT.</th>
        <th style="width:55%;">CONCEPTO</th>
        <th style="width:15%;">P.U.</th>
        <th style="width:15%;">IMPORTE</th>
    </tr>
<?php 
	$total=0;
	foreach($articulos as $id=>$d){ 
	$total+=$d["total"];
?>
    <tr>
        <td style="width:15%;text-align:center;"><?php echo $d["cantidad"] ?></td>
        <td style="width:55%;"><?php echo $d["nombre"] ?></td>
        <td style="width:15%;text-align:center;"><?php echo $d["precio"] ?></td>
        <td style="width:15%;text-align:center;"><?php echo $d["total"] ?></td>
    </tr>
<?php } ?>
	<tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:55%;"> </td>
        <td style="width:15%;text-align:right;">Total:</td>
        <td style="width:15%;text-align:center;"><?php echo $total; ?></td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:55%;"> </td>
        <td style="width:15%;text-align:right;">Anticipo:</td>
        <td style="width:15%;text-align:center;"><?php echo $anticipo; ?></td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:55%;"> </td>
        <td style="width:15%;text-align:right;">Restante:</td>
        <td style="width:15%;text-align:center;"><?php echo $total-$anticipo; ?></td>
    </tr>
</table>
<div>Observaciones:</div>
<textarea cols="50" style="font-size:10px;"></textarea>
<p style="font-size:10px;">NOTA: El cliente <u><?php echo $cliente; ?></u> se hace responsable por cualquier daño o maltrato en el equipo o material rentado, pagando el costo del mismo. La renta es hasta por 12 horas, El acomodo es por parte del cliente.</p>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
	<tr>
    	<td style="width:100%;vertical-align:top; text-align:center;">
        	ATENTAMENTE<br />C. P. Salomón Bahena Salinas<br />Gerente
        </td>
    </tr>
</table>
<?php }else{
	echo $error;
}?>
<?php
$html=ob_get_clean();
$path='../docs/';
$filename="generador.pdf";
//$filename=$_POST["nombre"].".pdf";

//configurar la pagina
//$orientar=$_POST["orientar"];
$orientar="portrait";

$topdf=new HTML2PDF($orientar,array($mmCartaW,$mmCartaH),'es');
$topdf->writeHTML($html);
$topdf->Output();
//$path.$filename,'F'

//echo "http://".$_SERVER['HTTP_HOST']."/docs/".$filename;

?>