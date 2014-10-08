<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es");
require_once('../clases/html2pdf.class.php');
include("datos.php");
include("func_form.php");
$emp=$_SESSION["id_empresa"];

$datos=$_POST["datos"];
//obtener datos del cliente
try{
	$sql="SELECT logo FROM empresas WHERE id_empresa=$emp;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$logo='<img src="../'.$res[0]["logo"].'" width="189" />';
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
$evento=$datos["nombre"];
$hora=varFechaExtensa($datos["fechamontaje"]);
$lugar=$datos["salon"];

//tamaño carta alto:279.4 ancho:215.9
$heightCarta=960;
$widthCarta=660;
$celdas=12;
$widthCell=$widthCarta/$celdas;

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
<table style="width:100%" cellpadding="0" cellspacing="0" >
    <tr>
		 <td style="width:25%"><?php echo $logo; ?></td>
         <td style="width:50%; text-align:center;"><h1>Formato para surtir equipo</h1></td>
         <td style="width:25%"><?php echo varFechaAbreviada(date("Y-m-d H:i:s")); ?></td>
    </tr>
</table>
<table style="width:100%;" cellpadding="0" cellspacing="0" >
    <tr>
		 <td style="width:30%; font-size:20px; padding:1mm;">
			<span style="text-decoration:underline;">Evento: <?php echo $evento; ?></span>
         </td>
         <td style="width:70%; font-size:18px;">
	        <span>Hora: <?php echo $hora; ?></span>
         </td>
    </tr>
    <tr>
    	<td style="width:50%; font-size:18px; padding-bottom:2mm;"><span>Lugar: <?php echo $lugar; ?></span></td>
    </tr>
</table>
<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0" >
<?php //foreach para cada elemento
		echo '<tr>';
		echo '<th style="width:40%; font-size: 14px; border:0.2mm;">Artículo</th>';
		echo '<th style="width:10%; font-size: 14px; border:0.2mm;">Cantidad</th>';
		echo '<th style="width:50%; font-size: 14px; border:0.2mm;">Observaciones:</th>';
		echo '</tr>';
	foreach($datos["items"] as $d){
		echo '<tr>';
		echo '<td style="width:40%; font-size: 14px; border:0.2mm;">'.$d["articulo"].'</td>';
		echo '<td style="width:10%; font-size: 14px; border:0.2mm;">'.$d["cantidad"].'</td>';
		echo '<td style="width:50%; font-size: 14px; border:0.2mm; text-align:center;"><input type="text" style="width:95%; font-size:12px;" /></td>';
		echo '</tr>';
	}
?>
</table>
<?php
$html=ob_get_clean();
$path='../docs/';
$filename=$_POST["nombre"].".pdf";

//configurar la pagina
$orientar=$_POST["orientar"];

$topdf=new HTML2PDF($orientar,'Letter','es');
$topdf->writeHTML($html);
$topdf->Output($path.$filename,'F');

echo "http://".$_SERVER['HTTP_HOST']."/docs/".$filename;

?>