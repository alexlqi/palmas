<?php
//funciones para usar

function updateCampoValor($tabla,$datos,$where=NULL){
	$sql="UPDATE $tabla SET ";
	//set y values
	$ctrl=1;
	$fin=count($datos);
	foreach($datos as $i=>$v){
		if($ctrl!=$fin){
			$sql.="$i='$v', ";
		}else{
			$sql.="$i='$v'";
		}
	$ctrl++;
	}
	$sql.=" WHERE $where;";
	$sql=str_replace("'NULL'",'NULL',$sql);
	return $sql;
	
	
}

function insertCamposValues($tabla,$datos,$unsets=NULL){
	//quitar datos
	$datos=unsetCampos($datos,$unsets);
	
	//inicializar sentencia select
	$sql="INSERT INTO $tabla ";
	
	//seccion de campos
	$campo="(";
	$ctrl=1;
	$fin=count($datos);
	foreach($datos as $dato=>$b){
		if($ctrl!=$fin){
			$campo.="$dato, ";
		}else{
			$campo.="$dato";
		}
	$ctrl++;
	}
	$sql.= $campo.") ";
	
	//Sección de values
	$val=" VALUES ( ";
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
	$sql.= $val.");";
	
	//
	return $sql;
}

function insertValues($tabla,$datos,$unsets=null){
	//quitar datos
	$datos=unsetCampos($datos,$unsets);
	
	//inicializar sentencia select
	$sql="INSERT INTO $tabla ";
	
	//Sección de values
	$val="(0, ";
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
	
	//
	return $sql;
}

function querySetValues($tabla,$datos,$unsets){
	
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