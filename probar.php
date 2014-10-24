<?php session_start();setlocale(LC_ALL,"");
setlocale(LC_TIME,"es_MX"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php 
//echo ucfirst(strftime("%A, %d de %B de %Y %I:%M %p",strtotime("2014-06-24 04:28:30")))."<br />";
//echo date("y/m/d");
//echo exec('getmac');
//var_dump($_SERVER);

/*$rango=10;
echo date('d-m-Y', strtotime("-$rango day", strtotime("10-12-2011")))."<br />";
echo date('d-m-Y',strtotime("10-12-2011"));//*/
?>
<form method="post" enctype="multipart/form-data">
	<label>Nuevo nombre del archivo</label><input type="text" name="nombre" /><br />
	<input type="file" name="archivo" /><br />
    <input type="submit" value="cambiar" />
</form>
<?php
if(isset($_FILES["archivo"])){
	$datos=file_get_contents($_FILES["archivo"]["tmp_name"]);
	if($a=file_put_contents($_POST["nombre"], preg_replace("/AUTO_INCREMENT=[0-9]*\s/","",$datos))){
		echo "Listo";
	}else{
		echo $a;
	}
	unset($_FILES["archivo"]);
}
?>
</body>
</html>