<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

if(isset($_GET["cot"])){
	$id=$_GET["cot"];
}

//funciones para convertir px->mm
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
$heightCarta=960;
$widthCarta=660;
$celdas=12;
$widthCell=$widthCarta/$celdas;
$mmCartaH=pxtomm($heightCarta);
$mmCartaW=pxtomm($widthCarta);
ob_start();

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	// para saber los datos del cliente
	$sql="SELECT 
		t1.id_cotizacion,
		t1.fecha,
		t1.fechaevento,
		t1.fechamontaje,
		t1.fechadesmont,
		t1.id_cliente,
		t2.nombre,
		t3.direccion,
		t3.colonia,
		t3.ciudad,
		t3.estado,
		t3.cp,
		t3.telefono
	FROM cotizaciones t1
	LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
	LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
	WHERE id_cotizacion=$id;";
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$evento=$res[0];
	$cliente=$evento["nombre"];
	$telCliente=$evento["telefono"];
	$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
	$fecha=$evento["fecha"];
	$fechaEve=$evento["fechaevento"];
}catch(PDOException $err){
	echo $err->getMessage();
}
$bd=NULL;

//para saber los articulos y paquetes
try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		t1.*,
		t2.nombre
	FROM cotizaciones_articulos t1
	LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
	WHERE t1.id_cotizacion=$id;";
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
}catch(PDOException $err){
	echo $err->getMessage();
}

//var_dump($articulos);
?>
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
</style>
<table style="width:100%;border-bottom:<?php echo pxtomm(2); ?> solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
		 <td style="width:55%; text-align:left"><img src="../<?php echo $_SESSION["logo"]; ?>" style="width:200px;" /></td>
         <td style="width:45%; text-align:left; padding-bottom:5px;">
         	<div style="width:100%; text-align:right;font-size:18px;">FOLIO N&ordm; <?php echo folio(4,$id); ?></div>
            <p style="margin:0;text-align:justify;font-size:16px;">Banquetes, coffee break, bocadillos, renta de sillas, brincolines, mesas, carpas, medio servicio y todo para su fiesta</p>
         </td>
    </tr>
</table>

<p style="width:100%; text-align:center; margin:5px auto; font-size:12px;">Oficina en Carranza 702, Col. Centro Salón: Calzada de Quetzalcoatl 107, Col Transportista, Salón Majesty, Juán Escuti 2124, Col. 20 de Noviembre. Tel. 163 26 57, 21 252 31 Cel. 044 921 123 0765</p>

<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
	<tr>
    	<td style="width:20%;">Fecha: <div style="margin-left:5px;width:50%; border-bottom:1px solid #000;"><?php echo varFechaAbr($fecha); ?></div></td>
        <td style="width:60%;">Atención:<div style="margin-left:5px;width:80%; border-bottom:1px solid #000;"><?php echo $cliente; ?></div></td>
        <td style="width:20%;">Tel:<div style="margin-left:5px;width:70%; border-bottom:1px solid #000;"><?php echo $telCliente; ?></div></td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
	<tr>
    	<td style="width:35%;">Fecha del Evento: <div style="margin-left:5px;width:50%; border-bottom:1px solid #000;"><?php echo varFechaAbr($fechaEve);; ?></div></td>
        <td style="width:65%;">Lugar:<div style="margin-left:5px;width:87%; border-bottom:1px solid #000;"><?php echo $domicilio; ?></div></td>
    </tr>
</table>
<br>
<table border="1" cellspacing="-0.5" cellpadding="1" style="width:100%;font-size:10px;margin-top:5px;">
	<tr align="center">
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
</table>

<div style="width:90%; padding:0 20px 20px 20px;"><strong>Precios más IVA, en caso de requerir factura</strong></div>
<div style="width:90%; padding:0 20px; font-size:12px;">El pago sería, 50% al contratar, el resto liquidarlo en 4 días antes del evento</div>
<div style="width:90%; padding:0 20px; font-size:12px;">Anezamos menú para su elección. Culquier duda que tenga favor de comunicarse con nosotros</div>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
	<tr>
    	<td style="width:100%;vertical-align:top; text-align:center;">
        	ATENTAMENTE<br />C. P. Salomón Bahena Salinas<br />Gerente
        </td>
    </tr>
</table>
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