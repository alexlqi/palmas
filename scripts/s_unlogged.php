<?php session_destroy();
$server=$_SERVER["HTTP_HOST"];$path=""; 
$logoutRedir='http://'.$server."/".$path;
$goto=urlencode($_SERVER["REQUEST_URI"]); //sirve para ir hacia la pagina donde se intento accesar sin login
/*echo '<script type="text/javascript">setTimeout(function(){self.location="'.$logoutRedir."/?goto=$goto".'"}, 0)</script>';//*/
echo '<script type="text/javascript">setTimeout(function(){self.location="'.$logoutRedir.'"}, 0)</script>';

?>