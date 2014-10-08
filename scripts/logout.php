<?php session_start();
session_destroy();
echo utf8_decode('Cerrando sesión').'<meta http-equiv="refresh" content="2;URL=http://'.$_SERVER["HTTP_HOST"].'" />';
?>