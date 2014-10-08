<?php //funciones para guardar o modificar
function fixFecha($fecha){
	//partimos la fecha y hora d[0] es para la fecha, d[1] es para la hora y d[2] es para meridiano
	$d=explode(" ",$fecha);
	
	//arreglamos la fecha
		$f=explode("/",$d[0]);
		$ff=$f[2]."-".$f[1]."-".$f[0];
	
	//arreglamos la hora y minutos
	$h=explode(":",$d[1]);
		$hh=$h[0];
		$mm=$h[1];
		
$fecha=$ff." ".$hh.":".$mm;
return $fecha;
}

?>