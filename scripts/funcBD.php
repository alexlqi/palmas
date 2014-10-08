<?php
//funciones para usar

function insertValues($tabla,$datos,$unsets=""){
	//quitar datos
	$datos=unsetCampos($datos,$unsets);
	
	//inicializar sentencia select
	$sql="INSERT INTO $tabla ";
	
	//Sección de values
	$val="VALUES (0, ";
	$ctrl=1;
	$fin=count($datos);
	foreach($datos as $a=>$dato){
		if($ctrl!=$fin){
			$val.="'$dato', ";
		}else{
			$val.="'$dato'";
		}
	$ctrl++;
	}
	$sql.= $val;
	
	//terminar y enviar
	$sql.=");";
	return $sql;
}

function querySetValues($tabla,$datos,$unsets){
	
}

function checarVacios($datos,$limite=0){
	$vacios=0;
	foreach($datos as $a=>$b){
		if($b==""){
			$vacios++;
		}
	}
	
	if($vacios>$limite){
		return false;
	} else {
		return true;
	}
}

function unsetCampos($datos,$unsets){
	if(is_array($unsets) and count($unsets)>0){
		foreach($unsets as $index=>$val){
			unset($datos[$val]);
		}
	} //quita los valores del formulario que se desean quitar
	return $datos;
}
?>