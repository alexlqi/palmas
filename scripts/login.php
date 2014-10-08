<?php session_start();
include("datos.php");
$usuario=$_POST["usuario"];
$contra=$_POST["pass"];

try{
if($usuario!="" and $contra!=""){
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$res=$bd->query("SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$contra';");
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$d=$res[0];
		unset($d["password"]);
		$_SESSION["id_usuario"]=$d["id_usuario"];
		$_SESSION["id_empresa"]=$d["id_empresa"];
		$_SESSION["usuario"]=$d["usuario"];
		$_SESSION["nombre"]=$d["nombre"];
		$_SESSION["apellido"]=$d["apellido"];
		$_SESSION["categoria"]=$d["categoria"];
		$_SESSION["comision"]=$d["comision"]*1/100;
		echo 'Iniciando sesión...<meta http-equiv="refresh" content="0;URL='.LIGA.'home.php" />';
		echo '<script>(function(){window.location="'.LIGA.'home.php"}())</script>';
	}else{
		echo 'Usuario o contraseña equivocadas';
	}
}else{
	echo 'Escribe el usuario y/o contraseña';
}
}catch(PDOException $err){
	echo 'Error encontrado: '.$err->getMessage();
}
$bd=NULL;
?>