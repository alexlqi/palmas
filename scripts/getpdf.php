<?php session_start();
require_once('../clases/html2pdf.class.php');

//captura el html
$html=$_POST["html"];

//para arreglar las imagenes
$html=str_replace('src="','src="../',$html);
$html=str_replace('#mias ','',$html);
$html=str_replace('<tr','<tr align="center"',$html);

$html=strip_tags($html,"<div><table><tr><th><td><style><br>");

$path='../docs/';
$filename=$_POST["nombre"].".pdf";

//configurar la pagina
$orientar=$_POST["orientar"];

$topdf=new HTML2PDF($orientar,'Letter','es');
$topdf->writeHTML($html);
$topdf->Output($path.$filename,'F');

echo "http://".$_SERVER['HTTP_HOST']."/docs/".$filename;

?>