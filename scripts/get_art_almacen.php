<?php session_start();
include("datos.php");
$cat=$_SESSION["categoria"];
$empresa=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
}catch(PDOException $err){
	echo $err;
}
?>