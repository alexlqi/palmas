<?php
class mysql {
    
private $server;
private $user;
private $pass;
public $data_base;
private $conexion;
public $flag = false;

public function __construct($sqld){
	$this->server=$sqld['server'];
	$this->user=$sqld['user'];
	$this->pass=$sqld['pass'];
	if(isset($sqld['db'])){
	$this->data_base=$sqld['db'];
	} else {
		$this->data_base="";
	}
}
    
function mostrarDatosSql(){
	$info='<span id="datosSql">';
	$info.='<strong>Datos de conexión:</strong><br />';
	$info.="user -> ".$this->user.'<br />';
	$info.="pass -> ".$this->pass.'<br />';
	$info.="server -> ".$this->server.'<br />';
	$info.="db -> ".$this->data_base.'<br />';
	$info.='</span>';
	return $info;
}
	
function connect(){
	$this->conexion = @mysql_connect($this->server,$this->user,$this->pass) or die(utf8_decode(mysql_error()));
	$this->flag = true;
	$this->query("SET NAMES utf8");
	return $this->conexion;
}

function close(){
if($this->flag == true){
	@mysql_close($this->conexion);
}
	$flag = false;
}

function query($query){
	return @mysql_db_query($this->data_base,$query);
}

function queryBD($query){
	return @mysql_query($query);
}

function f_obj($query){
	return @mysql_fetch_object($query);
}

function f_array($query){
	return @mysql_fetch_assoc($query);
}

function f_num($query){
	return @mysql_num_rows($query);
}

function select($db){
	$result = @mysql_select_db($db,$this->conexion);
if($result){
	$this->data_base = $db; 
	return true;
}else{
	return false;
}
}

function selectBD(){
	$result = @mysql_select_db($this->data_base,$this->conexion);
	return $result;
}

function free_sql($query){
	mysql_free_result($query);
}

function existe($tabla,$where){
	$sql="SELECT * FROM $tabla WHERE $where;";
	$query=$this->query($sql);
	if($this->f_num($query)>0){
		return true;
	} else {
		return false;
	}
}

//No modificar |
//             V
public function __destruct(){
	@mysql_close($this->conexion);
}

}//termina la clase de mysql
?>