<?php
$userId=$_SESSION["id_usuario"];
define("ESP"," ");
echo '<div id="datosUsuario" style="display:none;position:fixed;width:0;height:0;"'.ESP;
echo "data-id='$userId'".ESP;
echo '></div>';
?>