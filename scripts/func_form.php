<?php
include("datos.php");
//funciones para usar en los formularios
function tipoEventosOpt(){
	$id_empresa=$_SESSION["id_empresa"];
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	//inicializar variable html
	$option="";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			*
		FROM tipo_evento
		WHERE id_empresa=$id_empresa;";
		
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $row){
			$option.='<option value="'.$row["id_tipo"].'">'.$row["nombre"].'</option>';
		}
	}catch(PDOException $err){
		$option=$err->getMessage();
	}
	echo $option;
}

function salonesOpt(){
	$id_empresa=$_SESSION["id_empresa"];
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	//inicializar variable html
	$option="";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			*
		FROM salones
		WHERE id_empresa=$id_empresa;";
		
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $row){
			$option.='<option value="'.$row["nombre"].'">'.$row["nombre"].'</option>';
		}
	}catch(PDOException $err){
		$option=$err->getMessage();
	}
	echo $option;
}

function nuevaClaveCotizar(){
	$id_empresa=$_SESSION["id_empresa"];
	$id_usuario=$_SESSION["id_usuario"];
	
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	
	//inicializar variable html
	$option="";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			clave
		FROM cotizaciones
		WHERE id_empresa=$id_empresa AND id_usuario=$id_usuario ORDER BY id_cotizacion DESC;";
		
		$res=$bd->query($sql);
		if($res->rowCount()>0){
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
			$id=$res[0]["clave"]+1;
		}else{
			$id=1;
		}
		
		//escribe la clave
		echo ($id);
	}catch(PDOException $err){
		//escribe el error
		echo $err->getMessage();
	}
	$bd=NULL;
}

function nuevaClaveCliente(){
	$id_empresa=$_SESSION["id_empresa"];
	$id_usuario=$_SESSION["id_usuario"];
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	
	//inicializar variable html
	$option="";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			clave
		FROM clientes
		WHERE id_empresa=$id_empresa
		ORDER BY id_cliente DESC
		LIMIT 1;";
		
		$res=$bd->query($sql);
		if($res->rowCount()>0){
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
		
			//escribe la clave
			echo ($res[0]["clave"]+1);
		}else{
			echo 1;
		}
	}catch(PDOException $err){
		//escribe el error
		echo $err->getMessage();
	}
	$bd=NULL;
}

function nCveProv(){
	$id_empresa=$_SESSION["id_empresa"];
	$id_usuario=$_SESSION["id_usuario"];
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	
	//inicializar variable html
	$option="";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			clave
		FROM proveedores
		WHERE id_empresa=$id_empresa AND id_usuario=$id_usuario
		ORDER BY id_proveedor DESC
		LIMIT 1;";
		
		$res=$bd->query($sql);
		if($res->rowCount()>0){
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
		
			//escribe la clave
			echo ($res[0]["clave"]+1);
		}else{
			echo 1;
		}
	}catch(PDOException $err){
		//escribe el error
		echo $err->getMessage();
	}
	$bd=NULL;
}

function nuevaClaveArt($dsnw, $userw, $passw, $optPDO){
	$id_empresa=$_SESSION["id_empresa"];
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			clave
		FROM articulos
		WHERE id_empresa=$id_empresa
		ORDER BY id_articulo DESC;";
		
		$res=$bd->query($sql);
		if($res->rowCount()>0){
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
			$id=$res[0]["clave"]+1;
		}else{
			$id=1;
		}
		
		//escribe la clave
		return ($id);
	}catch(PDOException $err){
		//escribe el error
		return $err->getMessage();
	}
	$bd=NULL;
}

function nCvePaq(){
	$id_empresa=$_SESSION["id_empresa"];
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	
	//inicializar variable html
	$option="";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		
		$sql="SELECT
			clave
		FROM paquetes
		WHERE id_empresa=$id_empresa
		ORDER BY id_paquete DESC
		LIMIT 1;";
		
		$res=$bd->query($sql);
		if($res->rowCount()>0){
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
		
			//escribe la clave
			echo ($res[0]["clave"]+1);
		}else{
			echo 1;
		}
	}catch(PDOException $err){
		//escribe el error
		echo $err->getMessage();
	}
	$bd=NULL;
}

function stCotCls($st){
//escribe la clase del estatus de cotización con codigo de color
	$e=array(
		0=>"cancelado",
		1=>"pendiente"
	);
	return $e[$st];
}
function stEveCls($st){
	$e=array(
		0=>"cancelado",
		1=>"no_autorizado",
		2=>"evento"
	);
	return $e[$st];
}

function afs($criterio){
	$id_empresa=$_SESSION["id_empresa"];
	global $dsnw;
	global $userw;
	global $passw;
	global $optPDO;
	//inicializar variable html
	$option="";
	$tabla=$criterio."s";
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		$sql="SELECT
			*
		FROM $tabla
		WHERE id_empresa=$id_empresa;";
		
		$res=$bd->query($sql);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $row){
			$option.='<option value="'.$row["id_$criterio"].'">'.$row["nombre"].'</option>';
		}
	}catch(PDOException $err){
		$option=$err->getMessage();
	}
	echo $option;
}

function varFechaExtensa($fecha){
	return ucfirst(utf8_encode(strftime("%A, %d de %B de %Y %I:%M %p",strtotime($fecha))));
}
function varFechaAbreviada($fecha){
	return ucfirst(utf8_encode(strftime("%d / %B / %Y %I:%M %p",strtotime($fecha))));
}
function varFechaAbrNorm($fecha){
	return date("d/m/Y h:i a",strtotime($fecha));
}
function varFechaAbr($fecha){
	return date("d/m/Y",strtotime($fecha));
}
function fechaAbreviada($fecha){
	echo ucfirst(utf8_encode(strftime("%d / %B / %Y %I:%M %p",strtotime($fecha))));
}
function varFechaSql($fecha){
	return ucfirst(utf8_encode(strftime("%A, %d de %B de %Y %I:%M %p",strtotime($fecha))));
}
function varFechaDM($fecha){
	return ucfirst(utf8_encode(strftime("%A, %d de %B de %Y %I:%M %p",strtotime($fecha))));
}
function varFechaComp($fecha){
	return ucfirst(utf8_encode(strftime("%A, %d de %B de %Y %I:%M %p",strtotime($fecha))));
}
?>