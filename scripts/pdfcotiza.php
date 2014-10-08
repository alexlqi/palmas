<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include("datos.php");
require_once('../clases/html2pdf.class.php');
$emp=$_SESSION["id_empresa"];

//funciones para convertir px->mm
function mmtopx($d){
	$fc=96/25.4;
	$n=$d*$fc;
	return $n;
}
function pxtomm($d){
	$fc=96/25.4;
	$n=$d/$fc;
	return $n;
}
function checkmark(){
	$url="http://".$_SERVER["HTTP_HOST"]."/img/checkmark.png";
	$s='<img src="'.$url.'" style="height:10px;" />';
	return $s;
}
//tamaño carta alto:279.4 ancho:215.9
$heightCarta=960;
$widthCarta=660;
$celdas=12;
$widthCell=$widthCarta/$celdas;

$mmCartaH=pxtomm($heightCarta);
$mmCartaW=pxtomm($widthCarta);

//se trabaja aquí la información de la cotización
try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}


//Empieza a obtener los resultados
ob_start();
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
<table style="width:100%;border-bottom:<?php echo pxtomm(2)."mm"; ?> solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
		 <td style="width:55%; text-align:left"><img src="../img/laspalmas/logo.jpg" height="100" /></td>
         <td style="width:45%; text-align:left; padding-bottom:5px;">
         	<div style="width:100%; text-align:right;font-size:18px;">FOLIO N&ordm; <?php echo '0767'; ?></div>
            <p style="margin:0;text-align:justify;font-size:16px;">Banquetes, coffee break, bocadillos, renta de sillas, brincolines, mesas, carpas, medio servicio y todo para su fiesta</p>
         </td>
    </tr>
</table>

<p style="width:100%; text-align:center; margin:5px auto; font-size:12px;">Oficina en Carranza 702, Col. Centro Salón: Calzada de Quetzalcoatl 107, Col Transportista, Salón Majesty, Juán Escuti 2124, Col. 20 de Noviembre. Tel. 163 26 57, 21 252 31 Cel. 044 921 123 0765</p>

<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
	<tr>
    	<td style="width:20%;">Fecha: <div style="margin-left:5px;width:50%; border-bottom:1px solid #000;"><?php echo 'fechaCot'; ?></div></td>
        <td style="width:60%;">Atención:<div style="margin-left:5px;width:80%; border-bottom:1px solid #000;"><?php echo 'cliente'; ?></div></td>
        <td style="width:20%;">Tel:<div style="margin-left:5px;width:70%; border-bottom:1px solid #000;"><?php echo 'telCliente'; ?></div></td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
	<tr>
    	<td style="width:35%;">Fecha del Evento: <div style="margin-left:5px;width:50%; border-bottom:1px solid #000;"><?php echo 'fechaEvento'; ?></div></td>
        <td style="width:65%;">Lugar:<div style="margin-left:5px;width:87%; border-bottom:1px solid #000;"><?php echo 'Lugar'; ?></div></td>
    </tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" style=" font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
	<tr>
    	<td style="width:15%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;"></td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;">Pierna</td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;">Pollo</td>
        <td style="width:65%;border-left:1px solid #000;border-top:1px solid #000;border-right:1px solid #000;text-align:center;">Observaciones</td>
    </tr>
    <tr>
    	<td style="width:15%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;">3 tiempos</td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;"><?php  ?></td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;"></td>
        <td style="width:65%;border-left:1px solid #000;border-top:1px solid #000;border-right:1px solid #000;text-align:center;"></td>
    </tr>
    <tr>
    	<td style="width:15%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;">2 tiempos</td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;"></td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;text-align:center;"></td>
        <td style="width:65%;border-left:1px solid #000;border-top:1px solid #000;border-right:1px solid #000;text-align:center;"></td>
    </tr>
    <tr>
    	<td style="width:15%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;text-align:center;">1 tiempo</td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;text-align:center;"></td>
        <td style="width:10%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;text-align:center;"></td>
        <td style="width:65%;border:1px solid #000;text-align:center;"></td>
    </tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:20px; padding:0 20px;">
	<tr>
    	<td style="width:50%;"><strong>Un tiempo:</strong> <i>plato fuerte con 2 guarniciones</i></td>
        <td style="width:50%; text-align:right;"><strong>2 tiempo:</strong> <i>plato fuerte con 2 guarniciones</i></td>
    </tr>
    <tr>
    	<td colspan="2" style="text-align:center;padding:20px"><strong>3 tiempo:</strong> <i>plato fuerte con 2 guarniciones</i></td>
    </tr>
</table>

<div style="width:90%; padding:0 20px;"><strong>En el servicio de banquete incluimos:</strong></div>
<p style="width:90%; padding:0 20px; line-height:200%; margin-bottom:10px;">Meseros, capitán de meseros, edecán, personal de cocina _____________________________<br /> _________________________________________________ mantel y cubremantel, cubiertos, loza, cristaleria, refresco (a consumo) y hielo; el servicio es por _____ horas.</p>

<div style="width:90%;padding:0 20px;"><strong>Ponemos a su consideración otros servicios:</strong></div>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:90%; margin-top:10px; padding:0 20px;">
	<tr>
    	<td style="width:50%; padding-left:30px; vertical-align:top;">
        	<ul>
            	<li style="list-style:">Medio servicio</li>
                <li>Servicio de coctelería</li>
                <li>Arreglo de salón</li>
                <li>Pastel</li>
                <li>Servicio de cerveza a consumo</li>
            </ul>
        </td>
        <td style="width:50%; padding-left:30px; vertical-align:top;">
        	<ul>
            	<li>Medio Servicio de café</li>
                <li>Servicio de barman</li>
                <li>Desayunos tipo bufet</li>
                <li>Coordinador de evento</li>
            </ul>
        </td>
    </tr>
</table>
<div style="width:95%;border-bottom:1px solid #000; margin:auto; margin-top:10px;"></div>
<div style="width:95%;border-bottom:1px solid #000; margin:auto; margin-top:10px;"></div>
<div style="width:95%;border-bottom:1px solid #000; margin:auto; margin-top:10px;"></div>
<div style="width:95%;border-bottom:1px solid #000; margin:auto; margin-top:10px;"></div>
<div style="width:95%;border-bottom:1px solid #000; margin:auto; margin-top:10px;"></div>
<div style="width:95%;border-bottom:1px solid #000; margin:auto; margin-top:10px;"></div>

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